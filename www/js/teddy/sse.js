$(function () {

	$.nette.ext('sse', {
		init: function () {
			var self = this;
			self.createSource('');
			console.log('wat');
		}
	}, {
		source: false,
		createSource: function (hash) {
			var self = this;
			this.source = new EventSource('/sse/default/' + hash);

			this.source.addEventListener('message', function(e) {
				this.close();
				var json = e.data;
				var data = JSON.parse(e.data);
				console.log(data);

				self.updatePms(data.pm);

				var hash = md5(json);
				self.createSource(hash);
			}, false);

			this.source.addEventListener('open', function(e) {
				// Connection was opened.
			}, false);

			this.source.addEventListener('error', function(e) {
				if (e.readyState == EventSource.CLOSED) {
					// Connection was closed.
					console.log('closed');
				}
			}, false);

			return this.source;
		},
		updatePms: function (data) {
			console.log(data);
			var unreadCount = data.unreadCount;
			$('#unreadMessages').text(unreadCount + ' nepřečtených zpráv');
		},
		buildDefaultHash: function () {

		}
	});

});
