/**
 * Chrome doesn't allows setTimeout only 1/s when tab is inactive
 * This allows you to do that in the background
 * @return {tick, time} (# of tick, total time elapsed)
 */

var interval = 0;
var tick = 0;

self.onmessage = function(e) {
    interval = e.data;
    setInterval(function() {
        self.postMessage({tick: tick, time: tick * interval});
        tick++;
    }, interval);
}