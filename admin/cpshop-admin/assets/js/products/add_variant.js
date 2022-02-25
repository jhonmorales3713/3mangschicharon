$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var token    = $('#token').val();
    var ini      = $("body").data('ini');
    
    $('#f_tq_isset').click(function(){
        if($(this).is(':checked')){
            $('.contsellingdiv, .nostocksdiv').show(250);
        }else{
            $('.contsellingdiv, .nostocksdiv').hide(250);
        }
    });

    $('#f_max_qty_isset').click(function(){
        if($(this).is(':checked')){
            $('.maxqtydiv').show(250);
        }else{
            $('.maxqtydiv').hide(250);
        }
    });

    $('.cancelBtn, .saveBtn').click(function(){
        $('.contsellingdiv, .nostocksdiv').show(250);
    });
	
	$('#backBtn').click(function(){
        window.location.assign(base_url+"Main_products/products/"+token);
	})
	
	$('#f_shipping_isset').click(function(){
        if($(this).is(':checked')){
            $('.weightdiv').show(250);
        }else{
            $('.weightdiv').hide(250);
        }
    });
    
    $('#f_admin_isset').click(function(){
        if($(this).is(':checked')){
            $('.adminsettings_div').show(250);
        }else{
            $('.adminsettings_div').hide(250);
        }
    });

    $('#product_image_multip').on('change', function() {
        type_checker = 0;
       
        var files = $(this).prop("files");
        var names = $.map(files, function(val) { return val.name; });

        $.each(names, function( index, value ) {
            if (!hasExtension(value, ['.jpg', '.jpeg', '.png','.JPG','.PNG','.JPEG'])) {
                type_checker = 1;
            }
        });

        if (type_checker == 1) {
            $('#product_image_multip').val(''); 
            //sys_toast_warning('Only jpeg, jpg and png are allowed to upload.');
            showCpToast("warning", "Warning!", 'Only jpeg, jpg and png are allowed to upload.');  
        }
        else{
            countFiles = $(this)[0].files.length;
            $.LoadingOverlay("show");
            $( ".imagepreview" ).empty();
            imagesPreview(this, 'div.imagepreview');
            $('#product-placeholder').hide();
            $('.imagepreview').hide('slow');
            $('.imagepreview').show('slow');
            $('#file_label').text(countFiles+' Attached Image(s)');
            $('#upload_checker').val(0)
            $.LoadingOverlay("hide"); 
        }

    	
    });
    
    $("#f_disc_ratetype").change(function(){
		if($(this).val() == 'p'){
            $("#f_disc_rate").attr("max","1");
            $("#f_disc_rate").attr("placeholder","1.00");
        }
        else if($(this).val() == 'f'){
            $("#f_disc_rate").removeAttr("max");
            $("#f_disc_rate").attr("placeholder","100.00");
        }
	});
	
	var imagesPreview = function(input, placeToInsertImagePreview) {
        if (input.files) {
            var filesAmount = input.files.length;
            var count = 0;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    filename = input.files[count].name;
                    $('<li class="ui-state-default" data-id="'+count+'"  data-directory="'+event.target.result+'" data-filename="'+filename+'"><img id="product_preview" src="'+event.target.result+'"></li>').appendTo(placeToInsertImagePreview);
                    $('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
                    if(count == 0){
                        setPrimaryPhoto(event.target.result);
                    }
                    count++;
                    
                }

                reader.readAsDataURL(input.files[i]);
            }
        }
    };
    
    //// update product
    Id = $('#f_parent_product_id').val();

    if(Id != "" && Id != undefined){
        $.LoadingOverlay("show");
        $.ajax({
            type:'get',
            url:base_url+'products/Main_products/get_productdetails/'+Id,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success){

                    json_data.message.admin_isset == 1 ? $( "#f_admin_isset" ).prop( "checked", true ) : $( "#f_admin_isset" ).prop( "checked", false );
                    json_data.message.admin_isset == 1 ? '' : $('.adminsettings_div').hide(250);
                    if(shop_id != 0){
                        $( "#f_admin_isset" ).val(json_data.message.admin_isset);
                    }
                    $('#f_disc_ratetype').val(json_data.message.disc_ratetype).change();
                    if(ini == 'toktokmall'){
                        $('#f_disc_rate').val((json_data.message.disc_rate*100).toFixed(2));
                    }
                    else{
                        $('#f_disc_rate').val(json_data.message.disc_rate);
                    }
                    $('#f_startup').val((json_data.message.refstartup*100).toFixed(2));
                    $('#f_jc').val((json_data.message.refjc*100).toFixed(2));
                    $('#f_mcjr').val((json_data.message.refmcjr*100).toFixed(2));
                    $('#f_mc').val((json_data.message.refmc*100).toFixed(2));
                    $('#f_mcsuper').val((json_data.message.refmcsuper*100).toFixed(2));
                    $('#f_mcmega').val((json_data.message.refmcmega*100).toFixed(2));
                    $('#f_others').val((json_data.message.refothers*100).toFixed(2));
                }else{
                    showCpToast("info", "Note!", json_data.message);  
                    // $.toast({
                    //     heading: 'Note',
                    //     text: json_data.message,
                    //     icon: 'info',
                    //     loader: false,   
                    //     stack: false,
                    //     position: 'top-center',  
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'        
                    // });
                }
            },
            error: function(error){
                $.LoadingOverlay("hide");
                showCpToast("error", "Error!", "Error");  
                // $.toast({
                //     heading: 'Error',
                //     text: 'Error',
                //     icon: 'error',
                //     loader: false,   
                //     stack: false,
                //     position: 'top-center',  
                //     bgColor: '#FFA500',
                //     textColor: 'white'        
                // });
            }
        });
    }


    $('#form_save').submit(function(e){
		e.preventDefault();
        $.LoadingOverlay("show");
        var branch_id = $("input[name='branch[]']")
        .map(function(){return $(this).val();}).get();
     
        

        $(".oldimgurl").empty();
        $('#sortable li').each(function(){
            var id       = $(this).data('id');
            var filename = $(this).data('filename');
            $(".oldimgurl").append("<input type='text' class='reorder_image' name='reorder_image[]' value='"+filename+"'>");
        });

        var form = $(this);
        var form_data = new FormData(form[0]);

        var textinputs = document.querySelectorAll('input[name*=reorder_image]');
        for( var i = 0; i < textinputs.length; i++ ){
            form_data.append(textinputs[i].name, textinputs[i].value);
        }

        $.each(branch_id, function(key, value) {
            form_data.append('f_no_of_stocks_'+value, $('#f_no_of_stocks_'+value).val());
        });

        var textinputs = document.querySelectorAll('input[name*=variant_price]');
        for( var i = 0; i < textinputs.length; i++ ){
            form_data.append(textinputs[i].name, textinputs[i].value);
        }

        var textinputs = document.querySelectorAll('input[name*=variant_sku]');
        for( var i = 0; i < textinputs.length; i++ ){
            form_data.append(textinputs[i].name, textinputs[i].value);
        }
        
        var textinputs = document.querySelectorAll('input[name*=variant_barcode]');
        for( var i = 0; i < textinputs.length; i++ ){
            form_data.append(textinputs[i].name, textinputs[i].value);
        }

        if(ini == 'toktokmall'){
       
            if($("#f_admin_isset").prop('checked')){
                var save = 1;
                var f_disc_rate = $('#f_disc_rate').val() / 100;
                var f_disc_rate = (f_disc_rate * 90) * 0.01;
                var f_startup = $('#f_startup').val() / 100;
                var f_jc      = $('#f_jc').val() / 100;
                var f_mcjr    = $('#f_mcjr').val() / 100;
                var f_mc      = $('#f_mc').val() / 100;
                var f_mcsuper = $('#f_mcsuper').val() / 100;
                var f_mcmega  = $('#f_mcmega').val() / 100;
                var f_others  = $('#f_others').val() / 100;
                if(f_startup > f_disc_rate || f_jc > f_disc_rate || f_mcjr > f_disc_rate || f_mc > f_disc_rate || f_mcsuper > f_disc_rate || f_mcmega > f_disc_rate || f_others > f_disc_rate){
                    var save = 0;
                }
            }
            else{
                var save = 1;
            }
        }
        else{
            var save = 1;
        }
        
        if(save == 1){
            $.ajax({
                type: 'post',
                url: base_url+'products/Main_products/save_variant',
                data: form_data,
                contentType: false,   
                cache: false,      
                processData:false,
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    
                    if(json_data.success) {
                        //sys_toast_success(json_data.message);
                        showCpToast("success", "Success!", json_data.message);  
                        window.location.assign(base_url+"Main_products/update_products/"+token+"/"+json_data.parent_product_id);
                    }else{
                        //sys_toast_warning(json_data.message);
                        showCpToast("warning", "Warning!", json_data.message); 
                    }
                },
                error: function(error){
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message); 
                }
            });
        }
        else{
            $.LoadingOverlay("hide");
            //sys_toast_warning('Percentage of Account Type Commission Rate should not be more than 50% of Merchant Commission Rate.');
            showCpToast("warning", "Warning!", 'Percentage of Account Type Commission Rate should not be more than 50% of Merchant Commission Rate.'); 
        }
    });
    
    $("#f_member_shop").change(function(){
        shop_id = $(this).val();

        $.ajax({
            type:'get',
            url:base_url+'products/Main_products/get_sys_branch_profile/'+shop_id,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);

                if(json_data.success){
                    $('#nostocksdiv').empty();
                    $('#nostocksdiv2').empty();

                    if(json_data.branchid == 0){
                        option_str = "";
                        $.each(json_data.details, function(key, value) {
                            option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
                        });

                        $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'><option value='0' selected>Main</option>"+option_str+"</select></div>");
                        
                        $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_0'><label>Available quantity(Main Branch)</label><input type='number' class='form-control' name='f_no_of_stocks_0' id='f_no_of_stocks_0' placeholder='Number of stocks' value='0'></div>");
                        $('#nostocksdiv2').append("<input type='hidden' value='0' name='branch[]'>");

                        $.each(json_data.details, function(key, value) {
                            $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"' style='display:none'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
                            $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
                        });
                    }else{
                        option_str = "";
                        $.each(json_data.details, function(key, value) {
                            option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
                        });

                        $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'>"+option_str+"</select></div>");

                        $.each(json_data.details, function(key, value) {
                            $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"' style='display:none'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
                            $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
                        });
                    }
                }else{
                    $('#nostocksdiv').empty();
                    $('#nostocksdiv2').empty();

                    $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'><option value='0' selected>Main</option></select></div>");

                    $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_0'><label>Available quantity(Main Branch)</label><input type='number' class='form-control' name='f_no_of_stocks_0' id='f_no_of_stocks_0' placeholder='Number of stocks' value='0'></div>");
                    $('#nostocksdiv2').append("<input type='hidden' value='0' name='branch[]'>");
                }
            }
        });
    });

    $(document).delegate('#f_delivery_location','change',function(e){
        branchid = $(this).val();
        $('.divnostock').hide(100);
        $('#div_no_of_stocks_'+branchid).show(100);
    });

    shopid   = $('#shopid').val();
    branchid = $('#branchid').val();

    if(shopid != 0){
        $.ajax({
            type:'get',
            url:base_url+'products/Main_products/get_sys_branch_profile/'+shopid,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);

                if(json_data.success){
                    $('#nostocksdiv').empty();
                    $('#nostocksdiv2').empty();

                    if(json_data.branchid == 0){
                        option_str = "";
                        $.each(json_data.details, function(key, value) {
                            option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
                        });

                        $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'><option value='0' selected>Main</option>"+option_str+"</select></div>");
                        
                        $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_0'><label>Available quantity(Main Branch)</label><input type='number' class='form-control' name='f_no_of_stocks_0' id='f_no_of_stocks_0' placeholder='Number of stocks' value='0'></div>");
                        $('#nostocksdiv2').append("<input type='hidden' value='0' name='branch[]'>");

                        $.each(json_data.details, function(key, value) {
                            $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"' style='display:none'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
                            $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
                        });
                    }else{
                        option_str = "";
                        $.each(json_data.details, function(key, value) {
                            option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
                        });

                        $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'>"+option_str+"</select></div>");

                        $.each(json_data.details, function(key, value) {
                            $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
                            $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
                        });
                    }
                }
            }
        });
    }

    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();

    function setPrimaryPhoto(src){
        $('#primary_product').fadeOut(500);
        $('#primary_product').attr("src", src);
        $('#primary_product').fadeIn(2000);
    }

    $("#sortable").sortable({
        update: function(event, ui) {
            $('#sortable li').each(function(){
                var src = $(this).data('directory');
                setPrimaryPhoto(src);
                return false;
            });
        }
    });
    

    $('.contsellingdiv').hide(250);

    $('#featured_prod_isset').click(function(){
        if($(this).is(':unchecked')){
            $('.contsellingdiv').hide(250);
            $("#entry-feat-product-arrangement").val('').change();
        }else{
    
        }
    });

    $('#featured_prod_isset').click(function(){
        if($(this).is(':checked')){
            $("#featured_prod_isset").prop("checked", false);
            $('#show_feature_prod_modal').modal('show');
        }else{
            $('#show_feature_prod_modal').modal('hide');
        }
    });

    $('#uncheck_rabutton').click(function(){
        $("#featured_prod_isset").prop("checked", false);
        $("#entry-feat-product-arrangement").val('').change();
    });


    $('body').delegate("#check_rabutton", "click", function(e){
        e.preventDefault();
            $.ajax({
                type:'post',
                url:base_url+'products/Main_products/get_feutured_products_count/',
                success:function(data){
                    var res = data.result;
                    if (data >= 7){
                        showCpToast("warning", "Warning!", "You have reached the maximum of 7 featured products allowed."); 
                        // $.toast({
                        //     text: 'Warning!<br>You have reached the maximum of 7 featured products allowed.',
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
                       $("#featured_prod_isset").prop("checked", true);
                       $('#show_feature_prod_modal').modal('hide');
                       $('.contsellingdiv').show(250);
                    }
                }
            });
        });

        $('#entry-feat-product-arrangement').change(function(){ 
            var product_number = $(this).val();
            check_featuredproduct(product_number)
        });
        
        function check_featuredproduct(product_number)
        {
            var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
        
            $.ajax({
                method: "POST",
                url: base_url+'products/Main_products/check_feutured_product_arrangement/'+product_number,
                    success: function(data){
        
                       // alert(data);
                        if(data == 1) {             
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
                            showCpToast("warning", "Warning!", "This number is already selected..."); 
                            $("#entry-feat-product-arrangement").val("").change();
                        }else{
                           // alert('test');
                           // checkfeaturedproducts();
                        }
        
                    }
            });
        
        }

    $('#generateItemIDBtn').click(function(){
         var core_code = $('#f_company_initial').val();
         var randomitemid = core_code+"_"+Math.floor(Math.random() * 10000000);

         $('#f_itemid').val(randomitemid);
    });

    function hasExtension(value, exts) {
        var fileName = value;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }

    if(ini == 'toktokmall'){
        $("#f_disc_rate").keyup(function(e) { 
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

            $('#f_startup').val(f_startup.toFixed(2));
            $('#f_jc').val(f_jc.toFixed(2));
            $('#f_mcjr').val(f_mcjr.toFixed(2));
            $('#f_mc').val(f_mc.toFixed(2));
            $('#f_mcsuper').val(f_mcsuper.toFixed(2));
            $('#f_mcmega').val(f_mcmega.toFixed(2));
            $('#f_others').val(f_others.toFixed(2));
        }
    }

    $(document).delegate('.commcapping','input',function(e){
		var self = $(this);
		if (self.val() > 30 || self.val() < 0) 
		{
            self.val('');
		}
	});

});