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
        $context['json'] = '[{"month": 6, "bookings": "1" }]';
        
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

    public function post(Request $request, Application $app)
    {
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
        }
        return $app->tsugiReroute('main');
    }
}
