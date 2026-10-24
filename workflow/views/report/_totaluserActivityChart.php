
<div style="border:solid 1px #C7C7C7;    margin: 0 0 40px 0; border-top-left-radius:4px; border-top-right-radius:4px;padding:5px;" >
    <div id="total-graph-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>


<script type="text/javascript">
Highcharts.chart('total-graph-container', {
    credits:{
        enabled:false
    },
    chart: {
        type: 'column'
    },
    title: {
        text: 'گزارش مجموع عملکرد فعالیت های کاربران'
    },
    subtitle: {
        text: 'عنوان' },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Process Count'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'Count of activities : <b>{point.y} Process</b>'
    },
    series: [{
        name: 'Population',
        data: <?= $graphData ?>,
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});
	
</script>
