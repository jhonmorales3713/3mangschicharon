$( function () {
    data_toggle("hide");
    var first_run = false;

    var base_url = $("body").data('base_url');
    var token = $('#token').val();
    
    var filter = {
        search_val: $('#search_val').val(),
        shopid: $('#select_shop').val(),
        branchid: $('#select_branch').val(),
        fromdate: $('#date_from').val(),
        todate: $('#date_to').val()
    };
    var initial_shop_id = ($('#select_shop').val() == 'all') ? 0:$('#select_shop').val();
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();

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
            render: function (args) {
              var str = `${args.percentage}%`;
              // var new_lbl = (args.percentage > 20 && args.label.length > 20) ? args.label.substr(0, 17) + '...':args.label;
              // $(new_lbl.split(' ')).each( (k, v) => {
              //   str += `\n${v}`;
                // if (args.percentage > 15) {
                // } else {
                //   var new_str = (v.length > 10) ? v.substr(0, 7) + '...':v;
                //   str += `\n${new_str}`;
                // }
              // })
              // str +=`\n${args.label}`;
              // str +=`\n${args.value}`;
              return str;
            },
            fontColor: ['#000','#fff','#000','#fff','#000','#000','#000','#000','#000','#fff'],
            fontSize: 11,
            overlap: false,
            position: 'border',
            outsidePadding: 10,
            textMargin: 5,
          },
        },
        title:{
          display: false,
          position: 'top',
          text: "Pending Orders"
        },     
        legend: {
            display: true,
            align: 'start',
            position: 'left',
            fullWidth: true,
            labels: {
              boxWidth: 10
            }
        },             
      }
    });

    var gen_invlist_tbl = () => {
        return $('#table-grid').DataTable( {
          "processing": true,
          "serverSide": true,
          "responsive": true,
          "autoWidth": false,
          "searching": false,
          "destroy": true,
          "order":[[6, 'desc']],
          "columnDefs":[
            {"targets":[0],"visible":(initial_shop_id > 0) ? false:true}
          ],
          "ajax":{
            url: base_url+'reports/Inventory_list/get_invlist_table',
            type: 'post',
            data: filter,
            beforeSend: function(){
              $.LoadingOverlay("show");
            },
            complete: function(data){
              $.LoadingOverlay("hide");
              var response = $.parseJSON(data.responseText);
      
              if(response.data.length > 0){
                $('.btnExport').show(100);
                $("input#_search").val(JSON.stringify(this.data));
                $("input#_filter").val(JSON.stringify(filter));
                // $("td#p").text(`${response.tfoot['p']}`);
                // $("td#po").text(`${response.tfoot['po']}`);
                // $("td#rp").text(`${response.tfoot['rp']}`);
                // $("td#bc").text(`${response.tfoot['bc']}`);
                data_toggle("show");
                // get_po_chart(JSON.stringify(this.data));
              }
              else{
                data_toggle("hide");
                $('#btnExport').hide(100);
              }
              first_run = true;
              // console.log(JSON.stringify(decodeURIComponent(this.data)));
              $("input#_search").val(JSON.stringify(this.data));
              $("input#_filters").val(JSON.stringify(filter));
            },
            error: function(){
              $.LoadingOverlay("hide");
            }
          }
        });
    }

    function get_inv_chart() {
        $.ajax({
            type:'post',
            url: base_url+'reports/Inventory_list/get_inv_chart',
            data: filter,
            success:function(data){
              $.LoadingOverlay("hide");
              data = JSON.parse(data);
                if(data.success == true){
                  $('th .asof').text(`(of ${data.legend})`);                    
                  if (data.total == 0) {
                    $('#salesChart').hide();
                  }else{
                    $('#salesChart').show();
                  }
                  fill_data(data.chartdata);
                }
            }
        });
    }

    function fill_data (chartdata) {
        invChart.data.labels = chartdata.labels;
        invChart.data.datasets[0] = {
          'label': 'Inventory',
          'backgroundColor': chartdata.background,
          'data': chartdata.data[0]
        };
        invChart.update();
    }
    
    var currentDate = new Date();  
    $("#date_from").datepicker("setDate",initial_datefrom);
    $("#date_to").datepicker("setDate",initial_dateto);

    $(document).ready(function () {
      if (initial_datefrom !== '') {
        gen_invlist_tbl();
        get_inv_chart();
      }
    })

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

    $('#s_range').change( function (val) {
      $('#range_val').text(this.value);
    })

    $(document).on('click', '#btnSearch', function(){
        filter.search_val = $('#search_val').val() || '';
        filter.shopid = $('#select_shop').val() || '';
        filter.branchid = $('#select_branch').val() || '';
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.filtertype = $('#filtertype').val();

        $('#range_val').text($('#s_range').val());
        gen_invlist_tbl();
        get_inv_chart();
    });

    //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
        $('#date_to').val($(this).val());
    });

    //check mobile view for filter icon and chart icon
    if(window.matchMedia("(max-width: 767px)").matches){
    // The viewport is less than 768 pixels wide                  
        $("#search_hideshow_btn").html('<i class="fa fa-search"></i>  <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');        
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

    $(window).on('resize', function() {
      if (window.matchMedia("(max-width: 767px)").matches) {
        invChart.options.legend.position = 'bottom';
        invChart.options.legend.align = 'start';
        invChart.update();
      } else {
        invChart.options.legend.position = 'left';
        invChart.options.legend.align = 'start';
        invChart.update();
      }
    });

    //chart toggle
    $("#chart_toggle").click(function(e){
      e.preventDefault();

      var visibility = $('#salesChart').is(':visible');

      if(!visibility){
          //visible
          if(window.matchMedia("(max-width: 767px)").matches){              
              $("#chart_toggle").html('<i class="fa fa-area-chart"></i> <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
          } else{                
              $("#chart_toggle").html('&ensp;Hide Chart <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
          }              
      }else{
          //not visible
          if(window.matchMedia("(max-width: 767px)").matches){              
              $("#chart_toggle").html('<i class="fa fa-area-chart"></i> <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
          } else{                             
              $("#chart_toggle").html('Show Chart <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
          }
          
      }
      $("#salesChart").slideToggle("slow");
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
        showCpToast("info", "Note!", 'Error occured while trying get branches of selected shop.');
      }
    });      
  }
})