$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

	// start - for loading a table
	function fillDatatable() {
		var _record_status = $("select[name='_record_status']").val();
		var cron_name = $("input[name='cron_name']").val();
		var cron_desc = $("input[name='cron_desc']").val();
		// var _description 	= $("input[name='_description']").val();
		// var _main_nav 		= $("select[name='_main_nav']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			"columnDefs": [
				{ targets: 5, orderable: false, "sClass": "text-center" },
				{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			"ajax": {
				type: "post",
				url: base_url + "developer_settings/Main_dev_settings/cron_logs_table", // json datasource
				data: { '_record_status': _record_status, 'cron_name': cron_name, 'cron_desc': cron_desc }, // serialized dont work, idkw
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


	// $('#table-grid').delegate(".action_edit", "click", function(){
	// 	let id = $(this).data('value');

	// 	$.ajax({
	// 		type:'post',
	// 		url:base_url+'developer_settings/Main_dev_settings/get_content_navigation',
	// 		data:{'id':id},
	// 		success:function(data){
	// 			var data = JSON.parse(data);

	// 			$('#update_id').val(id);
	// 			$('#update_url').val(data.cn_url);
	// 			$('#update_name').val(data.cn_name);
	// 			$('#update_description').val(data.cn_description);
	// 			$('#update_main_nav_category').val(data.cn_fkey).change();
	// 		}
	// 	});


	// });

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

	// $("#delete_modal_confirm_btn").click(function(e){
	// 	e.preventDefault();
	// 	$.ajax({
	// 		type:'post',
	// 		url:base_url+'developer_settings/Main_dev_settings/delete_modal_confirm',
	// 		data:{'delete_id':delete_id},
	// 		success:function(data){
	// 			var res = data.result;
	// 			if (data.success == 1){
	// 				fillDatatable(); //refresh datatable

	// 				$.toast({
	// 				    heading: 'Success',
	// 				    text: data.message,
	// 				    icon: 'success',
	// 				    loader: false,  
	// 				    stack: false,
	// 				    position: 'top-center', 
	// 				    bgColor: '#5cb85c',
	// 					textColor: 'white',
	// 					allowToastClose: false,
	// 					hideAfter: 10000
	// 				});
	// 				$('#delete_modal').modal('toggle'); //close modal
	// 			}else{
	// 				$.toast({
	// 				    heading: 'Note',
	// 				    text: data.message,
	// 				    icon: 'info',
	// 				    loader: false,   
	// 				    stack: false,
	// 				    position: 'top-center',  
	// 				    bgColor: '#FFA500',
	// 					textColor: 'white'        
	// 				});
	// 			}
	// 		}
	// 	});
	// });

	$("#disable_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'developer_settings/Main_dev_settings/disable_modal_confirm_cron_logs',
			data: { 'disable_id': disable_id, 'record_status': record_status },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

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
					showCpToast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);
					$('#disable_modal').modal('toggle'); //close modal
				} else {
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
					showCpToast("info", "Note!", data.message);
				}
			}
		});
	});

	// $("#update_modal_confirm_btn").click(function(e){
	// 	e.preventDefault();

	// 	var id = $('#update_id').val();
	// 	var url = $('#update_url').val();
	// 	var name = $('#update_name').val();
	// 	var description = $('#update_description').val();
	// 	var category = $('#update_main_nav_category').val();

	// 	if(url == '' || name == '' || description == '' || category == ''){
	// 		$.toast({
	// 			heading: 'Note',
	// 			text: 'Something went wrong, Please Try again!',
	// 			icon: 'info',
	// 			loader: false,   
	// 			stack: false,
	// 			position: 'top-center',  
	// 			bgColor: '#FFA500',
	// 			textColor: 'white'        
	// 		});
	// 	}else{
	// 		$.ajax({
	// 			type:'post',
	// 			url:base_url+'developer_settings/Main_dev_settings/update_modal_confirm',
	// 			data:{'id':id, 'url':url, 'name':name, 'description':description, 'category':category},
	// 			success:function(data){
	// 				var res = data.result;
	// 				if (data.success == 1){
	// 					fillDatatable(); //refresh datatable

	// 					$.toast({
	// 						heading: 'Success',
	// 						text: data.message,
	// 						icon: 'success',
	// 						loader: false,  
	// 						stack: false,
	// 						position: 'top-center', 
	// 						bgColor: '#5cb85c',
	// 						textColor: 'white',
	// 						allowToastClose: false,
	// 						hideAfter: 10000
	// 					});
	// 					$('#edit_modal').modal('toggle'); //close modal
	// 				}else{
	// 					$.toast({
	// 						heading: 'Note',
	// 						text: data.message,
	// 						icon: 'info',
	// 						loader: false,   
	// 						stack: false,
	// 						position: 'top-center',  
	// 						bgColor: '#FFA500',
	// 						textColor: 'white'        
	// 					});
	// 				}
	// 			}
	// 		});
	// 	}
	// });

	// $("#add_modal_confirm_btn").click(function(e){
	// 	e.preventDefault();

	// 	var url = $('#add_url').val();
	// 	var name = $('#add_name').val();
	// 	var description = $('#add_description').val();
	// 	var category = $('#add_main_nav_category').val();

	// 	if(url == '' || name == '' || description == '' || category == ''){
	// 		$.toast({
	// 			heading: 'Note',
	// 			text: 'Something went wrong, Please Try again!',
	// 			icon: 'info',
	// 			loader: false,   
	// 			stack: false,
	// 			position: 'top-center',  
	// 			bgColor: '#FFA500',
	// 			textColor: 'white'        
	// 		});
	// 	}else{
	// 		$.ajax({
	// 			type:'post',
	// 			url:base_url+'developer_settings/Main_dev_settings/add_modal_confirm',
	// 			data:{'url':url, 'name':name, 'description':description, 'category':category},
	// 			success:function(data){
	// 				var res = data.result;
	// 				if (data.success == 1){
	// 					fillDatatable(); //refresh datatable

	// 					$.toast({
	// 						heading: 'Success',
	// 						text: data.message,
	// 						icon: 'success',
	// 						loader: false,  
	// 						stack: false,
	// 						position: 'top-center', 
	// 						bgColor: '#5cb85c',
	// 						textColor: 'white',
	// 						allowToastClose: false,
	// 						hideAfter: 10000
	// 					});

	// 					$('#add_url').val('');
	// 					$('#add_name').val('');
	// 					$('#add_description').val('');
	// 					$('#add_main_nav_category').val('').change();

	// 					$('#add_modal').modal('toggle'); //close modal
	// 				}else{
	// 					$.toast({
	// 						heading: 'Note',
	// 						text: data.message,
	// 						icon: 'info',
	// 						loader: false,   
	// 						stack: false,
	// 						position: 'top-center',  
	// 						bgColor: '#FFA500',
	// 						textColor: 'white'        
	// 					});
	// 				}
	// 			}
	// 		});
	// 	}
	// });
});




