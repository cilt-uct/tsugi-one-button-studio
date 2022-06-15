<?php
require_once('../config.php');
include 'tool-config-dist.php';
include 'src/Template.php';

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$menu = false; // We are not using a menu

$config = [
    'instructor' => $USER->instructor, 
    'styles'     => [ 'static/tooltipster.bundle.min.css', 'static/user.css' ],
];

// Start of the output
$OUTPUT->header();

Template::view('templates/header.html', $config);

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

?>
<div class="bgnew" style=" background-image: linear-gradient(to right top, #d16ba5, #c777b9, #ba83ca, #aa8fd8, #9a9ae1, #8aa7ec, #79b3f4, #69bff8, #52cffe, #41dfff, #46eefa, #5ffbf1);"></div>
<?php
$OUTPUT->splashPage(
    "Vula Help",
    __("This tool is only available to Site Owners.")
);

$OUTPUT->footerStart();

$OUTPUT->footerEnd();