<html>
<title> CEPH - Logical Info </title>
<head>
<style>
table.type00 {
	border-collapse: collapse;
	text-align: center;
	line-height: 1;
	margin : 1px 1px;
}
table.type00 tr {
	vertical-align: top;
}
table.type00 td {
	vertical-align: top;
}

table.type01 {
	border-collapse: collapse;
	text-align: left;
	line-height: 1;
	margin : 15px 15px;
}
table.type01 tr {
	vertical-align: top;
}
table.type01 td {
	vertical-align: top;
}
</style>
<script type="text/JavaScript">
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
</script>
</head>

<body onload="JavaScript:timedRefresh(5000);">

<?php
include '_config.php';
include '_functions.php';
?>

<?php
$jsonData = simple_curl("$ceph_api/health?detail=detail");
$arrHealth = json_decode($jsonData, true);
$arrHealthOutput = $arrHealth['output'];

$strHealthOK = "HEALTH_OK";
$currHealthStatus = $arrHealthOutput['status'];
$currHealthOverallStatus = "(N/A)";

if (array_key_exists('overall_status', $arrHealthOutput) == true) {
	$currHealthOverallStatus = $arrHealthOutput['overall_status'];
}

$arrStatOSD = getStatOSD();
//print_r($arrStatOSD);
$OSD_full = $arrStatOSD['full'];
$OSD_nearfull = $arrStatOSD['nearfull'];
$OSD_num_osds = $arrStatOSD['num_osds'];
$OSD_num_up_osds = $arrStatOSD['num_up_osds'];
$OSD_epoch = $arrStatOSD['epoch'];
$OSD_num_in_osds = $arrStatOSD['num_in_osds'];
$OSD_num_remapped_pgs = $arrStatOSD['num_remapped_pgs'];

if ($currHealthStatus == $strHealthOK) {
	$currHealthStatusColor = "#00E315";
} else {
	$currHealthStatusColor = "#FF0000";
	$currHealthStatusStr = "";
}

if ($currHealthOverallStatus == $strHealthOK || $currHealthOverallStatus == "(N/A)") {
	$currHealthOverallStatusColor = "#00E315";
} else {
	$currHealthOverallStatusColor = "#FF0000";
}

if ($OSD_num_in_osds == $OSD_num_osds) {
	$currStatOSD_in_Color = "#00E315";
} else {
	$currStatOSD_in_Color = "#FF0000";
}

if ($OSD_num_up_osds == $OSD_num_osds) {
	$currStatOSD_up_Color = "#00E315";
} else {
    $currStatOSD_up_Color = "#FF0000";
}

if ($OSD_full == null) {
	$currStatOSD_full_Color = "#00E315";
	$OSD_full_str = "(N/A)";
} else {
	$currStatOSD_full_Color = "#FF0000";
	$OSD_full_str = $OSD_full;
}

if ($OSD_nearfull == null) {
	$currStatOSD_nearfull_Color = "#00E315";
	$OSD_nearfull_str = "(N/A)";
} else {
	$currStatOSD_nearfull_Color = "#FF0000";
	$OSD_nearfull_str = $OSD_nearfull;
}

echo "<center>";
echo "<table class='type01' border='0' cellpadding='5' width=50%><tr>";
echo " <tr>";
echo "  <td bgcolor='$currHealthStatusColor' colspan=\"100%\">";
echo "    <center><b>Health:</b> $currHealthStatus<p>";
echo "    <center><input type=\"button\" value=\"Detail Health\" style=\"width:200px\" onclick=\"window.open('show-health.php', 'Health', 'width=1024, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "  </td>";
echo "  <td bgcolor='$currHealthOverallStatusColor' colspan=\"100%\">";
echo "    <center><b>Health Overall:</b> $currHealthOverallStatus<p>";
echo "    <center><input type=\"button\" value=\"Detail Health\" style=\"width:200px\" onclick=\"window.open('show-health.php', 'Health', 'width=1024, height=800')\">";
echo "  </td>";
echo "  </td>";
echo "  <td>";
echo "  <td bgcolor='$currStatOSD_in_Color' colspan=\"100%\">";
echo "    <center><b>OSD IN</b><p><b>$OSD_num_in_osds</b> / <b>$OSD_num_osds</b><p>";
echo "  </td>";
echo "  <td>";
echo "  </td>";
echo "  <td bgcolor='$currStatOSD_up_Color' colspan=\"100%\">";
echo "    <center><b>OSD UP</b><p><b>$OSD_num_up_osds</b> / <b>$OSD_num_osds</b><p>";
echo "  </td>";
echo "  <td>";
echo "  </td>";
echo "  <td bgcolor='$currStatOSD_full_Color' colspan=\"100%\">";
echo "    <center><b>OSD Full</b><p><b>$OSD_full_str</b><p>";
echo "  </td>";
echo "  <td>";
echo "  <td bgcolor='$currStatOSD_nearfull_Color' colspan=\"100%\">";
echo "    <center><b>OSD Near-Full</b><p><b>$OSD_nearfull_str</b><p>";
echo " </tr>";
echo "</table>";
echo "<p>";

