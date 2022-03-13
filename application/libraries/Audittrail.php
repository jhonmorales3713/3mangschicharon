<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Audittrail Class
 *
 * Audit Trail library for toktokmall
 *
 * @category Libraries
 * @author Tristan Ross Lazaro
 * @link http://cloudpanda.com/
 * @version 1 
 */

class Audittrail {
    private $CI;
    
    function __construct()
    {
        $this->CI = get_instance();
        $this->CI->load->database('default',TRUE);
    }

    function logActivity($module, $details, $action_type, $username){
        $query  = " INSERT INTO sys_audittrail (module, details, action_type, username, ip_address, date_created) 
                    VALUES (?, ?, ?, ?, ?, ?) ";

        $params = array(
            $module,
            $details,
            $action_type,
            $username,
            $_SERVER['REMOTE_ADDR'],
            date('Y-m-d H:i:s')
        );
        $result = $this->CI->db->query($query, $params);

        return $result;
    }

    function checkProductChanges_sys_products($prevData, $newData){
        $string = "\n";

        if($prevData['cat_id'] != $newData['f_category']){
            $string .="Category - ".$this->productCategory($prevData['cat_id'])['category_name']." into ".$this->productCategory($newData['f_category'])['category_name']."\n";
        }

        if($prevData['itemname'] != $newData['f_itemname']){
            $string .="Product Name - ".$prevData['itemname']." into ".$newData['f_itemname']."\n";
        }

        if($prevData['itemid'] != $newData['f_itemid']){
            $string .="Item ID - ".$prevData['itemid']." into ".$newData['f_itemid']."\n";
        }

        if($prevData['otherinfo'] != $newData['f_otherinfo']){
            $string .="Other Info - ".$prevData['otherinfo']." into ".$newData['f_otherinfo']."\n";
        }

        if($prevData['uom'] != $newData['f_uom']){
            $string .="UOM ID - ".$prevData['uom']." into ".$newData['f_uom']."\n";
        }

        if($prevData['price'] != $newData['f_price']){
            $string .="Price - ".$prevData['price']." into ".$newData['f_price']."\n";
        }

        if($prevData['compare_at_price'] != $newData['f_compare_at_price']){
            $string .="Compared at Price - ".$prevData['compare_at_price']." into ".$newData['f_compare_at_price']."\n";
        }

        if($prevData['tags'] != $newData['f_tags']){
            $string .="Product Tags - ".$prevData['tags']." into ".$newData['f_tags']."\n";
        }

        if($prevData['inv_sku'] != $newData['f_inv_sku']){
            $string .="SKU (Stock Keeping Unit) - ".$prevData['inv_sku']." into ".$newData['f_inv_sku']."\n";
        }

        if($prevData['inv_barcode'] != $newData['f_inv_barcode']){
            $string .="Barcode - ".$prevData['inv_barcode']." into ".$newData['f_inv_barcode']."\n";
        }

        if($prevData['tq_isset'] != $newData['f_tq_isset']){
            if($prevData['tq_isset'] == 0){
                $tq_isset = 'Disabled';
            }else{
                $tq_isset = 'Enabled';
            }
            if($newData['f_tq_isset'] == 0){
                $f_tq_isset = 'Disabled';
            }else{
                $f_tq_isset = 'Enabled';
            }
            $string .="Track Quantity - ".$tq_isset." into ".$f_tq_isset."\n";
        }

        if($prevData['cont_selling_isset'] != $newData['f_cont_selling_isset']){
            if($prevData['cont_selling_isset'] == 0){
                $cont_selling_isset = 'Disabled';
            }else{
                $cont_selling_isset = 'Enabled';
            }
            if($newData['f_cont_selling_isset'] == 0){
                $f_cont_selling_isset = 'Disabled';
            }else{
                $f_cont_selling_isset = 'Enabled';
            }
            $string .="Continue Selling - ".$cont_selling_isset." into ".$f_cont_selling_isset."\n";
        }

        if($prevData['max_qty_isset'] != $newData['f_max_qty_isset']){
            if($prevData['max_qty_isset'] == 0){
                $max_qty_isset = 'Disabled';
            }else{
                $max_qty_isset = 'Enabled';
            }
            if($newData['f_max_qty_isset'] == 0){
                $f_max_qty_isset = 'Disabled';
            }else{
                $f_max_qty_isset = 'Enabled';
            }
            $string .="Max Quantity per Checkout - ".$max_qty_isset." into ".$f_max_qty_isset."\n";
        }

        if($prevData['max_qty'] != $newData['f_max_qty']){
            $string .="Max Quantity per Checkout - ".$prevData['max_qty']." into ".$newData['f_max_qty']."\n";
        }


        if($prevData['age_restriction_isset'] != $newData['f_age_restriction_isset']){
            if($prevData['age_restriction_isset'] == 0){
                $age_restriction_isset = 'Disabled';
            }else{
                $age_restriction_isset = 'Enabled';
            }
            if($newData['f_age_restriction_isset'] == 0){
                $f_age_restriction_isset = 'Disabled';
            }else{
                $f_age_restriction_isset = 'Enabled';
            }
            $string .="With age restriction - ".$age_restriction_isset." into ".$f_age_restriction_isset."\n";
        }

        if($prevData['max_qty'] != $newData['f_max_qty']){
            $string .="Max Quantity per Checkout - ".$prevData['max_qty']." into ".$newData['f_max_qty']."\n";
        }

        if($prevData['admin_isset'] != $newData['f_admin_isset']){
            if($prevData['admin_isset'] == 0){
                $admin_isset = 'Disabled';
            }else{
                $admin_isset = 'Enabled';
            }
            if($newData['f_admin_isset'] == 0){
                $f_admin_isset = 'Disabled';
            }else{
                $f_admin_isset = 'Enabled';
            }
            $string .="Set admin settings - ".$admin_isset." into ".$f_admin_isset."\n";
        }

        if($prevData['disc_ratetype'] != $newData['f_disc_ratetype']){
            if($prevData['disc_ratetype'] == 'p'){
                $disc_ratetype = 'Percentage';
            }else{
                $disc_ratetype = 'Fixed Amount';
            }
            if($newData['f_disc_ratetype'] == 'p'){
                $f_disc_ratetype = 'Percentage';
            }else{
                $f_disc_ratetype = 'Fixed Amount';
            }
            $string .="Discount Rate Type - ".$disc_ratetype." into ".$f_disc_ratetype."\n";
        }

        if($prevData['disc_rate'] != $newData['f_disc_rate']){
            $string .="Discount Rate - ".$prevData['disc_rate']." into ".$newData['f_disc_rate']."\n";
        }

        if($prevData['summary'] != $newData['f_summary']){
            $string .="Product Summary - ".$prevData['summary']." into ".$newData['f_summary']."\n";
        }

        if(ini() == 'jcww'){
            $prevDelivery = explode(", ",$prevData['delivery_areas']);
            if($prevDelivery != $newData['f_delivery_areas']){
                $prevdeliver_areas_str = "";
                foreach($prevDelivery AS $row) {
                $prevdeliver_areas_str .= $this->checkProvince($row)['provDesc'].", ";
                }
                $prevdeliver_areas_str = rtrim($prevdeliver_areas_str, ', ');

                $newdeliver_areas_str = "";
                foreach($newData['f_delivery_areas'] AS $row) {
                $newdeliver_areas_str .= $this->checkProvince($row)['provDesc'].", ";
                }
                $newdeliver_areas_str = rtrim($newdeliver_areas_str, ', ');


                $string .="Delivery Areas - ".$prevdeliver_areas_str." into ".$newdeliver_areas_str."\n";
            }
        }

        if($prevData['arrangement'] != $newData['f_arrangement']){
            $string .="Product Arrangement - ".$prevData['arrangement']." into ".$newData['f_arrangement']."\n";
        }

        if($prevData['variant_isset'] != $newData['f_variants_isset']){
            if($prevData['variant_isset'] == 0){
                $variant_isset = 'Disabled';
            }else{
                $variant_isset = 'Enabled';
            }
            if($newData['f_variants_isset'] == 0){
                $f_variant_isset = 'Disabled';
            }else{
                $f_variant_isset = 'Enabled';
            }
            $string .="Variant - ".$variant_isset." into ".$f_variant_isset."\n";
        }

        return $string;
    }

