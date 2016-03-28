$(function() {
	window.Pathfinding = $class({

		map: $('#mainMap'),
		graph: undefined,
		start: undefined,
		target: undefined,

		constructor: function(graph, start) {
			var self = this;

			this.graph = graph;
			this.setStart(start);

			this.map.find('.cell').click(function () {
				var target = self.getPositionFromCell($(this));
				self.setTarget(target);
			});
		},

		setStart: function (start) {
			this.start = start;
			var cell = this.getCellFromPosition(start);

			cell.addClass('start');
		},

		setTarget: function (target) {
			var cell = this.getCellFromPosition(target);
			this.target = target;

			this.erasePath();
			cell.addClass('target');

			var start = this.graph.grid[this.start[0]][this.start[1]];
			var end = this.graph.grid[this.target[0]][this.target[1]];
			var result = astar.search(graph, start, end);

			var weight = 0;
			for (var i = 0; i < result.length; i++) {
				var gridNode = result[i];
				var x = gridNode.x;
				var y = gridNode.y;
				$('.cell[data-virtual-x=' + x + '][data-virtual-y=' + y + ']').addClass('path');
				weight += result[i].weight;
			}
		},

		erasePath: function () {
			this.map.find('.path').removeClass('path');
			this.map.find('.target').removeClass('target');
		},

		getCellFromPosition: function (position) {
			var virtualX = position[0];
			var virtualY = position[1];
			return this.map.find('.cell[data-virtual-x=' + virtualX + '][data-virtual-y=' + virtualY + ']');
		},

		getPositionFromCell: function (cell) {
			var virtualX = cell.data('virtualX');
			var virtualY = cell.data('virtualY');
			return [virtualX, virtualY];
		}

	});
});
