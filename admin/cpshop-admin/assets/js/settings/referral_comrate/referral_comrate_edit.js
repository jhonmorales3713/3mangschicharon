

var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
var edit_loadstate = true;

var prev_val = {
    'startup' : $('input[name=entry-startup]').val(),
    'jc' : $('input[name=entry-jc]').val(),
    'mcjr' : $('input[name=entry-mcjr]').val(),
    'mc' : $('input[name=entry-mc]').val(),
    'mcsuper' : $('input[name=entry-mcsuper]').val(),
    'mcmega' : $('input[name=entry-mcmega]').val(),
    'others' : $('input[name=entry-others]').val(),
};

get_record_details();

$("#add-form").submit(function(e){
	e.preventDefault();
	var thiss = $("#add-form");
    var serial = thiss.serializeArray();
    serial.push({name:'prev_val', value: JSON.stringify(prev_val)});
	if(checkInputs("#add-form") == 0){
        $.ajax({
            type:'post',
            url: base_url+'Referralcomrate/update',
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

function get_record_details(){
	get_product_of_shop('edit', $("#product_hidden").val());
	edit_loadstate  = false;
}

