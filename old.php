<?php


require_once "../config.php";

use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;

// Sanity checks
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;
$path = $CFG->getPWD('index.php');
$post_url = str_replace('\\','/',addSession($CFG->getCurrentFileURL('api.php')));

// View
$OUTPUT->header();
?>
    <link rel="stylesheet" type="text/css" href="<?= $CFG->staticroot ?>/css/app.css"/>
    <link rel="stylesheet" type="text/css" href="<?= addSession("test.css") ?>"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
<?php
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

$date_now = new DateTime();

$json = '[{"month": 6, "bookings": "1" }]';

if ( $USER->instructor ) { ?>
<?php
}

?>

<div id="iframe-dialog" title="Read Only Dialog" style="display: none;">
   <iframe name="iframe-frame" style="height:400px" id="iframe-frame"
    src="<?= $OUTPUT->getSpinnerUrl() ?>"></iframe>
</div>

<div id="application">

    <ul class="nav nav-tabs">
        <!--li><h1 style="margin-top: 0px;margin-bottom: 0px;padding-right: 20px;">One Button Studio: </h1></li-->
        <li class="active"><a href="#" rel="schedule">Schedule</a></li>
        <li><a href="#" rel="rec">Recordings</a></li>
    </ul>

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

    <div id="schedule" class="tab-content">
        <div class="row">
            <div class="col-xs-9 text-center" style="padding-top: 12px;margin-bottom: 0px;">
                <h4>
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    June, 2017 
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </h4>
            </div>
            <div id="timer-container" class="col-xs-3">
                <ul class="nav nav-pills" style="float: right;margin-top: 20px;">
                    <li role="presentation"><a href="#">Week</a></li>
                    <li role="presentation" class="active"><a href="#">Month</a></li>
                    <li role="presentation" style="border-left: 1px solid #ccc; margin-left: 15px; padding-left:8px;"><a href="#">Today</a></li>
                </ul>
            </div>
        </div>

        <div>
            <div class="row">
                <div class="col-xs-9" id="calendar" style="padding-right: 0px;"></div>
                <div class="col-xs-3" style="padding-left: 0px;">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left:10px;">My Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div id="agenda" class="record">
                                        <div class="time">June 5, 2017&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;13:30 - 14:15</div>
                                        <div class="title">Using Opencast as the cornerstone for the delivery of interactive, annotated multimedia learning, in Sakai and Tsugi</div>
                                    </div>
                                    <div style="padding:2em 0px" class="text-center">
                                        <button class="btn btn-success"><i class="glyphicon glyphicon-plus" style="margin-right: 8px;"></i>Add a booking...</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

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
                            <div class="box used"></diV>
                            <div class="text">Available</diV>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Using Opencast as the cornerstone for the delivery of interactive, annotated multimedia learning, in Sakai and Tsugi</h4>
      </div>
      <div class="modal-body">
         <form>
            <div class="row nopadding" style="margin-bottom: 10px;">
                <div class="col-xs-2 text-right">
                    <label for="exampleInputEmail1" style="margin-bottom: 0px;margin-top: 7px;padding-right: 8px;">Title</label>
                </div>
                <div class="col-xs-10" style="padding-right: 15px;">
                    <input class="form-control" id="exampleInputEmail1" placeholder="Title" value="Using Opencast as the cornerstone for the delivery of interactive, annotated multimedia learning, in Sakai and Tsugi." type="text">
                </div>
            </div>
            <div class="row nopadding" style="margin-bottom: 10px;">
                <div class="col-xs-2 text-right">
                    <label for="exampleInputEmail1" style="margin-bottom: 0px;margin-top: 7px;padding-right: 8px;">Date</label>
                </div>
                <div class="col-xs-10" style="padding-right: 15px;">
                      <div class="input-group" style="width:100%">
                        <input class="form-control" id="exampleInputEmail1" placeholder="Title" value="June 5, 2017" style="width:150px;float:left;display:inline-block" type="text">
                        <button class="btn" type="button" style="float:left;display:inline-block; border-radius: 0px 4px 4px 0px;"><i class="fa fa-calendar-o" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
            <div class="row nopadding" style="margin-bottom: 10px;">
                <div class="col-xs-2 text-right">
                    <label for="exampleInputPassword1" style="margin-bottom: 0px;margin-top: 7px;padding-right: 8px;">Time Slot</label>
                </div>
                <div class="col-xs-10" style="padding-right: 15px;">
                    <button class="btn" type="button"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>

                    <div class="input-group" style="float:left; display:inline-block; width:120px">
                        <input class="form-control" id="exampleInputPassword1" value="13" style="float:left; width:45px;border-right-width:0px;" type="text">
                        <div class="input-group-addon" style="width:20px;height: 34px;float:left;">:</div>
                        <input class="form-control" id="exampleInputPassword1" value="30" style="float:left; width:45px; border-left-width:0px;" type="text">
                    </div>

                    <div style="float:left;display:inline-block;padding-left: 16px;padding-right: 16px;font-size: 30px;font-size: 22px;">-</div>

                    <div class="input-group" style="float:left; display:inline-block; width:140px">
                        <input class="form-control" id="exampleInputPassword1" value="14" style="float:left; width:45px;border-right-width:0px;" readonly="true" type="text">
                        <div class="input-group-addon" style="width:20px;height: 34px;float:left;">:</div>
                        <input class="form-control" id="exampleInputPassword1" value="15" style="float:left; width:45px; border-left-width:0px;" readonly="true" type="text">
                    </div>

                    <button class="btn btn-primary" type="button"><i class="fa fa-clock-o" aria-hidden="true"></i></button>

                    <button class="btn" type="button"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                </div>
            </div>
            <div class="row nopadding" style="margin-bottom: 10px;">
                <div class="col-xs-2 text-right">
                    <label for="exampleInputPassword1" style="margin-bottom: 0px;margin-top: 7px;padding-right: 8px;">Options</label>
                </div>
                <div class="col-xs-5" style="padding-right: 15px;">

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" checked="true"> Upload presentation
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox"> Table
                        </label>
                    </div>   
                </div>
                <div class="col-xs-5" style="padding-right: 15px;">
                    <div style="background: url('http://localhost/tsugi-static/img/pres.png') center center; width:230px; height:129px;text-align:center">
                        <button class="btn" style="margin-top: 50px;">Change...</button>
                    </div>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">

          <div class="row">
                <div class="col-xs-10 col-xs-offset-2">
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        
      </div>
    </div>
  </div>
