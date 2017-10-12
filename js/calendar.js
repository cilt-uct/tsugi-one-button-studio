// Show month (year, month)
function GetCalendar (y, m) {
    console.log(" ");
    console.log("Calendar.prototype.Calendar = function(y,m){");

    typeof(y) == 'number' ? this.CurrentYear = y: null;

    console.log("this.CurrentYear == ", this.CurrentYear);
    
    typeof(y) == 'number' ? this.CurrentMonth = m: null;

    console.log("this.CurrentMonth == ", this.CurrentMonth);

    // 1st day of the selected month
    var firstDayOfCurrentMonth = new Date(y, m, 1).getDay();

    console.log("firstDayOfCurrentMonth == ", firstDayOfCurrentMonth);

    // Last date of the selected month
    var lastDateOfCurrentMonth = new Date(y, m + 1, 0).getDate();

    console.log("lastDateOfCurrentMonth == ", lastDateOfCurrentMonth);

    // Last day of the previous month
    console.log("m == ", m);

    var lastDateOfLastMonth = m == 0 ? new Date(y - 1, 11, 0).getDate() : new Date(y, m, 0).getDate();

    console.log("lastDateOfLastMonth == ", lastDateOfLastMonth);

    console.log("Print selected month and year.");

    // Write selected month and year. This HTML goes into <div id="year"></div>
    //var yearhtml = '<span class="yearspan">' + y + '</span>';

    // Write selected month and year. This HTML goes into <div id="month"></div>
    //var monthhtml = '<span class="monthspan">' + this.Months[m] + '</span>';

    // Write selected month and year. This HTML goes into <div id="month"></div>
    var monthandyearhtml = '<span id="monthandyearspan">' + this.Months[m] + ' - ' + y + '</span>';

    console.log("monthandyearhtml == " + monthandyearhtml);

    var html = '<table>';

    // Write the header of the days of the week
    html += '<tr>';

    console.log(" ");

    console.log("Write the header of the days of the week");

    for (var i = 0; i < 7; i++) {
        console.log("i == ", i);

        console.log("this.DaysOfWeek[i] == ", this.DaysOfWeek[i]);

        html += '<th class="daysheader">' + this.DaysOfWeek[i] + '</th>';
    }

    html += '</tr>';

    console.log("Before conditional operator this.f == ", this.f);

    //this.f = 'X';

    var p = dm = this.f == 'M' ? 1 : firstDayOfCurrentMonth == 0 ? -5 : 2;

    /*var p, dm;

    if(this.f =='M') {
        dm = 1;

        p = dm;
    } else {
        if(firstDayOfCurrentMonth == 0) {
        firstDayOfCurrentMonth == -5;
        } else {
        firstDayOfCurrentMonth == 2;
        }
    }*/

    console.log("After conditional operator");

    console.log("this.f == ", this.f);

    console.log("p == ", p);

    console.log("dm == ", dm);

    console.log("firstDayOfCurrentMonth == ", firstDayOfCurrentMonth);

    var cellvalue;

    for (var d, i = 0, z0 = 0; z0 < 6; z0++) {
        html += '<tr>';

        console.log("Inside 1st for loop - d == " + d + " | i == " + i + " | z0 == " + z0);

        for (var z0a = 0; z0a < 7; z0a++) {
            console.log("Inside 2nd for loop");

            console.log("z0a == " + z0a);

            d = i + dm - firstDayOfCurrentMonth;

            console.log("d outside if statm == " + d);

            // Dates from prev month
            if (d < 1) {
                console.log("d < 1");

                console.log("p before p++ == " + p);

                cellvalue = lastDateOfLastMonth - firstDayOfCurrentMonth + p++;

                console.log("p after p++ == " + p);

                console.log("cellvalue == " + cellvalue);

                html += '<td id="prevmonthdates">' +
                    '<span id="cellvaluespan">' + (cellvalue) + '</span><br/>' +
                    '<ul id="cellvaluelist"><li>apples</li><li>bananas</li><li>pineapples</li></ul>' +
                    '</td>';

                // Dates from next month
            } else if (d > lastDateOfCurrentMonth) {
                console.log("d > lastDateOfCurrentMonth");

                console.log("p before p++ == " + p);

                html += '<td id="nextmonthdates">' + (p++) + '</td>';

                console.log("p after p++ == " + p);

                // Current month dates
            } else {
                html += '<td id="currentmonthdates">' + (d) + '</td>';

                console.log("d inside else { == " + d);

                p = 1;

                console.log("p inside } else { == " + p);
            }

            if (i % 7 == 6 && d >= lastDateOfCurrentMonth) {
                console.log("INSIDE if (i % 7 == 6 && d >= lastDateOfCurrentMonth) {");
                console.log("i == " + i);
                console.log("d == " + d);
                console.log("z0 == " + z0);

                z0 = 10; // no more rows
            }

            console.log("i before i++ == " + i);
            i++;
            console.log("i after i++ == " + i);
        }

        html += '</tr>';
    }

    // Closes table
    html += '</table>';
    document.getElementById("monthandyear").innerHTML = monthandyearhtml;
    document.getElementById(this.divId).innerHTML = html;
};