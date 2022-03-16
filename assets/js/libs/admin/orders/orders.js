$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	
	// start - for loading a table
	fillDatatable();
    
	$("#search_clear_btn").click(function (e) {
		todaydate = $('#todaydate').val();
		$('#date_from').val(todaydate);
		$('#date_to').val(todaydate);
		$(".search-input-text").val("");
		$("#drno").val("");
		status_index = (shop_id !=0) ? 0:1;
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
	$("#btnSearch").click(function (e) {
		e.preventDefault();
		date_from = $('#date_from').val();
		date_to = $('#date_to').val();
		if(date_from == '' || date_to == ''){
			//sys_toast_warning('Please input date.');
			showCpToast("warning", "Warning!", 'Please input date.');
		}
		else{
			$('#tableDiv').show(100);
			fillDatatable();
		}
	});
	function fillDatatable() {
		var _record_status = $("select[name='_record_status']").val();
		var _name = $("input[name='_name']").val();
		var status = $("#select_status").val();
		var date = $("#select_date").val();
		var _shops = $("select[name='_shops']").val();
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
		var _isconfirmed = $("#_isconfirmed").val();

		// console.log(date);

		var dataTable = $("#table-grid-order").DataTable({
			processing: false,
			destroy: true,
			searching: false,
			serverSide: true,
			responsive: true,
			order: [[ 0, "desc" ]],
			columnDefs: [
				{ targets: [6, 7], orderable: false, sClass: "text-center" },
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: -1 },
			],
			ajax: {
				type: "post",
				url: base_url + "admin/Main_orders/order_table", // json datasource
				data: {
					_record_status: _record_status,
					_name: _name,
					status: status,
					date: date,
					_shops: _shops,
					date_from: date_from,
					date_to: date_to,
					location: location,
					address: address,
					regCode: regCode,
					provCode: provCode,
					citymunCode: citymunCode,
					drno: drno,
					order_status_view: order_status_view,
					forpickup: _forpickup,
					isconfirmed: _isconfirmed
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
					$("#date_export").val(date);
					$("#_shops_export").val(_shops);
					$("#date_from_export").val(date_from);
					$("#date_to_export").val(date_to);
					$("#location_export").val(location);
					$("#address_export").val(address);
					$("#regCode_export").val(regCode);
					$("#provCode_export").val(provCode);
					$("#citymunCode_export").val(citymunCode);
					$("#drno_export").val(drno);
					$("#forpickup_export").val(_forpickup);
					$("#isconfirmed_export").val(_isconfirmed);
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
});