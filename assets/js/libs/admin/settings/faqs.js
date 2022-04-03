
$(function () {
    var old_order = [];
    var selectedid = '';
    var targetstatus = 0;
    var new_order = [];
    $("#tblfaqs").sortable({
        items: 'td.span.fa-reorder',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
        }
    });
    $("#btnsaveArrangement").click(function(){
		$.ajax({
			type:'post',
			url:base_url+'admin/settings/Website_info/save_arrangement',
			data:{'arrangement':new_order},
			success:function(data){
                $("#btnsaveArrangement").css('display','none');
                var result = JSON.parse(data);
                sys_toast_success(result.message);
            }
        });
    });
    var dataTable='';
	fillDatatable();
    
    $("#btnSaveFAQ").click(function(){
        var form = $("form[name=form_faq]");
        var form_data = new FormData(form[0]);
        form_data.append('selectedid',selectedid);
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/settings/website_info/save_faq',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    sys_toast_success(json_data.message);
                    $("#f_title").val();
                    $("#f_content").val();
                    selectedid='';
                    $("#add_modal").modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    setTimeout(function(){location.reload()}, 2000);
                    //window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                }else{
                    //sys_toast_warning(json_data.message);
                    sys_toast_warning(json_data.message[0]);
                }
            },
            error: function(error){
                sys_toast_warning(json_data.message);
                //showCpToast("warning", "Warning!", json_data.message);
            }
        });
    });

    
    $("#disable_enable_modal_confirm_btn").click(function(){
        var form = $("form[name=form_faq]");
        var form_data = new FormData(form[0]);
        form_data.append('selectedid',selectedid);
        form_data.append('status',targetstatus);
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/settings/website_info/enable_disable_faq',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    sys_toast_success(json_data.message);
                    $("#f_title").val();
                    $("#f_content").val();
                    selectedid='';
                    $("#enable_disable_modal").modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    
          			setTimeout(function(){location.reload()}, 2000);
                    //window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                }else{
                    //sys_toast_warning(json_data.message);
                    sys_toast_warning(json_data.message[0]);
                }
            },
            error: function(error){
                sys_toast_warning(json_data.message);
                //showCpToast("warning", "Warning!", json_data.message);
            }
        });
    });

    $("#delete_modal_confirm_btn").click(function(){
        var form = $("form[name=form_faq]");
        var form_data = new FormData(form[0]);
        form_data.append('selectedid',selectedid);
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/settings/website_info/delete_faq',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    sys_toast_success(json_data.message);
                    $("#delete_modal").modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $("#f_title").val();
                    $("#f_content").val();
                    selectedid='';
          			setTimeout(function(){location.reload()}, 2000);
                    //window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                }else{
                    //sys_toast_warning(json_data.message);
                    sys_toast_warning(json_data.message[0]);
                }
            },
            error: function(error){
                sys_toast_warning(json_data.message);
                //showCpToast("warning", "Warning!", json_data.message);
            }
        });
    });

    $('#add_modal').on('show.bs.modal', function (e) {
        $("#f_title").val();
        $("#f_content").val();
        if(selectedid!=''){
            $.ajax({
                type: 'post',
                url: base_url+'admin/settings/website_info/get_faq',
                data: {'id':selectedid},
                success:function(data){
                    $("#add_update_title").html("Update FAQ");
                    $("#f_title").val(JSON.parse(data).title);
                    $("#f_content").val(JSON.parse(data).content);
                }
            });
        }else{
            
            $("#add_update_title").html("Add FAQ");
        }
    });
    $("#btnSearch,#btnRefresh").click(function(){
        fillDatatable();
    });
    $('#enable_disable_modal').on('show.bs.modal', function (e) {
        if(selectedid!=''){
            $.ajax({
                type: 'post',
                url: base_url+'admin/settings/website_info/get_faq',
                data: {'id':selectedid},
                success:function(data){
                    if(JSON.parse(data).status == 1){
                        targetstatus = 2;
                        $(".mtext_record_status").html('Disable');
                    }else{
                        targetstatus = 1;
                        $(".mtext_record_status").html('Enable');
                    }
                }
            });
        }
    });
	function fillDatatable(){
		var status       = $("#f_status").val();
        var search       = $("#f_search").val();
		dataTable = $('#tblfaqs').DataTable({
			"processing": false,
			destroy: true,
            rowReorder: {
                selector: 'span.fa-reorder',
                update:false,
            },
			"serverSide": true,
			"searching": false,
			responsive: true,
			"language": {                
				"infoFiltered": ""
			},"columns": [
                { "width": "10px" },
                { "width": "0px" },
                { "width": "150px" },
                { "width": "20px" },
                { "width": "20px" },
                { "width": "20px" }
        ],
			"columnDefs": [
				{ targets: 6, rowReorder: true, "sClass":"text-center"},
				{ responsivePriority: 1, targets: 5 },
				{ visible: false, targets: [1,4] },
				{ width: 10, targets: 0 }
			],
			"ajax":{
				type: "post",
				url:base_url+"admin/settings/Website_info/load_faqs", // json datasource
				data: {
                        '_search':search,
                        '_status':status
					}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);
                   // old_order = $("#tblfaqs tbody").html().replace("odd",'').replace("even",'').replace('style=""','').replace('class="" ','class=""');
					if(response.data.length > 0){
						$('.btnExport').show(100);
					}
					else{
						$('#btnExport').hide(100);
					}
					// console.log(JSON.stringify(decodeURIComponent(this.data)));
					$("#_search").val(JSON.stringify(this.data));
					//$("input#_record_status").val(_record_status);
					$("input#search").val(search);
					$(".table-grid-error").remove();
                    var data = $('#tblfaqs').DataTable().rows().data();
                    old_order=[];
                    data.each(function (value, index) {
                        old_order.push(value[1]);
                    });

                    $(".update_faq,.action_disable,.action_delete").click(function(){
                        selectedid=$(this).data('value');
                    });
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		},);
        dataTable.on( 'row-reorder', function ( e, diff, edit ) {
            var result = 'Reorder started on row: '+edit.triggerRow.data()[1]+'<br>';
            new_order = [];
            for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
                var rowData = dataTable.row( diff[i].node ).data();
                new_order.push({'value':rowData[1],'order':diff[i].newPosition});
                result += rowData[1]+' updated to be in position '+
                    diff[i].newData+' (was '+diff[i].oldData+')<br>';
               
            }
            if(JSON.stringify(old_order)!=JSON.stringify(new_order) && new_order.length!=0){
                $("#btnsaveArrangement").css('display','block');
            }
            console.log
        } );
	}
});