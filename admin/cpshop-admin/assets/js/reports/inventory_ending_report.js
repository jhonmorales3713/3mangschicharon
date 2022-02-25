$(function () {
	var base_url = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token = $("#token").val();
	// start - for loading a table
	function fillDatatable() {
		var _name             = $("input[name='_name']").val();
		var _shops            = $("select[name='_shops']").val();
		var _searchproduct    = $("select[name='_searchproduct']").val();
		var _branches         = $("select[name='_branches']").val();
		var date_from         = $("#date_from").val();
		var date_to           = $("#date_to").val();

		console.log(_branches);

		var dataTable = $("#table-grid").DataTable({
			processing: false,
			destroy: true,
			searching: false,
			serverSide: true,
			responsive: true,
			columnDefs: [
				// { targets: [3], orderable: false, sClass: "text-center" },
				// { responsivePriority: 1, targets: 0 },
				// { responsivePriority: 2, targets: -1 },
			],
			ajax: {
				type: "post",
				url: base_url + "reports/Inventory_report/inventory_ending_table", // json datasource
				data: {
					_name: _name,
					_searchproduct:_searchproduct,
					_shops: _shops,
					_branches: _branches,
					date_from: date_from,
					date_to: date_to
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
						$("#btnExport").hide(100);
					}
					$("#_name_export").val(_name);
					$("#_shops_export").val(_shops);
					$("#_searchproduct_export").val(_searchproduct);
					$("#_branches_export").val(_branches);
					$("#date_from_export").val(date_from);
					$("#date_to_export").val(date_to);
					$("#request_filter").val(JSON.stringify(this.data));
				},
				error: function () {
					// error handling
					$(".table-grid-error").html("");
					$("#table-grid").append(
						'<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#table-grid_processing").css("display", "none");
				},
			},
		});
	}

	fillDatatable();
	// end - for loading a table

	// start - for search purposes

	$("#search_hideshow_btn").click(function (e) {
		e.preventDefault();

		var visibility = $("#card-header_search").is(":visible");

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html(
				'<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>'
			);
		} else {
			//not visible
			$("#search_hideshow_btn").html(
				'<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>'
			);
		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function (e) {
		$("#_name").val("");
		$("#_shops").prop("selectedIndex", 0);
		$("#_branches").prop("selectedIndex", 0);
		fillDatatable();
	});

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$("#btnSearch").click(function (e) {
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes

	$("#table-grid").delegate(".action_edit", "click", function () {
		let id = $(this).data("value");
	});

	$("#_shops").change(function() {
        $.LoadingOverlay("show"); 
        $("#_branches option").remove();
        _shops = $(this).val();
		if(_shops != ""){
			$.ajax({
				type:'post',
				url:base_url+'reports/Inventory_report/get_branches',
				data:{
					'shopid': _shops 
				},
				success:function(data){
					$.LoadingOverlay("hide"); 
					var json_data = JSON.parse(data);
	
					if(json_data.success){
						$('#_branches')
							.append($("<option></option>")
							.attr("value", "")
							.text('All Branches'));

						$('#_branches')
							.append($("<option></option>")
							.attr("value", 0)
							.text('Main'));
	
						$.each(json_data.data, function(key, value) {
							$('#_branches')
								.append($("<option></option>")
								.attr("value", value.branchid)
								.text(value.branchname));
						});
					}else{
						//sys_toast_warning('No branch found');
						showCpToast("warning", "Warning!", 'No branch found');
					}
				},
				error: function(error){
					$.LoadingOverlay("hide"); 
					//sys_toast_error('Error');
					showCpToast("error", "Error!", 'Error');
				}
			});
		}
		else{
			$('#_branches')
				.append($("<option></option>")
				.attr("value", '')
				.text('All Branch'));
			
			$.LoadingOverlay("hide"); 

		}
        

    });

});

$('#date_to').datepicker().on('changeDate', (e) => {
	var todaydate = $('#todaydate').val();
	var new_start_date = moment(e.date).subtract(90, 'day').format('MM/DD/YYYY');

	$('#date_from').datepicker('setStartDate', new_start_date);
	$('#date_to').datepicker('setEndDate', todaydate);
});

$("#date_from").click(function (e) {
	var date_to = $('#date_to').val();
	var new_start_date = moment(date_to).subtract(90, 'day').format('MM/DD/YYYY');
	$('#date_from').datepicker('setStartDate', new_start_date);
});