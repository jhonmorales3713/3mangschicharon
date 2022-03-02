$('#delete_cart_all').click(function(){    
    $.ajax({
        url: base_url + 'user/cart/clear_cart',
        type: 'POST',
        data: {
            action: 'delete',
        },
        success: function(response){
            if(response.success){
                $('.cart-items').text(response.cart_items);
                $('#cart_container').html('<center>No Items in cart <br><br> <a href="'+ base_url +'shop">Back to shopping </a></center>')
                $('.cart-item-list').html('');
            }
        },
        error: function(){

        }
    });
});