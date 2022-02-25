$(function () {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    // start - for loading a table
    function fillDatatable() {
        var _name = $("input[name='_name']").val();
        var _start_date = $("input[name='start_date']").val();
        var _end_date = $("input[name='end_date']").val();

        var dataTable = $('#table-grid').DataTable({
            "processing": false,
            destroy: true,
            "serverSide": true,
            searching: false,
            responsive: true,
            "columnDefs": [
                { targets: 3, orderable: false, "sClass": "text-center" },
                //{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
            ],
            "ajax": {
                type: "post",
                url: base_url + "promotion/Main_promotion/discount_promo_list", // json datasource
                data: { '_name': _name, '_start_date': _start_date, '_end_date': _end_date }, // serialized dont work, idkw
                beforeSend: function (data) {
                    $.LoadingOverlay("show");
                },
                complete: function (res) {
                    var filter = { '_name': _name, '_start_date': _start_date, '_end_date': _end_date };
					$.LoadingOverlay("hide"); 
					$('#_search').val(JSON.stringify(this.data));
					$('#_filter').val(JSON.stringify(filter));
					if (res.responseJSON.data.length > 0) {
						$('#btnExport').show();
					}else{
						$('#btnExport').hide();
					}
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

