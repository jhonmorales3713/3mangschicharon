$(function () {
	//hides data container
	data_toggle("hide");
	//false on first run
	var first_run = false;

	var base_url = $("body").data("base_url");
	var token = $("#token").val();
	var filter = {
		member_id: $("#member_id").val(),
		fromdate: $("#date_from").val(),
		todate: $("#date_to").val(),
	};

	function chart() {
		$.ajax({
			type: "post",
			url: base_url + "reports/Payout_report/payout_report_data",
			data: filter,
			success: function (data) {
				if (data.success == 1) {
					fill_data(data.chartdata, member_id);
					$(".btnSearch").prop("disabled", false);
					$(".btnSearch").text("Search");
					// var html = json_data.showtable;
					// $(".comdiv").html(html);
					// $('.overlay').css("display","none");
				} else {
					fill_data(data.chartdata);
					$(".btnSearch").prop("disabled", false);
					$(".btnSearch").text("Search");
					// var html = json_data.showtable;
					// $(".comdiv").html(html);
					// $('.overlay').css("display","none");
				}
			},
		});
	}

	function gen_chart_tbl() {
		var gen_chart_tbl = $("#table-grid").DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			searching: false,
			destroy: true,
			order: [[0, "asc"]],
			ajax: {
				url: base_url + "reports/Payout_report/list_table",
				type: "post",
				data: filter,
				beforeSend: function () {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);
					if (response.recordsTotal > 0) {
						$(".btnExport").show(100);
						data_toggle("show");
                		$('#chart_toggle').show();
					} else {
						data_toggle("hide");
                		$('#chart_toggle').hide();
						$(".btnExport").hide(100);
					}

					$("#member_id_export").val(filter.member_id);
					$("#date_from_export").val(filter.fromdate);
					$("#date_to_export").val(filter.todate);
					$("input#_search").val(JSON.stringify(response.filters));
				},
				error: function () {
					$.LoadingOverlay("hide");
				},
			},
		});
	}

	chart();
	gen_chart_tbl();

	document.getElementById("reprange").onchange = function () {
		var reprange = $("#reprange").val();
		var member_id = $("#member_id").val();
		var todaydate = $("#todaydate").val();

		if (reprange == "custom") {
			document.getElementById("date_from").disabled = false;
			document.getElementById("date_to").disabled = false;
		} else {
			if (reprange == "today") {
				$("#date_from").datepicker().datepicker("setDate", todaydate);
				$("#date_to").datepicker().datepicker("setDate", todaydate);

				document.getElementById("date_from").disabled = true;
				document.getElementById("date_to").disabled = true;
			} else if (reprange == "yesterday") {
				var yesterday = moment().add(-1, "day").toDate(todaydate);

				$("#date_from").datepicker().datepicker("setDate", yesterday);
				$("#date_to").datepicker().datepicker("setDate", yesterday);

				document.getElementById("date_from").disabled = true;
				document.getElementById("date_to").disabled = true;
			} else if (reprange == "last_7") {
				var day7 = moment().add(-6, "day").toDate(todaydate);

				$("#date_from").datepicker().datepicker("setDate", day7);
				$("#date_to").datepicker().datepicker("setDate", todaydate);

				document.getElementById("date_from").disabled = true;
				document.getElementById("date_to").disabled = true;
			} else if (reprange == "last_30") {
				var day30 = moment().add(-30, "day").toDate(todaydate);

				$("#date_from").datepicker().datepicker("setDate", day30);
				$("#date_to").datepicker().datepicker("setDate", todaydate);

				document.getElementById("date_from").disabled = true;
				document.getElementById("date_to").disabled = true;
			} else if (reprange == "last_90") {
				var day90 = moment().add(-90, "day").toDate(todaydate);

				$("#date_from").datepicker().datepicker("setDate", day90);
				$("#date_to").datepicker().datepicker("setDate", todaydate);

				document.getElementById("date_from").disabled = true;
				document.getElementById("date_to").disabled = true;
			} else {
				$("#date_from").datepicker().datepicker("setDate", todaydate);
				$("#date_to").datepicker().datepicker("setDate", todaydate);

				document.getElementById("date_from").disabled = true;
				document.getElementById("date_to").disabled = true;
			}

			var fromdate = $("#date_from").val();
			var todate = $("#date_to").val();
		}
	};

	//mobile date to - temporary added because of layout problem (styles classes bootstrap used in views)
    //it will be removed soon once layout problem is fixed
    $('#date_to_m').on('change',function(){
		$('#date_to').val($(this).val());
	});

	$(document).on("click", "#btnSearch", function () {
		filter.member_id = $("#member_id").val();
		filter.fromdate = $("#date_from").val();
		filter.todate = $("#date_to").val();
		chart();
		gen_chart_tbl();
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
  
		  var visibility = $('#payoutChart').is(':visible');
  
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
		  $("#payoutChart").slideToggle("slow");
	  });

	$("#search_clear_btn").click(function (e) {
		filter.member_id = $("#member_id").val();
		filter.fromdate = $("#todaydate").val();
		filter.todate = $("#todaydate").val();
		// $(".search-input-text").val("");
		$("#date_from").val($("#todaydate").val());
		$("#date_to").val($("#todaydate").val());
		$('#reprange option[value="today"]')
			.prop("selected", true)
			.trigger("change");
		chart();
		gen_chart_tbl();
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

	// Charts --------------------

	var transactions_chart = document.getElementById("transactions");

	if (transactions_chart) {
		var graph__transactions_chart = new Chart(transactions_chart, {
			type: "bar",
			data: {
				labels: [],
				datasets: [
					{
						label: "Net Amount",
						borderColor: color_solid.green,
						backgroundColor: color_0pt6.green,
						borderWidth: 1,
						data: [],
					},
				],
			},
			options: chartOptions_line_bar,
		});
	}

	function fill_data(data, member_id) {
		if (transactions_chart) {
			var res = data.profit_share;
			var trandate = [];
			//var totalamount=0;
			var totalnet = 0;

			if (res) {
				if (res.length > 0) {
					trandate = res.map(function (obj) {
						return obj["trandate"];
					});
					//data0 = res.map(function(obj) { return obj['totalamount']; });
					data0 = res.map(function (obj) {
						return obj["netamount"];
					});
					//totalamount    = res.map(function(obj) { return parseFloat(obj['totalamount']); }).reduce(function(total,num) { return total+num; });
					totalnet = res
						.map(function (obj) {
							return parseFloat(obj["netamount"]);
						})
						.reduce(function (total, num) {
							return total + num;
						});
				} else {
					trandate = res.map(function (obj) {
						return obj["trandate"];
					});
					//data0 = res.map(function(obj) { return obj['totalamount']; });
					data0 = res.map(function (obj) {
						return obj["totalnet"];
					});
				}
			}
			graph__transactions_chart.data.labels = trandate;
			graph__transactions_chart.data.datasets[0].data = data0;
			graph__transactions_chart.update();

			//$('#total_profit').text(totalamount.toLocaleString());
			$("#total_profit_net").text(totalnet.toLocaleString());
		}
	}
});
