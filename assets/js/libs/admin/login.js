$(function(){
	var base_url       = $("body").data('base_url'); //base_url come from php functions base_url();
	var captchaChecker = 0;
	var loginAttempt   = 0;

	toastBgColor = {
		info: "#5cb85c",
		error: "#f0ad4e"
	}

	$("#signin").click(function(){
        var application_form = $("#form-login").serialize(); 
        var newform=new FormData($("#form-login")[0]);
        $.ajax({
        url: base_url+"admin/Login/signin",
            data: newform,  // what to expect back from the PHP script, if anything       
            type: 'post',
            dataType: 'json',
            contentType:false,
            cache:false,
            processData:false,
            beforeSend:function(data){
              $('#btnLogin').prop('disabled',true);
              $.LoadingOverlay("show");
            },
            success:function(data){
                console.log(data);
                $.LoadingOverlay("hide");
            }
        });
    });
	function toastMessage(heading, text, icon) {

		$.toast({
			heading: heading,
			text: text,
			icon: icon,
			loader: false,  
			stack: false,
			position: 'top-center', 
			allowToastClose: false,
			bgColor: toastBgColor[icon],
			textColor: 'white'  
		});
	}
	
	$("#login-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		var loginCompanycode = $("#login-companycode").val();
		var loginUsername = $("#login-username").val();
		var loginPassword = $("#login-password").val();

		if(loginAttempt > 4){
			validateCaptcha();
		}
		else{
			captchaChecker = 1;
		}

		if(captchaChecker == 0){
			sys_toast_warning('Invalid Captcha.');
		}
		else if (loginUsername == '' || loginPassword == '' || loginCompanycode == '') {
			sys_toast_warning('Please fill up all fields.');
		}
		else{
			$.ajax({
				type: 'post',
				url: base_url+'Main/login',
				data: serial,
				beforeSend:function(data){
					$('#btnLogin').prop('disabled',true);
					$.LoadingOverlay("show");
				},
				success:function(data){
					if(data.success == 1) {
						var userData = data.userData;
						var token = data.token_session;
						var first_login = data.first_login;
						var code_isset = data.code_isset;
						var username = data.username;
						var md5_sys_users_id = data.md5_sys_users_id;
						if(data.hasOwnProperty('redirect_url')){
							window.location.href = data.redirect_url;
						}else{
							if(code_isset == 1){
								sendCodeEmailLoginAttempts(username);
								setTimeout(function(){ 
									window.location.href = ''+base_url+'Main/set_code/'+md5_sys_users_id;
								}, 5000);
							}
							else if(first_login == 1){
								window.location.href = ''+base_url+'Main/set_password/'+md5_sys_users_id;
							}
							else if (data.is_dashboard == 1) {
								window.location.href = ''+base_url+'Main/home/'+token;
							}else{
								window.location.href = ''+base_url+'Main_orders/orders/'+token;	
							}
						}
					}
					else {
						sys_toast_warning(data.message);
						addLoginAttempts();
						$('#btnLogin').prop('disabled',false);
						$.LoadingOverlay("hide");
						createCaptcha();
						$('#inputCaptcha').val('');
					}
				}
			});
		}
		
	});

	$("#reset_passForm").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();
		if ($(".emailAddreset").val() != "") {
			$.ajax({
				type: 'post',
				url: base_url+'Main/reset_password',
				data: serial,
				beforeSend:function(data){
					$('#resetPasswordBtn').attr('disabled',true);
					$('#resetPasswordBtn').val('Please wait...');
				},
				success:function(data){

					$('#resetPasswordBtn').attr('disabled',false);
					$('#resetPasswordBtn').val('Reset My Password');
					if (data.success == 1) {
						$('#resetPasswordBtn').attr('disabled',true);
						sys_toast_success(data.message);
						
					}
					else{
						sys_toast_warning(data.message);
					}
				}
			});
		}
		else {
			sys_toast_warning('Please Enter your valid email address');
		}
		
	});
	createCaptcha();
	getLoginAttempts();

	$("#refreshCaptcha").click(function(e){
		createCaptcha();
	});

	$("#resendAccessCode").click(function(e){
		username = $('#loginUsername').val();
		resendCodeEmailLoginAttempts(username);
	});

	var code;
	function createCaptcha() {
		//clear the contents of captcha div first 
		document.getElementById('captcha').innerHTML = "";
		var charsArray =
		"0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@!#$%^&*";
		var lengthOtp = 6;
		var captcha = [];
		for (var i = 0; i < lengthOtp; i++) {
			//below code will not allow Repetition of Characters
			var index = Math.floor(Math.random() * charsArray.length + 1); //get the next character from the array
			if (captcha.indexOf(charsArray[index]) == -1)
			captcha.push(charsArray[index]);
			else i--;
		}
		var canv = document.createElement("canvas");
		canv.id = "captcha";
		canv.width = 150;
		canv.height = 50;
		var ctx = canv.getContext("2d");
		ctx.font = "25px Georgia";
		ctx.strokeText(captcha.join(""), 0, 30);
		//storing captcha so that can validate you can save it somewhere else according to your specific requirements
		code = captcha.join("");
		console.log(code);
		document.getElementById("captcha").appendChild(canv); // adds the canvas to the body element
	}

	function validateCaptcha() {
		event.preventDefault();
		// debugger
		if (document.getElementById("inputCaptcha").value == code) {
			captchaChecker = 1;
		}else{
			captchaChecker = 0;
			$('#inputCaptcha').val('');
			createCaptcha();
		}
	}

	function addLoginAttempts(){
		username = $('#login-username').val();
		
		$.ajax({
			type: 'post',
			url: base_url+'Main/addLoginAttempts',
			data:{
				'username': username
			},
			success:function(data){
				loginAttempt = data.attempt;

				(loginAttempt > 4) ? $('#captchaDiv').show():$('#captchaDiv').hide();
			}
		});
	}

	function sendCodeEmailLoginAttempts(username){
		$.ajax({
			type: 'post',
			url: base_url+'Main/sendCodeEmailLoginAttempts',
			data:{
				'username': username
			}
		});
	}

	function resendCodeEmailLoginAttempts(username){
		$.ajax({
			type: 'post',
			url: base_url+'Main/resendCodeEmailLoginAttempts',
			data:{
				'username': username
			},
			beforeSend:function(data){
				$.LoadingOverlay("show");
			},
			success:function(data){
				if(data.success == 1){
					$.LoadingOverlay("hide");
					sys_toast_success(data.message);
				}
				else{
					$.LoadingOverlay("hide");
					sys_toast_warning(data.message);
				}
			}
		});
	}

	function getLoginAttempts(){
		$.ajax({
			type: 'post',
			url: base_url+'Main/getLoginAttempts',
			success:function(data){
				loginAttempt = data.attempt;

				(loginAttempt > 4) ? $('#captchaDiv').show():$('#captchaDiv').hide();
			}
		});
	}
	$("#setpassword-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		var loginPassword   = $("#login-password").val();

		if (validate_strong_password(loginPassword)) {
			$.ajax({
				type: 'post',
				url: base_url+'Main/setpassword',
				data: serial,
				beforeSend:function(data){
					$('#btnLogin').prop('disabled',true);
					$.LoadingOverlay("show");
				},
				success:function(data){
					if(data.success == 1) {
						$.LoadingOverlay("hide");
						sys_toast_success(data.message);
						window.location.href = base_url+'/Main';
						
					}
					else {
						sys_toast_warning(data.message);
						$.LoadingOverlay("hide");
					}
				}
			});
		}
		else{
			sys_toast_warning('Password must contain at least 8 characters long, alphanumeric, lowercase and uppercase.');
		}
		
	});

	$("#setcode-form").submit(function(e){
		e.preventDefault();
		var serial = $(this).serialize();

		var loginCode   = $("#loginCode").val();

		if (loginCode != "") {
			$.ajax({
				type: 'post',
				url: base_url+'Main/setcode',
				data: serial,
				beforeSend:function(data){
					$('#loginCode').prop('disabled',true);
					$.LoadingOverlay("show");
				},
				success:function(data){
					if(data.success == 1) {
						$.LoadingOverlay("hide");
						sys_toast_success(data.message);
						window.location.href = base_url+'/Main';
						
					}
					else {
						sys_toast_warning(data.message);
						$.LoadingOverlay("hide");
						$('#loginCode').prop('disabled',false);
					}
				}
			});
		}
		else{
			sys_toast_warning('Please enter access code.');
		}
		
	});
});
  $("#show_hide_password a").on('click', function() {
    if($('#show_hide_password input').attr("type") == "text"){
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password i').addClass( "fa-eye-slash" );
        $('#show_hide_password i').removeClass( "fa-eye" );
    }else if($('#show_hide_password input').attr("type") == "password"){
        $('#show_hide_password input').attr('type', 'text');
        $('#show_hide_password i').removeClass( "fa-eye-slash" );
        $('#show_hide_password i').addClass( "fa-eye" );
    }
  });


	