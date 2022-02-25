$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

	// start - for loading a table
	function fillDatatable() {
		var _record_status = $("select[name='_record_status']").val();
		var _code = $("input[name='_code']").val();
		var _name = $("input[name='_name']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			"columnDefs": [
				{ targets: 3, orderable: false, "sClass": "text-center" },
				{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			"ajax": {
				type: "post",
				url: base_url + "Main_settings/shipping_partner_list", // json datasource
				data: { '_record_status': _record_status, 'code': _code, 'name': _name }, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (res) {
					var filter = { '_record_status': _record_status, 'code': _code, 'name': _name };
					$.LoadingOverlay("hide"); 
					$('#_search').val(JSON.stringify(this.data));
					$('#_filter').val(JSON.stringify(filter));
					if (res.responseJSON.data.length > 0) {
						$('#btnExport').show();
					}else{
						$('#btnExport').hide();
					}
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
			$("#search_hideshow_btn").html('&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		} else {
			//not visible
			$("#search_hideshow_btn").html('Show Search <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
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

	let disable_id;
	let record_status;
	let record_name;
	$('#table-grid').delegate(".action_disable", "click", function () {
		disable_id = $(this).data('value');
		record_status = $(this).data('record_status');
		record_name = $(this).data('record_name');

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		} else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	let delete_id;
	$('#table-grid').delegate(".action_delete", "click", function () {
		delete_id = $(this).data('value');
		record_name = $(this).data('record_name');
	});

	$("#delete_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'Main_settings/shipping_partner_delete_modal_confirm',
			data: { 'delete_id': delete_id , 'record_name' : record_name },
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
			url: base_url + 'Main_settings/shipping_partner_disable_modal_confirm',
			data: { 'disable_id': disable_id, 'record_status': record_status , 'record_name': record_name},
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

	var prev_edit = {
		'id': null, 'code': null, 'name': null, 'api_isset': null, 'dev_api_url': null, 'test_api_url': null, 'prod_api_url': null
	};
	$('#table-grid').delegate(".action_edit", "click", function () {
		edit_id = $(this).data('value');

		$.ajax({
			type: 'post',
			url: base_url + 'Main_settings/get_shipping_partner_data',
			data: { 'edit_id': edit_id },
			success: function (data) {
				var result = data.result;
				if (data.success == 1) {
					prev_edit = {
						'id' : result['id'],
						'code' : result['shipping_code'],
						'name' : result['name'],
						'api_isset' : result['api_isset'],
						'dev_api_url' : result['dev_api_url'],
						'test_api_url' : result['test_api_url'],
						'prod_api_url' : result['prod_api_url']
					};
					$("#edit_id").val(result['id']);
					$("#edit_code").val(result['shipping_code']);
					$("#edit_name").val(result['name']);
					result['api_isset'] == 1 ? $( "#edit_api_isset" ).prop( "checked", true ) : $( "#edit_api_isset" ).prop( "checked", false );
					result['api_isset'] == 1 ? $('.edit_apilink_div').show(100) : $('.edit_apilink_div').hide(100);
					$("#edit_dev_api_url").val(result['dev_api_url']);
					$("#edit_test_api_url").val(result['test_api_url']);
					$("#edit_prod_api_url").val(result['prod_api_url']);

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

	$("#update_modal_confirm_btn").click(function (e) {
		e.preventDefault();

		var _id          = $('#edit_id').val();
		var _code        = $('#edit_code').val();
		var _name        = $('#edit_name').val();
		var api_isset    = $('#edit_api_isset:checked').val();
		var dev_api_url  = $('#edit_dev_api_url').val();
		var test_api_url = $('#edit_test_api_url').val();
		var prod_api_url = $('#edit_prod_api_url').val();
		api_isset        = (api_isset != null) ? api_isset : 0;

		if (_id != '' && _code != '' && _name != '') {
			if (_code.length <= 5) {
				$.ajax({
					type: 'post',
					url: base_url + 'Main_settings/shipping_partner_update_modal_confirm',
					data: { cur_val : {
						'id'          : _id, 
						'code'        : _code, 
						'name'        : _name, 
						'api_isset'   : api_isset, 
						'dev_api_url' : dev_api_url, 
						'test_api_url': test_api_url, 
						'prod_api_url': prod_api_url 
					}, 
						prev_val : prev_edit
					},
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

							$('#edit_id').val('');
							$('#edit_code').val('');
							$('#edit_name').val('');

							$('#edit_modal').modal('toggle'); //close modal
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
			} else {
				showCpToast("info", "Note!", "Shipping code is more than 5 characters, please input valid Shipping Code");
				// $.toast({
				// 	heading: 'Note',
				// 	text: 'Shipping code is more than 5 characters, please input valid Shipping Code',
				// 	icon: 'info',
				// 	loader: false,
				// 	stack: false,
				// 	position: 'top-center',
				// 	bgColor: '#FFA500',
				// 	textColor: 'white'
				// });
			}
		} else {
			showCpToast("info", "Note!", "Please fill up all required fields");
			// $.toast({
			// 	heading: 'Note',
			// 	text: 'Please fill up all required fields',
			// 	icon: 'info',
			// 	loader: false,
			// 	stack: false,
			// 	position: 'top-center',
			// 	bgColor: '#FFA500',
			// 	textColor: 'white'
			// });
		}
	});

	$("#add_modal_confirm_btn").click(function (e) {
		e.preventDefault();

		var _code        = $('#add_code').val();
		var _name        = $('#add_name').val();
		var api_isset    = $('#add_api_isset:checked').val();
		var dev_api_url  = $('#add_dev_api_url').val();
		var test_api_url = $('#add_test_api_url').val();
		var prod_api_url = $('#add_prod_api_url').val();
		api_isset        = (api_isset != null) ? api_isset : 0;

		if (_code != '' && _name != '') {
			if (_code.length <= 5) {
				$.ajax({
					type: 'post',
					url: base_url + 'Main_settings/shipping_partner_add_modal_confirm',
					data: { 
						'code'        : _code, 
						'name'        : _name, 
						'api_isset'   : api_isset, 
						'dev_api_url' : dev_api_url,
						'test_api_url': test_api_url,
						'prod_api_url': prod_api_url
					},
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

							$('#add_code').val('');
							$('#add_name').val('');

							$('#add_modal').modal('toggle'); //close modal
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
			} else {
				showCpToast("info", "Note!", "Shipping code is more than 5 characters, please input valid Shipping Code");
				// $.toast({
				// 	heading: 'Note',
				// 	text: 'Shipping code is more than 5 characters, please input valid Shipping Code',
				// 	icon: 'info',
				// 	loader: false,
				// 	stack: false,
				// 	position: 'top-center',
				// 	bgColor: '#FFA500',
				// 	textColor: 'white'
				// });
			}
		} else {
			showCpToast("info", "Note!", "Please fill up all required fields");
			// $.toast({
			// 	heading: 'Note',
			// 	text: 'Please fill up all required fields',
			// 	icon: 'info',
			// 	loader: false,
			// 	stack: false,
			// 	position: 'top-center',
			// 	bgColor: '#FFA500',
			// 	textColor: 'white'
			// });
		}
	});

	$("#add_api_isset").change(function(){
		api_isset = $("#add_api_isset:checked").val();
		
		if(api_isset == 1){
			$('.add_apilink_div').show(100);
		}else{
			$('.add_apilink_div').hide(100);
		}
	});
	
	$("#edit_api_isset").change(function(){
		api_isset = $("#edit_api_isset:checked").val();
		
		if(api_isset == 1){
			$('.edit_apilink_div').show(100);
		}else{
			$('.edit_apilink_div').hide(100);
		}
    });
});




