<template>
  <apexchart ref="chart" type="line" height="200px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
  name:"servers",
  props:["chartData", 'type'],
  data(){
      return {
          series: [
              {name: "Servers",data: []},
          ],
          chartOptions: {
              chart: {
                  height: 200,
                  zoom: {
                      type: 'x',
                      enabled: false,
                      autoScaleYaxis: true
                  },
                  toolbar: {
                      show: false,
                  },
              },
              tooltip: {
                  x: {
                      format: ''
                  }              
              },
              dataLabels: {
                  enabled: false,
              },
              colors: ["#556ee6"],
              stroke: {
                  curve: 'smooth',
                  width: '2'
              },
              markers: {
                  size: 4,
                  strokeWidth: 3,

                  hover: {
                    size: 5,
                    sizeOffset: 2,
                  },
              },
              yaxis:{
                  min: 0,
                  labels: {
                      formatter: function (val) {
                          return parseFloat(val).toFixed(0)
                      }
                  }
              },
              xaxis: {
                  type: 'datetime',
                  labels: {
                      format: '',
                      datetimeUTC: false
                  }
              }
          }
      }
  },
  watch: {
      chartData(newVal){
          this.setData()
      }
  },
  created(){
      this.chartOptions.xaxis.labels.format = 'dd/MM/yy'
      this.chartOptions.tooltip.x.format = 'dd/MM/yy'
      this.setData()
  },
  methods: {
      setData(){
          var data = this.chartData.map((data) => {
              return [
                  data.date,
                  data.count
              ]
          })
          this.series[0].data = data
      }
  }
}
</script>