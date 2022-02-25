<?php 
class Model_client_information extends CI_Model {

    public function __construct(){
        parent::__construct();  
    }    

    public function get_clients(){
        $this->db = $this->load->database('core', TRUE);

		$sql = "SELECT * FROM cs_clients_info WHERE enabled = 1";
		return $this->db->query($sql);
	}
	
	public function add_client_information($row){
		$this->db = $this->load->database('core', TRUE);
		$this->db->insert('cs_clients_info', $row);
		return $this->db->insert_id();
	}
    
    public function update_client_info(){

    }

    public function get_client_info($c_id){
		$this->db = $this->load->database('core', TRUE);
		$id = $this->db->escape($c_id);
		$sql = "SELECT * FROM cs_clients_info WHERE enabled > 0 and c_id=".$id;
		return $this->db->query($sql);
	}

	public function log_audit_trail($remarks, $action){
		$this->db = $this->load->database('default', TRUE);	
		
		$query  = " INSERT INTO sys_audittrail (module, details, action_type, username, ip_address) 
		VALUES (?, ?, ?, ?, ?) ";

		$params = array(
			"Client Information",
			$remarks,
			$action,
			$this->session->userdata('username'),
			$_SERVER['REMOTE_ADDR']
		);   

		$result = $this->db->query($query, $params);
		return $result;		
	}

	public function deactivate($id) {
		$this->db = $this->load->database('core', TRUE);
		$query="UPDATE cs_clients_info SET enabled = ? WHERE c_id = ?";

		$bind_data = array(
			0,
			$id
		);
		return $this->db->query($query,$bind_data); 
	}

	public function activate($id) {
		$this->db = $this->load->database('core', TRUE);
		$query="UPDATE cs_clients_info SET enabled = ? WHERE c_id = ?";

		$bind_data = array(
			1,
			$id
		);
		return $this->db->query($query,$bind_data); 
	}

	public function get_prev_client_info($c_id) {
		$this->db = $this->load->database('core', TRUE);
		$query="SELECT c_id FROM cs_clients_info WHERE itemname < ? AND enabled = 1 ORDER BY itemname DESC LIMIT 1";
		
		$params = array($itemname);

		if(!empty($this->db->query($query, $params)->row()->c_id)){
			return $this->db->query($query, $params)->row()->c_id;
		}else{
			return 0;
		}
	
	}
	
	public function get_next_client_info($c_id) {
		$this->db = $this->load->database('core', TRUE);
		$query="SELECT c_id FROM cs_clients_info WHERE itemname > ? AND enabled = 1 ORDER BY itemname LIMIT 1";
		
		$params = array($itemname);
		
		if(!empty($this->db->query($query, $params)->row()->c_id)){
			return $this->db->query($query, $params)->row()->c_id;
		}else{
			return 0;
		}
	}

	public function read($id) {
		$this->db = $this->load->database('core', TRUE);
		$query=" SELECT * FROM cs_clients_info WHERE c_id = ? ";
		return $this->db->query($query, $id)->row_array();
	}

