<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Distribution Chart of PGs</title>

<!--
<script type="text/JavaScript">
window.addEventListener('load', function(){
    var select = document.getElementById('req_pool_id');

    select.addEventListener('change', function(){
        window.location = 'showDistributionPGs.php?req_pool_id=' + this.value;
    }, false);
}, false);
</script>
-->
</head>

<body>
<!--
<body onload="JavaScript:timedRefresh(60000);">
-->

<?php
include '_config.php';
include '_functions.php';

$req_pool_id = $_POST['req_pool_id'];
if (!$req_pool_id) {
	$req_pool_id = "all";
}

$req_osd_type = $_POST['req_osd_type'];
if (!$req_osd_type) {
	$req_osd_type = "acting";
}

if ($req_pool_id == "acting") {
	$chart_title = "ALL";
} else { 
	$chart_title = pool_id2name($req_pool_id)."(".$req_pool_id.")";
}

$arrTotalPoolList = getPoolList();

?>
<form id="form1" name="form1" method="post" action="showDistributionPGs.php">
<?php
echo "Pool: <select id=\"req_pool_id\" name=\"req_pool_id\">";
echo "<option value=\"all\">ALL</option>";
foreach ($arrTotalPoolList as $arrPoolInfo) {
	if ($arrPoolInfo['poolnum'] == $req_pool_id) {
?>
		<option value="<?php echo $arrPoolInfo['poolnum'] ?>" selected><?php echo $arrPoolInfo['poolname'] ?></option>
<?php
	} else {
		print_r($arrPoolInfo);
?>
		<option value="<?php echo $arrPoolInfo['poolnum'] ?>"><?php echo $arrPoolInfo['poolname'] ?></option>
<?php
	}
}
?>
</select>
&nbsp;&nbsp;&nbsp;
OSD: <select id="req_osd_type\" name="req_osd_type">
<option value="acting" <?php if ($req_osd_type == "acting") { echo "selected"; } ?>>Acting(ALL)</option>
<option value="acting_primary" <?php if ($req_osd_type == "acting_primary") { echo "selected"; } ?>>Acting Primary</option>
</select>
&nbsp;&nbsp;&nbsp;
<input type="submit" name="Submit" value="Submit"/>
</form>

<?php
$jsonData = simple_curl("$ceph_api/pg/dump_json?dumpcontents=pgs");
$arrPGStats = json_decode($jsonData, true)['output']['pg_stats'];

$arrSamplePGstates = array(
"active+clean",
"active+undersized",
"active+undersized+degraded",
"peering",
"activating",
"stale+active+clean",
"unknown",
);

$arrChartDatasets = array();

$transparency = 0.3;

${"color_active+clean"} = "0, 222, 0";
${"color_active+undersized"} = "209, 94, 255";
${"color_active+undersized+degraded"} = "255, 183, 64";
${"color_peering"} = "43, 88, 255";
${"color_activating"} = "255, 0, 243";
${"color_stale+active+clean"} = "11, 133, 164";
${"color_unknown"} = "50, 50, 50";

foreach ($arrSamplePGstates as $SampleState) {
	array_push($arrChartDatasets, array("label" => $SampleState, "hoverRadius" => 0, "backgroundColor" => "rgba(${'color_'.$SampleState}, $transparency)", "data" => array()));
}

foreach ($arrChartDatasets as $k => $v) {
	$label = $v['label'];
	${"arrKey_".$label} = $k;
}

foreach ($arrPGStats as $item_pg) {
	//$pg_ = $item_pg[''];
	$pg_id = $item_pg['pgid'];
	$pg_pool_id = explode('.', $pg_id)[0];
	$pg_hash_num16 = explode('.', $pg_id)[1];;
	$pg_hash_num10 = hexdec($pg_hash_num16);
	$pg_state = $item_pg['state'];
	$pg_state_var = str_replace('+', '_', $pg_state);
	$pg_acting_array = $item_pg['acting'];
	$pg_acting_primary = $item_pg['acting_primary'];
	$pg_up_array = $item_pg['up'];
	$pg_up_primary = $item_pg['up_primary'];

	if ($req_pool_id == $pg_pool_id || $req_pool_id == "all") {
		if ($req_osd_type == "acting_primary") {
			$osds = array($pg_acting_primary);
		} else {
			$osds = $pg_acting_array;
		}
		//print_r($osds);
		//foreach ($pg_acting_array as $osd) {
		foreach ($osds as $osd) {
			//$arrTMP = array('x' => $pg_pool_id.".".$pg_hash_num10, 'y' => $osd, 'r' => '10');
			$arrTMP = array('x' => $pg_hash_num10, 'y' => $osd, 'r' => '15');
			if (in_array($pg_state, $arrSamplePGstates)) {
				array_push($arrChartDatasets[${"arrKey_".$pg_state}]['data'], $arrTMP);
			} else {
				array_push($arrChartDatasets[$arrKey_unknown]['data'], $arrTMP);
			}
		}
	}
}

//print_r($arrChartDatasets);
?>

<canvas id="canvas"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script-->

<script type="text/javascript">
new Chart(document.getElementById("canvas"), {
    type: 'bubble',
    data: {
      labels: "HASH of PGs",
      datasets: <?php echo json_encode($arrChartDatasets); ?>
    },
    options: {
      title: {
        display: true,
        padding: 30,
        text: '<?php echo $chart_title; ?> - PGs Distribution & State'
      },
      legend: {
        display: true,
        position: 'right',
      },
      scales: {
        yAxes: [{ 
          scaleLabel: {
            display: true,
            labelString: "OSD ID"
          },
          ticks: {
            autoSkip: false,
            stepSize: 1,
            callback: function(value) {
              return "OSD-" + value;
            }
          }
        }],
        xAxes: [{ 
          scaleLabel: {
            display: true,
            labelString: "PG Hash-Number"
          },
          ticks: {
            autoSkip: false,
            stepSize: 50,
            callback: function(value) {
              return value + " (" + value.toString(16) + ")";
            }
          }
        }]
      },
      tooltips: {
        callbacks: {
          label: function(t, d) {
            //console.log(t);
            //return 'PGID: ' + <?php echo $pg_pool_id; ?> + '.' + num10_t0_num16(t.xLabel) + ' (OSD:' + get_osd_int(t.yLabel) + ')';
            return 'PGID: ' + num10_to_num16(t.xLabel) + '(' + t.xLabel + ')' + ', OSD:' + get_osd_int(t.yLabel);
          }
        }
      },
    }
});

function get_pool_id(num10) {
  return Number(String(num10).split('.')[0]);
}

function get_osd_int(num) {
  return Math.floor(num)
}

function num10_to_num16(num10) {
  //var result = Number(String(num10).split('.')[1]).toString(16);
  var result = num10.toString(16);
  return result;
}

</script>

</body>
</html>
