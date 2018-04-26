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

class Booking {
    
    public function getPage(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        
        //$app['tsugi']->context->settingsSet('admin', '1');
        $my_series = $app['tsugi']->user->getJsonKey('opencast_series', 'none');
        if ($my_series == 'none') {
            $raw = $app['tsugi']->context->ltiRawPostArray();
            $my_series = $this->CreateSeries($app['tsugi']->user->displayname, $app['tsugi']->user->getNameAndEmail(), $raw['lis_person_sourcedid'], $raw['context_id']);

            if ($my_series != false) {
                $app['tsugi']->user->setJsonKey('opencast_series', $my_series);
            }
        }

        $settings = $app['tsugi']->context->settingsGetAll(); // context and link settings

        $context = array();
        $context['styles'] = [ addSession( $CFG->staticroot .'/css/app.css'), addSession('assets/css/app.css') ];
        $context['scripts'] = [ addSession('assets/js/moment.min.js'), addSession('assets/js/moment.min.js'), 
                                addSession('assets/js/amcharts/amcharts.js'), addSession('assets/js/amcharts/serial.js'), addSession('assets/js/amcharts/themes/light.js') ];

        $context['booking_set'] = addSession('booking/set');
        $context['booking_pre'] = addSession('booking/pre');
        $context['booking_get'] = addSession('booking/{id}');
        $context['booking_get_current'] = addSession('booking/month');

        $context['isAdmin'] = $app['tsugi']->user->id == $settings['admin'];
        $context['readonly'] = $my_series == false ? "1" : "0";

        $context['settings'] = $settings;
        $context['my_series'] = $my_series;
        $context['raw'] = $app['tsugi']->context->ltiRawPostArray();
        
        $context['display_settings'] = json_encode($settings);

        $current = (object)[];
        $current->date = date("Y-n", time()); //"2018-4"

        $lastMonth = (new DateTime()) -> sub(new DateInterval('P1M')) -> format('Y-m-d');
        $nextMonth = (new DateTime()) -> add(new DateInterval('P1M')) -> format('Y-m-d');
        $context['prevPeriod'] = $lastMonth;
        $context['nextPeriod'] = $nextMonth;

        // 1. If the request is from Admin we return the complete class
        // if ($context['isAdmin']) {
            $rows = $PDOX->allRowsDie("SELECT booking_id as 'id', (user_id = :UI) as 'isme', DATE(booking_start) as 'date', TIME_FORMAT(booking_start, '%H') as 'time' FROM {$p}booking
                WHERE link_id = :LI and booking_start between :prevPeriod and :nextPeriod ORDER BY booking_start",
                array(':LI' => $app['tsugi']->link->id,
                    ':UI' => $app['tsugi']->user->id,
                    ':prevPeriod' => $lastMonth .' 00:00:00',
                    ':nextPeriod' => $nextMonth .' 23:59:59')
            );

            $current->bookings = $rows;
        //}

        $context['json'] = json_encode($current);
        
        /*
        $context['old_code'] = Settings::linkGet('code', '');

        $p = $CFG->dbprefix;
        if ( $app['tsugi']->user->instructor ) {
            $rows = $PDOX->allRowsDie("SELECT user_id,attend,ipaddr FROM {$p}attend
                    WHERE link_id = :LI ORDER BY attend DESC, user_id",
                    array(':LI' => $app['tsugi']->link->id)
            );
            $context['rows'] = $rows;
        }
        */

        return $app['twig']->render('Booking.twig', $context);
    }

    public function getFile(Request $request, Application $app, $file = '')
    {
        if (empty($file)) {
            $app->abort(400);
        }

        switch (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            case 'css' : {
                $contentType = 'text/css';
                break;
            }
            case 'js' : {
                $contentType = 'application/javascript';
                break;
            }
            case 'xml' : {
                $contentType = 'text/xml';
                break;
            }
            case 'svg' : {
                $contentType = 'image/svg+xml';
                break;
            }
            default : {
                $contentType = 'text/plain';
            }
        }

        return new Response( file_get_contents( __DIR__ ."/../../assets/". $file), 200, [ 
            'Content-Type' => $contentType
        ]);
    }

