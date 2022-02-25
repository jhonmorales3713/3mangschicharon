var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

function fillDatatable(inputs_arr) {
	var dataTable = $('#table-grid').DataTable({
		"pageLength": 10,
		"destroy": true,
		"serverSide": true,
		"columnDefs": [
			{ targets: 2, orderable: false, "sClass":"text-center" }
		],
		"destroy": true,
		"ajax":{
			url :base_url+"Main_settings/system_user_table", // json datasource
			type: "post",  // method  , by default get
			data: inputs_arr,
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
			complete: function() {
				$.LoadingOverlay("hide"); 
			},
			error: function() {  // error handling
				$(".table-grid-error").html("");
				$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		}
	});
}
$(function(){
	// 041520
	search_table_j(inputs_arr);
	generate_searches_j('#table-grid');
	// initiate datepicker;
	$('#datepicker').datepicker({
		todayBtn: "linked"
	});	
	// 041520

	$("#add_system_user-form").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		$.ajax({
			type:'post',
			url:base_url+'Main_settings/insert_system_user',
			data:serial,
			success:function(data){
				if (data.success == 1) {
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
					showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    $("#add_system_user-form").trigger("reset");
					fillDatatable(""); //refresh datatable
					$("#addSystemUserModal").modal('hide');
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

	$("#table-grid").delegate(".btnDelete", "click", function(){
		$.LoadingOverlay("show"); 
		var user_id = $(this).data('value');

		$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_settings/get_system_user',
	  		data:{'user_id':user_id},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
  					$(".del_user_id").val(user_id);
					$(".delete_username").text(res[0].user_fname + " " + res[0].user_lname);
	  			}
				$.LoadingOverlay("hide"); 
				$('#deleteSystemUserModal').modal('show');
	  		}
	  	});
	});

    $('#table-grid').delegate(".btnView", "click", function(){
		$.LoadingOverlay("show"); 

        var user_id = $(this).data('value');

        $.ajax({
            type: 'post',
            url: base_url+'Main_settings/get_system_user',
            data:{'user_id':user_id},
            success:function(data){
                var res = data.result;
                if (data.success == 1) {
                    $(".update_user_id").val(user_id);
                    $(".update_position").val(res[0].position_id).change();
                    $("#update_user_mname").val(res[0].user_mname);
                    $("#update_user_fname").val(res[0].user_fname);
                    $("#update_user_lname").val(res[0].user_lname);
                    $("#update_username").val(res[0].username);
                }
                else {
                    $(".update_user_id").val(user_id);
                    $(".update_position").val('').change();
                    $("#update_user_fname").val('');
                    $("#update_user_mname").val('');
                    $("#update_user_lname").val('');
                    $("#update_username").val('');
                }
				$.LoadingOverlay("hide"); 
				$('#viewSystemUserModal').modal('show');
            }
        });
  	});

	$("#update_system_user-form").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		$.ajax({
			type:'post',
			url:base_url+'Main_settings/update_system_user',
			data:serial,
			success:function(data){
				if (data.success == 1) {
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
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
                    $("#update_system_user-form").trigger("reset");
					fillDatatable(""); //refresh datatable
					$("#viewSystemUserModal").modal('hide');
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

	$("#delete_system_user-form").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		$.ajax({
			type:'post',
			url:base_url+'Main_settings/delete_system_user',
			data:serial,
			success:function(data){
				if (data.success == 1) {
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
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);

					fillDatatable(""); //refresh datatable
					$("#deleteSystemUserModal").modal('hide');
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