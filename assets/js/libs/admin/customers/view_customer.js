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

    $("#declinebtnCustomer").click(function(){
		$.ajax({				
			url: base_url+'admin/Main_customers/changestatus',
	       	type: 'POST',
			data: {id:custId,status:3,email:email},
			beforeSend:function() {
				$.LoadingOverlay("show"); 
			},
			success : function(data){
				json_data = JSON.parse(data);
                $("#verifyCustomerModal").modal('hide');
				$.LoadingOverlay("hide"); 
                sys_toast_success(json_data.message);
                setTimeout(function(){location.reload()}, 2000);
            }
        });
    });
    $("#verifybtnCustomer").click(function(){
		$.ajax({				
			url: base_url+'admin/Main_customers/changestatus',
	       	type: 'POST',
			data: {id:custId,status:1,email:email},
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