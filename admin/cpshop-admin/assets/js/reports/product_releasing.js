$(function(){
    //hides data container
    data_toggle("hide");
    //false on first run
    var first_run = false;
  
    var shopid = $('#shopid').val();
    var branchid = $('#branchid').val();
    
      var base_url = $("body").data('base_url');
      var token = $('#token').val();
      var filter = {
        fromdate: $('input#date_from').val(),
        todate: $('input#date_to').val(),
        shop_id: $('#select_shop').children("option:selected").val(),
        branch_id: $('#select_branch').children("option:selected").val(),
        release_type: $('#release_type').children("option:selected").val(),
      };
      var initial_datefrom = $('#date_from').val();
      var initial_dateto = $('#date_to').val();
  
      // Charts --------------------
    
      var transactions_chart = document.getElementById("transactions");
    
      if(transactions_chart){ 
        var graph__transactions = new Chart(transactions_chart, {
            type: 'horizontalBar',
            data: {
                labels: [],
                datasets: [
                  {
                    label: [],
                    borderColor: '#07DA63',                  
                    backgroundColor: '#07DA63',
                    borderWidth: 3, 
                    data: []
                  }                
              ]
            },
            options: {           
              tooltips: {
                  mode: 'point',                
              },    
              title:{
                display: true,
                position: 'top',
                text: "Product Releasing"
              },     
              legend: {
                  position: 'bottom',
                  display: false
              },
              scales: {
                yAxes: [{
                    ticks: {
                        //beginAtZero: true,
                        //stepSize: 4000,
                        precision: 0,
                        // stacked: true,
                        backdropPaddingY: 30
                    }
                }],
                xAxes: [{
                    ticks: {
                        autoSkip: true,
                        beginAtZero: true,
                        autoSkipPadding: 30,
                        minRotation: 0,
                        maxRotation: 80,
                        precision: 0,
                        // maxTicksLimit: 20
                    }
                }]
              }   
            }
      });
       
      } 
    
      function chart(){
        $.ajax({
                type:'post',
                url: base_url+'reports/Product_releasing/get_product_releasing_data',
                data: filter,
                success:function(data){
                  $.LoadingOverlay("hide"); 
                    if(data.success == true){                    
                        fill_data(data.chartdata);
                    }else{
                        fill_data(data.chartdata);
                    }
                    
                }
            });
      }
    
      function fillDataTable() {      
        var aov = $('#table-grid').DataTable({
          "processing": true,
          "serverSide": true,
          "responsive": true,
          "searching": false,
          "destroy": true,      
          columnDefs: [                      
            { responsivePriority: 1, targets: 0},
            {"targets":[0],"visible":shopid == 0 ? true:false},
            {"targets":[1],"visible":branchid == 0 ? true:false},
          ],
          "ajax": {
            url: base_url + 'reports/Product_releasing/get_product_releasing_table',
            type: 'post',
            data: filter,
            beforeSend: function () {            
              $.LoadingOverlay("show");            
            },
            complete: function (data) {
                $.LoadingOverlay("hide");          
                var response = $.parseJSON(data.responseText);
  
                if(response.data.length > 0){                
                  $('.btnExport').show(100);                                
                  $('#t_quantity').text(response.total_quantity);
                  data_toggle("show");
                  $('#chart_toggle').show();                 
                }
                else{
                  data_toggle("hide");
                  $('#chart_toggle').hide();
                  $('#btnExport').hide(100);                                       
                  $('#t_amount').html('');
                }
                first_run = true; 
                $("input#_search").val(JSON.stringify(this.data));
                $("input#_filters").val(JSON.stringify(filter));  
            },
            error: function () {  // error handling
                $(".table-grid-error").html("");            
                $("#table-grid_processing").css("display", "none");
            }
          }
        }).on('draw.dt', function () {
          var info = aov.page.info();        
          if(info.page + 1 === info.pages) {
            console.log('Last Page');
            //$('#last_page').show();
          } else {
            //$('#last_page').hide();
          }
        });
      };
  
      //mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
      //it will be removed soon once layout problem is fixed
      $('#date_to_m').on('change',function(){
        $('#date_to').val($(this).val());
      });
    
      fillDataTable();
    
      chart(); // initialize chart
    
      $(document).on('click', '#btnSearch', function(){
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.shop_id = $('#select_shop').children("option:selected").val();
        filter.branch_id = $('#select_branch').children("option:selected").val();
        filter.release_type = $('#release_type').children("option:selected").val();                   
        chart();
        fillDataTable();
      });
  
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
    
      $("#search_clear_btn").click(function(e){
        filter.fromdate = initial_datefrom;
        filter.todate = initial_dateto;          
        $('#date_from').val(initial_datefrom);
        $('#date_to').val(initial_dateto);
        chart();
        fillDataTable();
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
    
      function fill_data(data){
    
            if(transactions_chart)
            {
                var products = data.data;
                var release_type = data.release_type;

                items = products.map(function(obj) { return obj['quantity']; });      
                labels = products.map(function(obj) { return obj['itemname']; });                  
                  graph__transactions.data.labels = labels;
                  graph__transactions.data.datasets[0].label = '';
                  graph__transactions.data.datasets[0].data = items;
                              
                  
                if(release_type == "released"){
                    graph__transactions.options.title.text = "Top Products (Released)";
                }
                else{
                    graph__transactions.options.title.text = "Top Products (Not Released)";
                }
                
                graph__transactions.update();    
            }                   
        }
        
        function format_num(item, index, arr){
          arr[index] = parseFloat(item).toFixed(2);
        }
  
  
        //changeable branches by shops      
  
        if(shopid == 0){
          $('#select_branch').css('display','none');
          getShopBranches($('#select_shop').children("option:selected").val());
        }               
        else{
          getShopBranches(shopid);
        }         
        
  
        $('#select_shop').on('change',function(){
          if($('#select_shop').children("option:selected").val() == "all"){
            $('#select_branch').css('display','none');
          }
          else{
            $('#select_branch').css('display','inline-block');
            getShopBranches($('#select_shop').children("option:selected").val());
          }
          
        });
    
        //get branches of selected shop
        function getShopBranches($selected_shop){        
          $.ajax({
            url: base_url + 'reports/Report_tools/getBranchOptions/'+$selected_shop,
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function(){
              $.LoadingOverlay("show");     
            },
            success: function(data){                
              $('#select_branch').html(data.options);   
              $.LoadingOverlay("hide");
            },
            error: function(){
              $.LoadingOverlay("hide");
            }
          });      
        }      
    });
  
  
    