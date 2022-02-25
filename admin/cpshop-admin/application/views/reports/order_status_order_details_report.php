<!-- change the data-num and data-subnum for numbering of navigation -->
<style>
  /* .datepicker{
    z-index: 999 !important;
  } */

  table{
      width: 100% !important;
  }

  td, th {
    vertical-align: middle !important;
  }

  .align-right{
    text-align: right;
  }

  .flex-right {
    display:flex;
    justify-content: flex-end;       
  }

  .btn{
      padding-top: 10px !important;
      padding-bottom: 10px !important;
  }

  table.branchdetails tr, table.branchdetails tr td{
      border: none !important;
  }

  thead tr td{      
      text-align: center !important;
  }  

  tr.b *{
    font-weight: bold !important;
  }

  .big_font{
      font-size: 1.5rem !important;
  }

    /* responsiveness of datepicker */
    @media screen and (max-width: 450px) {
      #datepicker{
          height: max-content !important;
      }
      .f_s1{
          display: inline !important;
      }
      .f_s2{
          display: none !important;
      }
      .f_i{
          display: none !important;
      }
      #datepicker2{
          display: block !important;
      }
      .summary tr, .summary td{
          padding: 3px !important;
          font-size: .8rem !important;
      }
  }

</style>
<div class="content-inner" id="pageActive" data-num="7" data-namecollapse="" data-labelname="Reports">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/report_home/'.$token);?>">Reports</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a class="white-text" href="<?= base_url('reports/order_status/'.$token); ?>">Order Status Report</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><a href="<?= base_url('Order_status/shop_branch_pending_orders_in_date/'.$shop_id.'/'.$branch_id.'/'.$reference_num.'/'.$token.'?payment_date='.$payment_date); ?>"><?= $shopname; ?></a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?= $branchname ?></li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card" id = "main_body">
                <!-- <p class="border-search_hideshow"><a href="#" id="search_hideshow_btn">&ensp;Hide Search <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a> <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a></p> -->
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h3 class="card-title mb-0">
                                Orders Status Report
                            </h3>
                        </div>                      
                    </div>
                </div>                               
                <div class="card-body">
                    <div class="flex-right">
                        <form action="<?=base_url('reports/Order_status/export_order_details_table/'.$reference_num)?>" method="post" target="_blank">
                            <input type="hidden" name="_search" id="_search">
                            <input type="hidden" name="_filters" id="_filters">                            
                            <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;                            
                        </form>  
                        <!--
                        <button class="btn btn-primary btnSearch" id="btnSearch">Search</button>      
                        -->
                    </div>   
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="">Reference Number:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->reference_num; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Order Date:</label><br>
                            <label class="green-text font-weight-bold"><?= date('M d, Y g:i A',strtotime($order_details->date_ordered)); ?></label>
                        </div>  
                        <div class="col-12 col-md-6">
                            <label class="">Payment Date:</label><br>
                            <label class="green-text font-weight-bold"><?= date('M d, Y g:i A',strtotime($order_details->payment_date)); ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Subtotal:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->delivery_amount != null ? number_format(floatval($order_details->total_amount) - floatval($order_details->delivery_amount),2) : $order_details->total_amount; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Shipping:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->delivery_amount != null ? $order_details->delivery_amount : '0.00'; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Total Amount:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->total_amount; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Sold To:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->name; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Email:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->email; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Address:</label><br>
                            <label class="green-text font-weight-bold"><?= $order_details->address; ?></label>
                        </div> 
                        <div class="col-12 col-md-6">
                            <label class="">Order Status:</label><br>
                            <label class="green-text font-weight-bold big_font" id="order_status_label"><?= draw_transaction_status($order_details->order_status);?></label>
                        </div>                       
                    </div>                                    
                    <table class="table table-striped table-hover table-bordered" style="width:100%" id="table-grid">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>   
                        <tfoot>
                            <tr class="b">
                                <td class="align-right">Total</td>
                                <td id="t_qty"></td>
                                <td id="t_stotal"></td>
                                <td id="t_amount"></td>                            
                            </tr>
                        </tfoot>                          
                    </table>                          
                </div>
            </div>         
        </div>
    </section>
</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<input type="hidden" name="branchid" id="branch_id" value="<?=$branch_id?>">
<input type="hidden" name="branchid" id="shop_id" value="<?=$shop_id?>">
<input type="hidden" name="payment_date" id="payment_date" value="<?=$payment_date?>">
<input type="hidden" name="ref_num" id="ref_num" value="<?=$reference_num?>">
<input type="hidden" name="is_manual_order" id="is_manual_order" value="<?=$is_manual_order?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets\js\public.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\chart.min.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\chart\Chart.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets\js\reports\order_status_order_details.js');?>"></script>

<!-- end - load the footer here and some specific js -->

