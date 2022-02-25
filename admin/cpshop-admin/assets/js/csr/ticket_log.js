$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');

	// $('#entry-form').submit(function(e){
 //        e.preventDefault();
 //        //This is the important part dont remove
 //        for (instance in CKEDITOR.instances) {
 //        CKEDITOR.instances['commentbox'].updateElement();
 //    	}
 //    	/////////////////////////////////////////////////
 //        var serial = $("#entry-form").serialize();
 //        var commentbox = CKEDITOR.instances.commentbox.getData();
 //        //var datatosend = JSON.stringify({'ticketdetails':commentbox});\
 //        if(checkInputs("#entry-form") == 0){
 //            $.ajax({
 //                type:'post',
 //                url: base_url+'Csr/logticket',
 //                data: serial,
 //                beforeSend:function(data){
 //                    $.LoadingOverlay("show");
 //                    $(".btn-save").prop('disabled', true); 
 //                    $(".btn-save").text("Please wait...");
 //                },
 //                success:function(data){
 //                    $.LoadingOverlay("hide");
 //                    $(".btn-save").prop('disabled', false); 
 //                    $(".btn-save").text("Save");
 //                    if (data.success == 1) {
 //                        window.location.reload();
 //                        messageBox(data.message, 'Success', 'success');
 //                    }else{
 //                        messageBox(data.message, 'Warning', 'warning');
 //                    }
 //                }
 //            });
 //        }
 //    });

	$('#btn-savedetails').click(function(e){
        e.preventDefault();
        //This is the important part dont remove
        for (instance in CKEDITOR.instances) {
        CKEDITOR.instances['commentbox'].updateElement();
    	}
    	/////////////////////////////////////////////////
        var serial = $("#entry-form").serialize();
        var commentbox = CKEDITOR.instances.commentbox.getData();
        //var datatosend = JSON.stringify({'ticketdetails':commentbox});\
        if(checkInputs("#entry-form") == 0){
            $.ajax({
                type:'post',
                url: base_url+'Csr/logticket',
                data: serial,
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
                        //messageBox(data.message, 'Success', 'success');
                        showCpToast("success", "Success!", data.message);
                        window.location.href = base_url+"Main_page/display_page/csr_section_home/"+token;
                    }else{
                        //messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
        }
    });


	$("#btn-yesclose").click(function(e){
		e.preventDefault();
		var close_id = $(this).data('value');
		$.ajax({
			type:'post',
			url:base_url+'Csr/ticketclose',
			data:{'close_id':close_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					// $.toast({
					//     text: data.message,
					//     icon: 'success',
					//     loader: false,  
					//     stack: false,
					//     position: 'top-center', 
					//     bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 10000
					// });

					showCpToast("success", "Success!", data.message);
					window.location.href = base_url+"Main_page/display_page/csr_section_home/"+token;
					$('#close_ticket_modal').modal('toggle'); //close modal
				}else{
					// $.toast({
					//     text: data.message,
					//     icon: 'info',
					//     loader: false,   
					//     stack: false,
					//     position: 'top-center',  
					//     bgColor: '#FFA500',
					// 	textColor: 'white'        
					// });
					showCpToast("info", "Info!", data.message);
				}
			}
		});
	});
	
	$("#btn-yesreopen").click(function(e){
		e.preventDefault();
		var reopen_id = $(this).data('value');
		$.ajax({
			type:'post',
			url:base_url+'Csr/ticketreopen',
			data:{'reopen_id':reopen_id},
			success:function(data){
				var res = data.result;
				if (data.success == 1){
					// $.toast({
					//     text: data.message,
					//     icon: 'success',
					//     loader: false,  
					//     stack: false,
					//     position: 'top-center', 
					//     bgColor: '#5cb85c',
					// 	textColor: 'white',
					// 	allowToastClose: false,
					// 	hideAfter: 10000
					// });
					// window.location.reload();
					showCpToast("success", "Success!", json_data.message);
          			setTimeout(function(){location.reload()}, 2000);
					$('#reopen_ticket_modal').modal('toggle'); //close modal
				}else{
					// $.toast({
					//     text: data.message,
					//     icon: 'info',
					//     loader: false,   
					//     stack: false,
					//     position: 'top-center',  
					//     bgColor: '#FFA500',
					// 	textColor: 'white'        
					// });
					showCpToast("info", "Info!", json_data.message);
				}
			}
		});
	});

	$("#hideshow-orders").click(function(e){
		if($("#order-body").attr('hidden')){
			$("#order-body").attr('hidden', false);
			$("#hideshow-orders").text('Hide');
		}else{
			$("#order-body").attr('hidden', true);
			$("#hideshow-orders").text('Show');
		}
	});	
});