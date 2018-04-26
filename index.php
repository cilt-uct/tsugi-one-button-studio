<?php
require_once "../config.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
global $CFG, $PDOX;

error_reporting(E_ALL);
ini_set('display_errors', '1');

$launch = LTIX::requireData();

$path = $CFG->getPWD('index.php');
$app = new \Tsugi\Silex\Application($launch);

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
    'assets.version_format' => addSession('%s')
));

$app->get('/', 'AppBundle\\Booking::getPage')->bind('main');
$app->get('assets/{file}', 'AppBundle\\Booking::getFile')->assert('file', '.+');
$app->get('/booking/{id}', 'AppBundle\\Booking::getBooking');
$app->post('/booking/set', 'AppBundle\\Booking::setBooking');
$app->post('/booking/pre', 'AppBundle\\Booking::preBooking');

$app->run();