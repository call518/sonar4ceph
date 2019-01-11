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

$arrSamplePGstates = array(
"active+clean",
"active+undersized",
"active+undersized+degraded",
"active+recovering",
"active+recovering+degraded",
"active+recovery_wait+degraded",
"active+clean+scrubbing+deep",
"remapped+peering",
"peering",
"activating",
"stale+active+clean",
"activating+undersized",
"undersized+peered",
"undersized+degraded+peered",
"unknown",
);

$arrChartDatasets = array();

$transparency = 0.6;

${"color_active+clean"} = "0, 222, 0";
${"color_active+undersized"} = "137, 112, 0";
${"color_active+undersized+degraded"} = "235, 152, 78";
${"color_active+recovering"} = "56, 102, 54";
${"color_active+recovering+degraded"} = "16, 65, 172";
${"color_active+recovery_wait+degraded"} = "128, 10, 131";
${"color_active+clean+scrubbing+deep"} = "255, 105, 100";
${"color_remapped+peering"} = "121, 6, 57";
${"color_peering"} = "43, 88, 255";
${"color_activating"} = "255, 0, 243";
${"color_stale+active+clean"} = "11, 133, 164";
${"color_activating+undersized"} = "241, 196, 15";
${"color_undersized+peered"} = "115, 198, 182";
${"color_undersized+degraded+peered"} = "11, 83, 69";
${"color_unknown"} = "23, 32, 42";

$border_color_primary = "rgba(169, 40, 97, 0.5)";
foreach ($arrSamplePGstates as $SampleState) {
	//array_push($arrChartDatasets, array("label" => $SampleState, "hoverRadius" => 0, "backgroundColor" => "rgba(${'color_'.$SampleState}, $transparency)", "data" => array()));
	//array_push($arrChartDatasets, array("label" => $SampleState, "hoverRadius" => 10, "hoverBorderWidth" => 3, "borderColor" => $border_color_primary, "borderWidth" => 1, "backgroundColor" => "rgba(${'color_'.$SampleState}, $transparency)", "data" => array()));
	array_push($arrChartDatasets, array("label" => $SampleState, "hoverBorderWidth" => 3, "borderColor" => $border_color_primary, "borderWidth" => 1, "backgroundColor" => "rgba(${'color_'.$SampleState}, $transparency)", "data" => array()));
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
		if ($req_pg_type == "acting_primary") {
			$osds = array($pg_acting_primary);
		} else {
			$osds = $pg_acting_array;
		}
		//print_r($osds);
		//foreach ($pg_acting_array as $osd) {
		foreach ($osds as $osd) {
			//$arrTMP = array('x' => $pg_pool_id.".".$pg_hash_num10, 'y' => $osd, 'r' => '10');
			$arrTMP = array("x" => $pg_hash_num10, "y" => $osd, "r" => 15, "pool_id" => $pg_pool_id, "primary_osd" => $pg_acting_primary);
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
