$(function () {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    // start - for loading a table
    function fillDatatable() {
        var _record_status = $("select[name='_record_status']").val();
        var _name = $("input[name='_name']").val();
        var _shops = $("#_shops").val();
        console.log(_shops);
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
                url: base_url + "settings/Faqs/faqs_table", // json datasource
                data: { '_record_status': _record_status, '_name': _name, '_shops': _shops }, // serialized dont work, idkw
                beforeSend: function (data) {
                    $.LoadingOverlay("show");
                },
                complete: function (res) {
                    var filter = { '_record_status': _record_status, '_name': _name, '_shops': _shops };
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
            return false;
        }
    });

    $('#btnSearch').click(function (e) {
        e.preventDefault();
        fillDatatable();
    });
    // end - for search purposes

    let delete_id;
    let delete_name;
    $('#table-grid').delegate(".action_delete", "click", function () {
        delete_id = $(this).data('value');
        delete_name = $(this).data('name');
    });

    $("#delete_modal_confirm_btn").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'settings/Faqs/delete_faqs',
            data: { 'delete_id': delete_id, 'delete_name': delete_name },
            success: function (data) {
                var res = data.result;
                if (data.success == 1) {
                    fillDatatable(); //refresh datatable

                    //sys_toast_success(data.message);
                    showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    $('#delete_modal').modal('toggle'); //close modal
                } else {
                    //sys_toast_warning(data.message);
                    showCpToast("warning", "Warning!", data.message);
                }
            }
        });
    });

    let disable_id;
    let record_status;
    let disable_name;
    $('#table-grid').delegate(".action_disable", "click", function () {
        disable_id    = $(this).data('value');
        record_status = $(this).data('record_status');
        disable_name  = $(this).data('name');

        if (record_status == 1) {
            $(".mtext_record_status").text("disable");
        } else if (record_status == 2) {
            $(".mtext_record_status").text("enable");
        }
    });

    $("#disable_modal_confirm_btn").click(function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'settings/Faqs/disable_faqs',
            data: { 'disable_id': disable_id, 'record_status': record_status, 'disable_name': disable_name },
            success: function (data) {
                var res = data.result;
                if (data.success == 1) {
                    fillDatatable(); //refresh datatable

                    //sys_toast_success(data.message);
                    showCpToast("success", "Success!", data.message);
                    setTimeout(function(){location.reload()}, 2000);
                    $('#disable_modal').modal('toggle'); //close modal
                } else {
                    //sys_toast_warning(data.message);
                    showCpToast("warning", "Warning!", data.message);

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
            url: base_url + 'settings/Faqs/get_faqs_data',
            data: { 'edit_id': edit_id },
            success: function (data) {
                var result = data.result;
                if (data.success == 1) {
                    $("#edit_id").val(result['id']);

                    $("#edit_faqs_for").val(result['faqs_for']);
                    $("#edit_faqs_arrangement").val(result['faqs_arrangement']);
                    $("#edit_faqs_title").val(result['title_field']);
                    CKEDITOR.instances['edit_faqs_content'].setData(result['content_field']);
                    // $("#edit_faqs_content").val();

                    prev_val = {
                        'faqs_arrangement':result['faqs_arrangement'],
                        'title_field':result['title_field'],
                        'edit_faqs_content':result['edit_faqs_content'],
                        'faqs_for':result['faqs_for']
                    }
                    
                } else {
                    //sys_toast_warning(data.message);
                    showCpToast("warning", "Warning!", data.message);

                }
            }
        });
    });

    $("#update_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var edit_faqs_for = $('#edit_faqs_for').val();
        var _id = $('#edit_id').val();
        var edit_faqs_arrangement = $('#edit_faqs_arrangement').val();
        var edit_faqs_title = $('#edit_faqs_title').val();
        var edit_faqs_content = CKEDITOR.instances['edit_faqs_content'].getData();



        if (_id != '' && edit_faqs_arrangement != '' && edit_faqs_title != '' &&  edit_faqs_content != '' && edit_faqs_for !== null) {
            $.ajax({
                type: 'post',
                url: base_url + 'settings/Faqs/update_faqs',
                data: { 'id': _id, 'edit_faqs_arrangement': edit_faqs_arrangement, 'edit_faqs_title': edit_faqs_title, 'edit_faqs_content': edit_faqs_content, 'prev_val':prev_val, 'edit_faqs_for':edit_faqs_for },
                success: function (data) {
                    var res = data.result;
                    if (data.success == 1) {
                        fillDatatable(); //refresh datatable

                        //sys_toast_success(data.message);
                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);

                        $('#edit_id').val('');
                        $('#edit_faqs_title').val('');
                        $('#edit_faqs_arrangement').val('');
                        $('#edit_faqs_content').val('');

                        $('#edit_modal').modal('toggle'); //close modal
                    } else {
                       //sys_toast_warning(data.message);
                       showCpToast("warning", "Warning!", data.message);

                    }
                }
            });
        } else {
            sys_toast_warning('Please fill up all required fields');
        }
    });

    $("#add_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var faqs_for = $('#faqs_for').val();
        var faqs_arrangement = $('#faqs_arrangement').val();
        var faqs_title = $('#faqs_title').val();
        var faqs_content = CKEDITOR.instances['faqs_content'].getData();

        if (faqs_arrangement != '' && faqs_title != '' && faqs_content != '' && faqs_for !== null) {
            $.ajax({
                type: 'post',
                url: base_url + 'settings/Faqs/add_faqs',
                data: { 'faqs_arrangement': faqs_arrangement, 'faqs_title': faqs_title, 'faqs_content': faqs_content, 'faqs_for' : faqs_for},
                success: function (data) {
                    var res = data.result;
                    if (data.success == 1) {
                        fillDatatable(); //refresh datatable

                        //sys_toast_success(data.message);
                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);
                        $('#add_role_name').val('');

                        $('#add_modal').modal('toggle'); //close modal
                    } else {
                       //sys_toast_warning(data.message);
                       showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
        } else {
            //sys_toast_warning('Please fill up all required fields');
            showCpToast("warning", "Warning!", 'Please fill up all required fields');
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
CKEDITOR.replace( 'faqs_content' );
CKEDITOR.replace( 'edit_faqs_content' );




