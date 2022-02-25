$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	// start - for loading a table
	function fillDatatable() {

		var _reference_number = $("input[name='_reference_number']").val();
		var date_from = $("#date_from").val();
		var date_to = $("#date_to").val();

		var filters = {

			_reference_number: $("input[name='_reference_number']").val(),
			date_from: $("#date_from").val(),
			date_to: $("#date_to").val()
		}

		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			searching: false,
			"serverSide": true,
			responsive: true,
			columnDefs: [{ responsivePriority: 1, targets: 0 }],
			// "columnDefs": [
			// 	{ targets: 5, orderable: false, "sClass":"text-center"}
			// ],
			"ajax": {
				type: "post",
				url: base_url + "developer_settings/Dev_settings_pandabooks_api_logs/pandabooks_api_logs_table", // json datasource
				data: { '_reference_number': _reference_number, 'date_from': date_from, 'date_to': date_to }, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					$(".table-grid-error").remove();

					var response = $.parseJSON(data.responseText);

					if(response.data.length > 0){
						$('.btnExport').show(100);
					}
					else{
						$('#btnExport').hide(100);
					}
					// console.log(JSON.stringify(decodeURIComponent(this.data)));
					$("input#_search").val(JSON.stringify(this.data));
					$("input#_filters").val(JSON.stringify(filters));
					
				},
				error: function () {  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display", "none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table

	// start - for search purposes

	$("#_record_status").change(function () {
		$("#btnSearch").click();
	});

	$("#search_hideshow_btn").click(function (e) {
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		} else {
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function (e) {
		$(".search-input-text").val("");
		fillDatatable();
	})

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$('#btnSearch').click(function (e) {
		e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes

});




