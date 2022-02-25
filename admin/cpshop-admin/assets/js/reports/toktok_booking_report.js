$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shopid        = $("body").data("shop_id");
	var branchid      = $("body").data("branch_id");
	
	// start - for loading a table
	function fillDatatable() {
		var _record_status = $("select[name='_record_status']").val();
		var _name = $("input[name='_name']").val();
		var status = $("#select_status").val();
		var _shops = $("select[name='_shops']").val();
		var _branch = $("select[name='select_branch']").val();
		var date_from = $("#date_from").val();
		var date_to = $("#date_to").val();
		var location = $("#select_location").val();
		var address = $("#address").val();
		var regCode = $("#regCode").val();
		var provCode = $("#provCode").val();
		var citymunCode = $("#citymunCode").val();
		var drno = $("#drno").val();
		var order_status_view = $("#order_status_view").val();
		var _forpickup = $("#_forpickup").val();

		var dataTable = $("#table-grid-order").DataTable({
			processing: false,
			destroy: true,
			searching: false,
			serverSide: true,
			responsive: true,
			// order: [[ 0, "desc" ]],
			columnDefs: [
				{ targets: [7], orderable: false, sClass: "text-center" },
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: -1 },
			],
			ajax: {
				type: "post",
				url: base_url + "order_report/toktok_booking_report_table", // json datasource
				data: {
					_record_status: _record_status,
					_name: _name,
					status: status,
					_shops: _shops,
					_branch: _branch,
					date_from: date_from,
					date_to: date_to,
					location: location,
					address: address,
					regCode: regCode,
					provCode: provCode,
					citymunCode: citymunCode,
					drno: drno,
					order_status_view: order_status_view,
					forpickup: _forpickup
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
					$("#_record_status_export").val(_record_status);
					$("#_name_export").val(_name);
					$("#status_export").val(status);
					$("#_shops_export").val(_shops);
					$("#_branch_export").val(_branch);
					$("#date_from_export").val(date_from);
					$("#date_to_export").val(date_to);
					$("#location_export").val(location);
					$("#address_export").val(address);
					$("#regCode_export").val(regCode);
					$("#provCode_export").val(provCode);
					$("#citymunCode_export").val(citymunCode);
					$("#drno_export").val(drno);
					$("#forpickup_export").val(_forpickup);
					$("#request_filter").val(JSON.stringify(this.data));
				},
				error: function () {
					// error handling
					$(".table-grid-error").html("");
					$("#table-grid-order").append(
						'<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#table-grid_processing").css("display", "none");
				},
			},
		});
	}


	fillDatatable();
	if(branchid != 0 || shopid == 0){
		$('#select_branch').css('display','none');
	}   
	if(shopid != 0){
		$('#_shops').css('display','none');
		$('.shopdiv').css('display','none');
	}      
	getShopBranches();
	// end - for loading a table

	// start - for search purposes

	$("#_record_status").change(function () {
		$("#btnSearch").click();
	});

	$("#search_hideshow_btn").click(function (e) {
		e.preventDefault();

		var visibility = $("#card-header_search").is(":visible");

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html(
				'<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>'
			);
		} else {
			//not visible
			$("#search_hideshow_btn").html(
				'<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>'
			);
		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function (e) {
		todaydate = $('#todaydate').val();
		$('#date_from').val(todaydate);
		$('#date_to').val(todaydate);
		$(".search-input-text").val("");
		$("#drno").val("");
		status_index = (shopid !=0) ? 0:0;
		$("#select_status").prop("selectedIndex", status_index);
		$("#select_location").prop("selectedIndex", 0);
		$("#select_location").trigger("click");
		$("#address").val("");
		$("#regCode").prop("selectedIndex", 0);
		$("#provCode").prop("selectedIndex", 0);
		$("#citymunCode").prop("selectedIndex", 0);
		$("#_shops").prop("selectedIndex", 0);
		$("#_name").val("");
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


	$('#_shops').on('change',function(){
		console.log($('#_shops').children("option:selected").val());
		if($('#_shops').children("option:selected").val() == 0){
		  $('#select_branch').css('display','none');
		  $('#select_branch').html('<option value="">None</option>');
		}
		else{
		  $('#select_branch').css('display','inline-block');
		  $("#showBranches").show();
		  getShopBranches();
		}
		
	});

	function getShopBranches(){
		var $selected_shop = $('#_shops').children("option:selected").val();  
		if($selected_shop != ''){
			$.ajax({
				url: base_url + 'reports/Report_tools/getBranchOptions/'+$selected_shop,
				type: 'GET',
				dataType: 'JSON',
				beforeSend: function(){
					$.LoadingOverlay("show");     
				},
				success: function(data){
					if(data.options == null){
						$('#select_branch').html('<option value="">None</option>');
					}
					else{
						$('#select_branch').html(data.options);
					}                
					
					$.LoadingOverlay("hide");
				},
				error: function(){
					$.LoadingOverlay("hide");
				}
			});      
		}
	}
});

$('#date_to').datepicker().on('changeDate', (e) => {
	var todaydate = $('#todaydate').val();
	var new_start_date = moment(e.date).subtract(93, 'day').format('MM/DD/YYYY');

	$('#date_from').datepicker('setStartDate', new_start_date);
	$('#date_to').datepicker('setEndDate', todaydate);
});

$("#date_from").click(function (e) {
	var date_to = $('#date_to').val();
	var new_start_date = moment(date_to).subtract(93, 'day').format('MM/DD/YYYY');
	$('#date_from').datepicker('setStartDate', new_start_date);
});