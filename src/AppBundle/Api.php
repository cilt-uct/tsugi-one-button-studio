<?php

namespace AppBundle;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;
use GuzzleHttp\Client;

use DateTime;
use DateInterval;

class Api {
    
    public function getBookings(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;

        $params = $request->query->all();
        $qry = "select booking_id as id, user_id as user, booking_start, title from booking where ";
        $bind = array();

        $filters = array();
        if (isset($params['startDate'])) {
          $filters[] = array('field' => 'booking', 'operation' => '>', 'bindParam' => ':start');
          $bind[':start'] = $params['startDate'];
        }
        if (isset($params['endDate'])) {
          $filters[] = array('field' => 'booking', 'operation' => '<', 'bindParam' => ':end');
          $bind[':end'] = $params['endDate'];
        }
        if (isset($params['user'])) {
          $filters[] = array('field' => 'user_id', 'operation' => '=', 'bindParam' => ':user');
          $bind[':user'] = $params['user'];
        }

        if (!isset($params['startDate'])) {
          $qry .= "booking_start between now() and date_add(now(), interval 8 hour)";
        }
        if (!isset($params['orderBy'])) {
          $qry .= " order by booking_start asc";
        }
        $rows = $PDOX->allRowsDie($qry, $bind);
        $response = array( 'offset' => 0, 'count' => sizeof($rows), 'bookings' => $rows);

        return new Response(json_encode($response, JSON_PRETTY_PRINT), 200, [ 
            'Content-Type' => 'application/json'
        ]);
    }

    public function setBooking(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;

        $input = $_POST;
        $givenFields = array_keys($_POST);
        $requiredPostFields = ['title', 'start_date'];
        $isRequiredSatisfied = sizeof($requiredPostFields) === sizeof(array_intersect($requiredPostFields, $givenFields));

        if ( $isRequiredSatisfied && $app['tsugi']->user->instructor ) {

            $startTime = strtotime($input['start_date']);
            $query = "INSERT INTO {$p}booking
                (link_id, user_id, booking_start, booking_end, title, updated_at)
                VALUES ( :LI, :UI, :BOOKING_START, :BOOKING_END, :TITLE, NOW())
                ON DUPLICATE KEY UPDATE booking_start = :BOOKING_START, pre = 0";

            $bind = array(
                ':LI' => $app['tsugi']->link->id,
                ':UI' => $app['tsugi']->user->id,
                ':TITLE' => $input['title'],
                ':BOOKING_START' => $input['start_date'],
                ':BOOKING_END' => isset($input['end_date']) ? $input['end_date'] : date("Ymd H:i:s", $startTime + 3600)
            );

            if ($startTime > 0 && strtotime($bind[':BOOKING_END']) > $startTime) {
                $q = $PDOX->queryDie($query, $bind);
                $response['result'] = $q;
                return json_encode($response);
            }
            else {
                return new Response("Invalid dates supplied", 400);
            }
        }
        else if (!($app['tsugi']->user->instructor)) {
          return new Response("You are not authorised to perform this action", 403);
        }
        else if (!$isRequiredSatisfied) {
          return new Response("Please complete all required parameters", 400);
        }

        return json_encode($response);
    }

    public function getBooking(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        $id = str_replace('/api/booking/', '', $request->getPathInfo());

        $params = $request->query->all();
        $qry = "select booking_id as id, user_id as user, booking_start as start, booking_end as end, title from booking where booking_id = :id and user_id = :user_id";
        $bind = array(
           ':id' => $id
          ,':user_id' => $app['tsugi']->user->id
        );

        $rows = $PDOX->allRowsDie($qry, $bind);

        if (sizeof($rows) === 0) {
          return new Response('Not found', 404);
        }

        return new Response(json_encode($rows[0], JSON_PRETTY_PRINT), 200, [ 
            'Content-Type' => 'application/json'
        ]);
    }

    public function preBooking(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        
        $response = array('status' => false);

        if ( isset($_POST['title']) && $app['tsugi']->user->instructor ) {
            
            $input = $_POST;

            $expires = new DateTime();
            $expires->modify("+15 minutes");

            $q = $PDOX->queryDie("INSERT INTO {$p}booking
                (link_id, user_id, booking, updated_at, pre, pre_expire)
                VALUES ( :LI, :UI, :BOOKING, NOW(), 1, :EXPIRE)
                ON DUPLICATE KEY UPDATE booking = :BOOKING",
                array(
                    ':LI' => $app['tsugi']->link->id
                   ,':UI' => $app['tsugi']->user->id
                   ,':TITLE' => $input['title']
                   ,':BOOKING' => $input['date']
                   ,':EXPIRE' => $expires->format( 'Y-m-d H:M:S' )
                )
            );

            $response['success'] = $q->success;
        }

        return json_encode($response);
    }

    public function getAllPersonalSeries(Request $request, Application $app)
    {
        $req = array();
        $req['params'] = $_GET;
        $req['user_id'] = $app['tsugi']->user->id;

        $results = $this->searchPersonalSeries($req, $app['tsugi']->user->admin);
        $response = array(
          'total' => sizeof($results),
          'offset' => isset($req['params']['offset']) ? (is_numeric($req['params']['offset']) ? (int) $req['params']['offset'] : 0) : 0,
          'limit' => isset($req['params']['limit']) ? (is_numeric($req['params']['limit']) ? (int) $req['params']['limit'] : 0) : 0,
          'series' => $results
        );
        return new Response(json_encode($response, JSON_PRETTY_PRINT), sizeof($results) > 0 ? 200 : 404, [ 
            'Content-Type' => 'application/json'
        ]);
        
    }

