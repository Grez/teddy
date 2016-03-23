<?php

use NoiseGenerator\NoiseWriter;



require __DIR__ . '/../vendor/autoload.php';

$perlin = new \NoiseGenerator\PerlinNoise(3000);
$width = 250;
$height = 250;
//$octaves = array(64, 32, 16, 8, 4, 2);
$octaves = array(64, 32, 16, 8);
$noise = array();
for ($y = 0; $y < $height; $y += 1) {
	for ($x = 0; $x < $width; $x += 1) {
		$num = $perlin->noise($x - 1260, $y - 55, 0, $octaves);
		$raw = ($num / 2) + .5;
		if ($raw < 0) $raw = 0;
		$noise[$x][$y] = $raw;
	}
}
$fileName = 'perlin-'.implode('-', $octaves).'.png';
//echo '<pre>';
//print_r($noise);
header('Content-Type: image/png');

echo imagepng(\NoiseGenerator\NoiseWriter::toImage($noise));
