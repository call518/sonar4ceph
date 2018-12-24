<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Distribution Chart of PGs</title>
</head>

<body onload="JavaScript:timedRefresh(60000);">

<?php
include '_config.php';
include '_functions.php';

$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

//$total_pool_count = count(json_decode($jsonData, true)['output']['pool_stats']) + 1;
//$step_pool_X_Axes = round((1/$total_pool_count), 2);

$jsonPoolListData = simple_curl("$ceph_api/osd/lspools");
$arrPoolList = json_decode($jsonPoolListData, true)['output'];
//print_r($arrPoolList);

$arrPoolStep = array();
foreach ($arrPoolList as $key => $item) {
	$pool_count = count($arrPoolList) + 1;
	$pool_name = $item['poolname'];
	$arrPoolStep[$pool_name] = $key * round((1 / $pool_count), 2);
}
//print_r($arrPoolStep);
//exit;

//print_r($step_pool_X_Axes);
//exit;

//print_r($arrPGStats);
//exit;

$arrChartDatasets = array();
$arrPool_IDs = array();

foreach ($arrPGStats as $item_pg) {
	//$pg_ = $item_pg[''];
	$pg_id = $item_pg['pgid'];
	$pg_pool_id = explode('.', $pg_id)[0];
	$pg_hash_num = hexdec(explode('.', $pg_id)[1]);
	if (empty(${'pool_name_of_' . $pg_pool_id})) { 
		$pg_pool_name = pool_id2name($pg_pool_id);
		${'pool_name_of_' . $pg_pool_id} = $pg_pool_name;
		if (empty(${'pool_color_of_' . $pg_pool_id})) {
			$pg_pool_color = randomRBGA4ChartJS(0.7);
			${'pool_color_of_' . $pg_pool_id} = $pg_pool_color;
		} else {
			$pg_pool_color = ${'pool_color_of_' . $pg_pool_id};
		}
		if (empty(${'arrData_pool_' . $pg_pool_id})) { 
			${'arrData_pool_' . $pg_pool_id} = array("label" => "$pg_pool_name($pg_pool_id)", "backgroundColor" => $pg_pool_color, "data" => array());
			array_push($arrPool_IDs, $pg_pool_id);
		}
	} else {
		$pg_pool_name = ${'pool_name_of_' . $pg_pool_id};
	}
	if (empty(${'pool_repl_size_of_' . $pg_pool_name})) {
		$pg_pool_repl_size = json_decode(simple_curl("$ceph_api/osd/pool/get?pool=$pg_pool_name&var=size"), true)['output']['size'];
		${'pool_repl_size_of_' . $pg_pool_name} = $pg_pool_repl_size;
	} else {
		$pg_pool_repl_size = ${'pool_repl_size_of_' . $pg_pool_name};
	}
	$pg_state = $item_pg['state'];
	$pg_acting_array = $item_pg['acting'];
	$pg_acting_primary = $item_pg['acting_primary'];
	$pg_up_array = $item_pg['up'];
	$pg_up_primary = $item_pg['up_primary'];

	$chart_r = 5;
	if ($pg_state != "active+clean") {
		$chart_r = 10;
	}
	foreach ($pg_acting_array as $osd) {
		$arrTMP = array('x' => $osd + $arrPoolStep[$pg_pool_name], 'y' => $pg_hash_num, 'r' => $chart_r);
		array_push(${'arrData_pool_' . $pg_pool_id}['data'], $arrTMP);
	}
}

//print_r($arrPool_IDs);
foreach ($arrPool_IDs as $pg_pool_id) {
	array_push($arrChartDatasets, ${'arrData_pool_' . $pg_pool_id});
}
//print_r(json_encode($arrChartDatasets));
?>


<canvas id="bubble-chart"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>

<script type="text/javascript">
new Chart(document.getElementById("bubble-chart"), {
    type: 'bubble',
    data: {
      labels: "Africa",
      datasets: <?php echo json_encode($arrChartDatasets); ?>
    },
    options: {
      title: {
        display: true,
        text: 'Distribution Chart of PGs'
      },
      scales: {
        yAxes: [{ 
          ticks: {
            autoSkip: false,
            stepSize: 10,
          },
          scaleLabel: {
            display: true,
            labelString: "HASH of PG"
          }
        }],
        xAxes: [{ 
          ticks: {
            autoSkip: false,
            stepSize: 1,
          },
          scaleLabel: {
            display: true,
            labelString: "ID of OSD"
          }
        }]
      }
    }
});
</script>

</body>
</html>
