<?php

namespace AppBundle;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \Tsugi\Core\Settings;
use \Tsugi\Util\Net;

class Booking {
    
    public function getPage(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $context = array();
        $context['styles'] = [ addSession( $CFG->staticroot .'/css/app.css'), addSession('assets/css/app.css') ];
        $context['scripts'] = [ addSession('assets/js/moment.min.js') ];
        $context['booking_set'] = addSession('booking/set');

        $current = (object)[];
        $current->date = date("Y-n", time()); //"2018-4"
        $current->bookings = [];

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

    public function setBooking(Request $request, Application $app)
    {
        global $CFG, $PDOX;
        $p = $CFG->dbprefix;
        
        $response = array( 'status' => false);

        if ( isset($_POST['title']) && $app['tsugi']->user->instructor ) {
            
            $input = $_POST;

            $q = $PDOX->queryDie("INSERT INTO {$p}booking
                (link_id, user_id, booking, title, updated_at)
                VALUES ( :LI, :UI, :BOOKING, :TITLE, NOW())
                ON DUPLICATE KEY UPDATE booking = :BOOKING",
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
}