echo "<br>";

echo "<center>";
echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
echo " <tr>";
echo "  <td align='left'>";
echo "    <b>Sonar4Ceph</b>";
echo "  </td>";
echo " </tr>";
echo " <tr>";
echo "  <td>";
echo "    <center><input type=\"button\" value=\"Show Physical\" style=\"width:90%\" onclick=\"window.open('show-physical.php', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"PG Distribution\" style=\"width:90%\" onclick=\"window.open('showDistributionPGs.php', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Pools/OSDs/PGs\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/poolspgsosds.html?pool=&osd=', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"PG Count (of Each OSD)\" style=\"width:90%\" onclick=\"window.open('showPGCountByEachOSD.php', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"PG Count (of Each Pool)\" style=\"width:90%\" onclick=\"window.open('showPGCountByEachPool.php', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Dump-Info - Pools\" style=\"width:90%\" onclick=\"window.open('dump-info-pools.php', 'Dump-Info - Pools', 'width=1024, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Dump-Info - OSDs\" style=\"width:90%\" onclick=\"window.open('dump-info-osds.php', 'Dump-Info - OSDs', 'width=1024, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Dump-Info - PGs\" style=\"width:90%\" onclick=\"window.open('dump-info-pgs.php', 'Dump-Info - PGs', 'width=1024, height=800')\">";
echo "  </td>";
echo " </tr>";
echo "</table>";

echo "<center>";
echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
echo " <tr>";
echo "  <td>";
echo "   <input type=\"button\" style=\"width:90%\" value=\"Cluster B/W (Byte)\" onclick=\"window.open('cluster-bw.php', 'Cluster B/W (Byte)', 'width=1024, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "   <input type=\"button\" style=\"width:90%\" value=\"Cluster IOPS (Count)\" onclick=\"window.open('cluster-iops.php', 'Cluster IOPS (Count)', 'width=1024, height=800')\">";
echo "  </td>";
echo " </tr>";
echo "</table>";

//$jsonPoolData = shell_exec('ceph osd lspools --format=json');
$jsonPoolData = simple_curl("$ceph_api/osd/lspools");
$arrPoolData = json_decode($jsonPoolData, true)['output'];
//print_r($arrPoolData);

echo "<center>";
echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
echo " <tr>";
$i=0;
foreach ($arrPoolData as $object)
{
	if ($i % 5 == 0) {
		echo " </tr>";
		echo " <tr>";
	}
	$i++;
	echo "  <td>";
	$arrItem = json_decode(json_encode($object), True);
	$pool_name = $arrItem['poolname'];
	$pool_id = $arrItem['poolnum'];

	echo "   <input type=\"button\" style=\"width:90%\" value=\"Client B/W (Byte) : $pool_name($pool_id)\" onclick=\"window.open('client-bw-pool.php?pool_name=$pool_name&pool_id=$pool_id', 'Client B/W (Byte) : $pool_name($pool_id)', 'width=1024, height=800')\">";
	echo "<br>";
	echo "   <input type=\"button\" style=\"width:90%\" value=\"Client IOPS (Count) : $pool_name($pool_id)\" onclick=\"window.open('client-iops-pool.php?pool_name=$pool_name&pool_id=$pool_id', 'Client IOPS (Count) : $pool_name($pool_id)', 'width=1024, height=800')\">";
}
echo "  </td>";
echo " </tr>";
echo "</table>";

echo "<br><br>";

echo "<center>";
echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
echo " <tr>";
echo "  <td align='left'>";
echo "    <b>Inkscope-Lite</b>";
echo "  </td>";
echo " </tr>";
echo " <tr>";
echo "  <td>";
echo "    <input type=\"button\" value=\"OSD Map\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/osdMap.html', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"OSD Status\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/osds.html', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"OSD Performance\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/osdPerf.html', 'OSD Performance', 'width=500, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Pool Status\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/poolManagement.html', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Stuck PGs\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/pgStucks.html', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Object Lookup\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/objectLookup.html', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Erasure Profiles\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/erasureProfiles.html', '_blank')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"CRUSH Map\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/showCrushMap.html', '_blank')\">";
echo "  </td>";
echo " </tr>";
echo "</table>";

//echo "<center>";
//echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
//echo " <tr>";
//$i=0;
//foreach ($arrPoolData as $object)
//{
//	if ($i % 5 == 0) {
//		echo " </tr>";
//		echo " <tr>";
//	}
//	$i++;
//	echo "  <td>";
//	$arrItem = json_decode(json_encode($object), True);
//	$pool_name = $arrItem['poolname'];
//	$pool_id = $arrItem['poolnum'];
//
//	echo "<input type=\"button\" style=\"width:90%\" value=\"Client IOPS (Count) : $pool_name($pool_id)\" onclick=\"window.open('client-iops-pool.php?pool_name=$pool_name&pool_id=$pool_id', 'Client IOPS (Count) : $pool_name($pool_id)', 'width=1024, height=800')\">";
//}
//echo "  </td>";
//echo " </tr>";
//echo "</table>";

?>

</body>
</html>
