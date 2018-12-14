<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Client I/O per Pools</title>
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
	} else {
		$read_bytes_sec = 0;
		$write_bytes_sec = 0;
		$read_op_per_sec = 0;
		$write_op_per_sec = 0;
	}
?>  
<div style="width:90%;">
  <canvas id="canvas<?php echo $pool_id; ?>"></canvas>
</div>

<script>
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
//var now<?php echo $pool_id; ?> = Date.now();
var now<?php echo $pool_id; ?> = getNow();

var ctx_live<?php echo $pool_id; ?> = document.getElementById("canvas<?php echo $pool_id; ?>");
var Chart<?php echo $pool_id; ?> = new Chart(ctx_live<?php echo $pool_id; ?>, {
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
      text: "Client I/O per Pool - <?php echo "$pool_name($pool_id)"; ?>",
    },
    legend: {
      display: true
    },
    scales: {
      xAxes: [{
        ticks: {
          display: false
        }
      }],
      yAxes: [{
        ticks: {
          beginAtZero: true,
        }
      }]
    }
  }
});

// logic to get new data
var getData<?php echo $pool_id; ?> = function() {
  $.ajax({
    success: function(data) {
      // process your data to pull out what you plan to use to update the chart
      // e.g. new label and a new data point
      
      // add new label and data point to chart's underlying data structures
      Chart<?php echo $pool_id; ?>.data.labels.push(now<?php echo $pool_id; ?>);
      Chart<?php echo $pool_id; ?>.data.datasets[0].data.push(<?php echo $read_bytes_sec; ?>);
      Chart<?php echo $pool_id; ?>.data.datasets[1].data.push(<?php echo $write_bytes_sec; ?>);
      
      // re-render the chart
      Chart<?php echo $pool_id; ?>.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData<?php echo $pool_id; ?>, 2000);
</script>

<?php
}
?>

</body>

</html>
