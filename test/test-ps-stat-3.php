<?php
include '../_config.php';
include '../_functions.php';

$arrData = getPGDump();
$pg_stats = $arrData['output']['pg_stats'];
$arrPG_IDs = array_column($pg_stats, "pgid");
$arrPG_Up = array_column($pg_stats, "up");

$arr_PG_and_Up_OSDs = array();
foreach ($arrPG_IDs as $k => $v) {
	$up_osds = $arrPG_Up[$k];
	$arrTmp = array($v => $up_osds);
	$arr_PG_and_Up_OSDs[$v] = $up_osds;
}
//ksort($arr_PG_and_Up_OSDs);

$arrResult = array("osd_pg_state" => array());

foreach ($arr_PG_and_Up_OSDs as $pg_id => $osds) {
	foreach ($osds as $osd_num) {
		$osd_key = "osd_".$osd_num;
		if (array_key_exists($osd_key, $arrResult['osd_pg_state']) != true) {
			$arrResult['osd_pg_state'][$osd_key] = array();
		}
	}
	$pool_key = "pool_".explode('.', $pg_id)[0];
	foreach ($osds as $osd_num) {
		$osd_key = "osd_".$osd_num;
		if (array_key_exists($pool_key, $arrResult['osd_pg_state'][$osd_key]) != true) {
			$arrResult['osd_pg_state'][$osd_key][$pool_key] = 0;
		}
		$arrResult['osd_pg_state'][$osd_key][$pool_key] += 1;
	}
}
//print_r($arrResult['osd_pg_state']);
//exit;

foreach ($arrResult['osd_pg_state'] as $osd_key => $item) {
	$total_pg_count_of_osd = 0;
	foreach ($item as $pg_count) {
		$total_pg_count_of_osd += $pg_count;
	}
	$arrResult['osd_pg_state'][$osd_key]['total'] = $total_pg_count_of_osd;
}

$arrResult['total_pgs_pool'] = array();
foreach ($arrResult['osd_pg_state'] as $item) {
	unset($item['total']);
	foreach ($item as $pool_key => $pg_count) {
		if (array_key_exists($pool_key, $arrResult['total_pgs_pool']) != true) {
			$arrResult['total_pgs_pool'][$pool_key] = 0;
		}
		$arrResult['total_pgs_pool'][$pool_key] += $pg_count;
	}
}

ksort($arrResult['osd_pg_state'], SORT_NATURAL);
foreach ($arrResult['osd_pg_state'] as $k => $v) {
	ksort($arrResult['osd_pg_state'][$k], SORT_NATURAL);
}
ksort($arrResult['total_pgs_pool'], SORT_NATURAL);

print_r(json_encode($arrResult));

?>
