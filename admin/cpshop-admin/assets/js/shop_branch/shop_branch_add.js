var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
var edit_loadstate = false;
$(document).ready(function() {
    taginput_state(true);
});
form_state(false);
    
$("#add-form").submit(function(e){
    e.preventDefault();
    checkbox_status();
    var thiss = $("#add-form");
    var serial = thiss.serialize();

    if(checkInputs("#add-form") == 0){
        $.ajax({ 
            type:'post',
            url: base_url+'Shopbranch/save',
            data: serial,
            beforeSend:function(data){
                $(".cancelBtn, .saveBtn").prop('disabled', true); 
                $(".saveBtn").text("Please wait...");
                $.LoadingOverlay("show");
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                $(".cancelBtn, .saveBtn").prop('disabled', false);
                $(".saveBtn").text("Save");
                if (data.success == 1) {
                    location.reload();
                    //messageBox(data.message, "Success", "success");
                    showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    //messageBox(data.message, "Warning", "warning");
                    showCpToast("warning", "Warning!", data.message);
                }
            }
        });
    }
});