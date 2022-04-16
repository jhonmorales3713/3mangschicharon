$(function(){
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var shop_url     = $("body").data('shop_url');
    var token        = $("body").data('token');
    var ini          = $("body").data('ini');
    var shop_id      = $("body").data('shop_id');
    var branchid     = $("#branchid").val();

    check_featuredproductCheck();
    
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
            sys_toast_warning('Only jpeg, jpg and png are allowed to upload.');
            ////showcpToast("warning", "Warning!", 'Only jpeg, jpg and png are allowed to upload.');
        }
        else{
            countFiles = $(this)[0].files.length;
            $.LoadingOverlay("show");
            $( ".imagepreview2" ).empty();
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

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img id="product_preview"  width=100%>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    $('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
                }

                reader.readAsDataURL(input.files[i]);
            }
        }
    };

    function imageExists(filename, shopcode){
        /// png format
        result = false;

        imageCounter = 0;
        filename  = filename;
        imageExtention = filename;
        image_url_orig = filename;
        for(i = 0; 6 > i; i++){
            imageExtention = imageExtention;
            image_url = i+'-'+imageExtention+".png";
            image_url_2 = i+'-'+imageExtention+"png";

            var http = new XMLHttpRequest();
            http.open('HEAD', shop_url + 'assets/img/'+shopcode+'/products/'+ filename+'/' + image_url+"?"+Math.random(), false);
            http.send();
            result = http.status != 404;

            if(result == true){
                $('<div class="closediv p-1 bd-highlight img'+image_url_2+'">'+'<li class="ui-state-default" data-id="'+i+'" ><img id="product_preview" class="ui-state-default img'+image_url_2+'" src="'+shop_url + 'assets/img/'+shopcode+'/products/'+ filename+'/'+ image_url+"?"+Math.random()+'"></li><span class="deleteimg" data-value="'+image_url_2+'" data-format="'+image_url+'" data-noformat="'+i+'-'+filename+'">x</span></div>').appendTo('.imagepreview2');
                $('<font>&nbsp;</font>').appendTo('.imagepreview2');
                $('#current_product_url').val(image_url_orig);
                $('#upload_checker').val(image_url_orig);
                imageCounter++;
            }
        }
        
        //// jpg format
        // imageCounter = 0;
        filename  = filename;
        imageExtention = filename;
        image_url_orig = filename;
        for(i = 0; 6 > i; i++){
            imageExtention = imageExtention;
            image_url = i+'-'+imageExtention+".jpg";
            image_url_2 = i+'-'+imageExtention+"jpg";

            var http = new XMLHttpRequest();
            http.open('HEAD', shop_url + 'assets/img/'+shopcode+'/products/' + filename+'/'+ image_url+"?"+Math.random(), false);
            http.send();
            result = http.status != 404;

            if(result == true){

                $('<div class="closediv p-1 bd-highlight img'+image_url_2+'">'+'<li class="ui-state-default" data-id="'+i+'" ><img id="product_preview" class="ui-state-default img'+image_url_2+'" src="'+shop_url + 'assets/img/'+shopcode+'/products/'+ filename+'/'+ image_url+"?"+Math.random()+'"></li><span class="deleteimg" data-value="'+image_url_2+'" data-format="'+image_url+'" data-noformat="'+i+'-'+filename+'">x</span></div>').appendTo('.imagepreview2');
                $('<font>&nbsp;</font>').appendTo('.imagepreview2');
                $('#current_product_url').val(image_url_orig);
                $('#upload_checker').val(image_url_orig);
                imageCounter++;
            }
        }
        
        $('.imagepreview').show('slow');
        console.clear();
    }

    function imageExistsSpec(id, filename, shopcode, count){
        result = false;
        imageCounter = 0;
        filename  = filename;
        imageExtention = filename;
        image_url_orig = filename;

        // var http = new XMLHttpRequest();
        // http.open('HEAD', base_url + 'assets/img/'+shopcode+'/products/'+ id+'/' + filename+"?"+Math.random(), false);
        // http.send();
        // result = http.status != 404;

        filename_2 = filename.split('.').join("");
        filename_2=(filename_2.split('==').join("."));
        if(filename_2.includes('=')){
            filename_2=(filename_2.split('=').join("."));
        }
        console.log(filename_2);
        // if(result == true){
            $('<div class="closediv p-1 bd-highlight img'+filename_2+'">'+'<li class="ui-state-default" data-id="'+count+'" data-directory="'+base_url + 'assets/img/products/'+ filename_2+"?"+Math.random()+'" data-imagename="'+filename_2+'" ><img width=100% id="product_preview" class="img'+filename_2+'" src="'+base_url + 'assets/uploads/products/'+ filename_2+"?"+Math.random()+'"></li><span class="deleteimg" data-value="'+filename_2+'" data-format="'+filename_2+'" data-noformat="'+filename_2+'">x</span></div>').appendTo('.imagepreview2');
            $('<font>&nbsp;</font>').appendTo('.imagepreview2');
            $('#current_product_url').val(filename);
            $('#upload_checker').val(filename);
        // }
        
        $('.imagepreview').show('slow');
    }

    function setPrimaryPhoto(src){
        $('#primary_product').fadeOut(500);
        $('#primary_product').attr("src", src);
        $('#primary_product').fadeIn(2000);
    }

    $(document).delegate('.deleteimg','click',function(e){
        data     = $(this).data('value');
        format   = $(this).data('format');
        noformat = $(this).data('noformat');
        $('#productimage_changes').val(1);
        $('.img'+data).hide(250)
        $(".oldimgurl").append("<input type='hidden' name='prev_image_name[]' value='"+format+"'>");
        $(".oldimgurl").append("<input type='hidden' name='prev_image_name_noformat[]' value='"+noformat+"'>");
    });
    
    $("#btninventory").click(function(){console.log("SD");
        $('#inventory_modal').modal('show');
    });
    $("#btnAddInventory").click(function(){
        if($(".inventory_count").length == 0){

            $("#tbody_inventory").empty();
        }
        displayInventory($(".inventory_count").length+1);
    });
    var tomoveout = [];
    $(document).delegate('.btn_move_out','click',function(e){
        var index = $(this).data('value');
        var id = $(this).data('id');
        tomoveout.push(id);
        $('.inventory_tr_'+index).remove();
        count = 1;
        $('.id_key').each(function(index, tr) { 
           // console.log(tr);
            tr.innerText=(count);
            //tr.innerHTML=('2asd');
            count++;
        });

    });
    function displayInventory(key){
        var str="";
        str += "<tr class='inventory_tr_"+key+" inventory_count'>";
        str += "<td class='inventory_"+key+" id_key' >"+key+"<input name='inventory_id[]' type='text'  value='"+key+"' style='display:none;'></td>";
        str += "<td class='inventory_"+key+"'>"+"<input type='text' class='form-control allownumericwithoutdecimal' name='inventory_qty[]' ></td>";
        str += "<td class='inventory_"+key+"'><input class='form-control ' type='date' name='inventory_manufactured[]' ></td>";
        str += "<td class='inventory_"+key+"'><input class='form-control ' type='date'  name='inventory_expiration[]' ></td>";
        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_sku[]'></td>";
        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_barcode[]'></td>";
        str += "<td class='inventory_"+key+"'><button type='button' id='removeInventorySpec' class='btn btn-danger' data-value='"+key+"'><i class='fa fa-trash-o'></i></button>";
        str += "</td>";
        str += "</tr>";
        $('#tbody_inventory').append(str);
        
        //allowing numeric without decimal
        $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {
            $(this).val($(this).val().replace(/[^\d].+/, ""));
        
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
        
    }

    $("#btnSaveinventory").click(function()
    {
        var form = $("form[name=form_inventory]");
        var form_data = new FormData(form[0]);
        form_data.append('product_id',$(this).data('id'));
        form_data.append('todelete',todeleteinventory);
        form_data.append('tomoveout',tomoveout);
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/Main_products/store_inventory',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    sys_toast_success(json_data.message);
                    //$("#inventory_modal").modal('hide');
                    $.ajax({
                        type:'get',
                        url:base_url+'admin/Main_products/get_inventorydetails/'+Id,
                        success:function(datas){
                            var data = JSON.parse(datas).result;
                            var qty = 0;
                            for(var i = 0;i<data.length;i++){
                                if(data[i].status == 1){
                                    qty+=parseInt(data[i].qty);
                                }
                            }
                            $("#f_no_of_stocks").val(qty);
                        }
                    });
                    //setTimeout(function(){location.reload()}, 2000);
                    //window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                }else{
                    //sys_toast_warning(json_data.message);
                    sys_toast_warning(json_data.message[0]);
                }
            },
            error: function(error){
                sys_toast_warning(json_data.message);
                //showCpToast("warning", "Warning!", json_data.message);
            }
        });
    });

    //// update product
    Id = $('#u_id').val();

    if(Id != "" && Id != undefined){
        $.LoadingOverlay("show");
        $.ajax({
            type:'get',
            url:base_url+'admin/Main_products/get_productdetails/'+Id,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success){

                    imageBlob = ''; 
                    $('#product-placeholder').removeClass('hidden');
                    $('#product_image').val('');
                    // Product Image
                    if (json_data.message.Id != "") {
                        console.log(json_data);
                        $('#product-placeholder').addClass('hidden');
                        $('#change-product-image').show();
                        // if(json_data.message.img_1 == null || json_data.message.img_1 == ''){
                        //     imageExists(json_data.message.Id, json_data.message.shopcode);
                        $.each(json_data.images, function(key, value) {
                            imageExistsSpec(json_data.message.Id, value.filename, json_data.message.shopcode, value.arrangement);
                            if(value.arrangement == 1){
                                filename_2 = value.filename.split('.').join("");
                                filename_2=(filename_2.split('==').join("."));
                                if(filename_2.includes('=')){
                                    filename_2=(filename_2.split('=').join("."));
                                }
                                setPrimaryPhoto(base_url + 'assets/uploads/products/'+ filename_2+"?"+Math.random());
                            }
                        });
                    } else {
                        $('#product-placeholder').removeClass('hidden');
                        $('#change-product-image').hide();
                    }

                    $('#f_id').val(json_data.message.id);
                    $('#f_member_shop').val(1);
                    $('#f_category').val(json_data.message.cat_id);
                    $('#f_itemname').val(json_data.message.name);
                    $('#f_itemid').val(json_data.message.itemid);
                    $('#f_otherinfo').val(json_data.message.otherinfo);
                    $('#f_uom').val(json_data.message.uom);
                    $('#f_price').val(json_data.message.price);
                    $('#f_compare_at_price').val(json_data.message.compare_at_price);
                    $('#f_tags').val(json_data.message.tags);
                    $('#f_summary').val(json_data.message.summary);
                    $('#f_arrangement').val(json_data.message.arrangement);
                    json_data.message.age_restriction_isset == 1 ? $( "#f_age_restriction_isset" ).prop( "checked", true ) : $( "#f_age_restriction_isset" ).prop( "checked", false );
                    $('#f_inv_sku').val(json_data.message.inv_sku);
                    $('#f_inv_barcode').val(json_data.message.inv_barcode);
                    json_data.message.max_qty_isset == 1 ? $( "#f_max_qty_isset" ).prop( "checked", true ) : $( "#f_max_qty_isset" ).prop( "checked", false );
                    json_data.message.max_qty_isset == 1 ? '' : $('.maxqtydiv').hide(250);
                    json_data.message.tq_isset == 1 ? $( "#f_tq_isset" ).prop( "checked", true ) : $( "#f_tq_isset" ).prop( "checked", false );
                    json_data.message.tq_isset == 1 && parseFloat(branchid) == parseFloat(0) ? '' : $('.contsellingdiv, .nostocksdiv').hide(250);
                    json_data.message.tq_isset == 0 && parseFloat(branchid) != parseFloat(0) ? $('.nostocksdiv').show(250) : '';
                    json_data.message.tq_isset == 1 && parseFloat(branchid) != parseFloat(0) ? $('.nostocksdiv').show(250) : '';
                    json_data.message.cont_selling_isset == 1 ? $( "#f_cont_selling_isset" ).prop( "checked", true ) : $( "#f_cont_selling_isset" ).prop( "checked", false );
                    $('#f_max_qty').val(json_data.message.max_qty);
                    json_data.message.shipping_isset == 1 ? $( "#f_shipping_isset" ).prop( "checked", true ) : $( "#f_shipping_isset" ).prop( "checked", false );
                    if(branchid == 0){
                        json_data.message.shipping_isset == 1 ? '' : $('.weightdiv').hide(250);
                    }
                    $('#f_weight').val(json_data.message.weight);
                    $('#f_uom_id').val(json_data.message.uom_id).change();
                    $('#f_length').val(json_data.message.length);
                    $('#f_width').val(json_data.message.width);
                    $('#f_height').val(json_data.message.height);
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

                    
                    $.ajax({
                        type:'get',
                        url:base_url+'admin/Main_products/get_inventorydetails/'+Id,
                        success:function(datas){
                            var stocks = 0;
                            var data = JSON.parse(datas).result;
                            for(var i=0;i<data.length;i++){
                                // console.log(data[i]);
                                if(data[i].status == 1){
                                    stocks+=parseInt(data[i].qty);
                                }
                            }
                            $('#f_no_of_stocks').val(stocks);
                    
                        }
                    });
                }else{
                    ////showcpToast("info", "Note!", json_data.message);
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
                ////showcpToast("error", "Error!", "Error");
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

    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();


    $("#sortable").sortable({
        update: function(event, ui) {
            $('#sortable li').each(function(){
                var src = $(this).data('directory');
                setPrimaryPhoto(src);
                return false;
            });
        }
    });

    
    $('#form_update').submit(function(e){
		e.preventDefault();
        $.LoadingOverlay("show");
        $('#f_member_shop').prop('disabled',false);
        $('.reorder_image').val('');

        // get image ids order
        var prev_id = 0;
        $('#sortable li').each(function(){
            var curr_id = $(this).data('id');
            
            if(curr_id < prev_id){
                $('#productimage_changes').val(1);
            }
            var id = $(this).data('id');
            var imagename = $(this).data('imagename');
            $(".oldimgurl").append("<input type='hidden'class='reorder_image' name='reorder_image[]' value='"+imagename+"'>");
            prev_id = $(this).data('id');
            console.log("@#");
        });
        console.log($("#sortable"));
		var form = $(this);
        var form_data = new FormData(form[0]);

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
                type: form[0].method,
                url: base_url+'admin/Main_products/update_variant',
                data: form_data,
                contentType: false,   
                cache: false,      
                processData:false,
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    console.log(data);
                    if(json_data.success) {
                        sys_toast_success(json_data.message);
                        //showcpToast("success", "Success!", json_data.message);
                        setTimeout(function(){location.reload()}, 2000);
                    }else{
                        sys_toast_warning(json_data.message);
                        ////showcpToast("warning", "Warning!", json_data.message);
                        $('#f_member_shop').prop('disabled',true);

                    }
                },
                error: function(error){
                    sys_toast_warning(json_data.message);
                    ////showcpToast("warning", "Warning!", json_data.message);
                    $('#f_member_shop').prop('disabled',true);
                }
            });
        }
        else{
            $.LoadingOverlay("hide");
            sys_toast_warning('Percentage of Account Type Commission Rate should not be more than 50% of Merchant Commission Rate.');
            ////showcpToast("warning", "Warning!", 'Percentage of Account Type Commission Rate should not be more than 50% of Merchant Commission Rate.');
        }

    });
    
    $("#f_delivery_location").change(function(){
        branchid = $(this).val();

        $('.divnostock').hide(100);
        $('#div_no_of_stocks_'+branchid).show(100);
    });

    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();


    $("#sortable").sortable({
        update: function(event, ui) {
            $('#sortable li').each(function(){
                var src = $(this).data('directory');
                setPrimaryPhoto(src);
                return false;
            });
        }
    });

    $('#featured_prod_isset').click(function(){
        if($(this).is(':checked')){
            //$("#featured_prod_isset").prop("checked", true);
            $('#show_feature_prod_modal').modal('show');
        }else{
            $('#show_feature_prod_modal').modal('hide');
        }
    });

    $('#uncheck_rabutton').click(function(){
        $("#featured_prod_isset").prop("checked", false);
    });


    $('body').delegate("#check_rabutton", "click", function(){
        product_id  = $("#u_id").val()
        check_featuredproduct(product_id);
     });

    $('#generateItemIDBtn').click(function(){
        var core_code = $('#f_company_initial').val();
        var randomitemid = core_code+"_"+Math.floor(Math.random() * 10000000);

        $('#f_itemid').val(randomitemid);
   });

    function hasExtension(value, exts) {
        var fileName = value;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }
    
});

