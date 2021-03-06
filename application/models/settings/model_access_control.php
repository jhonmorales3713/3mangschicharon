<?php 
class Model_access_control extends CI_Model {

	public $access_control = array(
    );
    
	private $project_access_control = array(
		//transactions
		'transactions' => array(
			'view' => 0,
			'update' => 0,
		),
		//products
		'products' => array(
			'view' => 0,
			'create' => 0,
			'update' => 0,
			'delete' => 0,
			'disable' => 0
		)
	);


	public function generate_functions($data) {
		// overall access
		if(isset($data['overall_access'])) {
		  	$this->access_control['overall_access'] = 1;
		}
		if(isset($data['seller_access'])) {
		  	$this->access_control['seller_access'] = 1;
		}
		if(isset($data['seller_branch_access'])) {
		  	$this->access_control['seller_branch_access'] = 1;
		}
		// online ordering
		if(isset($data['ac_online_ordering_view'])) {
            $this->access_control['online_ordering'] = 1;
        }
        if(isset($data['ac_online_ordering_view'])) {
                $this->access_control['online_ordering'] = 1;
        }

        //products
        if(isset($data['ac_products_view'])) {
            $this->access_control['products']['view'] = 1;
        }else{
            $this->access_control['products']['view'] = 0;
		}
        if(isset($data['ac_products_create'])) {
            $this->access_control['products']['create'] = 1;
        }else{
            $this->access_control['products']['create'] = 0;
		}
        if(isset($data['ac_products_update'])) {
            $this->access_control['products']['update'] = 1;
        }else{
            $this->access_control['products']['update'] = 0;
		}
        if(isset($data['ac_products_delete'])) {
            $this->access_control['products']['delete'] = 1;
        }else{
            $this->access_control['products']['delete'] = 0;
		}
        if(isset($data['ac_products_disable'])) {
            $this->access_control['products']['disable'] = 1;
        }else{
            $this->access_control['products']['disable'] = 0;
		}
        //variants
        if(isset($data['ac_variants_view'])) {
            $this->access_control['variants']['view'] = 1;
        }else{
            $this->access_control['variants']['view'] = 0;
		}
        if(isset($data['ac_variants_create'])) {
            $this->access_control['variants']['create'] = 1;
        }else{
            $this->access_control['variants']['create'] = 0;
		}
        if(isset($data['ac_variants_update'])) {
            $this->access_control['variants']['update'] = 1;
        }else{
            $this->access_control['variants']['update'] = 0;
		}
        if(isset($data['ac_variants_disable'])) {
            $this->access_control['variants']['disable'] = 1;
        }else{
            $this->access_control['variants']['disable'] = 0;
		}
        if(isset($data['ac_variants_delete'])) {
            $this->access_control['variants']['delete'] = 1;
        }else{
            $this->access_control['variants']['delete'] = 0;
		}
        //products category
        if(isset($data['ac_products_category_view'])) {
            $this->access_control['product_category']['view'] = 1;
        }else{
            $this->access_control['product_category']['view'] = 0;
		}
        if(isset($data['ac_products_category_create'])) {
            $this->access_control['product_category']['create'] = 1;
        }else{
            $this->access_control['product_category']['create'] = 0;
		}
        if(isset($data['ac_products_category_update'])) {
            $this->access_control['product_category']['update'] = 1;
        }else{
            $this->access_control['product_category']['update'] = 0;
		}
        if(isset($data['ac_products_category_disable'])) {
            $this->access_control['product_category']['disable'] = 1;
        }else{
            $this->access_control['product_category']['disable'] = 0;
		}
        if(isset($data['ac_products_category_delete'])) {
            $this->access_control['product_category']['delete'] = 1;
        }else{
            $this->access_control['product_category']['delete'] = 0;
		}

        $this->access_control['profile']['view'] = 1;
        $this->access_control['profile']['update'] = 1;
        //admin user list
        if(isset($data['settings_aul_view'])) {
            $this->access_control['aul']['view'] = 1;
        }else{
            $this->access_control['aul']['view'] = 0;
		}
        if(isset($data['settings_aul_create'])) {
            $this->access_control['aul']['create'] = 1;
        }else{
            $this->access_control['aul']['create'] = 0;
		}
        if(isset($data['settings_aul_update'])) {
            $this->access_control['aul']['update'] = 1;
        }else{
            $this->access_control['aul']['update'] = 0;
		}
        if(isset($data['settings_aul_disable'])) {
            $this->access_control['aul']['disable'] = 1;
        }else{
            $this->access_control['aul']['disable'] = 0;
		}
        if(isset($data['settings_aul_delete'])) {
            $this->access_control['aul']['delete'] = 1;
        }else{
            $this->access_control['aul']['delete'] = 0;
		}
		//website information
        
        if(isset($data['settings_web_view'])) {
            $this->access_control['web']['view'] = 1;
        }else{
            $this->access_control['web']['view'] = 0;
		}
        if(isset($data['settings_web_update'])) {
            $this->access_control['web']['update'] = 1;
        }else{
            $this->access_control['web']['update'] = 0;
		}
        
        //prders
        if(isset($data['ac_transactions_view'])) {
            $this->access_control['orders']['view'] = 1;
        }else{
            $this->access_control['orders']['view'] = 0;
		}
        if(isset($data['ac_transactions_process'])) {
            $this->access_control['orders']['process'] = 1;
        }else{
            $this->access_control['orders']['process'] = 0;
		}
        if(isset($data['ac_transactions_decline'])) {
            $this->access_control['orders']['decline'] = 1;
        }else{
            $this->access_control['orders']['decline'] = 0;
		}
		
        //prders
        if(isset($data['ac_customer_view'])) {
            $this->access_control['customer']['view'] = 1;
        }else{
            $this->access_control['customer']['view'] = 0;
		}
        if(isset($data['ac_customer_update'])) {
            $this->access_control['customer']['update'] = 1;
        }else{
            $this->access_control['customer']['update'] = 0;
		}
        if(isset($data['ac_customer_disable'])) {
            $this->access_control['customer']['disable'] = 1;
        }else{
            $this->access_control['customer']['disable'] = 0;
		}

         
		// //dashboard
		// if(isset($data['ac_dashboard_view'])) {
		// 	$this->access_control['dashboard']['view'] = 1;
		// }
		// if(isset($data['ac_dashboard_sales_count'])) {
		// 	$this->access_control['dashboard']['sales_count_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_transactions_count'])) {
		// 	$this->access_control['dashboard']['transactions_count_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_views_count'])) {
		// 	$this->access_control['dashboard']['views_count_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_overall_sales_count'])) {
		// 	$this->access_control['dashboard']['overall_sales_count_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_visitors_chart'])) {
		// 	$this->access_control['dashboard']['visitors_chart_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_views_chart'])) {
		// 	$this->access_control['dashboard']['views_chart_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_sales_chart'])) {
		// 	$this->access_control['dashboard']['sales_chart_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_top10productsold_list'])) {
		// 	$this->access_control['dashboard']['top10productsold_list_view'] = 1;
		// }
		// if(isset($data['ac_dashboard_transactions_chart'])) {
		// 	$this->access_control['dashboard']['transactions_chart_view'] = 1;
		// }

        //Product discount
        if(isset($data['ac_pd_view'])) {
            $this->access_control['product_discount']['view'] = 1;
        }else{
            $this->access_control['product_discount']['view'] = 0;
		}
        if(isset($data['ac_pd_create'])) {
            $this->access_control['product_discount']['create'] = 1;
        }else{
            $this->access_control['product_discount']['create'] = 0;
		}
        if(isset($data['ac_pd_update'])) {
            $this->access_control['product_discount']['update'] = 1;
        }else{
            $this->access_control['product_discount']['update'] = 0;
		}
        if(isset($data['ac_pd_disable'])) {
            $this->access_control['product_discount']['disable'] = 1;
        }else{
            $this->access_control['product_discount']['disable'] = 0;
		}
        if(isset($data['ac_pd_delete'])) {
            $this->access_control['product_discount']['delete'] = 1;
        }else{
            $this->access_control['product_discount']['delete'] = 0;
		}

        //shipping discount
        
        if(isset($data['ac_shd_view'])) {
            $this->access_control['shipping_discount']['view'] = 1;
        }else{
            $this->access_control['shipping_discount']['view'] = 0;
		}
        if(isset($data['ac_shd_create'])) {
            $this->access_control['shipping_discount']['create'] = 1;
        }else{
            $this->access_control['shipping_discount']['create'] = 0;
		}
        if(isset($data['ac_shd_update'])) {
            $this->access_control['shipping_discount']['update'] = 1;
        }else{
            $this->access_control['shipping_discount']['update'] = 0;
		}
        if(isset($data['ac_shd_disable'])) {
            $this->access_control['shipping_discount']['disable'] = 1;
        }else{
            $this->access_control['shipping_discount']['disable'] = 0;
		}
        if(isset($data['ac_shd_delete'])) {
            $this->access_control['shipping_discount']['delete'] = 1;
        }else{
            $this->access_control['shipping_discount']['delete'] = 0;
		}
        //shop voucher
        
        if(isset($data['ac_sd_view'])) {
            $this->access_control['shop_discount']['view'] = 1;
        }else{
            $this->access_control['shop_discount']['view'] = 0;
		}
        if(isset($data['ac_sd_create'])) {
            $this->access_control['shop_discount']['create'] = 1;
        }else{
            $this->access_control['shop_discount']['create'] = 0;
		}
        if(isset($data['ac_sd_update'])) {
            $this->access_control['shop_discount']['update'] = 1;
        }else{
            $this->access_control['shop_discount']['update'] = 0;
		}
        if(isset($data['ac_sd_disable'])) {
            $this->access_control['shop_discount']['disable'] = 1;
        }else{
            $this->access_control['shop_discount']['disable'] = 0;
		}
        if(isset($data['ac_sd_delete'])) {
            $this->access_control['shop_discount']['delete'] = 1;
        }else{
            $this->access_control['shop_discount']['delete'] = 0;
		}

        
        if(isset($data['ac_sales_report_view'])) {
            $this->access_control['sales_report']['view'] = 1;
        }else{
            $this->access_control['sales_report']['view'] = 0;
		}
        if(isset($data['ac_sales_order_report_view'])) {
            $this->access_control['order_report']['vieww'] = 1;
        }else{
            $this->access_control['order_report']['view'] = 0;
		}
        if(isset($data['ac_sales_top_products_view'])) {
            $this->access_control['top_products']['view'] = 1;
        }else{
            $this->access_control['top_products']['view'] = 0;
		}
        if(isset($data['ac_sales_raw_data_view'])) {
            $this->access_control['sales_report_raw']['view'] = 1;
        }else{
            $this->access_control['sales_report_raw']['view'] = 0;
		}
        if(isset($data['ac_inventory_report_view'])) {
            $this->access_control['inventory_report']['view'] = 1;
        }else{
            $this->access_control['inventory_report']['view'] = 0;
		}
		

		return json_encode($this->access_control);
	}

	public function update_user_functions($args){
		$sql = 'INSERT into `sys_users` (id, functions)
		VALUES '.$args.'

		ON DUPLICATE KEY UPDATE
			functions = VALUES(functions)';
		return $this->db->query($sql);
		// return $this->db->query($sql, $args);
	}
}