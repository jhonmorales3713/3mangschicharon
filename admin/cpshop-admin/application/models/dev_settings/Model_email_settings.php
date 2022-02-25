<?php 
class Model_email_settings extends CI_Model {

    public function __construct(){
        parent::__construct();  
    }    

 

	public function get_email_settings(){
		$query="SELECT * FROM email_settings";
		return $this->db->query($query)->result_array();
	}



	
	public function emailSettings_update_data($data_email){

     
		$sql = "UPDATE email_settings  SET  `new_product_email` = ?, `new_product_name` = ?, `approval_product_email` = ? , `approval_product_name` = ? , `verification_product_email` = ?, `verification_product_name` = ?, shop_mcr_approval_email = ?, shop_mcr_approval_name = ?,  shop_mcr_verify_email = ?,  shop_mcr_verify_name = ?, shop_mcr_verifed_name = ?, shop_mcr_verifed_email = ? WHERE `id` = ?";
		$data = array( 
		         	$data_email['new_product_email'],
					$data_email['new_product_name'],
					$data_email['new_approval_email'],
					$data_email['new_approval_name'],
					$data_email['new_verification_email'],
					$data_email['new_verification_name'],
					$data_email['shop_mcr_approval_email'],
					$data_email['shop_mcr_approval_name'],
					$data_email['shop_mcr_verification_email'],
					$data_email['shop_mcr_verification_name'],
					$data_email['shop_mcr_verified_name'],
					$data_email['shop_mcr_verified_email'],
					$data_email['email_settings_id'],
			      );

	  

		if ($this->db->query($sql, $data) ) {
				return 1;
		}else{
				return 0;
		}
	
	}



 

}
