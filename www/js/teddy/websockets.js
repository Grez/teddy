(function () {

	var userId = Cookies.get('teddy.userId');
	var apiKey = Cookies.get('teddy.apiKey');
	//var userId = Math.floor(Math.random() * 3) + 1;
	//console.log("Hi, I'm #" + userId);
	//var userId = 1;

	//userId = 2;
	apiKey = 'apiKey';

	$.nette.ext('websockets', {

		init: function () {
			var self = this;
			self.conn = new WebSocket('ws://localhost:8080');

			self.conn.onopen = function(e) {
				console.log("Connection established!");
				self.authorize(userId, apiKey);
			};

			self.conn.onclose = function(e) {
				console.log("Terminated");
			};

			self.conn.onmessage = function(e) {
				var msg = JSON.parse(e.data);
				console.log(msg);
				if (msg.method === 'pm') {
					//$('#messagesUnread').text(msg.unreadMessages)
				}
			};

			//var poing = 1;
			//setInterval(function() {
			//	console.log('poing');
			//	self.msg('poing', 'Poing by ' + userId + ' #' + poing);
			//	poing++;
			//}, 2500);
		}

	}, {

		conn: false,
		msg: function (method, msg) {
			var req = {
				method: method,
				data: msg,
			};
			var json = JSON.stringify(req, null, 2);
			this.conn.send(json);
		},
		authorize: function (userId, apiKey) {
			var msg = { userId: userId, apiKey: apiKey };
			this.msg('authorize', msg);
		}

	});

})();
