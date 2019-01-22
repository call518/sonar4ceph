<?php
include '_config.php';
include '_functions.php';

$req_pool_id = $_POST['req_pool_id'];
if (!$req_pool_id) {
	$req_pool_id = "all";
}

$req_pg_type = $_POST['req_pg_type'];
if (!$req_pg_type) {
	$req_pg_type = "acting";
}

$arrTotalPoolList = getPoolList();

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

$arrChartDatasets = array();

$transparency = 0.6;

$arrTmp = array();
foreach ($arrPGStats as $item_pg) {
	array_push($arrTmp, $item_pg['state']);
}
$arrNowUniqStates = array_unique($arrTmp);

foreach ($arrNowUniqStates as $item) {
	$arrChartDatasets[$item] = array("label" => $item, "hoverBorderWidth" => 3, "borderColor" => "rgba(169, 40, 97, 0.5)", "borderWidth" => 1, "data" => array());
}
//print_r($arrChartDatasets);
//exit;

foreach ($arrPGStats as $item_pg) {
	//$pg_ = $item_pg[''];
	$pg_id = $item_pg['pgid'];
	$pg_pool_id = explode('.', $pg_id)[0];
	$pg_hash_num16 = explode('.', $pg_id)[1];;
	$pg_hash_num10 = hexdec($pg_hash_num16);
	$pg_state = $item_pg['state'];
	$pg_acting_array = $item_pg['acting'];
	$pg_acting_primary = $item_pg['acting_primary'];
	$pg_up_array = $item_pg['up'];
	$pg_up_primary = $item_pg['up_primary'];

	if ($req_pool_id == $pg_pool_id || $req_pool_id == "all") {
		if ($req_pg_type == "acting_primary") {
			$osds = array($pg_acting_primary);
		} else {
			$osds = $pg_acting_array;
		}
		foreach ($osds as $osd) {
			//$arrTMP = array('x' => $pg_pool_id.".".$pg_hash_num10, 'y' => $osd, 'r' => '10');
			$arrTMP = array("x" => $pg_hash_num10, "y" => $osd, "r" => 10, "pool_id" => $pg_pool_id, "primary_osd" => $pg_acting_primary, "ps_state" => $pg_state);
			array_push($arrChartDatasets[$pg_state]['data'], $arrTMP);
		}
	}
}
$arrTmp = $arrChartDatasets;
$arrChartDatasets = array();
foreach ($arrTmp as $item) {
	//print_r($item);
	array_push($arrChartDatasets, $item);
}

print_r(json_encode($arrChartDatasets));
?>
