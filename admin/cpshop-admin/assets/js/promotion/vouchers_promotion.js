$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

	// start - for loading a table
	function fillDatatable() {
		var _voucher_type   = $("select[name='_voucher_type']").val();
		var _voucher_code   = $("input[name='_vcode']").val();	
		var _vname          = $("input[name='_vname']").val();
		var date_from       = $("input[name='date_from']").val();	
		var date_to        = $("input[name='date_to']").val();	
		var _record_status  = $("select[name='_record_status']").val();

		//alert(_voucher_code);
		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			"columnDefs": [
				{ targets: 7, orderable: false, "sClass": "text-center" },
				{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			"ajax": {
				type: "post",
				url: base_url + "promotion/Main_promotion/get_vouchers_list_table", // json datasource
				data: {
					   '_voucher_type':_voucher_type,
					   '_voucher_code': _voucher_code, 
				       '_vname':_vname, 
					   'date_from':date_from,
					   'date_to':date_to,
					   '_record_status': _record_status,
					 }, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (res) {
					var filter = {
								'_voucher_type':_voucher_type,
								'_voucher_code': _voucher_code, 
								'_vname':_vname, 
								'date_from':date_from,
								'date_to':date_to,
								'_record_status': _record_status,
								};
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

	$("#_record_status").change(function () {
		$("#btnSearch").click();
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

	// delete
	let delete_id;
	$('#table-grid').delegate(".action_delete", "click", function(){
		delete_id = $(this).data('value');
		$('#delete_modal_confirm_btn').attr('data-id',delete_id);
	});


$('body').delegate("#delete_modal_confirm_btn", "click", function(e){
	e.preventDefault();
	var delete_id = $('#delete_modal_confirm_btn').attr('data-id');
		$.ajax({
			type:'post',
			url:base_url+'promotion/Main_promotion/delete_voucher/'+delete_id,
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


	
	let disable_id;
	let record_status;
	$('#table-grid').delegate(".action_disable", "click", function(){
		disable_id	  = $(this).data('value');
		record_status = $(this).data('record_status');
        
		if (record_status == 1) {
			$(".mtext_record_status").text("Disable");
		}else if (record_status == 2) {
			$(".mtext_record_status").text("Enable");
		}
	});

$("#disable_modal_confirm_btn").click(function(e){
	e.preventDefault();
		$.ajax({
			type:'post',
			url:base_url+'promotion/Main_promotion/disable_modal_confirm',
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


	$('#unsetFeadutedModal').on('show.bs.modal', function (e) {
		var voucher_id = $(e.relatedTarget).attr('data-id');
		$(this).find('#voucher_ids').val(voucher_id);
	});


	$('#setFeadutedModal').on('show.bs.modal', function (e) {
		var voucher_id = $(e.relatedTarget).attr('data-id');
		$(this).find('#voucher_id').val(voucher_id);
	});

	$('#saveFeatureConfirm').click(function(){
		var voucher_id	         	= $('#voucher_id').val();

		$.LoadingOverlay("Show");
			$.ajax({
				type: 'post',
				url: base_url+"promotion/Main_promotion/set_to_all",
				dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
				data:{
					'voucher_id': voucher_id,
	
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#setFeadutedModal').modal('hide');
						showCpToast("success", "Added!", "Voucher set to all successfully.");
						setTimeout(function(){location.reload()}, 2000);
					}
					else{
						//sys_toast_warning(json_data.message);
						showCpToast("info", "Note!", json_data.message);
						$('#setFeadutedModal').modal('hide');
					}
					
				},	
				error: function(error){
					//sys_toast_error('Something went wrong. Please try again.');
					showCpToast("error", "Error!", 'Something went wrong. Please try again.');
				}
			});
									   
	
	});
	
	
	$('#unsaveFeatureConfirm').click(function(){
	
		var voucher_ids	  = $('#voucher_ids').val();
		$.LoadingOverlay("Show");
		$.ajax({
			type: 'post',
			url: base_url+"promotion/Main_promotion/unset_to_all",
			dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
			data:{
				'voucher_id': voucher_ids,
	
			},
			success:function(data){
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if(json_data.success){
					$('#unsetFeadutedModal').modal('hide');
					showCpToast("success", "Removed!", "Voucher unset to all removed successfully.");
					setTimeout(function(){location.reload()}, 2000);
				}
				else{
					//sys_toast_warning(json_data.message);
					showCpToast("info", "Note!", json_data.message);
					$('#unsetFeadutedModal').modal('hide');
				}
			   
			},
			error: function(error){
				//sys_toast_error('Something went wrong. Please try again.');
				showCpToast("error", "Error!", 'Something went wrong. Please try again.');
			}
		});
	
	
	});
	


	
});







