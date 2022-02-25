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
    $('#refnum_search').on('shown.bs.select', function () {
        $('.bs-searchbox input').on('keyup', (e) => {
			var val = $(e.target).val();
            if (val.length > 4) {
                getSuggestions(val);
            }
        })
    })

    function getSuggestions(val) {
        $.ajax({				
			url: base_url+'orders/Refund_order/get_suggestions',
	       	type: "post",
			data: {'refnum':val},
			success : function(data){
				json_data = JSON.parse(data);
				// console.log(json_data);
				$('#refnum_search').empty();
				var opt = '';
				$.each(json_data, (k, v) => {
					opt += `<option value="${v}">${v}</option>`;
				})
				$('#refnum_search').append(opt);
				$('#refnum_search').selectpicker('refresh');
				// $.LoadingOverlay("hide");
			},
			error: function(error){
                // $.LoadingOverlay("hide");
                if (error.status == 403) {
                	//showToast('note', 'Security token has been expired. This page is being reloaded.');
                	showCpToast("info", "Note!", 'Security token has been expired. This page is being reloaded.');
                    setTimeout(function(){
                        window.location.href = window.location.href;
                    }, 1000)
                } else if (error.status == 404) {
                	//showToast('note', 'Something went wrong. Please contact the system administrator.');
                	showCpToast("info", "Note!", 'Something went wrong. Please contact the system administrator.');
                }
            }
		});
    }

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
				{ targets: [5, 6, 8, 9], orderable: false, sClass: "text-center" },
				{ targets: [3], sClass: "text-right" },
				{ targets: [1, 7], visible: (filter.status != 0) },
				{ responsivePriority: 1, targets: 10 },
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
	
	var approve_record_data = [];
	$('#table-grid tbody').on('click', 'button.is-action', (e) => {
		var id = e.currentTarget.getAttribute('data-id');
		var remarks = $(`#table-grid tbody .review_remarks[data-id=${id}]`).val();
		if (remarks == '') {
			var message = '<p>The Review Remarks field is required.</p>';
			//showToast('note', message);
			showCpToast("info", "Note!", message);
		} else {
			if ($(e.currentTarget).hasClass('btnApprove')) {
				$('#approve_mdl_btn').click();
			} else {
				$('#reject_mdl_btn').click();
			}
		}
		var form = {
			'refnum': id,
			'review_remarks': remarks,
			'status': ($(e.currentTarget).hasClass('btnApprove')) ? '1':'2',
		};
		var url = `${base_url}orders/Refund_order/approveOrderRefund/${token}`;
		approve_record_data = {
			'form': form,
			'url' : url
		};
	})
	
	$('#approve_modal_confirm_btn').click( () => {
		approve_record(approve_record_data.form, approve_record_data.url);
	})
	
	$('#reject_modal_confirm_btn').click( () => {
		approve_record(approve_record_data.form, approve_record_data.url);
	})

	function approve_record(form, url) {
		$('.modal').modal('hide');
		$.ajax({
			url: url,
	       	type: 'post',
			data: form,
			success : function(data){
				$.LoadingOverlay("hide");
				var resp = JSON.parse(data);
				if (resp.success) {
					//showToast('success', resp.message);
					showCpToast("success", "Success!", resp.message);

					$("#btnSearch").click();
				} else {
					$.each(resp.data, (k ,v) => {
						if (v[0]) {
							$(`#${k}`).addClass('is-invalid')
							$(`div.${k}.invalid-feedback`).html(v[1])
						} else {
							$(`input#${k}`).removeClass('is-invalid')
							$(`div.${k}.invalid-feedback`).empty()
						}
					})
					//showToast('note', resp.message);
					showCpToast("info", "Note!", resp.message);
				}
			},
			error: function(error){
				$.LoadingOverlay("hide");
                if (error.status == 403) {
                	//showToast('note', 'Security token has been expired. This page is being reloaded.');
                	showCpToast("info", "Note!", 'Security token has been expired. This page is being reloaded.');
                    setTimeout(function(){
                        window.location.href = window.location.href;
                    }, 1000)
                } else if (error.status == 404) {
                	//showToast('note', 'Something went wrong. Please contact the system administrator.');
                	showCpToast("info", "Note!", 'Something went wrong. Please contact the system administrator.');
                }
            }
		});
	}

	function showToast(type, message)
	{
		if (type == "success") {
			$.toast({
				heading: 'Success',
				text: message,
				icon: 'success',
				loader: false,  
				stack: false,
				position: 'top-center', 
				bgColor: '#5cb85c',
				textColor: 'white',
				allowToastClose: false,
				hideAfter: 10000
			});
		}
		else if (type == "note") {
			$.toast({
				heading: 'Note',
				text: message,
				icon: 'info',
				loader: false,   
				stack: false,
				position: 'top-center',  
				bgColor: '#FFA500',
				textColor: 'white'        
			});
		}
	}
	
});