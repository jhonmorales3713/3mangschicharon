<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping extends CI_Controller {

    public function __construct() {
        parent::__construct();        
        $this->load->model('user/model_address');
    }

    public function save_shipping_address()
	{		
        $shipping_address = $this->input->post();

        $customer_id = en_dec('dec',$_SESSION['customer_id']);
        $shipping_address['customer_id'] = $customer_id;

        $this->form_validation->validation_data = $shipping_address;

        //declaration of form validations
        $this->form_validation->set_rules('address_category_id','Address Type','required');
        $this->form_validation->set_rules('full_name','Full Name','required');
        $this->form_validation->set_rules('contact_no','Contact Number','required');
        $this->form_validation->set_rules('province','Province','required');
        $this->form_validation->set_rules('city','City / Municipality','required');
        $this->form_validation->set_rules('barangay','Barangay','required');
        $this->form_validation->set_rules('zip_code','Zip Code','required');
        $this->form_validation->set_rules('address','Street Address','required');

        if($this->form_validation->run() == FALSE) {
            $response = array(
            'success'      => false,
            'message'      => 'Please check for field errors',
            'field_errors' => $this->form_validation->error_array(),              
            );

            generate_json($response);
            die();     
        }       
		
        $shipping_address_id = $this->model_address->insert_address($shipping_address);
		
        $response['success'] = true;
        $response['message'] = 'Address saved successfully';
        $response['address_id'] = en_dec('en',$shipping_address_id);

        generate_json($response);
	}
    
}