    function checkProductChanges_refcommrate($prevData, $newData){
        $string = "\n";

        if($prevData['refstartup'] != $newData['f_startup']){
            $string .="Startup - ".$prevData['refstartup']." into ".$newData['f_startup']."\n";
        }

        if($prevData['refjc'] != $newData['f_jc']){
            $string .="JC - ".$prevData['refjc']." into ".$newData['f_jc']."\n";
        }

        if($prevData['refmcjr'] != $newData['f_mcjr']){
            $string .="MCJR - ".$prevData['refmcjr']." into ".$newData['f_mcjr']."\n";
        }

        if(strval($prevData['refmc']) != strval($newData['f_mc'])){
            $string .="MC - ".$prevData['refmc']." into ".$newData['f_mc']."\n";
        }

        if($prevData['refmcsuper'] != $newData['f_mcsuper']){
            $string .="MCSUPER - ".$prevData['refmcsuper']." into ".$newData['f_mcsuper']."\n";
        }

        if($prevData['refmcmega'] != $newData['f_mcmega']){
            $string .="MCMEGA - ".$prevData['refmcmega']." into ".$newData['f_mcmega']."\n";
        }

        if($prevData['refothers'] != $newData['f_others']){
            $string .="Others - ".$prevData['refothers']." into ".$newData['f_others']."\n";
        }

        return $string;
    }

    function checkProductChanges_sys_products_shipping($prevData, $newData){

        $string = "";
        if($prevData['weight'] != $newData['f_weight']){
            $string .="Weight - ".$prevData['weight']." into ".$newData['f_weight']."\n";
        }

        if($prevData['length'] != $newData['f_length']){
            $string .="Length - ".$prevData['length']." into ".$newData['f_length']."\n";
        }

        if($prevData['width'] != $newData['f_width']){
            $string .="Width - ".$prevData['width']." into ".$newData['f_width']."\n";
        }

        if($prevData['height'] != $newData['f_height']){
            $string .="Height - ".$prevData['height']." into ".$newData['f_height']."\n";
        }

        if($prevData['shipping_isset'] != $newData['f_shipping_isset']){
            if($prevData['shipping_isset'] == 1){
                $shipping_isset = 'Enabled';
            }else{
                $shipping_isset = 'Disabled';
            }
            if($newData['f_shipping_isset'] == 1){
                $f_shipping_isset = 'Enabled';
            }else{
                $f_shipping_isset = 'Disabled';
            }
            $string .="Shipping - ".$shipping_isset." into ".$f_shipping_isset."\n";
        }

        return $string;
    }

