<?php
include '../_config.php';
include '../_functions.php';

$jsonData = simple_curl("$ceph_api/health?detail=detail");
$arrHealth = json_decode($jsonData, true);
$arrHealthOutput = $arrHealth['output'];
$status = $arrHealthOutput['status'];
$overall_status = "(N/A)";
if (array_key_exists('overall_status', $arrHealthOutput) == true) {
	$overall_status = $arrHealthOutput['overall_status'];
}
//print_r($status);
//print_r($overall_status);

$strHealthOK = "HEALTH_OK";

print_r(array2table($arrHealthOutput));
?>
