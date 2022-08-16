<?php
require_once('../config.php');
require_once "dao/BookingDAO.php";

include 'tool-config-dist.php';
include 'src/Template.php';

use \Tsugi\Util\U;
use \Tsugi\Core\Cache;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\Util\LTI;
use \Tsugi\UI\SettingsForm;
use \Booking\DAO\BookingDAO;

global $CFG, $PDOX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();
$linkId = $LINK->id;
$p = $CFG->dbprefix;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$menu = false;

$settings = $LAUNCH->link->settingsGetAll(); // context and link settings

$bookingDAO = new BookingDAO($PDOX, $CFG->dbprefix);

$my_series = 'none'; // $app['tsugi']->user->getJsonKey('opencast_series', 'none');
// if ($my_series == 'none') {
//     $raw = $app['tsugi']->context->ltiRawPostArray();
//     $my_series = $this->CreateSeries($app['tsugi']->user->displayname, $app['tsugi']->user->getNameAndEmail(), $raw['lis_person_sourcedid'], $raw['context_id']);

//     if ($my_series != false) {
//         $app['tsugi']->user->setJsonKey('opencast_series', $my_series);
//     }
// }

$date_now = new DateTime();
$json = '[{"month": 6, "bookings": "1" }]';

$lastMonth = (new DateTime()) -> sub(new DateInterval('P1M')) -> format('Y-m-d');
$nextMonth = (new DateTime()) -> add(new DateInterval('P1M')) -> format('Y-m-d');

$context = [
    'instructor' => $USER->instructor,
    'isAdmin'    => $USER->id == $tool['admin'] ? 1 : 0,

    'styles'     => [ addSession('assets/css/app.min.css') ],
    'scripts'    => [ addSession( $CFG->staticroot .'/js/tmpl.min.js'), addSession('assets/js/moment.min.js'),
                        addSession('assets/js/jquery.validate.min.js'), 
                        addSession('assets/js/amcharts/amcharts.js'), addSession('assets/js/amcharts/serial.js'), 
                        addSession('assets/js/amcharts/themes/light.js') ],
    
    'my_series'  => $my_series,

    'action_process'    => addSession('actions/post.php'),
    'action_get'        => addSession('actions/get.php'),

    'display_settings'  => json_encode($settings),
    'raw'               => $LAUNCH->ltiRawPostArray(),
    
    'prevPeriod' => $lastMonth,
    'nextPeriod' => $nextMonth
];

$current = (object)[];
$current->date = date("Y-n", time()); //"2018-4"

// // 1. If the request is from Admin we return the complete class
// // if ($context['isAdmin']) {
//     $rows = $PDOX->allRowsDie("SELECT booking_id as 'id', (user_id = :UI) as 'isme', DATE(booking_start) as 'date', TIME_FORMAT(booking_start, '%H') as 'time' FROM {$p}booking
//         WHERE link_id = :LI and booking_start between :prevPeriod and :nextPeriod ORDER BY booking_start",
//         array(':LI' => $app['tsugi']->link->id,
//             ':UI' => $app['tsugi']->user->id,
//             ':prevPeriod' => $lastMonth .' 00:00:00',
//             ':nextPeriod' => $nextMonth .' 23:59:59')
//     );

    $current->bookings = []; //$rows;
// //}

$context['json'] = json_encode($current);

// Start of the output
$OUTPUT->header();

Template::view('templates/header.html', $context);

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

Template::view('templates/body.html', $context);

if ($tool['debug']) {
    echo '<pre>'; print_r($context); echo '</pre>';
}

$OUTPUT->footerStart();

Template::view('templates/footer.html', $context);
include('templates/tmpl.html');

$OUTPUT->footerEnd();
