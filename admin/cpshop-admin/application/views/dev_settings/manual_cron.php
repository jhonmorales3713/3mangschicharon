<!-- change the data-num and data-subnum for numbering of navigation -->
<style>

</style>
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Wallet">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Manual Cron</li>
        </ol>
    </div>
    <section class="tables">
      <div class="container-fluid">
        <div class="col-md-6 offset-md-3">
          <div class="card">
            <div class="card-header">
              <h4>Cron Manual Trigger</h4>
            </div>
            <div class="card-body">
              <div class="form-group row">
                <div class="col-md-12 mb-2">
                  <label for="Cron Type" class="form-control-label col-form-label-sm">Cron Type</label>
                  <select name="cron_type" id="cron_type" class="form-control select2">
                    <!-- <option value="per_product_rate">Per product rate</option> -->
                    <!-- <option value="per_product_rate_confirmed">Per product rate confirmed</option> -->
                    <option value="foodhub">Foodhub</option>
                    <option value="foodhub_confirmed">Foodhub Confirmed</option>
                    <option value="toktokmall">Toktokmall</option>
                    <option value="unsettled">Unsettled Only</option>
                    <option value="unsettled_confirmed">Unsettled Confirmed Only</option>
                    <!-- <option value="merchant_billing_codcash">Merchant (COD CASH)</option> -->
                    <!-- <option value="per_shop_rate">Per shop rate</option> -->
                    <!-- <option value="per_payment_portal_fee">Per payment porta fee</option> -->
                  </select>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="Shops (optional)" class="form-control-label col-form-label-sm">Shops (optional)</label>
                  <select name="shops" id="shops" class="form-control select2">
                    <option value="0">------</option>
                    <?php if(count((array)$shops) > 0):?>
                      <?php foreach($shops as $key => $shop):?>
                        <option value="<?=$shop['id']?>"><?=$shop['shopname']?></option>
                      <?php endforeach;?>
                    <?php endif;?>
                  </select>
                </div>
                <div class="col-md-6 mb-2">
                  <label for="Key" class="form-control-label col-form-label-sm">Key</label>
                  <input type="text" id = "cron_key" name = "cron_key" class="form-control" value = "ShopandaKeyCloud3578">
                </div>
                <div class="col-md-6 mb-2">
                  <label for="Date" class="form-control-label col-form-label-sm">Date</label>
                  <input type="text" class="form-control date_input" id = "cron_date" name = "cron_date">
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button class="btn btn-primary" id = "btn_trigger_cron">Run</button>
            </div>
          </div>
        </div>
      </div>
    </section>

</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\developer_settings\manual_cron.js');?>"></script>
<!-- end - load the footer here and some specific js -->
