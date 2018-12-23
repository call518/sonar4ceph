<html>
<title>Detail - "OSD-<?php echo $osd_id; ?>"</title>
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

<?php
include '_config.php';
include '_functions.php';

$jsonData = simple_curl("$ceph_api/health?detail=detail");
$arrHealth = json_decode($jsonData, true);
$arrHealthOutput = $arrHealth['output'];

print_r(array2table($arrHealthOutput));
?>

</body>
</html>
