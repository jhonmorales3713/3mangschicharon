var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var edit_loadstate = false;
$('body').delegate("#saveBtn", "click", function(e){

	var new_product_email        = $("#new_product_email").val();
    var new_product_name         = $("#new_product_name").val();
    var new_approval_email       = $("#new_approval_email").val();
    var new_approval_name        = $("#new_approval_name").val();
    var new_verification_email   = $("#new_verification_email").val();
    var new_verification_name    = $("#new_verification_name").val();
    var shop_mcr_approval_email     =  $("#shop_mcr_approval_email").val();
    var shop_mcr_approval_name      =  $("#shop_mcr_approval_name").val();
    var shop_mcr_verification_email =  $("#shop_mcr_verification_email").val();
    var shop_mcr_verification_name  =  $("#shop_mcr_verification_name").val();
    var shop_mcr_verified_email     =  $("#shop_mcr_verified_email").val();
    var shop_mcr_verified_name      =  $("#shop_mcr_verified_name").val();
    var email_settings_id           = $("#email_settings_id").val();





        e.preventDefault();
            $.ajax({
                type:'post',
                url: base_url+'developer_settings/Dev_settings_email_settings/update_email_info',
				data: {  
                        'new_product_email':new_product_email,
                        'new_product_name':new_product_name,
                        'new_approval_email': new_approval_email,
                        'new_approval_name': new_approval_name,
                        'new_verification_email':new_verification_email,
                        'new_verification_name': new_verification_name,
                        'shop_mcr_approval_email':shop_mcr_approval_email,
                        'shop_mcr_approval_name':shop_mcr_approval_name,
                        'shop_mcr_verification_email': shop_mcr_verification_email,
                        'shop_mcr_verification_name': shop_mcr_verification_name,
                        'shop_mcr_verified_email':shop_mcr_verified_email,
                        'shop_mcr_verified_name':shop_mcr_verified_name,
                        'email_settings_id':email_settings_id

                     },
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $(".saveBtn").prop('disabled', true); 

                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    $(".saveBtn").prop('disabled', false); 

                    if (data.success == 1) {
                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);
                        
                       // messageBox(data.message, 'Success', 'success');
                    }else{
                        //messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
       
    });

  