<?php
$default_time_zone = "Asia/Seoul";

//$ceph_api = "http://127.0.0.1:5000/api/v0.1";
$ceph_api = "http://127.0.0.1/ceph-rest-api/";

$prePoolCountFile = "/tmp/sonar4ceph-pre-pool-count";

$PoolColorFile = "/tmp/sonar4ceph-pre-pool-colors";

$graph_x_count = 50;

$refresh_interval_Cluster_IO = 3000;
$refresh_interval_Pools_IO = 3000;
$refresh_interval_PG_Stats = 3000;
$refresh_interval_PG_Size = 10000;
$refresh_interval_PG_Count_by_Each_OSD = 600000;
$refresh_interval_PG_Count_by_Each_Pool = 600000;
$refresh_interval_OSD_Latency = 3000;
?>
