INSERT INTO tsugi.booking_day_template 
                     (`id`,`template`) VALUES  
                     (1,'[{"id": 1, "start": "09:00", "end": "9:45"}, 
                     {"id": 2, "start": "10:00", "end": "10:45"},  
                     {"id": 3, "start": "11:00", "end": "11:45"}, 
                     {"id": 4, "start": "12:00", "end": "12:45"}, 
                     {"id": 5, "start": "13:00", "end": "13:45"}, 
                     {"id": 6, "start": "14:00", "end": "14:45"}, 
                     {"id": 7, "start": "15:00", "end": "15:45"}]');

INSERT INTO tsugi.booking_venue 
                     (`id`,`CA`,`name`,`color`,`active`) VALUES  
                     (1,'OBS1-CA','OB1','#3E4A89',1), 
                     (2,'OBS2-CA','OB2','#25828E',0), 
                     (3,'OBS3-CA','Podcast','#6DCD59',0);
                     