<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_billing_merchant extends CI_Model {
  public function get_shop_options() {
    $query="SELECT * FROM sys_shops WHERE status = 1";
    return $this->db->query($query)->result_array();
  }

  public function get_options() {
		$query="SELECT * FROM sys_payment_type WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

  public function getShopCode($shopid){
		$sql = "SELECT shopcode FROM sys_shops WHERE status=1 and id=?";
		$data = array($shopid);
		$res = $this->db->query($sql,$data);
		$r = $res->row_array();
		return $r["shopcode"];
	}

  public function get_billing_merchant_table($search){
    $requestData = $_REQUEST;
		$totalData = 0;
		$totalFiltered = 0;

		$columns = array(
            // 0 => 'Id',
            0 => 'trandate',
            1 => 'billcode',
            2 => 'totalamount',
            3 => 'processfee',
            4 => 'netamount',
            5 => 'shopname',
            6 => 'paystatus'
		);

    $sql=" SELECT a.*, b.shopname as shopname, c.accountname, c.accountno, c.bankname,
        @branch_name := (SELECT branchname FROM sys_branch_profile WHERE id = a.branchid AND status = 1) as branch_name
        FROM sys_billing_merchant as a
        LEFT JOIN sys_shops as b ON a.shopid = b.id
        LEFT JOIN sys_shop_account c ON a.shopid = c.sys_shop AND a.branchid = c.branch_id AND c.status = 1
        WHERE a.status = 1";


    if($search->status != 1){
      switch ($search->status) {
        case 2:
          $sql .= " AND a.paystatus = 'On Process'";
          break;
        case 3:
          $sql .= " AND a.paystatus = 'Settled'";
          break;
        default:
          // code...
          break;
      }
    }

    if($search->search != ''){
      $billcode = $this->db->escape($search->search);
      $sql .= " AND a.billcode = $billcode";
    }

    if($search->shop != ''){
      $shopid = $this->db->escape(en_dec('dec',$search->shop));
      $sql .= " AND a.shopid = $shopid";
    }

    if($search->branch != 'null'){
      $branchid = $this->db->escape($search->branch);
      $sql .= " AND a.branchid = $branchid";
    }

    if($search->from != '' && $search->to != ''){
      $from =  $this->db->escape(format_date_reverse_dash($search->from));
      $to = $this->db->escape(format_date_reverse_dash($search->to));
      $sql .= " AND DATE(a.trandate) BETWEEN $from AND $to";
    }

    if($this->loginstate->get_access()['seller_access'] == 1 && $this->session->sys_shop_id != ''){
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.shopid = $sys_shop_id";
    }

    if(($this->loginstate->get_access()['seller_branch_access'] == 1 || $this->loginstate->get_access()['food_hub_access'] == 1) && $this->session->sys_shop_id != ''){
      $branchid = $this->db->escape($this->session->branchid);
      $sys_shop_id = $this->db->escape($this->session->sys_shop_id);
      $sql .= " AND a.branchid = $branchid AND a.shopid = $sys_shop_id";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $branch_name = ($row['branch_name'] != NULL) ? $row['branch_name'] : 'Main';
      $shopname = ($row['per_branch_billing'] == 1) ? $row['shopname']."(".$branch_name.")" : $row['shopname'];



      $nestedData[] = readable_date($row["trandate"]);
      $nestedData[] = $row["billcode"];
      $nestedData[] = '<span class = "float-right">'.number_format($row["processfee"],2).'</span>';
      $nestedData[] = $shopname;
      switch ($row["paystatus"]) {
          case 'On Process':
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
          case 'Settled':
              $nestedData[] = '<center><label class="badge badge-success"> Settled</label></center>';
              break;
          case 'Unsettled':
              $nestedData[] = '<center><label class="badge badge-info"> Partially Settled</label></center>';
              break;

          default:
              $nestedData[] = '<center><label class="badge badge-info"> On Process</label></center>';
              break;
      }

      $delete_btn = "";
      if($this->loginstate->get_access()['billing']['admin_view'] == 1){
        $delete_btn =
        '
          <a class = "dropdown-item btn_delete"
            id = "'.en_dec('en',$row['id']).'"
            data-shopid = "'.en_dec('en',$row['shopid']).'"
            data-branchid = "'.en_dec('en',$row['branchid']).'"
            data-encrypted_id = "'.en_dec('en',$row['id']).'"
            data-billcode = "'.$row['billcode'].'"
            data-payref = "'.$row['payref'].'"
            data-name = "Billing '.$shopname.' - '.readable_date($row["trandate"]).'"
            data-trandate = "'.$row["trandate"].'"
          >
            Delete
          </a>
        ';
      }

      $nestedData[] =
      '
        <div class="dropdown text-center">
          <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
          <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            <a class="dropdown-item btn_view"
              id="'.$row['id'].'"
              data-encrypted_id = "'.en_dec('en',$row['id']).'"
              data-ref_num="'.$row['billcode'].'"
              data-processfee = "'.$row["processfee"].'"
              data-accountname = "'.$row['accountname'].'"
              data-accountno = "'.$row['accountno'].'"
              data-bankname = "'.$row['bankname'].'"
              data-branch_name = "'.$row['branch_name'].'"
            >
              <i class="fa fa-ye" aria-hidden="true"></i> View
            </a>
            '.$delete_btn.'
          </div>
        </div>
      ';

      $data[] = $nestedData;
    }
    $json_data = array(

      "filters"         => $requestData,
      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_merchant_billing($trandate,$shopid){
    $trandate = $this->db->escape($trandate);
    $sql = "SELECT * FROM sys_billing_merchant WHERE DATE(trandate) = $trandate AND status = 1";
    if($shopid){
      $shopid = $this->db->escape($shopid);
      $sql .= " AND shopid = $shopid";
    }

    return $this->db->query($sql);
  }

  public function process_billing_merchant($trandate,$shopid = false){
    // set process billing
    $count = 0;
    $todaydate = todaytime();
    $escape_trandate = $this->db->escape($trandate);
    // get process billing
    $process = $this->get_merchant_billing($trandate,$shopid);
    if($process->num_rows() == 0){
      // FETCH ALL SHOPS
      $shop_sql = "SELECT a.id as shopid, a.shopcode, a.shopname, a.shippingfee, a.billing_type,
        a.is_preorder, a.generatebilling as billing_perbranch,
        a.prepayment, b.ratetype as shoprate_type, b.rateamount as shoprate
        FROM sys_shops a
        LEFT JOIN sys_shop_rate b ON a.id = b.syshop
        WHERE a.status = 1 AND b.status = 1";

      // FETCH ALL SALES ORDERS
      $sales_order_sql = "SELECT a.id as so_id, a.sys_shop, a.reference_num, a.total_amount, a.srp_totalamount,
        a.total_amount_w_voucher, a.payment_portal_fee, a.delivery_amount, a.payment_id, a.payment_amount,
        a.date_ordered, a.date_shipped, a.date_confirmed, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        b.delivery_amount as shippingfee, b.daystoship, b.daystoship_to, c.branchid
        FROM app_sales_order_details a
        LEFT JOIN app_order_details_shipping b ON a.reference_num = b.reference_num AND a.sys_shop = b.sys_shop
        LEFT JOIN app_order_branch_details c ON a.reference_num = c.order_refnum AND a.sys_shop = c.shopid
        WHERE a.status = 1 AND b.status = 1 AND c.enabled = 1 AND
          a.order_status = 's' AND a.payment_status = 1 AND a.riderPaymentMethod = 'CASH' AND a.srp_totalamount > 0
          AND DATE(a.date_shipped) = $escape_trandate";

      // FETCH ALL SALES ORDER LOGS
      $sales_order_logs_sql = "SELECT a.order_id, a.product_id, a.quantity, a.amount, a.srp_amount,
        a.total_amount, a.exrate_n_to_php, a.exrate_php_to_n, a.currency,
        a.refcom_amount, a.refcom_totalamount, a.refcom_rate,
        b.sys_shop, b.cat_id, b.itemid, b.itemname, b.otherinfo, b.price, b.admin_isset,
        b.disc_ratetype, b.disc_rate
        FROM app_sales_order_logs a
        LEFT JOIN sys_products b ON a.product_id = b.Id
        WHERE a.status = 1 AND a.srp_amount > 0 AND a.order_id
        IN (SELECT * FROM (SELECT id FROM app_sales_order_details WHERE order_status = 's' AND riderPaymentMethod = 'CASH' AND DATE(date_shipped) = $escape_trandate AND payment_status = 1 AND status = 1) as so)";

      // IF SHOPID IS NOT FALSE
      if($shopid){
        $shopid = $this->db->escape($shopid);
        $shop_sql .= " AND a.id = $shopid";
        $sales_order_sql .= " AND a.sys_shop = $shopid";
        $sales_order_logs_sql .= " AND b.sys_shop = $shopid";
      }

      $shop_query = $this->db->query($shop_sql);
      $sales_order_query = $this->db->query($sales_order_sql);
      $sales_order_logs_query = $this->db->query($sales_order_logs_sql);

      if($sales_order_query->num_rows() > 0){
        // start cron logs
        $cron_id = $this->model_cron->set_cron_logs(insert_cron_data('Cron merchant billing (COD CASH)', 'For Accounts Billing'));
        $billno = $this->getNewBillNo();
        $sales_order_arr = $sales_order_query->result_array();
        $shops_arr = $shop_query->result_array();
        $sales_order_logs_arr = $sales_order_logs_query->result_array();
        $shops = filter_unique($sales_order_arr,'sys_shop'); // filter unique shop
        // print_r($shops);
        // die();
        // $main_so = filter_so_main($sales_order_arr);
        // $branch_so = filter_so_branch($sales_order_arr);

        $billing_batch = array();
        $billing_logs_batchdata = array();

        if(count((array)$shops) > 0){
          // LOOP THRU EACH SHOPS
          foreach($shops as $shop){
            $skey = array_search($shop['sys_shop'],array_column($shops_arr,'shopid')); // shop index key
            $shopdetails = $shops_arr[$skey];
            $shop_ratetype = $shopdetails['shoprate_type'];
            $shop_rate = $shopdetails['shoprate'];
            $main_so = filter_so_main($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            $branch_so = filter_so_branch($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
            // BILLING PER BRANCH
            if($shopdetails['billing_perbranch'] == 1){
              // MAIN
              if(count((array)$main_so) > 0){
                $total_amount = 0;
                $total_comrate = 0;
                $delivery_amount = 0;
                $total_fee = 0;
                $netamount = 0;
                foreach($main_so as $mkey => $main){
                  if($main['sys_shop'] == $shop['sys_shop']){
                    $total_amount += floatval($main['srp_totalamount']);
                    $delivery_amount += floatval($main['shippingfee']);
                    // $delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                    // ? floatval($main['shippingfee']) : 0;

                    $main_logs = filter_logs_main($sales_order_logs_arr,array('order_id' => $main['so_id']));
                    if(count((array)$main_logs) > 0){
                      $new_total_amount = 0;
                      foreach($main_logs as $lkey => $mlogs){
                        $fee = 0;
                        $comrate = 0;
                        if(floatval($mlogs['refcom_totalamount']) > 0){
                          $comrate = round(floatval($mlogs['total_amount'] * floatval($mlogs['refcom_rate'])),2);
                          $total_comrate += round($comrate,2);
                        }

                        // $new_total_amount += floatval($mlogs['total_amount']);

                        // with product rate
                        if($mlogs['admin_isset'] == 1){
                          if(floatval($mlogs['disc_rate']) > 0){
                            if($mlogs['disc_ratetype'] == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? round($mlogs['total_amount'] * $mlogs['disc_rate'],2)
                              : round(($mlogs['srp_amount'] * $mlogs['disc_rate']) * $mlogs['quantity'],2);
                            }else{
                              $fee = $mlogs['disc_rate'] * $mlogs['quantity'];
                            }
                            // $fee = ($mlogs['disc_ratetype'] == 'p')
                            // ? ($mlogs['total_amount'] * $mlogs['disc_rate']) // for percentate rate type
                            // : $mlogs['disc_rate'] * $mlogs['quantity'];  // for fix rate type
                            $rate = $mlogs['disc_rate'];
                            $ratetype = $mlogs['disc_ratetype'];

                          }else{
                            if($shop_ratetype == 'p'){
                              $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                              ? round($shop_rate * $mlogs['total_amount'],2)
                              : round(($shop_rate * $mlogs['srp_amount']) * $mlogs['quantity'],2);
                            }else{
                              $fee = $shop_rate * $mlogs['quantity'];
                            }
                            // $fee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $mlogs['total_amount']
                            // : $shop_rate * $mlogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                        // without product rate then fetch shop
                        }else{
                          if($shop_ratetype == 'p'){
                            $fee = (floatval($mlogs['refcom_totalamount']) > 0)
                            ? round($shop_rate * $mlogs['total_amount'],2)
                            : round(($shop_rate * $mlogs['srp_amount']) * $mlogs['quantity'],2);
                          }else{
                            $fee = $shop_rate * $mlogs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $mlogs['total_amount']
                          // : $shop_rate * $mlogs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                        // $total_fee += $fee;
                        // $total_fee += (floatval($mlogs['refcom_totalamount']) > 0) ? round(($fee - $comrate),2) : $fee;
                        $total_fee += $fee;

                        $billing_logs_data = array(
                          "shopid" => $shop['sys_shop'],
                          "branchid" => 0, // main shop
                          "productid" => $mlogs['product_id'],
                          "orderid" => $mlogs['order_id'],
                          "trandate" => $trandate,
                          "srp_totalamount" => $mlogs['srp_amount'] * $mlogs['quantity'],
                          "srp_amount" => $mlogs['srp_amount'],
                          "quantity" => $mlogs['quantity'],
                          "processtype" => $ratetype,
                          "processrate" => $rate,
                          "processfee" => $fee,
                        );

                        $billing_logs_batchdata[] = $billing_logs_data;
                      }
                    }

                    // if($new_total_amount > 0){
                    //   $temp_total_amount = $new_total_amount;
                    // }
                    //
                    // $total_amount += $temp_total_amount;

                  }
                }

                $total_commission = $total_fee + $total_comrate;
                $total_whtax = ($total_commission * c_whtax_percentage());
                $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                $remarks = 'Settlement for merchant billing (COD CASH) transactions dated '.$trandate;
                $count++;
                $billing_data = array(
                  "billno" => $billno,
                  "billcode" => $billcode,
                  "shopid" => $shop['sys_shop'],
                  "branchid" => 0, // main shop
                  "per_branch_billing" => 1,
                  "trandate" => $trandate,
                  "remarks" => $remarks,
                  "processdate" => $todaydate,
                  "processfee" => $total_fee,
                  "paystatus" => "On Process",
                  "status" => 1
                );

                $billing_batch[] = $billing_data;
              }

              // BRANCH
              if(count((array)$branch_so) > 0){
                $branches = filter_unique($branch_so,'branchid');
                foreach($branches as $branch){
                  $branch_total_amount = 0;
                  $branch_total_comrate = 0;
                  $branch_delivery_amount = 0;
                  $branch_total_fee = 0;
                  $branch_netamount = 0;

                  foreach($branch_so as $bkey => $b_so){
                    if($branch['sys_shop'] == $b_so['sys_shop'] && $branch['branchid'] == $b_so['branchid']){
                      // $total_amount += floatval($b_so['total_amount_w_voucher']);
                      $branch_total_amount += floatval($b_so['srp_totalamount']);
                      $branch_delivery_amount += floatval($b_so['shippingfee']);
                      // $branch_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                      // ? floatval($b_so['shippingfee']) : 0;

                      $branch_logs = filter_logs_branch($sales_order_logs_arr,array('order_id' => $b_so['so_id']));
                      if(count((array)$branch_logs) > 0){
                        $new_branch_total_amount = 0;
                        foreach($branch_logs as $blkey => $blogs){
                          $bfee = 0;
                          $bcomrate = 0;
                          if(floatval($blogs['refcom_totalamount']) > 0){
                            $bcomrate = round(floatval($blogs['total_amount'] * floatval($blogs['refcom_rate'])),2);
                            $branch_total_comrate += round($bcomrate,2);
                          }

                          // with product rate
                          if($blogs['admin_isset'] == 1){
                            if(floatval($blogs['disc_rate']) > 0){
                              if($blogs['disc_ratetype'] == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? round($blogs['total_amount'] * $blogs['disc_rate'],2)
                                : round(($blogs['srp_amount'] * $blogs['disc_rate']) * $blogs['quantity'],2);
                              }else{
                                $bfee = $blogs['disc_rate'] * $blogs['quantity'];
                              }
                              // $bfee = ($blogs['disc_ratetype'] == 'p')
                              // ? ($blogs['total_amount'] * $blogs['disc_rate']) // for percentate rate type
                              // : $blogs['disc_rate'] * $blogs['quantity'];  // for fix rate type
                              $rate = $blogs['disc_rate'];
                              $ratetype = $blogs['disc_ratetype'];
                            }else{
                              if($shop_ratetype == 'p'){
                                $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                                ? round($shop_rate * $blogs['total_amount'],2)
                                : round(($shop_rate * $blogs['srp_amount']) * $blogs['quantity'],2);
                              }else{
                                $bfee = $shop_rate * $blogs['quantity'];
                              }
                              // $bfee = ($shop_ratetype == 'p')
                              // ? $shop_rate * $blogs['total_amount']
                              // : $shop_rate * $blogs['quantity'];
                              $rate = $shop_rate;
                              $ratetype = $shop_ratetype;
                            }

                          // without product rate then fetch shop
                          }else{
                            if($shop_ratetype == 'p'){
                              $bfee = (floatval($blogs['refcom_totalamount']) > 0)
                              ? round($shop_rate * $blogs['total_amount'],2)
                              : round(($shop_rate * $blogs['srp_amount']) * $blogs['quantity'],2);
                            }else{
                              $bfee = $shop_rate * $blogs['quantity'];
                            }
                            // $bfee = ($shop_ratetype == 'p')
                            // ? $shop_rate * $blogs['total_amount']
                            // : $shop_rate * $blogs['quantity'];
                            $rate = $shop_rate;
                            $ratetype = $shop_ratetype;
                          }

                          // $branch_total_fee += (floatval($blogs['refcom_totalamount']) > 0) ? round(($bfee - $bcomrate),2) : $bfee;
                          $branch_total_fee += $bfee;

                          $billing_logs_data = array(
                            "shopid" => $shop['sys_shop'],
                            "branchid" => $b_so['branchid'], // branchid
                            "productid" => $blogs['product_id'],
                            "orderid" => $blogs['order_id'],
                            "trandate" => $trandate,
                            "srp_totalamount" => $blogs['srp_amount'] * $blogs['quantity'],
                            "srp_amount" => $blogs['srp_amount'],
                            "quantity" => $blogs['quantity'],
                            "processtype" => $ratetype,
                            "processrate" => $rate,
                            "processfee" => $bfee,
                          );
                          $billing_logs_batchdata[] = $billing_logs_data;
                        }
                      }

                      // if($new_branch_total_amount > 0){
                      //   $temp_branch_total_amount = $new_branch_total_amount;
                      // }
                      //
                      // $branch_total_amount += $temp_branch_total_amount;
                    }
                  }

                  $total_branch_commission = $branch_total_fee + $branch_total_comrate;
                  $total_branch_whtax = $total_branch_commission * c_whtax_percentage();
                  $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                  $billcode = $billcode.$branch['branchid'];
                  $remarks = 'Settlement for merchant billing (COD CASH) transactions dated '.$trandate;
                  $branch_netamount = $branch_total_amount - ($branch_total_fee + $branch_total_comrate);
                  $count++;
                  $billing_data = array(
                    "billno" => $billno,
                    "billcode" => $billcode,
                    "shopid" => $shop['sys_shop'],
                    "branchid" => $branch['branchid'],
                    "per_branch_billing" => 1,
                    "trandate" => $trandate,
                    "remarks" => $remarks,
                    "processdate" => $todaydate,
                    "processfee" => $branch_total_fee,
                    "paystatus" => "On Process",
                    "status" => 1
                  );

                  $billing_batch[] = $billing_data;
                }
              }

            // BILLING PER SHOP
            }else{
              $pershop_so = filter_so($sales_order_arr,array('sys_shop' => $shop['sys_shop']));
              if(count((array)$pershop_so) > 0){
                $pershop_total_amount = 0;
                $pershop_total_comrate = 0;
                $pershop_delivery_amount = 0;
                $pershop_total_fee = 0;
                $pershop_netamount = 0;

                foreach($pershop_so as $pskey => $pershop){
                  // $pershop_total_amount += floatval($pershop['total_amount_w_voucher']);
                  $pershop_total_amount += floatval($pershop['srp_totalamount']);
                  $pershop_delivery_amount += floatval($pershop['shippingfee']);
                  // $pershop_delivery_amount += ($shopdetails['billing_type'] == 1 && $shopdetails['prepayment'] == 0)
                  // ? floatval($pershop['shippingfee']) : 0;

                  $pershop_logs = filter_so_logs($sales_order_logs_arr,array('order_id' => $pershop['so_id']));
                  if(count((array)$pershop_logs) > 0){
                    $new_pershop_total_amount = 0;
                    foreach($pershop_logs as $psl_key => $ps_logs){
                      $pershop_fee = 0;
                      $pershop_comrate = 0;
                      if(floatval($ps_logs['refcom_totalamount']) > 0){
                        $pershop_comrate = round(floatval($ps_logs['total_amount'] * floatval($ps_logs['refcom_rate'])),2);
                        $pershop_total_comrate += round($pershop_comrate,2);
                      }

                      // with product rate
                      if($ps_logs['admin_isset'] == 1){
                        if(floatval($ps_logs['disc_rate']) > 0){
                          if($ps_logs['disc_ratetype'] == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? round($ps_logs['disc_rate'] * $ps_logs['total_amount'],2)
                            : round(($ps_logs['srp_amount'] * $ps_logs['disc_rate']) * $ps_logs['quantity'],2);
                          }else{
                            $pershop_fee = $ps_logs['disc_rate'] * $ps_logs['quantity'];
                          }
                          // $fee = ($ps_logs['disc_ratetype'] == 'p')
                          // ? ($ps_logs['total_amount'] * $ps_logs['disc_rate']) // for percentate rate type
                          // : $ps_logs['disc_rate'] * $ps_logs['quantity'];  // for fix rate type
                          $rate = $ps_logs['disc_rate'];
                          $ratetype = $ps_logs['disc_ratetype'];
                        }else{
                          if($shop_ratetype == 'p'){
                            $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                            ? round($shop_rate * $ps_logs['total_amount'],2)
                            : round(($shop_rate * $ps_logs['srp_amount']) * $ps_logs['quantity'],2);
                          }else{
                            $pershop_fee = $shop_rate * $ps_logs['quantity'];
                          }
                          // $fee = ($shop_ratetype == 'p')
                          // ? $shop_rate * $ps_logs['total_amount']
                          // : $shop_rate * $ps_logs['quantity'];
                          $rate = $shop_rate;
                          $ratetype = $shop_ratetype;
                        }

                      // without product rate then fetch shop
                      }else{
                        if($shop_ratetype == 'p'){
                          $pershop_fee = (floatval($ps_logs['refcom_totalamount']) > 0)
                          ? round($shop_rate * $ps_logs['total_amount'],2)
                          : round(($shop_rate * $ps_logs['srp_amount']) * $ps_logs['quantity'],2);
                        }else{
                          $pershop_fee = $shop_rate * $ps_logs['quantity'];
                        }
                        // $fee = ($shop_ratetype == 'p')
                        // ? $shop_rate * $ps_logs['total_amount']
                        // : $shop_rate * $ps_logs['quantity'];
                        $rate = $shop_rate;
                        $ratetype = $shop_ratetype;
                      }

                      // $pershop_total_fee += (floatval($ps_logs['refcom_totalamount']) > 0) ? round(($pershop_fee - $pershop_comrate),2) : $pershop_fee;
                      $pershop_total_fee += $pershop_fee;

                      $billing_logs_data = array(
                        "shopid" => $shop['sys_shop'],
                        "branchid" => 0, // main shop
                        "productid" => $ps_logs['product_id'],
                        "orderid" => $ps_logs['order_id'],
                        "trandate" => $trandate,
                        "srp_totalamount" => $ps_logs['srp_amount'] * $ps_logs['quantity'],
                        "srp_amount" => $ps_logs['srp_amount'],
                        "quantity" => $ps_logs['quantity'],
                        "processtype" => $ratetype,
                        "processrate" => $rate,
                        "processfee" => $pershop_fee,
                      );

                      $billing_logs_batchdata[] = $billing_logs_data;
                    }
                  }

                  // if($new_pershop_total_amount > 0){
                  //   $temp_pershop_total_amount = $new_pershop_total_amount;
                  // }
                  //
                  // $pershop_total_amount += $temp_pershop_total_amount;
                }

                $pershop_total_commission = $pershop_total_fee + $pershop_total_comrate;
                $pershop_total_whtax = $pershop_total_commission * floatval(c_whtax_percentage());
                $billcode = $this->generateBillCode($billno,$shopdetails["shopid"]);
                $remarks = 'Settlement for merchant billing (COD CASH) transactions dated '.$trandate;
                $pershop_netamount = $pershop_total_amount - ($pershop_total_fee + $pershop_total_comrate);
                $count++;
                $billing_data = array(
                  "billno" => $billno,
                  "billcode" => $billcode,
                  "shopid" => $shop['sys_shop'],
                  "branchid" => 0, // main shop
                  "per_branch_billing" => 1,
                  "trandate" => $trandate,
                  "remarks" => $remarks,
                  "processdate" => $todaydate,
                  "processfee" => $pershop_total_fee,
                  "paystatus" => "On Process",
                  "status" => 1
                );

                $billing_batch[] = $billing_data;
              }
            }


          } // END LOOP THRU SHOP

          // print_r($billing_logs_batchdata);

          // SET BILLING BATCH
          if(count($billing_batch) > 0){
            $this->db->insert_batch('sys_billing_merchant',$billing_batch);
          }

          // SET BILLING LOGS BATCH
          if(count($billing_logs_batchdata) > 0){
            $this->db->insert_batch('sys_billing_merchant_logs',$billing_logs_batchdata);
          }
        }

        // End of cron logs
        $cron_status = ($cron_id != '') ? 'successful' : 'failed';
        $this->model_cron->update_cron_logs(update_cron_data($cron_status),$cron_id);

      }

      // update process billing
      $this->update_process_billing($trandate);
    }

    return $count;
  }

  // BILLNO , BILLCODE
  public function getNewBillNo(){
		$sql = "SELECT billno FROM sys_idkey WHERE status=1";
		$res = $this->db->query($sql);
		$r = $res->row_array();

		$billno = $r["billno"];
		$billno ++;

		$checker=1;

		if($checker==1)
		{
			$sql = "SELECT billno FROM sys_billing WHERE status=1 AND billno=?";
			$data = array($billno);
			$res = $this->db->query($sql,$data);
			$r = $res->row_array();

			if($r["billno"] != "")
			{
				$billno++;
			}
			else
			{
				$sql = "UPDATE sys_idkey SET billno=? WHERE status=1";
				$data = array($billno);
				$this->db->query($sql,$data);

				$checker = 0;
			}

		}

		return $billno;

	}//close getNewBillNo

  public function generateBillCode($billno,$shopid){
		$todaydate = today();
		$todayref = str_replace('-','', $todaydate);
		if($billno < 100000)
		{
			$billno = $billno+100000;
		}

		$shopcode = $this->getShopCode($shopid);
		$shopcode = str_replace(' ','', $shopcode);
		$billcode = strtoupper($shopcode).$todayref.$billno;

		return $billcode;
	}

  // CRON LOGS
  public function set_process_billing($date){
    $data = array('billing_date' => $date, 'is_processed' => 0);
    $this->db->insert('sys_billing_processed',$data);
  }

  public function update_process_billing($date,$status = 1){
    $data = array('is_processed' => $status);
    $this->db->update('sys_billing_processed',$data,array('billing_date' => $date));
  }

}
