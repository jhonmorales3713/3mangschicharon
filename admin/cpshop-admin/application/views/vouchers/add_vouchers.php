
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="" data-namecollapse="" data-labelname="Add Voucher"> 
    <div class="bc-icons-2 card mb-4">
       <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/vouchers_home/'.$token);?>">Voucher</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('voucher_list/index/'.$token);?>">Voucher List</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add Voucher</li>
        </ol>
    </div>
  
    <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="h4">Details</h3>
                        </div>
                        <form id="save_voucher" enctype="multipart/form-data" method="post">
                            <div class="card-body center">

                            <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="idno" class="control-label">ID No*</label>
                                                    <input type="text"  class="form-control  form-control required_fields allownumericwithoutdecimal " name="idnum" id="idnum">
                                                </div>
                                        </div>
                                 </div>

                           <?php  
                                  $shopid = $this->session->userdata('sys_shop_id');
                            
                                  if($shopid != 0){
                                  foreach ($shops_per_id as $shop){  
                            ?>
                              <input type="hidden" name="shopid" id="shopid" value="<?php echo $shopid; ?>"> 
                              <input type="hidden" name="shopcode" id="shopcode" value="<?=$shop['shopcode'];?>"> 
                             <?php  }}else{   ?>
                                <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="shop_field">
                                                <label>Shop Name*</label>  
                                                    <select name="shopid" id="shopid" class="form-control material_josh form-control-sm search-input-text enter_search">
                                                        <option value="">Select Shop Name</option>
                                                        <?php foreach ($shops as $shop): ?>
                                                            <option value="<?=$shop['id'];?>" data-code="<?=$shop['shopcode'];?>"><?=$shop['shopname'];?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                            </div>
                                        </div>
                                </div>

                                <input type="hidden" name="shopcode" id="shopcode" value=""> 

                             <?php }?>

                                <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="vrefnum" class="control-label">Voucher Reference Number*</label>
                                                    <input type="text" class="form-control required_fields" name="vrefnum" id="vrefnum">
                                                </div>
                                        </div>
                                 </div>

                                 <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="vcode" class="control-label">Voucher Code*</label>
                                                    <input type="text" class="form-control required_fields" name="vcode" id="vcode">
                                                </div>
                                        </div>
                                 </div>

                                 <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="vamount" class="control-label">Voucher Amount*</label>
                                                    <input type="text" class="form-control required_fields" name="vamount" id="vamount">
                                                </div>
                                        </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-12">
                                            <label for="date_issue" class="control-label">Date Issue </label>    
                                              <div class="input-daterange input-group" id="datepicker">
                                            <input type="text" class="input-sm form-control required_fields search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_issue" value="<?=today_text();?>" name="date_issue" readonly/>     
                                        </div>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-12">
                                            <label for="date_valid" class="control-label">Valid Until</label>    
                                       <div class="input-daterange input-group" id="datepicker">
                                            <input type="text" class="input-sm form-control required_fields search-input-select1 date_valid datepicker" style="z-index: 2 !important;" id="date_valid" value="<?=today_text();?>" name="date_valid" readonly/>     
                                        </div>
                                    </div>
                                 </div>

                                <div class="form-group row mt-4">       
                                        <div class="col-md-12">
                                            <button style="float:right" class="btn btn-primary saveChangeAvatarBtn">Save</button>
                                        </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>   
            </div>
        </div>
  
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/vouchers/add_vouchers.js');?>"></script>
<!-- end - load the footer here and some specific js -->

