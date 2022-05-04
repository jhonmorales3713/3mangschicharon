
$(function(){

	var base_url = $(".body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var ini      = $(".body").data('ini');
    fillDatatable();

    
    $(document).on('click', '.action_disable', function(){
        id = $(this).data('value');
        date_from = $(this).data('date_from');
        date_to = $(this).data('date_to');
        $(".modal-btnsave").data("id",id);
        $(".modal-btnsave").data("status",2);
        $(".modal-btnsave").data("date_from",date_from);
        $(".modal-btnsave").data("date_to",date_to);
        $(".mtext_record_status").html("Disable");
        $("#disable_modal").modal('show');
    });
    $(document).on('click', '#btnSearch', function(){
        fillDatatable();
    });

    
    $(document).on('click', '.action_enable', function(){
        id = $(this).data('value');
        date_from = $(this).data('date_from');
        date_to = $(this).data('date_to');
        $(".modal-btnsave").data("id",id);
        $(".modal-btnsave").data("status",1);
        $(".modal-btnsave").data("date_from",date_from);
        $(".modal-btnsave").data("date_to",date_to);
        $(".mtext_record_status").html("Enable");
        $("#disable_modal").modal('show');
    });
    $(document).on('click', '.action_delete', function(){
        id = $(this).data('value');
        date_from = $(this).data('date_from');
        date_to = $(this).data('date_to');
        $(".modal-btnsave").data("id",id);
        $(".modal-btnsave").data("status",0);
        $(".modal-btnsave").data("date_from",date_from);
        $(".modal-btnsave").data("date_to",date_to);
        $("#delete_modal").modal('show');
    });

    $(document).on('click', '.modal-btnsave', function(){
        id = $(this).data('id');
        status_ = $(this).data('status');
        date_from = $(this).data('date_from');
        date_to = $(this).data('date_to');
		$.ajax({
			type:'post',
			url:base_url+'admin/Main_promotions/change_status',
			data:{'id':id,'status':status_,'date_from':date_from,'date_to':date_to},
			success:function(data){
                var res = data.result;
                
                $("#delete_modal").modal('hide');
                $("#disable_modal").modal('hide');
                if (data.success == 1){
                    sys_toast_success(data.message);
                    fillDatatable();
                }else{
                    var json_data = JSON.parse(data);
                    sys_toast_warning(json_data.message);
				}
			}
		});
    });
    $("#search_clear_btn").click(function(){
        $("input#_record_status").val('');
        // $("input#_shops").val(_shops);
        $("#date_from").val('');
        $("#date_to").val('');
        $("#_record_statusM").val('');
        fillDatatable();
    });
    function fillDatatable(){
		var _record_status 	= $("select[name='_record_status']").val();
		var _search 			= $("input[name='_search']").val();
		var _categories     = $("select[name='_categories']").val();
		var date_from       = $("#date_from").val();
		var date_to       = $("#date_to").val();



		var dataTable = $('#table-grid').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"searching": false,
			responsive: true,
			"language": {                
				"infoFiltered": ""
			},
			"columnDefs": [
				{ targets: 4, orderable: false, "sClass":"text-center"},
				{ responsivePriority: 1, targets: 4 },
			],createdRow: function( row, data, dataIndex ) {
            },
			"columnDefs": [
				{
					// "targets": [ 6 ],
					// "visible": false,
					// "searchable": false
				}],
			"ajax":{
				type: "post",
				url:base_url+"admin/Main_promotions/discount_table", // json datasource
				data: {'_record_status':_record_status, 
				        '_search':_search, 
						'_categories':_categories,
						'date_from':date_from,
						'date_to':date_to
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
					// $("#_search").val(JSON.stringify(this.data));
					$("input#_record_status").val(_record_status);
					// $("input#_shops").val(_shops);
					$("#date_from").val(date_from);
					$("#date_to").val(date_to);
					$(".table-grid-error").remove();
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
        });
    }
});