var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var edit_loadstate = false;
$('body').delegate("#edit_client_info", "click", function(e){

	var c_id = $("#c_id").val();
	var csc_local = Number($('#csc_local').is(':checked'));
	var csc_test = Number($('#csc_test').is(':checked'));
	var csc_live = Number($('#csc_live').is(':checked'));
	var csc_local_pass = $("#csc_local_pass").val();
	var csc_test_pass = $("#csc_test_pass").val();
	var csc_live_pass = $("#csc_live_pass").val();

	

        e.preventDefault();
            $.ajax({
                type:'post',
                url: base_url+'developer_settings/Dev_settings_maintenance_page/update_client_info',
				data: { 'c_id': c_id, 'csc_local': csc_local, 'csc_test': csc_test, 'csc_live': csc_live, 'csc_local_pass': csc_local_pass, 'csc_test_pass': csc_test_pass, 'csc_live_pass': csc_live_pass, },
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
                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);
                        
                        //messageBox(data.message, 'Success', 'success');
                    }else{
                        //messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
       
    });

  