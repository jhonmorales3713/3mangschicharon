$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	var checkBoxChecker   = 0;
	let addProdArr        = [];
	
	// start - for loading a table
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
			//"scrollX": true,
			order: [[ 0, "desc" ]],
			columnDefs: [
				{ targets: [8], orderable: false, sClass: "text-center" },
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: -1 },
			],
			ajax: { 
				type: "post",
				url: base_url + "orders/Main_orders/order_table", // json datasource
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

					if(_isconfirmed == 0){
						$("#check_all_items").show();
						$("#showButton").show();

					}
					else{
						$("#check_all_items").hide();
						$("#showButton").hide();
					}
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

	// fillDatatable();
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

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$("#btnSearch").click(function (e) {
		e.preventDefault();
		date_from = $('#date_from').val();
		date_to = $('#date_to').val();
		if(date_from == '' || date_to == ''){
			sys_toast_warning('Please input date.');
		}
		else{
			$('#tableDiv').show(100);
			fillDatatable();
		}
	});
	// end - for search purposes

	$('#table-grid-order').on( 'page.dt', function () {
		if($("#check_all_items").prop("checked") == true){
			$( "#check_all_items" ).prop( "checked", false );
			checkBoxChecker = 0;
	
		}
		else{
			checkBoxChecker = 3;
			
		}
	 });

	$("#check_all_items").click(function(){
	    //alert('test');
		if(checkBoxChecker == 0){
			$( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
			checkBoxChecker = 1;
		}
		else if(checkBoxChecker == 2){
			$( ".checkbox_perprod:checkbox:checked" ).trigger( "click" );
			checkBoxChecker = 0;
		}
		else if(checkBoxChecker == 3){
			$( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
			checkBoxChecker = 1;
			
		}
		else if(checkBoxChecker == 1){
			$( ".checkbox_perprod" ).trigger( "click" );
			checkBoxChecker = 0;
		}
	});

	$('#table-grid-order').on('click', "input[name='checkbox_perprod']", function() {
		// console.log('checkBoxChecker');
        var value = $(this).val();
        if(this.checked){
			dataArr = {
					'orderid'          : $(this).val()
				};
			addProdArr.push(dataArr);
        }
		else{
			var index = addProdArr.findIndex(p => p.orderid == $(this).val());
			if (index !== -1) {
				addProdArr.splice(index, 1);
			}
			if($("#check_all_items").prop("checked") == true){
				checkBoxChecker = 2;
			
			}
			else{
				checkBoxChecker = 3;
			
			}
        }

		
    });

    $(document).on('click', '#ConfirmedAll', function(e) {

    	console.log(addProdArr);

		if(addProdArr.length == 0){
			showCpToast("warning", "Warning!", 'Please select a order.');	
		}else{
			// alert('approve');
				bootbox.confirm({
					title: 'Approval',
					message: "Do you want to confirm the delivery of selected order?",
					buttons: {
						confirm: {
							label: 'Proceed',
							className: 'btn-success'
						},
						cancel: {
							label: 'Cancel',
							className: 'btn-danger'
						}
					},
					callback: function (result) {
						if(result == true){
							$.LoadingOverlay("show");
					
							var url = base_url+"orders/Main_orders/batch_delivery_confirmed_orders";
							$.post(url,{ order : addProdArr },function(rs){
						
					
								if(rs.success){
							    	$.LoadingOverlay("hide");
									$('#approveModal').modal('hide');
									// sys_toast_success(json_data.message);
									showCpToast("success", "Approved!", "Order changes has been confirmed.");
									setTimeout(function(){location.reload()}, 2000);
								}
								else{
									//sys_toast_warning(json_data.message);
									showCpToast("warning", "Warning!", json_data.message);
									$('#approveModal').modal('hide');
									// location.reload();
								}
					
							},'json');
						}
					}
				});
	

		}
			
    })


	$("#table-grid-order").delegate(".action_edit", "click", function () {
		let id = $(this).data("value");
	});

	let disable_id;
	let record_status;
	$("#table-grid-order").delegate(".action_disable", "click", function () {
		disable_id = $(this).data("value");
		record_status = $(this).data("record_status");

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		} else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	let delete_id;
	$("#table-grid-order").delegate(".action_delete", "click", function () {
		delete_id = $(this).data("value");
	});

	$("#delete_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: "post",
			url: base_url + "orders/Main_orders/delete_modal_confirm",
			data: { delete_id: delete_id },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					
					$("#delete_modal").modal("toggle"); //close modal
				} else {
					showCpToast("info", "Note!", data.message);
					
				}
			},
		});
	});

	$("#disable_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: "post",
			url: base_url + "orders/Main_orders/disable_modal_confirm",
			data: { disable_id: disable_id, record_status: record_status },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					
					$("#disable_modal").modal("toggle"); //close modal
				} else {
					showCpToast("info", "Note!", data.message);
					
				}
			},
		});
	});

	$("#select_location").click(function () {
		if ($(this).val() == "address") {
			$(".addressdiv").show(100);
			$(".regiondiv").hide(100);
			$(".provincediv").hide(100);
			$(".citymundiv").hide(100);
		} else if ($(this).val() == "region") {
			$(".addressdiv").hide(100);
			$(".regiondiv").show(100);
			$(".provincediv").hide(100);
			$(".citymundiv").hide(100);
		} else if ($(this).val() == "province") {
			$(".addressdiv").hide(100);
			$(".regiondiv").hide(100);
			$(".provincediv").show(100);
			$(".citymundiv").hide(100);
		} else if ($(this).val() == "citymun") {
			$(".addressdiv").hide(100);
			$(".regiondiv").hide(100);
			$(".provincediv").hide(100);
			$(".citymundiv").show(100);
		} else {
			$(".addressdiv").hide(100);
			$(".regiondiv").hide(100);
			$(".provincediv").hide(100);
			$(".citymundiv").hide(100);
		}
	});

	$("#addBtn").click(function () {
		window.location.assign(base_url + "Main_products/add_products/" + token);
	});
});

$('#date_to').datepicker().on('changeDate', (e) => {
	var todaydate = $('#todaydate').val();
	var new_start_date = moment(e.date).subtract(90, 'day').format('MM/DD/YYYY');

	$('#date_from').datepicker('setStartDate', new_start_date);
	$('#date_to').datepicker('setEndDate', todaydate);
});

$("#date_from").click(function (e) {
	var date_to = $('#date_to').val();
	var new_start_date = moment(date_to).subtract(90, 'day').format('MM/DD/YYYY');
	$('#date_from').datepicker('setStartDate', new_start_date);
});