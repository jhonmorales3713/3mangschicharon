$(function(){
    //hides data container
    data_toggle("hide");
    //false on first run
    var first_run = false;

    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    var filter = {
      shopid: $('#select_shop').val(),
      branchid: $('#select_branch').val(),
      filtertype: $('#filtertype').val(),
      location: $('#location').val(),
      fromdate: $('#date_from').val(),
      todate: $('#date_to').val()
    };
    var initial_shop_id = ($('#select_shop').val() == 'all') ? 0:$('#select_shop').val();
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
    var r_plugin_labels = [];
  
    var element = document.getElementById("oblrChart");
    var oblrChart = new Chart(element, {
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
                    fontSize: 11,
                    overlap: true,
                    position: 'border',
                    outsidePadding: 30,
                    textMargin: 15,
                }
            },
            title:{
                display: false,
                position: 'top',
                text: "Top Shops"
            },
            legend: {
                display: true,
                position: 'left',
                align: 'end',
                fullWidth: true,
                labels:{
                    boxWidth: 10,
                }
            },
            responsive: true,
        }
    });
  
    function fill_data(rBL)
    {
        if(oblrChart){
            // console.log(rBL);
            oblrChart.data.labels = rBL.labels;
            oblrChart.data.datasets = rBL.dataset;
            // oblrChart.options.scales.yAxes[0].ticks.stepSize = rBL.stepsize;
            // r_plugin_labels = rBL.p_labels;
            oblrChart.update();
        }
    }
  
    function table(){
      $('#table-grid').DataTable( {
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "searching": false,
        "destroy": true,
        "order":[[0, 'asc']],
        "columnDefs": [
            {"targets":[1],"visible":(filter.shopid == 'all' && filter.filtertype == 'summary') ? false:(initial_shop_id > 0) ? false:(filter.shopid > 0 && filter.filtertype == 'summary') ? true:(filter.shopid > 0 && filter.filtertype == 'logs') ? true:(filter.shopid == 'all' && filter.filtertype == 'logs') ? true:false},
            {"targets":[2],"visible":(filter.shopid > 0) ? true:false},
            {"targets":[4],"visible":(filter.shopid > 0 && filter.filtertype == 'logs') ? false:true},
            {"targets":[5,6],"visible":(filter.shopid > 0 && filter.filtertype == 'logs') ? true:false},
        ],
        "ajax":{
          url: base_url+'reports/order_by_location/oblr_data',
          type: 'post',
          data: filter,
          beforeSend: function(){
            $.LoadingOverlay("show");
          },
          complete: function(data){
            $.LoadingOverlay("hide");
            var response = data.responseJSON.success;
            // var table_data = data.responseJSON.data;
            // console.log(data);
            if(response == 1){
                $('.btnExport').show(100);
                $("input#_search").val(JSON.stringify(this.data));
                $("input#_filter").val(JSON.stringify(filter));
                $('#table-grid-top').show();
                data_toggle("show");
            }else{
                data_toggle("hide");
                $('#btnExport').hide(100);
                $('#table-grid-top').hide();
            }
            first_run = true;
          },
          error: function(){
            $.LoadingOverlay("hide");
          }
        }
    });
    }
  
    function chart(){
        $.ajax({
            type:'post',
            url: base_url+'reports/Order_by_location/oblr_chart_data',
            data: filter,
            success:function(data){
                // console.log(data);
                if(data.success == 1)
                {
                    // console.log(data);
                    fill_data(data.chartdata);
                    $(".btnSearch").prop('disabled', false);
                    $(".btnSearch").text("Search");
                    // $('#table-grid-top').show();

                    // var html = json_data.message;
                    // $(".comdiv").html(html);
                    // $('.overlay').css("display","none");
                }
                else
                {
                    fill_data(data.chartdata);
                    $(".btnSearch").prop('disabled', false);
                    $(".btnSearch").text("Search");
                    // var html = json_data.message;
                    // $(".comdiv").html(html);
                    // $('.overlay').css("display","none");
                }
                
                chart_data_count = data.chartdata.labels.length;
                if(chart_data_count != 0){
                  $('#salesChart').show();
                }
                else{
                  $('#salesChart').hide();
                }
            }
        });
    }
  
    // gen_sales_report_tbl(JSON.stringify(filter));
    chart();
  
    table();
  
    $(document).on('click', '#btnSearch', function(){
        filter.shopid = $('#select_shop').val() || '';
        filter.branchid = $('#select_branch').val() || '';
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.filtertype = $('#filtertype').val();
        filter.location = $('#location').val();
        chart();
        table();
    });

    //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
      $('#date_to').val($(this).val());
    });

    function data_toggle(action){
        if(first_run){
          $("#message-container div i").next().text(" No search results found");
        }
        else{
          $("#message-container div i").next().text(" To show records, kindly select your preferred date range. You may use other filter(s) if there's any.");
        }
        if(action == "hide"){
          $('#data-container').fadeOut();
          $('#message-container').show();
        }
        else if(action =="show"){
          $('#data-container').fadeIn(500);
          $('#message-container').hide();
        }
      }
  
    //check mobile view for filter icon and chart icon
    if(window.matchMedia("(max-width: 767px)").matches){
      // The viewport is less than 768 pixels wide                  
        $("#search_hideshow_btn").html('<i class="fa fa-search"></i>  <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
        $("#chart_toggle").html('<i class="fa fa-area-chart"></i>  <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
    }
  
    //filter toggle
    $("#search_hideshow_btn").click(function(e){
          e.preventDefault();
  
          var visibility = $('#card-header_search').is(':visible');
  
          if(!visibility){
              //visible
              if(window.matchMedia("(max-width: 767px)").matches){
                // The viewport is less than 768 pixels wide
                  $("#search_hideshow_btn").html('<i class="fa fa-search"></i> <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
              } else{
                  // The viewport is at least 768 pixels wide
                  $("#search_hideshow_btn").html('&ensp;Hide Filter <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
              }              
          }else{
              //not visible
              if(window.matchMedia("(max-width: 767px)").matches){
                // The viewport is less than 768 pixels wide                  
                  $("#search_hideshow_btn").html('<i class="fa fa-search"></i> <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
              } else{
                  // The viewport is at least 768 pixels wide                  
                  $("#search_hideshow_btn").html('Show Filter <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
              }
              
          }
  
          $("#card-header_search").slideToggle("slow");
      });

      //chart toggle
      $("#chart_toggle").click(function(e){
        e.preventDefault();
        $("#salesChart").hide();
        $("#chart_toggle").hide();
        $("#chart_toggle1").show();

        // var visibility = $('#salesChart').is(':visible');

        // if(!visibility){
        //     //visible
        //     if(window.matchMedia("(max-width: 767px)").matches){              
        //         $("#chart_toggle").html('<i class="fa fa-area-chart"></i> <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
        //     } else{                
        //         $("#chart_toggle").html('&ensp;Hide Chart <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
        //     }   
        //   chart(); 
        // }else{
        //     //not visible
        //     if(window.matchMedia("(max-width: 767px)").matches){              
        //         $("#chart_toggle").html('<i class="fa fa-area-chart"></i> <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
        //     } else{                             
        //         $("#chart_toggle").html('Show Chart <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
        //     }
            
        // }
        // $("#salesChart").slideToggle("slow");
    });

    $("#chart_toggle1").click(function(e){
        e.preventDefault();
        $("#chart_toggle").show();
        $("#chart_toggle1").hide();
          chart();
          table();
          $("#salesChart").show();
    });
  
    $("#search_clear_btn").click(function(e){
        filter.shopid = "all";
        filter.filtertype = "city";
        filter.fromdate = initial_datefrom;
        filter.todate = initial_dateto;
            // $(".search-input-text").val("");
        $('#date_from').val(initial_datefrom);
        $('#date_to').val(initial_dateto);
        $('#select_shop option[value="all"]').prop('selected', true);
        $('#filtertype option[value="summary"]').prop('selected', true);
        $('#location option[value="city"]').prop('selected', true);
        chart();
        table();
      });

      $('select#select_shop').change( (el) => {
        var s_shop = $(el.target).val();
        if (s_shop == "all") {
            $('#select_branch_container').hide();
        } else {
            getShopBranches(s_shop);
        }
      });
    
      function getShopBranches(s_shop){
        $.ajax({
          url: base_url + 'reports/Report_tools/getBranchOptions/'+s_shop,
          type: 'GET',
          dataType: 'JSON',
          success: function(data){
              if (data.total_opts > 0) {
                  $('#select_branch_container').show();
                  $('select#select_branch').html(data.options);   
              } else {
                  $('#select_branch_container').hide();
              }
          },
          error: function(){
            showCpToast("info", "Note!", "Error occured while trying get branches of selected shop.");
            // $.toast({
            //   heading: 'Note:',
            //   text: "Error occured while trying get branches of selected shop.",
            //   icon: 'info',
            //   loader: false,   
            //   stack: false,
            //   position: 'top-center',  
            //   bgColor: '#FFA500',
            //   textColor: 'white',
            //   allowToastClose: false,
            //   hideAfter: 3000          
            // });
          }
        });      
      }
  });
  