var todeleteinventory = [];
$(document).delegate('#removeInventorySpec','click',function(e){
    var index = $(this).data('value');
    var id = $(this).data('id');
    todeleteinventory.push(id);
    $('.inventory_tr_'+index).remove();
    count = 1;
    $('.id_key').each(function(index, tr) { 
       // console.log(tr);
        tr.innerText=(count);
        //tr.innerHTML=('2asd');
        count++;
    });

});

$("#btninventory").click(function(){
    $('#inventory_modal').modal('show');
    
    $.LoadingOverlay("show");
    $.ajax({
        type:'get',
        url:base_url+'admin/Main_products/get_inventorydetails/'+Id,
        success:function(datas){
            $.LoadingOverlay("hide");
            var data = JSON.parse(datas).result;
            var f_discount_product = false;
            var f_days = 0;
            var discount_value = 0;
            var discount_amount = 0;
            $("#tbody_inventory").empty();
            var count = 1;
            for(i=0;i<data.length;i++){
                if(data[i].status != 0){
                    var d1 = new Date();
                    var d2 = new Date(data[i].date_expiration);
                    var str=""; 
                    var additional_class="";
                    var difference = d2.getTime() - d1.getTime();
                    var days = Math.ceil(difference / (1000 * 3600 * 24));
                    if(d1 >= d2){
                        additional_class="bg-danger text-white";
                    }else
                    if(days <= 30){
                        additional_class="bg-warning";
                    }
                    if(data[i].status == 1){
                        str += "<tr class='inventory_tr_"+i+" inventory_count "+ additional_class+"'>";
                        str += "<td class='inventory_"+i+" id_key' >"+(count)+"</td>";
                        str += "<td class='inventory_"+i+"'>"+"<input type='text' name='inventory_id[]'  value='"+data[i].id+"' style='display:none;'><input type='text'value='"+data[i].qty+"' class='form-control allownumericwithoutdecimal' name='inventory_qty[]' ></td>";
                        str += "<td class='inventory_"+i+"'><input class='form-control 'value='"+data[i].date_manufactured+"' type='date' name='inventory_manufactured[]' ></td>";
                        str += "<td class='inventory_"+i+"'><input class='form-control 'value='"+data[i].date_expiration+"' type='date'  name='inventory_expiration[]' ></td>";
                        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_sku[]'></td>";
                        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_barcode[]'></td>";
                        str+="<td class='inventory_"+i+"'>";
                        if(d1 <= d2){
                            str += "<button type='button'data-id='"+data[i].id+"' id='removeInventorySpec' class='btn btn-danger' data-value='"+i+"'><i class='fa fa-trash-o'></i></button>";
                        }else
                        if(days <= 30 && data[i].status == 1){
                            str += "<button type='button' id='activateInventory' class='btn btn-warning btn_move_out' data-id='"+data[i].id+"' data-value='"+i+"'><i class='fa fa-share'></i></button>";
                        }
                        // if(data[i].status == 1){
                        //     str += "<button type='button' id='activateInventory' class='btn btn-warning' data-value='"+data[i].id+"'><i class='fa fa-share'></i></button>";
                        // }else{
                        //     str += "<button type='button' id='activateInventory' class='btn btn-success' data-value='"+data[i].id+"'><i class='fa fa-eye-o'></i></button>";
                        // }
                        str += "</td></tr>";
                        $('#tbody_inventory').append(str);
                        f_discount_product = data[i].discount_isset;
                        discount_value = data[i].discount_value;
                        f_days = data[i].discount_days;
                        discount_amount = data[i].discount_amount;
                        //allowing numeric without decimal
                        $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {
                            $(this).val($(this).val().replace(/[^\d].+/, ""));
                        
                            if ((event.which < 48 || event.which > 57)) {
                                event.preventDefault();
                            }
                        });
                        count++;
                    }
                }
            }
            if(f_discount_product){
                $(".f_discount_product").show(250);
                $("#f_days").val(f_days);
                $("#f_discount_product").prop('checked',true);
                $("#f_discount_value").val(discount_amount);
                if(discount_value == 'f'){

                    $("#f_fixed_amount").prop('checked',true);
                }else{
                    $("#f_percentage").prop('checked',true);
                }
            }
            //displayInventory($(".inventory_count").length+1);
            
        }
    });
});
function check_featuredproduct(product_id)
{
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    
    var product_id = product_id;
   // alert(product_id);
    $.ajax({
        method: "POST",
        url: base_url+'admin/Main_products/check_feutured_products/'+product_id,
            success: function(data){

               // alert(data);
                if(data == 1) {
                    ////showcpToast("info", "Warning!", "This product is already included in Featured Products");
                    // $.toast({
                    //     text: 'Warning!<br>This product is already included in Featured Products',
                    //     icon: 'info',
                    //     loader: false,  
                    //     stack: false,
                    //     position: 'top-center', 
                    //     bgColor: '#FFA500',
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $("#featured_prod_isset").prop("checked", true);
                    $('#show_feature_prod_modal').modal('hide');
                }else{
                   // alert('test');
                    checkfeaturedproducts();
                }

            }
    });

}


