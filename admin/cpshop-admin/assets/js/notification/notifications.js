$(function () {
	var base_url 	   = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token 		   = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	var branch_id      = $("body").data("branch_id");
	var pusher_app_key = $("body").data("pusher_app_key");
	// start - for loading a table
	function fillDatatable() {
		var _name = $("input[name='_name']").val();
		var _shops = $("select[name='_shops']").val();
		var date_from = $("#date_from").val();
		var date_to = $("#date_to").val();

		var dataTable = $("#table-grid-notif").DataTable({
			processing: false,
			destroy: true,
			searching: false,
			serverSide: true,
			responsive: true,
			order: [[ 0, "desc" ]],
			columnDefs: [
				{ targets: [0, 1, 2, 3, 4, 5], orderable: false, sClass: "text-center" },
			],
			ajax: {
				type: "post",
				url: base_url + "notification/Notification/notifications_table", // json datasource
				data: {
					_name: _name,
					_shops: _shops,
					date_from: date_from,
					date_to: date_to
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
					// $("#_record_status_export").val(_record_status);
					// $("#_name_export").val(_name);
					// $("#status_export").val(status);
					// $("#_shops_export").val(_shops);
					// $("#date_from_export").val(date_from);
					// $("#date_to_export").val(date_to);
					// $("#location_export").val(location);
					// $("#address_export").val(address);
					// $("#regCode_export").val(regCode);
					// $("#provCode_export").val(provCode);
					// $("#citymunCode_export").val(citymunCode);
					// $("#drno_export").val(drno);
					// $("#forpickup_export").val(_forpickup);
					// $("#request_filter").val(JSON.stringify(this.data));
				},
				error: function () {
					// error handling
					$(".table-grid-error").html("");
					$("#table-grid-notif").append(
						'<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#table-grid_processing").css("display", "none");
				},
			},
		});
	}

	fillDatatable();
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
		$(".search-input-text").val("");
		$("#address").val("");
		$("#drno").val("");
		statusSelectedIndex = $("#select_status")[0].selectedIndex;
		$("#select_status").prop("selectedIndex", statusSelectedIndex);
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

	$("#table-grid-notif").delegate(".action_edit", "click", function () {
		let id = $(this).data("value");
	});

	let disable_id;
	let record_status;
	$("#table-grid-notif").delegate(".action_disable", "click", function () {
		disable_id = $(this).data("value");
		record_status = $(this).data("record_status");

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		} else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	let delete_id;
	$("#table-grid-notif").delegate(".action_delete", "click", function () {
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
					// $.toast({
					// 	heading: "Success",
					// 	text: data.message,
					// 	icon: "success",
					// 	loader: false,
					// 	stack: false,
					// 	position: "top-center",
					// 	bgColor: "#5cb85c",
					// 	textColor: "white",
					// 	allowToastClose: false,
					// 	hideAfter: 3000,
					// });
					$("#delete_modal").modal("toggle"); //close modal
				} else {
					// $.toast({
					// 	heading: "Note",
					// 	text: data.message,
					// 	icon: "info",
					// 	loader: false,
					// 	stack: false,
					// 	position: "top-center",
					// 	bgColor: "#FFA500",
					// 	textColor: "white",
					// });
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
					// $.toast({
					// 	heading: "Success",
					// 	text: data.message,
					// 	icon: "success",
					// 	loader: false,
					// 	stack: false,
					// 	position: "top-center",
					// 	bgColor: "#5cb85c",
					// 	textColor: "white",
					// 	allowToastClose: false,
					// 	hideAfter: 10000,
					// });
					$("#disable_modal").modal("toggle"); //close modal
				} else {
					// $.toast({
					// 	heading: "Note",
					// 	text: data.message,
					// 	icon: "info",
					// 	loader: false,
					// 	stack: false,
					// 	position: "top-center",
					// 	bgColor: "#FFA500",
					// 	textColor: "white",
					// });
					showCpToast("info", "Note!", data.message);
				}
			},
		});
	});

    $("#table-grid-notif").delegate("#openNotifBtn", "click", function (e) {
		notiflogs_id = $(this).data("notiflogs_id");
		resetNotifDetails();
        e.preventDefault();
		$.ajax({
			type: "post",
			url: base_url + "notification/Notification/get_notification_details",
			data: { notiflogs_id: notiflogs_id },
			success: function (data) {
				var res = data.result;
				if(data.success == 1){
					
                    $('#notif_activity_details').text(data.notif_activity_details);
                    $('#notif_message').text(data.notif_message);
					$("#notif_link").attr("href", data.notif_link)
					$('#notif_date_created').text(data.notif_date_created);
					(data.notif_count == 0) ? $('#notif_count').hide():$('#notif_count').show();
					$('#notif_count').text(data.notif_count)  
					$("#table-grid-notif").DataTable().draw(false);
				}
                else{
                    //sys_toast_warning('Error fetching');
                    showCpToast("warning", "Warning!", 'Error fetching');
				}
			},
		});
	});

	$(".btnCloseModal").click(function () {
		resetNotifDetails();
	});

	function resetNotifDetails(){
		$('#notif_activity_details').text("");
		$('#notif_message').text("");
		$("#notif_link").attr("href", "")
		$('#notif_date_created').text("");
	}
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