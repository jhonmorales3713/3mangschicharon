let productArray      = [];
let addProdArr        = [];
let deletedProductArr = [];
var checkBoxChecker   = 0;
var showEntries       = 10;
var token        = $("body").data('token');

if($('#product_voucher_type').is(':checked')){ 

    $("#table-product-div").css("display", "block");
    $("#table-shop-div").css("display", "none");

}else{

    $("#table-product-div").css("display", "none");
    $("#table-shop-div").css("display", "block");

}


if($('#shop_voucher_type').is(':checked')){ 

    $("#table-product-div").css("display", "none");
    $("#table-shop-div").css("display", "block");

}else{

    $("#table-product-div").css("display", "block");
    $("#table-shop-div").css("display", "none");

}

$('#product_voucher_type').click(function(){
    if($('#product_voucher_type').is(':checked')){ 

        $("#table-product-div").css("display", "block");
        $("#table-shop-div").css("display", "none");
    
    }else{

        $("#table-product-div").css("display", "none");
        $("#table-shop-div").css("display", "block");

    }
});

$('#shop_voucher_type').click(function(){
    if($('#shop_voucher_type').is(':checked')){ 

        $("#table-product-div").css("display", "none");
        $("#table-shop-div").css("display", "block");
    
    }else{

        $("#table-product-div").css("display", "block");
        $("#table-shop-div").css("display", "none");

}
});

$('#product_voucher_type').click(function(){
    if($('#product_voucher_type').is(':checked')){ 

        $("#table-product-div").css("display", "block");
        $("#table-shop-div").css("display", "none");
    
    }else{

        $("#table-product-div").css("display", "none");
        $("#table-shop-div").css("display", "block");

    }
});

$('#shop_voucher_type').click(function(){
    if($('#shop_voucher_type').is(':checked')){ 

        $("#table-product-div").css("display", "none");
        $("#table-shop-div").css("display", "block");
    
    }else{

        $("#table-product-div").css("display", "block");
        $("#table-shop-div").css("display", "none");

}
});


$('#shopid').change(function(){
    var code = $("#shopid option:selected").attr('data-code');
    $('#shopcode').val(code)
})


if($('#disc_ammount_type').val() === '2'){
    $("#disc_off").css("display", "block");
    $("#maximum_discount_price").css("display", "block");
}else{
    $("#maximum_discount_price").css("display", "none");
    $("#disc_off").css("display", "none");
}

if($('#set_amount_limit').is(':checked')) { 
    $("#disc_ammountdiv").css("display", "block");
    $( '#set_amount_limit1' ).prop( "checked", false );
}

if($('#set_amount_limit1').is(':checked')) { 
    $("#disc_ammountdiv").css("display", "none");
    $( '#set_amount_limit' ).prop( "checked", false );
}

$('#disc_ammount_type').click(function(){
    if($('#disc_ammount_type').val() === '2'){
        $("#disc_off").css("display", "block");
        $("#maximum_discount_price").css("display", "block");
    }else{
        $("#maximum_discount_price").css("display", "none");
        $("#disc_off").css("display", "none");
    }
});



$('#set_amount_limit').click(function(){
    if($('#set_amount_limit').is(':checked')) { 
        $("#disc_ammountdiv").css("display", "block");
        $( '#set_amount_limit1' ).prop( "checked", false );
    }
});


$('#set_amount_limit1').click(function(){

    if($('#set_amount_limit1').is(':checked')) { 
        $("#disc_ammountdiv").css("display", "none");
        $( '#set_amount_limit' ).prop( "checked", false );
    }
});


$('#product_voucher_type').click(function(){

    $("#checkbox_all").prop("checked", false);
    populateProduct();

    $('#addProductModal').modal({
        backdrop: 'static',
        keyboard: false
    })
   

});




$('#addProductButton').click(function(){

    $("#checkbox_all").prop("checked", false);
    populateProduct();

    $('#addProductModal').modal({
        backdrop: 'static',
        keyboard: false
    })
   

});


$('#addShopButton').click(function(){

    $("#checkbox_all").prop("checked", false);
    populateProductShop();
    $('#addShopModal').modal({
            backdrop: 'static',
            keyboard: false
    })

});


$(document).delegate('#removeProdPromo','click',function(e){
    var index = $(this).data('value');
    var key = $(this).data('key');
    $('#deleteProdPromoId').val(index);
    $('#deleteProdPromoKey').val(key);
    $('#deleteProdPromoModal').modal();
    console.log(productArray);
});

