$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	var url_ref_num = $('#url_ref_num').val();
	
    var fillDataTableProducts = function(reference_num) {
        dataTable = $('#table-item').DataTable({
            destroy: true,
            "serverSide": true,
            "searching": false,
             "columnDefs": [
                { "orderable": false, "targets": [ 0 ], "className": "text-center" },
                { "targets": [ 3, 4 ], "className": "text-right" }
            ],
            responsive: true,
            "ajax":{
                url:base_url+"admin/Main_orders/order_item_table", // json datasource
                type: "post",  // method  , by default get
                data: {
                    'reference_num': reference_num 
                },
                beforeSend:function(data){
                    $.LoadingOverlay("show"); 
                },
                complete: function(data) {  
                    $.LoadingOverlay("hide"); 
                },
                error: function(){  // error handling
                    $.LoadingOverlay("hide"); 
                    $(".table-grid-error").html("");
                    $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });
    }


    $('#processBtn').click(function(e){
        ref_num = $(this).data('value');
        $.LoadingOverlay("show");
         $('#po_id').val(ref_num);
         $('#po_header_ref').html('Processing Order for Ref # '+ $('#tm_order_reference_num').html());
        $('#po_order_date').html(' '+ $('#tm_order_date').html());
        $('#po_order_reference_num').html(' '+$('#tm_order_reference_num').html());
         $('#po_amount').html(' '+$('#tm_amount').html());
        $('#po_order_status').html(' '+$('#tm_order_status').html());
        $('#po_payment_date').html(' '+$('#tm_payment_date').html());
        $('#po_payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
        $('#po_payment_status').html(' '+$('#tm_payment_status').html());
        $.LoadingOverlay("hide");
        $('#processOrder_modal').modal();

    });

    

    $('#form_save_process').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"orders/Main_orders/processOrder",
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#readyPickup_modal').modal('hide');
                    //sys_toast_success(json_data.message);
                    showCpToast("success", "Success!", json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    location.reload();
                }else{
                    showCpToast("warning", "Warning!", json_data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    // sys_toast_warning(json_data.message);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                }
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });
    
    fillDataTableProducts(url_ref_num);
});