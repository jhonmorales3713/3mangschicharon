$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');
	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _mainshop 	= $("select[name='_mainshop'] option:selected" ).text();
		var _effectivity_date 	= $("input[name='_effectivity_date']").val();
		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			searching: false,
			"serverSide": true,
			responsive: false,
			// "scrollX": true,
			"order": [[ 0, "asc" ]],
			"columnDefs": [
				{ responsivePriority: 1, targets: 0 }, {"targets": 1,"orderable": false}, {"targets": 1,"orderable": false}, 
				{"targets": 2,"orderable": false}, {"targets": 3,"orderable": false}, {"targets": 4,"orderable": false}, {"targets": 5,"orderable": false}, 
				{"targets": 6,"orderable": false}, {"targets": 7,"orderable": false}, {"targets": 8,"orderable": false}, {"targets": 11,"orderable": false}
			],
			"ajax":{
				type: "post",
				url:base_url+"shops/Shop_mcr_scheduling/shop_mcr_scheduling_data",
				data:{'_record_status':_record_status, '_mainshop':_mainshop, '_effectivity_date':_effectivity_date}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(res) {
					var filter = {'_record_status':_record_status, '_mainshop':_mainshop, '_effectivity_date':_effectivity_date};
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
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
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

	$('#table-grid').delegate(".action_view", "click", function(){
		let id = $(this).data('value');
		$.LoadingOverlay("show");
	  	window.location.href=base_url+"Shops/update/"+ id + "/" + token;
	  	// $.LoadingOverlay("hide");
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
	  	window.location.href=base_url+"Shopbranch/manage/"+ id + "/" + token;
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
			url:base_url+'Shop_mcr_scheduling/delete_mcr_schedule_record',
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
					showCpToast("info", "Noted!", data.message);
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
			url:base_url+'Shopbranch/disable_branch',
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
					showCpToast("info", "Noted!", data.message);
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

    function shop_mcr_cron(){
        $.ajax({
            type: "post",
            url: base_url + "shops/Shop_mcr_scheduling/shop_mcr_cron",
            dataType: 'json',
            success: function(data) {
                 console.log(data.message);
            }
        });
    }

    // setInterval(function() {shop_mcr_cron();}, 60000);
});