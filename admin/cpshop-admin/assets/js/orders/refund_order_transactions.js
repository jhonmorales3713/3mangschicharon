$(function () {
	var base_url = $("body").data("base_url"); //base_url came from built-in CI function base_url();
    var token = $("#token").val();
    var filter = {
        refnum: '',
        status: $('#refstatus').val(),
        fromdate: $('#date_from').val(),
        todate: $('#date_to').val(),
    };
    var initial_datefrom = $('#date_from').val();
    var initial_dateto = $('#date_to').val();
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
		$(".search-input-text").val("");
		$("#address").val("");
		$("#drno").val("");
		$("#select_status").prop("selectedIndex", 1);
		// fillDatatable();
	});

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$("#btnSearch").click(function (e) {
        e.preventDefault();
        filter.fromdate = $('#date_from').val();
        filter.todate = $('#date_to').val();
        filter.status = $('#refstatus').val(),
        filter.refnum = $('#refnum_search').val();
		getOrderRefunds();
	});
    // end - for search purposes
    getOrderRefunds();

	var datatable;
	function getOrderRefunds(){
        datatable = $('#table-grid').DataTable( {
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "searching": false,
            "destroy": true,
            "order":[[0, 'asc']],
            columnDefs: [
				{ targets: [5, 7, 8, 9], orderable: false, sClass: "text-center" },
				{ targets: [3], sClass: "text-right" },
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: -1 },
			],
            "ajax":{
            url: base_url+'orders/Refund_order/refund_orders_approval',
            type: 'post',
            data: filter,
            beforeSend: function(){
                $.LoadingOverlay("show");
            },
            complete: function(data){
                $.LoadingOverlay("hide");
                var response = data.responseJSON;
                if(response.success == 1){
                    $('.btnExport').show(100);
                } else {
                    $('.btnExport').hide(100);
				}
				$('#_search').val(JSON.stringify(this.data));
				$('#_filter').val(JSON.stringify(filter));
            },
            error: function(){
                $.LoadingOverlay("hide");
            }
            }
        } );
	}
});