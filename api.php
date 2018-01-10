<?php

require_once "../config.php";
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

// Sanity checks
$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];

$response = array( 'status' => false, 'method' => $method);

// create SQL based on HTTP method
switch ($method) {
    case 'GET':
      // Return the information for the Current Course
      
      $rows = false;
      if ( $USER->instructor ) {

        // 1. If the request is from an Instructor we return the complete class
        $rows = $PDOX->allRowsDie("SELECT user_id, section_id, completed FROM {$p}mecmovies
                WHERE link_id = :LI ORDER BY section_id DESC, user_id",
                array(':LI' => $LINK->id)
        );
      } else {

        // 2. Else the request is from a student so return their progress
        $rows = $PDOX->allRowsDie("SELECT user_id, section_id, completed FROM {$p}mecmovies
                WHERE link_id = :LI and user_id = :UI ORDER BY section_id DESC, user_id",
                array(':LI' => $LINK->id,
                      ':UI' => $USER->id
                )
        );
      }

      $response['result'] = $rows;
    case 'PUT':
      // probably similar to POST
      break;
    case 'POST':

      $input = $_POST;

      $q = $PDOX->queryDie("INSERT INTO {$p}mecmovies
          (link_id, user_id, completed, section_id, active)
          VALUES ( :LI, :UI, NOW(), :MODULE, 1)
          ON DUPLICATE KEY UPDATE section_id = :MODULE",
          array(
              ':LI' => $LINK->id,
              ':UI' => $USER->id,
              ':MODULE' => $input['module']
          )
      );

      //$response['in'] = $input;
      $response['result'] = $q;
      break;

    case 'DELETE':
      // How will we handle the deletes?
      // soft delete (requires deleted_by and deleted columns)
      break;
}

echo json_encode($response);