$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shopid        = $("body").data("shop_id");
	var branchid      = $("body").data("branch_id");
	
	// start - for loading a table
	function fillDatatable() {

		var _order_ref = $("input[name='_order_ref']").val();
		var _payment_ref = $("input[name='_payment_ref']").val();
		var _bill_code = $("input[name='_bill_code']").val();
		var _shops = $("select[name='_shops']").val();
		var _branch = $("select[name='select_branch']").val();
		var date_from = $("#date_from").val();
		// var date_to = $("#date_to").val();

		var dataTable = $("#table-grid-order").DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			// order: [[ 0, "desc" ]],
			columnDefs: [
				// { targets: [7], orderable: false, sClass: "text-center" },
				// { responsivePriority: 1, targets: 0 },
				// { responsivePriority: 2, targets: -1 },
			],
			ajax: {
				type: "post",
				url: base_url + "order_report/order_list_payout_status_report_table", // json datasource
				data: {
					_order_ref:_order_ref,
					_payment_ref:_payment_ref,
					_bill_code:_bill_code,
					_shops: _shops,
					_branch: _branch,
					date_from: date_from,
					// date_to: date_to,
				}, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);
					if (response.recordsTotal > 0) {
						$(".btnExport").show(100);
					} else {
						$("#btnExport").hide(100);
					}
					$("#_order_ref_export").val(_order_ref);
					$("#_payment_ref_export").val(_payment_ref);
					$("#_bill_code_export").val(_bill_code);
					$("#_shops_export").val(_shops);
					$("#_branch_export").val(_branch);
					$("#date_from_export").val(date_from);
					// $("#date_to_export").val(date_to);
				},
				error: function () {
					$(".table-grid-error").html("");
					$("#table-grid-order tbody").remove();
					$("#table-grid-order").append(
						'<tbody class="table-grid-error"><tr><th colspan="11" style="text-align: center;P">No data found in the server</th></tr></tbody>'
					);
					$("#table-grid_processing").css("display", "none");
				},
			},
		});
	}

	fillDatatable();
	// if(branchid != 0 || shopid == 0){
	// 	$('#select_branch').css('display','none');
	// }   
	// if(shopid != 0){
	// 	$('#_shops').css('display','none');
	// 	$('.shopdiv').css('display','none');
	// }      
	// getShopBranches();
	// end - for loading a table

	// start - for search purposes

	$("#_record_status").change(function () {
		$("#btnSearch").click();
	});

	// $("#search_hideshow_btn").click(function (e) {
	// 	e.preventDefault();

	// 	var visibility = $("#card-header_search").is(":visible");

	// 	if (!visibility) {
	// 		//visible
	// 		$("#search_hideshow_btn").html(
	// 			'<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>'
	// 		);
	// 	} else {
	// 		//not visible
	// 		$("#search_hideshow_btn").html(
	// 			'<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>'
	// 		);
	// 	}

	// 	$("#card-header_search").slideToggle("slow");
	// });

	$("#search_clear_btn").click(function (e) {
		todaydate = $('#todaydate').val();
		$('#date_from').val(todaydate);
		$('#date_to').val(todaydate);
		$(".search-input-text").val("");
		$("#_order_ref").val("");
		$("#_payment_ref").val("");
		fillDatatable();
	});

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$("#btnSearch").click(function (e) {
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes


	// $('#_shops').on('change',function(){
	// 	if($('#_shops').children("option:selected").val() == ""){
	// 	  $('#select_branch').css('display','none');
	// 	  $('#select_branch').html('<option value="">None</option>');
	// 	}
	// 	else{
	// 	  $('#select_branch').css('display','inline-block');
	// 	  getShopBranches();
	// 	}
		
	// });

	// function getShopBranches(){
	// 	var $selected_shop = $('#_shops').children("option:selected").val();  
	// 	if($selected_shop != ''){
	// 		$.ajax({
	// 			url: base_url + 'reports/Report_tools/getBranchOptions/'+$selected_shop,
	// 			type: 'GET',
	// 			dataType: 'JSON',
	// 			beforeSend: function(){
	// 				$.LoadingOverlay("show");     
	// 			},
	// 			success: function(data){
	// 				if(data.options == null){
	// 					$('#select_branch').html('<option value="">None</option>');
	// 				}
	// 				else{
	// 					$('#select_branch').html(data.options);
	// 				}                
					
	// 				$.LoadingOverlay("hide");
	// 			},
	// 			error: function(){
	// 				$.LoadingOverlay("hide");
	// 			}
	// 		});      
	// 	}
	// }
});

// $('#date_to').datepicker().on('changeDate', (e) => {
// 	var todaydate = $('#todaydate').val();
// 	var new_start_date = moment(e.date).subtract(93, 'day').format('MM/DD/YYYY');

// 	$('#date_from').datepicker('setStartDate', new_start_date);
// 	$('#date_to').datepicker('setEndDate', todaydate);
// });

// $("#date_from").click(function (e) {
// 	var date_to = $('#date_to').val();
// 	var new_start_date = moment(date_to).subtract(93, 'day').format('MM/DD/YYYY');
// 	$('#date_from').datepicker('setStartDate', new_start_date);
// });