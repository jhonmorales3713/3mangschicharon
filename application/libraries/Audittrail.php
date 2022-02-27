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
       // Merchant Registration
         if($prevData['merchant_registration']['view']!= $newData['merchant_registration']['view']){
            if($newData['merchant_registration']['view'] == 1)
            {
                $string .= "Shop Module - Merchant Registration View disabled to enabled."."\n" ;
            }else if($newData['merchant_registration']['view'] == 0){
                $string .= "Shop Module - Merchant Registration View enabled to disabled."."\n" ;
            }
         }

         if($prevData['merchant_registration']['approve']!= $newData['merchant_registration']['approve']){
            if($newData['merchant_registration']['approve'] == 1)
            {
                $string .= "Shop Module - Merchant Registration Approve disabled to enabled."."\n" ;
            }else if($newData['merchant_registration']['approve'] == 0){
                $string .= "Shop Module - Merchant Registration Approve enabled to disabled."."\n" ;
            }
         }

         if($prevData['merchant_registration']['edit']!= $newData['merchant_registration']['edit']){
            if($newData['merchant_registration']['edit'] == 1)
            {
                $string .= "Shop Module - Merchant Registration Edit disabled to enabled."."\n" ;
            }else if($newData['merchant_registration']['edit'] == 0){
                $string .= "Shop Module - Merchant Registration Edit enabled to disabled."."\n" ;
            }
         }

         if($prevData['merchant_registration']['decline']!= $newData['merchant_registration']['decline']){
            if($newData['merchant_registration']['decline'] == 1)
            {
                $string .= "Shop Module - Merchant Registration Decline disabled to enabled."."\n" ;
            }else if($newData['merchant_registration']['decline'] == 0){
                $string .= "Shop Module - Merchant Registration Decline enabled to disabled."."\n" ;
            }
         }

         if($prevData['merchant_registration']['delete']!= $newData['merchant_registration']['delete']){
            if($newData['merchant_registration']['delete'] == 1)
            {
                $string .= "Shop Module - Merchant Registration Delete disabled to enabled."."\n" ;
            }else if($newData['merchant_registration']['delete'] == 0){
                $string .= "Shop Module - Merchant Registration Delete enabled to disabled."."\n" ;
            }
         }

        //shop MCR approval
        if($prevData['shop_mcr']['view']!= $newData['shop_mcr']['view']){
            if($newData['shop_mcr']['view'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval View disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['view'] == 0){
                $string .= "Shop Module  - Shop MCR approval  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_mcr']['wfa_view']!= $newData['shop_mcr']['wfa_view']){
            if($newData['shop_mcr']['wfa_view'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Waiting for Approval View disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['wfa_view'] == 0){
                $string .= "Shop Module  - Shop MCR approval Waiting for Approval View enabled to disabled."."\n" ;
            }
        }


        if($prevData['shop_mcr']['approve_view']!= $newData['shop_mcr']['approve_view']){
            if($newData['shop_mcr']['approve_view'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Approve View disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['approve_view'] == 0){
                $string .= "Shop Module  - Shop MCR approval Approve View enabled to disabled."."\n" ;
            }
        }


        if($prevData['shop_mcr']['decline_view']!= $newData['shop_mcr']['decline_view']){
            if($newData['shop_mcr']['decline_view'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Decline View disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['decline_view'] == 0){
                $string .= "Shop Module  - Shop MCR approval Decline View enabled to disabled."."\n" ;
            }
        }


        if($prevData['shop_mcr']['verified_view']!= $newData['shop_mcr']['verified_view']){
            if($newData['shop_mcr']['verified_view'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Verified View disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['verified_view'] == 0){
                $string .= "Shop Module  - Shop MCR approval Verified View enabled to disabled."."\n" ;
            }
        }


        
        if($prevData['shop_mcr']['approve']!= $newData['shop_mcr']['approve']){
            if($newData['shop_mcr']['approve'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Approve Button disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['approve'] == 0){
                $string .= "Shop Module  - Shop MCR approval Approve Button enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_mcr']['verify']!= $newData['shop_mcr']['verify']){
            if($newData['shop_mcr']['verify'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Verify Button disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['verify'] == 0){
                $string .= "Shop Module  - Shop MCR approval Verify Button enabled to disabled."."\n" ;
            }
        }


        if($prevData['shop_mcr']['decline']!= $newData['shop_mcr']['decline']){
            if($newData['shop_mcr']['decline'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Decline Button disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['decline'] == 0){
                $string .= "Shop Module  - Shop MCR approval Decline Button enabled to disabled."."\n" ;
            }
        }


        if($prevData['shop_mcr']['edit']!= $newData['shop_mcr']['edit']){
            if($newData['shop_mcr']['edit'] == 1)
            {
                $string .= "Shop Module - Shop MCR approval Edit Button disabled to enabled."."\n" ;
            }else if($newData['shop_mcr']['edit'] == 0){
                $string .= "Shop Module  - Shop MCR approval Edit Button enabled to disabled."."\n" ;
            }
        }



        
        // Shop Account
        if($prevData['shop_account']['view']!= $newData['shop_account']['view']){
            if($newData['shop_account']['view'] == 1)
            {
                $string .= "Shop Module - Shops Account View disabled to enabled."."\n" ;
            }else if($newData['shop_account']['view'] == 0){
                $string .= "Shop Module - Shops Account View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_account']['create']!= $newData['shop_account']['create']){
            if($newData['shop_account']['create'] == 1)
            {
                $string .= "Shop Module - Shops Account Create disabled to enabled."."\n" ;
            }else if($newData['shop_account']['create'] == 0){
                $string .= "Shop Module - Shops Account Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_account']['update']!= $newData['shop_account']['update']){
            if($newData['shop_account']['update'] == 1)
            {
                $string .= "Shop Module - Shops Account Update disabled to enabled."."\n" ;
            }else if($newData['shop_account']['update'] == 0){
                $string .= "Shop Module - Shops Account Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_account']['delete']!= $newData['shop_account']['delete']){
            if($newData['shop_account']['delete'] == 1)
            {
                $string .= "Shop Module - Shops Account Delete disabled to enabled."."\n" ;
            }else if($newData['shop_account']['delete'] == 0){
                $string .= "Shop Module - Shops Account Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_account']['disable']!= $newData['shop_account']['disable']){
            if($newData['shop_account']['disable'] == 1)
            {
                $string .= "Shop Module - Shops Account Disable disabled to enabled."."\n" ;
            }else if($newData['shop_account']['disable'] == 0){
                $string .= "Shop Module - Shops Account Disable enabled to disabled."."\n" ;
            }
        }

        // Shop Branch Account
        if($prevData['branch_account']['view']!= $newData['branch_account']['view']){
            if($newData['branch_account']['view'] == 1)
            {
                $string .= "Shop Module - Shops Branch Account View disabled to enabled."."\n" ;
            }else if($newData['branch_account']['view'] == 0){
                $string .= "Shop Module - Shops Branch Account View enabled to disabled."."\n" ;
            }
        }

        if($prevData['branch_account']['create']!= $newData['branch_account']['create']){
            if($newData['branch_account']['create'] == 1)
            {
                $string .= "Shop Module - Shops Branch Account Create disabled to enabled."."\n" ;
            }else if($newData['branch_account']['create'] == 0){
                $string .= "Shop Module - Shops Branch Account Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['branch_account']['update']!= $newData['branch_account']['update']){
            if($newData['branch_account']['update'] == 1)
            {
                $string .= "Shop Module - Shops Branch Account Update disabled to enabled."."\n" ;
            }else if($newData['branch_account']['update'] == 0){
                $string .= "Shop Module - Shops Branch Account Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['branch_account']['delete']!= $newData['branch_account']['delete']){
            if($newData['branch_account']['delete'] == 1)
            {
                $string .= "Shop Module - Shops Branch Account Delete disabled to enabled."."\n" ;
            }else if($newData['branch_account']['delete'] == 0){
                $string .= "Shop Module - Shops Branch Account Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['branch_account']['disable']!= $newData['branch_account']['disable']){
            if($newData['branch_account']['disable'] == 1)
            {
                $string .= "Shop Module - Shops Branch Account Disable disabled to enabled."."\n" ;
            }else if($newData['branch_account']['disable'] == 0){
                $string .= "Shop Module - Shops Branch Account Disable enabled to disabled."."\n" ;
            }
        }

          // Shop Branch 
        if($prevData['shop_branch']['view']!= $newData['shop_branch']['view']){
            if($newData['shop_branch']['view'] == 1)
            {
                $string .= "Shop Module - Shops Branch View disabled to enabled."."\n" ;
            }else if($newData['shop_branch']['view'] == 0){
                $string .= "Shop Module - Shops Branch View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_branch']['create']!= $newData['shop_branch']['create']){
            if($newData['shop_branch']['create'] == 1)
            {
                $string .= "Shop Module - Shops Branch Create disabled to enabled."."\n" ;
            }else if($newData['shop_branch']['create'] == 0){
                $string .= "Shop Module - Shops Branch Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_branch']['update']!= $newData['shop_branch']['update']){
            if($newData['shop_branch']['update'] == 1)
            {
                $string .= "Shop Module - Shops Branch Update disabled to enabled."."\n" ;
            }else if($newData['shop_branch']['update'] == 0){
                $string .= "Shop Module - Shops Branch Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_branch']['delete']!= $newData['shop_branch']['delete']){
            if($newData['shop_branch']['delete'] == 1)
            {
                $string .= "Shop Module - Shops Branch Delete disabled to enabled."."\n" ;
            }else if($newData['shop_branch']['delete'] == 0){
                $string .= "Shop Module - Shops Branch Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_branch']['disable']!= $newData['shop_branch']['disable']){
            if($newData['shop_branch']['disable'] == 1)
            {
                $string .= "Shop Module - Shops Branch Disable disabled to enabled."."\n" ;
            }else if($newData['shop_branch']['disable'] == 0){
                $string .= "Shop Module - Shops Branch Disable enabled to disabled."."\n" ;
            }
        }

         // Shop Profile
         if($prevData['shops']['view']!= $newData['shops']['view']){
            if($newData['shops']['view'] == 1)
            {
                $string .= "Shop Module - Shops Profile View disabled to enabled."."\n" ;
            }else if($newData['shops']['view'] == 0){
                $string .= "Shop Module - Shops Profile View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shops']['create']!= $newData['shops']['create']){
            if($newData['shops']['create'] == 1)
            {
                $string .= "Shop Module - Shops Profile Create disabled to enabled."."\n" ;
            }else if($newData['shops']['create'] == 0){
                $string .= "Shop Module - Shops Profile Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['shops']['update']!= $newData['shops']['update']){
            if($newData['shops']['update'] == 1)
            {
                $string .= "Shop Module - Shops Profile Update disabled to enabled."."\n" ;
            }else if($newData['shops']['update'] == 0){
                $string .= "Shop Module - Shops Profile Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['shops']['delete']!= $newData['shops']['delete']){
            if($newData['shops']['delete'] == 1)
            {
                $string .= "Shop Module - Shops Profile Delete disabled to enabled."."\n" ;
            }else if($newData['shops']['delete'] == 0){
                $string .= "Shop Module - Shops Profile Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['shops']['disable']!= $newData['shops']['disable']){
            if($newData['shops']['disable'] == 1)
            {
                $string .= "Shop Module - Shops Profile Disable disabled to enabled."."\n" ;
            }else if($newData['shops']['disable'] == 0){
                $string .= "Shop Module - Shops Profile Disable enabled to disabled."."\n" ;
            }
        }

        // Shop pop up image
        if($prevData['shop_popup']['view']!= $newData['shop_popup']['view']){
            if($newData['shop_popup']['view'] == 1)
            {
                $string .= "Shop Module - Shops Pop up View disabled to enabled."."\n" ;
            }else if($newData['shop_popup']['view'] == 0){
                $string .= "Shop Module - Shops Pop up View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_popup']['create']!= $newData['shop_popup']['create']){
            if($newData['shop_popup']['create'] == 1)
            {
                $string .= "Shop Module - Shops Pop up Create disabled to enabled."."\n" ;
            }else if($newData['shop_popup']['create'] == 0){
                $string .= "Shop Module -Shops Pop up Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_popup']['update']!= $newData['shop_popup']['update']){
            if($newData['shop_popup']['update'] == 1)
            {
                $string .= "Shop Module -  Shops Pop up Update disabled to enabled."."\n" ;
            }else if($newData['shop_popup']['update'] == 0){
                $string .= "Shop Module -  Shops Pop up Update enabled to disabled."."\n" ;
            }
        } 


          //// Faqs
          if($prevData['faqs']['view']!= $newData['faqs']['view']){
            if($newData['faqs']['view'] == 1)
            {
                $string .= "Settings Module - Faqs View disabled to enabled."."\n" ;
            }else if($newData['faqs']['view'] == 0){
                $string .= "Settings Module  - Faqs  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['faqs']['create']!= $newData['faqs']['create']){
            if($newData['faqs']['create'] == 1)
            {
                $string .= "Settings Module - Faqs Create disabled to enabled."."\n" ;
            }else if($newData['faqs']['create'] == 0){
                $string .= "Settings Module  - Faqs  Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['faqs']['update']!= $newData['faqs']['update']){
            if($newData['faqs']['update'] == 1)
            {
                $string .= "Settings Module - Faqs Update disabled to enabled."."\n" ;
            }else if($newData['faqs']['update'] == 0){
                $string .= "Settings Module  - Faqs  Update enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['faqs']['disable']!= $newData['faqs']['disable']){
            if($newData['faqs']['disable'] == 1)
            {
                $string .= "Settings Module - Faqs Disable disabled to enabled."."\n" ;
            }else if($newData['faqs']['disable'] == 0){
                $string .= "Settings Module  - Faqs  Disable enabled to disabled."."\n" ;
            }
        }

        if($prevData['faqs']['delete']!= $newData['faqs']['delete']){
            if($newData['faqs']['delete'] == 1)
            {
                $string .= "Settings Module - Faqs  Delete disabled to enabled."."\n" ;
            }else if($newData['faqs']['delete'] == 0){
                $string .= "Settings Module  - Faqs  Delete enabled to disabled."."\n" ;
            }
        }
        
         // Developer Settings Module
         //Audit Trail
         if($prevData['audit_trail']['view']!= $newData['audit_trail']['view']){
            if($newData['audit_trail']['view'] == 1)
            {
                $string .= "Developer Settings Module - Audit Trail View disabled to enabled."."\n" ;
            }else if($newData['audit_trail']['view'] == 0){
                $string .= "Developer Settings Module  - Audit Trail View enabled to disabled."."\n" ;
            }
        }

        //Tok-Tok API Postback Logs
        if($prevData['api_postback_logs']['view']!= $newData['api_postback_logs']['view']){
            if($newData['api_postback_logs']['view'] == 1)
            {
                $string .= "Developer Settings Module - Tok-Tok API Postback Logs View disabled to enabled."."\n" ;
            }else if($newData['api_postback_logs']['view'] == 0){
                $string .= "Developer Settings Module  - Tok-Tok API Postback Logs View enabled to disabled."."\n" ;
            }
        }
    
         //Panda Books API  Logs
         if($prevData['pandabooks_api_logs']['view']!= $newData['pandabooks_api_logs']['view']){
            if($newData['pandabooks_api_logs']['view'] == 1)
            {
                $string .= "Developer Settings Module - Pandabooks Api Logs View disabled to enabled."."\n" ;
            }else if($newData['pandabooks_api_logs']['view'] == 0){
                $string .= "Developer Settings Module  - Pandabooks Api Logs View enabled to disabled."."\n" ;
            }
        }

        //Shop Utilities
        if($prevData['shop_utilities']['view']!= $newData['shop_utilities']['view']){
            if($newData['shop_utilities']['view'] == 1)
            {
                $string .= "Developer Settings Module - Shop Utilities View disabled to enabled."."\n" ;
            }else if($newData['shop_utilities']['view'] == 0){
                $string .= "Developer Settings Module  - Shop Utilities View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_utilities']['update']!= $newData['shop_utilities']['update']){
            if($newData['shop_utilities']['update'] == 1)
            {
                $string .= "Developer Settings Module - Shop Utilities Update disabled to enabled."."\n" ;
            }else if($newData['shop_utilities']['update'] == 0){
                $string .= "Developer Settings Module  - Shop Utilities Update enabled to disabled."."\n" ;
            }
        }

         //Content Navigation
         if($prevData['content_navigation']['view']!= $newData['content_navigation']['view']){
            if($newData['content_navigation']['view'] == 1)
            {
                $string .= "Developer Settings Module - Content Navigation  View disabled to enabled."."\n" ;
            }else if($newData['content_navigation']['view'] == 0){
                $string .= "Developer Settings Module  - Content Navigation View enabled to disabled."."\n" ;
            }
        }

        if($prevData['content_navigation']['update']!= $newData['content_navigation']['update']){
            if($newData['content_navigation']['update'] == 1)
            {
                $string .= "Developer Settings Module - Content Navigation Update disabled to enabled."."\n" ;
            }else if($newData['content_navigation']['update'] == 0){
                $string .= "Developer Settings Module  - Content Navigation Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['content_navigation']['create']!= $newData['content_navigation']['create']){
            if($newData['content_navigation']['create'] == 1)
            {
                $string .= "Developer Settings Module - Content Navigation Create disabled to enabled."."\n" ;
            }else if($newData['content_navigation']['create'] == 0){
                $string .= "Developer Settings Module  - Content Navigation Create enabled to disabled."."\n" ;
            }
        }


        if($prevData['content_navigation']['delete']!= $newData['content_navigation']['delete']){
            if($newData['content_navigation']['delete'] == 1)
            {
                $string .= "Developer Settings Module - Content Navigation Delete disabled to enabled."."\n" ;
            }else if($newData['content_navigation']['delete'] == 0){
                $string .= "Developer Settings Module  - Content Navigation Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['content_navigation']['disable']!= $newData['content_navigation']['disable']){
            if($newData['content_navigation']['disable'] == 1)
            {
                $string .= "Developer Settings Module - Content Navigation Disable disabled to enabled."."\n" ;
            }else if($newData['content_navigation']['disable'] == 0){
                $string .= "Developer Settings Module  - Content Navigation Disable enabled to disabled."."\n" ;
            }
        }

        //Cron Logs
        if($prevData['cron_logs']['view']!= $newData['cron_logs']['view']){
            if($newData['cron_logs']['view'] == 1)
            {
                $string .= "Developer Settings Module - Cron Logs View disabled to enabled."."\n" ;
            }else if($newData['cron_logs']['view'] == 0){
                $string .= "Developer Settings Module  - Cron Logs View enabled to disabled."."\n" ;
            }
        }

        if($prevData['cron_logs']['disable']!= $newData['cron_logs']['disable']){
            if($newData['cron_logs']['disable'] == 1)
            {
                $string .= "Developer Settings Module - Cron Logs Disable disabled to enabled."."\n" ;
            }else if($newData['cron_logs']['disable'] == 0){
                $string .= "Developer Settings Module  - Cron Logs Disable enabled to disabled."."\n" ;
            }
        }

          //Manual Cron
          if($prevData['manual_cron']!= $newData['manual_cron']){
            if($newData['manual_cron'] == 1)
            {
                $string .= "Developer Settings Module - Manual Cron  disabled to enabled."."\n" ;
            }else if($newData['manual_cron'] == 0){
                $string .= "Developer Settings Module  - Manual Cron  enabled to disabled."."\n" ;
            }
        }

        //Client Information
        if($prevData['client_information']['view']!= $newData['client_information']['view']){
            if($newData['client_information']['view'] == 1)
            {
                $string .= "Developer Settings Module - Client Information View disabled to enabled."."\n" ;
            }else if($newData['client_information']['view'] == 0){
                $string .= "Developer Settings Module  - Client Information View enabled to disabled."."\n" ;
            }
        }

        if($prevData['client_information']['create']!= $newData['client_information']['create']){
            if($newData['client_information']['create'] == 1)
            {
                $string .= "Developer Settings Module - Client Information Create disabled to enabled."."\n" ;
            }else if($newData['client_information']['create'] == 0){
                $string .= "Developer Settings Module  - Client Information Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['client_information']['update']!= $newData['client_information']['update']){
            if($newData['client_information']['update'] == 1)
            {
                $string .= "Developer Settings Module - Client Information Update disabled to enabled."."\n" ;
            }else if($newData['client_information']['update'] == 0){
                $string .= "Developer Settings Module  - Client Information Update enabled to disabled."."\n" ;
            }
        }


        if($prevData['client_information']['delete']!= $newData['client_information']['delete']){
            if($newData['client_information']['delete'] == 1)
            {
                $string .= "Developer Settings Module - Client Information Delete disabled to enabled."."\n" ;
            }else if($newData['client_information']['delete'] == 0){
                $string .= "Developer Settings Module  - Client Information Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['client_information']['disable']!= $newData['client_information']['disable']){
            if($newData['client_information']['disable'] == 1)
            {
                $string .= "Developer Settings Module - Client Information Disable disabled to enabled."."\n" ;
            }else if($newData['client_information']['disable'] == 0){
                $string .= "Developer Settings Module  - Client Information Disable enabled to disabled."."\n" ;
            }
        }

        //Maintenance Page
        if($prevData['maintenance_page']['view']!= $newData['maintenance_page']['view']){
            if($newData['maintenance_page']['view'] == 1)
            {
                $string .= "Developer Settings Module - Maintenance Page View disabled to enabled."."\n" ;
            }else if($newData['maintenance_page']['view'] == 0){
                $string .= "Developer Settings Module  - Maintenance Page View enabled to disabled."."\n" ;
            }
        }

        if($prevData['maintenance_page']['update']!= $newData['maintenance_page']['update']){
            if($newData['maintenance_page']['update'] == 1)
            {
                $string .= "Developer Settings Module - Maintenance Page Update disabled to enabled."."\n" ;
            }else if($newData['maintenance_page']['update'] == 0){
                $string .= "Developer Settings Module  - Maintenance Page Update enabled to disabled."."\n" ;
            }
        }


         //Api Request Postback Logs
         if($prevData['api_request_postback_logs']['view']!= $newData['api_request_postback_logs']['view']){
            if($newData['api_request_postback_logs']['view'] == 1)
            {
                $string .= "Developer Settings Module - Api Request Postback Logs View disabled to enabled."."\n" ;
            }else if($newData['api_request_postback_logs']['view'] == 0){
                $string .= "Developer Settings Module  - Api Request Postback Logs View enabled to disabled."."\n" ;
            }
        }


           //Email Settings
        if($prevData['email_settings']['view']!= $newData['email_settings']['view']){
            if($newData['email_settings']['view'] == 1)
            {
                $string .= "Developer Settings Module - Email Settings View disabled to enabled."."\n" ;
            }else if($newData['email_settings']['view'] == 0){
                $string .= "Developer Settings Module  - Email Settings View enabled to disabled."."\n" ;
            }
        }

        if($prevData['email_settings']['update']!= $newData['email_settings']['update']){
            if($newData['email_settings']['update'] == 1)
            {
                $string .= "Developer Settings Module - Email Settings Update disabled to enabled."."\n" ;
            }else if($newData['email_settings']['update'] == 0){
                $string .= "Developer Settings Module  - Email Settings Update enabled to disabled."."\n" ;
            }
        }

        //Supports
        //Ticket History
        if($prevData['ticket_history']['view']!= $newData['ticket_history']['view']){
            if($newData['ticket_history']['view'] == 1)
            {
                $string .= "Supports Module - Ticket History View disabled to enabled."."\n" ;
            }else if($newData['ticket_history']['view'] == 0){
                $string .= "Supports Module  - Ticket History  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['ticket_history']['create']!= $newData['ticket_history']['create']){
            if($newData['ticket_history']['create'] == 1)
            {
                $string .= "Supports Module - Ticket History Create disabled to enabled."."\n" ;
            }else if($newData['ticket_history']['create'] == 0){
                $string .= "Supports Module  - Ticket History  Create enabled to disabled."."\n" ;
            }
        }


        if($prevData['ticket_history']['update']!= $newData['ticket_history']['update']){
            if($newData['ticket_history']['update'] == 1)
            {
                $string .= "Supports Module - Ticket History Update disabled to enabled."."\n" ;
            }else if($newData['ticket_history']['update'] == 0){
                $string .= "Supports Module  - Ticket History  Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['ticket_history']['delete']!= $newData['ticket_history']['delete']){
            if($newData['ticket_history']['delete'] == 1)
            {
                $string .= "Supports Module - Ticket History Delete disabled to enabled."."\n" ;
            }else if($newData['ticket_history']['delete'] == 0){
                $string .= "Supports Module  - Ticket History  Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['ticket_history']['disable']!= $newData['ticket_history']['disable']){
            if($newData['ticket_history']['disable'] == 1)
            {
                $string .= "Supports Module - Ticket History Disable disabled to enabled."."\n" ;
            }else if($newData['ticket_history']['disable'] == 0){
                $string .= "Supports Module  - Ticket History  Disable enabled to disabled."."\n" ;
            }
        }

        //CSR Ticket
        if($prevData['csr_ticket']['view']!= $newData['csr_ticket']['view']){
            if($newData['csr_ticket']['view'] == 1)
            {
                $string .= "Supports Module - CSR Ticket View disabled to enabled."."\n" ;
            }else if($newData['csr_ticket']['view'] == 0){
                $string .= "Supports Module  - CSR Ticket  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['csr_ticket']['create']!= $newData['csr_ticket']['create']){
            if($newData['csr_ticket']['create'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Create disabled to enabled."."\n" ;
            }else if($newData['csr_ticket']['create'] == 0){
                $string .= "Supports Module  - CSR Ticket  Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['csr_ticket']['update']!= $newData['csr_ticket']['update']){
            if($newData['csr_ticket']['update'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Update disabled to enabled."."\n" ;
            }else if($newData['csr_ticket']['update'] == 0){
                $string .= "Supports Module  - CSR Ticket  Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['csr_ticket']['delete']!= $newData['csr_ticket']['delete']){
            if($newData['csr_ticket']['delete'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Delete disabled to enabled."."\n" ;
            }else if($newData['csr_ticket']['delete'] == 0){
                $string .= "Supports Module  - CSR Ticket  Delete enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['csr_ticket']['disable']!= $newData['csr_ticket']['disable']){
            if($newData['csr_ticket']['disable'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Disable disabled to enabled."."\n" ;
            }else if($newData['csr_ticket']['disable'] == 0){
                $string .= "Supports Module  - CSR Ticket  Disable enabled to disabled."."\n" ;
            }
        }


        //CSR Ticket Log
        if($prevData['csr_ticket_log']['view']!= $newData['csr_ticket_log']['view']){
            if($newData['csr_ticket_log']['view'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Log View disabled to enabled."."\n" ;
            }else if($newData['csr_ticket_log']['view'] == 0){
                $string .= "Supports Module  - CSR Ticket Log  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['csr_ticket_log']['create']!= $newData['csr_ticket_log']['create']){
            if($newData['csr_ticket_log']['create'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Log Create disabled to enabled."."\n" ;
            }else if($newData['csr_ticket_log']['create'] == 0){
                $string .= "Supports Module  - CSR Ticket Log Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['csr_ticket_log']['update']!= $newData['csr_ticket_log']['update']){
            if($newData['csr_ticket_log']['update'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Log Update disabled to enabled."."\n" ;
            }else if($newData['csr_ticket_log']['update'] == 0){
                $string .= "Supports Module  - CSR Ticket Log  Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['csr_ticket_log']['delete']!= $newData['csr_ticket_log']['delete']){
            if($newData['csr_ticket_log']['delete'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Log Delete disabled to enabled."."\n" ;
            }else if($newData['csr_ticket_log']['delete'] == 0){
                $string .= "Supports Module  - CSR Ticket Log Delete enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['csr_ticket_log']['disable']!= $newData['csr_ticket_log']['disable']){
            if($newData['csr_ticket_log']['disable'] == 1)
            {
                $string .= "Supports Module - CSR Ticket Log Disable disabled to enabled."."\n" ;
            }else if($newData['csr_ticket_log']['disable'] == 0){
                $string .= "Supports Module  - CSR Ticket Log Disable enabled to disabled."."\n" ;
            }
        }


        //Wallet
        //Pre Payment
        if($prevData['prepayment']['view']!= $newData['prepayment']['view']){
            if($newData['prepayment']['view'] == 1)
            {
                $string .= "Wallet Module - Pre Payment View disabled to enabled."."\n" ;
            }else if($newData['prepayment']['view'] == 0){
                $string .= "Wallet Module  - Pre Payment View enabled to disabled."."\n" ;
            }
        }

        if($prevData['prepayment']['create']!= $newData['prepayment']['create']){
            if($newData['prepayment']['create'] == 1)
            {
                $string .= "Wallet Module - Pre Payment Create disabled to enabled."."\n" ;
            }else if($newData['prepayment']['create'] == 0){
                $string .= "Wallet Module  - Pre Payment Create enabled to disabled."."\n" ;
            }
        }


          //Manual Order
        if($prevData['manual_order']['view']!= $newData['manual_order']['view']){
            if($newData['manual_order']['view'] == 1)
            {
                $string .= "Wallet Module - Manual Order View disabled to enabled."."\n" ;
            }else if($newData['manual_order']['view'] == 0){
                $string .= "Wallet Module  - Manual Order View enabled to disabled."."\n" ;
            }
        }


        if($prevData['manual_order']['create']!= $newData['manual_order']['create']){
            if($newData['manual_order']['create'] == 1)
            {
                $string .= "Wallet Module - Manual Order Create disabled to enabled."."\n" ;
            }else if($newData['manual_order']['create'] == 0){
                $string .= "Wallet Module  - Manual Order Create enabled to disabled."."\n" ;
            }
        }


        //Voucher
        //Vouchers Claimed
        if($prevData['vc']['view']!= $newData['vc']['view']){
            if($newData['vc']['view'] == 1)
            {
                $string .= "Voucher Module - Voucher Claimed View disabled to enabled."."\n" ;
            }else if($newData['vc']['view'] == 0){
                $string .= "Voucher Module  - Voucher Claimed View enabled to disabled."."\n" ;
            }
        }


         //Vouchers List
         if($prevData['voucher_list']['view']!= $newData['voucher_list']['view']){
            if($newData['voucher_list']['view'] == 1)
            {
                $string .= "Voucher Module - Voucher List View disabled to enabled."."\n" ;
            }else if($newData['voucher_list']['view'] == 0){
                $string .= "Voucher Module  - Voucher List View enabled to disabled."."\n" ;
            }
        }


        if($prevData['voucher_list']['create']!= $newData['voucher_list']['create']){
            if($newData['voucher_list']['create'] == 1)
            {
                $string .= "Voucher Module - Voucher List Create disabled to enabled."."\n" ;
            }else if($newData['voucher_list']['create'] == 0){
                $string .= "Voucher Module  - Voucher List Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['voucher_list']['update']!= $newData['voucher_list']['update']){
            if($newData['voucher_list']['update'] == 1)
            {
                $string .= "Voucher Module - Voucher List Update disabled to enabled."."\n" ;
            }else if($newData['voucher_list']['update'] == 0){
                $string .= "Voucher Module  - Voucher List Update enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['voucher_list']['delete']!= $newData['voucher_list']['delete']){
            if($newData['voucher_list']['delete'] == 1)
            {
                $string .= "Voucher Module - Voucher List Delete disabled to enabled."."\n" ;
            }else if($newData['voucher_list']['delete'] == 0){
                $string .= "Voucher Module  - Voucher List Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['voucher_list']['disable']!= $newData['voucher_list']['disable']){
            if($newData['voucher_list']['disable'] == 1)
            {
                $string .= "Voucher Module - Voucher List Disable disabled to enabled."."\n" ;
            }else if($newData['voucher_list']['disable'] == 0){
                $string .= "Voucher Module  - Voucher List Disable enabled to disabled."."\n" ;
            }
        }


        // Reclaimed Vouchers List
        if($prevData['rec_vc']['view']!= $newData['rec_vc']['view']){
            if($newData['rec_vc']['view'] == 1)
            {
                $string .= "Voucher Module - Reclaimed Voucher List View disabled to enabled."."\n" ;
            }else if($newData['rec_vc']['view'] == 0){
                $string .= "Voucher Module  - Reclaimed Voucher List View enabled to disabled."."\n" ;
            }
        }


        //Settings
        //Announcement
        if($prevData['announcement']['view']!= $newData['announcement']['view']){
            if($newData['announcement']['view'] == 1)
            {
                $string .= "Settings Module - Announcement View disabled to enabled."."\n" ;
            }else if($newData['announcement']['view'] == 0){
                $string .= "Settings Module  - Announcement View enabled to disabled."."\n" ;
            }
        }

        if($prevData['announcement']['update']!= $newData['announcement']['update']){
            if($newData['announcement']['update'] == 1)
            {
                $string .= "Settings Module - Announcement Update disabled to enabled."."\n" ;
            }else if($newData['announcement']['update'] == 0){
                $string .= "Settings Module  - Announcement Update enabled to disabled."."\n" ;
            }
        }


        //Members
        if($prevData['members']['view']!= $newData['members']['view']){
            if($newData['members']['view'] == 1)
            {
                $string .= "Settings Module - Members View disabled to enabled."."\n" ;
            }else if($newData['members']['view'] == 0){
                $string .= "Settings Module  - Members View enabled to disabled."."\n" ;
            }
        }

        if($prevData['members']['create']!= $newData['members']['create']){
            if($newData['members']['create'] == 1)
            {
                $string .= "Settings Module - Members Create disabled to enabled."."\n" ;
            }else if($newData['members']['create'] == 0){
                $string .= "Settings Module  - Members Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['members']['update']!= $newData['members']['update']){
            if($newData['members']['update'] == 1)
            {
                $string .= "Settings Module - Members Update disabled to enabled."."\n" ;
            }else if($newData['members']['update'] == 0){
                $string .= "Settings Module  - Members Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['members']['disable']!= $newData['members']['disable']){
            if($newData['members']['disable'] == 1)
            {
                $string .= "Settings Module - Members Disable disabled to enabled."."\n" ;
            }else if($newData['members']['disable'] == 0){
                $string .= "Settings Module  - Members Disable enabled to disabled."."\n" ;
            }
        }

        if($prevData['members']['delete']!= $newData['members']['delete']){
            if($newData['members']['delete'] == 1)
            {
                $string .= "Settings Module - Members Delete disabled to enabled."."\n" ;
            }else if($newData['members']['delete'] == 0){
                $string .= "Settings Module  - Members Delete enabled to disabled."."\n" ;
            }
        }

        
        //Currency
        if($prevData['currency']['view']!= $newData['currency']['view']){
            if($newData['currency']['view'] == 1)
            {
                $string .= "Settings Module - Currency View disabled to enabled."."\n" ;
            }else if($newData['currency']['view'] == 0){
                $string .= "Settings Module  - Currency View enabled to disabled."."\n" ;
            }
        }


        if($prevData['currency']['create']!= $newData['currency']['create']){
            if($newData['currency']['create'] == 1)
            {
                $string .= "Settings Module - Currency Create disabled to enabled."."\n" ;
            }else if($newData['currency']['create'] == 0){
                $string .= "Settings Module  - Currency Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['currency']['update']!= $newData['currency']['update']){
            if($newData['currency']['update'] == 1)
            {
                $string .= "Settings Module - Currency Update disabled to enabled."."\n" ;
            }else if($newData['currency']['update'] == 0){
                $string .= "Settings Module  - Currency Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['currency']['delete']!= $newData['currency']['delete']){
            if($newData['currency']['delete'] == 1)
            {
                $string .= "Settings Module - Currency Delete disabled to enabled."."\n" ;
            }else if($newData['currency']['delete'] == 0){
                $string .= "Settings Module  - Currency Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['currency']['disable']!= $newData['currency']['disable']){
            if($newData['currency']['disable'] == 1)
            {
                $string .= "Settings Module - Currency Disable disabled to enabled."."\n" ;
            }else if($newData['currency']['disable'] == 0){
                $string .= "Settings Module  - Currency Disable enabled to disabled."."\n" ;
            }
        }

         //Payment Types
         if($prevData['payment_type']['view']!= $newData['payment_type']['view']){
            if($newData['payment_type']['view'] == 1)
            {
                $string .= "Settings Module - Payment Type View disabled to enabled."."\n" ;
            }else if($newData['payment_type']['view'] == 0){
                $string .= "Settings Module  - Payment Type  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['payment_type']['create']!= $newData['payment_type']['create']){
            if($newData['payment_type']['create'] == 1)
            {
                $string .= "Settings Module - Payment Type Create disabled to enabled."."\n" ;
            }else if($newData['payment_type']['create'] == 0){
                $string .= "Settings Module  - Payment Type  Create enabled to disabled."."\n" ;
            }
        }


        if($prevData['payment_type']['update']!= $newData['payment_type']['update']){
            if($newData['payment_type']['update'] == 1)
            {
                $string .= "Settings Module - Payment Type Update disabled to enabled."."\n" ;
            }else if($newData['payment_type']['update'] == 0){
                $string .= "Settings Module  - Payment Type  Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['payment_type']['delete']!= $newData['payment_type']['delete']){
            if($newData['payment_type']['delete'] == 1)
            {
                $string .= "Settings Module - Payment Type Delete disabled to enabled."."\n" ;
            }else if($newData['payment_type']['delete'] == 0){
                $string .= "Settings Module  - Payment Type  Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['payment_type']['disable']!= $newData['payment_type']['disable']){
            if($newData['payment_type']['disable'] == 1)
            {
                $string .= "Settings Module - Payment Type Disable disabled to enabled."."\n" ;
            }else if($newData['payment_type']['disable'] == 0){
                $string .= "Settings Module  - Payment Type  Disable enabled to disabled."."\n" ;
            }
        }

          //Referral Comrate
        if($prevData['ref_comrate']['view']!= $newData['ref_comrate']['view']){
            if($newData['ref_comrate']['view'] == 1)
            {
                $string .= "Settings Module - Referral Comrate View disabled to enabled."."\n" ;
            }else if($newData['ref_comrate']['view'] == 0){
                $string .= "Settings Module  - Referral Comrate  View enabled to disabled."."\n" ;
            }
        }

        if($prevData['ref_comrate']['create']!= $newData['ref_comrate']['create']){
            if($newData['ref_comrate']['create'] == 1)
            {
                $string .= "Settings Module - Referral Comrate Create disabled to enabled."."\n" ;
            }else if($newData['ref_comrate']['create'] == 0){
                $string .= "Settings Module  - Referral Comrate  Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['ref_comrate']['update']!= $newData['ref_comrate']['update']){
            if($newData['ref_comrate']['update'] == 1)
            {
                $string .= "Settings Module - Referral Comrate Update disabled to enabled."."\n" ;
            }else if($newData['ref_comrate']['update'] == 0){
                $string .= "Settings Module  - Referral Comrate  Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['ref_comrate']['delete']!= $newData['ref_comrate']['delete']){
            if($newData['ref_comrate']['delete'] == 1)
            {
                $string .= "Settings Module - Referral Comrate Delete disabled to enabled."."\n" ;
            }else if($newData['ref_comrate']['delete'] == 0){
                $string .= "Settings Module  - Referral Comrate  Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['ref_comrate']['disable']!= $newData['ref_comrate']['disable']){
            if($newData['ref_comrate']['disable'] == 1)
            {
                $string .= "Settings Module - Referral Comrate Disable disabled to enabled."."\n" ;
            }else if($newData['ref_comrate']['disable'] == 0){
                $string .= "Settings Module  - Referral Comrate  Disable enabled to disabled."."\n" ;
            }
        }

        
        //Region
        if($prevData['settings_region']['view']!= $newData['settings_region']['view']){
            if($newData['settings_region']['view'] == 1)
            {
                $string .= "Settings Module - Region View disabled to enabled."."\n" ;
            }else if($newData['settings_region']['view'] == 0){
                $string .= "Settings Module  - Region View enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_region']['create']!= $newData['settings_region']['create']){
            if($newData['settings_region']['create'] == 1)
            {
                $string .= "Settings Module - Region Create disabled to enabled."."\n" ;
            }else if($newData['settings_region']['create'] == 0){
                $string .= "Settings Module  - Region Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_region']['update']!= $newData['settings_region']['update']){
            if($newData['settings_region']['update'] == 1)
            {
                $string .= "Settings Module - Region Update disabled to enabled."."\n" ;
            }else if($newData['settings_region']['update'] == 0){
                $string .= "Settings Module  - Region Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_region']['delete']!= $newData['settings_region']['delete']){
            if($newData['settings_region']['delete'] == 1)
            {
                $string .= "Settings Module - Region Delete disabled to enabled."."\n" ;
            }else if($newData['settings_region']['delete'] == 0){
                $string .= "Settings Module  - Region Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_region']['disable']!= $newData['settings_region']['disable']){
            if($newData['settings_region']['disable'] == 1)
            {
                $string .= "Settings Module - Region Disable disabled to enabled."."\n" ;
            }else if($newData['settings_region']['disable'] == 0){
                $string .= "Settings Module  - Region Disable enabled to disabled."."\n" ;
            }
        }

        //City
        if($prevData['settings_city']['view']!= $newData['settings_city']['view']){
            if($newData['settings_city']['view'] == 1)
            {
                $string .= "Settings Module - City View disabled to enabled."."\n" ;
            }else if($newData['settings_city']['view'] == 0){
                $string .= "Settings Module  - City View enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_city']['create']!= $newData['settings_city']['create']){
            if($newData['settings_city']['create'] == 1)
            {
                $string .= "Settings Module - City Create disabled to enabled."."\n" ;
            }else if($newData['settings_city']['create'] == 0){
                $string .= "Settings Module  - City Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_city']['update']!= $newData['settings_city']['update']){
            if($newData['settings_city']['update'] == 1)
            {
                $string .= "Settings Module - City Update disabled to enabled."."\n" ;
            }else if($newData['settings_city']['update'] == 0){
                $string .= "Settings Module  - City Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_city']['delete']!= $newData['settings_city']['delete']){
            if($newData['settings_city']['delete'] == 1)
            {
                $string .= "Settings Module - City Delete disabled to enabled."."\n" ;
            }else if($newData['settings_city']['delete'] == 0){
                $string .= "Settings Module  - City Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_city']['disable']!= $newData['settings_city']['disable']){
            if($newData['settings_city']['disable'] == 1)
            {
                $string .= "Settings Module - City Disable disabled to enabled."."\n" ;
            }else if($newData['settings_city']['disable'] == 0){
                $string .= "Settings Module  - City Disable enabled to disabled."."\n" ;
            }
        }


         //Province
         if($prevData['settings_province']['view']!= $newData['settings_province']['view']){
            if($newData['settings_province']['view'] == 1)
            {
                $string .= "Settings Module - Province View disabled to enabled."."\n" ;
            }else if($newData['settings_province']['view'] == 0){
                $string .= "Settings Module  - Province View enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_province']['create']!= $newData['settings_province']['create']){
            if($newData['settings_province']['create'] == 1)
            {
                $string .= "Settings Module - Province Create disabled to enabled."."\n" ;
            }else if($newData['settings_province']['create'] == 0){
                $string .= "Settings Module  - Province Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_province']['update']!= $newData['settings_province']['update']){
            if($newData['settings_province']['update'] == 1)
            {
                $string .= "Settings Module - Province Update disabled to enabled."."\n" ;
            }else if($newData['settings_province']['update'] == 0){
                $string .= "Settings Module  - Province Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_province']['delete']!= $newData['settings_province']['delete']){
            if($newData['settings_province']['delete'] == 1)
            {
                $string .= "Settings Module - Province Delete disabled to enabled."."\n" ;
            }else if($newData['settings_province']['delete'] == 0){
                $string .= "Settings Module  - Province Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['settings_province']['disable']!= $newData['settings_province']['disable']){
            if($newData['settings_province']['disable'] == 1)
            {
                $string .= "Settings Module - Province Disable disabled to enabled."."\n" ;
            }else if($newData['settings_province']['disable'] == 0){
                $string .= "Settings Module  - Province Disable enabled to disabled."."\n" ;
            }
        }

        //Shipping and Delivery
        //General Shipping
        if($prevData['general_shipping']['view']!= $newData['general_shipping']['view']){
            if($newData['general_shipping']['view'] == 1)
            {
                $string .= "Settings Module - General Shipping View disabled to enabled."."\n" ;
            }else if($newData['general_shipping']['view'] == 0){
                $string .= "Settings Module  - General Shipping View enabled to disabled."."\n" ;
            }
        }

        if($prevData['general_shipping']['create']!= $newData['general_shipping']['create']){
            if($newData['general_shipping']['create'] == 1)
            {
                $string .= "Settings Module - General Shipping Create disabled to enabled."."\n" ;
            }else if($newData['general_shipping']['create'] == 0){
                $string .= "Settings Module  - General Shipping Create enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['general_shipping']['update']!= $newData['general_shipping']['update']){
            if($newData['general_shipping']['update'] == 1)
            {
                $string .= "Settings Module - General Shipping Update disabled to enabled."."\n" ;
            }else if($newData['general_shipping']['update'] == 0){
                $string .= "Settings Module  - General Shipping Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['general_shipping']['delete']!= $newData['general_shipping']['delete']){
            if($newData['general_shipping']['delete'] == 1)
            {
                $string .= "Settings Module - General Shipping Delete disabled to enabled."."\n" ;
            }else if($newData['general_shipping']['delete'] == 0){
                $string .= "Settings Module  - General Shipping Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['general_shipping']['disable']!= $newData['general_shipping']['disable']){
            if($newData['general_shipping']['disable'] == 1)
            {
                $string .= "Settings Module - General Shipping Disable disabled to enabled."."\n" ;
            }else if($newData['general_shipping']['disable'] == 0){
                $string .= "Settings Module  - General Shipping Disable enabled to disabled."."\n" ;
            }
        }

         //Shipping and Delivery
        //Custom Shipping
        if($prevData['custom_shipping']['view']!= $newData['custom_shipping']['view']){
            if($newData['custom_shipping']['view'] == 1)
            {
                $string .= "Settings Module - Custom Shipping View disabled to enabled."."\n" ;
            }else if($newData['custom_shipping']['view'] == 0){
                $string .= "Settings Module  - Custom Shipping View enabled to disabled."."\n" ;
            }
        }

        if($prevData['custom_shipping']['create']!= $newData['custom_shipping']['create']){
            if($newData['custom_shipping']['create'] == 1)
            {
                $string .= "Settings Module - Custom Shipping Create disabled to enabled."."\n" ;
            }else if($newData['custom_shipping']['create'] == 0){
                $string .= "Settings Module  - Custom Shipping Create enabled to disabled."."\n" ;
            }
        }
        
        if($prevData['custom_shipping']['update']!= $newData['custom_shipping']['update']){
            if($newData['custom_shipping']['update'] == 1)
            {
                $string .= "Settings Module - Custom Shipping Update disabled to enabled."."\n" ;
            }else if($newData['custom_shipping']['update'] == 0){
                $string .= "Settings Module  - Custom Shipping Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['custom_shipping']['delete']!= $newData['custom_shipping']['delete']){
            if($newData['custom_shipping']['delete'] == 1)
            {
                $string .= "Settings Module - Custom Shipping Delete disabled to enabled."."\n" ;
            }else if($newData['custom_shipping']['delete'] == 0){
                $string .= "Settings Module  - Custom Shipping Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['custom_shipping']['disable']!= $newData['custom_shipping']['disable']){
            if($newData['custom_shipping']['disable'] == 1)
            {
                $string .= "Settings Module - Custom Shipping Disable disabled to enabled."."\n" ;
            }else if($newData['custom_shipping']['disable'] == 0){
                $string .= "Settings Module  - Custom Shipping Disable enabled to disabled."."\n" ;
            }
        }

          //Product Categories
        if($prevData['category']['view']!= $newData['category']['view']){
            if($newData['category']['view'] == 1)
            {
                $string .= "Settings Module - Product Categories View disabled to enabled."."\n" ;
            }else if($newData['category']['view'] == 0){
                $string .= "Settings Module  - Product Categories View enabled to disabled."."\n" ;
            }
        }

        if($prevData['category']['create']!= $newData['category']['create']){
            if($newData['category']['create'] == 1)
            {
                $string .= "Settings Module - Product Categories Create disabled to enabled."."\n" ;
            }else if($newData['category']['create'] == 0){
                $string .= "Settings Module  - Product Categories Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['category']['update']!= $newData['category']['update']){
            if($newData['category']['update'] == 1)
            {
                $string .= "Settings Module - Product Categories Update disabled to enabled."."\n" ;
            }else if($newData['category']['update'] == 0){
                $string .= "Settings Module  - Product Categories Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['category']['delete']!= $newData['category']['delete']){
            if($newData['category']['delete'] == 1)
            {
                $string .= "Settings Module - Product Categories Delete disabled to enabled."."\n" ;
            }else if($newData['category']['delete'] == 0){
                $string .= "Settings Module  - Product Categories Delete enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['category']['disable']!= $newData['category']['disable']){
            if($newData['category']['disable'] == 1)
            {
                $string .= "Settings Module - Product Categories Disable disabled to enabled."."\n" ;
            }else if($newData['category']['disable'] == 0){
                $string .= "Settings Module  - Product Categories Disable enabled to disabled."."\n" ;
            }
        }

          //Product Main Categories
        if($prevData['products_main_category']['view']!= $newData['products_main_category']['view']){
            if($newData['products_main_category']['view'] == 1)
            {
                $string .= "Settings Module - Product Categories View disabled to enabled."."\n" ;
            }else if($newData['products_main_category']['view'] == 0){
                $string .= "Settings Module  - Product Categories View enabled to disabled."."\n" ;
            }
        }

        if($prevData['products_main_category']['create']!= $newData['products_main_category']['create']){
            if($newData['products_main_category']['create'] == 1)
            {
                $string .= "Settings Module - Product Categories Create disabled to enabled."."\n" ;
            }else if($newData['products_main_category']['create'] == 0){
                $string .= "Settings Module  - Product Categories Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['products_main_category']['update']!= $newData['products_main_category']['update']){
            if($newData['products_main_category']['update'] == 1)
            {
                $string .= "Settings Module - Product Categories Update disabled to enabled."."\n" ;
            }else if($newData['products_main_category']['update'] == 0){
                $string .= "Settings Module  - Product Categories Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['products_main_category']['delete']!= $newData['products_main_category']['delete']){
            if($newData['products_main_category']['delete'] == 1)
            {
                $string .= "Settings Module - Product Categories Delete disabled to enabled."."\n" ;
            }else if($newData['products_main_category']['delete'] == 0){
                $string .= "Settings Module  - Product Categories Delete enabled to disabled."."\n" ;
            }
        }

        
        if($prevData['products_main_category']['disable']!= $newData['products_main_category']['disable']){
            if($newData['products_main_category']['disable'] == 1)
            {
                $string .= "Settings Module - Product Categories Disable disabled to enabled."."\n" ;
            }else if($newData['products_main_category']['disable'] == 0){
                $string .= "Settings Module  - Product Categories Disable enabled to disabled."."\n" ;
            }
        }

        //Shipping Partners
        if($prevData['shipping_partners']['view']!= $newData['shipping_partners']['view']){
            if($newData['shipping_partners']['view'] == 1)
            {
                $string .= "Settings Module - Shipping Partners View disabled to enabled."."\n" ;
            }else if($newData['shipping_partners']['view'] == 0){
                $string .= "Settings Module  - Shipping Partners View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shipping_partners']['create']!= $newData['shipping_partners']['create']){
            if($newData['shipping_partners']['create'] == 1)
            {
                $string .= "Settings Module - Shipping Partners Create disabled to enabled."."\n" ;
            }else if($newData['shipping_partners']['create'] == 0){
                $string .= "Settings Module  - Shipping Partners Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['shipping_partners']['update']!= $newData['shipping_partners']['update']){
            if($newData['shipping_partners']['update'] == 1)
            {
                $string .= "Settings Module - Shipping Partners Update disabled to enabled."."\n" ;
            }else if($newData['shipping_partners']['update'] == 0){
                $string .= "Settings Module  - Shipping Partners Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['shipping_partners']['delete']!= $newData['shipping_partners']['delete']){
            if($newData['shipping_partners']['delete'] == 1)
            {
                $string .= "Settings Module - Shipping Partners Delete disabled to enabled."."\n" ;
            }else if($newData['shipping_partners']['delete'] == 0){
                $string .= "Settings Module  - Shipping Partners Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['shipping_partners']['disable']!= $newData['shipping_partners']['disable']){
            if($newData['shipping_partners']['disable'] == 1)
            {
                $string .= "Settings Module - Shipping Partners Disable disabled to enabled."."\n" ;
            }else if($newData['shipping_partners']['disable'] == 0){
                $string .= "Settings Module  - Shipping Partners Disable enabled to disabled."."\n" ;
            }
        }

          //Shop Banners
        if($prevData['shop_banners']['view']!= $newData['shop_banners']['view']){
            if($newData['shop_banners']['view'] == 1)
            {
                $string .= "Settings Module - Shop Banners View disabled to enabled."."\n" ;
            }else if($newData['shop_banners']['view'] == 0){
                $string .= "Settings Module  - Shop Banners View enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_banners']['create']!= $newData['shop_banners']['create']){
            if($newData['shop_banners']['create'] == 1)
            {
                $string .= "Settings Module - Shop Banners Create disabled to enabled."."\n" ;
            }else if($newData['shop_banners']['create'] == 0){
                $string .= "Settings Module  - Shop Banners Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_banners']['update']!= $newData['shop_banners']['update']){
            if($newData['shop_banners']['update'] == 1)
            {
                $string .= "Settings Module - Shop Banners Update disabled to enabled."."\n" ;
            }else if($newData['shop_banners']['update'] == 0){
                $string .= "Settings Module  - Shop Banners Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_banners']['delete']!= $newData['shop_banners']['delete']){
            if($newData['shop_banners']['delete'] == 1)
            {
                $string .= "Settings Module - Shop Banners Delete disabled to enabled."."\n" ;
            }else if($newData['shop_banners']['delete'] == 0){
                $string .= "Settings Module  - Shop Banners Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['shop_banners']['disable']!= $newData['shop_banners']['disable']){
            if($newData['shop_banners']['disable'] == 1)
            {
                $string .= "Settings Module - Shop Banners Disable disabled to enabled."."\n" ;
            }else if($newData['shop_banners']['disable'] == 0){
                $string .= "Settings Module  - Shop Banners Disable enabled to disabled."."\n" ;
            }
        }

        //Users
        if($prevData['users']['view']!= $newData['users']['view']){
            if($newData['users']['view'] == 1)
            {
                $string .= "Settings Module - Users View disabled to enabled."."\n" ;
            }else if($newData['users']['view'] == 0){
                $string .= "Settings Module  - Users View enabled to disabled."."\n" ;
            }
        }

        if($prevData['users']['create']!= $newData['users']['create']){
            if($newData['users']['create'] == 1)
            {
                $string .= "Settings Module - Users Create disabled to enabled."."\n" ;
            }else if($newData['users']['create'] == 0){
                $string .= "Settings Module  - Users Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['users']['update']!= $newData['users']['update']){
            if($newData['users']['update'] == 1)
            {
                $string .= "Settings Module - Users Update disabled to enabled."."\n" ;
            }else if($newData['users']['update'] == 0){
                $string .= "Settings Module  - Users Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['users']['delete']!= $newData['users']['delete']){
            if($newData['users']['delete'] == 1)
            {
                $string .= "Settings Module - Users Delete disabled to enabled."."\n" ;
            }else if($newData['users']['delete'] == 0){
                $string .= "Settings Module  - Users Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['users']['disable']!= $newData['users']['disable']){
            if($newData['users']['disable'] == 1)
            {
                $string .= "Settings Module - Users Disable disabled to enabled."."\n" ;
            }else if($newData['users']['disable'] == 0){
                $string .= "Settings Module  - Users Disable enabled to disabled."."\n" ;
            }
        }

        //Void Record
        if($prevData['void_record']['process']!= $newData['void_record']['process']){
            if($newData['void_record']['process'] == 1)
            {
                $string .= "Settings Module - Void Record Process disabled to enabled."."\n" ;
            }else if($newData['void_record']['process'] == 0){
                $string .= "Settings Module  - Void Record Process enabled to disabled."."\n" ;
            }
        }

        //Void Record List
        if($prevData['void_record_list']['view']!= $newData['void_record_list']['view']){
            if($newData['void_record_list']['view'] == 1)
            {
                $string .= "Settings Module - Void Record List View disabled to enabled."."\n" ;
            }else if($newData['void_record_list']['view'] == 0){
                $string .= "Settings Module  - Void Record List View enabled to disabled."."\n" ;
            }
        }


          //Reports
        //Average Order Value Report
        if($prevData['aov']['view']!= $newData['aov']['view']){
            if($newData['aov']['view'] == 1)
            {
                $string .= "Reports Module - Average Order Value Report View disabled to enabled."."\n" ;
            }else if($newData['aov']['view'] == 0){
                $string .= "Reports Module  - Average Order Value Report View enabled to disabled."."\n" ;
            }
        }

        //Branch Performance Report
        if($prevData['bpr']['view']!= $newData['bpr']['view']){
            if($newData['bpr']['view'] == 1)
            {
                $string .= "Reports Module - Branch Performance Report View disabled to enabled."."\n" ;
            }else if($newData['bpr']['view'] == 0){
                $string .= "Reports Module  - Branch Performance Report View enabled to disabled."."\n" ;
            }
        }

        //Inventory List
        if($prevData['invlist']['view']!= $newData['invlist']['view']){
            if($newData['invlist']['view'] == 1)
            {
                $string .= "Reports Module - Inventory List View disabled to enabled."."\n" ;
            }else if($newData['invlist']['view'] == 0){
                $string .= "Reports Module  - Inventory List View enabled to disabled."."\n" ;
            }
        }

         //Inventory Report
         if($prevData['inv']['view']!= $newData['inv']['view']){
            if($newData['inv']['view'] == 1)
            {
                $string .= "Reports Module - Inventory Report View disabled to enabled."."\n" ;
            }else if($newData['inv']['view'] == 0){
                $string .= "Reports Module  - Inventory Report View enabled to disabled."."\n" ;
            }
        }

        //Online Store Conversion Rate
        if($prevData['oscrr']['view']!= $newData['oscrr']['view']){
            if($newData['oscrr']['view'] == 1)
            {
                $string .= "Reports Module - Online Store Conversion Rate View disabled to enabled."."\n" ;
            }else if($newData['oscrr']['view'] == 0){
                $string .= "Reports Module  - Online Store Conversion Rate View enabled to disabled."."\n" ;
            }
        }

         //Online Store Sessions
        if($prevData['ps']['view']!= $newData['ps']['view']){
            if($newData['ps']['view'] == 1)
            {
                $string .= "Reports Module - Online Store Sessions View disabled to enabled."."\n" ;
            }else if($newData['ps']['view'] == 0){
                $string .= "Reports Module  - Online Store Sessions Rate View enabled to disabled."."\n" ;
            }
        }

        //Inventory Ending Report
        if($prevData['invend']['view']!= $newData['invend']['view']){
            if($newData['invend']['view'] == 1)
            {
                $string .= "Reports Module - Inventory Ending Report View disabled to enabled."."\n" ;
            }else if($newData['invend']['view'] == 0){
                $string .= "Reports Module  - Inventory Ending Report View enabled to disabled."."\n" ;
            }
        }

        //Order and Sales Report
        if($prevData['os']['view']!= $newData['os']['view']){
            if($newData['os']['view'] == 1)
            {
                $string .= "Reports Module -  Order and Sales Report View disabled to enabled."."\n" ;
            }else if($newData['os']['view'] == 0){
                $string .= "Reports Module  - Order and Sales Report View enabled to disabled."."\n" ;
            }
        }

        //Order and Sales Report
        if($prevData['os']['view']!= $newData['os']['view']){
            if($newData['os']['view'] == 1)
            {
                $string .= "Reports Module -  Order and Sales Report View disabled to enabled."."\n" ;
            }else if($newData['os']['view'] == 0){
                $string .= "Reports Module  - Order and Sales Report View enabled to disabled."."\n" ;
            }
        }

        //Orders By Location
        if($prevData['oblr']['view']!= $newData['oblr']['view']){
            if($newData['oblr']['view'] == 1)
            {
                $string .= "Reports Module -  Orders By Location View disabled to enabled."."\n" ;
            }else if($newData['oblr']['view'] == 0){
                $string .= "Reports Module  - Orders By Location View enabled to disabled."."\n" ;
            }
        }

        //Pending Orders
        if($prevData['po']['view']!= $newData['po']['view']){
            if($newData['po']['view'] == 1)
            {
                $string .= "Reports Module -  Pending Orders View disabled to enabled."."\n" ;
            }else if($newData['po']['view'] == 0){
                $string .= "Reports Module  - Pending Orders View enabled to disabled."."\n" ;
            }
        }

         //Product Releasing Report
         if($prevData['prr']['view']!= $newData['prr']['view']){
            if($newData['prr']['view'] == 1)
            {
                $string .= "Reports Module -  Product Releasing Report View disabled to enabled."."\n" ;
            }else if($newData['prr']['view'] == 0){
                $string .= "Reports Module  - Product Releasing Report View enabled to disabled."."\n" ;
            }
        }

        //Order Report
        if($prevData['or']['view']!= $newData['or']['view']){
            if($newData['or']['view'] == 1)
            {
                $string .= "Reports Module -  Order Report View disabled to enabled."."\n" ;
            }else if($newData['or']['view'] == 0){
                $string .= "Reports Module  - Order Report View enabled to disabled."."\n" ;
            }
        }

         //Refund Order Summary
         if($prevData['rosum']['view']!= $newData['rosum']['view']){
            if($newData['rosum']['view'] == 1)
            {
                $string .= "Reports Module - Refund Order Summary View disabled to enabled."."\n" ;
            }else if($newData['rosum']['view'] == 0){
                $string .= "Reports Module  - Refund Order Summary View enabled to disabled."."\n" ;
            }
        }

        //Refund Order Status
        if($prevData['rostat']['view']!= $newData['rostat']['view']){
            if($newData['rostat']['view'] == 1)
            {
                $string .= "Reports Module - Refund Order Status View disabled to enabled."."\n" ;
            }else if($newData['rostat']['view'] == 0){
                $string .= "Reports Module  - Refund Order Status View enabled to disabled."."\n" ;
            }
        }

         //Revenue by Branch
         if($prevData['rbbr']['view']!= $newData['rostat']['view']){
            if($newData['rbbr']['view'] == 1)
            {
                $string .= "Reports Module - Revenue by Branch View disabled to enabled."."\n" ;
            }else if($newData['rbbr']['view'] == 0){
                $string .= "Reports Module  - Revenue by Branch View enabled to disabled."."\n" ;
            }
        }

        //Revenue By Location
        if($prevData['rbl']['view']!= $newData['rbl']['view']){
            if($newData['rbl']['view'] == 1)
            {
                $string .= "Reports Module - Revenue By Location View disabled to enabled."."\n" ;
            }else if($newData['rbl']['view'] == 0){
                $string .= "Reports Module  - Revenue By Location View enabled to disabled."."\n" ;
            }
        }

        //Revenue by Store
        if($prevData['rbsr']['view']!= $newData['rbsr']['view']){
            if($newData['rbsr']['view'] == 1)
            {
                $string .= "Reports Module - Revenue by Store View disabled to enabled."."\n" ;
            }else if($newData['rbsr']['view'] == 0){
                $string .= "Reports Module  - Revenue by Store View enabled to disabled."."\n" ;
            }
        }

        //toktok Booking Report
        if($prevData['tbr']['view']!= $newData['tbr']['view']){
            if($newData['tbr']['view'] == 1)
            {
                $string .= "Reports Module - toktok Booking Report View disabled to enabled."."\n" ;
            }else if($newData['tbr']['view'] == 0){
                $string .= "Reports Module  - toktok Booking Report View enabled to disabled."."\n" ;
            }
        }


          //Order List Payout Status Report
        if($prevData['olps']['view']!= $newData['olps']['view']){
            if($newData['olps']['view'] == 1)
            {
                $string .= "Reports Module - Order List Payout Status Report View disabled to enabled."."\n" ;
            }else if($newData['olps']['view'] == 0){
                $string .= "Reports Module  - Order List Payout Status Report View enabled to disabled."."\n" ;
            }
        }

          //Top Products Sold Report
          if($prevData['tps']['view']!= $newData['tps']['view']){
            if($newData['tps']['view'] == 1)
            {
                $string .= "Reports Module - Top Products Sold Report View disabled to enabled."."\n" ;
            }else if($newData['tps']['view'] == 0){
                $string .= "Reports Module  - Top Products Sold Report View enabled to disabled."."\n" ;
            }
        }

         //Total Abandoned Carts Report
         if($prevData['tacr']['view']!= $newData['tacr']['view']){
            if($newData['tacr']['view'] == 1)
            {
                $string .= "Reports Module - Total Abandoned Carts Report View disabled to enabled."."\n" ;
            }else if($newData['tacr']['view'] == 0){
                $string .= "Reports Module  - Total Abandoned Carts Report View enabled to disabled."."\n" ;
            }
        }


         //Total Orders Report
         if($prevData['to']['view']!= $newData['to']['view']){
            if($newData['to']['view'] == 1)
            {
                $string .= "Reports Module - Total Orders Report View disabled to enabled."."\n" ;
            }else if($newData['to']['view'] == 0){
                $string .= "Reports Module  - Total Orders Report View enabled to disabled."."\n" ;
            }
        }

        //Total Sales Report
        if($prevData['tsr']['view']!= $newData['tsr']['view']){
            if($newData['tsr']['view'] == 1)
            {
                $string .= "Reports Module - Total Sales Report View disabled to enabled."."\n" ;
            }else if($newData['tsr']['view'] == 0){
                $string .= "Reports Module  - Total Sales Report View enabled to disabled."."\n" ;
            }
        }
 
        //With holding Tax Report
        if($prevData['wtr']['view']!= $newData['wtr']['view']){
            if($newData['wtr']['view'] == 1)
            {
                $string .= "Reports Module - With holding Tax Report View disabled to enabled."."\n" ;
            }else if($newData['wtr']['view'] == 0){
                $string .= "Reports Module  - With holding Tax Report View enabled to disabled."."\n" ;
            }
        }


        //Merchant Serviceable Areas Report
        if($prevData['msr']['view']!= $newData['msr']['view']){
            if($newData['msr']['view'] == 1)
            {
                $string .= "Reports Module - Merchant Serviceable Areas Report View disabled to enabled."."\n" ;
            }else if($newData['wtr']['view'] == 0){
                $string .= "Reports Module  - Merchant Serviceable Areas Report View enabled to disabled."."\n" ;
            }
        }



         //Accounts
         //Billing Settlement
         if($prevData['billing']['view']!= $newData['billing']['view']){
            if($newData['billing']['view'] == 1)
            {
                $string .= "Accounts Module - Billing Settlement View disabled to enabled."."\n" ;
            }else if($newData['billing']['view'] == 0){
                $string .= "Accounts Module  - Billing Settlement View enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing']['create']!= $newData['billing']['create']){
            if($newData['billing']['create'] == 1)
            {
                $string .= "Accounts Module - Billing Settlement Create disabled to enabled."."\n" ;
            }else if($newData['billing']['create'] == 0){
                $string .= "Accounts Module  - Billing Settlement Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing']['update']!= $newData['billing']['update']){
            if($newData['billing']['update'] == 1)
            {
                $string .= "Accounts Module - Billing Settlement Update disabled to enabled."."\n" ;
            }else if($newData['billing']['update'] == 0){
                $string .= "Accounts Module  - Billing Settlement Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing']['delete']!= $newData['billing']['delete']){
            if($newData['billing']['delete'] == 1)
            {
                $string .= "Accounts Module - Billing Settlement Delete disabled to enabled."."\n" ;
            }else if($newData['billing']['delete'] == 0){
                $string .= "Accounts Module  - Billing Settlement Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing']['disable']!= $newData['billing']['disable']){
            if($newData['billing']['disable'] == 1)
            {
                $string .= "Accounts Module - Billing Settlement Disable disabled to enabled."."\n" ;
            }else if($newData['billing']['disable'] == 0){
                $string .= "Accounts Module  - Billing Settlement Disable enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing']['admin_view']!= $newData['billing']['admin_view']){
            if($newData['billing']['admin_view'] == 1)
            {
                $string .= "Accounts Module - Billing Settlement Admin View disabled to enabled."."\n" ;
            }else if($newData['billing']['admin_view'] == 0){
                $string .= "Accounts Module  - Billing Settlement Admin View enabled to disabled."."\n" ;
            }
        }

        //Billing (By Payment Portal Fee)
        if($prevData['billing_portal_fee']['view']!= $newData['billing_portal_fee']['view']){
            if($newData['billing_portal_fee']['view'] == 1)
            {
                $string .= "Accounts Module - Billing (By Payment Portal Fee) View disabled to enabled."."\n" ;
            }else if($newData['billing_portal_fee']['view'] == 0){
                $string .= "Accounts Module  - Billing (By Payment Portal Fee) View enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing_portal_fee']['create']!= $newData['billing_portal_fee']['create']){
            if($newData['billing_portal_fee']['create'] == 1)
            {
                $string .= "Accounts Module - Billing (By Payment Portal Fee) Create disabled to enabled."."\n" ;
            }else if($newData['billing_portal_fee']['create'] == 0){
                $string .= "Accounts Module  - Billing (By Payment Portal Fee) Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing_portal_fee']['update']!= $newData['billing_portal_fee']['update']){
            if($newData['billing_portal_fee']['update'] == 1)
            {
                $string .= "Accounts Module - Billing (By Payment Portal Fee) Update disabled to enabled."."\n" ;
            }else if($newData['billing_portal_fee']['update'] == 0){
                $string .= "Accounts Module  - Billing (By Payment Portal Fee) Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing_portal_fee']['delete']!= $newData['billing_portal_fee']['delete']){
            if($newData['billing_portal_fee']['delete'] == 1)
            {
                $string .= "Accounts Module - Billing (By Payment Portal Fee) Delete disabled to enabled."."\n" ;
            }else if($newData['billing_portal_fee']['delete'] == 0){
                $string .= "Accounts Module  - Billing (By Payment Portal Fee) Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['billing_portal_fee']['disable']!= $newData['billing_portal_fee']['disable']){
            if($newData['billing_portal_fee']['disable'] == 1)
            {
                $string .= "Accounts Module - Billing (By Payment Portal Fee) Disable disabled to enabled."."\n" ;
            }else if($newData['billing_portal_fee']['disable'] == 0){
                $string .= "Accounts Module  - Billing (By Payment Portal Fee) Disable enabled to disabled."."\n" ;
            }
        }

         //Customers
         //Customer List
         if($prevData['customer']['view']!= $newData['customer']['view']){
            if($newData['customer']['view'] == 1)
            {
                $string .= "Customers Module - Customer List View disabled to enabled."."\n" ;
            }else if($newData['customer']['view'] == 0){
                $string .= "Customers Module  - Customer List View enabled to disabled."."\n" ;
            }
        }

        if($prevData['customer']['create']!= $newData['customer']['create']){
            if($newData['customer']['create'] == 1)
            {
                $string .= "Customers Module - Customer List Create disabled to enabled."."\n" ;
            }else if($newData['customer']['create'] == 0){
                $string .= "Customers Module  - Customer List Create enabled to disabled."."\n" ;
            }
        }


        //Products
         //Products
         if($prevData['products']['view']!= $newData['products']['view']){
            if($newData['products']['view'] == 1)
            {
                $string .= "Products Module - Products View disabled to enabled."."\n" ;
            }else if($newData['products']['view'] == 0){
                $string .= "Products Module  - Products View enabled to disabled."."\n" ;
            }
        }

        if($prevData['products']['create']!= $newData['products']['create']){
            if($newData['products']['create'] == 1)
            {
                $string .= "Products Module - Products Create disabled to enabled."."\n" ;
            }else if($newData['products']['create'] == 0){
                $string .= "Products Module  - Products Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['products']['update']!= $newData['products']['update']){
            if($newData['products']['update'] == 1)
            {
                $string .= "Products Module - Products Update disabled to enabled."."\n" ;
            }else if($newData['products']['update'] == 0){
                $string .= "Products Module  - Products Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['products']['delete']!= $newData['products']['delete']){
            if($newData['products']['delete'] == 1)
            {
                $string .= "Products Module - Products Delete disabled to enabled."."\n" ;
            }else if($newData['products']['delete'] == 0){
                $string .= "Products Module  - Products Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['products']['disable']!= $newData['products']['disable']){
            if($newData['products']['disable'] == 1)
            {
                $string .= "Products Module - Products Disable disabled to enabled."."\n" ;
            }else if($newData['products']['disable'] == 0){
                $string .= "Products Module  - Products Disable enabled to disabled."."\n" ;
            }
        }

         //Products waiting for approval 
         if($prevData['products_wfa']['view']!= $newData['products_wfa']['view']){
            if($newData['products_wfa']['view'] == 1)
            {
                $string .= "Products Module - Products Waiting for approval View disabled to enabled."."\n" ;
            }else if($newData['products_wfa']['view'] == 0){
                $string .= "Products Module  - Products Waiting for approval View enabled to disabled."."\n" ;
            }
        }

        
         if($prevData['products_wfa']['approved']!= $newData['products_wfa']['approved']){
            if($newData['products_wfa']['approved'] == 1)
            {
                $string .= "Products Module - Products Waiting for approval Approved disabled to enabled."."\n" ;
            }else if($newData['products_wfa']['approved'] == 0){
                $string .= "Products Module  - Products Waiting for approval Approved enabled to disabled."."\n" ;
            }
        }


        if($prevData['products_wfa']['declined']!= $newData['products_wfa']['declined']){
            if($newData['products_wfa']['declined'] == 1)
            {
                $string .= "Products Module - Products Waiting for approval Declined disabled to enabled."."\n" ;
            }else if($newData['products_wfa']['declined'] == 0){
                $string .= "Products Module  - Products Waiting for approval Declined enabled to disabled."."\n" ;
            }
        }


        //Products approve
        if($prevData['products_apr']['view']!= $newData['products_apr']['view']){
            if($newData['products_apr']['view'] == 1)
            {
                $string .= "Products Module - Products Approve View disabled to enabled."."\n" ;
            }else if($newData['products_apr']['view'] == 0){
                $string .= "Products Module  - Products Approve View enabled to disabled."."\n" ;
            }
        }

        
         if($prevData['products_apr']['approved']!= $newData['products_apr']['approved']){
            if($newData['products_apr']['approved'] == 1)
            {
                $string .= "Products Module - Products Approve Approved disabled to enabled."."\n" ;
            }else if($newData['products_apr']['approved'] == 0){
                $string .= "Products Module  - Products Approve Approved enabled to disabled."."\n" ;
            }
        }


        if($prevData['products_apr']['declined']!= $newData['products_apr']['declined']){
            if($newData['products_apr']['declined'] == 1)
            {
                $string .= "Products Module - Products Approve  Declined disabled to enabled."."\n" ;
            }else if($newData['products_apr']['declined'] == 0){
                $string .= "Products Module  - Products Approve  Declined enabled to disabled."."\n" ;
            }
        }


         //Products Decline
         if($prevData['products_dec']['view']!= $newData['products_dec']['view']){
            if($newData['products_dec']['view'] == 1)
            {
                $string .= "Products Module - Products Decline View disabled to enabled."."\n" ;
            }else if($newData['products_dec']['view'] == 0){
                $string .= "Products Module  - Products Decline View enabled to disabled."."\n" ;
            }
        }

        
         if($prevData['products_dec']['approved']!= $newData['products_dec']['approved']){
            if($newData['products_dec']['approved'] == 1)
            {
                $string .= "Products Module - Products Decline Approved disabled to enabled."."\n" ;
            }else if($newData['products_dec']['approved'] == 0){
                $string .= "Products Module  - Products Decline Approved enabled to disabled."."\n" ;
            }
        }


          //Products verify
          if($prevData['products_verified']['view']!= $newData['products_verified']['view']){
            if($newData['products_verified']['view'] == 1)
            {
                $string .= "Products Module - Products Verify View disabled to enabled."."\n" ;
            }else if($newData['products_verified']['view'] == 0){
                $string .= "Products Module  - Products Verify View enabled to disabled."."\n" ;
            }
        }



         
         //Orders
         //Order List
         if($prevData['transactions']['view']!= $newData['transactions']['view']){
            if($newData['transactions']['view'] == 1)
            {
                $string .= "Orders Module - Order List View disabled to enabled."."\n" ;
            }else if($newData['transactions']['view'] == 0){
                $string .= "Orders Module  - Order List View enabled to disabled."."\n" ;
            }
        }

        if($prevData['transactions']['update']!= $newData['transactions']['update']){
            if($newData['transactions']['update'] == 1)
            {
                $string .= "Orders Module - Order List Update disabled to enabled."."\n" ;
            }else if($newData['transactions']['update'] == 0){
                $string .= "Orders Module  - Order List Update enabled to disabled."."\n" ;
            }
        }

         //Order List Reassign
        if($prevData['transactions']['reassign']!= $newData['transactions']['reassign']){
            if($newData['transactions']['reassign'] == 1)
            {
                $string .= "Orders Module - Order List Reassign disabled to enabled."."\n" ;
            }else if($newData['transactions']['reassign'] == 0){
                $string .= "Orders Module  - Order List Reassign enabled to disabled."."\n" ;
            }
        }

          //Order List Mark as Paid
        if($prevData['transactions']['mark_as_paid']!= $newData['transactions']['mark_as_paid']){
            if($newData['transactions']['mark_as_paid'] == 1)
            {
                $string .= "Orders Module - Order List Mark as Paid disabled to enabled."."\n" ;
            }else if($newData['transactions']['mark_as_paid'] == 0){
                $string .= "Orders Module  - Order List Mark as Paid enabled to disabled."."\n" ;
            }
        }

        //Order List  Process Order
        if($prevData['transactions']['process_order']!= $newData['transactions']['process_order']){
            if($newData['transactions']['process_order'] == 1)
            {
                $string .= "Orders Module - Order List Process Order disabled to enabled."."\n" ;
            }else if($newData['transactions']['process_order'] == 0){
                $string .= "Orders Module  - Order List Process Order enabled to disabled."."\n" ;
            }
        }


          //Order List Ready Pick-up
        if($prevData['transactions']['ready_pickup']!= $newData['transactions']['ready_pickup']){
            if($newData['transactions']['ready_pickup'] == 1)
            {
                $string .= "Orders Module - Order List  Ready Pick-up disabled to enabled."."\n" ;
            }else if($newData['transactions']['ready_pickup'] == 0){
                $string .= "Orders Module  - Order List  Ready Pick-up enabled to disabled."."\n" ;
            }
        }

        
          //Order List Confirm Booking
        if($prevData['transactions']['booking_confirmed']!= $newData['transactions']['booking_confirmed']){
            if($newData['transactions']['booking_confirmed'] == 1)
            {
                $string .= "Orders Module - Order List  Confirm Booking disabled to enabled."."\n" ;
            }else if($newData['transactions']['booking_confirmed'] == 0){
                $string .= "Orders Module  - Order List  Confirm Booking enabled to disabled."."\n" ;
            }
        }


        //Order List Mark as Fulfilled
        if($prevData['transactions']['mark_fulfilled']!= $newData['transactions']['mark_fulfilled']){
            if($newData['transactions']['mark_fulfilled'] == 1)
            {
                $string .= "Orders Module - Order List Mark as Fulfilled disabled to enabled."."\n" ;
            }else if($newData['transactions']['mark_fulfilled'] == 0){
                $string .= "Orders Module  - Order List  Mark as Fulfilled enabled to disabled."."\n" ;
            }
        }

        //Order List Return To Sender
        if($prevData['transactions']['returntosender']!= $newData['transactions']['returntosender']){
            if($newData['transactions']['returntosender'] == 1)
            {
                $string .= "Orders Module - Order List Return To Sender disabled to enabled."."\n" ;
            }else if($newData['transactions']['returntosender'] == 0){
                $string .= "Orders Module  - Order List Return To Sender enabled to disabled."."\n" ;
            }
        }

         //Order List Return To Sender
         if($prevData['transactions']['returntosender']!= $newData['transactions']['returntosender']){
            if($newData['transactions']['returntosender'] == 1)
            {
                $string .= "Orders Module - Order List Return To Sender disabled to enabled."."\n" ;
            }else if($newData['transactions']['returntosender'] == 0){
                $string .= "Orders Module  - Order List Return To Sender enabled to disabled."."\n" ;
            }
        }


         //Order List Re-Deliver Order
         if($prevData['transactions']['redeliver']!= $newData['transactions']['redeliver']){
            if($newData['transactions']['redeliver'] == 1)
            {
                $string .= "Orders Module - Order List Re-Deliver Order disabled to enabled."."\n" ;
            }else if($newData['transactions']['redeliver'] == 0){
                $string .= "Orders Module  - Order List Re-Deliver Order enabled to disabled."."\n" ;
            }
        }

         //Order List  Mark as Shipped
         if($prevData['transactions']['shipped']!= $newData['transactions']['shipped']){
            if($newData['transactions']['shipped'] == 1)
            {
                $string .= "Orders Module - Order List  Mark as Shipped disabled to enabled."."\n" ;
            }else if($newData['transactions']['shipped'] == 0){
                $string .= "Orders Module  - Order List  Mark as Shipped enabled to disabled."."\n" ;
            }
        }

         //Order List  Delivery Confirmed
         if($prevData['transactions']['confirmed']!= $newData['transactions']['confirmed']){
            if($newData['transactions']['confirmed'] == 1)
            {
                $string .= "Orders Module - Order List  Delivery Confirmed disabled to enabled."."\n" ;
            }else if($newData['transactions']['confirmed'] == 0){
                $string .= "Orders Module  - Order List Delivery Confirmed enabled to disabled."."\n" ;
            }
        }

         //Order List Merchant Order List
         if($prevData['transactions']['merchant_orderList']!= $newData['transactions']['merchant_orderList']){
            if($newData['transactions']['merchant_orderList'] == 1)
            {
                $string .= "Orders Module - Order List  Merchant Order List disabled to enabled."."\n" ;
            }else if($newData['transactions']['merchant_orderList'] == 0){
                $string .= "Orders Module  - Order List Merchant Order List enabled to disabled."."\n" ;
            }
        }

        //Pending Order List
        if($prevData['pending_orders']['view']!= $newData['pending_orders']['view']){
            if($newData['pending_orders']['view'] == 1)
            {
                $string .= "Orders Module - Pending Order List View disabled to enabled."."\n" ;
            }else if($newData['pending_orders']['view'] == 0){
                $string .= "Orders Module  - Pending Order List View enabled to disabled."."\n" ;
            }
        }

        //Paid Order List
        if($prevData['paid_orders']['view']!= $newData['paid_orders']['view']){
            if($newData['paid_orders']['view'] == 1)
            {
                $string .= "Orders Module - Paid Order List View disabled to enabled."."\n" ;
            }else if($newData['paid_orders']['view'] == 0){
                $string .= "Orders Module  - Paid Order List View enabled to disabled."."\n" ;
            }
        }

        //Ready For Processing Order List
        if($prevData['readyforprocessing_orders']['view']!= $newData['readyforprocessing_orders']['view']){
            if($newData['readyforprocessing_orders']['view'] == 1)
            {
                $string .= "Orders Module - Ready For Processing Order List View disabled to enabled."."\n" ;
            }else if($newData['readyforprocessing_orders']['view'] == 0){
                $string .= "Orders Module  - Ready For Processing Order List View enabled to disabled."."\n" ;
            }
        }

        //Processing Order List
        if($prevData['processing_orders']['view']!= $newData['processing_orders']['view']){
            if($newData['processing_orders']['view'] == 1)
            {
                $string .= "Orders Module - Processing Order List  View disabled to enabled."."\n" ;
            }else if($newData['processing_orders']['view'] == 0){
                $string .= "Orders Module  - Processing Order List  View enabled to disabled."."\n" ;
            }
        }

        //Ready For Pickup Order List
        if($prevData['readyforpickup_orders']['view']!= $newData['readyforpickup_orders']['view']){
            if($newData['readyforpickup_orders']['view'] == 1)
            {
                $string .= "Orders Module - Ready For Pickup Order List  View disabled to enabled."."\n" ;
            }else if($newData['readyforpickup_orders']['view'] == 0){
                $string .= "Orders Module  - Ready For Pickup Order List  View enabled to disabled."."\n" ;
            }
        }

        //Booking Confirmed Order List
        if($prevData['bookingconfirmed_orders']['view']!= $newData['bookingconfirmed_orders']['view']){
            if($newData['bookingconfirmed_orders']['view'] == 1)
            {
                $string .= "Orders Module - Booking Confirmed Order List View disabled to enabled."."\n" ;
            }else if($newData['bookingconfirmed_orders']['view'] == 0){
                $string .= "Orders Module  - Booking Confirmed Order List View enabled to disabled."."\n" ;
            }
        }

        //Fulfilled Order List
        if($prevData['fulfilled_orders']['view']!= $newData['fulfilled_orders']['view']){
            if($newData['fulfilled_orders']['view'] == 1)
            {
                $string .= "Orders Module - Fulfilled Order List View disabled to enabled."."\n" ;
            }else if($newData['fulfilled_orders']['view'] == 0){
                $string .= "Orders Module  - Fulfilled Order List View enabled to disabled."."\n" ;
            }
        }

          //Shipped Order List
        if($prevData['shipped_orders']['view']!= $newData['shipped_orders']['view']){
            if($newData['shipped_orders']['view'] == 1)
            {
                $string .= "Orders Module - Shipped Order List View disabled to enabled."."\n" ;
            }else if($newData['shipped_orders']['view'] == 0){
                $string .= "Orders Module  - Shipped Order List View enabled to disabled."."\n" ;
            }
        }

          //Return To Sender List
        if($prevData['returntosender_orders']['view']!= $newData['returntosender_orders']['view']){
            if($newData['returntosender_orders']['view'] == 1)
            {
                $string .= "Orders Module - Return To Sender List View disabled to enabled."."\n" ;
            }else if($newData['returntosender_orders']['view'] == 0){
                $string .= "Orders Module  - Return To Sender List View enabled to disabled."."\n" ;
            }
        }

        //Voided Order List
        if($prevData['voided_orders']['view']!= $newData['voided_orders']['view']){
            if($newData['voided_orders']['view'] == 1)
            {
                $string .= "Orders Module - Voided Order List View disabled to enabled."."\n" ;
            }else if($newData['voided_orders']['view'] == 0){
                $string .= "Orders Module  - Voided Order List View enabled to disabled."."\n" ;
            }
        }

          //Manual Orders
        if($prevData['manualorder_list']['view']!= $newData['manualorder_list']['view']){
            if($newData['manualorder_list']['view'] == 1)
            {
                $string .= "Orders Module - Manual Orders View disabled to enabled."."\n" ;
            }else if($newData['manualorder_list']['view'] == 0){
                $string .= "Orders Module  - Manual Orders View enabled to disabled."."\n" ;
            }
        }


        if($prevData['manualorder_list']['create']!= $newData['manualorder_list']['create']){
            if($newData['manualorder_list']['create'] == 1)
            {
                $string .= "Orders Module - Manual Orders Create disabled to enabled."."\n" ;
            }else if($newData['manualorder_list']['create'] == 0){
                $string .= "Orders Module  - Manual Orders Create enabled to disabled."."\n" ;
            }
        }

          //Refund Order
        if($prevData['refund_order']['create']!= $newData['refund_order']['create']){
            if($newData['refund_order']['create'] == 1)
            {
                $string .= "Orders Module - Refund Order Create disabled to enabled."."\n" ;
            }else if($newData['refund_order']['create'] == 0){
                $string .= "Orders Module  - Refund Order Create enabled to disabled."."\n" ;
            }
        }

         //Refund Order
         if($prevData['refund_order']['create']!= $newData['refund_order']['create']){
            if($newData['refund_order']['create'] == 1)
            {
                $string .= "Orders Module - Refund Order Create disabled to enabled."."\n" ;
            }else if($newData['refund_order']['create'] == 0){
                $string .= "Orders Module  - Refund Order Create enabled to disabled."."\n" ;
            }
        }

        //Refund Order Approval
        if($prevData['refund_order_approval']['view']!= $newData['refund_order_approval']['view']){
            if($newData['refund_order_approval']['view'] == 1)
            {
                $string .= "Orders Module - Refund Order Approval View disabled to enabled."."\n" ;
            }else if($newData['refund_order_approval']['view'] == 0){
                $string .= "Orders Module  - Refund Order Approval View enabled to disabled."."\n" ;
            }
        }

        if($prevData['refund_order_approval']['update']!= $newData['refund_order_approval']['update']){
            if($newData['refund_order_approval']['update'] == 1)
            {
                $string .= "Orders Module - Refund Order Approval Update disabled to enabled."."\n" ;
            }else if($newData['refund_order_approval']['update'] == 0){
                $string .= "Orders Module  - Refund Order Approval Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['refund_order_approval']['approve']!= $newData['refund_order_approval']['approve']){
            if($newData['refund_order_approval']['approve'] == 1)
            {
                $string .= "Orders Module - Refund Order Approval Approve disabled to enabled."."\n" ;
            }else if($newData['refund_order_approval']['approve'] == 0){
                $string .= "Orders Module  - Refund Order Approval Approve enabled to disabled."."\n" ;
            }
        }

        if($prevData['refund_order_approval']['reject']!= $newData['refund_order_approval']['reject']){
            if($newData['refund_order_approval']['reject'] == 1)
            {
                $string .= "Orders Module - Refund Order Approval Reject disabled to enabled."."\n" ;
            }else if($newData['refund_order_approval']['reject'] == 0){
                $string .= "Orders Module  - Refund Order Approval Reject enabled to disabled."."\n" ;
            }
        }


        //Refund Order Transactions
        if($prevData['refund_order_trans']['view']!= $newData['refund_order_trans']['view']){
            if($newData['refund_order_trans']['view'] == 1)
            {
                $string .= "Orders Module - Refund Order Transactions View disabled to enabled."."\n" ;
            }else if($newData['refund_order_trans']['view'] == 0){
                $string .= "Orders Module  - Refund Order Transactions View enabled to disabled."."\n" ;
            }
        }

        //For Pick up Order List
        if($prevData['forpickup_orders']['view']!= $newData['forpickup_orders']['view']){
            if($newData['forpickup_orders']['view'] == 1)
            {
                $string .= "Orders Module - For Pick up Order List View disabled to enabled."."\n" ;
            }else if($newData['forpickup_orders']['view'] == 0){
                $string .= "Orders Module  - For Pick up Order List View enabled to disabled."."\n" ;
            }
        }

        //Delivery Confirmed Order List
        if($prevData['confirmed_orders']['view']!= $newData['confirmed_orders']['view']){
            if($newData['confirmed_orders']['view'] == 1)
            {
                $string .= "Orders Module - Delivery Confirmed Order List View disabled to enabled."."\n" ;
            }else if($newData['confirmed_orders']['view'] == 0){
                $string .= "Orders Module  - Delivery Confirmed Order List enabled to disabled."."\n" ;
            }
        }


         //Dashboard
         //Dashboard View
         if($prevData['dashboard']['view']!= $newData['dashboard']['view']){
            if($newData['dashboard']['view'] == 1)
            {
                $string .= "Dashboard Module - Dashboard View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['view'] == 0){
                $string .= "Dashboard Module  - Dashboard View enabled to disabled."."\n" ;
            }
        }

         //Sales (Count)
         if($prevData['dashboard']['sales_count_view']!= $newData['dashboard']['sales_count_view']){
            if($newData['dashboard']['sales_count_view'] == 1)
            {
                $string .= "Dashboard Module - Sales (Count) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['sales_count_view'] == 0){
                $string .= "Dashboard Module  - Sales (Count) View enabled to disabled."."\n" ;
            }
        }

        //Transactions (Count)
        if($prevData['dashboard']['transactions_count_view']!= $newData['dashboard']['transactions_count_view']){
            if($newData['dashboard']['transactions_count_view'] == 1)
            {
                $string .= "Dashboard Module - Transactions (Count) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['transactions_count_view'] == 0){
                $string .= "Dashboard Module  - Transactions (Count) View enabled to disabled."."\n" ;
            }
        }

          //Views (Count)
          if($prevData['dashboard']['views_count_view']!= $newData['dashboard']['views_count_view']){
            if($newData['dashboard']['views_count_view'] == 1)
            {
                $string .= "Dashboard Module - Views (Count) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['views_count_view'] == 0){
                $string .= "Dashboard Module  - Views (Count) View enabled to disabled."."\n" ;
            }
        }

         //Overall Sales (Count)
         if($prevData['dashboard']['overall_sales_count_view']!= $newData['dashboard']['overall_sales_count_view']){
            if($newData['dashboard']['overall_sales_count_view'] == 1)
            {
                $string .= "Dashboard Module - Overall Sales (Count) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['overall_sales_count_view'] == 0){
                $string .= "Dashboard Module  - Overall Sales (Count) View enabled to disabled."."\n" ;
            }
        }

        // Visitors (Chart)
        if($prevData['dashboard']['visitors_chart_view']!= $newData['dashboard']['visitors_chart_view']){
            if($newData['dashboard']['visitors_chart_view'] == 1)
            {
                $string .= "Dashboard Module -  Visitors (Chart) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['visitors_chart_view'] == 0){
                $string .= "Dashboard Module  -  Visitors (Chart) View enabled to disabled."."\n" ;
            }
       }

         // Views (Chart)
         if($prevData['dashboard']['views_chart_view']!= $newData['dashboard']['views_chart_view']){
            if($newData['dashboard']['views_chart_view'] == 1)
            {
                $string .= "Dashboard Module -  Views (Chart) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['views_chart_view'] == 0){
                $string .= "Dashboard Module  -  Views (Chart) View enabled to disabled."."\n" ;
            }
       }

         // Sales (Chart)
         if($prevData['dashboard']['sales_chart_view']!= $newData['dashboard']['sales_chart_view']){
            if($newData['dashboard']['sales_chart_view'] == 1)
            {
                $string .= "Dashboard Module - Sales (Chart) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['sales_chart_view'] == 0){
                $string .= "Dashboard Module  - Sales (Chart) View enabled to disabled."."\n" ;
            }
        }

         // Sales (Chart)
         if($prevData['dashboard']['sales_chart_view']!= $newData['dashboard']['sales_chart_view']){
            if($newData['dashboard']['sales_chart_view'] == 1)
            {
                $string .= "Dashboard Module - Sales (Chart) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['sales_chart_view'] == 0){
                $string .= "Dashboard Module  - Sales (Chart) View enabled to disabled."."\n" ;
            }
        }

        //  Top 10 Products Sold (List)
        if($prevData['dashboard']['top10productsold_list_view']!= $newData['dashboard']['top10productsold_list_view']){
            if($newData['dashboard']['top10productsold_list_view'] == 1)
            {
                $string .= "Dashboard Module -  Top 10 Products Sold (List) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['top10productsold_list_view'] == 0){
                $string .= "Dashboard Module  -  Top 10 Products Sold (List) View enabled to disabled."."\n" ;
            }
        }

          //   Transactions (Chart)
          if($prevData['dashboard']['transactions_chart_view']!= $newData['dashboard']['transactions_chart_view']){
            if($newData['dashboard']['transactions_chart_view'] == 1)
            {
                $string .= "Dashboard Module - Transactions (Chart) View disabled to enabled."."\n" ;
            }else if($newData['dashboard']['transactions_chart_view'] == 0){
                $string .= "Dashboard Module  - Transactions (Chart) View enabled to disabled."."\n" ;
            }
        }


        //Promotion
        //Product promotion
        if($prevData['product_promotion']['view']!= $newData['product_promotion']['view']){
            if($newData['product_promotion']['view'] == 1)
            {
                $string .= "Promotion Module - Product Promotion View disabled to enabled."."\n" ;
            }else if($newData['product_promotion']['view'] == 0){
                $string .= "Promotion Module  - Product Promotion View enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_promotion']['create']!= $newData['product_promotion']['create']){
            if($newData['product_promotion']['create'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Create disabled to enabled."."\n" ;
            }else if($newData['product_promotion']['create'] == 0){
                $string .= "Promotion Module  - Product Promotion Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_promotion']['update']!= $newData['product_promotion']['update']){
            if($newData['product_promotion']['update'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Update disabled to enabled."."\n" ;
            }else if($newData['product_promotion']['update'] == 0){
                $string .= "Promotion Module  - Product Promotion Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_promotion']['delete']!= $newData['product_promotion']['delete']){
            if($newData['product_promotion']['delete'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Delete disabled to enabled."."\n" ;
            }else if($newData['product_promotion']['delete'] == 0){
                $string .= "Promotion Module  - Product Promotion Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['product_promotion']['disable']!= $newData['product_promotion']['disable']){
            if($newData['product_promotion']['disable'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Disable disabled to enabled."."\n" ;
            }else if($newData['product_promotion']['disable'] == 0){
                $string .= "Promotion Module  - Product Promotion Disable enabled to disabled."."\n" ;
            }
        }
        //Mystery Pouch
        if($prevData['mystery_coupon']['view']!= $newData['mystery_coupon']['view']){
            if($newData['mystery_coupon']['view'] == 1)
            {
                $string .= "Mystery Coupon - Product Promotion View disabled to enabled."."\n" ;
            }else if($newData['mystery_coupon']['view'] == 0){
                $string .= "Promotion Module  - Product Promotion View enabled to disabled."."\n" ;
            }
        }

        if($prevData['mystery_coupon']['create']!= $newData['mystery_coupon']['create']){
            if($newData['mystery_coupon']['create'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Create disabled to enabled."."\n" ;
            }else if($newData['mystery_coupon']['create'] == 0){
                $string .= "Promotion Module  - Product Promotion Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['mystery_coupon']['update']!= $newData['mystery_coupon']['update']){
            if($newData['mystery_coupon']['update'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Update disabled to enabled."."\n" ;
            }else if($newData['mystery_coupon']['update'] == 0){
                $string .= "Promotion Module  - Product Promotion Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['mystery_coupon']['delete']!= $newData['mystery_coupon']['delete']){
            if($newData['mystery_coupon']['delete'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Delete disabled to enabled."."\n" ;
            }else if($newData['mystery_coupon']['delete'] == 0){
                $string .= "Promotion Module  - Product Promotion Delete enabled to disabled."."\n" ;
            }
        }

        if($prevData['mystery_coupon']['disable']!= $newData['mystery_coupon']['disable']){
            if($newData['mystery_coupon']['disable'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Disable disabled to enabled."."\n" ;
            }else if($newData['mystery_coupon']['disable'] == 0){
                $string .= "Promotion Module  - Product Promotion Disable enabled to disabled."."\n" ;
            }
        }
        //Shipping Fee Discount
        if($prevData['sf_discount']['view']!= $newData['sf_discount']['view']){
            if($newData['sf_discount']['view'] == 1)
            {
                $string .= "Promotion Module - Product Promotion View disabled to enabled."."\n" ;
            }else if($newData['sf_discount']['view'] == 0){
                $string .= "Promotion Module  - Product Promotion View enabled to disabled."."\n" ;
            }
        }

        if($prevData['sf_discount']['create']!= $newData['sf_discount']['create']){
            if($newData['sf_discount']['create'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Create disabled to enabled."."\n" ;
            }else if($newData['sf_discount']['create'] == 0){
                $string .= "Promotion Module  - Product Promotion Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['sf_discount']['update']!= $newData['sf_discount']['update']){
            if($newData['sf_discount']['update'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Update disabled to enabled."."\n" ;
            }else if($newData['sf_discount']['update'] == 0){
                $string .= "Promotion Module  - Product Promotion Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['sf_discount']['delete']!= $newData['sf_discount']['delete']){
            if($newData['sf_discount']['delete'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Delete disabled to enabled."."\n" ;
            }else if($newData['sf_discount']['delete'] == 0){
                $string .= "Promotion Module  - Product Promotion Delete enabled to disabled."."\n" ;
            }
        }
        //Campaign Type
        if($prevData['campaign_type']['view']!= $newData['campaign_type']['view']){
            if($newData['campaign_type']['view'] == 1)
            {
                $string .= "Promotion Module - Product Promotion View disabled to enabled."."\n" ;
            }else if($newData['campaign_type']['view'] == 0){
                $string .= "Promotion Module  - Product Promotion View enabled to disabled."."\n" ;
            }
        }

        if($prevData['campaign_type']['create']!= $newData['campaign_type']['create']){
            if($newData['campaign_type']['create'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Create disabled to enabled."."\n" ;
            }else if($newData['campaign_type']['create'] == 0){
                $string .= "Promotion Module  - Product Promotion Create enabled to disabled."."\n" ;
            }
        }

        if($prevData['campaign_type']['update']!= $newData['campaign_type']['update']){
            if($newData['campaign_type']['update'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Update disabled to enabled."."\n" ;
            }else if($newData['campaign_type']['update'] == 0){
                $string .= "Promotion Module  - Product Promotion Update enabled to disabled."."\n" ;
            }
        }

        if($prevData['campaign_type']['delete']!= $newData['campaign_type']['delete']){
            if($newData['campaign_type']['delete'] == 1)
            {
                $string .= "Promotion Module - Product Promotion Delete disabled to enabled."."\n" ;
            }else if($newData['campaign_type']['delete'] == 0){
                $string .= "Promotion Module  - Product Promotion Delete enabled to disabled."."\n" ;
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