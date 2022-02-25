$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var checkBoxChecker   = 0;

	// start - for loading a table
	function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _vcode 			= $("input[name='_vcode']").val();
		var _order_ref 		= $("input[name='_order_ref']").val();
		var date_from       = $("#date_from").val();

		var dataTable = $('#reclaimed_order_table').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"searching": false,
			responsive: true,
			'columnDefs': [{
				'targets': 0,
				'searchable':false,
				'orderable':false,
				'className': 'dt-body-center'
			 }],
			"ajax":{
				type: "post",
				url:base_url+"vouchers/Reclaimed_vouchers/reclaimed_vouchers_table", // json datasource
				data: {'_record_status':_record_status,
				       '_vcode':_vcode,
					   '_order_ref':_order_ref,
					   'date_from': date_from
					   }, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);

					if(response.data.length > 0){
						$('.btnExport').show(100);
					}
					else{
						$('#btnExport').hide(100);
					}
					// console.log(JSON.stringify(decodeURIComponent(this.data)));
					$("#_search").val(JSON.stringify(this.data));
					$("#_vcode_export").val(_vcode);
					$("#_order_ref_export").val(_order_ref);
					$("#_record_status_export").val(_record_status);
					$("#date_from_export").val(date_from);
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table

	// start - for search purposes

	$("#_record_status").change(function(){
		$("#btnSearch").click(); 
	});

	$("#search_hideshow_btn").click(function(e){
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if(!visibility){
			//visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		}else{
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
   		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function(e){
		$(".search-input-text").val("");
		fillDatatable();
	})

	$(".enter_search").keypress(function(e) { 
        if (e.keyCode === 13) { 
			$("#btnSearch").click(); 
			return false;
        } 
    });

	$('#btnSearch').click(function(e){
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes



	

});