</div>

</div>

<?php
/*
if(strlen($json) < 1 ) {
    echo("<p>Not yet configured</p>\n");
} else {
?>
<p>Configuration:</p>
<pre>
<?= $json ?>
</pre>
<?php
}
*/
?>

<?php

$OUTPUT->footerStart();

?>
    <script type="text/javascript">    
        function getObj(id, arr, key) { key = key || 'id'; var o = null; $.each(groups, function (i, el) { if (el[key] == id) { o=el; return; } }); return o; };
        var raw = <?= $json ?>;

    var arr_days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var arr_months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var arr_months_short = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var days_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    
    // this is the current date
    var current = new Date(); 

    $(function () {
        
        //var cal = new Calendar();
        //cal.generateHTML();
        $('#calendar').html(tmpl('tmpl-calendar', {date: new Date()}));
        $('#d_6_5_11').addClass("used");
        $('#d_6_5_13').addClass("me");

        // Smiley
        //$('#d_6_16_12, #d_6_16_13, #d_6_16_15, #d_6_16_16').addClass("used");
        //$('#d_6_17_11, #d_6_17_12, #d_6_17_14, #d_6_17_15').addClass("used");
        //$('#d_6_23_11, #d_6_23_15, #d_6_23_16, #d_6_24_14, #d_6_24_15, #d_6_24_13').addClass("used");

        // var j=[]; $('.used').each(function(i, el){ j.push('#'+$(this).attr('id')); }); j.join(", ");
        /*
        $('.calendar-table td > div > div').click(function(){ 

            if (!$(this).hasClass("me")) {
                $(this).toggleClass("used"); 
            }
        });
        */
        $("#d_6_1_12, #d_6_2_10, #d_6_2_12, #d_6_3_11, #d_6_4_12, #d_6_5_11, #d_6_6_9, #d_6_7_8, #d_6_7_11, #d_6_7_12, #d_6_8_8, #d_6_8_9, #d_6_9_8, #d_6_9_12, #d_6_10_15, #d_6_11_9, #d_6_11_15, #d_6_12_8, #d_6_12_11, #d_6_13_11, #d_6_13_12, #d_6_13_15, #d_6_14_14, #d_6_15_8, #d_6_15_9, #d_6_15_10, #d_6_16_12, #d_6_16_13, #d_6_16_15, #d_6_16_16, #d_6_17_11, #d_6_17_12, #d_6_17_14, #d_6_17_15, #d_6_19_8, #d_6_19_9, #d_6_19_12, #d_6_20_8, #d_6_20_12, #d_6_20_13, #d_6_23_11, #d_6_23_15, #d_6_23_16, #d_6_24_13, #d_6_24_14, #d_6_24_15, #d_6_25_10, #d_6_25_15, #d_6_26_13, #d_6_26_14, #d_6_26_15, #d_6_26_16, #d_6_27_8, #d_6_27_10, #d_6_28_11, #d_6_29_8, #d_6_29_9, #d_6_29_10").addClass("used");

        $('#agenda, #d_6_5_13').click(function(event){ event.preventDefault(); $('#myModal').modal('show'); });

        $(".nav-tabs").on('click','a', function(event){
            event.preventDefault();
            $('.nav-tabs li').removeClass('active');
            $(this).parent().addClass('active');
            $('.tab-content').hide();
            $('#' + $(this).prop('rel')).show();
        });
    });
    </script>


    <script type="text/x-tmpl" id="tmpl-calendar">
        {%
        var CurrentYear = y = 2017;
        var CurrentMonth = m = 5;
        var f='dd/mm/yyyy';

        typeof(y) == 'number' ? CurrentYear = y: null;
        typeof(y) == 'number' ? CurrentMonth = m: null;

        // 1st day of the selected month
        var firstDayOfCurrentMonth = new Date(y, m, 1).getDay();

        // Last date of the selected month
        var lastDateOfCurrentMonth = new Date(y, m + 1, 0).getDate();
        var lastDateOfLastMonth = m == 0 ? new Date(y - 1, 11, 0).getDate() : new Date(y, m, 0).getDate();
        var lastMonth = arr_months_short[ (m-1)%11 ];
        var stcurrentMonth = arr_months_short[ m ];
        var nextMonth = arr_months_short[ (m+1)%11 ];

        %}
        <table class="calendar-table">
            <thead>
                <tr>
                    {% for(var i = 0, len = arr_days.length-1; i <= len; i++ ){ %}<th>{%=arr_days[i]%}</th>{% } %}
                </tr>
            </thead>
            <tbody>
        {%

        var p = dm = f == 'M' ? 1 : firstDayOfCurrentMonth == 0 ? -5 : 2;

        var klen = 8;
        var cellvalue;
        for (var d, i = 0, z0 = 0; z0 < 6; z0++) { %}<tr>{%

            for (var z0a = 0; z0a < 7; z0a++) {
                d = i + dm - firstDayOfCurrentMonth;

                // Dates from prev month
                if (d < 1) {
                    cellvalue = lastDateOfLastMonth - firstDayOfCurrentMonth + (p++);
                    %}<td class="prev">
                        <div class="day">{%=(cellvalue)%}</div>
                        <div>
                            {% for (var k=0; k <= klen; k++) { %}<div id="d_{%=(m-1)%}_{%=cellvalue%}_{%=(k+8)%}"></div>{% } %}
                        </div>
                    </td>{%                     

                    lastMonth = "";
            
                } else if (d > lastDateOfCurrentMonth) {
                    
                    p++;
                    %}<td class="next">
                        <div class="day">{%=(nextMonth != "" ? nextMonth +" ":"") + (p)%}</div>
                        <div>
                            {% for (var k=0; k <= klen; k++) { %}<div id="d_{%=m%}_{%=p%}_{%=(k+8)%}"></div>{% } %}
                        </div>
                    </td>{%             

                    nextMonth = "";
                } else {

                     %}<td class="current">
                        <div class="day">{%=(stcurrentMonth != "" ? stcurrentMonth +" ":"") + (d)%}</div>
                        <div>
                            {% for (var k=0; k <= klen; k++) { %}<div id="d_{%=(m+1)%}_{%=d%}_{%=(k+8)%}"></div>{% } %}
                        </div>
                    </td>{%

                    p = 1;
                    stcurrentMonth = '';
                }


                if (i % 7 == 6 && d >= lastDateOfCurrentMonth) {
                     z0 = 10; // no more rows
                }

                i++;
            }

            %}</tr>{%
        } 
        %}
        </tbody></table>
    </script>    


<?php
$OUTPUT->footerEnd();

