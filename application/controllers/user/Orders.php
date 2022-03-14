<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {

    public function __construct() {
        parent::__construct();    
        $this->load->model('user/model_orders');
    }

    public function receipt($order_id)
	{	
        $order_id = en_dec('dec',$order_id);

        $order = $this->model_orders->get_order_info($order_id);

        $view_data['order_id'] = $order['order_id'];
        $view_data['order_data'] = json_decode($order['order_data'],true);
        $view_data['payment_data'] = json_decode($order['payment_data'],true);

		$data['active_page'] = 'shop';		
        $data['page_content'] = $this->load->view('user/orders/receipt',$view_data,TRUE);     
		$this->load->view('landing_template',$data,'',TRUE);
	}
    
}