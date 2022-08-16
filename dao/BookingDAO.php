<?php
namespace Booking\DAO;

### Booking Data Object
# - Make / Update  : setBooking
# - Delete Booking : deleteBooking

class BookingDAO {

    private $PDOX;
    private $p;

    public function __construct($PDOX, $p) {
        $this->PDOX = $PDOX;
        $this->p = $p;
    }

    function getBooking() {
        // global $CFG, $PDOX;
        // $p = $CFG->dbprefix;
        // $id = str_replace('/booking/', '', $request->getPathInfo());

        // $response = array( 'status' => false, 'id' => $id, 'data' => []);
        
        // if ( $id > 0 ) {
            
        //     $rows = $PDOX->allRowsDie("SELECT booking_id as 'id', title, settings, DATE(booking_start) as 'date', TIME_FORMAT(booking_start, '%H') as 'time' FROM {$p}booking
        //         WHERE link_id = :LI and (user_id = :UI) and booking_id = :BOOKING_ID",
        //         array(':LI' => $app['tsugi']->link->id,
        //             ':UI' => $app['tsugi']->user->id,
        //             ':BOOKING_ID' => $id)
        //     );

        //     $response['status'] = true;
        //     $response['data'] = $rows;
        // } else if ($id == "month") {

        //     $month = $request->get('month');

        //     $rows = $PDOX->allRowsDie("SELECT booking_id as 'id', (user_id = :UI) as 'isme', DATE(booking_start) as 'date', TIME_FORMAT(booking_start, '%H') as 'time' FROM {$p}booking
        //         WHERE link_id = :LI and booking_start between :prevPeriod and :nextPeriod ORDER BY booking_start",
        //         array(':LI' => $app['tsugi']->link->id,
        //             ':UI' => $app['tsugi']->user->id,
        //             ':prevPeriod' => $month .'-01 00:00:00',
        //             ':nextPeriod' => $month .'-31 23:59:59')
        //     );

        //     $response['status'] = true;
        //     $response['data'] = $rows;            
        // }

        // return json_encode($response);
    }


    function setBooking($link_id, $user_id, 
                            $user_name, $user_email, $EID,
                            $time, $date, $venue,
                            $course, $department, $title, $category, $desc, $terms, $approval, $booking_id = 0) {

        # if $booking_id == 0  create else update

        # If the booking_day has not been filled in yet then: 
        # - get the booking template for the venue
        # - populate booking_day with the structure
        
        # get the booking day id (slot) from booking_day

        $SQL = "INSERT INTO `tsugi`.`booking` " .
                    "(`link_id`, `user_id`, `EID`, `booking_start`, `booking_end`, " .
                    " `email`, `title`, `settings`, " .
                    " `created_at`, `created_by`, `updated_at`, `updated_by`, `venue`, `agreed`, `need_approval`) ".
                    " VALUES " .
                    " (:linkId, :userId, :EID, :start, :end, :email, :title, :settings, " .
                    "   NOW(), :userId, NOW(), :userId, :venue, :terms, :approval)";
                                                                                   
        $arr = array(':linkId' => $link_id, ':userId' => $user_id, ':EID' => $EID, 
                        ':email' => $user_email, ':title' => $title, ':venue' => $venue, 
                        ':terms' => $terms, ':approval' => $approval,
                        ':start' => $date .' '. str_replace("h", ":", $time) . ":00", 
                        ':end'   => $date .' '. str_replace("h00", ":", $time) . "45:00", 
                        ':settings' => $date .' '. $time);

        $success = $this->PDOX->queryDie($SQL, $arr);
        return ['success' => $success, 'msg' => $success ? 'done' : 'could not create booking'];
    }

    function deleteBooking($link_id, $user_id, $booking_id) {
        # not implemented (are we doing soft deletes ?)
    }

    function
}