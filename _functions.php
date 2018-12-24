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

function getRootNodeIDs()
{
	global $ceph_api;
	$result = array();
	$jsonPoolData = simple_curl("$ceph_api/osd/df?output_method=tree");
	$arrPoolData = json_decode($jsonPoolData, true)['output'];
	$arrAllNodes = array_column($arrPoolData['nodes'], "type");
	$arrRootNodeKeys = array_keys($arrAllNodes, "root");
	foreach ($arrRootNodeKeys as $rootNodeKey)
	{
		$id = $arrPoolData['nodes'][$rootNodeKey]['id'];
		$name = $arrPoolData['nodes'][$rootNodeKey]['name'];
		$result[$name] = $id;
	}
    return $result;
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
		echo "  <td bgcolor='$color_root'><b><font color='#000000'>Root:</b>";
		global $rootIDs;
		echo "    <select id=\"root_node\" name=\"root_node\">";
		foreach ($rootIDs as $k => $v) {
			if ($rootNodeId == $v) {
				echo "      <option value=\"$v\" selected>$k</option>";
			} else {
				echo "      <option value=\"$v\">$k</option>";
			}
		}
		echo "    </select>";
		echo "  </td>";
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
		$chartData = convertPGDumpArray2ChartArray($arrPG_DUMP["osd_pg_state"]["osd_$osd_id"]);
		$arrLabels = $chartData[0];
		$arrDatasets = $chartData[1];
		$td_bgcolor_osd = "#F9E79F";
		if (count($arrLabels) == 0 || count($arrDatasets) == 0) {
			$color_osd = "#D60000";
			$td_bgcolor_osd = "#FF7400";
		}
		echo "<center>";
		echo "<table class='type01' width='300px' border='$border_size' bordercolor='$color_osd'><tr>";
		echo " <tr bgcolor='$color_osd'>";
		echo "  <td>";
		echo "   <b><font color='#000000'><input type=\"button\" value=\"Detail OSD-$osd_id\" onclick=\"window.open('detail-osd.php?osd_id=$osd_id', 'Detail of OSD.$osd_id', 'width=1024, height=800')\">";
		if (count($arrLabels) == 0 || count($arrDatasets) == 0) {
			echo "   <b>WARNING!!!</b>";
		}
		echo "  </td>";
		echo " </tr>";
		echo " <tr>";
		echo "  <td bgcolor='$td_bgcolor_osd'>";
		showUsageBarGraph($utilization, $kb_used, $kb_avail);
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
    <!--<center><table class="type03" width="90%">-->
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

function getPGDump()
{
	global $ceph_api;
	$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
	return json_decode($jsonData, true);
}

function uniq_OSD_list() {
	$arr = getPGDump();
	$arrStat = $arr['output']['osd_stats'];
	return array_column($arrStat, "osd");
}

function uniq_Pool_list() {
	$arr = getPGDump();
	$arrStat = $arr['output']['pool_stats'];
	return array_column($arrStat, "poolid");
}

function check_osd_pg_status() {
	$arrData = getPGDump();
	$pg_stats = $arrData['output']['pg_stats'];
	$arrPG_IDs = array_column($pg_stats, "pgid");
	$arrPG_Up = array_column($pg_stats, "up");

	$arr_PG_and_Up_OSDs = array();
	foreach ($arrPG_IDs as $k => $v) {
		$up_osds = $arrPG_Up[$k];
		$arrTmp = array($v => $up_osds);
		$arr_PG_and_Up_OSDs[$v] = $up_osds;
	}
	ksort($arr_PG_and_Up_OSDs);

	$arrResult = array("osd_pg_state" => array());

	foreach ($arr_PG_and_Up_OSDs as $pg_id => $osds) {
		foreach ($osds as $osd_num) {
			$osd_key = "osd_".$osd_num;
			if (array_key_exists($osd_key, $arrResult['osd_pg_state']) != true) {
				$arrResult['osd_pg_state'][$osd_key] = array();
			}
		}
		$pool_key = "pool_".explode('.', $pg_id)[0];
		foreach ($osds as $osd_num) {
			$osd_key = "osd_".$osd_num;
			if (array_key_exists($pool_key, $arrResult['osd_pg_state'][$osd_key]) != true) {
				$arrResult['osd_pg_state'][$osd_key][$pool_key] = 0;
			}
			$arrResult['osd_pg_state'][$osd_key][$pool_key] += 1;
		}
	}

	foreach ($arrResult['osd_pg_state'] as $osd_key => $item) {
		$total_pg_count_of_osd = 0;
		foreach ($item as $pg_count) {
			$total_pg_count_of_osd += $pg_count;
		}
		$arrResult['osd_pg_state'][$osd_key]['total'] = $total_pg_count_of_osd;
	}

	$arrResult['total_pgs_pool'] = array();
	foreach ($arrResult['osd_pg_state'] as $item) {
		unset($item['total']);
		foreach ($item as $pool_key => $pg_count) {
			if (array_key_exists($pool_key, $arrResult['total_pgs_pool']) != true) {
				$arrResult['total_pgs_pool'][$pool_key] = 0;
			}
			$arrResult['total_pgs_pool'][$pool_key] += $pg_count;
		}
	}

	ksort($arrResult['osd_pg_state'], SORT_NATURAL);
	foreach ($arrResult['osd_pg_state'] as $k => $v) {
		ksort($arrResult['osd_pg_state'][$k], SORT_NATURAL);
	}
	ksort($arrResult['total_pgs_pool'], SORT_NATURAL);

	return json_encode($arrResult);
}

function getStatOSD()
{
	global $ceph_api;
	$jsonData = simple_curl("$ceph_api/osd/stat");
	$arrData = json_decode($jsonData, true);
	$arrOSDStat = $arrData['output'];
	return $arrOSDStat;
}

function pool_name2id($pool_name) {
	global $ceph_api;
	$jsonData = simple_curl("$ceph_api/osd/lspools");
	$arrData = json_decode($jsonData, true)['output'];

	$arrIndex = array_search($pool_name, array_column($arrData, "poolname"));

	return $arrData[$arrIndex]['poolnum'];
}

function pool_id2name($pool_id) {
	global $ceph_api;
	$jsonData = simple_curl("$ceph_api/osd/lspools");
	$arrData = json_decode($jsonData, true)['output'];

	$arrIndex = array_search($pool_id, array_column($arrData, "poolnum"));

	return $arrData[$arrIndex]['poolname'];
}

function randomRBGA4ChartJS($transparency)
{
	$r = mt_rand(0, 255);
	$g = mt_rand(0, 255);
	$b = mt_rand(0, 255);
    return "rgba($r, $g, $b, $transparency)";
}
 
//=============================================================================================
?>
