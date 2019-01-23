<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>States Chart of PGs</title>

<script type="text/JavaScript">
//function timedRefresh(timeoutPeriod) {
//	setTimeout("location.reload(true);",timeoutPeriod);
//}
//window.addEventListener('load', function(){
//    var select = document.getElementById('req_pool_id');
//
//    select.addEventListener('change', function(){
//        window.location = 'showPGsStates.php?req_pool_id=' + this.value;
//    }, false);
//}, false);
</script>
</head>

<!--body onload="JavaScript:timedRefresh(1000);"-->
<body>

<?php
include '_config.php';
include '_functions.php';

$chart_title = "";

$req_pool_id = $_POST['req_pool_id'];
if ($req_pool_id == "all" || !$req_pool_id) {
	$req_pool_id = "all";
	$chart_title = "ALL(ALL)";
} else {
	$chart_title = pool_id2name($req_pool_id)."(".$req_pool_id.")";
}

$req_pg_type = $_POST['req_pg_type'];
if (!$req_pg_type) {
	//$req_pg_type = "acting";
	$req_pg_type = "acting_primary";
}

if ($req_pg_type == "acting") {
	$chart_title .= "(acting)";
} else { 
	$chart_title .= "(acting_primary)";
}

$arrTotalPoolList = getPoolList();

?>
<form id="form1" name="form1" method="post" action="showPGsStates.php">
<?php
//date_default_timezone_set($default_time_zone);
//echo "<b>".date("Y-m-d H:i:s")."</b>";
//echo "&nbsp;&nbsp;&nbsp;";
echo "Pool: <select id=\"req_pool_id\" name=\"req_pool_id\">";
echo "<option value=\"all\">ALL</option>";
foreach ($arrTotalPoolList as $arrPoolInfo) {
	if ($arrPoolInfo['poolnum'] == $req_pool_id) {
?>
		<option value="<?php echo $arrPoolInfo['poolnum'] ?>" selected><?php echo $arrPoolInfo['poolname'] ?></option>
<?php
	} else {
		//print_r($arrPoolInfo);
?>
		<option value="<?php echo $arrPoolInfo['poolnum'] ?>"><?php echo $arrPoolInfo['poolname'] ?></option>
<?php
	}
}
?>
</select>
&nbsp;&nbsp;&nbsp;
PG Type:
<select id="req_pg_type" name="req_pg_type">
<option value="acting" <?php if ($req_pg_type == "acting") { echo "selected"; } ?>>Acting(ALL)</option>
<option value="acting_primary" <?php if ($req_pg_type == "acting_primary") { echo "selected"; } ?>>Acting Primary</option>
</select>
&nbsp;&nbsp;&nbsp;
<input type="submit" name="Submit" value="Submit"/>
</form>

<canvas id="canvas"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.js'></script>
<!--
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.js'></script>
-->

<script type="text/javascript">

Chart.plugins.register({
   afterDatasetsDraw: c => {
      let datasets = c.data.datasets;
      datasets.forEach((dataset, i_dataset) => {
          let data = dataset.data;
          data.forEach((data, i_data) => {
              let pool_id = data.pool_id;
              let osd = data.y;
              let primary_osd = data.primary_osd;

              let ctx = c.chart.ctx;
              let meta = c.getDatasetMeta(i_dataset).data[i_data];
              let x = meta._model.x;
              let y = meta._model.y;
              let r = meta._model.radius;

              ctx.save();
              if (osd == primary_osd) {
                  //console.log(meta);
                  //console.log(data);

                  // draw a cross
                  // or you can draw anything using general canvas methods

                  //ctx.beginPath();
                  //ctx.moveTo(x - r / 4, y);
                  //ctx.lineTo(x + r / 4, y);
                  //ctx.moveTo(x, y + r / 4);
                  //ctx.lineTo(x, y - r / 4);
                  //ctx.strokeStyle = '#001FFF';
                  //ctx.lineWidth = 1;
                  //ctx.stroke();
                  ctx.textAlign = "center";
                  ctx.textBaseline = 'middle';
                  ctx.fillStyle = "#0000FF";
                  ctx.font = 'normal 10px Helvetica';
                  ctx.fillText("P", x - 1, y + 1);
              };
              ctx.restore();
          });
      });
   }
});

