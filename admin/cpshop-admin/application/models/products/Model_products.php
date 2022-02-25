<?php 
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) { //to ignore maximum time limit
    @set_time_limit(0);
}

class Model_products extends CI_Model {

	# Start - Products

    public function get_sys_shop($user_id){
		$sql=" SELECT sys_shop FROM app_members WHERE sys_user = ? AND status = 1";
		$sql = $this->db->query($sql, $user_id); 

        if($sql->num_rows() > 0){
            return $sql->row()->sys_shop;
        }else{
            return "";
        }
	}
	
	public function get_sys_branch_profile($shop_id, $product_id, $branchid){
		
		if($branchid == 0){
			$sql=" SELECT b.*, c.no_of_stocks as inv_qty FROM sys_branch_mainshop AS a
			LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
			LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
			WHERE a.mainshopid = ? AND a.status = 1
			GROUP BY b.id";
			$params = array($product_id, $shop_id);
		}else{
			$sql="SELECT b.*, c.no_of_stocks as inv_qty FROM sys_branch_mainshop AS a
			LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
			LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
			WHERE a.mainshopid = ? AND a.branchid = ? AND a.status = 1
			GROUP BY b.id";
			$params = array($product_id, $shop_id, $branchid);
			
		}

		$sql = $this->db->query($sql, $params); 

        if($sql->num_rows() > 0){
            return $sql->result_array();
        }else{
            return false;
        }
	}

	public function get_uptodate_nostocks($shop_id, $product_id){
		
		$sql=" SELECT SUM(c.no_of_stocks) as total_inv_qty FROM sys_branch_mainshop AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id AND b.status = 1
		LEFT JOIN sys_products_invtrans_branch AS c ON b.id = c.branchid AND c.status = 1 AND c.product_id = ?
		WHERE a.mainshopid = ? AND a.status = 1
		GROUP BY b.id";
		$params = array($product_id, $shop_id);

		$sql = $this->db->query($sql, $params); 

        if($sql->num_rows() > 0){
            return $sql->result_array();
        }else{
            return false;
        }
	}

	public function get_uptodate_nostocks_main($product_id){
		
		$sql=" SELECT SUM(no_of_stocks) as total_inv_qty FROM sys_products_invtrans_branch
		WHERE branchid = 0 AND product_id = ? AND status = 1";
		$params = array($product_id);

		$sql = $this->db->query($sql, $params); 

        if($sql->num_rows() > 0){
            return $sql->row();
        }else{
            return false;
        }
	}

	public function get_variants_nostocks($product_id){
		
		$sql="SELECT SUM(no_of_stocks) FROM sys_products
              WHERE parent_product_id = ? AND enabled > 0";
		$params = array($product_id);

		$sql = $this->db->query($sql, $params); 

        if($sql->num_rows() > 0){
            return $sql->row();
        }else{
            return false;
        }
	}
	
    public function get_shopcode($user_id){
		$sql=" SELECT b.shopcode as shopcode FROM app_members a 
			    LEFT JOIN sys_shops b ON a.sys_shop = b.id
				WHERE a.sys_user = ? AND a.status = 1";
		$sql = $this->db->query($sql, $user_id); 

        if($sql->num_rows() > 0){
            return $sql->row()->shopcode;
        }else{
            return "";
        }
    }

    public function get_shopcode_via_shopid($id) {
		$query="SELECT * FROM sys_shops WHERE id = ? AND status > 0";
		$params = array($id);

		return $this->db->query($query, $params)->row()->shopcode;
	}

    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

    public function get_category_options() {
		$query="SELECT * FROM sys_product_category WHERE status = 1";
		return $this->db->query($query)->result_array();
	}

	public function get_productdetails($Id) {
		$query=" SELECT a.*, c.weight, c.uom_id, c.shipping_isset, d.shopcode, d.shopname, e.no_of_stocks as inv_qty, f.startup as refstartup, f.jc as refjc, f.mcjr as refmcjr, f.mc as refmc, f.mcsuper as refmcsuper, f.mcmega as refmcmega, f.others as refothers,
		c.length, c.width, c.height 
		FROM sys_products AS a 
		LEFT JOIN sys_products_shipping AS c ON a.Id = c.product_id AND c.enabled = 1
		LEFT JOIN sys_shops AS d ON a.sys_shop = d.id 
		LEFT JOIN sys_products_invtrans_branch AS e ON a.sys_shop = e.shopid AND e.status = 1 AND e.branchid = 0  AND e.product_id = ?
		LEFT JOIN 8_referralcom_rate AS f ON a.Id = f.product_id AND f.status = 1
		WHERE a.Id = ? AND a.enabled > 0;";
		
		$params = array($Id, $Id);
		return $this->db->query($query, $params)->row_array();
	}

	public function getVariants($product_id) {
		$query  = "SELECT * FROM sys_products WHERE enabled > 0 AND  parent_product_id = ?";
		$params = array($product_id);
		return $this->db->query($query, $params)->result_array();
	}

	public function getVariantsOption($product_id) {
		$query  = "SELECT * FROM sys_products_variantsummary WHERE parent_product_id = ? AND status = 1 AND (variant_type <> '' OR variant_type IS NOT NULL AND variant_list <> '' OR variant_list IS NOT NULL)";
		$params = array($product_id);
		return $this->db->query($query, $params);
	}

	public function checkProductStatus($Id) {
		$query=" SELECT enabled FROM sys_products WHERE Id = ?";
		
		$params = array($Id);
		return $this->db->query($query, $params)->row();
	}

	public function get_prev_product($itemname) {
		$query="SELECT Id FROM sys_products WHERE itemname < ? AND enabled = 1 ORDER BY itemname DESC LIMIT 1";
		
		$params = array($itemname);

		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	
	}

	public function get_prev_product_per_shop($itemname, $sys_shop) {
		$query="SELECT Id FROM sys_products WHERE itemname < ? AND enabled = 1 AND sys_shop = ? ORDER BY itemname DESC LIMIT 1";
		
		$params = array($itemname, $sys_shop);

		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	
	}

	public function get_next_product($itemname) {
		$query="SELECT Id FROM sys_products WHERE itemname > ? AND enabled = 1 ORDER BY itemname LIMIT 1";
		
		$params = array($itemname);
		
		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	}

	public function get_next_product_per_shop($itemname, $sys_shop) {
		$query="SELECT Id FROM sys_products WHERE itemname > ? AND enabled = 1 AND sys_shop = ? ORDER BY itemname LIMIT 1";
		
		$params = array($itemname, $sys_shop);
		
		if(!empty($this->db->query($query, $params)->row()->Id)){
			return $this->db->query($query, $params)->row()->Id;
		}else{
			return 0;
		}
	}


	public function check_products_itemid($itemid, $sys_shop){

		if(ini() == 'jconlineshop'){
			$sql=" SELECT * FROM sys_products WHERE sys_shop = ? AND itemid = ? AND enabled > 0";
			$params = array($sys_shop, $itemid);
		}
		else{
			$sql=" SELECT * FROM sys_products WHERE itemid = ? AND enabled > 0";
			$params = array($itemid);
		}
		
        return $this->db->query($sql, $params);
	}

	public function check_referralcom_rate($shopcode, $itemid){
		$sql=" SELECT * FROM 8_referralcom_rate WHERE itemid = ? AND status = 1";
		$params = array($shopcode.'_'.$itemid);
       
        return $this->db->query($sql, $params);
        
	}
	
	public function check_products_id($id){
		$sql=" SELECT * FROM sys_products WHERE Id = ? AND enabled > 0";
		$params = array($id);
       
        return $this->db->query($sql, $params);
        
	}
	
	public function check_products($id){
		$sql=" SELECT a.*, c.weight, c.uom_id, c.shipping_isset, d.shopcode, d.shopname, e.no_of_stocks as inv_qty, f.startup as refstartup, f.jc as refjc, f.mcjr as refmcjr, f.mc as refmc, f.mcsuper as refmcsuper, f.mcmega as refmcmega, f.others as refothers  
		FROM sys_products AS a
		LEFT JOIN sys_products_shipping AS c ON a.Id = c.product_id AND c.enabled = 1
		LEFT JOIN sys_shops AS d ON a.sys_shop = d.id 
		LEFT JOIN sys_products_invtrans_branch AS e ON a.sys_shop = e.shopid AND e.status = 1 AND e.branchid = 0  AND e.product_id = ?
		LEFT JOIN 8_referralcom_rate AS f ON a.Id = f.product_id AND f.status = 1
		WHERE a.Id = ?";
		$params = array($id, $id);
       
        return $this->db->query($sql, $params);
        
    }
    
