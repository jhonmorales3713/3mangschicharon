$(function(){
	var base_url    = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var shop_url = $("body").data('shop_url');
    var token    = $("body").data('token');

    $('#recordType').change(function(e){
				$('.divorder').hide(500);
				$('.div_prepayment').hide(500);

        if($(this).val() == "Order List"){
            $('.divorder').show(500);
        }else if($(this).val() == "Pre-Payment"){
						$('.div_prepayment').show(500);
				}
        else{
            $('.divorder').hide(500);
						$('.div_prepayment').hide(500);
        }

    });

    $('#btnNext').click(function(){
        recordType = $('#recordType').val();

        if(recordType == "Order List"){
            reference_num = $('#order_reference_num').val();
            if(reference_num == ""){
            	showCpToast("warning", "Warning!", 'Please input reference number');
                //sys_toast_warning('Please input reference number');
            }else{
                $.ajax({
                    type:'post',
                    url:base_url+'settings/void_record/Settings_void_record/getOrders',
                    data:{
                        'reference_num': reference_num
					},
					beforeSend: function(){
						$.LoadingOverlay('show');
					},
                    success:function(data){
                        $.LoadingOverlay("hide");
                        var json_data = JSON.parse(data);

                        if(json_data.success){
                            window.location.assign(base_url+"Settings_void_record/void_order/"+token+"/"+reference_num);
                        }else{
                            //sys_toast_warning('Reference Number does not exist');
                            showCpToast("warning", "Warning!", 'Reference Number does not exist');
                        }
                    }
                });

            }

        }else if(recordType == "Pre-Payment"){
			let reference_num = $('#prepayment_tran_ref_num').val();
			if(reference_num == ""){
				//sys_toast_warning('Please input reference number');
				showCpToast("warning", "Warning!", 'Please input reference number');
				return;
			}

			$.ajax({
				url: base_url+'settings/void_record/Settings_void_record/get_prepayment_logs',
				type: 'post',
				data:{reference_num},
				beforeSend: function(){
				$.LoadingOverlay('show');
					$('#wallet_logs').html('');
				},
				success: function(data){
				$.LoadingOverlay('hide');
				if(data.success == 1){
						$('#deposit_ref_num').text(data.deposit_ref_num);
						$('#tran_date').text(data.tran_date);
						$('#tran_ref_num').text(data.tran_ref_num);
						$('#tran_type').text(data.tran_type);
						$('#log_type').val(data.log_type);
						$('#tran_amount').text(data.tran_amount);
						$('#prepayment_void_refnum').val(data.prepayment_void_refnum);
						$('#prepayment_void_modal').modal();
				}else{
						//sys_toast_warning(data.message);
						showCpToast("warning", "Warning!", data.message);
				}
				},
				error: function(){
				//sys_toast_warning('Something went wrong. Please try again.');
				showCpToast("warning", "Warning!",'Something went wrong. Please try again.');
				$.LoadingOverlay('hide');
				}
			});
		}else{
			//sys_toast_warning('Please select void record type.');
			showCpToast("warning", "Warning!", 'Please select void record type.');
        }
    });


		$(document).on('click', '#btn_void_prepayment', function(){
			let prepayment_void_refnum = $('#prepayment_void_refnum').val();
			let prepayment_reason = $('#prepayment_reason').val();
			let log_type = $('#log_type').val();
			if(prepayment_void_refnum == ""){
				//sys_toast_warning('Invalid transaction reference number')
				showCpToast("warning", "Warning!", 'Invalid transaction reference number');
			}
			// console.log(prepayment_reason);
			$.ajax({
			  url: base_url+'settings/void_record/Settings_void_record/set_prepayment_voidrecord',
			  type: 'post',
			  data:{prepayment_void_refnum,prepayment_reason,log_type},
			  beforeSend: function(){
			    $.LoadingOverlay('show');
			  },
			  success: function(data){
			    $.LoadingOverlay('hide');
			    if(data.success == 1){
						$('#prepayment_void_modal').modal('hide')
						//messageBox(data.message,'Success','success');
						showCpToast("success", "Success!", data.message);
						setTimeout(function(){location.reload()}, 2000);
						$('#recordType option[value=""]').prop('selected',true).trigger('change');
						$('#prepayment_tran_ref_num').val('');
			    }else{
						//sys_toast_warning(data.message);
						showCpToast("warning", "Warning!", data.message);
			    }
			  },
			  error: function(){
			    //sys_toast_warning('Oops! Something went wrong. Please try again.');
			    showCpToast("warning", "Warning!", 'Oops! Something went wrong. Please try again.');
			    $.LoadingOverlay('hide');
			  }
			});
		});


});
