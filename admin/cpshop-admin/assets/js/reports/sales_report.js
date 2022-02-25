$(function () {
	//hides data container
	data_toggle("hide");
	//false on first run
	var first_run = false;

	var base_url = $("body").data("base_url");
	var token = $("#token").val();
	var filter = {
		shopid: $("#select_shop").val(),
		branchid: $('#select_branch').val(),
		filtertype: $("#filtertype").val(),
		pmethodtype: $("#pmethodtype").val(),
		fromdate: $("#date_from").val(),
		todate: $("#date_to").val(),
	};
	var initial_datefrom = $("#date_from").val();
	var initial_dateto = $("#date_to").val();

	var transactions_chart = document.getElementById("transactions");

	if (transactions_chart) {
		var graph__transactions = new Chart(transactions_chart, {
			type: "bar",
			data: {
				labels: [],
				datasets: [
					{
						label: "Transaction Amount",
						borderColor: color_solid.green,
						backgroundColor: color_0pt6.green,
						borderWidth: 1,
						data: [],
					},
				],
			},
			options: chartOptions_line_bar,
		});

		// console.log("Declaration");
		// console.log(graph__transactions);
	}

	
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

	function fill_data(data) {
		if (transactions_chart) {
			var res = data;
			//var totalamount = 0;
			var trandate = [];

			if (res.length > 0) {
				trandate = res.map(function (obj) {
					return obj["trandate"];
				});
				data0 = res.map(function (obj) {
					return obj["totalamount"];
				});

				//totalamount    = res.map(function(obj) { return parseInt(obj['totalamount']); }).reduce(function(total,num) { return total+num; });
			} else {
				trandate = res.map(function (obj) {
					return obj["trandate"];
				});
				data0 = res.map(function (obj) {
					return obj["totalamount"];
				});
			}

			graph__transactions.data.labels = trandate;
			graph__transactions.data.datasets[0].data = data0;
			graph__transactions.update();

			// $('#paid_transactions').text(paid_value.toLocaleString());
			// $('#pending_transactions').text(pending_value.toLocaleString());
			// $('#expired_transactions').text(expired_value.toLocaleString());
			// $('#failed_transactions').text(failed_value.toLocaleString());

			//   console.log("Fill");
			// console.log(graph__transactions);
		}
	}

	
	var gen_chart_tbl = () => {
			return $("#table-grid").DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			searching: false,
			destroy: true,
			order: [[3, "asc"]],
			"columnDefs": [
				{ targets: [0], orderable: false },
				{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			ajax: {
				url: base_url + "reports/sales_report/salesreport_data",
				type: "post",
				data: filter,
				beforeSend: function () {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					// console.log(JSON.stringify(decodeURIComponent(this.data)));

					if (data.responseJSON.recordsTotal > 0) {
						$(".btnExport").show(100);
						$("#total_count").text(data.responseJSON.total_transaction);
						$("#total_paid").text(data.responseJSON.total_paid);
						$("#total_unpaid").text(data.responseJSON.total_unpaid);
						$("#total_transaction_amount").text(data.responseJSON.total_transaction_amount);
						$("#table-grid-top").show();
						$("#_search").val(JSON.stringify(this.data));
						$("input#_filters").val(JSON.stringify(filter));
						$("input#_data").val(
							JSON.stringify({
								total_transaction: data.responseJSON.total_transaction,
								total_paid: data.responseJSON.total_paid,
								total_unpaid: data.responseJSON.total_unpaid,
								total_transaction_amount:
									data.responseJSON.total_transaction_amount,
							})
						);
						data_toggle("show");
              			$('#chart_toggle').show();
					} else {
						data_toggle("hide");
                		$('#chart_toggle').hide();
						$("#btnExport").hide(100);
						$("#table-grid-top").hide();
					}
					first_run = true; 
				},
				error: function () {
					$.LoadingOverlay("hide");
				},
			}
		});	
	}	
	

	function chart() {
		$.ajax({
			type: "post",
			url: base_url + "reports/Sales_report/sales_report_chart_data",
			data: filter,
			success: function (data) {
				if (data.success == 1) {
					fill_data(data.chartdata);
					$(".btnSearch").prop("disabled", false);
					$(".btnSearch").text("Search");
					// var html = json_data.message;
					// $(".comdiv").html(html);
					// $('.overlay').css("display","none");
				} else {
					fill_data(data.chartdata);
					$(".btnSearch").prop("disabled", false);
					$(".btnSearch").text("Search");
					// var html = json_data.message;
					// $(".comdiv").html(html);
					// $('.overlay').css("display","none");
				}
			},
		});
	}

	// gen_sales_report_tbl(JSON.stringify(filter));
	chart();

	gen_chart_tbl();

	$(document).on("click", "#btnSearch", function () {
		filter.shopid = $("#select_shop").val() || "";
		filter.branchid = $('#select_branch').val() || '';
		filter.fromdate = $("#date_from").val();
		filter.todate = $("#date_to").val();
		filter.filtertype = $("#filtertype").val();
		filter.pmethodtype = $("#pmethodtype").val();
		chart();
		gen_chart_tbl();
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

	$("#search_clear_btn").click(function (e) {
		filter.shopid = "all";
		filter.branchid = "all";
		filter.filtertype = "all";
		filter.fromdate = initial_datefrom;
		filter.todate = initial_dateto;
		// $(".search-input-text").val("");
		$("#date_from").val(initial_datefrom);
		$("#date_to").val(initial_dateto);
		$('#select_shop option[value="all"]').prop("selected", true);
		$('#filtertype option[value="all"]').prop("selected", true);
		chart();
		gen_chart_tbl();
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
			showCpToast("info", "Note!", 'Error occured while trying get branches of selected shop.');
		  	 //$.toast({
			 //  heading: 'Note:',
			 //  text: "Error occured while trying get branches of selected shop.",
			 //  icon: 'info',
			 //  loader: false,   
			 //  stack: false,
			 //  position: 'top-center',  
			 //  bgColor: '#FFA500',
			 //  textColor: 'white',
			 //  allowToastClose: false,
			 //  hideAfter: 3000          
		  // });
		}
	  });      
	}
});
