$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	
	$("#resetpass-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		if ($("#email-for-pass").val() == '') {
			messageBox('Please enter your email address.', "Warning", "warning");
		}else{
			$.ajax({
				type: 'post',
				url: base_url+'Account/resetpassword',
				data: serial,
				beforeSend:function(data){
					$('#btnSubmit').attr('disabled',true);
					$.LoadingOverlay("show");
				},
				success:function(data){
					// $('#btnSubmit').attr('disabled',false);
					if(data.success == 1) {
						var userData = data.userData;
						var token = data.token_session;
						$.LoadingOverlay("hide");
						messageBox(data.message, "Success", "success");
						setTimeout(function(){  
							window.location.href = ''+base_url+'Main';
						}, 3000);
					}
					else {
						$.LoadingOverlay("hide");
						messageBox(data.message, "Warning", "warning");
						$('#btnSubmit').attr('disabled',false);
					}
				}
			});
		}
		
	});
});