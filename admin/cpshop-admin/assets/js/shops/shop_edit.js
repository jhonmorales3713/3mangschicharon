var ini          = $("body").data('ini');
check_featuredmerchantCheck();
check_whatsnewsmerchantCheck();



$('.whatsnew_div').hide(250);

$('#set_whatsnew_merchant').click(function(){
    if($(this).is(':unchecked')){
        $('.whatsnew_div').hide(250);
        $("#entry-feat-whatsnew-merchant-arrangement").val('').change();
    }else{

    }
});

$('#set_whatsnew_merchant').click(function(){
    if($(this).is(':checked')){
        $("#set_whatsnew_merchant").prop("checked", false);
        $('#show_whatsnew_merchant_modal').modal('show');
    }else{
        $('#show_whatsnew_merchant_modal').modal('hide');
      
    }
});




$('#entry-feat-whatsnew-merchant-arrangement').change(function(){ 
    var merchant_number = $(this).val();
    check_whatsnew_merchant(merchant_number)
});


function check_whatsnew_merchant(merchant_number)
{
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    //product_id  = $("#u_id").val()
   // var product_id = product_id;
  //  alert(product_id);
    $.ajax({
        method: "POST",
        url: base_url+'shops/Main_shops/check_whatsnew_merchant_arrangement/'+merchant_number,
            success: function(data){

               // alert(data);
                if(data == 1) {
                showCpToast("warning", "Warning!", 'This number is already selected...');             
                    // $.toast({
                    //     text: 'Warning!<br>This number is already selected...',
                    //     icon: 'info',
                    //     loader: false,  
                    //     stack: false,
                    //     position: 'top-center', 
                    //     bgColor: '#FFA500',
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $("#entry-feat-whatsnew-merchant-arrangement").val("").change();
                }else{
                   // alert('test');
                   // checkfeaturedproducts();
                }

            }
    });

}


$('#uncheck_rabutton_whatsnew').click(function(){
    $("#set_whatsnew_merchant").prop("checked", false);
    $("#entry-feat-whatsnew-merchant-arrangement").selectmenu('refresh');
   // $("#entry-feat-merchant-arrangement").val('').attr("Select Arrangement", "Select Arrangement");
   // $('#mycontrolId').val(myvalue).attr("selected", "selected");
});


$('body').delegate("#check_rabutton_whatsnew", "click", function(){
   shop_id  = $("#entry-id").val()
   check_Whatsnewmerchant(shop_id);
 });


function check_Whatsnewmerchant(shop_id)
{
var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var shop_id = shop_id;
// alert(product_id);
$.ajax({
    method: "POST",
    url: base_url+'shops/Main_shops/check_whatsnew_merchants/'+shop_id,
        success: function(data){

           // alert(data);
            if(data == 1) {
                showCpToast("warning", "Warning!", 'This Merchant is already included in What`s New Merchants'); 
                // $.toast({
                //     text: 'Warning!<br>This Merchant is already included in What`s New Merchants',
                //     icon: 'info',
                //     loader: false,  
                //     stack: false,
                //     position: 'top-center', 
                //     bgColor: '#FFA500',
                //     textColor: 'white',
                //     allowToastClose: false,
                //     hideAfter: 10000
                // });
             
             $("#set_whatsnew_merchant").prop("checked", true);
             $('#show_feature_merchant_modal').modal('hide');
            }else{
               // alert('test');
               checkWhatsNewmerchant();
            }

        }
});

}

function  checkWhatsNewmerchant()
{
    $.ajax({
        type:'post',
        url:base_url+'shops/Main_shops/get_whatsnew_merchant_count/',
        success:function(data){
            var res = data.result;
            if (data >= 16){
                showCpToast("warning", "Warning!", 'You have reached the maximum of 16 What`s New Merchants allowed'); 
                // $.toast({
                //     text: 'You have reached the maximum of 16 What`s New Merchants allowed',
                //     icon: 'info',
                //     loader: false,  
                //     stack: false,
                //     position: 'top-center', 
                //     bgColor: '#FFA500',
                //     textColor: 'white',
                //     allowToastClose: false,
                //     hideAfter: 10000
                // });
            }else{
                $("#set_whatsnew_merchant").prop("checked", true);
                $('#show_whatsnew_merchant_modal').modal('hide');
                $('.whatsnew_div').show(250);
            }
        }
    });
   
}

 
$('#set_whatsnew_merchant').click(function(){
    if($(this).is(':checked')){
        $("#set_whatsnew_merchant").prop("checked", false);
        $('#show_whatsnew_merchant_modal').modal('show');
        
    }else{
        $('#show_whatsnew_merchant_modal').modal('hide');
       
    }
});


