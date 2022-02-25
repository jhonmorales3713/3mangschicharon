$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	
	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _code 			= $("input[name='_code']").val();
		var _delivery_area 	= $("input[name='_delivery_area']").val();
		var _date_from 		= $("input[name='_date_from']").val();
		var _date_to 		= $("input[name='_date_to']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			"columnDefs": [
				{ targets: 4, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				type: "post",
				url:base_url+"settings/delivery_areas/delivery_areas_table", // json datasource
				data:{
					'_record_status':_record_status, 
					'_code':_code, 
					'_delivery_area':_delivery_area, 
					'_date_from':_date_from, 
					'_date_to':_date_to, 
				}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table


	// start - for search purposes
	$('#btnSearch').click(function(e){
		e.preventDefault();
		fillDatatable();
	});

	$("#_record_status").change(function(){
		$("#btnSearch").click(); 
	});

	$("#search_clear_btn").click(function(e){
		$(".search-input-text").val("");
		fillDatatable();
	});
	// end - for search purposes


	// start - add function
	$('#add_area_form').submit(function(e){
		e.preventDefault();

		const form_data = $(this).serializeArray();
		if (validateInputs(form_data)) {
			$.ajax({
				type: "post",
				url: base_url + "settings/delivery_areas/create_data",
				data: form_data,
				success: function(data) {
					if (data.success == 1) {
						fillDatatable();
						//showToast('success', data.message);
						showCpToast("success", "Success!", data.message);
						setTimeout(function(){location.reload()}, 2000);
					}
					else {
						//showToast('note', data.message);
						showCpToast("info", "Note!", data.message);
					}

					$("#add_modal").modal('hide');
					document.getElementById("add_area_form").reset();
				},
				error: function(){
					//showToast('note', 'Something went wrong, Please Try again!');
					showCpToast("info", "Note!", 'Something went wrong, Please Try again!');
				}
			});
		}
		else {
			//showToast('note', 'Please fill-up all fields');
			showCpToast("info", "Note!", 'Please fill-up all fields');
		}
		
	});
	// end - add function

	// start - edit function
	let id;
	$('#table-grid').delegate(".action_edit", "click", function(){
		id = $(this).data('value');
		$.ajax({
			type:'post',
			url:base_url+'settings/delivery_areas/get_data',
			data:{'id':id},
			success:function(data){
				const res = data[0];
				$("input[name='_edit_code']").val(res.code);
				$("input[name='_edit_area']").val(res.name);
			}
		});
	});

	$('#edit_area_form').submit(function(e){
		e.preventDefault();
		const form_data = $(this).serializeArray();
		form_data.push({name: 'id', value: id});
		if (validateInputs(form_data)) {
			$.ajax({
				type: "post",
				url: base_url + "settings/delivery_areas/update_data",
				data: form_data,
				success: function(data) {
					if (data.success == 1) {
						fillDatatable();
						//showToast('success', data.message);
						showCpToast("success", "Success!", data.message);
						setTimeout(function(){location.reload()}, 2000);
					}
					else {
						//showToast('note', data.message);
						showCpToast("info", "Note!", data.message);
					}

					$("#edit_modal").modal('hide');
				},
				error: function(){
					//showToast('note', 'Something went wrong, Please Try again!');
					showCpToast("info", "Note!", 'Something went wrong, Please Try again!');
				}
			});
		}
		else {
			//showToast('note', 'Please fill-up all fields');
			showCpToast("info", "Note!", 'Please fill-up all fields');
		}
		
	});
	// end - edit function

	// start - disable function
	let disable_id;
	let record_status;
	$('#table-grid').delegate(".action_disable", "click", function(){
		disable_id	  = $(this).data('value');
		record_status = $(this).data('record_status');

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		}
		else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	$("#disable_modal_confirm_btn").click(function(e){
		e.preventDefault();
		$.ajax({
			type:'post',
			url:base_url+'settings/delivery_areas/disable_data',
			data:{'disable_id':disable_id, 'record_status':record_status},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable
					//showToast('success', data.message);
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					$('#disable_modal').modal('toggle'); //close modal
				}else{
					//showToast('note', data.message);
					showCpToast("info", "Note!", data.message);
				}
			}
		});
	});
	// end - disable function

	// start - delete function
	let delete_id = 0;
	$('#table-grid').delegate(".action_delete", "click", function(){
		delete_id = $(this).data('value');
	});

	$("#delete_modal_confirm_btn").click(function(e){
		e.preventDefault();
		$.ajax({
			type:'post',
			url:base_url+'settings/delivery_areas/delete_data',
			data:{'delete_id':delete_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable
					//showToast('success', data.message);
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					$('#delete_modal').modal('toggle'); //close modal
				}else{
					//showToast('note', data.message);
					showCpToast("info", "Note!", data.message);
				}
			}
		});
	});
	// end - delete function


	function validateInputs(data)
	{
		for (var i = 0; i < data.length; i++) {
			if (data[i].value == "") {
				return false;
			}
		}

		return true;
	}

	/*
	 * type: success / note
	 */
	// function showToast(type, message)
	// {
	// 	if (type == "success") {
	// 		$.toast({
	// 			heading: 'Success',
	// 			text: message,
	// 			icon: 'success',
	// 			loader: false,  
	// 			stack: false,
	// 			position: 'top-center', 
	// 			bgColor: '#5cb85c',
	// 			textColor: 'white',
	// 			allowToastClose: false,
	// 			hideAfter: 10000
	// 		});
	// 	}
	// 	else if (type == "note") {
	// 		$.toast({
	// 			heading: 'Note',
	// 			text: message,
	// 			icon: 'info',
	// 			loader: false,   
	// 			stack: false,
	// 			position: 'top-center',  
	// 			bgColor: '#FFA500',
	// 			textColor: 'white'        
	// 		});
	// 	}
	// }


	$("#search_hideshow_btn").click(function(e){
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if(!visibility){
			//visible
			$("#search_hideshow_btn").html('&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		}else{
			//not visible
			$("#search_hideshow_btn").html('Show Search <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
   		}

		$("#card-header_search").slideToggle("slow");
	});
});