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

<body onload="JavaScript:timedRefresh(10000);">

<?php
/* Here I am setting  up the variables for the commands to pass down through to the shell */

//$date = (shell_exec("date"));
//echo "<pre><font color=black>$date</font></pre>";

$rawDataPG_DUMP = shell_exec("./check-osd_pg_state.sh");
$arrPG_DUMP = json_decode($rawDataPG_DUMP, true);
//var_dump($arrPG_DUMP);

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

//=============================================================================================

//////////////////////////////////////
//           Functions              //
//////////////////////////////////////

function buildNode($nodeID) {
	global $nodes;

	$return = array(
		"name" => $nodes[$nodeID]->name,
		"status" => $nodes[$nodeID]->status,
		"type" => $nodes[$nodeID]->type,
		"reweight" => $nodes[$nodeID]->reweight,
		"kb" => $nodes[$nodeID]->kb,
		"kb_used" => $nodes[$nodeID]->kb_used,
		"kb_avail" => $nodes[$nodeID]->kb_avail,
		"utilization" => $nodes[$nodeID]->utilization,
		"var" => $nodes[$nodeID]->var,
		"pgs" => $nodes[$nodeID]->pgs,
		"children" => array()
	);

	if (count($nodes[$nodeID]->children)) {
		foreach ($nodes[$nodeID]->children as $child) {
			$return['children'][] = buildNode($child);
		}
	}

	return $return;
}

function getChildren($arr)
{
	global $arrPG_DUMP;
	//$color_root = "#7B68EE";
	//$color_datacenter = '#3CB371';
	//$color_rack = '#7B68EE';
	//$color_host = '#BDB76B';
	//$color_osd = '#20B2AA';

	$border_size = '5px';

	$color_root = "#787878";
	$color_datacenter = '#696969';
	$color_rack = '#585858';
	$color_host = '#484848';
	$color_osd = '#383838';

	//var_dump(rsort($arr));
	$name = $arr['name'];
	$status = $arr['status'];
	$type = $arr['type'];
	$kb = $arr['kb'];
	$kb_used = $arr['kb_used'];
	$kb_avail = $arr['kb_avail'];
	$utilization = $arr['utilization'];
	$var = $arr['var'];
	$pgs = $arr['pgs'];

	if ($type == "root") {
		echo "<center>";
		echo "<table class='type01' border='$border_size' bordercolor='$color_root'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_root'><b><font color='#FFFFFF'>Root:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
		showUsageBarGraph($utilization);
	} else if ($type == "datacenter") {
		echo "<table class='type01' border='$border_size' bordercolor='$color_datacenter'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_datacenter'><b><font color='#FFFFFF'>Datacenter:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
		showUsageBarGraph($utilization);
	} else if ($type == "rack") {
		echo "<table class='type01' style='float:left' width='300px' border='$border_size' bordercolor='$color_rack'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_rack'><b><font color='#FFFFFF'>Rack:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
		showUsageBarGraph($utilization);
	} else if ($type == "host") {
		echo "<center>";
		echo "<table class='type01' width='250px' border='$border_size' bordercolor='$color_host'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_host'><b><font color='#FFFFFF'>Host:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
		showUsageBarGraph($utilization);
	} else if ($type == "osd") {
		echo "<center>";
		echo "<table class='type01' width='200px' border='$border_size' bordercolor='$color_osd'><tr>";
		echo " <tr bgcolor='$color_osd'>";
		echo "  <td><b><font color='#FFFFFF'>OSD:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
		showUsageBarGraph($utilization);
		$osd_id = explode('.', $name)[1];
		//echo "   <center><a href='detail-osd.php?osd_id=$osd_id' target='_blank'>Detail</a>";
		echo "<br><center><input type=\"button\" value=\"Detail OSD-$osd_id\" onclick=\"window.open('detail-osd.php?osd_id=$osd_id', 'Detail of OSD.$osd_id', 'width=1024, height=800')\"><p>";
		//include "osd-pgs.php";
		//var_dump($arrPG_DUMP["osd_pg_state"]["osd_$osd_id"]);
		$chartData = convertPGDumpArray2ChartArray($arrPG_DUMP["osd_pg_state"]["osd_$osd_id"]);
		$arrLabels = $chartData[0];
		$arrDatasets = $chartData[1];
		//print_r($arrLabels);
		//print_r($arrDatasets);
?>
<canvas id="barChart<?php echo $osd_id; ?>"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.min.js'></script>

<script>
var canvas = document.getElementById("barChart<?php echo $osd_id; ?>");
var ctx = canvas.getContext('2d');

// Global Options:
// Chart.defaults.global.defaultFontColor = 'black';
// Chart.defaults.global.defaultFontSize = 16;

var data = {
    labels: <?php echo json_encode($arrLabels, JSON_NUMERIC_CHECK); ?>,
    datasets: [
        {
            fill: true,
            data: <?php echo json_encode($arrDatasets, JSON_NUMERIC_CHECK); ?>,
        }
    ]
};

// Notice the rotation from the documentation.

var options = {
        rotation: -0.7 * Math.PI
};


// Chart declaration:
var myBarChart = new Chart(ctx, {
    type: 'pie',
    data: data,
    options: options
});

// Fun Fact: I've lost exactly 3 of my favorite T-shirts and 2 hoodies this way :|
</script>

<?php
	}
	$children = $arr[children];
	if (count($children) > 0) {
		foreach (arr_sort($children, "name") as $item) {
			getChildren($item);
		}
	}
	if ($type == "root") {
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "datacenter") {
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "rack") {
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "host") {
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "osd") {
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	}
}

function arr_sort($array, $key, $sort='asc')
{
	$keys = array();
	$vals = array();
	foreach ($array as $k=>$v) {
		$i = $v[$key].'.'.$k;
		$vals[$i] = $v;
		array_push($keys, $k);
	}
	unset($array);
	if ($sort=='asc') {
		ksort($vals);
	} else {
		krsort($vals);
	}
	$ret = array_combine($keys, $vals);
	unset($keys);
	unset($vals);
	return $ret;
}

function formatKBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('KiB', 'MiB', 'GiB', 'TiB');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}


function showUsageBarGraph($utilization)
{
	echo "<table broder='0' width='100%' align='center' cellpadding='0' cellspacing='0' style='border-collapse: collapse;'>";
	echo " <tr height='20' colspan='0'>";
	echo "  <td width=$utilization% bgcolor='#FFA500'>";
	echo "  <td bgcolor='#32CD32'>";
	echo " </tr>";
	echo "</table>";
}

function convertPGDumpArray2ChartArray($arr)
{
	$arrLabels = array();
	$arrDatasets = array();
	//$total = $arr['total'];
	unset($arr['total']);
	foreach ($arr as $k=>$v) {
		//$item = array("label"=>"$k", "y"=>$v);
		//array_push($cahrtArray, $item);
		array_push($arrLabels, $k);
		array_push($arrDatasets, $v);
	}
	return array($arrLabels, $arrDatasets);
}
//////////////////////////////////////


?>

</body>
</html>
