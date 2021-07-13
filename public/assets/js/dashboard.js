( function ( $ ) {
    "use strict";

const brandSuccess = '#05B947'
const brandInfo = '#2A8DFF'
const brandDanger = '#F32929'

function convertHex (hex, opacity) {
  hex = hex.replace('#', '')
  const r = parseInt(hex.substring(0, 2), 16)
  const g = parseInt(hex.substring(2, 4), 16)
  const b = parseInt(hex.substring(4, 6), 16)

  const result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity / 100 + ')'
  return result
}

function random (min, max) {
  return Math.floor(Math.random() * (max - min + 1) + min)
}

    var elements = 3
    var data1 = [40,30,100]
    var data2 = []
    var data3 = []

    var d = new Date();
    var curYear = d.getFullYear();
    var lastYear = curYear - 1;

    //var labeldata = ['Jan','Feb','Mar','Apr']

    $('input[type=radio][name=options]').change(function() {
        // ketika option berubah ubah chart
        let searchby = this.value;

        for (var i = 0; i <= elements; i++) {
          data1.push(random(50, 200))
          data2.push(random(80, 100))
          data3.push(65)
        }

        if(searchby == '1'){ // Sales Customer
          let total = myChart.data.datasets.length;
          myChart.data.labels = topSalesName;
          myChart.data.datasets[0].data = topSales;
          myChart.options.title.text = 'Top 10 Customers';
          myChart.options.legend.display = false;

          while (total > 1) {
              myChart.data.datasets.pop();
              total--;
          }

        }else if(searchby == '2'){ // Sales Item
          let total = myChart.data.datasets.length;
          myChart.data.labels = topItemName;
          myChart.data.datasets[0].data = topItem;
          myChart.options.title.text = 'Top 10 Items';
          myChart.options.legend.display = false;

          while (total > 1) {
              myChart.data.datasets.pop();
              total--;
          }

        }else if(searchby == '3'){ // Sales Region
          let total = myChart.data.datasets.length;

          myChart.data.labels = topRegionName;
          myChart.data.datasets[0].data = topRegion;
          myChart.options.title.text = 'Top 10 Region';
          myChart.options.legend.display = false;
          
          while (total > 1) {
              myChart.data.datasets.pop();
              total--;
          }

        }else if(searchby == '4'){ // Sales Total
          myChart.data.labels = ['Jan','Feb','Mar','Apr','May','June','July','Aug','Sep','Okt','Nov','Dec'];
          myChart.data.datasets[0].data = topYearPrev;
          myChart.options.title.text = 'Total Sales';
          myChart.data.datasets[0].label = lastYear;
          myChart.options.legend.display = true;

          var newDataSet = {
            backgroundColor: '#97CAAA',
            borderColor: brandSuccess,
            pointHoverBackgroundColor: '#fff',
            label: curYear,
            borderWidth: 2,
            data: topYear,
          }

          myChart.data.datasets.push(newDataSet);
        }

        myChart.update();
    });

    for (var i = 0; i <= elements; i++) {
      data1.push(random(50, 200))
      data2.push(random(80, 100))
      data3.push(65)
    }


    var ctx = document.getElementById( "trafficChart" );
    var myChart = new Chart( ctx, {
        type: 'bar',
        data: {
            labels: topSalesName,
            datasets: [
            {
              label: 'Total Sales',
              backgroundColor: '#90C4FF',
              borderColor: brandInfo,
              pointHoverBackgroundColor: '#fff',
              borderWidth: 2,
              data: topSales
          }
          ]
        },
        options: {
            maintainAspectRatio: false,
            title:{
              display: true,
              text: 'Top 10 Customers',
              fontSize: 16,
            },
            legend: {
                display: false
            },
            responsive: true,
            scales: {
                xAxes: [{
                  gridLines: {
                    drawOnChartArea: false,
                  },
                  ticks: {
                    callback: function(t) {
                        var maxLabelLength = 5;
                        if (t.length > maxLabelLength) return t.substr(0, maxLabelLength) + '..';
                        else return t;
                    }
                  }
                }],
                yAxes: [ {
                      ticks: {
                        beginAtZero: true,
                        maxTicksLimit: 5,
                        // callback: function(label, index, labels) {
                        //   return label.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        // }
                        callback: function(value) {
					         var ranges = [
					            { divider: 1e12, suffix: ' T' },
					            { divider: 1e9, suffix: ' M' },
					            { divider: 1e6, suffix: ' Jt' },
					            { divider: 1e3, suffix: ' K' }
					         ];
					         function formatNumber(n) {
					            for (var i = 0; i < ranges.length; i++) {
					               if (n >= ranges[i].divider) {
					                  return (n / ranges[i].divider).toString() + ranges[i].suffix;
					               }
					            }
					            return n;
					         }
					         return 'Rp.' + formatNumber(value);
					      }
                      },
                      gridLines: {
                        display: true
                      }
                } ]
            },
            elements: {
                point: {
                  radius: 0,
                  hitRadius: 10,
                  hoverRadius: 4,
                  hoverBorderWidth: 3
              }
            },
            tooltips: {
              callbacks: {
                  label: function(tooltipItem, data) {
                      return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                  },
                  title: function(t, d) {
                     return d.labels[t[0].index];
                  }
              }
            }


        }
    } );

} )( jQuery );