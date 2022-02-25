$(function(){
	var base_url    = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var shop_url = $("body").data('shop_url');
    var token    = $("body").data('token');

    function fillDatatable(){
		var reference_num 	= $("#reference_num").val();

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			responsive: true,
			"columnDefs": [
				{ targets: 10, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				type: "post",
				url:base_url+"settings/void_record/Settings_void_record/order_table", // json datasource
				data:{'reference_num':reference_num}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	fillDatatable();    
});