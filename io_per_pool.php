<!DOCTYPE html>
<html lang="en" >
<h
ead>
  <meta charset="UTF-8">
  <title>Chart.js - Dynamically Update Chart Via Ajax Requests</title>
</head>

<body>

<?php
$jsonData = shell_exec('ceph osd pool stats --format=json');
$arrData = json_decode($jsonData);
//print_r($arrData);
?>

<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js'></script>

<?php
foreach ($arrData as $object)
{
	$arrItem = json_decode(json_encode($object), True);
	//print_r($arrItem);
	$pool_name = $arrItem['pool_name'];
	$pool_id = $arrItem['pool_id'];
	//$arr_recovery = $arrItem['recovery'];
	//$arr_recovery_rate = $arrItem['recovery_rate'];
	$arr_client_io_rate = $arrItem['client_io_rate'];
	if (count($arr_client_io_rate) != 0) {
		$read_bytes_sec = $arr_client_io_rate['read_bytes_sec'];
		$write_bytes_sec = $arr_client_io_rate['write_bytes_sec'];
		$read_op_per_sec = $arr_client_io_rate['read_op_per_sec'];
		$write_op_per_sec = $arr_client_io_rate['write_op_per_sec'];
		echo "$read_bytes_sec<br>";
		echo "$write_bytes_sec<br>";
		echo "$read_op_per_sec<br>";
		echo "$write_op_per_sec<br>";
	} else {
		$read_bytes_sec = 0;
		$write_bytes_sec = 0;
		$read_op_per_sec = 0;
		$write_op_per_sec = 0;
	}
?>  
<div style="width:50%;">
  <canvas id="canvas-<?php echo $pool_id; ?>"></canvas>
</div>

<script>
// create initial empty chart
var now_time = Date.now();
var ctx_live = document.getElementById("canvas-<?php echo $pool_id; ?>");
var myChart = new Chart(ctx_live, {
  type: 'bar',
  data: {
    labels: [],
    datasets: 
    [
      {
      data: [],
      borderWidth: 1,
      borderColor:'#58D68D',
      label: now_time,
      },
      {
      data: [],
      borderWidth: 1,
      borderColor:'#EB984E',
      label: now_time,
      }
    ]
  },
  options: {
    responsive: true,
    title: {
      display: true,
      text: "Client I/O Bytes",
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
var getData = function() {
  $.ajax({
    success: function(data) {
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      myChart.data.labels.push(now_time);
      myChart.data.datasets[0].data.push(100);
      myChart.data.datasets[1].data.push(50);
      
      // re-render the chart
      myChart.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData, 1000);

<?php } ?>

</script>

</body>

</html>
