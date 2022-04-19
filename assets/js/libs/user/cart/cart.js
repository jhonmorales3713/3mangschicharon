$(function(){

set_remove_item_click();
set_qty_change();
set_is_included_change();

//cart set items
function set_cart_data(cart_data){
    var cart_item_string = '';
    var summary_string = '';
    var sub_total = 0;

    Object.keys(cart_data).forEach(function(key){

        var is_included = cart_data[key]['is_included'] == 1 ? 'checked' : '';

        cart_item_string += '<div class="row p5"> ';
        cart_item_string += '<div class="col-1"><input type="checkbox" data-product_id="' + key +'" ' + is_included + ' class="is_included"/></div>';
        cart_item_string += '<div class="col-5"><strong>' + cart_data[key]['name'] + ' (' + cart_data[key]['size'] + ')</strong></div>';
        cart_item_string += '<div class="col-3"><input type="number" min="1" max="1000" class="qty" data-target="' + key + '" value="' + cart_data[key]['quantity'] + '" /> </div>';
        cart_item_string += '<div class="col-3"><span class="remove-item a" data-target="' + key + '"><i class="fa fa-times" aria-hidden="true"></i></span> </div>';
        cart_item_string += '</div>';

        var cur_amount = parseFloat(cart_data[key]['amount']) * parseInt(cart_data[key]['quantity']);

        summary_string += '<div class="row">';

        if(cart_data[key]['is_included'] == 1){
            summary_string += '<div class="col-7"><small><strong>' + cart_data[key]['name'] + '</strong> (' + cart_data[key]['size'] + ') <b>x</b> ' + cart_data[key]['quantity'] + '</small></div>';
            summary_string += '<div class="col-5 text-right"><span>' + format_number(cur_amount,2) + '</span></div>';
            sub_total += (parseFloat(cart_data[key]['amount']) * parseInt(cart_data[key]['quantity']));
        }        

        summary_string += '</div>';
    });

    sub_total += 50; //add automatic shipping fee

    $('#cart_div').html(cart_item_string);
    $('#summary_div').html(summary_string);
    $('#sub_total').html(php_money(sub_total));    
    $('#total_amount').val(sub_total)

    set_remove_item_click();
    set_qty_change();
    set_is_included_change();
}

$('#remove_from_cart').click(function(){  
    var selected_key = $('#item_key').val();
    $.ajax({
        url: base_url + 'user/cart/remove_from_cart',
        type: 'POST',
        data: {
            key: selected_key,
        },
        success: function(response){
            if(response.success){
                if(selected_key != 'all'){                      
                    if(typeof response.cart_data === 'undefined'){
                        window.location.reload();                            
                    }
                    else{
                        set_cart_data(response.cart_data);
                        $('#cart-items').text(response.cart_items);    
                        $('#remove_item_modal').modal('hide');
                    }                    
                }
                else{                    
                    window.location.reload();
                }
            }
        },
        error: function(){

        }
    });
});

$('#remove_item_modal').on('hidden.bs.modal',function(){
    $('#item_key').val('all');
    $('#remove_label').text('Are you sure you want to remove all items?');
});

function set_remove_item_click(){
    $('.remove-item').click(function(){
        var key = $(this).data('target');
        $('#item_key').val(key);
        $('#remove_label').text('Are you sure you want to remove this item?');
        $('#remove_item_modal').modal('show');    
    });
}

function set_qty_change(){
    $('.qty').on('change',function(){
        var target = $(this).data('target');
        var quantity = $(this).val();
        $.ajax({
            url: base_url + 'user/cart/modify_quantity',
            type: 'POST',
            data: {
                target: target,
                quantity: quantity,
            },
            success: function(response){
                if(response.success){                    
                    $('#cart-items').text(response.cart_items);    
                    set_cart_data(response.cart_data);
                }
            },
            error: function(){
    
            }
        });
    });
}

$('#btn_checkout').click(function(){
    window.location.href = base_url + 'user/cart/checkout';
});

$('.payment-method-select').click(function(){
    $('.payment-method-select').removeClass('method-selected');
    $(this).addClass('method-selected');
});

function set_is_included_change(){
    $('.is_included').on('change',function(){
        var target = $(this).data('product_id');
        var value = $(this).prop('checked');
        $.ajax({
            url: base_url + 'user/cart/modify_included',
            type: 'POST',
            data: {
                target: target,
                value: value,
            },
            success: function(response){
                if(response.success){                    
                    set_cart_data(response.cart_data);
                }                
            },
            error: function(){
    
            }
        });
    });
}

});