import "../css/dashboard.scss"
import { get } from './ajax.js'
import Highcharts from 'highcharts'
import HighchartsMore from 'highcharts/highcharts-more'
import noDataToDisplay from 'highcharts/modules/no-data-to-display'

HighchartsMore(Highcharts)
noDataToDisplay(Highcharts)
let chart

window.addEventListener('DOMContentLoaded', function() {
    const currentUrl    = window.location.href
    const url           = new URL(currentUrl)
    const pathName      = url.pathname
    const workoutPlanId = pathName.split('/').pop()

    get(`/load/${ workoutPlanId }/0/0`)
        .then(response => response.json())
        .then(data => {
            if (!chart) {
                createChart(data)
            } else {
                updateChart(data)
            }
        })

    document.getElementById('submitChartBtn').addEventListener('click', function (event) {
        const trainingDayIndexSelectElement = document.getElementById('trainingDaySelectInput')
        const selectedTrainingDayIndex      = trainingDayIndexSelectElement.selectedIndex
        const trainingDayIndexValue         = trainingDayIndexSelectElement.options[selectedTrainingDayIndex].value

        const periodSelectElement = document.getElementById('periodSelectInput')
        const selectedPeriodIndex = periodSelectElement.selectedIndex
        const PeriodValue         = periodSelectElement.options[selectedPeriodIndex].value

        get(`/load/${ workoutPlanId }/${ trainingDayIndexValue }/${ PeriodValue }`)
        .then(response => response.json())
        .then(data => {
            createChart(data)
        })
    })
})

function createChart(chartData) {
    const chartContainer = document.getElementById('resultChartContainer')

    Highcharts.setOptions({
        lang: { 
            noData: "No trainings recorded" 
        },
        noData: {
            style: {
                fontWeight: 'bold',
                fontSize:   '50px',
                color:      '333',
            }
        }
    })

    chart = Highcharts.chart(chartContainer, {
        chart: {
            type: 'spline',
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