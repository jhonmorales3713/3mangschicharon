$(function(){

	var base_url = $(".body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var ini      = $(".body").data('ini');
	// start - for loading a table
	$(document).on('click',".action_disable",(event)=>{
		id = $(this).data('value');
		$.ajax({
			type:'post',
			url:base_url+'admin/Main_products/check_product_orders',
			data:{'id':event.target.dataset.value},
			success:function(data){
				if(data > 0){
					sys_toast_error("Manage Inventory by moving out of expired stocks from inventory of product.");
				}else{
					$("#disable_modal").modal('show');
				}
			}
		});
	});
	
	$(document).on('click',".action_delete",(event)=>{
		id = $(this).data('value');
		$.ajax({
			type:'post',
			url:base_url+'admin/Main_products/check_product_orders',
			data:{'id':event.target.dataset.value},
			success:function(data){
				if(data == 1){

					sys_toast_error("You cannot delete a product with active order");
				}else{
					$("#delete_modal").modal('show');
				}
			}
		});
	});
	
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _name 			= $("input[name='_name']").val();
		var _categories     = $("select[name='_categories']").val();
		var date_from       = $("#date_from").val();



		var dataTable = $('#table-grid-product').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"searching": false,
			responsive: true,
			"language": {                
				"infoFiltered": ""
			},
			"columnDefs": [
				{ targets: 7, orderable: false, "sClass":"text-center"},
				{ responsivePriority: 1, targets: 7 },
			],createdRow: function( row, data, dataIndex ) {
				//console.log(row);
				var data2 = $('#table-grid-product').DataTable().row(row).data();
				if(data2[6]=='Expired Stocks'){
					$(row).addClass( 'bg-danger text-white' );
			   	}
				if(data2[6]=='Expiring Soon'){
					$(row).addClass( 'bg-warning' );
		   		}
				if(data2[6]=='Out of Stocks'){
					$(row).addClass( 'bg-secondary' );
				}
				// if ( data['jobStatus'] == "red" ) {
				// 	$(row).addClass( 'lightRed' );
				// }else if(data['jobStatus'] == "green"){
				// 	$(row).addClass( 'lightGreen' );
				// }else if(data['jobStatus'] == "amber"){
				// 	$(row).addClass( 'lightAmber' );
				// }
			},
			"columnDefs": [
				{
					"targets": [ 6 ],
					"visible": false,
					"searchable": false
				}],
			"ajax":{
				type: "post",
				url:base_url+"admin/Main_products/product_table", // json datasource
				data: {'_record_status':_record_status, 
				        '_name':_name, 
						'_categories':_categories,
						'date_from':date_from
					}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(data) {
					$.LoadingOverlay("hide");
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
					$("#date_from").val(date_from);
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

	
	$('#table-grid-product').delegate(".action_edit", "click", function(){
		let id = $(this).data('value');
	});

	let disable_id;
	let record_status;
	$('#table-grid-product').delegate(".action_disable", "click", function(){
		disable_id	  = $(this).data('value');
		record_status = $(this).data('record_status');

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		}else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	let delete_id;
	$('#table-grid-product').delegate(".action_delete", "click", function(){
		delete_id = $(this).data('value');
	});

	$("#delete_modal_confirm_btn").click(function(e){
		e.preventDefault();
		$.ajax({
			type:'post',
			url:base_url+'admin/Main_products/delete_modal_confirm',
			data:{'delete_id':delete_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					fillDatatable(); //refresh datatable

					sys_toast_success(data.message);
					//showCpToast("success", "Success!", data.message);
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
					// 	hideAfter: 3000
					// });
					$('#delete_modal').modal('toggle'); //close modal
				}else{
					
					sys_toast_info(data.message);
					//showCpToast("info", "Note!", data.message);
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
		$.LoadingOverlay("show");
		$('#disable_modal_confirm_btn').prop('disabled', true);
		$.ajax({
			type:'post',
			url:base_url+'admin/Main_products/disable_modal_confirm',
			data:{'disable_id':disable_id, 'record_status':record_status},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					$.LoadingOverlay("hide");
					fillDatatable(); //refresh datatable
					sys_toast_success(data.message);
					//sys_toast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);
					$('#disable_modal_confirm_btn').prop('disabled', false);
					$('#disable_modal').modal('toggle'); //close modal
					
				}else{
					$.LoadingOverlay("hide");
					sys_toast_warning(data.message);
					$('#disable_modal_confirm_btn').prop('disabled', false);
				}
			}
		});
	});

	$('#addBtn').click(function(){
        window.location.assign(base_url+"Main_products/add_products/"+token);
	})

		function validateInputs(data) {
			for (var i = 0; i < data.length; i++) {
				if (data[i].value == "") {
					return false;
				}
			}
	
			return true;
		}
	
});