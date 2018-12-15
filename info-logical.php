<html>
<title> CEPH - Logical Info </title>
<head>
<style>
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

echo "<center>";
echo "<table class='type01' border='0' cellpadding='10'><tr>";
echo " <tr>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Dump-Info - Pools\" onclick=\"window.open('dump-info-pools.php', 'Dump-Info - Pools', 'width=1024, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" value=\"Dump-Info - OSDs\" onclick=\"window.open('dump-info-osds.php', 'Dump-Info - OSDs', 'width=1024, height=800')\">";
echo "  </td>";
echo "  <td>";
echo "    <input type=\"button\" style=\"width: 100%\" value=\"Dump-Info - PGs\" onclick=\"window.open('dump-info-pgs.php', 'Dump-Info - PGs', 'width=1024, height=800')\">";
echo "  </td>";
echo " </tr>";
echo "</table>";


//$jsonPoolData = shell_exec('ceph osd lspools --format=json');
$jsonPoolData = simple_curl("$ceph_api/osd/lspools");
$arrPoolData = json_decode($jsonPoolData, true)['output'];
//print_r($arrPoolData);

echo "<center>";
echo "<table class='type01' border='0' cellpadding='10'><tr>";
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

	echo "<input type=\"button\" value=\"Client I/O : $pool_name($pool_id)\" onclick=\"window.open('client-io-pool.php?pool_name=$pool_name&pool_id=$pool_id', 'Client I/O : $pool_name($pool_id)', 'width=1024, height=800')\">";
}
echo "  </td>";
echo " </tr>";
echo "</table>";

//$date = (shell_exec("date"));
//echo "<pre><font color=black>$date</font></pre>";

$rawDataPG_DUMP = shell_exec("./check-osd_pg_state.sh");
// $arrPG_DUMP -> _functions.php의 getChildren() 함수가 참조. 
$arrPG_DUMP = json_decode($rawDataPG_DUMP, true);

// Pool Color
//$countPool = shell_exec('ceph osd pool stats | grep -c "^pool"');
$jsonDataCountPool = simple_curl("$ceph_api/osd/pool/stats");
$countPool = count(json_decode($jsonDataCountPool, true)['output']);
$countPool_pre = file_get_contents($prePoolCountFile);
if ($countPool != $countPool_pre or count(json_decode(file_get_contents($PoolColorFile), true)) == 0) {
	$arrColors = array();
	for ($i=0; $i<$countPool; $i++) {
		$color = getRandomColor();
		array_push($arrColors, $color);
	}
	file_put_contents($PoolColorFile, json_encode($arrColors));
	file_put_contents($prePoolCountFile, $countPool);
} else {
	$arrColors = json_decode(file_get_contents($PoolColorFile), true);
}


//$rawData = shell_exec('ceph osd tree --format=json');
//$rawData = shell_exec('ceph osd df tree --format=json');
$rawData = json_encode(json_decode(simple_curl("$ceph_api/osd/df?output_method=tree"), true)['output']);
$input_json = json_decode($rawData);
//var_dump($input_json);
$nodes = array();

foreach ($input_json->nodes as $node)
{
	$nodes[$node->id] = $node;
}

$nodeTree = buildNode(-1);

//var_dump(json_encode($nodeTree));

getChildren($nodeTree);

?>

</body>
</html>
