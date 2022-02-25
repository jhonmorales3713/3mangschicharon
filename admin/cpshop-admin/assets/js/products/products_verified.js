$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var checkBoxChecker   = 0;

	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _name 			= $("input[name='_name']").val();
		var _shops 			= $("select[name='_shops']").val();

		var dataTable = $('#table-grid-product').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"searching": false,
			responsive: true,
			'columnDefs': [{
				'targets': 0,
				'searchable':false,
				'orderable':false,
				'className': 'dt-body-center'
			 }],
			"ajax":{
				type: "post",
				url:base_url+"products/Products_approval/products_verified_table", // json datasource
				data: {'_record_status':_record_status, '_name':_name, '_shops':_shops}, // serialized dont work, idkw
				beforeSend:function(data) {
					// $.LoadingOverlay("show"); 
				},
				complete: function(data) {
					// $.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);

					if(response.data.length > 0){
						$('.btnExport').show(100);
					}
					else{
						$('#btnExport').hide(100);
					}
					// console.log(JSON.stringify(decodeURIComponent(this.data)));
					$("#_search").val(JSON.stringify(this.data));
					$("input#_record_status").val(_record_status);
					$("input#_name").val(_name);
					$("input#_shops").val(_shops);
					$(".table-grid-error").remove();
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
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
			return false;
        } 
    });

	$('#btnSearch').click(function(e){
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes



	$(document).on('click', '#ApproveButton', function(e) {

        e.preventDefault();
        const id = $(this).data('prod-id');
		$('#proceedBtn').attr('data-id', id);
   
    })
	
    $('#proceedBtn').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');
	
			$.ajax({
				type: 'post',
				url: base_url+"products/Products_approval/product_approved_to_verified_application",
				data:{
					'id': action_id,
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#approveModal').modal('hide');
						// sys_toast_success(json_data.message);
						showCpToast("success", "Success!", "Product changes has been approved.");
						setTimeout(function(){location.reload()}, 2000);
					}
					else{
						//sys_toast_warning(json_data.message);
						showCpToast("warning", "Warning!", json_data.message);
						$('#approveModal').modal('hide');
						// location.reload();
					}
				
				},
				error: function(error){
					//sys_toast_error('Something went wrong. Please try again.');
					showCpToast("error", "Error!", 'Something went wrong. Please try again.');
				}
			});
    
       
    });


	
	$(document).on('click', '#DeclineButton', function(e) {

        e.preventDefault();
        const id = $(this).data('prod-id');
		$('#proceedDecBtn').attr('data-id', id);
   
    })
	
    $('#proceedDecBtn').click(function(e){

        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');
		const reason = $('#dec_reason').val()
		
        if(reason != ''){

;
		$.ajax({
			type: 'post',
			url: base_url+"products/Products_approval/product_approved_application_decline",
			data:{
				'id': action_id,
				'reason':reason
			},
			success:function(data){
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if(json_data.success){
					$('#declineModal').modal('hide');
					// sys_toast_success(json_data.message);
					showCpToast("warning", "Success!", "Product changes has been declined.");
					setTimeout(function(){location.reload()}, 2000);
				}
				else{
					//sys_toast_warning(json_data.message);
					showCpToast("warning", "Warning!", json_data.message);
					$('#declineModal').modal('hide');
					// location.reload();
				}
			
			},
			error: function(error){
				//sys_toast_error('Something went wrong. Please try again.');
				showCpToast("error", "Error!", 'Something went wrong. Please try again.');
			}
		});
		}else{
			$.LoadingOverlay("hide");
			//sys_toast_warning('Please enter notes.');
			showCpToast("warning", "Warning!", 'Please enter notes.');
		}

    
       
    });




	

});




