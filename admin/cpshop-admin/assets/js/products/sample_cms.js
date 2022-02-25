$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	
	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _url 			= $("input[name='_url']").val();
		var _name 			= $("input[name='_name']").val();
		var _description 	= $("input[name='_description']").val();
		var _main_nav 		= $("select[name='_main_nav']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 4, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				type: "post",
				url:base_url+"developer_settings/Main_dev_settings/content_navigation_table", // json datasource
				data:{'_record_status':_record_status, '_url':_url, '_name':_name, '_description':_description, '_main_nav':_main_nav}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table

	// start - for search purposes

	$("#_record_status").change(function(){
		$("#btnSearch").click(); 
	});

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

	$("#search_clear_btn").click(function(e){
		$(".search-input-text").val("");
		fillDatatable();
	})

	$(".enter_search").keypress(function(e) { 
        if (e.keyCode === 13) { 
            $("#btnSearch").click(); 
        } 
    });

	$('#btnSearch').click(function(e){
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes

	
	$('#table-grid').delegate(".action_edit", "click", function(){
		let id = $(this).data('value');
	});

	let disable_id;
	let record_status;
	$('#table-grid').delegate(".action_disable", "click", function(){
		disable_id	  = $(this).data('value');
		record_status = $(this).data('record_status');

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		}else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	let delete_id;
	$('#table-grid').delegate(".action_delete", "click", function(){
		delete_id = $(this).data('value');
	});

	$("#delete_modal_confirm_btn").click(function(e){
		e.preventDefault();
		$.ajax({
			type:'post',
			url:base_url+'developer_settings/Main_dev_settings/delete_modal_confirm',
			data:{'delete_id':delete_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);
					// $.toast({
					//     heading: 'Success',
					//     text: data.message,
					//     icon: 'success',
					//     loader: false,  
					//     stack: false,
					//     position: 'top-center', 
					//     bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 10000
					// });
					$('#delete_modal').modal('toggle'); //close modal
				}else{
					showCpToast("info", "Note!", data.message);
					// $.toast({
					//     heading: 'Note',
					//     text: data.message,
					//     icon: 'info',
					//     loader: false,   
					//     stack: false,
					//     position: 'top-center',  
					//     bgColor: '#FFA500',
					// 	textColor: 'white'        
					// });
				}
			}
		});
	});
	
	$("#disable_modal_confirm_btn").click(function(e){
		e.preventDefault();
		$.ajax({
			type:'post',
			url:base_url+'developer_settings/Main_dev_settings/disable_modal_confirm',
			data:{'disable_id':disable_id, 'record_status':record_status},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);
					// $.toast({
					//     heading: 'Success',
					//     text: data.message,
					//     icon: 'success',
					//     loader: false,  
					//     stack: false,
					//     position: 'top-center', 
					//     bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 10000
					// });
					$('#disable_modal').modal('toggle'); //close modal
				}else{
					showCpToast("info", "Note!", data.message);
					// $.toast({
					//     heading: 'Note',
					//     text: data.message,
					//     icon: 'info',
					//     loader: false,   
					//     stack: false,
					//     position: 'top-center',  
					//     bgColor: '#FFA500',
					// 	textColor: 'white'        
					// });
				}
			}
		});
	});
	

	// $(".saveBtnArea").click(function(e){
	// 	e.preventDefault();

	// 	var thiss = $("#add_area-form");

	// 	var serial = thiss.serialize();

	// 	$.ajax({
	// 		type:'post',
	// 		url: base_url+'Main_settings/insert_area',
	// 		data: serial,
	// 		beforeSend:function(data){
	// 			$(".cancelBtn, .saveBtnArea").prop('disabled', true); 
	// 			$(".saveBtnArea").text("Please wait...");
	// 		},
	// 		success:function(data){
	// 			$(".cancelBtn, .saveBtnArea").prop('disabled', false);
	// 			$(".saveBtnArea").text("Add Area");
	// 			if (data.success == 1) {
	// 				fillDatatable(""); //refresh datatable
	// 				$(thiss).find('input').val(""); // clean fields

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
	// 				$('#addAreaModal').modal('toggle'); //close modal
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

	// $('#table-grid').delegate(".btnView", "click", function(){
	// 	$.LoadingOverlay("show");
	//   	var areaId = $(this).data('value');

	//   	$.ajax({
	//   		type: 'post',
	//   		url: base_url+'Main_settings/get_area',
	//   		data:{'areaId':areaId},
	//   		success:function(data){
	//   			var res = data.result;
	//   			if (data.success == 1) {
	//   				$(".info_areaId").val(areaId);
	//   				$(".info_desc").val(res[0].description);
	//   				if(res[0].mon == 'true')
	//   					$(".monday_check").prop("checked", true);
	//   				else
	//   					$(".monday_check").prop("checked", false);
	//   				if(res[0].tue == 'true')
	//   					$(".tuesday_check").prop("checked", true);
	//   				else
	//   					$(".tuesday_check").prop("checked", false);
	//   				if(res[0].wed == 'true')
	//   					$(".wednesday_check").prop("checked", true);
	//   				else
	//   					$(".wednesday_check").prop("checked", false);
	//   				if(res[0].thu == 'true')
	//   					$(".thursday_check").prop("checked", true);
	//   				else
	//   					$(".thursday_check").prop("checked", false);
	//   				if(res[0].fri == 'true')
	//   					$(".friday_check").prop("checked", true);
	//   				else
	//   					$(".friday_check").prop("checked", false);
	//   				if(res[0].sat == 'true')
	//   					$(".saturday_check").prop("checked", true);
	//   				else
	//   					$(".saturday_check").prop("checked", false);
	//   			}
	//   			else {
	//   				$(".info_desc").val('');
	// 			}
				
	// 			$.LoadingOverlay("hide");
	// 			$("#viewAreaModal").modal('show');
	//   		}
	//   	});
	// });

	// $("#update_area-form").submit(function(e){
	// 	e.preventDefault();
	// 	var serial = $(this).serialize();

	// 	$.ajax({
	// 		type:'post',
	// 		url:base_url+'Main_settings/update_area',
	// 		data:serial,
	// 		success:function(data){
	// 			if (data.success == 1) {
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
	// 					hideAfter: 4000
	// 				});
	// 				fillDatatable(""); //refresh table
	// 				$('#viewAreaModal').modal('toggle'); //close modal
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
	
	// $('#table-grid').delegate(".btnDelete","click", function(){
	// 	$.LoadingOverlay("show");
	// 	var areaId = $(this).data('value');

	// 	$.ajax({
	//   		type: 'post',
	//   		url: base_url+'Main_settings/get_area',
	//   		data:{'areaId':areaId},
	//   		success:function(data){
	//   			var res = data.result;
	//   			if (data.success == 1) {
 //  					$(".del_areaId").val(areaId);
	// 				$(".info_desc").text(res[0].description);
	// 			}
	// 			$.LoadingOverlay("hide");
	// 			$('#deleteAreaModal').modal('show');
	//   		}
	//   	});
	// });

	// $('.deleteAreaBtn').click(function(e){
	// 	e.preventDefault();
	// 	var del_areaId = $(".del_areaId").val();

	// 	$.ajax({
	// 		type:'post',
	// 		url:base_url+'Main_settings/delete_area',
	// 		data:{'del_areaId':del_areaId},
	// 		success:function(data){
	// 			if (data.success == 1) {
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
	// 					hideAfter: 4000
	// 				});
	// 				fillDatatable(""); //refresh table
	// 				$('#deleteAreaModal').modal('toggle'); //close modal
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
	
});