$('#set_whatsnew_merchant').click(function(){
    if($(this).is(':unchecked')){
        $("#entry-feat-whatsnew-merchant-arrangement").val('').change();
        $("#img_preview_container_whatsnew").val('').change();
        $("#imgthumbnail-whatsnew").val('').change();
    }else{
    }
}); 


function check_whatsnewsmerchantCheck()
{

shop_id  = $("#entry-id").val()   
var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var shop_id = shop_id;
// alert(product_id);
    $.ajax({
        method: "POST",
        url: base_url+'shops/Main_shops/check_whatsnew_merchants/'+shop_id,
            success: function(data){

              //  alert(data);
                if(data == 1) {
                   $('.whatsnew_div').show(250);
                
                }else{
                   $('.whatsnew_div').hide(250);
                }

            }
    });

}

/// Set Featured Merchant
$('.contsellingdiv').hide(250);

$('#set_advertisement').click(function(){
    if($(this).is(':unchecked')){
        $('.contsellingdiv').hide(250);
        $("#entry-feat-merchant-arrangement").val('').change();
    }else{

    }
});


$('#set_advertisement').click(function(){
    if($(this).is(':checked')){
        $("#set_advertisement").prop("checked", false);
        $('#show_feature_merchant_modal').modal('show');
    }else{
        $('#show_feature_merchant_modal').modal('hide');
      
    }
});

$('#uncheck_rabutton').click(function(){
    $("#set_advertisement").prop("checked", false);
    $("#entry-feat-merchant-arrangement").val('').change();
});


$('#entry-feat-merchant-arrangement').change(function(){ 
    var merchant_number = $(this).val();
    check_featuredMerchant_Number(merchant_number)
});

function check_featuredMerchant_Number(merchant_number)
{
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    //product_id  = $("#u_id").val()
   // var product_id = product_id;
  //  alert(product_id);
    $.ajax({
        method: "POST",
        url: base_url+'shops/Main_shops/check_feutured_merchat_arrangement/'+merchant_number,
            success: function(data){

               // alert(data);
                if(data == 1) { 
                    showCpToast("warning", "Warning!", 'This number is already selected...');            
                    // $.toast({
                    //     text: 'Warning!<br>This number is already selected...',
                    //     icon: 'info',
                    //     loader: false,  
                    //     stack: false,
                    //     position: 'top-center', 
                    //     bgColor: '#FFA500',
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $("#entry-feat-merchant-arrangement").val('').change();
                }else{
                   // alert('test');
                   // checkfeaturedproducts();
                }

            }
    });

}

$('#uncheck_rabutton').click(function(){
    $("#set_advertisement").prop("checked", false);
    $("#entry-feat-merchant-arrangement").selectmenu('refresh');
   // $("#entry-feat-merchant-arrangement").val('').attr("Select Arrangement", "Select Arrangement");
   // $('#mycontrolId').val(myvalue).attr("selected", "selected");
});


$('body').delegate("#check_rabutton", "click", function(){
   shop_id  = $("#entry-id").val()
   check_featuredmerchant(shop_id);
 });


function check_featuredmerchant(shop_id)
{
var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var shop_id = shop_id;
// alert(product_id);
$.ajax({
    method: "POST",
    url: base_url+'shops/Main_shops/check_feutured_merchants/'+shop_id,
        success: function(data){

           // alert(data);
            if(data == 1) {
                showCpToast("warning", "Warning!", 'This Merchant is already included in Featured Merchants');
                // $.toast({
                //     text: 'Warning!<br>This Merchant is already included in Featured Merchants',
                //     icon: 'info',
                //     loader: false,  
                //     stack: false,
                //     position: 'top-center', 
                //     bgColor: '#FFA500',
                //     textColor: 'white',
                //     allowToastClose: false,
                //     hideAfter: 10000
                // });
             
             $("#set_advertisement").prop("checked", true);
             $('#show_feature_merchant_modal').modal('hide');
            }else{
               // alert('test');
               checkfeaturedmerchant();
            }

        }
});

}

