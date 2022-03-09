$(function(){



$('#btn_place_order').click(function(){    
    var data = {
        total_amount: $('#sub_total').val(),
        delivery_amount: 50,   
        shipping_data: get_shipping_details()     
    };   

    /*
    data.address_category_id = $('#address_category_id').val();
    data.alias = $('#alias').val();
    data.full_name = $('#full_name').val();
    data.contact_no = $('#contact_no').val();
    data.province = $('#province').val();
    data.city = $('#city').val();
    data.barangay = $('#barangay').val();
    data.zip_code = $('#zip_code').val();
    data.address = $('#address').val();   
    data.notes = $('#notes').val();
    */

    $.ajax({
        url: base_url + 'user/cart/place_order',
        type: 'POST',
        data: data,
        success: function(response){
            if(response.success){
                sys_toast_success(response.message);
                $('.cart-items').text('0');
            }
            else{
                clearFormErrors();
                show_errors(response,$('#checkout_container'));
            }
        },
        error: function(){

        }
    })
});

function get_shipping_details(){
    var data = {
        address_category_id: $('#address_category_id').val(),
        alias: $('#alias').val(),
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


});