$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();	

	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _username 		= $("input[name='_username']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			searching: false,
			"serverSide": true,
			responsive: true,
			"columnDefs": [
				{ targets: [1, 4], orderable: false, "sClass":"text-center"},
				{ targets: [0], visible: false },
				{ responsivePriority: 1, targets: 0 },
        		{ responsivePriority: 2, targets: -1 }
			],
			"ajax":{
				type: "post",
				url:base_url+"admin/settings/user_list/user_list_table", // json datasource
				data:{
					'_record_status':_record_status, 
					'_username':_username
				}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(res) {
					var filter = {
						'_record_status':_record_status, 
						'_username':_username
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
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="4">No data found in the server</th></tr></tbody>');
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

	$('#addBtn').click(function(){
        window.location.assign(base_url+"settings/user_list/add_user/"+token);
	})

	
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
		$.LoadingOverlay("show");
		$.ajax({
			type:'post',
			url:base_url+'admin/settings/user_list/disable_data',
			data:{'disable_id':disable_id, 'record_status':record_status},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable
					//showToast('success', data.message);
					sys_toast_success(data.message);
					$('#disable_modal').modal('toggle'); //close modal
					$.LoadingOverlay("hide");
                    setTimeout(function(){location.reload()}, 2000);
					$(".modal-backdrop").remove();
				}else{
					//showToast('note', data.message);
					sys_toast_info(data.message);
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
		$.LoadingOverlay("show");
		$.ajax({
			type:'post',
			url:base_url+'admin/settings/user_list/delete_data',
			data:{'delete_id':delete_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable
					//showToast('success', data.message);
					sys_toast_success(data.message);
					$(".modal-backdrop").remove();
					
                    setTimeout(function(){location.reload()}, 2000);
					$('#delete_modal').modal('toggle'); //close modal
					$.LoadingOverlay("hide");
				}else{
					//showToast('note', data.message);
					sys_toast_info(data.message);
				}
			}
		});
	});
	// end - delete function

	/*
	 * type: success / note
	 */
	function showToast(type, message)
	{
		if (type == "success") {
			$.toast({
				heading: 'Success',
				text: message,
				icon: 'success',
				loader: false,  
				stack: false,
				position: 'top-center', 
				bgColor: '#5cb85c',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 10000
			});
		}
		else if (type == "note") {
			$.toast({
				heading: 'Note',
				text: message,
				icon: 'info',
				loader: false,   
				stack: false,
				position: 'top-center',  
				bgColor: '#FFA500',
				textColor: 'white'        
			});
		}
	}


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

	$('button[data-target="#demo"]').click(function(){
		if($('#demo').hasClass('show')){
			$(this).text('Show Access Control');
		}else{
			$(this).text('Hide Access Control');
		}
	});

	$('button[data-target="#demo2"]').click(function(){
		if($('#demo2').hasClass('show')){
			$(this).text('Show Access Control');
		}else{
			$(this).text('Hide Access Control');
		}
	});
});