function  checkfeaturedmerchant()
{
    $.ajax({
        type:'post',
        url:base_url+'shops/Main_shops/get_feutured_merchant_count/',
        success:function(data){
            var res = data.result;
            if (data >= 4){
                showCpToast("warning", "Warning!", 'You have reached the maximum of 4 featured merchants allowed');
                // $.toast({
                //     text: 'You have reached the maximum of 4 featured merchants allowed',
                //     icon: 'info',
                //     loader: false,  
                //     stack: false,
                //     position: 'top-center', 
                //     bgColor: '#FFA500',
                //     textColor: 'white',
                //     allowToastClose: false,
                //     hideAfter: 10000
                // });
            }else{
                $("#set_advertisement").prop("checked", true);
                $('#show_feature_merchant_modal').modal('hide');
                $('.contsellingdiv').show(250);
            }
        }
    });
   
}

 
$('#set_advertisement').click(function(){
    if($(this).is(':checked')){
        $("#set_advertisement").prop("checked", false);
        $('#show_feature_merchant_modal').modal('show');
        
    }else{
        $('#show_feature_merchant_modal').modal('hide');
       
    }
});


$('#set_advertisement').click(function(){
    if($(this).is(':unchecked')){
        $("#entry-feat-merchant-arrangement").val('').change();
        $("#img_preview_container_advertisement").val('').change();
        $("#imgthumbnail-advertisement").val('').change();
    }else{
    }
}); 


function check_featuredmerchantCheck()
{

shop_id  = $("#entry-id").val()   
var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var shop_id = shop_id;
// alert(product_id);
    $.ajax({
        method: "POST",
        url: base_url+'shops/Main_shops/check_feutured_merchants/'+shop_id,
            success: function(data){

              //  alert(data);
                if(data == 1) {
                   $('.contsellingdiv').show(250);
                
                }else{
                   $('.contsellingdiv').hide(250);
                }

            }
    });

}



