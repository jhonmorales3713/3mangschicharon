$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');
	// start - for loading a table
	function fillDatatable() {
		var _record_status = $("select[name='_record_status']").val();
		var ticket_refno = $("input[name='ticket_refno']").val();
		var date_from = $("#date_from").val();
		var date_to = $("#date_to").val();

		var filters = {
			_record_status : $("select[name='_record_status']").val(),
			ticket_refno : $("input[name='ticket_refno']").val(),
			date_from : $("#date_from").val(),
			date_to : $("#date_to").val()
		}

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			"columnDefs": [ //DataTable
				{ targets: 1, orderable: true },
				{ targets: 2, orderable: true },
				{ targets: 3, orderable: true },
				{ targets: 4, orderable: true },
				{ targets: 5, orderable: false },
			],
			"ajax": {
				type: "post",
				url: base_url + "Csr/ticket_list", // json datasource
				data: { 'date_from': date_from, 'date`_to': date_to, 'ticket_refno': ticket_refno, '_record_status': _record_status }, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");					
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);

					if(response.data.length > 0){
						$('.btnExport').show(100);
					}
					else{
						$('#btnExport').hide(100);
					}
					// console.log(JSON.stringify(decodeURIComponent(this.data)));
					$("input#_search").val(JSON.stringify(this.data));
					$("input#_filters").val(JSON.stringify(filters));
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
		$.LoadingOverlay("show");
		window.location.href = base_url + "Csr/ticket_log/" + id + "/" + token;
		$.LoadingOverlay("hide");
	});

	let disable_id;
	let record_status;
	$('#table-grid').delegate(".action_disable", "click", function () {
		disable_id = $(this).data('value');
		record_status = $(this).data('record_status');

		if (record_status == 1) {
			$(".mtext_record_status").text("archive");
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
			url: base_url + 'Csr/deactivate',
			data: { 'delete_id': delete_id },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					// $.toast({
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
					$('#delete_modal').modal('toggle'); //close modal
				} else {

					showCpToast("info", "Info!", data.message);

					// $.toast({
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
			url: base_url + 'Csr/disable',
			data: { 'disable_id': disable_id, 'record_status': record_status },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					// $.toast({
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

					showCpToast("info", "Info!", data.message);

					// $.toast({
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

	$('.action_ticket').click(function (e) {
		let id = $(this).data('value');
		$.LoadingOverlay("show");
		window.location.href = base_url + "Csr/ticket_log/" + id + "/" + token;
		$.LoadingOverlay("hide");
	});

	$('#table-grid').delegate(".action_approve", "click", function () {
		approve_id = $(this).data('value');
	});

	$('#table-grid').delegate(".action_reject", "click", function () {
		reject_id = $(this).data('value');
	});

	$("#approve_modal_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'Csr/approve_ticket',
			data: { 'approve_id': approve_id },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					// $.toast({
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
					$('#approve_modal').modal('toggle'); //close modal
				} else {

					showCpToast("info", "Info!", data.message);

					// $.toast({
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

	$("#reject_modal_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'Csr/reject_ticket',
			data: { 'reject_id': reject_id },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					// $.toast({
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
					$('#reject_modal').modal('toggle'); //close modal
				} else {
					showCpToast("info", "Info!", data.message);
					// $.toast({
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

});