    public function disable_modal_confirm($disable_id, $record_status){
		$sql = "UPDATE `sys_products` SET `enabled` = ?, `featured_prod_isset` = '0', `set_product_arrangement` = '0' WHERE `Id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}
	
	public function delete_modal_confirm($delete_id){

		$sql = "UPDATE sys_products_promotion SET `status` = '0' WHERE product_id = ?";
		$bind_data = array(
			$delete_id
		);	

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE `sys_products` SET `enabled` = '0', `featured_prod_isset` = '0', `set_product_arrangement` = '0' WHERE `Id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function save_product($args, $f_id, $imgArr,$featured_product,$featured_product_arrangment) {

		$branchid = $this->session->userdata('branchid');
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2]         = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3] 		   = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4] 		   = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5]         = (!empty($imgArr[5])) ? $imgArr[5] : '';

		$args['f_startup'] = ($args['f_startup'] != '') ? $args['f_startup'] : 0;
		$args['f_startup'] =  $args['f_startup'] / 100;
		$args['f_jc']      = ($args['f_jc'] != '') ? $args['f_jc'] : 0;
		$args['f_jc']      =  $args['f_jc'] / 100;
		$args['f_mcjr']    = ($args['f_mcjr'] != '') ? $args['f_mcjr'] : 0;
		$args['f_mcjr']    =  $args['f_mcjr'] / 100;
		$args['f_mc']      = ($args['f_mc'] != '') ? $args['f_mc'] : 0;
		$args['f_mc']      =  $args['f_mc'] / 100;
		$args['f_mcsuper'] = ($args['f_mcsuper'] != '') ? $args['f_mcsuper'] : 0;
		$args['f_mcsuper'] =  $args['f_mcsuper'] / 100;
		$args['f_mcmega']  = ($args['f_mcmega'] != '') ? $args['f_mcmega'] : 0;
		$args['f_mcmega']  =  $args['f_mcmega'] / 100;
		$args['f_others']  = ($args['f_others'] != '') ? $args['f_others'] : 0;
		$args['f_others']  =  $args['f_others'] / 100;

		if(ini() == 'toktokmall'){
			$args['f_disc_rate'] = ($args['f_disc_rate'] != '') ? $args['f_disc_rate'] : 0;
			$args['f_disc_rate'] =  $args['f_disc_rate'] / 100;
			$args['f_disc_ratetype'] = "p";
		}

		$args['f_status']  = ($args['f_itemid'] == '') ? 2 : $args['f_status'];

		$sql = "INSERT INTO sys_products (`Id`, `sys_shop`, `cat_id`,`itemid`, `itemname`, `otherinfo`, `uom`, `price`, `compare_at_price`, `tags`, `inv_sku`, `inv_barcode`, `tq_isset`, `cont_selling_isset`, `max_qty_isset`, `max_qty`, `admin_isset`, `disc_ratetype`, `disc_rate`, `summary`, `arrangement`, `age_restriction_isset`, `img_1`, `img_2`, `img_3`, `img_4`, `img_5`, `img_6`, `enabled`, `date_created`, `date_updated`, `featured_prod_isset`, `variant_isset`, `set_product_arrangement`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";

		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}
		
		$bind_data = array(
			$f_id,
			$args['f_member_shop'],
			$args['f_category'],
			$args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$args['f_uom'],
			$args['f_price'],
			$args['f_compare_at_price'],
			$args['f_tags'],
			$args['f_inv_sku'],
			$args['f_inv_barcode'],
			$args['f_tq_isset'],
			$args['f_cont_selling_isset'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_admin_isset'],
			$args['f_disc_ratetype'],
			$args['f_disc_rate'],
			$args['f_summary'],
			$args['f_arrangement'],
			$args['f_age_restriction_isset'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			ini()=='jcww'?1:$args['f_status'],
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			$featured_product,
			$args['f_variants_isset'],
			$featured_product_arrangment
		);

		$this->db->query($sql, $bind_data);

		foreach($imgArr as $key => $value){
			
			if($value != ""){
				$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
				$bind_data = array(
					$f_id,
					$key+1,
					$value,
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
			}
		}

		////save image filename


		///for promo price tracking
		$sql = "INSERT INTO sys_products_promo (`product_id`, `promo_price`, `date_created`,`enabled`) VALUES (?,?,?,?) ";
		$bind_data = array(
			$f_id,
			$args['f_compare_at_price'],
			date('Y-m-d H:i:s'),
			1
		);

		/// referralcommrate
		$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$args['f_itemid'],
			$f_id,
			ini(),
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			1
		);

		$this->db->query($sql, $bind_data);

		///for inventory shipping weight
		$sql = "INSERT INTO sys_products_shipping (`product_id`, `weight`, `uom_id`, `shipping_isset`, `length`, `width`, `height`, `date_created`, `enabled`) VALUES (?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$f_id,
			$args['f_weight'],
			$args['f_uom'],
			$args['f_shipping_isset'],
			$args['f_length'],
			$args['f_width'],
			$args['f_height'],
			date('Y-m-d H:i:s'),
			1
		);

		$this->db->query($sql, $bind_data);


		$sql = "INSERT INTO sys_product_status (`product_id`, `status`, `user_id`, `created`, `updated`,`itemid`,`instance_id`,`disc_rate`,`startup`,`jc`,`mcjr`,`mc`,`mcsuper`,`mcmega`,`others`,`price`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$f_id,
	        3,
			$this->session->userdata('id'),
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			$args['f_itemid'],
			ini(),
			$args['f_disc_rate'],
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			$args['f_price'],
		);

		$this->db->query($sql, $bind_data);

		 
		/// product inventory validation per branch
		if($branchid == 0){
			$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
			$bind_data = array(
				$args['f_member_shop'],
				$f_id
			);

			$branch_invtrans = $this->db->query($sql, $bind_data);

			if($branch_invtrans->num_rows() > 0){
				$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
				$bind_data = array(
					$args['f_no_of_stocks_0'],
					$args['f_member_shop'],
					$f_id

				);	

				$this->db->query($sql, $bind_data);
			}else{
				$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
				$bind_data = array(
					$args['f_member_shop'],
					0,
					$f_id,
					$args['f_no_of_stocks_0'],
					date('Y-m-d H:i:s'),
					1
				);	

				$this->db->query($sql, $bind_data);
			}

			$sql = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = 0 AND enabled = 1";
			$bind_data = array(
				$f_id
			);

			$invtrans = $this->db->query($sql, $bind_data);

			if($invtrans->row()->qty_count_stocks > 0){
				$total_qty = 0;
				$total_qty = $args['f_no_of_stocks_0'] - $invtrans->row()->qty_count_stocks;

				if($total_qty != 0){
					$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
					$bind_data = array(
						0,
						$f_id,
						$total_qty,
						'Add_products_admin',
						$this->session->userdata('username'),
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}else{
					$this->db->query($sql, $bind_data);
				}
			}else{
				if($args['f_no_of_stocks_0'] != 0){
					$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
					$bind_data = array(
						0,
						$f_id,
						$args['f_no_of_stocks_0'],
						'Add_products_admin',
						$this->session->userdata('username'),
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}else{
					$this->db->query($sql, $bind_data);
				}
				
			}
		}
		
		$branchdetails = $this->get_sys_branch_profile($args['f_member_shop'], $f_id, $branchid);

		if($branchdetails != false){
			foreach($branchdetails as $val){
				if(isset($args['f_no_of_stocks_'.$val['id']])){
					$branch_no_of_stocks = 'f_no_of_stocks_'.$val['id'];
					$this->update_branch_invtrans($args['f_member_shop'], $val['id'], $args[$branch_no_of_stocks], $f_id, 'Add_products_admin');
				}
			}
		}

		$sql = "SELECT SUM(quantity) as grand_total_no_of_stocks FROM sys_products_invtrans WHERE product_id = ? AND enabled = 1";
		$bind_data = array(
			$f_id
		);

		$grand_total_no_of_stocks = $this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			$grand_total_no_of_stocks->row()->grand_total_no_of_stocks,
			$f_id
		);
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET img_1 = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$f_id,
			$f_id
		);

		return $this->db->query($sql, $bind_data);
	}

	public function save_variant($args, $f_id, $imgArr,$featured_product,$featured_product_arrangment, $delivery_areas_str) {

		$branchid          = $this->session->userdata('branchid');
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2] 		   = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3] 	   	   = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4] 		   = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5] 		   = (!empty($imgArr[5])) ? $imgArr[5] : '';

		$args['f_startup'] = ($args['f_startup'] != '') ? $args['f_startup'] : 0;
		$args['f_startup'] =  $args['f_startup'] / 100;
		$args['f_jc']      = ($args['f_jc'] != '') ? $args['f_jc'] : 0;
		$args['f_jc']      =  $args['f_jc'] / 100;
		$args['f_mcjr']    = ($args['f_mcjr'] != '') ? $args['f_mcjr'] : 0;
		$args['f_mcjr']    =  $args['f_mcjr'] / 100;
		$args['f_mc']      = ($args['f_mc'] != '') ? $args['f_mc'] : 0;
		$args['f_mc']      =  $args['f_mc'] / 100;
		$args['f_mcsuper'] = ($args['f_mcsuper'] != '') ? $args['f_mcsuper'] : 0;
		$args['f_mcsuper'] =  $args['f_mcsuper'] / 100;
		$args['f_mcmega']  = ($args['f_mcmega'] != '') ? $args['f_mcmega'] : 0;
		$args['f_mcmega']  =  $args['f_mcmega'] / 100;
		$args['f_others']  = ($args['f_others'] != '') ? $args['f_others'] : 0;
		$args['f_others']  =  $args['f_others'] / 100;
		$args['f_status']  = ($args['f_itemid'] == '') ? 2 : $args['f_status'];

		//delivery areas condition. only set to jcww 
		if(ini() == 'jcww'){
			$str_insert = ", `delivery_areas`";
			$str_value  = ", '".$delivery_areas_str."'";
		}
		else{
			$str_insert = "";
			$str_value  = "";
		}
		/// merchant comm rate, only set to toktokmall
		if(ini() == 'toktokmall'){
			$args['f_disc_rate'] = ($args['f_disc_rate'] != '') ? $args['f_disc_rate'] : 0;
			$args['f_disc_rate'] =  $args['f_disc_rate'] / 100;
			$args['f_disc_ratetype'] = "p";
		}else{
			$args['f_disc_rate'] = 0;
			$args['f_disc_ratetype'] = "p";
		}

		$sql = "INSERT INTO sys_products (`Id`, `sys_shop`, `cat_id`,`itemid`, `itemname`, `otherinfo`, `uom`, `price`, `compare_at_price`, `tags`, `inv_sku`, `inv_barcode`, `tq_isset`, `cont_selling_isset`, `max_qty_isset`, `max_qty`, `admin_isset`, `disc_ratetype`, `disc_rate`, `summary`, `arrangement`, `img_1`, `img_2`, `img_3`, `img_4`, `img_5`, `img_6`, `enabled`, `date_created`, `date_updated`, `featured_prod_isset`, `variant_isset`, `parent_product_id`,`set_product_arrangement`".$str_insert.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?".$str_value.") ";

		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}
		
		$bind_data = array(
			$f_id,
			$args['f_member_shop'],
			$args['f_category'],
			$args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$args['f_uom'],
			$args['f_price'],
			$args['f_compare_at_price'],
			$args['f_tags'],
			$args['f_inv_sku'],
			$args['f_inv_barcode'],
			$args['f_tq_isset'],
			$args['f_cont_selling_isset'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_admin_isset'],
			$args['f_disc_ratetype'],
			$args['f_disc_rate'],
			$args['f_summary'],
			$args['f_arrangement'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			$args['f_status'],
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			$featured_product,
			$args['f_variants_isset'],
			$args['f_parent_product_id'],
			$featured_product_arrangment
		);

		$this->db->query($sql, $bind_data);

		foreach($imgArr as $key => $value){
			
			if($value != ""){
				$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
				$bind_data = array(
					$f_id,
					$key+1,
					$value,
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
			}
		}

		////save image filename


		///for promo price tracking
		$sql = "INSERT INTO sys_products_promo (`product_id`, `promo_price`, `date_created`,`enabled`) VALUES (?,?,?,?) ";
		$bind_data = array(
			$f_id,
			$args['f_compare_at_price'],
			date('Y-m-d H:i:s'),
			1
		);

		/// referralcommrate
		$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$args['f_itemid'],
			$f_id,
			ini(),
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			1
		);

		$this->db->query($sql, $bind_data);

		///for inventory shipping weight
		$sql = "INSERT INTO sys_products_shipping (`product_id`, `weight`, `uom_id`, `shipping_isset`, `length`, `width`, `height`, `date_created`, `enabled`) VALUES (?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$f_id,
			$args['f_weight'],
			$args['f_uom'],
			$args['f_shipping_isset'],
			$args['f_length'],
			$args['f_width'],
			$args['f_height'],
			date('Y-m-d H:i:s'),
			1
		);

		$this->db->query($sql, $bind_data);


		
		$sql = "INSERT INTO sys_product_status (`product_id`, `status`, `user_id`, `created`, `updated`,`itemid`,`instance_id`,`disc_rate`,`startup`,`jc`,`mcjr`,`mc`,`mcsuper`,`mcmega`,`others`,`price`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$f_id,
	        3,
			$this->session->userdata('id'),
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
			$args['f_itemid'],
			ini(),
			$args['f_disc_rate'],
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			$args['f_price'],
		);
		$this->db->query($sql, $bind_data);
		 
		/// product inventory validation per branch
		if($branchid == 0){
			$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
			$bind_data = array(
				$args['f_member_shop'],
				$f_id
			);

			$branch_invtrans = $this->db->query($sql, $bind_data);

			if($branch_invtrans->num_rows() > 0){
				$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
				$bind_data = array(
					$args['f_no_of_stocks_0'],
					$args['f_member_shop'],
					$f_id

				);	

				$this->db->query($sql, $bind_data);
			}else{
				$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
				$bind_data = array(
					$args['f_member_shop'],
					0,
					$f_id,
					$args['f_no_of_stocks_0'],
					date('Y-m-d H:i:s'),
					1
				);	

				$this->db->query($sql, $bind_data);
			}

			$sql = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = 0 AND enabled = 1";
			$bind_data = array(
				$f_id
			);

			$invtrans = $this->db->query($sql, $bind_data);

			if($invtrans->row()->qty_count_stocks > 0){
				$total_qty = 0;
				$total_qty = $args['f_no_of_stocks_0'] - $invtrans->row()->qty_count_stocks;

				if($total_qty != 0){
					$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
					$bind_data = array(
						0,
						$f_id,
						$total_qty,
						'Add_products_admin',
						$this->session->userdata('username'),
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}else{
					$this->db->query($sql, $bind_data);
				}
			}else{
				if($args['f_no_of_stocks_0'] != 0){
					$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
					$bind_data = array(
						0,
						$f_id,
						$args['f_no_of_stocks_0'],
						'Add_products_admin',
						$this->session->userdata('username'),
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}else{
					$this->db->query($sql, $bind_data);
				}
				
			}
		}
		
		$branchdetails = $this->get_sys_branch_profile($args['f_member_shop'], $f_id, $branchid);

		if($branchdetails != false){
			foreach($branchdetails as $val){
				//
				if(isset($args['f_no_of_stocks_'.$val['id']])){
					$branch_no_of_stocks = 'f_no_of_stocks_'.$val['id'];
					$this->update_branch_invtrans($args['f_member_shop'], $val['id'], $args[$branch_no_of_stocks], $f_id, 'Add_products_admin');	
				}
			}
		}


		$sql = "SELECT SUM(quantity) as grand_total_no_of_stocks FROM sys_products_invtrans WHERE product_id = ? AND enabled = 1";
		$bind_data = array(
			$args['f_parent_product_id']
		);

		$grand_parentproduct_stocks = $this->db->query($sql, $bind_data);

		$sql = "SELECT SUM(quantity) as grand_total_no_of_stocks FROM sys_products_invtrans WHERE product_id = ? AND enabled = 1";
		$bind_data = array(
			$f_id
		);
		$grand_total_no_of_stocks = $this->db->query($sql, $bind_data);
		$grand_total_no_of_stocks_=0;
		if($grand_total_no_of_stocks->row()->grand_total_no_of_stocks==''){
			$grand_total_no_of_stocks_ = 0;
		}
		if($grand_parentproduct_stocks->row()->grand_total_no_of_stocks==''){
			$grand_total_no_of_stocks_ = 0;
		}
		
		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			strval($grand_total_no_of_stocks_) + strval($grand_total_no_of_stocks_),
			$f_id
		);
		//end
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET img_1 = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$f_id,
			$f_id
		);

		return $this->db->query($sql, $bind_data);
	}

	public function update_product($args, $id, $save_promo_log, $imgArr,$featured_product,$featured_product_arrangment) {
		$branchid    	   = $this->session->userdata('branchid');
		$get_product 	   = $this->check_products($id)->row_array();
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2]         = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3]         = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4]         = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5]   	   = (!empty($imgArr[5])) ? $imgArr[5] : '';

		$args['f_startup'] = ($args['f_startup'] != '') ? $args['f_startup'] : 0;
		$args['f_startup'] =  $args['f_startup'] / 100;
		$args['f_jc']      = ($args['f_jc'] != '') ? $args['f_jc'] : 0;
		$args['f_jc']      =  $args['f_jc'] / 100;
		$args['f_mcjr']    = ($args['f_mcjr'] != '') ? $args['f_mcjr'] : 0;
		$args['f_mcjr']    =  $args['f_mcjr'] / 100;
		$args['f_mc']      = ($args['f_mc'] != '') ? $args['f_mc'] : 0;
		$args['f_mc']      =  $args['f_mc'] / 100;
		$args['f_mcsuper'] = ($args['f_mcsuper'] != '') ? $args['f_mcsuper'] : 0;
		$args['f_mcsuper'] =  $args['f_mcsuper'] / 100;
		$args['f_mcmega']  = ($args['f_mcmega'] != '') ? $args['f_mcmega'] : 0;
		$args['f_mcmega']  =  $args['f_mcmega'] / 100;
		$args['f_others']  = ($args['f_others'] != '') ? $args['f_others'] : 0;
		$args['f_others']  =  $args['f_others'] / 100;
		$f_status          = ($args['f_itemid'] == '') ? 2 : $get_product['enabled'];

		if(ini() == 'toktokmall'){
			$args['f_disc_rate'] = ($args['f_disc_rate'] != '') ? $args['f_disc_rate'] : 0;
			$args['f_disc_rate'] =  $args['f_disc_rate'] / 100;

			$args['f_disc_ratetype'] = "p";
		}

	
		/// referralcommrate
		$sql = "SELECT a.*, b.`disc_rate`, a.itemID as ProductItemID, b.`featured_prod_isset`, b.price, b.itemid as productItemID2
		        FROM 8_referralcom_rate AS a 
				LEFT JOIN sys_products AS b 
				ON a.`product_id` = b.`Id` 
				WHERE a.product_id = '$id' ";

		$prod_refcomm = $this->db->query($sql);
		$prod_refcom1 =   $prod_refcomm->result_array();

		
		$sql_prodStatus = "SELECT * FROM sys_product_status WHERE product_id =  '$id' ";
		$prod_status  = $this->db->query($sql_prodStatus);


		
	      if($prod_refcom1[0]['price'] != $args['f_price'] || strval($prod_refcom1[0]['ProductItemID']) != strval($args['f_itemid']) || strval($prod_refcom1[0]['startup'])  != strval($args['f_startup'])  || strval($prod_refcom1[0]['jc']) != strval($args['f_jc'])  || strval($prod_refcom1[0]['mcjr']) != strval($args['f_mcjr']) || strval($prod_refcom1[0]['mc']) != strval($args['f_mc']) || strval($prod_refcom1[0]['mcsuper']) != strval($args['f_mcsuper']) || strval($prod_refcom1[0]['mcmega']) != strval($args['f_mcmega']) || strval($prod_refcom1[0]['others']) != strval($args['f_others']) || strval($prod_refcom1[0]['disc_rate']) != strval($args['f_disc_rate'])){


			if(count($prod_refcom1) != 0){

				if($prod_status->num_rows() == 0){
					print_r('if');
					$sql = "INSERT INTO sys_product_status (`itemid`, `product_id`, `instance_id`, `disc_rate`, `startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`,`price`, `status`, `user_id`, `created`, `updated`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
					$bind_data = array(
						$args['f_itemid'],
						$id,
						ini(),
						$args['f_disc_rate'],
						$args['f_startup'],
						$args['f_jc'],
						$args['f_mcjr'],
						$args['f_mc'],
						$args['f_mcsuper'],
						$args['f_mcmega'],
						$args['f_others'],
						$args['f_price'],
						3,
						$this->session->userdata('id'),
						date('Y-m-d H:i:s'),
						date('Y-m-d H:i:s'),
					);	
					$this->db->query($sql, $bind_data);
					

				}else{
				
					$sql = "UPDATE sys_product_status SET `itemid` = ?, `disc_rate` = ?, `startup` = ?, `jc` = ?, `mcjr` = ?, `mc` = ?, `mcsuper` = ?, `mcmega` = ?, `others` = ?, `price` = ?, `user_id` = ?, `updated` = ?, `status` = ?  WHERE `product_id` = ?";
					$bind_data = array(
						$args['f_itemid'],
						$args['f_disc_rate'],
						$args['f_startup'],
						$args['f_jc'],
						$args['f_mcjr'],
						$args['f_mc'],
						$args['f_mcsuper'],
						$args['f_mcmega'],
						$args['f_others'],
						$args['f_price'],
						$this->session->userdata('id'),
						date('Y-m-d H:i:s'),
						3,
						$id,
					);
					$this->db->query($sql, $bind_data);

				
				}
				$string .= $this->audittrail->checkProductChanges_refcommrate($get_product, $args);

			}

		}else{   

					if($prod_refcomm->num_rows() == 0){
						$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
						$bind_data = array(
							$args['f_itemid'],
							$id,
							ini(),
							$args['f_startup'],
							$args['f_jc'],
							$args['f_mcjr'],
							$args['f_mc'],
							$args['f_mcsuper'],
							$args['f_mcmega'],
							$args['f_others'],
							1
						);	

						
						$this->db->query($sql, $bind_data);
					}
					else{
						$sql = "UPDATE 8_referralcom_rate SET itemid = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE product_id = ? AND status = ?";
						$bind_data = array(
							$args['f_itemid'],
							$args['f_startup'],
							$args['f_jc'],
							$args['f_mcjr'],
							$args['f_mc'],
							$args['f_mcsuper'],
							$args['f_mcmega'],
							$args['f_others'],
							$id,
							1
						);


						$this->db->query($sql, $bind_data);
					}


					$string .= $this->audittrail->checkProductChanges_refcommrate($get_product, $args);


		}




		$sql = "UPDATE sys_products SET sys_shop = ?, cat_id = ?, itemid = ?, itemname = ?, otherinfo = ?, uom = ?, compare_at_price = ?,  tags = ?, inv_sku = ?, inv_barcode = ?, tq_isset = ?, cont_selling_isset = ?, max_qty_isset = ?, max_qty = ?, admin_isset = ?, disc_ratetype = ?, summary = ?, arrangement = ?, age_restriction_isset = ?, img_1 = ?, img_2 = ?, img_3 = ?, img_4 = ?, img_5 = ?, img_6 = ?, enabled = ?, date_updated = ?, featured_prod_isset = ?, variant_isset = ?, set_product_arrangement = ? WHERE Id = ?";
		
		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}

		$bind_data = array(
			$args['f_member_shop'],
			$args['f_category'],
			$args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$args['f_uom'],
			$args['f_compare_at_price'],
			$args['f_tags'],
			$args['f_inv_sku'],
			$args['f_inv_barcode'],
			$args['f_tq_isset'],
			$args['f_cont_selling_isset'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_admin_isset'],
			$args['f_disc_ratetype'],
			$args['f_summary'],
			$args['f_arrangement'],
			$args['f_age_restriction_isset'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			$f_status,
			date('Y-m-d H:i:s'),
			$featured_product,
			$args['f_variants_isset'],
			$featured_product_arrangment,
			$id
		);

		$this->db->query($sql, $bind_data);
		$string = $this->audittrail->checkProductChanges_sys_products($get_product, $args);
		
		if($imgArr[0] != ''){
			$sql = "UPDATE sys_products_images SET status = 0 WHERE product_id = ? AND status = 1";
			$bind_data = array(
				$id
			);

			$this->db->query($sql, $bind_data);

			foreach($imgArr as $key => $value){
				if($value != ""){
					$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
					$bind_data = array(
						$id,
						$key+1,
						$value,
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}
			}
		}





		//// product shipping validation
		$sql = "SELECT * FROM sys_products_shipping WHERE product_id = ? AND enabled = 1";
		$bind_data = array(
			$id
		);

		$prod_shipping = $this->db->query($sql, $bind_data);

		if($prod_shipping->num_rows() > 0){
			$sql = "UPDATE sys_products_shipping SET weight = ?, uom_id = ?, shipping_isset = ?, length = ?, width = ?, height = ?, date_updated = ? WHERE product_id = ?";

			$bind_data = array(
				$args['f_weight'],
				$args['f_uom'],
				$args['f_shipping_isset'],
				$args['f_length'],
				$args['f_width'],
				$args['f_height'],
				date('Y-m-d H:i:s'),
				$id
			);

			$this->db->query($sql, $bind_data);
		}else{
			$sql = "INSERT INTO sys_products_shipping (`product_id`, `weight`, `uom_id`, `shipping_isset`, `length`, `width`, `height`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?,?,?) ";
				$bind_data = array(
					$id,
					$args['f_weight'],
					$args['f_uom'],
					$args['f_shipping_isset'],
					$args['f_length'],
					$args['f_width'],
					$args['f_height'],
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
		}

		$string .= $this->audittrail->checkProductChanges_sys_products_shipping($prod_shipping->row_array(), $args);
		
		if($save_promo_log == 1){
			///for promo price tracking
			$sql = "UPDATE sys_products_promo SET enabled = 0 WHERE product_id = ?";
			$bind_data = array(
				$id,
			);

			$this->db->query($sql, $bind_data);

			$sql = "INSERT INTO sys_products_promo (`product_id`, `promo_price`, `date_created`,`enabled`) VALUES (?,?,?,?) ";
			$bind_data = array(
				$id,
				$args['f_compare_at_price'],
				date('Y-m-d H:i:s'),
				1
			);

			$this->db->query($sql, $bind_data);
		}

		/// product inventory validation per branch
		if($branchid == 0){
			if(floatval($args['f_no_of_stocks_0']) != floatval($args['hidden_f_no_of_stocks_0'])){
				$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
				$bind_data = array(
					$args['f_member_shop'],
					$id
				);

				$branch_invtrans = $this->db->query($sql, $bind_data);

				$string .= $this->audittrail->checkProductChanges_sys_products_invtrans_branch($id, 0, $args['f_no_of_stocks_0']);

				if($branch_invtrans->num_rows() > 0){
					$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
					$bind_data = array(
						$args['f_no_of_stocks_0'],
						$args['f_member_shop'],
						$id

					);	

					$this->db->query($sql, $bind_data);
				}else{
					$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
					$bind_data = array(
						$args['f_member_shop'],
						0,
						$id,
						$args['f_no_of_stocks_0'],
						date('Y-m-d H:i:s'),
						1
					);	

					$this->db->query($sql, $bind_data);
				}

				$sql = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = 0 AND enabled = 1";
				$bind_data = array(
					$id
				);

				$invtrans = $this->db->query($sql, $bind_data);

				if($invtrans->row()->qty_count_stocks > 0){
					$total_qty = 0;
					$total_qty = $args['f_no_of_stocks_0'] - $invtrans->row()->qty_count_stocks;

					if($total_qty != 0){
						$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							0,
							$id,
							$total_qty,
							'Update_products_admin',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);

						$this->db->query($sql, $bind_data);
					}else{
						$this->db->query($sql, $bind_data);
					}
				}else{
					if($args['f_no_of_stocks_0'] != 0){
						$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							0,
							$id,
							$args['f_no_of_stocks_0'],
							'Update_products_admin',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);

						$this->db->query($sql, $bind_data);
					}else{
						$this->db->query($sql, $bind_data);
					}
					
				}
			}
		}
		
		$branchdetails = $this->get_sys_branch_profile($args['f_member_shop'], $id, $branchid);

		if($branchdetails != false){
			foreach($branchdetails as $val){
				
				if(isset($args['f_no_of_stocks_'.$val['id']])){
					$branch_no_of_stocks = 'f_no_of_stocks_'.$val['id'];
					$hidden_branch_no_of_stocks = 'hidden_f_no_of_stocks_'.$val['id'];
					if(floatval($args[$branch_no_of_stocks]) != floatval($args[$hidden_branch_no_of_stocks])){
						$string .= $this->audittrail->checkProductChanges_sys_products_invtrans_branch($id, $val['id'], $args[$branch_no_of_stocks]);
						$this->update_branch_invtrans($args['f_member_shop'], $val['id'], $args[$branch_no_of_stocks], $id, 'Update_products_admin');
					}
				}
			}
		}

		$sql = "SELECT SUM(no_of_stocks) as grand_total_no_of_stocks FROM sys_products_invtrans_branch WHERE product_id = ? AND status = 1";
		
		$bind_data = array(
			$id
		);
		$grand_total_no_of_stocks = $this->db->query($sql, $bind_data);

		$sql = "SELECT SUM(a.no_of_stocks) as deleted_stocks FROM sys_products_invtrans_branch AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
		WHERE a.product_id = ? AND a.status = 1 AND b.status IN (0, 2)";

		$bind_data = array(
			$id
		);
		$deleted_stocks = $this->db->query($sql, $bind_data)->row()->deleted_stocks;

		$grand_total_no_of_stocks = $grand_total_no_of_stocks->row()->grand_total_no_of_stocks;
		$grand_total              = $grand_total_no_of_stocks - abs($deleted_stocks);


		
		$sql = "SELECT SUM(no_of_stocks) as variants_no_of_stocks FROM sys_products WHERE parent_product_id = ? AND enabled > 0";
		$bind_data = array(
			$id
		);
		$grand_variants_total = $this->db->query($sql, $bind_data);


		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			strval($grand_total) +  strval($grand_variants_total->row()->variants_no_of_stocks),
			$id
		);

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET img_1 = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$id,
			$id
		);

		$this->db->query($sql, $bind_data);

		return $string;
	}

	public function update_variant($args, $id, $save_promo_log, $imgArr,$featured_product,$featured_product_arrangment, $delivery_areas_str) {


		$branchid    	   = $this->session->userdata('branchid');
		$get_product 	   = $this->check_products($id)->row_array();
		$imgArr[0]         = (!empty($imgArr[0])) ? $imgArr[0] : '';
		$imgArr[1]         = (!empty($imgArr[1])) ? $imgArr[1] : '';
		$imgArr[2]         = (!empty($imgArr[2])) ? $imgArr[2] : '';
		$imgArr[3]         = (!empty($imgArr[3])) ? $imgArr[3] : '';
		$imgArr[4]         = (!empty($imgArr[4])) ? $imgArr[4] : '';
		$imgArr[5]   	   = (!empty($imgArr[5])) ? $imgArr[5] : '';

		$args['f_startup'] = ($args['f_startup'] != '') ? $args['f_startup'] : 0;
		$args['f_startup'] =  $args['f_startup'] / 100;
		$args['f_jc']      = ($args['f_jc'] != '') ? $args['f_jc'] : 0;
		$args['f_jc']      =  $args['f_jc'] / 100;
		$args['f_mcjr']    = ($args['f_mcjr'] != '') ? $args['f_mcjr'] : 0;
		$args['f_mcjr']    =  $args['f_mcjr'] / 100;
		$args['f_mc']      = ($args['f_mc'] != '') ? $args['f_mc'] : 0;
		$args['f_mc']      =  $args['f_mc'] / 100;
		$args['f_mcsuper'] = ($args['f_mcsuper'] != '') ? $args['f_mcsuper'] : 0;
		$args['f_mcsuper'] =  $args['f_mcsuper'] / 100;
		$args['f_mcmega']  = ($args['f_mcmega'] != '') ? $args['f_mcmega'] : 0;
		$args['f_mcmega']  =  $args['f_mcmega'] / 100;
		$args['f_others']  = ($args['f_others'] != '') ? $args['f_others'] : 0;
		$args['f_others']  =  $args['f_others'] / 100;

		
		//delivery areas, only set to jcww
		if(ini() == 'jcww'){
			$str_update = ", `delivery_areas` = '$delivery_areas_str'";
			$f_status          = $get_product['enabled'];
		}
		else{
			$str_update = "";
			$f_status          = ($args['f_itemid'] == '') ? 2 : $get_product['enabled'];
		}
		// merchant comm rate, onlty set to toktokmall
		if(ini() == 'toktokmall'){
			$args['f_disc_rate'] = ($args['f_disc_rate'] != '') ? $args['f_disc_rate'] : 0;
			$args['f_disc_rate'] =  $args['f_disc_rate'] / 100;

			$args['f_disc_ratetype'] = "p";
		}


		
		$sql = "UPDATE sys_products SET sys_shop = ?, cat_id = ?, itemid = ?, itemname = ?, otherinfo = ?, uom = ?, price = ?, compare_at_price = ?,  tags = ?, inv_sku = ?, inv_barcode = ?, tq_isset = ?, cont_selling_isset = ?, max_qty_isset = ?, max_qty = ?, admin_isset = ?, disc_ratetype = ?, disc_rate = ?,  summary = ?, arrangement = ?, img_1 = ?, img_2 = ?, img_3 = ?, img_4 = ?, img_5 = ?, img_6 = ?, enabled = ?, date_updated = ?, featured_prod_isset = ?, variant_isset = ?, set_product_arrangement = ? $str_update WHERE Id = ?";
		
		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}



		$bind_data = array(
			$args['f_member_shop'],
			$args['f_category'],
			$args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$args['f_uom'],
			$args['f_price'],
			$args['f_compare_at_price'],
			$args['f_tags'],
			$args['f_inv_sku'],
			$args['f_inv_barcode'],
			$args['f_tq_isset'],
			$args['f_cont_selling_isset'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_admin_isset'],
			$args['f_disc_ratetype'],
			$args['f_disc_rate'],
			$args['f_summary'],
			$args['f_arrangement'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			$f_status,
			date('Y-m-d H:i:s'),
			$featured_product,
			$args['f_variants_isset'],
			$featured_product_arrangment,
			$id
		);

		$this->db->query($sql, $bind_data);
		$string = $this->audittrail->checkProductChanges_sys_products($get_product, $args);
		
		if($imgArr[0] != ''){
			$sql = "UPDATE sys_products_images SET status = 0 WHERE product_id = ? AND status = 1";
			$bind_data = array(
				$id
			);

			$this->db->query($sql, $bind_data);

			foreach($imgArr as $key => $value){
				if($value != ""){
					$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
					$bind_data = array(
						$id,
						$key+1,
						$value,
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}
			}
		}

		
		/// referralcommrate
		$sql = "SELECT a.*, b.`disc_rate`, a.itemID as ProductItemID, b.`featured_prod_isset`, b.price
		        FROM 8_referralcom_rate AS a 
				LEFT JOIN sys_products AS b 
				ON a.`product_id` = b.`Id` 
				WHERE a.product_id = '$id' ";

		$prod_refcomm = $this->db->query($sql);
		$prod_refcom1 =   $prod_refcomm->result_array();


		$sql_prodStatus = "SELECT * FROM sys_product_status WHERE product_id =  '$id' ";
		$prod_status  = $this->db->query($sql_prodStatus);




	    if($prod_refcom1[0]['price'] != $args['f_price']  || strval($prod_refcom1[0]['ProductItemID']) != strval($args['f_itemid']) || strval($prod_refcom1[0]['startup'])  != strval($args['f_startup'])  || strval($prod_refcom1[0]['jc']) != strval($args['f_jc'])  || strval($prod_refcom1[0]['mcjr']) != strval($args['f_mcjr']) || strval($prod_refcom1[0]['mc']) != strval($args['f_mc']) || strval($prod_refcom1[0]['mcsuper']) != strval($args['f_mcsuper']) || strval($prod_refcom1[0]['mcmega']) != strval($args['f_mcmega']) || strval($prod_refcom1[0]['others']) != strval($args['f_others']) || strval($prod_refcom1[0]['disc_rate']) != strval($args['f_disc_rate'])){

		if(count($prod_refcom1) != 0){
			if($prod_status->num_rows() == 0){
				// print_r('if');
				 $sql = "INSERT INTO sys_product_status (`itemid`, `product_id`, `instance_id`, `disc_rate`, `startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`,`price`,`status`, `user_id`, `created`, `updated`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
				 $bind_data = array(
					 $args['f_itemid'],
					 $id,
					 ini(),
					 $args['f_disc_rate'],
					 $args['f_startup'],
					 $args['f_jc'],
					 $args['f_mcjr'],
					 $args['f_mc'],
					 $args['f_mcsuper'],
					 $args['f_mcmega'],
					 $args['f_others'],
					 $args['f_price'],
					 3,
					 $this->session->userdata('id'),
					 date('Y-m-d H:i:s'),
					 date('Y-m-d H:i:s'),
				 );	
				 $this->db->query($sql, $bind_data);
				 
					 

			 }else{
			  
				 $sql = "UPDATE sys_product_status SET `itemid` = ?, `disc_rate` = ?, `startup` = ?, `jc` = ?, `mcjr` = ?, `mc` = ?, `mcsuper` = ?, `mcmega` = ?, `others` = ?, `price` = ?, `user_id` = ?, `updated` = ?, `status` = ?  WHERE `product_id` = ?";
				 $bind_data = array(
					 $args['f_itemid'],
					 $args['f_disc_rate'],
					 $args['f_startup'],
					 $args['f_jc'],
					 $args['f_mcjr'],
					 $args['f_mc'],
					 $args['f_mcsuper'],
					 $args['f_mcmega'],
					 $args['f_others'],
					 $args['f_price'],
					 $this->session->userdata('id'),
					 date('Y-m-d H:i:s'),
					 3,
					 $id,
				 );
				 $this->db->query($sql, $bind_data);


			 
			 }
		   
			 $string .= $this->audittrail->checkProductChanges_refcommrate($get_product, $args);

		}

		}else{   

	
					if($prod_refcomm->num_rows() == 0){
						$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
						$bind_data = array(
							$args['f_itemid'],
							$id,
							ini(),
							$args['f_startup'],
							$args['f_jc'],
							$args['f_mcjr'],
							$args['f_mc'],
							$args['f_mcsuper'],
							$args['f_mcmega'],
							$args['f_others'],
							1
						);	
						
						$this->db->query($sql, $bind_data);
					}
					else{
						$sql = "UPDATE 8_referralcom_rate SET itemid = ?, startup = ?, jc = ?, mcjr = ?, mc = ?, mcsuper = ?, mcmega = ?, others = ? WHERE product_id = ? AND status = ?";
						$bind_data = array(
							$args['f_itemid'],
							$args['f_startup'],
							$args['f_jc'],
							$args['f_mcjr'],
							$args['f_mc'],
							$args['f_mcsuper'],
							$args['f_mcmega'],
							$args['f_others'],
							$id,
							1
						);


						$this->db->query($sql, $bind_data);
					}


					$string .= $this->audittrail->checkProductChanges_refcommrate($get_product, $args);


		}

			
		$sql = "UPDATE sys_products SET sys_shop = ?, cat_id = ?, itemid = ?, itemname = ?, otherinfo = ?, uom = ?, compare_at_price = ?,  tags = ?, inv_sku = ?, inv_barcode = ?, tq_isset = ?, cont_selling_isset = ?, max_qty_isset = ?, max_qty = ?, admin_isset = ?, disc_ratetype = ?,  summary = ?, arrangement = ?, img_1 = ?, img_2 = ?, img_3 = ?, img_4 = ?, img_5 = ?, img_6 = ?, enabled = ?, date_updated = ?, featured_prod_isset = ?, variant_isset = ?, set_product_arrangement = ? $str_update WHERE Id = ?";
		
		if($args['f_max_qty'] == null || $args['f_max_qty'] == ''){
			$args['f_max_qty'] = 1;
		}


		$bind_data = array(
			$args['f_member_shop'],
			$args['f_category'],
			$args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$args['f_uom'],
			$args['f_compare_at_price'],
			$args['f_tags'],
			$args['f_inv_sku'],
			$args['f_inv_barcode'],
			$args['f_tq_isset'],
			$args['f_cont_selling_isset'],
			$args['f_max_qty_isset'],
			$args['f_max_qty'],
			$args['f_admin_isset'],
			$args['f_disc_ratetype'],
			$args['f_summary'],
			$args['f_arrangement'],
			'none',
			'none',
			'none',
			'none',
			'none',
			'none',
			$f_status,
			date('Y-m-d H:i:s'),
			$featured_product,
			$args['f_variants_isset'],
			$featured_product_arrangment,
			$id
		);

		$this->db->query($sql, $bind_data);
		$string = $this->audittrail->checkProductChanges_sys_products($get_product, $args);
		
		if($imgArr[0] != ''){
			$sql = "UPDATE sys_products_images SET status = 0 WHERE product_id = ? AND status = 1";
			$bind_data = array(
				$id
			);

			$this->db->query($sql, $bind_data);

			foreach($imgArr as $key => $value){
				if($value != ""){
					$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
					$bind_data = array(
						$id,
						$key+1,
						$value,
						date('Y-m-d H:i:s'),
						1
					);

					$this->db->query($sql, $bind_data);
				}
			}
		}



	
	
		//// product shipping validation
		$sql = "SELECT * FROM sys_products_shipping WHERE product_id = ? AND enabled = 1";
		$bind_data = array(
			$id
		);

		$prod_shipping = $this->db->query($sql, $bind_data);

		if($prod_shipping->num_rows() > 0){
			$sql = "UPDATE sys_products_shipping SET weight = ?, uom_id = ?, shipping_isset = ?, length = ?, width = ?, height = ?, date_updated = ? WHERE product_id = ?";

			$bind_data = array(
				$args['f_weight'],
				$args['f_uom'],
				$args['f_shipping_isset'],
				$args['f_length'],
				$args['f_width'],
				$args['f_height'],
				date('Y-m-d H:i:s'),
				$id
			);

			$this->db->query($sql, $bind_data);
		}else{
			$sql = "INSERT INTO sys_products_shipping (`product_id`, `weight`, `uom_id`, `shipping_isset`, `length`, `width`, `height`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?,?,?) ";
				$bind_data = array(
					$id,
					$args['f_weight'],
					$args['f_uom'],
					$args['f_shipping_isset'],
					$args['f_length'],
					$args['f_width'],
					$args['f_height'],
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
		}

		$string .= $this->audittrail->checkProductChanges_sys_products_shipping($prod_shipping->row_array(), $args);
		
		if($save_promo_log == 1){
			///for promo price tracking
			$sql = "UPDATE sys_products_promo SET enabled = 0 WHERE product_id = ?";
			$bind_data = array(
				$id,
			);

			$this->db->query($sql, $bind_data);

			$sql = "INSERT INTO sys_products_promo (`product_id`, `promo_price`, `date_created`,`enabled`) VALUES (?,?,?,?) ";
			$bind_data = array(
				$id,
				$args['f_compare_at_price'],
				date('Y-m-d H:i:s'),
				1
			);

			$this->db->query($sql, $bind_data);
		}

		/// product inventory validation per branch
		if($branchid == 0){
			if(floatval($args['f_no_of_stocks_0']) != floatval($args['hidden_f_no_of_stocks_0'])){
				$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
				$bind_data = array(
					$args['f_member_shop'],
					$id
				);

				$branch_invtrans = $this->db->query($sql, $bind_data);

				$string .= $this->audittrail->checkProductChanges_sys_products_invtrans_branch($id, 0, $args['f_no_of_stocks_0']);

				if($branch_invtrans->num_rows() > 0){
					$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND product_id = ? AND branchid = 0 AND status = 1";
					$bind_data = array(
						$args['f_no_of_stocks_0'],
						$args['f_member_shop'],
						$id

					);	

					$this->db->query($sql, $bind_data);
				}else{
					$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
					$bind_data = array(
						$args['f_member_shop'],
						0,
						$id,
						$args['f_no_of_stocks_0'],
						date('Y-m-d H:i:s'),
						1
					);	

					$this->db->query($sql, $bind_data);
				}

				$sql = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = 0 AND enabled = 1";
				$bind_data = array(
					$id
				);

				$invtrans = $this->db->query($sql, $bind_data);

				if($invtrans->row()->qty_count_stocks > 0){
					$total_qty = 0;
					$total_qty = $args['f_no_of_stocks_0'] - $invtrans->row()->qty_count_stocks;

					if($total_qty != 0){
						$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							0,
							$id,
							$total_qty,
							'Update_products_admin',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);

						$this->db->query($sql, $bind_data);
					}else{
						$this->db->query($sql, $bind_data);
					}
				}else{
					if($args['f_no_of_stocks_0'] != 0){
						$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							0,
							$id,
							$args['f_no_of_stocks_0'],
							'Update_products_admin',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);

						$this->db->query($sql, $bind_data);
					}else{
						$this->db->query($sql, $bind_data);
					}
					
				}
			}
		}
		
		$branchdetails = $this->get_sys_branch_profile($args['f_member_shop'], $id, $branchid);

		if($branchdetails != false){
			foreach($branchdetails as $val){
				if(isset($args['f_no_of_stocks_'.$val['id']])){
					$branch_no_of_stocks = 'f_no_of_stocks_'.$val['id'];
					$hidden_branch_no_of_stocks = 'hidden_f_no_of_stocks_'.$val['id'];
					if(floatval($args[$branch_no_of_stocks]) != floatval($args[$hidden_branch_no_of_stocks])){
						$string .= $this->audittrail->checkProductChanges_sys_products_invtrans_branch($id, $val['id'], $args[$branch_no_of_stocks]);
						$this->update_branch_invtrans($args['f_member_shop'], $val['id'], $args[$branch_no_of_stocks], $id, 'Update_products_admin');
					}
				}
			}
		}

		$sql = "SELECT SUM(no_of_stocks) as grand_total_no_of_stocks FROM sys_products_invtrans_branch WHERE product_id = ? AND status = 1";
		
		$bind_data = array(
			$id
		);
		$grand_total_no_of_stocks = $this->db->query($sql, $bind_data);



		$sql = "SELECT SUM(a.no_of_stocks) as deleted_stocks FROM sys_products_invtrans_branch AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
		WHERE a.product_id = ? AND a.status = 1 AND b.status IN (0, 2)";

		$bind_data = array(
			$id
		);
		$deleted_stocks = $this->db->query($sql, $bind_data)->row()->deleted_stocks;

		
		$grand_total_no_of_stocks = $grand_total_no_of_stocks->row()->grand_total_no_of_stocks;
		$grand_total              = $grand_total_no_of_stocks - abs($deleted_stocks);

		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			$grand_total,
			$id
		);
		$this->db->query($sql, $bind_data);



		$sql = "SELECT parent_product_id, Id , no_of_stocks as variant_stocks FROM sys_products WHERE Id = ?";
		$bind_data = array(
			$id
		);
		$parent_product_id = $this->db->query($sql, $bind_data);


		$sql = "SELECT SUM(quantity) as grand_total_no_of_stocks FROM sys_products_invtrans WHERE product_id = ? AND enabled = 1";
		$bind_data = array(
			$parent_product_id->row()->parent_product_id
		);
		$grand_parentproduct_stocks = $this->db->query($sql, $bind_data);

		$sql = "SELECT SUM(no_of_stocks)  as variants_no_of_stocks FROM sys_products WHERE parent_product_id = ?";
		$bind_data = array(
			$parent_product_id->row()->parent_product_id
		);
		$variant_stocks = $this->db->query($sql, $bind_data);

    	$sum_of_stocks = strval($variant_stocks->row()->variants_no_of_stocks) + strval($grand_parentproduct_stocks->row()->grand_total_no_of_stocks);


		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			strval($sum_of_stocks),
			$parent_product_id->row()->parent_product_id
		);
		$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET img_1 = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$id,
			$id
		);

		$this->db->query($sql, $bind_data);
		
		return $string;
	}

	public function save_variantsummary($product_id, $var_option_name, $var_option_list, $variant_counter) {
		$sql = "INSERT INTO sys_products_variantsummary (`parent_product_id`, `option_no`, `variant_type`, `variant_list`, `date_created`, `status`) VALUES (?, ?, ?, ?, ?, ?) ";
		
		$bind_data = array(
			$product_id,
			$variant_counter+1,
			$var_option_name,
			$var_option_list,
			date('Y-m-d H:i:s'),
			1
		);	

		return $this->db->query($sql, $bind_data);
	}

	public function update_variantsummary($product_id, $var_option_name, $var_option_list, $variant_counter) {
		$sql = "UPDATE sys_products_variantsummary SET variant_type = ?, variant_list = ?, date_updated = ? WHERE parent_product_id = ? AND option_no = ?";
		
		$bind_data = array(
			$var_option_name,
			$var_option_list,
			date('Y-m-d H:i:s'),
			$product_id,
			$variant_counter+1,
 		);	

		$this->db->query($sql, $bind_data);
	
		if($this->db->affected_rows() == 0){
			$sql = "INSERT INTO sys_products_variantsummary (`parent_product_id`, `option_no`, `variant_type`, `variant_list`, `date_created`, `status`) VALUES (?, ?, ?, ?, ?, ?) ";
		
			$bind_data = array(
				$product_id,
				$variant_counter+1,
				$var_option_name,
				$var_option_list,
				date('Y-m-d H:i:s'),
				1
			);	

			return $this->db->query($sql, $bind_data);
		}
		
	}

	public function save_variants($product_id, $child_product_id, $variant_name, $variant_price, $variant_sku, $variant_barcode, $args, $variant_isset) {
		
		if(ini() == 'toktokmall'){
			$args['f_disc_rate'] = ($args['f_disc_rate'] != '') ? $args['f_disc_rate'] : 0;
			$args['f_disc_rate'] =  $args['f_disc_rate'] / 100;
			$args['f_disc_ratetype'] = "p";
		}

		$args['f_startup'] = ($args['f_startup'] != '') ? $args['f_startup'] : 0;
		$args['f_startup'] =  $args['f_startup'] / 100;
		$args['f_jc']      = ($args['f_jc'] != '') ? $args['f_jc'] : 0;
		$args['f_jc']      =  $args['f_jc'] / 100;
		$args['f_mcjr']    = ($args['f_mcjr'] != '') ? $args['f_mcjr'] : 0;
		$args['f_mcjr']    =  $args['f_mcjr'] / 100;
		$args['f_mc']      = ($args['f_mc'] != '') ? $args['f_mc'] : 0;
		$args['f_mc']      =  $args['f_mc'] / 100;
		$args['f_mcsuper'] = ($args['f_mcsuper'] != '') ? $args['f_mcsuper'] : 0;
		$args['f_mcsuper'] =  $args['f_mcsuper'] / 100;
		$args['f_mcmega']  = ($args['f_mcmega'] != '') ? $args['f_mcmega'] : 0;
		$args['f_mcmega']  =  $args['f_mcmega'] / 100;
		$args['f_others']  = ($args['f_others'] != '') ? $args['f_others'] : 0;
		$args['f_others']  =  $args['f_others'] / 100;

		$sql = "INSERT INTO sys_products (`Id`, `sys_shop`, `itemname`, `price`, `inv_sku`, `inv_barcode`, `tq_isset`, `admin_isset`, `disc_ratetype`, `disc_rate`, `enabled`, `date_created`, `variant_isset`, `parent_product_id`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";

		$variant_price = ($variant_price == '') ? 0: $variant_price;
		$bind_data = array(
			$child_product_id,
			$args['f_member_shop'],
			$variant_name,
			$variant_price,
			$variant_sku,
			$variant_barcode,
			1,
			$args['f_admin_isset'],
			$args['f_disc_ratetype'],
			$args['f_disc_rate'],
			2,
			date('Y-m-d H:i:s'),
			$variant_isset,
			$product_id
		);	

		$this->db->query($sql, $bind_data);

		/// referralcommrate
		$sql = "INSERT INTO 8_referralcom_rate (`itemid`, `product_id`, `instance_id`,`startup`, `jc`, `mcjr`, `mc`, `mcsuper`, `mcmega`, `others`, `status`) VALUES (?,?,?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$args['f_itemid'],
			$child_product_id,
			ini(),
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			1
		);

		$this->db->query($sql, $bind_data);

		$sql = "INSERT INTO sys_product_status (`product_id`, `status`, `user_id`, `created`, `updated`) VALUES (?,?,?,?,?) ";
		$bind_data = array(
			$child_product_id,
	        3,
			$this->session->userdata('id'),
			date('Y-m-d H:i:s'),
			date('Y-m-d H:i:s'),
		);
		$this->db->query($sql, $bind_data);
		
	}

	public function update_variants($product_id, $child_product_id, $variant_name, $variant_price, $variant_sku, $variant_status) {
		$sql = "UPDATE sys_products SET itemname = ?, price = ?, inv_sku = ?, enabled = ?, date_updated = ?";


        if($variant_status == 2){
			$sql .= ",featured_prod_isset = '0', set_product_arrangement = '0'";
		}	

	 	$sql .= " WHERE Id = ? AND parent_product_id = ? ";
		
		$variant_price = ($variant_price == '') ? 0: $variant_price;
		$bind_data = array(
			$variant_name,
			$variant_price,
			$variant_sku,
			$variant_status,
			date('Y-m-d H:i:s'),
			$child_product_id,
			$product_id
		);	

		return $this->db->query($sql, $bind_data);
	}

	public function deleteVariant($Id){
		$sql = "UPDATE sys_products SET `enabled` = '0', `featured_prod_isset` = '0', set_product_arrangement = '0' WHERE Id = ?";
		$bind_data = array(
			$Id
		);	

		$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products_promotion SET `status` = '0' WHERE product_id = ?";
		$bind_data = array(
			$Id
		);	

		$this->db->query($sql, $bind_data);
	}

	public function updateParentProductInventoryQty($Id){

		// $sql = "SELECT * FROM sys_products WHERE Id = ? AND enabled > 0";
		// $bind_data = array(
		// 	$Id
		// );	

		// $parentProduct = $this->db->query($sql, $bind_data)->row_array();
		// $parentInvQty  = $parentProduct['no_of_stocks'];

		$sql = "SELECT SUM(no_of_stocks) as total_qty_variant FROM sys_products WHERE parent_product_id = ? AND enabled > 0";
		$bind_data = array(
			$Id
		);	

		$variantProduct = $this->db->query($sql, $bind_data)->row_array();
		$variantInvQty  = $variantProduct['total_qty_variant'];

		$grand_total_no_of_stocks = $variantInvQty;

		$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		$bind_data = array(
			$grand_total_no_of_stocks,
			$Id
		);	

		return $this->db->query($sql, $bind_data);
	}

	public function getParentProductInvBranch($Id, $branchid){

		$sql = "SELECT * FROM sys_products WHERE parent_product_id = ? AND enabled > 0";
		$bind_data = array(
			$Id
		);

		$result = $this->db->query($sql, $bind_data)->result_array();
		$total_qty = 0;
	
		foreach($result as $row){
			$sql = "SELECT * FROM sys_products_invtrans_branch WHERE branchid = ? AND product_id = ? AND status > 0";
			$bind_data = array(
				$branchid,
				$row['Id']
			);

			$variantQtyBranch = $this->db->query($sql, $bind_data)->row_array();
			$variantQtyBranch['no_of_stocks'] = (!empty($variantQtyBranch['no_of_stocks'])) ? $variantQtyBranch['no_of_stocks'] : 0;
			$total_qty += $variantQtyBranch['no_of_stocks'];
		}	
	
		return $total_qty;
	}

	public function update_branch_invtrans($shopid, $branchid, $no_of_stocks, $product_id, $type){

		$sql = "SELECT * FROM sys_products_invtrans_branch WHERE shopid = ? AND branchid = ? AND product_id = ? AND status = 1";
		$bind_data = array(
			$shopid,
			$branchid,
			$product_id
		);

		$branch_invtrans = $this->db->query($sql, $bind_data);

		if($branch_invtrans->num_rows() > 0){
			$sql = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE shopid = ? AND branchid = ? AND product_id = ? AND status = 1";
			$bind_data = array(
				$no_of_stocks,
				$shopid,
				$branchid,
				$product_id
			);	

			$this->db->query($sql, $bind_data);
		}else{
			$sql = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
			$bind_data = array(
				$shopid,
				$branchid,
				$product_id,
				$no_of_stocks,
				date('Y-m-d H:i:s'),
				1
			);	

			$this->db->query($sql, $bind_data);
		}

		$sql = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = ? AND enabled = 1";
		$bind_data = array(
			$product_id,
			$branchid
		);

		$invtrans = $this->db->query($sql, $bind_data);

		if($invtrans->row()->qty_count_stocks > 0){
			$total_qty = 0;
			$total_qty = $no_of_stocks - $invtrans->row()->qty_count_stocks;

			if($total_qty != 0){
				$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
				$bind_data = array(
					$branchid,
					$product_id,
					$total_qty,
					$type,
					$this->session->userdata('username'),
					date('Y-m-d H:i:s'),
					1
				);

				 $this->db->query($sql, $bind_data);
			}else{
				 $this->db->query($sql, $bind_data);
			}
		}else{

			if($no_of_stocks != 0){
				$sql = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
				$bind_data = array(
					$branchid,
					$product_id,
					$no_of_stocks,
					$type,
					$this->session->userdata('username'),
					date('Y-m-d H:i:s'),
					1
				);

				$this->db->query($sql, $bind_data);
			}else{
				$this->db->query($sql, $bind_data);
			}
			
		}
	}

	public function update_product_refcommrate($args, $id, $shopcode){
		//// update referral commission rate
		$sql = "UPDATE 8_referralcom_rate SET itemid = ?, itemname = ?, others = ? WHERE product_id = ? AND status = 1";
		$bind_data = array(
			$shopcode.'_'.$args['f_itemid'],
			$args['f_itemname'],
			$args['f_otherinfo'],
			$id
		);

		return $this->db->query($sql, $bind_data);
	}

    public function product_table($sys_shop, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$date_from      = $this->input->post('date_from');
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_shops 		= $this->input->post('_shops');
		$token_session  = $this->session->userdata('token_session');
		$token          = en_dec('en', $token_session);
		$branchid       = $this->session->userdata('branchid');

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'itemname',
            1 => 'itemname',
            2 => 'category_name',
            3 => 'price',
            4 => 'no_of_stocks',
            5 => 'shopname',
            6 => 'enabled',
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
                LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				WHERE a.enabled > 0 AND a.parent_product_id IS NULL";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		if (!$exportable) {
			$sql = "SELECT a.*, code.shopcode, c.category_name, b.shopname, no_of_stocks FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id";
				// LEFT JOIN sys_products_images d ON a.Id = d.product_id AND d.arrangement = 1 AND d.status = 1";
		}
		else{
			$sql = "SELECT a.*, code.shopcode, c.category_name, b.shopname, no_of_stocks FROM sys_products a 
                LEFT JOIN sys_shops b ON a.sys_shop = b.id AND b.status > 0
				LEFT JOIN sys_product_category c ON a.cat_id = c.id AND c.status > 0
				LEFT JOIN sys_shops code ON a.sys_shop = code.id";
		}
		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE a.enabled = " . $this->db->escape($_record_status) . "";
		}else{
			$sql.=" WHERE a.enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		
		if($_name != ""){
			$sql.=" AND a.itemname LIKE '%" . $this->db->escape_like_str($_name) . "%' ";
		}
		if($_shops != ""){
			$sql.=" AND b.id = " . $this->db->escape($_shops) . "";
		}

		if($date_from != ""){
			$sql.="  AND DATE_FORMAT(a.`date_created`, '%m/%d/%Y') ='".$date_from. "'";
		}


		if($sys_shop != 0){
			$sql.=" AND b.id = " . $this->db->escape($sys_shop) . "";
		}

		$sql.=" AND a.parent_product_id IS NULL";

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";  // adding length
		if (!$exportable) {
			$sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}

		// print_r($sql);
		// exit();
		$query = $this->db->query($sql);

		$data = array();
		$get_s3_imgpath_upload = get_s3_imgpath_upload();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = (!$exportable) ? '<img class="img-thumbnail" style="width: 50px;" src="'.$get_s3_imgpath_upload.'assets/img/'.$row['shopcode'].'/products-250/'.$row['Id'].'/'.removeFileExtension($row['img_1']).'.jpg?'.rand().'">' : '';;
			// $nestedData[] = (!$exportable) ?'<u><a href="'.base_url('Main_products/view_products/'.$token.'/'.$row['Id']).'" style="color:blue;">'.$row["itemname"].'</a></u>':$row["itemname"];
			$nestedData[] = $row["itemname"];
			$nestedData[] = $row["category_name"];
			$nestedData[] = number_format($row["price"], 2);


			
			// $inv_qty_branch = $this->get_uptodate_nostocks($row['sys_shop'], $row['Id']);
			// $total_inv_qty  = 0;

			// if($inv_qty_branch != false){
			// 	foreach($inv_qty_branch as $val){
			// 		$total_inv_qty += $val['total_inv_qty'];
			// 	}
			// }
			// $total_inv_qty_main = ($this->get_uptodate_nostocks_main($row['Id']) != false) ? $this->get_uptodate_nostocks_main($row['Id'])->total_inv_qty:0;
			// $grand_total_qty = ($total_inv_qty+$total_inv_qty_main == 0) ? $row['no_of_stocks'] : $total_inv_qty+$total_inv_qty_main;
			// $no_of_stocks = ($branchid != 0) ? (!empty($this->get_invqty_branch($branchid, $row['Id'])['no_of_stocks']) ? $this->get_invqty_branch($branchid, $row['Id'])['no_of_stocks'] : 0) : $grand_total_qty;
			// $no_of_stocks = ($branchid != 0 && $no_of_stocks == 0) ? $this->getParentProductInvBranch($row['Id'], $branchid) : $no_of_stocks;
			// $nestedData[] = number_format($no_of_stocks, 1);

			$nestedData[] = $row["no_of_stocks"];
            $nestedData[] = $row["shopname"];



			if($row["enabled"]==1){
                $nestedData[] = 'Enabled';
            }else{
                $nestedData[] = 'Disabled';
            }

			if ($row['enabled'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['enabled'] == 2) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}else{
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}

			$buttons = "";
			$buttons .= '
				<a class="dropdown-item" data-value="'.$row['Id'].'" href="'.base_url('Main_products/view_products/'.$token.'/'.$row['Id']).'"><i class="fa fa-search" aria-hidden="true"></i> View</a>';

			if($this->loginstate->get_access()['products']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item" data-value="'.$row['Id'].'" href="'.base_url('Main_products/update_products/'.$token.'/'.$row['Id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
			}

            /// checker of product in product status table
			if(ini() == 'toktokmall'){


				$sql = "SELECT * FROM sys_product_status WHERE product_id = '".$row['Id']."' AND status != '1'";
				$check_status_product = $this->db->query($sql);		
	
				if($check_status_product->num_rows() == 0){
	
						if($this->loginstate->get_access()['products']['disable'] == 1){
							$buttons .= '<div class="dropdown-divider"></div>
							<a class="dropdown-item action_disable" data-value="'.$row['Id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
						}
		
				}else{
	
				}

			}else{

				if($this->loginstate->get_access()['products']['disable'] == 1){
					$buttons .= '<div class="dropdown-divider"></div>
					<a class="dropdown-item action_disable" data-value="'.$row['Id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
				}

			}
		

			if($this->loginstate->get_access()['products']['delete'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_delete " data-value="'.$row['Id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
			}


			$nestedData[] = 
			'<div class="dropdown">
				<i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
				<div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
			    '.$buttons.'
			  	</div>
			</div>';
			$data[] = $nestedData;
		}

		$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
		);

		return $json_data;
	}

	public function automate_updateProductImg(){
		$sql=" SELECT a.Id as product_id, b.shopcode FROM sys_products AS a
			LEFT JOIN sys_shops AS b ON a.sys_shop = b.id
			LEFT JOIN sys_products_images AS c ON a.Id = c.product_id
			WHERE a.enabled > 0 AND c.product_id IS NULL";
       
        return $this->db->query($sql)->result_array();;
	}

	public function update_productImgUrl($product_id, $filename, $arrangement){

		if(!empty($filename)){
			$sql = "INSERT INTO sys_products_images (`product_id`, `arrangement`, `filename`,`date_created`, `status`) VALUES (?,?,?,?,?) ";
			$bind_data = array(
				$product_id,
				$arrangement,
				$filename,
				date('Y-m-d H:i:s'),
				1
			);

			$this->db->query($sql, $bind_data);
		}
	}

	public function get_invqty_branch($branchid, $product_id){
		$sql="SELECT * FROM sys_products_invtrans_branch WHERE branchid = ? AND product_id = ? AND status = 1";
		$params = array($branchid, $product_id);
		
        return $this->db->query($sql, $params)->row_array();
	}

	public function get_inventory_table($fromdate, $todate, $search_val = '', $shopid = "all", $branchid, $requestData, $exportable = false) 
	{	
		$fromdate  = $this->db->escape($fromdate);
		$date_to_2 = $this->db->escape(date('Y-m-d H:i:s',strtotime($todate.' 23:59:59')));
		$todate    = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate    = $this->db->escape(date_format($todate, 'Y-m-d'));
		// $branchid  = (!empty($this->input->post('branchid'))) ? $this->input->post('branchid'):$this->session->userdata('branchid');

		$getTotalEndingQty    = $this->getTotalEndingQty($date_to_2)->result_array();
		$getTotalEndingQtyArr = [];

		foreach($getTotalEndingQty as $row){
			$getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'] = $row['ending_quantity'];
		}

		$columns = ['shopname', 'branchname', 'itemname', 'price', 'start_qty', 'added_qty', 'stocks', 'inv_price', 'sold', 'totalsales'];
		$shop_filter = ""; $branch_filter = "";
		if ($shopid > 0) {
			$shop_filter = " AND a.shopid = $shopid";
			if ($branchid == 'main') {
				$branch_filter = " AND a.branchid = 0";
			} elseif ($branchid > 0) {
				$branch_filter = " AND a.branchid = $branchid";
			}
		}

		$sql = "SELECT 
					CONCAT(branchid,'.', product_id) as id,
					d.shopname,
					IFNULL(e.branchname, 'Main') AS branchname,
					c.itemname,
					c.price,
					a.branchid, 
					a.product_id,
					f.itemname as parent_product_name,
					IF(a.branchid = 0, 1, e.status = 1) as b_status
				FROM `sys_products_invtrans_branch` a 
				LEFT JOIN `sys_products` c
					ON c.id = a.product_id AND c.enabled > 0
				LEFT JOIN `sys_shops` d
					ON d.id = a.shopid
				LEFT JOIN `sys_branch_profile` e
					ON e.status = 1 AND e.id = a.branchid
				LEFT JOIN sys_products AS f 
					ON c.parent_product_id = f.Id
				WHERE LOWER(c.itemname) LIKE '%$search_val%' $shop_filter $branch_filter
				GROUP BY a.product_id, a.branchid HAVING b_status = 1";
		
		$query = $this->db->query($sql);

		$totalData = $query->num_rows();
		$totalFiltered = $totalData;
		$total_count = $totalData;

		$res = $this->db->query($sql)->result_array();
		$data = [];
		$branch_ids = implode("','", array_column($res, 'id'));
		$startQty_arr = $this->get_InvStartQty($fromdate, $branch_ids);
		$startQty_id_arr = array_column($startQty_arr, 'id');
		$addedQty_arr = $this->get_InvAddedQty($fromdate, $todate, $branch_ids);
		$addedQty_id_arr = array_column($addedQty_arr, 'id');
		$soldQty_arr = $this->get_InvSoldQty($fromdate, $todate, $branch_ids);
		$soldQty_id_arr = array_column($soldQty_arr, 'id');

		foreach ($res as $key => $row) {
			$id = $row['id'];
			$start_qty_index = array_search($id, $startQty_id_arr);
			$start_qty_index = gettype($start_qty_index) == 'boolean' ? -1:$start_qty_index;
			$added_qty_index = array_search($id, $addedQty_id_arr);
			$added_qty_index = gettype($added_qty_index) == 'boolean' ? -1:$added_qty_index;
			$sold_qty_index  = array_search($id, $soldQty_id_arr);
			$sold_qty_index = gettype($sold_qty_index) == 'boolean' ? -1:$sold_qty_index;

			$start_qty = 0; $added_qty = 0; $sold_qty = 0;
			if (isset($startQty_arr[$start_qty_index])) {
				$start_qty = $startQty_arr[$start_qty_index]['start_qty'];
			}
			if (isset($addedQty_arr[$added_qty_index])) {
				$added_qty = $addedQty_arr[$added_qty_index]['added_qty'];
			}
			if (isset($soldQty_arr[$sold_qty_index])) {
				$sold_qty  = $soldQty_arr[$sold_qty_index]['sold_qty'];
			}

			// $row['stocks'] = ($start_qty + $added_qty);
			$row['stocks'] = (!empty($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'])) ? $getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'] : 0;
			$row['inv_price'] = $row['stocks'] * $row['price'];
			$row['totalsales'] = $sold_qty * $row['price'];
			$parent_itemname   = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : ''; 
			$data[] = [
				'shopname' => $row['shopname'], 
				'branchname' => $row['branchname'], 
				'itemname' => $parent_itemname.$row['itemname'], 
				'price' => $row['price'], 
				'start_qty' => $start_qty, 
				'added_qty' => $added_qty, 
				'stocks' => $row['stocks'], 
				'inv_price' => $row['inv_price'], 
				'sold' => $sold_qty,
				'totalsales' => $row['totalsales'],
			];
		}

		$key = $columns[$requestData['order'][0]['column']];
		$dir = $requestData['order'][0]['dir'];
		uasort($data, build_sorter($key, $dir));
		if (!$exportable) {
			$data = (isset($requestData['start'])) ? array_slice($data, $requestData['start'], $requestData['length']):$data;
		}

		foreach ($data as $key => $value) {
			$value['price'] = (!$exportable) ? "<div class='text-right'>" .number_format($value['price'], 2). "</div>":number_format($value['price'], 2);
			$value['inv_price'] = (!$exportable) ? "<div class='text-right'>" .number_format($value['inv_price'], 2). "</div>":number_format($value['inv_price'], 2);
			$value['totalsales'] = (!$exportable) ? "<div class='text-right'>" .number_format($value['totalsales'], 2). "</div>":number_format($value['totalsales'], 2);
			
			$value['start_qty'] = (!$exportable) ? "<div class='text-right'>" .intval($value['start_qty']). "</div>":(string) intval($value['start_qty']);
			$value['added_qty'] = (!$exportable) ? "<div class='text-right'>" .intval($value['added_qty']). "</div>":(string) intval($value['added_qty']);
			$value['stocks'] = (!$exportable) ? "<div class='text-right'>" .intval($value['stocks']). "</div>":(string) intval($value['stocks']);
			$value['sold'] = (!$exportable) ? "<div class='text-right'>" .intval($value['sold']). "</div>":(string) intval($value['sold']);
			$data[$key] = $value;
		}

		$json_data = array(
			"recordsTotal"    	=> intval( $totalData ),
			"recordsFiltered" 	=> intval( $totalFiltered ),
			"total_transaction" => $total_count,
			"data"				=> array_chunk(array_flatten($data), 10)
		);
	
		return $json_data;
	}

	public function get_inv_chart($fromdate, $todate, $search_val = '', $shopid = 'all', $branchid)
	{
		$fromdate = $this->db->escape($fromdate);
		$date_to_2 = $this->db->escape(date('Y-m-d H:i:s',strtotime($todate.' 23:59:59')));
		$todate = date_add(date_create($todate),date_interval_create_from_date_string("1 days"));
		$todate = $this->db->escape(date_format($todate, 'Y-m-d'));

		$getTotalEndingQty    = $this->getTotalEndingQty($date_to_2)->result_array();
		$getTotalEndingQtyArr = [];

		foreach($getTotalEndingQty as $row){
			$getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'] = $row['ending_quantity'];
		}

		$shop_filter = ""; $branch_filter = "";
		if ($shopid > 0) {
			$shop_filter = " AND a.shopid = $shopid";
			if ($branchid == 'main') {
				$branch_filter = " AND a.branchid = 0";
			} elseif ($branchid > 0) {
				$branch_filter = " AND a.branchid = $branchid";
			}
		}

		$sql = "SELECT 
					CONCAT(branchid,'.', product_id) as id,
					c.itemname,
					a.branchid,
					a.product_id,
					IF(a.branchid = 0, 1, e.status = 1) as b_status
				FROM `sys_products_invtrans_branch` a 
				LEFT JOIN `sys_products` c
					ON c.id = a.product_id AND c.enabled = 1
				LEFT JOIN `sys_shops` d
					ON d.id = a.shopid
				LEFT JOIN `sys_branch_profile` e
					ON e.status = 1 AND e.id = a.branchid
				LEFT JOIN sys_products AS f 
					ON c.parent_product_id = f.Id
				WHERE LOWER(c.itemname) LIKE '%$search_val%' $shop_filter $branch_filter
				GROUP BY a.product_id, a.branchid HAVING b_status = 1";

		$res = $this->db->query($sql)->result_array();
		$dataset = []; $labels = [];
		$branch_ids = implode("','", array_column($res, 'id'));
		$startQty_arr = $this->get_InvStartQty($fromdate, $branch_ids);
		$startQty_id_arr = array_column($startQty_arr, 'id');
		$addedQty_arr = $this->get_InvAddedQty($fromdate, $todate, $branch_ids);
		$addedQty_id_arr = array_column($addedQty_arr, 'id');
		$soldQty_arr = $this->get_InvSoldQty($fromdate, $todate, $branch_ids);
		$soldQty_id_arr = array_column($soldQty_arr, 'id');

		foreach ($res as $key => $row) {
			$id = $row['id']; $stock = 0;
			$start_qty_index = array_search($id, $startQty_id_arr);
			$start_qty_index = gettype($start_qty_index) == 'boolean' ? -1:$start_qty_index;
			$added_qty_index = array_search($id, $addedQty_id_arr);
			$added_qty_index = gettype($added_qty_index) == 'boolean' ? -1:$added_qty_index;
			$sold_qty_index  = array_search($id, $soldQty_id_arr);
			$sold_qty_index = gettype($sold_qty_index) == 'boolean' ? -1:$sold_qty_index;
			
			if (isset($startQty_arr[$start_qty_index])) {
				$stock = $startQty_arr[$start_qty_index]['start_qty'];
			}
			if (isset($addedQty_arr[$added_qty_index])) {
				$stock += $addedQty_arr[$added_qty_index]['added_qty'];
			}
			if (isset($soldQty_arr[$sold_qty_index])) {
				$stock -= $soldQty_arr[$sold_qty_index]['sold_qty'];
			}
			$stock = (!empty($getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'])) ? $getTotalEndingQtyArr[strval($row['branchid'])][strval($row['product_id'])]['ending_quantity'] : 0;
			$parent_itemname   = (!empty($row['parent_product_name'])) ? $row['parent_product_name'].' - ' : ''; 
			

			$dataset[] = [
				'label' => $parent_itemname.$row['itemname'],
				'inventory' => $stock,
			];
		}
		uasort($dataset, build_sorter('inventory', 'desc'));
		$labels = array_column($dataset, 'label');

		return [
			'labels' => $labels,
			'data' => $dataset,
		];
	}

	private function get_InvStartQty($fromdate, $branch_ids)
	{
		$sql = "SELECT CONCAT(branchid,'.', product_id) as id, SUM(quantity) as start_qty
				FROM
					`sys_products_invtrans`
				USE INDEX
					(date_created)
				WHERE
					date_created < $fromdate AND CONCAT(branchid,'.', product_id) IN ('$branch_ids') 
				GROUP BY branchid, product_id";
		
		return $this->db->query($sql)->result_array();
	}

	private function get_InvAddedQty($fromdate, $todate, $branch_ids)
	{
		$sql = "SELECT CONCAT(branchid,'.', product_id) as id, SUM(quantity) AS added_qty
				FROM
					`sys_products_invtrans`
				USE INDEX
					(date_created)
				WHERE
					date_created BETWEEN $fromdate AND $todate AND CONCAT(branchid,'.', product_id) IN ('$branch_ids') 
				GROUP BY branchid, product_id";
		
		return $this->db->query($sql)->result_array();
	}

	private function get_InvSoldQty($fromdate, $todate, $branch_ids)
	{
		$sql = "SELECT CONCAT(branchid,'.', product_id) as id, ABS(SUM(quantity)) as sold_qty
				FROM
					`sys_products_invtrans`
				USE INDEX
					(date_created)
				WHERE
					date_created BETWEEN $fromdate AND $todate AND `type` IN(
						'Online Purchase',
						'Online Payment',
						'Manual Order',
						'Refund Order'
					) AND CONCAT(branchid,'.', product_id) IN ('$branch_ids') 
				group by branchid, product_id";
		
		return $this->db->query($sql)->result_array();
	}

	public function getImagesfilename($Id) {
		$query="SELECT * FROM sys_products_images
		WHERE product_id = ? AND status = 1
		ORDER BY arrangement ASC";
		
		$params = array($Id);
		return $this->db->query($query, $params);
	}

	public function checkOrderActive($product_id){
		$query="SELECT b.id, b.date_ordered FROM app_sales_order_logs AS a
		LEFT JOIN app_sales_order_details AS b ON a.order_id = b.id
		WHERE a.product_id = ? AND b.order_status <> 's' AND date(b.date_ordered) >= ? AND b.status = 1 LIMIT 1";
		
		$params = array(
			$product_id,
			date('Y-m-d', strtotime("-30 days"))
		);
		return $this->db->query($query, $params);
	}
	
	public function getWishlist($product_id){
		$query="SELECT a.product_id, b.first_name, b.email, c.itemname, d.filename AS primary_pic, e.shopcode FROM app_customers_wishlist AS a
		LEFT JOIN app_customers AS b ON a.user_id = b.user_id
		LEFT JOIN sys_products AS c ON a.product_id = c.Id
		LEFT JOIN sys_products_images AS d ON c.Id = d.product_id AND d.arrangement = 1 AND d.status = 1
		LEFT JOIN sys_shops AS e ON c.sys_shop = e.id
		WHERE a.product_id = ? AND a.status > 0";
		
		$params = array(
			$product_id
		);
		return $this->db->query($query, $params);
	}
	
	public function getProductTotalStocks($product_id){
		$query="SELECT no_of_stocks FROM sys_products
		WHERE Id = ?";
		
		$params = array(
			$product_id
		);
		return $this->db->query($query, $params);
    }

	public function getTotalEndingQty($date_to_2){

		$sql = "SELECT branchid, product_id, SUM(quantity) as ending_quantity FROM sys_products_invtrans 
        WHERE date_created <= ".$date_to_2." AND enabled = 1
        GROUP BY branchid, product_id";

		
		$result = $this->db->query($sql);

		return $result;
    }


	public function getFeaturedProduct(){
    $query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ORDER BY set_product_arrangement ASC";
	return $this->db->query($query)->result_array();
    }

	public function getFeaturedProductCount(){
		$query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ";
		return $this->db->query($query)->num_rows();
	}

	public function checkFeaturedProductArrangement($product_number){
		$query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' AND set_product_arrangement = '$product_number'  AND set_product_arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}

	public function  checkedFeaturedProduct($product_id){
		$query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' AND  `Id` = '$product_id' ";
		$result = $this->db->query($query)->num_rows();

		if($result > 0){
            return 1;
          }else{
            return 0;
          }
	}

	public function getAppMember($id){
		$query="SELECT * FROM app_members WHERE sys_user = '$id' AND status = '1' ";
		return $this->db->query($query)->result_array();
	}

	public function getParentProduct($id){
		$query="SELECT * FROM sys_products WHERE Id = '$id'";
		return $this->db->query($query)->result_array();
		
	}

	public function getSysShopsDetails($id){
		$query="SELECT * FROM sys_shops WHERE id = '$id' AND status = '1' ";
		return $this->db->query($query)->result_array();
	}

	public function getAppmemberDetails($id){
		$query="SELECT * FROM app_members WHERE sys_shop = '$id' ";
		return $this->db->query($query)->result_array();
	}

	public function get_email_settings(){
		$query="SELECT * FROM email_settings";
		return $this->db->query($query)->result_array();
	}

	public function get_province() {
		$query="SELECT * FROM sys_prov WHERE status = 1";

		return $this->db->query($query)->result_array();
	}

	public function getShopDetails_byname($name){
		$sql=" SELECT * FROM sys_shops WHERE shopname = ?";
		$sql = $this->db->query($sql, $name); 

        if($sql->num_rows() > 0){
            return $sql;
        }else{
            return 0;
        }
	}

	public function getShopDetails_byID($id){
		$sql=" SELECT * FROM sys_shops WHERE id = ?";
		$sql = $this->db->query($sql, $id); 

        if($sql->num_rows() > 0){
            return $sql;
        }else{
            return 0;
        }
	}

	public function getCategoryDetails_byname($name){
		$sql=" SELECT * FROM sys_product_category WHERE category_name = ?";
		$sql = $this->db->query($sql, $name); 

        if($sql->num_rows() > 0){
            return $sql;
        }else{
            return false;
        }
	}

	public function getBranches(){
		$sql=" SELECT b.* FROM sys_branch_mainshop AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
		WHERE a.status > 0";
		$sql = $this->db->query($sql); 

        if($sql->num_rows() > 0){
            return $sql;
        }else{
            return 0;
        }
	}

	public function getBranches_shop($id){
		$sql=" SELECT b.* FROM sys_branch_mainshop AS a
		LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
		WHERE a.mainshopid = ? AND a.status > 0";
		$sql = $this->db->query($sql, $id); 

        if($sql->num_rows() > 0){
            return $sql;
        }else{
            return 0;
        }
	}

	public function puregold_upload_products_files($sheetData){

		$insert_checker = 0;
		$sql = "INSERT INTO `sys_products` (`Id`,`sys_shop`,`cat_id`,`itemname`,`inv_sku`,`inv_barcode`,`summary`,`otherinfo`,`price`,`tq_isset`,`no_of_stocks`,`enabled`) VALUES ";
		
		foreach(array_slice($sheetData, 1) as $value){
			$f_id = $this->uuid->v4_formatted(); 
		
			$val        = trim($value[7]);
			$value[4]   = trim($value[4]);
			$value[8]   = trim($value[8]);
			$value[9]   = trim($value[9]);
			$value[1]   = trim($value[1]);
			$value[2]   = trim($value[2]);
			$value[10]  = trim($value[10]);
			$value[11]  = trim($value[11]);
			$value[12]  = trim($value[12]);
			$value[13]  = trim($value[13]);
			$value[14]  = trim($value[14]);
			$value[1]   = str_replace(' ', '', $value[1]);
			$value[2]   = str_replace(' ', '', $value[2]);
			$value[8]   = str_replace(' ', '', $value[8]);
			$value[10]  = str_replace(' ', '', $value[10]);
			$value[11]  = str_replace(' ', '', $value[11]);
			$value[12]  = str_replace(' ', '', $value[12]);
			$value[13]  = str_replace(' ', '', $value[13]);
			$value[14]  = str_replace(' ', '', $value[14]);
		
			$sql_select = "SELECT * FROM sys_products WHERE inv_barcode = ? AND enabled > 0";
			$params = array(
				$value[2],
			);
		
			$result_select = $this->db->query($sql_select, $params);
			
			if($result_select->num_rows() == 0){
				$sql .=  "('$f_id','$value[0]',(SELECT id FROM sys_product_category WHERE category_code = '$val' AND status = '1'),'$value[4] $value[5]','$value[1]','$value[2]','$value[4]  $value[8]','$value[9]','$value[10]','$value[11]','1','1'),";
		
				$sql1 = "INSERT INTO `sys_products_invtrans` (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`)
				VALUES (?, ?, ?, ?, ?, ?, ?)";
		
				$data1 = array(
					0, 
					$f_id, 
					$value[11], 
					'uploaded_file',
					$this->session->userdata('username'), 
					date('Y-m-d H:i:s'), 
					1
				);
				$this->db->query($sql1, $data1);
		
				$sql2 = "INSERT INTO `sys_products_invtrans_branch` (`shopid`,`branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`)
				VALUES (?, ?, ?, ?, ?, ?)";
		
				$data2 = array(
					$value[0],
					0,
					$f_id, 
					$value[11], 
					date('Y-m-d H:i:s'), 
					1
				);
				$this->db->query($sql2, $data2);
				$insert_checker = 1;
		
				if($value[12] != ''){
					$shopcode = $this->get_shopcode_via_shopid($value[0]);
					$batchUpload_S3 = $this->batchUpload_S3($value[12], $f_id, $shopcode);
		
					if($batchUpload_S3 == 1){
						$sql_img = "INSERT INTO `sys_products_images` (`product_id`,`arrangement`,`filename`,`date_created`,`status`) VALUES (?,?,?,?,?)";
					
						$params_img = array(
							$f_id,
							1,
							'0-'.$f_id.'.jpg',
							date('Y-m-d H:i:s'),
							1
						);
		
						$this->db->query($sql_img, $params_img);
					}
				}
		
		
			}
			else{
				$value[7]       = trim($value[7]);
				$product_id     = $result_select->row_array()['Id'];
				$productDetails = $result_select->row_array();
		
				if(!$this->getCategoryDetails_byname($value[7])){
					$value[7] = 0;
				}
				else{
					$value[7] = $this->getCategoryDetails_byname($value[7])->row_array()['id'];
				}
		
				$sql_update = " UPDATE `sys_products` SET `cat_id` = ?, `itemname` = ?, `inv_sku` = ?, `inv_barcode` = ?, `summary` = ?, `otherinfo` = ?, `price` = ?, `tq_isset` = 1, `no_of_stocks` = ? WHERE `inv_barcode` = ?";
		
				$params = array(
					$value[7],
					$value[4]." ".$value[5],
					$value[1],
					$value[2],
					$value[4]." ".$value[8],
					$value[9],
					$value[10],
					$value[11],
					$value[2]
				);
		
				$result = $this->db->query($sql_update, $params);
		
				$query = "SELECT * FROM sys_products_invtrans_branch WHERE product_id = ? AND branchid = 0 AND status = 1";
				$bind_data = array(
					$product_id
				);
		
				$branch_invtrans = $this->db->query($query, $bind_data);
		
				if($branch_invtrans->num_rows() > 0){
					$query = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE product_id = ? AND branchid = 0 AND status = 1";
					$bind_data = array(
						$value[11],
						$product_id
					);	
		
					$this->db->query($query, $bind_data);
				}else{
					$query = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
					$bind_data = array(
						$productDetails['sys_shop'],
						0,
						$product_id,
						$value[11],
						date('Y-m-d H:i:s'),
						1
					);	
		
					$this->db->query($query, $bind_data);
				}
		
				$query = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = 0 AND enabled = 1";
				$bind_data = array(
					$product_id
				);
		
				$invtrans = $this->db->query($query, $bind_data);
		
				if($invtrans->row()->qty_count_stocks > 0){
					$total_qty = 0;
					$total_qty = $value[11] - $invtrans->row()->qty_count_stocks;
		
					if($total_qty != 0){
						$query = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							0,
							$product_id,
							$total_qty,
							'Update_products_admin_batch',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);
		
						$this->db->query($query, $bind_data);
					}
				}else{
					if($value[11] != 0){
						$query = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							0,
							$product_id,
							$value[11],
							'Update_products_admin_batch',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);
		
						$this->db->query($query, $bind_data);
					}
				}
			}
		} 
		
		if($insert_checker == 1){
			substr_replace($sql ,"",-1);   
			$this->db->query(rtrim($sql,","));
		}
		
		return true;
	}
		
	public function upload_products_files($sheetData){
		$shop_id = $this->session->userdata('sys_shop');
		foreach(array_slice($sheetData, 1) as $value){
			if($shop_id != 0){
				$value[0] = $shop_id;
			}
			else{
				$value[0] = $this->getShopDetails_byname($value[0])->row_array()['id'];
			}
			
			$value[1] = $this->getCategoryDetails_byname($value[1])->row_array()['id'];
		
			$max_qty_isset = ($value[10] != '') ? 1 : 0 ;
			$value[10]    = ($value[10] != '') ? $value[10] : 0;
		
			$sql_select = "SELECT * FROM sys_products WHERE inv_barcode = ? AND enabled > 0";
			$params = array(
				$value[9],
			);
		
			$result_select = $this->db->query($sql_select, $params);
			
			if($result_select->num_rows() == 0){
				$sql = "INSERT INTO `sys_products` (`Id`,`sys_shop`,`cat_id`,`itemname`,`summary`,`otherinfo`,`price`,`compare_at_price`, `tags`, `inv_sku`, `inv_barcode`, `max_qty_isset`, `max_qty`, `enabled`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				$f_id = $this->uuid->v4_formatted(); 
				
				$params = array(
					$f_id,
					$value[0],
					$value[1],
					$value[2],
					$value[3],
					$value[4],
					$value[5],
					$value[6],
					$value[7],
					$value[8],
					$value[9],
					$max_qty_isset,
					$value[10],
					1
				);
		
				$this->db->query($sql, $params);
		
				if($value[11] != '' && $value[12] != '' && $value[13] != '' && $value[14] != ''){
					$sql = "INSERT INTO `sys_products_shipping` (`product_id`,`weight`,`uom_id`,`shipping_isset`,`length`,`width`,`height`,`date_created`,`enabled`) VALUES (?,?,?,?,?,?,?,?,?)";
				
					$params = array(
						$f_id,
						$value[11],
						0,
						1,
						$value[12],
						$value[13],
						$value[14],
						date('Y-m-d H:i:s'),
						1
					);
		
					$this->db->query($sql, $params);
				}
		
				$shopcode = $this->get_shopcode_via_shopid($value[0]);
				// upload image 1 to s3 and save to db
				if($value[15] != ''){
					$batchUpload_S3 = $this->batchUpload_S3($value[15], $f_id, $shopcode, 1);
		
					if($batchUpload_S3 == 1){
						$this->insertImage($f_id, 1);
					}
				}
				// upload image 2 to s3 and save to db
				if($value[16] != ''){
					$batchUpload_S3 = $this->batchUpload_S3($value[16], $f_id, $shopcode, 2);
		
					if($batchUpload_S3 == 1){
						$this->insertImage($f_id, 2);
					}
				}
				// upload image 3 to s3 and save to db
				if($value[17] != ''){
					$batchUpload_S3 = $this->batchUpload_S3($value[17], $f_id, $shopcode, 3);
		
					if($batchUpload_S3 == 1){
						$this->insertImage($f_id, 3);
					}
				}
				// upload image 4 to s3 and save to db
				if($value[18] != ''){
					$batchUpload_S3 = $this->batchUpload_S3($value[18], $f_id, $shopcode, 4);
		
					if($batchUpload_S3 == 1){
						$this->insertImage($f_id, 4);
					}
				}
				// upload image 5 to s3 and save to db
				if($value[19] != ''){
					$batchUpload_S3 = $this->batchUpload_S3($value[19], $f_id, $shopcode, 5);
		
					if($batchUpload_S3 == 1){
						$this->insertImage($f_id, 5);
					}
				}
				// upload image 6 to s3 and save to db
				if($value[20] != ''){
					$batchUpload_S3 = $this->batchUpload_S3($value[20], $f_id, $shopcode, 6);
		
					if($batchUpload_S3 == 1){
						$this->insertImage($f_id, 6);
					}
				}
			}
			else{
				$sql_update = " UPDATE `sys_products` SET `cat_id` = ?, `itemname` = ?, `inv_sku` = ?, `inv_barcode` = ?, `summary` = ?, `otherinfo` = ?, `price` = ?, `compare_at_price` = ?, `max_qty_isset` = ?, `max_qty` = ? WHERE `Id` = ?";
		
				$params = array(
					$value[1],
					$value[2],
					$value[8],
					$value[9],
					$value[3],
					$value[4],
					$value[5],
					$value[6],
					$max_qty_isset,
					$value[10],
					$result_select->row_array()['Id']
				);
		
				$result = $this->db->query($sql_update, $params);
		
				if($value[11] != '' && $value[12] != '' && $value[13] != '' && $value[14] != ''){
					$sql = " UPDATE `sys_products_shipping` SET `weight` = ?, `length` = ?, `width` = ?, `height` = ?, `date_updated` = ?WHERE `product_id` = ?";
				
					$params = array(
						$value[11],
						$value[12],
						$value[13],
						$value[14],
						date('Y-m-d H:i:s'),
						$result_select->row_array()['Id']
					);
		
					$this->db->query($sql, $params);
				}
			}			
		} 
		return true;
	}
		
	public function upload_inventory_products_files($sheetData){
		
		foreach(array_slice($sheetData, 1) as $value){
		
			$sql_select = "SELECT * FROM sys_products WHERE inv_barcode = ? AND enabled > 0";
			$params = array(
				$value[2],
			);
		
			$result_select = $this->db->query($sql_select, $params);
			$product_id    = $result_select->row_array()['Id'];
			$shop_id       = $result_select->row_array()['sys_shop'];
		
			if($value[1] == 'Main'){
				$branchid = 0;
			}
			else{
				$sql_branch = "SELECT * FROM sys_branch_profile WHERE branchname = ? AND status > 0";
				$params = array(
					$value[1],
				);
		
				$result_branch = $this->db->query($sql_branch, $params);
				$branchid = $result_branch->row_array()['id'];
				
			}
			
			
			if($result_select->num_rows() == 0){
		
				$sql1 = "INSERT INTO `sys_products_invtrans` (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`)
				VALUES (?, ?, ?, ?, ?, ?, ?)";
		
				$data1 = array(
					$branchid, 
					$product_id, 
					$value[3], 
					'uploaded_file',
					$this->session->userdata('username'), 
					date('Y-m-d H:i:s'), 
					1
				);
				$this->db->query($sql1, $data1);
		
				$sql2 = "INSERT INTO `sys_products_invtrans_branch` (`shopid`,`branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`)
				VALUES (?, ?, ?, ?, ?, ?)";
		
				$data2 = array(
					$shop_id,
					$branchid,
					$product_id, 
					$value[3], 
					date('Y-m-d H:i:s'), 
					1
				);
				$this->db->query($sql2, $data2);
				$insert_checker = 1;
			}
			else{
				$query = "SELECT * FROM sys_products_invtrans_branch WHERE product_id = ? AND branchid = ? AND status = 1";
				$bind_data = array(
					$product_id,
					$branchid
				);
		
				$branch_invtrans = $this->db->query($query, $bind_data);
		
				if($branch_invtrans->num_rows() > 0){
					$query = "UPDATE sys_products_invtrans_branch SET no_of_stocks = ? WHERE product_id = ? AND branchid = ? AND status = 1";
					$bind_data = array(
						$value[3],
						$product_id,
						$branchid
					);	
		
					$this->db->query($query, $bind_data);
				}else{
					$query = "INSERT INTO sys_products_invtrans_branch (`shopid`, `branchid`, `product_id`, `no_of_stocks`, `date_created`, `status`) VALUES (?,?,?,?,?,?) ";
					$bind_data = array(
						$shop_id,
						$branchid,
						$product_id,
						$value[3],
						date('Y-m-d H:i:s'),
						1
					);	
		
					$this->db->query($query, $bind_data);
				}
		
				$query = "SELECT SUM(quantity) as qty_count_stocks FROM sys_products_invtrans WHERE product_id = ? AND branchid = ? AND enabled = 1";
				$bind_data = array(
					$product_id,
					$branchid
				);
		
				$invtrans = $this->db->query($query, $bind_data);
		
				if($invtrans->row()->qty_count_stocks > 0){
					$total_qty = 0;
					$total_qty = $value[3] - $invtrans->row()->qty_count_stocks;
		
					if($total_qty != 0){
						$query = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							$branchid,
							$product_id,
							$total_qty,
							'Update_products_admin_batch',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);
		
						$this->db->query($query, $bind_data);
					}
				}else{
					if($value[3] != 0){
						$query = "INSERT INTO sys_products_invtrans (`branchid`, `product_id`, `quantity`, `type`, `username`, `date_created`,`enabled`) VALUES (?,?,?,?,?,?,?) ";
						$bind_data = array(
							$branchid,
							$product_id,
							$value[3],
							'Update_products_admin_batch',
							$this->session->userdata('username'),
							date('Y-m-d H:i:s'),
							1
						);
		
						$this->db->query($query, $bind_data);
					}
				}
			}
		
			$sql = "SELECT SUM(no_of_stocks) as grand_total_no_of_stocks FROM sys_products_invtrans_branch WHERE product_id = ? AND status = 1";
		
			$bind_data = array(
				$product_id
			);
			$grand_total_no_of_stocks = $this->db->query($sql, $bind_data);
		
			$sql = "SELECT SUM(a.no_of_stocks) as deleted_stocks FROM sys_products_invtrans_branch AS a
			LEFT JOIN sys_branch_profile AS b ON a.branchid = b.id
			WHERE a.product_id = ? AND a.status = 1 AND b.status IN (0, 2)";
		
			$bind_data = array(
				$product_id
			);
			$deleted_stocks = $this->db->query($sql, $bind_data)->row()->deleted_stocks;
		
			$grand_total_no_of_stocks = $grand_total_no_of_stocks->row()->grand_total_no_of_stocks;
			$grand_total              = $grand_total_no_of_stocks - abs($deleted_stocks);
		
			$sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
			$bind_data = array(
				$grand_total,
				$product_id
			);
		
			$this->db->query($sql, $bind_data);
		} 
		
		return true;
	}
		
	public function batchUpload_S3($url, $product_id, $shopcode, $arrangement){
		$this->load->library('s3');
		$this->config->load('s3', TRUE);
		$s3_config         = $this->config->item('s3');
		$bucket_name       = $s3_config['bucket_name'];
		$return_val        = 0;
		$checkURL_valid    = $this->checkURL_valid($url);
		$arrangement_fname = $arrangement - 1;
		
		if($checkURL_valid){
			//upload orig
			$filename = substr($url, strrpos($url, '/') + 1);
			$orig_filename = $arrangement_fname.'-'.$product_id.'.jpg';
			file_put_contents('assets/img/'.$orig_filename, file_get_contents($url));
			$file = 'assets/img/'.$orig_filename;
			$this->s3->putObjectFile($file, $bucket_name, 'assets/img/'.$shopcode.'/products/'.$product_id.'/'.$orig_filename, S3::ACL_PUBLIC_READ);
			unlink('assets/img/'.$orig_filename);
		
			//resized image 40 width
			$filename = substr($url, strrpos($url, '/') + 1);
			$orig_filename = $arrangement_fname.'-'.$product_id.'.jpg';
			file_put_contents('assets/img/'.$orig_filename, file_get_contents($url));
			$getImageDim = getimagesize('assets/img/'.$orig_filename);
			$resizedHeight = intval(getresizedHeight(40, $getImageDim[0], $getImageDim[1]));
			$this->image_resize($orig_filename, 40, $resizedHeight);
			$filename = explode('.', $orig_filename);
			$filename = $filename[0].'_thumb.jpg';
			$file = 'assets/images/'.$filename;
			$this->s3->putObjectFile($file, $bucket_name, 'assets/img/'.$shopcode.'/products-40/'.$product_id.'/'.$orig_filename, S3::ACL_PUBLIC_READ);
			unlink('assets/images/'.$filename);
			unlink('assets/img/'.$orig_filename);
		
			//resized image 50 width
			$filename = substr($url, strrpos($url, '/') + 1);
			$orig_filename = $arrangement_fname.'-'.$product_id.'.jpg';
			file_put_contents('assets/img/'.$orig_filename, file_get_contents($url));
			$getImageDim = getimagesize('assets/img/'.$orig_filename);
			$resizedHeight = intval(getresizedHeight(50, $getImageDim[0], $getImageDim[1]));
			$this->image_resize($orig_filename, 50, $resizedHeight);
			$filename = explode('.', $orig_filename);
			$filename = $filename[0].'_thumb.jpg';
			$file = 'assets/images/'.$filename;
			$this->s3->putObjectFile($file, $bucket_name, 'assets/img/'.$shopcode.'/products-50/'.$product_id.'/'.$orig_filename, S3::ACL_PUBLIC_READ);
			unlink('assets/images/'.$filename);
			unlink('assets/img/'.$orig_filename);
		
			//resized image 250 width
			$filename = substr($url, strrpos($url, '/') + 1);
			$orig_filename = $arrangement_fname.'-'.$product_id.'.jpg';
			file_put_contents('assets/img/'.$orig_filename, file_get_contents($url));
			$getImageDim = getimagesize('assets/img/'.$orig_filename);
			$resizedHeight = intval(getresizedHeight(250, $getImageDim[0], $getImageDim[1]));
			$this->image_resize($orig_filename, 250, $resizedHeight);
			$filename = explode('.', $orig_filename);
			$filename = $filename[0].'_thumb.jpg';
			$file = 'assets/images/'.$filename;
			$this->s3->putObjectFile($file, $bucket_name, 'assets/img/'.$shopcode.'/products-250/'.$product_id.'/'.$orig_filename, S3::ACL_PUBLIC_READ);
			unlink('assets/images/'.$filename);
			unlink('assets/img/'.$orig_filename);
		
			//resized image 520 width
			$filename = substr($url, strrpos($url, '/') + 1);
			$orig_filename = $arrangement_fname.'-'.$product_id.'.jpg';
			file_put_contents('assets/img/'.$orig_filename, file_get_contents($url));
			$getImageDim = getimagesize('assets/img/'.$orig_filename);
			$resizedHeight = intval(getresizedHeight(520, $getImageDim[0], $getImageDim[1]));
			$this->image_resize($orig_filename, 520, $resizedHeight);
			$filename = explode('.', $orig_filename);
			$filename = $filename[0].'_thumb.jpg';
			$file = 'assets/images/'.$filename;
			$this->s3->putObjectFile($file, $bucket_name, 'assets/img/'.$shopcode.'/products-520/'.$product_id.'/'.$orig_filename, S3::ACL_PUBLIC_READ);
			unlink('assets/images/'.$filename);
			unlink('assets/img/'.$orig_filename);
		
			$return_val = 1;
		}
		return $return_val;
	}		
	public function image_resize($filename, $width, $height){
		
		// path to image in your project
		$image_path  = 'assets/img/'.$filename;
		$image_path2 = 'assets/images/'.$filename;
		
		// if image doesn't exist show 404 error
		if(!file_exists($image_path))
			show_404();
		
		// Load image library
		$this->load->library('image_lib');
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $image_path; //get original image
		$config ['new_image'] = $image_path2; //save as new image //need to create thumbs first
		$config ['maintain_ratio'] = false;
		$config ['create_thumb'] = true;
		//$config2 ['overwrite'] = true;
		$config['width'] = $width;
		$config['height'] = $height;
		
		// load configuration
		$this->image_lib->initialize($config);
		
		// resize
		$this->image_lib->resize();
		
		}
		
		public function checkURL_valid($Url=''){
		if ($Url == NULL){
			return false;
		}
		$ch = curl_init($Url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return ($httpcode >= 200 && $httpcode < 300) ? true : false; 
		}
		
		public function insertImage($product_id, $arrangement){
		$sql_img = "INSERT INTO `sys_products_images` (`product_id`,`arrangement`,`filename`,`date_created`,`status`) VALUES (?,?,?,?,?)";
		
		$arrangement_fname = $arrangement - 1;
		$params_img = array(
			$product_id,
			$arrangement,
			$arrangement_fname.'-'.$product_id.'.jpg',
			date('Y-m-d H:i:s'),
			1
		);
		
		$this->db->query($sql_img, $params_img);
	}

    
    # End - Products
}