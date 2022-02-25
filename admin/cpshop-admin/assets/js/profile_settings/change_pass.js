$(function(){
	var base_url     = $("body").data('base_url'); //base_url come from php functions base_url();
	var passReq      = 1;
	var passMessage  = "";

	$(".secOldpass, .secNewpass, .secRetypenewpass").keyup(function(){
		var secOldpass = $('.secOldpass').val();
		var secNewpass = $('.secNewpass').val();
		var secRetypenewpass = $('.secRetypenewpass').val();

		if (secOldpass == "" || secNewpass == "" || secRetypenewpass == "") {
			$(".saveChangePassBtn").prop('disabled',true);
		}else{
			$(".saveChangePassBtn").prop('disabled',false);
		}
	});
		
	$("#saveChangePassForm").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		var thiss  = $(this);
		var pass   = $(".secNewpass").val();

        if (validate_strong_password(pass)) {
			$.ajax({
				type:'post',
				url: base_url+'Main_profile_settings/save_changepass_user',
				data: serial,
				beforeSend:function(data){
					$(".saveChangePassBtn").prop('disabled', true); 
					$(".saveChangePassBtn").text("Please wait..."); 
				},
				success:function(data){
					$(".saveChangePassBtn").prop('disabled', false); 
					$(".saveChangePassBtn").text("Save");

					if(data.success == 1) {
						//sys_toast_success(data.message);
						showCpToast("success", "Success!", data.message);
						$(thiss).find('input').val(''); //clear the password fields
					}else{
						//sys_toast_warning(data.message);
						showCpToast("warning", "Warning!", data.message);
					}
				}
			});
		}
		else{
			//sys_toast_warning('Password must contain at least 8 characters long, alphanumeric, lowercase and uppercase.');
			showCpToast("warning", "Warning!", 'Password must contain at least 8 characters long, alphanumeric, lowercase and uppercase.');
		}
	});

	$(".secEmail").keyup(function(){
		var secEmail = $('.secEmail').val();

		if (secEmail == "") {
			$(".saveChangeEmailBtn").prop('disabled',true);
		}else{
			$(".saveChangeEmailBtn").prop('disabled',false);
		}
	});

	$('.input-group-addon a i').click(function(e){
		e.preventDefault();
		togglePasswordVisibility($(this));
	});

	function togglePasswordVisibility(element){
		if($(element).hasClass('fa-eye-slash')){
			$(element).removeClass('fa-eye-slash');
			$(element).addClass('fa-eye');	
			$(element).parent().parent().prev().prop("type","text");
		}
		else{
			$(element).removeClass('fa-eye');
			$(element).addClass('fa-eye-slash');
			$(element).parent().parent().prev().attr("type","password");
		}
	}

	function checkPasswordRequirements(pass, repass){
		if(pass != repass){
			passReq     = 0;
			passMessage = "New Password and Re-type Password is not the same.";
		}
	}
	
});
