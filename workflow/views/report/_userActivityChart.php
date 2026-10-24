<div style="border:solid 1px #C7C7C7;    margin: 0 0 40px 0; border-top-left-radius:4px; border-top-right-radius:4px;padding:5px;" >
    <div id="user-activity-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>


<script type="text/javascript">
Highcharts.chart('user-activity-container', {
    credits:{
        enabled:false
    },
    chart: {
        type: 'column'
    },
    title: {
        text: 'گزارش عملکرد فعالیت های کاربر'
    },
    subtitle: {
        text: '<?= $obsTitle.' *** '.$userFullName ?>' },
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
        data: <?= $userActivityChartData ?>,
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
