$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var shopid = $('#md5').val();
	

	$('#backBtn').click(function(){
        window.location.assign(base_url+"Shops/comrate_approval/"+token);
	})


	$('#EditBtn').click(function(){
        window.location.assign(base_url+"Shops/update_comrate_approval/"+shopid+"/"+token);
	})



	
	$(document).on('click', '#ApproveButton', function(e) {

        e.preventDefault();
        const id = $('#shopid').val();
		$('#proceedBtn').attr('data-id', id);

    })

    $('#proceedBtn').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');


			$.ajax({
				type: 'post',
				url: base_url+"shops/Main_shops_approval/shop_mcr_approve",
				data:{
					'id': action_id,
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#approveModal').modal('hide');
						// sys_toast_success(json_data.message);
						showCpToast("success", "Approved!", "Shop MCR has been approved.");
						setTimeout(function(){location.reload()}, 2000);
					}
					else{
						sys_toast_warning(json_data.message);
						$('#approveModal').modal('hide');
						// location.reload();
					}

				},
				error: function(error){
					sys_toast_error('Something went wrong. Please try again.');
				}
			});


    });


	$(document).on('click', '#VerifyButton', function(e) {

        e.preventDefault();
        const id = $('#shopid').val();
		$('#proceedBtnVerify').attr('data-id', id);

    })

    $('#proceedBtnVerify').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');


			$.ajax({
				type: 'post',
				url: base_url+"shops/Main_shops_approval/shop_mcr_verify",
				data:{
					'id': action_id,
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#VerifyModal').modal('hide');
						// sys_toast_success(json_data.message);
						showCpToast("success", "Approved!", "Shop MCR has been approved.");
						setTimeout(function(){location.reload()}, 2000);
					}
					else{
						sys_toast_warning(json_data.message);
						$('#VerifyModal').modal('hide');
						// location.reload();
					}

				},
				error: function(error){
					sys_toast_error('Something went wrong. Please try again.');
				}
			});


    });



	$(document).on('click', '#DeclineButton', function(e) {

        e.preventDefault();
        const id = $('#shopid').val();
		$('#proceedDecBtn').attr('data-id', id);

    })

    $('#proceedDecBtn').click(function(e){

        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');
		// const reason = $('#dec_reason').val();

		// alert(action_id);

        // if(reason != ''){
		$.ajax({
			type: 'post',
			url: base_url+"shops/Main_shops_approval/shops_mcr_decline",
			data:{
				'id': action_id,
				// 'reason':reason
			},
			success:function(data){
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if(json_data.success){
					$('#declineModal').modal('hide');
					// sys_toast_success(json_data.message);
					showCpToast("warning", "Declined!", "Shop MCR has been declined.");
					setTimeout(function(){location.reload()}, 2000);
				}
				else{
					sys_toast_warning(json_data.message);
					$('#declineModal').modal('hide');
					// location.reload();
				}

			},
			error: function(error){
				sys_toast_error('Something went wrong. Please try again.');
			}
		});
		// }else{
		// 	$.LoadingOverlay("hide");
		// 	sys_toast_warning('Please enter notes.');
		// }



    });



	

});




