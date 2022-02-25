$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	function fillDatatable(position) {
		var dataTable = $('#table-grid').DataTable({
			"pageLength": 10,
			"destroy": true,
			"serverSide": true,
			"columnDefs": [
				{ targets: 1, orderable: false, "sClass":"text-center" }
			],
			"destroy": true,
			"ajax":{
				url :base_url+"Main_settings/user_role_table", // json datasource
				type: "post",  // method  , by default get
				data: { "position" : position },
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

	fillDatatable("");
	
	$(".btnSearch").on("click", function(){
		position = $(".searchPosition").val();

		fillDatatable(position);
	});

	$(".checkbox-template2").click(function(){
		$("#acb_all").prop('checked', false);
	});
	$("#acb_all").click(function(){
		if ($(this).prop('checked') == true) {
			$(".checkbox-template2").prop('checked', true);
		}else{
			$(".checkbox-template2").prop('checked', false);
		}
	});

	$("#add_userrole_form").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		$.ajax({
			type:'post',
			url:base_url+'Main_settings/add_userrole',
			data:serial,
			success:function(data){
				if (data.success == 1) {
                    //toastMessage('Success', data.message, 'success');
                    showCpToast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);

					fillDatatable(""); //refresh datatable
					$("#add_userrole_modal").modal('hide');
                }
                else{
                    //toastMessage('Note', data.message, 'error');
                    showCpToast("error", "Note!", data.message);
				}
			}
		});
	});

	$("#table-grid").delegate(".btnDelete", "click", function(){
		var position_id = $(this).data('position_id');
		var position = $(this).data('position');

		$("#r_position_delete").text(position);
		$("#r_position_id_delete").val(position_id);
	});

	$("#table-grid").delegate(".btnEdit", "click", function(){
		$(".checkbox-template").prop('checked', false);
		$(".checkbox-template2").prop('checked', false);

		var position_id = $(this).data('position_id');
		var position = $(this).data('position');
		var access_nav = $(this).data('access_nav');
		var access_content_nav = $(this).data('access_content_nav'); //071618
		var ac_arr = access_content_nav.split(', '); //071618
		var ac_arr_len = ac_arr.length;
		for(var i = 0; i < ac_arr_len; i++){ //071618
			$(".cb_content_class").each(function(){ 
				if ($(this).val() == ac_arr[i]) {
					$(this).prop('checked', true);
				}
			});
			
		} //071618

		$("#r_position").val(position);
		
		$("#r_positionorig").val(position);

		$("#r_position_id").val(position_id);
		var cb_arr = access_nav.split(', ');
		var arr_len = cb_arr.length;
		var cb_home = $('#cb_home').val();
		var cb_sales = $('#cb_sales').val();
		var cb_purchases = $('#cb_purchases').val();
		var cb_inventory = $('#cb_inventory').val();
		var cb_entity = $('#cb_entity').val();
		var cb_manufacturing = $('#cb_manufacturing').val();
		var cb_accounts = $('#cb_accounts').val();
		var cb_settings = $('#cb_settings').val();
		var cb_packagecart = $('#cb_packagecart').val();
		var cb_reports = $('#cb_reports').val();
		var cb_qr = $('#cb_qr').val();
		var cb_ds = $('#cb_ds').val();

		for(var x = 0; x < arr_len; x++){
			// alert(cb_arr[x]);
			if (cb_arr[x] == "All Access") {
				$(".checkbox-template").prop('checked', true);
			}
			if (cb_home == cb_arr[x]) {
				$("#cb_home").prop('checked', true);
			}
			if (cb_sales == cb_arr[x]) {
				$("#cb_sales").prop('checked', true);
			}
			if (cb_purchases == cb_arr[x]) {
				$("#cb_purchases").prop('checked', true);
			}
			if (cb_inventory == cb_arr[x]) {
				$("#cb_inventory").prop('checked', true);
			}
			if (cb_entity == cb_arr[x]) {
				$("#cb_entity").prop('checked', true);
			}
			if (cb_manufacturing == cb_arr[x]) {
				$("#cb_manufacturing").prop('checked', true);
			}
			if (cb_accounts == cb_arr[x]) {
				$("#cb_accounts").prop('checked', true);
			}
			if (cb_settings == cb_arr[x]) {
				$("#cb_settings").prop('checked', true);
			}
			if (cb_packagecart == cb_arr[x]) {
				$("#cb_packagecart").prop('checked', true);
			}
			if (cb_reports == cb_arr[x]) {
				$("#cb_reports").prop('checked', true);
			}
			if (cb_qr == cb_arr[x]) {
				$("#cb_qr").prop('checked', true);
			}
			if (cb_ds == cb_arr[x]) {
				$("#cb_ds").prop('checked', true);
			}
		}	
	});

	$(".checkbox-template2").click(function(){
		$("#cb_all").prop('checked', false);
	});
	$("#cb_all").click(function(){
		if ($(this).prop('checked') == true) {
			$(".checkbox-template2").prop('checked', true);
		}else{
			$(".checkbox-template2").prop('checked', false);
		}
	});

	$("#edit_userrole_form").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		$.ajax({
			type:'post',
			url:base_url+'Main_settings/edit_userrole',
			data:serial,
			success:function(data){
                if (data.success == 1) {
                    //toastMessage('Success', data.message, 'success');

                    showCpToast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);

                    fillDatatable(""); //refresh datatable
                    $("#add_userrole_modal").modal('hide');
                }
                else{
                	showCpToast("error", "Note!", data.message);
                    //toastMessage('Note', data.message, 'error');
                }
            }
		});
	});


	$("#delete_userrole_form").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		$.ajax({
			type:'post',
			url:base_url+'Main_settings/delete_userrole',
			data:serial,
			success:function(data){
                if (data.success == 1) {
                    //toastMessage('Success', data.message, 'success');
                    showCpToast("success", "Success!", data.message);
          			setTimeout(function(){location.reload()}, 2000);

                    fillDatatable(""); //refresh datatable
                    $("#add_userrole_modal").modal('hide');
                }
                else{
                    //toastMessage('Note', data.message, 'error');
                    showCpToast("error", "Note!", data.message);
                }
            }
		});
	});
});