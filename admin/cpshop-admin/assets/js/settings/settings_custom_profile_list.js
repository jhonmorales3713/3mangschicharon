$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var shop_url = $("body").data('shop_url');
	var token = $("body").data('token');
	// start - for loading a table
	function fillDatatable() {
		var _name = $("input[name='_name']").val();
		var shop_id_md5 = $("#shop_id_md5").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			searching: false,
			"serverSide": true,
			"columnDefs": [
				{ targets: 2, orderable: false, "sClass": "text-center" },
				{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			"ajax": {
				type: "post",
				url: base_url + "shipping_delivery/Settings_shipping_delivery/profile_table", // json datasource
				data: { '_name': _name, 'shop_id_md5': shop_id_md5 }, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function () {
					$.LoadingOverlay("hide");
				},
				error: function () {  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display", "none");
				}
			}
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

		var visibility = $('#card-header_search').is(':visible');

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		} else {
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function (e) {
		$(".search-input-text").val("");
		fillDatatable();
	})

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$('#btnSearch').click(function (e) {
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes


	$('#table-grid').delegate(".action_edit", "click", function () {
		let id = $(this).data('value');
	});

	let disable_id;
	let record_status;
	$('#table-grid').delegate(".action_disable", "click", function () {
		disable_id = $(this).data('value');
		record_status = $(this).data('record_status');

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		} else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	let delete_id;
	$('#table-grid').delegate(".action_delete", "click", function () {
		delete_id = $(this).data('value');
	});

	$("#delete_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'shipping_delivery/Settings_shipping_delivery/delete_custom_shipping',
			data: { 'delete_id': delete_id },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					// $.toast({
					// 	heading: 'Success',
					// 	text: data.message,
					// 	icon: 'success',
					// 	loader: false,
					// 	stack: false,
					// 	position: 'top-center',
					// 	bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 3000
					// });
					$('#delete_modal').modal('toggle'); //close modal
				} else {
					showCpToast("info", "Note!", data.message);
					// $.toast({
					// 	heading: 'Note',
					// 	text: data.message,
					// 	icon: 'info',
					// 	loader: false,
					// 	stack: false,
					// 	position: 'top-center',
					// 	bgColor: '#FFA500',
					// 	textColor: 'white'
					// });
				}
			}
		});
	});

	$("#disable_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'products/Main_products/disable_modal_confirm',
			data: { 'disable_id': disable_id, 'record_status': record_status },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					// $.toast({
					// 	heading: 'Success',
					// 	text: data.message,
					// 	icon: 'success',
					// 	loader: false,
					// 	stack: false,
					// 	position: 'top-center',
					// 	bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 10000
					// });
					$('#disable_modal').modal('toggle'); //close modal
				} else {
					showCpToast("info", "Note!", data.message);
					// $.toast({
					// 	heading: 'Note',
					// 	text: data.message,
					// 	icon: 'info',
					// 	loader: false,
					// 	stack: false,
					// 	position: 'top-center',
					// 	bgColor: '#FFA500',
					// 	textColor: 'white'
					// });
				}
			}
		});
	});

	$('#addBtn').click(function () {
		shop_id_md5 = $('#shop_id_md5').val();
		window.location.assign(base_url + "Settings_shipping_delivery/custom_rates/" + token + "/" + shop_id_md5 + "/0");
	})

});




