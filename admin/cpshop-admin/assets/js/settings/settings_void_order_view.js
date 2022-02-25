$(function(){
	var base_url    = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var shop_url = $("body").data('shop_url');
    var token    = $("body").data('token');
	var url_ref_num = $('#url_ref_num').val();
	var reference_num = $('#reference_num').val();

    var fillDataTableProducts = function(reference_num) {
        dataTable = $('#table-item').DataTable({
            destroy: true,
            "serverSide": true,
             "columnDefs": [
                { "orderable": false, "targets": [ 0 ], "className": "text-center" }
            ],
            responsive: true,
            "ajax":{
                url:base_url+"orders/Main_orders/order_item_table", // json datasource
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

    fillDataTableProducts(url_ref_num);

    $('#backBtn').click(function(){
        window.location.assign(base_url+"settings/void_record/Settings_void_record/void_record/"+token);
    })

    $('#voidBtn').click(function(e){
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
        $('#void_modal').modal();

    });

    $('#form_void_order').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        
        $.ajax({
            type: form[0].method,
            url: base_url+"settings/void_record/Settings_void_record/voidOrder",
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
                    window.location.assign(base_url+"Settings_void_record/void_order/"+token+'/'+reference_num);
                }else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                }
            },
            error: function(jqXHR, exception){
                var msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status == 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status == 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", msg);
            }
        });

    });

});