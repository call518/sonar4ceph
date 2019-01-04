<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>PG Count (of Each Pool)</title>
<script type="text/JavaScript">
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
window.addEventListener('load', function(){
    var select = document.getElementById('req_pg_type');

    select.addEventListener('change', function(){
        window.location = 'showPGCountByEachPool.php?req_pg_type=' + this.value;
    }, false);
}, false);
</script>
</head>

<!--body onload="JavaScript:timedRefresh(1000);"-->
<body>

<?php
include '_config.php';
include '_functions.php';

$req_pg_type = $_GET['req_pg_type'];
if (!$req_pg_type) {
	$req_pg_type = "acting";
}
?>

PG Type:
<select id="req_pg_type" name="req_pg_type">
<option value="acting" <?php if ($req_pg_type == "acting") { echo "selected"; } ?>>Acting(ALL)</option>
<option value="acting_primary" <?php if ($req_pg_type == "acting_primary") { echo "selected"; } ?>>Acting Primary</option>
</select>

<canvas id="canvas"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.js'></script>

<script type="text/javascript">
var ctx_live = document.getElementById("canvas");
var Chart = new Chart(ctx_live, {
    plugins: [{
    }],
    type: 'bar',
    data: {
    //  labels: [],
    //  datasets: [],
    },
    options: {
      responsive: true,
      title: {
        display: true,
        padding: 30,
        //text: '[' + getNow() + '] - PG Count (of Each Pool)',
        text: '[' + getNow() + '] - <?php if ($req_pg_type == "acting") { echo "(ALL)"; } else { echo "(Primary)"; } ?> PG Count (of Each Pool)',
        fontSize: 20,
      },
      legend: {
        display: true,
        position: 'right',
      },
      scales: {
        yAxes: [{ 
          stacked: true,
          scaleLabel: {
            display: true,
            labelString: "PG Count"
          },
          ticks: {
            autoSkip: false,
            min: 0,
            //stepSize: 10,
          }
        }],
        xAxes: [{ 
          stacked: true,
          scaleLabel: {
            display: true,
            labelString: "Pool Name(ID)"
          },
          ticks: {
            autoSkip: false,
            min: 0,
          }
        }]
      },
    }
});

// logic to get new data
var getData = function() {
  $.ajax({
    type: 'GET',
    //url: 'jq-pool-client-io.php?pool_name=<?php echo $pool_name; ?>&pool_id=<?php echo $pool_id; ?>',
    url: 'jq-pool-pg-count.php',
    data: {
      "req_pg_type": "<?php echo $req_pg_type; ?>"
    },
    success: function(data) {
      Chart.options.title.text = '[' + getNow() + '] - <?php if ($req_pg_type == "acting") { echo "(ALL)"; } else { echo "(Primary)"; } ?> PG Count (of Each Pool)';
      //Chart.options.scales.xAxes[0].ticks.max = 1000;
      //console.log(data);
      var parsed_data = JSON.parse(data);
      //console.log(parsed_data);
      Chart.data = parsed_data;

      // re-render the chart
      Chart.update();
    }
  });
};

$(document).ready(getData);

// get new data every 3 seconds
setInterval(getData, <?php echo $refresh_interval_PG_Count_by_Each_Pool; ?>);

//function get_osd_int(num) {
//  return Math.floor(num)
//}

//function num10_to_num16(num10) {
//  //var result = Number(String(num10).split('.')[1]).toString(16);
//  var result = num10.toString(16);
//  return result;
//}

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
