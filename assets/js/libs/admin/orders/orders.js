$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	var orderstatus = 10;
	// start - for loading a table
	fillDatatable();
    var intervalId = window.setInterval(function(){
		/// call your function here
		fillDatatable();
	  }, 60000);
    // setTimeout(function(){fillDatatable()}, 2000);
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
	

    $(document).delegate('.status-select','click',function(e){
		target = $(this).data('target')
		orderstatus = $(this).data('status');
		console.log(orderstatus);
		$(".status-select").removeClass('bg-success text-white');
		$("."+target).addClass('bg-success text-white');
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
		var status =orderstatus;
		var date = $("#select_date").val();
		// var order_status = orderstatus;
		// console.log(orderstatus);
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
				// { targets: [10], visible: false, sClass: "text-center" },
				// { responsivePriority: 1, targets: 0 },
				// { responsivePriority: 2, targets: -1 },
			],createdRow: function( row, data, dataIndex ) {

				// var data2 = $('#table-grid-order').DataTable().row(row).data();
				// // console.log()
				// if(data[10] == "1"){
				// 	response.order_status.pending++
				// }
				// if(data[10] == "2"){
				// 	response.order_status.processing++
				// }
				// if(data[10] == "3"){
				// 	response.order_status.fordelivery++
				// }
				// if(data[10] == "4"){
				// 	response.order_status.shipped++
				// }
				// if(data[10] == "5"){
				// 	response.order_status.delivered++
				// }
				// if(data[10] == "6"||data[10] == "7"){
				// 	response.order_status.failed++
				// }

				// if(data[10] == "9"||data[10] == "8"){
				// 	response.order_status.cancelled++
				// }

			},
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
					$("#_record_status_export").val(JSON.stringify(this.data));
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
					// console.log(response);
					$(".all-status").html(response.recordsTotal);
					$(".pending-status").html(response.order_status.pending);
					$(".processing-status").html(response.order_status.processing);
					$(".fulfilled-status").html(response.order_status.fordelivery);
					$(".shipped-status").html(response.order_status.shipped);
					$(".delivered-status").html(response.order_status.delivered);
					$(".cancelled-status").html(response.order_status.cancelled);
					$(".failed-status").html(response.order_status.failed);
					// $(".all-status").html(response.recordsTotal);
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