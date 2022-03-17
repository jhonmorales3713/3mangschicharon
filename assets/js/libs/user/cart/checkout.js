$(function(){

$('#btn_place_order').click(function(){
    var data = {
        total_amount: $('#total_amount').val(),
        delivery_amount: 50,   
        shipping_data: get_shipping_details()     
    };    

    $.ajax({
        url: base_url + 'user/cart/place_order',
        type: 'POST',
        data: data,
        success: function(response){
            if(response.success){
                sys_toast_success(response.message);
                $('.cart-items').text(response.cart_items);
                window.location.href = base_url + 'user/orders/receipt/'+response.order_id;
            }
            else{
                clearFormErrors();
                show_errors(response,$('#checkout_container'));
            }
        },
        error: function(){

        }
    });
});

function get_shipping_details(){
    var data = {
        address_category_id: $('#address_category_id').val(),
        address_alias: $('#alias').val(),
        full_name: $('#full_name').val(),
        contact_no: $('#contact_no').val(),
        province: $('#province').val(),
        city: $('#city').val(),
        barangay: $('#barangay').val(),        
        zip_code: $('#zip_code').val(),
        address: $('#address').val(),    
        notes: $('#notes').val()
    }   
    return data; 
}

$('#save_address_btn').click(function(){
    var address = get_shipping_details();
    save_shipping_address(address);
});

$('#new_address').click(function(){
    $('#address_form').slideToggle(400);
});

function save_shipping_address(address){
    $.ajax({
        url: base_url + 'user/shipping/save_shipping_address',
        type: 'POST',
        data: address,
        success: function(response){
            if(response.success){
                clearFormErrors();
                sys_toast_success(response.message);
                $('#address_form').show(300);
            }
            else{
                clearFormErrors();                
                show_errors(response,$('#address_form'));                
            }
        },
        error: function(){

        }
    });
}

function set_addresses(){

}

function set_address_select(){

}

});