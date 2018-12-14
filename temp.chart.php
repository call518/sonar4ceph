?>
<canvas id="barChart<?php echo $osd_id; ?>"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.min.js'></script>
<script>
var canvas = document.getElementById("barChart<?php echo $osd_id; ?>");
var ctx = canvas.getContext('2d');

// Global Options:
// Chart.defaults.global.defaultFontColor = 'black';
// Chart.defaults.global.defaultFontSize = 16;

var data = {
    labels: <?php echo json_encode($arrLabels, JSON_NUMERIC_CHECK); ?>,
    datasets: [
        {
            fill: true,
            data: <?php echo json_encode($arrDatasets, JSON_NUMERIC_CHECK); ?>,
        }
    ]
};

// Notice the rotation from the documentation.

var options = {
        animation: false,
        rotation: -0.7 * Math.PI
};


// Chart declaration:
var myBarChart = new Chart(ctx, {
    type: 'pie',
    data: data,
    options: options
});
// Fun Fact: I've lost exactly 3 of my favorite T-shirts and 2 hoodies this way :|
</script>
<?php