var ctx_live = document.getElementById("canvas");
var Chart = new Chart(ctx_live, {
    plugins: [{
    }],
    type: 'bubble',
    data: {
//      labels: "HASH of PGs",
//      //datasets: <?php echo json_encode($arrChartDatasets); ?>
      datasets: [],
    },
    options: {
      animation: false,
      title: {
        display: true,
        padding: 30,
        text: '<?php echo $chart_title; ?> - PGs States',
        fontSize: 20,
      },
      legend: {
        display: true,
        position: 'right',
      },
      scales: {
        yAxes: [{ 
          //offset: true,
          scaleLabel: {
            display: true,
            labelString: "OSD ID"
          },
          ticks: {
            autoSkip: false,
            min: 0,
            max: get_max_OSD_num(),
            stepSize: 1,
            callback: function(value) {
              return "OSD-" + value;
            }
          }
        }],
        xAxes: [{ 
          //offset: true,
          scaleLabel: {
            display: true,
            labelString: "PG Hash-Number"
          },
          ticks: {
            autoSkip: false,
            min: 0,
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
            //console.log(d);
            var pg_state = d.datasets[t.datasetIndex].label
            var pg_pool_id = d.datasets[t.datasetIndex].data[t.index].pool_id
            var pg_primary_osd = d.datasets[t.datasetIndex].data[t.index].primary_osd
            var pg_is_primary = "(N/A)";
            if (t.yLabel == pg_primary_osd) {
              str_pg_primary_replica = "P";
            } else {
              str_pg_primary_replica = "R";
            }
            //return 'PGID: ' + <?php echo $pg_pool_id; ?> + '.' + num10_t0_num16(t.xLabel) + ' (OSD:' + get_osd_int(t.yLabel) + ')';
            return 'PGID(' + str_pg_primary_replica + '): ' + pg_pool_id + '.' + num10_to_num16(t.xLabel) + '(' + t.xLabel + ')' + ', OSD: ' + get_osd_int(t.yLabel) + ', STATE: ' + pg_state;
          }
        }
      },
    }
});

var arr_backgroundColor = new Array();

// logic to get new data
var getData = function() {
  $.ajax({
    type: 'POST',
    //url: 'jq-pool-client-io.php?pool_name=<?php echo $pool_name; ?>&pool_id=<?php echo $pool_id; ?>',
    url: 'jq-pg-stats.php',
    data: {
      "req_pool_id": "<?php echo $req_pool_id; ?>",
      "req_pg_type": "<?php echo $req_pg_type; ?>"
    },
    success: function(data) {
      Chart.options.title.text = '[' + getNow() + ']  ' + '<?php echo $chart_title; ?> - PGs States';

      var parsed_data = JSON.parse(data);
      var max_pg_number = 0;
      parsed_data.forEach(datasets => {
        var dataset = datasets.data;
        dataset.forEach(data => {
          if (data.x > max_pg_number) {
            max_pg_number = data.x
          }
        });
      });
      //Chart.options.scales.xAxes[0].ticks.max = max_pg_number + 10;

      var arrLabels = new Array();
      for (var key in parsed_data) {
        //console.log(parsed_data[key].label);
        arrLabels.push(parsed_data[key].label);
      }
      Chart.data.lables = arrLabels;

      parsed_data.forEach((dataset, i) => {
        if (dataset.label in arr_backgroundColor) {
          parsed_data[i].backgroundColor = arr_backgroundColor[dataset.label];
        } else {
          now_backgroundColor = random_rgba(0.3);
          arr_backgroundColor[dataset.label] = now_backgroundColor;
          parsed_data[i].backgroundColor = now_backgroundColor;
        }
      });

      Chart.data.datasets = parsed_data;

      // re-render the chart
      Chart.update();
    }
  });
};


$(document).ready(getData);

// get new data every 3 seconds
setInterval(getData, <?php echo $refresh_interval_PG_Stats; ?>);

function get_osd_int(num) {
  return Math.floor(num)
}

function num10_to_num16(num10) {
  //var result = Number(String(num10).split('.')[1]).toString(16);
  var result = num10.toString(16);
  return result;
}

function get_max_OSD_num() {
	return <?php echo max(uniq_OSD_list()); ?>
}

function random_rgba(transparency) {
    var r = Math.floor(Math.random() * (255 - 0 + 1)) + 0;
    var g = Math.floor(Math.random() * (255 - 0 + 1)) + 0;
    var b = Math.floor(Math.random() * (255 - 0 + 1)) + 0;
    return 'rgba(' + r + ',' + g + ',' + b + ',' + transparency + ')';
}

function getNow() {
  now = new Date();
  year = "" + now.getFullYear();
  month = "" + (now.getMonth() + 1); if (month.length == 1) { month = "0" + month; }
  day = "" + now.getDate(); if (day.length == 1) { day = "0" + day; }
  hour = "" + now.getHours(); if (hour.length == 1) { hour = "0" + hour; }
  minute = "" + now.getMinutes(); if (minute.length == 1) { minute = "0" + minute; }
  second = "" + now.getSeconds(); if (second.length == 1) { second = "0" + second; }
  //return year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
  return hour + ":" + minute + ":" + second;
}

</script>

</body>
</html>
