<html>
<title> CEPH - Logical Info </title>
<head>
<style>
table.type00 {
	border-collapse: collapse;
	text-align: left;
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
window.addEventListener('load', function(){
    var select = document.getElementById('root_node');

    select.addEventListener('change', function(){
        window.location = 'show-physical.php?rootNodeId=' + this.value;
    }, false);
}, false);
</script>
</head>

<body onload="JavaScript:timedRefresh(5000);">

<?php
include '_config.php';
include '_functions.php';

$pre_rootNodeId = $_GET['rootNodeId'];
if ($pre_rootNodeId && $pre_rootNodeId != "undefined") {
	$rootNodeId = $pre_rootNodeId;
} else {
	$rootNodeId = -1;
}
$rootIDs = getRootNodeIDs();
$rootNodeName = array_search($rootNodeId, $rootIDs);
//print_r($rootNodeId);
//print_r($rootNodeName);
?>

<?php
//echo "<center>";
//echo "<table class='type00' border='0' cellpadding='5'><tr>";
//echo " <tr>";
//echo "  <td>";
//echo "    Root: <select id=\"root_node\" name=\"root_node\">";
//foreach ($rootIDs as $k => $v) {
//	if ($rootNodeId == $v) {
//		echo "      <option value=\"$v\" selected>$k</option>";
//	} else {
//		echo "      <option value=\"$v\">$k</option>";
//	}
//}
//echo "    </select>";
//echo "  </td>";
//echo " </tr>";
//echo "</table>";


//$date = (shell_exec("date"));
//echo "<pre><font color=black>$date</font></pre>";

//$rawDataPG_DUMP = shell_exec("./check-osd_pg_state.sh");
$rawDataPG_DUMP = check_osd_pg_status();
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


// buildNode("Root Node ID")
//echo $rootNodeId;
if (!empty($rootNodeId) && !empty($rootNodeName)) {
	$nodeTree = buildNode($rootNodeId);

	//var_dump(json_encode($nodeTree));

	getChildren($nodeTree);
}

?>

</body>
</html>
