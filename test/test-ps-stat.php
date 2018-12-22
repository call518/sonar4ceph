<?php
include '../_config.php';
include '../_functions.php';

$jsonData = getPGDump();
$arrData = json_decode($jsonData, true);

$pg_stats = $arrData['output']['pg_stats'];
//print_r($ps_stats);

$arrPG_IDs = array_column($pg_stats, "pgid");
//print_r($arrPG_IDs);

$arrPG_Acting = array_column($pg_stats, "acting");
//print_r($arrPG_Acting);

$arrPG_Acting_Primary = array_column($pg_stats, "acting_primary");
//print_r($arrPG_Acting_Primary);

$arrPGStaus = array();
for ($i = 0; $i < count($arrPG_IDs); $i++) {
	$arrTmp = array();
	$arrTmp['pgid'] = $arrPG_IDs[$i];
	$arrTmp['acting'] = $arrPG_Acting[$i];
	$arrTmp['acting_primary'] = $arrPG_Acting_Primary[$i];
	array_push($arrPGStaus, $arrTmp);
}

print_r($arrPGStaus);
//$osd_arrIndex = array_search("$osd_id", array_column($arrOsdData, "osd"));

function getPGDump()
{
	global $ceph_api;
	return simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
}
?>
