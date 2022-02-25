<!--
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
 -->

 <?php // matching the token url and the token session
if ($this->session->userdata('token_session') != en_dec("dec", $token)) {
    header("Location:" . base_url('Main/logout')); /* Redirect to login */
    exit();
}

//022818
$access_nav = $this->session->userdata('access_nav');


?>
<div class="content-inner" id="pageActive" data-num="1" data-namecollapse="" data-labelname="Home">
    <!-- Page Header-->
    <!-- Breadcrumb-->
<div class="dashboard">
    <div class="container-fluid">

        <div class="row mb-5">
            <div class="col-12">
                <div class="card p-4">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="border-top-gray" id="prodstat">
                                <div class="row">
                                    <div class="col-lg-12">                                                            
                                        <div class="input-group">                                                                             
                                                <select style="height:42px;" type="text" name="reprange" id="reprange" class="form-control" >
                                                    <option value="today">Today</option>                                                                        
                                                    <option value="yesterday">Yesterday</option>    
                                                    <option value="last_7">Last 7 Days</option>    
                                                    <option value="last_30">Last 30 Days</option>   
                                                    <option value="last_90">Last 90 Days</option>  
                                                    <option value="custom">Custom</option>
                                                </select>                                                                                                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-daterange input-group">
                                <span class="input-group-addon">From</span>
                                <input type="text" autocomplete="off" class="form-control form-control-sm search-input-text datepicker-from" value="<?=today_date();?>" id="f_date" name="f_date" placeholder="MM/DD/YYYY" autocomplete="false">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-daterange input-group">
                                <span class="input-group-addon">To &nbsp;&nbsp;</span>
                                <input type="text" autocomplete="off" class="form-control form-control-sm search-input-text datepicker-from" value="<?=today_date();?>" id="f_date_2" name="f_date_2" placeholder="MM/DD/YYYY" autocomplete="false">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="border-top-gray" id="searchRN">
                                <div class="row">
                                    <div class="col-lg-12">                                                            
                                        <div class="input-group">                                                                             
                                            <button class="input-group-btn btn-sm btn btn-primary btn-auto" type="button" id="btnSearch">
                                                <i class="fa fa-search no-margin"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
        <input type="hidden" id="shopid" name="shopid" value="<?=$shopid;?>"/>


        <div class="row mb-5">
            <?php if ($this->loginstate->get_access()['dashboard']['sales_count_view']==1): ?>
                <div class="col-12 col-md-3 summary-stat">
                    <div class="card p-4">
                        <div class="row no-gutters">
                            <div class="col-auto">
                                <img class="summary-image" src="<?=base_url("assets/img/sales2.png")?>" alt="">
                            </div>
                            <div class="col d-flex align-items-center">
                                <h1 class="stat-title font-weight-bold mb-0" id="head_sales">0 <span class="summary-stat-title d-block">Sales</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($this->loginstate->get_access()['dashboard']['transactions_count_view']==1): ?>
                <div class="col-12 col-md-3 summary-stat">
                    <div class="card p-4">
                        <div class="row no-gutters">
                            <div class="col-auto">
                                <img class="summary-image" src="<?=base_url("assets/img/transaction.png")?>" alt="">
                            </div>
                            <div class="col d-flex align-items-center">
                                <h1 class="stat-title font-weight-bold mb-0" id="head_transactions">0 <span class="summary-stat-title d-block">Transactions</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($this->loginstate->get_access()['dashboard']['views_count_view']==1): ?>
                <div class="col-12 col-md-3 summary-stat">
                    <div class="card p-4">
                        <div class="row no-gutters">
                            <div class="col-auto">
                                <img class="summary-image" src="<?=base_url("assets/img/views.png")?>" alt="">
                            </div>
                            <div class="col d-flex align-items-center">
                                <h1 class="stat-title font-weight-bold mb-0" id="head_views">0 <span class="summary-stat-title d-block">Views</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if ($this->loginstate->get_access()['dashboard']['overall_sales_count_view']==1): ?>
                <div class="col-12 col-md-3 summary-stat">
                    <div class="card p-4">
                        <div class="row no-gutters">
                            <div class="col-auto">
                                <img class="summary-image" src="<?=base_url("assets/img/totalsales.png")?>" alt="">
                            </div>
                            <div class="col d-flex align-items-center">
                                <h1 class="stat-title font-weight-bold mb-0" id="head_overall_sales">0 <span class="summary-stat-title d-block">Overall Sales</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <div class="row chart-item">
            <?php if ($this->loginstate->get_access()['dashboard']['visitors_chart_view']==1){?>
            <div class="col-12 col-md-6">
            <?php }else{ ?>
            <div class="col-12 col-md-6" hidden>  
            <?php }?>
                <h1 class="font-weight-bold mb-3">Visitors</h1>
                <div class="card p-4 chart-card">
                    <canvas class="chart" id="visitorChart"></canvas>
                </div>
            </div>

            <?php if ($this->loginstate->get_access()['dashboard']['views_chart_view']==1){?>
            <div class="col-12 col-md-6">
            <?php }else{ ?>
            <div class="col-12 col-md-6" hidden>  
            <?php }?>
                <h1 class="font-weight-bold mb-3">Views</h1>
                <div class="card p-4 chart-card">
                    <canvas class="chart" id="viewChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row chart-item">
            <div class="col-12 col-md-8">
                <div class="row">
                    <?php if ($this->loginstate->get_access()['dashboard']['sales_chart_view']==1){?>
                    <div class="col-12">
                        <h1 class="font-weight-bold mb-3">Sales</h1>
                    </div>
                    <div class="col-12 mb-5">
                    <?php }else{ ?>
                    <div class="col-12 mb-5" hidden>
                    <?php }?>
                        <div class="card p-4 chart-card">
                            <div class="row col-12">                                                                                                 
                                <div class="col-12 col-lg-4 space-10">
                                    <a class="no-decor">
                                        <h1 id="paid_sales" class="bolder">0</h1>
                                        <p class="no-margin-bottom">Total Amount of Paid Orders</p>
                                    </a>
                                </div>
                                <div class="col-12 col-lg-4 space-10">
                                    <a class="no-decor">
                                        <h1 id="unpaid_sales" class="bolder">0</h1>
                                        <p class="no-margin-bottom">Total Unpaid Orders</p>
                                    </a>
                                </div>                                    
                                <div class="col-12 col-lg-4 space-10">
                                    <a class="no-decor">
                                        <h1 id="total_sales" class="bolder">0</h1>
                                        <p class="no-margin-bottom">Total Orders</p>
                                    </a>
                                </div>  
                            </div>
                            <canvas class="chart" id="salesChart"></canvas>
                        </div>
                    </div>

                    <?php if ($this->loginstate->get_access()['dashboard']['transactions_chart_view']==1){?>
                    <div class="col-12">
                        <h1 class="font-weight-bold mb-3">Transactions</h1>
                    </div>
                    <div class="col-12">
                    <?php }else{ ?>
                    <div class="col-12" hidden>
                    <?php }?>
                        <div class="card p-4 chart-card">
                            <div class="row col-12">                                                                                                 
                                <div class="col-12 col-lg-4 space-10">
                                    <a class="no-decor">
                                        <h1 id="paid_count" class="bolder">0</h1>
                                        <p class="no-margin-bottom">Total No. of Paid Orders</p>
                                    </a>
                                </div>
                                <div class="col-12 col-lg-4 space-10">
                                    <a class="no-decor">
                                        <h1 id="unpaid_count" class="bolder">0</h1>
                                        <p class="no-margin-bottom">Total No. of Unpaid Orders</p>
                                    </a>
                                </div>                                    
                                <div class="col-12 col-lg-4 space-10">
                                    <a class="no-decor">
                                        <h1 id="total_count" class="bolder">0</h1>
                                        <p class="no-margin-bottom">Total No. of Orders</p>
                                    </a>
                                </div> 
                            </div>
                            <canvas class="chart" id="transcationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($this->loginstate->get_access()['dashboard']['top10productsold_list_view']==1){?>
            <div class="col-12 col-md-4 mt-5">
            <?php }else{ ?>
            <div class="col-12 col-md-4 mt-5" hidden>
            <?php }?>
            
                <div class="card p-4">
                    <h2 class="font-weight-bold pb-4">Top 10 Products Sold</h2>
                    <ol class="item-list" id="top_10_products"></ol>
                </div>
            </div>
        </div>
    </div>

    
</div>
<?php $this->load->view('includes/footer');?>
<script src="<?=base_url('assets/js/dashboard.js');?>"></script>


