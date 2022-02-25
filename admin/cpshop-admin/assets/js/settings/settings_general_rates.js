$(document).ready(function(){

    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var shop_url = $("body").data('shop_url');
    var token    = $("body").data('token');

    var fillDataTableProducts = function() {
        dataTable = $('#table-grid_products').DataTable({
            destroy: true,
            "serverSide": true,
             "columnDefs": [
                { "orderable": false, "targets": [ 0 ], "className": "text-center" }
            ],
            responsive: true,
            "ajax":{
                url:base_url+"shipping_delivery/Settings_shipping_delivery/product_list", // json datasource
                type: "post",  // method  , by default get
                data: {
                    'shop_id': $('#shop_id_md5').val() 
                },
                beforeSend:function(data){
                    $.LoadingOverlay("show"); 
                },
                complete: function(data) {  
                    $.LoadingOverlay("hide"); 
                },
                error: function(){  // error handling
                    $.LoadingOverlay("hide"); 
                    $(".table-grid-error").html("");
                    $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });
    }

    fillDataTableProducts();
    
    ///backup, old way 1
    $("#regCode_backup").change(function() {
        $.LoadingOverlay("show"); 
        $("#citymunCode option").remove();
        regCode = $(this).val();

        $.ajax({
            type:'post',
            url:base_url+'shipping_delivery/Settings_shipping_delivery/get_province',
            data:{
                'regCode': $(this).val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                var json_data = JSON.parse(data);

                if(json_data.success){
                    // $('#citymunCode')
                    //      .append($("<option></option>")
                    //         .attr("value", "")
                    //         .attr("readonly", "")
                    //         .text(""));

                    if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.provCode) != parseFloat(0) && e.status === 1).length > 0){
                    }
                    else{
                        // $('#citymunCode')
                        //      .append($("<option></option>")
                        //         .attr("value", "0")
                        //         .text("ENTIRE REGION"));

                        // $('#citymunCode')
                        //      .append($("<option></option>")
                        //         .attr("value", "0")
                        //         .text("ENTIRE PROVINCE"));
                    }

                    $.each(json_data.data, function(key, value) {
                        // if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.provCode) === parseFloat(value.provCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.citymunCode) != parseFloat(0) && e.status === 1).length > 0 ){
                        // }else{
                            $('#citymunCode')
                                 .append($("<option></option>")
                                    .attr("value", value.citymunCode)
                                    .text(value.citymunDesc+" ("+value.provDesc+")"));
                        // }   
                      
                    });
                    $('#citymunCode').prop('disabled', false);
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
    
    ///backup, old way 2
    $("#regCode_backup").change(function() {
        $.LoadingOverlay("show"); 
        $("#provCode option").remove();
        $("#citymunCode option").remove();
        $('#citymunCode').attr("disabled", "");
        regCode = $(this).val();

        $.ajax({
            type:'post',
            url:base_url+'shipping_delivery/Settings_shipping_delivery/get_province',
            data:{
                'regCode': $(this).val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                var json_data = JSON.parse(data);

                if(json_data.success){
                    $('#provCode')
                         .append($("<option></option>")
                            .attr("value", "")
                            .attr("readonly", "")
                            .text(""));

                    if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.provCode) != parseFloat(0) && e.status === 1).length > 0){
                    }
                    else{
                        $('#provCode')
                             .append($("<option></option>")
                                .attr("value", "0")
                                .text("ENTIRE REGION"));

                        $('#citymunCode')
                             .append($("<option></option>")
                                .attr("value", "0")
                                .text("ENTIRE PROVINCE"));
                    }

                    $.each(json_data.data, function(key, value) {
                        // if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.provCode) === parseFloat(value.provCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.citymunCode) != parseFloat(0) && e.status === 1).length > 0 ){
                        // }else{
                            $('#provCode')
                                 .append($("<option></option>")
                                    .attr("value", value.provCode)
                                    .text(value.provDesc));  
                        // }   
                      
                    });
                    $('#provCode').prop('disabled', false);
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
    ///backup, old way 1
    $("#provCode_backup").change(function() {
        $.LoadingOverlay("show"); 
        $("#citymunCode option").remove();
        provCode = $(this).val();

        $.ajax({
            type:'post',
            url:base_url+'shipping_delivery/Settings_shipping_delivery/get_citymun',
            data:{
                'provCode': $(this).val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                var json_data = JSON.parse(data);

                if(json_data.success){
                    $('#citymunCode')
                         .append($("<option></option>")
                            .attr("value", "")
                            .attr("readonly", "")
                            .text(""));
                    if(zoneArray.filter(e => parseFloat(e.provCode) === parseFloat(provCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.citymunCode) != parseFloat(0) && e.status === 1).length > 0 && parseFloat(provCode) != 0){
                    }else{
                        $('#citymunCode')
                             .append($("<option></option>")
                                .attr("value", "0")
                                .text("ENTIRE PROVINCE"));   
                    }
                    

                    $.each(json_data.data, function(key, value) {
                        if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.provCode) === parseFloat(value.provCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.citymunCode) === parseFloat(value.citymunCode) && e.status === 1).length > 0){
                        }
                        else if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.provCode) === parseFloat(value.provCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.citymunCode) === parseFloat(0) && e.status === 1).length > 0){
                        }else{
                            $('#citymunCode')
                                .append($("<option></option>")
                                    .attr("value", value.citymunCode)
                                    .text(value.citymunDesc));
                        }   
                          
                    });
                    $('#citymunCode').prop('disabled', false);

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
    ///backup, old way 1
    $("#set_entire_region_backup").change(function() {

        if($(this).is(':checked')){
            $('#citymunCode').prop('disabled', true);
        }else{
            $('#citymunCode').prop('disabled', false);
        }
    });

    let zoneArray              = [];
    let branchArray            = [];
    let rateArray              = [];
    let rateExists             = 0;
    let shipping_access_create = $('#shipping_access_create').val();
    let shipping_access_update = $('#shipping_access_update').val();
    let shipping_access_delete = $('#shipping_access_delete').val();

    /// preload saved general rates
    shop_id_md5 = $('#shop_id_md5').val();
    if(shop_id_md5){
        $.ajax({
            type:'post',
            url:base_url+'shipping_delivery/Settings_shipping_delivery/get_general_rates',
            data:{
                'shop_id_md5': $('#shop_id_md5').val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 

                var json_data = JSON.parse(data);

                if(json_data.success){
                    rateExists = json_data.shipping_id;
                    regDescCons = "";
                    provDescCons = "";
                    citymunDescCons = "";
                    checker_f_key = 1000000;
                    counter = 0;


                    $.each(json_data.shipping_zone, function(key, value) {
                        regDescCons = "";
                        provDescCons = "";
                        citymunDescCons = "";

                        if(value.array_f_key == '' || value.array_f_key == null){
                            f_key = counter;

                            if(parseFloat(value.provCode) == parseFloat(0) && parseFloat(value.citymunCode) == parseFloat(0)){
                            regDescCons += value.regDesc;
                            }
                            if(parseFloat(value.citymunCode) == parseFloat(0)){
                            provDescCons += value.provDescCons;
                            }
                            if(parseFloat(value.regCode) != parseFloat(0) && parseFloat(value.provCode) != parseFloat(0)){
                            citymunDescCons += value.citymunDescCons;
                            }

                        }else{
                            f_key = value.array_f_key;
                        }

                        $.each(json_data.shipping_zone, function(key2, value2) {
                            if(f_key == value2.array_f_key){
                                if(parseFloat(value2.provCode) == parseFloat(0) && parseFloat(value2.citymunCode) == parseFloat(0)){
                                    regDescCons += value2.regDesc+", ";
                                }
                                if(parseFloat(value2.citymunCode) == parseFloat(0)){

                                    if(parseFloat(value2.provCode) != parseFloat(0)){
                                        provDescCons += value2.provDescCons+", ";
                                    }
                                    
                                }
                                if(parseFloat(value2.regCode) != parseFloat(0) && parseFloat(value2.provCode) != parseFloat(0)){
                                    if(parseFloat(value2.citymunCode) != parseFloat(0)){
                                        citymunDescCons += value2.citymunDescCons+", ";
                                    }
                                }
                            }
                        });
                        
                        branchname   = "(";
                        if(parseFloat(checker_f_key) != parseFloat(f_key)){
                            $.each(json_data.shipping_zone_branches[key], function(subkey, subvalue) {
                                
                                if(subvalue.branch_id != 0){
                                    dataArr = {
                                        'index_id'      : f_key,
                                        'branch_id'     : subvalue.branch_id,
                                        'status'        : 1
                                    };

                                    branchArray.push(dataArr);
                                    branchname += subvalue.branchname+", ";
                                }else{
                                    dataArr = {
                                        'index_id'      : f_key,
                                        'branch_id'     : subvalue.branch_id,
                                        'status'        : 0
                                    };

                                    branchArray.push(dataArr);
                                    branchname = "(Main";
                                }
                                
                                
                            });

                            if(json_data.shipping_zone_branches[key].length == 0){
                                branchname = "(Main";
                            }
                        }
                        branchname = branchname.replace(/,\s*$/, "");
                        branchname += ")";

                        dataArr = {
                            'zone_name'   : value.zone_name,
                            'regCode'     : value.regCode,
                            'regDesc'     : value.regDesc,
                            'provCode'    : value.provCode,
                            'provDesc'    : value.provDesc,
                            'citymunCode' : value.citymunCode,
                            'citymunDesc' : value.citymunDesc,
                            'branchname'  : branchname,
                            'f_key'       : f_key,
                            'regDescCons'  : regDescCons,
                            'provDescCons'  : provDescCons,
                            'citymunDescCons'  : citymunDescCons,
                            'status'      : 1
                        };
                        zoneArray.push(dataArr);

                        
                        if(parseFloat(checker_f_key) != parseFloat(f_key)){
                            $.each(json_data.shipping_zone_rates[key], function(subkey, subvalue) {
                                
                                dataArr = {
                                    'index_id'      : f_key,
                                    'rate_name'     : subvalue.rate_name,
                                    'rate_amount'   : subvalue.rate_amount,
                                    'is_condition'  : subvalue.is_condition,
                                    'minimum_value' : subvalue.condition_min_value,
                                    'maximum_value' : subvalue.condition_max_value,
                                    'from_day'      : subvalue.from_day,
                                    'to_day'        : subvalue.to_day,
                                    'additional_isset' : subvalue.additional_isset,
                                    'set_value'     : subvalue.set_value,
                                    'set_amount'    : subvalue.set_amount,
                                    'status'        : 1
    
                                };
    
                                rateArray.push(dataArr);
                                
                            });
                        }
                        counter++;
                        checker_f_key = f_key;
                        
                    });
                    displayZone();
                    displayRate();

                }else{

                }
            },
        });
    }
    

    $('#addZoneBtn').click(function(){

        zone_name    = $('#zone_name').val();
        regCode      = $('#regCode').val();
        regDesc      = $('#regCode option:selected').text();
        provCode     = $('#provCode').val();
        provDesc     = $('#provCode option:selected').text();
        citymunCode  = $('#citymunCode').val();
        citymunDesc  = $('#citymunCode option:selected').text();
        branch_id    = $('#branch_id').val();
        branchname   = "(";
        regExists  = 0;
        provExists = 0;
        citymunExists = 0;
        regDescCons = "";
        provDescCons = "";
        citymunDescCons = "";
        zone_checker = $('#zone_checker').val();
        zone_f_key   = $('#zone_f_key').val();

        if(zone_checker == 'edit'){
            for(i = zoneArray.length - 1; i >= 0; i--){
                if(parseFloat(zoneArray[i].f_key) === parseFloat(zone_f_key)){
                    zoneArray[i].status = 0;
                 }  
            }

            for(i = branchArray.length - 1; i >= 0; i--){
                if(branchArray[i].index_id === parseFloat(zone_f_key)){
                    branchArray[i].status = 0;
                }  
            }
        }

        $("#regCode option:selected").each(function () {
            var $this = $(this);

            if(zoneArray.filter(e => parseFloat(e.regCode)=== parseFloat($this.val()) && e.status === 1 && parseFloat(e.provCode) === parseFloat(0) && parseFloat(e.citymunCode) === parseFloat(0)).length > 0){
                regExists = 1;
            }

            regDescCons += $this.text()+", ";
        });

        $("#provCode option:selected").each(function () {
            var $this = $(this);

            if(zoneArray.filter(e => parseFloat(e.provCode)=== parseFloat($this.val()) && e.status === 1 && parseFloat(e.citymunCode) === parseFloat(0)).length > 0){
                provExists = 1;
            }
            
            provDescCons += $this.text()+", ";
        });

        $("#citymunCode option:selected").each(function () {
            var $this = $(this);

            if(zoneArray.filter(e => parseFloat(e.citymunCode)=== parseFloat($this.val()) && e.status === 1).length > 0){
                citymunExists = 1;
            }
            
            citymunDescCons += $this.text()+", ";
        });


        if(zone_name == ''){
            //sys_toast_warning('Please input all the required fields.');
            showCpToast("warning", "Warning!", 'Please input all the required fields.');
        }
        else if(regCode.length == 0 && provCode.length == 0 && citymunCode.length == 0){
            //sys_toast_warning('Please select at least 1 area.');
            showCpToast("warning", "Warning!", 'Please select at least 1 area.');
        }
        else if(zoneArray.filter(e => e.zone_name === zone_name && e.status === 1).length > 0){
            //sys_toast_warning('Zone Name already exists.');
            showCpToast("warning", "Warning!", 'Zone Name already exists.');
        }
        // else if(regExists == 1){
        //     sys_toast_warning('Region already exists.');
        // }
        // else if(provExists == 1){
        //     sys_toast_warning('Province already exists.');
        // }
        // else if(citymunExists == 1){
        //     sys_toast_warning('City/Muncipality already exists.');
        // }
        else{
            index_id = zoneArray.length;
            f_key = zoneArray.length;
            
            max_f_key = 0;
            for(i = 0; i < zoneArray.length; i++){
                curr_f_key = zoneArray[i].f_key;
                if(parseFloat(curr_f_key) > parseFloat(max_f_key)){
                    max_f_key = curr_f_key;
                }
            }

            f_key = parseInt(max_f_key) + 1;

            if(branch_id.length > 0){
                $.each(branch_id, function(key, value) {
                    dataArr = {
                        'index_id'    : f_key,
                        'branch_id'   : value,
                        'status'      : 1
                    };

                    branchArray.push(dataArr);
                });

                $("#branch_id option:selected").each(function () {
                    var $this = $(this);
                        if ($this.length) {
                            var selText = $this.text();
                            branchname += selText+", ";
                        }
                });
                branchname = branchname.replace(/,\s*$/, "");
                branchname += ")";
            }else{
                dataArr = {
                    'index_id'    : f_key,
                    'branch_id'   : 0,
                    'status'      : 0
                };

                branchArray.push(dataArr);  
                branchname = "(Main)";
            }

            $("#regCode option:selected").each(function () {
                var $this = $(this);

                dataArr = {
                    'zone_name'   : zone_name,
                    'regCode'     : $this.val(),
                    'regDesc'     : $this.text(),
                    'provCode'    : $this.data('provcode'),
                    'provDesc'    : $this.data('provdesc'),
                    'citymunCode' : $this.data('citymuncode'),
                    'citymunDesc' : $this.data('citymundesc'),
                    'branchname'  : branchname,
                    'f_key'       : f_key,
                    'regDescCons': regDescCons,
                    'provDescCons': provDescCons,
                    'citymunDescCons': citymunDescCons,
                    'status'      : 1
                };
                zoneArray.push(dataArr);
            });

            $("#provCode option:selected").each(function () {
                var $this = $(this);

                dataArr = {
                    'zone_name'   : zone_name,
                    'regCode'     : $this.data('regcode'),
                    'regDesc'     : $this.data('regDesc'),
                    'provCode'    : $this.val(),
                    'provDesc'    : $this.text(),
                    'citymunCode' : $this.data('citymuncode'),
                    'citymunDesc' : $this.data('citymundesc'),
                    'branchname'  : branchname,
                    'f_key'       : f_key,
                    'regDescCons': regDescCons,
                    'provDescCons': provDescCons,
                    'citymunDescCons': citymunDescCons,
                    'status'      : 1
                };
                zoneArray.push(dataArr);
            });

            $("#citymunCode option:selected").each(function () {
                var $this = $(this);

                dataArr = {
                    'zone_name'   : zone_name,
                    'regCode'     : $this.data('regcode'),
                    'regDesc'     : $this.data('regdesc'),
                    'provCode'    : $this.data('provcode'),
                    'provDesc'    : $this.data('provdesc'),
                    'citymunCode' : $this.val(),
                    'citymunDesc' : $this.text(),
                    'branchname'  : branchname,
                    'f_key'       : f_key,
                    'regDescCons': regDescCons,
                    'provDescCons': provDescCons,
                    'citymunDescCons': citymunDescCons,
                    'status'      : 1
                };
                zoneArray.push(dataArr);
            });

            if(zone_checker == 'edit'){
                for(i = rateArray.length - 1; i >= 0; i--){
                    if(rateArray[i].index_id === String(zone_f_key)){
                      rateArray[i].index_id = String(f_key);
                   }  
                }
            }

            
            displayZone();
            displayRate();
            resetZone();
        }
        

    });

    $(document).delegate('#EditZoneBtn','click',function(e){
        arrIndex = $(this).data('value');

        $('#shippingZoneModal').modal('toggle');

        $.each(zoneArray, function(key, value) {
            if(value.status == 1){
                if(parseFloat(value.f_key) == parseFloat(arrIndex)){
                    $('#zone_name').val(value.zone_name);
                    if(parseFloat(value.provCode) == parseFloat(0) && parseFloat(value.citymunCode) == parseFloat(0)){
                        $('#regCode ').children("option[value=" + value.regCode + "]").prop("selected", true);
                        $( "#regCode" ).select2().trigger('change');
                    }
                    if(parseFloat(value.citymunCode) == parseFloat(0)){
                        $('#provCode ').children("option[value=" + value.provCode + "]").prop("selected", true);
                        $( "#provCode" ).select2().trigger('change');
                    }
                    if(parseFloat(value.regCode) != parseFloat(0) && parseFloat(value.provCode) != parseFloat(0)){
                        $('#citymunCode ').children("option[value=" + value.citymunCode + "]").prop("selected", true);
                        $( "#citymunCode" ).select2().trigger('change');
                    }

                    $.each(branchArray, function(key, val) {
                        if(parseFloat(val.status) != parseFloat(0) && parseFloat(val.index_id) == parseFloat(arrIndex)){
                            $('#branch_id ').children("option[value=" + val.branch_id + "]").prop("selected", true);
                            $( "#branch_id" ).select2().trigger('change');
                        }
                    });
                }

            }
        });

        $('#zone_checker').val('edit');
        $('#zone_f_key').val(arrIndex);

    });

    $(document).delegate('#EditRateBtn','click',function(e){
        resetRate();
        arrIndex = $(this).data('value');
        index_id = $(this).data('index_id');

        $('#AddRateModal').modal('toggle');

        $.each(rateArray, function(key, value) {
            if(value.status == 1){
                if(parseFloat(key) == parseFloat(arrIndex)){
                    $('#index_id').val(index_id);
                    $('#rate_name').val(value.rate_name);
                    $('#rate_amount').val(value.rate_amount);
                    $('#from_day').val(value.from_day);
                    $('#to_day').val(value.to_day);

                    if(parseFloat(value.from_day) == parseFloat(0) && parseFloat(value.from_day) == parseFloat(0)){
                        $('.daysshipdiv').hide();
                        $("#sameday_delivery").trigger( "click" );
                    }
                    else{
                        $('.daysshipdiv').show();
                    }

                    if(parseFloat(value.is_condition) != parseFloat(0)){

                        $("#condition_rate").trigger( "click" );
                        $('#to_day').val(value.to_day);

                        if(value.is_condition == "1"){
                            $('.minimum_value').text('Minimum Weight');
                            $('.maximum_value').text('Maximum Weight');
                            $('.set_value_label').text('grams,');
                        }else if(value.is_condition == "2") {
                            $('.minimum_value').text('Minimum Price');
                            $('.maximum_value').text('Maximum Price');
                            $('.set_value_label').text('PHP,');
                        }

                        $("input[name=is_condition][value=" + value.is_condition + "]").prop('checked',true);
                        $('#minimum_value').val(value.minimum_value);
                        $('#maximum_value').val(value.maximum_value);

                        if(value.additional_isset == "1"){
                            $("#additional_isset").trigger( "click" );
                            $('#set_value').val(value.set_value);
                            $('#set_amount').val(value.set_amount);
                        }


                    }else{
                    }
                }
            }
        });

        $('#rate_checker').val('edit');
        $('#rate_p_key').val(arrIndex);

    });

    $(document).delegate('#rateBtn','click',function(e){
        arrIndex = $(this).data('value');
        $('#index_id').val(arrIndex);
    });

    $('#addRateBtn').click(function(){

        index_id      = $('#index_id').val();
        rate_name     = $('#rate_name').val();
        rate_amount   = $('#rate_amount').val();
        is_condition  = $('input[name=is_condition]:checked', '#conditionForm').val();
        minimum_value = $('#minimum_value').val();
        maximum_value = $('#maximum_value').val();
        from_day      = $('#from_day').val();
        to_day        = $('#to_day').val();
        additional_isset = $('input[name=additional_isset]:checked').val();
        set_value     = $('#set_value').val();
        set_amount    = $('#set_amount').val();
        rate_checker  = $('#rate_checker').val();
        rate_p_key    = $('#rate_p_key').val();

        if(rate_checker == 'edit'){
            rateArray[rate_p_key].status = 0;
        }

        if(parseFloat(additional_isset) == 1){
            if(set_value == '' || set_amount == ''){
                additional_condition = 1;
            }else{
                additional_condition = 0;
            }
        }else{
            additional_condition = 0;
        }

        if(index_id == '' || rate_name == '' || rate_amount == '' || from_day == '' || to_day == ''){
            //sys_toast_warning('Please input all the required fields.');
            showCpToast("warning", "Warning!", "Please input all the required fields.");
        }
        else if(parseFloat(from_day) > parseFloat(to_day)){
            //sys_toast_warning('From (Days to ship) is greater than To (Days to ship)');
            showCpToast("warning", "Warning!", 'From (Days to ship) is greater than To (Days to ship)');
        }
        else if(parseFloat(minimum_value) > parseFloat(maximum_value) && parseFloat(maximum_value) != 0.00){
            //sys_toast_warning('Minimum field is greater than maximum field');
            showCpToast("warning", "Warning!", 'Minimum field is greater than maximum field');
        }
        else if(additional_condition == 1){
            //sys_toast_warning('Minimum field is greater than maximum field');
            showCpToast("warning", "Warning!", 'Minimum field is greater than maximum field');
        }
        else{
            if(is_condition != 0){
                if(minimum_value == ''){
                    //sys_toast_warning('Minimum field is empty');
                    showCpToast("warning", "Warning!", 'Minimum field is empty');
                }else{
                    dataArr = {
                        'index_id'      : index_id,
                        'rate_name'     : rate_name,
                        'rate_amount'   : rate_amount,
                        'is_condition'  : is_condition,
                        'minimum_value' : minimum_value,
                        'maximum_value' : maximum_value,
                        'from_day'      : from_day,
                        'to_day'        : to_day,
                        'additional_isset' : additional_isset,
                        'set_value'     : set_value,
                        'set_amount'    : set_amount,
                        'status'        : 1

                    };
                    
                    rateArray.push(dataArr);
                    displayZone();
                    displayRate();
                    resetRate();
                    $('#AddRateModal').modal('hide'); 
                }
            }else{
                dataArr = {
                    'index_id'      : index_id,
                    'rate_name'     : rate_name,
                    'rate_amount'   : rate_amount,
                    'is_condition'  : is_condition,
                    'minimum_value' : minimum_value,
                    'maximum_value' : maximum_value,
                    'from_day'      : from_day,
                    'to_day'        : to_day,
                    'additional_isset' : additional_isset,
                    'set_value'     : set_value,
                    'set_amount'    : set_amount,
                    'status'        : 1

                };

                rateArray.push(dataArr);
                displayZone();
                displayRate();
                resetRate();
                $('#AddRateModal').modal('hide');
            }
        }
        

    });

    $(document).delegate('#deleteZone_backup','click',function(e){

        arrIndex = $(this).data('value');

        alertify.confirm("CONFIRMATION",'Are you sure you want to remove this zone?',
            function(){
                $.LoadingOverlay("show"); 
                
                // zoneArray[arrIndex].status = 0;
                for(i = zoneArray.length - 1; i >= 0; i--){
                    if(parseFloat(zoneArray[i].f_key) === parseFloat(arrIndex)){
                        zoneArray[i].status = 0;
                     }  
                }

                for(i = branchArray.length - 1; i >= 0; i--){
                    if(branchArray[i].index_id === String(arrIndex)){
                      branchArray[i].status = 0;
                   }  
                }

                for(i = rateArray.length - 1; i >= 0; i--){
                    if(rateArray[i].index_id === String(arrIndex)){
                      rateArray[i].status = 0;
                   }  
                }

                zoneArray = zoneArray.filter(function( obj ) {
                    return obj.status !== 0;
                });

                rateArray = rateArray.filter(function( obj ) {
                    return obj.status !== 0;
                });
    
                   
                //sys_toast_success('Zone successfully removed.');
                showCpToast("success", "Success!", 'Zone successfully removed.');
                $('#confirmBtn').prop('disabled', false);
                $.LoadingOverlay("hide"); 
                displayZone();
                displayRate();
            },
            function(){
            }
        );

    });

    $(document).delegate('#deleteZone','click',function(e){

        arrIndex = $(this).data('value');
        $('#DeleteZoneModal').modal('toggle');
        $('#delete_id_zone').val(arrIndex);
    });

    $('#DeleteZoneConfirmBtn').click(function(){
        arrIndex = $('#delete_id_zone').val();
        $.LoadingOverlay("show"); 
        
        // zoneArray[arrIndex].status = 0;
        for(i = zoneArray.length - 1; i >= 0; i--){
            if(parseFloat(zoneArray[i].f_key) === parseFloat(arrIndex)){
                zoneArray[i].status = 0;
                }  
        }

        for(i = branchArray.length - 1; i >= 0; i--){
            if(branchArray[i].index_id === String(arrIndex)){
                branchArray[i].status = 0;
            }  
        }

        for(i = rateArray.length - 1; i >= 0; i--){
            if(rateArray[i].index_id === String(arrIndex)){
                rateArray[i].status = 0;
            }  
        }

        zoneArray = zoneArray.filter(function( obj ) {
            return obj.status !== 0;
        });

        rateArray = rateArray.filter(function( obj ) {
            return obj.status !== 0;
        });

        $('#DeleteZoneModal').modal('toggle');
        //sys_toast_success('Zone successfully removed.');
        showCpToast("success", "Success!", 'Zone successfully removed.');
        $('#confirmBtn').prop('disabled', false);
        $.LoadingOverlay("hide"); 
        displayZone();
        displayRate();

    });

    $(document).delegate('#deleteRate_backup','click',function(e){

        arrIndex = $(this).data('value');

        alertify.confirm("CONFIRMATION",'Are you sure you want to remove this rate?',
            function(){
                $.LoadingOverlay("show"); 
                rateArray[arrIndex].status = 0;
                //sys_toast_success('Rate successfully removed.');
                showCpToast("success", "Success!", 'Rate successfully removed.');
                $('#confirmBtn').prop('disabled', false);
                $.LoadingOverlay("hide"); 
                displayZone();
                displayRate();
            },
            function(){
            }
        );

    });

    $(document).delegate('#deleteRate','click',function(e){

        arrIndex = $(this).data('value');
        $('#DeleteRateModal').modal('toggle');
        $('#delete_id_rate').val(arrIndex);
    });

    $('#DeleteRateConfirmBtn').click(function(){
        arrIndex = $('#delete_id_rate').val();

        $('#DeleteRateModal').modal('toggle');
        $.LoadingOverlay("show"); 
        rateArray[arrIndex].status = 0;
        //sys_toast_success('Rate successfully removed.');
        showCpToast("success", "Success!", 'Rate successfully removed.');
        $('#confirmBtn').prop('disabled', false);
        $.LoadingOverlay("hide"); 
        displayZone();
        displayRate();

    });

    $('#condition_rate').click(function(){
        condition = $(this).text();

        if(condition == 'Add Condition'){
            $(this).text('Remove Condition');
            $('.conditiondiv').show('slow');
            $('.additionaldiv').show('slow');
            $('.additionaldiv2').hide('slow');
            $("input[name=is_condition][value='1']").prop("checked",true);
        }else{
            $(this).text('Add Condition');
            $('.conditiondiv').hide('slow');
            $('.additionaldiv').hide('slow');
            $('.additionaldiv2').hide('slow');
            $('#minimum_value').val('');
            $('#maximum_value').val('');
            $('.minimum_value').text('Minimum Weight');
            $('.maximum_value').text('Maximum Weight');
            $('.maximum_value').text('Maximum Weight');
            $("input[name=is_condition][value='0']").prop("checked",true);
            $("#additional_isset").prop("checked", false);
            $('#set_value').val('');
            $('#set_amount').val('');
        }

        
    });

    $('#additional_isset').click(function(){

        if($(this).is(':checked')){
            $('.additionaldiv2').show('slow');
        }else{
            $('.additionaldiv2').hide('slow');
            $('#set_value').val('');
            $('#set_amount').val('');
        }

    });

    $('#sameday_delivery').click(function(){
        if($(this).is(':checked')){
            $('.daysshipdiv').hide('slow');
            $('#from_day').val(0);
            $('#to_day').val(0);
        }else{
            $('.daysshipdiv').show('slow');
        }
    });

    $('#conditionForm input').on('change', function() {
       is_condition = $('input[name=is_condition]:checked', '#conditionForm').val();

       if(is_condition == 1){
            $('.minimum_value').text('Minimum Weight');
            $('.maximum_value').text('Maximum Weight');
            $('.set_value_label').text('grams,');
        }else if(is_condition == 2) {
            $('.minimum_value').text('Minimum Price');
            $('.maximum_value').text('Maximum Price');
            $('.set_value_label').text('PHP,');
        }else{
            resetRate();
        }
        $('#minimum_value').val('');
        $('#maximum_value').val(''); 
    });

    function displayZone(){
        $('#zoneDiv').empty();
        checker_f_key = 1000000;
        counter = 0;
        $.each(zoneArray, function(key, value) {
            
            if(value.status == 1){
                if(parseFloat(value.f_key) != parseFloat(checker_f_key)){
                    table = "<table class='table table-striped table-hover table-bordered table-grid display nowrap'><thead><tr><th scope='col'><b>Rate Name</b></th><th scope='col'><b>Condition</b></th><th scope='col'><b>Price</b></th><th scope='col'><b>Days to Ship</b></th><th scope='col'><b>Additional</b></th><th scope='col'></th></tr></thead><tbody id='tbody_"+value.f_key+"' class='tbody_"+value.f_key+"'></tbody></table>";
                    if(shipping_access_update == 1 && shipping_access_delete == 1){
                        $('#zoneDiv').append("<button type='button' id='deleteZone' data-value='"+value.f_key+"' class='close' aria-label='Close'><span aria-hidden='true' ><h1>&times;</h1></span></button><button type='button' id='EditZoneBtn' data-value='"+value.f_key+"' class='close' aria-label='Close'><span aria-hidden='true' ><h4><i class='fa fa-pencil'></i>&nbsp;</h4></span></button>");
                    }
                    else if(shipping_access_update == 1 && shipping_access_delete == 0){
                        $('#zoneDiv').append("<button type='button' id='EditZoneBtn' data-value='"+value.f_key+"' class='close' aria-label='Close'><span aria-hidden='true' ><h4><i class='fa fa-pencil'></i>&nbsp;</h4></span></button>");
                    }
                    else if(shipping_access_update == 0 && shipping_access_delete == 1){
                        $('#zoneDiv').append("<button type='button' id='deleteZone' data-value='"+value.f_key+"' class='close' aria-label='Close'><span aria-hidden='true' ><h1>&times;</h1></span></button>");
                    }
                    else{
                        $('#zoneDiv').append("");
                    }
                    $('#zoneDiv').append("<h1>"+value.zone_name+"</h1>");
                    $('#zoneDiv').append("<h5><b>Region: </b>"+value.regDescCons+"</h5>");
                    $('#zoneDiv').append("<h5><b>Province: </b>"+value.provDescCons+"</h5>");
                    $('#zoneDiv').append("<h5><b>City/Municipality: </b>"+value.citymunDescCons+"</h5>");
                    $('#zoneDiv').append("<h6><b>Branch: </b>"+value.branchname+"</h6>");
                    $('#zoneDiv').append(table);
                    if(shipping_access_create == 1){
                        $('#zoneDiv').append("<button type='button' id='rateBtn' data-toggle='modal' data-value='"+value.f_key+"' data-target='#AddRateModal' class='btn btn-primary'>Add Rate</button><hr>");
                    }
                    else{
                        $('#zoneDiv').append("");
                    }
                }

                checker_f_key = value.f_key;
                
            }   
        });

        console.log(zoneArray);
        console.log(branchArray);

    }

    function displayRate(){

        $.each(rateArray, function(key, value) {   
            if(value.status == 1){
                $('#tbody_'+value.index_id).append("<tr>");
                $('#tbody_'+value.index_id).append("<td>"+value.rate_name+"</td>");

                if(value.is_condition == 1){
                    if(value.maximum_value == '' || value.maximum_value == 0.00 || value.maximum_value == 0){
                        condition = formatMoney(value.minimum_value, 2)+" minimum grams";
                    }else{
                        condition = formatMoney(value.minimum_value, 2)+" to "+formatMoney(value.maximum_value, 2)+" grams"; 
                    }
                }
                else if(value.is_condition == 2){
                    if(value.maximum_value == '' || value.maximum_value == 0.00 || value.maximum_value == 0){
                        condition = formatMoney(value.minimum_value, 2)+" minimum price"; 
                    }else{
                        condition = formatMoney(value.minimum_value, 2)+" to "+formatMoney(value.maximum_value, 2)+" price";  
                    }
                }else{
                    condition = "N/A";
                }

                $('#tbody_'+value.index_id).append("<td>"+condition+"</td>");

                if(value.rate_amount == 0){
                    $('#tbody_'+value.index_id).append("<td>Free</td>");
                }else{
                    $('#tbody_'+value.index_id).append("<td>"+formatMoney(value.rate_amount, 2)+"</td>");
                }

                if(parseFloat(value.from_day) == parseFloat(value.to_day) && parseFloat(value.to_day) != parseFloat(0)){
                    $('#tbody_'+value.index_id).append("<td>"+value.to_day+" days</td>");
                }
                else if(parseFloat(value.from_day) == parseFloat(0) && parseFloat(value.to_day) == parseFloat(0)){
                    $('#tbody_'+value.index_id).append("<td>Delivers within 24 hours</td>");
                }
                else{
                    $('#tbody_'+value.index_id).append("<td>"+value.from_day+" to "+value.to_day+" days</td>");
                }

                if(value.additional_isset == 1){
                    if(value.is_condition == 1){
                        $('#tbody_'+value.index_id).append("<td>For every succeeding "+formatMoney(value.set_value)+" grams, add additional "+formatMoney(value.set_amount)+" PHP.</td>");
                    }
                    else if(value.is_condition == 2){
                        $('#tbody_'+value.index_id).append("<td>For every succeeding "+formatMoney(value.set_value)+" PHP, add additional "+formatMoney(value.set_amount)+" PHP.</td>");
                    }
                    
                }else{
                    $('#tbody_'+value.index_id).append("<td>N/A</td>");
                }
                if(shipping_access_update == 1 && shipping_access_delete == 1){
                    $('#tbody_'+value.index_id).append("<td><button type='button' id='EditRateBtn' data-value='"+key+"' data-index_id='"+value.index_id+"' class='btn btn-success'>Edit</button>&nbsp;<button type='button' id='deleteRate' data-value='"+key+"' class='btn btn-danger'>Remove</button></td>");
                }
                else if(shipping_access_update == 1 && shipping_access_delete == 0){
                    $('#tbody_'+value.index_id).append("<td><button type='button' id='EditRateBtn' data-value='"+key+"' data-index_id='"+value.index_id+"' class='btn btn-success'>Edit</button></td>");
                }
                else if(shipping_access_update == 0 && shipping_access_delete == 1){
                    $('#tbody_'+value.index_id).append("<td><button type='button' id='deleteRate' data-value='"+key+"' class='btn btn-danger'>Remove</button></td>");
                }
                else{
                    $('#tbody_'+value.index_id).append("<td></td>");
                }
                $('#tbody_'+value.index_id).append("</tr>");
            }
        });

        console.log(rateArray);
    }

    function resetZone(){
        $('#shippingZoneModal').modal('hide');
        $('#zone_name').val('');
        $('#regCode').prop('selectedIndex',-1);
        $("#regCode").select2("destroy");
        $("#regCode").select2();
        $('#provCode').prop('selectedIndex',-1);
        $("#provCode").select2("destroy");
        $("#provCode").select2();
        $('#citymunCode').prop('selectedIndex',-1);
        $("#citymunCode").select2("destroy");
        $("#citymunCode").select2();
        $('#branch_id').prop('selectedIndex',-1);
        $("#branch_id").select2("destroy");
        $("#branch_id").select2();
        $('#confirmBtn').prop('disabled', false);
        // $('#branch_id').prop('selectedIndex',-1);
        $("#set_entire_region").prop("checked", false);
        $('#zone_checker').val('add');
        $('#zone_f_key').val('');

    }

    function resetRate(){
        $('#rate_name').val('');
        $('#rate_amount').val('');
        $('#minimum_value').val('');
        $('#maximum_value').val('');
        $('#from_day').val(0);
        $('#to_day').val(0);
        $('#index_id').val('');
        $("input[name=is_condition][value='0']").prop("checked",true);
        $("#additional_isset").prop("checked", false);
        $('#set_value').val('');
        $('#set_amount').val('');
        $('#condition_rate').text('Add Condition');
        $('.conditiondiv').hide('slow');
        $('.additionaldiv').hide('slow');
        $('.additionaldiv2').hide('slow');
        $('#confirmBtn').prop('disabled', false);
        $('#rate_checker').val('add');
        $('#rate_p_key').val('');
        $('.set_value_label').text('grams,');
        $("#sameday_delivery").prop("checked", false);
        $('.daysshipdiv').show();

    }


    $("#SaveRateBtn").click(function(){
        $("#SaveRateBtn").prop("disabled", true);

        shop_id   = $('#shop_id').val();
        zoneClear = 0;

        for(i = zoneArray.length - 1; i >= 0; i--){
            if(zoneArray[i].status === 1){
              zoneClear = 1;
           }  
        }
        $.LoadingOverlay("show"); 

        if(zoneClear == 0){
            $.LoadingOverlay("hide"); 
            //sys_toast_warning('The Zone is empty. Please add Zone details.');
            showCpToast("warning", "Warning!", 'The Zone is empty. Please add Zone details.');
            $("#SaveRateBtn").prop("disabled", false);
        }else{
            $.ajax({
                type:'post',
                url:base_url+'shipping_delivery/Settings_shipping_delivery/save_general_rates',
                data:{
                    'shop_id': shop_id,
                    'zoneArray': JSON.stringify(zoneArray),
                    'branchArray': JSON.stringify(branchArray),
                    'rateArray': JSON.stringify(rateArray),
                    'rateExists': rateExists
                },
                success:function(data){
                    $.LoadingOverlay("hide"); 
                    var json_data = JSON.parse(data);

                    if(json_data.success){
                        //sys_toast_success('General Shipping successfully saved.');
                        showCpToast("success", "Success!", 'General Shipping successfully saved.');
                        window.location.assign(base_url+"Settings_shipping_delivery/shipping_delivery/"+token);
                        // location.reload();
                    }else{
                        //sys_toast_warning('No data found');
                        showCpToast("warning", "Warning!", 'No data found');
                        $("#SaveRateBtn").prop("disabled", false);
                    }
                },
                error: function(error){
                    $.LoadingOverlay("hide"); 
                    //sys_toast_error('Error');
                    showCpToast("error", "Error!", 'Error');
                }
            });
        }
       

    });

    $('.cancelBtn').click(function(){
        resetZone();
    });
    
    $('#closeRateBtn').click(function(){
        resetRate();
    });
});