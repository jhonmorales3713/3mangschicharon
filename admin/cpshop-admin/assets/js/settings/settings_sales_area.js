$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	function fillDatatable(area) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 2, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				url:base_url+"Main_settings/sales_area_table", // json datasource
				type: "post",  // method  , by default get
				data: { "area" : area },
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

	fillDatatable("");

	$(".btnSearch").on("click", function() {
		area = $(".searchArea").val();

		fillDatatable(area);
	})

	$(".btnClickAddSalesArea").click(function(e){
		var thiss = $('#add_sales_area-form'); // target form
		$(thiss).find('input').val(""); // clean fields 
		$(thiss).find("input:checkbox").prop("checked", false); //unchecked checkbox

	});

	$(".saveBtnSalesArea").click(function(e){
		
		e.preventDefault();

		var thiss = $("#add_sales_area-form");

		var serial = thiss.serialize();

		$.ajax({
			type:'post',
			url: base_url+'Main_settings/insert_sales_area',
			data: serial,
			beforeSend:function(data){
				$(".cancelBtn, .saveBtnSalesArea").prop('disabled', true); 
				$(".saveBtnSalesArea").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .saveBtnSalesArea").prop('disabled', false);
				$(".saveBtnSalesArea").text("Add Sales Area");
				if (data.success == 1) {
					$(thiss).find('input').val(""); // clean fields
					
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
					fillDatatable(""); //refresh table
					$('#addSalesAreaModal').modal('toggle'); //close modal
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

	$('#table-grid').delegate(".btnView", "click", function(){
		$.LoadingOverlay("show"); 
	  	var id = $(this).data('value');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_settings/get_sales_area',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
	  				$(".info_id").val(id);
	  				$(".info_desc").val(res[0].description);
	  			}
	  			else {
	  				$(".info_desc").val('');
	  			}
				$.LoadingOverlay("hide");
				$('#viewSalesAreaModal').modal('show'); 
	  		}
	  	});
	});

	$("#update_sales_area-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		$.ajax({
			type:'post',
			url:base_url+'Main_settings/update_sales_area',
			data:serial,
			beforeSend:function(data){
				$(".cancelBtn, .updateBtnSalesArea").prop('disabled', true); 
				$(".updateBtnSalesArea").text("Please wait...");
				
			},
			success:function(data){
				$(".cancelBtn, .updateBtnSalesArea").prop('disabled', false);
				$(".updateBtnSalesArea").text("Save Changes");
				if (data.success == 1) {
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
					// 	hideAfter: 4000
					// });
					fillDatatable(""); //refresh table
					$('#viewSalesAreaModal').modal('toggle'); //close modal
				}else{
					showCpToast("info", "Note!", data.message);
					// $.toast({
					//     heading: 'Note',
					//     text: data.message,
					//     icon: 'error',
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
	
	$('#table-grid').delegate(".btnDelete","click", function(){
		$.LoadingOverlay("show"); 
		var id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_settings/get_sales_area',
	  		data:{'id':id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
  					$(".del_id").val(id);
					$(".info_desc").text(res[0].description);
	  			}
				$.LoadingOverlay("hide"); 
				$('#deleteSalesAreaModal').modal('show');
	  		}
	  	});

	});

	$('.deleteSalesAreaBtn').click(function(e){
		e.preventDefault();

		var del_id = $(".del_id").val();

		$.ajax({
			type:'post',
			url:base_url+'Main_settings/delete_sales_area',
			data:{'del_id':del_id},
			success:function(data){
				if (data.success == 1) {
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
					// 	hideAfter: 4000
					// });
					fillDatatable(""); //refresh table
					$('#deleteSalesAreaModal').modal('toggle'); //close modal
				}else{
					showCpToast("info", "Note!", data.message);
					// $.toast({
					//     heading: 'Note',
					//     text: data.message,
					//     icon: 'error',
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
	
});