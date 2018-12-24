<?php
include '../_config.php';
include '../_functions.php';


$jsonData = shell_exec("ceph -s --format json");
$arrData = json_decode($jsonData, true);
print_r($arrData['pgmap']);

$write_bytes_sec = 0;
$write_op_per_sec = 0;
$read_bytes_sec = 0;
$read_op_per_sec = 0;

$arrDataPGMAP = $arrData['pgmap'];

if (array_key_exists('write_bytes_sec', $arrDataPGMAP) == true) {
	$write_bytes_sec = $arrDataPGMAP['write_bytes_sec'];
}

if (array_key_exists('write_op_per_sec', $arrDataPGMAP) == true) {
	$write_op_per_sec = $arrDataPGMAP['write_op_per_sec'];
}

if (array_key_exists('read_bytes_sec', $arrDataPGMAP) == true) {
	$read_bytes_sec = $arrDataPGMAP['read_bytes_sec'];
}

if (array_key_exists('read_op_per_sec', $arrDataPGMAP) == true) {
	$read_op_per_sec = $arrDataPGMAP['read_op_per_sec'];
}

echo $write_bytes_sec."\n";
echo $write_op_per_sec."\n";
echo $read_bytes_sec."\n";
echo $read_op_per_sec."\n";

?>
