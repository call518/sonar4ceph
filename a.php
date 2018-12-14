<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>Chart.js - Dynamically Update Chart Via Ajax Requests</title>
</head>

<body>

<?php
$read_bytes_sec = 50;
$write_bytes_sec = 30;
$read_op_per_sec = 0;
$write_op_per_sec = 0;
?>  

<div style="width:50%;">
  <canvas id="canvas1"></canvas>
</div>

<div style="width:50%;">
  <canvas id="canvas2"></canvas>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>

<script>

// create initial empty chart
var now_time1 = Date.now();
var ctx_live1 = document.getElementById("canvas1");
var Chart1 = new Chart(ctx_live1, {
  type: 'bar',
  data: {
    labels: [],
    datasets:
    [
     {
      data: [],
      borderWidth: 1,
      backgroundColor:'#5DADE2',
      label: 'read_bytes_sec',
     },
     {
      data: [],
      borderWidth: 1,
      backgroundColor:'#F5B041',
      label: 'write_bytes_sec',
     }
    ]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: "test I/O client chart",
    },
    legend: {
      display: true
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
        }
      }]
    }
  }
});

// this post id drives the example data
var postId = 1;

// logic to get new data
var getData1 = function() {
  $.ajax({
    success: function(data) {
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      Chart1.data.labels.push(now_time1);
      Chart1.data.datasets[0].data.push(10);
      Chart1.data.datasets[1].data.push(20);
      
      // re-render the chart
      Chart1.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData1, 1000);
</script>

<script>

// create initial empty chart
var now_time2 = Date.now();
var ctx_live2 = document.getElementById("canvas2");
var Chart2 = new Chart(ctx_live2, {
  type: 'bar',
  data: {
    labels: [],
    datasets:
    [
     {
      data: [],
      borderWidth: 1,
      backgroundColor:'#5DADE2',
      label: 'read_bytes_sec',
     },
     {
      data: [],
      borderWidth: 1,
      backgroundColor:'#F5B041',
      label: 'write_bytes_sec',
     }
    ]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: "test I/O client chart",
    },
    legend: {
      display: true
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
        }
      }]
    }
  }
});

// this post id drives the example data
var postId = 1;

// logic to get new data
var getData2 = function() {
  $.ajax({
    success: function(data) {
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      Chart2.data.labels.push(now_time2);
      Chart2.data.datasets[0].data.push(30);
      Chart2.data.datasets[1].data.push(40);
      
      // re-render the chart
      Chart2.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData2, 1000);
</script>

</body>
</html>