    public function client_information_table(){
        //switch db to core
        $this->db = $this->load->database('core', TRUE);

		// storing  request (ie, get/post) global array to a variable  
		$_record_status = $this->input->post('_record_status');
		$c_name 			= $this->input->post('c_name');
		$c_initial 			= $this->input->post('c_initial');
		$token_session 		= $this->session->userdata('token_session');
		$token = en_dec('en', $token_session);

		$requestData = $_REQUEST;

		$columns = array( 
		// datatable column index  => database column name for sorting
			0 => 'c_name',
			1 => 'c_initial',
			2 => 'c_email'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(*) as count FROM cs_clients_info ";
		$query = $this->db->query($sql);
		$totalData = $query->row()->count;
		$totalFiltered = $totalData; 
		//

		$sql = "SELECT * FROM cs_clients_info ";

		
		// start - for default search
		if ($_record_status == 1) {
			$sql.=" WHERE enabled = " . $this->db->escape($_record_status) . "";
		}else if ($_record_status == 2){
			$sql.=" WHERE enabled = 2 ";
		}else{
			$sql.=" WHERE enabled > 0 ";
		}
		// end - for default search

		// getting records as per search parameters
		if($c_name != ""){
			$sql.=" AND c_name LIKE '%" . $this->db->escape_like_str($c_name) . "%' ";
		}
		if($c_initial != ""){
			$sql.=" AND c_initial LIKE '%" . $this->db->escape_like_str($c_initial) . "%' ";
		}
		// if($_description != ""){
		// 	$sql.=" AND cn_description LIKE '%" . $this->db->escape_like_str($_description) . "%' ";
		// }
		// if($_main_nav != ""){
		// 	$sql.=" AND main_nav_id = " . $this->db->escape($_main_nav) . "";
		// }

		$query = $this->db->query($sql);
		$totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length

		$query = $this->db->query($sql);

		$data = array();
		foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
			$nestedData=array(); 
			$nestedData[] = $row["c_name"];
			$nestedData[] = $row["c_initial"];
			$nestedData[] = $row["id_key"];

			if ($row['enabled'] == 1) {
				$record_status = 'Disable';
				$rec_icon = 'fa-ban';
			}else if ($row['enabled'] == 2 || $row['enabled'] == 0) {
				$record_status = 'Enable';
				$rec_icon = 'fa-check-circle';
			}

			$buttons = "";
			if($this->loginstate->get_access()['client_information']['update'] == 1){
				$buttons .= '
				<a class="dropdown-item" data-value="'.$row['c_id'].'" href="'.base_url('Dev_settings_client_information/update_client_information/'.$token.'/'.$row['c_id']).'"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>';
			}

			if($this->loginstate->get_access()['client_information']['disable'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_disable" data-value="'.$row['c_id'].'" data-record_status="'.$row['enabled'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>';
			}

			if($this->loginstate->get_access()['client_information']['delete'] == 1){
				$buttons .= '<div class="dropdown-divider"></div>
				<a class="dropdown-item action_delete " data-value="'.$row['c_id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
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

	//no-color checking returns supplied array when no colors checked
	private function check_no_colors($argsArray){
		if(isset($argsArray['f_header_upper_bg_no_color'])){
			$argsArray['f_header_upper_bg'] = '';
		}
		if(isset($argsArray['f_header_upper_txtcolor_no_color'])){
			$argsArray['f_header_upper_txtcolor'] = '';
		}
		if(isset($argsArray['f_header_middle_bg_no_color'])){
			$argsArray['f_header_middle_bg'] = '';
		}
		if(isset($argsArray['f_header_middle_txtcolor_no_color'])){
			$argsArray['f_header_middle_txtcolor'] = '';
		}
		if(isset($argsArray['f_header_middle_icons_no_color'])){
			$argsArray['f_header_middle_icons'] = '';
		}
		if(isset($argsArray['f_header_bottom_bg_no_color'])){
			$argsArray['f_header_bottom_bg'] = '';
		}
		if(isset($argsArray['f_header_bottom_textcolor_no_color'])){
			$argsArray['f_header_bottom_textcolor'] = '';
		}
		if(isset($argsArray['f_footer_bg_no_color'])){
			$argsArray['f_footer_bg'] = '';
		}
		if(isset($argsArray['f_footer_textcolor_no_color'])){
			$argsArray['f_footer_textcolor'] = '';
		}
		if(isset($argsArray['f_footer_titlecolor_no_color'])){
			$argsArray['f_footer_titlecolor'] = '';
		}
		if(isset($argsArray['f_primaryColor_accent_no_color'])){
			$argsArray['f_primaryColor_accent'] = '';
		}
		return $argsArray;
	}

	public function create($args, $main_logo_filename, $secondary_logo_filename, $placeholder_img_filename, $fb_image_filename, $favicon_filename) {
		//for no-color checking
		$args = $this->check_no_colors($args);
		//$filename = 
		$sql = "INSERT into cs_clients_info (`c_name`, `c_initial`, `id_key`, `c_tag_line`, `c_email`, `c_phone`, `c_auto_email_sender`, `c_social_media_fb_link`, `c_social_media_ig_link`, `c_url_shop_live`, `c_url_shop_test`, `c_url_shop_local`, `c_url_admin_live`, `c_url_admin_test`, `c_url_admin_local`, `c_url_root_live`, `c_url_root_test`, `c_url_root_local`, `c_url_root_segment_live`, `c_url_root_segment_test`, `c_url_root_segment_local`, `c_google_api_key`, `c_apiserver_link_live`, `c_apiserver_link_test`, `c_apiserver_link_local`, `c_s3bucket_link_live`, `c_s3bucket_link_test`, `c_s3bucket_link_local`, `c_cpshop_api_url`, `c_jcfulfillment_shopidno`, `c_privacy_policy`, `c_terms_and_condition`, `c_contact_us`, `c_fb_pixel_id_live`, `c_fb_pixel_id_test`, `c_get_seller_reg_form`, 
		`google_site_verification`, `header_upper_bg`, `header_upper_txtcolor`, `header_middle_bg`, `header_middle_txtcolor`, `header_middle_icons`, `header_bottom_bg`, `header_bottom_textcolor`, `footer_bg`, `footer_textcolor`, `footer_titlecolor`, `primaryColor_accent`, `fontChoice`,
		`c_allow_login`,`c_allow_shop_page`, `c_allow_facebook_login`, `c_allow_gmail_login`, `c_allow_connect_as_online_reseller`, `c_allow_physical_login`, `c_continue_as_guest_button`, 
		`c_allow_registration`, `c_default_order`, `c_allow_sms`, `c_allow_cod`, `c_allow_google_addr`, `c_allow_preorder`, `c_allow_voucher`, `c_allow_toktok_shipping`, `c_main_logo`, `c_secondary_logo`,`c_placeholder_img`, `c_fb_image`, `c_favicon`,
		`c_order_ref_prefix`,`c_order_so_ref_prefix`, `c_seo_website_desc`, `c_inv_threshold`, `c_order_threshold`, `c_comingsoon_password_local`, `c_comingsoon_password_test`, `c_comingsoon_password_live`, 
		`c_ofps`, `c_startup`, `c_jc`, `c_mcjr`, `c_mc`, `c_mcsuper`, `c_mcmega`, `c_others`, `c_toktok_authorization_key`, `c_pusher_app_key_local`, `c_pusher_app_key_test`, `c_pusher_app_key_live`, `c_api_auth_key_local`, `c_api_auth_key_test`, `c_api_auth_key_live`, `c_toktok_api_endpoint`, `c_toktokwallet_api_endpoint`, `c_toktokwallet_authorization_key`,
		`c_international`, `c_allow_pickup`, `c_with_comingsoon_cover_local`, `c_with_comingsoon_cover_test`, `c_with_comingsoon_cover_live`, `c_realtime_notif`, `c_allow_whats_new`, `c_allow_promotions`, `c_allow_following`, `c_shop_main_announcement`, `c_404page`, `c_shop_faqs`, `c_allow_flash_sale`, `c_allow_mystery_coupon`, `c_allow_piso_deal`, `c_allow_categories_section`, `c_allow_promo_featured_items`) 
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ";
		$bind_data = array(
			$args['f_c_name'],
			$args['f_c_initial'],
			$args['f_id_key'],
			$args['f_tag_line'],
			$args['f_email'],
			$args['f_phone'],
			$args['f_auto_email_sender'],
			$args['f_social_media_fb_link'],
			$args['f_social_media_ig_link'],
			$args['f_url_shop_live'],
			$args['f_url_shop_test'],
			$args['f_url_shop_local'],
			$args['f_url_admin_live'],
			$args['f_url_admin_test'],
			$args['f_url_admin_local'],
			$args['f_url_root_live'],
			$args['f_url_root_test'],
			$args['f_url_root_local'],
			$args['f_url_root_segment_live'],
			$args['f_url_root_segment_test'],
			$args['f_url_root_segment_local'],
			$args['c_google_api_key'],
			$args['c_apiserver_link_live'],
			$args['c_apiserver_link_test'],
			$args['c_apiserver_link_local'],
			$args['c_s3bucket_link_live'],
			$args['c_s3bucket_link_test'],
			$args['c_s3bucket_link_local'],
			$args['c_cpshop_api_url'],
			$args['c_jcfulfillment_shopidno'],
			$args['f_privacy_policy'],
			$args['f_terms_and_condition'],
			$args['f_contact_us'],
			$args['f_fb_pixel_id_live'],
			$args['f_fb_pixel_id_test'],
			$args['f_get_seller_reg_form'],
			$args['f_google_site_verification'],
			$args['f_header_upper_bg'],
			$args['f_header_upper_txtcolor'],
			$args['f_header_middle_bg'],
			$args['f_header_middle_txtcolor'],
			$args['f_header_middle_icons'],
			$args['f_header_bottom_bg'],
			$args['f_header_bottom_textcolor'],
			$args['f_footer_bg'],
			$args['f_footer_textcolor'],
			$args['f_footer_titlecolor'],
			$args['f_primaryColor_accent'],
			$args['f_fontChoice'],
			$args['c_allow_login'],
			$args['c_allow_shop_page'],
			$args['c_allow_facebook_login'],
			$args['c_allow_gmail_login'],
			$args['c_allow_connect_as_online_reseller'],
			$args['c_allow_physical_login'],
			$args['c_continue_as_guest_button'],
			$args['c_allow_registration'],
			$args['c_default_order'],
			$args['c_allow_sms'],
			$args['c_allow_cod'],
			$args['c_allow_google_addr'],
			$args['c_allow_preorder'],
			$args['c_allow_voucher'],
			$args['c_allow_toktok_shipping'],
			$main_logo_filename,
			$secondary_logo_filename,
			$placeholder_img_filename,
			$fb_image_filename,
			$favicon_filename,
			$args['c_order_ref_prefix'],
			$args['c_order_so_ref_prefix'],
			$args['c_seo_website_desc'],
			$args['c_inv_threshold'],
			$args['c_order_threshold'],
			$args['c_comingsoon_password_local'],
			$args['c_comingsoon_password_test'],
			$args['c_comingsoon_password_live'],
			$args['f_ofps'],
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			$args['c_toktok_authorization_key'],
			$args['c_pusher_app_key_local'],
			$args['c_pusher_app_key_test'],
			$args['c_pusher_app_key_live'],
			$args['c_api_auth_key_local'],
			$args['c_api_auth_key_test'],
			$args['c_api_auth_key_live'],
			$args['c_toktok_api_endpoint'],
			$args['c_toktokwallet_api_endpoint'],
			$args['c_toktokwallet_authorization_key'],
			$args['c_international'],
			$args['c_allow_pickup'],
			$args['c_with_comingsoon_cover_local'],
			$args['c_with_comingsoon_cover_test'],
			$args['c_with_comingsoon_cover_live'],
			$args['c_realtime_notif'],
			$args['c_allow_whats_new'],
			$args['c_allow_promotions'],
			$args['c_allow_following'],
			$args['c_shop_main_announcement'],
			$args['c_404page'],
			$args['c_shop_faqs'],
			$args['c_allow_flash_sale'],
			$args['c_allow_mystery_coupon'],
			$args['c_allow_piso_deal'],
			$args['c_allow_categories_section'],
			$args['c_allow_promo_featured_items']
		);
		//switch db to core
        $this->db = $this->load->database('core', TRUE);
		return $this->db->query($sql, $bind_data);
	}

	public function update($args, $c_id, $main_logo_filename, $secondary_logo_filename, $placeholder_img_filename, $fb_image_filename, $favicon_filename) {
		//for no-color checking
		$args = $this->check_no_colors($args);

		$sql = "UPDATE cs_clients_info SET c_name = ?, c_initial = ?, c_tag_line = ?, c_email = ?, c_phone = ?, c_auto_email_sender = ?, c_social_media_fb_link = ?, c_social_media_ig_link = ?, c_social_media_youtube_link = ?,  c_faqs_link = ?, c_url_shop_live = ?, c_url_shop_test = ?, c_url_shop_local = ?, c_url_admin_live = ?, c_url_admin_test = ?, c_url_admin_local = ?, c_url_root_live = ?, c_url_root_test = ?, c_url_root_local = ?, c_url_root_segment_live = ?, c_url_root_segment_test = ?, c_url_root_segment_local = ?,  c_google_api_key = ?, c_apiserver_link_live = ?, c_apiserver_link_test = ?, c_apiserver_link_local = ?, c_s3bucket_link_live = ?, c_s3bucket_link_test = ?, c_s3bucket_link_local = ?, c_cpshop_api_url = ?, c_jcfulfillment_shopidno = ?, c_privacy_policy = ?, c_terms_and_condition = ?, c_contact_us = ?, c_fb_pixel_id_live = ?, c_fb_pixel_id_test = ?, c_get_seller_reg_form = ?, google_site_verification = ?, 
		header_upper_bg = ?, header_upper_txtcolor = ?, header_middle_bg = ?, header_middle_txtcolor = ?, header_middle_icons = ?, header_bottom_bg = ?, header_bottom_textcolor = ?, footer_bg = ?, footer_textcolor = ?, footer_titlecolor = ?, primaryColor_accent = ?,
		fontChoice = ?, c_allow_login = ?, c_allow_shop_page = ?, c_allow_facebook_login = ?, c_allow_gmail_login = ?, c_allow_connect_as_online_reseller = ?, c_allow_physical_login = ?, c_continue_as_guest_button = ?, c_allow_registration = ?, c_default_order = ?, c_allow_sms = ?, c_allow_cod = ?, c_allow_google_addr = ?, c_allow_preorder = ?, c_allow_voucher = ?, c_allow_toktok_shipping = ?, c_main_logo = ?, c_secondary_logo = ?, c_placeholder_img = ?, c_fb_image = ?, c_favicon = ?, c_order_ref_prefix = ?,
		c_order_so_ref_prefix = ?, c_seo_website_desc = ?, c_inv_threshold = ?, c_order_threshold = ?, c_comingsoon_password_local = ?, c_comingsoon_password_test = ?, c_comingsoon_password_live = ?, 
		c_ofps = ?, c_startup = ?, c_jc = ?, c_mcjr = ?, c_mc = ?, c_mcsuper = ?, c_mcmega = ?, c_others = ?, c_toktok_authorization_key = ?, c_pusher_app_key_local = ?, c_pusher_app_key_test = ?, c_pusher_app_key_live = ?, c_api_auth_key_local = ?, c_api_auth_key_test = ?, c_api_auth_key_live = ?, c_toktok_api_endpoint = ?,
		c_toktokwallet_api_endpoint = ?, c_toktokwallet_authorization_key = ?, 
		c_international = ?, c_allow_pickup = ?, c_with_comingsoon_cover_local = ?, c_with_comingsoon_cover_test = ?, c_with_comingsoon_cover_live = ?, c_realtime_notif = ?, c_allow_whats_new = ?, c_allow_promotions = ?, c_allow_following = ?, c_shop_main_announcement = ?, c_404page = ?, c_shop_faqs = ?, c_allow_flash_sale = ?, c_allow_mystery_coupon = ?, c_allow_piso_deal = ?, c_allow_categories_section = ?, c_allow_promo_featured_items = ?
		WHERE c_id = ? ";
		$bind_data = array(
			$args['f_name'],
			$args['f_initial'],
			$args['f_tag_line'],
			$args['f_email'],
			$args['f_phone'],
			$args['f_auto_email_sender'],
			$args['f_social_media_fb_link'],
			$args['f_social_media_ig_link'],
			$args['f_social_media_youtube_link'],
			$args['f_faqs_link'],
			$args['f_url_shop_live'],
			$args['f_url_shop_test'],
			$args['f_url_shop_local'],
			$args['f_url_admin_live'],
			$args['f_url_admin_test'],
			$args['f_url_admin_local'],
			$args['f_url_root_live'],
			$args['f_url_root_test'],
			$args['f_url_root_local'],
			$args['f_url_root_segment_live'],
			$args['f_url_root_segment_test'],
			$args['f_url_root_segment_local'],
			$args['c_google_api_key'],
			$args['c_apiserver_link_live'],
			$args['c_apiserver_link_test'],
			$args['c_apiserver_link_local'],
			$args['c_s3bucket_link_live'],
			$args['c_s3bucket_link_test'],
			$args['c_s3bucket_link_local'],
			$args['c_cpshop_api_url'],
			$args['c_jcfulfillment_shopidno'],
			$args['f_privacy_policy'],
			$args['f_terms_and_condition'],
			$args['f_contact_us'],
			$args['f_fb_pixel_id_live'],
			$args['f_fb_pixel_id_test'],
			$args['f_get_seller_reg_form'],
			$args['f_google_site_verification'],
			$args['f_header_upper_bg'],
			$args['f_header_upper_txtcolor'],
			$args['f_header_middle_bg'],
			$args['f_header_middle_txtcolor'],
			$args['f_header_middle_icons'],
			$args['f_header_bottom_bg'],
			$args['f_header_bottom_textcolor'],
			$args['f_footer_bg'],
			$args['f_footer_textcolor'],
			$args['f_footer_titlecolor'],
			$args['f_primaryColor_accent'],
			$args['f_fontChoice'],
			$args['f_allow_login'],
			$args['f_allow_shop_page'],
			$args['f_allow_facebook_login'],
			$args['f_allow_gmail_login'],
			$args['f_allow_connect_as_online_reseller'],
			$args['f_allow_physical_login'],
			$args['f_continue_as_guest_button'],
			$args['f_allow_registration'],
			$args['f_default_order'],
			$args['f_allow_sms'],
			$args['f_allow_cod'],
			$args['c_allow_google_addr'],
			$args['c_allow_preorder'],
			$args['c_allow_voucher'],
			$args['c_allow_toktok_shipping'],
			$main_logo_filename,
			$secondary_logo_filename,
			$placeholder_img_filename,
			$fb_image_filename,
			$favicon_filename,
			$args['c_order_ref_prefix'],
			$args['c_order_so_ref_prefix'],
			$args['c_seo_website_desc'],
			$args['c_inv_threshold'],
			$args['c_order_threshold'],
			$args['c_comingsoon_password_local'],
			$args['c_comingsoon_password_test'],
			$args['c_comingsoon_password_live'],
			$args['f_ofps'],
			$args['f_startup'],
			$args['f_jc'],
			$args['f_mcjr'],
			$args['f_mc'],
			$args['f_mcsuper'],
			$args['f_mcmega'],
			$args['f_others'],
			$args['c_toktok_authorization_key'],
			$args['c_pusher_app_key_local'],
			$args['c_pusher_app_key_test'],
			$args['c_pusher_app_key_live'],
			$args['c_api_auth_key_local'],
			$args['c_api_auth_key_test'],
			$args['c_api_auth_key_live'],
			$args['c_toktok_api_endpoint'],
			$args['c_toktokwallet_api_endpoint'],
			$args['c_toktokwallet_authorization_key'],
			$args['c_international'],
			$args['c_allow_pickup'],
			$args['c_with_comingsoon_cover_local'],
			$args['c_with_comingsoon_cover_test'],
			$args['c_with_comingsoon_cover_live'],
			$args['c_realtime_notif'],
			$args['c_allow_whats_new'],
			$args['c_allow_promotions'],
			$args['c_allow_following'],
			$args['c_shop_main_announcement'],
			$args['c_404page'],
			$args['c_shop_faqs'],
			$args['c_allow_flash_sale'],
			$args['c_allow_mystery_coupon'],
			$args['c_allow_piso_deal'],
			$args['c_allow_categories_section'],
			$args['c_allow_promo_featured_items'],
			$c_id
		);
	
		//switch db to core
        $this->db = $this->load->database('core', TRUE);
		return $this->db->query($sql, $bind_data);
	}

	public function disable_modal_confirm($record_status,$disable_id){
		//switch db to core
        $this->db = $this->load->database('core', TRUE);
		$sql = "UPDATE `cs_clients_info` SET `enabled` = ? WHERE `c_id` = ?";
		$data = array($record_status, $disable_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}
	
	public function delete_modal_confirm($delete_id){
		//switch db to core
        $this->db = $this->load->database('core', TRUE);
		$sql = "UPDATE `cs_clients_info` SET `enabled` = '0' WHERE `c_id` = ?";
		$data = array($delete_id);
		if ($this->db->query($sql, $data)) {
			return 1;
		}else{
			return 0;
		}
	}

	public function get_idkey($id_key) {
		//switch db to core
        $this->db = $this->load->database('core', TRUE);
		$query=" SELECT * FROM cs_clients_info WHERE id_key = ? AND enabled = 1";
		return $this->db->query($query, $id_key);
	}

	public function get_clients_info($c_id){
		//switch db to core
        $this->db = $this->load->database('core', TRUE);
		$query=" SELECT * FROM cs_clients_info WHERE c_id = ?";
		return $this->db->query($query, $c_id);
	}

	public function update_popup_promo($img = '', $link, $popup_enable, $id_key)
	{
		$this->db = $this->load->database('core', TRUE);

		if ($img != '') {
			$query="UPDATE cs_clients_info SET c_popup_img = ? , c_popup_link = ?, c_popup_isset = ? WHERE id_key = ?";

			$bind_data = array(
				$img,
				$link,
				$popup_enable,
				$id_key
			);

			return $this->db->query($query, $bind_data); 
		}

		$query="UPDATE cs_clients_info SET c_popup_link = ?, c_popup_isset = ? WHERE id_key = ?";

		$bind_data = array(
			$link,
			$popup_enable,
			$id_key
		);

		return $this->db->query($query, $bind_data); 
		
	}
}

