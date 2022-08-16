<?php

# The configuration file for this tool - copy to tool-config.php
$tool = array(
    "debug" => false,
    "admin" => false,
    "config" => basename(__FILE__, '.php'),
    "opencast_server" => 'https://stable.opencast.org/',
    "opencast_user"   => 'admin',
    "opencast_pass"   => 'opencast'
);

if (file_exists("tool-config.php") && basename(__FILE__, '.php') != "tool-config") {
    include 'tool-config.php';
}