    public function getBooking(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        $id = str_replace('/booking/', '', $request->getPathInfo());

        $response = array( 'status' => false, 'id' => $id, 'data' => []);
        
        if ( $id > 0 ) {
            
            $rows = $PDOX->allRowsDie("SELECT booking_id as 'id', title, settings, DATE(booking_start) as 'date', TIME_FORMAT(booking_start, '%H') as 'time' FROM {$p}booking
                WHERE link_id = :LI and (user_id = :UI) and booking_id = :BOOKING_ID",
                array(':LI' => $app['tsugi']->link->id,
                    ':UI' => $app['tsugi']->user->id,
                    ':BOOKING_ID' => $id)
            );

            $response['status'] = true;
            $response['data'] = $rows;
        } else if ($id == "month") {

            $month = $request->get('month');

            $rows = $PDOX->allRowsDie("SELECT booking_id as 'id', (user_id = :UI) as 'isme', DATE(booking_start) as 'date', TIME_FORMAT(booking_start, '%H') as 'time' FROM {$p}booking
                WHERE link_id = :LI and booking_start between :prevPeriod and :nextPeriod ORDER BY booking_start",
                array(':LI' => $app['tsugi']->link->id,
                    ':UI' => $app['tsugi']->user->id,
                    ':prevPeriod' => $month .'-01 00:00:00',
                    ':nextPeriod' => $month .'-31 23:59:59')
            );

            $response['status'] = true;
            $response['data'] = $rows;            
        }

        return json_encode($response);
    }

    public function setBooking(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        
        $response = array( 'status' => false);

        if ( isset($_POST['title']) && $app['tsugi']->user->instructor ) {
            
            $input = $_POST;

            $q = $PDOX->queryDie("INSERT INTO {$p}booking
                (link_id, user_id, booking_start, title, updated_at)
                VALUES ( :LI, :UI, :BOOKING, :TITLE, NOW())
                ON DUPLICATE KEY UPDATE booking_start = :BOOKING, pre = 0",
                array(
                    ':LI' => $app['tsugi']->link->id,
                    ':UI' => $app['tsugi']->user->id,
                    ':TITLE' => $input['title'],
                    ':BOOKING' => $input['date']
                )
            );

            $response['result'] = $q;
        } 
        
        /*
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        $old_code = Settings::linkGet('code', '');
        if ( isset($_POST['code']) && isset($_POST['set']) && $app['tsugi']->user->instructor ) {
            Settings::linkSet('code', $_POST['code']);
            $app->tsugiFlashSuccess('Code updated');
        } else if ( isset($_POST['clear']) && $app['tsugi']->user->instructor ) {
            $rows = $PDOX->queryDie("DELETE FROM {$p}attend WHERE link_id = :LI",
                    array(':LI' => $app['tsugi']->link->id)
            );
            $app->tsugiFlashSuccess('Data cleared');
        } else if ( isset($_POST['code']) ) { // Student
            if ( $old_code == $_POST['code'] ) {
                $PDOX->queryDie("INSERT INTO {$p}attend
                    (link_id, user_id, ipaddr, attend, updated_at)
                    VALUES ( :LI, :UI, :IP, NOW(), NOW() )
                    ON DUPLICATE KEY UPDATE updated_at = NOW()",
                    array(
                        ':LI' => $app['tsugi']->link->id,
                        ':UI' => $app['tsugi']->user->id,
                        ':IP' => Net::getIP()
                    )
                );
                $app->tsugiFlashSuccess(__('Attendance Recorded...'));
            } else {
                $app->tsugiFlashSuccess(__('Code incorrect'));
            }
        }*/

        return json_encode($response);
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
                (link_id, user_id, booking_start, updated_at, pre, pre_expire)
                VALUES ( :LI, :UI, :BOOKING, NOW(), 1, :EXPIRE)
                ON DUPLICATE KEY UPDATE booking_start = :BOOKING",
                array(
                    ':LI' => $app['tsugi']->link->id,
                    ':UI' => $app['tsugi']->user->id,
                    ':TITLE' => $input['title'],
                    ':BOOKING' => $input['date'],
                    ':EXPIRE' => $expires->format( 'Y-m-d H:M:S' )
                )
            );

            $response['success'] = $q->success;
        }

        return json_encode($response);
    }

    public function CreateSeries($name, $display, $source_id, $context_id)
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
}
