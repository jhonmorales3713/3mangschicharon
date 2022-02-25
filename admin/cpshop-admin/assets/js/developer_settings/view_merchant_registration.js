
$(function(){
    var ini     = $("body").data('ini');
	var base_url    = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    // var merchant_logo_global = null;
    // var merchant_banner_global = null;
	var shop_url = $("body").data('shop_url');
    var token    = $("body").data('token');
	var url_ref_num = $('#url_ref_num').val();
    socmed_counter = $("#socmed_counter").val();

    

    checkGoogleMap();

    $('#approveBtn').click(function(e){
        $.LoadingOverlay("show");
        $.LoadingOverlay("hide");
        $('#approveModal').modal();
    });

    $('#saveChangesBtn').click(function(e){
        $.LoadingOverlay("show");
        $.LoadingOverlay("hide");
        $('#saveModal').modal();
    });

    $('#declineBtn').click(function(e){
        $.LoadingOverlay("show");
        $.LoadingOverlay("hide");
        $('#declineModal').modal();
    });

    $('#saveBtn').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
      
        var id              = $(this).data('app_id');
        // var formData = new FormData($('#merchant_form')[0]);
        var form_data = new FormData($('#merchant_form')[0]);    
        form_data.append('file_container', get_img_file('file_container'));
        form_data.append('file_container_banner', get_img_file('file_container_banner'));

        if(checkInputs("#merchant_form") == 0){
            $.ajax({
                type: 'post',
                url: base_url+"developer_settings/Shops_merchant_registration/updateApplication",
                data: form_data,
                processData: false,
                contentType: false,
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    if(json_data.success){
                        $('#saveModal').modal('hide');
                        // sys_toast_success(json_data.message);
                        // location.reload();
                        showCpToast("success", "Success!", "Application saved successfully.");
                        setTimeout(function(){location.reload()}, 2000);
                    }
                    else{
                        //sys_toast_warning(json_data.message);
                        showCpToast("warning", "Warning!", json_data.message);
                        // location.reload();
                        $('#saveModal').modal('hide');
                    }
                   
                },
                error: function(error){
                    //sys_toast_error('Something went wrong. Please try again.');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                }
            });
        }else{
            //sys_toast_warning('Please input required fields.');
            showCpToast("warning", "Warning!", 'Please input required fields.');
            $.LoadingOverlay("hide");
            $('#saveModal').modal('hide');
        }
        

    });

    

    $('#proceedBtn').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var id                = $(this).data('app_id');


        var save = 1;
        var merchant_comrate = $('#entry-merchant-comrate').val() / 100;
        var merchant_comrate = merchant_comrate / 2;
        var f_startup = $('#entry-f_startup').val() / 100;
        var f_jc      = $('#entry-f_jc').val() / 100;
        var f_mcjr    = $('#entry-f_mcjr').val() / 100;
        var f_mc      = $('#entry-f_mc').val() / 100;
        var f_mcsuper = $('#entry-f_mcsuper').val() / 100;
        var f_mcmega  = $('#entry-f_mcmega').val() / 100;
        var f_others  = $('#entry-f_others').val() / 100;
        if(f_startup > merchant_comrate || f_jc > merchant_comrate || f_mcjr > merchant_comrate || f_mc > merchant_comrate || f_mcsuper >  merchant_comrate || f_mcmega > merchant_comrate || f_others > merchant_comrate){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Percentage of Account Type Commision Rate should not be more than to 50% of Merchant Commission Rate.');
            showCpToast("warning", "Warning!", 'Percentage of Account Type Commision Rate should not be more than to 50% of Merchant Commission Rate.');
        } else if(merchant_comrate == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter merchant Commission rate.');
            showCpToast("warning", "Warning!", 'Please enter merchant Commission rate.');
        }
        else if(merchant_comrate <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Merchant Commission Rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'Merchant Commission Rate must not be equal to 0.');
        }
        else if(f_startup == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter Startup.');
            showCpToast("warning", "Warning!", 'Please enter Startup.');
        }
        else if(f_startup <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Startup Rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'Startup Rate must not be equal to 0.');
        }
        else if(f_jc == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter JC.');
            showCpToast("warning", "Warning!", 'Please enter JC.');
        }
        else if(f_jc <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('JC rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'JC rate must not be equal to 0.');
        }
        else if(f_mcjr == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter MCJR.');
            showCpToast("warning", "Warning!", 'Please enter MCJR.');
        }
        else if(f_mcjr <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('MCJR rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'MCJR rate must not be equal to 0.');
        }
        else if(f_mc == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter MC.');
            showCpToast("warning", "Warning!", 'Please enter MC.');
        }
        else if(f_mc <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('MC rate  must not be equal to 0.');
            showCpToast("warning", "Warning!", 'MC rate  must not be equal to 0.');
        }
        else if(f_mcsuper == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter MCSUPER.');
            showCpToast("warning", "Warning!", 'Please enter MCSUPER.');
        }
        else if(f_mcsuper <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('MC rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'MC rate must not be equal to 0.');
        }
        else if(f_mcmega == ''){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Please enter MCMEGA.');
            showCpToast("warning", "Warning!", 'Please enter MCMEGA.');
        }
        else if(f_mcmega <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('MCMEGA rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'MCMEGA rate must not be equal to 0.');
        }
        else if(f_others <= 0){
            $.LoadingOverlay("hide");
            $('#approveModal').modal('hide');
            //sys_toast_warning('Others rate must not be equal to 0.');
            showCpToast("warning", "Warning!", 'Others rate must not be equal to 0.');
        }
        else{
            if(f_others == ''){
                $.LoadingOverlay("hide");
                $('#approveModal').modal('hide');
                //sys_toast_warning('Please enter Others.');
                showCpToast("warning", "Warning!", 'Please enter Others.');
            }

                $.ajax({
                    type: 'post',
                    url: base_url+"developer_settings/Shops_merchant_registration/approveApplication",
                    data:{
                        'id': id,
                        'merchant_comrate':merchant_comrate,
                        'f_startup':f_startup,
                        'f_jc':f_jc,
                        'f_mcjr':f_mcjr,
                        'f_mc':f_mc,
                        'f_mcsuper':f_mcsuper,
                        'f_mcmega':f_mcmega,
                        'f_others':f_others,
                        // 'ratetype': ratetype,
                        // 'rate': rate,
                        // 'invtreshold': invtreshold,
                        // 'allowed_unful': allowed_unful,
                        // 'set_allowpickup': set_allowpickup,
                        
                    },
                    success:function(data){
                        $.LoadingOverlay("hide");
                        var json_data = JSON.parse(data);
                        if(json_data.success){
                            $('#approveModal').modal('hide');
                            // sys_toast_success(json_data.message);
                            showCpToast("success", "Success!", "Merchant Application has been approved.");
                            setTimeout(function(){location.reload()}, 2000);
                        }
                        else{
                            //sys_toast_warning(json_data.message);
                            showCpToast("warning", "Warning!", json_data.message);
                            $('#approveModal').modal('hide');
                            // location.reload();
                        }
                    
                    },
                    error: function(error){
                        //sys_toast_error('Something went wrong. Please try again.');
                        showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                    }
                });
    }
       
    });

    $('#proceedDecBtn').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var id     = $(this).data('app_id');
        var reason = $('#dec_reason').val();
   
        if(reason != ''){
         
             $.ajax({
                type: 'post',
                url: base_url+"developer_settings/Shops_merchant_registration/declineApplication",
                data:{
                    'id': id,
                    'reason': reason
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    if(json_data.success){
                        $('#declineModal').modal('hide');
                        $('#dec_reason').val('');
                        // sys_toast_success(json_data.message);
                        // location.reload();
                        showCpToast("success", "Success!", "Merchant Application has been successfully removed.");
                        setTimeout(function(){location.reload()}, 2000);
                    }
                    else{
                        //sys_toast_warning(json_data.message);
                        showCpToast("warning", "Warning!", json_data.message);
                        // location.reload();
                    }
                    
                },
                error: function(error){
                    //sys_toast_error('Something went wrong. Please try again.');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                }
            });
        }else{
            $.LoadingOverlay("hide");
            //sys_toast_warning('Please enter notes.');
            showCpToast("warning", "Warning!", 'Please enter notes.');
        }
      
    
    });


    $("#entry-ratetype").change(function(){
		if($(this).val() == 'p'){
            $("#entry-rate").attr("max","1");
            $("#entry-rate").attr("placeholder","1.00");
        }
        else if($(this).val() == 'f'){
            $("#entry-rate").removeAttr("max");
            $("#entry-rate").attr("placeholder","100.00");
        }
	});

    $("#pin_address").keypress(function(){
        $('#map').show(100); 
    });

    function checkGoogleMap(){

        var latitude  = $('#loc_latitude').val();
        var longitude = $('#loc_longitude').val();
        if(latitude == '14.594432888198051' && longitude == '121.01474704470128'){
            $('#map').hide(100);
            setTimeout(function(){ $('#pin_address').val(''); }, 4000);
        }
        else{
            $('#map').show(100); 
        }
    }

    function checkShopActve(){
        var shop_status  = $('#shop_status').val();

        if(shop_status != ''){
            //sys_toast_warning('Order cannot be process. Shop is not active anymore.');
            showCpToast("warning", "Warning!", 'Order cannot be process. Shop is not active anymore.');
            return 0;
        }
        else{
            return 1;
        }
    }

    $("#up_regCode").change(function() {
        $.LoadingOverlay("show"); 
        $("#up_citymunCode option").remove();
        $("#up_provCode option").remove();
        regCode = $(this).val();

        $.ajax({
            type:'post',
            url:base_url+'developer_settings/Shops_merchant_registration/get_province',
            data:{
                'regCode': $(this).val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                var json_data = JSON.parse(data);

                if(json_data.success){
                    $('#up_citymunCode')
                        .append($("<option></option>")
                        .attr("value", "")
                        .text('Select Municipality'));

                    $.each(json_data.data, function(key, value) {
                        $('#up_citymunCode')
                                .append($("<option></option>")
                                .attr("value", value.citymunCode)
                                .text(value.citymunDesc));
                      
                    });

                    $('#up_provCode')
                        .append($("<option></option>")
                        .attr("value", "")
                        .text('Select Province'));  

                    provID = "";
                    $.each(json_data.data, function(key, value) {
                        if(provID != value.provCode){
                            $('#up_provCode')
                                    .append($("<option></option>")
                                    .attr("value", value.provCode)
                                    .text(value.provDesc));  
                        }
                        provID = value.provCode;
                    });
                    // $('#up_citymunCode').prop('disabled', false);
                }else{
                    //sys_toast_warning('No data found');
                    showCpToast("warning", "Warning!", 'No data found');
                }
            },
            error: function(error){
                $.LoadingOverlay("hide"); 
                //sys_toast_error('Error');
                showCpToast("error", "Error!", 'Error');
            }
        });

    });

    $("#up_provCode").change(function() {
        $.LoadingOverlay("show"); 
        $("#up_citymunCode option").remove();
        // $("#up_regCode option").remove();
        provCode = $(this).val();

        $.ajax({
            type:'post',
            url:base_url+'developer_settings/Shops_merchant_registration/get_citymun',
            data:{
                'provCode': $(this).val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                var json_data = JSON.parse(data);

                if(json_data.success){
                    $('#up_citymunCode')
                        .append($("<option></option>")
                        .attr("value", "")
                        .text("Select Municipality"));

                    $.each(json_data.data, function(key, value) {
                        $('#up_citymunCode')
                            .append($("<option></option>")
                                .attr("value", value.citymunCode)
                                .text(value.citymunDesc));
                          
                    });

                    // $('#up_citymunCode').prop('disabled', false);

                }else{
                    //sys_toast_warning('No data found');
                    showCpToast("warning", "Warning!", 'No data found');
                }
            },
            error: function(error){
                $.LoadingOverlay("hide"); 
                //sys_toast_error('Error');
                showCpToast("error", "Error!", 'Error');
            }
        });

    });

    $(".removeBtn").on('click', function(){
        id_field = $(this).data('value');

        $(".up_socmed_"+id_field).remove();
    });

    $(document).on('click', ".removeBtn2", function() {
        id_field = $(this).data('value');

        $(".up_socmed_"+id_field).remove();
    });

    $(".addBtn").click(function() {
        socmed_counter = parseInt(socmed_counter) + 1;
        $('.additionalSocmedFieldsDiv').append("<div class='input-group mb-3 up_socmed_"+socmed_counter+"'><input type='text' class='form-control mb-2 up_socmed_"+socmed_counter+"' placeholder='Enter social media link' id='up_socmed' name='up_socmed[]'><div class='input-group-append'><button class='btn btn-danger removeBtn2 up_socmed_"+socmed_counter+"' data-value='"+socmed_counter+"' type='button'><i class='fa fa-minus'></i></button></div></div>");
        
    });


    //////////////////////////////////////////////////
    var renderImages = (input, placeToInsertImagePreview) => {
        if (input.files) {  
            console.log(input.files);
            var filesAmount = input.files.length;
            if(input.files.length){
                //User choose a picture
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    var image_size = input.files[i].size;
                    var current_file = input.files[i];

                     //must be jpg/png type, and not greater than 2mb
                    if(!hasExtension(input.value,['.jpg', '.png','.JPG','.PNG','.jpeg','.JPEG'])){
                        //messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        showCpToast("warning", "Warning!", 'Invalid file type, Only JPG/PNG are allowed');
                        $('#file_container').val("");
                        $('#file_description').text('Choose file');
                    }else{
                        if(image_size > 3000000){
                           // messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
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

                                    if((image.width != 200) && (image.height != 200)){
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

    $('#file_container').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files(countFiles, this);//param 1 file count, param 2 the file container/input
    });

    function prep_files(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-logo', 'remove');
    	$( ".img_preview_container" ).empty();
        renderImages(file_container, 'div.img_preview_container');
        //$('#main_logo_checker').val('true');
        $.LoadingOverlay("hide");
    }
//////////////////////////////////////////////////////////////////////////////////

    var renderImages_banner = (input, placeToInsertImagePreview) => {
        if (input.files) {  
            var filesAmount = input.files.length;
            if(input.files.length){
                //User choose a picture
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    var image_size = input.files[i].size;
                    var current_file = input.files[i];
                    
                         //must be jpg/png type, and not greater than 2mb
                     if(!hasExtension(input.value,['.jpg', '.png','.JPG','.PNG','.jpeg','.JPEG'])){
                        //messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        showCpToast("warning", "Warning!", 'Invalid file type, Only JPG/PNG are allowed');
                        $('#file_container_banner').val("");
                        $('#file_description_banner').text('Choose file');
                    }else{
                        if(image_size > 3000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-banner', 'show');
                            $('.img_preview_container_banner').hide('slow');
                            $('#file_container_banner').val("");
                            $('#file_description_banner').text('Choose file');
                        }else{                        
                            reader.onload = function(event) {
                                var image = new Image();
                                image.src = reader.result;
                                image.onload = function() {
                                     console.log(image.width);
                                     console.log(image.height);
                                    if(image.width != 1500){
                                        //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                        showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                        uploadthumbnail('imgthumbnail-banner', 'show');
                                        $('.img_preview_container_banner').hide();
                                        $('#file_container_banner').val("");
                                        $('#file_description_banner').text('Choose file');
                                    }
                                    else if(image.height != 400){
                                        //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                        showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                        uploadthumbnail('imgthumbnail-banner', 'show');
                                        $('.img_preview_container_banner').hide();
                                        $('#file_container_banner').val("");
                                        $('#file_description_banner').text('Choose file');
                                    }
                                
                                };
                                $($.parseHTML('<img id="product_preview_banner" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#file_description_banner').text(filesAmount+' Attached Image(s)');
                            $('.img_preview_container_banner').show('slow');
                        }
                    }
                }
            }else{
                //User clicked cancel
                uploadthumbnail('imgthumbnail-banner', 'show');
                $('.img_preview_container_banner').hide('slow');
                $('#file_description_banner').text('Choose file');
            }
        };
    }

    $('#file_container_banner').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files_banner(countFiles, this);//param 1 file count, param 2 the file container/input
    });



    function prep_files_banner(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-banner', 'remove');
        $( ".img_preview_container_banner" ).empty();
        renderImages_banner(file_container, 'div.img_preview_container_banner');
        //$('#main_logo_checker').val('true');
        $.LoadingOverlay("hide");
    }

    ////////////////////////////////////////////////////////////////


    function hasExtension(file, exts) {
        var fileName = file;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }

    function get_img_file(element_id){
        var fileInputs = $('#'+element_id);
        console.log(fileInputs[0].files);
        return fileInputs[0].files;
    }

    function uploadthumbnail(imagetoclear, action=''){
        if(action == 'show'){
            $("#"+imagetoclear).attr('hidden', false);
        }else{
            $("#"+imagetoclear).attr('hidden', true);
        }
    }


    if(ini == 'toktokmall'){
        $("#entry-merchant-comrate").keyup(function(e) { 
            //  alert('test');
            rate = $(this).val();
            console.log(rate);
            refcomratePopulate(rate);
        });
    
        function refcomratePopulate(rate){
            c_startup = $('#c_startup').val();
            c_jc      = $('#c_jc').val();
            c_mcjr    = $('#c_mcjr').val();
            c_mc      = $('#c_mc').val();
            c_mcsuper = $('#c_mcsuper').val();
            c_mcmega  = $('#c_mcmega').val();
            c_others  = $('#c_others').val();
            c_ofps    = $('#c_ofps').val();
    
            f_startup = (rate/100) * c_ofps;
            f_startup = parseFloat((f_startup * c_startup) * 100);
    
            f_jc = (rate/100) * c_ofps;
            f_jc = parseFloat((f_jc * c_jc) * 100);
    
            f_mcjr = (rate/100) * c_ofps;
            f_mcjr = parseFloat((f_mcjr * c_mcjr) * 100);
    
            f_mc = (rate/100) * c_ofps;
            f_mc = parseFloat((f_mc * c_mc) * 100);
    
            f_mcsuper = (rate/100) * c_ofps;
            f_mcsuper = parseFloat((f_mcsuper * c_mcsuper) * 100);
    
            f_mcmega = (rate/100) * c_ofps;
            f_mcmega = parseFloat((f_mcmega * c_mcmega) * 100);
    
            f_others = (rate/100) * c_ofps;
            f_others = parseFloat((f_others * c_others) * 100);
    
            $('#entry-f_startup').val(f_startup.toFixed(2));
            $('#entry-f_jc').val(f_jc.toFixed(2));
            $('#entry-f_mcjr').val(f_mcjr.toFixed(2));
            $('#entry-f_mc').val(f_mc.toFixed(2));
            $('#entry-f_mcsuper').val(f_mcsuper.toFixed(2));
            $('#entry-f_mcmega').val(f_mcmega.toFixed(2));
            $('#entry-f_others').val(f_others.toFixed(2));
        }
    }

});



$('#declineButton').click(function(e){
    $('#dec_reason').val('');
    // alert('test');
});



