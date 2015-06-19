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
    messages: 0,
    attacks: 0,
    title: '',

    constructor: function() {
        this.title = document.title;
    },

    addEvent: function () {
        this.events++;
        this.changeTitle();
    },

    addMessage: function () {
        this.messages++;
        this.changeTitle()
    },

    addAttack: function () {
        this.attacks++;
        this.changeTitle();
    },

    getTotalEvents: function() {
        return this.events + this.messages + this.attacks;
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
                case 3: document.title = '(' + that.getTotalEvents() + ') ' + that.title; break;
                default: worker.terminate();
            }
        }
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

//var options = {swfPath: '/js/teddy/fingerprint2/flash/compiled/FontList.swf', excludeUserAgent: false};
//var d1 = new Date();
//var fp = new Fingerprint2(options);
//fp.get(function(result) {
//    var d2 = new Date();
//    console.log("Time took to calculate the fingerprint: " + (d2 - d1) + "ms");
//    console.log(result);
//});