<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Distribution Chart of PGs</title>

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

$arrTotalPoolList = getPoolList();

?>
<form id="form1" name="form1" method="post" action="showSizeOfPGs.php">
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

var ctx_live = document.getElementById("canvas");
var Chart = new Chart(ctx_live, {
    plugins: [{
    }],
    type: 'bubble',
    data: {
      //labels: "HASH of PGs",
      //datasets: <?php echo json_encode($arrChartDatasets); ?>
      datasets: [],
    },
    options: {
      animation: false,
      title: {
        display: true,
        padding: 30,
        text: '<?php echo $chart_title; ?> - PGs Size (Primary PG)',
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
            labelString: "Size"
          },
          ticks: {
            autoSkip: false,
            min: 0,
            //max: get_max_OSD_num(),
            //stepSize: 10*1024*1024,
            //callback: function(value) {
            //  return "OSD-" + value;
            //}
            callback: function(label, index, labels) {
              return (label/1024/1024).toFixed(2)+'MB';
            },
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
            //console.log(d.datasets[t.datasetIndex].data.data[t.index]);
            //var pg_num = d.datasets[t.datasetIndex].data[t.index].x
            var pg_pool_id = d.datasets[t.datasetIndex].data[t.index].pool_id
            var pg_primary_osd = d.datasets[t.datasetIndex].data[t.index].primary_osd
            var pg_num_objects = d.datasets[t.datasetIndex].data[t.index].num_objects
            //var pg_current_osd = d.datasets[t.datasetIndex].data[t.index].current_osd
            //var pg_is_primary = "(N/A)";
            //if (pg_current_osd == pg_primary_osd) {
            //  str_pg_primary_replica = "P";
            //} else {
            //  str_pg_primary_replica = "R";
            //}
            return 'PGID: ' + pg_pool_id + '.' + num10_to_num16(t.xLabel) + '(' + t.xLabel + ')' + ', Size: ' + toMB(t.yLabel) + "MB(" + t.yLabel + "B)" + ', Primary-OSD: ' + pg_primary_osd + ', Objects: ' + pg_num_objects;
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
    url: 'jq-pg-size.php',
    data: {
      "req_pool_id": "<?php echo $req_pool_id; ?>",
    },
    success: function(data) {
      Chart.options.title.text = '[' + getNow() + ']  ' + '<?php echo $chart_title; ?> - PGs Size (Primary PG)';
      //Chart.options.scales.xAxes[0].ticks.max = 1000;
      //console.log(data);
      //alert(data);
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      //Chart.data.datasets = [];
      var parsed_data = JSON.parse(data);
      var min_y_axis;
      //console.log(parsed_data);
      parsed_data.forEach(datasets => {
        //console.log(datasets);
        var dataset = datasets.data;
        //console.log(dataset);
        dataset.forEach(data => {
          //console.log(data);
          //console.log(data.x);
          if (data.y == null) {
            min_y_axis = data.y
          } else {
            if (data.y < min_y_axis) {
              min_y_axis = data.y
            }
          }
        });
      });
      //console.log(max_pg_number);
      Chart.options.scales.yAxes[0].ticks.min = min_y_axis;
      //console.log(Chart.data.datasets);
      if (Chart.data.datasets.length == 0) {
        Chart.data.datasets = parsed_data;
      } else {
        parsed_data.forEach((dataset, i) => {
          //console.log(Chart.data.datasets[i].data);
          Chart.data.datasets[i].data = [];
          //console.log(i, dataset);
          Chart.data.datasets[i].data = dataset.data;
        }); 
      }
      //for(var k in parsed_data) {
      //  //console.log(k, parsed_data[k]);
      //  Chart.data.datasets.push(parsed_data[k]);
      //}
      
      //console.log(Chart.data.datasets);

      // re-render the chart
      Chart.update();
    }
  });
};


$(document).ready(getData);

// get new data every 3 seconds
setInterval(getData, <?php echo $refresh_interval_PG_Size; ?>);

function get_pool_id(num10) {
  return Number(String(num10).split('.')[0]);
}

function get_osd_int(num) {
  return Math.floor(num);
}

function toMB(num) {
  return (num/1024/1024).toFixed(2);
}

function num10_to_num16(num10) {
  //var result = Number(String(num10).split('.')[1]).toString(16);
  var result = num10.toString(16);
  return result;
}

function get_max_OSD_num() {
	return <?php echo max(uniq_OSD_list()); ?>
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
