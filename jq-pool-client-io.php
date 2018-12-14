<?php
$pool_name = $_GET['pool_name'];
$pool_id = $_GET['pool_id'];
//$pool_name = "vms";
//$pool_id = 5;
$jsonData = shell_exec("ceph osd pool stats $pool_name --format=json");
$object = json_decode($jsonData)[0];
$arrData = json_decode(json_encode($object), True);
$arr_client_io_rate_object = json_decode(json_encode($arrData['client_io_rate'], True));
$arr_client_io_rate = json_decode(json_encode($arr_client_io_rate_object), True);
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
