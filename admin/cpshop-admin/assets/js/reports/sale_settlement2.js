$(function () {
	var base_url = $("body").data('base_url');
	var token = $('#token').val();
	var filter = {
	  status: 1,
	  shop: '',
	  from: '',
	  to: ''
	};
  
	var resetPaymentForm = function () {
	  $('#f_payment').val('');
	  $('#f_payment_others').val('');
	  $('#f_payment_ref_num').val('');
	  $('#f_payment_fee').val('');
	  $('#f_payment_notes').val('');
	}
  
	function get_billing_table(search) {
	  var billing = $('#table-grid').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"searching": false,
		"destroy": true,
		"autoWidth": true,
		"order": [[0, 'desc']],
		"columnDefs": [
		  { targets: [6, 7], orderable: false },
		  { responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
		],
		"ajax": {
		  url: base_url + 'reports/Sale_settlement2/get_billing_table',
		  type: 'post',
		  data: {
			searchValue: search
		  },
		  beforeSend: function () {
			$.LoadingOverlay("show");
		  },
		  complete: function (data) {
			$.LoadingOverlay("hide");
			var response = $.parseJSON(data.responseText);
			if (response.recordsTotal > 0) {
				$('.btnExport').show(100);
			}
  
			$('input#_search').val(JSON.stringify(response.filters));
		  },
		  error: function () {
			$.LoadingOverlay("hide");
		  }
		}
	  });
	};
  
	function get_billing_breakdown_table(billing_data) {
	  var billing = $('#table-item').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		searching: false,
		"destroy": true,
		"autoWidth": true,
		"order": [[0, 'desc']],
		"columnDefs": [
		  { targets: [5, 6, 7], orderable: false }
		],
		"ajax": {
		  url: base_url + 'reports/Sale_settlement2/get_billing_breakdown_table',
		  type: 'post',
		  data: billing_data,
		  beforeSend: function () {
			$.LoadingOverlay("show");
		  },
		  complete: function (data) {
			$.LoadingOverlay("hide");
		  },
		  error: function () {
			$.LoadingOverlay("hide");
		  }
		}
	  });
	};
  
	function get_billing_logs(order_id, totalamount, processfee, netamount) {
	  var billing = $('#tbl_logs').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"searching": false,
		"destroy": true,
		"autoWidth": false,
		"order": [[0, 'desc']],
		"ajax": {
		  url: base_url + 'reports/Sale_settlement2/get_billing_logs',
		  type: 'post',
		  data: {
			order_id: order_id,
			totalamount, processfee, netamount
		  },
		  beforeSend: function () {
			$.LoadingOverlay("show");
		  },
		  complete: function (data) {
			$.LoadingOverlay("hide");
		  },
		  error: function () {
			$.LoadingOverlay("hide");
		  }
		}
	  });
	};
  
	function gen_branch_billing_tbl(search, id, trandate) {
	  var table_branch = $('#table-item-branch').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"searching": false,
		"destroy": true,
		"autoWidth": true,
		"order": [[0, 'desc']],
		"ajax": {
		  url: base_url + 'reports/Sale_settlement2/get_billing_branch_tbl',
		  type: 'post',
		  data: {
			searchValue: search,
			shopid: id,
			trandate
		  },
		  beforeSend: function () {
			$.LoadingOverlay('show');
			$('.branch_billing_breakdown').show();
		  },
		  complete: function (data) {
			if (data.responseJSON.recordsTotal == 0) {
			  $('.branch_billing_breakdown').hide();
			}
			$.LoadingOverlay('hide');
		  },
		  error: function () {
			$.LoadingOverlay('hide');
		  }
		}
	  });
	};
  
	function get_billing_logs(order_id, totalamount, processfee, netamount) {
	  var billing = $('#tbl_logs').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"searching": false,
		"destroy": true,
		"autoWidth": false,
		"order": [[0, 'desc']],
		"ajax": {
		  url: base_url + 'reports/Sale_settlement2/get_billing_logs',
		  type: 'post',
		  data: {
			order_id: order_id,
			totalamount, processfee, netamount
		  },
		  beforeSend: function () {
			$.LoadingOverlay("show");
		  },
		  complete: function (data) {
			$.LoadingOverlay("hide");
		  },
		  error: function () {
			$.LoadingOverlay("hide");
		  }
		}
	  });
	};
  
	function get_billing_branch_logs(branchid, trandate, totalamount, processfee, netamount) {
	  var billing = $('#tbl_branch_logs').DataTable({
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"searching": false,
		"destroy": true,
		"autoWidth": false,
		"order": [[0, 'desc']],
		"ajax": {
		  url: base_url + 'reports/Sale_settlement2/get_billing_branch_logs',
		  type: 'post',
		  data: {
			branchid: branchid, trandate,
			totalamount, processfee, netamount
		  },
		  beforeSend: function () {
			$.LoadingOverlay("show");
		  },
		  complete: function (data) {
			$.LoadingOverlay("hide");
		  },
		  error: function () {
			$.LoadingOverlay("hide");
		  }
		}
	  });
	};
  
	get_billing_table(JSON.stringify(filter));
  
	$(document).on('click', '#btnSearch', function () {
	  filter.status = $('#select_status').val() || 1;
	  filter.shop = $('#select_shop').val() || '';
	  filter.from = $('#date_from').val();
	  filter.to = $('#date_to').val();
  
	  get_billing_table(JSON.stringify(filter));
	});
  
	$(document).on('click', '.btn_view', function () {
	  let id = $(this).get(0).id;
	  let bilcod = $(this).data('ref_num');
	  let total_amount = $(this).data('total_amount');
	  let processfee = $(this).data('processfee');
	  let netamount = $(this).data('netamount');
	  let total_amount_w_shipping = $(this).data('total_amount_w_shipping');
	  let delivery_amount = $(this).data('delivery_amount');
	  let netamount_w_shipping = $(this).data('netamount_w_shipping');
  
	  $.ajax({
		url: base_url + 'reports/Sale_settlement2/get_billing',
		type: 'post',
		data: { id,bilcod },
		dataType: 'json',
		beforeSend: function () {
		  $.LoadingOverlay('show');
		},
		success: function (json_data) {
		  $.LoadingOverlay('hide');
		  if (json_data.success == true) {
			$('#exampleModalLabel').html('Billing Code: ' + json_data.message.billcode);
			$('#f_billid').html(json_data.message.id);
			$('#tm_billno').html(json_data.message.billno);
			$('#tm_billcode').html(json_data.message.billcode);
			$('#tm_syshop').html(json_data.message.shopname);
			$('#tm_billcode').html(json_data.message.billcode);
			$('#tm_trandate').html(json_data.message.trandate);
			$('#tm_delivery_amount').html(delivery_amount);
			$('#tm_totalamount').html(accounting.formatMoney(json_data.message.totalamount));
			$('#tm_totalamount_w_shipping').html(total_amount_w_shipping);
			$('#tm_remarks').html(json_data.message.remarks);
  
			if (json_data.message.ratetype == 'f')
			  $('#tm_ratetype').html('Fixed');
			else
			  $('#tm_ratetype').html('Percentage');
  
			$('#tm_processrate').html(json_data.message.processrate);
			$('#tm_processfee').html(accounting.formatMoney(json_data.message.processfee));
			// $('#tm_netamount').html(accounting.formatMoney(json_data.message.netamount));
			$('#tm_netamount').html(netamount_w_shipping);
			$('#tm_paystatus').html(draw_transaction_status(json_data.message.paystatus));
  
			if (json_data.message.paystatus != 'Settled') {
			  $('.grp_payment').hide();
			  $('.btn_tbl_pay').show();
			}
			else {
			  $('#tm_paiddate').html(json_data.message.paiddate);
			  $('#tm_paidamount').html(json_data.message.paidamount);
			  $('#tm_paytype').html(json_data.message.pay_type);
			  $('#tm_payref').html(json_data.message.payref);
			  $('#tm_payattach').html(json_data.message.payattach);
			  $('#tm_payremarks').html(json_data.message.payremarks);
			  $('.grp_payment').show();
			  $('.btn_tbl_pay').hide();
			}
			const billing_data = {
			  search: '',
			  shop: json_data.message.syshop,
			  trandate: json_data.message.trandate,
			  ratetype: json_data.message.ratetype,
			  processrate: json_data.message.processrate,
			  branch_id: json_data.message.branch_id,
			  per_branch_billing: json_data.message.per_branch_billing,
			  totalamount: total_amount,
			  processfee: processfee,
			  netamount: netamount,
			  netamount_w_shipping: netamount_w_shipping,
			  delivery_amount: delivery_amount
			}
			get_billing_breakdown_table(billing_data);
			gen_branch_billing_tbl('', json_data.message.syshop, json_data.message.trandate);
			$('#main_body').hide();
			$('#sub_body').show();
		  } else {
			//messageBox(data.message, 'Warning', 'warning');
			showCpToast("warning", "Warning!", data.message);
		  }
		},
		error: function () {
		  //notificationError('Error', 'Oops! Something went wrong. Please try again.');
		  showCpToast("error", "Error!", 'Oops! Something went wrong. Please try again.');
		  $.LoadingOverlay('hide');
		}
	  });
	});
  
	$(document).delegate('.btn_tbl_pay', 'click', function (e) {
	  $("#tag_payment").prop("checked", false);
	  $('#modal_save').modal('hide');
	  $('.grp_payment_others').hide();
	  // $('.grp_payment-p').hide();
	  $.LoadingOverlay('show');
	  $('#f_id-p').val($('#tm_billcode').html().trim());
	  $('#tm_header_ref').html('Settlement for Billing Code ' + $('#tm_billcode').html());
	  $('#tm_order_date-p').html(' ' + $('#tm_trandate').html());
	  $('#tm_order_reference_num-p').html(' ' + $('#tm_billcode').html());
	  $('#tm_amount-p').html(' ' + $('#tm_netamount').html());
	  $('#tm_payment_status-p').html(' ' + $('#tm_paystatus').html());
	  $.LoadingOverlay('hide');
	  $('#payment_modal').modal();
  
	});
  
	$(document).on('submit', '#form_save_payment', function (e) {
	  e.preventDefault();
	  $.LoadingOverlay('show');
	  // checkPayment();
	  var form = $(this);
	  var form_data = new FormData(form[0]);
	  // form_data.append([ajax_token_name],ajax_token);
  
	  $.ajax({
		type: form[0].method,
		url: base_url + "reports/Sale_settlement2/settleBilling",
		data: form_data,
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
		  $.LoadingOverlay('hide')
		  var json_data = JSON.parse(data);
		  update_token(json_data.csrf_hash);
		  if (json_data.success) {
			resetPaymentForm();
			$('#payment_modal').modal('hide');
			//messageBox(json_data.message, 'Success', 'success');
			showCpToast("success", "Success!", json_data.message);
			setTimeout(function(){location.reload()}, 2000);
			get_billing_table(JSON.stringify(filter));
			$('.btn_back').trigger('click');
		  } else {
			//messageBox(json_data.message, 'Warning', 'warning');
			showCpToast("warning", "Warning!", json_data.message);
		  }
  
		},
		error: function (error) {
		  //messageBox('Something went wrong. Please try again.', 'Error', 'error');
		  showCpToast("error", "Error!", 'Something went wrong. Please try again.');
		}
	  });
  
	});
  
	$(document).on('click', '.btn_view_logs', function () {
	  let orderid = $(this).data('orderid');
	  let refnum = $(this).data('refnum');
	  let totalamount = $(this).data('totalamount');
	  let processfee = $(this).data('processfee');
	  let netamount = $(this).data('netamount');
  
	  get_billing_logs(orderid, totalamount, processfee, netamount);
	  $('#view_modal').modal();
	});
  
	$(document).on('click', '.btn_view_branch_logs', function () {
	  let branchid = $(this).data('branchid');
	  let trandate = $(this).data('trandate');
	  let totalamount = $(this).data('totalamount');
	  let processfee = $(this).data('processfee');
	  let netamount = $(this).data('netamount');
	  get_billing_branch_logs(branchid, trandate, totalamount, processfee, netamount);
	  $('#view_modal2').modal();
	});
  
	$(document).on('click', '.btn_back', function () {
	  $.LoadingOverlay('show');
	  $('#sub_body').hide();
	  $('#main_body').show();
	  $.LoadingOverlay('hide');
	});
  
	$("#search_hideshow_btn").click(function (e) {
	  e.preventDefault();
  
	  var visibility = $('#card-header_search').is(':visible');
  
	  if (!visibility) {
		//visible
		$("#search_hideshow_btn").html('&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
	  } else {
		//not visible
		$("#search_hideshow_btn").html('Show Search <i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
	  }
  
	  $("#card-header_search").slideToggle("slow");
	});
  
	$("#search_clear_btn").click(function (e) {
	  filter.status = 1;
	  filter.shop = "";
	  filter.from = "";
	  filter.to = "";
	  // $(".search-input-text").val("");
	  $('#date_from').val('');
	  $('#date_to').val('');
	  $('#select_status option[value="1"]').prop('selected', true);
	  $('#select_shop option[value=""]').prop('selected', true);
	  get_billing_table(JSON.stringify(filter));
	});
  });
  