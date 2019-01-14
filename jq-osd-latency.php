<?php
include '_config.php';
include "_functions.php";

//$jsonData = shell_exec("ceph --status --format json");
$jsonData = simple_curl("$ceph_api/osd/perf.json");
//print_r($jsonData);
//exit;
$arrData = json_decode($jsonData, true)['output']['osd_perf_infos'];
$arrTmp = $arrData;
$arrData = array();
foreach ($arrTmp as $k => $v) {
	$osd_id = $v['id'];
	$apply_latency_ms = $v['perf_stats']['apply_latency_ms'];
	$commit_latency_ms = $v['perf_stats']['commit_latency_ms'];
	$arrData[$osd_id] = array('id' => $osd_id, "apply_latency_ms" => $apply_latency_ms, "commit_latency_ms" => $commit_latency_ms);
}
sort($arrData);
$arrTmp = $arrData;
$arrData = array();
foreach ($arrTmp as $item) {
	array_push($arrData, $item);
}
print_r(json_encode($arrData));
?>