    function checkProductChanges_sys_products_invtrans_branch($product_id, $branchid, $no_of_stocks){
        $string = "";
        $query = " SELECT a.*, b.branchname FROM sys_products_invtrans_branch AS a
                LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
                WHERE a.product_id = ? AND a.branchid = ?";

        $params = array(
            $product_id,
            $branchid
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        if(floatval($result['no_of_stocks']) != floatval($no_of_stocks)){
            $branch = ($branchid == 0) ? 'Main':$this->checkBranch($branchid)['branchname']; 
            $result['no_of_stocks'] = ($result['no_of_stocks'] == '') ? 0 : $result['no_of_stocks'];
            $string .= $branch." no of stocks - ".$result['no_of_stocks']." into ".$no_of_stocks."\n";
        }

        return $string;
    }

    function productCategory($category_id){
        $query  = "SELECT * FROM sys_product_category WHERE id = ?";

        $params = array(
            $category_id
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        return $result;
    }

    function ordersFilterString($data){
        $filter_string = "Date: ".$data['date_from_export'].' to '.$data['date_to_export'];

        if($data['_name_export'] != ''){
            $filter_string .= ", DR No./Order# ".$data['_name_export'];
        }

        if($data['status_export'] == ''){
            $filter_string .= ", Status: All Status";
        }
        else if($data['status_export'] == '0'){
            $filter_string .= ", Status: Waiting for Payment";
        }
        else if($data['status_export'] == '1'){
            $filter_string .= ", Status: Paid";
        }
        else if($data['status_export'] == 'p'){
            $filter_string .= ", Status: Ready for Processing";
        }
        else if($data['status_export'] == 'po'){
            $filter_string .= ", Status: Processing Order";
        }
        else if($data['status_export'] == 'rp'){
            $filter_string .= ", Status: Ready for Pickup";
        }
        else if($data['status_export'] == 'bc'){
            $filter_string .= ", Status: Booking Confirmed";
        }
        else if($data['status_export'] == 'f'){
            $filter_string .= ", Status: Fulfilled";
        }
        else if($data['status_export'] == 'rs'){
            $filter_string .= ", Status: Return to Sender";
        }
        else if($data['status_export'] == 's'){
            $filter_string .= ", Status: Shipped";
        }

        if($data['location_export'] == 'address'){
            $filter_string .= ", Location: ".$data['address_export'];
        }
        else if($data['location_export'] == 'region'){
            $filter_string .= ", Location: ".$this->checkRegion($data['regCode_export'])['regDesc'];
        }
        else if($data['location_export'] == 'province'){
            $filter_string .= ", Location: ".$this->checkProvince($data['provCode_export'])['provDesc'];
        }
        else if($data['location_export'] == 'citymun'){
            $filter_string .= ", Location: ".$this->checkCityMun($data['citymunCode_export'])['citymunDesc'];
        }

        if($data['_shops_export'] == ''){
            $filter_string .= ", Shop: All Shops";
        }else{
            $filter_string .= ", Shop: ".$this->checkShop($data['_shops_export'])['shopname'];
        }


        return $filter_string;
    }

    function checkRegion($regCode){
        $query  = "SELECT * FROM sys_region WHERE regCode = ?";

        $params = array(
            $regCode
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        return $result;
    }

    function checkProvince($provCode){
        $query  = "SELECT * FROM sys_prov WHERE provCode = ?";

        $params = array(
            $provCode
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        return $result;
    }

    function checkBranch($branchid){
        $query  = "SELECT * FROM sys_branch_profile WHERE id = ?";

        $params = array(
            $branchid
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        return $result;
    }

    function checkCityMun($citymunCode){
        $query  = "SELECT * FROM sys_citymun WHERE citymunCode = ?";

        $params = array(
            $citymunCode
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        return $result;
    }

    function checkShop($shop_id){
        $query  = "SELECT * FROM sys_shops WHERE id = ?";

        $params = array(
            $shop_id
        );
        $result = $this->CI->db->query($query, $params)->row_array();

        return $result;
    }

    function shippingdeliveryZoneString($zone_name, $regCode, $provCode, $citymunCode){
        $string = "";
        $substring = "";
        
        $regDes = ($regCode == 0 || $regCode == '' || $regCode == '0') ? '':$this->checkRegion($regCode)['regDesc'];
        $provDes = ($provCode == 0 || $provCode == '' || $provCode == '0') ? '':$this->checkProvince($provCode)['provDesc'];
        $citymunDes = ($citymunCode == 0 || $citymunCode == '' || $citymunCode == '0') ? '':$this->checkCityMun($citymunCode)['citymunDesc'];
        
        if($regDes != ''){
            $substring .= " - ".$regDes;
        }

        if($provDes != ''){
            $substring .= " - ".$provDes;
        }

        if($citymunDes != ''){
            $substring .= " - ".$citymunDes;
        }

        $string .= $zone_name.$substring."\n";

        return $string;

    }

    function shippingdeliveryRateString($zone_name, $rate_name, $rate_amount, $is_condition, $minimum_value, $maximum_value, $from_day, $to_day, $additional_isset, $set_value, $set_amount){
        $string = "";
        $additional_string = "";

        if($is_condition == 1){
            if($maximum_value == '' || $maximum_value == 0.00 || $maximum_value == 0){
                $condition_value = number_format($minimum_value, 2)." minimum grams";
            }else{
                $condition_value = number_format($minimum_value, 2)." to ".number_format($maximum_value, 2). "grams";
            }
        }
        else if($is_condition == 2){
            if($maximum_value == '' || $maximum_value == 0.00 || $maximum_value == 0){
                $condition_value = number_format($minimum_value, 2)." minimum price";
            }else{
                $condition_value = number_format($minimum_value, 2)." to ".number_format($maximum_value, 2). "price";
            }
        }
        else{
            $condition_value = "N/A";
        }
        
        if($additional_isset == 1){
            if($is_condition == 1){
                $additional_string = "For every succeeding ".number_format($set_value, 2)." grams, add additional".number_format($set_amount, 2). "PHP";
            }
            else if($is_condition == 2){
                $additional_string = "For every succeeding ".number_format($set_value, 2)." price, add additional".number_format($set_amount, 2). "PHP";
            }
        }else{
            $additional_string = "N/A";
        }

        $string .= "Rate: ".$rate_name." - Condition: ".$condition_value.", Price: ".$rate_amount.", Days to Ship: ".$from_day." to ".$to_day." days, Additional Condition: ".$additional_string."\n";
        return $string;
    }

    function shippingdeliveryProductsString($data){
        $string = "Product: \n";

        foreach($data as $row){
            $string .= $row['product_name']."\n";
        }
        
        return $string;
    }

    function voidrecordListString($data){
        $string = "Date: ".$data->date_from. " to ". $data->date_to;

        if($data->_name != ''){
            $string .= ", Search Field: ".$data->_name;
        }

        if($data->status != ''){
            $string .= ", Status: ".$data->status;
        }
    
        return $string;
    }

    function createUpdateFormat($array, $pre_arr) {
        $result = [];
        $merged = 0;
        foreach ($array as $key => $value) {
            $prev = (isset($pre_arr[$key])) ? $pre_arr[$key]:'';
            $result[] = "$key - $prev into $value";
            $merged = 1;
        }

        if($merged == 1){
            return implode("\n", $result);
        }
        else{
            return 'None';
        }
    }

    function shopsString($prevData, $newData){
        $string = "";

        if($prevData['shopcode'] != $newData['entry-shopcode']){
            $string .= "Shop Code: ".$prevData['shopcode']. " into ".$newData['entry-shopcode']."\n" ;
        }

        if($prevData['shopurl'] != $newData['entry-shopurl']){
            $string .= "Shop URL: ".$prevData['shopurl']. " into ".$newData['entry-shopurl']."\n" ;
        }

        if($prevData['shopname'] != $newData['entry-shopname']){
            $string .= "Shop Name: ".$prevData['shopname']. " into ".$newData['entry-shopname']."\n" ;
        }

       

        if($prevData['inv_threshold'] != $newData['entry-treshold']){
            $string .= "Threshold Inventory: ".$prevData['inv_threshold']. " into ".$newData['entry-treshold']."\n" ;
        }
       /*
        if($prevData['app_currency_id'] != $newData['entry-currency']){
            $string .= "Currency ID: ".$prevData['app_currency_id']. " into ".$newData['entry-currency']."\n" ;
        }  */

        switch (ini()) {
            case "toktokmall":

             
                if(floatval($prevData['rateamount']) != floatval($newData['entry-merchant-comrate'])){
                    $string .= "Merchant Comission  Rate: ".$prevData['rateamount']. " into ".$newData['entry-merchant-comrate']."\n" ;
                }

                if(floatval($prevData['startup']) != floatval($newData['entry-f_startup'])){
                    $string .= "Shop Startup: ".$prevData['startup']. " into ".$newData['entry-f_startup']."\n" ;
                }

                if(floatval($prevData['jc']) != floatval($newData['entry-f_jc'])){
                    $string .= "Shop Jc: ".$prevData['jc']. " into ".$newData['entry-f_jc']."\n" ;
                }

                if(floatval($prevData['mcjr']) != floatval($newData['entry-f_mcjr'])){
                    $string .= "Shop Mcjr: ".$prevData['mcjr']. " into ".$newData['entry-f_mcjr']."\n" ;
                }
        
                if(floatval($prevData['mc']) != floatval($newData['entry-f_mc'])){
                    $string .= "Shop Mc: ".$prevData['mc']. " into ".$newData['entry-f_mc']."\n" ;
                }
        
                if(floatval($prevData['mcsuper']) != floatval($newData['entry-f_mcsuper'])){
                    $string .= "Shop Mcsuper: ".$prevData['mcsuper']. " into ".$newData['entry-f_mcsuper']."\n" ;
                }

                if(floatval($prevData['mcmega']) != floatval($newData['entry-f_mcmega'])){
                    $string .= "Shop Mcmega: ".$prevData['mcmega']. " into ".$newData['entry-f_mcmega']."\n" ;
                }

                if(floatval($prevData['others']) != floatval($newData['entry-f_others'])){
                    $string .= "Shop Others: ".$prevData['others']. " into ".$newData['entry-f_others']."\n" ;
                }

            break;
            default;
                if($prevData['ratetype'] != $newData['entry-ratetype']){
                    $prevRateType = ($prevData['ratetype'] == 'f') ? 'Fix Amount':'Percentage';
                    $newRateType = ($newData['entry-ratetype'] == 'f') ? 'Fix Amount':'Percentage';
                    $string .= "Rate Type: ".$prevRateType. " into ".$newRateType."\n" ;
                }
        
                if(floatval($prevData['rateamount']) != floatval($newData['entry-rate'])){
                    $string .= "Rate Amount: ".$prevData['rateamount']. " into ".$newData['entry-rate']."\n" ;
                }
        
                if(floatval($prevData['commission_rate']) != floatval($newData['entry-commrate'])){
                    $string .= "Commission Rate: ".$prevData['commission_rate']. " into ".$newData['entry-commrate']."\n" ;
                }
            break;

        }

        

        if($prevData['billing_type'] != $newData['entry-withshipping']){
            $prevBillingType = ($prevData['billing_type'] == 1) ? 'Enabled':'Disabled';
            $newBillingType = ($newData['entry-withshipping'] == 1) ? 'Enabled':'Disabled';
            $string .= "Billing Type: ".$prevBillingType. " into ".$newBillingType."\n" ;
        }

        if($prevData['generatebilling'] != $newData['entry-generatebilling']){
            $prevBilling = ($prevData['generatebilling'] == 1) ? 'Enabled':'Disabled';
            $newBilling = ($newData['entry-generatebilling'] == 1) ? 'Enabled':'Disabled';
            $string .= "Billing per Branch: ".$prevBilling. " into ".$newBilling."\n" ;
        }

        if($prevData['prepayment'] != $newData['entry-prepayment']){
            $prevPrePayment = ($prevData['prepayment'] == 1) ? 'Enabled':'Disabled';
            $newPrePayment  = ($newData['entry-prepayment'] == 1) ? 'Enabled':'Disabled';
            $string .= "Pre-Payment: ".$prevPrePayment. " into ".$newPrePayment."\n" ;
        }

        if($prevData['toktok_shipping'] != $newData['entry-toktokdel']){
            $prevtoktokship = ($prevData['toktok_shipping'] == 1) ? 'Enabled':'Disabled';
            $newtoktokship  = ($newData['entry-toktokdel'] == 1) ? 'Enabled':'Disabled';
            $string .= "toktok Shipping: ".$prevtoktokship. " into ".$newtoktokship."\n" ;
        }

        if(floatval($prevData['threshold_amt']) != floatval($newData['entry-thresholdamt'])){
            $string .= "Threshold Amount: ".$prevData['threshold_amt']. " into ".$newData['entry-thresholdamt']."\n" ;
        }

        if($prevData['mobile'] != $newData['entry-mobile']){
            $string .= "Contact Number: ".$prevData['mobile']. " into ".$newData['entry-mobile']."\n" ;
        }

        if($prevData['email'] != $newData['entry-email']){
            $string .= "Email: ".$prevData['email']. " into ".$newData['entry-email']."\n" ;
        }

        if($prevData['address'] != $newData['entry-address']){
            $string .= "Address: ".$prevData['address']. " into ".$newData['entry-address']."\n" ;
        }

        if($prevData['latitude'] != $newData['loc_latitude']){
            $string .= "Latitude: ".$prevData['latitude']. " into ".$newData['loc_latitude']."\n" ;
        }

        if($prevData['longitude'] != $newData['loc_longitude']){
            $string .= "Longitude: ".$prevData['longitude']. " into ".$newData['loc_longitude']."\n" ;
        }

        if($prevData['shop_region'] != $newData['entry-shop_region']){
            $string .= "Region: ".$this->checkRegion($prevData['shop_region'])['regDesc']. " into ".$this->checkRegion($newData['entry-shop_region'])['regDesc']."\n" ;
        }

        if($prevData['shop_city'] != $newData['entry-shop_city']){
            $prevCity = (!empty($this->checkCityMun($prevData['shop_city'])['citymunDesc']))? $this->checkCityMun($prevData['shop_city'])['citymunDesc'] : "Empty";
            $newCity = (!empty($this->checkCityMun($newData['entry-shop_city'])['citymunDesc']))? $this->checkCityMun($newData['entry-shop_city'])['citymunDesc'] : "Empty";
            $string .= "City: ".$prevCity. " into ".$newCity."\n" ;
        }

        if($prevData['bankname'] != $newData['entry-bankname']){
            $string .= "Bank Name: ".$prevData['bankname']. " into ".$newData['entry-bankname']."\n" ;
        }

        if($prevData['accountname'] != $newData['entry-acctname']){
            $string .= "Account Name: ".$prevData['accountname']. " into ".$newData['entry-acctname']."\n" ;
        }

        if($prevData['accountno'] != $newData['entry-acctno']){
            $string .= "Account No: ".$prevData['accountno']. " into ".$newData['entry-acctno']."\n" ;
        }

        if($prevData['description'] != $newData['entry-desc']){
            $string .= "Description: ".$prevData['description']. " into ".$newData['entry-desc']."\n" ;
        }

        return $string;
    }

    function branchString($prevData, $newData){
        $string = "";

        if($prevData['sys_shop'] != $newData['entry-mainshop']){
            $string .= "Main Shop: ".$this->checkShop($prevData['sys_shop'])['shopname']. " into ".$this->checkShop($newData['entry-mainshop'])['shopname']."\n" ;
        }

        if($prevData['branchname'] != $newData['entry-branch']){
            $string .= "Branch Name: ".$prevData['branchname']. " into ".$newData['entry-branch']."\n" ;
        }

        if($prevData['contactperson'] != $newData['entry-contactperson']){
            $string .= "Contact Person: ".$prevData['contactperson']. " into ".$newData['entry-contactperson']."\n" ;
        }

        if($prevData['mobileno'] != $newData['entry-conno']){
            $string .= "Contact No: ".$prevData['mobileno']. " into ".$newData['entry-conno']."\n" ;
        }

        if($prevData['email'] != $newData['entry-email']){
            $string .= "Email: ".$prevData['email']. " into ".$newData['entry-email']."\n" ;
        }

        if($prevData['address'] != $newData['entry-address']){
            $string .= "Branch Address: ".$prevData['address']. " into ".$newData['entry-address']."\n" ;
        }

        if($prevData['latitude'] != $newData['loc_latitude']){
            $string .= "Latitude: ".$prevData['latitude']. " into ".$newData['loc_latitude']."\n" ;
        }

        if($prevData['longitude'] != $newData['loc_longitude']){
            $string .= "Longitude: ".$prevData['longitude']. " into ".$newData['loc_longitude']."\n" ;
        }

        if($prevData['branch_region'] != $newData['entry-branch_region']){
            $string .= "Branch Region: ".$prevData['branch_region']. " into ".$newData['entry-branch_region']."\n" ;
        }

        if($prevData['branch_city'] != $newData['entry-branch_city']){
            $string .= "Branch City: ".$prevData['branch_city']. " into ".$newData['entry-branch_city']."\n" ;
        }

        if($prevData['branch_city'] != $newData['entry-branch_city']){
            $string .= "Branch City: ".$prevData['branch_city']. " into ".$newData['entry-branch_city']."\n" ;
        }

        if($prevData['isautoassign'] != $newData['entry-isautoassign']){
            $prevAutoAssign = ($prevData['isautoassign'] == 1) ? 'Enabled':'Disabled';
            $newAutoAssign = ($newData['entry-isautoassign'] == 1) ? 'Enabled':'Disabled';
            $string .= "Auto Assign: ".$prevAutoAssign. " into ".$newAutoAssign."\n" ;
        }

        if($prevData['bankname'] != $newData['entry-bankname']){
            $string .= "Bank Name: ".$prevData['bankname']. " into ".$newData['entry-bankname']."\n" ;
        }

        if($prevData['accountname'] != $newData['entry-acctname']){
            $string .= "Account Name: ".$prevData['accountname']. " into ".$newData['entry-acctname']."\n" ;
        }

        if($prevData['accountno'] != $newData['entry-acctno']){
            $string .= "Account No: ".$prevData['accountno']. " into ".$newData['entry-acctno']."\n" ;
        }

        if($prevData['description'] != $newData['entry-desc']){
            $string .= "Description: ".$prevData['description']. " into ".$newData['entry-desc']."\n" ;
        }

        if($prevData['idnopb'] != $newData['entry-idnopb']){
            $string .= "IDNO: ".$prevData['idnopb']. " into ".$newData['entry-idnopb']."\n" ;

        }
        if($prevData['inv_threshold'] != $newData['entry-treshold']){
                $string .= "Treshold Inventory: ".$prevData['inv_threshold']. " into ".$newData['entry-treshold']."\n" ;
        }

        $prevDelivery_cities      = (!empty(explode(",",$prevData['city']))) ? explode(",",$prevData['city']):null;
        $newDelivery_cities       = (!empty($newData['entry-city'])) ? $newData['entry-city']:null;
        
        if($prevDelivery_cities[0] == '' && $newDelivery_cities == null){
        }else{
            if($prevDelivery_cities != $newDelivery_cities){
                if($prevDelivery_cities[0] != ''){
                    $string .= "Delivery Areas City: \n";
                    foreach($prevDelivery_cities as $val){
                        $string .= $this->checkCityMun($val)['citymunDesc']."\n";
                    }
                }else{
                    $string .= "Delivery Areas City: \nEmpty City \n";
                }

                if($newDelivery_cities != null){
                    $string .= "into \n";
                    foreach($newDelivery_cities as $val){
                        $string .= $this->checkCityMun($val)['citymunDesc']."\n";
                    }
                }else{
                    $string .= "into \nEmpty City \n";
                }
    
                
            }
        }
        
        $prevDelivery_provinces   = (!empty(explode(",",$prevData['province']))) ? explode(",",$prevData['province']):null;
        $newDelivery_provinces    = (!empty($newData['entry-province'])) ? $newData['entry-province']:null;

        if($prevDelivery_provinces[0] == '' && $newDelivery_provinces == null){
        }else{
            if($prevDelivery_provinces != $newDelivery_provinces){
                if($prevDelivery_provinces[0] != ''){
                    $string .= "Delivery Areas Province: \n";
                    foreach($prevDelivery_provinces as $val){
                        $string .= $this->checkProvince($val)['provDesc']."\n";
                    }
                }else{
                    $string .= "Delivery Areas Province: \nEmpty Province \n";
                }

                if($newDelivery_provinces != null){
                    $string .= "into \n";
                    foreach($newDelivery_provinces as $val){
                        $string .= $this->checkProvince($val)['provDesc']."\n";
                    }
                }else{
                    $string .= "into \nEmpty Province \n";
                }

               
            }
        }
        
        $prevDelivery_regions   = (!empty(explode(",",$prevData['region']))) ? explode(",",$prevData['region']):null;
        $newDelivery_regions    = (!empty($newData['entry-region'])) ? $newData['entry-region']:null;

        if($prevDelivery_regions[0] == '' && $newDelivery_regions == null){
        }else{
            if($prevDelivery_regions != $newDelivery_regions){
                if($prevDelivery_regions[0] != ''){
                    $string .= "Delivery Areas Region: \n";
                    foreach($prevDelivery_regions as $val){
                        $string .= $this->checkRegion($val)['regDesc']."\n";
                    }
                }else{
                    $string .= "Delivery Areas Region: \nEmpty Region \n";
                }

                if($newDelivery_regions != null){
                    $string .= "into \n";
                    foreach($newDelivery_regions as $val){
                        $string .= $this->checkRegion($val)['regDesc']."\n";
                    }
                }else{
                    $string .= "into \nEmpty Region \n";
                }
            }
        }
        
        return $string;
    }

    function readyforPickupToktokString($rp_shipping_partner, $sys_shop, $reference_num, $senderName, $senderMobile, $senderDetails, $salesOrder, $signature){
        $string = "";

        $string .= "Shipping Partner: ".$rp_shipping_partner."\n";
        $string .= "Shop ID: ".$sys_shop."\n";
        $string .= "Reference Number: ".$reference_num."\n";
        $string .= "Sender Name: ".$senderName."\n";
        $string .= "Sender Mobile: ".$senderMobile."\n";
        $string .= "Sender Details: \n";

        foreach($senderDetails as $row){
            $string .= $row."\n";
        }

        $string .= "Recepient Details: \n";

        foreach($salesOrder as $row){
            $string .= $row."\n";
        }

        $string .= "Signature: ".$signature;

        return $string;
    }
  
    function get_ReportExportRemarks($shopid, $branchid, $fromdate, $todate, $report, $additional_filters = []){
        $this->CI->load->model('shops/Model_shops');
        $this->CI->load->model('shop_branch/Model_shopbranch', 'model_branch');
        $filter=" with filters:";
        if ($shopid > 0) {
            $shop_name = $this->CI->Model_shops->get_shop_details($shopid)->result_array()[0]['shopname'];
            if ($branchid === "main") {
                $shop_name .= " -> Main";
            } elseif ($branchid > 0) {
                $branchname = $this->CI->model_branch->get_branchnameById($branchid)->result_array()[0]['branchname'];
                $shop_name .= " -> $branchname";
            } else {
                if ($branchid === "") {
                    $shop_name .= "";
                }else{
                    $shop_name .= " -> All Branches";
                }
            }
        } else {
            $shop_name = "All Shops";
        }
        $filters = "";
        $filter.= ($shopid !== "") ? " Shop = $shop_name":"";
        foreach ($additional_filters as $key => $value) {
            if ($value !== '') {
                if ($filter === " with filters:") {
                    // for audit trail
                    $filters = ($filters === "") ? " $key = $value":$filters." $key = $value";
                    // for export header
                    $filter.=" $key = $value";
                } else {
                    // for audit trail
                    $filter.=", $key = $value";
                    // for export header
                    $filters = ($filters === "") ? " $key = $value":$filters.", $key = $value";
                }
            }
        }
        if ($fromdate == $todate) {
            $filter.=", Dated $fromdate";
        }else{
            $filter.=", Dated $fromdate to $todate";
        }
        return [
            'remarks' => "$report has been exported into excel $filter",
            'shop_name' => $shop_name,
            '_filters' => $filters,
        ];
    }

    function MerchantAppString($prevData, $newData){
        $string = "";

        if($prevData['up_first_name'] != $newData['cn_first_name']){
            $string .= "First Name: ".$prevData['up_first_name']. " from ".$newData['cn_first_name']."\n" ;
        }

        if($prevData['up_last_name'] != $newData['cn_last_name']){
            $string .= "Last Name: ".$prevData['up_last_name']. " from ".$newData['cn_last_name']."\n" ;
        }

        if($prevData['up_email'] != $newData['ci_email']){
            $string .= "Email: ".$prevData['up_email']. " from ".$newData['ci_email']."\n" ;
        }

        if($prevData['up_conno'] != $newData['ci_conno']){
            $string .= "Contact Number: ".$prevData['up_conno']. " from ".$newData['ci_conno']."\n" ;
        }

        if($prevData['up_facebook'] != $newData['sml_facebook']){
            $string .= "Facebook: ".$prevData['up_facebook']. " from ".$newData['sml_facebook']."\n" ;
        }

        if($prevData['up_instagram'] != $newData['sml_instagram']){
            $string .= "Instagram: ".$prevData['up_instagram']. " from ".$newData['sml_instagram']."\n" ;
        }

        if($prevData['up_registered_company_name'] != $newData['ci_registered_company_name']){
            $string .= "Company Name: ".$prevData['up_registered_company_name']. " from ".$newData['ci_registered_company_name']."\n" ;
        }

        if($prevData['up_company_description'] != $newData['ci_company_description']){
            $string .= "Company Description: ".$prevData['up_company_description']. " from ".$newData['ci_company_description']."\n" ;
        }

        if($prevData['up_shop_name'] != $newData['shop_name']){
            $string .= "Shop Name: ".$prevData['up_shop_name']. " from ".$newData['shop_name']."\n" ;
        }

        if($prevData['up_shop_description'] != $newData['shop_description']){
            $string .= "Product Description: ".$prevData['up_shop_description']. " from ".$newData['shop_description']."\n" ;
        }

        if($prevData['up_unit_no'] != $newData['a_unit_no']){
            $string .= "Unit #: ".$prevData['up_unit_no']. " from ".$newData['a_unit_no']."\n" ;
        }

        if($prevData['up_street'] != $newData['a_street']){
            $string .= "Street: ".$prevData['up_street']. " from ".$newData['a_street']."\n" ;
        }

        if($prevData['up_brgy'] != $newData['a_brgy']){
            $string .= "Brgy: ".$prevData['up_brgy']. " from ".$newData['a_brgy']."\n" ;
        }

        if($prevData['up_regCode'] != $newData['a_regCode']){
            $string .= "regCode: ".$prevData['up_regCode']. " from ".$newData['a_regCode']."\n" ;
        }

        if($prevData['up_provCode'] != $newData['a_provCode']){
            $string .= "provCode: ".$prevData['up_provCode']. " from ".$newData['a_provCode']."\n" ;
        }

        if($prevData['up_citymunCode'] != $newData['a_citymunCode']){
            $string .= "Citymuncode: ".$prevData['up_citymunCode']. " from ".$newData['a_citymunCode']."\n" ;
        }

        if($prevData['up_zipcode'] != $newData['a_zipcode']){
            $string .= "Zipcode: ".$prevData['up_zipcode']. " from ".$newData['a_zipcode']."\n" ;
        }

        if($prevData['up_referral_code'] != $newData['referral_code']){
            $string .= "Referral Code: ".$prevData['up_referral_code']. " from ".$newData['referral_code']."\n" ;
        }

        if($prevData['loc_latitude'] != $newData['pa_latitude']){
            $string .= "Latitude: ".$prevData['loc_latitude']. " from ".$newData['pa_latitude']."\n" ;
        }

        if($prevData['loc_longitude'] != $newData['pa_longitude']){
            $string .= "Longitude: ".$prevData['loc_longitude']. " from ".$newData['pa_longitude']."\n" ;
        }

        if($prevData['up_bank'] != $newData['bi_bank']){
            $string .= "Bank name: ".$prevData['up_bank']. " from ".$newData['bi_bank']."\n" ;
        }

        if($prevData['up_bank_account_name'] != $newData['bi_bank_account_name']){
            $string .= "Bank Account Name: ".$prevData['up_bank']. " from ".$newData['bi_bank_account_name']."\n" ;
        }

        if($prevData['up_bank_account_number'] != $newData['bi_bank_account_number']){
            $string .= "Bank Account Number: ".$prevData['up_bank']. " from ".$newData['bi_bank_account_number']."\n" ;
        }

        
        return $string;
    }


    function UserListString($prevData, $newData){

        $string = "";
        /// Shop
         if($prevData['products']['view']!= $newData['products']['view']){
             if($newData['products']['view'] == 1)
             {
                 $string .= "Products Module - Products View disabled to enabled."."\n" ;
             }else if($newData['products']['view'] == 0){
                 $string .= "Products Module - Products View enabled to disabled."."\n" ;
             }
         }
 
         if($prevData['products']['create']!= $newData['products']['create']){
             if($newData['products']['create'] == 1)
             {
                 $string .= "Products Module - Products Create disabled to enabled."."\n" ;
             }else if($newData['products']['create'] == 0){
                 $string .= "Products Module - Products Create enabled to disabled."."\n" ;
             }
         }
 
         if($prevData['products']['update']!= $newData['products']['update']){
             if($newData['products']['update'] == 1)
             {
                 $string .= "Products Module - Products Update disabled to enabled."."\n" ;
             }else if($newData['products']['update'] == 0){
                 $string .= "Products Module - Products Update enabled to disabled."."\n" ;
             }
         }
 
         if($prevData['products']['disable']!= $newData['products']['disable']){
             if($newData['products']['disable'] == 1)
             {
                 $string .= "Products Module - Products Disable disabled to enabled."."\n" ;
             }else if($newData['products']['disable'] == 0){
                 $string .= "Products Module - Products Disable enabled to disabled."."\n" ;
             }
         }
 
         if($prevData['products']['delete']!= $newData['products']['delete']){
             if($newData['products']['delete'] == 1)
             {
                 $string .= "Products Module - Products Delete disabled to enabled."."\n" ;
             }else if($newData['products']['delete'] == 0){
                 $string .= "Products Module - Products Delete enabled to disabled."."\n" ;
             }
         }
         
       /// Products Category
        if($prevData['product_category']['view']!= $newData['product_category']['view']){
            if($newData['product_category']['view'] == 1)
            {
                $string .= "Products Module - Products Category Submodule View disabled to enabled."."\n" ;
            }else if($newData['product_category']['view'] == 0){
                $string .= "Products Module - Products Category Submodule View enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_category']['create']!= $newData['product_category']['create']){
            if($newData['product_category']['create'] == 1)
            {
                $string .= "Products Module - Products Category Submodule Create disabled to enabled."."\n" ;
            }else if($newData['product_category']['create'] == 0){
                $string .= "Products Module - Products Category Submodule Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_category']['update']!= $newData['product_category']['update']){
            if($newData['product_category']['update'] == 1)
            {
                $string .= "Products Module - Products Category Submodule Update disabled to enabled."."\n" ;
            }else if($newData['product_category']['update'] == 0){
                $string .= "Products Module - Products Category Submodule Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_category']['disable']!= $newData['product_category']['disable']){
            if($newData['product_category']['disable'] == 1)
            {
                $string .= "Products Module - Products Category Submodule Disable disabled to enabled."."\n" ;
            }else if($newData['products']['disable'] == 0){
                $string .= "Products Module - Products Category Submodule Disable enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_category']['delete']!= $newData['product_category']['delete']){
            if($newData['product_category']['delete'] == 1)
            {
                $string .= "Products Module - Products Category Submodule Delete disabled to enabled."."\n" ;
            }else if($newData['product_category']['delete'] == 0){
                $string .= "Products Module - Products Category Submodule Delete enabled to disabled."."\n" ;
            }
        }
    
        //variants
        if($prevData['variants']['view']!= $newData['variants']['view']){
            if($newData['variants']['view'] == 1)
            {
                $string .= "Variants - Variants View disabled to enabled."."\n" ;
            }else if($newData['variants']['view'] == 0){
                $string .= "Variants - Variants View enabled to disabled."."\n" ;
            }
        }

        if($prevData['variants']['create']!= $newData['variants']['create']){
            if($newData['variants']['create'] == 1)
            {
                $string .= "Variants - Variants Create disabled to enabled."."\n" ;
            }else if($newData['variants']['create'] == 0){
                $string .= "Variants - Variants Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['variants']['update']!= $newData['variants']['update']){
            if($newData['variants']['update'] == 1)
            {
                $string .= "Variants - Variants Update disabled to enabled."."\n" ;
            }else if($newData['variants']['update'] == 0){
                $string .= "Variants - Variants Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['variants']['disable']!= $newData['variants']['disable']){
            if($newData['variants']['disable'] == 1)
            {
                $string .= "Variants - Variants Disable disabled to enabled."."\n" ;
            }else if($newData['variants']['disable'] == 0){
                $string .= "Variants - Variants Disable enabled to disabled."."\n" ;
            }
        }

        if($prevData['variants']['delete']!= $newData['variants']['delete']){
            if($newData['variants']['delete'] == 1)
            {
                $string .= "Variants - Variants Delete disabled to enabled."."\n" ;
            }else if($newData['variants']['delete'] == 0){
                $string .= "Variants - Variants Delete enabled to disabled."."\n" ;
            }
        }
        //Admin User List
        if($prevData['aul']['view']!= $newData['aul']['view']){
            if($newData['aul']['view'] == 1)
            {
                $string .= "User List - User List Submodule View disabled to enabled."."\n" ;
            }else if($newData['aul']['view'] == 0){
                $string .= "User List - User List Submodule View enabled to disabled."."\n" ;
            }
        }

        if($prevData['aul']['create']!= $newData['aul']['create']){
            if($newData['aul']['create'] == 1)
            {
                $string .= "User List - User List Submodule Create disabled to enabled."."\n" ;
            }else if($newData['aul']['create'] == 0){
                $string .= "User List - User List Submodule Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['aul']['update']!= $newData['aul']['update']){
            if($newData['aul']['update'] == 1)
            {
                $string .= "User List - User List Submodule Update disabled to enabled."."\n" ;
            }else if($newData['aul']['update'] == 0){
                $string .= "User List - User List Submodule Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['aul']['disable']!= $newData['aul']['disable']){
            if($newData['aul']['disable'] == 1)
            {
                $string .= "User List - User List Submodule disabled to enabled."."\n" ;
            }else if($newData['aul']['disable'] == 0){
                $string .= "User List - User List Submodule enabled to disabled."."\n" ;
            }
        }

        if($prevData['aul']['delete']!= $newData['aul']['delete']){
            if($newData['aul']['delete'] == 1)
            {
                $string .= "User List - User List Submodule Delete disabled to enabled."."\n" ;
            }else if($newData['aul']['delete'] == 0){
                $string .= "User List - User List Submodule Delete enabled to disabled."."\n" ;
            }
        }
        //orders
        

        if($prevData['orders']['decline']!= $newData['orders']['decline']){
            if($newData['orders']['decline'] == 1)
            {
                $string .= "Orders - Orders Module Decline disabled to enabled."."\n" ;
            }else if($newData['orders']['decline'] == 0){
                $string .= "Orders - Orders Module Decline Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['orders']['process']!= $newData['orders']['process']){
            if($newData['orders']['process'] == 1)
            {
                $string .= "Orders - Orders Module Process disabled to enabled."."\n" ;
            }else if($newData['aul']['delete'] == 0){
                $string .= "Orders - Orders Module Process Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['orders']['view']!= $newData['orders']['view']){
            if($newData['orders']['view'] == 1)
            {
                $string .= "Orders - Orders Module View disabled to enabled."."\n" ;
            }else if($newData['orders']['view'] == 0){
                $string .= "Orders - Orders Module View enabled to disabled."."\n" ;
            }
        }

        return $string;

    }

    public function resetLoginAttempts($user_id){
        $ip_address   = $this->getClientIP();
        $date_created = date('Y-m-d H:i:s');

        $attempt = $this->model->resetLoginAttempts($user_id, $ip_address, $date_created);
       
    }

    function prodPromString($prevData, $newData){
        $string = "\n";

        if(empty($prevData)){
            $string .= $newData['product_name']." successfully added.\nDetails:\n";

            if($newData['product_promo_type'] == 1){
                $product_promo_type = 'Piso Deals';
            }
            else{
                $product_promo_type = 'Mystery Coupon';
            }
            $string .= "Promo Type: ".$product_promo_type."\n";

            if($newData['product_promo_rate'] == 1){
                $product_promo_rate = 'Fixed';
            }
            else if($newData['product_promo_rate'] == 2){
                $product_promo_rate = 'Percentage';
            }
            else{
                $product_promo_rate = '';
            }

            $string .= "Promo Rate: ".$product_promo_rate."\n";
            $string .= "Promo Price: ".$newData['product_promo_price']."\n";
            $string .= "Promo Stock: ".$newData['product_promo_stock']."\n";
            $string .= "Purchase Limit: ".$newData['product_purch_limit']."\n";

            if($newData['product_status'] == 1){
                $product_status = 'Active';
            }
            else if($newData['product_status'] == 2){
                $product_status = 'Inactive';
            }
            else{
                $product_status = '';
            }
            $string .= "Status: ".$product_status."\n";

            $string .= "Start Date: ".$newData['start_date']."\n";
            $string .= "End Date: ".$newData['end_date']."\n";

        }
        else{
            $string .= $newData['product_name']." successfully updated.\nChanges:\n";
            $changes = 0;
            if($prevData['promo_type'] == 1){
                $prevproduct_promo_type = 'Piso Deals';
            }
            else{
                $prevproduct_promo_type = '';
            }

            if($newData['product_promo_type'] == 1){
                $newproduct_promo_type = 'Piso Deals';
            }
            else{
                $newproduct_promo_type = '';
            }

            if($prevData['promo_type'] != $newData['product_promo_type']){
                $string .= "Promo Type: ".$prevproduct_promo_type." into ".$newproduct_promo_type."\n";
                $changes = 1;
            }

            if($prevData['promo_rate'] == 1){
                $prevproduct_promo_rate = 'Fixed';
            }
            else if($prevData['promo_rate'] == 2){
                $prevproduct_promo_rate = 'Percentage';
            }
            else{
                $prevproduct_promo_rate = '';
            }

            if($newData['product_promo_rate'] == 1){
                $newproduct_promo_rate = 'Fixed';
            }
            else if($newData['product_promo_rate'] == 2){
                $newproduct_promo_rate = 'Percentage';
            }
            else{
                $newproduct_promo_rate = '';
            }

            if($prevData['promo_rate'] != $newData['product_promo_rate']){
                $string .= "Promo Rate: ".$prevproduct_promo_rate." into ".$newproduct_promo_rate."\n";
                $changes = 1;
            }

            if($prevData['promo_price'] != $newData['product_promo_price']){
                $string .= "Promo Price: ".$prevData['promo_price']." into ".$newData['product_promo_price']."\n";
                $changes = 1;
            }

            $prevData['promo_stock'] = ($prevData['promo_stock'] == null) ? 'No Limit' : $prevData['promo_stock'];
            $newData['product_promo_stock'] = ($newData['product_promo_stock'] == null) ? 'No Limit' : $newData['product_promo_stock'];

            if($prevData['promo_stock'] != $newData['product_promo_stock']){
                $string .= "Promo Stock: ".$prevData['promo_stock']." into ".$newData['product_promo_stock']."\n";
                $changes = 1;
            }

            $prevData['purchase_limit'] = ($prevData['purchase_limit'] == null || $prevData['purchase_limit'] == 0) ? 'No Limit' : $prevData['purchase_limit'];
            $newData['product_purch_limit'] = ($newData['product_purch_limit'] == null || $newData['product_purch_limit'] == 0) ? 'No Limit' : $newData['product_purch_limit'];

            if($prevData['purchase_limit'] != $newData['product_purch_limit']){
                $string .= "Purchase Limit: ".$prevData['purchase_limit']." into ".$newData['product_purch_limit']."\n";
                $changes = 1;
            }

            if($prevData['status'] == 1){
                $prevproduct_status = 'Active';
            }
            else if($prevData['status'] == 2){
                $prevproduct_status = 'Inactive';
            }
            else{
                $prevproduct_status = '';
            }

            if($newData['product_status'] == 1){
                $newproduct_status = 'Active';
            }
            else if($newData['product_status'] == 2){
                $newproduct_status = 'Inactive';
            }
            else{
                $newproduct_status = '';
            }

            if($prevData['status'] != $newData['product_status']){
                $string .= "Status: ".$prevproduct_status." into ".$newproduct_status."\n";
                $changes = 1;
            }

            if($prevData['start_date'] != $newData['start_date']){
                $string .= "Start Date: ".$prevData['start_date']." into ".$newData['start_date']."\n";
                $changes = 1;
            }

            if($prevData['end_date'] != $newData['end_date']){
                $string .= "End Date: ".$prevData['end_date']." into ".$newData['end_date']."\n";
                $changes = 1;
            }
            
            if($changes == 0){
                $string .= "None";
            }

        }
       
        return $string;
    }
}