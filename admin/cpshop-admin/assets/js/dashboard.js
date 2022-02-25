$(function(){
    var base_url = $("body").data('base_url');
    var fromdate = $('#f_date').val();
    var todate = $('#f_date_2').val();
    var rbl_filter = $('#rbl_filter').val();
    var oblr_filter = $('#oblr_filter').val();
    var shopid = 'all';
    var branchid = 0;
    var initial_fromdate = $('#f_date').val();

    // lazy load chart count to load
    var report_charts_chunk_size = () => {
        if ($(window).width() >= 1400) {
            return 6;
        } else if ($(window).width() >= 1200) {
            return 6;
        } else if ($(window).width() >= 992) {
            return 4;
        } else if ($(window).width() >= 768) {
            return 4;
        } else if ($(window).width() >= 576) {
            return 2;
        } else if ($(window).width() < 576) {
            return 2;
        }
    };
    // lazy load chart initialization
    var chart_lazyload = {
        'start' : 0,
        'end'   : report_charts_chunk_size() - 1,
    };

    var ctx = document.getElementById('chartLoad').getContext('2d');
    var chartLoad = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'My First dataset',
                fill: false,
                borderColor: '#e2e8f0',
                backgroundColor: '#e2e8f0',
                data: [
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor()
                ]
            }, {
                label: 'My Second dataset ',
                fill: false,
                borderColor: '#8b8c8e',
                backgroundColor: '#8b8c8e',
                data: [
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor(),
                    randomScalingFactor()
                ]
            }]
        },
        options: {
            legend: {
                display: true,
                position: 'bottom',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
        }
    })

    window.setInterval(function () {
        chartLoad.data.datasets.forEach(function(dataset) {
            dataset.data = dataset.data.map(function() {
                return randomScalingFactor();
            });
        });

        chartLoad.update();
    }, 1500);

    function randomScalingFactor () {
		return Math.round(Math.random() * 100);
	}
    
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Paid Amount',
                data: [],
                backgroundColor: [
                    'rgba(51, 181, 229, 0.2)'
                ],
                borderColor: [
                    'rgba(51, 181, 229, 1)'
                ],
                borderWidth: 1
            },
            {
                label: 'Unpaid Amount',
                data: [],
                backgroundColor: [
                    'rgba(255, 187, 51, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 187, 51, 1)'
                ],
                borderWidth: 1
            },{
                label: 'Total Amount',
                data: [],
                backgroundColor: [
                    'rgba(0, 200, 81, 0.2)'
                ],
                borderColor: [
                    'rgba(0, 200, 81, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    var ctx = document.getElementById('totalSales').getContext('2d');
    var totalSales = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: '',
                data: [],
                backgroundColor: 'rgba(0,0,0,0)',
                borderColor: '#07DA63',
                borderWidth: 2
            },
            {
                label: '',
                data: [],
                backgroundColor: 'rgba(0,0,0,0)',
                borderColor: 'darkgray',
                borderWidth: 2
            }]
        },
        options: {
            title:{
                display: true,
                position: 'top',
                text: "Sales Over Time"
            },
            elements: {
                line: {
                    tension: 0
                },
                point: {
                    radius: 0
                }
            },
            legend: {
                display: true,
                position: 'bottom',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        stepSize: 4000,
                        backdropPaddingY: 30
                    }
                }],
                xAxes: [{
                    ticks: {
                        autoSkip: true,
                        autoSkipPadding: 30,
                        maxRotation: 0,
                        // maxTicksLimit: 20
                    }
                }]
            }
        }
    })

    var ctx = document.getElementById('revenueByStore').getContext('2d');
    var revenueByStore = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: ['name1','name2','name3','name4','name5','name6'],
            datasets: [{
                data: [],
                backgroundColor: '#07DA63',
                label: "Top Shop",
            },{
                data: [],
                backgroundColor: 'darkgray',
                label: "Top Shop",
            }]
        },
        options: {
            title:{
                display: true,
                position: 'top',
                text: "Top Stores"
            },
            legend: {
                display: true,
                position: 'bottom',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            responsive: true,
            scales: {
                xAxes: [{
                    ticks: {
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                            } else if (value >= 1000) {
                                return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                            }else{
                                return value;
                            }
                        }
                    }
                }]
            }
        }
    })

    var ctx = document.getElementById('revenueByBranch').getContext('2d');
    var revenueByBranch = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: ['name1','name2','name3','name4','name5','name6'],
            datasets: [{
                data: [],
                backgroundColor: '#07DA63',
                label: "Today",
            },{
                data: [],
                backgroundColor: 'darkgray',
                label: "Yesterday",
            }]
        },
        options: {
            title:{
                display: true,
                position: 'top',
                text: "Top Branches"
            },
            legend: {
                display: true,
                position: 'bottom',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            responsive: true,
            scales: {
                xAxes: [{
                    ticks: {
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                            } else if (value >= 1000) {
                                return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                            }else{
                                return value;
                            }
                        }
                    }
                }]
            }
        }
    })

    var ctx = document.getElementById('revenueByLocation').getContext('2d');
    var rbl_chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            plugins: {
                labels: {
                    render: function (args) {
                        return `${args.percentage}%\n${args.label}\n${args.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
                    },
                    fontColor: ['#000','#fff','#000','#fff','#000','#000','#000','#000','#000','#fff'],
                    fontSize: 9,
                    overlap: true,
                    position: 'border',
                    outsidePadding: 10,
                    textMargin: 5,
                }
            },
            title:{
                display: false,
                position: 'top',
                text: "City"
            },
            legend: {
                display: false,
                position: 'bottom',
                align: 'start',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            responsive: true,
        }
    });
    
    var ctx = document.getElementById('ordersByLocation').getContext('2d');
    var oblr_chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            plugins: {
                labels: {
                    render: function (args) {
                        return `${args.percentage}%\n${args.label}\n${args.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}`;
                    },
                    fontColor: ['#000','#fff','#000','#fff','#000','#000','#000','#000','#000','#fff'],
                    fontSize: "9",
                    position: 'border',
                }
            },
            title:{
                display: false,
                position: 'top',
                text: "City"
            },
            legend: {
                display: false,
                position: 'bottom',
                align: 'start',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            responsive: true,
        }
    });

    var abandonedChart = document.getElementById("abandonedcart-chart").getContext('2d');
    var abandoned_cart_chart = null;
    setChart('line', {
        'labels':[],
        'legend':[],
        'data':[],
    });

    var ctx = document.getElementById('transcationChart').getContext('2d');
    var transcationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Paid Count',
                data: [],
                backgroundColor: [
                    'rgba(51, 181, 229, 0.2)'
                ],
                borderColor: [
                    'rgba(51, 181, 229, 1)'
                ],
                borderWidth: 1
            },
            {
                label: 'Unpaid Count',
                data: [],
                backgroundColor: [
                    'rgba(255, 187, 51, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 187, 51, 1)'
                ],
                borderWidth: 1
            },{
                label: 'Total Count',
                data: [],
                backgroundColor: [
                    'rgba(0, 200, 81, 0.2)'
                ],
                borderColor: [
                    'rgba(0, 200, 81, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    var ctx = document.getElementById('onlineStore').getContext('2d');
    var visitorChart = new Chart(ctx, {
        type: 'line',
      data: {
          labels: [],
          datasets: [
            {
              label: 'Visitors',
              borderColor: '#07DA63',                  
              backgroundColor: 'rgba(0, 0, 0, 0)',
              borderWidth: 2,
              data: []
            },
            {
              label: 'Previous Visitors',
              borderColor: 'darkgray',                  
              backgroundColor: 'rgba(0, 0, 0, 0)',
              borderWidth: 2,
              data: []
            }
        ]
      },
      options: {
        elements: {
            line: {
                tension: 0
            },
            point: {
                radius: 0.01
            }
        },
        tooltips: {
            mode: 'point'
        },
        legend: {
            display: true,
            position: 'bottom',
            align: 'end',
            fullWidth: true,
            labels:{
                boxWidth: 10,
            }
        },
        scales: {
            yAxes: [{
                ticks: {
                    // beginAtZero: true,
                    stepSize: 5,
                    precision: 0,
                    // stacked: true,
                    backdropPaddingY: 30
                }
            }],
            xAxes: [{
                ticks: {
                    autoSkip: true,
                    autoSkipPadding: 30,
                    maxRotation: 0,
                    // maxTicksLimit: 20
                }
            }]
        }         
      }
    });

    var ctx = document.getElementById('viewChart').getContext('2d');
    var viewChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Views',
                data: [],
                backgroundColor: [
                    'rgba(0, 200, 81, 0.2)'
                ],
                borderColor: [
                    'rgba(0, 200, 81, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });    

    var ctx = document.getElementById('averageOrderValue').getContext('2d');
    var aovChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
              {
                label: 'Order Values',
                borderColor: '#07DA63',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 2,
                data: []
              },
              {
                label: 'Previous Order Values',
                borderColor: 'darkgray',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 2,
                data: []
              }
          ]
        },
        options: {
          elements: {
              line: {
                  tension: 0.01
              },
              point: {
                  radius: 0
              }
          },
          tooltips: {
              mode: 'point'
          },
          legend: {
            display: true,
            position: 'bottom',
            align: 'end',
            fullWidth: true,
            labels:{
                boxWidth: 10,
            }
          },
          scales: {
              yAxes: [{
                  ticks: {
                      // beginAtZero: true,
                      stepSize: 10000,
                      // precision: 200
                      // stacked: true,
                      backdropPaddingY: 30,
                  }
              }],
              xAxes: [{
                  ticks: {
                      autoSkip: true,
                      autoSkipPadding: 30,
                      maxRotation: 0,
                      // maxTicksLimit: 20
                  }
              }]
          }         
        }
    }); 

    var ctx = document.getElementById('orderAndSales').getContext('2d');
    var osChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
              {
                label: 'Total Orders',
                borderColor: '#07DA63',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 2,
                data: []
              },
              {
                label: 'Previous Orders',
                borderColor: 'darkgray',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 2,
                data: []
              }
          ]
        },
        options: {
          elements: {
              line: {
                  tension: 0.01
              },
              point: {
                  radius: 0
              }
          },
          tooltips: {
              mode: 'point'
          },
          legend: {
            display: true,
            position: 'bottom',
            align: 'end',
            fullWidth: true,
            labels:{
                boxWidth: 10,
            }
          },
          scales: {
              yAxes: [{
                ticks: {
                    beginAtZero: true,
                    stepSize: 20,
                    precision: 0,
                    // stacked: true,
                    backdropPaddingY: 30
                }
              }],
              xAxes: [{
                  ticks: {
                      autoSkip: true,
                      autoSkipPadding: 30,
                      maxRotation: 0,
                      // maxTicksLimit: 20
                  }
              }]
          }         
        }
    }); 

    var ctx = document.getElementById('totalOrders').getContext('2d');
    var toChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
              {
                label: 'Total Orders',
                borderColor: '#07DA63',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 2,
                data: []
              },
              {
                label: 'Previous Orders',
                borderColor: 'darkgray',                  
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderWidth: 2,
                data: []
              }
          ]
        },
        options: {
          elements: {
              line: {
                  tension: 0.01
              },
              point: {
                  radius: 0
              }
          },
          tooltips: {
              mode: 'point'
          },
          legend: {
            display: true,
            position: 'bottom',
            align: 'end',
            fullWidth: true,
            labels:{
                boxWidth: 10,
            }
          },
          scales: {
              yAxes: [{
                  ticks: {
                      beginAtZero: true,
                      stepSize: 20,
                      precision: 0,
                      // stacked: true,
                      backdropPaddingY: 30
                  }
              }],
              xAxes: [{
                  ticks: {
                      autoSkip: true,
                      autoSkipPadding: 30,
                      maxRotation: 0,
                      // maxTicksLimit: 20
                  }
              }]
          }      
        }
    }); 

    var ctx = document.getElementById('topProductsSold').getContext('2d');
    var tpsChart = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: [],
            datasets: [
              {
                label: 'Current',                            
                backgroundColor: '#07DA63',                                
                data: []
              },
              {
                label: 'Previous',                
                backgroundColor: 'darkgray',                                
                data: []
              }
          ]
        },        
        options: {
            title:{
                display: true,
                position: 'top',
                text: "Top Produst Sold"
            },
            legend: {
                display: true,
                position: 'bottom',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        //beginAtZero: true,
                        //stepSize: 4000,
                        // precision: 200
                        // stacked: true,
                        backdropPaddingY: 30
                    }
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                        autoSkip: true,
                        autoSkipPadding: 30,
                        maxRotation: 0,
                        precision: 0,
                        // maxTicksLimit: 20,
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                            } else if (value >= 1000) {
                                return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                            }else{
                                return value;
                            }
                        }
                    }
                }]
              }          
        }
    });

    var ctx = document.getElementById("poChart").getContext('2d');
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, '#07da63');
    gradient.addColorStop(0.4, '#08f36e');
    gradient.addColorStop(0.8, '#1cf87c');
    gradient.addColorStop(1, '#35f98a');
    var poChart = new Chart(ctx, {
      type: 'horizontalBar',
      data: {
          labels: [],
          datasets: [{
              'backgroundColor': gradient
          }]
      },        
      options: {
        title:{
          display: true,
          position: 'top',
          text: "Stores with Pending Orders"
        },     
        legend: {
            position: 'bottom',
            fullWidth: true,
            labels: {
              boxWidth: 10
            }
        },
        scales: {
          yAxes: [{
              ticks: {
                  autoSkip: true,
                  backdropPaddingY: 30
              }
          }],
          xAxes: [{
              ticks: {
                beginAtZero: true,
                maxRotation: 0,
                precision: 0
              }
          }]
        }               
      }
    });

    var ctx = document.getElementById("invChart").getContext("2d");
    var invChart = new Chart(ctx, {
      type: 'pie',
      data: {
          labels: [],
          datasets: []
      },        
      options: {
        plugins: {
            labels: {
                fontColor: ['#000','#fff','#000','#fff','#000','#000','#000','#000','#000','#fff'],
                fontSize: 9,
                overlap: true,
                position: 'border',
                outsidePadding: 10,
                textMargin: 5,
            },
        },
        title:{
          display: true,
          position: 'top',
          text: "Most Stocked Items"
        },     
        legend: {
            display: true,
            align: 'start',
            position: 'bottom',
            fullWidth: true,
            labels: {
              boxWidth: 10
            }
        },     
      }
    });

    $(document).ready( function () {	
        fromdate = $('#f_date').val();
        todate = $('#f_date_2').val();
        shopid = $('#shopid').val();
        rbl_filter = $('#rbl_filter').val();
        branchid = $('#branchid').val();
        $('#reprange').val('today');
        var reprange = $('#reprange').val();

        if (reprange == 'custom') {
            if(!$(".search-filter").is(":visible")) {
                $(".search-filter ").toggleClass('hidden');
            }
            $("#f_date").prop('disabled', false);
            $("#f_date_2").prop('disabled', false);
            // document.getElementById("f_date").disabled=false;
            // document.getElementById("f_date_2").disabled=false;
        }

        document.getElementById("f_date").disabled=true;
        document.getElementById("f_date_2").disabled=true;

        var dashb_charts = $('#report-charts > div[fromrecord]');
        $(dashb_charts).each( (k,v) => {
            // console.log(k,v);
            if (!Object.values(report_charts).find( d => d == v.id)) {
                v.remove();
            }
        });
        $('#pageActive').LoadingOverlay("show", {'zIndex': 998});
        $('#report-charts > div[fromrecord]').hide();
        get_NullDatesRecord();
    });

    function get_NullDatesRecord() {
        $.ajax({
            type:'get',
            url: base_url+'Main/get_NullDatesRecord',
            success:function(data){
                nullDateRecords = JSON.parse(data);
                // console.log(json_data.chartdata.totalsales);
                aj_req(fromdate, todate, shopid, branchid, rbl_filter);
            }
        });
    }

    $('#rbl_filter').change( () => {
        rbl_filter = $('#rbl_filter').val();
        fromdate = $('#f_date').val();
        todate = $('#f_date_2').val();
        shopid = $('#shopid').val();
        var branchid = $('#branchid').val();

        $('#pageActive').LoadingOverlay("show");
        $.ajax({
            type:'get',
            url: base_url+'Main/get_rblChartData',
            data: {'fromdate':fromdate,'todate':todate,'shopid':shopid,'branchid':branchid,'rbl_filter':rbl_filter},
            success:function(data){
                $('#pageActive').LoadingOverlay("hide");
                json_data = JSON.parse(data);
                
                // console.log(json_data.chartdata.totalsales);
                setRblChart(json_data.chartdata);

                $(".btnSearch").prop('disabled', false); 
                $(".btnSearch").text("Search");
                $('.overlay').css("display","none");												
            }
        });
    })

    $('#oblr_filter').change( () => {
        oblr_filter = $('#oblr_filter').val();
        fromdate = $('#f_date').val();
        todate = $('#f_date_2').val();
        shopid = $('#shopid').val();
        var branchid = $('#branchid').val();

        $('#pageActive').LoadingOverlay("show");
        $.ajax({
            type:'get',
            url: base_url+'Main/oblr_chart_data',
            data: {'fromdate':fromdate,'todate':todate,'shopid':shopid,'branchid':branchid,'oblr_filter':oblr_filter},
            success:function(data){
                $('#pageActive').LoadingOverlay("hide");
                json_data = JSON.parse(data);
                
                // console.log(json_data.chartdata.totalsales);
                setOblrChart(json_data.chartdata);

                $(".btnSearch").prop('disabled', false); 
                $(".btnSearch").text("Search");
                $('.overlay').css("display","none");												
            }
        });
    })

    document.getElementById('reprange').onchange = function () {
	    var reprange = $('#reprange').val();
	    var shopid = $('#shopid').val();
	    var todaydate = $('#todaydate').val();
        var branchid = $('#branchid').val();
        
	    if(reprange == 'custom')
	    {
            $(".search-filter").toggleClass('hidden');
	    	document.getElementById("f_date").disabled=false;
			document.getElementById("f_date_2").disabled=false;
	    }
	    else
	    {
            if (!$(".search-filter").is(':hidden')) {
                $(".search-filter").toggleClass('hidden');
            }
		    if(reprange == 'today')
		    {
			 	$('#f_date').datepicker().datepicker('setDate', todaydate);
		    	$('#f_date_2').datepicker().datepicker('setDate', todaydate);   	

		    	document.getElementById("f_date").disabled=true;
				document.getElementById("f_date_2").disabled=true;
		    }
		    else if(reprange == 'yesterday')
		    {
		    	var yesterday = moment().add(-1, 'day').toDate(todaydate);

		    	$('#f_date').datepicker().datepicker('setDate', yesterday);
		    	$('#f_date_2').datepicker().datepicker('setDate', yesterday);
		    	
		    	document.getElementById("f_date").disabled=true;
				document.getElementById("f_date_2").disabled=true;
		    }
		    else if(reprange == 'last_7')
		    {
		    	var day7 = moment().add(-6, 'day').toDate(todaydate);

		    	$('#f_date').datepicker().datepicker('setDate', day7);
		    	$('#f_date_2').datepicker().datepicker('setDate', todaydate);

		    	document.getElementById("f_date").disabled=true;
				document.getElementById("f_date_2").disabled=true;
		    }
		    else if(reprange == 'last_30')
		    {
		    	var day30 = moment().add(-30, 'day').toDate(todaydate);

		    	$('#f_date').datepicker().datepicker('setDate', day30);
		    	$('#f_date_2').datepicker().datepicker('setDate', todaydate);

		    	document.getElementById("f_date").disabled=true;
				document.getElementById("f_date_2").disabled=true;
		    }
		    else if(reprange == 'last_90')
		    {
		    	var day90 = moment().add(-90, 'day').toDate(todaydate);

		    	$('#f_date').datepicker().datepicker('setDate', day90);
		    	$('#f_date_2').datepicker().datepicker('setDate', todaydate);

		    	document.getElementById("f_date").disabled=true;
				document.getElementById("f_date_2").disabled=true;
		    }
		    else
		    {
		    	$('#f_date').datepicker().datepicker('setDate', todaydate);
		    	$('#f_date_2').datepicker().datepicker('setDate', todaydate);   
				
				document.getElementById("f_date").disabled=true;
				document.getElementById("f_date_2").disabled=true;
            }
            $('#pageActive').LoadingOverlay("show");
            rbl_filter = $('#rbl_filter').val();
		    fromdate = $('#f_date').val();
            todate = $('#f_date_2').val();
            // reset values for lazy load
            viewReportCharts = 0;
            chart_lazyload.start = 0;
            chart_lazyload.end = report_charts_chunk_size() - 1;
            current_report_loaded = Object.values(report_charts)[0];
            chartIndex = 0;
            scrollLoad_chart_ctr = 1;
            // console.log(branchid);
            aj_req(fromdate, todate, shopid, branchid, rbl_filter);
		}
    };

    // lazy load
    var scrollable = false;
    $(window).scroll(function() {
        var scrollSize = $(window).scrollTop() + $(window).height();
        // $('#pageActive').LoadingOverlay("show");
        if (current_report_loaded !== 'end' && scrollable) {
            if(Math.round(scrollSize) >= Math.round($(document).height()) - 60) {
                scrollable = false;
                show_ChartLoading();
                aj_req(fromdate, todate, shopid, branchid, rbl_filter);
            }
        }
    });

    var scrollLoad_chart_ctr = 1;
    var current_report_loaded = Object.values(report_charts)[0];
    var viewReportCharts = 0; var chartIndex = 0;
    function aj_req (fromdate, todate, shopid, branchid) {
        if (viewReportCharts == 0) reset_display();
        // don't load chart without data on picked date
        var chart_key = $(`#${current_report_loaded}`).attr('fromrecord'); var getchartdata = true;
        if (fromdate == todate && chart_key != undefined) {
            getchartdata = nullDateRecords[chart_key].find( data => data == fromdate) ? false:true;
        } else {
            // get ranges of dates that has no data
            var nullDateRanges = nullDateRecords[chart_key].filter(data => data.match(' - '));
            // console.log(nullDateRanges);
            nullDateRanges.every( (val, k) => {
                var fdate_temp = new Date(val.split(' - ')[0]);
                var tdate_temp = new Date(val.split(' - ')[1]);
                var fromdate_temp = new Date(fromdate);
                var todate_temp = new Date(todate);
                // test if picked date range is in the range of any null date ranges
                if (fdate_temp.getTime() <= fromdate_temp.getTime() && tdate_temp.getTime() >= todate_temp.getTime()) {
                    getchartdata = false; return false;
                } else {
                    getchartdata = true; return true;
                }
            });
        }
        var data = {
            'report':current_report_loaded,
            'fromdate':fromdate,
            'todate':todate,
            'shopid':shopid,
            'branchid':branchid,
            'index':chartIndex,
            'run_request':getchartdata,
            'chartsPerScrollLoad':report_charts_chunk_size(),
            'scrollLoad_chart_ctr':scrollLoad_chart_ctr,
        };
        if (current_report_loaded == 'rbl' || current_report_loaded == 'oblr') {
            data['rbl_filter'] = rbl_filter;
        }
        
        $.ajax({
            type:'get',
            url: base_url+'Main/dashboard_table',
            data: data,
            success:function(data){
                json_data = JSON.parse(data);
                // console.log(json_data.chartdata.totalsales);
                fill_data(json_data.chartdata, shopid);
                // viewReportCharts = $('#report-charts > div:visible').length;
                if (json_data.show_chart == 0) {
                    // nullDateRecords[chart_key] = [...nullDateRecords[chart_key], ];
                    var temp_from_date = fromdate.split('/');
                    var temp_end_date  = todate.split('/');
                    var new_fromdate = `${temp_from_date[2]}-${temp_from_date[1]}-${temp_from_date[0]}`;
                    var new_todate   = `${temp_end_date[2]}-${temp_end_date[1]}-${temp_end_date[0]}`;
                    var is_equal = (new_fromdate === new_todate) ? fromdate:`${new_fromdate} - ${new_todate}`;
                    if (!nullDateRecords[chart_key].find( data => data == is_equal)) {
                        nullDateRecords[chart_key].push(is_equal);
                    }
                }
                viewReportCharts += json_data.show_chart;
                current_report_loaded = json_data.next_chart;
                scrollLoad_chart_ctr += json_data.show_chart;
                chartIndex = json_data.next_index;
                scrollable = viewReportCharts%report_charts_chunk_size() == 0 ? true:viewReportCharts-1%report_charts_chunk_size() ? true:false;
                if (current_report_loaded == 'end') {
                    hide_ChartLoading();
                    $(".btnSearch").prop('disabled', false); 
                    $(".btnSearch").text("Search");
                    $('.overlay').css("display","none");
                    $('#pageActive').LoadingOverlay("hide");
                    // test if we're viewing report chart
                    if (viewReportCharts == 0){
                        $('#no-chart-to-view').show();
                        $('#nreport-charts').hide();
                    }
                } else {
                    if (viewReportCharts > 0) {
                        show_ChartLoading();
                    }
                    if (scrollLoad_chart_ctr <= report_charts_chunk_size()) {
                        setTimeout(aj_req(fromdate, todate, shopid, branchid, rbl_filter), 500);
                    } else {
                        hide_ChartLoading();
                        scrollLoad_chart_ctr = 1;
                    }
                }
            },
            error: function (data) {
                if (data.status == 404 || data.status == 408 || data.status == 500 || data.status == 504) {
                    chartIndex++;
                    current_report_loaded = Object.values(report_charts)[chartIndex];
                    current_report_loaded = current_report_loaded == undefined ? 'end':current_report_loaded;
                    if (current_report_loaded !== 'end') {
                        setTimeout(aj_req(fromdate, todate, shopid, branchid, rbl_filter), 500);
                    } else {
                        hide_ChartLoading();
                    }
                }
            }
        });
    }

    function show_ChartLoading () {
        $('#chart-load').css({
            'height':'auto',
            'visibility':'visible'
        });
    }

    function hide_ChartLoading () {
        $('#chart-load').css({
            'height':'0px',
            'visibility':'hidden'
        });
    }

    $("#btnSearch").click(function(e){
		e.preventDefault();
        $('#pageActive').LoadingOverlay("show");
        $('.overlay').css("display","block");
        $('#report-charts > div[fromrecord]').hide();

        rbl_filter = $('#rbl_filter').val();
		fromdate = $('#f_date').val();
		todate = $('#f_date_2').val();
		var shopid = $('#shopid').val();
		var reprange = $('#reprange').val();
        var branchid = $('#branchid').val();
        viewReportCharts = 0;
        chart_lazyload.start = 0;
        chart_lazyload.end = report_charts_chunk_size() - 1;
        current_report_loaded = Object.values(report_charts)[0];
        chartIndex = 0;
        scrollLoad_chart_ctr = 1;

		checker=0;

		if(fromdate == "" || todate == ""){
			checker=0;
			$.toast({
			    heading: 'Note:',
			    text: "Please enter a Date Range",
			    icon: 'info',
			    loader: false,   
			    stack: false,
			    position: 'top-center',  
			    bgColor: '#FFA500',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 3000          
			});
		}else{
			checker=1;
		}

		if(checker==1){
			aj_req(fromdate, todate, shopid, branchid, rbl_filter);
		}
	});


    function fill_data(chart, shopid){

        //////////////////////////////////////////////////// Average Order Value
        if (chart.aov) {
            
            var aov = chart.aov;        
    
                var cur_data = aov.current_data;
                var pre_data = aov.previous_data;
                var cur_period = aov.cur_period;
                var pre_period = aov.pre_period;                
                if (parseFloat(aov.cur_ave) > 0) {
                    $('#pageActive').LoadingOverlay("hide");
                    $('#no-chart-to-view').hide();
                    $('#nreport-charts').show();
                    $('#aov').show();
                    $('#aov').css('visibility','visible');
                    data0 = cur_data.map(function(obj) { return obj['total_amount']; });
                    data1 = pre_data.map(function(obj) { return obj['total_amount']; });    
                    aovChart.options.scales.yAxes[0] = {
                        ticks: {
                            stepSize: aov.step,
                            backdropPaddingY: 30,
                            callback: function (value) {
                                if (value >= 1000000) {
                                    return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                                } else if (value >= 1000) {
                                    return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                                }else{
                                    return value;
                                }
                            }
                        },
                        scaleLabel: {
                            display: true,
                            labelString: '1k = 1000'
                        }
                    };
        
                      aov.dates[0] = "";
                      aovChart.data.labels = aov.dates;
          
                      aovChart.data.datasets[0].label = cur_period;
                      aovChart.data.datasets[1].label = pre_period;
                      
                      aovChart.data.datasets[0].data = data0;
                      aovChart.data.datasets[1].data = data1;
                      
                    aovChart.update();        
            
                    $('#aov_current_ave').text("P "+aov.cur_ave);

                    if(aov.pre_ave != aov.cur_ave){
                        $('#aov_percent').html(`<i class="fa fa-arrow-${(aov.percentage.increased) ? 'up text-blue-400':'down text-red-400'}"></i> ${aov.percentage.percentage} %`);
                    }
                    else{
                        $('#aov_percent').text(`${aov.percentage.percentage} %`);
                    }                    
                }else{
                    $('#aov').hide();
                }
        }

        //////////////////////////////////////////////////// Visitors
        if (chart.ps) {
            var tv = chart.ps;

            var cur_data = tv.current_data;
            var pre_data = tv.previous_data;
            var cur_period = tv.cur_period;
            var pre_period = tv.pre_period;                

            if (tv.cur_total > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#ps').show()
                $('#ps').css('visibility','visible');;
                data0 = cur_data.map(function(obj) { return obj['visitors']; });
                data1 = pre_data.map(function(obj) { return obj['visitors']; });                
    
                visitorChart.options.scales.yAxes[0] = {
                    ticks: {
                        stepSize: tv.step,
                        backdropPaddingY: 30,
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                            } else if (value >= 1000) {
                                return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                            }else{
                                return value;
                            }
                        }
                    },
                };
                    tv.dates[0] = "";
                    visitorChart.data.labels = tv.dates;
    
                    visitorChart.data.datasets[0].label = cur_period;
                    visitorChart.data.datasets[1].label = pre_period;
                    
                    visitorChart.data.datasets[0].data = data0;
                    visitorChart.data.datasets[1].data = data1;
                    
                visitorChart.update();
    
                $('#tv_total').text(tv.cur_total);
                if(tv.pre_total != tv.cur_total){
                    $('#tv_percent').html(`<i class="fa fa-arrow-${(tv.percentage.increased) ? 'up text-blue-400':'down text-red-400'}"></i> ${tv.percentage.percentage} %`);    
                }
                else{
                    $('#tv_percent').html(`${tv.percentage.percentage} %`);
                }
                
            }else{
                $('#ps').hide();
            }
        }

        //////////////////////////////////////////////////// Order and Sales
        if (chart.os) {
            var os = chart.os;
            var cur_data = os.current_data;
            var pre_data = os.previous_data;
            var cur_period = os.cur_period;
            var pre_period = os.pre_period;
    
            if (os.cur_total_to > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#os').show();
                $('#os').css('visibility','visible');
                data0 = cur_data.map(function(obj) { return obj['total_orders']; });
                data1 = pre_data.map(function(obj) { return obj['total_orders']; });                

                os.dates[0] = "";
                osChart.data.labels = os.dates;
    
                osChart.data.datasets[0].label = cur_period;
                osChart.data.datasets[1].label = pre_period;
                
                osChart.data.datasets[0].data = data0;
                osChart.data.datasets[1].data = data1;
                
                osChart.update();
    
                $('#os_total_orders').text(os.cur_total_to);
                if(os.pre_total_to != os.cur_total_to){
                    $('#os_to_percent').html(`<i class="fa fa-arrow-${(os.percentage_to.increased_to) ? 'up text-blue-400':'down text-red-400'}"></i> ${os.percentage_to.percentage_to} %`);
                }
                else{                    
                    $('#os_to_percent').text(`${os.percentage_to.percentage_to} %`);
                }                

                $('#os_total_sales').text("Php "+os.cur_total_ts);
                if(os.pre_total_ts != os.cur_total_ts){
                    $('#os_ts_percent').html(`<i class="fa fa-arrow-${(os.percentage_ts.increased_ts) ? 'up text-blue-400':'down text-red-400'}"></i> ${os.percentage_ts.percentage_ts} %`);   
                }
                else{                    
                    $('#os_to_percent').text(`${os.percentage_ts.percentage_ts} %`);
                } 
                
            } else {
                $('#os').hide();
            }           
        }

        //////////////////////////////////////////////////// Total Orders
        if (chart.to) {
            var to = chart.to;
        
            var cur_data = to.current_data;
            var pre_data = to.previous_data;
            var cur_period = to.cur_period;
            var pre_period = to.pre_period;                
            if (to.cur_total > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#to').show();
                $('#to').css('visibility','visible');
                data0 = cur_data.map(function(obj) { return obj['total_paid_orders']; });
                data1 = pre_data.map(function(obj) { return obj['total_paid_orders']; });                

                to.dates[0] = "";
                toChart.data.labels = to.dates;

                toChart.data.datasets[0].label = cur_period;
                toChart.data.datasets[1].label = pre_period;
                
                toChart.data.datasets[0].data = data0;
                toChart.data.datasets[1].data = data1;
                
                toChart.update();

                $('#to_total_orders').text(to.cur_total);
                if(to.pre_total != to.cur_total){
                    $('#to_percent').html(`<i class="fa fa-arrow-${(to.percentage.increased) ? 'up text-blue-400':'down text-red-400'}"></i> ${to.percentage.percentage} %`);
                }
                else{                    
                    $('#to_percent').text(`${to.percentage.percentage} %`);
                }                

                $('#to_f_total').html('<b>' + to.cur_total_f + '<b>');
                if(to.pre_total_f != to.cur_total_f){
                    $('#to_f_percent').html(`<b><i class="fa fa-arrow-${(to.percentage_f.increased_f) ? 'up text-blue-400':'down text-red-400'}"></i> ${to.percentage_f.percentage_f} %</b>`);
                }
                else{
                    $('#to_f_percent').html(`<b>${to.percentage_f.percentage_f} %</b>`);
                }                

                $('#to_d_total').html('<b>' + to.cur_total_d + '<b>');
                if(to.pre_total_d != to.cur_total_d){
                    $('#to_d_percent').html(`<b><i class="fa fa-arrow-${(to.percentage_d.increased_d) ? 'up text-blue-400':'down text-red-400'}"></i> ${to.percentage_d.percentage_d} %</b>`);
                }
                else{                    
                    $('#to_d_percent').html(`<b>${to.percentage_d.percentage_d} %</b>`);
                }
                
            } else {
                $('#to').hide();
            }
        }

        //////////////////////////////////////////////////// Top Products Sold
        if (chart.tps) {
            var tps = chart.tps;

            var cur_data = tps.cur_data;
            var pre_data = tps.pre_data;
            var cur_period = tps.cur_period;
            var pre_period = tps.pre_period;                
            if (tps.cur_result.length > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#tps').show();
                $('#tps').css('visibility','visible');
                top10 = cur_data.map(function(obj) { return obj['itemname']; });
                current = cur_data.map(function(obj) { return obj['qty']; });
                previous = pre_data.map(function(obj) { return obj['qty']; });                
                
                tpsChart.data.labels = top10;
        
                tpsChart.data.datasets[0].label = cur_period;
                tpsChart.data.datasets[1].label = pre_period;
                
                tpsChart.data.datasets[0].data = current;
                tpsChart.data.datasets[1].data = previous;
                
                tpsChart.update();
            } else {
                $('#tps').hide();
            }
        }

        //////////////////////////////////////////////////// View
        var all_views=0;

        $.each(chart.pageviews, function(k, v) {
            viewChart.data.labels.push(v.trandate);
            viewChart.data.datasets[0].data.push(v.bilang);

            all_views += parseFloat(v.bilang);
        });

        viewChart.update();

        $('#head_views').html(formatNumber(all_views).toLocaleString() + '<span class="summary-stat-title d-block">Views</span>');

        //////////////////////////////////////////////////// Sales
        var all_paid_amount=0;
        var all_unpaid_amount=0;
        var all_total_amount=0;

        $.each(chart.total_sales, function(k, v) {
            salesChart.data.labels.push(v.trandate);
            salesChart.data.datasets[0].data.push(v.paid_amount);
            salesChart.data.datasets[1].data.push(v.unpaid_amount);
            salesChart.data.datasets[2].data.push(v.total_amount);
         
            all_paid_amount += parseFloat(v.paid_amount);
            all_unpaid_amount += parseFloat(v.unpaid_amount);
            all_total_amount += parseFloat(v.total_amount);
        });

        // $('span#total_sales_amount').text('P ' + formatNumber(all_total_amount).toLocaleString());
        // console.log(all_paid_amount);

        salesChart.update();

        $('#head_sales').html(formatNumber(all_total_amount).toLocaleString() + '<span class="summary-stat-title d-block">Sales</span>');

        $('#paid_sales').text(formatNumber(all_paid_amount).toLocaleString());
        $('#unpaid_sales').text(formatNumber(all_unpaid_amount).toLocaleString());
        $('#total_sales').text(formatNumber(all_total_amount).toLocaleString());

        ////////////////////////////////////////////////////// Total Sales

        if (chart.totalsales) {
            if (chart.totalsales.total > 0) {
                totalSales.data.datasets[0].label = [chart.totalsales.legend[0]];
                totalSales.data.datasets[1].label = [chart.totalsales.legend[1]];
                // console.log(totalsales.dates);
                totalSales.data.labels = chart.totalsales.dates;
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                totalSales.options.scales.yAxes[0] = {
                    ticks: {
                        stepSize: chart.totalsales.step,
                        backdropPaddingY: 30,
                        callback: function (value) {
                            if (value >= 1000000) {
                                return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                            } else if (value >= 1000) {
                                return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                            }else{
                                return value;
                            }
                        }
                    },
                    scaleLabel: {
                        display: true,
                        labelString: '1k = 1000'
                    }
                };
                $('#tsr').show();
                $('#tsr').css('visibility','visible');

                totalSales.data.datasets[0].data = chart.totalsales.ts[0];
                totalSales.data.datasets[1].data = chart.totalsales.ts[1];
    
                $('span#total_sales_amount').text(`P ${chart.totalsales.head.total}`);
                $('span#total_sales_percent').html(chart.totalsales.head.percent);
                $('td#op_total').text(`P ${chart.totalsales.op.total}`);
                $('td#op_percent').html(chart.totalsales.op.percent);
                $('td#mp_total').text(`P ${chart.totalsales.mp.total}`);
                $('td#mp_percent').html(chart.totalsales.mp.percent);
    
                // $('span#total_sales_amount').text('P ' + formatNumber(all_total_amount).toLocaleString());
                // console.log(all_paid_amount);
    
                totalSales.update();
            } else {
                $('#tsr').hide();
            }
        }

        ///////////////////////////////////////////////////// Online Store Conversion Rate

        var oscrr = chart.oscrr
        if (oscrr) {
            if (oscrr.success) {
                //   $('.oscr_data').html(oscrr);
                if (oscrr.result > 0) {
                    $('#pageActive').LoadingOverlay("hide");
                    $('#no-chart-to-view').hide();
                    $('#nreport-charts').show();
                    $('#oscrr').show();
                    $('#oscrr').css('visibility','visible');
                    $.each(oscrr.data, function ($k, $v) {
                      $.each($(`tr .${$k}`), function ($el_k, $el_v) {
                        $($el_v).html($v[$el_k]);
                      })
                    });
                    $('.oscr_top #oscr_p1').html(oscrr.top_data[0]);
                    $('.oscr_top #oscr_p2').html(oscrr.top_data[1])
                } else {
                    $('#oscrr').hide();
                }
            } else {
                $('#oscrr').hide();
            }
        }

        ///////////////////////////////////////////////////// Total Abandoned Carts

        if(chart.tacr){
            var abandoned_cart = chart.tacr;
            // $('#abandonedcart-chart').height('338px');

            if (abandoned_cart.totalData > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#tacr').show();
                $('#tacr').css('visibility','visible');
                $("#abandoned_percent").html(abandoned_cart.computation);

                setChart(abandoned_cart.chartType, abandoned_cart);
            } else {
                $('#tacr').hide();
            }

        }
        
        ///////////////////////////////////////////////////// revenueByStore
        
        if (chart.rBS) {
            // $.each(chart.rBS, function(k, v) {
            // });
            revenueByStore.data.labels = chart.rBS.shopnames;
            // console.log(v);
            if (chart.rBS.data1.length > 0 || chart.rBS.data2.length > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#rbsr').show();
                $('#rbsr').css('visibility','visible');
                revenueByStore.data.datasets[0].data = chart.rBS.data1;
                revenueByStore.data.datasets[0].label = chart.rBS.legend[0];
                revenueByStore.data.datasets[1].data = chart.rBS.data2;
                revenueByStore.data.datasets[1].label = chart.rBS.legend[1];
                revenueByStore.update();
            } else {
                $('#rbsr').hide();
            }
        }

        ///////////////////////////////////////////////////// Revenue By Branch
        
        if (chart.rBB) {
            // $.each(chart.rBS, function(k, v) {
            // });
            revenueByBranch.data.labels = chart.rBB.branchnames;
            // console.log(v);
            if (chart.rBB.data1.length > 0 || chart.rBB.data2.length > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#rbbr').show();
                $('#rbbr').css('visibility','visible');
                revenueByBranch.data.datasets[0].data = chart.rBB.data1;
                revenueByBranch.data.datasets[0].label = chart.rBB.legend[0];
                revenueByBranch.data.datasets[1].data = chart.rBB.data2;
                revenueByBranch.data.datasets[1].label = chart.rBB.legend[1];
                revenueByBranch.update();
            } else {
                $('#rbbr').hide();
            }
        }

        //////////////////////////////////////////////////// Revenue By Location

        viewReportCharts = setRblChart(chart, viewReportCharts);

        ///////////////////////////////////////////////////// Orders By Location

        viewReportCharts = setOblrChart(chart, viewReportCharts);

        ///////////////////////////////////////////////////// Pending Orders

        if (chart.po) {
            if (chart.po.total > 0) {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#po').show();
                $('#po').css('visibility','visible');
                poChart.data.labels = chart.po.chartdata.labels;
                poChart.data.datasets[0] = {
                    'label' : chart.po.chartdata.data.label,
                    'backgroundColor' : gradient,
                    'data' : chart.po.chartdata.data.data,
                };
                poChart.update();
            } else {
                $('#po').hide();
            }
        }

        ///////////////////////////////////////////////////// Inventory List

        if (chart.invlist) {
            shopid = (shopid == 0) ? 'all':shopid;
            if (chart.invlist.total > 0 && shopid !== 'all') {
                $('#pageActive').LoadingOverlay("hide");
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#invlist').show();
                $('#invlist').css('visibility','visible');
                invChart.data.labels = chart.invlist.chartdata.labels;
                invChart.data.datasets[0] = {
                    'label': 'Inventory',
                    'backgroundColor': chart.invlist.chartdata.background,
                    'data': chart.invlist.chartdata.data[0]
                };
                invChart.update();
            } else {
                $('#invlist').hide();
            }
        }

        ///////////////////////////////////////////////////// Transactions
        var all_paid_count=0;
        var all_unpaid_count=0;
        var all_total_count=0;

        $.each(chart.total_orderscount, function(k, v) {
            transcationChart.data.labels.push(v.trandate);
            transcationChart.data.datasets[0].data.push(v.paid_count);
            transcationChart.data.datasets[1].data.push(v.unpaid_count);
            transcationChart.data.datasets[2].data.push(v.total_count);

            all_paid_count += parseFloat(v.paid_count);
            all_unpaid_count += parseFloat(v.unpaid_count);
            all_total_count += parseFloat(v.total_count);
        });

        transcationChart.update();

        $('#head_transactions').html(formatNumber(all_total_count).toLocaleString() + '<span class="summary-stat-title d-block">Transactions</span>');

        $('#paid_count').text(formatNumber(all_paid_count).toLocaleString());
        $('#unpaid_count').text(formatNumber(all_unpaid_count).toLocaleString());
        $('#total_count').text(formatNumber(all_total_count).toLocaleString());
   
        ///////////////////////////////////////////////////// Overall Sales
        // var overall_sales = parseFloat(table.overallsales[0].total_amount);

        // $('#head_overall_sales').html(formatNumber(overall_sales).toLocaleString() + '<span class="summary-stat-title d-block">Overall Sales</span>');

        ///////////////////////////////////////////////////// Top 10 Products Sold
        // $.each(table.topitems, function(k, v) {
        //     var li = "";

        //     li = "<li>"+v.itemname+" ("+v.uom+")</li>"

        //     $("#top_10_products").append(li);
        // });
    }

    function reset_display() {
        visitorChart.data.labels = [];
        visitorChart.data.datasets[0].data = [];
        visitorChart.update();

        viewChart.data.labels = [];
        viewChart.data.datasets[0].data = [];
        viewChart.update();

        salesChart.data.labels = [];
        salesChart.data.datasets[0].data = [];
        salesChart.data.datasets[1].data = [];
        salesChart.data.datasets[2].data = [];
        salesChart.update();

        $('#oscrr').hide();

        $('#tsr').hide();
        totalSales.data.labels = [];
        totalSales.data.datasets[0].data = [];
        totalSales.data.datasets[1].data = [];
        totalSales.update();
        
        $('#rbsr').hide();
        revenueByStore.data.labels = [];
        revenueByStore.data.datasets[0].data = [];
        revenueByStore.data.datasets[1].data = [];
        revenueByStore.update();
        
        $('#rbbr').hide();
        revenueByBranch.data.labels = [];
        revenueByBranch.data.datasets[0].data = [];
        revenueByBranch.data.datasets[1].data = [];
        revenueByBranch.update();

        $('#rbl').hide();
        rbl_chart.data.labels = [];
        rbl_chart.data.datasets = [];
        rbl_chart.update();

        $('#oblr').hide();
        oblr_chart.data.labels = [];
        oblr_chart.data.datasets = [];
        oblr_chart.update();
       
        transcationChart.data.labels = [];
        transcationChart.data.datasets[0].data = [];
        transcationChart.data.datasets[1].data = [];
        transcationChart.data.datasets[2].data = [];
        transcationChart.update();

        visitorChart.data.labels = [];
        visitorChart.data.datasets[0].data = [];
        visitorChart.data.datasets[1].data = [];
        visitorChart.update();

        $('#aov').hide();
        aovChart.data.labels = [];
        aovChart.data.datasets[0].data = [];
        aovChart.data.datasets[1].data = [];
        aovChart.update();

        $('#ps').hide();
        visitorChart.data.labels = [];
        visitorChart.data.datasets[0].label = "";
        visitorChart.data.datasets[1].label = "";
        visitorChart.data.datasets[0].data = [];
        visitorChart.data.datasets[1].data = [];
        visitorChart.update();

        $('#os').hide();
        osChart.data.labels = []
        osChart.data.datasets[0].label = "";
        osChart.data.datasets[1].label = "";             
        osChart.data.datasets[0].data = [];
        osChart.data.datasets[1].data = [];
        osChart.update();

        $('#to').hide();
        toChart.data.labels = [];
        toChart.data.datasets[0].label = "";
        toChart.data.datasets[1].label = "";              
        toChart.data.datasets[0].data = [];
        toChart.data.datasets[1].data = [];
        toChart.update();

        $('#tps').hide();
        tpsChart.data.labels = [];    
        tpsChart.data.datasets[0].label = "";
        tpsChart.data.datasets[1].label = "";
        tpsChart.data.datasets[0].data = [];
        tpsChart.data.datasets[1].data = [];
        tpsChart.update();

        $('#tacr').hide();
        if ($('#abandonedcart-chart').length > 0) {
            abandoned_cart_chart.destroy();
            abandonedChart = document.getElementById("abandonedcart-chart").getContext('2d');
            abandonedChart.canvas.height = '338';
        }

        $('#po').hide();
        poChart.data.labels = [];
        poChart.data.datasets[0].data = [];
        poChart.update();

        $('#invlist').hide();
        invChart.data.labels = [];
        invChart.data.datasets = [];
        invChart.update();

        $("#top_10_products").html("");
    }
    
    $("#f_date").datepicker("setDate",fromdate);
    $("#f_date_2").datepicker("setDate",todate);

    function formatNumber(num) {
	    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }
    
    $("a.view_report_link").click((el) => {
        var h = $(el.target).attr('href').split("?");
        var add_ = (shopid == 0) ? "":`&shopid=${shopid}`;
        if (h[0].match('revenue_by_store/by_location')) {
            rbl_filter = $('#rbl_filter').val();
            $(el.target).attr('href', `${h[0]}?fromdate=${fromdate}&todate=${todate}${add_}&rbl_location=${rbl_filter}`);
        } else if (h[0].match('reports/orders_by_location')) {
            rbl_filter = $('#oblr_filter').val();
            $(el.target).attr('href', `${h[0]}?fromdate=${fromdate}&todate=${todate}${add_}&oblr_filter=${rbl_filter}`);
        } else {
            $(el.target).attr('href', `${h[0]}?fromdate=${fromdate}&todate=${todate}${add_}`);
        }
    });

    function setRblChart (chart, viewReportCharts) {
        if (chart.rBL) {
            var rBL = chart.rBL.chartdata;
            if (rBL.labels.length > 0) {
                $('#pageActive').LoadingOverlay("hide"); viewReportCharts += 1;
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#rbl').show();
                $('#rbl').css('visibility','visible');
                rbl_chart.data.labels = rBL.labels;
                rbl_chart.data.datasets = rBL.dataset;
                // rbl_chart.options.scales.yAxes[0].ticks.stepSize = rBL.stepsize;
                // r_plugin_labels = rBL.p_labels;
                rbl_chart.update();
            } else {
                $('#rbl').hide();
            }
        }

        return viewReportCharts;
    }

    function setOblrChart (chart, viewReportCharts) {
        if (chart.oblr) {
            var oBL = chart.oblr.chartdata;
            if (oBL.total > 0) {
                $('#pageActive').LoadingOverlay("hide"); viewReportCharts += 1;
                $('#no-chart-to-view').hide();
                $('#nreport-charts').show();
                $('#oblr').show();
                $('#oblr').css('visibility','visible');
                oblr_chart.data.labels = oBL.labels;
                oblr_chart.data.datasets = oBL.dataset;
                // oblr_chart.options.scales.yAxes[0].ticks.stepSize = oBL.stepsize;
                // r_plugin_labels = rBL.p_labels;
                oblr_chart.update();
            } else {
                $('#oblr').hide();
            }
        }

        return viewReportCharts;
    }

    function setChart(type, data) {
        abandoned_cart_chart = new Chart(abandonedChart, {
            type: type,
            data: {
                labels: (type == 'line') ? data.labels:['Abandoned'],
                datasets: [{
                    label: data.legend[0],
                    data: data.data[0],
                    backgroundColor: (type == 'line') ? 'rgba(0,0,0,0)':'#07DA63',
                    borderColor: (type == 'line') ? '#07DA63':'rgba(0,0,0,0)',
                    borderWidth: (type == 'line') ? 2:0
                },
                {
                    label: data.legend[1],
                    data: data.data[1],
                    backgroundColor: (type == 'line') ? 'rgba(0,0,0,0)':'darkgray',
                    borderColor: (type == 'line') ? 'darkgray':'rgba(0,0,0,0)',
                    borderWidth: (type == 'line') ? 2:0
                }]
            },
            options: {
                title:{
                    display: true,
                    position: 'top',
                    text: "Abandoned Carts Overtime"
                },
                elements: {
                  line: {
                      tension: 0
                  },
                  point: {
                      radius: 0
                  }
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    align: 'end',
                    fullWidth: true,
                    labels:{
                        boxWidth: 10,
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            stepSize: data.step,
                            beginAtZero: true,
                            callback: function (value) {
                                if (type == 'line') {
                                    if (value >= 1000000) {
                                        return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                                    } else if (value >= 1000) {
                                        return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                                    }else{
                                        return value;
                                    }
                                } else {
                                    return value;
                                }
                            }
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            autoSkip: true,
                            beginAtZero: true,
                            autoSkipPadding: 30,
                            maxRotation: 0,
                            callback: function (value) {
                                if (type !== 'line') {
                                    if (value >= 1000000) {
                                        return (value%1000000 == 0) ? (value/1000000) + 'k':(value/1000000).toPrecision(2) + 'k';
                                    } else if (value >= 1000) {
                                        return (value%1000 == 0) ? (value/1000) + 'k':(value/1000).toPrecision(2) + 'k';
                                    }else{
                                        return value;
                                    }
                                } else {
                                    return value;
                                }
                            }
                        }
                    }]
                }
            }
        });
    }
});

$('#f_date_2').datepicker({
    todayBtn: 'linked',
    autoclose: true,
}).on('changeDate', (e) => {
    var todaydate = $('#todaydate').val();
    var new_start_date = moment(e.date).subtract(90, 'day').format('MM/DD/YYYY');
    // console.log(new_start_date);

    $('#f_date').datepicker('setStartDate', new_start_date);
    $('#f_date_2').datepicker('setEndDate', todaydate);
})