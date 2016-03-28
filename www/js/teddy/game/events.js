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
				case 0: console.log(this); document.title = '(.) ' + that.title; break;
				case 1: document.title = '(..) ' + that.title; break;
				case 2: document.title = '(...) ' + that.title; break;
				case 3: document.title = that.finalTitle(); break;
				default: worker.terminate();
			}
		};
		worker.postMessage(250);
	}

});

window.events = new TeddyEvents();
