$(function(){
	var company_code = $("#company_code").val();
    
    function hasAdmin() {
        $.ajax({
			type:'post',
			url:base_url+'Main_dev_settings/has_admin',
			data:{'company_code' : company_code},
			success:function(data){
				if (data.success == 1) {
					$("#user_position option").each(function() {
						if ($(this).val() == "2" || $(this).val() == "4")
							$(this).attr("disabled", true);
					});
                }
                else {
					$("#user_position option").each(function() {
						if ($(this).val() == "2" || $(this).val() == "4")
							$(this).attr("disabled", false);
					});
                }
			}
		});
	}
	
	function userLimitation() {
		$.ajax({
			type:'post',
			url:base_url+'Main_dev_settings/user_limitation',
			data:{'company_code' : company_code},
			success:function(data){
				if (data.success == 1) {
					$("#user_position option").each(function() {
						if ($(this).val() == "3" || $(this).val() == "5")
							$(this).attr("disabled", true);
					});
                }
                else {
					$("#user_position option").each(function() {
						if ($(this).val() == "3" || $(this).val() == "5")
							$(this).attr("disabled", false);
					});
                }
			}
		});
	}

	hasAdmin();
	userLimitation();

	function fillDatatable(name, position) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 3, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				url:base_url+"Main_dev_settings/company_users_table", // json datasource
				type: "post",  // method  , by default get
				data: { "name" : name, "position" : position, "company_code" : company_code },
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
	
	fillDatatable("", "");

	// ON MODAL CLOSE, FORM RESET
	$('#userModal').on('hidden.bs.modal', function () {
		$('#user_form').trigger('reset')
	})

	$("#btnClickAddSystemUser").on('click', function(){
		$("#btnSave").text("Add User");
		$("#userModalLabel").text("Add New User");
		$('#password_div').show()
		$('#new_password_div').hide()
	});

	/* Search company user */
	$(".btnSearch").on("click", function(){
		company_name = $(".txt_search_name").val();
		company_position = $(".txt_search_position").val();

		fillDatatable(company_name, company_position);
    });

	/* Add and Update company user */
	$('#user_form').submit(function(e) {
		e.preventDefault(); 
		var serial = $(this).serialize();
		$.ajax({
			type : 'post'
			, url : base_url+'Main_dev_settings/user_user'
			, data : serial
			, beforeSend : function(data){
				$.LoadingOverlay("show");	
			}
			, success : function(data){
				$.LoadingOverlay("hide")
				if (data.success == 1) 
				{
                    //toastMessage('Success', data.message, 'success');
                    showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

                    $('#user_form').trigger('reset');
					fillDatatable("", ""); //refresh table
					$('#userModal').modal('hide'); //close modal
                }
				else
				{
                    //toastMessage('Note', data.message, 'error');
                    showCpToast("error", "Note!", data.message);
				}
			}
		});
	});

	/* Edit company user */
	$('#table-grid').delegate(".btnEdit", "click", function(){
		$.LoadingOverlay("show")
		var user_id = $(this).data('user_id')
		
		$('#new_password_div').show()
		$('#password_div').hide()

	  	$.ajax({
	  		type: 'post'
	  		, url : base_url+'Main_dev_settings/get_user_details'
	  		,data : {'user_id':user_id}
	  		, beforeSend : function(data){
				$.LoadingOverlay("show");	
			}
	  		, success : function(data){
				$.LoadingOverlay("hide");
				var res = data.result
				  
				if (data.success == 1) 
				{
					$("#user_idno").val(user_id)
					$("#user_fname").val(res[0].user_fname)
					$("#user_mname").val(res[0].user_mname)
					$("#user_lname").val(res[0].user_lname)
					$("#user_username").val(res[0].username)
					$('#user_password').val(res[0].password)
					$("#user_position").val(res[0].position_id).change()
					$("#btnSave").text("Update User")
					$("#userModalLabel").text("Update User Details")
				}
				else 
				{
                    $("#user_form")[0].reset()
                    $("#user_position").val('').change()
				}
				  
				$.LoadingOverlay("hide")
				$("#userModal").modal("show")
	  		}
		});  
	});

	/* Deactivate user confirmation */
	$('#table-grid').delegate(".btnDeactivate","click", function(){
		$.LoadingOverlay("show");
		var id = $(this).data('user_id');
		var status = $(this).data('user_status')

		$.ajax({
	  		type : 'post'
	  		, url : base_url+'Main_dev_settings/select_deactivate_user'
			, data : {'id' : id}
			, dataType : 'JSON'
	  		, success:function(data){
				  
				if (data.success == 1) 
				{
					$(".del_id").val(id)
					$(".del_status").val(status)
					$(".del_name").text(data.result[0].name)
				}
				  
				$.LoadingOverlay("hide")
				$('#deactivateUserModal').modal('show')
	  		}
	  	});
	});

	/* Set deactivate status to user */
	$('.deactUserBtn').click(function(e){
		e.preventDefault();
		$.LoadingOverlay("show");

		var del_id = $(".del_id").val()
		let del_status = $(".del_status").val()

		$.ajax({
			type : 'post'
			, url : base_url+'Main_dev_settings/set_user_status'
			, data : {
				'id' : del_id
				, 'status' : del_status
			}
			, success : function(data){
				if (data.success == 1) 
				{
					//toastMessage('Success', data.message, 'success')
					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					fillDatatable("", "") //refresh table
					$('#deactivateUserModal').modal('hide') //close modal
				}
				else 
				{
					//toastMessage('Note', data.message, 'error')
					showCpToast("error", "Note!", data.message);
				}

				$.LoadingOverlay("hide")
			}
		});
	});

	/* Reactivate user confirmation */
	$(document).on('click', '.btnReactivate', function(){
		$.LoadingOverlay("show");
		var id = $(this).data('user_id');
		var status = $(this).data('user_status')

		$.ajax({
	  		type : 'post'
	  		, url : base_url+'Main_dev_settings/select_deactivate_user'
			, data : {'id' : id}
			, dataType : 'JSON'
	  		, success:function(data){
				  
				if (data.success == 1) 
				{
					$(".react_id").val(id)
					$(".react_status").val(status)
					$(".react_name").text(data.result[0].name)
				}
				  
				$.LoadingOverlay("hide")
				$('#reactivateUserModal').modal('show')
	  		}
	  	});
	})

	/* Set registered status to reactivated user */
	$('.reactUserBtn').click(function(e){
		e.preventDefault();
		$.LoadingOverlay("show");

		var react_id = $(".react_id").val()
		let react_status = $(".react_status").val()

		$.ajax({
			type : 'post'
			, url : base_url+'Main_dev_settings/set_user_status'
			, data : {
				'id' : react_id
				, 'status' : react_status
			}
			, dataType : 'JSON'
			, success : function(data){
				if (data.success == 1) 
				{
					//toastMessage('Success', data.message, 'success')
					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					fillDatatable("", "") //refresh table
					$('#reactivateUserModal').modal('hide') //close modal
				}
				else 
				{
					//toastMessage('Note', data.message, 'error')
					showCpToast("error", "Note!", data.message);
				}

				$.LoadingOverlay("hide")
			}
		})

		// alert(react_id)
	});

	$("#update_company_form").submit(function(e){
		e.preventDefault(); 

		formData = new FormData(this);

		formData.append('update_id', $("#update_id").val());
		formData.append('update_code', $("#update_code").val());
		formData.append('update_name', $("#update_name").val());
		formData.append('update_initial', $("#update_initial").val());
		formData.append('update_address', $("#update_address").val());
		formData.append('update_website', $("#update_website").val());
		formData.append('update_phone', $("#update_phone").val());
		formData.append('update_email', $("#update_email").val());
		formData.append('update_database', $("#update_database").val());
		formData.append('update_plan', $("#update_plan").val());

		if ($("#update_logo").get(0).files.length === 0) {
			formData.append('update_logo', update_logo);
		}

		if ($("#update_logo_small").get(0).files.length === 0) {
			formData.append('update_logo_small', update_logo_small);
		}

		$.ajax({
			type:'post',
			url:base_url+'Main_dev_settings/update_company',
			data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend : function(data){
				$.LoadingOverlay("show");	
			}
			, success:function(data){
				$.LoadingOverlay("hide");
				if (data.success == 1) {
					toastMessage('Success', data.message, 'success');
                    $("#update_company_form").trigger("reset");
					fillDatatable("", ""); //refresh table
					$('#updateCompanyModal').modal('toggle'); //close modal
				}
				else {
					toastMessage('Note', data.message, 'error');
				}
			}
		});
	});
	
});