<?php
    $data = get30DagenOrderGrafiekJsonData($msconn, $conn);

    $labels = $data['labels'];
    $data = $data['data'];
?>

<div id="orders30Dagen" class="col-lg-12 grafiek-transparant" style="height:525px"></div>

<script type='text/javascript'>
    FusionCharts.ready(function(){
        new FusionCharts({
            type: "stackedarea2d",
			renderAt: "orders30Dagen",
            width: "100%",
            height: "100%",
            dataFormat: "json",
            dataSource: {
                chart: {
                    showalternateHgridcolor: "1",
					crossLineAnimation: "1",
					plotFillAlpha: "65",
					showPlotBorder: "1",
					showTickMarks: "1",
					drawcrossline: "1",
					legendItemFontSize: "30",
                    legendPosition: "top-right",
                    valueFontColor: "#5a5a5a",
                    rotateLabels: "0",
					labelStep: "2",
                    captionFontSize: "36",
                    baseFontSize : "28",
                    captionFontColor: "#00adba",
                    caption: "Orders past 30 days",
                    formatnumberscale: "0",
                    showvalues: "0",
                    drawcrossline: "1",
                    labelFontBold: "1",
                    yAxisValueFontBold: "0",
					canvasPadding: "0",
                    valueFontBold: "1",
                    showsum: "1",
                    plottooltext: "$dataValue ($seriesName)",
					theme: "fusion",
                },
                categories: [
                    {
                        category: <?php echo $labels;?>
                    }
                ],
                dataset: <?php echo $data;?>
            }
        }).render();
    });
</script>