    private function searchPersonalSeries($details = array(), $isAdmin = false)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;

        if (isset($details['params']['PHPSESSID'])) {
          unset($details['params']['PHPSESSID']);
        }

        if (!$isAdmin) {
          $details['params']['user_id'] = $details['user_id'];
        }

        $fields = ['user_id', 'series_id', 'workspace_id'];
        $qry = "select " . implode(", ", $fields) . " from " . $p . "booking_series";
        $paramQryKeys = array_intersect($fields, array_keys($details['params']));
        $bind = array();

        if (sizeof($paramQryKeys) > 0) {
          $qry .= " where ";
          $constraints = array();
          foreach($paramQryKeys as $paramQryKey) {
            $constraints[] = "$paramQryKey = :$paramQryKey";
            $bind[":$paramQryKey"] = $details['params'][$paramQryKey];
          }

          $qry .= implode($constraints, " and ");
        }

        $offset = isset($details['params']['offset']) ? $details['params']['offset'] : 0;
        $offset = is_numeric($offset) ? $offset : 0;
        $limit = isset($details['params']['limit']) ? $details['params']['limit'] : 20;
        $limit = is_numeric($limit) ? $limit : 20;
        $qry .= " limit $offset,$limit";

        $rows = $PDOX->allRowsDie($qry, $bind);
        return $rows;
    }

    private function createOCSeries($name, $display, $source_id, $context_id)
    {        
        $username = 'PersonalSeriesCreator';
        $password = 'tester';
        $remote_url = 'https://mediadev.uct.ac.za/api/series';
        
        $series = array(
            array(
                'flavor' => 'dublincore/series',
                'title' => 'Opencast Series DublinCore',
                'fields' => array(
                    array('id' => 'title', 'value' => 'Personal Series ('. $name .')')
                    ,array('id' => 'subject', 'value' => 'Personal')
                    ,array('id' => 'description', 'value' => "Personal Series: $display\nSakai site: $context_id")
                    ,array('id' => 'language', 'value' => 'eng')
                    ,array('id' => 'rightsHolder', 'value' => 'University of Cape Town')
                    ,array('id' => 'license', 'value' => 'ALLRIGHTS')
                    ,array('id' => 'creator', 'value' => array($name))
                    ,array('id' => 'contributor', 'value' => array($name))
                    ,array('id' => 'publisher', 'value' => array($name))
                )
            ),
            array(
                'flavor' => 'ext/series',
                'title' => 'UCT Series Extended Metadata',
                'fields' => array(
                    array('id' => 'course', 'value' => '')
                    ,array('id' => 'creator-id', 'value' => $source_id)
                    ,array('id' => 'site-id', 'value' => $context_id)
                )
            )
        );

        // Create a stream
        $opts = array(
            'http'=>array(
                'method'=>'POST'
                ,'header' => array(
                    'Authorization: Basic ' . base64_encode("$username:$password"), 
                    'Content-type: application/x-www-form-urlencoded'
            )
            ,'content' => http_build_query(
                array(
                    'metadata' => json_encode($series),
                    'acl' => '[{"action":"read","allow":true,"role":"ROLE_USER_' . $source_id . '"},{"action":"write","allow":true,"role":"ROLE_USER_' . $source_id . '"}]'
                )
            )
          )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $result = file_get_contents($remote_url, false, $context);

        if ($result != false) {
            $result = json_decode($result)->identifier;
        }

        return $result;
    }

    public function addOBStool(Request $request, Application $app) {
        $raw = $app['tsugi']->context->ltiRawPostArray();
        $vula = new SakaiWS('https://devslscle001.uct.ac.za', 'admin', 'vula054');

        $homeSite = $vula->getUserHome($raw['lis_person_sourcedid']);
        if (!$homeSite) {
            return new Response("User not found", 404);
        }

        $my_series = $this->createOCSeries($app['tsugi']->user->displayname, $app['tsugi']->user->getNameAndEmail(), $raw['lis_person_sourcedid'], $homeSite);
        if (!$my_series) {
            return new Response("Could not create series", 500);
        }

        $obsCreation = $vula->addOBStoolToSite($raw['lis_person_sourcedid'], $my_series, $homeSite);
        if ($obsCreation !== 'added' && $obsCreation !== 'updated') {
            return new Response("Could not create OBS tool in workspace", 500);
        }

        $userOBStool = $vula->getOBStool($raw['lis_person_sourcedid'], $homeSite);
        $userOBStool['id'] = $userOBStool['@attributes']['id'];
        $userOBStool['page-link'] = "https://devslscle001.uct.ac.za/portal/site/~$raw[lis_person_sourcedid]/page/" . $userOBStool['@attributes']['id'];
        return new Response(json_encode($userOBStool, JSON_PRETTY_PRINT), 201, [
            'Content-Type' => 'application/json'
        ]);
    }

    function createVulaTool($eid, $seriesId) {
        $vula = new SakaiWS('https://devslscle001.uct.ac.za', 'admin', 'vula054');
        return $vula->addOBStoolToSite($eid, $seriesId);
    }
}
