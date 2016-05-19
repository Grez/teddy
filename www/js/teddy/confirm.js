// For default requests
$(function() {
	$('body').on('click', '[data-confirm]', function (e) {
		var confirm = window.confirm($(this).attr('data-confirm'));
		if (!confirm) {
			e.stopImmediatePropagation();
			e.preventDefault();
			return false;
		}
	});
});

// For ajax requests
$.nette.ext('confirm', {
	before: function (xhr, settings) {
		var analyze = settings.nette;
		if (!analyze) return;
		var e = analyze.e;
		var $el = $(analyze.el);

		if ($el.is('[data-confirm]')) {
			var confirm = window.confirm($el.data('confirm'));
			if (!confirm) {
				e.stopImmediatePropagation();
				e.preventDefault();
				return false;
			}
		}
	}
});
