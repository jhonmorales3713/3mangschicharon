$(function () {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var s3bucket_url = $("body").data('s3bucket_url');

    // start - for loading a table
    function fillDatatable() {
        var _name = $("input[name='_name']").val();

        var dataTable = $('#table-grid').DataTable({
            "processing": false,
            destroy: true,
            "serverSide": true,
            searching: false,
            responsive: true,
            "columnDefs": [
                { targets: 1, orderable: false, "sClass": "text-center" },
                { targets: 2, orderable: false, "sClass": "text-center" },
                { targets: 3, orderable: false, "sClass": "text-center" },
                //{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
            ],
            "ajax": {
                type: "post",
                url: base_url + "promotion/Main_promotion/campaign_type_list", // json datasource
                data: { '_name': _name }, // serialized dont work, idkw
                beforeSend: function (data) {
                    $.LoadingOverlay("show");
                },
                complete: function (res) {
                    var filter = { '_name': _name };
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

    let delete_id;
    $('#table-grid').delegate(".action_delete", "click", function () {
        delete_id = $(this).data('value');
    });

    $("#feat_campaign").on('click', function(e){
        e.preventDefault();
        $.ajax({
            type: 'get',
            url: base_url + 'promotion/Main_promotion/featured_campaign_list',
            dataType: 'json',
            success: function(data) {
                var list = "";
                for (let i = 0; i < data.length; i++) {
                    list += "<option value=\""+ data[i]['id'] +"\">"+ data[i]['name'] +"</option>";
                }
                $("#campaign_type_div").append(list);
                $("#featured_modal").on("hidden.bs.modal", function() {
                    $("#campaign_type_div option").remove();
                });
            }
        });
    });

    $("#confirm_feature").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            type: "post",
            data: { id: $("#campaign_type_div").val() },
            url: base_url + 'promotion/Main_promotion/confirm_featured_campaign',
            dataType: 'json',
            success: function(data) {
                if (data) {
                    showCpToast("success", "Success!", "Featured Campaign successfully set.");
                    setTimeout(function(){location.reload()}, 2000);
                    // $.toast({
                    //     heading: 'Success',
                    //     text: 'Success',
                    //     icon: 'success',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#5cb85c',
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                } else {
                    showCpToast("info", "Note!", "Failed Selection");
                    // $.toast({
                    //     heading: 'Note',
                    //     text: 'Failed Selection',
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
                
                $("#featured_modal").modal("hide");
                setTimeout(function(){location.reload();},3000);
            }
        });
    });

    $("#delete_modal_confirm_btn").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'promotion/Main_promotion/campaign_type_delete_modal_confirm',
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

    // start - edit function
    let id;
    let prev_val = {};

    $('#table-grid').delegate(".action_edit", "click", function () {
        edit_id = $(this).data('value');

        $.ajax({
            type: 'post',
            url: base_url + 'promotion/Main_promotion/get_campaign_type_data',
            data: { 'edit_id': edit_id },
            success: function (data) {
                var result = data.result;
                if (data.success == 1) {
                    $("#edit_id").val(result['id']);
                    $("#edit_name").val(result['name']);

                    prev_val = {
                        'name':result['name']
                    }
                    
                    var upd_pic= base_url+'assets/img/placeholder-any.jpg';
                    if(result['promo_img']!==null && result['promo_img']!=""){
                        upd_pic=s3bucket_url+'assets/img/promo_img/'+result['promo_img'];
                    }             
                    var prod=`<div class="form-group">
                                    <div class="square imgthumbnail-logo" id="imgthumbnail-logo"><img src="${upd_pic}" style="max-width: 100%;max-height: 100%; "></div>
                                    <div class="img_preview_container_update square" style="display:none;"></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label col-form-label-sm">Upload Image</label>
                                    <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file_container_update" id="file_container_update">
                                            <label class="custom-file-label" id="file_description_update">Choose file</label>
                                        </div>
                                    </div>
                                </div>`;

                    $("#img-upload-update").empty();
                    $("#img-upload-update").append(prod);
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

    $("#update_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var _id = $('#edit_id').val();
        var _name = $('#edit_name').val();
        var fd = new FormData();
        var file = get_img_file('file_container_update');
        var filename="";
        if(typeof(file[0]) != "undefined" && file[0] !== null){
            filename=file[0].name;
        }
        
        fd.append("id", _id);
        fd.append("add_name", _name);
        fd.append("prev_val", JSON.stringify(prev_val));
        fd.append("filename", filename);
        fd.append("image", $("#file_container_update")[0].files[0]);

        if (_id != '' && _name != '') {
            $.ajax({
                type: 'post',
                url: base_url + 'promotion/Main_promotion/campaign_type_update_modal_confirm',
                data: fd,
                processData: false,
                contentType: false,
                async:false,
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

                        $('#edit_id').val('');
                        $('#edit_name').val('');

                        $('#edit_modal').modal('toggle'); //close modal
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
        } else {
            // $.toast({
            //     heading: 'Note',
            //     text: 'Please fill up all required fields',
            //     icon: 'info',
            //     loader: false,
            //     stack: false,
            //     position: 'top-center',
            //     bgColor: '#FFA500',
            //     textColor: 'white'
            // });
            showCpToast("info", "Note!", "Please fill up all required fields");
        }
    });

    $("#add_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var _add_name = $('#add_name').val();
        var fd = new FormData();
        var file = get_img_file('file_container');
        var filename="";
        if(typeof(file[0]) != "undefined" && file[0] !== null){
            filename=file[0].name;
        }

        fd.append("add_name", _add_name);
        fd.append("filename", filename);
        fd.append("image", $("#file_container")[0].files[0]);
        if (_add_name != '') {
            $.ajax({
                type: 'post',
                url: base_url + 'promotion/Main_promotion/campaign_type_add_modal_confirm',
                data: fd,
                processData: false,
                contentType: false,
                async:false,
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

                        $('#add_category').val('');
                        $('#add_name').val('');
                        $('#add_onmenu').val('');
                        $('#add_priority').val('');

                        $('#add_modal').modal('toggle'); //close modal
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
        } else {
            // $.toast({
            //     heading: 'Note',
            //     text: 'Please fill up all required fields',
            //     icon: 'info',
            //     loader: false,
            //     stack: false,
            //     position: 'top-center',
            //     bgColor: '#FFA500',
            //     textColor: 'white'
            // });
            showCpToast("info", "Note!", 'Please fill up all required fields');
        }
    });

    let save_id = "";
    let unsaved_id = "";

    $('#unsetFeadutedModal').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).attr('data-id');
        $(this).find('#id').val(id);
        unsaved_id = id;
    });


    $('#setFeadutedModal').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).attr('data-id');
        $(this).find('#id').val(id);
        saved_id = id;

    });

    $('#saveFeatureConfirm').click(function(){
        var id = saved_id;

        $.LoadingOverlay("Show");
            $.ajax({
                type: 'post',
                url: base_url+"promotion/Main_promotion/ct_set_to_all",
                dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
                data:{
                    'id': id,
    
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    if(json_data.success){
                        $('#setFeadutedModal').modal('hide');
                        showCpToast("success", "Added!", "Campaign Type set to all successfully.");
                        setTimeout(function(){location.reload()}, 2000);
                    }
                    else{
                        //sys_toast_warning(json_data.message);
                        showCpToast("warning", "Warning!", json_data.message);
                        $('#setFeadutedModal').modal('hide');
                    }
                    
                },
                error: function(error){
                    //sys_toast_error('Something went wrong. Please try again.');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                }
            });
                                       
    
    });
    
    
    $('#unsaveFeatureConfirm').click(function(){
    
        var id   = unsaved_id;

        $.LoadingOverlay("Show");
        $.ajax({
            type: 'post',
            url: base_url+"promotion/Main_promotion/ct_unset_to_all",
            dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
            data:{
                'id': id,
    
            },
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success){
                    $('#unsetFeadutedModal').modal('hide');
                    showCpToast("success", "Removed!", "Campaign Type unset to all removed successfully.");
                    setTimeout(function(){location.reload()}, 2000);
                }
                else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                    $('#unsetFeadutedModal').modal('hide');
                }
               
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });
    
    
    });

    let saved_id_promo = "";
    let unsaved_id_promo = "";

    $('#unsetFeadutedModalPromo').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).attr('data-id');
        $(this).find('#id').val(id);
        unsaved_id_promo = id;
    });


    $('#setFeadutedModalPromo').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).attr('data-id');
        $(this).find('#id').val(id);
        saved_id_promo = id;

    });

    $('#saveFeatureConfirmPromo').click(function(){
        var id = saved_id_promo;

        $.LoadingOverlay("Show");
            $.ajax({
                type: 'post',
                url: base_url+"promotion/Main_promotion/ct_set_to_all_promo",
                dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
                data:{
                    'id': id,
    
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    if(json_data.success){
                        $('#setFeadutedModal').modal('hide');
                        showCpToast("success", "Updated!", "Campaign Type loss promotion set up successfully update.");
                        setTimeout(function(){location.reload()}, 2000);
                    }
                    else{
                        //sys_toast_warning(json_data.message);
                        showCpToast("warning", "Warning!", json_data.message);
                        $('#setFeadutedModal').modal('hide');
                    }
                    
                },
                error: function(error){
                    //sys_toast_error('Something went wrong. Please try again.');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                }
            });
                                       
    
    });
    
    
    $('#unsaveFeatureConfirmPromo').click(function(){
    
        var id   = unsaved_id_promo;

        $.LoadingOverlay("Show");
        $.ajax({
            type: 'post',
            url: base_url+"promotion/Main_promotion/ct_unset_to_all_promo",
            dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
            data:{
                'id': id,
    
            },
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success){
                    $('#unsetFeadutedModal').modal('hide');
                    showCpToast("success", "Updated!", "Campaign Type loss promotion set up successfully update.");
                    setTimeout(function(){location.reload()}, 2000);
                }
                else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                    $('#unsetFeadutedModal').modal('hide');
                }
               
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });
    
    
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

                                    // if((image.width != 250) && (image.height != 250)){
                                    //     //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                    //     showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                    //     uploadthumbnail('imgthumbnail-logo', 'show');
                                    //     $('.img_preview_container').hide();
                                    //     $('#file_container').val("");
                                    //     $('#file_description').text('Choose file');
                                    // }
                                
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

                                    // if((image.width != 250) && (image.height != 250)){
                                    //     //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                    //     showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                    //     uploadthumbnail('imgthumbnail-logo', 'show');
                                    //     $('.img_preview_container_update').hide();
                                    //     $('#file_container_update').val("");
                                    //     $('#file_description_update').text('Choose file');
                                    // }
                                
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
            $("."+imagetoclear).attr('hidden', false);
        }else{
            $("."+imagetoclear).attr('hidden', true);
        }
    }

    $("#action_add").click(function (e) {
        var prod=`<div class="form-group">
                        <div class="square imgthumbnail-logo" id="imgthumbnail-logo"><img src="${base_url+'assets/img/placeholder-any.jpg'}" style="max-width: 100%;max-height: 100%; "></div>
                        <div class="img_preview_container square" style="display:none;"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label col-form-label-sm">Upload Image</label>
                        <div class="input-group" style="width:100%;">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="file_container" id="file_container">
                                <label class="custom-file-label" id="file_description">Choose file</label>
                            </div>
                        </div>
                    </div>`;

        $("#img-upload-add").empty();
        $("#img-upload-add").append(prod);
    });

    $(document).on('change', '#file_container', function(){
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files(countFiles, this);//param 1 file count, param 2 the file container/input
    });

    $(document).on('change', '#file_container_update', function(){
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files_update(countFiles, this);//param 1 file count, param 2 the file container/input
    });
});