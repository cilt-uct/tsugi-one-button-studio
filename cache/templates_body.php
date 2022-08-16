<?php class_exists('Template') or exit; ?>
<div id="application">

    <ul class="nav nav-tabs">
        <?php if ($isAdmin) { ?>
            <li class="active"><a href="#" rel="admin">Dashboard</a></li>
        <?php } ?>
        <li <?php if (!$isAdmin) { ?> class="active" <?php } ?>><a href="#" rel="schedule">Schedule</a></li>
        <li><a href="#" rel="rec">Recordings</a></li>
    </ul>

    <div id="schedule" class="tab-content" <?php if ($isAdmin) { ?>style="display:none"<?php } ?>>
        <div class="row">
            <div class="col-xs-9 text-center" style="padding-top: 12px;margin-bottom: 0px;">
                <h4 id="month-title-container">
                    <a href="#" class="glyphicon glyphicon-chevron-left"></a>
                    <span id="month-title"></span>
                    <a href="#" class="glyphicon glyphicon-chevron-right"></a>
                </h4>
            </div>
            <!--div id="timer-container" class="col-xs-3">
                <ul class="nav nav-pills" style="float: right;margin-top: 20px;">
                    <!- -li role="date-filter"><a href="#">Week</a></li- ->
                    <li role="date-filter" class="active"><a href="#">Month</a></li>
                    <li role="date-filter" data-goto="today" style="border-left: 1px solid #ccc; margin-left: 15px; padding-left:8px;"><a href="#">Today</a></li>
                </ul>
            </div-->
        </div>

        <div>
            <div class="row">
                <div class="col-xs-9" id="calendar" style="padding-right: 0px;"></div>
                <div class="col-xs-3" style="padding-left: 0px;">
                    
                    <h4 class="my_bookings">
                        <button id="add_booking" class="btn btn-success" title="Add a booking ...">
                            <i class="fas fa-plus-circle"></i>
                        </button>
                        My Bookings
                    </h4>
                    <div id="my_bookings">
                        <div id="agenda" class="record">
                            <div class="time">June 5, 2017&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;13:30 - 14:15</div>
                            <div class="title">Using Opencast as the cornerstone for the delivery of interactive, annotated multimedia learning, in Sakai and Tsugi</div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="legend row">
                        <div class="col-xs-12">
                            <div class="box"></diV>
                            <div class="text">Unavailable</diV>
                        </div>
                        <div class="col-xs-12">
                            <div class="box me"></diV>
                            <div class="text">My bookings</diV>
                        </div>
                        <div class="col-xs-12">
                            <div class="box used">
                                <div id="triangle-topleft"></div>
                            </diV>
                            <div class="text">Available</diV>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="rec" class="tab-content" style="display:none">
        <div>
            <table id="grid" class="table table-bordered table-hover" style="margin-top: 10px;">
                <thead>
                    <tr>
                    <th class="text-center" style="width: 1rem;">
                    </th>
                    <th style="width: 160px;">&nbsp;</th>
                    <th data-column="title" data-sort="true" data-sortdir="asc" style="width: calc((100% - 30rem)/3)">Title</th>
                    <th data-column="start_date" data-sort="true" data-sortdir="asc" data-sorttype="date" style="width: calc((100% - 30rem)/3)">Date</th>
                    <th data-column="status" data-sort="true" data-sortdir="asc" style="width: 10em">Status</th>
                    <th style="width: 7em">Manage</th>
                </tr></thead>
                <tbody id="grid-body">
            
                    <tr id="row-0375c238-e079-4db4-83f8-d1cdac37d9ba" data-status="Published">
                        <td class="text-center">
                        <input type="checkbox" id="chk-0375c238-e079-4db4-83f8-d1cdac37d9ba">
                        </label></td>
                        <td><img src="http://localhost/tsugi-static/img/1.png" class="img-responsive" style="border: 1px solid #222;"/></td>
                        <td>Teaching and Learning (Take 1)</td>
                        <td>Thu 25 May 2015, 13:00</td>
                        <td>Ready</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($isAdmin) { ?>
        <div id="admin" class="tab-content">
            <div id="dashboard">
                <article id="view" style="height:360px;">
                </article>
                <aside id="summary">
                    <!-- second chart group -->
                    <div class="chart-block" style="padding:28px">
                        120 <div id="line2" style="vertical-align: middle; display: inline-block; width: 100px; height: 30px;"></div> 
                        6% <div id="column2" style="vertical-align: middle;display: inline-block; width: 110px; height: 30px;"></div>
                    </div>
                </aside>
            </div>

            <pre><?php echo $display_settings ?></pre>
            <pre><?php echo $json ?></pre>
        </div>
    <?php } ?>

    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
        Launch demo modal
      </button>

    <!-- Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="bookingModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="booking_form">
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="form-group required">
                                    <label for="input_name" class="col-sm-2 control-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="input_name" name="input_name" placeholder="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="form-group required">
                                    <label for="input_eid" class="col-sm-4 control-label">Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="input_eid" name="input_eid" placeholder="Staff/Student Number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7">           
                                <div class="form-group required">
                                    <label for="input_email" class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="input_email" name="input_email" placeholder="Email" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Time</label>
                                    <div class="col-sm-10">
                                        <table>
                                            <tr>
                                                <th>
                                                    09:00
                                                </th>
                                                <th>
                                                    10:00
                                                </th>
                                                <th>
                                                    11:00
                                                </th>
                                                <th>
                                                    12:00
                                                </th>
                                                <th>
                                                    13:00
                                                </th>
                                                <th>
                                                    14:00
                                                </th>
                                                <th>
                                                    15:00
                                                </th>                                                                                                                                                                                                                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_09" value="09h00" aria-label="09h00">
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_10" value="10h00" aria-label="10h00" checked>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_11" value="11h00" aria-label="11h00">
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_12" value="12h00" aria-label="12h00">
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_13" value="13h00" aria-label="13h00">
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_14" value="14h00" aria-label="14h00">
                                                    </label>
                                                </td>
                                                <td>
                                                    <label>
                                                        <input type="radio" name="input_time" id="input_time_15" value="15h00" aria-label="15h00">
                                                    </label>
                                                </td>                                                                                                                                                                                                                                                
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="form-group required">
                                    <label for="input_date" class="col-sm-4 control-label">Date</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="input_date" name="input_date" 
                                            required pattern="\d{4}-\d{2}-\d{2}" value="2022-08-06"/>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label for="input_venue" class="col-sm-4 control-label">Venue</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="input_venue" name="input_venue">
                                            <option value="OBS1-CA" selected>OB 1</option>
                                            <option value="OBS2-CA">OB 2</option>
                                            <option value="OBS3-CA">Podcast</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-7">           
                                <div class="form-group required">
                                    <label for="input_course" class="col-sm-2 control-label">Course</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="input_course" name="input_course" placeholder="" value="test">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="form-group">
                                    <label for="input_dept" class="col-sm-4 control-label">Department</label>
                                    <div class="col-sm-8">
                                        <select name="input_dept" id="input_dept" id="input_name" class="form-control">
                                            <option>Please select ...</option>
                                            <option>Dept 1</option>
                                            <option>Dept 2</option>
                                            <option>Dept 3</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-7">           
                                <div class="form-group required">
                                    <label for="input_title" class="col-sm-2 control-label">Title</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="input_title" name="input_title" placeholder="" value="test">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-5">
                                <div class="form-group">
                                    <label for="input_category" class="col-sm-4 control-label">Category</label>
                                    <div class="col-sm-8">
                                        <select name="input_category" id="input_category" name="input_category" class="form-control">
                                            <option>Please select ...</option>
                                            <option>Type 1</option>
                                            <option>Type 2</option>
                                            <option>Type 3</option>
                                          </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-7">           
                                <div class="form-group">
                                    <label for="input_desc" class="col-sm-2 control-label"> </label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="input_desc" name="input_desc" rows="3"
                                        placeholder="Description of this booking"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                          <div class="col-sm-offset-1 col-sm-10">
                            <div class="checkbox">
                              <label for="input_terms">
                                <input type="checkbox" id="input_terms" name="input_terms" value="yes" > I agree to the terms and conditions for this booking.
                              </label>
                            </div>
                          </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_submit_booking" class="btn btn-primary disabled" disabled="true">Make Booking</button>
                    <button type="button" id="bnt_cancel_nooking" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div><!-- end application -->