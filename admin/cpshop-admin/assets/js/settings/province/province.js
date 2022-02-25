$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');
	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _code 	= $("input[name='_code']").val();
		var _name		= $("input[name='_name']").val();
		var _searchby		= $("select[name='_searchby']").val();
		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			searching: false,
			"serverSide": true,
			"columnDefs": [
				{ targets: 2, orderable: true},
				{ targets: 3, orderable: true},
				{ targets: 4, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				type: "post",
				url:base_url+"Province/list", // json datasource
				data:{'_record_status':_record_status, '_code':_code, '_name':_name, '_searchby':_searchby}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(res) {
					var filter = {'_record_status':_record_status, '_code':_code, '_name':_name, '_searchby':_searchby};
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

	
	// $('#table-grid').delegate(".action_edit", "click", function(){
	// 	let id = $(this).data('value');
	// 	$.LoadingOverlay("show");
	//   	window.location.href=base_url+"Referralcomrate/manage/"+ id + "/" + token;
	//   	$.LoadingOverlay("hide");
	// });

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
			url:base_url+'Province/deactivate',
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
			url:base_url+'Province/disable',
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

	$('#_searchby').on( 'change', function () {
    	if($(this).val() == "province"){
    		$("#_provcode").attr('hidden', false);
    	}else{
    		$("#_provcode").attr('hidden', true);
    	}	
		$(".enter_search").val("");
    });
});