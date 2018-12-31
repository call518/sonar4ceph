<?php
include '_config.php';
include '_functions.php';

$req_pool_id = $_POST['req_pool_id'];
if (!$req_pool_id) {
	$req_pool_id = "all";
}

$req_osd_type = $_POST['req_osd_type'];
if (!$req_osd_type) {
	$req_osd_type = "acting";
}

if ($req_pool_id == "acting") {
	$chart_title = "ALL";
} else { 
	$chart_title = pool_id2name($req_pool_id)."(".$req_pool_id.")";
}

$arrTotalPoolList = getPoolList();

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

$arrSamplePGstates = array(
"active+clean",
"active+undersized",
"active+undersized+degraded",
"peering",
"activating",
"stale+active+clean",
"unknown",
);

$arrChartDatasets = array();

$transparency = 0.3;

${"color_active+clean"} = "0, 222, 0";
${"color_active+undersized"} = "209, 94, 255";
${"color_active+undersized+degraded"} = "255, 183, 64";
${"color_peering"} = "43, 88, 255";
${"color_activating"} = "255, 0, 243";
${"color_stale+active+clean"} = "11, 133, 164";
${"color_unknown"} = "50, 50, 50";

foreach ($arrSamplePGstates as $SampleState) {
	array_push($arrChartDatasets, array("label" => $SampleState, "hoverRadius" => 0, "backgroundColor" => "rgba(${'color_'.$SampleState}, $transparency)", "data" => array()));
}

foreach ($arrChartDatasets as $k => $v) {
	$label = $v['label'];
	${"arrKey_".$label} = $k;
}

foreach ($arrPGStats as $item_pg) {
	//$pg_ = $item_pg[''];
	$pg_id = $item_pg['pgid'];
	$pg_pool_id = explode('.', $pg_id)[0];
	$pg_hash_num16 = explode('.', $pg_id)[1];;
	$pg_hash_num10 = hexdec($pg_hash_num16);
	$pg_state = $item_pg['state'];
	$pg_state_var = str_replace('+', '_', $pg_state);
	$pg_acting_array = $item_pg['acting'];
	$pg_acting_primary = $item_pg['acting_primary'];
	$pg_up_array = $item_pg['up'];
	$pg_up_primary = $item_pg['up_primary'];

	if ($req_pool_id == $pg_pool_id || $req_pool_id == "all") {
		if ($req_osd_type == "acting_primary") {
			$osds = array($pg_acting_primary);
		} else {
			$osds = $pg_acting_array;
		}
		//print_r($osds);
		//foreach ($pg_acting_array as $osd) {
		foreach ($osds as $osd) {
			//$arrTMP = array('x' => $pg_pool_id.".".$pg_hash_num10, 'y' => $osd, 'r' => '10');
			$arrTMP = array('x' => $pg_hash_num10, 'y' => $osd, 'r' => '15');
			if (in_array($pg_state, $arrSamplePGstates)) {
				array_push($arrChartDatasets[${"arrKey_".$pg_state}]['data'], $arrTMP);
			} else {
				array_push($arrChartDatasets[$arrKey_unknown]['data'], $arrTMP);
			}
		}
	}
}

print_r(json_encode($arrChartDatasets));
?>
