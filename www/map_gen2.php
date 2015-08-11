<?php

class MapGenerator
{
    protected $x = 0;
    protected $y = 0;

    protected $map = [];
    protected $canvasBg = '?';

    protected $bla = 0;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;

        $this->generateCanvas();
    }

    public function generateCanvas()
    {
        for ($i = 0; $i < $this->y; $i++) {
            for ($j = 0; $j < $this->x; $j++) {
                $this->map[$i][$j] = $this->canvasBg;
            }
        }
    }

    public function getMap()
    {
        return $this->map;
    }

    public function render()
    {
        $html = '<table style="width: ' . $this->x * 10 . 'px;">';
        foreach($this->map as $key => $val) {
            $html .= '<tr>';
            foreach($val as $key2 => $val2) {
                $html .= '<td class="type' . $val2 . '" title="' . $key . ';' . $key2 . '"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
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

    /**
     * @param $x
     * @param $y
     * @return array
     * @TODO: boundaries!
     */
    protected function pickRandomNeighbour($x, $y)
    {
        $this->bla++;
        $coord = [];
        switch (mt_rand(0, 7)) {
            case 0: $coord = [$x, $y+1]; break;
            case 1: $coord = [$x+1, $y+1]; break;
            case 2: $coord = [$x-1, $y+1]; break;
            case 3: $coord = [$x, $y-1]; break;
            case 4: $coord = [$x+1, $y-1]; break;
            case 5: $coord = [$x-1, $y-1]; break;
            case 6: $coord = [$x+1, $y]; break;
            case 7: $coord = [$x-1, $y]; break;
        }

        if ($coord[0] < 0 || $coord[0] >= $this->x || $coord[1] < 0 || $coord[1] >= $this->y) {
            //echo '<pre>';
            //echo '- ' . $coord[0] . ';' . $coord[1] . '<br>';
            //echo '</pre>';
            return $this->pickRandomNeighbour($x, $y);
        }

        echo $this->bla . '<br>';

        //echo '<pre>';
        //echo '+ ' . $coord[0] . ';' . $coord[1] . '<br>';
        //echo '</pre>';

        return $coord;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function serialize($file)
    {
        file_put_contents(__DIR__ . '/' . $file, serialize($this->map));
    }

}

class SpaceMapGenerator extends MapGenerator
{
    protected $canvasBg = 'B';

    /**
     * @param int $amount
     * @param int $size 0-3 (=> 1-5, 5-12, 13-20 fields)
     * @param enum (random|round|noodle)
     */
    public function addClouds($amount, $size = 2, $type = 'random')
    {
        $this->addCloud(1);
        return ;

        while ($amount-- > 0) {
            $cloud = [];

            switch ($size) {
                case 1: $fields = mt_rand(1, 5); break;
                case 2: $fields = mt_rand(5, 12); break;
                case 3: $fields = mt_rand(13, 20); break;
                default: $fields = mt_rand(1, 5);
            }

            $cloud[] = array(
                'x' => mt_rand(0, $this->x),
                'y' => mt_rand(0, $this->y),
            );

            $this->map[$cloud[0]['y']][$cloud[0]['x']] = 'G';
            while ($fields-- > 0) {
//                echo '<b>' . $fields . '</b><br>';
                $rand = $cloud[array_rand($cloud)];

                echo '<pre>';
//                print_r($this->getNeighbours($rand['y'], $rand['x']));
                echo '</pre>';
                //$neigh = $this->pickRandomNeighbour($rand['y'], $rand['x']);
                //$this->map[$neigh[0]][$neigh[1]] = 'G';
            }
        }
    }
    
    protected function drawPixel($x, $y)
    {
        $this->map[$x][$y] = 'G';
    }

    public function drawFilledCircle($x0, $y0, $radius)
    {
        while($radius >= 0) {
            $this->drawCircle($x0, $y0, $radius--);
        }
    }

    protected function drawVerticalLine($x, $y_start, $y_end, $color = 'G')
    {
        $y = $y_start;
        while ($y < $y_end) {
            $this->map[$x][$y] = $color;
            $y++;
        }
    }

    public function drawCircle($x0, $y0, $radius)
    {

        $x = $radius;
        $y = 0;
        $radiusError = 1 - $x;

        while($x >= $y) {
            $this->drawVerticalLine(-$x + $y0, -$y + $x0, $y + $x0);
            $this->drawVerticalLine(-$y + $y0, -$x + $x0, $x + $x0);
            $this->drawVerticalLine($y + $y0, -$x + $x0, $x + $x0);
            $this->drawVerticalLine($x + $y0, -$y + $x0, $y + $x0);

//            $this->drawPixel( $x + $x0,  $y + $y0);
//            $this->drawPixel(-$x + $x0,  $y + $y0);

//            $this->drawLine($y + $x0, $y + $y0, -$x + $x0, $y + $y0);
//            $this->drawPixel( $y + $x0,  $x + $y0);
//            $this->drawPixel(-$y + $x0,  $x + $y0);
//
//            $this->drawPixel(-$x + $x0, -$y + $y0);
//            $this->drawPixel( $x + $x0, -$y + $y0);
//
//            $this->drawPixel(-$y + $x0, -$x + $y0);
//            $this->drawPixel( $y + $x0, -$x + $y0);
            $y++;
            if ($radiusError < 0) {
                $radiusError += mt_rand(10, 34) * $y + 1;
            } else {
                $x--;
                $radiusError += mt_rand(1, 12) * ($y - $x) + 1;
            }
        }
    }

    public function drawShapes($amount, $size)
    {
        while ($amount--) {
            $actualSize = $size + mt_rand(-10, 20);
            $x = mt_rand($actualSize, $this->x - $actualSize);
            $y = mt_rand($actualSize, $this->y - $actualSize);

            $this->drawShape($x, $y, $actualSize);
        }
    }

    public function drawShape($x0, $y0, $radius)
    {
        $colors = ['G', 'D', 'S', 'W'];
        $color = $colors[array_rand($colors)];
        $i = $radius;
        $y = 0;

        $widthLeft = 1;
        $widthRight = 1;
        while ($i) {
            if ($i > $radius/2) {
                $widthLeft += mt_rand(-1, 2);
                $widthRight += mt_rand(-1, 2);
            } else {
                $widthLeft -= mt_rand(-1, 2);
                $widthRight -= mt_rand(-1, 2);
            }
            $this->drawVerticalLine($y0 + $i, $x0 - $widthLeft, $x0 + $widthRight, $color);
            $i--;
        }
    }

    protected function addCloud($radius)
    {
        $x = mt_rand(10, ($this->x - 10));
        $y = mt_rand(10, ($this->y - 10));


    }
}

class WorldMapGenerator extends MapGenerator
{
    protected $canvasBg = 1;

    const MAX_LVL = 11;

    public function raiseLevel($x, $y)
    {
        if ($this->map[$x][$y] >= self::MAX_LVL) {
            return ;
        }

        $this->map[$x][$y]++;
        $neighbours = $this->getNeighbours($x, $y);
        foreach ($neighbours as $coord) {
            while ($this->map[$coord[0]][$coord[1]] < $this->map[$x][$y] - 1) {
                $this->raiseLevel($coord[0], $coord[1]);
            }
        }
    }

    public function lowerLevel($x, $y)
    {

    }
}

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

for ($i = 0; $i < $y; $i++) {
    for ($j = 0; $j < $x; $j++) {
        $map[$i][$j] = 'B';
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


//function printMap($map)
//{
//    echo '<table>';
//    foreach($map as $key => $val) {
//        echo '<tr>';
//        foreach($val as $key2 => $val2) {
//            echo '<td class="' . $val2 . '" title="' . $key . ';' . $key2 . '"></td>';
//        }
//        echo '</tr>';
//    }
//    echo '</table>';
//
////    echo '<pre>';
////    print_r($map);
//}

echo '<style>
* {box-sizing: border-box;}
td {width: 10px; height: 10px; border: 1px solid #333; padding: 0;}
.typeB {background: #000;}
.typeG {background: #666;}
.typeD {background: #66a;}
.typeS {background: #fa0;}
.typeW {background: #aa2;}
.type1 {background: #00f;}
.type2 {background: #ff9;}
.type3 {background: #5c5;}
.type4 {background: #4b4;}
.type5 {background: #3a3;}
.type6 {background: #292;}
.type7 {background: #181;}
.type8 {background: #b93;}
.type9 {background: #a82;}
.type10 {background: #ccc;}
.type11 {background: #fff;}
table {border-collapse: collapse;}
</style>';
//printMap($map);


//$map = new SpaceMapGenerator(150, 150);
$map = new WorldMapGenerator(150, 150);
for ($i = 0; $i < $map->getX(); $i++) {
    $x = mt_rand(0, $map->getX() - 1);
    $y = mt_rand(0, $map->getY() - 1);
    //$j = mt_rand(1, 6);
    //while ($j-- > 0) {
    $length = mt_rand(10, $map->getX() / 2);
    for ($j = 0; $j < $length; $j++) {

        if ($j%5 == 0) {
            switch (mt_rand(1, 4)) {
                case 1:
                    $direction = array('x' => -1, 'y' => 1);
                    break;
                case 2:
                    $direction = array('x' => 1, 'y' => 1);
                    break;
                case 3:
                    $direction = array('x' => -1, 'y' => -1);
                    break;
                case 4:
                    $direction = array('x' => 1, 'y' => -1);
                    break;
            }
        }

        $map->raiseLevel($x, $y);
        if (mt_rand(1, 2) == 1) {
            $x += $direction['x'];
        }

        if (mt_rand(1, 2) == 1) {
            $y += $direction['y'];
        }

        if ($y < 0 || $y >= $map->getY() || $x < 0 || $x >= $map->getX()) {
            break;
        }
    }
//    }
}
//$map->addClouds(1, 2);
//$map->drawCircle(25, 25, 10);
//$map->drawShapes(1000, 20);
//$map->drawShape(25, 25, 10);
//$map->addClouds(1, 3);
echo $map->render();

echo '<br><br>';
$map->serialize('map.txt');