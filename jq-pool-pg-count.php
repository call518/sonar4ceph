<?php
include '_config.php';
include '_functions.php';

$req_pg_type = $_GET['req_pg_type'];
if (!$req_pg_type) {
	$req_pg_type = "acting";
}

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

$arrChartData = array();

$transparency = 0.7;

$arr_osd_list = uniq_OSD_list();
sort($arr_osd_list);

$arrPoolList = getPoolList();

$arrLabels = array();
foreach ($arrPoolList as $pool) {
	$poolnum = $pool['poolnum'];
	$poolname = $pool['poolname'];
	$arrLabels[$poolnum] = $poolname."(".$poolnum.")";
}

$arrChartData['labels'] = $arrLabels;
ksort($arrChartData['labels']);

$arrChartData['datasets'] = array();

foreach ($arr_osd_list as $osd_num) {
	$arrTmp = array("label" => "OSD-".$osd_num, "data" => array(), "backgroundColor" => randomRBGA4ChartJS($transparency));
	foreach ($arrPoolList as $pool) {
		$poolnum = $pool['poolnum'];
		$poolname = $pool['poolname'];
		$arrTmp['data'][$poolnum] = 0;
	}
	$arrChartData['datasets'][$osd_num] = $arrTmp;
}

foreach ($arrPGStats as $pgStats) {
	//print_r($pgStats);
    $pgid = $pgStats['pgid'];
    $pool_id = explode(".", $pgid)[0];
    $pg_num = explode(".", $pgid)[1];
    $arr_acting_osds = $pgStats['acting'];
    $acting_primary_osds = $pgStats['acting_primary'];
	if ($req_pg_type == "acting") {
		foreach ($arr_acting_osds as $ps_acting_osd) {
			$arrChartData['datasets'][$ps_acting_osd]['data'][$pool_id]++;
		}
	} else {
		$arrChartData['datasets'][$acting_primary_osds]['data'][$pool_id]++;
	}
}

$tmpArray = $arrChartData['labels'];
$arrChartData['labels'] = array();
foreach ($tmpArray as $label_data) {
	array_push($arrChartData['labels'], $label_data);
}

foreach ($arrChartData['datasets'] as $key => $arrOSDPGDataset) {
	$arrData = $arrOSDPGDataset['data'];
	ksort($arrData);
	//$arrChartData['datasets'][$key]['data'] = array();
	$tmpArray = array();
	foreach ($arrData as $data) {
		array_push($tmpArray, $data);
	}
	$arrChartData['datasets'][$key]['data'] = $tmpArray;
}

//print_r($arrChartData);
//exit;

print_r(json_encode($arrChartData));

//$resultArray = array();
//array_push($resultArray, $arrChartData);
//print_r(json_encode($resultArray));
?>
