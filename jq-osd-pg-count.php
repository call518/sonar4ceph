<?php
include '_config.php';
include '_functions.php';

$req_osd_type = $_GET['req_osd_type'];
if (!$req_osd_type) {
	$req_osd_type = "acting";
}

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

$arrChartData = array();

//$transparency = 0.5;
//$border_color_primary = "rgba(169, 40, 97, 0.5)";

$arr_osd_list = uniq_OSD_list();
sort($arr_osd_list);

$arrLabels = array();
foreach ($arr_osd_list as $osd_num) {
	//${"osd_".$osd_num} = 0;
	array_push($arrLabels, "OSD-".$osd_num);
}

$arrChartData['labels'] = $arrLabels;
$arrChartData['datasets'] = array();

$arrPoolList = getPoolList();
foreach ($arrPoolList as $pool) {
	$poolnum = $pool['poolnum'];
	$poolname = $pool['poolname'];
	$arrTmp = array("label" => $poolname."($poolnum)", "data" => array(), "backgroundColor" => getRandomColor());
	foreach ($arr_osd_list as $osd_num) {
		$arrTmp['data'][$osd_num] = 0;
	}
	$arrChartData['datasets'][$poolnum] = $arrTmp;
}

foreach ($arrPGStats as $pgStats) {
	//print_r($pgStats);
    $pgid = $pgStats['pgid'];
    $pool_id = explode(".", $pgid)[0];
    $pg_num = explode(".", $pgid)[1];
    $arr_acting_osds = $pgStats['acting'];
	foreach ($arr_acting_osds as $ps_acting_osd) {
		$arrChartData['datasets'][$pool_id]['data'][$ps_acting_osd]++;
	}
	//array_push($arrChartDatasets, array("label" => $SampleState, "hoverRadius" => 0, "backgroundColor" => "rgba(${'color_'.$SampleState}, $transparency)", "data" => array()));
}

$tmpArray = $arrChartData['datasets'];
//print_r($tmpArray);
$arrChartData['datasets'] = array();
foreach ($tmpArray as $pool_data) {
	array_push($arrChartData['datasets'], $pool_data);
}
//print_r($arrChartData);
//exit;

print_r(json_encode($arrChartData));

//$resultArray = array();
//array_push($resultArray, $arrChartData);
//print_r(json_encode($resultArray));
?>
