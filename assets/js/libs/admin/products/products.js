$(function(){

	var base_url = $(".body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var ini      = $(".body").data('ini');
	// start - for loading a table
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
			],
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
			url:base_url+'products/Main_products/delete_modal_confirm',
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
					// 	hideAfter: 3000
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
		$.LoadingOverlay("show");
		$('#disable_modal_confirm_btn').prop('disabled', true);
		$.ajax({
			type:'post',
			url:base_url+'products/Main_products/disable_modal_confirm',
			data:{'disable_id':disable_id, 'record_status':record_status},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					$.LoadingOverlay("hide");
					fillDatatable(); //refresh datatable
					//sys_toast_success(data.message);
					showCpToast("success", "Success!", data.message);
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



		$('#upload_pg_form').submit(function (e) {
			$.LoadingOverlay("show");
			e.preventDefault();
			$('#PGupload').attr('disabled', true);
			var form = $(this);
			var form_data = new FormData(form[0]);
			//form_data.append('file');
			if (validateInputs(form_data)) {
				$.ajax({
					type: "post",
					url: base_url + "products/Main_products/puregold_upload_products_files",
					data: form_data,
					processData: false,
					contentType: false,
					success: function (data) {
						if (data.success == 1) {
							$('#puregoldUploadModal').modal('hide');
							$.LoadingOverlay("hide");
							showCpToast("success", "Success!", "Puregold products uploaded successfully.");
							fillDatatable();
							$('#PGupload').attr('disabled', false);
						}
						else {
							showCpToast("warning", "Note!", data.message);
							$('#PGupload').attr('disabled', false);
							$.LoadingOverlay("hide");
						}
	
						$("#puregoldUploadModal").modal('hide');
						document.getElementById("upload_pg_form").reset();
					},
					error: function () {
						//showToast('note', 'Something went wrong, Please Try again!');
						showCpToast("warning", "Note!", 'Something went wrong, Please Try again!');
					}
				});
			}
			else {
				//showToast('note', 'Please fill-up all fields');
				showCpToast("warning", "Note!", 'Please fill-up all fields');
			}
	
		});

		$('#upload_tokmart_form').submit(function (e) {
			$.LoadingOverlay("show");
			e.preventDefault();
			$('#TMupload').attr('disabled', true);
			var form = $(this);
			var form_data = new FormData(form[0]);
			//form_data.append('file');
			if (validateInputs(form_data)) {
				$.ajax({
					type: "post",
					url: base_url + "products/Main_products/upload_products_files",
					data: form_data,
					processData: false,
					contentType: false,
					success: function (data) {
						var res = data.result;
						if (data.success == 1) {
							$('#tokmartUploadModal').modal('hide');
							$.LoadingOverlay("hide");
							showCpToast("success", "Success!", "Products uploaded successfully.");
							fillDatatable();
							$('#TMupload').attr('disabled', false);
						}
						else {
							showCpToast("warning", "Note!", data.message);
							$('#TMupload').attr('disabled', false);
							$.LoadingOverlay("hide");
						}
	
						$("#tokmartUploadModal").modal('hide');
						document.getElementById("upload_tokmart_form").reset();
					},
					error: function () {
						// showToast('note', 'Something went wrong, Please Try again!');
						showCpToast("warning", "Note!", "Something went wrong, Please Try again!");
						$.LoadingOverlay("hide");

					}
				});
			}
			else {
				//showToast('note', 'Please fill-up all fields');
				showCpToast("warning", "Note!", 'Please fill-up all fields');
			}
	
		});

		$('#upload_inv_tokmart_form').submit(function (e) {
			$.LoadingOverlay("show");
			e.preventDefault();
			$('#TMinvupload').attr('disabled', true);
			var form = $(this);
			var form_data = new FormData(form[0]);
			//form_data.append('file');
			if (validateInputs(form_data)) {
				$.ajax({
					type: "post",
					url: base_url + "products/Main_products/upload_inventory_products_files",
					data: form_data,
					processData: false,
					contentType: false,
					success: function (data) {
						var res = data.result;
						if (data.success == 1) {
							$('#tokmartinvUploadModal').modal('hide');
							$.LoadingOverlay("hide");
							showCpToast("success", "Success!", "Products inventory uploaded successfully.");
							fillDatatable();
							$('#TMinvupload').attr('disabled', false);
						}
						else {
							showCpToast("warning", "Note!", data.message);
							$('#TMinvupload').attr('disabled', false);
							$.LoadingOverlay("hide");
						}
	
						$("#tokmartinvUploadModal").modal('hide');
						document.getElementById("upload_inv_tokmart_form").reset();
					},
					error: function () {
						// showToast('note', 'Something went wrong, Please Try again!');
						showCpToast("warning", "Note!", "Something went wrong, Please Try again!");
						$.LoadingOverlay("hide");

					}
				});
			}
			else {
				//showToast('note', 'Please fill-up all fields');
				showCpToast("warning", "Note!", 'Please fill-up all fields');
			}
	
		});
	
		function validateInputs(data) {
			for (var i = 0; i < data.length; i++) {
				if (data[i].value == "") {
					return false;
				}
			}
	
			return true;
		}
	
});