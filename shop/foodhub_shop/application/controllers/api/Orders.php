<?php
  class Orders extends CI_Controller {
    function __construct() {
      parent::__construct();
      date_default_timezone_set("Asia/Manila");
      $this->load->library('ConversionRate');
    }

    //START API FOR JCW SO CODE
    protected function idno_for_ecommerce(){

      if (ENVIRONMENT == "production") {
        return '60413';
      }else if (ENVIRONMENT == "testing") {
        return '60413';
      }else{
        return '60413';
      }
    }

    protected function sk_api_url(){
      // return "http://35.173.0.77/dev/jcw/SKShop/save_skshop_so";

      // return "https://jcworldwideinc.com/pb/SKShop/save_skshop_so";
      if (ENVIRONMENT == "production") {
        return "https://jcworldwideinc.com/pb/Onlineshops/save_so";
      }else if (ENVIRONMENT == "testing") {
        return "http://35.173.0.77/dev/jcw/Onlineshops/save_so";
      }else{
        return "http://35.173.0.77/dev/jcw/Onlineshops/save_so";
      }
    }

    protected function sk_api_key(){

      if (ENVIRONMENT == "production") {
        return "SUPERPROXY";
      }else if (ENVIRONMENT == "testing") {
        return "SUPERPROXY";
      }else{
        return "SUPERPROXY";
      }
    }
    //END API FOR JCW SO CODE

    //START API FOR JC REFERRAL CODE
    protected function jc_api_url() {
      if (ENVIRONMENT == "production") {
        return "https://thedarkhorse.ph/tdh/api/JCReferralAPI/process_referral_code";
      }else if (ENVIRONMENT == "testing") {
        return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/process_referral_code";
      }else{
        return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/process_referral_code";
      }
    }

    protected function jc_api_key() {

      if (ENVIRONMENT == "production") {
        return "JCCPCOVID19!";
      }else if (ENVIRONMENT == "testing") {
        return "JCCPCOVID19!";
      }else{
        return "JCCPCOVID19!";
      }
    }
    //END API FOR JC REFERRAL CODE

    // START API FOR JC FULFILLMENT
    protected function jc_fulfillment_url(){

      if (ENVIRONMENT == "production") {
        return "http://35.173.0.77/dev/jc_fulfillment/shop_orders/add_data";
      }else if (ENVIRONMENT == "testing") {
        return "http://35.173.0.77/dev/jc_fulfillment/shop_orders/add_data";
      }else{
        return "http://35.173.0.77/dev/jc_fulfillment/shop_orders/add_data";
      }
    }

    protected function jc_fulfillment_update_url(){

      if (ENVIRONMENT == "production") {
        return "http://35.173.0.77/dev/jc_fulfillment/shop_orders/update_payment_data";
      }else if (ENVIRONMENT == "testing") {
        return "http://35.173.0.77/dev/jc_fulfillment/shop_orders/update_payment_data";
      }else{
        return "http://35.173.0.77/dev/jc_fulfillment/shop_orders/update_payment_data";
      }
    }

    protected function jcfskey(){

      if (ENVIRONMENT == "production") {
        return "JCFULFILLMENT";
      }else if (ENVIRONMENT == "testing") {
        return "JCFULFILLMENT";
      }else{
        return "JCFULFILLMENT";
      }
    }
    // END API FOR JC FULFILLMENT

    //START API FOR JC COPPERMASK DR
    protected function idno_for_coppermask(){

      if (ENVIRONMENT == "production") {
        return 'CMASK';
      }else if (ENVIRONMENT == "testing") {
        return 'CMASK';
      }else{
        return 'CMASK';
      }
    }

    protected function jc_cm_api_url() {

      if (ENVIRONMENT == "production") {
        return "https://thedarkhorse.ph/tdh/api/JCReferralAPI/process_drsales_cm";
      }else if (ENVIRONMENT == "testing") {
        return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/process_drsales_cm";
      }else{
        return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/process_drsales_cm";
      }
    }

    protected function jc_cm_api_key() {

      if (ENVIRONMENT == "production") {
        return "JCCOPPER#MASK!";
      }else if (ENVIRONMENT == "testing") {
        return "JCCOPPER#MASK!";
      }else{
        return "JCCOPPER#MASK!";
      }
    }

    protected function totok_shipping_api_key(){

      if (ENVIRONMENT == "production") {
        return "TOKTOKSHIPPINGFEE";
      }else if (ENVIRONMENT == "testing") {
        return "TOKTOKSHIPPINGFEE";
      }else{
        return "TOKTOKSHIPPINGFEE";
      }
    }

    protected function email_signature_key(){
      // return 'CPSHOPEMAIL2021';
      if (ENVIRONMENT == "production") {
        return 'CPSHOPEMAIL2021';
      }else if (ENVIRONMENT == "testing") {
        return 'CPSHOPEMAIL2021';
      }else{
        return 'CPSHOPEMAIL2021';
      }
    }

    //END API FOR JC COPPERMASK DR

    function getOrders(){
      $start = $this->input->get('start', TRUE);
      $length = $this->input->get('length', TRUE);
      $page = $this->input->get('page', TRUE);
      $active_user = $this->session->userdata("active_franchise");
      $userId = $this->input->get("userId", TRUE) ? $this->input->get("userId", TRUE) : $active_user->userId;

      $isParent = $userId == "All" ? true : false;
      $filter = array(
        'userId' => $userId == "All" ? $active_user->userId : $userId,
        'startDate' => date($this->input->get('startDate', TRUE)),
        'endDate' => date($this->input->get('endDate', TRUE)),
        'search_input' => $this->input->get('reference_num', TRUE),
        'status' => $this->input->get('status', TRUE),
        'parent' => $isParent
      );
      // $data = array(
      //   "draw" => $draw,
      //   "recordsTotal" => $this->ordersModel->getOrders(null, null,$filter)->num_rows(),
      //   "recordsFiltered" => $this->ordersModel->getOrders(null, null, $filter)->num_rows(),
      //   "data" => $this->ordersModel->getOrders($start,$length,$filter)->result()
      // );
      $res = $this->ordersModel->getOrders($start,$length,$filter)->result();
      $total = count($res);
      ($page - 1) * 5;

      $data = array(
        //slice array for number per page
        // 'data' => array_slice($products, $page, 5),
        'data' => $this->ordersModel->getOrders($start,$length,$filter)->result(),
        //divide total count of product and round it up for numbers of page needed
        'totalRecords' => $total > 10 ? round($total / 10, 0, PHP_ROUND_HALF_UP) : 1,
        'page' => $page,
      );
      echo json_encode($data);
    }

    function ordersTable(){
      $active_user = $this->session->userdata("active_franchise");
      $userId = sanitize($this->input->post('userId'));
      $query = $this->ordersModel->ordersTable($active_user->userId);
      echo json_encode($query);
    }

    function getOrderDetails(){
      $draw = $this->input->get('draw', TRUE);
      $start = $this->input->get('start', TRUE);
      $length = $this->input->get('length', TRUE);
      $orderId = $this->input->get('orderId', TRUE);

      $data = array(
        "draw" => $draw,
        "recordsTotal" => $this->ordersModel->getOrderDetails(null, null,$orderId)->num_rows(),
        "recordsFiltered" => $this->ordersModel->getOrderDetails(null, null, $orderId)->num_rows(),
        "data" => $this->ordersModel->getOrderDetails($start,$length,$orderId)->result(),
        "orderId" => $orderId
      );
      echo json_encode($data);
    }

    function checkoutOrder() {
      $checkoutItems = $this->session->userdata("cart") ? $this->session->userdata("cart") : [];
      $checkoutDetails = $this->input->post("checkout_details");
      $voucherDetails = json_decode($this->input->post('vouchers'), true);
      // print_r($voucherDetails);
      // die();
      $areaId = $checkoutDetails["areaid"];
      $latitude = $this->input->post('latitude');
      $longitude = $this->input->post('longitude');
      $total_amount = 0;
      $total_shipping = 0;
      $voucherAmount = 0.00;
      // print('<pre>'.print_r($checkoutItems).'</pre>');
      // die();

      //Revalidate all checkout details before proceeding, check if cart is not empty
      if($checkoutItems != []){
        //Loop each shop package on the cart
        foreach($checkoutItems as $index => $shop) {
          // for conversion rate
          $this->conversionrate->logConversionRate('ptp',$shop['shopid']);

          //if instance is for jcww
          // if(ini() == "jcww") { x - start
          //   $totamt = $this->session->userdata("total_amount");
          //   $areaId = $this->model->getAreaById($areaId);
          //   $shippingPerShop = array();
          //   $shippingPerShop["shippingfee"] = getShippingRateJCWW($areaId, $totamt);
          //   $shippingPerShop["from_dts"] = "1";
          //   $shippingPerShop["to_dts"] = "3";
          // }
          // //if instance is for jcww
          // else if(ini() == "coppermask") {
          //   $shippingPerShop = array();
          //   $shippingPerShop["shippingfee"] = "180.00";
          //   $shippingPerShop["from_dts"] = "5";
          //   $shippingPerShop["to_dts"] = "8";
          // }
          // //if instance is not jcww
          // //NEW SHIPPING LOGIC
          // else {
            $productTotal = $this->getProductTotals($shop["items"]);
            // if shipping fee is already calculated
            if(isset($this->session->shipping_per_shop) && $this->session->shipping_per_shop != []){
              $shippingPerShop = $this->session->shipping_per_shop[$shop['shopid']];

            // in case if shipping fee is still not calculated. this is just a fail safe it should never happen
            }else{
              $shippingPerShop = $this->calculateShipping($shop["shopid"], $shop["items"], $checkoutDetails["citymunCode"], $productTotal["total_weight"], $productTotal["total_amount"],$latitude,$longitude);
            }

          // } x - end
          //OLD SHIPPING LOGIC
          // else
          //   $shippingPerShop = $this->model->getShippingPerShop($areaId,$shop["shopid"])->row_array();
          //OLD SHIPPING LOGIC

          //validate shipping fee per shop and check if there is shipping fee setup
          if($shippingPerShop != null) {

            //NEW SHIPPING LOGIC
            $checkoutItems[$shop["shopid"]]["shippingfee"] = $shippingPerShop["shippingfee"];
            $checkoutItems[$shop["shopid"]]["shopdts"] = $shippingPerShop["from_dts"];
            $checkoutItems[$shop["shopid"]]["shopdts_to"] = $shippingPerShop["to_dts"];
            $checkoutItems[$shop["shopid"]]["items"] = $shippingPerShop["items"];

            //OLD SHIPPING LOGIC
            // $checkoutItems[$shop["shopid"]]["shippingfee"] = $shippingPerShop["sf"];
            // $checkoutItems[$shop["shopid"]]["shopdts"] = $shippingPerShop["dts"];
            //OLD SHIPPING LOGIC


            // $total_shipping += $shippingPerShop["sf"];

            //loop all items in the shop to get total order amount
            foreach($shop["items"] as $key => $item) {
              if($item["available"] == 1)
                $total_amount += $item["total_amount"];
              else
                unset($checkoutItems[$shop["shopid"]]["items"][$key]);
            }

            // empty shop items
            if(count($checkoutItems[$shop["shopid"]]["items"]) == 0){
              unset($checkoutItems[$index]);
            }else{
              //get total shipping of all shops in the cart
              $total_shipping += $shippingPerShop["shippingfee"];
            }

          }
          //remove shop from cart if there is no shipping setup for the shop
          else {
            unset($checkoutItems[$shop["shopid"]]);
          }
        }

        //set total shipping fee
        $checkoutDetails["shipping_fee"] = $total_shipping;
        //set total order amount in session
        $this->session->set_userdata("total_amount", $total_amount);
      }
      else {
        $data = array("status" => 204, "message" => "Session Expired.");
        echo json_encode($data);
        die();
      }


      //get referral code from session
      $referralCode = $this->input->post("referral_code");
      $verified = true;
      $notValidItem = array();
      //generate random sequenec number for orderid
      $orderId = $this->uuid->v4_formatted();

      //generate order reference number
      $orderRefNum = strtoupper(uniqid(order_ref_prefix()));
      while ($this->ordersModel->isRefNumExist($orderRefNum) == '1') {
        $orderRefNum = strtoupper(uniqid(order_ref_prefix()));
      }

      $user_id = "0";
      if($this->session->userdata("user_id") != null)
        $user_id = $this->session->userdata("user_id2");

      //prepare order details for insertion in app_order_details table
      $orderSO = array(
        "order_id" => $orderId,
        "order_so_no" => order_so_ref_prefix().str_pad($this->ordersModel->so_no(), 8, '0', STR_PAD_LEFT),
        "reference_num" => $orderRefNum,
        "paypanda_ref" => 0, //payment reference number
        "user_id" => $user_id, //userid of customer
        "name" => $this->session->userdata("name"),
        "conno" => $this->session->userdata("conno"),
        "email" => trim($this->session->userdata("email")),
        "address" => $this->session->userdata("address") . ", " .$checkoutDetails["citymunDesc"],
        "notes" => $this->session->userdata("landmark"),
        "areaid" => $checkoutDetails["areaid"],
        "provCode" => $this->session->userdata("provCode"),
        "regCode" => $this->session->userdata("regCode"),
        "citymunCode" => $this->session->userdata("citymunCode"),
        "postalcode" => $this->session->userdata("postal"),
        "latitude" => $latitude,
        "longitude" => $longitude,
        "admin_drno" => 0,
        "total_amount" => $this->session->userdata("total_amount"),
        "order_status" => "p",
        "payment_status" => 0,
        "payment_method" => $checkoutDetails["payment_method"],
        "date_ordered" => date("Y-m-d H:i:s"),
        "delivery_amount" => $checkoutDetails["shipping_fee"]
      );

      //if cart is no empty
      if($checkoutItems != []){
        //loop each items in shop to check if item is
        foreach($checkoutItems as $shop)
          foreach($shop["items"] as $key => $item) {
            $verified_item = $this->ordersModel->verify_item($item["productid"]);
            if(!$verified_item){
              $verified = false;
              unset($shop["items"][$key]);
              array_push($notValidItem, $item);
            }
          }
      }else{
        $data = array("status" => 204, "message" => "Session Expired.");
        echo json_encode($data);
        die();
      }

      //if voucherDetails is not empty run some validations
      if (sizeof($voucherDetails) > 0) {
        $this->load->model('VouchersModel');

        // if logged in as an online reseller (JC), do not go through
        if (!empty($this->session->userdata("user_id")) && $this->session->userdata("user_type") == "JC") {
          $data = array("status" => 200, "message" => "Cannot use vouchers if you are an online reseller.");
          echo json_encode($data);
          die();
        }

        // if has referral session, do not go through
        // if ($this->session->userdata('referral') != '' || $referralCode != '') {
        //   $data = array("status" => 200, "message" => "Cannot use vouchers if you are already have referral codes.");
        //   echo json_encode($data);
        //   die();
        // }

        //loop through and check if all vouchers are still valid, if there's at least 1
        //that's not valid anymore do not go through
        //invalid vouchers are not existing, expired, or with claim_status = 2 or 4
        $invalid_codes = array();
        foreach($voucherDetails as $key => $value) {
          foreach($value['vouchers'] as $key2 => $value2) {
            $voucher = $this->VouchersModel->getAvailableVoucher($value2['shopid'], $value2['vcode']);
            if (empty($voucher) || (strtotime(today()) > strtotime($voucher['date_valid'])) || in_array($voucher['claim_status'], ['2','4'])) {
              array_push($invalid_codes, $value2['vcode']);
            }
          }
        }

        if (sizeof($invalid_codes) > 0) {
          $data = array("status" => 204, "message" => "There's at least one voucher that is not valid anymore. " . implode(", ", $invalid_codes));
          echo json_encode($data);
          die();
        }
      }

      if($verified){
        //insert order details to app_order_details
        $insertOrder = $this->ordersModel->insertOrder($orderSO);

        if($insertOrder > 0) {

            $shopItems = array();
            $voucher_total = 0;
            $total_payable_amount_w_voucher = 0.00;
            $toktok_api_data = $this->session->toktok_api_shipping_logs;
            foreach($checkoutItems as $shop) {
              $subTotalPerShop = 0.00;
              foreach($shop["items"] as $item) {
                $orderItems = array(
                  "order_id" => $orderId,
                  "sys_shop" => $item["shop"],
                  "product_id" => $item["productid"],
                  "quantity" => $item["quantity"],
                  "amount" => $item["price"],
                  "total_amount" => floatval($item["quantity"]) * floatval($item["price"]),
                );

                $this->ordersModel->insertOrderItem($orderItems);
                $subTotalPerShop += floatval($item["total_amount"]);
              }//end of inner foreach
              $checkoutItems[$shop["shopid"]]['subTotalPerShop'] = $subTotalPerShop;
              //saving in app_order_details_shipping table
              //NEW SHIPPING LOGIC
              $shippingDetails = array(
                "reference_num" => $orderRefNum,
                "sys_shop" => $shop["shopid"],
                "delivery_amount" => $checkoutItems[$shop["shopid"]]["shippingfee"],
                "daystoship" => $checkoutItems[$shop["shopid"]]["shopdts"],
                "daystoship_to" => $checkoutItems[$shop["shopid"]]["shopdts_to"]
              );
              $this->ordersModel->insertShippingPerShop($shippingDetails);

              // for toktok shipping api if available
              if(isset($this->session->toktok_api_shipping_logs) && $this->session->toktok_api_shipping_logs != []){
                $toktok_api_data[$shop['shopid']]['refnum'] = $orderRefNum;
              }

              //OLD SHIPPING LOGIC
              // $shippingPerShop = $this->model->getShippingPerShop($checkoutDetails["areaid"],$shop["shopid"])->row_array();
              // if($shippingPerShop != null) {
              //   $shippingDetails = array(
              //     "reference_num" => $orderRefNum,
              //     "sys_shop" => $shop["shopid"],
              //     "delivery_amount" => $shippingPerShop["sf"],
              //     "daystoship" => $shippingPerShop["dts"]
              //   );
              //   $this->ordersModel->insertShippingPerShop($shippingDetails);
              // }
              //OLD SHIPPING LOGIC

              // else {
              //   $shippingDetails = array(
              //     "reference_num" => $orderRefNum,
              //     "sys_shop" => $shop["shopid"],
              //     "delivery_amount" => $shop["shippingfee"],
              //     "daystoship" => $shop["shopdts"]
              //   );
              // }
              // $this->ordersModel->insertShippingPerShop($shippingDetails);

              // VOUCHER LOGIC
              if (sizeof($voucherDetails) > 0) {
                // check if shop has voucher details


                $vKey = array_search($shop['shopid'], array_column($voucherDetails, 'shopid'));

                if ($vKey !== false) {
                  $voucherSubTotal = 0.00; // voucherSubTotal amount per shop

                  $voucherData = array(
                    'shopid' => $shop['shopid'],
                    'order_ref_num' => $orderRefNum,
                    'amount' => 0,
                    'payment_type' => '',
                    'payment_refno' => '',
                    'date_created' => todaytime()
                  );

                  // insert each individueal voucher data in app_order_voucher
                  foreach($voucherDetails[$vKey]['vouchers'] as $key => $value) {
                    // push vcode to shop list of vouchers
                    $checkoutItems[$shop["shopid"]]['vouchers'][] = $value['vcode'];
                    $voucherSubTotal += floatval($value['amount']);

                    $voucherData['amount'] = $value['amount'];
                    $voucherData['payment_type'] = 'Shoplink';
                    $voucherData['payment_refno'] = $value['vcode'];

                    $this->ordersModel->insertOrderVoucher($voucherData);

                    // update v_wallet_available claim_status = 2
                    $this->VouchersModel->updateClaimStatus($value['shopid'], $value['vcode'], 2);
                  }

                  // If vouchers do not cover up all the payable, means that there's still payable in paypanda. Insert another row for paypanda in app_order_voucher
                  $voucher_total += floatval($voucherSubTotal);
                  $remainingPayable = $subTotalPerShop - $voucherSubTotal;
                  if ($remainingPayable > 0) {
                    $voucherData['amount'] = $remainingPayable;
                    $voucherData['payment_type'] = 'paypanda';
                    $voucherData['payment_refno'] = ''; // empty payment refno to be updated upon postback

                    $this->ordersModel->insertOrderVoucher($voucherData);
                  }
                  $discounted = floatval($subTotalPerShop) - floatval($voucherSubTotal);
                  $checkoutItems[$shop["shopid"]]['voucherSubTotal'] = $voucherSubTotal;
                  $checkoutItems[$shop["shopid"]]["discounted"] = ($discounted < 0) ? 0.00 : $discounted;
                  $temp_value = floatval($checkoutItems[$shop["shopid"]]['subTotalPerShop']) - floatval($voucherSubTotal);
                  $total_payable_amount_w_voucher += ($temp_value < 0) ? 0 : $temp_value;
                  $voucherAmount += floatval($voucherSubTotal);
                }else{
                  $total_payable_amount_w_voucher += floatval($checkoutItems[$shop["shopid"]]['subTotalPerShop']);
                }
              }else{
                $total_payable_amount_w_voucher += floatval($checkoutItems[$shop["shopid"]]['subTotalPerShop']);
              } // END VOUCHER LOGIC

            }//end of outer foreach

            //Check if referral code has value
            if($referralCode != null && $referralCode != '') {
              //Prepare referral code details for insertion
              // $referral_total_amount = floatval($this->session->userdata("total_amount")) - $voucher_total;
              $referral_total_amount = $total_payable_amount_w_voucher;
              $referral_total_amount = ($referral_total_amount < 0) ? 0.00 : $referral_total_amount;
              $referralDetails = array (
                "referral_code" => $referralCode,
                "order_reference_num" => $orderRefNum,
                "soldto" => strtoupper($checkoutDetails["name"]),
                "total_amount" => $referral_total_amount,
                "payment_status" => 0,
                "date_ordered" => date("Y-m-d H:i:s"),
                "is_processed" => 0
              );
              //Insert referal code in app_referral_codes table
              $this->ordersModel->insertReferralCode($referralDetails);
            }

            // $total_amount_w_voucher = (floatval($this->session->userdata("total_amount")) - floatval($voucherAmount));
            $total_amount_w_voucher = $total_payable_amount_w_voucher;
            $total_amount_w_voucher = ($total_amount_w_voucher < 0) ? 0.00 : $total_amount_w_voucher;
            $pay_paypanda = array(
              "merchant_id" => $this->paypanda->get_merchant_key(),
              "reference_number" => $orderRefNum,
              "amount_to_pay" => $total_amount_w_voucher + floatval($checkoutDetails["shipping_fee"]),
              "signature" => $this->paypanda->generate_signature($orderRefNum, $total_amount_w_voucher + floatval($checkoutDetails["shipping_fee"]))
            );

            // INSERT APP ORDER BRANCH DETAILS
            $branch_order_details = $this->session->temp_branch_orders;
            $new_arr = array();
            foreach($branch_order_details as $temp){
              $temp['order_refnum'] = $orderRefNum;
              $temp['created_at'] = date("Y-m-d H:i:s");
              $new_arr[] = $temp;
            }
            $this->ordersModel->set_app_order_branch_details_batch($new_arr);

            // INSERT TOKTOK SHIPPING API LOGS
            if(isset($this->session->toktok_api_shipping_logs) && $this->session->toktok_api_shipping_logs != []){
              $this->ordersModel->set_toktok_shipping_api_logs_batch($toktok_api_data);
            }

            $this->destroyCart();
            $order = $this->ordersModel->getByRefNum($orderRefNum);
            $order['referral_code'] = $referralCode;
            $order["shopItems"] = $checkoutItems;
            $order["voucherAmount"] = $voucherAmount;

            // START JC FULFILLMENT API
            if(c_jcfulfillment_shopidno() != '' && in_array(order_ref_prefix(),c_allowed_jcfulfillment_prefix()) && $branch_order_details[0]['branchid'] == 0){
              $totalamt = $total_amount_w_voucher + floatval($checkoutDetails["shipping_fee"]);
              $shopidno = c_jcfulfillment_shopidno();

              $order['signature'] = en_dec("en",md5($shopidno.$this->jcfskey().round($totalamt)));
              $order['reference_num'] = $order['reference_num'];
              $order['total_amount'] = $totalamt;
              $order['idno'] = $shopidno;

              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $this->jc_fulfillment_url());
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($order));
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              $server_output = curl_exec($ch);

              // INSERT JC FULFILLMENT POSBACK LOGS
              $api_jcfulfillment_logs_data = array(
                "refnum" => $orderRefNum,
                "response" => $server_output,
                "type" => "checkout",
                "date_created" => today()
              );
              $this->ordersModel->set_jc_fulfillment_logs($api_jcfulfillment_logs_data);

              curl_close ($ch);
            }
            // END JC FULFILLMENT API

            $this->send_email("order_pending_email_send", $order, "Waiting for payment");

            // REGISTER USER UPON CHECKOUT
            if(allow_registration() == 1 && !isset($this->session->user_id) && isset($checkoutDetails['register_upon_checkout']) && $checkoutDetails['register_upon_checkout'] == 1){
              $this->load->model('auth/model_authentication');
              $isExist = $this->model_authentication->validate_username($checkoutDetails['email']);
              if($isExist->num_rows() == 0){
                $password = generate_random_password();
                $hash_password = password_hash($password,PASSWORD_BCRYPT);
                $sys_data = array(
                  "username" => $checkoutDetails['email'],
                  "password" => $hash_password,
                  "active" => 1
                );

                $inserted = $this->model_authentication->set_sys_customer_auth($sys_data);
                if($inserted['status'] === false){
                  $data = array("status" => 200, "success" => false, "message" => "Registration Failed.");
                  generate_json($data);
                  exit();
                }

                $customer_data = array(
                  "user_id" => $inserted['user_id'],
                  "first_name" => ucfirst($checkoutDetails['name']),
                  "last_name" => "",
                  "email" => $checkoutDetails['email'],
                  "conno" => $checkoutDetails['conno'],
                  "address1" => $checkoutDetails['address']
                );

                $registered = $this->model_authentication->set_app_customers($customer_data);
                if($registered === false){
                  $data = array("status" => 200, "success" => false, "message" => "Unable to save customer information.");
                  echo json_encode($data);
                  exit();
                }

                $app_customer_address_data = array(
                  "customer_id" => $inserted['user_id'],
                  "receiver_name" => ucfirst($checkoutDetails['name']),
                  "receiver_contact" => $checkoutDetails['conno'],
                  "address" => $checkoutDetails['address'],
                  "landmark" => "",
                  "postal_code" => 0,
                  "region_id" => 0,
                  "province_id" => 0,
                  "municipality_id" => 0,
                  "brgy_id" => 0,
                  "updated_at" => todaytime(),
                  "created_at" => todaytime(),
                  "default_add" => 1
                );

                $this->model_authentication->set_address($app_customer_address_data);

                $this->session->set_userdata('user_id', $inserted['user_id']);
                $this->session->set_userdata('user_id2', $inserted['user_id']);
                $this->session->set_userdata('user_type', get_company_name());
                $this->session->set_userdata('username', $checkoutDetails['email']);
                $this->session->set_userdata('last_seen', todaytime());
                $this->session->set_userdata('fname', ucwords(strtolower($checkoutDetails['name'])));
                $this->session->set_userdata('lname', "");
                $this->session->set_userdata('address', ucwords(strtolower($checkoutDetails['address'])));
                $this->session->set_userdata('city', "");
                $this->session->set_userdata('email', $checkoutDetails['email']);
                $this->session->set_userdata('conno', $checkoutDetails['conno']);

                $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
                $this->email->to($checkoutDetails['email']);
                $this->email->subject("Your Registration was Successful");
                $email_data['username'] = $checkoutDetails['email'];
                $email_data['fullname'] = $checkoutDetails['name'];
                $email_data['password'] = $password;
                $this->email->message($this->load->view("emails/register_upon_checkout", $email_data, TRUE));
                $this->email->send();
              }
            }
            // $this->order_pending_email_send($order, "Waiting for payment", null);
            $data = array("status" => 200, "message" => "Successfully added checked out.", "data" => $pay_paypanda);
        }else{
          $data = array("status" => 201, "message" => "Something went wrong in inserting data.");
        }
      }else{
        $data = array("status" => 201, "message" => "There's a product that is not valid anymore.", "data" => $notValidItem);
      }
      echo json_encode($data);
    }

    public function paypanda_postback(){
      $this->load->library('Sms');
      $this->load->library('Postback_logs');

      $reference_number = $this->input->post('reference_number');
      $paypanda_refno = $this->input->post('paypanda_refno');
      $paid_amount = $this->input->post('paid_amount');
      $status = $this->input->post('payment_status');
      $signature = $this->input->post('signature');
      $service_code = $this->input->post('service_code');
      $payment_portal_fee = $this->input->post('payment_portal_fee');
      $payment_portal_fee = $payment_portal_fee !== null ? $payment_portal_fee : 0.00;
      $trigger = $this->input->post('trigger');
      $manual_payment_method = $this->input->post('payment_method');
      $payment_notes = $this->input->post('payment_notes');
      $latitude = $this->input->post('latitude');
      $longitude = $this->input->post('longitude');
      $signature = ($signature == "FREE") ? $this->paypanda->validate_manual_signature($reference_number,$paypanda_refno,$status, $paid_amount, $trigger) : $signature;
      // Initialize log data
      $logs = $this->postback_logs->logs;

      $logs['activity'] = 'postback attempt';
      $logs['details'] = 'Attempting to update payment record';
      $logs['action_by'] = $this->input->post('action_by');
      $logs['reference_number'] = $reference_number;
      $logs['data'] = json_encode($this->input->post());
      // save attempt
      $this->postback_logs->save_log($logs);

      // Checking of signature for manual payment
      if ($trigger !== null && $trigger == 'manual_payment') {
        if ($signature !== $this->paypanda->validate_manual_signature($reference_number, $paypanda_refno, $status, $paid_amount, $trigger)) {
          $message = "Invalid signature for manual payment";
          $logs['activity'] = 'postback failed';
          $logs['details'] = $message;
          $this->postback_logs->save_log($logs);

          echo json_encode(array("status" => "failed", "message" => $message));
          http_response_code(200);
          die();
        }

      // Checking of signature for paypanda
      } else {
        //validate paypanda signature
        if($signature !== $this->paypanda->validate_signature($reference_number, $paypanda_refno, $status, $paid_amount)){
          $message = "Invalid signature for paypanda payment";
          $logs['activity'] = 'postback failed';
          $logs['details'] = $message;
          $this->postback_logs->save_log($logs);

          echo json_encode(array("status" => "failed", "message" => $message));
          http_response_code(200);
          die();
        }
      }

      // if passed signature validation
      //get order details by reference number
      $order = $this->ordersModel->getByRefNum($reference_number);
      // $order_sales = $this->ordersModel->get_app_order_sales($reference_number);
      $postback_logs = $this->ordersModel->get_postback_process_order($reference_number);
      if($order == null || empty($order)) {
        $message = "Order reference number not found.";
        $logs['activity'] = 'postback failed';
        $logs['details'] = $message;
        $this->postback_logs->save_log($logs);

        echo json_encode(array("status" => "failed", "message" => $message));
        http_response_code(200);
        die();
      }

      $vouchers = $this->ordersModel->getOrderVoucher($order["reference_num"]);
      // if there are vouchers in the order check if there's at least 1 that has claim_status = 4, if yes skip the remaining postback process
      $has_invalid_voucher = false;
      if (sizeof($vouchers) > 0) {
        $this->load->model('VouchersModel');

        // loop through voucher records, if payment_type is gogome validate in gogome_voucher v_wallet_available if claim_status == 4
        foreach($vouchers as $key => $value) {
          if ($value['payment_type'] == 'Shoplink') {
            $voucher = $this->VouchersModel->getAvailableVoucher($value['shopid'], $value['payment_refno']);
            if ($voucher['claim_status'] == '4') {
              $has_invalid_voucher = true;
              break;
            }
          }
        }
      }

      if($has_invalid_voucher) {
        $message =  "Invalid voucher(s) has been detected in the order. Refund must be made if payment has been settled";
        $logs['activity'] = 'postback failed';
        $logs['details'] = $message;
        $this->postback_logs->save_log($logs);

        echo json_encode(array("status" => "failed", "message" => $message));
        http_response_code(200);
        die();
      }

      switch($status){
        case "S":
            if(count((array)$postback_logs) == 0){
              try{
                $this->ordersModel->set_postback_process_order(array("refnum" => $reference_number));
              }catch(Exception $e){
                $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
              }

              $postback_logs['referral_status']        = 500;
              $postback_logs['voucher_status']         = 500;
              $postback_logs['split_order_status']     = 500;
              $postback_logs['jc_api_status']          = 500;
              $postback_logs['jcww_api_status']        = 500;
              $postback_logs['branch_sms_status']      = 404;
              $postback_logs['branch_email_status']    = 404;
              $postback_logs['seller_sms_status']      = 500;
              $postback_logs['seller_email_status']    = 500;
              $postback_logs['update_order_status']    = 500;
              $postback_logs['jc_fulfillment_status']  = 500;
              $postback_logs['client_email_status']    = 500;
              $postback_logs['success']                = 500;
              $postback_logs['shop_order_failed'] = array(
                "insert_sales" => array(),
                "insert_saleslogs_productid" => array(),
                "update_amount" => array(),
                "insert_branch" => array(),
                "update_prod_inv" => array(),
                "update_inv_branch" => array(),
                "send_branch_sms" => array(),
                "send_branch_email" => array()
              );
            }else{
              $postback_logs['shop_order_failed'] = json_decode($postback_logs['shop_order_failed']);
            }
            //Check if order is not yet paid or order is not in app sales order
            if($order["payment_status"] != 1){
              try{
                //initialize variables
                $orderDRItem = array();
                $shopsArr = array();
                $shopItems = array();

                //get order items
                $orderItems = $this->ordersModel->getOrderDetails(null,null,$order["order_id"])->result_array();
                $sales_order_items = $orderItems;

                //Get referral code records for sending
                $record = $this->ordersModel->getReferralRecord($reference_number)->row_array();

                //loop through order items
                foreach($orderItems as $item){

                  //get shop details of each item
                  $shopDetails = $this->ordersModel->getShopDetails($item["sys_shop"]);
                  //get shipping details
                  $shippingdts = $this->ordersModel->get_daystoship($reference_number, $item["sys_shop"]);
                  //if new shop, create shop details
                  if(empty($shopItems[$item["sys_shop"]])) {
                    $shopItems[$item["sys_shop"]] = array(
                      "shopname" => $shopDetails["shopname"],
                      "shopcode" => $shopDetails["shopcode"],
                      "shopemail" => $shopDetails["email"],
                      "shopmobile" => $shopDetails["mobile"],
                      "shopdts" => $shippingdts["daystoship"],
                      "shopdts_to" => $shippingdts["daystoship_to"],
                      "shopemail" => $shopDetails["email"],
                      "shippingfee" => $shippingdts["delivery_amount"]
                    );
                    $shopItems[$item["sys_shop"]]["items"] = array();
                  }

                  //push product item into shop object
                  array_push($shopItems[$item["sys_shop"]]["items"], array(
                          "productid" => $item["product_id"],
                          "itemname" => $item["itemname"],
                          "unit" => $item["otherinfo"],
                          "quantity" => $item["quantity"],
                          "price" => $item["amount"],
                          "primary_pics" => $item["primary_pics"]
                  ));

                  //START SPECIAL HANDLING FOR JCWW
                  //get shopcode to check if item belongs to JCWW
                  $shopCode = $this->ordersModel->getShopCode($item["sys_shop"]);
                  if($shopCode == 'JCW' || $shopCode == 'JCWW') {
                    $invID = $this->ordersModel->getInventoryID($item["product_id"]);
                    array_push($orderDRItem, array(
                      "itemid" => $invID,
                      "qty" => $item["quantity"],
                      "price" => $item["amount"],
                      "discamt" => 0,
                      "disctype" => 0,
                      "subtotal" => $item["total_amount"],
                    ));
                  }
                  //END SPECIAL HANDLING FOR JCWW

                  //START SPECIAL HANDLING FOR COPPERMASK JC
                  //get shopcode to check if item belongs to COPPERMASK JC
                  $shopCode = $this->ordersModel->getShopCode($item["sys_shop"]);
                  if($shopCode == 'COP') {
                    //remove "COP_" from itemid before saving
                    $invID = str_replace("COP_","",$this->ordersModel->getInventoryID($item["product_id"]));
                    array_push($orderDRItem, array(
                      "idno" => $this->idno_for_coppermask(),
                      "trandate" => date("Y-m-d"),
                      "drno" => 0,
                      "itemlocid" => 1,
                      "itemid" => $invID,
                      "qty" => $item["quantity"],
                      "price" => $item["amount"],
                      "total" => $item["total_amount"],
                      "process" => 2,
                      "olddrno" => "none",
                      "status" => 1
                    ));
                  }
                  //END SPECIAL HANDLING FOR COPPERMASK JC

                  //store all shops that belong to the order
                  array_push($shopsArr, $item["sys_shop"]);

                  //Update product inventory
                  // $this->updateProductInventory($item);
                }

                //get distict shops in the order
                $shopsArr = array_unique($shopsArr);

                $jcww_sono = 0;
                $jc_cm_drno = 0;
                $api_call = 0; // use to check if the api call for jcww and copper mask is already called

                // ----------------------------------------
                // JCWW AND COPPER MASK API USED TO BE HERE
                // ----------------------------------------

                $order["admin_sono"] = $jcww_sono;
                $order["payment_status"] = "Paid";
                $order["payment_method"] = $trigger == 'manual_payment'
                      ? $manual_payment_method
                      : $order["payment_method"];
                $order["payment_date"] = date("Y-m-d H:i:s");
                $order["shopItems"] = $shopItems;

                //prepare payment details for updating app_referral_codes table
                $referral_data = array(
                  "payment_status" => 1,
                  "payment_date" => date("Y-m-d H:i:s")
                );
                //update record in app_referral_codes to paid
                $this->ordersModel->updateReferenceCode($reference_number, $referral_data);

                //START SPECIAL HANDLING FOR JC REFERRAL CODES API

                if($postback_logs['referral_status'] == 500){
                  try{
                    //check if referral code record exist
                    if($record != null && $record != "") {
                      $ref_codes = array();
                      $ref_codes_details = array();

                      //prepare referral code details for API
                      array_push($ref_codes, array(
                        "idno" => strtoupper($record["referral_code"]),
                        "order_reference_num" => $record["order_reference_num"],
                        "soldto" => $record["soldto"],
                        "totalamount" => $record["total_amount"],
                        "payment_status" => 1,
                        "date_ordered" => $record["date_ordered"],
                        "payment_date" => $record["payment_date"],
                        "processed_date" => date('Y-m-d H:i:s'),
                        "is_processed" => 1,
                        "status" => 1
                      ));

                      //get order items
                      $recordArr = $this->ordersModel->getOrderLogs($reference_number)->result_array();

                      //loop thru the order items
                      foreach($recordArr as $record_details) {

                        //prepare referral code logs array for API
                        array_push($ref_codes_details, array(
                          "idno" => strtoupper($record["referral_code"]),
                          "order_reference_num" => $record["order_reference_num"],
                          "qty" => $record_details["quantity"],
                          "itemid" => $this->ordersModel->getInventoryID($record_details["product_id"]),
                          "price" => $record_details["amount"],
                          "totalamount" => $record_details["total_amount"],
                          "comrate" => 0,
                          "comamount" => 0,
                          "trandate" => date('Y-m-d H:i:s'),
                          "date_ordered" => $record["date_ordered"],
                          "payment_date" => $record["payment_date"],
                          "is_processed" => 1,
                          "status" => 1
                        ));
                      }

                      $processDate = date('Y-m-d H:i:s');

                      //prepare referral code data for API
                      $referralData = array (
                        "signature" => en_dec_jc_api("en", md5($processDate.$this->jc_api_key())),
                        "date" => $processDate,
                        "referral_codes" => $ref_codes,
                        "referral_codes_details" => $ref_codes_details
                      );

                      //API CURL CODE
                      $postvars = http_build_query($referralData);

                      $ch = curl_init();
                      curl_setopt($ch, CURLOPT_URL, $this->jc_api_url());
                      curl_setopt($ch, CURLOPT_POST, count($referralData));
                      curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                      $server_output = curl_exec($ch);

                      // INSERT RESPONSE TO API REFERRAL LOGS
                      $api_referral_logs_data = array(
                        "refnum" => $reference_number,
                        "response" => $server_output,
                        "date_created" => today()
                      );
                      $this->ordersModel->set_api_referral_logs($api_referral_logs_data);

                      if ($server_output === false) {
                          $info = curl_getinfo($ch);
                          curl_close($ch);
                          die('error occured during curl exec. Additional info: ' . var_export($info));
                      }
                      curl_close($ch);
                      $response =  json_decode($server_output);

                      //if successful response, update app_referral_codes.is_processed = 1
                      if($response != null && $response->success) {
                        //Referral Code as processed
                        $referral_data = array(
                          "processed_date" => date('Y-m-d H:i:s'),
                          "is_processed" => 1
                        );
                        $this->ordersModel->updateReferenceCode($reference_number, $referral_data);

                        //Get distributor details for email sending
                        $dis_array = json_decode(en_dec_jc_api("dec", $response->data_res));

                          //Send email if details are not empty
                          if($dis_array != null) {

                            $order["idno"] = strtoupper($record["referral_code"]);

                            $dis_name = "";
                            if($dis_array->fname != null && $dis_array->fname != "")
                              $dis_name .= $dis_array->fname." ";
                            if($dis_array->mname != null && $dis_array->mname != "")
                              $dis_name .= $dis_array->mname." ";
                            if($dis_array->lname != null && $dis_array->lname != "")
                              $dis_name .= $dis_array->lname;

                            $order["dis_name"] = strtoupper($dis_name);

                            // send sms to distributor
                            if($dis_array->conno != null && $dis_array->conno != '') {
                              $order["dis_mobile"] = $dis_array->conno;
                              $this->send_sms($order);
                            }

                            // send email to distributor
                            if($dis_array->email != null && $dis_array->email != '') {
                              $order["dis_email"] = strtolower($dis_array->email);
                              $this->send_email("order_distributor_email_send", $order, "Processing", $paypanda_refno, $shopCode);
                            }
                          }
                      }
                    }
                    //END SPECIAL HANDLING FOR JC REFERRAL CODES API
                    $postback_logs['referral_status'] = 200;
                  }catch(Exception $e){
                    $postback_logs['referral_status'] = 500;
                    $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                  }
                }


                // divide payment portal fee by no of shop
                $payment_portal_fee_per_shop = 0.00;
                if ($payment_portal_fee > 0) {
                  $num_shop = sizeof($shopsArr);
                  $payment_portal_fee_per_shop = (float) $payment_portal_fee / $num_shop;
                }

                // START VOUCHER LOGIC AFTER POSTBACK
                $voucherAmount = 0.00;
                if($postback_logs['voucher_status'] == 500){
                  try{
                    if (sizeof($vouchers) > 0) {
                      // update date_processed column for all voucher data in app_order_voucher that matches order_ref_num
                      $this->ordersModel->processOrderVoucherPayment($order["reference_num"]);

                      // update payment_refno of paypanda records in app_order_voucher that matches order_ref_num
                      $this->ordersModel->updateOrderVoucherPaypanda($order["reference_num"], $paypanda_refno);

                      // loop through voucher records, if payment_type is gogome add amount to voucherAmount
                      // and then process voucher in gogome_voucher db
                      foreach($vouchers as $key => $value) {
                        // $shopItems[$value['shopid']]['voucherSubTotal'] = 0.00;
                        if ($value['payment_type'] == 'Shoplink') {
                          $order['shopItems'][$value['shopid']]['vouchers'][] = $value['payment_refno'];
                          $order['shopItems'][$value['shopid']]['voucherSubTotal'] = isset($order['shopItems'][$value['shopid']]['voucherSubTotal']) ? floatval($order['shopItems'][$value['shopid']]['voucherSubTotal']) + floatval($value['amount']) : floatval($value['amount']);
                          $voucherAmount += floatval($value['amount']);
                          $this->claimVoucher($value['shopid'], $value['payment_refno'], $order['reference_num']);
                        }
                      }
                    }
                    $postback_logs['voucher_status'] = 200;
                  }catch(Exception $e){
                    $postback_logs['voucher_status'] = 500;
                    $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                  }
                }
                // END VOUCHER LOGIC
                $order["voucherAmount"] = $voucherAmount;
                $temp_total_amount = 0;

                $order['payment_option'] = $service_code;
                $order['payment_portal_fee'] = $payment_portal_fee;
                //START SPLIT ORDER LOGIC
                //loop thru each shop present in the order
                if($postback_logs['split_order_status'] == 500){
                  try{
                    foreach($shopsArr as $shop){
                      $shopDetails = $this->ordersModel->getShopDetails($shop);
                      $shopCode = $shopDetails['shopcode'];
                      $invtrans_branch_arr = array();
                      $item_arr = array();
                      //prepare app_sales_order_details array for insertion
                      $salesOrder = array(
                        "sys_shop" => $shop,
                        "reference_num" => $order["reference_num"],
                        "paypanda_ref" => $paypanda_refno,
                        "user_id" => $order["user_id"],
                        "name" => $order["name"],
                        "conno" => $order["conno"],
                        "email" => $order["email"],
                        "address" => $order["address"],
                        "notes" => $order["notes"],
                        "areaid" => $order["areaid"],
                        "provCode" => $order["provCode"],
                        "regCode" => $order["regCode"],
                        "citymunCode" => $order["citymunCode"],
                        "postalcode" => $order["postalcode"],
                        "total_amount" => 0,
                        "order_status" => $order["order_status"],
                        "payment_status" => 1,
                        "payment_date" => date("Y-m-d H:i:s"),
                        "payment_method" => $trigger == 'manual_payment'
                            ? $manual_payment_method
                            : $order["payment_method"],
                        "payment_portal_fee" => $payment_portal_fee_per_shop,
                        "payment_notes" => $payment_notes,
                        "date_ordered" => $order["date_ordered"],
                        "status" => 1
                      );

                      //insert records to app_sales_order_details table
                      if(!in_array($shop,$postback_logs['shop_order_failed']['insert_sales'])){
                        try{
                          $sales_order_id = $this->ordersModel->insertSalesOrder($salesOrder);
                          $postback_logs['shop_order_failed']['insert_sales'][] = $shop;
                        }catch(Exception $e){
                          $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                        }

                      }
                      $total_amount_per_shop = 0;

                      //loop thru each order item
                      foreach($sales_order_items as $item){


                        if($item["sys_shop"] == $shop) {
                          $salesOrderItems = array(
                            "order_id" => $sales_order_id,
                            "product_id" => $item["product_id"],
                            "quantity" => $item["quantity"],
                            "amount" => $item["amount"],
                            "total_amount" => $item["total_amount"],
                            "status" => 1
                          );

                          //insert records to app_sales_order_logs table
                          if(!in_array($item['product_id'],$postback_logs['shop_order_failed']['insert_saleslogs_productid'])){
                            try{
                              $this->ordersModel->insertSalesOrderItem($salesOrderItems);
                              $postback_logs['shop_order_failed']['insert_saleslogs_productid'][] = $item['product_id'];
                            }catch(Exception $e){
                              $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                            }
                          }

                          // for sys_prodduct_invtrans and product update
                          $invtrans = array(
                            "product_id" => $item['product_id'],
                            "quantity" => $item['quantity'],
                            "type" => "Online Purchase",
                            "date_created" => date('Y-m-d H:i:s'),
                            "enabled" => 1
                          );

                          $item_arr[] = $invtrans;

                          // for sys_products_invtrans_branch
                          $intvtrans_branch = array(
                            "sys_shop" => $item["sys_shop"],
                            "product_id" => $item['product_id'],
                            "quantity" => $item['quantity']
                          );
                          $invtrans_branch_arr[] = $intvtrans_branch;

                          //get total order amount per shop.
                          $total_amount_per_shop += $item["total_amount"];
                        }
                      }
                      $order['shopItems'][$shop]['subTotalPerShop'] = $total_amount_per_shop;
                      $temp_total_amount += $total_amount;

                      //update total_amount field in app_sales_order_details
                      $salesOrderTotAmt = array (
                        "total_amount" => $total_amount_per_shop
                      );

                      if(!in_array($shop,$postback_logs['shop_order_failed']['update_amount'])){
                        try{
                          $this->ordersModel->updateSalesOrderAmount($salesOrderTotAmt, $sales_order_id);
                          $postback_logs['shop_order_failed']['update_amount'][] = $shop;
                        }catch(Exception $e){
                          $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                        }
                      }

                      // Branch auto assign
                      // $branches = $this->model->get_shopbranch($shop, 1);
                      $branch = $this->ordersModel->get_app_order_branch_details($order['reference_num'],$shop);
                      $branch = $branch->row_array();
                      if (count((array)$branch) > 0 && $branch['branchid'] != 0) {
                        $branch_assigned_to = $branch;

                        // Assigned branch send sms and email notificaiton
                        if(count((array)$branch_assigned_to) > 0){
                          // $branch_assigned_to = $branch_assigned_to[0];

                          if(!in_array($shop,$postback_logs['shop_order_failed']['insert_branch'])){
                            try{
                              $this->model->set_order_to_branch($branch_assigned_to['branchid'], $order['reference_num'], 'AUTOASSIGN');
                              $postback_logs['shop_order_failed']['insert_branch'][] = $shop;
                            }catch(Exception $e){
                              $this->ordersModel->set_postback_error_logs(array('text' => "single_branch ".$e->message));
                            }
                          }

                          if(!in_array($shop,$postback_logs['shop_order_failed']['update_prod_inv'])){
                            try{
                              $this->updateProductInventory($item_arr,$branch_assigned_to['branchid']);
                              $postback_logs['shop_order_failed']['update_prod_inv'][] = $shop;
                            }catch(Exception $e){
                              $this->ordersModel->set_postback_error_logs(array('text' => "single_branch 2 ".$e->message));
                            }
                          }

                          if(!in_array($shop,$postback_logs['shop_order_failed']['update_inv_branch'])){
                            try{
                              $this->update_invtrans_branch($invtrans_branch_arr,$branch_assigned_to['branchid']);
                              $postback_logs['shop_order_failed']['update_inv_branch'][] = $shop;
                            }catch(Exception $e){
                              $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                            }
                          }

                          $message2 = "Hi ".$branch_assigned_to['branchname'].". New order #".$order['reference_num']." is now paid and ready for processing. Please check your branch portal for details.";

                          if(!in_array($shop,$postback_logs['shop_order_failed']['send_branch_sms'])){
                            try{
                              $this->sms->sendSMS(3, $branch_assigned_to['mobileno'], $message2, 'JC.');
                              $postback_logs['shop_order_failed']['send_branch_sms'][] = $shop;
                              $postback_logs['branch_sms_status'] = 200;
                            }catch(Exception $e){
                              $postback_logs['branch_sms_status'] = 500;
                              $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                            }
                          }

                          $order['branch_assigned_to'] = $branch_assigned_to;

                          if(!in_array($shop,$postback_logs['shop_order_failed']['send_branch_email'])){
                            try{
                              // send  email
                              $this->send_email("assigned_branch_email_send", $order, "Processing", $paypanda_refno, $shop, $shopCode);
                              $postback_logs['shop_order_failed']['send_branch_email'][] = $shop;
                              $postback_logs['branch_email_status'] = 200;
                            }catch(Exception $e){
                              $postback_logs['branch_email_status'] = 500;
                              $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                            }
                          }
                        }

                      // Order is not auto assign
                      }else{
                        if($api_call == 0){
                          //START SPECIAL HANDLING FOR JCWW AND COPPERMASK JC
                          //check if there are JCWW OR COPPERMASK JC items in the order
                          if(!empty($orderDRItem)) {
                            // JC API
                            if(order_ref_prefix() == "COP") {

                              if($postback_logs['jc_api_status'] == 500){
                                try{
                                  // check order admin_drno
                                  if($order['admin_drno'] == 0 || $order['admin_drno'] == ""){
                                    //Prepare direct sales order object for JC api call
                                    $drsum = array();
                                    $drdetails = array();
                                    $refcode = "none";

                                    //check if referral code record exist
                                    if($record != null && $record != "") {
                                      $refcode = strtoupper($record["referral_code"]);
                                    }

                                    //prepare dr summary for JC API
                                    array_push($drsum, array(
                                      "idno" => $this->idno_for_coppermask(),
                                      "trandate" => date("Y-m-d"),
                                      "drno" => 0,
                                      "totalamt" => $order["total_amount"],
                                      "shipping" => 1,
                                      "process" => 1,
                                      "ispaid" => "Full Payment",
                                      "freight" => $order["delivery_amount"],
                                      "username" => "CMASKWEB",
                                      "status" => 1,
                                      "notes" => $reference_number." | shoplink: ".$refcode." | ".$order["fullname"]." | ".$order["conno"]." | ".$order["email"]." | ".$order["address"]." ".$order["notes"],
                                      "classification" => "Regular Order",
                                      "approvaldate" => '0000-00-00',
                                      "approvedby" => "none"
                                    ));

                                    //set dr details for JC API
                                    $drdetails = $orderDRItem;

                                    $processDate = date('Y-m-d H:i:s');
                                    $paytype = $this->getPaymentType($service_code);

                                    //prepare DR data for JC API
                                    $DRData = array (
                                      "signature" => en_dec_jc_api("en", md5($processDate.$this->jc_cm_api_key())),
                                      "date" => $processDate,
                                      "drsum" => en_dec_jc_api("en", json_encode($drsum)),
                                      "drdetails" => en_dec_jc_api("en", json_encode($drdetails)),
                                      "paytype" => en_dec_jc_api("en", $paytype)
                                    );

                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $this->jc_cm_api_url());
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($DRData));
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    $server_output = curl_exec($ch);

                                    // INSERT JC API LOGS
                                    $api_jc_logs_data = array(
                                      "refnum" => $reference_number,
                                      "response" => $server_output,
                                      "date_created" => today()
                                    );
                                    $this->ordersModel->set_api_jc_logs($api_jc_logs_data);

                                    curl_close ($ch);
                                    $response = json_decode($server_output);

                                    if($response->success) {
                                      $jc_cm_drno = en_dec_jc_api("dec", $response->drno);
                                    }
                                    $order['admin_drno'] = $jc_cm_drno;
                                  }
                                  $postback_logs['jc_api_status'] = 200;
                                }catch(Exception $e){
                                  $postback_logs['jc_api_status'] = 500;
                                  $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                                }
                              }

                            }
                            // JCWW API
                            else {

                              if($postback_logs['jcww_api_status'] == 500){
                                try{
                                  if($order['admin_sono'] == "" || $order['admin_sono'] == 0){
                                    //Prepare sales order object for JCW api call
                                    $orderDR = array(
                                    "idno" => $this->idno_for_ecommerce(),
                                    "shipping_id" => 0,
                                    "itemlocid" => 0,
                                    "sales_date" => date("Y-m-d H:i:s"),
                                    "totalamt" => $order["total_amount"],
                                    "freight" => $order["delivery_amount"],
                                    "discamt" => 0,
                                    "gen_disc" => 0,
                                    "gendisctype" => 0,
                                    "name" => $order["fullname"],
                                    "conno" => $order["conno"],
                                    "email" => $order["email"],
                                    "address" => $order["address"],
                                    "instructions" => ($order["notes"] == "") ? "NA | ".$order["reference_num"] : $order["notes"]." | ".$order["reference_num"]
                                    );
                                    $orderDR["orderItems"] = $orderDRItem;
                                    $orderDR["signature"] = en_dec("en", md5($this->idno_for_ecommerce().$this->sk_api_key().round($order["total_amount"])));
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $this->sk_api_url());
                                    curl_setopt($ch, CURLOPT_POST, 1);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($orderDR));
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    $server_output = curl_exec($ch);

                                    // INSERT API JCWW LOGS
                                    $api_jcww_logs_data = array(
                                      "refnum" => $reference_number,
                                      "response" => $server_output,
                                      "date_created" => today()
                                    );
                                    $this->ordersModel->set_api_jcww_logs($api_jcww_logs_data);

                                    curl_close ($ch);
                                    $response = json_decode($server_output);
                                    $jcww_sono = $response->sono;

                                    $order["admin_sono"] = $jcww_sono;
                                  }
                                  $postback_logs['jcww_api_status'] = 200;
                                }catch(Exception $e){
                                  $postback_logs['jcww_api_status'] = 500;
                                  $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                                }
                              }

                            }

                            $api_call = 1;
                          }
                          //END SPECIAL HANDLING FOR JCWW AND COPPERMASK JC

                        }

                        if(!in_array($shop,$postback_logs['shop_order_failed']['update_prod_inv'])){
                          try{
                            $this->updateProductInventory($item_arr);
                            $postback_logs['shop_order_failed']['update_prod_inv'][] = $shop;
                          }catch(Exception $e){
                            $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                          }
                        }

                        if(!in_array($shop,$postback_logs['shop_order_failed']['update_inv_branch'])){
                          try{
                            $this->update_invtrans_branch($invtrans_branch_arr);
                            $postback_logs['shop_order_failed']['update_inv_branch'][] = $shop;
                          }catch(Exception $e){
                            $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                          }
                        }

                        // Send sms to seller
                        $message1 = "Hi ".$shopDetails['shopname'].". New order #".$order['reference_num']." is now paid and ready for processing. Please check your seller portal for details.";
                        if($postback_logs['seller_sms_status'] == 500){
                          try{
                            $this->sms->sendSMS(2, $shopDetails['mobile'], $message1, 'JC.');
                            $postback_logs['seller_sms_status'] = 200;
                          }catch(Exception $e){
                            $postback_logs['seller_sms_status'] = 500;
                            $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                          }
                        }

                        //send email to seller
                        if($postback_logs['seller_email_status'] == 500){
                          try{
                            $this->send_email("seller_order_done_email_send", $order, "Processing", $paypanda_refno, $shop, $shopCode);
                            $postback_logs['seller_email_status'] = 200;
                          }catch(Exception $e){
                            $postback_logs['seller_email_status'] = 500;
                            $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                          }
                        }

                        // START JC FULFILLMENT API
                        if(c_jcfulfillment_shopidno() != '' && in_array(order_ref_prefix(),c_allowed_jcfulfillment_prefix())){
                          if($postback_logs['jc_fulfillment_status'] == 500){
                            try{
                              $shopidno = c_jcfulfillment_shopidno();
                              $order['signature'] = en_dec("en",md5($shopidno.$this->jcfskey().round($paid_amount)));
                              $order['reference_num'] = $reference_number;
                              $order['total_amount'] = $paid_amount;
                              $order['idno'] = $shopidno;
                              $order['payment_date'] = date("Y-m-d H:i:s");
                              $order['paypanda_ref'] = $paypanda_refno;
                              $order['payment_notes'] = ($payment_notes == null || $payment_notes == "") ? "" : $payment_notes;
                              $order['payment_status'] = 1;
                              $order['service_code'] = $service_code;

                              $ch = curl_init();
                              curl_setopt($ch, CURLOPT_URL, $this->jc_fulfillment_update_url());
                              curl_setopt($ch, CURLOPT_POST, 1);
                              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($order));
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                              $server_output = curl_exec($ch);

                              // INSERT JC FULFILLMENT POSBACK LOGS
                              $api_jcfulfillment_logs_data = array(
                                "refnum" => $reference_number,
                                "response" => $server_output,
                                "type" => "postback",
                                "date_created" => today()
                              );
                              $this->ordersModel->set_jc_fulfillment_logs($api_jcfulfillment_logs_data);

                              curl_close ($ch);
                              $postback_logs['jc_fulfillment_status'] = 200;
                            }catch(Exception $e){
                              $postback_logs['jc_fulfillment_status'] = 500;
                              $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                            }
                          }
                          // die($server_output);
                        }
                        // END JC FULFILLMENT API
                      }


                    }//end of foreach
                    $postback_logs['split_order_status'] = 200;
                  }catch(Exception $e){
                    $postback_logs['split_order_status'] = 500;
                    $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                  }
                }
                //END SPLIT ORDER LOGIC

                //prepare payment details for updating app_order_details table
                $data = array(
                  "paypanda_ref" => $paypanda_refno,
                  "payment_status" => 1,
                  "payment_method" => $trigger == 'manual_payment'
                  ? $manual_payment_method
                  : $order["payment_method"],
                  "payment_portal_fee" => $payment_portal_fee,
                  "payment_date" => date("Y-m-d H:i:s"),
                  "admin_sono" => $jcww_sono,
                  "admin_drno" => $jc_cm_drno,
                  "payment_notes" => $payment_notes,
                  "payment_option" => $service_code
                );
                //update record in app_order_details to paid
                if($postback_logs['update_order_status'] == 500){
                  try{
                    $this->ordersModel->payOrder($reference_number,$data);
                    $postback_logs['update_order_status'] = 200;
                  }catch(Exception $e){
                    $postback_logs['update_order_status'] = 500;
                    $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
                  }
                }

                //send email to client
                if($postback_logs['client_email_status'] == 500){
                  try{
                    $this->send_email("order_done_email_send", $order, "Processing", $paypanda_refno, $shopCode);
                    $postback_logs['client_email_status'] = 200;
                  }catch(Exception $e){
                    $postback_logs['client_email_status'] = 500;
                  }
                }

                $postback_logs['shop_order_failed'] = json_encode($postback_logs['shop_order_failed']);
                $postback_logs['success'] = 200;
                $this->ordersModel->update_postback_process_order($postback_logs,$reference_number);
                $message =  'Order#' . $reference_number . ' has been tagged as Paid';
                $logs['activity'] = 'postback success';
                $logs['details'] = $message;
                $this->postback_logs->save_log($logs);

                echo json_encode(array("status" => "success", "message" => $message));
              }catch(Exception $e){
                $this->ordersModel->set_postback_error_logs(array('text' => $e->message));
              }

            } else {
              $message =  "Transaction is already paid.";
              $logs['activity'] = 'postback failed';
              $logs['details'] = $message;
              $this->postback_logs->save_log($logs);

              echo json_encode(array("status" => "failed", "message" => $message));
            }
        break;
        case "P":
          if($order['payment_status'] != 1){
            $data = array(
              "paypanda_ref" => $paypanda_refno,
              "payment_status" => 0
            );
            if($order["payment_status"] != 0){
              $order["payment_status"] = "Pending";
              $this->send_email("order_pending_email_send", $order, "Waiting for payment", $paypanda_refno);
              // $this->order_pending_email_send($order, "Waiting for payment", $paypanda_refno);
            }
            $this->ordersModel->payOrder($reference_number, $data);

            $message =  'Order#' . $reference_number . ' has been tagged as Pending';
            $logs['activity'] = 'postback success';
            $logs['details'] = $message;
            $this->postback_logs->save_log($logs);

            echo json_encode(array("status" => "success", "message" => $message));
          }else{
            echo json_encode(array("status" => "Transaction is already paid,"));
          }
        break;
        case "C":
          // $data = array(
          //   "paypanda_ref" => $paypanda_refno,
          //   "payment_status" => 2
          // );
          // $order["payment_status"] = "Cancelled";
          // $this->order_done_email_send($order, "Cancelled", $paypanda_refno);
          // $this->ordersModel->payOrder($reference_number, $data);
          // echo json_encode(array("status" => "Cancelled"));
        break;
        case "F":
          if($order['payment_status'] != 1){
            $data = array(
              "paypanda_ref" => $paypanda_refno,
              "payment_status" => 2
            );
            $order["payment_status"] = "Failed";
            $this->send_email("order_failed_email_send", $order, "Failed", $paypanda_refno);
            // $this->order_failed_email_send($order, "Failed", $paypanda_refno);
            $this->ordersModel->payOrder($reference_number, $data);

            $message =  'Order#' . $reference_number . ' has been tagged as Unpaid';
            $logs['activity'] = 'postback success';
            $logs['details'] = $message;
            $this->postback_logs->save_log($logs);

            echo json_encode(array("status" => "success", "message" => $message));
          }else{
            echo json_encode(array("status" => "Transaction is already paid"));
          }
        break;
        default:
          $message =  "Payment status not recognized.";
          $logs['activity'] = 'postback failed';
          $logs['details'] = $message;
          $this->postback_logs->save_log($logs);

          echo json_encode(array("status" => "failed", "message" => $message));
        break;
      }
    }

    public function paypanda_return_url(){
      $orderDetails = $this->ordersModel->getByRefNum($this->input->get("refno", TRUE));
      $orderItems = $this->ordersModel->getOrderDetails(null,null,$orderDetails["order_id"])->result_array();
      $referral_codes = $this->ordersModel->getReferralRecord($this->input->get("refno", TRUE));
      if($referral_codes->num_rows() > 0){
        $orderDetails['referral_code'] = $referral_codes->row()->referral_code;
      }
      $shopItems = array();
      foreach($orderItems as $item){
          $shopDetails = $this->ordersModel->getShopDetails($item["sys_shop"]);
          // $shippingPerShop = $this->model->getShippingPerShop($orderDetails["areaid"],$item["sys_shop"])->row_array();
          $shippingPerShop = $this->model->getShippingPerShop($this->input->get("refno", TRUE), $item["sys_shop"])->row_array();

          $orderStatus = $this->ordersModel->getOrderStatusPerShop($this->input->get("refno", TRUE), $item["sys_shop"]);
          if($orderStatus != null && $orderStatus != "") { // && ini() != "jcww"
              $order_status = $orderStatus["order_status"];
              $date_shipped = $orderStatus["date_shipped"];
              $date_ordered = $orderStatus["date_ordered"];
              $date_order_processed = $orderStatus["date_order_processed"];
              $date_ready_pickup = $orderStatus["date_ready_pickup"];
              $date_booking_confirmed = $orderStatus["date_booking_confirmed"];
              $date_fulfilled = $orderStatus["date_fulfilled"];
          }
          else {
              $order_status = $orderDetails["order_status"];
              $date_shipped = $orderDetails["date_shipped"];
              $date_ordered = $orderDetails["date_ordered"];
              $date_order_processed = '';
              $date_ready_pickup = '';
              $date_booking_confirmed = '';
              $date_fulfilled = '';
          }

          if(empty($shopItems[$item["sys_shop"]])) {
            $shopItems[$item["sys_shop"]] = array(
              "shopname" => $shopDetails["shopname"],
              "shopcode" => $shopDetails["shopcode"],
              "shopemail" => $shopDetails["email"],
              "shopmobile" => $shopDetails["mobile"],
              "shopdts" => $shippingPerShop["dts"],
              "shopdts_to" => $shippingPerShop["dts_to"],
              "shippingfee" => $shippingPerShop["sf"],
              "logo" => $shopDetails["logo"],
              "order_status" => $order_status,
              "date_shipped" => $date_shipped,
              "date_ordered" => $date_ordered,
              "date_order_processed" => $date_order_processed,
              "date_ready_pickup" => $date_ready_pickup,
              "date_booking_confirmed" => $date_booking_confirmed,
              "date_fulfilled" => $date_fulfilled,
            );
            $shopItems[$item["sys_shop"]]["items"] = array();
          }
          array_push($shopItems[$item["sys_shop"]]["items"], array(
                  "productid" => $item["product_id"],
                  "itemname" => $item["itemname"],
                  "unit" => $item["otherinfo"],
                  "quantity" => $item["quantity"],
                  "price" => $item["amount"],
                  "primary_pics" => $item['primary_pics']
          ));
      }

      // Get voucher details
      $voucherAmount = 0.00;
      $vouchers = $this->ordersModel->getOrderVoucher($orderDetails["reference_num"]);
      if (sizeof($vouchers) > 0) {
        // loop through voucher records, if payment_type is gogome add amount to voucherAmount
        foreach($vouchers as $key => $value) {
          if ($value['payment_type'] == 'Shoplink') {
            $shopItems[$value['shopid']]['vouchers'][] = array(
                'vcode' => $value['payment_refno'],
                'vamount' => $value['amount']
            );
            $shopItems[$value['shopid']]['voucherSubTotal'] = isset($shopItems[$value['shopid']]['voucherSubTotal']) ? floatval($shopItems[$value['shopid']]['voucherSubTotal']) + floatval($value['amount']) : floatval($value['amount']);
            $voucherAmount += floatval($value['amount']);
          }
        }
      }
      // dd($shopItems);
      $orderDetails['voucherAmount'] = $voucherAmount;
      $est_deliveryArr = "NA";
      $data = array(
        'status' => $this->input->get('status', TRUE),
        'order_details' => $orderDetails,
        'order_items' => $shopItems,
        'est_delivery_date' => generate_est_delivery_date($est_deliveryArr),
      );
      $data['categories'] = null;
      $this->load->view("includes/header", $data);
      $this->load->view("shop/payment-done", $data);
    }

    function send_email($function, $data, $orderStatus="", $paypandaRef="", $shop="", $shopCode="") {

      if(get_apiserver_link() != "" || get_apiserver_link() != null)
        $url = get_apiserver_link()."api/Emails/".$function;
      else
        $url = base_url()."api/Emails/".$function."/";

      //post parameters
      $fields = array(
        'data'   => $data,
        'orderStatus' => $orderStatus,
        'paypandaRef'  => $paypandaRef,
        'shop'      => $shop,
        'shopCode'   => $shopCode,
        'signature' => en_dec('en',$this->email_signature_key())
      );
      //build post parameters
      $postvars = http_build_query($fields);

      // open connection
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
      curl_setopt($ch, CURLOPT_USERAGENT, 'api');
      curl_setopt($ch, CURLOPT_TIMEOUT, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
      curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

      // execute post
      curl_exec($ch);
        // die($server_output);

      //close connection
      curl_close($ch);
    }

    public function destroyCart(){
      if($this->session->userdata("user_id") == null)
        $this->session->sess_destroy();
      else {
        $this->session->set_userdata("total_amount", 0);
        $this->session->set_userdata("cart_count", 0);
        $this->session->set_userdata("cart", []);
        $this->session->set_userdata("validVouchers", []);
      }
    }

    public function save_admin_dr(){
      $admin_sono = $this->input->post("sono");
      $admin_drno = $this->input->post("drno");
      $signature = $this->input->post("signature");
      if($signature == en_dec("en", md5($admin_sono.$this->sk_api_key().$admin_drno))){
        $data = array(
          array(
            'admin_sono' => $admin_sono,
            'admin_drno' => $admin_drno,
            'order_status' => "f"
          )
        );
        $this->ordersModel->process_dr_no($data);

        //Fulfill order
        $reference_num = $this->ordersModel->getReferenceNumberBySO($admin_sono);
        $sys_shop = $this->ordersModel->getJCWWShopID();

        if($reference_num != "" && $sys_shop != "") {
          $return_id = $this->ordersModel->FulfilledOrder($reference_num, $sys_shop);
          if($return_id !== false) {

            $this->ordersModel->addOrderHistory($return_id, 'Order is being delivered.', 'Mark as Fulfilled', 'Pandabooks', date('Y-m-d H:i:s'));
            $this->ordersModel->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Fulfilled', 'Mark as Fulfilled', 'Pandabooks');

            //Prepare email
            $orderDetails = $this->ordersModel->getByRefNum($reference_num);
            $orderItems = $this->ordersModel->getOrderDetails(null,null,$orderDetails["order_id"])->result_array();

            $shopItems = array();
            foreach($orderItems as $item){
              $shopDetails = $this->ordersModel->getShopDetails($item["sys_shop"]);
              if(empty($shopItems[$item["sys_shop"]])) {
                //get shipping details
                $shippingdts = $this->ordersModel->get_daystoship($reference_num, $sys_shop);

                $shopItems[$item["sys_shop"]] = array(
                  "shopname" => $shopDetails["shopname"],
                  "shopcode" => $shopDetails["shopcode"],
                  "shopemail" => $shopDetails["email"],
                  "shopmobile" => $shopDetails["mobile"],
                  "shopdts" => $shippingdts["daystoship"],
                  "shopdts_to" => $shippingdts["daystoship_to"]
                );
                $shopItems[$item["sys_shop"]]["items"] = array();
              }
              array_push($shopItems[$item["sys_shop"]]["items"], array(
                      "productid" => $item["product_id"],
                      "itemname" => $item["itemname"],
                      "unit" => $item["otherinfo"],
                      "quantity" => $item["quantity"],
                      "price" => $item["amount"],
                      "primary_pics" => $item['primary_pics']
              ));
            }

            $orderDetails["shopItems"] = $shopItems;
            $orderDetails["payment_status"] = "Paid";
            //send email to client
            $this->send_email("order_fulfilled_email_send", $orderDetails);
            // $this->order_fulfilled_email_send($orderDetails);
            generate_json(array('success' => 1));
            exit();
          }else{
            generate_json(array('success' => 0));
          }

        }


      }else{
        generate_json(array('success' => 0));
        exit();
        // show_404();
        // die();
      }
    }

    public function process_delivery(){
      $admin_drno = $this->input->post("drno");
      $riderInfo = $this->input->post("riderinfo");
      $refno = $this->input->post("refno");
      $amount = $this->input->post("amount");
      $signature = $this->input->post("signature");
      if($signature == en_dec("en", md5($admin_drno.$this->sk_api_key().round($amount)))){
        $data = array(
          "admin_drno" => $admin_drno,
          "order_status" => "s",
          "delivery_info" => $riderInfo,
          "delivery_ref_num" => $refno,
          "delivery_amount" => $amount,
          'date_shipped' => date("Y-m-d H:i:s"),
        );
        $res = $this->ordersModel->processDelivery($data);

        if($res !== false){

          $order = $this->ordersModel->getByDrno($admin_drno);
          $this->ordersModel->addOrderHistory($res, 'Order has been successfully shipped.', 'Mark as Shipped', 'Pandabooks', date('Y-m-d H:i:s'));
          $this->ordersModel->logActivity('Order List', 'Order #'.$refno.' has been tagged as Shipped', 'Mark as Shipped', 'Pandabooks');

          $this->send_email("processing_for_delivery_email_send", $order, "Shipped", $order["paypanda_ref"]);
          // $this->processing_for_delivery_email_send($order, "Shipped", $order["paypanda_ref"]);
          $result = array("status" => 1, "message" => "Successfully updated the record.");
        }else{
          $result = array("status" => 0, "message" => "No record has this drno, ". $admin_drno);
        }
        echo json_encode($result);
      }else{
        show_404();
        die();
      }
    }

    public function updateProductInventory($items,$branch = 0) {

      foreach($items as $item){
        //Check if inventory track quantity is enabled
        if($this->itemsModel->isQuantityTrackingSet($item["product_id"])) {

          //Insert in sys_products_invtrans table
          $data = array(
            "product_id" => $item["product_id"],
            "quantity" => floatval($item["quantity"]) * -1.00,
            "type" => "Online Purchase",
            "date_created" => date("Y-m-d H:i:s"),
            "branchid" => $branch,
            "enabled" => 1
          );

          $res = $this->itemsModel->insertInventoryTrans($data);
          $this->itemsModel->updateProductStocks($item["product_id"]);

          // if($res) {
            // return $res['insert_id'];
          // }
        }
      }


    }

    public function calculateShipping($shopid, $items, $citymunCode, $total_weight, $total_amount, $latitude = "", $longitude = "") {
        $shippingfee = 0;
        $from_dts = "";
        $to_dts = "";
        $new_total_amount = 0.00;
        $new_total_weight = 0.00;
        $itemList = array();
        $xItemList = array(); // unserviceable items will be pushed here
        $good_item_arr = array();
        // print_r($items);
        // die();

        // check inventory stocks of main or sub branches.
        $branchid = 0; // zero means main branch
        // TRAP SHOPLINK AND RESELLER LOGIN . ASSIGN TO MAIN FOR NOW
        // if(!isset($this->session->referral)){
          $loc_array = array("citymunCode" => $citymunCode, "provCode" => 0, "regCode" => 0);
          $branches = $this->model->get_shopbranch($shopid, 1);
          $unfulfilled_settings = $this->ordersModel->get_default_allowed_unfulfilled_orders($shopid);
          $allowed_unfulfilled_orders = $unfulfilled_settings['allowed_unfulfilled'];
          if(sizeof($branches) > 0){
            $branch_assigned_to = $this->auto_assign_branch($loc_array, $branches);
            // If there was 1 assigned branch send sms and email notificaiton
            if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) == 1){
              $branchid = $branch_assigned_to[0]['branchid'];
              // if check unfulfilled orders is on
              if($unfulfilled_settings['check_unfulfilled_orders'] == 1){

                $allowed_days = $this->ordersModel->get_specific_allowed_unfulfilled_orders($shopid,$branch_assigned_to[0]['branchid'],$citymunCode);
                $allowed_unfulfilled_orders = ($allowed_days == null) ? $allowed_unfulfilled_orders : $allowed_days;
                $unfulfilled = $this->ordersModel->get_last_unfulfilled_order($shopid,$branchid,$allowed_unfulfilled_orders);
                if($unfulfilled->num_rows() > 0){
                  $yesterday = date_create(date('Y-m-d H:i:s',strtotime('Yesterday')));
                  $date_assigned = date_create($unfulfilled->row()->date_assigned);
                  $diff = date_diff($yesterday,$date_assigned);
                  $days_diff = $diff->format("%a");
                  if($days_diff >= (int)$allowed_unfulfilled_orders){
                    // HOLD EMAIL SEND
                    // var_dump($branch_assigned_to[0]['on_hold']);
                    if($branch_assigned_to[0]['on_hold'] == 0){
                      $hold_data['branchemail'] = $branch_assigned_to[0]['email'];
                      $hold_data['branchname'] = $branch_assigned_to[0]['branchname'];
                      $hold_data['allowed_days'] = $allowed_unfulfilled_orders;
                      $this->send_email("hold_email_send",$hold_data);
                      // UPDATE ON HOLD STATUS ON BRANCH
                      $this->model->update_on_hold_status($branchid,1);
                    }

                    $branchid = 0;
                  }else{
                    if($branch_assigned_to[0]['on_hold'] == 1){
                      // REACTIVATION EMAIL SEND
                      $reactivation_data['branchemail'] = $branch_assigned_to[0]['email'];
                      $reactivation_data['branchname'] = $branch_assigned_to[0]['branchname'];
                      $reactivation_data['allowed_days'] = $allowed_unfulfilled_orders;
                      $this->send_email("reactivation_email_send",$reactivation_data);
                      $this->model->update_on_hold_status($branchid,0);
                    }
                  }
                }
              }
            }

            // If there was 2 or more assign branch
            if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) > 1){
              // check branch who can fulfill more items.
              $branches_that_fulfilled_all_orders = array();
              foreach($branch_assigned_to as $key_index => $branch){
                // if check unfulfilled orders is on
                if($unfulfilled_settings['check_unfulfilled_orders'] == 1){
                  $allowed_days = $this->ordersModel->get_specific_allowed_unfulfilled_orders($shopid,$branch['branchid'],$citymunCode);
                  $allowed_unfulfilled_orders = ($allowed_days == null) ? $allowed_unfulfilled_orders : $allowed_days;
                  $unfulfilled = $this->ordersModel->get_last_unfulfilled_order($shopid,$branch['branchid'],$allowed_unfulfilled_orders);
                  if($unfulfilled->num_rows() > 0){

                    $yesterday = date_create(date('Y-m-d H:i:s',strtotime('Yesterday')));
                    $date_assigned = date_create($unfulfilled->row()->date_assigned);
                    $diff = date_diff($yesterday,$date_assigned);
                    $days_diff = $diff->format("%a");
                    if($days_diff < $allowed_unfulfilled_orders){

                      if($branch['on_hold'] == 1){
                        // REACTIVATION EMAIL SEND
                        $reactivation_data['branchemail'] = $branch['email'];
                        $reactivation_data['branchname'] = $branch['branchname'];
                        $reactivation_data['allowed_days'] = $allowed_unfulfilled_orders;
                        $this->send_email("reactivation_email_send",$reactivation_data);
                        $this->model->update_on_hold_status($branch['branchid'],0);
                      }

                      $branches_that_fulfilled_all_orders[] = $branch['branchid'];
                    }else{

                      if($branch['on_hold'] == 0){
                        $hold_data['branchemail'] = $branch['email'];
                        $hold_data['branchname'] = $branch['branchname'];
                        $hold_data['allowed_days'] = $allowed_unfulfilled_orders;
                        $this->send_email("hold_email_send",$hold_data);
                        // UPDATE ON HOLD STATUS ON BRANCH
                        $this->model->update_on_hold_status($branch['branchid'],1);
                      }
                      // array_splice($branch_assigned_to,$key_index,1);
                      unset($branch_assigned_to[$key_index]);
                    }
                  }else{

                    if($branch['on_hold'] == 1){
                      // REACTIVATION EMAIL SEND
                      $reactivation_data['branchemail'] = $branch['email'];
                      $reactivation_data['branchname'] = $branch['branchname'];
                      $reactivation_data['allowed_days'] = $allowed_unfulfilled_orders;
                      $this->send_email("reactivation_email_send",$reactivation_data);
                      $this->model->update_on_hold_status($branch['branchid'],0);
                    }

                    $branches_that_fulfilled_all_orders[] = $branch['branchid'];
                  }
                }

              }

                // REORDER ARRAY INDEX
              $branch_assigned_to = array_values($branch_assigned_to);

              // if only one branch has fulfill all its orders assign order to that branch.
              if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) == 1){
                $branchid = $branch_assigned_to[0]['branchid'];
              }

              // if multiple branch has fulfill all there orders
              if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) > 1){
                $branch_stocks = array();
                // print_r($branch_assigned_to);
                foreach($branch_assigned_to as $branch){
                  $branch_score = 0;
                  foreach($items as $index => $item){
                    $no_of_stocks = $this->ordersModel->get_branch_inv($shopid,$branch['branchid'],$item['productid']);
                    $no_of_stocks = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->no_of_stocks : 0;
                    $branch_score += ($no_of_stocks > 0) ? 1 : 0;
                    $branch_score += ($no_of_stocks >= $item['quantity']) ? 1 : 0;
                  }
                  $branch_stocks[] = $branch_score;
                }
                $most_fulfill = array_keys($branch_stocks,max($branch_stocks));
                // if 2 or more branch can still fulfill order check google distance or pending order
                if(count((array)$most_fulfill) > 1){
                  // check nearest branch on the city using google distance
                  if(allow_google_addr() == 1 && $longitude != "" && $latitude != ""){
                    // $dist = get_distance2($latitude,$longitude,$branch_assigned_to[0]['latitude'],$branch_assigned_to[0]['longitude']);
                    $dist_arr = array();
                    foreach($most_fulfill as $i){
                      $dist = get_distance2($latitude,$longitude,$branch_assigned_to[$i]['latitude'],$branch_assigned_to[$i]['longitude']);
                      $dist_arr[] = $dist;
                    }
                    // print_r($dist_arr);
                    // die();
                    $nearest = array_keys($dist_arr,min($dist_arr));
                    $branch_assigned_to = $branch_assigned_to[$most_fulfill[$nearest[0]]];

                  // check whose branch has the least to process order
                  }else{
                    $least_arr = array();
                    foreach($most_fulfill as $x){
                      // $least_arr[] = $this->model->get_branch_pending_order($shopid,$branch_assigned_to[$x]['branchid']);
                      $least_arr[] = $branch_assigned_to[$x]['branch_pending_orders'];
                    }
                    $least = array_keys($least_arr,min($least_arr));
                    // if pending orders tied get last order for each branch then get the branch with oldest order
                    if(count((array)$least) > 1){
                      $last_order_arr = array();
                      foreach($least as $n){
                        // $last_order_arr[] = strtotime($this->model->get_last_branch_ordered($shopid,$branch_assigned_to[$n]['branchid']));
                        $last_order_arr[] = strtotime($branch_assigned_to[$n]['last_order']);
                      }
                      $oldest_order = array_keys($last_order_arr,min($last_order_arr));
                      $branch_assigned_to = $branch_assigned_to[$most_fulfill[$oldest_order[0]]];

                    // else set the least branch with least pending orders.
                    }else{
                      $branch_assigned_to = $branch_assigned_to[$most_fulfill[$least[0]]];
                    }
                  }

                // if only one branch can fulfill order
                }else{
                  $branch_assigned_to = $branch_assigned_to[$most_fulfill[0]];
                }

                $branchid = $branch_assigned_to['branchid'];
              }

            }
          }
        // }


        // var_dump($branchid);

        // if not auto assign to any branch get normal process for getting shipping fee
        if($branchid == 0){
          $shippingPerShop = $this->model->get_general_shipping_per_shop($shopid, $citymunCode);
          // die($shippingPerShop);
        // if auto assign to branch get shipping fee of branch if nothing was found
        // return to normal process
        }else{
          $custom_shipping_per_shop = $this->model->get_custom_shipping_per_branch($shopid, $branchid, $citymunCode);
          $shippingPerShop = $this->model->get_general_shipping_of_branch($shopid,$branchid,$citymunCode);
          if(sizeof($shippingPerShop) == 0 && sizeof($custom_shipping_per_shop) == 0 ){
            $shippingPerShop = $this->model->get_general_shipping_per_shop($shopid, $citymunCode);
            $branchid = 0;
            // die($shippingPerShop);
          }
        }

        // print_r($shippingPerShop);
        // die();
        // Loop thru each item to check item availability
        foreach($items as $key => $item){
          $item["available"] = 1;


          $no_of_stocks = $this->ordersModel->get_branch_inv($shopid,$branchid,$item['productid']);
          $cont_selling_isset = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->cont_selling_isset : 0;
          $no_of_stocks = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->no_of_stocks : 0;
          if(c_inv_threshold() == 1){
            if($branchid == 0){
              $inv_threshold = $this->model->get_shop_inv_threshold($shopid);
              $inv_threshold = ($inv_threshold->num_rows() > 0) ? $inv_threshold->row()->inv_threshold : 100;
            }else{
              $branch_key = array_search($branchid,array_column($branches,'branchid'));
              if($branch_key !== false && isset($branches[$branch_key]['inv_threshold'])){
                $inv_threshold = $branches[$branch_key]['inv_threshold'];
                $inv_threshold = ((float)$inv_threshold > 0) ? $inv_threshold : 100;
              }else{
                $inv_threshold = $this->model->get_branch_inv_threshold($branchid);
                $inv_threshold = ($inv_threshold->num_rows() > 0) ? $inv_threshold->row()->inv_threshold : 100;
              }
            }

            if($no_of_stocks > 0){
              $no_of_stocks = $no_of_stocks * round(((int)$inv_threshold / 100),2);
            }
          }

          if($cont_selling_isset == 0){
            if($no_of_stocks <= 0 || $item['quantity'] > $no_of_stocks){
              $item['available'] = 2;
              $item['available_stocks'] = (int)$no_of_stocks;
              array_push($xItemList, $item);
            }
          }

          // push item to itemList
          array_push($itemList, $item);
        }

        // loop thru each item to check if branch can accommodate 30% of order
        if($branchid != 0 && c_order_threshold() > 0){
          $total_item = count($items);
          $unavailable_item = 0;
          foreach($itemList as $key => $item){
            if($item['available'] == 2){
              $unavailable_item += 1;
            }
          }

          $percentage = 1;
          if($unavailable_item > 0){
            $percentage = round(floatval($unavailable_item) / floatval($total_item),2);
            if($percentage >= c_order_threshold() ){
              $shippingPerShop = $this->model->get_general_shipping_per_shop($shopid, $citymunCode);
              if(count($shippingPerShop) > 0){
                $branchid = 0;
                $itemList = array();
                $xItemList = array(); // unserviceable items will be pushed here
                // Loop thru each item to check item availability
                foreach($items as $key => $item){
                  $item["available"] = 1;


                  $no_of_stocks = $this->ordersModel->get_branch_inv($shopid,$branchid,$item['productid']);
                  $cont_selling_isset = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->cont_selling_isset : 0;
                  $no_of_stocks = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->no_of_stocks : 0;
                  if(c_inv_threshold() == 1){
                    if($branchid == 0){
                      $inv_threshold = $this->model->get_shop_inv_threshold($shopid);
                      $inv_threshold = ($inv_threshold->num_rows() > 0) ? $inv_threshold->row()->inv_threshold : 100;
                    }else{
                      $branch_key = array_search($branchid,array_column($branches,'branchid'));
                      if($branch_key !== false && isset($branches[$branch_key]['inv_threshold'])){
                        $inv_threshold = $branches[$branch_key]['inv_threshold'];
                        $inv_threshold = ((float)$inv_threshold > 0) ? $inv_threshold : 100;
                      }else{
                        $inv_threshold = $this->model->get_branch_inv_threshold($branchid);
                        $inv_threshold = ($inv_threshold->num_rows() > 0) ? $inv_threshold->row()->inv_threshold : 100;
                      }
                    }

                    if($no_of_stocks > 0){
                      $no_of_stocks = $no_of_stocks * round(((int)$inv_threshold / 100),2);
                    }
                  }

                  if($cont_selling_isset == 0){
                    if($no_of_stocks <= 0 || $item['quantity'] > $no_of_stocks){
                      $item['available'] = 2;
                      $item['available_stocks'] = (int)$no_of_stocks;
                      array_push($xItemList, $item);
                    }
                  }

                  // push item to itemList
                  array_push($itemList, $item);
                }
              }
            }
          }


        }

        // calculate new total amount and new total weight
        foreach($itemList as $ikey => $list){
          if($list['available'] == 1){
            $good_item_arr[] = $list;
          }
        }

        $new_total = $this->getProductTotals($good_item_arr);
        $new_total_amount = floatval($new_total['total_amount']);
        $new_total_weight = floatval($new_total['total_weight']);
        $total_amount = $new_total_amount;
        $total_weight = $new_total_weight;


        //if general shipping found
        if($shippingPerShop != null && !empty($shippingPerShop)) {

            $is_condition = 0;
            foreach ($shippingPerShop as $shippingDetails) {
                //check if total weight condition is set
                if($shippingDetails["is_condition"] == 1) {

                    // if($total_weight >= $shippingDetails["condition_min_value"]) {
                    if(($total_weight >= $shippingDetails["condition_min_value"] && ($total_weight <= $shippingDetails["condition_max_value"] || $shippingDetails["condition_max_value"] == 0)) || ($total_weight > $shippingDetails['condition_min_value'] && $shippingDetails['additional_isset'] == 1)) {

                        if($is_condition && $shippingDetails["rate_amount"] >= $shippingfee || !$is_condition) {
                            $shippingfee = $shippingDetails["rate_amount"];
                            $from_dts = $shippingDetails["from_day"];
                            $to_dts = $shippingDetails["to_day"];
                            $is_condition = 1;

                            if($shippingDetails["additional_isset"] == 1 && $total_weight > $shippingDetails["condition_max_value"]){
                              $temp_total_weight = $total_weight - $shippingDetails['condition_max_value'];
                              while($temp_total_weight > 0){
                                $temp_total_weight -= (float)$shippingDetails['set_value'];
                                $shippingfee += (float)$shippingDetails['set_amount'];
                              }
                              // $succeding = ceil($temp_total_weight / (float)$shippingDetails['set_value']);
                              // $shippingfee += ($succeding * (float)$shippingDetails['set_amount']);
                            }
                        }
                    }

                }
                //check if total amount condition is set
                else if($shippingDetails["is_condition"] == 2) {
                    // if($total_amount >= $shippingDetails["<conditi></conditi>on_min_value"]){
                    if(($total_amount >= $shippingDetails["condition_min_value"] && ($total_amount <= $shippingDetails["condition_max_value"] || $shippingDetails["condition_max_value"] == 0)) || ($total_amount > $shippingDetails['condition_max_value'] && $shippingDetails['additional_isset'] == 1)) {

                        if($is_condition && $shippingDetails["rate_amount"] >= $shippingfee || !$is_condition) {
                            $shippingfee = $shippingDetails["rate_amount"];
                            $from_dts = $shippingDetails["from_day"];
                            $to_dts = $shippingDetails["to_day"];
                            $is_condition = 1;

                            if($shippingDetails["additional_isset"] == 1 && $total_amount > $shippingDetails["condition_max_value"]){
                              $temp_total_price = $total_amount - (float)$shippingDetails["condition_max_value"];
                              while($temp_total_price > 0){
                                $temp_total_price -= (float)$shippingDetails['set_value'];
                                $shippingfee += (float)$shippingDetails['set_amount'];
                              }
                              // $succeding = ceil($total_amount / (float)$shippingDetails['set_value']);
                              // $shippingfee += ($succeding * (float)$shippingDetails['set_amount']);
                            }
                        }
                    }

                }
                //if no condition set
                else {

                    if(!$is_condition && $shippingDetails["rate_amount"] >= $shippingfee) {
                        $shippingfee = $shippingDetails["rate_amount"];
                        $from_dts = $shippingDetails["from_day"];
                        $to_dts = $shippingDetails["to_day"];
                    }
                }
            }

        }

        $general_shippingfee = $shippingfee;
        $general_from_dts = $from_dts;
        $general_to_dts = $to_dts;

        $is_condition = 0;

        //loop thru each item in the order to determine which are unserviceable
        foreach ($items as $key => $item) {
            // assign result to items array so that we don't have to call the model again
            // if not auto assign to any branch
            if($branchid == 0){
              $items[$key]['shippingPerShop'] = $this->model->get_custom_shipping_per_product($item["productid"], $citymunCode);
            // if assign to a branch get custom shipping fee for the product
            }else{
              $custom_shipping = $this->model->get_custom_shipping_per_product_branch($shopid,$branchid,$item["productid"],$citymunCode);
              if(sizeof($custom_shipping) > 0){
                $items[$key]['shippingPerShop'] = $custom_shipping;
              }else{
                $items[$key]['shippingPerShop'] = $this->model->get_custom_shipping_per_product($item["productid"], $citymunCode);
              }
            }


            //if custom & general shipping is cannot be applied for item
            if(empty($items[$key]['shippingPerShop']) && $general_from_dts == "") {
                array_push($xItemList, $item);
            }
        }

        //loop thru each item in the order to identify which shipping condition to be applied
        foreach ($items as $key => $item) {

            //set item is available
            // $item["available"] = 1;
            //
            //
            // $no_of_stocks = $this->ordersModel->get_branch_inv($shopid,$branchid,$item['productid']);
            // $cont_selling_isset = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->cont_selling_isset : 0;
            // $no_of_stocks = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->no_of_stocks : 0;
            // if(c_inv_threshold() == 1){
            //   if($branchid == 0){
            //     $inv_threshold = $this->model->get_shop_inv_threshold($shopid);
            //     $inv_threshold = ($inv_threshold->num_rows() > 0) ? $inv_threshold->row()->inv_threshold : 100;
            //   }else{
            //     $branch_key = array_search($branchid,array_column($branches,'branchid'));
            //     if($branch_key !== false && isset($branches[$branch_key]['inv_threshold'])){
            //       $inv_threshold = $branches[$branch_key]['inv_threshold'];
            //       $inv_threshold = ((float)$inv_threshold > 0) ? $inv_threshold : 100;
            //     }else{
            //       $inv_threshold = $this->model->get_branch_inv_threshold($branchid);
            //       $inv_threshold = ($inv_threshold->num_rows() > 0) ? $inv_threshold->row()->inv_threshold : 100;
            //     }
            //   }
            //
            //   if($no_of_stocks > 0){
            //     $no_of_stocks = $no_of_stocks * round(((int)$inv_threshold / 100),2);
            //   }
            // }
            //
            // if($cont_selling_isset == 0){
            //   if($no_of_stocks <= 0 || $item['quantity'] > $no_of_stocks){
            //     $item['available'] = 2;
            //     $item['available_stocks'] = (int)$no_of_stocks;
            //     array_push($xItemList, $item);
            //   }
            // }
            // $shippingPerShop = $this->model->get_custom_shipping_per_product($item["productid"], $citymunCode);

            //if custom shipping found for item
            if($item['shippingPerShop'] != null && !empty($item['shippingPerShop'])) {

                // if there are unserviceable items recompute total_weight and total_amount
                if (sizeof($xItemList) > 0) {
                  $xProductTotal = $this->getProductTotals($xItemList);
                  $total_weight = (float) $total_weight - (float) $xProductTotal['total_weight'];
                  $total_amount = (float) $total_amount - (float) $xProductTotal['total_amount'];
                }

                foreach ($item['shippingPerShop'] as $shippingDetails) {
                    //check if total weight condition is set
                    if($shippingDetails["is_condition"] == 1) {
                        // if($total_weight >= $shippingDetails["condition_min_value"]){
                        if(($total_weight >= $shippingDetails["condition_min_value"] && ($total_weight <= $shippingDetails["condition_max_value"] || $shippingDetails["condition_max_value"] == 0)) || ($total_weight > $shippingDetails['condition_min_value'] && $shippingDetails['additional_isset'] == 1)) {

                            if($is_condition && $shippingDetails["rate_amount"] >= $shippingfee || !$is_condition) {
                                $shippingfee = $shippingDetails["rate_amount"];
                                $from_dts = $shippingDetails["from_day"];
                                $to_dts = $shippingDetails["to_day"];
                                $is_condition = 1;

                                if($shippingDetails["additional_isset"] == 1 && $total_weight > $shippingDetails["condition_max_value"]){
                                  $temp_total_weight = $total_weight - $shippingDetails['condition_max_value'];
                                  while($temp_total_weight > 0){
                                    $temp_total_weight -= (float)$shippingDetails['set_value'];
                                    $shippingfee += (float)$shippingDetails['set_amount'];
                                  }
                                  // $succeding = ceil($temp_total_weight / (float)$shippingDetails['set_value']);
                                  // $shippingfee += ($succeding * (float)$shippingDetails['set_amount']);
                                }
                            }
                        }

                    }
                    //check if total amount condition is set
                    else if($shippingDetails["is_condition"] == 2) {
                        // if($total_amount >= $shippingDetails["condition_min_value"]){
                        if(($total_amount >= $shippingDetails["condition_min_value"] && ($total_amount <= $shippingDetails["condition_max_value"] || $shippingDetails["condition_max_value"] == 0)) || ($total_amount > $shippingDetails['condition_max_value'] && $shippingDetails['additional_isset'] == 1)) {

                            if($is_condition && $shippingDetails["rate_amount"] >= $shippingfee || !$is_condition) {
                                $shippingfee = $shippingDetails["rate_amount"];
                                $from_dts = $shippingDetails["from_day"];
                                $to_dts = $shippingDetails["to_day"];
                                $is_condition = 1;

                                if($shippingDetails["additional_isset"] == 1 && $total_amount > $shippingDetails["condition_max_value"]){
                                  $temp_total_price = $total_amount - float($shippingDetails["condition_max_value"]);
                                  while($temp_total_price > 0){
                                    $temp_total_price -= (float)$shippingDetails['set_value'];
                                    $shippingfee += (float)$shippingDetails['set_amount'];
                                  }
                                  // $succeding = ceil($total_amount / (float)$shippingDetails['set_value']);
                                  // $shippingfee += ($succeding * (float)$shippingDetails['set_amount']);
                                }
                            }
                        }

                    }
                    //if no condition set
                    else {
                        if(!$is_condition && $shippingDetails["rate_amount"] >= $shippingfee) {
                            $shippingfee = $shippingDetails["rate_amount"];
                            $from_dts = $shippingDetails["from_day"];
                            $to_dts = $shippingDetails["to_day"];
                        }
                    }
                }

            }
            //if item is not in custom shipping settings
            else {
              //if no shipping zone set in general shipping settings
              if($general_from_dts == "") {
                //set item unavailable
                $item["available"] = 0;
              }
              else if($general_shippingfee > $shippingfee) {
                $shippingfee = $general_shippingfee;
                $from_dts = $general_from_dts;
                $to_dts = $general_to_dts;
                $is_condition = 1;
              }
            }

            // unset attached shippingPerShop per item
            unset($item['shippingPerShop']);


        }

        if($from_dts == "") {
            return null;
        }

        $data = array("shippingfee" => $shippingfee, "from_dts" => $from_dts, "to_dts" => $to_dts, "items" => $itemList, "branchid" => $branchid);

        return $data;
    }

    public function getProductTotals($items) {

        $total_weight = 0.00;
        $total_amount = 0.00;
        foreach ($items as $item) {
            $total_weight += ($this->itemsModel->getProductWeight($item["productid"]) * $item["quantity"]);
            $total_amount += $item["total_amount"];
        }

        return array("total_weight" => $total_weight, "total_amount" => $total_amount);
    }

    public function getShippingRate(){
        // $this->session->unset_userdata('shipping_per_shop');
        // $this->session->unset_userdata('toktok_api_shipping_logs');

        $cartItems = $this->session->userdata("cart") ? $this->session->userdata("cart") : [];
        $newCart = array();
        $removedCart = array();
        $validVouchers = json_decode($this->input->post('vouchers'), true);
        $areaId = sanitize($this->input->post("areaid"));
        $name = sanitize($this->input->post("name"));
        $email = sanitize($this->input->post("email"));
        $conno = sanitize($this->input->post("conno"));
        $address = sanitize($this->input->post("address"));
        $latitude = sanitize($this->input->post('latitude'));
        $longitude = sanitize($this->input->post('longitude'));
        $landmark = sanitize($this->input->post("landmark"));
        $postal = sanitize($this->input->post("postal"));

        $citymunCode = sanitize($this->input->post("citymunCode"));
        $hash_address = en_dec('en',json_encode(array($citymunCode,$latitude,$longitude,$this->session->total_amount)));

        if(isset($this->session->hash_address) && $this->session->hash_address != ""){
          if($this->session->hash_address != $hash_address){
            $this->session->unset_userdata('shipping_per_shop');
            $this->session->unset_userdata('toktok_api_shipping_logs');
          }
        }

        // var_dump($latitude);
        // var_dump($longitude);
        // die();

        $this->session->name =  strtoupper($name);
        $this->session->address =  strtoupper($address);
        $this->session->city =  $areaId;
        $this->session->email =  strtolower($email);
        $this->session->conno =  $conno;
        $this->session->landmark =  strtoupper($landmark);
        $this->session->postal =  strtoupper($postal);
        $this->session->citymunCode =  $citymunCode;

        if($cartItems != []){

            $shippingfee = 0;
            $available = 0;
            $temp_branch_orders = array();
            $shipping_per_shop_arr = array();
            $totok_api_shipping_logs_arr = array();
            foreach($cartItems as $shop) {
                // for conversion rate
                $this->conversionrate->logConversionRate('rc',$shop["shopid"]);
                
                // foreach($shop['items'] as $k => $v){
                //   if(array_key_exists('is_prov_variant', $v)&&$v['is_prov_variant']==1){
                //     $shop['items'][$k]['productid'] = $v['prov_id'];
                //   }
                // }
                // if(ini() == "jcww") { x - start
                //   $totamt = $this->session->userdata("total_amount");
                //   $areaId = $this->model->getAreaById($areaId);
                //   $cartItems[$shop["shopid"]]["shippingfee"] = getShippingRateJCWW($areaId, $totamt);
                //   $cartItems[$shop["shopid"]]["shopdts"] = "1";
                //   array_push($newCart, $cartItems[$shop["shopid"]]);
                // }
                // else if(ini() == "coppermask") {
                //   $cartItems[$shop["shopid"]]["shippingfee"] = 180.00;
                //   $cartItems[$shop["shopid"]]["shopdts"] = "5";
                //   $cartItems[$shop["shopid"]]["shopdts_to"] = "10";
                //   array_push($newCart, $cartItems[$shop["shopid"]]);
                // }
                // else {
                    //NEW SHIPPING LOGIC
                    // $all_items_available = true;
                    if((!isset($this->session->hash_address) || $this->session->hash_address != $hash_address)){
                      $productTotal = $this->getProductTotals($shop["items"]);
                      $shippingPerShop = $this->calculateShipping($shop["shopid"], $shop["items"], $citymunCode, $productTotal["total_weight"], $productTotal["total_amount"],$latitude,$longitude);
                      if(allow_toktok_shipping() == 1 && allow_google_addr() == 1){
                        $city = $this->ordersModel->get_citymun($citymunCode);
                        if($city->num_rows() > 0){
                          // only execute if delivery is inside manila
                          // if($city->row()->regDesc == 13){
                            $curl_data = array();
                            if($shippingPerShop['branchid'] == 0){
                              $shopDetails = $this->ordersModel->getShopDetails($shop["shopid"]);
                            }else{
                              $shopDetails = $this->ordersModel->get_branch_details($shippingPerShop['branchid']);
                              if($shopDetails->num_rows() > 0){
                                $shopDetails = $shopDetails->row_array();
                              }
                            }
                            if($city->row()->regDesc == $shopDetails['region']){
                              $curl_data['shopid'] = $shop['shopid'];
                              $curl_data['des_lat'] = $latitude;
                              $curl_data['des_lng'] = $longitude;
                              $curl_data['origin_lat'] = $shopDetails['latitude'];
                              $curl_data['origin_lng'] = $shopDetails['longitude'];
                              $curl_data['date_today'] = today();
                              $curl_data['signature'] = en_dec('en',md5($this->totok_shipping_api_key().$shop['shopid'].today()));
                              // die(cpshop_api_url().'api/Shipping/get_toktok_shipping');

                              $ch = curl_init();
                              curl_setopt($ch, CURLOPT_URL, get_apiserver_link().'api/Shipping/get_toktok_shipping');
                              curl_setopt($ch, CURLOPT_POST, 1);
                              curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curl_data));
                              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                              $server_output = curl_exec($ch);
                              curl_close($ch);
                              // echo $server_output;
                              $res = json_decode($server_output);
                              // print_r($res);

                              // for toktok shipping logs
                              $totok_api_shipping_logs_data = array(
                                "refnum" => "",
                                "response" => $server_output,
                                "shopid" => $shop['shopid'],
                                "date_created" => today()
                              );
                              $totok_api_shipping_logs_arr[$shop['shopid']] = $totok_api_shipping_logs_data;
                              // end

                              if($res->success == 1){
                                if($res->price > 0 || $res->price != null || $res->price != "")
                                  $shippingPerShop['shippingfee'] = floatval($res->price);
                              }
                            }

                          // }
                        }

                      }
                    }else{
                      // if(isset($this->session->referral)){
                      //   $productTotal = $this->getProductTotals($shop["items"]);
                      //   $shippingPerShop = $this->calculateShipping($shop["shopid"], $shop["items"], $citymunCode, $productTotal["total_weight"], $productTotal["total_amount"],$latitude,$longitude);
                      //   if(allow_toktok_shipping() == 1 && allow_google_addr() == 1){
                      //     $city = $this->ordersModel->get_citymun($citymunCode);
                      //     if($city->num_rows() > 0){
                      //       // only execute if delivery is inside manila
                      //       // if($city->row()->regDesc == 13){
                      //         $curl_data = array();
                      //         if($shippingPerShop['branchid'] == 0){
                      //           $shopDetails = $this->ordersModel->getShopDetails($shop["shopid"]);
                      //         }else{
                      //           $shopDetails = $this->ordersModel->get_branch_details($shippingPerShop['branchid']);
                      //           if($shopDetails->num_rows() > 0){
                      //             $shopDetails = $shopDetails->row_array();
                      //           }
                      //         }
                      //
                      //         if($city->row()->regDesc == $shopDetails['region']){
                      //           $curl_data['shopid'] = $shop['shopid'];
                      //           $curl_data['des_lat'] = $latitude;
                      //           $curl_data['des_lng'] = $longitude;
                      //           $curl_data['origin_lat'] = $shopDetails['latitude'];
                      //           $curl_data['origin_lng'] = $shopDetails['longitude'];
                      //           $curl_data['date_today'] = today();
                      //           $curl_data['signature'] = en_dec('en',md5($this->totok_shipping_api_key().$shop['shopid'].today()));
                      //           // die(cpshop_api_url().'api/Shipping/get_toktok_shipping');
                      //
                      //           $ch = curl_init();
                      //           curl_setopt($ch, CURLOPT_URL, get_apiserver_link().'api/Shipping/get_toktok_shipping');
                      //           curl_setopt($ch, CURLOPT_POST, 1);
                      //           curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curl_data));
                      //           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                      //           $server_output = curl_exec($ch);
                      //           curl_close($ch);
                      //           // echo $server_output;
                      //           $res = json_decode($server_output);
                      //           // print_r($res);
                      //
                      //           // for toktok shipping logs
                      //           $totok_api_shipping_logs_data = array(
                      //             "refnum" => "",
                      //             "response" => $server_output,
                      //             "shopid" => $shop['shopid'],
                      //             "date_created" => today()
                      //           );
                      //           $totok_api_shipping_logs_arr[$shop['shopid']] = $totok_api_shipping_logs_data;
                      //           // end
                      //
                      //           if($res->success == 1){
                      //             if($res->price > 0 || $res->price != null || $res->price != "")
                      //               $shippingPerShop['shippingfee'] = floatval($res->price);
                      //           }
                      //         }
                      //
                      //       // }
                      //     }
                      //
                      //   }
                      // }else{
                      //   $shippingPerShop = $this->session->shipping_per_shop[$shop["shopid"]];
                      // }

                      $shippingPerShop = $this->session->shipping_per_shop[$shop["shopid"]];
                    }

                    // die();

                    // for branch auto assign, 0 = main shop
                    if($shippingPerShop != null){
                      $temp_branchids = array(
                        "shopid" => $shop['shopid'],
                        "branchid" => $shippingPerShop['branchid'],
                        "order_refnum" => ""
                      );

                      $temp_branch_orders[] = $temp_branchids;
                    }

                    // session calculated shipping for later use
                    if((!isset($this->session->hash_address) || $this->session->hash_address != $hash_address) && $shippingPerShop != null){
                      $shipping_per_shop_arr[$shop['shopid']] = $shippingPerShop;
                    }

                    // Count the total unavailable items in the shop
                    $unavailable = 0;
                    $cartItems[$shop["shopid"]]["items_availability"] = 1;
                    if(count((array)$shippingPerShop) > 0){
                      foreach($shippingPerShop['items'] as $row){
                        if($row['available'] == 1){
                          $available += 1;
                        }
                        if($row['available'] == 0 || $row['available'] == 2){
                          $unavailable += 1;
                        }
                      }
                      // if all items in the shop are not available , set shop to unserviceable area
                      if(count((array)$shippingPerShop['items']) == $unavailable){
                        $cartItems[$shop["shopid"]]["items_availability"] = 0;
                      }
                    }

                    if($shippingPerShop != null) {
                      $cartItems[$shop["shopid"]]["shippingfee"] = $shippingPerShop["shippingfee"];
                      $cartItems[$shop["shopid"]]["shopdts"] = $shippingPerShop["from_dts"];
                      $cartItems[$shop["shopid"]]["shopdts_to"] = $shippingPerShop["to_dts"];
                      $cartItems[$shop["shopid"]]["items"] = $shippingPerShop["items"];
                      // foreach($cartItems[$shop["shopid"]]['items'] as $k => $v){
                      //   if(array_key_exists('is_prov_variant', $v)&&$v['is_prov_variant']==1){
                      //     $cartItems[$shop["shopid"]]['items'][$k]['productid'] = $v['orig_id'];
                      //   }
                      // }
                      array_push($newCart, $cartItems[$shop["shopid"]]);

                      $areaDetails = $this->model->get_region_prov($citymunCode);
                      $this->session->regCode =  $areaDetails["regDesc"];
                      $this->session->provCode =  $areaDetails["provCode"];
                    }
                    else {
                      // $shopDetails = $this->ordersModel->getShopDetails($shop["shopid"]);
                      // $cartItems[$shop["shopid"]]["items"] = $shippingPerShop["items"];
                      // $cartItems[$shop["shopid"]]["shippingfee"] = $shopDetails["shippingfee"];
                      // $cartItems[$shop["shopid"]]["shopdts"] = $shopDetails["daystoship"];

                      array_push($removedCart, $cartItems[$shop["shopid"]]);

                      // remove shops voucherDetails if there's any
                      if ($validVouchers && sizeof($validVouchers) > 0) {
                        $vIndex = array_column($validVouchers, 'shopid');
                        $vIndex = array_search($shop["shopid"], $vIndex);
                        array_splice($validVouchers, $vIndex, 1);
                      }
                    }

                    //OLD SHIPPING LOGIC
                    // $shippingPerShop = $this->model->getShippingPerShop($areaId,$shop["shopid"])->row_array();

                    // if($shippingPerShop != null) {
                    //   $cartItems[$shop["shopid"]]["shippingfee"] = $shippingPerShop["sf"];
                    //   $cartItems[$shop["shopid"]]["shopdts"] = $shippingPerShop["dts"];
                    //   array_push($newCart, $cartItems[$shop["shopid"]]);
                    // }
                    // else {
                    //   $shopDetails = $this->ordersModel->getShopDetails($shop["shopid"]);
                    //   $cartItems[$shop["shopid"]]["shippingfee"] = $shopDetails["shippingfee"];
                    //   $cartItems[$shop["shopid"]]["shopdts"] = $shopDetails["daystoship"];

                    //   array_push($removedCart, $cartItems[$shop["shopid"]]);
                    // }
                    //OLD SHIPPING LOGIC
                // } x - end
            }

            // print('<pre>'.print_r($shipping_per_shop_arr,true).'</pre>');
            // print('<pre>'.print_r($totok_api_shipping_logs_arr,true).'</pre>');
            // die();

            // set shipping fee per shop on session if not equal to null
            if((!isset($this->session->hash_address) || $this->session->hash_address != $hash_address) && $shippingPerShop != null){
              $this->session->set_userdata('shipping_per_shop', $shipping_per_shop_arr);
              $this->session->set_userdata('hash_address', $hash_address);
            }

            // set toktok shipping api to session if available
            if(count($totok_api_shipping_logs_arr) > 0){
              $this->session->set_userdata('toktok_api_shipping_logs',$totok_api_shipping_logs_arr);
            }

            $this->session->set_userdata("temp_branch_orders", $temp_branch_orders);
            $this->session->set_userdata("cart", $cartItems);
            $this->session->set_userdata("newValidVouchers", $validVouchers);
        }

        $cart = $this->session->userdata('cart');
        // print('<pre>'.print_r($newCart,true).'</pre>');
        // die();

        $data = array(
          'cart' => $cart,
          'newCart' => $newCart,
          'removedCart' => $removedCart,
          'newValidVouchers' => $validVouchers,
          'available' => $available
        );
        echo json_encode($data);
    }

    private function claimVoucher($shopid, $vcode, $order_ref_num) {
        $this->load->model('VouchersModel');
        $voucher = $this->VouchersModel->getAvailableVoucher($shopid, $vcode);

        $voucher_details = array(
            'shopid' => $voucher['shopid'],
            'shopcode' => $voucher['shopcode'],
            'vrefno' => $voucher['vrefno'],
            'vcode' => $voucher['vcode'],
            'vamount' => $voucher['vamount'],
            'date_issue' => $voucher['date_issue'],
            'date_valid' => $voucher['date_valid'],
        );

        $use_details = array(
            'date_used' => todaytime(),
            'use_orderref' => $order_ref_num,
            'use_in' => 'Shoplink',
            'others' => 'claimed via shop',
            'status' => '1',
            'claim_status' => '3'
        );

        // update 8_wallet_vouchers and v_wallet_all
        $this->VouchersModel->updateClaimedVoucher($voucher['shopid'], $voucher['vcode'], $use_details);

        // insert to v_wallet_claimed
        $this->VouchersModel->insertClaimedVoucher(array_merge($voucher_details, $use_details));

        // delete record in v_wallet_available
        $this->VouchersModel->deleteClaimedVoucher($voucher['shopid'], $voucher['vcode']);

        return array(
          'claim_voucher' => 'success'
        );
    }

    public function getPaymentType($paycode) {

      $paytypeid = 0;

      switch($paycode) {
        case "ob": $paytypeid = 29; break;
        case "otcb": $paytypeid = 30; break;
        case "otcnb": $paytypeid = 31; break;
        case "ccdb": $paytypeid = 22; break;
        case "wechatpay": $paytypeid = 34; break;
        case "mobilepay": $paytypeid = 32; break;
        case "ewallets": $paytypeid = 32; break;
        case "alipay": $paytypeid = 33; break;
        case "ewllt": $paytypeid = 35; break;
        default: break;
      }

      return $paytypeid;
    }

    public function auto_assign_branch($order, $branches) {
      // $shop = $this->ordersModel->getShopDetails($shopid);

      $branch_assigned_to = array();

      $citymunCode = $order['citymunCode'];
      $provCode = $order['provCode'];
      $regCode = $order['regCode'];
      if ($provCode == 0) {
        $provCode = $this->model->get_specific_city($order['citymunCode'])['provCode'];
      }
      if ($regCode == 0) {
        $regCode = $this->model->get_specific_city($order['citymunCode'])['regCode'];
      }

      $col_region = array_column($branches, 'delivery_region');
      $search_region = column_multiple_search($regCode, $col_region);

      $col_province = array_column($branches, 'delivery_province');
      $search_province = column_multiple_search($provCode, $col_province);

      $col_city = array_column($branches, 'delivery_city');
      $search_city = column_multiple_search($citymunCode, $col_city);

      // set $branch_assigned_to if found in search_region
      if (sizeof($search_region) > 0) {
        $branch_assigned_to = array();
        if(sizeof($search_region) > 1){
          for ($i=0; $i < count((array)$search_region); $i++) {
            $branch_assigned_to[] = $branches[$search_region[$i]];
          }
        }else{
          $branch_assigned_to[] = $branches[$search_region[0]];
        }
      }

      // set $branch_assigned_to if found in search_province
      // will override previously assigned if there's any
      if (sizeof($search_province) > 0) {
        $branch_assigned_to = array();
        if(sizeof($search_province) > 1){
          for ($i=0; $i < count((array)$search_province); $i++) {
            $branch_assigned_to[] = $branches[$search_province[$i]];
          }
        }else{
          $branch_assigned_to[] = $branches[$search_province[0]];
        }
      }

      // set $branch_assigned_to if found in search_city
      // will override previously assigned if there's any
      if (sizeof($search_city) > 0) {
        $branch_assigned_to = array();
        if(sizeof($search_city) > 1){
          for ($i=0; $i < count((array)$search_city); $i++) {
            $branch_assigned_to[] = $branches[$search_city[$i]];
          }
        }else{
          $branch_assigned_to[] = $branches[$search_city[0]];
        }
      }

      return $branch_assigned_to;
    }

    public function paymentRedirect() {

      // Check if valid get method
      if (!isset($_GET)) {
          show_404();
          die();
      }

      $data['categories'] = $this->model->getCategories()->result_array();
      $data['get_banners'] = $this->model->get_banners();

      // Missing $_GET data
      if (sizeof($this->input->get()) == 1 && array_key_exists('reference_num', $this->input->get())) {

          // Check if reference_num is not empty
          $reference_num = en_dec('dec', $this->input->get('reference_num', TRUE));
          if (!$reference_num) {
            redirect(base_url());
            die();
          }

          // and if is existing
          $is_exists = $this->ordersModel->isRefNumExist($reference_num);

          if ($is_exists !== '1') {
              redirect(base_url());
              die();
          }

          // Get transaction record
          $transaction = $this->ordersModel->getByRefNum($reference_num);

          // Get voucher details
          $voucherAmount = 0.00;
          $vouchers = $this->ordersModel->getOrderVoucher($transaction["reference_num"]);
          if (sizeof($vouchers) > 0) {
            // loop through voucher records, if payment_type is gogome add amount to voucherAmount
            foreach($vouchers as $key => $value) {
              if ($value['payment_type'] == 'Shoplink') {
                $voucherAmount += floatval($value['amount']);
              }
            }
          }
          // Make sure that transaction is not yet settled
          $total_amount = (floatval($transaction['total_amount']) - floatval($voucherAmount)) + floatval($transaction['delivery_amount']);

          $params = array(
              'merchant_id'      => $this->paypanda->get_merchant_key(),
              'payment_choice'   => '1',
              'reference_number' => $transaction['reference_num'],
              'email_address'    => $transaction['email'],
              'payer_name'       => $transaction['name'],
              'mobile_number'    => $transaction['conno'],
              'amount_to_pay'    => $total_amount,
              'currency'         => 'PHP',
              'remarks'          => '',
              'signature'        => $this->paypanda->generate_signature($transaction['reference_num'], $total_amount)
          );

          $data = array(
              'params' => $params,
              'title' => 'Payment Redirect',
              'token' => en_dec('en', uniqid())
          );

          $data['categories'] = $this->model->getCategories()->result_array();
          $data['get_banners'] = $this->model->get_banners();
          $data['transaction'] = $transaction;
          $this->load->view('orders/payment-redirect', $data);
      } else {
          redirect(base_url());
          die();
      }

    }

    public function update_invtrans_branch($orders,$branch = 0){
      if(count((array)$orders) > 0){
        foreach($orders as $order){
          $this->itemsModel->update_invtrans_branch($order,$branch);
        }
      }
    }

    function send_sms($order) {

      $message = "Hello! You have a new order on your ".get_company_name()." Shoplink ".$order["idno"]." from ".$order["name"]." with REF#".$order['reference_num']." amounting to ".number_format($order['total_amount'], 2, ".", ",");
      $this->sms->sendSMS(4, $order["dis_mobile"], $message, 'JC.');
    }

    public function get_prov_variant_price(){
      $result=array();
      $display=$this->input->post('display');
      $provCode=$this->input->post('provCode');
      $cart_item=$this->input->post('item');
      if(!empty($cart_item)){
        foreach($cart_item as $key=>$value) {
            $result[$key]['productid']=$value['productid'];
            $result[$key]['shopid']=$value['shopid'];
            $price = $this->model->get_prov_variant_price($result[$key]['productid'],$provCode);
            if($price==""){
              $default_price = $this->model->get_default_price($result[$key]['productid']);
              $result[$key]['price']=$default_price;
              $result[$key]['is_prov_variant']=0;
            }else{
              $result[$key]['price']=$price;
              $result[$key]['is_prov_variant']=1;
            }
        }
      }

      $cartItems = $this->session->cart;
      foreach($result as $key=>$val) {
         foreach($cartItems[$val['shopid']]['items'] as $key => $item){
          if($item['productid'] == $val['productid']){
            $cartItems[$val["shopid"]]["items"][$key]["price"] = $val["price"];
            $cartItems[$val["shopid"]]["items"][$key]["total_amount"] = $item["quantity"] * $val["price"];
            $cartItems[$val["shopid"]]["items"][$key]["is_prov_variant"] = $val["is_prov_variant"];
            if($val['is_prov_variant']==1){
              $provid=$this->model->get_prov_variant_id($val['productid'],$provCode);
              $cartItems[$val["shopid"]]["items"][$key]["prov_id"] = $provid;
              $cartItems[$val["shopid"]]["items"][$key]["orig_id"] = $cartItems[$val["shopid"]]["items"][$key]["productid"];
            }else{
              $cartItems[$val["shopid"]]["items"][$key]["prov_id"] = "";
              $cartItems[$val["shopid"]]["items"][$key]["orig_id"] = "";
            }
          }
        }
      }

      $this->session->cart = [];
      $this->session->cart = $cartItems;
      generate_json($result);
    }

    public function get_prov_variant_price_backup(){
      $result=array();
      $display=$this->input->post('display');
      $provCode=$this->input->post('provCode');
      $cart_item=$this->input->post('item');
      if(!empty($cart_item)){
        foreach($cart_item as $key=>$value) {
            $result[$key]['productid']=$value['productid'];
            $result[$key]['shopid']=$value['shopid'];
            $price = $this->model->get_prov_variant_price($result[$key]['productid'],$provCode);
            if(empty($price)){
              $default_price = $this->model->get_default_price($result[$key]['productid']);
              $result[$key]['price']=$default_price[0]['price'];
              $result[$key]['new_productid']=$default_price[0]['Id'];
              $result[$key]['is_prov_variant']=0;
            }else{
              $result[$key]['is_prov_variant']=1;
              $result[$key]['price']=$price[0]['price'];
              $result[$key]['new_productid']=$price[0]['Id'];
            }
        }
      }

      $cartItems = $this->session->cart;
      foreach($result as $key=>$val) {
         foreach($cartItems[$val['shopid']]['items'] as $key => $item){
          if($item['productid'] == $val['productid']){
            $cartItems[$val["shopid"]]["items"][$key]["price"] = $val["price"];
            $cartItems[$val["shopid"]]["items"][$key]["total_amount"] = $item["quantity"] * $val["price"];
            if(array_key_exists('is_prov_variant', $val)&&$val['is_prov_variant']==1){
              $cartItems[$val["shopid"]]["items"][$key]["is_prov_variant"] = 1;
              $cartItems[$val["shopid"]]["items"][$key]["productid"] = $val["new_productid"];
            }
          }else{
            if(array_key_exists('is_prov_variant', $val)&&$val['is_prov_variant']==0){
              $revert_id = $this->model->get_parent_id($item['productid']);
              if(!empty($revert_id)){
                if($val['new_productid']==$revert_id[0]['parent_product_id']){
                  $cartItems[$val["shopid"]]["items"][$key]["price"] = $revert_id[0]["price"];
                  $cartItems[$val["shopid"]]["items"][$key]["total_amount"] = $item["quantity"] * $revert_id[0]["price"];
                  $cartItems[$val["shopid"]]["items"][$key]["is_prov_variant"] = 0;
                  $cartItems[$val["shopid"]]["items"][$key]["productid"] = $revert_id[0]['parent_product_id'];
                }
              }
            }
          }
        }
      }

      $this->session->cart = [];
      $this->session->cart = $cartItems;
      generate_json($result);
    }

    // public function paypanda_postback_test(){
    //   $reference_number = "SPD9801593247110";
    //   $paypanda_refno = "123123123123";
    //   $paid_amount = "2079.00";
    //   $status = "S";
    //   $signature = $this->paypanda->validate_signature($reference_number, $paypanda_refno, $status, $paid_amount);

    //   //validate paypanda signature
    //   if($signature == $this->paypanda->validate_signature($reference_number, $paypanda_refno, $status, $paid_amount)){
    //     //get order details by reference number
    //     $order = $this->ordersModel->getByRefNum($reference_number);

    //     if($order == null || empty($order)) {
    //       echo json_encode(array("status" => "Order reference number not found."));
    //       http_response_code(200);
    //       die();
    //     }

    //     switch($status){
    //       case "S":
    //           //Check if order is not yet paid
    //           if($order["payment_status"] != 1){

    //             //initialize variables
    //             $orderDRItem = array();
    //             $shopsArr = array();
    //             $shopItems = array();

    //             //get order items
    //             $orderItems = $this->ordersModel->getOrderDetails(null,null,$order["order_id"])->result_array();
    //             $sales_order_items = $orderItems;

    //             //loop through order items
    //             foreach($orderItems as $item){

    //               //get shop details of each item
    //               $shopDetails = $this->ordersModel->getShopDetails($item["sys_shop"]);

    //               //if new shop, create shop details
    //               if(empty($shopItems[$item["sys_shop"]])) {
    //                 $shopItems[$item["sys_shop"]] = array(
    //                   "shopname" => $shopDetails["shopname"],
    //                   "shopcode" => $shopDetails["shopcode"],
    //                   "shopemail" => $shopDetails["email"],
    //                   "shopmobile" => $shopDetails["mobile"],
    //                   "shopdts" => $shopDetails["daystoship"],
    //                   "shopemail" => $shopDetails["email"],
    //                 );
    //                 $shopItems[$item["sys_shop"]]["items"] = array();
    //               }

    //               //push product item into shop object
    //               array_push($shopItems[$item["sys_shop"]]["items"], array(
    //                       "productid" => $item["product_id"],
    //                       "itemname" => $item["itemname"],
    //                       "unit" => $item["otherinfo"],
    //                       "quantity" => $item["quantity"],
    //                       "price" => $item["amount"]
    //               ));

    //               //START SPECIAL HANDLING FOR JCWW
    //               //get shopcode to check if item belongs to JCWW
    //               $shopCode = $this->ordersModel->getShopCode($item["sys_shop"]);
    //               if($shopCode == 'JCWW') {
    //                 $invID = $this->ordersModel->getInventoryID($item["product_id"]);
    //                 array_push($orderDRItem, array(
    //                   "itemid" => $invID,
    //                   "qty" => $item["quantity"],
    //                   "price" => $item["amount"],
    //                   "discamt" => 0,
    //                   "disctype" => 0,
    //                   "subtotal" => $item["total_amount"],
    //                 ));
    //               }
    //               //END SPECIAL HANDLING FOR JCWW

    //               //store all shops that belong to the order
    //               array_push($shopsArr, $item["sys_shop"]);

    //               //Update product inventory
    //               $this->updateProductInventory($item);
    //             }

    //             //get distict shops in the order
    //             $shopsArr = array_unique($shopsArr);

    //             //START SPECIAL HANDLING FOR JCWW
    //             $jcww_sono = 0;
    //             //check if there are JCWW items in the order
    //             if(!empty($orderDRItem)) {
    //               //Prepare sales order object for JCW api call
    //               $orderDR = array(
    //                 "idno" => $this->idno_for_ecommerce(),
    //                 "shipping_id" => 0,
    //                 "itemlocid" => 0,
    //                 "sales_date" => date("Y-m-d H:i:s"),
    //                 "totalamt" => $order["total_amount"],
    //                 "freight" => $order["delivery_amount"],
    //                 "discamt" => 0,
    //                 "gen_disc" => 0,
    //                 "gendisctype" => 0,
    //                 "name" => $order["fullname"],
    //                 "conno" => $order["conno"],
    //                 "email" => $order["email"],
    //                 "address" => $order["address"],
    //                 "instructions" => $order["notes"],
    //               );
    //               $orderDR["orderItems"] = $orderDRItem;
    //               $orderDR["signature"] = en_dec("en", md5($this->idno_for_ecommerce().$this->sk_api_key().round($order["total_amount"])));
    //               $ch = curl_init();
    //               curl_setopt($ch, CURLOPT_URL, $this->sk_api_url());
    //               curl_setopt($ch, CURLOPT_POST, 1);
    //               curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($orderDR));
    //               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //               $server_output = curl_exec($ch);
    //               curl_close ($ch);
    //               $response = json_decode($server_output);
    //               $jcww_sono = $response->sono;
    //             }
    //             //END SPECIAL HANDLING FOR JCWW

    //             //prepare payment details for updating app_order_details table
    //             $data = array(
    //               "paypanda_ref" => $paypanda_refno,
    //               "payment_status" => 1,
    //               "payment_date" => date("Y-m-d H:i:s"),
    //               "admin_sono" => $jcww_sono,
    //             );

    //             //update record in app_order_details to paid
    //             $this->ordersModel->payOrder($reference_number, $data);

    //             $order["admin_sono"] = $jcww_sono;
    //             $order["payment_status"] = "Paid";
    //             $order["payment_date"] = date("Y-m-d H:i:s");
    //             $order["shopItems"] = $shopItems;

    //             //prepare payment details for updating app_referral_codes table
    //             $referral_data = array(
    //               "payment_status" => 1,
    //               "payment_date" => date("Y-m-d H:i:s")
    //             );
    //             //update record in app_referral_codes to paid
    //             $this->ordersModel->updateReferenceCode($reference_number, $referral_data);

    //             //START SPECIAL HANDLING FOR JC REFERRAL CODES API
    //             //Get referral code records for sending
    //             $record = $this->ordersModel->getReferralRecord($reference_number)->row_array();

    //             //check if referral code record exist
    //             if($record != null && $record != "") {
    //               $ref_codes = array();
    //               $ref_codes_details = array();

    //               //prepare referral code details for API
    //               array_push($ref_codes, array(
    //                 "idno" => strtoupper($record["referral_code"]),
    //                 "order_reference_num" => $record["order_reference_num"],
    //                 "soldto" => $record["soldto"],
    //                 "totalamount" => $record["total_amount"],
    //                 "payment_status" => 1,
    //                 "date_ordered" => $record["date_ordered"],
    //                 "payment_date" => $record["payment_date"],
    //                 "processed_date" => date('Y-m-d H:i:s'),
    //                 "is_processed" => 1,
    //                 "status" => 1
    //               ));

    //               //get order items
    //               $recordArr = $this->ordersModel->getOrderLogs($reference_number)->result_array();

    //               //loop thru the order items
    //               foreach($recordArr as $record_details) {

    //                 //prepare referral code logs array for API
    //                 array_push($ref_codes_details, array(
    //                   "idno" => strtoupper($record["referral_code"]),
    //                   "order_reference_num" => $record["order_reference_num"],
    //                   "qty" => $record_details["quantity"],
    //                   "itemid" => $this->ordersModel->getInventoryID($record_details["product_id"]),
    //                   "price" => $record_details["amount"],
    //                   "totalamount" => $record_details["total_amount"],
    //                   "comrate" => 0,
    //                   "comamount" => 0,
    //                   "trandate" => date('Y-m-d H:i:s'),
    //                   "date_ordered" => $record["date_ordered"],
    //                   "payment_date" => $record["payment_date"],
    //                   "is_processed" => 1,
    //                   "status" => 1
    //                 ));
    //               }

    //               $processDate = date('Y-m-d H:i:s');

    //               //prepare referral code data for API
    //               $referralData = array (
    //                 "signature" => en_dec_jc_api("en", md5($processDate.$this->jc_api_key())),
    //                 "date" => $processDate,
    //                 "referral_codes" => $ref_codes,
    //                 "referral_codes_details" => $ref_codes_details
    //               );

    //               //API CURL CODE
    //               $postvars = http_build_query($referralData);
    //               $ch = curl_init();
    //               curl_setopt($ch, CURLOPT_URL, $this->jc_api_url());
    //               curl_setopt($ch, CURLOPT_POST, count($referralData));
    //               curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    //               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //               $server_output = curl_exec($ch);

    //               if ($server_output === false) {
    //                   $info = curl_getinfo($ch);
    //                   curl_close($ch);
    //                   die('error occured during curl exec. Additional info: ' . var_export($info));
    //               }
    //               curl_close($ch);
    //               $response =  json_decode($server_output);

    //               //if successful response, update app_referral_codes.is_processed = 1
    //               if($response != null && $response->success) {
    //                 //Referral Code as processed
    //                 $referral_data = array(
    //                   "processed_date" => date('Y-m-d H:i:s'),
    //                   "is_processed" => 1
    //                 );
    //                 $this->ordersModel->updateReferenceCode($reference_number, $referral_data);

    //                 //Get distributor details for email sending
    //                 $dis_array = json_decode(en_dec_jc_api("dec", $response->data_res));

    //                 //Send email if details are not empty
    //                 if($dis_array != null) {

    //                   $order["idno"] = strtoupper($record["referral_code"]);
    //                   $order["dis_email"] = strtolower($dis_array->email);

    //                   $dis_name = "";
    //                   if($dis_array->fname != null && $dis_array->fname != "")
    //                     $dis_name .= $dis_array->fname." ";
    //                   if($dis_array->mname != null && $dis_array->mname != "")
    //                     $dis_name .= $dis_array->mname." ";
    //                   if($dis_array->lname != null && $dis_array->lname != "")
    //                     $dis_name .= $dis_array->lname;

    //                   $order["dis_name"] = strtoupper($dis_name);

    //                   $this->order_distributor_email_send($order, "Processing", $paypanda_refno, $shopCode);
    //                   // $this->send_email("order_distributor_email_send", $order, "Processing", $paypanda_refno, $shopCode);
    //                 }
    //               }
    //             }
    //             //END SPECIAL HANDLING FOR JC REFERRAL CODES API

    //             //START SPLIT ORDER LOGIC
    //             //loop thru each shop present in the order
    //             foreach($shopsArr as $shop){

    //               //prepare app_sales_order_details array for insertion
    //               $salesOrder = array(
    //                 "sys_shop" => $shop,
    //                 "reference_num" => $order["reference_num"],
    //                 "paypanda_ref" => $paypanda_refno,
    //                 "user_id" => $order["user_id"],
    //                 "name" => $order["name"],
    //                 "conno" => $order["conno"],
    //                 "email" => $order["email"],
    //                 "address" => $order["address"],
    //                 "notes" => $order["notes"],
    //                 "areaid" => $order["areaid"],
    //                 "regCode" => $order["regCode"],
    //                 "citymunCode" => $order["citymunCode"],
    //                 "postalcode" => $order["postalcode"],
    //                 "total_amount" => 0,
    //                 "order_status" => $order["order_status"],
    //                 "payment_status" => 1,
    //                 "payment_date" => date("Y-m-d H:i:s"),
    //                 "payment_method" => $order["payment_method"],
    //                 "date_ordered" => $order["date_ordered"],
    //                 "status" => 1
    //               );

    //               //insert records to app_sales_order_details table
    //               $sales_order_id = $this->ordersModel->insertSalesOrder($salesOrder);
    //               $total_amount_per_shop = 0;

    //               //loop thru each order item
    //               foreach($sales_order_items as $item){
    //                 if($item["sys_shop"] == $shop) {
    //                   $salesOrderItems = array(
    //                     "order_id" => $sales_order_id,
    //                     "product_id" => $item["product_id"],
    //                     "quantity" => $item["quantity"],
    //                     "amount" => $item["amount"],
    //                     "total_amount" => $item["total_amount"],
    //                     "status" => 1
    //                   );
    //                   //insert records to app_sales_order_logs table
    //                   $this->ordersModel->insertSalesOrderItem($salesOrderItems);

    //                   //get total order amount per shop.
    //                   $total_amount_per_shop += $item["total_amount"];
    //                 }
    //               }

    //               //update total_amount field in app_sales_order_details
    //               $salesOrderTotAmt = array (
    //                 "total_amount" => $total_amount_per_shop
    //               );
    //               $this->ordersModel->updateSalesOrderAmount($salesOrderTotAmt, $sales_order_id);

    //               //send email to seller
    //               $this->send_email("seller_order_done_email_send", $order, "Processing", $paypanda_refno, $shop, $shopCode);
    //             }//end of foreach
    //             //END SPLIT ORDER LOGIC

    //             //send email to client
    //             $this->send_email("order_done_email_send", $order, "Processing", $paypanda_refno, $shopCode);

    //             echo json_encode(array("status" => "Success"));
    //           } else {
    //             echo json_encode(array("status" => "Transaction is already paid."));
    //           }
    //       break;
    //       case "P":
    //         $data = array(
    //           "paypanda_ref" => $paypanda_refno,
    //           "payment_status" => 0
    //         );
    //         if($order["payment_status"] != 0){
    //           $order["payment_status"] = "Pending";
    //           $this->send_email("order_pending_email_send", $order, "Waiting for payment", $paypanda_refno);
    //           // $this->order_pending_email_send($order, "Waiting for payment", $paypanda_refno);
    //         }
    //         $this->ordersModel->payOrder($reference_number, $data);
    //         echo json_encode(array("status" => "Pending"));
    //       break;
    //       case "C":
    //         // $data = array(
    //         //   "paypanda_ref" => $paypanda_refno,
    //         //   "payment_status" => 2
    //         // );
    //         // $order["payment_status"] = "Cancelled";
    //         // $this->order_done_email_send($order, "Cancelled", $paypanda_refno);
    //         // $this->ordersModel->payOrder($reference_number, $data);
    //         // echo json_encode(array("status" => "Cancelled"));
    //       break;
    //       case "F":
    //         $data = array(
    //           "paypanda_ref" => $paypanda_refno,
    //           "payment_status" => 2
    //         );
    //         $order["payment_status"] = "Failed";
    //         $this->send_email("order_failed_email_send", $order, "Failed", $paypanda_refno);
    //         // $this->order_failed_email_send($order, "Failed", $paypanda_refno);
    //         $this->ordersModel->payOrder($reference_number, $data);
    //         echo json_encode(array("status" => "Failed"));
    //       break;
    //       default:
    //         // echo json_encode(array("status" => "Status not recognized."));
    //         http_response_code(500);
    //       break;
    //     }
    //   }else{
    //     http_response_code(500);
    //     // echo json_encode(array("status" => "wrong signature"));

    //   }
    // }
}
