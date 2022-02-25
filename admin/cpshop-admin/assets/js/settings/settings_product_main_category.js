$(function () {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var s3bucket_url = $("body").data('s3bucket_url'); //base_url came from built-in CI function base_url();

    // start - for loading a table
    function fillDatatable() {
        var _record_status = $("select[name='_record_status']").val();
        var _category = $("input[name='_category']").val();
        var _name = $("input[name='_name']").val();
        var _onmenu = $("select[name='_onmenu']").val();
        var _priority = $("input[name='_priority']").val();

        var dataTable = $('#table-grid').DataTable({
            "processing": false,
            destroy: true,
            "serverSide": true,
            searching: false,
            responsive: true,
            "columnDefs": [
                { targets: 5, orderable: false, "sClass": "text-center" },
                { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
            ],
            "ajax": {
                type: "post",
                url: base_url + "Main_settings/product_main_category_list", // json datasource
                data: { '_record_status': _record_status, '_category': _category, '_name': _name, '_onmenu': _onmenu, '_priority': _priority }, // serialized dont work, idkw
                beforeSend: function (data) {
                    $.LoadingOverlay("show");
                },
                complete: function (res) {
                    var filter = { '_record_status': _record_status, '_category': _category, '_name': _name, '_onmenu': _onmenu, '_priority': _priority };
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
                    $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display", "none");
                }
            }
        });
    }

    fillDatatable();
    // end - for loading a table

    // start - for search purposes

    $("#_record_status").change(function () {
        $("#btnSearch").click();
    });

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

    $("#search_clear_btn").click(function (e) {
        $(".search-input-text").val("");
        fillDatatable();
    })

    $(".enter_search").keypress(function (e) {
        if (e.keyCode === 13) {
            $("#btnSearch").click();
        }
    });

    $('#btnSearch').click(function (e) {
        e.preventDefault();
        fillDatatable();
    });
    // end - for search purposes

    let disable_id;
    let record_status;
    $('#table-grid').delegate(".action_disable", "click", function () {
        disable_id = $(this).data('value');
        record_status = $(this).data('record_status');

        if (record_status == 1) {
            $(".mtext_record_status").text("disable");
        } else if (record_status == 2) {
            $(".mtext_record_status").text("enable");
        }
    });

    let delete_id;
    $('#table-grid').delegate(".action_delete", "click", function () {
        delete_id = $(this).data('value');
    });

    $("#delete_modal_confirm_btn").click(function (e) {
        //alert(delete_id);
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/product_main_category_delete_modal_confirm',
            data: { 'delete_id': delete_id },
            success: function (data) {
                var res = data.result;
                if (data.success == 1) {
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
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $('#delete_modal').modal('toggle'); //close modal
                } else {
                    showCpToast("info", "Note!", data.message);
                    // $.toast({
                    //     heading: 'Note',
                    //     text: data.message,
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    });

    $("#disable_modal_confirm_btn").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/product_main_category_disable_modal_confirm',
            data: { 'disable_id': disable_id, 'record_status': record_status },
            success: function (data) {
                var res = data.result;
                if (data.success == 1) {
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
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $('#disable_modal').modal('toggle'); //close modal
                } else {
                    showCpToast("info", "Note!", data.message);
                    // $.toast({
                    //     heading: 'Note',
                    //     text: data.message,
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    });
    $('#add_record_form').submit(function (e) {
        //alert("go");
        e.preventDefault();
        $('#addCategory').attr('disabled', true);
        const form_data_2 = $(this).serializeArray();
        var form = $(this);
        var form_data = new FormData(form[0]);
        form_data.append('file_container', get_img_file('file_container'));

        if (validateInputs(form_data)) {
            $.ajax({
                type: "post",
                url: base_url + "Main_settings/save_product_main_category",
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $("#addCurrency").prop('disabled', true); 
                    $("#addCategory").text("Please wait...");
                },   
                success: function (data) {
                    if (data.success == 1) {
                        $.LoadingOverlay("hide");
                        // fillDatatable();
                        //showToast('success', data.message);
                        $('#addCategory').attr('disabled', false);
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

    
    
    // start - edit function
	let id;
	let prev_val = {};

    $('#table-grid').delegate(".action_edit", "click", function () {
        edit_id = $(this).data('value');

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_product_main_category_data',
            data: { 'edit_id': edit_id },
            success: function (data) {
                var result = data.result;
                if (data.success == 1) {
                
                    update_form(data.result);
                    
                } else {
                    showCpToast("info", "Note!", data.message);
                    // $.toast({
                    //     heading: 'Note',
                    //     text: data.message,
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    });

    function update_form(result){

        $('.category_img').empty();
        $('.category_img').append('<img id="category_img" src="'+s3bucket_url+'assets/img/main_category/'+result.parent_img+'" />');
        $("input[name='_edit_filename']").val(result.parent_img);

        $( "input[name*='edit_id']" ).val(result.id);
        $( "input[name*='edit_code']" ).val(result.parent_category_code);
        $( "input[name*='edit_name']" ).val(result.parent_category_name);
        $( "input[name*='edit_icon']" ).val(result.parent_icon);
        $( "select[name*='edit_onmenu']" ).val(result.on_menu);
        $( "input[name*='edit_priority']" ).val(result.priority);
        set_selected('edit-entry-subcategory', result.sub_category_id);
    }

    
    $('#update_record_form').submit(function (e) {
        //alert("go");
        e.preventDefault();
        $('#updateCategory').attr('disabled', true);
        const form_data_2 = $(this).serializeArray();
        var form = $(this);
        var form_data = new FormData(form[0]);
        form_data.append('file_container', get_img_file('file_container'));


        if (validateInputs(form_data)) {
            $.ajax({
                type: "post",
                url: base_url + "Main_settings/update_product_main_category",
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $("#updateCategory").prop('disabled', true); 
                    $("#updateCategory").text("Please wait...");
                },   
                success: function (data) {
                    if (data.success == 1) {
                        $.LoadingOverlay("hide");
                        // fillDatatable();
                        //showToast('success', data.message);
                        $('#updateCategory').attr('disabled', false);
                        //location.reload();

                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);

                    }
                    else {
                        //showToast('note', data.message);
                        showCpToast("info", "Note!", data.message);
                        $.LoadingOverlay("hide");
                    }

                    $("#edit_modal").modal('hide');
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

    $(".allownumber").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $(".allowdecimal").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    function set_selected(name, values){
        $.each(values.split(","), function(i,e){
            $("select[name*='"+name+"'] option[value='" + e + "']").prop("selected", true).select2().trigger('change');
        });
    }

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
                       // messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        showCpToast("warning", "Warning!", 'Invalid file type, Only JPG/PNG are allowed');
                        $('#file_container').val("");
                        $('#file_description').text('Choose file');
                    }else{

                        if(image_size > 3000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-logo', 'show');
                            $('.img_preview_container').hide('slow');
                            $('#file_container').val("");
                            $('#file_description').text('Choose file');
                        }
                         else{                        
                            reader.onload = function(event) {
                                var image = new Image();
                                image.src = reader.result;
                                image.onload = function() {

                                    if((image.width != 250) && (image.height != 250)){
                                        //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                        showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                        uploadthumbnail('imgthumbnail-logo', 'show');
                                        $('.img_preview_container').hide();
                                        $('#file_container').val("");
                                        $('#file_description').text('Choose file');
                                    }
                                
                                };
                                $($.parseHTML('<img id="product_preview" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
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
                        //messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        showCpToast("warning", "Warning!", 'Invalid file type, Only JPG/PNG are allowed');
                        $('#file_container_update').val("");
                        $('#file_description_update').text('Choose file');
                    }else{
                    
                        if(image_size > 3000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-logo', 'show');
                            $('.img_preview_container_update').hide('slow');
                            $('#file_container_update').val("");
                            $('#file_description_update').text('Choose file');
                        }
                         else{                        
                            reader.onload = function(event) {
                                var image = new Image();
                                image.src = reader.result;
                                image.onload = function() {

                                    if((image.width != 250) && (image.height != 250)){
                                        //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                        showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                        uploadthumbnail('imgthumbnail-logo', 'show');
                                        $('.img_preview_container_update').hide();
                                        $('#file_container_update').val("");
                                        $('#file_description_update').text('Choose file');
                                    }
                                
                                };
                                $($.parseHTML('<img id="product_preview" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
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
                $('.category_img').hide('slow');
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

    function validateInputs(data) {
        for (var i = 0; i < data.length; i++) {
            if (data[i].value == "") {
                return false;
            }
        }

        return true;
    }

    function showToast(type, message) {
        if (type == "success") {
            $.toast({
                heading: 'Success',
                text: message,
                icon: 'success',
                loader: false,
                stack: false,
                position: 'top-center',
                bgColor: '#5cb85c',
                textColor: 'white',
                allowToastClose: false,
                hideAfter: 10000
            });
        }
        else if (type == "note") {
            $.toast({
                heading: 'Note',
                text: message,
                icon: 'info',
                loader: false,
                stack: false,
                position: 'top-center',
                bgColor: '#FFA500',
                textColor: 'white'
            });
        }
    }
});




