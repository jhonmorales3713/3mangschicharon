<?php 
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
			1,
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
					$args['f_no_of_stocks'],
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
					$args['f_no_of_stocks'],
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
				$total_qty = $args['f_no_of_stocks'] - $invtrans->row()->qty_count_stocks;

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
				if($args['f_no_of_stocks'] != 0){
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
		
		// $branchdetails = $this->get_sys_branch_profile($args['f_member_shop'], $f_id, $branchid);

		// if($branchdetails != false){
		// 	foreach($branchdetails as $val){
		// 		if(isset($args['f_no_of_stocks_'.$val['id']])){
		// 			$branch_no_of_stocks = 'f_no_of_stocks_'.$val['id'];
		// 			$this->update_branch_invtrans($args['f_member_shop'], $val['id'], $args[$branch_no_of_stocks], $f_id, 'Add_products_admin');
		// 		}
		// 	}
		// }

		// $sql = "SELECT SUM(quantity) as grand_total_no_of_stocks FROM sys_products_invtrans WHERE product_id = ? AND enabled = 1";
		// $bind_data = array(
		// 	$f_id
		// );

		// $grand_total_no_of_stocks = $this->db->query($sql, $bind_data);

		// $sql = "UPDATE sys_products SET no_of_stocks = ? WHERE Id = ?";
		// $bind_data = array(
		// 	$grand_total_no_of_stocks->row()->grand_total_no_of_stocks,
		// 	$f_id
		// );
		//$this->db->query($sql, $bind_data);

		$sql = "UPDATE sys_products SET img_1 = (SELECT sys_products_images.filename FROM sys_products_images WHERE sys_products_images.product_id = ? AND arrangement = 1 AND status = 1) WHERE Id = ? AND enabled > 0";
		$bind_data = array(
			$f_id,
			$f_id
		);

		return $this->db->query($sql, $bind_data);
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


	public function getParentProduct($id){
		$query="SELECT * FROM sys_products WHERE Id = '$id'";
		return $this->db->query($query)->result_array();
		
	}

	public function getSysShopsDetails($id){
		$query="SELECT * FROM sys_shops WHERE id = '$id' AND status = '1' ";
		return $this->db->query($query)->result_array();
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
		
		// $branchdetails = $this->get_sys_branch_profile($args['f_member_shop'], $f_id, $branchid);

		// if($branchdetails != false){
		// 	foreach($branchdetails as $val){
		// 		//
		// 		if(isset($args['f_no_of_stocks_'.$val['id']])){
		// 			$branch_no_of_stocks = 'f_no_of_stocks_'.$val['id'];
		// 			$this->update_branch_invtrans($args['f_member_shop'], $val['id'], $args[$branch_no_of_stocks], $f_id, 'Add_products_admin');	
		// 		}
		// 	}
		// }


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
	public function getAppMember($id){
		$query="SELECT * FROM app_members WHERE sys_user = '$id' AND status = '1' ";
		return $this->db->query($query)->result_array();
	}
	public function getAppmemberDetails($id){
		$query="SELECT * FROM app_members WHERE sys_shop = '$id' ";
		return $this->db->query($query)->result_array();
	}
	public function getImagesfilename($Id) {
		$query="SELECT * FROM sys_products_images
		WHERE product_id = ? AND status = 1
		ORDER BY arrangement ASC";
		
		$params = array($Id);
		return $this->db->query($query, $params);
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

	public function getFeaturedProduct(){
        $query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ORDER BY set_product_arrangement ASC";
        return $this->db->query($query)->result_array();
    }

	public function checkFeaturedProductArrangement($product_number){
		$query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' AND set_product_arrangement = '$product_number'  AND set_product_arrangement != '0'";
		return $this->db->query($query)->num_rows();
	}
    
    public function get_shopcode_via_shopid($id) {
		$query="SELECT * FROM sys_shops WHERE id = ? AND status > 0";
		$params = array($id);

		return $this->db->query($query, $params)->row()->shopcode;
	}

    public function getFeaturedProductCount(){
        $query="SELECT * FROM sys_products WHERE enabled = '1' AND featured_prod_isset = '1' ";
        return $this->db->query($query)->num_rows();
    }
    public function product_table($sys_shop, $requestData, $exportable = false){
		// storing  request (ie, get/post) global array to a variable  
		$date_from      = $this->input->post('date_from');
		$_record_status = $this->input->post('_record_status');
		$_name 			= $this->input->post('_name');
		$_categories 	= $this->input->post('_categories');
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
		if($_categories != ""){
			$sql.=" AND c.id = " . $this->db->escape($_categories) . "";
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
				<a class="dropdown-item" data-value="'.$row['Id'].'" href="'.base_url('admin/Main_products/view_products/'.$token.'/'.$row['Id']).'"><i class="fa fa-search" aria-hidden="true"></i> View</a>';

			if($this->loginstate->get_access()['products']['update'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item" data-value="'.$row['Id'].'" href="'.base_url('admin/Main_products/update_products/'.$token.'/'.$row['Id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
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
    public function get_shop_options() {
		$query="SELECT * FROM sys_shops WHERE status = 1";
		return $this->db->query($query)->result_array();
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

	public function checkProductStatus($Id) {
		$query=" SELECT enabled FROM sys_products WHERE Id = ?";
		
		$params = array($Id);
		return $this->db->query($query, $params)->row();
	}

    public function get_category_options() {
		$query="SELECT * FROM sys_product_category WHERE status = 1";
		return $this->db->query($query)->result_array();
    }

}