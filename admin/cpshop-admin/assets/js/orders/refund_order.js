var table_data = [], refund_tbl = [];
$(function () {
	var base_url = $("body").data("base_url"); //base_url came from built-in CI function base_url();
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
		$.LoadingOverlay("show");
		getOrderDetail();
	});
	// end - for search purposes

	$(document).ready(function () {
		// $.LoadingOverlay("show");
		if (task == 'View') {
			$('#refund_form *[data-createdetails]').prop('disabled',true);
			getOrderDetail();
		} else if (task == 'Edit') {
			getOrderDetail();
		}
	});

	function getOrderDetail() {
		var val = (task == "Create") ? $('#refnum_search').selectpicker('val'):$('#summary_id')[0].value;
		$.ajax({
			url: base_url+'orders/Refund_order/getOrderDetail',
	       	type: "post",
			data: {'type':task.toLowerCase(),'refnum':val},
			success : function(data){
				$.LoadingOverlay("hide");
				$('#ref-info-container').hide();
				if (data !== '') {
					json_data = JSON.parse(data);
					if (json_data.success == 1) {
						$('#ref-info-container').show();
		
						table_data = json_data.details;
						refund_tbl = table_data;
						$('#refund_tbl').val(JSON.stringify(refund_tbl))
						$('#refnum').val(val)
						
						var summary = json_data[0];
						$('#summary_tbl').val(JSON.stringify(summary));
						$.each(summary, function (k, v) {
							$(`#card-table *[data-${k}]`).text(v);
						})
						if (task == 'Create') {
							$('#ref_amt').val(summary.total_amount);
						} else {
							if (task == 'View') {
								$('#chkAll').remove();
							}
							var view_summary = json_data.summary;
							if (view_summary.status > 0) {
								$(`.record_status_${view_summary.status}`).show();
								$('#refund_form *[data-viewdetails]').prop('disabled', true);
								$('#viewdetails-actions').remove();
							}
							$.each(view_summary, function (k, v) {
								$(`#refund_form *[data-${k}]`).val(v);
							})
						}
						// var summary_tbl = ;
						// console.log(summary_tbl);
						fillDatatable(json_data.table);
					} else {
						//showToast('note', json_data.message);
						showCpToast("info", "Note!", json_data.message);
					}
				}
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

	function fillDatatable(dataSet) {
		$('#table-grid').DataTable( {
			searching: false,
			paging:false,
			ordering: false,
			responsive: true,
			destroy: true,
			data: dataSet,
		} );
		if (task !== 'View') addval();
	}

	$('#ref_mode').change( (e) => {
		if (e.target.value == 'cash') {
			$('*[data-acc_num_str]').text('Address');
		} else {
			$('*[data-acc_num_str]').text('Account Number');
		}
	})

	$('#chkAll').change((e) => {
		$('.tbl-items-chkbx').prop('checked', $(e.target).is(':checked'));
		addval();
	})

	$("#addBtn").click(function () {
		window.location.assign(base_url + "Main_products/add_products/" + token);
	});

	var form;
	$('#refund_form').submit(function(e){
		e.preventDefault();
		$.LoadingOverlay("show");

		//*START* check if all items are refunded > void if all//
		var array_all=[];
		var array_selected=[];
		var i=0;
		$('#table-grid tr').each(function(row, tr){
			if(i>0){
				 var name=$(this).find("td").eq(1).html(); 
				 var max=$(this).find("td:eq(5) input[type='number']").attr('max');
				 var qty=$(this).find("td:eq(5) input[type='number']").val();
				 array_all[row]={
				 	'name':name,
				 	'qty':max
				 }

				 if($(this).find("td:eq(0) input[type='checkbox']").is(':checked')){
				 	array_selected[row]={
				 		'name':name,
				 		'qty':qty
				 	}
				 }

			}
			i++;
		});

		if(JSON.stringify(array_all)==JSON.stringify(array_selected)){
			$(this).append('<input type="hidden" name="actionRefund" id="actionRefund" value="void">');
		}else{
			$(this).append('<input type="hidden" name="actionRefund" id="actionRefund" value="refund">');
		}
		//*END* check if all items are refunded > void if all//

		form = $(this);
		if (task == 'Create') {
			add_record(form);
		} else if(task == 'View') {
			if ($('#review_remarks').val() == '') {
				var message = '<p>The Review Remarks field is required.</p>';
				showToast('note', message);
			} else {
				$('#approve_mdl_btn').click();
			}
		} else if(task == 'Edit') {
			add_record(form);
		}

		$.LoadingOverlay("hide");
	});

	$('#btnReject').click( (e) => {
		form = $('#refund_form');
		if ($('#review_remarks').val() == '') {
			var message = '<p>The Review Remarks field is required.</p>';
			showToast('note', message);
		} else {
			$('#reject_mdl_btn').click();
		}
	});

	$('#approve_modal_confirm_btn').click( () => {
		approve_record(form, '1');
	})
	
	$('#reject_modal_confirm_btn').click( () => {
		approve_record(form, '2');
	})

	function add_record(form) {
		var post_data = new FormData(form[0]);
		$.ajax({
			url: form.attr("action"),
	       	type: form.attr("method"),
			data: post_data,
			contentType: false,
			cache: false,
			processData:false,
			success : function(data){
				$.LoadingOverlay("hide");
				var resp = JSON.parse(data);
				if (resp.success) {
					showToast('success', resp.message);
					location.reload();
					// setTimeout(function () {
					// }, 1200);
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

	function approve_record(form, status) {
		var post_data = new FormData(form[0]);
		post_data.append('status', status);
		$.ajax({
			url: form.attr("action"),
	       	type: form.attr("method"),
			data: post_data,
			contentType: false,
			cache: false,
			processData:false,
			beforeSend: function () {
				$.LoadingOverlay("show");
			},
			success : function(data){
				$.LoadingOverlay("hide");
				var resp = JSON.parse(data);
				if (resp.success) {
					//showToast('success', resp.message);
					showCpToast("success", "Success!", resp.message);
					window.location.assign(base_url+"Main_orders/refund_approval/"+token);
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

function addval() {
	refund_tbl = [];
	var total_amt = 0;
	var checkboxes = $('.tbl-items-chkbx');
	$.each(checkboxes, (k, el) => {
		var id = el.id;
		var qty = $(`#quantity-${id}`).val();
		var key = $(el).attr('data-key');
		var price = table_data[key]['itemprice'];
		var amt = qty * price;
		if ($(el).is(':checked')) {
			total_amt += amt;
		}
		refund_tbl = [...refund_tbl, {
			'id':table_data[key]['id'],
			'is_checked':$(el).is(':checked'),
			'order_log_id':table_data[key]['order_log_id'],
			'sys_shop':table_data[key]['sys_shop'],
			'shopname':table_data[key]['shopname'],
			'branchid':table_data[key]['branchid'],
			'branchname':table_data[key]['branchname'],
			'quantity':qty,
			'maxqty':table_data[key]['maxqty'],
			'product_id':table_data[key]['product_id'],
			'itemname':table_data[key]['itemname'],
			'itemprice':table_data[key]['itemprice'],
			'amount':amt,
		}];
	})
	$('#refund_tbl').val(JSON.stringify(refund_tbl))
	$('#ref_amt').val(total_amt.toFixed(2));
}
