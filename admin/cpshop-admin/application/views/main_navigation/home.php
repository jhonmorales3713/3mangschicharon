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

// print_r($this->session);
?>
<style>
    table td, th{
        padding: 0px !important;
        border-top: none !important;
    }
</style>
<div class="content-inner" id="pageActive" data-num="1" data-namecollapse="" data-labelname="Home">
    <!-- Page Header-->
    <!-- Breadcrumb-->
<div class="dashboard" style="height: 100%;">
    <div class="container-fluid">
        <div class="row mb-5">
            <div class="col-12">
                <!-- <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Dashboard Overview</li>
                    </ol>
                </nav> -->
                <div class="card">
                    <div class="breadcrumb active"><h2 class="breadcrumb-item font-bold">Dashboard Overview</h2></div>
                    <div class="row p-4">
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 mb-2 sm:mb-0">
                            <div class="border-top-gray" id="prodstat">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="input-group" style="height: 46px;">
                                            <select type="text" name="reprange" id="reprange" class="form-control" >
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
                        <div class="search-filter hidden col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0">
                            <div class="input-daterange input-group" data-date-start-date="0day" data-date-end-date="0d">
                                <span class="input-group-addon col-xs-2 m-0 mb-2 sm:mb-0">From</span>
                                <input type="text" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" value="<?=today_date();?>" id="f_date" name="f_date" placeholder="MM/DD/YYYY" readonly>
                                <div class="d-block d-sm-none w-100"></div>
                                <span class="input-group-addon col-xs-2 m-0 mb-2 sm:mb-0">&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;</span>
                                <input type="text" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" value="<?=today_date();?>" id="f_date_2" name="f_date_2" placeholder="MM/DD/YYYY" readonly>
                            </div>
                            <div class="my-2 my-md-0 text-xs font-semibold text-orange-400"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;You cannot pick more than 3 months of data. Pick End Date First.</div>
                        </div>
                        <!-- <div class="search-filter hidden col-lg-3">
                            <div class="input-daterange input-group">
                                
                            </div>
                        </div> -->
                        <div class="search-filter hidden col-sm-2 col-md-2 col-lg-1 mb-2 sm:mb-0">
                            <div class="border-top-gray" id="searchRN">
                                <div class="row">
                                    <div class="col-12 w-100">
                                        <div class="input-group">
                                            <button class="input-group-btn btn-sm btn btn-primary btn-auto col-12" type="button" id="btnSearch">
                                                <i class="fa fa-search no-margin"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div id="noresult-warn" class="alert text-center" role="alert"> <i class="fa fa-search"></i> No search results found.</div> -->
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
        <input type="hidden" id="shopid" name="shopid" value="<?=$shopid;?>"/>
        <input type="hidden" id="branchid" name="branchid" value="<?=$this->session->userdata('branchid');?>"/>

        <!-- <div class="row mb-5">
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
        </div> -->
        <div id="no-chart-to-view" class="row m-0 chart-item" style="display: none;">
            <div class="card w-100">
                <div class="card-body">
                    <div class="alert-container row align-items-center h-100 m-0">
                        <div class="alert text-center m-0 w-100" role="alert"> <i class="fa fa-search"></i> No records to show for the selected date range. To show records, kindly select your preferred date range.</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="report-charts" class="row mb-5 chart-item align-self-stretch" style="height: auto;">
            <div id="aov" fromrecord="order" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Average Order Value</h4>
                            <div class="col text-right">
                                <a href='<?= base_url("reports/average_order_value/$token");?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="row a-result">
                            <span id="aov_current_ave" class="col-7 font-weight-bold text-sm lg:text-base">P 0.00</span>
                            <span class="col text-right font-weight-bold text-sm lg:text-base" id="aov_percent"></span>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="averageOrderValue" style="height: 339px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="invlist" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col-7 card-title font-weight-bold">Inventory List Report</h4>
                            <div class="col text-right">
                                <a href="<?= base_url('reports/inventory_list/'.$token);?>" class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="invChart" style="height: 363px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="oscrr" fromrecord="views" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col-8 card-title font-weight-bold">Online Store Conversion Rate</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("online_conversion_rate/index/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="row oscr_top my-1 a-result">
                            <span id="oscr_p1" class="col-4 font-weight-bold oscr_data text-sm lg:text-base">%</span>
                            <span class="col-8 text-right oscr_data font-semibold text-sm lg:text-base" id="oscr_p2"></span>
                        </div>
                        <h4 class="mt-4 mb-3 font-weight-bold a-result">Convertion Funnel</h4>
                        <div class="mt-3 h-100 a-result">
                            <table class="table" style="height: 70%;">
                                <tbody>
                                    <tr>
                                        <td class="text-left font-weight-bold pb-2">Added To Cart<div class="small oscr_data atc">? sessions</div></td>
                                        <td class="text-right oscr_data atc"></td>
                                        <td class="text-right oscr_data atc"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left font-weight-bold pb-2">Reached Checkout<div class="small oscr_data rc">? sessions</div></td>
                                        <td class="text-right oscr_data rc"></td>
                                        <td class="text-right oscr_data rc"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left font-weight-bold pb-2">Procceeded To Payment<div class="small oscr_data ptp">? sessions</div></td>
                                        <td class="text-right oscr_data ptp"></td>
                                        <td class="text-right oscr_data ptp"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left font-weight-bold pb-2">Session Converted<div class="small sessions oscr_data">? sessions</div></td>
                                        <td class="text-right oscr_data sessions"></td>
                                        <td class="text-right oscr_data sessions"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="ps" fromrecord="views" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col-8 card-title font-weight-bold">Online Store Sessions</h4>
                            <div class="col text-right">
                                <a href="<?= base_url('reports/online_store_sessions/'.$token);?>" class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="row a-result">
                            <span class="col-6 font-weight-bold text-sm lg:text-base" id="tv_total">0</span>
                            <span class="col text-right font-weight-bold text-sm lg:text-base" id="tv_percent">0</span>
                        </div>                        
                        <div class="chart-card mt-1 a-result">
                            <canvas class="chart" id="onlineStore" style="height: 335px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="oblr" fromrecord="order" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Orders By Location</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("reports/orders_by_location/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="oblr_filter" id="oblr_filter">
                                <option value="city" selected>City</option>
                                <option value="prov">Province</option>
                                <option value="reg">Region</option>
                            </select>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="ordersByLocation" style="height: 300px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="os" fromrecord="order" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100">
                    <div class="card-body" style="min-height: 365px;">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Order & Sales</h4>
                            <div class="col text-right">
                                <a href='<?= base_url("reports/order_and_sales/$token");?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="mt-3 a-result">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="text-left"><small>Total Orders</small></td>
                                        <td class="text-right font-semibold text-sm lg:text-base" id="os_total_orders"></td>
                                        <td class="text-right font-semibold text-sm lg:text-base" id="os_to_percent"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left"><small>Total Sales</small></td>
                                        <td class="text-right font-semibold text-sm lg:text-base" id="os_total_sales"></td>
                                        <td class="text-right font-semibold text-sm lg:text-base" id="os_ts_percent"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>    
                        <div class="chart-card a-result">
                            <canvas class="chart" id="orderAndSales" style="height: 302px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="rbl" fromrecord="sales" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Revenue By Location</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("revenue_by_store/by_location/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="rbl_filter" id="rbl_filter">
                                <option value="city" selected>City</option>
                                <option value="prov">Province</option>
                                <option value="reg">Region</option>
                            </select>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="revenueByLocation" style="height: 300px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="rbbr" fromrecord="sales" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Revenue By Branch</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("revenue_by_store/by_branch/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="revenueByBranch" style="height: 363px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="rbsr" fromrecord="sales" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Revenue By Store</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("revenue_by_store/index/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="revenueByStore" style="height: 363px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tps" fromrecord="sales" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col-7 card-title font-weight-bold">Top Products Sold</h4>
                            <div class="col text-right">
                                <a href="<?= base_url('reports/top_products_sold/'.$token);?>" class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="topProductsSold" style="height: 363px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tacr" fromrecord="views" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col-8 card-title font-weight-bold">Total Abandoned Carts</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("abandoned_carts/index/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="row a-result">
                            <span id="abandoned_percent" class="col-8 font-weight-bold text-sm lg:text-base">%</span>
                            <!-- <span class="col text-right" id="total_ac_amount" style="font-size: 2.4vh;">0%</span> -->
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="abandonedcart-chart" style="height: 338px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="to" fromrecord="order" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Total Orders</h4>
                            <div class="col text-right">
                                <a href="<?= base_url('reports/total_orders/'.$token);?>" class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="row a-result">
                            <span id="to_total_orders" class="col-6 font-weight-bold text-sm lg:text-base"></span>
                            <span class="col text-right text-sm lg:text-base"><b id="to_percent"></b></span>
                        </div>
                        <div class=" a-result">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="text-left"><small>Total Orders Fulfilled</small></td>
                                        <td class="text-right"><small id="to_f_total"></small></td>
                                        <td class="text-right" id="to_f_percent"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left"><small>Total Orders Delivered</small></td>
                                        <td class="text-right"><small id="to_d_total"></small></td>
                                        <td class="text-right" id="to_d_percent"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="totalOrders" style="height: 286px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="po" fromrecord="order" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col-7 card-title font-weight-bold">Total Pending Orders</h4>
                            <div class="col text-right">
                                <a href="<?= base_url('reports/pending_orders/'.$token);?>" class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="poChart" style="height: 363px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tsr" fromrecord="sales" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold">Total Sales</h4>
                            <div class="col text-right">
                                <a href='<?=base_url("total_sales/index/$token")?>' class="view_report_link text-blue-500 small">View Report</a>
                            </div>
                        </div>
                        <div class="row a-result">
                            <span id="total_sales_amount" class="col-7 font-weight-bold text-sm lg:text-base">P </span>
                            <span class="col text-right font-semibold text-sm lg:text-base" id="total_sales_percent"></span>
                        </div>
                        <div class=" a-result">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-left"><small>Online Purchases</small></td>
                                        <td class="text-right font-semibold" id="op_total"></td>
                                        <td class="text-right" id="op_percent"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-left"><small>Manual Purchases</small></td>
                                        <td class="text-right font-semibold" id="mp_total"></td>
                                        <td class="text-right" id="mp_percent"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="totalSales"  style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="chart-load" class="col-xl-4 col-md-6 row m-0 align-self-stretch" style="height: 0px; overflow:hidden; visibility: hidden;">
                <div class="card mb-2 w-100" style="min-height: 365px;">
                    <div class="card-body">
                        <div class="row">
                            <h4 class="col card-title font-weight-bold text-gray-400">Chart</h4>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-gray-400" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                        </div>
                        <div class="chart-card a-result">
                            <canvas class="chart" id="chartLoad" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row chart-item hidden">
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

        <div class="row chart-item hidden">
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
<script>
    var report_charts = Object.values(<?= $this->loginstate->get_report_access(); ?>);
    var last_report_chart = report_charts[report_charts.length - 1];
    var nullDateRecords = [];
</script>
<script defer src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
<script defer src="<?=base_url('assets/js/dashboard.js');?>"></script>


