<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>OSD IOPS - Read (Operations)</title>
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
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: "OSD IOPS - Read (Operations)",
    },
    legend: {
      display: true,
      position: 'right',
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
          //callback: function(label, index, labels) {
          //  return (label/1024/1024).toFixed(2)+'MiB';
          //},
        },
        scaleLabel: {
          display: true,
          labelString: 'Operations'
        },
      }]
    },
  }
});

// logic to get new data
var getData = function() {
  $.ajax({
    type: 'GET',
    //url: 'jq-pool-client-io.php?pool_name=<?php echo $pool_name; ?>&pool_id=<?php echo $pool_id; ?>',
    url: 'jq-osd-io.php',
    //data: {
    //  "pool_name": "<?php echo $pool_name; ?>",
    //  "pool_id": "<?php echo $pool_id; ?>"
    //},
    success: function(data) {
      //console.log(data);
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
      //console.log(count);
      //console.log(parsed_data);
	  if (Chart.data.datasets.length == 0) {
        parsed_data.forEach((osd_dataset, index) => {
          color = randomColor();
          osd_id = osd_dataset.id;
          Chart.data.datasets[index] = [];
          Chart.data.datasets[index].data = [];
          Chart.data.datasets[index].label = "OSD-" + osd_id;
          Chart.data.datasets[index].borderWidth = 1;
          Chart.data.datasets[index].backgroundColor = color;
          Chart.data.datasets[index].borderColor = color;
          Chart.data.datasets[index].pointRadius = 2;
          Chart.data.datasets[index].fill = false;
          Chart.data.datasets[index].showLine = true;
          //Chart.data.datasets[index].lineTension = 0.3;
	    });
      }
      Chart.data.labels.push(getNow());
      parsed_data.forEach((osd_dataset, index) => {
        osd_id = osd_dataset.id;
        rd_ops = osd_dataset.rd_ops;
      	Chart.data.datasets[index].data.label = "osd-" + osd_id;
      	Chart.data.datasets[index].data.push(rd_ops);
      });
      
      // re-render the chart
      Chart.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData, <?php echo $refresh_interval_OSD_Perf; ?>);

function randomColor() {
    var color = Math.floor(0x1000000 * Math.random()).toString(16);
    return '#' + ('000000' + color).slice(-6);
}
</script>

</body>

</html>
