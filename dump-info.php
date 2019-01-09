<?php
include '_config.php';
include '_functions.php';

$req_info_type = $_GET['req_info_type'];
//echo $req_info_type;
?>

<html>
<title>Dump-Info - Pool/OSD/PG</title>
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
    vertical-align: top;
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
//function timedRefresh(timeoutPeriod) {
//	setTimeout("location.reload(true);",timeoutPeriod);
//}
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
window.addEventListener('load', function(){
    var select = document.getElementById('req_info_type');

    select.addEventListener('change', function(){
        window.location = 'dump-info.php?req_info_type=' + this.value;
    }, false);
}, false);
</script>
</head>

<body>

Info Type: 
<select id="req_info_type" name="req_info_type">
<option value="POOL" <?php if ($req_info_type == "POOL") { echo "selected"; } ?>>POOL</option>
<option value="OSD" <?php if ($req_info_type == "OSD") { echo "selected"; } ?>>OSD</option>
<option value="PG" <?php if ($req_info_type == "PG") { echo "selected"; } ?>>PG</option>
</select>

<?php
if ($req_info_type == "POOL") {
	$query_str = "pg/dump_pools_json";
} else if ($req_info_type == "OSD") {
	$query_str = "osd/dump";
} else if ($req_info_type == "PG") {
	$query_str = "pg/dump";
} else {
	$query_str = null;
}
if (strlen($query_str) > 0) {
	$rawJsonData = simple_curl("$ceph_api/$query_str");
	$arrData = json_decode($rawJsonData, true)['output'];
	//print_r($outputJsonData);
	print_r(array2table($arrData));
}
?>

</body>
</html>
