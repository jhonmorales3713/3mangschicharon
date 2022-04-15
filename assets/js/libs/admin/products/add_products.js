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

    if($('#f_tq_isset').is(':checked')){
        $('.contsellingdiv, .nostocksdiv').show(250);
    }

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
    $(".f_discount_product").hide(250);
    $("#btnAddInventory").click(function(){
        if($(".inventory_count").length == 0){

            $("#tbody_inventory").empty();
        }
        displayInventory($(".inventory_count").length+1);
    });
    $("#btnCloseInventory").click(function(){
        // $("#f_discount_product").prop('checked',false);
        // $("#f_days").val(0);
        // $("#f_discount_value").val(0);
        // $(".f_discount_product").hide(0);
        // $("#tbody_inventory").html('');
    });
    function displayInventory(key){
        var str="";
        str += "<tr class='inventory_tr_"+key+" inventory_count'>";
        str += "<td class='inventory_"+key+" id_key' >"+key+"<input type='text'  value='"+key+"' style='display:none;'></td>";
        str += "<td class='inventory_"+key+"'>"+"<input type='text' class='form-control allownumericwithoutdecimal' name='inventory_qty[]' ></td>";
        str += "<td class='inventory_"+key+"'><input class='form-control ' type='date' name='inventory_manufactured[]' ></td>";
        str += "<td class='inventory_"+key+"'><input class='form-control ' type='date'  name='inventory_expiration[]' ></td>";
        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_sku[]'></td>";
        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_barcode[]'></td>";
        str += "<td class='inventory_"+key+"'><button type='button' id='removeInventorySpec' class='btn btn-danger' data-value='"+key+"'><i class='fa fa-trash-o'></i></button></td>";
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

    $(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
    
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
    $('#inventory_modal').on('show.bs.modal', function () {
        // Load up a new modal...
        //$('#inventory_modal').modal('show')
    });$(document).on('hide.bs.modal', '#modal' , function(e){
        var contentArea = $(this).find('#modalContent');
        contentArea.html('');
    });
    $("#btninventory").click(function(){
        $('#inventory_modal').modal('show');
    });
    $("#btnSaveinventory").click(function()
    {
        var form = $("form[name=form_inventory]");
        var form_data = new FormData(form[0]);
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
                    $("#inventory_modal").modal('hide');
                    $("#f_no_of_stocks").val(json_data.qty);
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

    $("#f_discount_product").change(function(){
        if($(this).prop('checked')){
            $(".f_discount_product").show(250);
        }else{
            $(".f_discount_product").hide(250);
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
                    $('<li class="ui-state-default" data-id="'+count+'"  data-directory="'+event.target.result+'" data-filename="'+filename+'"><img id="product_preview" width=100% src="'+event.target.result+'"></li>').appendTo(placeToInsertImagePreview);
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

        $.each(branch_id, function(key, value) {
            form_data.append('f_no_of_stocks_'+value, $('#f_no_of_stocks_'+value).val());
        });

        var form_data2 = new FormData($("form[name=form_inventory]")[0]);
        for (var pair of form_data2.entries()) {
            form_data.append(pair[0], pair[1]);
        }

        var textinputs = document.querySelectorAll('input[name*=variant_name]');
        for( var i = 0; i < textinputs.length; i++ ){
            form_data.append(textinputs[i].name, textinputs[i].value);
        }

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

            var save = 1;
        
        if(save == 1){
            $.ajax({
                type: 'post',
                url: base_url+'admin/Main_products/save_product',
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
                        window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                    }else{
                        //sys_toast_warning(json_data.message);
                        sys_toast_warning(json_data.message);
                    }
                },
                error: function(error){
                    sys_toast_warning(json_data.message);
                    //showCpToast("warning", "Warning!", json_data.message);
                }
            });
        }
        else{
            $.LoadingOverlay("hide");
            sys_toast_warning('Percentage of Account Type Commission Rate should not be more than 50% of Merchant Commission Rate.');
        }
    });
    
    // $("#f_member_shop").change(function(){
    //     shop_id = $(this).val();

    //     $.ajax({
    //         type:'get',
    //         url:base_url+'admin/Main_products/get_sys_branch_profile/'+shop_id,
    //         success:function(data){
    //             $.LoadingOverlay("hide");
    //             var json_data = JSON.parse(data);

    //             if(json_data.success){
    //                 $('#nostocksdiv').empty();
    //                 $('#nostocksdiv2').empty();

    //                 if(json_data.branchid == 0){
    //                     option_str = "";
    //                     $.each(json_data.details, function(key, value) {
    //                         option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
    //                     });

    //                     $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'><option value='0' selected>Main</option>"+option_str+"</select></div>");
                        
    //                     $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_0'><label>Available quantity(Main Branch)</label><input type='number' class='form-control parentProductStock' name='f_no_of_stocks_0' id='f_no_of_stocks_0' placeholder='Number of stocks' value='0'></div>");
    //                     $('#nostocksdiv2').append("<input type='hidden' value='0' name='branch[]'>");

    //                     $.each(json_data.details, function(key, value) {
    //                         $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"' style='display:none'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control parentProductStock' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
    //                         $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
    //                     });
    //                 }else{
    //                     option_str = "";
    //                     $.each(json_data.details, function(key, value) {
    //                         option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
    //                     });

    //                     $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'>"+option_str+"</select></div>");

    //                     $.each(json_data.details, function(key, value) {
    //                         $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"' style='display:none'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
    //                         $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
    //                     });
    //                 }
    //             }else{
    //                 $('#nostocksdiv').empty();
    //                 $('#nostocksdiv2').empty();

    //                 $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'><option value='0' selected>Main</option></select></div>");

    //                 $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_0'><label>Available quantity(Main Branch)</label><input type='number' class='form-control' name='f_no_of_stocks_0' id='f_no_of_stocks_0' placeholder='Number of stocks' value='0'></div>");
    //                 $('#nostocksdiv2').append("<input type='hidden' value='0' name='branch[]'>");
    //             }
    //         }
    //     });
    // });

    $(document).delegate('#f_delivery_location','change',function(e){
        branchid = $(this).val();
        $('.divnostock').hide(100);
        $('#div_no_of_stocks_'+branchid).show(100);
    });

    shopid   = $('#shopid').val();
    branchid = $('#branchid').val();

    // if(shopid != 0){
    //     $.ajax({
    //         type:'get',
    //         url:base_url+'admin/Main_products/get_sys_branch_profile/'+shopid,
    //         success:function(data){
    //             $.LoadingOverlay("hide");
    //             var json_data = JSON.parse(data);

    //             if(json_data.success){
    //                 $('#nostocksdiv').empty();
    //                 $('#nostocksdiv2').empty();

    //                 if(json_data.branchid == 0){
    //                     option_str = "";
    //                     $.each(json_data.details, function(key, value) {
    //                         option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
    //                     });

    //                     $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'><option value='0' selected>Main</option>"+option_str+"</select></div>");
                        
    //                     $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_0'><label>Available quantity(Main Branch)</label><input type='number' class='form-control' name='f_no_of_stocks_0' id='f_no_of_stocks_0' placeholder='Number of stocks' value='0'></div>");
    //                     $('#nostocksdiv2').append("<input type='hidden' value='0' name='branch[]'>");

    //                     $.each(json_data.details, function(key, value) {
    //                         $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"' style='display:none'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
    //                         $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
    //                     });
    //                 }else{
    //                     option_str = "";
    //                     $.each(json_data.details, function(key, value) {
    //                         option_str += "<option value='"+value.id+"'>"+value.branchname+"</option>";
    //                     });

    //                     $('#nostocksdiv').append("<div class='form-group'><label>Shop Branch:</label><select class='form-control' name='f_delivery_location' id='f_delivery_location'>"+option_str+"</select></div>");

    //                     $.each(json_data.details, function(key, value) {
    //                         $('#nostocksdiv2').append("<div class='form-group divnostock' id='div_no_of_stocks_"+value.id+"'><label>Available quantity("+value.branchname+")</label><input type='number' class='form-control' name='f_no_of_stocks_"+value.id+"' id='f_no_of_stocks_"+value.id+"' placeholder='Number of stocks' value='0'></div>");
    //                         $('#nostocksdiv2').append("<input type='hidden' value='"+value.id+"' name='branch[]'>");
    //                     });
    //                 }
    //             }
    //         }
    //     });
    // }

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
                url:base_url+'admin/Main_products/get_feutured_products_count/',
                success:function(data){
                    var res = data.result;
                    if (data >= 7){
                        sys_toast_warning( "You have reached the maximum of 7 featured products allowed.");
                        //showCpToast("warning", "Warning!", "You have reached the maximum of 7 featured products allowed.");
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
        
            //product_id  = $("#u_id").val()
           // var product_id = product_id;
          //  alert(product_id);
            $.ajax({
                method: "POST",
                url: base_url+'admin/Main_products/check_feutured_product_arrangement/'+product_number,
                    success: function(data){
        
                       // alert(data);
                        if(data == 1) {
                            sys_toast_warning("This number is already selected...");
                            //showCpToast("warning", "Warning!", "This number is already selected...");             
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

    count_variant   = 1;
    values_variant  = $("input[id='f_var_option_list']").map(function(){return $(this).val();}).get();;
    values_variant2 = $("input[id='f_var_option_list']").map(function(){return $(this).val();}).get();;
    parentInvQty    = [];

    $('#f_variants_isset').click(function(){
        if($(this).is(':checked')){
            $('.parentVariantDiv').show(250);
            $('.varoptionDiv2').hide();
            $('.varoptionDiv3').hide();

                var inputs = $(".parentProductStock");
                for(var i = 0; i < inputs.length; i++){
                    // console.log($(inputs[i]).val());
                    parentInvQty.push($(inputs[i]).val());
                }
                $('.parentProductStock').val(0);
                $('.parentInvDiv').hide(250);
                // $('.f_otherinfodiv').hide(250);
                // $('#f_otherinfodiv').val('none');
                $("#f_price").hide(250);
        }else{
            $('.parentVariantDiv').hide(250);
                $('.parentInvDiv').show(250);
                // $('.f_otherinfodiv').show(250);
                // $('#f_otherinfodiv').val('');

                var inputs = $(".parentProductStock");
                for(var i = 0; i < inputs.length; i++){
                    parentInvQty.push($(inputs[i]).val());
                }

                inv_counter = 0;
                $(".parentProductStock").each(function(){
                    $(this).val(parentInvQty[inv_counter]);
                    inv_counter++;
                });

                parentInvQty = [];
        }
    });
    count = 1;
    $('#addOptionVariantBtn').click(function(){
        // var index = $(this).data('value');
        if(count == 1){
            
            $('#tbody_variants').empty();
        }
        displayVariants('',count);
        count++;
        //addVariantOption();
    });

    $('.removeVariantBtn').click(function(){
       var index = $(this).data('value');
       removeVariantOption(index);
    });

    $(document).delegate('#removeVariantSpec','click',function(e){
        var index = $(this).data('value');
        $('.variant_tr_'+index).remove();
        count = 1;
        $('.id_key').each(function(index, tr) { 
           // console.log(tr);
            tr.innerText=(count);
            //tr.innerHTML=('2asd');
            count++;
        });

    });

    $(document).delegate('#removeInventorySpec','click',function(e){
        var index = $(this).data('value');
        $('.inventory_tr_'+index).remove();
        count = 1;
        $('.id_key').each(function(index, tr) { 
           // console.log(tr);
            tr.innerText=(count);
            //tr.innerHTML=('2asd');
            count++;
        });

    });

    $(document).on('keyup', '.bootstrap-tagsinput input', function(){
        $(this).attr('placeholder', '')
    })

    setInterval(function(){
        values_variant = $("input[id='f_var_option_list']")
        .map(function(){return $(this).val();}).get();

        if(arrayCompare(values_variant, values_variant2) == false){
            values_variant2 = values_variant;
            alignVariants(values_variant);
        }
    }, 2000);
    
    function addVariantOption(){
        if(count_variant == 1){
            $('.varoptionDiv2').show();
            count_variant = 2;
        }
        else if(count_variant == 2){
            $('.varoptionDiv3').show();
            count_variant = 3;
            $('#addOptionVariantBtn').hide();
        }
    }

    function removeVariantOption(index){
        if(index == 2){
            $('.varoptionDiv2').hide();
            $('.varoption2').val('');
            $('.varoption2').tagsinput('removeAll');
            count_variant = 1;
            $('#addOptionVariantBtn').show();
        }
        else if(index == 3){
            $('#addOptionVariantBtn').show();
            $('.varoptionDiv3').hide();
            $('.varoption3').val('');
            $('.varoption3').tagsinput('removeAll');
            count_variant = 1;
        }
    }

    function alignVariants(values_variant){
        $('#tbody_variants').empty();
        var variants_counter = 0;
        $.each(values_variant[0].split(","), function(key, value) {
            if(values_variant[1].split(",").length != 0){
                $.each(values_variant[1].split(","), function(key2, value2) {
                    if(value2 != ""){
                        if(values_variant[2].split(",").length != 0){
                            $.each(values_variant[2].split(","), function(key3, value3) {
                                if(value3 != ""){
                                    var string = value+"/"+value2+"/"+value3;
                                    displayVariants(string, variants_counter);
                                    variants_counter++;
                                }
                                else{
                                    var string = value+"/"+value2;
                                    displayVariants(string, variants_counter);
                                    variants_counter++;
                                }
                            });
                        }
                    }
                    else{
                        if(values_variant[2].split(",").length != 0){
                            $.each(values_variant[2].split(","), function(key3, value3) {
                                if(value3 != ""){
                                    var string = value+"/"+value3;
                                    displayVariants(string, variants_counter);
                                    variants_counter++;
                                }
                                else{
                                    var string = value;
                                    displayVariants(string, variants_counter);
                                    variants_counter++;
                                }
                            });
                        }
                        // var string = value;
                        // displayVariants(string, variants_counter);
                        // variants_counter++;
                    }
                });
            }
        });
    }

    function displayVariants(string, key){
        str = "";
        str += "<tr class='variant_tr_"+key+"'>";
        str += "<td class='variant_tr_"+key+" id_key' >"+key+"<input type='text'  value='"+key+"' style='display:none;'></td>";
        str += "<td class='variant_tr_"+key+"'>"+"<input type='text' class='form-control' name='variant_name[]' ></td>";
        str += "<td class='variant_tr_"+key+"'><input type='number' min='0' class='form-control allownumericwithdecimal' name='variant_price[]'  placecholder='0.00'></td>";
        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_sku[]'></td>";
        // str += "<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_barcode[]'></td>";
        str += "<td class='variant_tr_"+key+"'><button type='button' id='removeVariantSpec' class='btn btn-danger' data-value='"+key+"'><i class='fa fa-trash'></i></button></td>";
        str += "</tr>";
        $('#tbody_variants').append(str);

        // $('#tbody_variants').append("<tr class='variant_tr_"+key+"'>");
        // $('#tbody_variants').append("<td class='variant_tr_"+key+"'>"+string+"<input type='text' name='variant_name[]' value='"+string+"' style='display:none;'></td>");
        // $('#tbody_variants').append("<td class='variant_tr_"+key+"'><input type='number' class='form-control allownumericwithdecimal' name='variant_price[]' placecholder='0.00'></td>");
        // $('#tbody_variants').append("<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_sku[]'></td>");
        // $('#tbody_variants').append("<td class='variant_tr_"+key+"'><input type='text' class='form-control' name='variant_barcode[]'></td>");
        // $('#tbody_variants').append("<td class='variant_tr_"+key+"'><button type='button' id='removeVariantSpec' class='btn btn-danger' data-value='"+key+"'><i class='fa fa-trash'></i></button></td>");
        // $('#tbody_variants').append("</tr>");
    }
	function isNumberKey(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31
		&& (charCode < 48 || charCode > 57))
			return false;
	
		return true;
	}

    function arrayCompare(_arr1, _arr2) {
        if (
          !Array.isArray(_arr1)
          || !Array.isArray(_arr2)
          || _arr1.length !== _arr2.length
          ) {
            return false;
          }
        
        // .concat() to not mutate arguments
        const arr1 = _arr1.concat().sort();
        const arr2 = _arr2.concat().sort();
        
        for (let i = 0; i < arr1.length; i++) {
            if (arr1[i] !== arr2[i]) {
                return false;
             }
        }
        
        return true;
    }

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