var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var edit_loadstate = true;
get_record_details();
    $('#entry-form').submit(function(e){
        e.preventDefault();

        var form = $(this);
        var form_data = new FormData(form[0]);
        form_data.append('file_container', get_img_file('file_container'));
        form_data.append('file_container_banner', get_img_file('file_container_banner'));
        form_data.append('file_container_advertisement', get_img_file('file_container_advertisement'));

        if(ini == 'toktokmall'){
            var save = 1;
            var f_disc_rate = $('#entry-merchant-comrate').val() / 100;
            var f_disc_rate = (f_disc_rate * 90) * 0.01;
            var f_startup = $('#entry-f_startup').val() / 100;
            var f_jc      = $('#entry-f_jc').val() / 100;
            var f_mcjr    = $('#entry-f_mcjr').val() / 100;
            var f_mc      = $('#entry-f_mc').val() / 100;
            var f_mcsuper = $('#entry-f_mcsuper').val() / 100;
            var f_mcmega  = $('#entry-f_mcmega').val() / 100;
            var f_others  = $('#entry-f_others').val() / 100;
            if(f_startup > f_disc_rate || f_jc > f_disc_rate || f_mcjr > f_disc_rate || f_mc > f_disc_rate || f_mcsuper > f_disc_rate || f_mcmega > f_disc_rate || f_others > f_disc_rate){
                var save = 0;
            }
        }
        else{
            var save = 1;
        }

        if(checkInputs("#entry-form") == 0 && save == 1){
            $.ajax({
                type:'post',
                url: base_url+'Shops/update_shop',
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $(".btn-save").prop('disabled', true); 
                    $(".btn-save").text("Please wait...");
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    $(".btn-save").prop('disabled', false); 
                    $(".btn-save").text("Update");
                    if (data.success == 1) {
                        //messageBox(data.message, 'Success', 'success');
                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);
                    }else{
                        //messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
        }
        else if(save == 0){
            $.LoadingOverlay("hide");
            //sys_toast_warning('Percentage of Account Type Commision Rate should not be more than to 50% of Merchant Commission Rate.');
            showCpToast("warning", "Warning!", 'Percentage of Account Type Commision Rate should not be more than to 50% of Merchant Commission Rate.');
        }
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
                    if(!hasExtension(input.value,['.jpg', '.png','.JPG','.PNG','.jpeg','.JPEG'])){
                       // messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        showCpToast("warning", "Warning!", 'Invalid file type, Only JPG/PNG are allowed');
                        $('#file_container').val("");
                        $('#file_description').text('Choose file');
                    }else{
                        if(image_size > 2000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
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
                        if(image_size > 2000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-banner', 'show');
                            $('.img_preview_container_banner').hide('slow');
                            $('#file_container_banner').val("");
                            $('#file_description_banner').text('Choose file');
                        }else{                        
                            reader.onload = function(event) {
                                $($.parseHTML('<img id="product_preview_banner" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                                $('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
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

      var renderImages_advertisement = (input, placeToInsertImagePreview) => {
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
                        $('#file_container_advertisement').val("");
                        $('#file_description_advertisement').text('Choose file');
                    }else{
                        if(image_size > 2000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-advertisement', 'show');
                            $('.img_preview_container_advertisement').hide('slow');
                            $('#file_container_advertisement').val("");
                            $('#file_description_advertisement').text('Choose file');
                        }else{                        
                            reader.onload = function(event) {
                                $($.parseHTML('<img id="product_preview_advertisement" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#file_description_advertisement').text(filesAmount+' Attached Image(s)');
                            $('.img_preview_container_advertisement').show('slow');
                        }
                    }
                }
            }else{
                //User clicked cancel
                uploadthumbnail('imgthumbnail-advertisement', 'show');
                $('.img_preview_container_advertisement').hide('slow');
                $('#file_description_advertisement').text('Choose file');
            }
        };
    }

    $('#file_container_advertisement').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files_advertisement(countFiles, this);//param 1 file count, param 2 the file container/input
    });

    

    function prep_files_advertisement(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-advertisement', 'remove');
        $( ".img_preview_container_advertisement" ).empty();
        renderImages_advertisement(file_container, 'div.img_preview_container_advertisement');
        //$('#main_logo_checker').val('true');
        $.LoadingOverlay("hide");
    }


    ////////////////////////////////////////////////////////////////

       ////////////////////////////////////////////////////////////////

       var renderImages_whatsnew = (input, placeToInsertImagePreview) => {
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
                        $('#file_container_whatsnew').val("");
                        $('#file_description_whatsnew').text('Choose file');
                    }else{
                        if(image_size > 3000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-whatsnew', 'show');
                            $('.img_preview_container_whatsnew').hide('slow');
                            $('#file_container_whatsnew').val("");
                            $('#file_description_whatsnew').text('Choose file');
                        }
                         else{                        
                            reader.onload = function(event) {
                                var image = new Image();
                                image.src = reader.result;
                                image.onload = function() {

                                    if((image.width != 520) && (image.height != 520)){
                                        //messageBox('Invalid file Dimension.', 'Warning', 'warning');
                                        showCpToast("warning", "Warning!", 'Invalid file Dimension.');
                                        uploadthumbnail('imgthumbnail-whatsnew', 'show');
                                        $('.img_preview_container_whatsnew').hide();
                                        $('#file_container_whatsnew').val("");
                                        $('#file_description_whatsnew').text('Choose file');
                                    }
                                
                                };
                                $($.parseHTML('<img id="product_preview" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#file_description_whatsnew').text(filesAmount+' Attached Image(s)');
                            $('.img_preview_container_whatsnew').show('slow');
                        }
                    }
                }
            }else{
                //User clicked cancel
                uploadthumbnail('imgthumbnail-whatsnew', 'show');
                $('.img_preview_container_whatsnew').hide('slow');
                $('#file_description_whtasnew').text('Choose file');
            }
        };
    }

    $('#file_container_whatsnew').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files_whatsnew(countFiles, this);//param 1 file count, param 2 the file container/input
    });

    

    function prep_files_whatsnew(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-whatsnew', 'remove');
        $( ".img_preview_container_whatsnew" ).empty();
        renderImages_whatsnew(file_container, 'div.img_preview_container_whatsnew');
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
        return fileInputs[0].files;
    }

    function uploadthumbnail(imagetoclear, action=''){
        if(action == 'show'){
            $("#"+imagetoclear).attr('hidden', false);
        }else{
            $("#"+imagetoclear).attr('hidden', true);
        }
    }

    function get_record_details(){
        get_city_of_region('edit', $("#city_hidden").val());
        edit_loadstate  = false;
    }

    if(ini == 'toktokmall'){
        $("#entry-merchant-comrate").keyup(function(e) { 
            rate = $(this).val();
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

    $(document).delegate('.commcapping','input',function(e){
		var self = $(this);
		if (self.val() > 30 || self.val() < 0) 
		{
            self.val('');
		}
	});
