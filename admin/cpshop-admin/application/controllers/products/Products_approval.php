<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Products_approval extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('products/model_products_approval');
        $this->load->model('products/model_products');
        $this->load->model('adhoc_resize/Model_adhoc_resize');
        $this->load->library('uuid');
        $this->load->library('s3_resizeupload');
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
    }

    public function checkProductStatus($product_id)
    {
        $product_status = $this->model_products_approval->checkProductStatus($product_id);
        $product_status = $product_status->enabled;

        if ($product_status == 0) {
            header("location:" . base_url('products/Main_products/display404'));
        }
    }

    public function display404()
    {
        $this->load->view('error_404');
    }

    public function views_restriction($content_url)
    {
        //this code is for destroying session and page if they access restricted page
        $access_content_nav = $this->session->userdata('access_content_nav');
        $arr_ = explode(', ', $access_content_nav); //string comma separated to array
        $get_url_content_db = $this->model->get_url_content_db($arr_)->result_array();
        $url_content_arr = array();
        foreach ($get_url_content_db as $cun) {
            $url_content_arr[] = $cun['cn_url'];
        }

        if (in_array($content_url, $url_content_arr) == false) {
            header("location:" . base_url('Main/logout'));
        } else {
            return $get_main_nav_id_cn_url = $this->model->get_main_nav_id_cn_url($content_url);
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        $this->load->view('login');
    }

    public function postCURL($_url, $_param)
    {

        $postvars = http_build_query($_param);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_url);
        curl_setopt($ch, CURLOPT_POST, count($_param));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close ($ch);
        $response = json_decode($server_output);

        return $response;
    }


    public function postjsonCURL($endpoint, $curl_post_data)
    {
        $curl = curl_init($endpoint);
        
        $data_string =  json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            die('error occured during curl exec. Additional info: ' . var_export($info));
        }
        curl_close($curl);

        // $curl_response = json_decode($curl_response);
        
        return $curl_response;
    }

    public function index()
    {
        if ($this->session->userdata('isLoggedIn') == true) {
            $token_session = $this->session->userdata('token_session');
            $token = en_dec('en', $token_session);

            // $this->load->view(base_url('Main/home/'.$token));
            header("location:" . base_url('Main/home/' . $token));
        }

        $this->load->view('login');
    }


    //waiting for approval


    public function product_waiting_for_approval_table()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

        $query = $this->model_products_approval->product_waiting_for_approval_table($sys_shop, $request);

     
        $response = [
            'success' => true,
            'productArr' => $query
        ];

        echo json_encode($query);

    }

    public function products_waiting_for_approval($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_wfa']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'shops'               => $this->model_products_approval->get_shop_options(),
                'validation'          => $this->loginstate->get_access()['products_wfa'],
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/products_waiting_for_approval', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }




    public function sendProductVerifiedEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['verified_email']);
        $this->email->subject(get_company_name()." | Product Verification Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/products__forVerfiefied_template", $data, TRUE));
        $this->email->send();
    }


    public function product_waiting_for_approval_application_all()
    {

        $ApproveAll    = $this->input->post('product');

        foreach($ApproveAll as $k => $v){
            $this->model_products_approval->product_waiting_for_approval_application($v['product_id']);
            
            $product_ID     = $this->model_products_approval->getProduct($v['product_id']);
            $url = get_apiserver_link().'notify/logProductNotification';
            $data = array(
                'prod_id' => $v['product_id'],
                'itemname' => $product_ID[0]['itemname'],
                'module' => 'Products Waiting for Approval',
                'action' => 'Approved',
                'username' => $this->session->userdata('username'),
                'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$v['product_id'])),
                'link'  => 'products_waiting_for_approval',
            );
            $result = $this->postCURL($url, $data);


            $Get_app_member_details = $this->model_products->getAppMember($this->session->userdata('id'));
            $Get_verified_email = $this->model_products->get_email_settings();
            $data_email = array(
                'itemname'               => $product_ID[0]['itemname'],
                'fname'                  => $Get_app_member_details[0]['fname'],
                'lname'                  => $Get_app_member_details[0]['lname'],
                'verified_email'         => $Get_verified_email[0]['verification_product_email'],
                'verified_name'          => $Get_verified_email[0]['verification_product_name']
            );
            $this->sendProductVerifiedEmail($data_email);

            $get_product = $this->model_products->check_products($v['product_id'])->row();
            $this->audittrail->logActivity('Products Waiting for Approval', $get_product->itemname.' has been approved', 'Approved', $this->session->userdata('username'));
      
        }

        
       
        $response['success'] = true;
        echo json_encode($response);
    }



    public function product_waiting_for_approval_decline_all()
    {

        $ApproveAll    = $this->input->post('product');
        $reason =  $this->input->post('textarea');
        foreach($ApproveAll as $k => $v){
            $this->model_products_approval->product_waiting_for_approval_decline_all($v['product_id'],$reason);


            $product_ID     = $this->model_products_approval->getProduct($v['product_id']);
            $url = get_apiserver_link().'notify/logProductNotification';
            $data = array(
                'prod_id' => $v['product_id'],
                'itemname' => $product_ID[0]['itemname'],
                'module' => 'Products Waiting for Approval',
                'action' => 'Declined',
                'username' => $this->session->userdata('username'),
                'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$v['product_id'])),
                'link'  => 'products_waiting_for_approval',
            );
            $result = $this->postCURL($url, $data);

            $get_product = $this->model_products->check_products($v['product_id'])->row();
            $this->audittrail->logActivity('Products Waiting for Approval', $get_product->itemname.' has been declined', 'Declined', $this->session->userdata('username'));
        }
       
        $response['success'] = true;
        echo json_encode($response);
    }


    public function product_waiting_for_approval_application()
    {

        $prod_id    = sanitize($this->input->post('id'));
        $this->model_products_approval->product_waiting_for_approval_application($prod_id);

        $product_ID     = $this->model_products_approval->getProduct($prod_id);
        $url = get_apiserver_link().'notify/logProductNotification';
        $data = array(
            'prod_id' => $prod_id,
            'itemname' => $product_ID[0]['itemname'],
            'module' => 'Products Waiting for Approval',
            'action' => 'Approved',
            'username' => $this->session->userdata('username'),
            'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$prod_id)),
            'link'  => 'products_waiting_for_approval',
        );
        $result = $this->postCURL($url, $data);


        $Get_app_member_details = $this->model_products->getAppMember($this->session->userdata('id'));
        $Get_verified_email = $this->model_products->get_email_settings($data = 2);
        $data_email = array(
            'itemname'               => $product_ID[0]['itemname'],
            'fname'                  => $Get_app_member_details[0]['fname'],
            'lname'                  => $Get_app_member_details[0]['lname'],
            'verified_email'         => $Get_verified_email[0]['verification_product_email'],
            'verified_name'          => $Get_verified_email[0]['verification_product_name']
        );
        $this->sendProductVerifiedEmail($data_email);


        $get_product = $this->model_products->check_products($prod_id)->row();
        $this->audittrail->logActivity('Products Waiting for Approval', $get_product->itemname.' has been approved', 'Approved', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }


    public function product_waiting_for_approval_application_decline()
    {

        $array_prod
            = array(
                'reason' => sanitize($this->input->post('reason')),
                'id' => sanitize($this->input->post('id'))
            );

        $this->model_products_approval->product_waiting_for_approval_application_decline($array_prod);

        $product_ID     = $this->model_products_approval->getProduct($array_prod['id']);
        $url = get_apiserver_link().'notify/logProductNotification';
        $data = array(
            'prod_id' => $array_prod['id'],
            'itemname' => $product_ID[0]['itemname'],
            'module' => 'Products Waiting for Approval',
            'action' => 'Declined',
            'username' => $this->session->userdata('username'),
            'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$array_prod['id'])),
            'link'  => 'products_waiting_for_approval',
        );
        $result = $this->postCURL($url, $data);

        $get_product = $this->model_products->check_products($prod_id)->row();
        $this->audittrail->logActivity('Products Waiting for Approval', $get_product->itemname.' has been declined', 'Declined', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }


    public function view_products_approval($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_apr']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

            if ($sys_shop == 0) {
                $prev_product = $this->model_products_approval->get_prev_product($this->model_products_approval->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products_approval->get_next_product($this->model_products_approval->get_productdetails($Id)['itemname']);
            } else {
                $prev_product = $this->model_products_approval->get_prev_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products_approval->get_next_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products_approval->get_shop_options(),
                'categories'          => $this->model_products_approval->get_category_options(),
                'get_productdetails'  => $this->model_products_approval->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products_approval->get_sys_branch_profile($this->model_products_approval->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'getVariants'         => $this->model_products_approval->getVariants($Id),
                'getVariantsOption'   => $this->model_products_approval->getVariantsOption($Id)->result_array(),
                'validation'          => $this->loginstate->get_access()['products_wfa']
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/view_products_approval', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }



    //products declined

    public function products_declined_table()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

        $query = $this->model_products_approval->product_declined_table($sys_shop, $request);

     
        $response = [
            'success' => true,
            'productArr' => $query
        ];

        echo json_encode($query);
    }

    public function products_declined($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_dec']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'shops'               => $this->model_products_approval->get_shop_options(),
                'validation'          => $this->loginstate->get_access()['products_dec'],
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/products_declined', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function view_products_declined($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_dec']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

            if ($sys_shop == 0) {
                $prev_product = $this->model_products_approval->get_prev_product($this->model_products_approval->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products_approval->get_next_product($this->model_products_approval->get_productdetails($Id)['itemname']);
            } else {
                $prev_product = $this->model_products_approval->get_prev_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products_approval->get_next_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products_approval->get_shop_options(),
                'categories'          => $this->model_products_approval->get_category_options(),
                'get_productdetails'  => $this->model_products_approval->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products_approval->get_sys_branch_profile($this->model_products_approval->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'getVariants'         => $this->model_products_approval->getVariants($Id),
                'getVariantsOption'   => $this->model_products_approval->getVariantsOption($Id)->result_array(),
                'validation'          => $this->loginstate->get_access()['products_dec']
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/view_products_declined', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }


    public function product_declined_to_approve_application()
    {

        $prod_id    = sanitize($this->input->post('id'));

        $this->model_products_approval->product_declined_to_approved_application($prod_id);


        $get_product = $this->model_products->check_products($prod_id)->row();
        $this->audittrail->logActivity('Products Declined', $get_product->itemname.' has been approved', 'Approved', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }



    public function product_decline_all()
    {

        $ApproveAll    = $this->input->post('product');
        foreach($ApproveAll as $k => $v){
            $this->model_products_approval->product_decline_application_all($v['product_id']);

            $get_product = $this->model_products->check_products($v['product_id'])->row();
            $this->audittrail->logActivity('Products Declined', $get_product->itemname.' has been approved', 'Approved', $this->session->userdata('username'));
        }
       
        $response['success'] = true;
        echo json_encode($response);
    }




    //products approved

    public function products_approved_table()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

        $query = $this->model_products_approval->product_approved_table($sys_shop, $request);

     
        $response = [
            'success' => true,
            'productArr' => $query
        ];

        echo json_encode($query);
    }

    public function products_approved($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_apr']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'shops'               => $this->model_products_approval->get_shop_options(),
                'validation'          => $this->loginstate->get_access()['products_apr']
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/products_approved', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function view_products_approved($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_apr']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

            if ($sys_shop == 0) {
                $prev_product = $this->model_products_approval->get_prev_product($this->model_products_approval->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products_approval->get_next_product($this->model_products_approval->get_productdetails($Id)['itemname']);
            } else {
                $prev_product = $this->model_products_approval->get_prev_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products_approval->get_next_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products_approval->get_shop_options(),
                'categories'          => $this->model_products_approval->get_category_options(),
                'get_productdetails'  => $this->model_products_approval->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products_approval->get_sys_branch_profile($this->model_products_approval->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'getVariants'         => $this->model_products_approval->getVariants($Id),
                'getVariantsOption'   => $this->model_products_approval->getVariantsOption($Id)->result_array(),
                'validation'          => $this->loginstate->get_access()['products_apr']
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/view_products_approved', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }


    public function product_approved_to_verified_application()
    {

        $prod_id    = sanitize($this->input->post('id'));

        $this->model_products_approval->product_approved_to_verified_application($prod_id);

        $product_ID     = $this->model_products_approval->getProduct($prod_id);
        $url = get_apiserver_link().'notify/logProductNotification';
        $data = array(
            'prod_id' => $prod_id,
            'itemname' => $product_ID[0]['itemname'],
            'module' => 'Products Approved',
            'action' => 'Verified',
            'username' => $this->session->userdata('username'),
            'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$prod_id)),
            'link'  => 'products_approved',
        );
        $result = $this->postCURL($url, $data);

        $Get_shop_details = $this->model_products->getSysShopsDetails($product_ID[0]['sys_shop']);
        $data_email = array(
            'itemname'               => $product_ID[0]['itemname'],
            'merchantname'           => $Get_shop_details[0]['shopname'],
            'merchant_email'        =>  $Get_shop_details[0]['email'],
            'shopurl'               =>  $Get_shop_details[0]['shopurl']
        );
        $this->sendProductMerchantEmail($data_email);

        $get_product = $this->model_products->check_products($prod_id)->row();
        $this->audittrail->logActivity('Products Approved', $get_product->itemname.' has been verified', 'Verified', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }


    public function sendProductMerchantEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['merchant_email']);
        $this->email->subject(get_company_name()." | Enabled Products Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/products__merchant_template", $data, TRUE));
        $this->email->send();
    }



    public function product_approved_application_decline()
    {

        $array_prod
            = array(
                'reason' => sanitize($this->input->post('reason')),
                'id' => sanitize($this->input->post('id'))
            );

        $this->model_products_approval->product_approved_application_decline($array_prod);

        $product_ID     = $this->model_products_approval->getProduct($array_prod['id']);
        $url = get_apiserver_link().'notify/logProductNotification';
        $data = array(
            'prod_id' => $array_prod['id'],
            'itemname' => $product_ID[0]['itemname'],
            'module' => 'Products Approved',
            'action' => 'Declined',
            'username' => $this->session->userdata('username'),
            'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$array_prod['id'])),
            'link'  => 'products_approved',
        );
        $result = $this->postCURL($url, $data);

        $get_product = $this->model_products->check_products($array_prod['id'])->row();
        $this->audittrail->logActivity('Products Approved', $get_product->itemname.' has been declined', 'Declined', $this->session->userdata('username'));

        $response['success'] = true;
        echo json_encode($response);
    }


    public function product_approved_to_verify_application_all()
    {

        $ApproveAll    = $this->input->post('product');
        foreach($ApproveAll as $k => $v){
            $this->model_products_approval->product_approved_to_verify_application_all($v['product_id']);

            $product_ID     = $this->model_products_approval->getProduct($v['product_id']);
            $url = get_apiserver_link().'notify/logProductNotification';
            $data = array(
                'prod_id' => $v['product_id'],
                'itemname' => $product_ID[0]['itemname'],
                'module' => 'Products Approved',
                'action' => 'Verified',
                'username' => $this->session->userdata('username'),
                'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$v['product_id'])),
                'link'  => 'products_approved',
            );
            $result = $this->postCURL($url, $data);

           $Get_shop_details = $this->model_products->getSysShopsDetails($product_ID[0]['sys_shop']);
            $data_email = array(
                'itemname'               => $product_ID[0]['itemname'],
                'merchantname'           => $Get_shop_details[0]['shopname'],
                'merchant_email'        =>  $Get_shop_details[0]['email'],
                'shopurl'               =>  $Get_shop_details[0]['shopurl']
            );
            $this->sendProductMerchantEmail($data_email);

            $get_product = $this->model_products->check_products($v['product_id'])->row();
            $this->audittrail->logActivity('Products Approved', $get_product->itemname.' has been verified', 'Verified', $this->session->userdata('username'));
        }
       
        $response['success'] = true;
        echo json_encode($response);
    }

    public function product_approved_to_decline_all()
    {

        $ApproveAll    = $this->input->post('product');
        $reason        = $this->input->post('textarea');
        foreach($ApproveAll as $k => $v){
            $this->model_products_approval->product_waiting_for_approval_decline_all($v['product_id'],$reason);


            $product_ID     = $this->model_products_approval->getProduct($v['product_id']);
            $url = get_apiserver_link().'notify/logProductNotification';
            $data = array(
                'prod_id' => $v['product_id'],
                'itemname' => $product_ID[0]['itemname'],
                'module' => 'Products Approved',
                'action' => 'Declined',
                'username' => $this->session->userdata('username'),
                'signature' => en_dec("en",md5($product_ID[0]['itemname']."PRODUCTSNOTIFICATION".$v['product_id'])),
                'link'  => 'products_approved',
            );
            $result = $this->postCURL($url, $data);

            $get_product = $this->model_products->check_products($v['product_id'])->row();
            $this->audittrail->logActivity('Products Approved', $get_product->itemname.' has been declined', 'Declined', $this->session->userdata('username'));
        }
       
        $response['success'] = true;
        echo json_encode($response);
    }


    //products verified

    public function products_verified_table()
    {
        $this->isLoggedIn();
        $request = $_REQUEST;

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

        $query = $this->model_products_approval->product_verified_table($sys_shop, $request);


        generate_json($query);
    }

    public function products_verified($token = '')
    {
        $this->isLoggedIn();
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_verified']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');

            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'shops'               => $this->model_products_approval->get_shop_options(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/products_verified', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }

    public function view_products_verified($token = '', $Id)
    {
        $this->isLoggedIn();
        $this->checkProductStatus($Id);
        if ($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['products_verified']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

            if ($sys_shop == 0) {
                $prev_product = $this->model_products_approval->get_prev_product($this->model_products_approval->get_productdetails($Id)['itemname']);
                $next_product = $this->model_products_approval->get_next_product($this->model_products_approval->get_productdetails($Id)['itemname']);
            } else {
                $prev_product = $this->model_products_approval->get_prev_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
                $next_product = $this->model_products_approval->get_next_product_per_shop($this->model_products_approval->get_productdetails($Id)['itemname'], $sys_shop);
            }

            $branchid = $this->session->userdata('branchid');

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_products_approval->get_sys_shop($member_id),
                'shopcode'            => $this->model_products_approval->get_shopcode($member_id),
                'prev_product'        => $prev_product,
                'next_product'        => $next_product,
                'Id'                  => $Id,
                'shops'               => $this->model_products_approval->get_shop_options(),
                'categories'          => $this->model_products_approval->get_category_options(),
                'get_productdetails'  => $this->model_products_approval->get_productdetails($Id),
                'get_branchdetails'   => $this->model_products_approval->get_sys_branch_profile($this->model_products_approval->get_productdetails($Id)['sys_shop'], $Id, $branchid),
                'branchid'            => $branchid,
                'getVariants'         => $this->model_products_approval->getVariants($Id),
                'getVariantsOption'   => $this->model_products_approval->getVariantsOption($Id)->result_array()
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('products/view_products_verified', $data_admin);
        } else {
            $this->load->view('error_404');
        }
    }


    






    public function export_product_table()
    {
        $this->isLoggedIn();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $request = url_decode(json_decode($this->input->post("_search")));
        $member_id = $this->session->userdata('sys_users_id');
        $filter_text = "";

        $sys_shop = $this->model_products_approval->get_sys_shop($member_id);

        if ($sys_shop == 0) {
            $query = $this->model_products_approval->product_table($sys_shop, $request, true);
        } else {
            $query = $this->model_products_approval->product_table($sys_shop, $request, true);
        }

        // $_record_status = ($this->input->post('_record_status') == 1 && $this->input->post('_record_status') != '') ? "Enabled":"Disabled";

        if ($this->input->post('_record_status') == '') {
            $_record_status = 'All Records';
        } else if ($this->input->post('_record_status') == 1) {
            $_record_status = 'Enabled';
        } else if ($this->input->post('_record_status') == 2) {
            $_record_status = 'Disabled';
        } else {
            $_record_status = '';
        }

        $_name             = ($this->input->post('_name') == "") ? "" : "'" . $this->input->post('_name') . "'";
        $_shops         = ($this->input->post('_shops') == "") ? "All Shops" : array_get($query, 'data.0.5');

        /// for details column in audit trail
        if ($_name != '') {
            $filter_text .= $_record_status . ' in ' . $_shops . ', Product Name: ' . $_name;
        } else {
            $filter_text .= $_record_status . ' in ' . $_shops;
        }

        $sheet->setCellValue('B1', "Products");
        $sheet->setCellValue('B2', "Filter: '$_name', $_record_status in $_shops");
        $sheet->setCellValue('B3', date('Y/m/d'));

        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $sheet->setCellValue('A6', 'Product Name');
        $sheet->setCellValue('B6', 'Category');
        $sheet->setCellValue('C6', 'Price');
        $sheet->setCellValue('D6', 'No of Stock');
        $sheet->setCellValue('E6', 'Shop Name');
        $sheet->setCellValue('F6', 'Status');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);

        // print_r($query);
        $exceldata = array();
        foreach ($query['data'] as $key => $row) {
            $resultArray = array(
                '1' => $row[1],
                '2' => $row[2],
                '3' => $row[3],
                '4' => $row[4],
                '5' => ucwords($row[5]),
                '6' => ucwords($row[6])
            );
            $exceldata[] = $resultArray;
        }

        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata) + 7;
        for ($i = 7; $i < $row_count; $i++) {
            $sheet->getStyle("C$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Products ' . date('Y/m/d');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Product List', 'Products has been exported into excel with filter ' . $filter_text, 'export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }





    public function get_sys_branch_profile($shop_id)
    {
        $branchid = $this->session->userdata('branchid');
        $result   = $this->model_products_approval->get_sys_branch_profile($shop_id, 0, $branchid);

        if ($result != false) {
            $response = [
                'success'  => true,
                'details'  => $result,
                'branchid' => $branchid
            ];
        } else {
            $response = [
                'success' => false,
                'branchid' => $branchid
            ];
        }

        echo json_encode($response);
    }


    function display_PHPInform()
    {
        phpinfo();
    }


    

    // function Test_email(){


    //     $this->email->from('ovallecera@cloudpanda.ph', 'Oneal Vallecera');
    //     $this->email->to('onealvall123@gmail.com');
    //     $this->email->cc('another@another-example.com');
    //     $this->email->bcc('them@their-example.com');
        
    //     $this->email->subject('Products Update');
    //     $this->email->message('This is to notify you that Oneal Vallecera added a new product Timebomb.');
    //     $this->email->send();
    // }

  
}
