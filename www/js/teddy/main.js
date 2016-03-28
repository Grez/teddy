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
