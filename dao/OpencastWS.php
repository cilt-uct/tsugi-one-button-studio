<?php
namespace Booking\DAO;

### Opencast Web Service

class OpencastWS {

    public function CreateSeries($name, $display, $source_id, $context_id) {        
        $username = 'PersonalSeriesCreator';
        $password = 'tester';
        $remote_url = 'https://opencast.uct.ac.za/api/series';
        
        $series = array(
            array(
                'flavor' => 'dublincore/series',
                'title' => 'Opencast Series DublinCore',
                'fields' => array(
                    array('id' => 'title', 'value' => 'Personal Series ('. $name .')')
                    ,array('id' => 'subject', 'value' => 'Personal')
                    ,array('id' => 'description', 'value' => 'Personal Series for '. $display)
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
                    'acl' => '[{"action":"read","allow":true,"role":"ROLE_GROUP_SAKAI_INSTRUCTOR"},{"action":"write","allow":true,"role":"ROLE_GROUP_SAKAI_INSTRUCTOR"}]'
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

    private function createOCSeries($name, $display, $source_id, $context_id) {        
        $username = 'PersonalSeriesCreator';
        $password = 'tester';
        $remote_url = 'https://opencast.uct.ac.za/api/series';
        
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

    public function getAllPersonalSeries(Request $request, Application $app) {
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

    private function searchPersonalSeries($details = array(), $isAdmin = false) {
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
    
}