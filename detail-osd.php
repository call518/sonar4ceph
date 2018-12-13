<html>
<title> System Stats </title>
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

<?php
$osd_id = $_GET['osd_id'];


?>
