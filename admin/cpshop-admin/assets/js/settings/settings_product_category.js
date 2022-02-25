$(function () {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    // start - for loading a table
    function fillDatatable() {
        var _record_status = $("select[name='_record_status']").val();
        var _category = $("input[name='_category']").val();
        var _name = $("input[name='_name']").val();
        var _onmenu = $("select[name='_onmenu']").val();
        var _priority = $("input[name='_priority']").val();

        var dataTable = $('#table-grid').DataTable({
            "processing": false,
            destroy: true,
            "serverSide": true,
            searching: false,
            responsive: true,
            "columnDefs": [
                { targets: 5, orderable: false, "sClass": "text-center" },
                { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
            ],
            "ajax": {
                type: "post",
                url: base_url + "Main_settings/product_category_list", // json datasource
                data: { '_record_status': _record_status, '_category': _category, '_name': _name, '_onmenu': _onmenu, '_priority': _priority }, // serialized dont work, idkw
                beforeSend: function (data) {
                    $.LoadingOverlay("show");
                },
                complete: function (res) {
                    var filter = { '_record_status': _record_status, '_category': _category, '_name': _name, '_onmenu': _onmenu, '_priority': _priority };
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

    let disable_id;
    let record_status;
    $('#table-grid').delegate(".action_disable", "click", function () {
        disable_id = $(this).data('value');
        record_status = $(this).data('record_status');

        if (record_status == 1) {
            $(".mtext_record_status").text("disable");
        } else if (record_status == 2) {
            $(".mtext_record_status").text("enable");
        }
    });

    let delete_id;
    $('#table-grid').delegate(".action_delete", "click", function () {
        delete_id = $(this).data('value');
    });

    $("#delete_modal_confirm_btn").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/product_category_delete_modal_confirm',
            data: { 'delete_id': delete_id },
            success: function (data) {
                var res = data.result;
                if (data.success == 1) {
                    fillDatatable(); //refresh datatable

                    showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    // $.toast({
                    //     heading: 'Success',
                    //     text: data.message,
                    //     icon: 'success',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#5cb85c',
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $('#delete_modal').modal('toggle'); //close modal
                } else {
                    showCpToast("info", "Note!", data.message);
                    // $.toast({
                    //     heading: 'Note',
                    //     text: data.message,
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    });

    $("#disable_modal_confirm_btn").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/product_category_disable_modal_confirm',
            data: { 'disable_id': disable_id, 'record_status': record_status },
            success: function (data) {
                var res = data.result;
                if (data.success == 1) {
                    fillDatatable(); //refresh datatable

                    showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    // $.toast({
                    //     heading: 'Success',
                    //     text: data.message,
                    //     icon: 'success',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#5cb85c',
                    //     textColor: 'white',
                    //     allowToastClose: false,
                    //     hideAfter: 10000
                    // });
                    $('#disable_modal').modal('toggle'); //close modal
                } else {
                    showCpToast("info", "Note!", data.message);
                    // $.toast({
                    //     heading: 'Note',
                    //     text: data.message,
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    });

    // start - edit function
	let id;
	let prev_val = {};

    $('#table-grid').delegate(".action_edit", "click", function () {
        edit_id = $(this).data('value');

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_product_category_data',
            data: { 'edit_id': edit_id },
            success: function (data) {
                var result = data.result;
                if (data.success == 1) {
                    $("#edit_id").val(result['id']);
                    $("#edit_category").val(result['category_code']);
                    $("#edit_name").val(result['category_name']);
                    $("#edit_onmenu").val(result['on_menu']);
                    $("#edit_priority").val(result['priority']);

                    prev_val = {
                        'category_code':result['category_code'],
                        'category_name':result['category_name'],
                        'on_menu':result['on_menu'],
                        'priority':result['priority']
                    }
                    
                } else {
                    showCpToast("info", "Note!", data.message);
                    // $.toast({
                    //     heading: 'Note',
                    //     text: data.message,
                    //     icon: 'info',
                    //     loader: false,
                    //     stack: false,
                    //     position: 'top-center',
                    //     bgColor: '#FFA500',
                    //     textColor: 'white'
                    // });
                }
            }
        });
    });

    $("#update_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var _id = $('#edit_id').val();
        var _category = $('#edit_category').val();
        var _name = $('#edit_name').val();
        var _onmenu = $('#edit_onmenu').val();
        var _priority = $('#edit_priority').val();


        if (_id != '' && _category != '' && _name != '' && _onmenu != '' && _priority != '') {
            if (_priority < 10) {
                $.ajax({
                    type: 'post',
                    url: base_url + 'Main_settings/product_category_update_modal_confirm',
                    data: { 'id': _id, 'category': _category, 'name': _name, 'onmenu': _onmenu, 'priority': _priority, 'prev_val':prev_val },
                    success: function (data) {
                        var res = data.result;
                        if (data.success == 1) {
                            fillDatatable(); //refresh datatable

                            // $.toast({
                            //     heading: 'Success',
                            //     text: data.message,
                            //     icon: 'success',
                            //     loader: false,
                            //     stack: false,
                            //     position: 'top-center',
                            //     bgColor: '#5cb85c',
                            //     textColor: 'white',
                            //     allowToastClose: false,
                            //     hideAfter: 10000
                            // });
                            showCpToast("success", "Success!", data.message);
                            setTimeout(function(){location.reload()}, 2000);


                            $('#edit_id').val('');
                            $('#edit_category').val('');
                            $('#edit_name').val('');
                            $('#edit_onmenu').val('');
                            $('#edit_priority').val('');

                            $('#edit_modal').modal('toggle'); //close modal
                        } else {
                            showCpToast("info", "Note!", data.message);
                            // $.toast({
                            //     heading: 'Note',
                            //     text: data.message,
                            //     icon: 'info',
                            //     loader: false,
                            //     stack: false,
                            //     position: 'top-center',
                            //     bgColor: '#FFA500',
                            //     textColor: 'white'
                            // });
                        }
                    }
                });
            } else {
                showCpToast("info", "Note!", 'Priority must be less than or equal to 10');
                // $.toast({
                //     heading: 'Note',
                //     text: 'Priority must be less than or equal to 10',
                //     icon: 'info',
                //     loader: false,
                //     stack: false,
                //     position: 'top-center',
                //     bgColor: '#FFA500',
                //     textColor: 'white'
                // });
            }
        } else {
            showCpToast("info", "Note!", 'Please fill up all required fields');
            // $.toast({
            //     heading: 'Note',
            //     text: 'Please fill up all required fields',
            //     icon: 'info',
            //     loader: false,
            //     stack: false,
            //     position: 'top-center',
            //     bgColor: '#FFA500',
            //     textColor: 'white'
            // });
        }
    });

    $("#add_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var _category = $('#add_category').val();
        var _name = $('#add_name').val();
        var _onmenu = $('#add_onmenu').val();
        var _priority = $('#add_priority').val();

        if (_category != '' && _name != '' && _onmenu != '' && _priority != '') {
            if (_priority < 10) {
                $.ajax({
                    type: 'post',
                    url: base_url + 'Main_settings/product_category_add_modal_confirm',
                    data: { 'category': _category, 'name': _name, 'onmenu': _onmenu, 'priority': _priority },
                    success: function (data) {
                        var res = data.result;
                        if (data.success == 1) {
                            fillDatatable(); //refresh datatable

                            showCpToast("success", "Success!", data.message);
                            setTimeout(function(){location.reload()}, 2000);
                            // $.toast({
                            //     heading: 'Success',
                            //     text: data.message,
                            //     icon: 'success',
                            //     loader: false,
                            //     stack: false,
                            //     position: 'top-center',
                            //     bgColor: '#5cb85c',
                            //     textColor: 'white',
                            //     allowToastClose: false,
                            //     hideAfter: 10000
                            // });

                            $('#add_category').val('');
                            $('#add_name').val('');
                            $('#add_onmenu').val('');
                            $('#add_priority').val('');

                            $('#add_modal').modal('toggle'); //close modal
                        } else {
                            showCpToast("info", "Note!", data.message);
                            // $.toast({
                            //     heading: 'Note',
                            //     text: data.message,
                            //     icon: 'info',
                            //     loader: false,
                            //     stack: false,
                            //     position: 'top-center',
                            //     bgColor: '#FFA500',
                            //     textColor: 'white'
                            // });
                        }
                    }
                });
            } else {
                showCpToast("info", "Note!", 'Priority must be less than or equal to 10');
                // $.toast({
                //     heading: 'Note',
                //     text: 'Priority must be less than or equal to 10',
                //     icon: 'info',
                //     loader: false,
                //     stack: false,
                //     position: 'top-center',
                //     bgColor: '#FFA500',
                //     textColor: 'white'
                // });
            }
        } else {
            showCpToast("info", "Note!", 'Please fill up all required fields');
            // $.toast({
            //     heading: 'Note',
            //     text: 'Please fill up all required fields',
            //     icon: 'info',
            //     loader: false,
            //     stack: false,
            //     position: 'top-center',
            //     bgColor: '#FFA500',
            //     textColor: 'white'
            // });
        }
    });

    $(".allownumber").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $(".allowdecimal").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});




