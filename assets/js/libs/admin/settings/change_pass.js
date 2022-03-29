$(function(){
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
  $("#show_hide_password2 a").on('click', function() {
    if($('#show_hide_password2 input').attr("type") == "text"){
        $('#show_hide_password2 input').attr('type', 'password');
        $('#show_hide_password2 i').addClass( "fa-eye-slash" );
        $('#show_hide_password2 i').removeClass( "fa-eye" );
    }else if($('#show_hide_password input').attr("type") == "password"){
        $('#show_hide_password2 input').attr('type', 'text');
        $('#show_hide_password2 i').removeClass( "fa-eye-slash" );
        $('#show_hide_password2 i').addClass( "fa-eye" );
    }
  });
  $("#show_hide_password3 a").on('click', function() {
    if($('#show_hide_password3 input').attr("type") == "text"){
        $('#show_hide_password3 input').attr('type', 'password');
        $('#show_hide_password3 i').addClass( "fa-eye-slash" );
        $('#show_hide_password3 i').removeClass( "fa-eye" );
    }else if($('#show_hide_password input').attr("type") == "password"){
        $('#show_hide_password3 input').attr('type', 'text');
        $('#show_hide_password3 i').removeClass( "fa-eye-slash" );
        $('#show_hide_password3 i').addClass( "fa-eye" );
    }
  });

	$("#saveChangePassForm").submit(function(e){
		e.preventDefault();
		var email       = $("#show_hide_password").data('email'); 
        console.log(email);
		var serial = $(this).serialize()+"&email="+email;
		var pass = $("#passwordretype").val();
        $.ajax({
            type:'post',
            url: base_url+'admin/Main_settings/validate_password',
            data: serial,
            beforeSend:function(data){
                $(".saveChangePassBtn").prop('disabled', true); 
                $(".saveChangePassBtn").text("Please wait..."); 
                $.LoadingOverlay("show");
            },
            success:function(data){
                $(".saveChangePassBtn").text("Save");
                $.LoadingOverlay("hide");
                $(".saveChangePassBtn").prop('disabled', false); 
                console.log(data);
                if (data == 1) {
                    if (validate_strong_password(pass)) {
                        $.ajax({
                            type:'post',
                            url: base_url+'Main/update_password',
                            data: serial,
                            beforeSend:function(data){
                                $(".saveChangePassBtn").text("Please wait..."); 
                                $.LoadingOverlay("show");
                            },
                            success:function(data){
                                $(".saveChangePassBtn").text("Save");
                                $.LoadingOverlay("hide");
                                if (data.success == 1) {
                                    sys_toast_success(data.message);
                                    setTimeout(function(){  
                                        window.location.href = ''+base_url+'admin/Main_settings/';
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
                }else{
                    $.LoadingOverlay("hide");
                    sys_toast_warning("Old password not matched");
                    $(".saveChangePassBtn").prop('disabled', false); 
                }
            }
        });
	});
});