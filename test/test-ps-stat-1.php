<?php
include '../_config.php';
include '../_functions.php';

$arrData = getPGDump();

$pg_stats = $arrData['output']['pg_stats'];
//print_r($ps_stats);

$arrPG_IDs = array_column($pg_stats, "pgid");
//print_r($arrPG_IDs);

$arrPG_Up = array_column($pg_stats, "up");
//print_r($arrPG_Up);

$arrPG_Up_Primary = array_column($pg_stats, "up_primary");
//print_r($arrPG_Up_Primary);

$arrPG_Acting = array_column($pg_stats, "acting");
//print_r($arrPG_Acting);

$arrPG_Acting_Primary = array_column($pg_stats, "acting_primary");
//print_r($arrPG_Acting_Primary);

foreach ($arrPG_Up as $k => $v) {
	$pg_id = $arrPG_IDs[$k];
	$pg_up_primary = $arrPG_Up_Primary[$k];
	$pg_up_replicas = $v;
	$del_key = array_search($pg_up_primary, $pg_up_replicas);
	if ($del_key !== false) {
		unset($pg_up_replicas[$key]);
	}
	print_r($pg_id."\n");
	print_r($pg_up_primary."\n");
	print_r($pg_up_replicas);
}

function getPGDump()
{
	global $ceph_api;
	$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
	return json_decode($jsonData, true);
}
?>
