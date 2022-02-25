$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	
	// start - for loading a table
	function fillDatatable(){
        var _name = $("input[name='_name']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			"destroy": true,
			"serverSide": true,
			"responsive": true,
			"columnDefs": [
				{ targets: 2, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				type: "post",
				url:base_url+"settings/announcement/announcement_table", // json datasource
				data:{
                    "_name": _name
				}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table


	// start - for search purposes
	$('#btnSearch').click(function(e){
		e.preventDefault();
		fillDatatable();
	});

	$("#search_clear_btn").click(function(e){
		$(".search-input-text").val("");
		fillDatatable();
	});
	// end - for search purposes


	// start - edit function
	let id;
	$('#table-grid').delegate(".action_edit", "click", function(){
		id = $(this).data('value');
		$.ajax({
			type:'post',
			url:base_url+'settings/announcement/get_data',
			data:{'id':id},
			success:function(data){
				const res = data[0];
				$("textarea[name='_edit_announcement']").val(res.c_shop_main_announcement);
			}
		});
	});

	$('#edit_announcement_form').submit(function(e){
		e.preventDefault();
		const form_data = $(this).serializeArray();
		form_data.push({name: 'id', value: id});
		if (validateInputs(form_data)) {
			$.ajax({
				type: "post",
				url: base_url + "settings/announcement/update_data",
				data: form_data,
				success: function(data) {
					if (data.success == 1) {
						fillDatatable();
						//showToast('success', data.message);
						showCpToast("success", "Success!", data.message);
						setTimeout(function(){location.reload()}, 2000);
					}
					else {
						//showToast('note', data.message);
						showCpToast("info", "Note!", data.message);
					}

					$("#edit_modal").modal('hide');
				},
				error: function(){
					//showToast('note', 'Something went wrong, Please Try again!');
					showCpToast("info", "Note!", 'Something went wrong, Please Try again!');
				}
			});
		}
		else {
			//showToast('note', 'Please fill-up all fields');
			showCpToast("info", "Note!", 'Please fill-up all fields');
		}
		
	});
	// end - edit function

	function validateInputs(data)
	{
		for (var i = 0; i < data.length; i++) {
			if (data[i].value == "") {
				return false;
			}
		}

		return true;
	}

	/*
	 * type: success / note
	 */
	// function showToast(type, message)
	// {
	// 	if (type == "success") {
	// 		$.toast({
	// 			heading: 'Success',
	// 			text: message,
	// 			icon: 'success',
	// 			loader: false,  
	// 			stack: false,
	// 			position: 'top-center', 
	// 			bgColor: '#5cb85c',
	// 			textColor: 'white',
	// 			allowToastClose: false,
	// 			hideAfter: 10000
	// 		});
	// 	}
	// 	else if (type == "note") {
	// 		$.toast({
	// 			heading: 'Note',
	// 			text: message,
	// 			icon: 'info',
	// 			loader: false,   
	// 			stack: false,
	// 			position: 'top-center',  
	// 			bgColor: '#FFA500',
	// 			textColor: 'white'        
	// 		});
	// 	}
	// }


	$("#search_hideshow_btn").click(function(e){
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if(!visibility){
			//visible
			$("#search_hideshow_btn").html('&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		}else{
			//not visible
			$("#search_hideshow_btn").html('Show Search <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
   		}

		$("#card-header_search").slideToggle("slow");
	});
});