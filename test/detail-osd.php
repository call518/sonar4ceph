<?php
include '_config.php';
include '_functions.php';

$osd_id = $_GET['osd_id'];
?>

<html>
<title> Detail - "OSD-<?php echo $osd_id; ?>"</title>
<head>
<style>
table.type03 {
    border-collapse: collapse;
    text-align: left;
    line-height: 1;
    border-top: 1px solid #ccc;
    border-left: 3px solid #369;
    margin : 3px 3px;
}
table.type03 th {
    width: 147px;
    padding: 10px;
    font-weight: bold;
    vertical-align: middle;
    color: #153d73;
    border-right: 1px solid #ccc;
    border-bottom: 1px solid #ccc;

}
table.type03 td {
    //width: 349px;
    padding: 10px;
    vertical-align: top;
    border-right: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
}
</style>
<script type="text/JavaScript">
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
</script>
</head>

<body onload="JavaScript:timedRefresh(10000);">

<?php
echo("<strong><OSD: $osd_id></strong>");
$jsonData = simple_curl("$ceph_api/osd/dump");
$arrOsdData = json_decode($jsonData, true)['output']['osds'];
//print_r($arrOsdData);
$osd_arrIndex = array_search("$osd_id", array_column($arrOsdData, "osd"));
//echo $osd_arrIndex;
//print_r($arrOsdData[$osd_arrIndex]);
//print_r(json2table($arrOsdData[$osd_arrIndex]));


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
