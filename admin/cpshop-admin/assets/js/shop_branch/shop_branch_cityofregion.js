$("select[name*='entry-branch_region']").change(function(e){
    e.preventDefault();
    if(edit_loadstate == false){
        get_city_of_region('add');
    }
});

function get_city_of_region(type, city = ''){
    $.ajax({
        type:'post',
        url: base_url+'Shopbranch/getcityofregion',
        data:{'region':$("select[name*='entry-branch_region']").select2().find(":selected").data("regcode")},
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
                
                $("select[name*='entry-branch_city']").empty().append(list);
            }else{
                var list = "";
                list += '<option value="" selected>Select City</option>';
                $("select[name*='entry-branch_city']").empty().append(list); 
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