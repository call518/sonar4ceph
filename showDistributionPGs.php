<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Distribution Chart of PGs</title>

<script type="text/JavaScript">
//function timedRefresh(timeoutPeriod) {
//	setTimeout("location.reload(true);",timeoutPeriod);
//}
//window.addEventListener('load', function(){
//    var select = document.getElementById('req_pool_id');
//
//    select.addEventListener('change', function(){
//        window.location = 'showDistributionPGs.php?req_pool_id=' + this.value;
//    }, false);
//}, false);
</script>
</head>

<!--body onload="JavaScript:timedRefresh(1000);"-->
<body>

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
date_default_timezone_set($default_time_zone);
echo "<b>(".date("Y-m-d H:i:s").")</b>";
echo "&nbsp;&nbsp;&nbsp;";
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

<canvas id="canvas"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js'></script-->
<!--script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js'></script-->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>

<script type="text/javascript">
var ctx_live = document.getElementById("canvas");
var Chart = new Chart(ctx_live, {
    type: 'bubble',
    data: {
      labels: "HASH of PGs",
      //datasets: <?php echo json_encode($arrChartDatasets); ?>
      datasets: [],
    },
    options: {
      animation: false,
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

// logic to get new data
var getData = function() {
  $.ajax({
    type: 'POST',
    //url: 'jq-pool-client-io.php?pool_name=<?php echo $pool_name; ?>&pool_id=<?php echo $pool_id; ?>',
    url: 'jq-pg-stats.php',
    data: {
      "req_pool_id": "<?php echo $req_pool_id; ?>",
      "req_osd_type": "<?php echo $req_osd_type; ?>"
    },
    success: function(data) {
      //Chart.data.datasets = [];
      //console.log(data);
      //alert(data);
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      var parsed_data = JSON.parse(data);
      console.log(parsed_data);
      //for(var k in parsed_data) {
      //  console.log(k, parsed_data[k]);
      //  Chart.data.datasets.push(parsed_data[k]);
      //}
      Chart.data.datasets = parsed_data;
      //console.log(parsed_data.write_bytes_sec);
      //Chart.data.datasets[0].data.push(parsed_data.read_bytes_sec);
      //Chart.data.datasets[1].data.push(parsed_data.write_bytes_sec);
      
      // re-render the chart
      Chart.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData, <?php echo $refresh_interval_PG_Stats; ?>);

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