$(document).delegate('#deleteProdPromoConfirm','click',function(e){
    $.LoadingOverlay("show");
    var index = $('#deleteProdPromoId').val();
    var key = $('#deleteProdPromoKey').val();
    $('.product_tr_'+index).remove();
    $('#deleteProdPromoModal').modal();
    deletedProductArr.push(index);
    productArray.splice(key, 1);
    $.LoadingOverlay("hide");
    //sys_toast_success('Product successfully removed.');
    showCpToast("success", "Success!", 'Product successfully removed.'); 
})


$('#shop_voucher_type').click(function(){

    $("#checkbox_all").prop("checked", false);
    populateProductShop();
    $('#addShopModal').modal({
            backdrop: 'static',
            keyboard: false
    })


});


$('#btnSearch').click(function(e){
    e.preventDefault();
    populateProduct();
    populateProductShop();
    $( "#checkbox_all:checkbox:checked" ).trigger( "click" );
    
});


$('#table-grid-productpromo').on( 'page.dt', function () {
    if($("#checkbox_all").prop("checked") == true){
        $( "#checkbox_all" ).prop( "checked", false );
        checkBoxChecker = 0;
    }
    else{
        checkBoxChecker = 3;
    }
 });

$("#checkbox_all").click(function(){
    // alert('test');
    if(checkBoxChecker == 0){
        $( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
        checkBoxChecker = 1;
    }
    else if(checkBoxChecker == 2){
        $( ".checkbox_perprod:checkbox:checked" ).trigger( "click" );
        checkBoxChecker = 0;
    }
    else if(checkBoxChecker == 3){
        $( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
        checkBoxChecker = 1;
    }
    else if(checkBoxChecker == 1){
        $( ".checkbox_perprod" ).trigger( "click" );
        checkBoxChecker = 0;
    }
});


$('#table-grid-productpromo').on('click', "input[name='checkbox_perprod[]']", function() {
    var value = $(this).val();
    if(this.checked){
        dataArr = {
                'product_id'          : $(this).val(),
                'sys_shop'            : $(this).data('sys_shop'),
                'product_name'        : $(this).data('product_name'),
                'product_promo_type'  : 1,
                'product_promo_rate'  : 1,
                'product_orig_price'  : $(this).data('product_price'),
                'product_promo_price' : "1.00",
                'product_promo_stock' : 1,
                'product_curr_stock'  : $(this).data('product_stock'),
                'product_purch_limit' : 1,
                'product_status'      : 1
            };
        addProdArr.push(dataArr);
    }
    else{
        var index = addProdArr.findIndex(p => p.product_id == $(this).val());
        if (index !== -1) {
            addProdArr.splice(index, 1);
        }
        if($("#checkbox_all").prop("checked") == true){
            checkBoxChecker = 2;
        }
        else{
            checkBoxChecker = 3;
        }
    }
});




$("#btnConfirm").click(function(){

    counter = 0;
    let product_id='';
    let product_name ='';
    $.each(addProdArr, function(key, value) {
        dataArr = {
            'product_id'          : value.product_id,
            'sys_shop'            : value.sys_shop,
            'product_name'        : value.product_name,
            'product_promo_type'  : value.product_promo_type,
            'product_promo_rate'  : value.product_promo_rate,
            'product_orig_price'  : value.product_orig_price,
            'product_promo_price' : value.product_promo_price,
            'product_promo_stock' : value.product_promo_stock,
            'product_curr_stock'  : value.product_curr_stock,
            'product_purch_limit' : value.product_purch_limit,
            'product_status'      : value.product_status,
            'product_length'      : addProdArr.length
        };
        alignProductPromotion(dataArr, counter);
        productArray.push(dataArr);
        counter++;
    });
    if(addProdArr.length > 0){
        //sys_toast_success('Product has been successfully added.');
        showCpToast("success", "Success!", 'Product has been successfully added.'); 	
        $('#addProductModal').modal('hide');
        
        addProdArr = [];
    }
    else{
        //sys_toast_warning('You did not select any product.');
        showCpToast("warning", "Warning!", 'You did not select any product.'); 	
    }
    
});

function alignProductPromotion(prodArr, key){
    $("#table-product").css("display", "block");
        console.log(key);
        displayProductPromotion(prodArr, key);
}


function displayProductPromotion(value, key){


    if(value.product_id != null && value.product_name != null && value.product_id && 'null' && value.product_name != 'null'){

      

        buttons = "";
        buttons += "<button type='button' id='removeProdPromo' class='btn btn-danger mb-2' data-value='"+value.product_id+"' data-key='"+key+"'><i class='fa fa-trash'></i></button>";
        str = "";
        str += "<tr class='product_tr_"+value.product_id+"'>";
        str += "<td class='product_tr_"+value.product_id+"' width='12%'>"+value.product_name+"<input type='text' class='ddate' name='product_id[]' style='display:none' value='"+value.product_id+"'><input type='text' class='ddate' name='product_name[]' style='display:none' value='"+value.product_name+"'></td>";
        str += "<td class='product_tr_"+value.product_id+"'  style='text-align: center;' width='8%'>"+buttons+"</td>";
        str += "</tr>";
        $('#tbody_prodpromo').prepend(str);

    }
    
}


function populateProduct() {
    var select_shop 	= $("#select_shop").val();
    var select_category = $("#select_category").val();
    var _name        	= $("input[name='_name']").val();

    $.ajax({
      url:base_url+"promotion/Main_promotion/product_table", // json datasource
      type: 'post',
      data: {
            'select_shop':select_shop,
            'select_category':select_category,
            '_name':_name,
            'productArray': JSON.stringify(productArray)
       },
      beforeSend: function () {
        $.LoadingOverlay("show");          
      },
      complete: function (data) {
          $.LoadingOverlay("hide");          
          var response = $.parseJSON(data.responseText);
         
          if(response.data.length > 0){
            
          }
          else{
            
          } 

          var dataTable = $('#table-grid-productpromo').DataTable({
            "processing": true,
            // "responsive": true,
            "searching": true,
            "destroy": true,
            "data": response.data,
            "order":[[0, 'asc']],
            "columnDefs": [
                {  orderable: false, targets: 0},
                { responsivePriority: 1, width: "10%", targets: 0},
                { type: 'sort-numbers-ignore-text', targets : 2 }
            ],
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[parseInt(showEntries), 10, 25, 50, 100], [parseInt(showEntries), 10, 25, 50, 100]]
          });
      },
      error: function () {  // error handling
        $.LoadingOverlay("hide");
      }
    })
}





/// shop modal


$("#checkbox_all_shop").click(function(){
    // alert('test');
    if(checkBoxChecker == 0){
        $( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
        checkBoxChecker = 1;
    }
    else if(checkBoxChecker == 2){
        $( ".checkbox_perprod:checkbox:checked" ).trigger( "click" );
        checkBoxChecker = 0;
    }
    else if(checkBoxChecker == 3){
        $( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
        checkBoxChecker = 1;
    }
    else if(checkBoxChecker == 1){
        $( ".checkbox_perprod" ).trigger( "click" );
        checkBoxChecker = 0;
    }
});

function populateProductShop() {
    var _name        	= $("input[name='_name']").val();

    $.ajax({
      url:base_url+"promotion/Main_promotion/shop_table", // json datasource
      type: 'post',
      data: {
            '_name':_name,
            'productArray': JSON.stringify(productArray)
       },
      beforeSend: function () {
        $.LoadingOverlay("show");          
      },
      complete: function (data) {
          $.LoadingOverlay("hide");          
          var response = $.parseJSON(data.responseText);
         
          if(response.data.length > 0){
            
          }
          else{
            
          } 

          var dataTable = $('#table-grid-shop').DataTable({
            "processing": true,
            // "responsive": true,
            "searching": true,
            "destroy": true,
            "data": response.data,
            "order":[[0, 'asc']],
            "columnDefs": [
                {  orderable: false, targets: 0},
            ],
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[parseInt(showEntries), 10, 25, 50, 100], [parseInt(showEntries), 10, 25, 50, 100]]
          });
      },
      error: function () {  // error handling
        $.LoadingOverlay("hide");
      }
    })
}



$('#table-grid-shop').on('click', "input[name='checkbox_perprod[]']", function() {
    var value = $(this).val();
    if(this.checked){
        dataArr = {
                'shop_id'          : $(this).val(),
                'shopname'         : $(this).data('shopname'),

            };
        addProdArr.push(dataArr);
    }
    else{
        var index = addProdArr.findIndex(p => p.product_id == $(this).val());
        if (index !== -1) {
            addProdArr.splice(index, 1);
        }
        if($("#checkbox_all").prop("checked") == true){
            checkBoxChecker = 2;
        }
        else{
            checkBoxChecker = 3;
        }
    }
});

$('#table-grid-shop').on( 'page.dt', function () {
    if($("#checkbox_all").prop("checked") == true){
        $( "#checkbox_all" ).prop( "checked", false );
        checkBoxChecker = 0;
    }
    else{
        checkBoxChecker = 3;
    }
 });



 $("#btnConfirmShop").click(function(){
    // alert('test');
    counter = 0;
    let shop_id='';
    let shopname ='';
    $.each(addProdArr, function(key, value) {
        dataArr = {
            'shop_id'          : value.shop_id,
            'shopname'         : value.shopname,
        };
        alignProductShop(dataArr, counter);
        productArray.push(dataArr);
        counter++;
    });
    if(addProdArr.length > 0){
        //sys_toast_success('Shop has been successfully added.');
        showCpToast("success", "Success!", 'Shop has been successfully added.');	
        $('#addShopModal').modal('hide');
        
        addProdArr = [];
    }
    else{
        //sys_toast_warning('You did not select any product.');
        showCpToast("warning", "Warning!", 'You did not select any product.');	
    }
    
});

function alignProductShop(prodArr, key){
    $("#table-shop").css("display", "block");
        console.log(key);
        displayShop(prodArr, key);

}


function displayShop(value, key){


    if(value.shop_id != null && value.shopname != null && value.shop_id && 'null' && value.shopname != 'null'){

    
        buttons = "";
        buttons += "<button type='button' id='removeShop' class='btn btn-danger mb-2' data-value='"+value.shop_id+"' data-key='"+key+"'><i class='fa fa-trash'></i></button>";
        str = "";
        str += "<tr class='shop_tr_"+value.shop_id+"'>";
        str += "<td class='shop_tr_"+value.shop_id+"' width='12%'>"+value.shopname+"<input type='text' class='ddate' name='shop_id[]' style='display:none' value='"+value.shop_id+"'></td>";
        str += "<td class='shop_tr_"+value.shop_id+"'  style='text-align: center;' width='8%'>"+buttons+"</td>";
        str += "</tr>";
        $('#tbody_shops').prepend(str);

    }
    
}


$(document).delegate('#removeShop','click',function(e){
    var index = $(this).data('value');
    var key = $(this).data('key');
    $('#deleteShopId').val(index);
    $('#deleteShopKey').val(key);
    $('#deleteShopModal').modal();
    console.log(productArray);
});

$(document).delegate('#deleteShopConfirm','click',function(e){
    $.LoadingOverlay("show");
    var index = $('#deleteShopId').val();
    var key = $('#deleteShopKey').val();
    $('.shop_tr_'+index).remove();
    $('#deleteShopModal').modal();
    deletedProductArr.push(index);
    productArray.splice(key, 1);
    $.LoadingOverlay("hide");
    //sys_toast_success('Shop successfully removed.');
    showCpToast("success", "Success!", 'Shop successfully removed.');
})


var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var edit_loadstate = false;
    $('#save_voucher').submit(function(e){
        e.preventDefault();

        var form = $(this);
        var form_data = new FormData(form[0]);

  
        if($('#disc_ammount_type').val() === '1'){
            disc_ammount = 0;
        }else if($('#disc_ammount_type').val() === '2'){
            disc_ammount  =  $('#disc_ammount').val();
        }else{
            disc_ammount = 0;
        }
        form_data.append('disc_ammount', (disc_ammount));

        voucher_code  =  $('#voucher_code').val();


        if($('#product_id').val() == '' && $('#shop_id').val() == '' ){
            //sys_toast_warning('Please select a voucher type with products or shopname');
            showCpToast("warning", "Warning!", 'Please select a voucher type with products or shopname');
        }
        else if(checkInputs("#save_voucher") == 0){
            $.ajax({
                type:'post',
                url: base_url+'promotion/Main_promotion/update_voucher',
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $(".btn-save").prop('disabled', true); 
                    $(".btn-save").text("Please wait...");
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    $(".btn-save").prop('disabled', false); 
                    $(".btn-save").text("Save");
                    if (data.success == 1) {
                        setTimeout(function(){  window.location.assign(base_url+"Main_promotion/voucher_discounts/"+token); }, 2000);
                        showCpToast("success", "Success!", "Record updated successfully!");
                        // messageBox(data.message, 'Success', 'success');
                    }else{
                        showCpToast("Warning", "Warning!", "Something went wrong, Please Try again!");
                    }
                }
            });
        }



});

  