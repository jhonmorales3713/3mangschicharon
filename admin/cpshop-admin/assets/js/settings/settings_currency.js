$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var s3bucket_url = $("body").data('s3bucket_url'); //base_url came from built-in CI function base_url();

	// start - for loading a table
	function fillDatatable() {
		var _record_status = $("select[name='_record_status']").val();
		var _code = $("input[name='_code']").val();
		var _country_name = $("input[name='_country_name']").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: true,
			"columnDefs": [
				{ targets: 6, orderable: false, "sClass": "text-center" },
				{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			"ajax": {
				type: "post",
				url: base_url + "settings/currency/currency_table", // json datasource
				data: {
					'_record_status': _record_status,
					'_code': _code,
					'_country_name': _country_name
				}, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (res) {
					var filter = {
						'_record_status': _record_status,
						'_code': _code,
						'_country_name': _country_name,
					};
					$.LoadingOverlay("hide"); 
					$('#_search').val(JSON.stringify(this.data));
					$('#_filter').val(JSON.stringify(filter));
					if (res.responseJSON.data.length > 0) {
						$('#btnExport').show();
					}else{
						$('#btnExport').hide();
					}
				},
				error: function () {  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display", "none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table


	// start - for search purposes
	$('#btnSearch').click(function (e) {
		e.preventDefault();
		fillDatatable();
	});

	$("#_record_status").change(function () {
		$("#btnSearch").click();
	});

	$("#search_clear_btn").click(function (e) {
		$(".search-input-text").val("");
		fillDatatable();
	});
	// end - for search purposes


	// start - add function
	$('#add_record_form').submit(function (e) {
		e.preventDefault();
		$('#addCurrency').attr('disabled', true);
		const form_data_2 = $(this).serializeArray();
		var form = $(this);
        var form_data = new FormData(form[0]);
        form_data.append('file_container', get_img_file('file_container'));
		phone_prefix      = $('.add_phone_prefix').val();
		phone_limit       = $('.add_phone_limit').val();
		total_count_phone = parseInt(digits_count(phone_prefix))+parseInt(phone_limit);
		if(total_count_phone > 15){
			showToast('note', 'Maximum digits of phone number is 15.');
			$('#addCurrency').attr('disabled', false);
		}
		else if (validateInputs(form_data)) {
			$.ajax({
				type: "post",
				url: base_url + "settings/currency/create_data",
				data: form_data,
				processData: false,
				contentType: false,
				beforeSend:function(data){
					$.LoadingOverlay("show");
					$("#addCurrency").prop('disabled', true); 
                    $("#addCurrency").text("Please wait...");
				},   
				success: function (data) {
					if (data.success == 1) {
						$.LoadingOverlay("hide");
						// fillDatatable();
						//showToast('success', data.message);
						$('#addCurrency').attr('disabled', false);
						//location.reload();

						showCpToast("success", "Success!", data.message);
						setTimeout(function(){location.reload()}, 2000);

					}
					else {
						//showToast('note', data.message);
						showCpToast("info", "Note!", data.message);
						$.LoadingOverlay("hide");
					}

					$("#add_modal").modal('hide');
					document.getElementById("add_record_form").reset();
				},
				error: function () {
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
	// end - add function

	// start - edit function
	let id;
	let prev_val = {};
	$('#table-grid').delegate(".action_edit", "click", function () {
		id = $(this).data('value');
		$.ajax({
			type: 'post',
			url: base_url + 'settings/currency/get_data',
			data: { 'id': id },
			success: function (data) {
				const res = data[0];
				prev_val = {
					'country_name' : res.country_name,
					'currency' : res.currency,
					'currency_symbol' : res.currency_symbol,
					'country_code' : res.country_code,
					'exchangerate_php_to_n' : res.exchangerate_php_to_n,
					'exchangerate_n_to_php' : res.exchangerate_n_to_php,
					'from_dts' : res.from_dts,
					'to_dts' : res.to_dts,
					'phone_prefix' : res.phone_prefix,
					'phone_limit' : res.phone_limit,
					'utc' : res.utc,
					'arrangement' : res.arrangement
				};

				$('.flag_img').empty();
				$('.flag_img').append('<img id="flag_img" src="'+s3bucket_url+'assets/img/flags/'+res.filename+'" />')

				$("input[name='_edit_country_name']").val(res.country_name);
				$("input[name='_edit_currency']").val(res.currency);
				$("input[name='_edit_currency_symbol']").val(res.currency_symbol);
				$("input[name='_edit_country_code']").val(res.country_code);
				$("input[name='_edit_exchangerate_php_to_n']").val(res.exchangerate_php_to_n);
				$("input[name='_edit_exchangerate_n_to_php']").val(res.exchangerate_n_to_php);
				$("input[name='_edit_from_dts']").val(res.from_dts);
				$("input[name='_edit_to_dts']").val(res.to_dts);
				$("input[name='_edit_phone_prefix']").val(res.phone_prefix);
				$("input[name='_edit_phone_limit']").val(res.phone_limit);
				$("input[name='_edit_utc']").val(res.utc);
				$("input[name='_edit_filename']").val(res.filename);
				$("input[name='_edit_arrangement']").val(res.arrangement);
			}
		});
	});

	$('#edit_record_form').submit(function (e) {
		e.preventDefault();
		// const form_data = $(this).serializeArray();
		
		var form = $(this);
		var form_data = new FormData(form[0]);
		form_data.append('id', id );
		form_data.append('prev_val', JSON.stringify(prev_val));
        form_data.append('file_container_update', get_img_file('file_container_update'));
		// console.log(form_data);
		phone_prefix = $('.edit_phone_prefix').val();
		phone_limit  = $('.edit_phone_limit').val();
		total_count_phone = parseInt(digits_count(phone_prefix))+parseInt(phone_limit);

		if(total_count_phone > 15){
			showToast('note', 'Maximum digits of phone number is 15.');
		}
		else if (validateInputs(form_data)) {
			$.ajax({
				type: "post",
				url: base_url + "settings/currency/update_data",
				data: form_data,
				processData: false,
				contentType: false,
				beforeSend:function(data){
					$.LoadingOverlay("show");
					$("#editCurrency").prop('disabled', true); 
				},   
				success: function (data) {
					if (data.success == 1) {
						$.LoadingOverlay("hide");
						$("#editCurrency").prop('disabled', false); 
						// fillDatatable();
						//showToast('success', data.message);
						$('.flag_img').empty();
						$('.img_preview_container_update').empty();
						// $('#file_container_update').val('')
						//location.reload();
						showCpToast("success", "Success!", data.message);
						setTimeout(function(){location.reload()}, 2000);
					}
					else {
						$.LoadingOverlay("hide");
						$("#editCurrency").prop('disabled', false); 
						//showToast('note', data.message);
						showCpToast("info", "Note!", data.message);
						$('.flag_img').empty();
						$('.img_preview_container_update').empty();
					}

					$("#edit_modal").modal('hide');
				},
				error: function () {
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

	// start - disable function
	let disable_id;
	let record_status;
	let record_name;
	$('#table-grid').delegate(".action_disable", "click", function () {
		disable_id = $(this).data('value');
		record_status = $(this).data('record_status');
		record_name = $(this).data('record_name');

		if (record_status == 1) {
			$(".mtext_record_status").text("disable");
		}
		else if (record_status == 2) {
			$(".mtext_record_status").text("enable");
		}
	});

	$("#disable_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'settings/currency/disable_data',
			data: { 'disable_id': disable_id, 'record_status': record_status, 'record_name' : record_name },
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable
					//showToast('success', data.message);
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					$('#disable_modal').modal('toggle'); //close modal
				} else {
					//showToast('note', data.message);
					showCpToast("info", "Note!", data.message);
				}
			}
		});
	});
	// end - disable function

	// start - delete function
	let delete_id = 0;
	$('#table-grid').delegate(".action_delete", "click", function () {
		delete_id = $(this).data('value');
		record_name = $(this).data('record_name');
	});

	$("#delete_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'settings/currency/delete_data',
			data: { 'delete_id': delete_id , 'record_name': record_name},
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable
					//showToast('success', data.message);
					showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);
					$('#delete_modal').modal('toggle'); //close modal
				} else {
					//showToast('note', data.message);
					showCpToast("info", "Note!", data.message);
				}
			}
		});
	});
	// end - delete function


	function validateInputs(data) {
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
	// function showToast(type, message) {
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


	$("#search_hideshow_btn").click(function (e) {
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		} else {
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
		}

		$("#card-header_search").slideToggle("slow");
	});

	var renderImages = (input, placeToInsertImagePreview) => {
        if (input.files) {  
            var filesAmount = input.files.length;
            if(input.files.length){
                //User choose a picture
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    var image_size = input.files[i].size;
                    var current_file = input.files[i];
                    
                                                //must be jpg/png type, and not greater than 2mb
                    if(!hasExtension(input.value,['.jpg', '.png','.JPG','.PNG'])){
                        messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        $('#file_container').val("");
                        $('#file_description').text('Choose file');
                    }else{
                        if(image_size > 2000000){
                            messageBox('Please enter with a valid size no larger than 2MB', 'Warning', 'warning');
                            uploadthumbnail('imgthumbnail-logo', 'show');
							$('.img_preview_container').hide('slow');
                            $('#file_container').val("");
                            $('#file_description').text('Choose file');
                        }else{                        
                            reader.onload = function(event) {
                                $($.parseHTML('<img id="product_preview" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
								$('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#file_description').text(filesAmount+' Attached Image(s)');
							$('.img_preview_container').show('slow');
                        }
                    }
                }
            }else{
                //User clicked cancel
                uploadthumbnail('imgthumbnail-logo', 'show');
                $('.img_preview_container').hide('slow');
                $('#file_description').text('Choose file');
            }
        };
	}
	
	var renderImages_update = (input, placeToInsertImagePreview) => {
        if (input.files) {  
            var filesAmount = input.files.length;
            if(input.files.length){
                //User choose a picture
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    var image_size = input.files[i].size;
                    var current_file = input.files[i];
                    
                                                //must be jpg/png type, and not greater than 2mb
                    if(!hasExtension(input.value,['.jpg', '.png','.JPG','.PNG'])){
                        messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        $('#file_container_update').val("");
                        $('#file_description_update').text('Choose file');
                    }else{
                        if(image_size > 2000000){
                            messageBox('Please enter with a valid size no larger than 2MB', 'Warning', 'warning');
                            uploadthumbnail('imgthumbnail-logo', 'show');
							$('.img_preview_container_update').hide('slow');
                            $('#file_container_update').val("");
                            $('#file_description_update').text('Choose file');
                        }else{                        
                            reader.onload = function(event) {
                                $($.parseHTML('<img id="product_preview" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
								$('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
								$('.flag_img').empty();
								
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#file_description_update').text(filesAmount+' Attached Image(s)');
							$('.img_preview_container_update').show('slow');
                        }
                    }
                }
            }else{
                //User clicked cancel
                uploadthumbnail('imgthumbnail-logo', 'show');
                $('.img_preview_container').hide('slow');
                $('#file_description').text('Choose file');
            }
        };
    }

    $('#file_container').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files(countFiles, this);//param 1 file count, param 2 the file container/input
	});
	
	$('#file_container_update').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files_update(countFiles, this);//param 1 file count, param 2 the file container/input
    });

    function prep_files(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-logo', 'remove');
    	$( ".img_preview_container" ).empty();
        renderImages(file_container, 'div.img_preview_container');
        //$('#main_logo_checker').val('true');
        $.LoadingOverlay("hide");
	}

	function prep_files_update(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-logo', 'remove');
    	$( ".img_preview_container_update" ).empty();
        renderImages_update(file_container, 'div.img_preview_container_update');
        //$('#main_logo_checker').val('true');
        $.LoadingOverlay("hide");
	}
	
	function hasExtension(file, exts) {
        var fileName = file;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }

    function get_img_file(element_id){
        var fileInputs = $('#'+element_id);
        return fileInputs[0].files;
    }

    function uploadthumbnail(imagetoclear, action=''){
        if(action == 'show'){
            $("#"+imagetoclear).attr('hidden', false);
        }else{
            $("#"+imagetoclear).attr('hidden', true);
        }
    }

	function digits_count(n) {
		var count = 0;
		if (n >= 1) ++count;
	  
		while (n / 10 >= 1) {
		  n /= 10;
		  ++count;
		}
	  
		return count;
	  }
});