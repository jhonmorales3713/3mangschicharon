$(function(){
	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();

	$(".secNewpass, .secRetypenewpass").keyup(function(){
		var secNewpass = $('.secNewpass').val();
		var secRetypenewpass = $('.secRetypenewpass').val();

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
		var pass = $(".secNewpass").val();

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
						$.toast({
						    heading: 'Success',
						    text: data.message,
						    icon: 'success',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
						    bgColor: '#5cb85c',
							textColor: 'white',
							allowToastClose: false,
							hideAfter: 4000
						});
						setTimeout(function(){  
							window.location.href = ''+base_url+'Main';
						}, 3000);
					}else{
						$.LoadingOverlay("hide");
						$.toast({
						    heading: 'Note',
						    text: data.message,
						    icon: 'error',
						    loader: false,  
						    stack: false,
						    position: 'top-center', 
							allowToastClose: false,
							bgColor: '#f0ad4e',
							textColor: 'white'  
						});
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