$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

	// start - for loading a table
	function fillDatatable() {
		var _voucher_code   = $("input[name='_vcode']").val();	
		var _voucher_refnum = $("input[name='_vref']").val();
		var _shopname       = $("select[name='_sname']").val();
		var _record_status = $("select[name='_record_status']").val();
		var _select_status = $("select[name='_select_status']").val();

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
				url: base_url + "vouchers/List_vouchers/get_vouchers_list_json_data", // json datasource
				data: {'_voucher_code': _voucher_code, 
				       '_voucher_refnum':_voucher_refnum, 
					   '_shopname': _shopname, 
					   '_record_status': _record_status,
					   '_select_status': _select_status
					 }, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (res) {
					var filter = {'_voucher_code': _voucher_code, 
					              '_voucher_refnum':_voucher_refnum,
								   '_shopname': _shopname, 
								   '_record_status': _record_status,
								   '_select_status': _select_status
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
			url:base_url+'vouchers/List_vouchers/delete_voucher/'+delete_id,
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
					// $.toast({
					//     text: data.message,
					//     icon: 'info',
					//     loader: false,   
					//     stack: false,
					//     position: 'top-center',  
					//     bgColor: '#FFA500',
					// 	textColor: 'white'        
					// });
					showCpToast("info", "Note!", data.message);
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
			url:base_url+'voucher/disable_voucher',
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


	
});





