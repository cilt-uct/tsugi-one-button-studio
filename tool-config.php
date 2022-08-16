<?php
# The configuration file for this tool - copy to tool-config.php
$tool = array(
    "debug" => false,
    "config" => basename(__FILE__, '.php'),
    "opencast_server" => 'https://opencast.uct.ac.za/',
    "opencast_user"   => 'PersonalSeriesCreator',
    "opencast_pass"   => 'tester'
);

if (file_exists("tool-config.php") && basename(__FILE__, '.php') != "tool-config") {
    include 'tool-config.php';
}
