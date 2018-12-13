<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>JungIn Jung</title>
</head>

<body>

<?php
$rawData = shell_exec("./check-osd_pg_state.sh | jq -r .osd_pg_state.osd_$osd_id");
$input_json = json_decode($rawData);
var_dump($input_json);
?>

</body>
</html>
