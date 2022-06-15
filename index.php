<?php
require_once "../config.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
global $CFG, $PDOX;

error_reporting(E_ALL);
ini_set('display_errors', '1');

$launch = LTIX::requireData();

if ( $USER->instructor ) {
    header( 'Location: '.addSession('instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('student-home.php') ) ;
}


/*
$path = $CFG->getPWD('index.php');
$app = new \Tsugi\Silex\Application($launch);

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
    'assets.version_format' => addSession('%s')
));

$app->get('/', 'AppBundle\\Booking::getPage')->bind('main');
$app->get('assets/{file}', 'AppBundle\\Booking::getFile')->assert('file', '.+');
$app->post('/booking/set', 'AppBundle\\Booking::setBooking');
$app->post('/booking/pre', 'AppBundle\\Booking::preBooking');
$app->get('/api/booking/{id}', 'AppBundle\\Api::getBooking');
$app->get('/api/booking', 'AppBundle\\Api::getBookings');
$app->get('/api/booking/', 'AppBundle\\Api::getBookings');
$app->post('/api/booking', 'AppBundle\\Api::setBooking');
$app->post('/api/booking/', 'AppBundle\\Api::setBooking');

$app->get('/api/series/', 'AppBundle\\Api::getAllPersonalSeries');
$app->get('/api/beries/', 'AppBundle\\Api::addOBStool');

$app->run();
*/