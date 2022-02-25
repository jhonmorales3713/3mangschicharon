$('#shopid').change(function(){
    var code = $("#shopid option:selected").attr('data-code');
    $('#shopcode').val(code)
    $('#shopcode-edit').val(code)
})



var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var edit_loadstate = false;
    $('#edit_voucher').submit(function(e){
        e.preventDefault();

        var form = $(this);
        var form_data = new FormData(form[0]);
        if(checkInputs("#edit_voucher") == 0){
            $.ajax({
                type:'post',
                url: base_url+'vouchers/List_vouchers/update_voucher',
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
                    $(".btn-save").text("Save");
                    if (data.success == 1) {
                        setTimeout(function(){ location.reload(); }, 2000);
                        // messageBox(data.message, 'Success', 'success');
                        showCpToast("success", "Success!", "Record updated successfully!");
                    }else{
                        showCpToast("Warning", "Warning!", "Something went wrong, Please Try again!");
                        // messageBox(data.message, 'Warning', 'warning');
                    }
                }
            });
        }
    });

  