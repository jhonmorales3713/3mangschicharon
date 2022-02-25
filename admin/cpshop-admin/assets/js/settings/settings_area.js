$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	
	function fillDatatable(area) {
		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 2, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				url:base_url+"developer_settings/Main_dev_settings/content_navigation_table", // json datasource
				data:{'area': area},
				type: "post",  // method  , by default get
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

	$('.btnSearch').on('click', function(){
		area = $('.searchArea').val();
		fillDatatable(area);
	});

	$(".btnClickAddArea").click(function(e){
		var thiss = $('#add_area-form'); // target form
		$(thiss).find('input').val(""); // clean fields 
		$(thiss).find("input:checkbox").prop("checked", false); //unchecked checkbox
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
					$('#addAreaModal').modal('toggle'); //close modal
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
					$('#viewAreaModal').modal('toggle'); //close modal
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
	
	$('#table-grid').delegate(".btnDelete","click", function(){
		$.LoadingOverlay("show");
		var areaId = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_settings/get_area',
	  		data:{'areaId':areaId},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
  					$(".del_areaId").val(areaId);
					$(".info_desc").text(res[0].description);
				}
				$.LoadingOverlay("hide");
				$('#deleteAreaModal').modal('show');
	  		}
	  	});
	});

	$('.deleteAreaBtn').click(function(e){
		e.preventDefault();
		var del_areaId = $(".del_areaId").val();

		$.ajax({
			type:'post',
			url:base_url+'Main_settings/delete_area',
			data:{'del_areaId':del_areaId},
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
					$('#deleteAreaModal').modal('toggle'); //close modal
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
	
});

function checkMonday(checkbox) {
    if (checkbox.checked) {
        $("#monday_check").prop("value", true);
    }
    else {
    	$("#monday_check").prop("value", false);
    }
}

function checkTuesday(checkbox) {
    if (checkbox.checked) {
        $("#tuesday_check").prop("value", true);
    }
    else {
    	$("#tuesday_check").prop("value", false);
    }
}

function checkWed(checkbox) {
    if (checkbox.checked) {
        $("#wednesday_check").prop("value", true);
    }
    else {
    	$("#wednesday_check").prop("value", false);
    }
}

function checkThurs(checkbox) {
    if (checkbox.checked) {
        $("#thursday_check").prop("value", true);
    }
    else {
    	$("#thursday_check").prop("value", false);
    }
}

function checkFriday(checkbox) {
    if (checkbox.checked) {
        $("#friday_check").prop("value", true);
    }
    else {
    	$("#friday_check").prop("value", false);
    }
}

function checkSat(checkbox) {
    if (checkbox.checked) {
        $("#saturday_check").prop("value", true);
    }
    else {
    	$("#saturday_check").prop("value", false);
    }
}




