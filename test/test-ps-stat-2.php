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

$uniq_pool_list = uniq_Pool_list();

$uniq_osd_list = uniq_OSD_list();
$arrOSDs = array("osd_pg_state" => array());
foreach ($uniq_osd_list as $k => $v) {
	$key_name = "osd_".$v;
	$arrOSDs['osd_pg_state'][$key_name] = array();
	foreach ($uniq_pool_list as $item) {
		$pool_key = "pool_".$item;
		$arrOSDs['osd_pg_state'][$key_name][$pool_key] = 0;
	}
}
//print_r(json_encode($arrOSDs));
//exit;

print_r($arrPG_IDs);
//print_r($arrPG_Up);
exit;
foreach ($arrPG_Up as $k => $v) {
	//print_r($v);
	foreach ($v as $item) {
		print_r($item);
	}
}


#{
#  "osd_pg_state": {
#    "osd_3": {
#      "pool_5": 21,
#      "pool_6": 18,
#      "pool_7": 78,
#      "pool_8": 39,
#      "total": 156
#    },
#    "osd_16": {
#      "pool_5": 25,
#      "pool_6": 19,
#      "pool_7": 95,
#      "pool_8": 32,
#      "total": 171
#    }
#  }
#}




//$arrPG_Acting = array_column($ps_stats, "acting");
//print_r($arrPG_Acting);

//$arrPG_Acting_Primary = array_column($ps_stats, "acting_primary");
//print_r($arrPG_Acting_Primary);

//foreach ($arrPG_Up as $k => $v) {
//	$pg_id = $arrPG_IDs[$k];
//	$pg_up_primary = $arrPG_Up_Primary[$k];
//	$pg_up_replicas = $v;
//	$del_key = array_search($pg_up_primary, $pg_up_replicas);
//	if ($del_key !== false) {
//		unset($pg_up_replicas[$key]);
//	}
//	print_r($pg_id."\n");
//	print_r($pg_up_primary."\n");
//	print_r($pg_up_replicas);
//}



?>
