$('.product-img').click(function(){
    var product_id = $(this).data('product_id');
    window.location.href = base_url + 'products/'+product_id;
});
// $(".category_checkbox").on('change',function(){
//     var categories = Array();
//     $('.category_checkbox').each(function() {
//         if($(this).prop('checked')){
//             categories.push($(this).val());
//         }
//     });
//     if(categories.length == 0){
//         $('.category_checkbox').each(function() {
//             $(this).prop('checked',true);
//         });
//     }
// });
$("#btn_searchbox").click(function(){
    window.location.href = base_url + 'user/shop?search='+$("#searchbox").val();
});

$("#searchbox").on('keyup', function (e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        window.location.href = base_url + 'user/shop?search='+$("#searchbox").val();
    }
});
$(".apply-filter").click(function(){
    var categories = Array();
    $('.category_checkbox').each(function() {
        if($(this).prop('checked')){
            categories.push($(this).val());
        }
    });
    if(categories.length == 0){
        sys_toast_warning("Select category first");
    }else{
        $.ajax({
            url: base_url + 'user/shop/search_category/',
            type: 'POST',
            data: {         
                categories:categories
            },
            success: function(response){
                window.location.href = base_url + 'user/shop?search='+$("#searchbox").val();
                // sys_toast_success(response.message);
            },
            error: function(){
    
            }
        });
    }
});

