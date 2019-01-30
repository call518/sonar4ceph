<?php
include '_config.php';
include "_functions.php";

$arrResult = array();

$Data = shell_exec("ceph tell mgr osd status 2>&1 > /dev/null | sed -e 's/|/ /g' -e '/---/d' -e '/id.*host.*used.*avail.*wr.*ops.*wr.*data.*rd.*ops.*rd.*data.*state/d' | sed -r \"s/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[mGK]//g\"");
$arrData = explode("\n", trim($Data));

foreach ($arrData as $line) {
	$arrLine = explode(" ", trim(preg_replace('!\s+!', ' ', $line)));
	//print_r($arrLine);

	$id = $arrLine[0];
	$host = $arrLine[1];
	$used = toBytes($arrLine[2]);
	$avail = toBytes($arrLine[3]);
	$wr_ops = $arrLine[4];
	$wr_data = toBytes($arrLine[5]);
	$rd_ops = $arrLine[6];
	$rd_data = toBytes($arrLine[7]);
	$state = $arrLine[8];

	$arrTmp = array("id" => $id, "host" => $host, "used" => $used, "avail" => $avail, "wr_ops" => $wr_ops, "wr_data" => $wr_data, "rd_ops" => $rd_ops, "rd_data" => $rd_data, "state" => $state);
	array_push($arrResult, $arrTmp);
}

print_r(json_encode($arrResult));
