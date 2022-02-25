$(function() {

    var base_url = $("body").data('base_url');
    var token = $("body").data('token');
    var url_ref_num = $("#url_ref_num").val();
    var reference_num = $("#ref_num").val();

    var fillDataTable = function(reference_num) {
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

    fillDataTable(url_ref_num);

    $("#closeBtn").on("click", function(e) {
        e.preventDefault();
        window.top.close(); // close current tab
    });

});