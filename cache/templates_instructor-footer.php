<?php class_exists('Template') or exit; ?>
<?php foreach($scripts as $script): ?>
    <script src="<?php echo $script ?>" type="text/javascript"></script>
<?php endforeach; ?>

<script type="text/javascript">    


    $(function () {

    });
</script>

<script type="text/javascript">
    jQuery.fn.exists = function(){ return this.length > 0; }

    function getObj(id, arr, key) { key = key || 'id'; var o = null; $.each(arr, function (i, el) { if (el[key] == id) { o=el; return; } }); return o; };
    function getObjIndex(id, arr, key) { key = key || 'id'; var o = -1; $.each(arr, function (i, el) { if (el[key] == id) { o=i; return; } }); return o; };
    function getDisplayData(d, arr) { var o = null; $.each(arr, function (i, el) { if (el.date === d) { o=el; return; } }); return o; };

    var defaultLocaleWeekdaysShort = 'Sun_Mon_Tue_Wed_Thu_Fri_Sat'.split('_');
    var times = ['', '15:00', '14:00', '13:00', '12:00', '11:00', '10:00', '09:00', '08:00', '', ''];
    var raw = '[{"month": 6, "bookings": "1" }]';
   
    var initial_data = <?php echo $json ?>, data = [], current_date = new Date(), current = {}, refresh_id = null;

    var dates = [];
    var currDate = moment('<?php echo $prevPeriod ?>').startOf('day');
    var lastDate = moment('<?php echo $nextPeriod ?>').startOf('day');

    while(currDate.add(1, 'days').diff(lastDate) < 0) {
        var d_st = currDate.format('YYYY-MM-DD'),
            arr = initial_data.bookings.filter(z => z.date == d_st); 
        
        var o = {"date": currDate.format('YYYY-MM-DD'), "usage": arr.length / 8 * 100};
        for(var i = 8; i <= 15; i++) {
            o['am'+ i] = '1';
            o['am'+ i +'-color'] = arr.filter( z => z.time == (i < 10 ? '0'+i : i)).length == 0 ? "#ccc" : "#449D44"; 
        }
        dates.push(o);
    }
    
    function displayCurrent() {
        current = getDisplayData(current_date.format("YYYY-MM"), data);

        $('#month-title').html(current_date.format('MMMM, YYYY'));
        $('#calendar').html(tmpl('tmpl-calendar', {date: current_date, data: current}));
    }

    function refreshCurrent() {

        $.get('<?php echo $booking_get_current ?>&month=' + current_date.format("YYYY-MM"), function(data) {
            console.log(data);
        }, 'json');
    }

    $(function () {

        data.push(initial_data);

        current_date = moment(initial_data.date, "YYYY-MM");
        displayCurrent();

        //var refresh_id = window.setInterval(refreshCurrent, 400);

        $('#calendar').on('click', '.open div[id*=d_]:not(.me):not(.used)', function(event) {
            event.preventDefault();

            var _id = $(this).attr('id'),
                _date = moment(_id,'#_M_D_H');
            
            console.log(_date.format('YYYY-MM-DD HH:mm:ss'));
            
            $.post('<?php echo $booking_pre ?>', { title: 'Test', date: _date.format('YYYY-MM-DD HH:mm:ss') }, function( data ) {
               
               console.log(data.success);
                if (data.success) {
                    $('#myModal').modal('show');
                } else {
                    alert( "error" );
                }
            }, 'json').fail(function() {
                alert( "error" );
            });                
        });

        $('#calendar').on('click', 'div.me[id*=d_]', function(event) {
            event.preventDefault();
            
            var _id = $(this).attr('id'),
                _date = moment(_id,'#_M_D_H'),
                _o = initial_data.bookings.filter( z => (z.date == _date.format('YYYY-MM-DD')) && (z.time == _date.format('HH')) && (z.isme === "1"));

            if (_o.length == 1) {
                _o = _o[0];
                console.log(_o);

                $.get(('<?php echo $booking_get ?>').replace('{id}', _o.id), function(data){
                    console.log(data);

                    $('#myModal').modal('show');
                }).fail(function() {
                    alert( "error" );
                });
            }
        });


        $('#api form').on('submit', function(e) {
            e.preventDefault();
            var method = $(this).attr('method') || 'GET';
            var action = $(this).attr('action');
            var fd = new FormData();
            var hasData = false;
            $(this).find('input,textarea,select').each(function() {
                if (this.name && this.value) {
                    hasData = true;
                    fd.append(this.name, this.value);
                }
            });
            var ajaxOpts = {
                url: action,
                type: method,
                dataType: 'text'
            };

            if (hasData) {
                ajaxOpts.data = fd;
                    ajaxOpts.processData = false;
                    ajaxOpts.cache = false;
                    ajaxOpts.contentType = false;
            }
            $.ajax(ajaxOpts)
                .done(function(res) {
                  $(e.target).siblings('.serverResponse').text(res);
                })
                .fail(function(err) {
                  $(e.target).siblings('.serverResponse').text(JSON.stringify(err));
                });
        });

        $('#api form[data-baseaction]').on('input', 'input,textarea,select', function(e) {
            var alteredAction = $(this).parents('form').data('baseaction');
            var replacements = {};
            $(this).parents('form').find('[data-replace]').each(function() {
                replacements[$(this).data('replace')] = this.value;
            });
            for (var key in replacements) {
                if (replacements[key]) {
                    alteredAction = alteredAction.replace(key, replacements[key]);
                }
            }
            $(this).parents('form')
                .attr('action', alteredAction)
                .siblings('h4')
                    .find('[data-baseaction]').text(alteredAction.substring(0, alteredAction.indexOf('?PHPSESSID'))); //<-- handle GET params better
        });

        $('#month-title-container').on('click', '.glyphicon-chevron-left', function(event) {
            event.preventDefault();
            current_date.subtract(1, 'months');
            displayCurrent();
        });

        $('#month-title-container').on('click', '.glyphicon-chevron-right', function(event) {
            event.preventDefault();
            current_date.add(1, 'months');
            displayCurrent();
        });

        $(".nav-tabs").on('click','a', function(event){
            event.preventDefault();
            $('.nav-tabs li').removeClass('active');
            $(this).parent().addClass('active');
            $('.tab-content').hide();
            $('#' + $(this).prop('rel')).show();
        });

        $(".nav-pills").on('click','a', function(event){
            event.preventDefault();
            var li = $(this).parent(),
                goto = li.data('goto');
            console.log(goto);
            if(goto == 'today') {
                current_date = moment();
                displayCurrent();
            }
        });
       

        var graphs = [];
        for (var i = 15; i >= 8; i--) {
            graphs.push({
                "balloonText": "",
                "fillAlphas": 0.8,
                "fillColorsField": "am"+ i +"-color",
                "lineColorField": "am"+ i +"-color",
                "id": "graph-am"+ i,
                "lineAlpha": 0.2,
                "type": "column",
                "valueAxis": "Days",
                "valueField": "am"+ i
            });
        }
        graphs.push({
            "id": "graph-occ",
            "balloonText": "[[value]]",
            "fillColors": "#FF0000",
            "title": "graph 3",
            "valueAxis": "Occupancy",
            "valueField": "usage",
            "visibleInLegend": false
        });

        AmCharts.makeChart( "view", {
            "type": "serial",
            "categoryField": "date",
            "dataDateFormat": "YYYY-MM-DD",
            "sequencedAnimation": false,
            "pathToImages": "http://cdn.amcharts.com/lib/3/images/", // required for grips
            "categoryAxis": {
                "parseDates": true,
                "labelFrequency": 1,
                "fontSize": 11,
                "labelRotation": 90,	
                "gridAlpha": 0,
                "tickLength": 2,
                //"categoryFunction": function (category, dataItem, categoryAxis) {
                //    console.log(category);
                //    return moment(category).format("MMM DD");
                //}
            },
            "trendLines": [],
            "graphs": graphs,
            "guides": [],
            "chartScrollbar": {
                "graph": "graph-occ",
                "graphType": "line",
                "scrollbarHeight": 30
            },
            "valueAxes": [
                {
                    "id": "Days",
                    "maximum": 10,
                    "minimum": 0,
                    "stackType": "regular",
                    "autoGridCount": false,
                    "showFirstLabel": false,
                    "showLastLabel": false,
                    "title": "",
                    gridCount: 10,
                    labelFunction: function(value, valueText, valueAxis) {
                        return times[value];
                    }
                },
                {
                    "id": "Occupancy",
                    "maximum": 100,
                    "minimum": 0,
                    "position": "right",
                    "axisColor": "#666666",
                    "axisThickness": 0,
                    "title": "Occupancy %"
                }
            ],
            "allLabels": [],
            "balloon": {},
            "titles": [],
            "dataProvider": dates,
            "listeners": [{
                /*
                "event": "rendered",
                "method": function(e) {
                    e.chart.valueAxes[0].zoomToValues(moment().startOf('month').format('YYYY-MM-DD'), moment().endOf('month').format('YYYY-MM-DD'));
                }*/
                event: "rendered",
                method: function(e) {
                    var a = getObjIndex(moment().startOf('month').format('YYYY-MM-DD'), dates, 'date'),
                        b = getObjIndex(moment().endOf('month').format('YYYY-MM-DD'), dates, 'date');

                    e.chart.zoomToIndexes(a, b); //set default zoom
                }
            }]
        });

        /**
         * Line Chart #2
         */
        AmCharts.makeChart( "line2", {
            "type": "serial",
            "dataProvider": [ {
                "day": 1,
                "value": 120
            }, {
                "day": 2,
                "value": 54
            }, {
                "day": 3,
                "value": -18
            }, {
                "day": 4,
                "value": -12
            }, {
                "day": 5,
                "value": -51
            }, {
                "day": 6,
                "value": 12
            }, {
                "day": 7,
                "value": 56
            }, {
                "day": 8,
                "value": 113
            }, {
                "day": 9,
                "value": 142
            }, {
                "day": 10,
                "value": 125
            } ],
            "categoryField": "day",
            "autoMargins": false,
            "marginLeft": 0,
            "marginRight": 5,
            "marginTop": 0,
            "marginBottom": 0,
            "graphs": [ {
                "valueField": "value",
                "showBalloon": false,
                "lineColor": "#ffbf63",
                "negativeLineColor": "#289eaf"
            } ],
            "valueAxes": [ {
                "gridAlpha": 0,
                "axisAlpha": 0,
                "guides": [ {
                "value": 0,
                "lineAlpha": 0.1
                } ]
            } ],
            "categoryAxis": {
                "gridAlpha": 0,
                "axisAlpha": 0,
                "startOnAxis": true
            }
        });

        /**
         * Column Chart #2
         */
        AmCharts.makeChart( "column2", {
            "type": "serial",
            "dataProvider": [ {
                "day": 1,
                "value": -5
            }, {
                "day": 2,
                "value": 3
            }, {
                "day": 3,
                "value": 7
            }, {
                "day": 4,
                "value": -3
            }, {
                "day": 5,
                "value": 3
            }, {
                "day": 6,
                "value": 4
            }, {
                "day": 7,
                "value": 6
            }, {
                "day": 8,
                "value": -3
            }, {
                "day": 9,
                "value": -2
            }, {
                "day": 10,
                "value": 6
            } ],
            "categoryField": "day",
            "autoMargins": false,
            "marginLeft": 0,
            "marginRight": 0,
            "marginTop": 0,
            "marginBottom": 0,
            "graphs": [ {
                "valueField": "value",
                "type": "column",
                "fillAlphas": 1,
                "showBalloon": false,
                "lineColor": "#ffbf63",
                "negativeFillColors": "#289eaf",
                "negativeLineColor": "#289eaf"
            } ],
            "valueAxes": [ {
                "gridAlpha": 0,
                "axisAlpha": 0
            } ],
            "categoryAxis": {
                "gridAlpha": 0,
                "axisAlpha": 0
            }
        });
    });
</script>