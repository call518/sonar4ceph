<?php
include '../_config.php';
include '../_functions.php';

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=summary");
$arrOsdData = json_decode($jsonData, true)['output']['pg_stats_delta'];
//print_r($arrOsdData);

?>
