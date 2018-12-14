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
include '_common.php';
include 'functions.php';

//$date = (shell_exec("date"));
//echo "<pre><font color=black>$date</font></pre>";

$rawDataPG_DUMP = shell_exec("./check-osd_pg_state.sh");
$arrPG_DUMP = json_decode($rawDataPG_DUMP, true);
//var_dump($arrPG_DUMP);

// Pool Color
$countPool = shell_exec('ceph osd pool stats | grep -c "^pool"');
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
$rawData = shell_exec('ceph osd df tree --format=json');
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
