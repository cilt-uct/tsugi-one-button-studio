<?php class_exists('Template') or exit; ?>
<div id="application">

    <ul class="nav nav-tabs">
        <!--li><h1 style="margin-top: 0px;margin-bottom: 0px;padding-right: 20px;">One Button Studio: </h1></li-->
        <?php if ($isAdmin) { ?><?php } ?>
        <li class="active"><a href="#" rel="admin">Dashboard</a></li>
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
            <div id="timer-container" class="col-xs-3">
                <ul class="nav nav-pills" style="float: right;margin-top: 20px;">
                    <!--li role="date-filter"><a href="#">Week</a></li-->
                    <li role="date-filter" class="active"><a href="#">Month</a></li>
                    <li role="date-filter" data-goto="today" style="border-left: 1px solid #ccc; margin-left: 15px; padding-left:8px;"><a href="#">Today</a></li>
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
    
    <?php if ($isAdmin) { ?><?php } ?>
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

</div><!-- end application -->