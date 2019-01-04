<?php
include '_config.php';
include '_functions.php';

$req_pg_type = $_POST['req_pg_type'];
if (!$req_pg_type) {
	$req_pg_type = "acting";
}

$req_pool_id = $_POST['req_pool_id'];
if (!$req_pool_id) {
	$req_pool_id = "all";
}

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

$arrChartData = array();

$transparency = 0.7;

$arr_osd_list = uniq_OSD_list();
sort($arr_osd_list);

$arrPoolList = getPoolList();

$arrLabels = array();
foreach ($arr_osd_list as $osd_num) {
	array_push($arrLabels, "OSD-".$osd_num);
}

$arrChartData['labels'] = $arrLabels;
$arrChartData['datasets'] = array();

foreach ($arrPoolList as $pool) {
	$poolnum = $pool['poolnum'];
	$poolname = $pool['poolname'];
	$arrTmp = array();
	if ($req_pool_id == "all") {
		$arrTmp = array("label" => $poolname."($poolnum)", "data" => array(), "backgroundColor" => randomRBGA4ChartJS($transparency));
		foreach ($arr_osd_list as $osd_num) {
			$arrTmp['data'][$osd_num] = 0;
		}
		$arrChartData['datasets'][$poolnum] = $arrTmp;
	} else {
		if ($req_pool_id == $poolnum) {
			$arrTmp = array("label" => $poolname."($poolnum)", "data" => array(), "backgroundColor" => randomRBGA4ChartJS($transparency));
			foreach ($arr_osd_list as $osd_num) {
				$arrTmp['data'][$osd_num] = 0;
			}
			$arrChartData['datasets'][$poolnum] = $arrTmp;
		}
	}
}

foreach ($arrPGStats as $pgStats) {
	//print_r($pgStats);
    $pgid = $pgStats['pgid'];
    $pool_id = explode(".", $pgid)[0];
    $pg_num = explode(".", $pgid)[1];
    $arr_acting_osds = $pgStats['acting'];
    $acting_primary_osds = $pgStats['acting_primary'];

	if ($req_pool_id == "all") {
		if ($req_pg_type == "acting") {
			foreach ($arr_acting_osds as $ps_acting_osd) {
				$arrChartData['datasets'][$pool_id]['data'][$ps_acting_osd]++;
			}
		} else {
			$arrChartData['datasets'][$pool_id]['data'][$acting_primary_osds]++;
		}
	} else {
		if ($req_pool_id == $pool_id) {
			if ($req_pg_type == "acting") {
				foreach ($arr_acting_osds as $ps_acting_osd) {
					//print_r($arrChartData['datasets'][$pool_id]);
					$arrChartData['datasets'][$pool_id]['data'][$ps_acting_osd]++;
				}
			} else {
				$arrChartData['datasets'][$pool_id]['data'][$acting_primary_osds]++;
			}
		}
	}
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
