import "../css/dashboard.scss"
import { get } from './ajax.js'
import Highcharts from 'highcharts'

window.addEventListener('DOMContentLoaded', function() {
    let chart
    const currentUrl    = window.location.href
    const url           = new URL(currentUrl)
    const pathName      = url.pathname
    const workoutPlanId = pathName.split('/').pop()

    get(`/load/${ workoutPlanId }`)
        .then(response => response.json())
        .then(data => {
            if (!chart) {
                createChart(data)
            } else {
                updateChart(data)
            }
        })

})

function createChart(chartData) {
    const chartContainer = document.getElementById('resultChartContainer')

    chart = Highcharts.chart(chartContainer, {
        chart: {
            type: 'line',
            height: 600,
            backgroundColor: '#D8D9DA'
        },
        title: {
            text: 'Workout Results'
        },
        xAxis: {
            categories: chartData.data.dates
        },
        yAxis: {
            title: {
                text: 'Wight (kg)'
            }
        },
        series: chartData.data.series
    })
}

function updateChart(chartData) {
    chart.update({
        xAxis: {
            categories: chartData.data.dates
        },
        series: chartData.data.series
    })
}