<?php
class Shop extends CI_Controller {
    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Manila");
        $this->load->library('ConversionRate');
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
        $shippingPerShop = $this->model->get_general_shipping_of_branch($shopid,$branchid,$citymunCode);
        if(sizeof($shippingPerShop) == 0){
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
    public function getProductTotals($items) {

        $total_weight = 0.00;
        $total_amount = 0.00;
        foreach ($items as $item) {
            $total_weight += ($this->itemsModel->getProductWeight($item["productid"]) * $item["quantity"]);
            $total_amount += $item["total_amount"];
        }

        return array("total_weight" => $total_weight, "total_amount" => $total_amount);
    }
    public function getItems(){
      if($this->session->userdata('get_shipping_locs')!=''){
        $id = 5;
      //get products from userdata
        $data = $this->input->post("data");
        //print_r($data);
      //if search value is null/empty get the page count, else start from page 1

        //if search value is null/empty get from userdata, else get new data from database
        $products = $this->itemsModel->getProducts($id, $data);
        foreach($products['results'] as $p => $product){
          if($product['parent_product_id'] != null){
            $key = array_search($product['parent_product_id'],array_column($products['results'],'Id'));
            if($key !== false){
              unset($products['results'][$p]);
            }
          }
        }
        
        //print_r($products);
        //apply distributor rate if applicable
        $disrate = $this->session->userdata("distributorRate");
        if(!empty($disrate)) {
            foreach ($products['results'] as $key1 => $value1) {
              foreach ($disrate as $rate) {
                if($rate->itemid == $products['results'][$key1]["itemid"])
                  $products['results'][$key1]["price"] = round(floatval($products['results'][$key1]["price"]) * (1.00 - floatval($rate->discrate)),2);
              }

            }
        }

        $variants_arr = $this->itemsModel->get_product_variants();
        $primary_pics = $this->itemsModel->get_all_productimg();
        $itemid_arr = array();

        $province_code=$this->session->userdata('get_shipping_locs')!=''?(explode(',',$this->session->userdata('get_shipping_locs')))[2]:0;
        foreach($products['results'] as $key => $product){
          //$shippingPerShop = $this->calculateShipping($products['results'][$key]['sys_shop'], [], $citymunCode, 0, $products['results'][$key]['price'],$latitude,$longitude);  
          
          if($province_code!=""){
              $prov_variant = $this->model->get_prov_variant($product['Id'],$province_code);

          }
          
          //
          //$productbranchavailability = $this->model->get_custom_shipping_per_product($product['Id'],$citymunCode);
          //+print_r(count($productbranchavailability));
          if(isset($this->session->account_type) && !empty($this->session->account_type)){
            $itemid_arr[] = $this->db->escape($product['itemid']);
          }
          $index = array_search($product['Id'],array_column($primary_pics,'product_id'));
          if($index !== false){
            $products['results'][$key]['primary_pics'] = $primary_pics[$index]['primary_pics'];
          }
          $shippingtotal=0;
          //$shippingPerShop2 = $this->model->get_general_shipping_per_shop($products['results'][$key]['sys_shop'], $citymunCode);
         // print_r('???');
          //print_r($shippingPerShop2);
         //getShippingRate($shippingPerShop);
          $products['results'][$key]['price'] = $shippingtotal + $product['price'];
          

          // if($product['variant_isset'] == 1 && $product['parent_product_id'] == null && $prov_variant_price == ""){
          //   $keys = array_keys(array_column($variants_arr,'parent_product_id'),$product['Id']);
          //   $variants = array();
          //   $min = "";
          //   $min_float = 0;
          //   $max = "";
          //   $max_float = 0;
          //   $i = 0;
          //   foreach($keys as $row){
          //     $variants[] = $variants_arr[$row];
          //   }

          //   $parent = array(
          //       "Id" => $product['Id'],
          //       "itemname" => $product['itemname'],
          //       "itemid" => $product['itemid'],
          //       "parent_product_id" => $product['Id'],
          //       "price" => $product['price'],
          //   );

          //   $variants[]=$parent;
          //   foreach($variants as $variant){
          //     if($i == 0){
          //       $min = floatval($variant['price']).','.$variant['itemid'];
          //       $min_float = floatval($variant['price']);

          //       $max = floatval($variant['price']).','.$variant['itemid'];
          //       $max_float = floatval($variant['price']);
          //     }else{
          //       if($min_float > floatval($variant['price'])){
          //         $min = floatval($variant['price']).','.$variant['itemid'];
          //         $min_float = floatval($variant['price']);
          //       }

          //       if($max_float < floatval($variant['price'])){
          //         $max = floatval($variant['price']).','.$variant['itemid'];
          //         $max_float = floatval($variant['price']);
          //       }
          //     }

          //     $i++;
          //   }

          //   $products['results'][$key]['min'] = $min;
          //   $products['results'][$key]['max'] = $max;
          // }

          if($product['variant_isset'] == 1 && $product['parent_product_id'] == null && !empty($prov_variant)){
            $products['results'][$key]['no_of_stocks'] = $prov_variant[0]['stocks'];
            $products['results'][$key]['price'] = $prov_variant[0]['price'];
            $products['results'][$key]['Id'] = $prov_variant[0]['Id'];
            $products['results'][$key]['otherinfo'] = $prov_variant[0]['otherinfo'];
            $products['results'][$key]['itemid'] = $prov_variant[0]['itemid'];
            $products['results'][$key]['sys_shop'] = $prov_variant[0]['sys_shop'];
            $products['results'][$key]['max_qty_isset'] = $prov_variant[0]['max_qty_isset'];
            $products['results'][$key]['max_qty'] = $prov_variant[0]['max_qty'];
            $products['results'][$key]['variant_isset'] = $prov_variant[0]['variant_isset'];
            $products['results'][$key]['min'] = null;
            $products['results'][$key]['max'] = null;
          }

          else{
            $products['results'][$key]['min'] = null;
            $products['results'][$key]['max'] = null;
          }
          $products['results'][$key]['shippingfee'] = $shippingtotal;
          
         // print_r(sizeof($productbranchavailability));
        }


        $page = $this->input->get("page", TRUE);

      //get total count of products for pagination
        $total = $products['total'];

      //page variable starts at 1 but array index starts at 0, deduct 1 for pagination
        $page = ($page - 1) * 5;
        //print_r($products['results']);
        $result = array(
        //slice array for number per page
        // 'data' => array_slice($products, $page, 5),
            'data' => $products['results'],
        //divide total count of product and round it up for numbers of page needed
        // 'totalRecords' => $total > 10 ? round(floatval($total) / 10, 0, PHP_ROUND_HALF_UP) : 1,
            'totalRecords' => $total > 20 ? ceil($total/20) : 1,
            'page' => $page,
        );

        
        generate_json($result);
      }
    }

    function getCartItems($return = false){
        $cart = $this->session->userdata('cart');
        //print_r($cart);
        
        $latitude='';
        $longitude='';
        $citymunCode=(explode(',',$this->session->userdata('get_shipping_locs')))[1];
        // $total_amount = 0;
        if(!empty($cart)){
          foreach ($cart as $key => $value) {
            $shopprice=0;
            $itemcount = 0;
            // $shippingPerShop = $this->calculateShipping($cart[$key]['shopid'], [], $citymunCode, 0, $shopprice,$latitude,$longitude);  
            // foreach ($cart[$key]['items'] as $items => $carts) {
            //   //print_r($cart[$key]['items'][$items]);
            //   $shopprice = (floatval($cart[$key]['items'][$items]['price']));
            //   //print_r(floatval($cart[$key]['items'][$items]['price']).'/');
            //   //print_r($cart[$key]['items']);
            //   $total_amount = $total_amount + floatval($shopprice);
            //   $cart[$key]['items'][$items]['price']=$shopprice;
            // }
            //print_r('...'.$shopprice);
            //print_r(floatval($shopprice).'<>');
          }
        }
        // /print_r($total_amount);
        // $this->session->set_userdata('total_amount',$total_amount);
        $total_amount = $this->session->userdata('total_amount');
        $data = array(
            'cart' => $cart,
            'total_amount' => $total_amount,
            'est_date' => $this->session->userdata("est_delivery"),
        );

        if ($this->session->userdata('validVouchers')) {
            $data['validVouchers'] = $this->session->userdata('validVouchers');
        }

        if($return){
            return $data;
        }else{
            echo json_encode($data);
        }
    }

    function removeCartShop(){
        $key = $this->input->post("id");
        $shopid = $this->input->post("shopid");
        $cart = $this->session->userdata("cart");
        $cartItems = $cart[$shopid]["items"];
        $cart_count = $this->session->userdata("cart_count");
        $total_amount = $this->session->userdata("total_amount");

        foreach ($cartItems as $key => $value) {
            $itemToRemove = $cartItems[$key];
            $total_amount -= $itemToRemove["total_amount"];
            $cart_count -= $itemToRemove["quantity"];
        }
        unset($cart[$shopid]);
        $this->session->set_userdata("cart_count", $cart_count);
        $this->session->set_userdata("total_amount", $total_amount);
        $this->session->set_userdata("cart", $cart);
        $newCart = $this->getCartItems(true);
        $cartData = $this->getCartCount(true);
        echo json_encode(array('cart' => $newCart, 'cartData' => $cartData));
    }

    function removeCartItem(){
        $key = $this->input->post("id");
        $shopid = $this->input->post("shopid");
        $cart = $this->session->userdata("cart");
        $cartItems = $cart[$shopid]["items"];
        $cart_count = $this->session->userdata("cart_count");
        $itemToRemove = $cartItems[$key];
        $total_amount = $this->session->userdata("total_amount");
        $total_amount = $total_amount - $itemToRemove["total_amount"];
        array_splice($cartItems, $key, 1);
        $cart_count-= $itemToRemove["quantity"];
        if(empty($cartItems)) {
            unset($cart[$shopid]);
        }
        else {
            $cart[$shopid]["items"] = $cartItems;
        }
        $this->session->set_userdata("cart_count", $cart_count);
        $this->session->set_userdata("total_amount", $total_amount);
        $this->session->set_userdata("cart", $cart);
        $newCart = $this->getCartItems(true);
        $cartData = $this->getCartCount(true);
        echo json_encode(array('cart' => $newCart, 'cartData' => $cartData));
    }

    public function changeQuantityInCart(){
        $cart = $this->input->post("cart");
        $cart_count = $this->session->userdata("cart_count") ? $this->session->userdata("cart_count") : 0;
        $oldQuantity = $this->input->post("oldQuantity");
        $newQuantity = $this->input->post("newQuantity");
        $cart_count -= $oldQuantity;
        $cart_count += $newQuantity;
        $grandTotal = 0.00;
        foreach($cart as $item){
            $grandTotal += $item["total_amount"];
        }
        $this->session->set_userdata("cart", $cart);
        $this->session->set_userdata("total_amount", $grandTotal);
        $this->session->set_userdata("cart_count", $cart_count);

        $newCart = $this->getCartItems(true);
        $cartData = $this->getCartCount(true);
        echo json_encode(array('cart' => $newCart, 'cartData' => $cartData));
    }

    public function getProductPrice($product_id) {

        if(isset($this->session->get_products) && $this->session->get_products != []){
          $key = array_search($product_id,array_column($this->session->get_products,"Id"));
          if($key !== false){
            $product = $this->session->get_products[$key];
          }
          else{
            $product = $this->itemsModel->getProductDetails($product_id);
          }
        }else{
          $product = $this->itemsModel->getProductDetails($product_id);
        }

        //apply distributor rate if applicable
        $disrate = $this->session->userdata("distributorRate");
        if(!empty($disrate)) {
            foreach ($disrate as $rate) {
                if($rate->itemid == $product["itemid"])
                    return round(floatval($product["price"]) * (1.00 - floatval($rate->discrate)),2);
            }
            return round($product["price"], 2);
        }
        else {
            return round($product["price"], 2);
        }
    }

    function getAllowedProductQty($cartItem, $cartItems) {
        $allowedQty = 0;

        //get product max qty value
        // $productMaxQty = $this->itemsModel->getProductMaxQty($cartItem["productid"]);
        $productMaxQty = ($cartItem['max_qty_isset'] == 1) ? (int)$cartItem['max_qty'] : 0;

        //check if product has max qty set
        if($productMaxQty > 0) {
            //check if item to be added is already in cart
            if(!empty($cartItems[$cartItem["shop"]])) {
                foreach($cartItems[$cartItem["shop"]]["items"] as $key => $item) {
                    //if the item is already in the cart
                    if($item["productid"] == $cartItem["productid"]) {
                        $item_quan = $item["quantity"];
                        $cart_quan = $cartItem["quantity"];
                        if($item_quan + $cart_quan <= $productMaxQty) {
                            $allowedQty = $cartItem["quantity"];
                        }
                        else {
                            $allowedQty = $productMaxQty - $item["quantity"];
                        }
                        return $allowedQty;
                    }
                }
                //if product qty is less than or equal to set max qty
                if($cartItem["quantity"] <= $productMaxQty) {
                    $allowedQty = $cartItem["quantity"];
                }
                else {
                    $allowedQty = $productMaxQty;
                }
            }
            else
            {
                //if product qty is less than or equal to set max qty
                if($cartItem["quantity"] <= $productMaxQty) {
                    $allowedQty = $cartItem["quantity"];
                }
                else {
                    $allowedQty = $productMaxQty;
                }
            }
        }
        else {
            $allowedQty = $cartItem["quantity"];
        }

        return $allowedQty;
    }

    function insertCartItems(){
        $cartItem = $this->input->post("item");

        $this->conversionrate->logConversionRate('atc',$cartItem["shop"]); // for conversion rate
        //get cart from userdata if null/empty defaults to empty array
        $cartItems = $this->session->userdata("cart") ? $this->session->userdata("cart") : [];

        //get allowed product qty
        $allowedProductQty = intval($this->getAllowedProductQty($cartItem, $cartItems));
        $success = 1;

        //check if allowed product qty is not equal to item qty
        if($allowedProductQty != $cartItem["quantity"]) {
            $success = 2;
        }

        if($allowedProductQty > 0) {

            $cart_count = $this->session->userdata("cart_count") ? $this->session->userdata("cart_count") : 0;
            $grandTotal = $this->session->userdata("total_amount") ? $this->session->userdata("total_amount") : 0.00;

            $cartItem["quantity"] = $allowedProductQty;

            // $cartItem["price"] = $this->getProductPrice($cartItem["productid"]);
            // $citymunCode=$this->session->userdata('get_shipping_locs')!=''?(explode(',',$this->session->userdata('get_shipping_locs')))[1]:0;

            //$shippingPerShop = $this->calculateShipping($products['results'][$key]['sys_shop'], [], $citymunCode, 0, $products['results'][$key]['price'],$latitude,$longitude);  
            
            // print_r($cartItem);
            // exit();
            // $shippingPerShop = $this->model->get_general_shipping_per_shop($cartItem['shop'],$citymunCode);
            // $shippingtotal=0;
            // $firstind=0;
            // foreach($shippingPerShop as $key2){
            //   if(count($shippingPerShop)>1){
                
            //     if($firstind==0){
            //       $firstind++;
            //     }else{
            //       $shippingtotal=floatval($key2['rate_amount']);
            //     }
            //   }else{
            //     $shippingtotal=$shippingtotal+floatval($key2['rate_amount']);
            //   }
            // }
            //print_r($shippingPerShop);
            //
           // if($cartItem['variant_isset'] == 1){
           //   $min_arr = explode(',',$cartItem['min']);
           //   $min = $min_arr[0];
           //   if(is_numeric($min)){
           //      $cartItem["price"]=$min;
           //   }
           // }
           $cartItem["price"] =  $this->getProductPrice($cartItem["productid"]);
           //print_r($cartItem["price"] );
            // if(count($shippingPerShop)>1){
            // $cartItem["price"] =  $this->getProductPrice($cartItem["productid"])+$shippingtotal;
            // }
            $cartItem["total_amount"] = $cartItem["price"] * $cartItem["quantity"];
            $grandTotal += $cartItem['total_amount'];
            $cart_count += $cartItem['quantity'];

            //if shop of the product is not yet on the cart
            if(empty($cartItems[$cartItem["shop"]])) {
                //get shop details
                $shopDetails = $this->ordersModel->getShopDetails($cartItem["shop"]);

                //prepare shop details for cart insertion
                $cartItems[$cartItem["shop"]] = array(
                    "shopid" => $cartItem["shop"],
                    "shopname" => $shopDetails["shopname"],
                    "shopcode" => $shopDetails["shopcode"],
                    "shopemail" => $shopDetails["email"],
                    "shopmobile" => $shopDetails["mobile"],
                    "shopdts" => $shopDetails["daystoship"],
                    "shippingfee" => $shopDetails["shippingfee"],
                    "logo" => $shopDetails["logo"],
                    "inv_threshold" => $shopDetails["inv_threshold"],
                    "check_unfulfilled_orders" => $shopDetails["check_unfulfilled_orders"],
                    "allowed_unfulfilled" => $shopDetails['allowed_unfulfilled']
                );

                //insert the product into the cart
                $cartItems[$cartItem["shop"]]["items"] = array();
                array_push($cartItems[$cartItem["shop"]]["items"], $cartItem);
            }
            else {
                $is_cart_updated = false;

                //loop through every items in cart of the same shop
                foreach($cartItems[$cartItem["shop"]]["items"] as $key => $item) {
                    //if the item is already in the cart
                    if($item["productid"] == $cartItem["productid"]) {
                        $cartItems[$cartItem["shop"]]["items"][$key]["quantity"] = $item["quantity"] + $cartItem["quantity"];
                        $cartItems[$cartItem["shop"]]["items"][$key]["total_amount"] = $item["total_amount"] + $cartItem["total_amount"];
                        // $cart_count += $cartItem["quantity"];
                        //set to through so that it wont redo
                        $is_cart_updated = true;
                    }
                    else{
                        //set to true so that it wont redo, false if not yet added
                        $is_cart_updated = $is_cart_updated == true ? true : false;
                    }
                }   // end of foreach
                //if not edited add it to the array
                if(!$is_cart_updated){
                    array_push($cartItems[$cartItem["shop"]]["items"], $cartItem);
                }
            }
              //set all to the userdata
            $this->session->set_userdata("total_amount", $grandTotal);
            $this->session->set_userdata("cart", $cartItems);
            $this->session->set_userdata("cart_count", $cart_count);
        }
        else {
            $success = 0;
        }

        $newCart = $this->getCartItems(true);
        $cartData = $this->getCartCount(true);
        echo json_encode(array('success' => $success,'cart' => $newCart, 'cartData' => $cartData));
    }

    public function change_cart_item(){
      $cart_item = $this->input->post('item');
      $cartItems = $this->session->cart;
      $product_price = $this->getProductPrice($cart_item['productid']);

      $product_price = $product_price * (float)$cart_item['quantity'];
      // echo $this->session->total_amount;
      // $allowedProductQty = intval($this->getAllowedProductQty($cart_item, $cartItems));
      $productMaxQty = $this->itemsModel->getProductMaxQty($cart_item["productid"]);
      if($productMaxQty != 0){
        if($cart_item['quantity'] > $productMaxQty){
          $data = array("success" => 0, "message" => "You have reached the purchase limit for this item.");
          generate_json($data);
          exit();
        }
      }
      // $success = 1;
      //
      // if($allowedProductQty != $cart_item["quantity"]) {
      //     $success = 2;
      // }
      //
      // if($allowedProductQty <= 0){
      //   $data = array("success" => 0, "message" => "You have reached the purchase limit for this item.");
      //   generate_json($data);
      //   exit();
      // }

      // if($allowedProductQty > 0){
        foreach($cartItems[$cart_item['shop']]['items'] as $key => $item){
          if($item['productid'] == $cart_item['productid']){
            $this->session->total_amount -= $cartItems[$cart_item["shop"]]["items"][$key]["total_amount"];
            $this->session->cart_count -= $cartItems[$cart_item["shop"]]["items"][$key]["quantity"];
            $cartItems[$cart_item["shop"]]["items"][$key]["quantity"] = (float)$cart_item["quantity"];
            $cartItems[$cart_item["shop"]]["items"][$key]["total_amount"] = $product_price;
            $this->session->total_amount += $product_price;
            $this->session->cart_count += (float)$cart_item["quantity"];
          }
        }
      // }

      if(count((array)$cartItems[$cart_item['shop']]['items']) == 0){
        unset($cartItems[$cart_item['shop']]);
      }

      // echo $this->session->total_amount;
      $this->session->cart = [];
      $this->session->cart = $cartItems;
      $cart_count = (count((array)$this->session->cart) <= 0) ? 0 :count((array)$this->session->cart);
      $data = array("success" => 1, "cart_count" => $cart_count, "total_checkout_amount" => $this->session->total_amount);
      generate_json($data);
      exit();
    }

    public function add_cart_item(){
      $cart_item = $this->input->post('item');
      $cartItems = $this->session->cart;
      $product_price = $this->getProductPrice($cart_item['productid']);
      // echo $this->session->total_amount;

      $allowedProductQty = intval($this->getAllowedProductQty($cart_item, $cartItems));
      $success = 1;

      if($allowedProductQty != $cart_item["quantity"]) {
          $success = 2;
      }

      if($allowedProductQty <= 0){
        $data = array("success" => 0, "message" => "You have reached the purchase limit for this item.");
        generate_json($data);
        exit();
      }

      if($allowedProductQty > 0){
        foreach($cartItems[$cart_item['shop']]['items'] as $key => $item){
          if($item['productid'] == $cart_item['productid']){
            if($cartItems[$cart_item["shop"]]["items"][$key]["variant_isset"]==1){
                $min_arr = explode(',',$cartItems[$cart_item["shop"]]["items"][$key]["min"]);
                if(is_numeric($min_arr[0])){
                    $product_price=$min_arr[0];
                }            
            }

            $cartItems[$cart_item["shop"]]["items"][$key]["quantity"] = $item["quantity"] + 1;
            $cartItems[$cart_item["shop"]]["items"][$key]["total_amount"] = $item["total_amount"] + $product_price;
            $this->session->total_amount += $product_price;
            $this->session->cart_count += 1;
          }
        }
      }

      // echo $this->session->total_amount;
      $this->session->cart = [];
      $this->session->cart = $cartItems;
      $data = array("success" => 1, 'total_checkout_amount' => $this->session->total_amount);
      generate_json($data);
      exit();
    }

    public function subtract_cart_item(){
      $cart_item = $this->input->post('item');
      $cartItems = $this->session->cart;
      $product_price = $this->getProductPrice($cart_item['productid']);
      // echo $this->session->total_amount;

      foreach($cartItems[$cart_item['shop']]['items'] as $key => $item){
        if($item['productid'] == $cart_item['productid']){
          if($cartItems[$cart_item["shop"]]["items"][$key]["variant_isset"]==1){
                $min_arr = explode(',',$cartItems[$cart_item["shop"]]["items"][$key]["min"]);
                if(is_numeric($min_arr[0])){
                    $product_price=$min_arr[0];
                }
          }
          
          $cartItems[$cart_item["shop"]]["items"][$key]["quantity"] = $item["quantity"] - 1;
          $cartItems[$cart_item["shop"]]["items"][$key]["total_amount"] = $item["total_amount"] - $product_price;
          $this->session->total_amount -= $product_price;
          $this->session->cart_count -= 1;

          if($cartItems[$cart_item["shop"]]["items"][$key]["quantity"] <= 0){
            // unset($cartItems[$cart_item["shop"]]["items"][$key]);
            array_splice($cartItems[$cart_item["shop"]]["items"],$key,1);
          }
        }
      }

      if(count((array)$cartItems[$cart_item['shop']]['items']) == 0){
        unset($cartItems[$cart_item['shop']]);
      }

      $allowedProductQty = intval($this->getAllowedProductQty($cart_item, $cartItems));
      $success = 1;

      if($allowedProductQty != $cart_item["quantity"]) {
          $success = 2;
      }

      if($allowedProductQty <= 0){
        $data = array("success" => 0, "message" => "You have reached the purchase limit for this item.");
        generate_json($data);
        exit();
      }

      // if($allowedProductQty > 0){
        // foreach($cartItems[$cart_item['shop']]['items'] as $key => $item){
        //   if($item['productid'] == $cart_item['productid']){
        //     $cartItems[$cart_item["shop"]]["items"][$key]["quantity"] = $item["quantity"] - 1;
        //     $cartItems[$cart_item["shop"]]["items"][$key]["total_amount"] = $item["total_amount"] - $product_price;
        //     $this->session->total_amount -= $product_price;
        //     $this->session->cart_count -= 1;
        //
        //     if($cartItems[$cart_item["shop"]]["items"][$key]["quantity"] <= 0){
        //       unset($cartItems[$cart_item["shop"]]["items"][$key]);
        //     }
        //   }
        // }
        //
        // if(count((array)$cartItems[$cart_item['shop']]['items']) == 0){
        //   unset($cartItems[$cart_item['shop']]);
        // }
      // }

      // echo $this->session->total_amount;
      $this->session->cart = [];
      $this->session->cart = $cartItems;
      $cart_count = (count((array)$this->session->cart) <= 0) ? 0 :count((array)$this->session->cart);
      $data = array("success" => 1, "cart_count" => $cart_count, "total_checkout_amount" => $this->session->total_amount);
      generate_json($data);
      exit();
    }

    public function getCartCount($return = false){
        $count = $this->session->userdata("cart_count");
        $total_amount = $this->session->userdata("total_amount");
          //conditions if null/empty it will default to 0;
        if($return){
            return array("count" => $count ? $count : 0, "total_amount" => $total_amount ? $total_amount : 0, 'est_date' => $this->session->userdata("est_delivery"));
        }else{
            echo json_encode(array("count" => $count ? $count : 0, "total_amount" => $total_amount ? $total_amount : 0, 'est_date' => $this->session->userdata("est_delivery")));
        }
    }

    public function getEstDate(){
        $areaId = $this->input->post("areaId");
          // $areas = $this->model->getEstDelivery($areaId)->row_array();
        $est_deliveryArr = "NA";
        echo json_encode(generate_est_delivery_date($est_deliveryArr));
    }

    public function get_province(){
        $regCode = sanitize($this->input->post('regCode'));

        $data = $this->model->get_province($regCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
    }

    public function get_citymun(){
        $regCode = sanitize($this->input->post('regCode'));

        $data = $this->model->get_citymun($regCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
    }

    public function get_brgy(){
        $citymunCode = sanitize($this->input->post('citymunCode'));

        $data = $this->model->get_brgy($citymunCode)->result_array();

        $response = [
            'success' => true,
            'data' => $data
        ];

        echo json_encode($response);
    }

    public function get_cities(){
      $reg_code = $this->input->post('reg_code');

      if(empty($reg_code)){
        $data = array("success" => 0, "message" => "No items available under this region");
        generate_json($data);
        exit();
      }

      $shipping_city = "";
      $city_per_prov = array();
      $reg_code = explode(',',$reg_code);
      foreach($reg_code as $region){
        $city = $this->model->get_shipping_city($region);
        if($city->num_rows() > 0){
          $cities = $city->result_array();
          $shipping_city = ($shipping_city != "") ? array_merge($shipping_city,$cities) : $cities;
          foreach($shipping_city as $key => $prov){
            $shipping_city[$key]["name"] = $prov['citymunDesc'];
            $shipping_city[$key]["type"] = "city";
            $shipping_city[$key]["value"] = $prov['citymunCode'];
            $city_per_prov[] = $prov['provCode'];
          }
        }
      }

      // print_r($shipping_city);

      // $city_count = array_count_values($city_per_prov);
      // foreach($city_count as $key => $row){
      //   if($row > 3){
      //     foreach($shipping_city as $index => $scity){
      //       if($scity['provCode'] == $key){
      //         unset($shipping_city[$index]);
      //       }
      //     }
      //     $prov = $this->model->get_shipping_prov($key);
      //     if($prov->num_rows() > 0){
      //       $province = $prov->result_array();
      //       $province[0]["name"] = $province[0]['provDesc'];
      //       $province[0]["type"] = "prov";
      //       $province[0]["value"] = $province[0]['provCode'];
      //       array_unshift($shipping_city,$province[0]);
      //     }
      //   }
      // }

      if(count((array)$shipping_city) == 0 || $shipping_city == ""){
        $data = array("success" => 0, "message" => "No available province or city");
        generate_json($data);
        exit();
      }

      // print_r($shipping_city);
      // die();

      $data = array("success" => 1, "cities" => $shipping_city);
      generate_json($data);
    }

    public function get_shipping_city_w_prov(){
      $prov_code = $this->input->post('prov_code');
      if(empty($prov_code)){
        $data = array("success" => 0, "message" => "No available city under this province");
        generate_json($data);
        exit();
      }

      $cities = $this->model->get_shipping_city_w_prov($prov_code);
      if($cities->num_rows() == 0){
        $data = array("success" => 0, "message" => "No available city");
        generate_json($data);
        exit();
      }

      $shipping_city = $cities->result_array();
      foreach ($shipping_city as $key => $value) {
        $shipping_city[$key]["name"] = $value['citymunDesc'];
        $shipping_city[$key]["type"] = "city";
        $shipping_city[$key]["value"] = $value['citymunCode'];
      }

      $data = array("success" => 1, "cities" => $shipping_city);
      generate_json($data);
    }

    //shipping
    public function get_shipping_locations() {
        if(!isset($this->session->checkout_cities) || $this->session->checkout_cities == []){
          $this->session->checkout_cities = $this->model->get_cities()->result_array();
        }
        // print('<pre>'.print_r($this->session->checkout_cities,true).'</pre>');
        $filter_cities = $this->model->get_filtered_cities();
        $new_array = array();
        if($filter_cities->num_rows() > 0){
          foreach($this->session->checkout_cities as $cities){
            foreach($filter_cities->result_array() as $filtered){
              if($cities['citymunCode'] == $filtered['citymunCode']){
                $new_array[] = $filtered;
              }
            }
          }
        } 
        $cities = (count((array)$new_array) > 0) ? $new_array : $this->session->checkout_cities;
        generate_json($cities);
    }

    public function set_session_shipping_location($page, $cities, $province, $shipped, $location){
        $location = urldecode($location);
        $location = str_replace("comma", ",", $location);
        $location = str_replace("openp", "(", $location);
        $location = str_replace("closep", ")", $location);
        

        if($location == "SELECT CITY"){
            $location = "Philippines";
            $this->session->set_userdata('get_shipping_locs', "");
        }else{
            $this->session->set_userdata('get_shipping_locs', $page.",".$cities.",".$province.",".$shipped.",".$location);
        }

        $this->session->unset_userdata('total_amount');
        $this->session->unset_userdata('cart_count');
        $this->session->unset_userdata('cart');
        $this->session->unset_userdata('est_delivery');
        $this->session->set_userdata('shipping_location', $location);
        return;
    }

    public function clear_session_shipping_location(){
      $this->session->unset_userdata('get_shipping_locs');
      $this->session->unset_userdata('shipping_location');
      $this->session->unset_userdata('total_amount');
      $this->session->unset_userdata('cart_count');
      $this->session->unset_userdata('cart');
      $this->session->unset_userdata('est_delivery');
      return;
    }
}

?>
