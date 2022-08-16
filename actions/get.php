<?php
require_once "../../config.php";
include '../tool-config.php';

require_once "../dao/BookingDAO.php";

use \Tsugi\Core\LTIX;
use \Booking\DAO\BookingDAO;

### return information for requested bookings
# - getBookings : day + time details for ALL bookings - in Week or Month
# - getBooking  : get details of specific booking
# - getFiles    : get attachments of a booking

# - getAllPersonalSeries : retrieve personal series details