<?php
include '_config.php';
include '_functions.php';
?>

<html>
<title>Dump-Info - PGs"</title>
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
</script>
</head>

<body>

<?php
$rawJsonData = simple_curl("$ceph_api/pg/dump");
$arrData = json_decode($rawJsonData, true)['output'];
//print_r($outputJsonData);
print_r(array2table($arrData));
?>

</body>
</html>
