$(document).ready(function () {
	var base_url = $("body").data("base_url"); //base_url came from built-in CI function base_url();
    var custId='';
    var email='';
    $(".approvalbtn").click(function(){
        target = $(this).data('content');
        email = $(this).data('email');
        $(target).show(250);
        disable = $(this).data('disable');
        $(disable).hide(250);
        $("#verifyCustomerModal").modal('show');
        custId = $(this).data('custid');
    });
    $(".other_reason").hide();
    $("#decline_reason_select").on("change",function(){
        if($(this).val() == "Other"){
            $(".other_reason").show(250);
        }else{
            $(".other_reason").hide(250);
        }
    });
    $("#declinebtnCustomer").click(function(){
		$.ajax({				
			url: base_url+'admin/Main_customers/changestatus',
	       	type: 'POST',
			data: {
                id:custId,
                status:4,
                email:email,
                decline_reason_select:$("#decline_reason_select").val(),
                decline_reason:$("#decline_reason").val(),
                allow_to_resubmit:$("#allow_to_resubmit").prop('checked')
            },
			beforeSend:function() {
				$.LoadingOverlay("show"); 
			},
			success : function(data){
				json_data = JSON.parse(data);
				$.LoadingOverlay("hide"); 
                if(json_data.success){
                    $("#verifyCustomerModal").modal('hide');
                    sys_toast_success(json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    sys_toast_warning(json_data.message);
                }
            }
        });
    });
    $("#verifybtnCustomer").click(function(){
		$.ajax({				
			url: base_url+'admin/Main_customers/changestatus',
	       	type: 'POST',
			data: {id:custId,status:1,
                email:email,
                decline_reason_select:$("#decline_reason_select").val(),
                decline_reason:$("#decline_reason").val(),
                allow_to_resubmit:$("#allow_to_resubmit").prop('checked')
            },
			beforeSend:function() {
				$.LoadingOverlay("show"); 
			},
			success : function(data){
				json_data = JSON.parse(data);
				$.LoadingOverlay("hide"); 
                $("#verifyCustomerModal").modal('hide');
                sys_toast_success(json_data.message);
                setTimeout(function(){location.reload()}, 2000);
            }
        });
    });
});