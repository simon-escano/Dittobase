?>
<!DOCTYPE HTML>
<html>
<head>
<style>
    .chart-title {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    .axis-title, .axis-label {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
</style>
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
    title: {
        text: "Win Trajectory",
        fontFamily: "Arial",
        fontColor: "#008080",
        fontSize: 24,
        fontWeight: "bold",
        fontStyle: "italic"
    },
    axisY: {
        title: "Wins",
        labelFontFamily: "Arial",
        fontColor: "#008080",
        fontSize: 18,
        fontWeight: "bold",
        fontStyle: "italic"
    },
    axisX: {
        labelFontFamily: "Arial",
        labelFontColor: "#008080",
        labelFontSize: 14,
        fontWeight: "bold",
        fontStyle: "italic"
    },
    data: [{
        type: "line",
        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
    }]
});
chart.render();
 
}
</script>
</head>
<body>
<div id="chartContainer" class="chart-title" style="height: 370px; width: 100%;"></div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</body>
</html>