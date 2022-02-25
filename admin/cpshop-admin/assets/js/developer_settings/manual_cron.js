$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();

  $(document).on('click', '#btn_trigger_cron', function(){
    let cron_type = $('#cron_type').val();
    let cron_key = $('#cron_key').val();
    let cron_date = $('#cron_date').val();
    let cron_shop = $('#shops').val();

    let url = "";
    switch (cron_type) {
      case 'per_product_rate':
        url = base_url+`accounts/Billing/processDailyMerchantPay/${cron_key}/${cron_date}/1`;
        break;
      case 'per_product_rate_confirmed':
        url = base_url+`accounts/Billing/processDailyMerchantPay_confirmed/${cron_key}/${cron_date}/1`;
        break;
      case 'per_shop_rate':
        url = base_url+`accounts/Billing/processDailyMerchantPay_old/${cron_key}/${cron_date}/1`;
        break;
      case 'per_payment_portal_fee':
        url = base_url+`accounts/Billing/processdaily_merchant_pay_government/${cron_key}/${cron_date}/1`;
        break;
      case 'foodhub':
        url = base_url+`accounts/Billing/processBilling_foodhub/${cron_key}/${cron_date}/1`;
        break;
      case 'foodhub_confirmed':
        url = base_url+`accounts/Billing/processBilling_foodhub_confirmed/${cron_key}/${cron_date}/1`;
        break;
      case 'toktokmall':
        url = base_url+`accounts/Billing/processBilling_toktokmall/${cron_key}/${cron_date}/${cron_shop}`;
        break;
      case 'unsettled':
        url = base_url+`accounts/Billing/processBilling_unsettled/${cron_key}/${cron_date}/1`;
        break;
      case 'merchant_billing_codcash':
        url = base_url+`accounts/Billing_merchant/processBilling_merchant/${cron_key}/${cron_date}/${cron_shop}`;
        break;
      default:

    }

    $.ajax({
      url: url,
      type: 'post',
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        $('#cron_date').val('');
        $('#cron_type option[value="per_product_rate"]').prop('selected',true);
        $('#cron_key').val('ShopandaKeyCloud3578');
        //messageBox("DONE", 'Success', 'success');
        showCpToast("success", "Success!", "DONE");
        setTimeout(function(){location.reload()}, 2000);
      },
      error: function(){
        //messageBox('Something went wrong. Please try again.', 'Error', 'error');
        showCpToast("error", "Error!", 'Something went wrong. Please try again.');
        $.LoadingOverlay('hide');
      }
    });
  });
});
