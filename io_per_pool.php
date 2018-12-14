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
<div style="width:50%;">
  <canvas id="canvas<?php echo $pool_id; ?>"></canvas>
</div>

<script>
// create initial empty chart
var now_time<?php echo $pool_id; ?> = Date.now();
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
      Chart<?php echo $pool_id; ?>.data.labels.push(now_time<?php echo $pool_id; ?>);
      Chart<?php echo $pool_id; ?>.data.datasets[0].data.push(<?php echo $read_bytes_sec; ?>);
      Chart<?php echo $pool_id; ?>.data.datasets[1].data.push(<?php echo $write_bytes_sec; ?>);
      
      // re-render the chart
      Chart<?php echo $pool_id; ?>.update();
    }
  });
};

// get new data every 3 seconds
setInterval(getData<?php echo $pool_id; ?>, 10000);
</script>

<?php
}
?>

</body>

</html>
