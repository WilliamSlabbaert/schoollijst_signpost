$(document).ready(() => {
    var options = {
        series: [{
            data: []
        }],
        legend: {
            show: false,
        },
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                distributed: true
            }
        },
        dataLabels: {
            enabled: false
        },
        colors: [
            '#00ADBD',
            '#3ce9f9',
            '#46f3ff'
        ],
        xaxis:{
            categories:[]
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: '16px',
                fontFamily: undefined
            }
        }
    };
    options.series.push({ data: itemArray2.map(a => Math.round(a.total * 100) / 100) });
   
    options.xaxis.categories =itemArray2.map(a => a.x);

    var chart = new ApexCharts(document.querySelector("#chartLocation"), options);
    chart.render();
});