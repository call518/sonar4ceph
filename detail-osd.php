<html>
<title> Detail - OSD </title>
<head>
<style>
table.type01 {
	border-collapse: collapse;
	text-align: left;
	line-height: 1.5;
	margin : 20px 20px;
	padding: 15px;
}
table.type01 tr {
	vertical-align: top;
}
table.type01 td {
	vertical-align: top;
}
</style>
<script type="text/JavaScript">
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
</script>
</head>

<body onload="JavaScript:timedRefresh(5000);">

<?php
include '_config.php';
include '_functions.php';

$osd_id = $_GET['osd_id'];
echo "OSD ID: $osd_id";
echo "<br>Working..........";

$rawDataPG_DUMP = shell_exec("./check-osd_pg_state.sh");
$arrPG_DUMP = json_decode($rawDataPG_DUMP, true);

//var_dump($arrPG_DUMP["osd_pg_state"]["osd_$osd_id"]);
$chartData = convertPGDumpArray2ChartArray($arrPG_DUMP["osd_pg_state"]["osd_$osd_id"]);
$arrLabels = $chartData[0];
$arrDatasets = $chartData[1];

$arrColors = json_decode(file_get_contents($PoolColorFile), true);
?>

<canvas id="pieChart<?php echo $osd_id; ?>"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.min.js'></script>

<script>
function random_rgba() {
    var o = Math.round, r = Math.random, s = 255;
    return 'rgba(' + o(r()*s) + ',' + o(r()*s) + ',' + o(r()*s) + ',' + r().toFixed(1) + ')';
}

var canvas = document.getElementById("pieChart<?php echo $osd_id; ?>");
var ctx = canvas.getContext('2d');

var data = {
    labels: <?php echo json_encode($arrLabels, JSON_NUMERIC_CHECK); ?>,
    datasets: [
        {
            fill: true,
            data: <?php echo json_encode($arrDatasets, JSON_NUMERIC_CHECK); ?>,
			backgroundColor: <?php echo json_encode($arrColors, JSON_NUMERIC_CHECK); ?>,
        }
    ]
};
var options = {
        rotation: -0.7 * Math.PI,
        animation: false,
        responsive: true,
};
// Chart declaration:
var myChart = new Chart(ctx, {
    //type: 'pie',
    type: 'doughnut',
    data: data,
    options: options
});
</script>

</body>
</html>
