<?php

class Map
{

    protected $map;


    public function setMap($map)
    {
        $this->map = $map;
    }

    public function unserialize($file)
    {
        $this->map = unserialize(file_get_contents(__DIR__ . '/'. $file));
    }

    public function getMap()
    {
        return $this->map;
    }

    protected function createEdges()
    {

    }

    public function getJs()
    {
        $lvls = [];
        foreach ($this->map as $x => $col) {
            foreach ($col as $y => $lvl) {
                $lvls[] = $lvl;
            }
        }
        echo 'var size = ' . count($this->map) . ';' . "\n";
        echo 'var map = [' . implode(',', $lvls) . '];' . "\n";
    }

    public function dijkstra($startX, $startY, $targetX, $targetY)
    {
        $dist = [];
        $prev = [];

        foreach ($this->map as $x => $row) {
            foreach ($row as $y => $lvl) {
                $dist[$x][$y] = 999;
                $prev[$x][$y] = -1;
            }
        }
        $dist[$startX][$startY] = 0;
    }

    protected function getNeighbours($x, $y)
    {
        $coords = [
            [$x, $y+1],
            [$x+1, $y+1],
            [$x-1, $y+1],
            [$x, $y-1],
            [$x+1, $y-1],
            [$x-1, $y-1],
            [$x+1, $y],
            [$x-1, $y],
        ];

        foreach ($coords as $key => $coord) {
            if ($coord[0] < 0 || $coord[0] >= $this->x || $coord[1] < 0 || $coord[1] >= $this->y) {
//                echo '<pre>';
//                echo $x .'-'. $y;
//                print_r($coord);
//                exit;
                unset($coords[$key]);
            }
        }

        return $coords;
    }

}



function dijkstra(Map $map)
{
    $s = '0,0';
    $target = '15,15';
    $dist = [];
    $previous = [];
    foreach ($map as $vertex => $lengths) {
        $dist[$vertex] = 9999;
        $previous[$vertex] = -1;
    }

    $dist[$s] = 0;
    $n = $map->getMap();
    while(count($n)) {
        $min_key = findMin(array_keys($n), $dist);
//        echo $min_key . '<br>';
        $u = $n[$min_key];
        unset($n[$min_key]);

        foreach($u as $vertex => $length) {
//            if($length > 0) {
            $alt = $dist[$min_key] + $length;
            if($alt < $dist[$vertex]) {
                $dist[$vertex] = $alt;
                $previous[$vertex] = $min_key;
            }
//            }

            if($min_key == $target) {
                return $dist;
            }
        }
    }
    echo '<pre>';
//    findPath($previous);
    print_r($dist);
    //print_r($path);
}

function findMin($vertices, $distances)
{
    $min = $vertices[0];
    foreach($vertices as $vertex) {
        if($distances[$vertex] < $distances[$min]) {
            $min = $vertex;
        }
    }
    return $min;
}

function findPath($path, $kam = '5,12')
{
    while(array_key_exists($kam, $path)) {
        echo $kam . ' <- ' . $path[$kam] . '<br>';
        $kam = $path[$kam];
    }
}

$map = new Map();
$map->unserialize('map.txt');
?>

<script>

    function getNeighbours(map, i) {
        var size = Math.sqrt(map.length);
        var neighbours = [];

        if (i % size != 0) {
            neighbours.push(i-1);
        }

        if (i % size != (size - 1)) {
            neighbours.push(i+1);
        }

        if (i >= size) {
            neighbours.push(i-size);
        }

        if (i <= size * size - size) {
            neighbours.push(i+size);
        }

        return neighbours;
    }

    function closest(map, i) {
        var minNeigh = undefined;
        var neighbours = getNeighbours(map, i);
        for (var j = 0; j < neighbours.length; j++) {
            console.log(neighbours[j]);
            console.log(map[neighbours[j]]);
            if (minNeigh == undefined || map[minNeigh] > map[neighbours[j]]) {
                minNeigh = neighbours[j]
            }
        }
        console.log('min:', minNeigh);
    }

    function bf(map, target) {
        var dist = [];
        var prev = [];
        var toVisit = map;


        for (var i = 0; i < map.length; i++) {
            dist[i] = 999999;
            prev[i] = undefined;
        }

        dist[0] = 0;
        prev[0] = undefined;

        for (var i = 0; i < map.length - 1; i++) {
            var neighbours = getNeighbours(map, i);
            for (var j = 0; j < neighbours.length; j++) {
                if (map[neighbours[j]] != undefined) {
                    if (dist[i] > dist[neighbours[j]] + map[neighbours[j]]) {
                        dist[i] = dist[neighbours[j]] + map[neighbours[j]];
                        prev[i] = neighbours[j];
                    }
                }
            }
        }

        console.log(dist);
        path(prev, target);
    }

    function path(prev, target) {
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


<?= $map->getJs(); ?>

    var start = new Date();
    bf(map, 122321);
    var end = new Date();
    console.log(end - start);
</script>