$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$(".passwordretype, .password").keyup(function(){
		var secNewpass = $('.password').val();
		var secRetypenewpass = $('.passwordretype').val();

		if (secNewpass == "" || secRetypenewpass == "") {
			$(".saveChangePassBtn").prop('disabled',true);
		}else{
			$(".saveChangePassBtn").prop('disabled',false);
		}
	});
	$("#saveChangePassForm").submit(function(e){
		e.preventDefault();

		var serial = $(this).serialize();
		var thiss = $(this);
		var pass = $(".passwordretype").val();

        if (validate_strong_password(pass)) {
			$.ajax({
				type:'post',
				url: base_url+'Account/setfirstpassword',
				data: serial,
				beforeSend:function(data){
					$(".saveChangePassBtn").prop('disabled', true); 
					$(".saveChangePassBtn").text("Please wait..."); 
					$.LoadingOverlay("show");
				},
				success:function(data){
					$(".saveChangePassBtn").text("Save");
					$.LoadingOverlay("hide");
					if (data.success == 1) {
                        sys_toast_success(data.message);
						setTimeout(function(){  
							window.location.href = ''+base_url+'Main';
						}, 3000);
					}else{
						$.LoadingOverlay("hide");
                        sys_toast_warning(data.message);
						$(".saveChangePassBtn").prop('disabled', false); 
					}
				}
			});
		}
		else{
			sys_toast_warning('Password must contain at least 8 characters long, alphanumeric, lowercase and uppercase.');
		}
	});
		
});