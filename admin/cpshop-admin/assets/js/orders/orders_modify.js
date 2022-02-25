$(function(){
	var base_url                 = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var shop_url                 = $("body").data('shop_url');
    var token                    = $("body").data('token');
	var url_ref_num              = $('#url_ref_num').val();
	var sys_shop                 = $('#sys_shop').val();
	var reference_number         = $('#reference_num').val();
	var order_status_view        = $('#order_status_view').val();
	var total_refund_amount      = 0;
	var order_shipping_amount    = $('#order_shipping_amount').val();
	var order_sub_total          = 0;
	var order_vouchertotal       = $('#order_vouchertotal').val();
    let productArray             = [];
    var productArr_index_counter = 0;
    var product_table_str        = "";

    populateProduct();

    $('#backBtn').click(function(){
        var backlink = $('.backlink').attr("href");
        window.location.assign(backlink);
    })
    

    $('#modifyBtn').click(function(e){
        checkShopActve();
        if(checkShopActve() == 1){
            ref_num = $(this).data('value');
            $.LoadingOverlay("show");
            $('#po_id').val(ref_num);
            $('#po_header_ref').html('Processing Order for Ref # '+ $('#tm_order_reference_num').html());
            $('#po_order_date').html(' '+ $('#tm_order_date').html());
            $('#po_order_reference_num').html(' '+$('#tm_order_reference_num').html());
            $('#po_amount').html(' '+$('#tm_total_amount').html());
            $('#po_order_status').html(' '+$('#tm_order_status').html());
            $('#po_payment_date').html(' '+$('#tm_payment_date').html());
            $('#po_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
            $('#po_payment_status').html(' '+$('#tm_payment_status').html());
            $.LoadingOverlay("hide");
            $('#modifyOrder_modal').modal();
        }

    });

    $('#modifyOrders').click(function(e){
        reference_num = $(this).data('reference_num');
        window.open(base_url+'Main_orders/orders_modify/'+token+'/'+reference_num);
    });

    function populateProduct(){
        $.ajax({
            type:'post',
            url:base_url+'orders/Main_orders/orders_product_modify_table',
            data:{
                'sys_shop': sys_shop,
                'reference_num': reference_number 
            },
			beforeSend:function(data) {
				$.LoadingOverlay("show"); 
			},
            success:function(data){
                var json_data = JSON.parse(data);

                if(json_data.success){
					counter = 0;
					if(json_data.productArr.length > 0){
						$('#tbody_product').empty();
					}
					clearProductStr();
					$.each(json_data.productArr, function(key, value) {

						dataArr = {
							'array_index_key' : productArr_index_counter,
							'log_id'          : value.log_id,
							'order_id'        : value.order_id,
							'product_id'      : value.product_id,
							'product_name'    : value.product_name,
							'edit_quantity'    : value.quantity,
							'quantity'        : value.quantity,
							'amount'          : value.amount,
							'total_amount'    : value.total_amount,
							'is_deleted'      : 0
						};

						productArr_index_counter++;
						productArray.push(dataArr);
					});
                    // console.log(productArray);
                    storeStrProductTable();
					prependProductTable();
                    updateOrderAmounts();
					$.LoadingOverlay("hide");
                }else{

                }
            },
        });
    }

    function storeStrProductTable(){
        $.each(productArray, function(key, value) {
            if(value.is_deleted == 0){
                buttons = "<button type='button' id='removeProductBtn' class='btn btn-danger mb-2' data-value='"+value.product_id+"' data-key='"+value.array_index_key+"'><i class='fa fa-trash'></i></button>";
                product_table_str += "<tr class='product_tr_"+value.product_id+"'>";
                product_table_str += "<td class='product_tr_"+value.product_id+"' width='12%'>"+value.product_name+"<input type='text' class='ddate' name='product_id[]' style='display:none' value='"+value.product_id+"'><input type='text' class='ddate' name='product_name[]' style='display:none' value='"+value.product_name+"'></td>";
            
                product_table_str += "<td class='product_tr_"+value.product_id+"' width='15%'><input type='text' min='0' class='form-control editQty allownumericwithoutdecimal notallowzero product_edit_qty"+value.product_id+"' name='product_edit_qty[]' onkeypress='return isNumberKey(event)' style='background-color:white;' value="+value.quantity+" data-array_index="+value.array_index_key+"></td>";
                product_table_str += "<td class='product_tr_"+value.product_id+"' width='6%'>"+value.quantity+"</td>";
                product_table_str += "<td class='product_tr_amount"+value.array_index_key+"' width='6%'>"+number_format(value.amount, 2)+"</td>";
                product_table_str += "<td class='product_tr_total_amount"+value.array_index_key+"' width='6%'><span id='txtProd_total_amount"+value.array_index_key+"'>"+number_format(value.total_amount, 2)+"</span></td>";
                product_table_str += "<td class='product_tr_"+value.product_id+"' width='8%'>"+buttons+"</td>";
                product_table_str += "</tr>";
            }
		});
    }

    $(document).delegate('.editQty','keyup paste focusout',function(e){

        array_index = $(this).data('array_index');
        edit_qty    = $(this).val();
        
        if(parseFloat(edit_qty) > parseFloat(productArray[array_index].quantity)){
            showCpToast("warning", "Note", "Edit quantity should not be higher than the previous quantity.");
            $(this).val(productArray[array_index].quantity);
        }
        else{
            if(edit_qty != ''){
                productArray[array_index].edit_quantity = edit_qty;
            }
        }
        updateOrderAmounts();
    });

    $(document).delegate('.editQty','focusout',function(e){
        array_index = $(this).data('array_index');
        edit_qty    = $(this).val();

        if(edit_qty == ''){
            $(this).val(productArray[array_index].quantity);
            productArray[array_index].edit_quantity = productArray[array_index].quantity;
        }
        updateOrderAmounts();
    });

    $(document).delegate('#removeProductBtn','click',function(e){
        var array_index = $(this).data('key');
        $('#deleteProductArrayIndex').val(array_index);
        $('#deleteProductModal').modal();

    });

    $(document).delegate('#removeProductConfirm','click',function(e){
        var array_index = $('#deleteProductArrayIndex').val();

        productArray[array_index].is_deleted = 1;
        clearProductStr();
        storeStrProductTable();
        prependProductTable();
        updateOrderAmounts();
    });

    $(document).delegate('#savesChangesConfirm','click',function(e){
       
        if(order_sub_total  == 0){
            showCpToast("warning", "Note", "Unable to save. You cannot remove all order items.");
        }
        else{
            $.ajax({
                type:'post',
                url:base_url+'orders/Main_orders/save_modify_orders',
                data:{
                    'sys_shop'     : sys_shop,
                    'reference_num': reference_number,
                    'productArray' : JSON.stringify(productArray)
                },
                beforeSend:function(data){
                    $.LoadingOverlay("show"); 
                    $("#removeProductConfirm").prop("disabled", true);
                },
                success:function(data){
                    $.LoadingOverlay("hide"); 
                    var json_data = JSON.parse(data);

                    if(json_data.success){
                        showCpToast("success", "Note", json_data.message);
                        $('#modifyOrder_modal').modal('hide');
                        window.location.assign(base_url+'Main_orders/orders_view/'+token+'/'+url_ref_num+"/"+order_status_view);
                    }else{
                        showCpToast("warning", "Note", json_data.message);
                        $("#removeProductConfirm").prop("disabled", false);
                        $('#modifyOrder_modal').modal('hide');
                    }
                },
                error: function(error){
                    $.LoadingOverlay("hide"); 
<<<<<<< Updated upstream
                    sys_toast_error('Error');
=======
                    //sys_toast_error('Error');
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
>>>>>>> Stashed changes
                }
            });

        }
    });

    function updateOrderAmounts(){
        new_total_amount    = 0;
        total_refund_amount = 0;
        order_sub_total     = 0;
        $.each(productArray, function(key, value) {
            if(value.is_deleted == 0){
                new_total_amount        = parseFloat(value.edit_quantity) * parseFloat(value.amount);
                prod_refunded_total_amt = parseFloat(new_total_amount) - parseFloat(value.total_amount);
                $('#txtProd_total_amount'+value.array_index_key).text(number_format(new_total_amount, 2));
                total_refund_amount += toPositive(prod_refunded_total_amt);
                order_sub_total     += new_total_amount;
            }
            if(value.is_deleted == 1){
                total_refund_amount += parseFloat(value.total_amount);
            }
        });

        // $('#tm_refund_amount').text(number_format(parseFloat(toPositive(total_refund_amount)).toFixed(2)));
        // $('#tm_subtotal').text(number_format(parseFloat(toPositive(order_sub_total)).toFixed(2)));
        $('#tm_refund_amount').text(number_format(toPositive(total_refund_amount), 2));
        $('#tm_subtotal').text(number_format(toPositive(order_sub_total-order_vouchertotal), 2));
        $('#tm_total_amount').text(number_format(toPositive(parseFloat(order_sub_total)+parseFloat(order_shipping_amount)), 2));
        
    }

    function clearProductStr(){
		product_table_str = "";
	}
	function prependProductTable(){
        $('#tbody_product').empty();
		$('#tbody_product').prepend(product_table_str);
	}

    function checkShopActve(){
        var shop_status  = $('#shop_status').val();

        if(shop_status != ''){
<<<<<<< Updated upstream
            sys_toast_warning('Order cannot be process. Shop is not active anymore.');
=======
            //sys_toast_warning('Order cannot be process. Shop is not active anymore.');
            showCpToast("warning", "Note", 'Order cannot be process. Shop is not active anymore.');
>>>>>>> Stashed changes
            return 0;
        }
        else{
            return 1;
        }
    }

});