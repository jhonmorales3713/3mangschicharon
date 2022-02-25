<?php

class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Manila");
        $this->load->model('cmj/Model_webtraff');
        $this->load->library('Error_404');
    }

    protected function jc_api_url() {
        if (ENVIRONMENT == "production") {
            return "https://thedarkhorse.ph/tdh/api/JCReferralAPI/validate_referral_code";
        }else{
            return "https://thedarkhorse.ph/tdhtest/api/JCReferralAPI/validate_referral_code";
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
        // if(sizeof($branches) > 0){
        //   $branch_assigned_to = $this->auto_assign_branch($loc_array, $branches);
        //   // If there was 1 assigned branch send sms and email notificaiton
        //   if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) == 1){
        //     $branchid = $branch_assigned_to[0]['branchid'];
        //     // if check unfulfilled orders is on
        //     if($unfulfilled_settings['check_unfulfilled_orders'] == 1){

        //       $allowed_days = $this->ordersModel->get_specific_allowed_unfulfilled_orders($shopid,$branch_assigned_to[0]['branchid'],$citymunCode);
        //       $allowed_unfulfilled_orders = ($allowed_days == null) ? $allowed_unfulfilled_orders : $allowed_days;
        //       $unfulfilled = $this->ordersModel->get_last_unfulfilled_order($shopid,$branchid,$allowed_unfulfilled_orders);
        //       if($unfulfilled->num_rows() > 0){
        //         $yesterday = date_create(date('Y-m-d H:i:s',strtotime('Yesterday')));
        //         $date_assigned = date_create($unfulfilled->row()->date_assigned);
        //         $diff = date_diff($yesterday,$date_assigned);
        //         $days_diff = $diff->format("%a");
        //         if($days_diff >= (int)$allowed_unfulfilled_orders){
        //           // HOLD EMAIL SEND
        //           // var_dump($branch_assigned_to[0]['on_hold']);
        //           if($branch_assigned_to[0]['on_hold'] == 0){
        //             $hold_data['branchemail'] = $branch_assigned_to[0]['email'];
        //             $hold_data['branchname'] = $branch_assigned_to[0]['branchname'];
        //             $hold_data['allowed_days'] = $allowed_unfulfilled_orders;
        //             $this->send_email("hold_email_send",$hold_data);
        //             // UPDATE ON HOLD STATUS ON BRANCH
        //             $this->model->update_on_hold_status($branchid,1);
        //           }

        //           $branchid = 0;
        //         }else{
        //           if($branch_assigned_to[0]['on_hold'] == 1){
        //             // REACTIVATION EMAIL SEND
        //             $reactivation_data['branchemail'] = $branch_assigned_to[0]['email'];
        //             $reactivation_data['branchname'] = $branch_assigned_to[0]['branchname'];
        //             $reactivation_data['allowed_days'] = $allowed_unfulfilled_orders;
        //             $this->send_email("reactivation_email_send",$reactivation_data);
        //             $this->model->update_on_hold_status($branchid,0);
        //           }
        //         }
        //       }
        //     }
        //   }

        //   // If there was 2 or more assign branch
        //   if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) > 1){
        //     // check branch who can fulfill more items.
        //     $branches_that_fulfilled_all_orders = array();
        //     foreach($branch_assigned_to as $key_index => $branch){
        //       // if check unfulfilled orders is on
        //       if($unfulfilled_settings['check_unfulfilled_orders'] == 1){
        //         $allowed_days = $this->ordersModel->get_specific_allowed_unfulfilled_orders($shopid,$branch['branchid'],$citymunCode);
        //         $allowed_unfulfilled_orders = ($allowed_days == null) ? $allowed_unfulfilled_orders : $allowed_days;
        //         $unfulfilled = $this->ordersModel->get_last_unfulfilled_order($shopid,$branch['branchid'],$allowed_unfulfilled_orders);
        //         if($unfulfilled->num_rows() > 0){

        //           $yesterday = date_create(date('Y-m-d H:i:s',strtotime('Yesterday')));
        //           $date_assigned = date_create($unfulfilled->row()->date_assigned);
        //           $diff = date_diff($yesterday,$date_assigned);
        //           $days_diff = $diff->format("%a");
        //           if($days_diff < $allowed_unfulfilled_orders){

        //             if($branch['on_hold'] == 1){
        //               // REACTIVATION EMAIL SEND
        //               $reactivation_data['branchemail'] = $branch['email'];
        //               $reactivation_data['branchname'] = $branch['branchname'];
        //               $reactivation_data['allowed_days'] = $allowed_unfulfilled_orders;
        //               $this->send_email("reactivation_email_send",$reactivation_data);
        //               $this->model->update_on_hold_status($branch['branchid'],0);
        //             }

        //             $branches_that_fulfilled_all_orders[] = $branch['branchid'];
        //           }else{

        //             if($branch['on_hold'] == 0){
        //               $hold_data['branchemail'] = $branch['email'];
        //               $hold_data['branchname'] = $branch['branchname'];
        //               $hold_data['allowed_days'] = $allowed_unfulfilled_orders;
        //               $this->send_email("hold_email_send",$hold_data);
        //               // UPDATE ON HOLD STATUS ON BRANCH
        //               $this->model->update_on_hold_status($branch['branchid'],1);
        //             }
        //             // array_splice($branch_assigned_to,$key_index,1);
        //             unset($branch_assigned_to[$key_index]);
        //           }
        //         }else{

        //           if($branch['on_hold'] == 1){
        //             // REACTIVATION EMAIL SEND
        //             $reactivation_data['branchemail'] = $branch['email'];
        //             $reactivation_data['branchname'] = $branch['branchname'];
        //             $reactivation_data['allowed_days'] = $allowed_unfulfilled_orders;
        //             $this->send_email("reactivation_email_send",$reactivation_data);
        //             $this->model->update_on_hold_status($branch['branchid'],0);
        //           }

        //           $branches_that_fulfilled_all_orders[] = $branch['branchid'];
        //         }
        //       }

        //     }

        //       // REORDER ARRAY INDEX
        //     $branch_assigned_to = array_values($branch_assigned_to);

        //     // if only one branch has fulfill all its orders assign order to that branch.
        //     if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) == 1){
        //       $branchid = $branch_assigned_to[0]['branchid'];
        //     }

        //     // if multiple branch has fulfill all there orders
        //     if(count((array)$branch_assigned_to) > 0 && count((array)$branch_assigned_to) > 1){
        //       $branch_stocks = array();
        //       // print_r($branch_assigned_to);
        //       foreach($branch_assigned_to as $branch){
        //         $branch_score = 0;
        //         foreach($items as $index => $item){
        //           $no_of_stocks = $this->ordersModel->get_branch_inv($shopid,$branch['branchid'],$item['productid']);
        //           $no_of_stocks = ($no_of_stocks->num_rows() > 0) ? $no_of_stocks->row()->no_of_stocks : 0;
        //           $branch_score += ($no_of_stocks > 0) ? 1 : 0;
        //           $branch_score += ($no_of_stocks >= $item['quantity']) ? 1 : 0;
        //         }
        //         $branch_stocks[] = $branch_score;
        //       }
        //       $most_fulfill = array_keys($branch_stocks,max($branch_stocks));
        //       // if 2 or more branch can still fulfill order check google distance or pending order
        //       if(count((array)$most_fulfill) > 1){
        //         // check nearest branch on the city using google distance
        //         if(allow_google_addr() == 1 && $longitude != "" && $latitude != ""){
        //           // $dist = get_distance2($latitude,$longitude,$branch_assigned_to[0]['latitude'],$branch_assigned_to[0]['longitude']);
        //           $dist_arr = array();
        //           foreach($most_fulfill as $i){
        //             $dist = get_distance2($latitude,$longitude,$branch_assigned_to[$i]['latitude'],$branch_assigned_to[$i]['longitude']);
        //             $dist_arr[] = $dist;
        //           }
        //           // print_r($dist_arr);
        //           // die();
        //           $nearest = array_keys($dist_arr,min($dist_arr));
        //           $branch_assigned_to = $branch_assigned_to[$most_fulfill[$nearest[0]]];

        //         // check whose branch has the least to process order
        //         }else{
        //           $least_arr = array();
        //           foreach($most_fulfill as $x){
        //             // $least_arr[] = $this->model->get_branch_pending_order($shopid,$branch_assigned_to[$x]['branchid']);
        //             $least_arr[] = $branch_assigned_to[$x]['branch_pending_orders'];
        //           }
        //           $least = array_keys($least_arr,min($least_arr));
        //           // if pending orders tied get last order for each branch then get the branch with oldest order
        //           if(count((array)$least) > 1){
        //             $last_order_arr = array();
        //             foreach($least as $n){
        //               // $last_order_arr[] = strtotime($this->model->get_last_branch_ordered($shopid,$branch_assigned_to[$n]['branchid']));
        //               $last_order_arr[] = strtotime($branch_assigned_to[$n]['last_order']);
        //             }
        //             $oldest_order = array_keys($last_order_arr,min($last_order_arr));
        //             $branch_assigned_to = $branch_assigned_to[$most_fulfill[$oldest_order[0]]];

        //           // else set the least branch with least pending orders.
        //           }else{
        //             $branch_assigned_to = $branch_assigned_to[$most_fulfill[$least[0]]];
        //           }
        //         }

        //       // if only one branch can fulfill order
        //       }else{
        //         $branch_assigned_to = $branch_assigned_to[$most_fulfill[0]];
        //       }

        //       $branchid = $branch_assigned_to['branchid'];
        //     }

        //   }
        // }
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

    public function getProductTotals($items) {

        $total_weight = 0.00;
        $total_amount = 0.00;
        foreach ($items as $item) {
            $total_weight += ($this->itemsModel->getProductWeight($item["productid"]) * $item["quantity"]);
            $total_amount += $item["total_amount"];
        }

        return array("total_weight" => $total_weight, "total_amount" => $total_amount);
    }
    protected function jc_api_key() {
        return "SKSHOPCODE";
    }

    function validate_referral_code($refcode = "") {

        if($refcode != "")
            $referralCode = $refcode;
        else
            $referralCode = strtoupper($this->input->post('referral_code'));


        $referralData = array (
            "referral_code" => $referralCode,
            "signature" => en_dec_jc_api("en", md5($referralCode.$this->jc_api_key()))
        );

        $postvars = http_build_query($referralData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->jc_api_url());
        curl_setopt($ch, CURLOPT_POST, count($referralData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $response = json_decode($server_output);

        if($refcode != "")
            return json_encode($response);
        else
            echo json_encode($response);

        //Referral Code Logic *DONT DELETE
        // $res = $this->model->validate_referral_code("$referralCode")->row();

        // if($res != null) {
        //     $data = array("success"=> 1, "message" => "Referral codes processed successfully.");
        // } else {
        //     $data = array("success"=> 0, "message" => "Something went wrong.");
        // }

        // if($refcode != "")
        //     return json_encode($data);
        // else
        //     echo json_encode($data);
    }

    public function index($idno = "") {
        // $this->session->set_userdata('validVouchers', []);
        $idno = sanitize($idno); //this is the referral code 'get whatever the word is typed'

        //if client is logged in using JC, don't save referral link
        if ($this->session->userdata("user_type") != "JC"){
            if($idno != "") {
                $response = json_decode($this->validate_referral_code($idno));
                if($response->success){

                    //Get distributor details for email sending
                    $dis_array = json_decode(en_dec_jc_api("dec", $response->data_res));

                    //Send email if details are not empty
                    if($dis_array != null) {

                        $dis_name = "";
                        if($dis_array->fname != null && $dis_array->fname != "")
                        $dis_name .= $dis_array->fname." ";
                        // if($dis_array->mname != null && $dis_array->mname != "")
                        // $dis_name .= $dis_array->mname." ";
                        if($dis_array->lname != null && $dis_array->lname != "")
                        $dis_name .= $dis_array->lname;

                        $this->session->set_userdata("referral_disname", strtoupper($dis_name));
                    }else{
                        $this->session->set_userdata("referral_disname", "");
                    }


                    $this->session->set_userdata("referral", $idno);

                    $referralLog = array(
                        'idno' => $idno,
                        'ip_address' => get_ip($_SERVER),
                        'shop_id' => 0,
                        'product_id' => 0,
                        'trandate' => todaytime()
                    );

                    $referralLogId = $this->model->insertReferralLog($referralLog);
                }
            }

        } else {
            $this->session->set_userdata("referral", "");
        }

        $data['categories'] = $this->model->getCategories()->result_array();
        $data['get_banners'] = $this->model->get_banners();
        $this->load->view("includes/header", $data);
        $this->load->view("shop/shop" ,$data);
        $this->Model_webtraff->total_pageviews();
    }

    public function shoppingCart(){
        $cart = $this->session->userdata("cart_count");
        $data['categories'] = null;
        if($cart){
            $this->load->view("includes/header",$data);
            $this->load->view("shop/cart");
        }else{
            redirect(base_url());
        }
    }

    public function checkoutPage(){
        $this->session->unset_userdata('temp_branch_orders');
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
        // $data['areas'] = $this->model->getAreas()->result_array();
        $data['categories'] = $this->model->getCategories()->result_array();
        $data['get_city'] = $cities;
        $cart = $this->session->userdata("cart_count");
        if($cart){
            $this->load->view("includes/header", $data);
            $this->load->view("shop/checkout", $data);
        }else{
            redirect(base_url());
        }
    }

    function orders(){
        $userData = $this->session->userdata();
        $data['userFranchise'] = $userData["franchises"];
        $data["active_franchise"] = $userData["active_franchise"];
        $data['gg'] = "<script>console.log(".json_encode($data['userFranchise'], JSON_HEX_TAG).")</script>";
        $this->load->view("includes/header",$data);
        $this->load->view("orders/order", $data);
    }

    function checkOrderPage(){
        $data['categories'] = null;
        $this->load->view("includes/header",$data);
        $this->load->view("orders/check-order");
    }

    function checkOrder(){
        $orderDetails = $this->ordersModel->getByRefNum($this->input->get("refno", TRUE));
        $referral_codes = $this->ordersModel->get_order_ReferralRecord($this->input->get("refno", TRUE));
        if($referral_codes->num_rows() > 0){
          $orderDetails['referral_code'] = $referral_codes->row()->referral_code;
        }
        if(!empty($orderDetails)) {
            $orderItems = $this->ordersModel->getOrderDetails(null,null,$orderDetails["order_id"])->result_array();
            // dd($orderDetails);
            $shopItems = array();
            foreach($orderItems as $item){
                $shopDetails = $this->ordersModel->getShopDetails($item["sys_shop"]);
                // $shippingPerShop = $this->model->getShippingPerShop($orderDetails["areaid"],$item["sys_shop"])->row_array();
                $shippingPerShop = $this->model->getShippingPerShop($this->input->get("refno", TRUE), $item["sys_shop"])->row_array();

                $orderStatus = $this->ordersModel->getOrderStatusPerShop($this->input->get("refno", TRUE), $item["sys_shop"]);
                if($orderStatus != null && $orderStatus != "" && ini() != "jcww") {
                    $order_status = $orderStatus["order_status"];
                    $date_shipped = $orderStatus["date_shipped"];
                    $date_ordered = $orderStatus["date_ordered"];
                    $date_order_processed = $orderStatus["date_order_processed"];
                    $date_ready_pickup = $orderStatus["date_ready_pickup"];
                    $date_booking_confirmed = $orderStatus["date_booking_confirmed"];
                    $date_fulfilled = $orderStatus["date_fulfilled"];
                }
                else {
                  if($orderDetails['payment_status'] != 1){

                    $order_status = $orderDetails["order_status"];
                    $date_shipped = $orderDetails["date_shipped"];
                    $date_ordered = $orderDetails["date_ordered"];
                    $date_order_processed = '';
                    $date_ready_pickup = '';
                    $date_booking_confirmed = '';
                    $date_fulfilled = '';
                  }else{
                    $order_status = $orderStatus["order_status"];
                    $date_shipped = $orderStatus["date_shipped"];
                    $date_ordered = $orderStatus["date_ordered"];
                    $date_order_processed = $orderStatus["date_order_processed"];
                    $date_ready_pickup = $orderStatus["date_ready_pickup"];
                    $date_booking_confirmed = $orderStatus["date_booking_confirmed"];
                    $date_fulfilled = $orderStatus["date_fulfilled"];
                  }
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
                        "primary_pics" => $item["primary_pics"]
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
            $this->load->view("orders/check-order-page", $data);
        }
        else {
            $this->index();
        }

    }

    function checkRef(){
        $ref = sanitize($this->input->post("ref"));
        if($ref != "") {
            $orderDetails = $this->ordersModel->searchByRefNum($ref);
            $branch_orders = $this->ordersModel->get_branch_orders($ref);

            // if order has branch orders use gogome track order
            if($branch_orders->num_rows() > 0){
              if($orderDetails){
                  echo json_encode(array("status" => 1, "message" => "Reference number exists."));
              }else{
                  echo json_encode(array("status" => 2, "message" => "Invalid reference number."));
              }

            // if all order are main branch use jc fulfillment track order
            }else{
              if($orderDetails){
                $status = (c_jcfulfillment_shopidno() != '' && in_array(order_ref_prefix(),c_allowed_jcfulfillment_prefix()))
                ? 4 // if jc fulfillment is on use jc fulfillment track order
                : 1; // else us gogome track order
                echo json_encode(array("status" => $status, "message" => "Reference number exists."));
              }else{
                echo json_encode(array("status" => 2, "message" => "Invalid reference number."));
              }
            }

        }else{
            echo json_encode(array("status" => 3, "message" => "Invalid reference number."));
        }
    }

    public function products($productId,$idno = ""){
        $productId = sanitize($productId);
        if($productId != "") {
            $idno = sanitize($idno); //this is the referral code 'get whatever the word is typed'
            $province_code="";
            if($this->session->userdata("get_shipping_locs")!=""){
              $exp = explode(",", $this->session->userdata("get_shipping_locs"));
              $province_code=$exp[2];
            }
            

            $productInfoParent = $this->itemsModel->getProductDetails($productId);

            if($province_code!=""){
              $prov_variant_id = $this->model->get_prov_variant_id($productId,$province_code);
            }

            if($prov_variant_id==""){
              $productInfo = $productInfoParent;
            }else{
              $productInfo = $this->itemsModel->getProductDetails($prov_variant_id);

              $productInfo['itemname'] = $productInfoParent['itemname'];
            }
            
            //if client is logged in using JC, don't save referral link
            if ($this->session->userdata("user_type") != "JC"){
                if($idno != "") {
                    $response = json_decode($this->validate_referral_code($idno));
                    if($response->success) {
                        //Get distributor details for email sending
                        $dis_array = json_decode(en_dec_jc_api("dec", $response->data_res));

                        //Send email if details are not empty
                        if($dis_array != null) {

                            $dis_name = "";
                            if($dis_array->fname != null && $dis_array->fname != "")
                            $dis_name .= $dis_array->fname." ";
                            // if($dis_array->mname != null && $dis_array->mname != "")
                            // $dis_name .= $dis_array->mname." ";
                            if($dis_array->lname != null && $dis_array->lname != "")
                            $dis_name .= $dis_array->lname;

                            $this->session->set_userdata("referral_disname", strtoupper($dis_name));
                        }else{
                            $this->session->set_userdata("referral_disname", "");
                        }

                        $this->session->set_userdata("referral", $idno);

                        $referralLog = array(
                            'idno' => $idno,
                            'ip_address' => get_ip($_SERVER),
                            'shop_id' => $productInfo['sys_shop'],
                            'product_id' => $productId,
                            'trandate' => todaytime()
                        );

                        $referralLogId = $this->model->insertReferralLog($referralLog);
                    }
                }
            } else {
                $this->session->set_userdata("referral", "");
            }

            $variants_arr = $this->itemsModel->get_product_variants();
            $primary_pics = $this->itemsModel->get_all_productimg();
            $itemid_arr = array();

          //  if($productInfo['variant_isset'] == 1 && $productInfo['parent_product_id'] == null && $prov_variant_price == ""){
          //       $keys = array_keys(array_column($variants_arr,'parent_product_id'),$productInfo['Id']);
          //       $variants = array();
          //       $min = "";
          //       $min_float = 0;
          //       $max = "";
          //       $max_float = 0;
          //       $i = 0;
          //       foreach($keys as $row){
          //         $variants[] = $variants_arr[$row];
          //       }

          //       $parent = array(
          //           "Id" => $productInfo['Id'],
          //           "itemname" => $productInfo['itemname'],
          //           "itemid" => $productInfo['itemid'],
          //           "parent_product_id" => $productInfo['Id'],
          //           "price" => $productInfo['price'],
          //       );

          //       $variants[]=$parent;
          //       foreach($variants as $variant){

          //         if($i == 0){
          //           $min = floatval($variant['price']).','.$variant['itemid'];
          //           $min_float = floatval($variant['price']);

          //           $max = floatval($variant['price']).','.$variant['itemid'];
          //           $max_float = floatval($variant['price']);
          //         }else{
          //           if($min_float > floatval($variant['price'])){
          //             $min = floatval($variant['price']).','.$variant['itemid'];
          //             $min_float = floatval($variant['price']);
          //           }

          //           if($max_float < floatval($variant['price'])){
          //             $max = floatval($variant['price']).','.$variant['itemid'];
          //             $max_float = floatval($variant['price']);
          //           }
          //         }

          //         $i++;
          //       }

          //       $productInfo['min'] = $min;
          //       $productInfo['max'] = $max;
          // }

          // if($productInfo['variant_isset'] == 1 && $productInfo['parent_product_id'] == null && $prov_variant_price != ""){
          //       $productInfo['price'] = $prov_variant_price;
          //       $productInfo['min'] = null;
          //       $productInfo['max'] = null;
          // }

          // else{
          //       $productInfo['min'] = null;
          //       $productInfo['max'] = null;
          // }
            $productInfo['min'] = null;
            $productInfo['max'] = null;


            if($productInfo!= null && !empty($productInfo)) {
                $latitude='';
                $longitude='';
                $citymunCode=(explode(',',$this->session->userdata('get_shipping_locs')))[1];
                $shippingPerShop = $this->calculateShipping($productInfo['sys_shop'], [], $citymunCode, 0, $productInfo["price"],$latitude,$longitude);  
          
                //print_r($productInfo);
                //$products['results'][$key]['shippingfee'] = $shippingPerShop['shippingfee'];
                if ($productInfo['enabled'] == '1') {

                    //apply distributor rate if applicable
                    $disrate = $this->session->userdata("distributorRate");
                    if(!empty($disrate)) {
                        foreach ($disrate as $rate) {
                            if($rate->itemid == $productInfo["itemid"]){
                                $productInfo["price"] = round(floatval($productInfo["price"]) * (1.00 - floatval($rate->discrate)), 2);
                                //$productInfo["price"] = round(floatval($productInfo["price"]+floatval($shippingPerShop["shippingfee"])) * (1.00 - floatval($rate->discrate)), 2);
                        
                            }
                        }
                    }
                    
                    $data["productInfo"] = $productInfo;
                    $data["productInfo"]["cartable"] = $productInfo["tq_isset"] <= 0 || $productInfo["cont_selling_isset"] == 1 ||  $productInfo["no_of_stocks"] > 0;

                    // get branch details
                    // $cities = $provinces = $regions = $shipsFrom = [];
                    // $branches = $this->model->get_shopbranch($productInfo['sys_shop']);
                    // if (sizeof($branches) > 0) {
                    //     $cities = array_filter(array_column($branches, 'delivery_city'));
                    //     $provinces = array_filter(array_column($branches, 'delivery_province'));
                    //     $regions = array_filter(array_column($branches, 'delivery_region'));
                    // } else {
                    //     $shop = $this->ordersModel->get_shipping_zone($productInfo['sys_shop']);
                    //     $cities = array_filter(array_column($shop, 'shop_city'));
                    //     $provinces = array_filter(array_column($shop, 'shop_prov'));
                    //     $regions = array_filter(array_column($shop, 'shop_region'));
                    // }
                    //
                    // if (sizeof($cities) > 0) {
                    //     $cities = $this->get_ship_from_details('citymunDesc', 'citymunCode', 'sys_citymun', $cities);
                    //     $shipsFrom = array_merge($shipsFrom, $cities);
                    // }
                    // if (sizeof($provinces) > 0) {
                    //     $provinces = $this->get_ship_from_details('provDesc', 'provCode', 'sys_prov', $provinces);
                    //     $shipsFrom = array_merge($shipsFrom, $provinces);
                    // }
                    // if (sizeof($regions) > 0) {
                    //     $regions = $this->get_ship_from_details('regDesc', 'regCode', 'sys_region', $regions);
                    //     $shipsFrom = array_merge($shipsFrom, $regions);
                    // }

                    $data['categories'] = $this->model->getCategories()->result_array();
                    $data['get_banners'] = $this->model->get_banners();
                    // $data["shipsFrom"] = implode(", ", $shipsFrom);
                    $this->load->view("includes/header", $data);
                    $this->load->view("shop/single-product", $data);

                } else if ($productInfo['enabled'] == '0' || $productInfo['enabled'] == '2') {
                    // show 404 if product is disabled/deleted
                    $this->error_404->view();
                }

            }
            else {
                $this->error_404->view();
            }
        }
        else {
            $this->error_404->view();
        }
    }

    private function get_ship_from_details($col1, $col2, $table, $arr){
        $arr = implode(",", $arr);
        $arr = explode(",", $arr);
        $arr = array_unique($arr);
        $arr = implode(",", $arr);

        $locations = $this->model->get_ship_from_details($col1, $col2, $table, $arr);

        return array_column($locations, $col1);
    }

    public function privacy(){
        $data['categories'] = null;
        $this->load->view("includes/header",$data);
        $this->load->view(privacy_policy_view());
        $this->load->view("includes/footer");
    }

    public function terms(){
        $data['categories'] = null;
        $this->load->view("includes/header",$data);
        $this->load->view(terms_and_condition_view());
        $this->load->view("includes/footer");
    }

    public function contact(){
        $data['categories'] = null;
        $this->load->view("includes/header",$data);
        $this->load->view(contact_us_view());
        $this->load->view("includes/footer");
    }

    function search(){
      // $searchKey = $this->input->get("keyword");
        $searchKey = sanitize($this->input->get("keyword"));
        $categories = $this->model->getCategories()->result_array();
        $data = array(
            'searchKey' => $searchKey,
            'categories' => $categories
        );
        $this->load->view("includes/header", $data);
        $this->load->view("shop/search", $data);
    }

    public function shop_page($shopurl,$idno = ""){

        $shopurl = sanitize($shopurl); //shopurl
        if($shopurl != "") {
            $idno = sanitize($idno); //this is the referral code 'get whatever the word is typed'
            $shopInfo = $this->ordersModel->getShopDetailsByShopUrl($shopurl);

            //if client is logged in using JC, don't save referral link
            if ($this->session->userdata("user_type") != "JC"){
                if($idno != "") {
                    $response = json_decode($this->validate_referral_code($idno));
                    if($response->success){
                        //Get distributor details for email sending
                        $dis_array = json_decode(en_dec_jc_api("dec", $response->data_res));

                        //Send email if details are not empty
                        if($dis_array != null) {

                            $dis_name = "";
                            if($dis_array->fname != null && $dis_array->fname != "")
                            $dis_name .= $dis_array->fname." ";
                            // if($dis_array->mname != null && $dis_array->mname != "")
                            // $dis_name .= $dis_array->mname." ";
                            if($dis_array->lname != null && $dis_array->lname != "")
                            $dis_name .= $dis_array->lname;

                            $this->session->set_userdata("referral_disname", strtoupper($dis_name));
                        }else{
                            $this->session->set_userdata("referral_disname", "");
                        }

                        $this->session->set_userdata("referral", $idno);

                        $referralLog = array(
                            'idno' => $idno,
                            'ip_address' => get_ip($_SERVER),
                            'shop_id' => $shopInfo['id'],
                            'product_id' => 0,
                            'trandate' => todaytime()
                        );

                        $referralLogId = $this->model->insertReferralLog($referralLog);
                    }
                }
            } else {
                $this->session->set_userdata("referral", "");
            }
            if($shopInfo!= null && !empty($shopInfo)) {

                if ($shopInfo['status'] == '1') {
                    $data['categories'] = $this->model->getCategories()->result_array();

                    $data["shopInfo"] = $shopInfo;

                    $this->load->view("includes/header", $data);
                    $this->load->view("shop/shop-page", $data);

                } else if ($shopInfo['status'] == '0' || $shopInfo['status'] == '2') {
                    // show 404 if shop is disabled/deleted
                    $this->error_404->view();
                }
            }
            else {
                $this->error_404->view();
            }
        }
        else {
            $this->error_404->view();
        }
    }

    public function automate_updateProductImg(){
        $this->load->helper('directory');
        $result  = $this->model->automate_updateProductImg();
        $counter = 0;

        foreach($result as $row){
            $map = directory_map($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$row['shopcode'].'/products/'.$row['product_id'].'/'), 1);
            if(!empty($map)){
                $count = 1;
                for($x = 0; $x < count($map); $x++){
                    $arrangement = $count;
                    $success = $this->model->update_productImgUrl($row['product_id'], $map[$x], $arrangement);
                    $count++;
                }
                $counter += 1;
            }
        }

        print_r($counter." product/s image updated.");
    }

}
