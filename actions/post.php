<?php
require_once "../../config.php";
include '../tool-config.php';

require_once "../dao/BookingDAO.php";

use \Tsugi\Core\LTIX;
use \Booking\DAO\BookingDAO;

### Create / Delete the booking information
$LAUNCH = LTIX::requireData();

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$bookingDAO = new BookingDAO($PDOX, $CFG->dbprefix);

$result = ['success' => 0, 'msg' => 'requires POST'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result['msg'] = 'POST is mallformed';
    // $result['msg'] = $_POST;
    if (isset($_POST['type'])) {

        switch($_POST['type']) {
            case 'new':
                $user_email = $USER->email;
                $user_name = $USER->displayname;
                $EID = $LAUNCH->ltiRawParameter('user_id','');

                $result = $bookingDAO->setBooking($LINK->id, $USER->id, 
                                                    $user_name, $user_email, $EID,
                                                    $_POST['input_time'], $_POST['input_date'], $_POST['input_venue'],
                                                    $_POST['input_course'], $_POST['input_dept'], $_POST['input_title'],
                                                    $_POST['input_category'], $_POST['input_desc'], $_POST['input_terms'] == 'yes', false, 0);
                break;
            case 'delete':
                // $result['success'] = $bookingDAO->deleteBooking($LINK->id, $USER->id, $_POST['booking_id']) ? 1 : 0;
                break;
        }
    }
}

echo json_encode($result);
exit;
