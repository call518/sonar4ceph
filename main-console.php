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
echo "<center>";
echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
echo " <tr>";
echo "  <td colspan=\"100%\">";
echo "    <center><input type=\"button\" value=\"Show Physical\" style=\"width:90%\" onclick=\"window.open('show-physical.php', '_blank')\">";
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
echo "    <input type=\"button\" value=\"Pools/OSDs/PGs\" style=\"width:90%\" onclick=\"window.open('inkscope-lite/poolspgsosds.html', '_blank')\">";
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

echo "<center>";
echo "<table class='type00' border='0' cellpadding='5' width=90%><tr>";
echo " <tr>";
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

	echo "<input type=\"button\" style=\"width:90%\" value=\"Client B/W (Byte) : $pool_name($pool_id)\" onclick=\"window.open('client-bw-pool.php?pool_name=$pool_name&pool_id=$pool_id', 'Client B/W (Byte) : $pool_name($pool_id)', 'width=1024, height=800')\">";
	echo "<br>";
	echo "<input type=\"button\" style=\"width:90%\" value=\"Client IOPS (Count) : $pool_name($pool_id)\" onclick=\"window.open('client-iops-pool.php?pool_name=$pool_name&pool_id=$pool_id', 'Client IOPS (Count) : $pool_name($pool_id)', 'width=1024, height=800')\">";
}
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
