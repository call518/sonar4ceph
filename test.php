<html>
<title> System Stats </title>
<head>
<script type="text/JavaScript">
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
</script>
</head>

<body onload="JavaScript:timedRefresh(5000);">
<?php
/* Here I am setting  up the variables for the commands to pass down through to the shell */

#$date = (shell_exec("date"));
#echo "<pre><font color=black>$date</font></pre>";

$file = shell_exec('ceph osd tree --format=json');
$input_json = json_decode($file);
$nodes = array();

foreach ( $input_json->nodes as $node )
{
	$nodes[$node->id] = $node;
}

function buildNode($nodeID) {
	global $nodes;

	$return = array(
		"name" => $nodes[$nodeID]->name,
		"status" => $nodes[$nodeID]->status,
		"type" => $nodes[$nodeID]->type,
		"children" => array()
	);

	if (count($nodes[$nodeID]->children)) {
		foreach ($nodes[$nodeID]->children as $child) {
			$return['children'][] = buildNode($child);
		}
	}

	return $return;
}

$nodeTree = buildNode(-1);

#$output = json_encode($nodeTree);
#var_dump($output);

getChildren($nodeTree);


######################################
## Functions
######################################
function getChildren($arr) {
	#var_dump(rsort($arr));
	$name = $arr[name];
	$status = $arr[status];
	$type = $arr[type];
	if ($type == "root") {
		#echo "==== root start ==============<br>";
		echo "<table border='2'><tr>";
		echo " <tr>";
		echo "  <td><b>$name</b></td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
	} else if ($type == "datacenter") {
		#echo "==== datacenter start ==============<br>";
		echo "<table border='2'><tr>";
		echo " <tr>";
		echo "  <td><b>$name</b></td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
	} else if ($type == "rack") {
		#echo "==== rack start ==============<br>";
		echo "<table border='2' style='float: left'><tr>";
		echo " <tr>";
		echo "  <td><b>$name</b></td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
	} else if ($type == "host") {
		#echo "==== host start ==============<br>";
		echo "<table border='2'><tr>";
		echo " <tr>";
		echo "  <td><b>$name</b></td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
	} else if ($type == "osd") {
		#echo "==== osd start ==============<br>";
		echo "<table border='2'><tr>";
		echo " <tr>";
		echo "  <td><b>$name</b></td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td>";
	}
	#echo "<pre><font color=black>name: $name</font></pre>";
	#echo "<pre><font color=black>status: $status</font></pre>";
	#echo "<pre><font color=black>type: $type</font></pre>";
	$children = $arr[children];
	if (count($children) > 0) {
		foreach (arr_sort($children, "name") as $item) {
			getChildren($item);
		}
	}
	if ($type == "root") {
		#echo "==== root stop ==============<br>";
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "datacenter") {
		#echo "==== datacenter stop ==============<br>";
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "rack") {
		#echo "==== rack stop ==============<br>";
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "host") {
		#echo "==== host stop ==============<br>";
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	} else if ($type == "osd") {
		#echo "==== osd stop ==============<br>";
		echo "  </td>";
		echo " </tr>";
		echo "</table>";
	}
}

function arr_sort($array, $key, $sort='asc') {
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
######################################


?>

</body>
</html>
