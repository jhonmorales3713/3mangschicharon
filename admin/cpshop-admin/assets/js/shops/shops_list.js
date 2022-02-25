$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');
	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _shopname		= $("input[name='_shopname']").val();
		var _address		= $("input[name='_address']").val();
		var _city		= $("select[name='_city']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"searching": false,
			responsive: true,
			"columnDefs": [
				{ targets: 5, orderable: false, "sClass":"text-center"},
				{ responsivePriority: 1, targets: 0 },
        		{ responsivePriority: 2, targets: -1 }
			],
			"ajax":{
				type: "post",
				url:base_url+"Shops/profile_list", // json datasource
				data:{'_record_status':_record_status, '_shopname':_shopname, '_address':_address, '_city':_city}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(res) {
					var filter = {'_record_status':_record_status, '_shopname':_shopname, '_address':_address, '_city':_city};
					$.LoadingOverlay("hide"); 
					$('#_search').val(JSON.stringify(this.data));
					$('#_filter').val(JSON.stringify(filter));
					if (res.responseJSON.data.length > 0) {
						$('#btnExport').show();
					}else{
						$('#btnExport').hide();
					}
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
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		}else{
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
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
		$.LoadingOverlay("show");
	  	window.location.href=base_url+"Shops/update/"+ id + "/" + token;
	  	$.LoadingOverlay("hide");
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
			url:base_url+'Shops/delete_shop',
			data:{'delete_id':delete_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					// $.toast({
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
			url:base_url+'Shops/disable_shop',
			data:{'disable_id':disable_id, 'record_status':record_status},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					// $.toast({
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
	

	$(".saveBtnArea").click(function(e){
		e.preventDefault();

		var thiss = $("#add_area-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main_settings/insert_area',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnArea").prop('disabled', true); 
				$(".saveBtnArea").text("Please wait...");
			},
			success:function(data){
				$(".cancelBtn, .saveBtnArea").prop('disabled', false);
				$(".saveBtnArea").text("Add Area");
				if (data.success == 1) {
					fillDatatable(""); //refresh datatable
					$(thiss).find('input').val(""); // clean fields

					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					// $.toast({
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
					$('#addAreaModal').modal('toggle'); //close modal
				}else{
					showCpToast("info", "Note!", data.message);
					// $.toast({
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

	$('#table-grid').delegate(".btnView", "click", function(){
		$.LoadingOverlay("show");
	  	var areaId = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_settings/get_area',
	  		data:{'areaId':areaId},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".info_areaId").val(areaId);
	  				$(".info_desc").val(res[0].description);
	  				if(res[0].mon == 'true')
	  					$(".monday_check").prop("checked", true);
	  				else
	  					$(".monday_check").prop("checked", false);
	  				if(res[0].tue == 'true')
	  					$(".tuesday_check").prop("checked", true);
	  				else
	  					$(".tuesday_check").prop("checked", false);
	  				if(res[0].wed == 'true')
	  					$(".wednesday_check").prop("checked", true);
	  				else
	  					$(".wednesday_check").prop("checked", false);
	  				if(res[0].thu == 'true')
	  					$(".thursday_check").prop("checked", true);
	  				else
	  					$(".thursday_check").prop("checked", false);
	  				if(res[0].fri == 'true')
	  					$(".friday_check").prop("checked", true);
	  				else
	  					$(".friday_check").prop("checked", false);
	  				if(res[0].sat == 'true')
	  					$(".saturday_check").prop("checked", true);
	  				else
	  					$(".saturday_check").prop("checked", false);
	  			}
	  			else {
	  				$(".info_desc").val('');
				}
				
				$.LoadingOverlay("hide");
				$("#viewAreaModal").modal('show');
	  		}
	  	});
	});

	$("#update_area-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main_settings/update_area',
			data:serial,
			success:function(data){
				if (data.success == 1) {
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					// $.toast({
					//     text: data.message,
					//     icon: 'success',
					//     loader: false,  
					//     stack: false,
					//     position: 'top-center', 
					//     bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 4000
					// });
					fillDatatable(""); //refresh table
					$('#viewAreaModal').modal('toggle'); //close modal
				}else{
					showCpToast("info", "Note!", data.message);
					// $.toast({
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
	
	let id;
	$('#table-grid').delegate(".action_approved", "click", function(){
		 id = $(this).data('value');
		
	});

	$("#approved_modal_confirm_btn").click(function(e){
		e.preventDefault();
		// alert(id);
		$.LoadingOverlay("show");
		$.ajax({
			type:'post',
			url:base_url+'shops/Main_shops/approved_shops',
			data:{'shop_id':id},
			success:function(data){
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if(json_data.success){
					$('#approved_login').modal('hide');
					showCpToast("success", "Approved!", "Shop  approved successfully.");
					setTimeout(function(){location.reload()}, 2000);
				}else{
					$.LoadingOverlay("hide");
					$('#approved_login').modal('hide');
				}
			}
		});
	});
	
});



