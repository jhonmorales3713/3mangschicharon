$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	function fillDatatable(companyCode, projectName, issueType, status, datefrom, dateto) {
		var dataTable = $('#table-grid').DataTable({
			"pageLength": 10,
			"destroy": true,
			"serverSide": true,
			"order": [[ 4, "asc" ]],
			"columnDefs": [
				{ targets: 5, orderable: false, "sClass":"text-center" }
			],
			"ajax":{
				url :base_url+"Main_cs/client_request_table", // json datasource
				type: "post",  // method  , by default get
				data: {
					'companyCode' : companyCode,
					'projectName' : projectName,
					'issueType' : issueType,
					'status' : status,
					'datefrom' : datefrom,
					'dateto' : dateto
				},
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

	fillDatatable("", "", "", "", "", "");

	$(".btnSearch").on("click", function(){
		companyCode = $("#companyCode").val();
		projectName = $("#projectName").val();
		issueType = $("#issueType").val();
		status = $("#status").val();
		datefrom = $("#datefrom").val();
		dateto = $("#dateto").val();
		
		fillDatatable(companyCode, projectName, issueType, status, datefrom, dateto);
	});

	var issue_type = "1";
	$(".dp_click").click(function(){
		var html_data = $(this).html();
		var val_data = $(this).data('val');
		
		issue_type = val_data;
		$(".dp_active").html(html_data);
		$(".dp_active").attr('data-val', val_data);
	});

	$("#addRequestForm").submit(function(e){
		e.preventDefault();
		// alert(issue_type);

		var formData = new FormData(this);
		formData.append('issue_type', issue_type);

		$.ajax({
			url: base_url + 'main_page/contact_support_submit',
			method: 'POST',
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function (data) {
				$('#dropdownMenu2').addClass('disabled');
				$('#project_name').prop('disabled', true);
				$('#summary').prop('disabled', true);
				$('#description').prop('disabled', true);
				$('#attachments').prop('disabled', true);
				$('#contact_person').prop('disabled', true);
				$('#email_address').prop('disabled', true);
				$('#contact_no').prop('disabled', true);

				$.LoadingOverlay("show");
			},
			success: function (data) {
				$('#dropdownMenu2').removeClass('disabled');
				$('#project_name').prop('disabled', false);
				$('#summary').prop('disabled', false);
				$('#description').prop('disabled', false);
				$('#attachments').prop('disabled', false);
				$('#contact_person').prop('disabled', false);
				$('#email_address').prop('disabled', false);
				$('#contact_no').prop('disabled', false);

				$.LoadingOverlay("hide");

				if(data.success){
					$('#addRequestModal').modal('hide')

					//toastMessage('Success', data.result, 'info');
					showCpToast("success", "Success!", data.result);
          			setTimeout(function(){location.reload()}, 2000);

					fillDatatable("", "", "", "", "", "");
				} else{
					//toastMessage('Error', data.result, 'error');
					showCpToast("error", "Error!", data.result);
				}
			}
		});

	});
});

