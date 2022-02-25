$("select[name*='entry-mainshop']").change(function(e){
    e.preventDefault();
    if(edit_loadstate == false){
        get_product_of_shop('add');
    }
});

$("#back-button").click(function(e){
    window.location.href= $(this).data('value');
});

function get_product_of_shop(type, product = ''){
    $.ajax({
        type:'post',
        url: base_url+'Referralcomrate/getproducts',
        data:{'mainshop':$("select[name*='entry-mainshop']").val()},
        beforeSend:function(data){
            $.LoadingOverlay("show");
        },
        success:function(data){
        $.LoadingOverlay("hide");
            if (data.success == 1) {
                var list = "";
                if(type == 'edit' && product != ""){
                    list += '<option value="">Select Product</option>';
                }else{
                    list += '<option value="" selected>Select Product</option>';
                }
                for(var x = 0; x < data.result.length; x++){
                    if(type == 'edit' && product == data.result[x].Id){
                        list += "<option value='"+data.result[x].Id+"'' selected>"+data.result[x].itemname+"</option>";
                    }else{
                        list += "<option value='"+data.result[x].Id+"''>"+data.result[x].itemname+"</option>";
                    }
                }
                
                $("select[name*='entry-product']").empty().append(list);
            }else{
                var list = "";
                list += '<option value="" selected>Select Product</option>';
                $("select[name*='entry-product']").empty().append(list); 
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