<div class="content-inner" id="pageActive" data-num="3"></div>

<h4 class="text-uppercase page-title">Orders</h4>
<?=$gg; ?>
<div class="container-fluid shop-container">
    <div class="portal-table col-lg-10">
      <div class="portal-table__header">
        <div class="row">
          <div class="col-md-5">
            <label for="">Select Branch</label>
            <select  name="branchSelected" class="custom-select" id="branchSelected">
              <?php if(count($userFranchise) > 1) { ?>
                <option  selected value="ALL">ALL Branches</option>
              <?php } ?>
              <?php foreach($userFranchise as $franchise){?> 
                  <option selected value="<?=$franchise->userId;?>"><?=$franchise->branchname;?></option>
              <?php }  ?>
            </select>
          </div>

          <div class="col-md-5">
            <label for="">Search Date</label>
            <input class = "custom-select dateFilter" type="text" name="daterange" placeholder = "Select Date Range" />
          </div>
        </div>
      </div>
    </div>
</div>
  

  <div class="col-md-12 mt-5 p-0">
    <div class="card" style = "border-radius: 0" >
      <input type="hidden" value = "<?= $this->session->userdata('user_id');?>" id = "userId">
      <div class="portal-table__container portal-table__container--shadow row" id = "order-table-container">
       
      </div>
    </div>
  </div>

  <div id = "viewOrderDetailsModal" class="modal" tabindex="-1" role="dialog" style = "z-index: 10000000 !important;">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content"">
        <div class="modal-header">
          <h5 class="modal-title">Order Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-3">
            <div class="orderItems">
              <!-- <h3><strong id="titleFranchise"></strong></h3><br/> -->

                <div class="table-container">
                    <table id="orderDetailsTable" class="table table-striped" style="min-height:330px;">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <!-- <th>Delivery Date</th> -->
                        <th>Amount</th>
                        <th>Total Amount</th>
                      </tr>
                    </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-primary">Checkout</button> -->
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

<?php $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/orders/orders.js');?>"></script>
