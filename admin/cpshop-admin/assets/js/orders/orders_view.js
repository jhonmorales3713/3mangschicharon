$(function(){
	var base_url    = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var shop_url = $("body").data('shop_url');
    var token    = $("body").data('token');
	var url_ref_num = $('#url_ref_num').val();

    var fillDataTableProducts = function(reference_num) {
        dataTable = $('#table-item').DataTable({
            destroy: true,
            "serverSide": true,
            "searching": false,
             "columnDefs": [
                { "orderable": false, "targets": [ 0 ], "className": "text-center" },
                { "targets": [ 3, 4 ], "className": "text-right" }
            ],
            responsive: true,
            "ajax":{
                url:base_url+"orders/Main_orders/order_item_table", // json datasource
                type: "post",  // method  , by default get
                data: {
                    'reference_num': reference_num 
                },
                beforeSend:function(data){
                    $.LoadingOverlay("show"); 
                },
                complete: function(data) {  
                    $.LoadingOverlay("hide"); 
                },
                error: function(){  // error handling
                    $.LoadingOverlay("hide"); 
                    $(".table-grid-error").html("");
                    $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });
    }

    fillDataTableProducts(url_ref_num);
    checkGoogleMap();

    $('#backBtn').click(function(){
        var backlink = $('.backlink').attr("href");
        window.location.assign(backlink);
    })

    $('#reassignBtn').click(function(e){
        get_shop_branches($(this).data('value'), $(this).data('branchid'));
    })

    $('#update-branches').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            var old_branchid = $('.reassignBtn').data('branchid');
            var new_branchid = $('#shop-branches').val();
            $.LoadingOverlay("show");
            if(old_branchid == new_branchid){
                //sys_toast_warning('Current order already assigned to ' + $('#shop-branches').select2('data')[0]['text']);
                showCpToast("warning", "Warning!", 'Current order already assigned to ' + $('#shop-branches').select2('data')[0]['text']);
                $.LoadingOverlay("hide");
            }else{
                $.ajax({
                    type:'post',
                    url:base_url+'orders/Main_orders/check_reassign_item',
                    data:{
                        'mainshopid':$('.reassignBtn').data('value'), 
                        'reference_num':$('.reassignBtn').data('reference_num'),
                        'branchid':$('#shop-branches').val(), 
                        'remarks':$('#branch-remarks').val(),
                        'prev_branchid':$('#prev_branchid').val()
                    },
                    success:function(data){
                        if (data.success == 1) {
                            $('#check_branches-modal').modal('show');
                            str_note = data.message;
                            str_note = str_note.replace(/\\r\\n/g, "<br />");
                            document.getElementById("reassign_note").innerHTML = str_note;
                            $.LoadingOverlay("hide");
                        }else{
                            //sys_toast_warning(data.message);
                            showCpToast("warning", "Warning!", data.message);
                            $.LoadingOverlay("hide");
                        }
                    }
                });

            
            }
        }
    });

    $('#check_update-branches').click(function(e){
        $.LoadingOverlay("show");
        $.ajax({
            type:'post',
            url:base_url+'orders/Main_orders/reassign_branch',
            data:{
                'mainshopid':$('.reassignBtn').data('value'), 
                'reference_num':$('.reassignBtn').data('reference_num'),
                'branchid':$('#shop-branches').val(), 
                'remarks':$('#branch-remarks').val(),
                'prev_branchid':$('#prev_branchid').val()
            },
            success:function(data){
                if (data.success == 1) {
                    // sys_toast_success(data.message);
                    // location.reload();
                    showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    $('#branches-modal').modal('hide');
                    $('#branch-remarks').val("");
                    $('#reassignBtn').attr('disabled', true);
                    $.LoadingOverlay("hide");
                }else{
                    //sys_toast_warning(data.message);
                    showCpToast("warning", "Warning!", data.message);
                    $.LoadingOverlay("hide");
                }
            }
        });
    });

    $('#payBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            $("#tag_payment").prop("checked", false);
            $('.grp_payment_others').hide();
            $('.grp_payment-p').hide();
            $.LoadingOverlay("show");
            $('#f_id-p').val($('#tm_order_reference_num').html().trim());
            $('#tm_header_ref').html('Order Payment for Ref # '+ $('#tm_order_reference_num').html());
            $('#tm_order_date-p').html(' '+ $('#tm_order_date').html());
            $('#tm_order_reference_num-p').html(' '+$('#tm_order_reference_num').html());
            $('#tm-subtotal-p').html(' '+$('#tm_subtotal').html());
            $('#tm_shipping-p').html(' '+$('#tm_shipping').html());
            $('#tm_amount-p').html(' '+$('#tm_amount').html());
            $('#tm_order_status-p').html(' '+$('#tm_order_status').html());
            $('#tm_payment_date-p').html(' '+$('#tm_payment_date').html());
            $('#tm_payment_ref_num-p').html(' '+$('#tm_payment_ref_num').html());
            $('#tm_payment_status-p').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#payment_modal').modal();
        }

    });

    $('#tag_payment').change(function(e){
        if($(this).is(':checked') == true){
            $('#f_payment_ischecked').val(true);
            $('.grp_payment-p').show(500);
        }
        else {
            $('#f_payment_ischecked').val(false);
            $('.grp_payment-p').hide(500);
        }
    });

    $('#tag_rider').change(function(e){
        if($(this).is(':checked') == true){
            $('#bc_rider_ischecked').val(true);
            $('.grp_rider').show();
        }
        else {
            $('#bc_rider_ischecked').val(false);
            $('.grp_rider').hide();
        }
    });

    $('#mf_rider').change(function(e){
        if($(this).is(':checked') == true){
            $('#mf_rider_ischecked').val(true);
            $('.mf_rider').show();
        }
        else {
            $('#mf_rider_ischecked').val(false);
            $('#mf_').val(false);
            $('#mf_rider_ischecked').val(false);
            $('#mf_rider_name').val('');
            $('#mf_platenum').val('');
            $('#mf_conno').val('');
            $('.mf_rider').hide();
        }
    });

    $('#tag_shipping').change(function(e){
        if($(this).is(':checked') == true){
            $('#f_shipping_ischecked').val(true);
            $('.grp_shipping').show();
        }
        else {
            $('#f_shipping_ischecked').val(false);
            $('.grp_shipping').hide();
        }
    });

    $('#f_shipping').change(function(e){

        if($(this).val() == 'Others') {
            $('.grp_shipping_others').show();
        }
        else {
            $('.grp_shipping_others').hide();
        }
    });

    $('#f_payment').change(function(e){

        if($(this).val() == 'Others') {
            $('.grp_payment_others').show();
        }
        else {
            $('.grp_payment_others').hide();
        }
    });

    $('#processBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#po_id').val(ref_num);
            $('#po_header_ref').html('Processing Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#po_order_date').html(' '+ $('#tm_order_date').html());
            $('#po_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#po_amount').html(' '+$('#tm_amount').html());
            $('#po_order_status').html(' '+$('#tm_order_status').html());
            $('#po_payment_date').html(' '+$('#tm_payment_date').html());
            $('#po_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#po_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#processOrder_modal').modal();
        }

    });

    $('#readyPickupBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#rp_id').val(ref_num);
            $('#rp_header_ref').html('Ready for Pickkup Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#rp_order_date').html(' '+ $('#tm_order_date').html());
            $('#rp_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#rp_amount').html(' '+$('#tm_amount').html());
            $('#rp_order_status').html(' '+$('#tm_order_status').html());
            $('#rp_payment_date').html(' '+$('#tm_payment_date').html());
            $('#rp_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#rp_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#readyPickup_modal').modal();
        }

    });

    $('#cancelOrderBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');         
            $.LoadingOverlay("show");
            $('#cn_id').val(ref_num);
            $('#cn_header_ref').html('Ready for Pickkup Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#cn_order_date').html(' '+ $('#tm_order_date').html());
            $('#cn_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#cn_amount').html(' '+$('#tm_amount').html());
            $('#cn_order_status').html(' '+$('#tm_order_status').html());
            $('#cn_payment_date').html(' '+$('#tm_payment_date').html());
            $('#cn_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#cn_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#CancelOrder_modal').modal();
            $.LoadingOverlay("show");
            $.ajax({
                type:'post',
                url:base_url+'orders/Main_orders/getDeliveryCancellationCategories',
                data:{
                    'reference_num': ref_num
                },
                success:function(data){
                    var json_data = JSON.parse(data);

                    if(json_data.success){
                        cancelcat = json_data.categories.data.getDeliveryCancellationCategories;

                        $.each(cancelcat, function(key, value) {
                            $('#cn_cancellation_cat')
                                    .append($("<option></option>")
                                    .attr("value", value.id)
                                    .text(value.name));  
                        });
                        $.LoadingOverlay("hide");
                    }else{
                        //sys_toast_warning('Fetching of Cancellation Categories failed.');
                        showCpToast("warning", "Warning!", 'Fetching of Cancellation Categories failed.');
                        $.LoadingOverlay("hide");
                    }
                },
                error: function(error){
                    //sys_toast_error('Error');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                    $.LoadingOverlay("hide");
                }
            });
        }

    });

    $('#bookingConfirmBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value'); 
            $("#tag_rider").prop("checked", false);
            $('.grp_rider').hide();
            $.LoadingOverlay("show");
            $('#bc_id').val(ref_num);
            $('#bc_header_ref').html('Order Fulfillment for Ref # '+ $('#tm_order_reference_num').html());
            $('#bc_order_date').html(' '+ $('#tm_order_date').html());
            $('#bc_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#bc_amount').html(' '+$('#tm_amount').html());
            $('#bc_order_status').html(' '+$('#tm_order_status').html());
            $('#bc_payment_date').html(' '+$('#tm_payment_date').html());
            $('#bc_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#bc_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#bookingConfirm_modal').modal();
        }
    });

    $('#fulfillmentBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value'); 
            $("#tag_shipping").prop("checked", false);
            $('.grp_shipping_others').hide();
            // $('.grp_shipping').hide();
            $.LoadingOverlay("show");
            $('#f_id').val(ref_num);
            $('#f_header_ref').html('Order Fulfillment for Ref # '+ $('#tm_order_reference_num').html());
            $('#f_order_date').html(' '+ $('#tm_order_date').html());
            $('#f_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#f_amount').html(' '+$('#tm_amount').html());
            $('#f_order_status').html(' '+$('#tm_order_status').html());
            $('#f_payment_date').html(' '+$('#tm_payment_date').html());
            $('#f_payment_ref_num-f').html(' '+$('#tm_payment_ref_num').html());
            $('#f_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#fulfillment_modal').modal();
        }

    });

    $('#returntosenderBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#rs_id').val(ref_num);
            $('#rs_header_ref').html('Ready for Pickkup Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#rs_order_date').html(' '+ $('#tm_order_date').html());
            $('#rs_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#rs_amount').html(' '+$('#tm_amount').html());
            $('#rs_order_status').html(' '+$('#tm_order_status').html());
            $('#rs_payment_date').html(' '+$('#tm_payment_date').html());
            $('#rs_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#rs_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#returntosender_modal').modal();
        }

    });

    $('#redeliverBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#rd_id').val(ref_num);
            $('#rd_header_ref').html('Ready for Pickkup Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#rd_order_date').html(' '+ $('#tm_order_date').html());
            $('#rd_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#rd_amount').html(' '+$('#tm_amount').html());
            $('#rd_order_status').html(' '+$('#tm_order_status').html());
            $('#rd_payment_date').html(' '+$('#tm_payment_date').html());
            $('#rd_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#rd_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#redeliver_modal').modal();
        }

    });

    $('#shippedBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#s_id').val(ref_num);
            $('#s_header_ref').html('Ready for Pickkup Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#s_order_date').html(' '+ $('#tm_order_date').html());
            $('#s_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#s_amount').html(' '+$('#tm_amount').html());
            $('#s_order_status').html(' '+$('#tm_order_status').html());
            $('#s_payment_date').html(' '+$('#tm_payment_date').html());
            $('#s_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#s_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#shipped_modal').modal();
        }

    });

    $('#confirmedBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#oc_id').val(ref_num);
            $('#oc_header_ref').html('Order Confirmed for Ref # '+ $('#tm_order_reference_num').html());
            $('#oc_order_date').html(' '+ $('#tm_order_date').html());
            $('#oc_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#oc_amount').html(' '+$('#tm_amount').html());
            $('#oc_order_status').html(' '+$('#tm_order_status').html());
            $('#oc_payment_date').html(' '+$('#tm_payment_date').html());
            $('#oc_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#oc_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#confirmed_modal').modal();
        }

    });

    $('#form_save_payment').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        checkPayment();
        var form = $(this);
        var form_data = new FormData(form[0]);
        // form_data.append([ajax_token_name],ajax_token);
        var paid_amount   = $('#f_payment_fee').val();
        var total_amount  = $('#f_total_amount').val();
        var payment_isset = $('#tag_payment').is(":checked");

        if(parseFloat(paid_amount) != parseFloat(total_amount) && payment_isset == true){
            //sys_toast_warning('Paid amount is not equal to total order amount.');
            showCpToast("warning", "Warning!", 'Paid amount is not equal to total order amount.');
            $.LoadingOverlay("hide");
        }else{
            $.ajax({
                type: form[0].method,
                url: base_url+"orders/Main_orders/payOrder",
                data: form_data,
                contentType: false,   
                cache: false,      
                processData:false,
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    // update_token(json_data.csrf_hash);
                    if(json_data.success) {
                        resetPaymentForm();
                        $('#payment_modal').modal('hide');
                        //sys_toast_success(json_data.message);
                        showCpToast("success", "Success!", json_data.message);
                        window.location.assign(base_url+"Main_orders/orders/"+token);
                    }else{
                        //sys_toast_warning(json_data.message);
                        showCpToast("warning", "Warning!", data.message);
                    }
    
                },
                error: function(error){
                    //sys_toast_error('Something went wrong. Please try again.');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                }
            });
        }
    });

    $('#form_save_process').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/processOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#readyPickup_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_ready_pickup').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/readyPickupOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#readyPickup_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                    if(json_data.reload == 1){
                        // setInterval(
                        //     function(){ 
                        //         location.reload(); 
                        //     },
                        // 2000);
                        setTimeout(function(){location.reload()}, 2000);
                    }
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_cancel_order').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/cancelOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#CancelOrder_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    //sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_booking_confirm').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        checkRider();
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/bookingConfirmOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success) {
                    resetRiderForm();
                    $('#bookingConfirm_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                }

            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_fulfillment').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        checkShipping();
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/FulfilledOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success) {
                    resetShippingForm();
                    $('#fulfillment_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         // location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }

            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_returntosender').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/returntosenderOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#shipped_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_redeliver').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/redeliverOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#shipped_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_shipped').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/shippedOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#shipped_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    $('#form_save_confirmed').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/confirmedOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#confirmed_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    //location.reload();
                    showCpToast("success", "Success!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

    var checkPayment = function() {
        if($('#tag_payment').is(':checked') == false){
            resetPaymentForm();
        }
    }

    var resetPaymentForm = function() {
        $('#f_payment').val('');
        $('#f_payment_others').val('');
        $('#f_payment_ref_num').val('');
        $('#f_payment_fee').val('');
        $('#f_payment_notes').val('');
    }

    var checkRider = function() {
        if($('#tag_rider').is(':checked') == false){
            resetRiderForm();
        }
    }

    var resetRiderForm = function() {
        $('#bc_rider_name').val('');
        $('#bc_platenum').val('');
        $('#bc_conno').val('');
    }

    var checkShipping = function() {
        if($('#tag_shipping').is(':checked') == false){
            // resetShippingForm();
        }
    }

    var resetShippingForm = function() {
        $('#f_shipping').val('');
        $('#f_shipping_others').val('');
        $('#f_shipping_ref_num').val('');
        $('#f_shipping_fee').val('');
        $('#f_shipping_notes').val('');
    }

    function get_shop_branches(mainshopid, branchid){
        $.ajax({
            type:'post',
            url:base_url+'orders/Main_orders/get_shop_branches',
            data:{'mainshopid':mainshopid},
            success:function(data){
                if (data.success == 1) {
                    var list = "";
                    list += '<option value="" selected>Main</option>';
                    for(var x = 0; x < data.shopbranches.length; x++){
                        if(branchid == data.shopbranches[x].branchid){
                            list += "<option data-branchid='"+data.shopbranches[x].branchid+"' value='"+data.shopbranches[x].branchid+"'' selected>"+data.shopbranches[x].branchname+"</option>";
                        }else{
                            list += "<option data-branchid='"+data.shopbranches[x].branchid+"' value='"+data.shopbranches[x].branchid+"''>"+data.shopbranches[x].branchname+"</option>";
                        }
                    }
                    
                    $("#shop-branches").empty().append(list);
                }else{
                    var list = "";
                    list += '<option value="" selected>Main</option>';
                    $("#shop-branches").empty().append(list); 
                }
                if(branchid == 0){
                    $('#shop-branches').val("");
                    $('#prev_branchid').val(0);
                    $('#shop-branches').select2().trigger('change');
                }else{
                    $('#shop-branches').val(branchid);
                    $('#prev_branchid').val(branchid);
                    $('#shop-branches').select2().trigger('change');
                }
                $('#branch-remarks').val("");
                $('#branches-modal').modal();
            }
        });
    }

    $('#printBtn').click(function(e){
        reference_num = $(this).data('reference_num');
        window.open(base_url+'Main_orders/print_order/'+token+'/'+reference_num, '_blank');
    });

    $('#modifyOrders').click(function(e){
        reference_num     = $(this).data('reference_num');
        order_status_view = $(this).data('order_status_view');
        
        window.location.assign(base_url+'Main_orders/orders_modify/'+token+'/'+reference_num+"/"+order_status_view);
    });

    $("#pin_address").keypress(function(){
        $('#map').show(100); 
    });

    function checkGoogleMap(){

        var latitude  = $('#loc_latitude').val();
        var longitude = $('#loc_longitude').val();

        if(latitude == '14.594432888198051' && longitude == '121.01474704470128'){
            $('#map').hide(100);
            setTimeout(function(){ $('#pin_address').val(''); }, 4000);
        }
        else{
            $('#map').show(100); 
        }
    }

    function checkShopActve(){
        var shop_status  = $('#shop_status').val();

        if(shop_status != ''){
            //sys_toast_warning('Order cannot be process. Shop is not active anymore.');
            showCpToast("warning", "Warning!", 'Order cannot be process. Shop is not active anymore.');
            return 0;
        }
        else{
            return 1;
        }
    }

});