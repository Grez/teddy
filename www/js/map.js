var map = $class({

    map: new Array(),
    size: 0,

    constructor: function(map) {
        this.map = map;
        this.size = sqrt(map);
    },

    getNeighbours: function(i) {
        var neighbours = [];

        if (i % this.size != 0) {
            neighbours.push(i - 1);
        }

        if (i % this.size != (this.size - 1)) {
            neighbours.push(i + 1);
        }

        if (i >= this.size) {
            neighbours.push(i - this.size);
        }

        if (i <= this.size * this.size - this.size) {
            neighbours.push(i + this.size);
        }

        return neighbours;
    },

    /*
    closest: function(i) {
        var minNeigh = undefined;
        var neighbours = this.getNeighbours(i);
        for (var j = 0; j < neighbours.length; j++) {
            if (minNeigh == undefined || this.map[minNeigh] > this.map[neighbours[j]]) {
                minNeigh = neighbours[j]
            }
        }
        console.log('min:', minNeigh);
    },
    */

    bf: function bf(target) {
        var dist = [];
        var prev = [];
        var toVisit = this.map;
    
    
        for (var i = 0; i < this.map.length; i++) {
            dist[i] = 999999;
            prev[i] = undefined;
        }
    
        dist[0] = 0;
        prev[0] = undefined;
    
        for (var i = 0; i < this.map.length - 1; i++) {
            var neighbours = this.getNeighbours(this.map, i);
            for (var j = 0; j < neighbours.length; j++) {
                if (this.map[neighbours[j]] != undefined) {
                    if (dist[i] > dist[neighbours[j]] + this.map[neighbours[j]]) {
                        dist[i] = dist[neighbours[j]] + this.map[neighbours[j]];
                        prev[i] = neighbours[j];
                    }
                }
            }
        }
    
        this.path(prev, target);
    },

    path: function(prev, target) {
        var i = 0;
        var path = target;
        while (prev[target] != undefined) {
            i++;
            path = prev[target] + ' -> ' + path;
            target = prev[target];
        }
        console.log('Path:', path);
        console.log(i);
    }
});