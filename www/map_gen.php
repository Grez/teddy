<?php

$config = [
    'G' => 50,
    'W' => 10,
    'D' => 70,
    'S' => 50,
];

$types = [
    'G' => 100,
    'D' => 40,
    'S' => 10,
];

$x = 100;
$y = 50;

$map = [];

for($i = 0; $i < $y; $i++) {
    $map[$i] = [];
    for($j = 0; $j < $x; $j++) {
        if ($i == 0 && $j == 0) {
            $map[$i][$j] = 'G';
        } else {
            $neighbours = getNeighbours($map, $i, $j);
            if ($neighbours['S'] > 0 && $neighbours['D'] > 0) {
                $map[$i][$j] = 'G';
            } else if ($neighbours['S'] > 0) {
                if ($neighbours['G'] > $neighbours['S']) {
                    $map[$i][$j] = (mt_rand(0, 100) <= $config['G']) ? 'G' : 'S';
                } else {
                    $map[$i][$j] = 'G';
                }
            } else if ($neighbours['D'] > 0) {
                if ($neighbours['G'] > $neighbours['D']) {
                    $map[$i][$j] = (mt_rand(0, 100) <= $config['G']) ? 'G' : 'D';
                } else {
                    $map[$i][$j] = 'G';
                }
            } else {
                $rand = mt_rand(0, 100);
                if ($rand < $types['S']) {
                    $map[$i][$j] = 'S';
                } else if ($rand < $types['D']) {
                    $map[$i][$j] = 'D';
                } else {
                    $map[$i][$j] = 'G';
                }
            }

            /*
            $dominatingNeighbour = getDominatingNeighbour($neighbours);
            if(mt_rand(0, 100) <= $config[$dominatingNeighbour]) {
                $map[$i][$j] = $dominatingNeighbour;
            } else {
                $map[$i][$j] = (mt_rand(0, 1)) ? 'G' : 'W';
            }
            */
        }
    }
}

function getNeighbours($map, $i, $j) {
    $neigh = ['G' => 0, 'W' => 0, 'D' => 0, 'S' => 0];
    if (isset($map[$i][$j+1])) {
        $neigh[$map[$i][$j+1]]++;
    }

    if (isset($map[$i][$j-1])) {
        $neigh[$map[$i][$j-1]]++;
    }

    if (isset($map[$i+1][$j])) {
        $neigh[$map[$i+1][$j]]++;
    }

    if (isset($map[$i-1][$j])) {
        $neigh[$map[$i-1][$j]]++;
    }

    if (isset($map[$i+1][$j+1])) {
        $neigh[$map[$i+1][$j+1]]++;
    }

    if (isset($map[$i-1][$j-1])) {
        $neigh[$map[$i-1][$j-1]]++;
    }

    if (isset($map[$i-1][$j+1])) {
        $neigh[$map[$i-1][$j+1]]++;
    }

    if (isset($map[$i+1][$j-1])) {
        $neigh[$map[$i+1][$j-1]]++;
    }

    asort($neigh);
//    echo '<pre>';
//    echo $i.';'.$j;
//    print_r($neigh);
//    echo '</pre>';
    return $neigh;
}

function getDominatingNeighbour($neighbours)
{
    if ($neighbours['G'] < $neighbours['W']) {
        return 'W';
    } else {
        return 'G';
    }

    foreach($neighbours as $key => $count) {
        return $key;
    }
}

//$map
//    $rivers = 4;
//    for($r = 0; $r < $rivers; $r++) {
//        $i = mt_rand(0, $x);
//        $j = mt_rand(0, $y);
//        $map[$i][$j] = 'W';
//
//        $length = mt_rand(5, 40);
//        for($l = 0; $l < $length; $l++) {
//            $coord = pickRandomNeighbour($i, $j);
//            $i = $coord[0];
//            $j = $coord[1];
//            $map[$i][$j] = 'W';
//        }
//    }

function pickRandomNeighbour($i, $j)
{
    global $x, $y, $map;

    if(mt_rand(0, 1)) {
        $i += mt_rand(-1, 1);
        if ($map[$i][$j] == 'W') {
            $i += mt_rand(-5, 5);
        }
    } else {
        $j += mt_rand(-1, 1);
    }
    $i = min($y, max(0, $i));
    $j = min($x, max(0, $j));
    return [$i, $j];
}

function printMap($map)
{
    echo '<table>';
    foreach($map as $key => $val) {
        echo '<tr>';
        foreach($val as $key2 => $val2) {
            echo '<td class="' . $val2 . '" title="' . $key . ';' . $key2 . '"></td>';
        }
        echo '</tr>';
    }
    echo '</table>';

//    echo '<pre>';
//    print_r($map);
}

echo '<style>
td {width: 10px; height: 10px;}
.G {background: #0a0;}
.D {background: #fa0;}
.S {background: #eee;}
.W {background: #22a;}
</style>';
printMap($map);