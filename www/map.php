<?php

$x_max = 32;
$y_max = 32;

$edges = [];
for($x = 0; $x < $x_max; $x++) {
	for($y = 0; $y < $y_max; $y++) {
        if ($y > 0) {
            $edges[$x . ',' . $y][$x . ',' . ($y - 1)] = 2;
        }

        if ($y < $y_max-1) {
            $edges[$x . ',' . $y][$x . ',' . ($y + 1)] = 2;
        }

        if ($x > 0) {
            $edges[$x . ',' . $y][($x-1) . ',' . $y] = 1;
        }

        if ($x < $x_max-1) {
            $edges[$x . ',' . $y][($x + 1) . ',' . $y] = 1;
        }
	}
}
//$edges['0,1']['15,15'] = 1;
//$edges['15,15']['0,1'] = 1;
//$edges['5,5']['5,6'] = 127;
//$edges['5,6']['5,5'] = 127;
echo '<pre>';
//print_R($edges);

function dijkstra($map)
{
    $s = '0,0';
    $target = '63,63';
    $dist = [];
    $previous = [];
    foreach ($map as $vertex => $lengths) {
        $dist[$vertex] = 999;
        $previous[$vertex] = -1;
    }

    $dist[$s] = 0;
    $n = $map;
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

echo '<pre>';
//print_r($edges);
$start = microtime(true);
print_r(dijkstra($edges));

$end = microtime(1);
echo ($end - $start);

//exit;

/*
2-0  2-1  2-2
1-0  1-1  1-2
0-0  0-1  0-2

*/