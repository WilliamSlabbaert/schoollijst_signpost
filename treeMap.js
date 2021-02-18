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
        plotOptions: {
            bar: {
                distributed: true,
                horizontal: true
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