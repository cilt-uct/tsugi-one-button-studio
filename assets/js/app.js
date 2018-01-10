var num = ['st','nd','rd'], timespan = { year: 31536000, month: 2592000, week: 604800, day: 86400, h: 3600, m: 60, s: 1 };

String.prototype.upFirst = function(){ var s = this; return s.charAt(0).toUpperCase() + s.slice(1); };
function getObj(id, arr, key) { key = key || 'id'; var o = null; $.each(arr, function (i, el) { if (el[key] == id) { o=el; return; } }); return o; };
function getOption(_arr, _id) { return getObj(_id, _arr, 'id'); }

(function($) {
    "use strict";

	$.fn.student_selection = function(options) {
		var settings = $.extend({
            path: ""
        }, options || {});
        this.settings = settings;

        var self = $.extend(this, {
			init: function() {

            }
        });

        self.init();
		self.data('welcome', self);
        return self;
    };
});