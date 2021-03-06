<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Cluster IOPS (Count)</title>
</head>

<body>

<?php
include '_config.php';
?>

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>

<center>
<div style="width:90%;">
  <canvas id="canvas"></canvas>
</div>
</center>

<script>
function getRandomIntInclusive(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
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

// create initial empty chart
var ctx_live = document.getElementById("canvas");
var Chart = new Chart(ctx_live, {
  type: 'line',
  data: {
    labels: [],
    datasets:
    [
     {
      data: [],
      borderWidth: 1,
      backgroundColor:'#5DADE2',
      borderColor:'#5DADE2',
      pointRadius: 1,
      label: 'read_op_per_sec',
      fill: false,
      showLine: true,
      //lineTension: 0.3,
      borderWidth: 2,
     },
     {
      data: [],
      borderWidth: 1,
      backgroundColor:'#F5B041',
      borderColor:'#F5B041',
      pointRadius: 1,
      label: 'write_op_per_sec',
      fill: false,
      showLine: true,
      //lineTension: 0.3,
      borderWidth: 2,
     }
    ]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: "Cluster IOPS (Count)",
    },
    legend: {
      display: true
    },
    scales: {
      xAxes: [{
        ticks: {
          display: true,
          autoSkip: true,
          maxTicksLimit: 20,
        }
      }],
      yAxes: [{
        ticks: {
          beginAtZero: true,
//          callback: function(label, index, labels) {
//            return (label/1024/1024).toFixed(2)+'MiB';
//          },
        },
        scaleLabel: {
          display: true,
          labelString: 'IOPS Count'
        },
      }]
    }
  }
});

// logic to get new data
var getData = function() {
  $.ajax({
    type: 'GET',
    //url: 'jq-pool-client-io.php?pool_name=<?php echo $pool_name; ?>&pool_id=<?php echo $pool_id; ?>',
    url: 'jq-cluster-io.php',
    //data: {
    //  "pool_name": "<?php echo $pool_name; ?>",
    //  "pool_id": "<?php echo $pool_id; ?>"
    //},
    success: function(data) {
      console.log(data);
      //alert(data);
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      var count = Chart.data.labels.length; 
      if (count > <?php echo $graph_x_count; ?>) {
        Chart.data.labels.splice(0, 1);
        Chart.data.datasets.forEach((dataset) => {
          dataset.data.splice(0, 1);
        });
      }
      var parsed_data = JSON.parse(data);
      console.log(count);
      console.log(parsed_data.read_op_per_sec);
      console.log(parsed_data.write_op_per_sec);
      //Chart.data.labels.push(Date.now());
      Chart.data.labels.push(getNow());
      //Chart.data.datasets[0].data.push(getRandomIntInclusive(1, 25));
      //Chart.data.datasets[1].data.push(getRandomIntInclusive(1, 25));
      //Chart.data.datasets[0].data.push(<?php echo $read_op_per_sec; ?>);
      //Chart.data.datasets[1].data.push(<?php echo $write_op_per_sec; ?>);
      Chart.data.datasets[0].data.push(parsed_data.read_op_per_sec);
      Chart.data.datasets[1].data.push(parsed_data.write_op_per_sec);
      
      // re-render the chart
      Chart.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData, <?php echo $refresh_interval_Cluster_IO; ?>);
</script>

</body>

</html>
