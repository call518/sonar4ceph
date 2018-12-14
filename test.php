<html>
<body>
<canvas id="barChart<?php echo $osd_id; ?>"></canvas>
<script src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.min.js'></script>

<script>
var canvas = document.getElementById("barChart<?php echo $osd_id; ?>");
var ctx = canvas.getContext('2d');
// Global Options:
// Chart.defaults.global.defaultFontColor = 'black';
// Chart.defaults.global.defaultFontSize = 16;
data = {
    datasets: [{
        data: [10, 20, 30],
backgroundColor: [
                "#FF6384",
                "#63FF84",
                "#84FF63",
                "#8463FF",
                "#6384FF"
            ]
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
        'Red',
        'Yellow',
        'Blue'
    ]
};
// Notice the rotation from the documentation.
var options = {
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
</body>
</html>
