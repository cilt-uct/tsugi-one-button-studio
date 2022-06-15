<?php
# The configuration file for this tool - copy to tool-config.php
$tool = array(
    "debug" => true,
    "admin" => [1],
    "config" => basename(__FILE__, '.php')
);

if (file_exists("tool-config.php") && basename(__FILE__, '.php') != "tool-config") {
    include 'tool-config.php';
}
