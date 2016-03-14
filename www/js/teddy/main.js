// http://daniel.steigerwald.cz (MIT Licensed)
var $class = function(definition) {
    var constructor = definition.constructor;
    var parent = definition.Extends;
    if (parent) {
        var F = function() { };
        constructor._superClass = F.prototype = parent.prototype;
        constructor.prototype = new F();
    }
    for (var key in definition) {
        constructor.prototype[key] = definition[key];
    }
    constructor.prototype.constructor = constructor;
    return constructor;
};

var TeddyEvents = $class({

    events: 0,
    notifications: 0,
    unreadMessages: 0,
    title: '',

    constructor: function() {
        this.title = document.title;
        this.notifications = $('#teddy-events-notifications').text();
        this.unreadMessages = $('#teddy-events-unreadMessages').text();
        this.events = this.getTotalEvents();
        document.title = this.finalTitle();
    },

    setNotifications: function (notifications) {
        this.notifications = parseInt(notifications);
        this.update();
    },

    setUnreadMessages: function (unreadMessages) {
        this.unreadMessages = parseInt(unreadMessages);
        this.update();
    },

    getTotalEvents: function() {
        return parseInt(this.notifications) + parseInt(this.unreadMessages);
    },

    update: function () {
        if (this.events !== this.getTotalEvents()) {
            this.events = this.getTotalEvents();
            this.changeTitle();
        }
    },

    finalTitle: function () {
        return this.getTotalEvents() > 0 ? '(' + this.getTotalEvents() + ') ' + this.title : this.title;
    },

    changeTitle: function () {
        // Chrome allows setTimeout only 1/s when tab is inactive
        // We need to use Web Workers ^^
        var that = this;
        var worker = new Worker('/js/workers/teddy/setInterval.js');

        worker.onmessage = function(e) {
            switch (e.data.tick) {
                case 0: document.title = '(.) ' + this.title; break;
                case 1: document.title = '(..) ' + this.title; break;
                case 2: document.title = '(...) ' + this.title; break;
                case 3: document.title = this.finalTitle(); break;
                default: worker.terminate();
            }
        };
        worker.postMessage(250);
    }

});

var events = new TeddyEvents();

$(function() {
    $('input[data-nella-date-format]').datepicker({
        autoclose: true,
        format: 'dd.mm.yyyy',
        language: 'cs',
        weekStart: 1,
        orientation: 'top',
    });
});

// Fingerprint
// Maybe make in iframe and allow fonts?
if (Cookies.get('fingerprint') === undefined) {
    var options = {
        excludeJsFonts: true,
        excludeFlashFonts: true,
    };
    var fp = new Fingerprint2(options);
    fp.get(function (result) {
        var d2 = new Date();
        Cookies.set('fingerprint', result, {expires: 1});
    });
}


/**
 * Nette.ajax.js
 */
$(function () {
    $.nette.init();
});
