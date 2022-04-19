<link rel="stylesheet" href="<?=base_url('assets/css/libs/bootstrap-tagsinput.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/libs/app.css');?>">
<style>
/* .imageprevies{
    width: 100%;
    height: auto;
    margin: 0 auto 0 auto;
    background-color: #b5b5b5;
    overflow: hidden;
    position: relative;
} */
.divclose {
  position: relative;
}
.deleteimg {
  position: absolute;
  margin-bottom: 75px;
  margin-left: -28px;
  font-size: 18px;
  cursor:pointer;
  background-color:white;
  /* border-radius:50px; */
  opacity:0.9;
  padding:10px;
  display:none;

}

#sortable { 
    list-style-type: none; 
    margin: 0; padding: 0; 
    width: 100%; 
    }
#sortable li { 
    margin: 3px 3px 3px 0; 
    padding: 1px; float: left; 
    width: 100%; height: 100%; 
    font-size: 4em; 
    text-align: center; 
    }
/* .parentVariantDiv {
    display:none;
} */
</style>

<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_customers/');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_customers/customers/'.$token);?>">Customer List</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold">View Customer</span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular"><?=$get_customerdetails['full_name'];?></span>
    </div>
</div>

<div class="col-12">
    <div class="container-fluid ml-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-12 mb-3 pr-5">
                        <div class="card-header">
                            <h3 class="card-title">Customer Information </h3>
                        </div>
                        <div class="card">
                            <form id="form_update" enctype="multipart/form-data" method="post">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row hidden">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>ID</label>
                                                        <input type="text" name="f_id" id="f_id" class="form-control" value="0" >
                                                        <input type="text" name="productimage_changes" id="productimage_changes" class="form-control" value="0" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="f_firstname" class="control-label">Full Name*</label>
                                                        <input type="text" class="form-control" value="<?=$get_customerdetails['full_name']?>"name="f_firstname" id="f_firstname" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="f_email" class="control-label">Email*</label>
                                                        <input type="email" class="form-control" value="<?=$get_customerdetails['email']?>" name="f_email" id="f_email" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="f_conno" class="control-label">Mobile No.*</label>
                                                        <input type="number" class="form-control" value="<?=$get_customerdetails['mobile']?>" name="f_conno" id="f_conno" disabled>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12 mb-3">
                                                    <div class="image-upload-card">
                                                        <div class="form-group">
                                                            <label>User Image </label></br>
                                                            <label><small><b>Primary Photo</b></small></label>
                                                            <img src="<?= base_url('assets/img/logo-imgplaceholder.jpg') ?>" id="primary_product" class="img-thumbnail" alt="Responsive image">
                                                            </br>
                                                            
                                                            <div id="product-placeholder">
                                                                
                                                            </div>
                
                                                            <div id="sortable" class="imagepreview2 d-flex flex-row bd-highlight mb-3 col-12" style="display:none;"></div>
                                                            <div class="imagepreview mb-3" style="display:none;"></div>
                
                                                            <div class="oldimgurl" style="display:none;"></div>
                                                            <img src="" id="product_preview_multiple" class="img-responsive">
                                                        </div>
                                                    </div>
                                                </div>
            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="u_id" value="<?=$Id?>">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-12 d-flex justify-content-end pl-4">
                <div class="row">
                    <div class="col-12 mb-3 pr-5">
                        <div class="card ">
                            <div class="card-body">
                                <!-- <button type="button" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</button> -->
                                <?php if($get_customerdetails['user_type_id']==2 ){?>
                                <button type="button" class="btn btn-danger approvalbtn" data-content=".declineContent" data-custid="<?=$Id?>" data-disable=".verifyContent">Decline</button>
                                <button type="button" class="btn btn-success approvalbtn" data-content=".verifyContent" data-custid="<?=$Id?>"  data-disable=".declineContent">Verify</button>
                                <?php } ?>
                                <!-- <button type="submit" class="btn btn-success saveBtn">Save</button> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="col-md-1">&nbsp;</div>
        
    </div>
</div>
<!-- Add Modal-->
<div id="verifyCustomerModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md modal-md-custom">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h5 id="exampleModalLabel" class="modal-title">Verify Customer</h5>
            </div>
            <div class="verifyContent" style="display:none">
                <div class="modal-body">
                    <div class="card-body">
                        Verifying this customer information will enable COD transactions for this user. Continue?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                    <button id="verifybtnCustomer" class="btn btn-primary">Continue</button>
                </div>
            </div>
            <div class="declineContent" style="display:none">
                <div class="modal-body">
                    <div class="card-body">
                        Declining this customer information will disable COD transactions for this user. Continue?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary cancelBtn" data-dismiss="modal" aria-label="Close">Close</button>
                    <button id="declinebtnCustomer" class="btn btn-primary">Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/customers/view_customer.js');?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>
<script src="<?=base_url('assets/js/libs/bootstrap-tagsinput.min.js');?>"></script>
<script src="<?=base_url('assets/js/libs/app.js');?>"></script>