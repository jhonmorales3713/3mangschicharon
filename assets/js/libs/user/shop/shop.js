$('.product-img').click(function(){
    product_id = $(this).data('product_id');
    window.location.href = base_url + 'products/'+product_id;
});