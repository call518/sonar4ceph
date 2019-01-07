<?php
include '_config.php';
include '_functions.php';

$req_pool_id = $_POST['req_pool_id'];
if (!$req_pool_id) {
	$req_pool_id = "all";
}

$arrTotalPoolList = getPoolList();

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

//$max_num_bytes = 0;
//foreach ($arrPGStats as $item_pg) {
//	$num_bytes = $item_pg['stat_sum']['num_bytes'];
//	if ($num_bytes > $max_num_bytes) {
//		$max_num_bytes = $num_bytes;
//	}
//}

$arrChartDatasets = array();

$transparency = 0.5;

$border_color_primary = "rgba(169, 40, 97, 0.5)";

foreach ($arrTotalPoolList as $pool) {
	$poolnum = $pool['poolnum'];
	$poolname = $pool['poolname'];
	if ($req_pool_id == $poolnum || $req_pool_id == "all") {
		$arrChartDatasets[$poolnum] = array("label" => $poolname."(".$poolnum.")", "hoverBorderWidth" => 3, "borderColor" => $border_color_primary, "borderWidth" => 1, "backgroundColor" => randomRBGA4ChartJS($transparency), "data" => array());
	}
}

//foreach ($arrChartDatasets as $k => $v) {
//	$label = $v['label'];
//	${"arrKey_".$label} = $k;
//}

$max_num_bytes = 0;
foreach ($arrPGStats as $item_pg) {
	$pg_id = $item_pg['pgid'];
	$pg_pool_id = explode('.', $pg_id)[0];
	if ($req_pool_id == $pg_pool_id || $req_pool_id == "all") {
		$num_bytes = $item_pg['stat_sum']['num_bytes'];
		if ($num_bytes > $max_num_bytes) {
			$max_num_bytes = $num_bytes;
		}
	}
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
	$num_bytes = $item_pg['stat_sum']['num_bytes'];
	$num_objects = $item_pg['stat_sum']['num_objects'];

	if ($req_pool_id == $pg_pool_id || $req_pool_id == "all") {
		if ($num_bytes > 0) {
			$pg_radius = get_bubble_radius(100, $max_num_bytes, 10, $num_bytes);
			$arrTMP = array("x" => $pg_hash_num10, "y" => $num_bytes, "r" => $pg_radius, "pool_id" => $pg_pool_id, "primary_osd" => $pg_acting_primary, "num_objects" => $num_objects);
			array_push($arrChartDatasets[$pg_pool_id]['data'], $arrTMP);
		}
	}

//echo "$pg_pool_id - $pg_hash_num10 - $pg_acting_primary - $num_bytes \n";

}

$tmpArray = $arrChartDatasets;
$arrChartDatasets = array();
foreach ($tmpArray as $datasets) {
	array_push($arrChartDatasets, $datasets);
}

print_r(json_encode($arrChartDatasets));
?>
