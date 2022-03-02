$(function(){

    $('.size-select').click(function(){
        $('.size-select').removeClass('selected');
        $(this).addClass('selected');
    });

    $('.add-to-cart').click(function(){

        var size = check_size_selected();
        if(size == ''){
            sys_toast_error('Please select size');
            return;
        }

        var product_id = $(this).data('product_id');
        var qty = $('#qty').val();
          
        $.ajax({
            url: base_url + 'user/cart/add_to_cart',
            type: 'POST',
            data: {
                product_id: product_id,
                size: size,
                quantity: qty,
            },
            success: function(response){
                if(response.success){
                    $('.cart-items').text(response.cart_items);
                    sys_toast_success(response.message);
                }
            },
            error: function(){

            }
        });
    });

    function check_size_selected(){
        var element = $('.size-select');        
        var size = '';
        if(element.length > 0){
            $.each(element,function(index, value){
                if($(value).hasClass('selected')){
                    size = $(value).data('size');
                }
            });
        }
        else{            
            size = 'Regular';
        }
        return size;
    }

});