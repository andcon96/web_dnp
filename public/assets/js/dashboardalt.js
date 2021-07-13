( function ( $ ) {

// Top 10 Sales

var ctx = document.getElementById("topten");
var myMachineDown = new Chart(ctx, {
    type: 'bar',
    data: {	  
    labels: toplabel,       
    datasets: [{
    label: "Total",
    backgroundColor: ["#FFD729","#146EB4","#E94C39","#9F0047","#439D45","#2DBFF8","#08AAE3","#E4C083","#CEA968","#B25F4A"],
    hoverBorderColor: "#000000",
    data: topdata,
        
    }]
},
options: {
maintainAspectRatio: false,
layout: {
    backgroundColor: "#000000",
    padding: {
    left: 0,
    right: 0,
    top: 0,
    bottom: 0
    }
},
scales: {
    xAxes: [{
    time: {
        unit: 'month'
    },
    gridLines: {
        display: false,
        drawBorder: false
    },
    ticks: {
        callback: function(t) {
                var maxLabelLength = 5;
                if (t.length > maxLabelLength) return t.substr(0, maxLabelLength) + '..';
                else return t;
            },
        maxTicksLimit: 6,
        fontSize : 11,
        fontColor :"black",
        fontstyle:'bold',
    },
    maxBarThickness: 55,
    }],
    yAxes: [{
    ticks: {
        min: 0,              
        maxTicksLimit: 7,
        padding: 10,
        fontSize : 14,
        fontColor :"black",
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
    }
    
    }],
},
legend: {
    display: false
},
tooltips:{
    callbacks: {
    label: function(tooltipItem, data) {
        return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    },
    title: function(t, d) {
        return d.labels[t[0].index];
    }
    }
},
plugins:{
    labels: {
    render:'value',
    }     
},

}
});

// Top 10 Item

var ctx2 = document.getElementById("toptenheal");
var myMachineDown2 = new Chart(ctx2, {
    type: 'bar',
    data: {	  
        labels: toplabel2,       
        datasets: [{
        label: "Total",
        backgroundColor: ["#FFD729","#146EB4","#E94C39","#9F0047","#439D45","#2DBFF8","#08AAE3","#E4C083","#CEA968","#B25F4A"],
        hoverBorderColor: "#000000",
        data: topdata2,
    }]
    },
    options: {
        maintainAspectRatio: false,
        
        layout: {
                backgroundColor: "#000000",
        padding: {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0
        }
        },
        scales: {
        xAxes: [{
            time: {
            unit: 'month'
            },
            gridLines: {
            display: false,
            drawBorder: false
            },
            ticks: {
            callback: function(t) {
                        var maxLabelLength = 5;
                        if (t.length > maxLabelLength) return t.substr(0, maxLabelLength) + '..';
                        else return t;
                    },
            maxTicksLimit: 6,
            fontSize : 11,
            fontColor :"black",
            },
            maxBarThickness: 55,
        }],
        yAxes: [{
            ticks: {
            min: 0,              
            maxTicksLimit: 7,
            padding: 10,
            fontSize : 14,
            fontColor :"black",
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
            
        }],
        },
        legend: {
        display: false
        },
        tooltips:{
        callbacks: {
            label: function(tooltipItem, data) {
                return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            },
            title: function(t, d) {
                return d.labels[t[0].index];
            }
        }
        },
        plugins:{
        labels: {
            render:'value',
        }     
        },
        
}    
});


// Top 10 Region
var ctx4 = document.getElementById("engchart");
var myMachineDown4 = new Chart(ctx4, {
    type: 'bar',
    data: {	  
    labels: topkey,       
        datasets: [{
        label: "Total",
        backgroundColor: ["#FFD729","#146EB4","#E94C39","#9F0047","#439D45","#2DBFF8","#08AAE3","#E4C083","#CEA968","#B25F4A"],
        hoverBorderColor: "#000000",
        data: topval,
    }]
},
    options: {
        
    maintainAspectRatio: false,
    layout: {
        backgroundColor: "#000000",
        padding: {
        left: 0,
        right: 0,
        top: 0,
        bottom: 0
        }
    },
    scales: {
        xAxes: [{
        time: {
            unit: 'month'
        },
        gridLines: {
            display: false,
            drawBorder: false
        },
        ticks: {
            callback: function(t) {
                    var maxLabelLength = 5;
                    if (t.length > maxLabelLength) return t.substr(0, maxLabelLength) + '..';
                    else return t;
                },
            maxTicksLimit: 6,
            fontSize : 11,
            fontColor :"black",
            fontstyle:'bold',
        },
        maxBarThickness: 55,
        }],
        yAxes: [{
            ticks: {
                min: 0,              
                maxTicksLimit: 7,
                padding: 10,
                fontSize : 14,
                fontColor :"black",
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
        
        }],
    },
    legend: {
        display: false
    },
    tooltips:{
        callbacks: {
        label: function(tooltipItem, data) {
            return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
        title: function(t, d) {
            return d.labels[t[0].index];
        }
        }
    },
    plugins:{
        labels: {
        render:'value',
        }     
    },
    
    }
});


// Top 10 Yearly
var d = new Date();
var curYear = d.getFullYear();
var lastYear = curYear - 1;

var ctx3 = document.getElementById("totchart");
var myMachineDown3 = new Chart(ctx3, {
    type: 'bar',
    data: {	  
    labels: ['Jan','Feb','Mar','Apr','May','June','July','Aug','Sep','Okt','Nov','Dec'],       
        datasets: [
        {
            label: 'Total Sales',
            backgroundColor: '#90C4FF',
            borderColor: '#2A8DFF',
            pointHoverBackgroundColor: '#fff',
            label: lastYear,
            borderWidth: 2,
            data: topYearPrev,
        },
        {
            label: 'Total Sales',
            backgroundColor: '#97CAAA',
            borderColor: '#05B947',
            pointHoverBackgroundColor: '#fff',
            borderWidth: 2,
            label: curYear,
            data: topYear,
        }
    
    ]
},
options: {
    maintainAspectRatio: false,
    title:{
      display: true,
      text: 'Top 10 Yearly',
      fontSize: 16,
    },
    legend: {
        display: true
    },
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
});


function noexpitm(event, array){
    if(array[0]){
        let element = this.getElementAtEvent(event);
        if (element.length > 0) {
            //var series= element[0]._model.datasetLabel;
            //var label = element[0]._model.label;
            //var value = this.data.datasets[element[0]._datasetIndex].data[element[0]._index];
            window.location = "/expitem";

            //console.log()
        }
    }
}
    
function belowStockClickEvent(event, array){
    if(array[0]){
        let element = this.getElementAtEvent(event);
        if (element.length > 0) {
            //var series= element[0]._model.datasetLabel;
            //var label = element[0]._model.label;
            //var value = this.data.datasets[element[0]._datasetIndex].data[element[0]._index];
            window.location = "/bstock";
            //console.log()
        }
    }
}

} )( jQuery );





