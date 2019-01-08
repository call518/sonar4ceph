<?php
include '_config.php';
include "_functions.php";

$pool_name = $_GET['pool_name'];
$pool_id = $_GET['pool_id'];
//$jsonData = shell_exec("ceph osd pool stats $pool_name --format=json");
$jsonData = simple_curl("$ceph_api/osd/pool/stats?name=$pool_name");
//print_r($jsonData);
$arrData = json_decode($jsonData, true)['output'];
//print_r($arrData);
$arr_client_io_rate = $arrData[0]['client_io_rate'];;
//print_r($arr_client_io_rate);
if (count($arr_client_io_rate) != 0) {
	$read_bytes_sec = $arr_client_io_rate['read_bytes_sec'];
	if ($read_bytes_sec == '') $read_bytes_sec = 0;
	$write_bytes_sec = $arr_client_io_rate['write_bytes_sec'];
	if ($write_bytes_sec == '') $write_bytes_sec = 0;
	$read_op_per_sec = $arr_client_io_rate['read_op_per_sec'];
	$write_op_per_sec = $arr_client_io_rate['write_op_per_sec'];
    //echo "$read_bytes_sec, $write_bytes_sec";
} else {
	$read_bytes_sec = 0;
	$write_bytes_sec = 0;
	$read_op_per_sec = 0;
	$write_op_per_sec = 0;
    //echo "$read_bytes_sec, $write_bytes_sec";
}
echo(json_encode(array("pool_name" => $pool_name, "pool_id" => $pool_id, "read_bytes_sec" => $read_bytes_sec, "write_bytes_sec" => $write_bytes_sec, "read_op_per_sec" => $read_op_per_sec, "write_op_per_sec" => $write_op_per_sec)));
?>
