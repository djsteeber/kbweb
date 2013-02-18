<?php


function ah($a, $b) {
   return $a . ':"' . $b . '"';
}


$x = array('name', 'position');
$y = array('dale', 'webm');

$ary = array_map("ah", $x, $y);


print_r($ary);

$r = join(",", $ary);
echo $r . PHP_EOL;

?>
