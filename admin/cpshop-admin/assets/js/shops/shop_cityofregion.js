$("select[name*='entry-shop_region']").change(function(e){
    e.preventDefault();
    if(edit_loadstate == false){
        get_city_of_region('add');
    }
});

validate_ratetype_max_val();

function get_city_of_region(type, city = ''){
    $.ajax({
        type:'post',
        url: base_url+'Shopbranch/getcityofregion',
        data:{'region':$("select[name*='entry-shop_region']").select2().find(":selected").data("regcode")},
        beforeSend:function(data){
            $.LoadingOverlay("show");
        },
        success:function(data){
        $.LoadingOverlay("hide");
            if (data.success == 1) {
                var list = "";
                if(type == 'edit' && city != ""){
                    list += '<option value="">Select City</option>';
                }else{
                    list += '<option value="" selected>Select City</option>';
                }
                for(var x = 0; x < data.cityofregion.length; x++){
                    if(type == 'edit' && city == data.cityofregion[x].citymunCode){
                        list += "<option data-regioncode='"+data.cityofregion[x].regDesc+"' value='"+data.cityofregion[x].citymunCode+"'' selected>"+data.cityofregion[x].citymunDesc+"</option>";
                    }else{
                        list += "<option data-regioncode='"+data.cityofregion[x].regDesc+"' value='"+data.cityofregion[x].citymunCode+"''>"+data.cityofregion[x].citymunDesc+"</option>";
                    }
                }
                
                $("select[name*='entry-shop_city']").empty().append(list);
            }else{
                var list = "";
                list += '<option value="" selected>Select City</option>';
                $("select[name*='entry-shop_city']").empty().append(list); 
            }
            // if(branchid == 0){
            //     $("select[name*='entry-branch_region']").val("");
            //     $("select[name*='entry-branch_region']").select2().trigger('change');
            // }else{
            //     $("select[name*='entry-branch_region']").val(branchid);
            //     $("select[name*='entry-branch_region']").select2().trigger('change');
            // }
        }
    });
}

function checkbox_status(){
    if ($("#checkbox-withshipping").prop('checked')){
        $("#entry-withshipping").val(1);
    } else {
        $("#entry-withshipping").val(0);
    }

    if ($("#checkbox-generatebilling").prop('checked')){
        $("#entry-generatebilling").val(1);
    } else {
        $("#entry-generatebilling").val(0);
    }
}

$('#checkbox-withshipping').change(function() {
    if ($("#checkbox-withshipping").prop('checked')){
        $("#entry-withshipping").val(1);
    } else {
        $("#entry-withshipping").val(0);
    }
});

$('#checkbox-generatebilling').change(function() {
    if ($("#checkbox-generatebilling").prop('checked')){
        $("#entry-generatebilling").val(1);
    } else {
        $("#entry-generatebilling").val(0);
    }
});

$('#checkbox-prepayment').change(function() {
    if ($("#checkbox-prepayment").prop('checked')){
        $("#entry-prepayment").val(1);
        $("#threshold_amt_div").attr('hidden', false);
    } else {
        $("#entry-prepayment").val(0);
        $("#threshold_amt_div").attr('hidden', true);
    }
});

$('#checkbox-toktokdel').change(function() {
    if ($("#checkbox-toktokdel").prop('checked')){
        $("#entry-toktokdel").val(1);
    } else {
        $("#entry-toktokdel").val(0);
    }
});

$("#back-button").click(function(e){
    window.location.href= $(this).data('value');
});

$('#entry-ratetype').change(function() {
    console.log($(this).val());
    if($(this).val() == 'p'){
        $("#entry-rate").attr('max', '1');
    }else{
        $("#entry-rate").attr('max', '');
    }
});

function validate_ratetype_max_val(){
    if($('#entry-ratetype').val() == 'p'){
        $("#entry-rate").attr('max', '1');
    }else{
        $("#entry-rate").attr('max', '');
    }
}