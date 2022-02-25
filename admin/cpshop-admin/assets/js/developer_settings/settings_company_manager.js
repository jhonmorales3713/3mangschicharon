$(function(){
	var base_url = $("body").data('base_url');
	var token = $("body").data('token');
	var update_logo = "";
	var update_logo_small = "";
	var original_company_code = "";

	function fillDatatable(name, mainNav) {
		var dataTable = $('#table-grid').DataTable({
			"destroy": true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 5, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				url: base_url+"Main_dev_settings/company_manager_table", // json datasource
				dataType : "json",
				type: "post",  // method  , by default get
				data: { "name" : name, "mainNav" : mainNav },
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

	$(".btnSearch").on("click", function(){
		company_code = $(".txt_search_code").val();
		company_name = $(".txt_search_name").val();

		fillDatatable(company_code, company_name);
	});
	
	$("#add_logo").change(function(){
		readURL(this, $("#add_logo_view"));
	});
	
	$("#add_logo_small").change(function(){
		readURL(this, $("#add_logo_small_view"));
	});

	$('#add_company_form').submit(function(e) {
		e.preventDefault(); 

		formData = new FormData(this);

		formData.append('add_code', $("#add_code").val());
		formData.append('add_name', $("#add_name").val());
		formData.append('add_initial', $("#add_initial").val());
		formData.append('add_address', $("#add_address").val());
		formData.append('add_website', $("#add_website").val());
		formData.append('add_phone', $("#add_phone").val());
		formData.append('add_email', $("#add_email").val());
		formData.append('add_database', $("#add_database").val());
		formData.append('add_plan', $("#add_plan").val());

		$.ajax({
            type: "POST",
            url: base_url+"Main_dev_settings/save_company",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success:function(data) {
                if(data.success == 1) {
					//toastMessage('Success', data.message, 'success');
					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					dataTable.draw(); //refresh datatable
					$("#addContentNavigationModal").modal('hide');
					$("#addContentNavigationModal").find('input, select').val("");
                }
                else {
					//toastMessage('Note', data.message, 'error');
					showCpToast("error", "Note!", data.message);
                }
            }
        });
	});

	$('#table-grid').delegate(".btnView", "click", function(){
		$.LoadingOverlay("show");
	  	var company_code = $(this).data('company_code');

	  	$.ajax({
	  		type: 'post',
	  		url: base_url+'Main_dev_settings/get_company_details',
	  		data:{'company_code':company_code},
	  		success:function(data){
	  			var res = data.result;
	  			if (data.success == 1) {
					$("#update_id").val(res[0].company_id);
					original_company_code = company_code;
	  				$("#update_code").val(company_code);
	  				$("#update_name").val(res[0].company_name);
					$("#update_initial").val(res[0].company_initial);
					$("#update_logo_view").attr('src', base_url + 'assets/img/' + res[0].company_logo);
					update_logo = res[0].company_logo;
					$("update_logo_small").val(base_url + 'assets/img/' + res[0].company_logo_small);
					update_logo_small = res[0].company_logo_small;
					$("#update_logo_small_view").attr('src', base_url + 'assets/img/' + res[0].company_logo_small);
	  				$("#update_address").val(res[0].company_address);
	  				$("#update_website").val(res[0].company_website);
	  				$("#update_phone").val(res[0].company_phone);
	  				$("#update_email").val(res[0].company_email);
	  				$("#update_database").val(res[0].company_database);
                    $("#update_plan").val(res[0].plan_id).change();
                }
	  			else {
                    $("#form_update_company")[0].reset();
                    $("#update_plan").val('').change();
	  			}
				$.LoadingOverlay("hide");
				$("#updateCompanyModal").modal("show");
	  		}
	  	});
	});

	$("#update_company_form").submit(function(e){
		e.preventDefault(); 

		formData = new FormData(this);

		formData.append('update_id', $("#update_id").val());
		formData.append('original_company_code', original_company_code);
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
			success:function(data){
				if (data.success == 1) {
					
					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					//toastMessage('Success', data.message, 'success');
                    $("#update_company_form").trigger("reset");
					fillDatatable("", ""); //refresh table
					$('#updateCompanyModal').modal('toggle'); //close modal
				}
				else {
					//toastMessage('Note', data.message, 'error');
					showCpToast("error", "Note!", data.message);
				}
			}
		});
	});
	
	$("#update_logo").change(function(){
		readURL(this, $("#update_logo_view"));
	});
	
	$("#update_logo_small").change(function(){
		readURL(this, $("#update_logo_small_view"));
	});
	
	/* Deactivate company button */
	$('#table-grid').delegate(".btnDeact","click", function(){
		$.LoadingOverlay("show");
		let company_code = $(this).data('company_code')
		let company_status = $(this).data('company_status')

		$.ajax({
	  		type : 'post'
	  		, url : base_url+'Main_dev_settings/get_company_details'
	  		, data : { 
				'company_code' : company_code
			}
	  		, success : function(data){
	  			var res = data.result
				if (data.success == 1) 
				{
  					$(".del_comp_id").val(company_code)
					$(".del_comp_name").text(res[0].company_name)
					$(".del_comp_status").val(company_status)
				}
				  
				$.LoadingOverlay("hide")
				$('#deleteCompanyModal').modal('show')
	  		}
		});
	});

	/* Set deactivate status to user */
	$('#delete_form').on('submit', function(e){
		e.preventDefault();

        var serialize = $(this).serialize();

		$.ajax({
			type : 'post'
			, url : base_url+'Main_dev_settings/set_company_status'
			, data : serialize
			, dataType : 'JSON'
			, success : function(data) {
				if (data.success == 1) 
				{
					//toastMessage('Success', data.message, 'success');
					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);
					fillDatatable("", ""); //refresh table
					$('#deleteCompanyModal').modal('hide'); //close modal
				}
				else 
				{
					//toastMessage('Note', data.message, 'error');
					showCpToast("error", "Note!", data.message);
				}
			}
		});
	});

	/* Reactivate company button */
	$(document).on('click', '.btnReactivate', function(){
		$.LoadingOverlay("show");
		let company_code = $(this).data('company_code')
		let company_status = $(this).data('company_status')

		$.ajax({
	  		type : 'post'
	  		, url : base_url+'Main_dev_settings/get_company_details'
	  		, data : { 
				'company_code' : company_code
			}
	  		, success : function(data){
	  			var res = data.result
				if (data.success == 1) 
				{
  					$(".react_comp_id").val(company_code)
					$(".react_comp_name").text(res[0].company_name)
					$(".react_comp_status").val(company_status)
				}
				  
				$.LoadingOverlay("hide")
				$('#reactCompanyModal').modal('show')
	  		}
		});
	})

	/* Set deactivate status to user */
	$('#reactivate_form').on('submit', function(e){
		e.preventDefault();

		var serialize = $(this).serialize();

		$.ajax({
			type : 'post'
			, url : base_url+'Main_dev_settings/set_company_status'
			, data : serialize
			, dataType : 'JSON'
			, success : function(data) {
				if (data.success == 1) 
				{
					//toastMessage('Success', data.message, 'success');
					showCpToast("success", "Success!", data.message);
        			setTimeout(function(){location.reload()}, 2000);

					fillDatatable("", ""); //refresh table
					$('#reactCompanyModal').modal('hide'); //close modal
				}
				else 
				{
					//toastMessage('Note', data.message, 'error');
					showCpToast("error", "Note!", data.message);
				}
			}
		});
	});

	$('#table-grid').delegate(".btnUsers", "click", function(){
		url_company_code = $(this).data("company_code");
		window.open(base_url+"Main_dev_settings/company_users/" + token + "/" + url_company_code, '_self');
	});
	
});