$(document).ready(() => {
    var options = {
        series: [],
        legend: {
            show: false,
        },
        chart: {
            height: 350,
            type: 'treemap',
            toolbar: {
                show: false
            }
        },
        tooltip: {
            style: {
                fontSize: '16px',
                fontFamily: undefined
            },
            callbacks: {
                labelColor: function (tooltipItem, chart) {
                    return {
                        borderColor: 'rgb(255, 0, 0)',
                        backgroundColor: 'rgb(255, 0, 0)'
                    };
                }
            }

        },
        colors: [
            '#000000',
            '#00ADBD',
            '#3ce9f9',
            '#46f3ff'
        ],
        plotOptions: {
            treemap: {
                distributed: false,
                enableShades: true
            }
        }
    };

    itemArray2.forEach(element => {
        options.series.push(
            {
                data: [
                    {
                        x: element.x,
                        y: element.y
                    }
                ]
            }
        )
    });
    var chart = new ApexCharts(document.querySelector("#treeMapLocation"), options);
    chart.render();
});