function checkfeaturedproducts()
{
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    $.ajax({
        type:'post',
        url:base_url+'admin/Main_products/get_feutured_products_count/',
        success:function(data){
            var res = data.result;
         
            if (data >= 7){
                ////showcpToast("info", "Warning!", "You have reached the maximum of 7 featured products allowed.");
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
               $("#entry-feat-product-arrangement").val('').change();
               $('.contsellingdiv').show(250);
            }
        }
    });

}


$('#entry-feat-product-arrangement').change(function(){ 
    var product_number = $(this).val();
    check_featuredProduct_Number(product_number);
});

function check_featuredProduct_Number(product_number)
{
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    //product_id  = $("#u_id").val()
   // var product_id = product_id;
  //  alert(product_id);
    $.ajax({
        method: "POST",
        url: base_url+'admin/Main_products/check_feutured_product_arrangement/'+product_number,
            success: function(data){

               // alert(data);
                if(data == 1) {  
                    ////showcpToast("info", "Warning!", "This number is already selected...");           
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
                    $("#entry-feat-product-arrangement").val('').change();
                }else{
                   // alert('test');
                   // checkfeaturedproducts();
                }

            }
    });

}


$('#uncheck_rabutton').click(function(){
    $("#featured_prod_isset").prop("checked", false);
    $("#entry-feat-product-arrangement").selectmenu('refresh');
});


$('#featured_prod_isset').click(function(){
    if($(this).is(':unchecked')){
        // $("#featured_prod_isset").prop("checked", false);
        $("#entry-feat-product-arrangement").val('').change();
    }else{
      
    }
}); 


function check_featuredproductCheck()
{
    var base_url     = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    product_id  = $("#u_id").val()
    // $.ajax({
    //     method: "POST",
    //     url: base_url+'admin/Main_products/check_feutured_products/'+product_id,
    //         success: function(data){

    //            // alert(data);
    //             if(data == 1) {
    //                 $('.contsellingdiv').show(250); 
    //             }else{
    //                 $('.contsellingdiv').hide(250);
    //             }

    //         }
    // });

}

$(document).delegate('.commcapping','input',function(e){
    var self = $(this);
    if (self.val() > 30 || self.val() < 0) 
    {
        self.val('');
    }
});



    
