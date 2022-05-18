$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	
	// start - for loading a table
	fillDatatable();
    
	$("#search_clear_btn").click(function (e) {
		$("#date_from").val("");
		$("#date_to").val("");
		$("#payment_type").val("");
		$("#city").val("");
		$("#search").val("");
		fillDatatable();
	});
	$("#btnSearch").click(function (e) {
		e.preventDefault();
		date_from = $('#date_from').val();
		date_to = $('#date_to').val();
		if(date_from == '' || date_to == ''){
			//sys_toast_warning('Please input date.');
			showCpToast("warning", "Warning!", 'Please input date.');
		}
		else{
			$('#tableDiv').show(100);
			fillDatatable();
		}
	});
	function fillDatatable() {
        // <input type="hidden" name="_date_from" id="_date_from">
        // <input type="hidden" name="_date_to" id="_date_to">
        // <input type="hidden" name="_payment_type" id="_payment_type">
        // <input type="hidden" name="_search" id="_search">
        // <input type="hidden" name="_city" id="_city">
		var date_from = $("#date_from").val();
		var date_to = $("#date_to").val();
		var payment_type = $("#payment_type").val();
		var category = $("#category").val();
		var search = $("#search").val();
		var dataTable = $("#table-grid-inventory").DataTable({
			processing: false,
			destroy: true,
			searching: false,
			serverSide: true,
			ordering: false,
			responsive: true,
			order: [[ 0, "desc" ]],
            createdRow: function( row, data, dataIndex ) {
				//console.log(row);
				var data2 = $('#table-grid-inventory').DataTable().row(row).data();
				if(data2[8]=='Expired Stocks'){
					$(row).addClass( 'bg-danger text-white' );
			   	}
				if(data2[8]=='Expiring Soon'){
					$(row).addClass( 'bg-warning' );
		   		}
				if(data2[8]=='Out of Stocks'){
					$(row).addClass( 'bg-secondary text-white' );
				}
				// if ( data['jobStatus'] == "red" ) {
				// 	$(row).addClass( 'lightRed' );
				// }else if(data['jobStatus'] == "green"){
				// 	$(row).addClass( 'lightGreen' );
				// }else if(data['jobStatus'] == "amber"){
				// 	$(row).addClass( 'lightAmber' );
				// }
			},
			columnDefs: [
				// { targets: [6, 7], orderable: false, sClass: "text-center" },
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: -1 },
			],
			ajax: {
				type: "post",
				url: base_url + "admin/Main_reports/inventory_report_table", // json datasource
				data: {
                    date_from:date_from,
                    payment_type:payment_type,
                    date_to:date_to,
                    category:category,
                    search:search
				}, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);
					if (response.recordsTotal > 0) {
						$(".btnExport").show(100);
					} else {
						$(".btnExport").hide(100);
					}
					$("input[name=_record_status]").val(JSON.stringify(this.data));
					$("input[name=_date_from]").val(date_from);
					$("input[name=_date_to]").val(date_to);
					$("input[name=_search]").val(search);
					$("input[name=_payment_type]").val(payment_type);
					$("input[name=_category]").val(category);
				},
				error: function () {
					// error handling
					$(".table-grid-error").html("");
					$("#table-grid-order").append(
						'<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#table-grid_processing").css("display", "none");
				},
			},
		});
	}
});