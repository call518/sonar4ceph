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
include 'functions.php';

//$date = (shell_exec("date"));
//echo "<pre><font color=black>$date</font></pre>";

$rawDataPG_DUMP = shell_exec("./check-osd_pg_state.sh");
$arrPG_DUMP = json_decode($rawDataPG_DUMP, true);
//var_dump($arrPG_DUMP);

// Pool Color
$tmpFile = "/tmp/sonar4ceph-pre-pools-count";
$pre_poolsCount = preg_replace('/\R/', '', shell_exec("cat $tmpFile"));
$poolsCount = shell_exec('ceph osd pool stats | grep -c "^pool"');
echo "$poolsCount <---- $pre_poolsCount";
if ($poolsCount != $pre_poolsCount) {
	$arrColors = array();
	for ($i=0; $i<$poolsCount; $i++) {
		array_push($arrColors, getRandomColor());
	}
}
$pre_poolsCountFile = fopen($tmpFile, "w") or die("Unable to open file!");
fwrite($pre_poolsCountFile, $poolsCount);
fclose($pre_poolsCountFile);


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
