$(function () {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

    // function IsEmail(email) {
    //     var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    //     if(!regex.test(email)) {
    //        return false;
    //     }else{
    //        return true;
    //     }
    // }

    $(".allownumber").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    // start - for loading a table
    function fillDatatable() {
        var _record_status = $("select[name='_record_status']").val();
        var _shop = $("select[name='_shop']").val();
        var _shopbranch = $("select[name='_shopbranch']").val();
        var _name = $("input[name='_name']").val();
        var _email    = $("input[name='_email']").val();
        var _mobile = $("input[name='_mobile']").val();

        var dataTable = $('#table-grid').DataTable({
            "processing": false,
            destroy: true,
            searching: false,
            "serverSide": true,
            responsive: true,
            "columnDefs": [
                { targets: 0, orderable: false },
                { targets: 5, orderable: false, "sClass": "text-center" },
                { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
            ],
            "ajax": {
                type: "post",
                url: base_url + "Main_settings/member_list", // json datasource
                data: { '_record_status': _record_status, '_shop': _shop, '_shopbranch': _shopbranch, '_name': _name, '_mobile': _mobile, '_email':_email }, // serialized dont work, idkw
                beforeSend: function (data) {
                    $.LoadingOverlay("show");
                },
                complete: function (res) {
                var filter = { '_record_status': _record_status, '_shop': _shop, '_shopbranch': _shopbranch, '_name': _name, '_mobile': _mobile ,'_email':_email};
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
            url: base_url + 'Main_settings/member_delete_modal_confirm',
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
            url: base_url + 'Main_settings/member_disable_modal_confirm',
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

    //previous value
    let prev_val = {};
    $('#table-grid').delegate(".action_edit", "click", function () {
        edit_id = $(this).data('value');

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_member_data',
            data: { 'edit_id': edit_id },
            success: function (data) {
                var result = data.result;
                if (data.success == 1) {
                    $("#edit_shop").val(result['sys_shop']);
                    $("#edit_id").val(result['id']);
                    $("#edit_fname").val(result['fname']);
                    $("#edit_mname").val(result['mname']);
                    $("#edit_lname").val(result['lname']);
                    // $("#edit_email").val(result['email']);
                    $("#edit_mobile").val(result['mobile']);

                    prev_val = {
                        'sys_shop' : result['sys_shop'],                        
                        'fname' : result['fname'],
                        'mname' : result['mname'],
                        'lname' : result['lname'],
                        'mobile' : result['mobile'],
                        'branchid' : result['branchid']
                    }

                    edit_shopbranch(result['sys_shop'], result['branchid']);
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
        var _shop = $('#edit_shop').val();
        var _fname = $('#edit_fname').val();
        var _mname = $('#edit_mname').val();
        var _lname = $('#edit_lname').val();
        // var _email = $('#edit_email').val();
        var _mobile = $('#edit_mobile').val();
        var _shopbranch = $('#edit_shopbranch').val();

        if (_id != '' && _shop != '' && _fname != '' && _lname != '' && _mobile != '' && _shopbranch != '') {
            $.ajax({
                type: 'post',
                url: base_url + 'Main_settings/member_update_modal_confirm',
                data: { 'id': _id, 'shop': _shop, 'fname': _fname, 'mname': _mname, 'lname': _lname, 'mobile': _mobile, 'shopbranch': _shopbranch, 'prev_val':prev_val },
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

                        $('#edit_id').val('');
                        $('#edit_shop').val('');
                        $('#edit_fname').val('');
                        $('#edit_mname').val('');
                        $('#edit_lname').val('');
                        // $('#edit_email').val('');
                        $('#edit_mobile').val('');

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

    $("#action_add").click(function (e) {
        $("#add_user").empty();

        $('#add_user').append($('<option>', {
            text: '-- Select User --'
        }));

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_non_members',
            success: function (data) {
                $.each(data, function (index, value) {
                    console.log();
                    $('#add_user').append($('<option>', {
                        value: value.id,
                        text: value.username
                    }));
                });
            }
        });
    })

    $("#add_modal_confirm_btn").click(function (e) {
        e.preventDefault();

        var _shop = $('#add_shop').val();
        var _user = $('#add_user').val();
        var _fname = $('#add_fname').val();
        var _mname = $('#add_mname').val();
        var _lname = $('#add_lname').val();
        // var _email = $('#add_email').val();
        var _mobile = $('#add_mobile').val();

        var _shopbranch = $('#add_shopbranch').val();

        if (_shop != '' && _user != '' && _fname != '' && _lname != '' && _mobile != '' && _shopbranch != '') {
            $.ajax({
                type: 'post',
                url: base_url + 'Main_settings/member_add_modal_confirm',
                data: { 'shop': _shop, 'user': _user, 'fname': _fname, 'mname': _mname, 'lname': _lname, 'mobile': _mobile, 'shopbranch': _shopbranch },
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

                        $('#add_shop').val('');
                        $('#add_user').val('');
                        $('#add_fname').val('');
                        $('#add_mname').val('');
                        $('#add_lname').val('');
                        // $('#add_email').val('');
                        $('#add_mobile').val('');
                        $('#add_shopbranch').val('');

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


    $("#add_shop").change(function () {
        var shop_id = $(this).val();

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_shopbranch',
            data: { 'shop_id': shop_id },
            success: function (data) {
                // $("#add_shopbranch");
                if (data.success == 1) {
                    var result = data.result;
                    var list = "";
                    list += '<option value="" selected hidden>-- Select Shop Branch --</option>';
                    list += '<option value="0">-- Main --</option>';
                    for (var x = 0; x < result.length; x++) {
                        list += '<option value="' + result[x].id + '">' + result[x].branchname + '</option>';
                    }
                    // console.log('test');
                    $("#add_shopbranch").html(list);
                } else {
                    $("#add_shopbranch").html(list);
                }
            }
        });
    });

    $("#edit_shop").change(function () {
        var shop_id = $(this).val();

        edit_shopbranch(shop_id, "");
    });

    $("#_shop").change(function () {
        var shop_id = $(this).val();

        search_shopbranch(shop_id, "");
    });

    function search_shopbranch(shop_id, branchid) {

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_shopbranch',
            data: { 'shop_id': shop_id },
            success: function (data) {
                // $("#add_shopbranch");
                var list = "";
                list += '<option value="" selected>-- Select Shop Branch --</option>';
                list += '<option value="0">-- Main --</option>';
                if (data.success == 1) {
                    var result = data.result;


                    for (var x = 0; x < result.length; x++) {
                        list += '<option value="' + result[x].id + '">' + result[x].branchname + '</option>';
                    }

                    $("#search_shopbranch").html(list);
                } else {
                    $("#search_shopbranch").html(list);
                }
            }
        });
    }

    function edit_shopbranch(shop_id, branchid) {

        $.ajax({
            type: 'post',
            url: base_url + 'Main_settings/get_shopbranch',
            data: { 'shop_id': shop_id },
            success: function (data) {
                // $("#add_shopbranch");
                var list = "";

                if (data.success == 1) {
                    var result = data.result;

                    list += '<option value="" hidden>-- Select Shop Branch --</option>';
                    list += '<option value="0">-- Main --</option>';
                    for (var x = 0; x < result.length; x++) {
                        if (branchid != "" && branchid == result[x].id) {
                            list += '<option selected value="' + result[x].id + '">' + result[x].branchname + '</option>';
                        } else {
                            list += '<option value="' + result[x].id + '">' + result[x].branchname + '</option>';
                        }
                    }

                    $("#edit_shopbranch").html(list);
                } else {
                    list += '<option value="" selected hidden>-- Select Shop Branch --</option>';
                    list += '<option value="0">-- Main --</option>';
                    $("#edit_shopbranch").html(list);
                }
            }
        });
    }

    function imgError(image) {
        image.onerror = "";
        image.src = base_url+'assets/img/blank_avatar.png';
        return true;
    }

    $(".memberavatar").on("error", function () {
        $(".memberavatar").attr("src", base_url+"assets/img/blank_avatar.png");
    });

});




