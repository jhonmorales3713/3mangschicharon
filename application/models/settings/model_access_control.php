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
		if(isset($data['food_hub_access'])) {
		  	$this->access_control['food_hub_access'] = 1;
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
        }
        if(isset($data['ac_products_create'])) {
            $this->access_control['products']['create'] = 1;
        }
        if(isset($data['ac_products_update'])) {
            $this->access_control['products']['update'] = 1;
        }
        if(isset($data['ac_products_delete'])) {
            $this->access_control['products']['delete'] = 1;
        }
        if(isset($data['ac_products_disable'])) {
            $this->access_control['products']['disable'] = 1;
        }
        //variants
        if(isset($data['ac_variants_view'])) {
            $this->access_control['variants']['view'] = 1;
        }
        if(isset($data['ac_variants_create'])) {
            $this->access_control['variants']['create'] = 1;
        }
        if(isset($data['ac_variants_update'])) {
            $this->access_control['variants']['update'] = 1;
        }
        if(isset($data['ac_variants_disable'])) {
            $this->access_control['variants']['disable'] = 1;
        }
        if(isset($data['ac_variants_delete'])) {
            $this->access_control['variants']['delete'] = 1;
        }
        //admin user list
        if(isset($data['settings_aul_view'])) {
            $this->access_control['aul']['view'] = 1;
        }
        if(isset($data['settings_aul_create'])) {
            $this->access_control['aul']['create'] = 1;
        }
        if(isset($data['settings_aul_update'])) {
            $this->access_control['aul']['update'] = 1;
        }
        if(isset($data['settings_aul_disable'])) {
            $this->access_control['aul']['disable'] = 1;
        }
        if(isset($data['settings_aul_delete'])) {
            $this->access_control['aul']['delete'] = 1;
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