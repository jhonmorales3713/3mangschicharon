$(function(){

    $('.size-select').click(function(){
        $('.size-select').removeClass('selected');
        $(this).addClass('selected');
    });

    $('.add-to-cart').click(function(){

        var variant = check_variant_selected();
        if(Object.keys(variant).length === 0){
            sys_toast_error('Please select size');
            return;
        }
        
        var product_id = $(this).data('product_id');
        var qty = $('#qty').val();
          
        $.ajax({
            url: base_url + 'user/cart/add_to_cart/',
            type: 'POST',
            data: {
                product_id: product_id,
                size: variant.size,
                variant_id: variant.variant_id,
                discount_id: variant.discount_id,
                quantity: qty,
                from_checkout: false
            },
            success: function(response){                
                if(response.success){
                    $('.cart-items').text(response.cart_items);
                    sys_toast_success(response.message);
                }else{
                    sys_toast_error(response.message);
                }
            },
            error: function(){

            }
        });
    });

    $('.order-now').click(function(e){ //order now redirects to checkout with product id
        e.preventDefault(); 

        var variant = check_variant_selected();
        if(Object.keys(variant).length === 0){
            sys_toast_error('Please select size');
            return;
        }

        var product_id = $(this).data('product_id');
        var qty = $('#qty').val();
        var max_checkout = $(this).data('max_checkout');
        $.ajax({
            url: base_url + 'user/cart/add_to_cart/',
            type: 'POST',
            data: {
                product_id: product_id,
                size: variant.size,
                max_checkout: max_checkout,
                variant_id: variant.variant_id,
                quantity: qty,
                order_now: true              
            },
            success: function(response){
                if(response.success){
                    $('.cart-items').text(response.cart_items);
                    //console.log(response.message);

                    window.location.href = base_url + 'user/cart/checkout/'+product_id+'/'+variant.variant_id+'/'+variant.size + '/' +qty+ '/' + true;
                    sys_toast_success(response.message);
                }else{
                    sys_toast_error(response.message);
                }
            },
            error: function(){

            }
        });
        
    });

    function check_variant_selected(){
        var element = $('.size-select');        
        var variant = {};
        var variant_id = '';
        if(element.length > 0){
            $.each(element,function(index, value){
                if($(value).hasClass('selected')){
                    variant.size = $(value).data('size');
                    variant.variant_id = $(value).data('variant_id');
                    variant.discount_id = $(value).data('discountid');
                }
            });
        }
        else{            
            size = 'R';
        }
        console.log(variant)
        return variant;
    }

});