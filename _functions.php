<?php
//////////////////////////////////////
//           Functions              //
//////////////////////////////////////

//=============================================================================================
function simple_curl($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_NOBODY, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
  $content = curl_exec($ch);
  $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
  curl_close($ch);
  //echo $content;
  return $content;
}

function buildNode($nodeID) {
	global $nodes;

	$return = array(
		"name" => $nodes[$nodeID]->name,
		//"status" => $nodes[$nodeID]->status,
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

	$color_root = "#EB984E";
	$color_datacenter = '#239B56';
	$color_rack = '#7D3C98';
	$color_host = '#2874A6';
	$color_osd = '#F1C40F';

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
		echo "  <td bgcolor='$color_root'><b><font color='#000000'>Root:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td bgcolor='#FAE5D3'>";
		showUsageBarGraph($utilization, $kb_used, $kb_avail);
		echo "<br>";
	} else if ($type == "datacenter") {
		echo "<table class='type01' border='$border_size' bordercolor='$color_datacenter'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_datacenter'><b><font color='#FFFFFF'>Datacenter:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td bgcolor='#D5F5E3'>";
		showUsageBarGraph($utilization, $kb_used, $kb_avail);
		echo "<br>";
	} else if ($type == "rack") {
		echo "<table class='type01' style='float:left' width='400px' border='$border_size' bordercolor='$color_rack'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_rack'><b><font color='#FFFFFF'>Rack:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td bgcolor='#E8DAEF'>";
		showUsageBarGraph($utilization, $kb_used, $kb_avail);
		echo "<br>";
	} else if ($type == "host") {
		echo "<center>";
		echo "<table class='type01' width='350px' border='$border_size' bordercolor='$color_host'><tr>";
		echo " <tr>";
		echo "  <td bgcolor='$color_host'><b><font color='#FFFFFF'>Host:</b> $name</td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td bgcolor='#AED6F1'>";
		showUsageBarGraph($utilization, $kb_used, $kb_avail);
		echo "<br>";
	} else if ($type == "osd") {
		$osd_id = explode('.', $name)[1];
		echo "<center>";
		echo "<table class='type01' width='300px' border='$border_size' bordercolor='$color_osd'><tr>";
		echo " <tr bgcolor='$color_osd'>";
		echo "  <td><b><font color='#000000'><input type=\"button\" value=\"Detail OSD-$osd_id\" onclick=\"window.open('detail-osd.php?osd_id=$osd_id', 'Detail of OSD.$osd_id', 'width=1024, height=800')\"></td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td bgcolor='#F9E79F'>";
		showUsageBarGraph($utilization, $kb_used, $kb_avail);
		$chartData = convertPGDumpArray2ChartArray($arrPG_DUMP["osd_pg_state"]["osd_$osd_id"]);
		$arrLabels = $chartData[0];
		$arrDatasets = $chartData[1];
		showPoolPGBarGraph($arrLabels, $arrDatasets, $arrColors);
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

    return round(pow(1024, $base - floor($base)), $precision) .''. $suffixes[floor($base)];
}


function showUsageBarGraph($utilization, $kb_used, $kb_avail)
{
	$pcnt_used = number_format($utilization, 2);
	$pcnt_avail = 100 - $pcnt_used;
	$byte_used = formatKBytes($kb_used);
	$byte_avail = formatKBytes($kb_avail);
	echo "<center><table broder='0' width='95%' align='center' cellpadding='0' cellspacing='0' style='border-collapse: collapse; margin: 5px;'>";
	echo " <tr height='20' colspan='0'>";
	echo "  <td width=$pcnt_used% bgcolor='#FFA500'><center><span style='font-size: 0.2em'>USE<br>($byte_used)</span>";
	echo "  <td bgcolor='#32CD32'><center><span style='font-size: 0.2em'>AVAIL<br>($byte_avail)</span>";
	echo " </tr>";
	echo "</table>";
}

function showPoolPGBarGraph($arrLabels, $arrDatasets, $arrColors)
{
	global $arrColors;
	echo "<center><table broder='0' width='95%' align='center' cellpadding='0' cellspacing='0' style='border-collapse: collapse; margin: 5px;'>";
	echo " <tr height='20' colspan='0'>";
	$total = 0;
	foreach ($arrDatasets as $k=>$v) {
		$total += $v;
	}
	foreach ($arrLabels as $k=>$v) {
		$label = $v;
		$pool_id = explode("pool_", $label)[1];
		$data = $arrDatasets[$k];
		$pcnt = floor(($data / $total) * 100);
		$color = $arrColors[$k];
		echo "  <td width=$pcnt% bgcolor='$color'><center><span style='font-size: 0.2em'>$pool_id<br>($data)</span>";
	}
	echo " </tr>";
	echo "</table>";
}

function getRandomColor() {
	$color = "#".substr(md5(rand()), 0, 6);
	return $color;
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

function array2table($data)
{
    $table = '
    <center><table class="type03" width="90%">
    ';
    foreach ($data as $key => $value) {
        $table .= '
        <tr valign="top">
        ';
        if ( ! is_numeric($key)) {
            $table .= ' 
            <th scope="row">'.$key.'</th>
            <td>
            ';
        } else {
            $table .= '
            <td colspan="2">
            ';
        }
        if (is_object($value) || is_array($value)) {
            $table .= array2table($value);
        } else {
            $table .= $value;
        }
        $table .= '
            </td>
        </tr>
        ';
    }
    $table .= '
    </table>
    ';
    return $table;
}
//=============================================================================================
?>
