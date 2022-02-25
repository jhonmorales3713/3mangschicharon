<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
set_time_limit(0);
ini_set('memory_limit', '2048M');

class Main_orders extends CI_Controller
{
    

    public function __construct()
    {
        parent::__construct();
        //load model or libraries below here...
        $this->load->model('orders/model_orders');
        $this->load->model('products/model_products_approval');
        $this->load->model('adhoc_resize/Model_adhoc_resize');
        $this->load->model('products/model_products');
        $this->load->library('uuid');
        $this->load->library('Paypanda');
        
    }

    public function isLoggedIn()
    {
        if ($this->session->userdata('isLoggedIn') == false) {
            header("location:" . base_url('Main/logout'));
        }
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

    public function delete_modal_confirm()
    {
        $this->isLoggedIn();

        $delete_id = sanitize($this->input->post('delete_id'));

        if ($delete_id === 0) {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        } else {
            $query = $this->model_orders->delete_modal_confirm($delete_id);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Product deleted successfully!");
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }

        }

        generate_json($data);
    }

    public function disable_modal_confirm()
    {
        $this->isLoggedIn();

        $disable_id = sanitize($this->input->post('disable_id'));
        $record_status = sanitize($this->input->post('record_status'));

        if ($record_status == 1) {
            $record_status = 2;
            $record_text = "disabled";
        } else if ($record_status == 2) {
            $record_status = 1;
            $record_text = "enabled";
        } else {
            $record_status = 0;
        }

        if ($disable_id > 0 && $record_status > 0) {
            $query = $this->model_orders->disable_modal_confirm($disable_id, $record_status);

            if ($query == 1) {
                $data = array("success" => 1, 'message' => "Record " . $record_text . " successfully!");
            } else {
                $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
            }
        } else {
            $data = array("success" => 0, 'message' => "Something went wrong, Please Try again!");
        }

        generate_json($data);
    }

    public function orders($token = '')
    {
        $this->isLoggedIn();
        $is_seller = $this->loginstate->get_access()['seller_access'] ?? 0;
        if ($is_seller == 1) {
            $this->load->view('error_404');
        } else if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
                'branchid' 		      => $this->session->userdata('branchid')
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function pending_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['pending_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/pending_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function paid_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['paid_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/paid_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function readyforprocessing_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['readyforprocessing_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/readyforprocessing_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function processing_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['processing_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/processing_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function readyforpickup_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['readyforpickup_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/readyforpickup_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function forpickup_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['forpickup_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/forpickup_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function bookingconfirmed_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['bookingconfirmed_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/bookingconfirmed_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function fulfilled_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['fulfilled_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/fulfilled_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function shipped_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1  || $this->loginstate->get_access()['shipped_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/shipped_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function confirmed_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1  || $this->loginstate->get_access()['confirmed_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/confirmed_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function returntosender_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1  || $this->loginstate->get_access()['returntosender_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/returntosender_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function voided_orders($token = '')
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1  || $this->loginstate->get_access()['voided_orders']['view'] == 1) {
            $content_url = $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/';
            $main_nav_id = $this->views_restriction($content_url);

            $member_id = $this->session->userdata('sys_users_id');
        
            $data_admin = array(
                'token'               => $token,
                'main_nav_id'         => $main_nav_id, //for highlight the navigation
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'shops'               => $this->model_orders->get_shop_options(),
                'regions'             => $this->model_orders->get_regions()->result_array(),
                'provinces'           => $this->model_orders->get_provinces()->result_array(),
                'citymuns'            => $this->model_orders->get_citymuns()->result_array(),
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/voided_orders', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function orders_view($token = '', $ref_num, $order_status_view)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['pending_orders']['view'] == 1 || $this->loginstate->get_access()['paid_orders']['view'] == 1 || $this->loginstate->get_access()['readyforprocessing_orders']['view'] == 1 || $this->loginstate->get_access()['processing_orders']['view'] == 1 || $this->loginstate->get_access()['readyforpickup_orders']['view'] == 1 || $this->loginstate->get_access()['bookingconfirmed_orders']['view'] == 1 || $this->loginstate->get_access()['fulfilled_orders']['view'] == 1 || $this->loginstate->get_access()['shipped_orders']['view'] == 1 || $this->loginstate->get_access()['returntosender_orders']['view'] == 1) {
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_orders->get_sys_shop($member_id);

            if($sys_shop == 0) {
                $split         = explode("-",$ref_num);
                $sys_shop      = $split[0];
                $reference_num = $split[1];
                
                // $date_ordered  = $this->model_orders->get_app_order_details_date_ordered($reference_num);
                // $prev_order    = $this->model_orders->get_prev_orders($reference_num, $date_ordered, $order_status_view);
                // $next_order    = $this->model_orders->get_next_orders($reference_num, $date_ordered, $order_status_view);
                // $prev_order    = (!empty($prev_order)) ? $prev_order[0]['sys_shop'].'-'.$prev_order[0]['reference_num'] : '';
                // $next_order    = (!empty($next_order)) ?  $next_order[0]['sys_shop'].'-'.$next_order[0]['reference_num'] : '';

                if($sys_shop == 0){
                    $get_vouchers = $this->model_orders->get_vouchers($reference_num);
                }else{
                    $get_vouchers = $this->model_orders->get_vouchers_shop($reference_num, $sys_shop);
                }
            }else{
                $reference_num = $ref_num;
                // $date_ordered  = $this->model_orders->get_app_order_details_date_ordered($reference_num);
                // $prev_order    = $this->model_orders->get_prev_orders_per_shop($reference_num, $sys_shop, $date_ordered, $order_status_view);
                // $next_order    = $this->model_orders->get_next_orders_per_shop($reference_num, $sys_shop, $date_ordered, $order_status_view);

                // $prev_order    = (!empty($prev_order)) ? $prev_order[0]['reference_num'] : '';
                // $next_order    = (!empty($next_order)) ? $next_order[0]['reference_num'] : '';
                $get_vouchers  = $this->model_orders->get_vouchers_shop($reference_num, $sys_shop);
            }

            $row            = $this->model_orders->orders_details($reference_num, $sys_shop);
            $refcode        = $this->model_orders->get_referral_code($row['reference_num']);
            $branch_details = $this->model_orders->get_branchname_orders($reference_num, $sys_shop)->row();
            $order_items    = $this->model_orders->order_item_table_print($reference_num, $sys_shop);
            $refundedOrder  = $this->model_orders->get_refundedOrder($reference_num, $sys_shop)->result_array();
            
            if($sys_shop != 0){
                $mainshopname   = $this->model_orders->get_mainshopname_all($sys_shop)->row()->shopname;
                $shop_status    = $this->model_orders->get_mainshopname_all($sys_shop)->row()->status;
                $status_shop    = ($shop_status == 0) ? '(Deleted)':'';
            }else{
                $mainshopname = get_company_name();
                $status_shop  = '';
            }

            $orders_history = $this->model_orders->orders_history($row['order_id']);
            if(!empty($row['app_sales_id'])){
                $orders_history_sales = $this->model_orders->orders_history_sales($row['app_sales_id']);
            }else{
                $orders_history_sales = array();
            }
            $orders_history = array_merge($orders_history_sales, $orders_history);

            if(ini() == 'toktokmart'){
                $modified_order = $this->model_orders->modified_orders($reference_num, $sys_shop);
            }
            else{
                $modified_order = null;
            }

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'partners'            => $this->model_orders->get_partners_options(),
                'partners_api_isset'  => $this->model_orders->get_partners_options_api_isset(),
                'payments'            => $this->model_orders->get_payments_options(),
                'reference_num'       => $reference_num,
                'order_details'       => $row,
                'referral'            => $refcode,
                'branch_details'      => $branch_details,
                'mainshopname'        => $mainshopname,
                'mainshopid'          => $sys_shop,
                'branch_count'        => count($this->model_orders->get_all_branch($sys_shop)->result()),
                'url_ref_num'         => $ref_num,
                'prev_order'          => '',
                'next_order'          => '',
                'orders_history'      => $orders_history,
                'voucher_details'     => $get_vouchers,
                'branchid' 		      => $this->session->userdata('branchid'),
                'order_status_view'   => $order_status_view,
                'refunded_order'      => $refundedOrder,
                'order_items'         => $order_items,
                'status_shop'         => $status_shop,
                'getOrderLogs'        => $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array(),
                'modified_order'      => $modified_order
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/orders_view', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function orders_modify($token = '', $ref_num, $order_status_view)
    {
        $this->isLoggedIn();

        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop  = $this->session->userdata('sys_shop');

        if($sys_shop == 0) {
            $split         = explode("-",$ref_num);
            $sys_shop      = $split[0];
            $reference_num = $split[1];
            
            if($sys_shop == 0){
                $get_vouchers = $this->model_orders->get_vouchers($reference_num);
            }else{
                $get_vouchers = $this->model_orders->get_vouchers_shop($reference_num, $sys_shop);
            }
        }else{
            $reference_num = $ref_num;
            $get_vouchers  = $this->model_orders->get_vouchers_shop($reference_num, $sys_shop);
        }
        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'p');
        if(ini() != 'toktokmart'){
            $this->load->view('error_404');
        }
        else if($checkIfOrderChanged == 1){
            $this->load->view('error_404');
        }
        else if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['modify'] == 1) {
            $row            = $this->model_orders->orders_details($reference_num, $sys_shop);
            $refcode        = $this->model_orders->get_referral_code($row['reference_num']);
            $branch_details = $this->model_orders->get_branchname_orders($reference_num, $sys_shop)->row();
            $order_items    = $this->model_orders->order_item_table_print($reference_num, $sys_shop);
            $refundedOrder  = $this->model_orders->get_refundedOrder($reference_num, $sys_shop)->result_array();
            
            if($sys_shop != 0){
                $mainshopname   = $this->model_orders->get_mainshopname_all($sys_shop)->row()->shopname;
                $shop_status    = $this->model_orders->get_mainshopname_all($sys_shop)->row()->status;
                $status_shop    = ($shop_status == 0) ? '(Deleted)':'';
            }else{
                $mainshopname = get_company_name();
                $status_shop  = '';
            }

            $orders_history = $this->model_orders->orders_history($row['order_id']);
            if(!empty($row['app_sales_id'])){
                $orders_history_sales = $this->model_orders->orders_history_sales($row['app_sales_id']);
            }else{
                $orders_history_sales = array();
            }
            $orders_history = array_merge($orders_history_sales, $orders_history);

            $data_admin = array(
                'token'               => $token,
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'partners'            => $this->model_orders->get_partners_options(),
                'partners_api_isset'  => $this->model_orders->get_partners_options_api_isset(),
                'payments'            => $this->model_orders->get_payments_options(),
                'reference_num'       => $reference_num,
                'order_details'       => $row,
                'referral'            => $refcode,
                'branch_details'      => $branch_details,
                'mainshopname'        => $mainshopname,
                'mainshopid'          => $sys_shop,
                'branch_count'        => count($this->model_orders->get_all_branch($sys_shop)->result()),
                'url_ref_num'         => $ref_num,
                'prev_order'          => '',
                'next_order'          => '',
                'orders_history'      => $orders_history,
                'voucher_details'     => $get_vouchers,
                'branchid' 		      => $this->session->userdata('branchid'),
                'order_status_view'   => $order_status_view,
                'refunded_order'      => $refundedOrder,
                'order_items'         => $order_items,
                'status_shop'         => $status_shop,
                'getOrderLogs'        => $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array()
            );

            $this->load->view('includes/header', $data_admin);
            $this->load->view('orders/orders_modify', $data_admin);
        }else{
            $this->load->view('error_404');
        }
    }

    public function orders_product_modify_table(){
        $sys_shop      = sanitize($this->input->post('sys_shop'));
        $reference_num = sanitize($this->input->post('reference_num'));

        $data = array(
            'sys_shop'      => $sys_shop,
            'reference_num' => $reference_num
        );

        $productArr = $this->model_orders->orders_product_modify_table($data)->result_array();

        $response = [
            'success' => true,
            'productArr' => $productArr
        ];

        echo json_encode($response);
    }

    public function getProduct_perOrder(){

    }

    public function save_modify_orders()
	{
        $sys_shop          = sanitize($this->input->post('sys_shop'));
        $reference_num     = sanitize($this->input->post('reference_num'));
        $productArray      = json_decode($this->input->post('productArray'));
        $productArray      = json_decode(json_encode($productArray), true);
        $totalRefunded     = 0;
        $new_total_amount  = 0;

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'p');

        if(ini() != 'toktokmart'){
            $response['success'] = false;
            $response['message'] = "This feature is for toktokmart only.";
        }
        else if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Order #".$reference_num." has been already processed.";
        }
        else{
            foreach($productArray as $row){
                if($row['is_deleted'] == 0){
                    $new_total_amount         = floatval($row['edit_quantity']) * floatval($row['amount']);
                    $prod_refunded_total_amt  = floatval($new_total_amount) - floatval($row['total_amount']);
                    $totalRefunded += abs($prod_refunded_total_amt);
                }
                if($row['is_deleted'] == 1){
                    $totalRefunded += floatval($row['total_amount']);
                }
            }

            $salesOrder = $this->model_orders->getSalesOrder_modify($reference_num, $sys_shop);

            $proceed = 1;

            if($totalRefunded == 0){
                $proceed = 0;
                $response['success'] = false;
                $response['message'] = "No changes found.";
            }
            else if($salesOrder->payment_method == 'TOKTOKWALLET' && $totalRefunded > 0){
                $url = get_apiserver_link().'toktok/ToktokAPI/giveMoney_toktokwallet';
                $data = array(
                    'sys_shop'      => $sys_shop,
                    'reference_num' => $reference_num,
                    'toktokuser_id' => $salesOrder->toktok_userid,
                    'amount'        => $totalRefunded,
                    'signature'     => en_dec('en',md5('TOKTOKWALLET2021')),
                    'refund_type'   => 'Cancellation'
                );
                $result = $this->postCURL($url, $data);

                if(!empty($result->success)){
                    if($result->success == 1){
                        $proceed = 1;
                    }
                    else{
                        $proceed = 0;
                        $response['success']  = false;
                        $response['message']  = "There's a problem encountered refunding the amount.";
                        $response['response'] = json_encode($result);
                    }
                }
                else{
                    $proceed = 0;
                    $response['success']  = false;
                    $response['message']  = "There's a problem encountered refunding the amount.";
                    $response['response'] = json_encode($result);
                }
            }
            else{
                $proceed = 1;
            }

            if($proceed == 1){
                $this->model_orders->save_modify_orders($productArray, $reference_num, $sys_shop);
                $this->model_orders->delete_modify_orders($productArray, $reference_num, $sys_shop);

                $response['success'] = true;
                $response['message'] = "Order #".$reference_num." has been successfully modified.";

                $this->audittrail->logActivity('Order List', "Order #".$reference_num." has been successfully modified", 'Modify Order', $this->session->userdata('username'));
                $this->model_orders->addOrderHistory($salesOrder->id, 'Order has been successfully modified.', 'Modified Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));

                if($salesOrder->payment_method == 'TOKTOKWALLET'){
                    $shopDetails               = $this->model_orders->read_shop($sys_shop);
                    $order["shopItems"]        = $this->getShopItems($sys_shop, $reference_num);
                    $order["payment_status"]   = "Paid";

                    $checkbranchOrder          = $this->model_orders->checkbranchOrder($reference_num, $sys_shop);

                    if($checkbranchOrder->num_rows() > 0){
                        $order["shop_email"]     = $checkbranchOrder->row_array()['email']; 
                        $order["shop_name"]      = $checkbranchOrder->row_array()['branchname'];
                    }else{
                        $order["shop_email"]     = $shopDetails['email']; 
                        $order["shop_name"]      = $shopDetails['shopname'];
                    }

                    $url = get_apiserver_link().'Email/sendModifiedOrderEmail';
                    $data = array(
                        'sys_shop' => $sys_shop,
                        'reference_num' => $reference_num,
                        'refunded_amt' => $totalRefunded
                    );
                    $result = $this->postCURL($url, $data);
                }
            }
        }

        echo json_encode($response);
	}

    public function check_reassign_item()
    {   
        $mainshopid    = sanitize($this->input->post('mainshopid'));
        $branchid      = sanitize($this->input->post('branchid'));
        $reference_num = sanitize($this->input->post('reference_num'));
        $remarks       = sanitize($this->input->post('remarks'));
        $prev_branchid = sanitize($this->input->post('prev_branchid'));
        $message_str   = "";
        $str_checker   = 0;

        if(!empty($mainshopid) AND !empty($remarks) AND !empty($reference_num)){
            if($branchid != "" || $branchid != null || !empty($branchid)){
                $get_branch_details = $this->model_orders->get_branch_details_id($branchid)->row();
                $items              = $this->model_orders->listShopItems($reference_num, $mainshopid);
                $branch             = $get_branch_details->branchname;

                foreach($items as $row){
                    $details = $this->model_orders->checkinvQty_fromBranch($row['productid'], $branchid, $mainshopid)->row_array();
                    if(!empty($details['no_of_stocks'])){
                        if($row['qty'] > $details['no_of_stocks']){
                            $left         = ($details['no_of_stocks'] == 0) ? "out of stock" : $details['no_of_stocks']." unit/s left";
                            $message_str .= $details['itemname']." - ".$left."<br />";
                            $str_checker  = 1;
                        }
                    }
                }
                
            }else{
                $items   = $this->model_orders->listShopItems($reference_num, $mainshopid);
                $branch  = "Main";

                foreach($items as $row){
                    $details = $this->model_orders->checkinvQty_fromBranch($row['productid'], 0, $mainshopid)->row_array();
                    if(!empty($details['no_of_stocks'])){
                        if($row['qty'] > $details['no_of_stocks']){
                            $left         = ($details['no_of_stocks'] == 0) ? "out of stock" : $details['no_of_stocks']." unit/s left";
                            $message_str .= $details['itemname']." - ".$left."<br />";
                            $str_checker  = 1;
                        }
                    }
                }
                
            }

            if($str_checker == 1){
                $note         = "Note: Branch ".$branch."'s stock is currently low on the following items:";
                $message_str  = $note."<br />".$message_str;
            }
            
            $data = array(
                'success' => 1,
                'message' => $message_str,
                'checker' => $str_checker
            );
        }else{
            $data = array(
                'success' => 0,
                'message' => 'Please Complete All Required Fields',
                'checker' => 0
            );
        }
        return generate_json($data);
    }

    public function getShopItems($sys_shop, $reference_num){
        $shopItems = array();
        $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);

        foreach($items as $item){
            if(empty($shopItems[$sys_shop])) {
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                    $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                }else{
                    $shippingdts['daystoship'] = $shopDetails["daystoship"];
                    $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                }
    
                $shopItems[$sys_shop] = array(
                    "shopname" => $shopDetails["shopname"],
                    "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                    "shopcode" => $shopDetails["shopcode"],
                    "shopaddress" => $shopDetails["address"],
                    "shopemail" => $shopDetails["email"],
                    "shopmobile" => $shopDetails["mobile"],
                    "shopdts" => $shippingdts["daystoship"],
                    "shopdts_to" => $shippingdts["daystoship_to"]
                );

                $shopItems[$sys_shop]["items"] = array();
            }

            array_push($shopItems[$sys_shop]["items"], array(
                "productid" => $item["productid"],
                "itemname" => $item["itemname"],
                "unit" => $item["unit"],
                "quantity" => $item["qty"],
                "price" => $item["amount"]
            ));
        }

        return $shopItems;
    }

    public function reassign_branch()
    {   
        $this->load->library('Sms');
        $mainshopid    = sanitize($this->input->post('mainshopid'));
        $branchid      = sanitize($this->input->post('branchid'));
        $reference_num = sanitize($this->input->post('reference_num'));
        $remarks       = sanitize($this->input->post('remarks'));
        $prev_branchid = sanitize($this->input->post('prev_branchid'));
       

        if(!empty($mainshopid) AND !empty($remarks) AND !empty($reference_num)){
            $this->model_orders->reassign_branch($branchid, $reference_num, $remarks, $mainshopid, $prev_branchid);
            $this->model_orders->reassign_date($reference_num, $mainshopid);

            if($branchid != "" || $branchid != null || !empty($branchid)){
                $get_branch_details = $this->model_orders->get_branch_details_id($branchid)->row();
                $mobile_no          = $get_branch_details->mobileno;
                $message            = 'This is to inform you that order# ' . $reference_num . ' has been transferred to your branch. Please check your portal for other information.';
                $this->sms->sendSMS(3, $mobile_no, $message, 'toktokmall');
                $this->transferOrderEmailBranch($reference_num, $mainshopid, $branchid);
                $salesOrder = $this->model_orders->getSalesOrder($reference_num, $mainshopid);

                $items   = $this->model_orders->listShopItems($reference_num, $mainshopid);
                foreach($items as $row){
                    $this->model_orders->updateInv_fromBranch($row['productid'], $row['qty'], $prev_branchid, $mainshopid);
                    $this->model_orders->updateInv_toBranch($row['productid'], $row['qty'], $branchid, $mainshopid);
                }

                if($prev_branchid != 0){
                    $this->model_orders->update_branch_pending_orders($prev_branchid, '-');
                }

                if($branchid != 0){
                    $this->model_orders->update_branch_pending_orders($branchid, '+');
                }
                
                $branch = $get_branch_details->branchname;
                $this->model_orders->addOrderHistory($salesOrder->id, 'Order has been transferred to '.$get_branch_details->branchname.' Branch.', 'Re Assign', $this->session->userdata('username'), date('Y-m-d H:i:s'));
            }else{
                $salesOrder = $this->model_orders->getSalesOrder($reference_num, $mainshopid);

                $items   = $this->model_orders->listShopItems($reference_num, $mainshopid);
                foreach($items as $row){
                    $this->model_orders->updateInv_fromBranch($row['productid'], $row['qty'], $prev_branchid, $mainshopid);
                    $this->model_orders->updateInv_toBranch($row['productid'], $row['qty'], 0, $mainshopid);
                }

                if($prev_branchid != 0){
                    $this->model_orders->update_branch_pending_orders($prev_branchid, '-');
                }

                $branch = "Main";
                $this->model_orders->addOrderHistory($salesOrder->id, 'Order has been transferred to Main Branch.', 'Re Assign', $this->session->userdata('username'), date('Y-m-d H:i:s'));

                if(ini() == 'jcww'){
                    $url = get_apiserver_link().'api/reassignOrdertoPB';
                    $data = array(
                        'reference_num' => $reference_num,
                        'signature' => en_dec("en", md5($reference_num.'REASSIGNORDERJCWW'))
                    );
                    $result = $this->postCURL($url, $data);
                }
                else if(ini() == 'jconlineshop'){
                    $url = get_apiserver_link().'api/reassignOrdertoPB_JC';
                    $data = array(
                        'reference_num' => $reference_num,
                        'signature' => en_dec("en", md5($reference_num.'REASSIGNORDERJCP'))
                    );
                    $result = $this->postCURL($url, $data);
                }
            }
            
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been transferred to '.$branch, 'Re Assign', $this->session->userdata('username'));
            $data = array(
                'success' => 1,
                'message' => 'Order #'.$reference_num.' has been transferred to '.$branch
            );
        }else{
            $data = array(
                'success' => 0,
                'message' => 'Please Complete All Required Fields'
            );
        }
        return generate_json($data);
    }

    public function get_shop_branches()
    {
        $mainshopid = sanitize($this->input->post('mainshopid'));
        $shopbranches = $this->model_orders->get_all_branch($mainshopid);
        if($shopbranches->num_rows() > 0){
            $data = array(
                "success" => 1,
                "shopbranches" => $shopbranches->result()
            );
        }else{
            $data = array(
                "success" => 0
            );
        }
        generate_json($data);
    }

    public function payOrder()
    {
        
        if($this->input->post('f_payment_ischecked') == TRUE) {

            if($this->input->post('f_payment') != 'Others') {
                $validation = array(
                    array('f_payment','Payment Type','required|max_length[5]|min_length[1]'),
                    array('f_payment_ref_num','Payment Reference Number','required|max_length[50]'),
                    array('f_payment_fee','Payment Amount','required|max_length[10]')
                );
            } else {
                $validation = array(
                    array('f_payment_others','Payment Type','required|max_length[50]|min_length[2]'),
                    array('f_payment_ref_num','Payment Reference Number','required|max_length[50]'),
                    array('f_payment_fee','Payment Amount','required|max_length[10]')
                );
            }
            
            foreach ($validation as $value) {
                $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
            }
        }

        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => $this->response->message('error')
        ];

        if ($this->input->post('f_payment_ischecked') == TRUE && $this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
        } 
        else {
            if($this->input->post('f_payment') != 'Others' && $this->input->post('f_payment_ischecked') == TRUE) {
                $payment_partner = $this->model_orders->get_payment_type($this->input->post('f_payment'))['description'];
            } else {
                $payment_partner = $this->input->post('f_payment_others');
            }

            $reference_number = $this->input->post('f_id-p');
            $user_id          = $this->session->userdata('id');
            $payment_notes    = $this->input->post('f_payment_notes');
            $ifPaid           = $this->model_orders->getPaidOrder($reference_number)->num_rows();
            
            if($ifPaid > 0){
                $response['success'] = false;
                $response['message'] = "Order #".$reference_number." is already paid.";
                echo json_encode($response);
                die();
            }
            else{
                if($this->input->post('f_payment_fee') != ''){
                    $paid_amount    = $this->input->post('f_payment_fee');
                    $paypanda_refno = $this->input->post('f_payment_ref_num');
    
                }else{
                    $paid_amount    = 0;
                    $paypanda_refno = $reference_number;
                }
    
                $signature = $this->paypanda->validate_manual_signature($reference_number, $paypanda_refno, 'S', $paid_amount, 'manual_payment');
    
                $url = get_apiserver_link().'paypanda/postback';
    
                $data = array(
                    'reference_number' => $reference_number, 
                    'paypanda_refno' => $paypanda_refno, 
                    'payment_status' => 'S', 
                    'payment_method' => $payment_partner, 
                    'paid_amount' => $paid_amount, 
                    'signature' => $signature, 
                    'trigger' => 'manual_payment', 
                    'payment_notes' => $payment_notes, 
                    'action_by' => $user_id
                );
    
                $result = $this->postCURL($url, $data);
                // $order_id = $this->model_orders->getAppOrderDetails($reference_number)->order_id;
                
                if($result->status == 'success'){
                    // $this->model_orders->addOrderHistory($order_id, 'Payment for order has been confirmed.', 'Mark as Paid', $this->session->userdata('username'), date('Y-m-d H:i:s'));
                    $this->audittrail->logActivity('Order List', 'Payment for order #'.$reference_number.' has been confirmed', 'Mark as Paid', $this->session->userdata('username'));
                    $response['success'] = true;
                    $response['message'] = $result->message;
                }else{
                    $response['success'] = false;
                    $response['message'] = $result->message;
                }
                echo json_encode($response);
    
            }
        }
    }

    function preparePaymentEmail($reference_num, $shop) 
    {

        //Prepare email
        $items = $this->model_orders->listShopItems($reference_num, $shop);

        $shopItems = array();
        foreach($items as $item){
          $shopDetails = $this->model_orders->read_shop($shop);
          if(empty($shopItems[$shop])) {

            if(count($this->model_orders->get_daystoship($reference_num, $shop)) > 0){
                $shippingdts = $this->model_orders->get_daystoship($reference_num, $shop);    
            }else{
                $shippingdts['daystoship'] = $shopDetails["daystoship"];
                $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
            }

            $shopItems[$shop] = array(
                "shopname" => $shopDetails["shopname"],
                "shopcode" => $shopDetails["shopcode"],
                "shopemail" => $shopDetails["email"],
                "shopmobile" => $shopDetails["mobile"],
                "shopdts" => $shippingdts["daystoship"],
                "shopdts_to" => $shippingdts["daystoship_to"]
            );

            $shopItems[$shop]["items"] = array();
          }

        array_push($shopItems[$shop]["items"], array(
            "productid" => $item["productid"],
            "itemname" => $item["itemname"],
            "unit" => $item["unit"],
            "quantity" => $item["qty"],
            "price" => $item["amount"]
        ));
        }//end foreach

        $orderDetails = $this->model_orders->orders_details($reference_num, $shop);

        $orderDetails["shopItems"] = $shopItems;
        // $orderDetails["delivery_amount"] = $orderDetails["sf"];
        $orderDetails["payment_status"] = "Paid";
        $orderDetails["payment_method"] = "Paid to Seller";

        $this->sendPaymentEmail($orderDetails);
    }
    
    function transferOrderEmailBranch($reference_num, $shop, $branchid)
    {

        //Prepare email
        $items = $this->model_orders->listShopItems($reference_num, $shop);

        $shopItems = array();
        foreach($items as $item){
          $shopDetails = $this->model_orders->read_shop($shop);
          if(empty($shopItems[$shop])) {

            if(count($this->model_orders->get_daystoship($reference_num, $shop)) > 0){
                $shippingdts = $this->model_orders->get_daystoship($reference_num, $shop);    
            }else{
                $shippingdts['daystoship'] = $shopDetails["daystoship"];
                $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
            }

            $shopItems[$shop] = array(
                "shopname" => $shopDetails["shopname"],
                "shopcode" => $shopDetails["shopcode"],
                "shopemail" => $shopDetails["email"],
                "shopmobile" => $shopDetails["mobile"],
                "shopdts" => $shippingdts["daystoship"],
                "shopdts_to" => $shippingdts["daystoship_to"]
            );

            $shopItems[$shop]["items"] = array();
          }

        array_push($shopItems[$shop]["items"], array(
            "productid" => $item["productid"],
            "itemname" => $item["itemname"],
            "unit" => $item["unit"],
            "quantity" => $item["qty"],
            "price" => $item["amount"]
        ));
        }//end foreach

        $orderDetails = $this->model_orders->orders_details($reference_num, $shop);

        $orderDetails["shopItems"] = $shopItems;
        // $orderDetails["delivery_amount"] = $orderDetails["sf"];
        $orderDetails["payment_status"] = "Paid";
        $orderDetails["payment_method"] = "Paid to Seller";
        $orderDetails['branchEmail'] = $this->model_orders->get_branch_details_id($branchid)->row()->email;
        $orderDetails['branchname'] = $this->model_orders->get_branch_details_id($branchid)->row()->branchname;


        // $this->sendPaymentEmailBranch($orderDetails);
        //customer email
        $url = get_apiserver_link().'Email/sendPaymentEmailBranch';
        $data = array(
            'data' => $orderDetails
        );
        $result = $this->postCURL($url, $data);
    }

    function sendPaymentEmail($data)
    {
        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/payment_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendPaymentEmailBranch($data)
    {
        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["branchEmail"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/transferbranch_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    public function processOrder()
    {
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $reference_num = $this->input->post('po_id');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }else{
            $reference_num = $reference_num;
        }

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'p');
       
        if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Unable to cancel booking of Order #".$reference_num.". Order page will reload in a moment.";
        }
        else{
            $success    = $this->model_orders->processOrder($reference_num, $sys_shop);
            $items      = $this->model_orders->listShopItems($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Order is being prepared for shipping by the seller.', 'Process Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            $shopItems = array();
            foreach($items as $item){
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(empty($shopItems[$sys_shop])) {

                    if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                        $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                    }else{
                        $shippingdts['daystoship'] = $shopDetails["daystoship"];
                        $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                    }
        
                    $shopItems[$sys_shop] = array(
                        "shopname" => $shopDetails["shopname"],
                        "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                        "shopcode" => $shopDetails["shopcode"],
                        "shopaddress" => $shopDetails["address"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingdts["daystoship"],
                        "shopdts_to" => $shippingdts["daystoship_to"]
                    );

                    $shopItems[$sys_shop]["items"] = array();
                }

                array_push($shopItems[$sys_shop]["items"], array(
                    "productid" => $item["productid"],
                    "itemname" => $item["itemname"],
                    "unit" => $item["unit"],
                    "quantity" => $item["qty"],
                    "price" => $item["amount"]
                ));
            }

            $order = $this->model_orders->orders_details($reference_num, $sys_shop);
            $order["shopItems"] = $shopItems;
            $order["payment_status"] = "Paid";
            // $this->sendProcessOrderEmail($order);
            //customer email
            $url = get_apiserver_link().'Email/sendProcessOrderEmail';
            $data = array(
                'data' => $order
            );
            $result = $this->postCURL($url, $data);

            //push notif api customer app
            $url = get_apiserver_link().'toktok/ToktokAPI/pushnotifCustomerApp';
            $data = array(
                'sys_shop' => $sys_shop,
                'reference_num' => $reference_num,
                'postback_event' => 'patchOrderProcessing',
                'signature' => en_dec("en",md5($sys_shop."PUSHNOTIFCUSTOMERAPP".$reference_num))
            );
            $result = $this->postCURL($url, $data);

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been tagged as Process Order";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Process Order', 'Process Order', $this->session->userdata('username'));
        }

        echo json_encode($response);
    }

    public function readyPickupOrder()
    {
        $member_id           = $this->session->userdata('sys_users_id');
        $sys_shop            = $this->model_orders->get_sys_shop($member_id);
        $reference_num       = $this->input->post('rp_id');
        $rp_shop_id          = $this->input->post('rp_shop_id');
        $rp_branch_id        = $this->input->post('rp_branch_id');
        $rp_pickup_address   = $this->input->post('rp_pickup_address');
        $rp_shipping_partner = $this->input->post('rp_shipping_partner');
        $loc_latitude        = $this->input->post('loc_latitude');
        $loc_longitude       = $this->input->post('loc_longitude');
        $referralCode        = $this->input->post('rp_referralCode');
        $rp_notes            = $this->input->post('rp_notes');

        if($rp_pickup_address != ''){
            if($sys_shop == 0) {
                $split = explode("-",$reference_num);
                $sys_shop = $split[0];
                $reference_num = $split[1];
            }else{
                $reference_num = $reference_num;
            }

            $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'po');
       
            if($checkIfOrderChanged == 1){
                $response['success'] = false;
                $response['reload']  = 1;
                $response['message'] = "Unable to prepare Order #".$reference_num.". Order page will reload in a moment.";
            }
            else
            {
                $this->model_orders->updateLongLatCustomer($reference_num, $loc_latitude, $loc_longitude, $rp_notes);
                $salesOrder = $this->model_orders->getSalesOrder_book($reference_num, $sys_shop);

                if($rp_shipping_partner != ""){
                    if($rp_shop_id == 0) {
                        $senderDetails = $this->model_orders->get_branch_details_id($rp_branch_id)->row_array();
                        $senderName    = $senderDetails['branchname'];
                        $senderMobile  = $senderDetails['mobileno'];
                        $this->model_orders->updatePickupAddressBranch($rp_branch_id, $rp_pickup_address);
                    }else{
                        $senderDetails = $this->model_orders->get_shop_details_id($sys_shop)->row_array();
                        $senderName    = $senderDetails['shopname'];
                        $senderMobile  = $senderDetails['mobile'];
                        $this->model_orders->updatePickupAddressShop($rp_shop_id, $rp_pickup_address);

                    }

                    $url = get_bookdelivery_link();
                    $data = array(
                        'shipping_partner' => $rp_shipping_partner, 
                        'sys_shop'         => $sys_shop, 
                        'reference_num'    => $reference_num, 
                        'senderName'       => $senderName, 
                        'senderMobile'     => $senderMobile, 
                        'senderDetails'    => $senderDetails,
                        'recipientDetails' => $salesOrder,
                        'referralCode'     => $referralCode,
                        'hash'             => $this->model_orders->getDetailsfromShipping($sys_shop, $reference_num)['hash'],
                        'signature'        => en_dec("en",md5($sys_shop."BOOKDELIVERY".$reference_num))
                    );

                    $audit_string = $this->audittrail->readyforPickupToktokString($rp_shipping_partner, $sys_shop, $reference_num, $senderName, $senderMobile, $senderDetails, $salesOrder, en_dec("en",md5($sys_shop."BOOKDELIVERY".$reference_num)));

                    $bookDeliveryPostBack = $this->postCURL($url, $data);
                    if(!empty($bookDeliveryPostBack->errors)){
                        $response['success'] = false;
                        $response['message'] = $bookDeliveryPostBack->errors[0]->message;
                        echo json_encode($response);
                        die();
                    }
                    else if(empty($bookDeliveryPostBack->data->postDelivery->delivery->deliveryId)){
                        $response['success'] = false;
                        $response['message'] = "API Error.";
                        echo json_encode($response);
                        die();
                    }

                    $deliveryId      = $bookDeliveryPostBack->data->postDelivery->delivery->deliveryId;
                    $shareLink       = $bookDeliveryPostBack->data->postDelivery->delivery->shareLink;
                    $price           = $bookDeliveryPostBack->data->postDelivery->delivery->price;
                    $success         = $this->model_orders->readyPickupOrder_toktok($reference_num, $sys_shop, $price, $deliveryId, $rp_shipping_partner, $shareLink, $referralCode);
                    $this->audittrail->logActivity('Order List', "Order #".$reference_num." has been tagged as Ready for Pickup. \n Passed data:\n".$audit_string, 'Ready for Pickup - Toktok API', $this->session->userdata('username'));
                }
                else{
                    $success = $this->model_orders->readyPickupOrder($reference_num, $sys_shop);
                    $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Ready for Pickup', 'Ready for Pickup', $this->session->userdata('username'));
                }

                $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);
                $this->model_orders->addOrderHistory($salesOrder->id, 'Order is ready for pickup. <u><a href='.$shareLink.' target="_blank"><small>Delivery Link</small></a></u>', 'Ready for Pickup', $this->session->userdata('username'), date('Y-m-d H:i:s'));

                $shopItems = array();
                foreach($items as $item){
                    $shopDetails = $this->model_orders->read_shop($sys_shop);
                    if(empty($shopItems[$sys_shop])) {

                        if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                            $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                        }else{
                            $shippingdts['daystoship'] = $shopDetails["daystoship"];
                            $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                        }
            
                        $shopItems[$sys_shop] = array(
                            "shopname" => $shopDetails["shopname"],
                            "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                            "shopcode" => $shopDetails["shopcode"],
                            "shopaddress" => $shopDetails["address"],
                            "shopemail" => $shopDetails["email"],
                            "shopmobile" => $shopDetails["mobile"],
                            "shopdts" => $shippingdts["daystoship"],
                            "shopdts_to" => $shippingdts["daystoship_to"]
                        );

                        $shopItems[$sys_shop]["items"] = array();
                    }

                    array_push($shopItems[$sys_shop]["items"], array(
                        "productid" => $item["productid"],
                        "itemname" => $item["itemname"],
                        "unit" => $item["unit"],
                        "quantity" => $item["qty"],
                        "price" => $item["amount"]
                    ));
                }

                $order = $this->model_orders->orders_details($reference_num, $sys_shop);
                $order["shopItems"] = $shopItems;
                $order["payment_status"] = "Paid";

                // $this->sendreadyPickupEmail($order);
                //customer email
                $url = get_apiserver_link().'Email/sendreadyPickupEmail';
                $data = array(
                    'data' => $order
                );
                $result = $this->postCURL($url, $data);

                $response['success'] = $success;
                $response['message'] = "Order #".$reference_num." has been tagged as Ready for Pickup";
            }
        }
        else{
            $response['success'] = false;
            $response['message'] = "Pick up address empty";
        }
        echo json_encode($response);
    }

    public function cancelOrder()
    {
        $member_id         = $this->session->userdata('sys_users_id');
        $sys_shop          = $this->model_orders->get_sys_shop($member_id);
        $reference_num     = $this->input->post('cn_id');
        $cancelcat_id      = $this->input->post('cn_cancellation_cat');
        $cancel_notes      = $this->input->post('cn_cancellation_notes');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }else{
            $reference_num = $reference_num;
        }

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'rp');
       
        if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Unable to cancel booking of Order #".$reference_num.". Order page will reload in a moment.";
        }
        else{
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $url = get_cancelDelivery_link();
            $data = array(
                'shipping_partner' => $salesOrder->rp_shipping_partner, 
                'sys_shop'         => $sys_shop, 
                'reference_num'    => $reference_num, 
                'deliveryId'       => $salesOrder->t_deliveryId, 
                'cancelcat_id'     => $cancelcat_id, 
                'cancel_notes'     => $cancel_notes, 
                'signature'        => en_dec("en",md5($sys_shop."CANCELDELIVERY".$reference_num))
            );

            $cancelDeliveryPostBack = $this->postCURL($url, $data);
            
            if(empty($cancelDeliveryPostBack->data->patchDeliveryCancel)){
                $response['success'] = false;
                $response['message'] = "Cancellation of booking failed. API Error.";
                echo json_encode($response);
                die();
            }

            $success = $this->model_orders->cancelOrder($reference_num, $sys_shop);
        
            $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Booking has been cancelled.', 'Cancel Booking', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            $shopItems = array();
            foreach($items as $item){
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(empty($shopItems[$sys_shop])) {

                    if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                        $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                    }else{
                        $shippingdts['daystoship'] = $shopDetails["daystoship"];
                        $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                    }
        
                    $shopItems[$sys_shop] = array(
                        "shopname" => $shopDetails["shopname"],
                        "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                        "shopcode" => $shopDetails["shopcode"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingdts["daystoship"],
                        "shopdts_to" => $shippingdts["daystoship_to"]
                    );

                    $shopItems[$sys_shop]["items"] = array();
                }

                array_push($shopItems[$sys_shop]["items"], array(
                    "productid" => $item["productid"],
                    "itemname" => $item["itemname"],
                    "unit" => $item["unit"],
                    "quantity" => $item["qty"],
                    "price" => $item["amount"]
                ));
            }

            $order = $this->model_orders->orders_details($reference_num, $sys_shop);
            $order["shopItems"] = $shopItems;
            $order["payment_status"] = "Paid";

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been cancelled from booking.";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been cancelled from booking', 'Cancel Booking', $this->session->userdata('username'));
        }

        echo json_encode($response);
    }

    public function getDeliveryCancellationCategories(){

        $member_id         = $this->session->userdata('sys_users_id');
        $sys_shop          = $this->model_orders->get_sys_shop($member_id);
        $reference_num     = $this->input->post('reference_num');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }else{
            $reference_num = $reference_num;
        }

        $CustomerDetails = $this->model_orders->getSalesOrder($reference_num, $sys_shop);

        $url = getDeliveryCancellationCat();
        $data = array(
            'shipping_partner' => $CustomerDetails->rp_shipping_partner,
            'signature'        => en_dec("en",md5("CANCELLATIONCATEGORIES".$CustomerDetails->rp_shipping_partner))
        );

        $getDeliveryCancellationCat = $this->postCURL($url, $data);

        if(empty($getDeliveryCancellationCat)){
            $response['success'] = false;
            $response['message'] = "API Error.";
            echo json_encode($response);
            die();
        }
      
        $response = array(
            'success' => true,
            'message' => 'Success',
            'categories' => $getDeliveryCancellationCat
        );
        
        echo json_encode($response);
    }

    public function bookingConfirmOrder()
    {
        
        $validation = array(
            array('bc_rider_name','Rider\'s Name','required'),
            array('bc_platenum','Rider\'s Plate Number','required'),
            array('bc_conno','Rider\'s Contact Number','required')
        );
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }

        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => $this->response->message('error'),
        ];

        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
        } else {
            $member_id     = $this->session->userdata('sys_users_id');
            $sys_shop      = $this->model_orders->get_sys_shop($member_id);
            $reference_num = $this->input->post('bc_id');
            if($sys_shop == 0) {
                $split = explode("-",$reference_num);
                $sys_shop = $split[0];
                $reference_num = $split[1];
            } else {
                $member_id     = $this->session->userdata('sys_users_id');
                $sys_shop      = $this->model_orders->get_sys_shop($member_id);
            }

            $success = $this->model_orders->bookingConfirmOrder($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Booking of order is confirmed.', 'Booking Confirmed', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            if($this->input->post('bc_rider_ischecked') == TRUE) {
                $this->model_orders->tagRider($this->input->post(), $success);
            }

            //Prepare email
            $items = $this->model_orders->listShopItems($reference_num, $sys_shop);

            $shopItems = array();
            foreach($items as $item){
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(empty($shopItems[$sys_shop])) {

                    if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                        $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                    }else{
                        $shippingdts['daystoship'] = $shopDetails["daystoship"];
                        $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                    }
        
                    $shopItems[$sys_shop] = array(
                        "shopname" => $shopDetails["shopname"],
                        "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                        "shopcode" => $shopDetails["shopcode"],
                        "shopaddress" => $shopDetails["address"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingdts["daystoship"],
                        "shopdts_to" => $shippingdts["daystoship_to"]
                    );

                    $shopItems[$sys_shop]["items"] = array();
                }

                array_push($shopItems[$sys_shop]["items"], array(
                    "productid" => $item["productid"],
                    "itemname" => $item["itemname"],
                    "unit" => $item["unit"],
                    "quantity" => $item["qty"],
                    "price" => $item["amount"]
                ));
            }

            $order = $this->model_orders->orders_details($reference_num, $sys_shop);
            $order["shopItems"] = $shopItems;
            $order["payment_status"] = "Paid";

          
            $order["rider_name"] = $this->input->post('bc_rider_name');
            $order["rider_platenum"] = $this->input->post('bc_platenum');
            $order["rider_conno"] = $this->input->post('bc_conno');
            
            $checkbranchOrder        = $this->model_orders->checkbranchOrder($reference_num, $sys_shop);
            
            if($checkbranchOrder->num_rows() > 0){
                $order["shop_email"]     = $checkbranchOrder->row_array()['email']; 
                $order["shop_name"]      = $checkbranchOrder->row_array()['branchname'];
            }else{
                $order["shop_email"]     = $shopDetails['email']; 
                $order["shop_name"]      = $shopDetails['shopname'];
            }

            // $this->sendbookingConfirmEmail($order);
            // $this->sendbookingConfirmEmail_shop($order);
            //customer email
            $url = get_apiserver_link().'Email/sendbookingConfirmEmail';
            $data = array(
                'data' => $order
            );
            $result = $this->postCURL($url, $data);

            $url = get_apiserver_link().'Email/sendbookingConfirmEmail_shop';
            $data = array(
                'data' => $order
            );
            $result = $this->postCURL($url, $data);

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been tagged as Booking Confirmed.";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Booking Confirmed', 'Booking Confirmed', $this->session->userdata('username'));

        }
        echo json_encode($response);
    }

    public function FulfilledOrder()
    {
        if($this->input->post('mf_rider_name') == '' && $this->input->post('mf_platenum') == '' && $this->input->post('mf_conno') == '') {
        }
        else if($this->input->post('mf_rider_name') != '' && $this->input->post('mf_platenum') != '' && $this->input->post('mf_conno') != '') {
        }
        else{
            $response['success'] = false;
            $response['message'] = 'Please complete rider details';
            echo json_encode($response);
            die();
        }

        $validation = array(
            array('f_shipping','Shipping Partner','required'),
            array('f_shipping_ref_num','Shipping Reference Number','required'),
        );
        
        foreach ($validation as $value) {
            $this->form_validation->set_rules($value[0],$value[1],$value[2], (count($value) > 3 ? $value[3] : ''));
        }

        $response = [
            'environment' => ENVIRONMENT,
            'success'     => false,
            'message'     => $this->response->message('error'),
        ];

        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
            echo json_encode($response);
            die();
        }else{
            $member_id     = $this->session->userdata('sys_users_id');
            $sys_shop      = $this->model_orders->get_sys_shop($member_id);
            $reference_num = $this->input->post('f_id');
            if($sys_shop == 0) {
                $split = explode("-",$reference_num);
                $sys_shop = $split[0];
                $reference_num = $split[1];
            } else {
                $member_id     = $this->session->userdata('sys_users_id');
                $sys_shop      = $this->model_orders->get_sys_shop($member_id);
            }

            $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged_fulfilled($sys_shop, $reference_num);
            
            if($checkIfOrderChanged == 1){
                $response['success'] = false;
                $response['message'] = "Unable to fulfill Order #".$reference_num.". Please reload the page.";
            }
            else{
                // if($this->input->post('f_shipping_ischecked') == TRUE) {
                    $this->model_orders->tagShipping($reference_num, $this->input->post(), $sys_shop);
                // }

                $success = $this->model_orders->FulfilledOrder($reference_num, $sys_shop);
                $branchid_if_assigned = $this->model_orders->get_branchname_id($reference_num, $sys_shop);

                if($branchid_if_assigned != 0){
                    $this->model_orders->update_branch_pending_orders($branchid_if_assigned, '-');
                }

                if($this->input->post('mf_rider_ischecked') == TRUE) {
                    $this->model_orders->mf_tagRider($this->input->post(), $success);
                }

                $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
                $this->model_orders->addOrderHistory($salesOrder->id, 'Order is being delivered.', 'Mark as Fulfilled', $this->session->userdata('username'), date('Y-m-d H:i:s'));

                //Prepare email
                $items = $this->model_orders->listShopItems($reference_num, $sys_shop);

                $shopItems = array();
                foreach($items as $item){
                    $shopDetails = $this->model_orders->read_shop($sys_shop);
                    if(empty($shopItems[$sys_shop])) {

                        if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                            $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                        }else{
                            $shippingdts['daystoship'] = $shopDetails["daystoship"];
                            $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                        }
            
                        $shopItems[$sys_shop] = array(
                            "shopname" => $shopDetails["shopname"],
                            "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                            "shopcode" => $shopDetails["shopcode"],
                            "shopaddress" => $shopDetails["address"],
                            "shopemail" => $shopDetails["email"],
                            "shopmobile" => $shopDetails["mobile"],
                            "shopdts" => $shippingdts["daystoship"],
                            "shopdts_to" => $shippingdts["daystoship_to"]
                        );

                        $shopItems[$sys_shop]["items"] = array();
                    }

                    array_push($shopItems[$sys_shop]["items"], array(
                        "productid" => $item["productid"],
                        "itemname" => $item["itemname"],
                        "unit" => $item["unit"],
                        "quantity" => $item["qty"],
                        "price" => $item["amount"]
                    ));
                }

                $order = $this->model_orders->orders_details($reference_num, $sys_shop);
                $order["shopItems"] = $shopItems;
                $order["payment_status"] = "Paid";

                if($this->input->post('f_shipping') != 'Others'){
                    $shippingname = $this->model_orders->get_shipping_partner($this->input->post('f_shipping'));
                }
                else{
                    $shippingname = $this->input->post('f_shipping_others');
                }

                $order["rider_name"]       = $this->input->post('mf_rider_name');
                $order["rider_platenum"]   = $this->input->post('mf_platenum');
                $order["rider_conno"]      = $this->input->post('mf_conno');
                $order["delivery_info"]    = $shippingname;
                $order["delivery_ref_num"] = $this->input->post('f_shipping_ref_num');

                // $this->sendFulfillmentEmail($order);
                //customer email
                $url = get_apiserver_link().'Email/sendFulfillmentEmail';
                $data = array(
                    'data' => $order
                );
                $result = $this->postCURL($url, $data);

                //push notif api customer app
                $url = get_apiserver_link().'toktok/ToktokAPI/pushnotifCustomerApp';
                $data = array(
                    'sys_shop' => $sys_shop,
                    'reference_num' => $reference_num,
                    'postback_event' => 'patchOrderFulfilled',
                    'signature' => en_dec("en",md5($sys_shop."PUSHNOTIFCUSTOMERAPP".$reference_num))
                );
                $result = $this->postCURL($url, $data);

                $response['success'] = $success;
                $response['message'] = "Order #".$reference_num." has been tagged as Fulfilled.";
                //removed this sms
                // $this->load->library('Sms');
                // $mobile_no = $salesOrder->conno;
                // $message  = 'Our rider will attempt to deliver your order #' . $reference_num . ' today.';
                // $this->sms->sendSMS(1, $mobile_no, $message, 'toktokmall');
                $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Fulfilled', 'Mark as Fulfilled', $this->session->userdata('username'));
            }
        }

        echo json_encode($response);
    }

    public function returntosenderOrder()
    {
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $reference_num = $this->input->post('rs_id');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'f');
       
        if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Unable to return to sender Order #".$reference_num.". Order page will reload in a moment.";
        }
        else{
            $success = $this->model_orders->returntosenderOrder($reference_num, $sys_shop);
            $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Order has been returned to sender.', 'Return to Sender', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            $shopItems = array();
            foreach($items as $item){
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(empty($shopItems[$sys_shop])) {

                    if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                        $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                    }else{
                        $shippingdts['daystoship'] = $shopDetails["daystoship"];
                        $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                    }
        
                    $shopItems[$sys_shop] = array(
                        "shopname" => $shopDetails["shopname"],
                        "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                        "shopcode" => $shopDetails["shopcode"],
                        "shopaddress" => $shopDetails["address"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingdts["daystoship"],
                        "shopdts_to" => $shippingdts["daystoship_to"]
                    );

                    $shopItems[$sys_shop]["items"] = array();
                }

                array_push($shopItems[$sys_shop]["items"], array(
                    "productid" => $item["productid"],
                    "itemname" => $item["itemname"],
                    "unit" => $item["unit"],
                    "quantity" => $item["qty"],
                    "price" => $item["amount"]
                ));
            }

            $order = $this->model_orders->orders_details($reference_num, $sys_shop);
            $order["shopItems"] = $shopItems;
            $order["payment_status"] = "Paid";
            $order["shop_email"] = $shopDetails['email'];
            $order["shop_name"] = $shopDetails['shopname'];

            // $this->sendreturntosenderOrder($order);
            //customer email
            $url = get_apiserver_link().'Email/sendreturntosenderOrder';
            $data = array(
                'data' => $order
            );
            $result = $this->postCURL($url, $data);

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been tagged as Return to Sender.";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Return to Sender', 'Return to Sender', $this->session->userdata('username'));
        }

        echo json_encode($response);
    }

    public function redeliverOrder()
    {
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $reference_num = $this->input->post('rd_id');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'rs');

        if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Unable to re-deliver Order #".$reference_num.". Order page will reload in a moment.";
        }
        else{
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $success = $this->model_orders->redeliverOrder($reference_num, $sys_shop, $salesOrder->id);
            $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Order will re-deliver.', 'ReDeliver Order', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            $shopItems = array();
            foreach($items as $item){
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(empty($shopItems[$sys_shop])) {

                    if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                        $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                    }else{
                        $shippingdts['daystoship'] = $shopDetails["daystoship"];
                        $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                    }
        
                    $shopItems[$sys_shop] = array(
                        "shopname" => $shopDetails["shopname"],
                        "shopcode" => $shopDetails["shopcode"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingdts["daystoship"],
                        "shopdts_to" => $shippingdts["daystoship_to"]
                    );

                    $shopItems[$sys_shop]["items"] = array();
                }

                array_push($shopItems[$sys_shop]["items"], array(
                    "productid" => $item["productid"],
                    "itemname" => $item["itemname"],
                    "unit" => $item["unit"],
                    "quantity" => $item["qty"],
                    "price" => $item["amount"]
                ));
            }

            $order = $this->model_orders->orders_details($reference_num, $sys_shop);
            $order["shopItems"] = $shopItems;
            $order["payment_status"] = "Paid";
            $order["shop_email"] = $shopDetails['email'];
            $order["shop_name"] = $shopDetails['shopname'];

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been tagged as Re-Deliver Order.";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged Re-Deliver Order', 'ReDeliver Order', $this->session->userdata('username'));
        }

        echo json_encode($response);
    }

    public function shippedOrder()
    {
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $reference_num = $this->input->post('s_id');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 'f');

        if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Unable to ship Order #".$reference_num.". Order page will reload in a moment.";
        }
        else{
            $success = $this->model_orders->shippedOrder($reference_num, $sys_shop);
            $items   = $this->model_orders->listShopItems($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Order has been successfully shipped.', 'Mark as Shipped', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            $shopItems = array();
            foreach($items as $item){
                $shopDetails = $this->model_orders->read_shop($sys_shop);
                if(empty($shopItems[$sys_shop])) {

                    if(count($this->model_orders->get_daystoship($reference_num, $sys_shop)) > 0){
                        $shippingdts = $this->model_orders->get_daystoship($reference_num, $sys_shop);    
                    }else{
                        $shippingdts['daystoship'] = $shopDetails["daystoship"];
                        $shippingdts['daystoship_to'] = $shopDetails["daystoship"];
                    }
        
                    $shopItems[$sys_shop] = array(
                        "shopname" => $shopDetails["shopname"],
                        "branchname" => $this->model_orders->get_branchname($reference_num, $sys_shop),
                        "shopcode" => $shopDetails["shopcode"],
                        "shopaddress" => $shopDetails["address"],
                        "shopemail" => $shopDetails["email"],
                        "shopmobile" => $shopDetails["mobile"],
                        "shopdts" => $shippingdts["daystoship"],
                        "shopdts_to" => $shippingdts["daystoship_to"]
                    );

                    $shopItems[$sys_shop]["items"] = array();
                }

                array_push($shopItems[$sys_shop]["items"], array(
                    "productid" => $item["productid"],
                    "itemname" => $item["itemname"],
                    "unit" => $item["unit"],
                    "quantity" => $item["qty"],
                    "price" => $item["amount"]
                ));
            }

            $order = $this->model_orders->orders_details($reference_num, $sys_shop);
            $order["shopItems"]      = $shopItems;
            $order["payment_status"] = "Paid";

            $checkbranchOrder        = $this->model_orders->checkbranchOrder($reference_num, $sys_shop);
            
            if($checkbranchOrder->num_rows() > 0){
                $order["shop_email"]     = $checkbranchOrder->row_array()['email']; 
                $order["shop_name"]      = $checkbranchOrder->row_array()['branchname'];
            }else{
                $order["shop_email"]     = $shopDetails['email']; 
                $order["shop_name"]      = $shopDetails['shopname'];
            }

            // $this->sendShippedOrder($order);
            // $this->sendShippedOrder_Shop($order);
            //customer email
            $url = get_apiserver_link().'Email/sendShippedOrder';
            $data = array(
                'data' => $order
            );
            $result = $this->postCURL($url, $data);

            $url = get_apiserver_link().'Email/sendShippedOrder_Shop';
            $data = array(
                'data' => $order
            );
            $result = $this->postCURL($url, $data);

            if(ini() == 'toktokmall'){
                ///jc referral codes api
                $url = get_apiserver_link().'postback/Orders/jc_referral_codes_api';
                $data = array(
                    'reference_number' => $reference_num
                );
                $result = $this->postCURL($url, $data);
            }

            if($order['rider_name'] != ''){
                //push notif api customer app
                $url = get_apiserver_link().'toktok/ToktokAPI/pushnotifCustomerApp';
                $data = array(
                    'sys_shop' => $sys_shop,
                    'reference_num' => $reference_num,
                    'postback_event' => 'patchOrderCompleted',
                    'signature' => en_dec("en",md5($sys_shop."PUSHNOTIFCUSTOMERAPP".$reference_num))
                );
                $result = $this->postCURL($url, $data);
            }
            else{
                //push notif api customer app
                $url = get_apiserver_link().'toktok/ToktokAPI/pushnotifCustomerApp';
                $data = array(
                    'sys_shop' => $sys_shop,
                    'reference_num' => $reference_num,
                    'postback_event' => 'patchOrderShipped',
                    'signature' => en_dec("en",md5($sys_shop."PUSHNOTIFCUSTOMERAPP".$reference_num))
                );
                $result = $this->postCURL($url, $data);
            }

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been tagged as Shipped.";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Shipped', 'Mark as Shipped', $this->session->userdata('username'));
        }
        echo json_encode($response);
    }

    public function confirmedOrder()
    {
        $member_id      = $this->session->userdata('sys_users_id');
        $sys_shop       = $this->model_orders->get_sys_shop($member_id);
        $reference_num  = $this->input->post('oc_id');

        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }

        $checkIfOrderChanged = $this->model_orders->checkIfOrderChanged($sys_shop, $reference_num, 's');

        if($checkIfOrderChanged == 1){
            $response['success'] = false;
            $response['message'] = "Unable to ship Order #".$reference_num.". Order page will reload in a moment.";
        }
        else{
            $success = $this->model_orders->confirmedOrder($reference_num, $sys_shop);
            $salesOrder = $this->model_orders->getSalesOrder($reference_num, $sys_shop);
            $this->model_orders->addOrderHistory($salesOrder->id, 'Order has been successfully confirmed.', 'Order Confirmed', $this->session->userdata('username'), date('Y-m-d H:i:s'));

            $response['success'] = $success;
            $response['message'] = "Order #".$reference_num." has been tagged as Confirmed.";
            $this->audittrail->logActivity('Order List', 'Order #'.$reference_num.' has been tagged as Confirmed', 'Order Confirmed', $this->session->userdata('username'));
        }
        echo json_encode($response);
    }

    function sendProcessOrderEmail($data)
    {
        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/process_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendreadyPickupEmail($data)
    {
        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/readypickup_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendbookingConfirmEmail($data = '')
    {
        if(empty($data)){
            $data = $this->input->post('data');
        }

        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/bookingconfirm_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendbookingConfirmEmail_shop($data = '')
    {
        if(empty($data)){
            $data = $this->input->post('data');
        }

        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["shop_email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/bookingconfirm_template_shop", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendFulfillmentEmail($data = '')
    {
        if(empty($data)){
            $data = $this->input->post('data');
        }

        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/fulfillment_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendreturntosenderOrder($data)
    {
        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/returntosender_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendShippedOrder($data = '')
    {
        if(empty($data)){
            $data = $this->input->post('data');
        }

        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/shipped_template", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    function sendShippedOrder_Shop($data = '')
    {
        if(empty($data)){
            $data = $this->input->post('data');
        }

        try
        {
            $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
            $this->email->to($data["shop_email"]);
            $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
            $data['transaction'] = $data;
            $data['company_email'] = get_company_email();
            $this->email->message($this->load->view("includes/emails/shipped_template_shop", $data, TRUE));
            $this->email->send();
        }catch(Exception $err){
            echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
            //  return;
        }
    }

    public function order_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_orders->get_sys_shop($member_id);
        if($sys_shop == 0){
            $query = $this->model_orders->order_table($sys_shop);
        }else{
            $query = $this->model_orders->order_table_shop($sys_shop); 
        }
        
        
        generate_json($query);
    }

    public function confirmed_orders_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_orders->get_sys_shop($member_id);
        if($sys_shop == 0){
            $query = $this->model_orders->confirmed_orders_table($sys_shop);
        }else{
            $query = $this->model_orders->confirmed_orders_table_shop($sys_shop); 
        }
        
        
        generate_json($query);
    }

    public function order_item_table()
    {
        $this->isLoggedIn();
        $member_id = $this->session->userdata('sys_users_id');
        $sys_shop = $this->model_orders->get_sys_shop($member_id);

        $reference_num = sanitize($this->input->post('reference_num'));
       
        if($sys_shop == 0) {
            $split = explode("-",$reference_num);
            $sys_shop = $split[0];
            $reference_num = $split[1];
        }else{
            $reference_num = $reference_num;
        }
           
        $query = $this->model_orders->order_item_table($reference_num, $sys_shop);
        
        generate_json($query);
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

    public function export_orders()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $status        = $this->input->post('status_export');
        $date          = $this->input->post('date_export');
        $date_from 	   = format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 	   = format_date_reverse_dash($this->input->post('date_to_export'));
        $requestData   = url_decode(json_decode($this->input->post("request_filter")));
        $filter_string = $this->audittrail->ordersFilterString($this->input->post());
        $date_from_2   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2     = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
        $_name 		   = $this->input->post('_name_export');

        if($sys_shop == 0){
            $query = $this->model_orders->order_table_export($sys_shop);
        }else{
            $query = $this->model_orders->order_table_shop_export($sys_shop); 
        }

  
        $sheet->setCellValue('B1', 'Order List');
        $sheet->setCellValue('B2', $this->input->post('date_from_export').' - '.$this->input->post('date_to_export'));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);


        if($date == 'date_fulfilled'){
                 $sheet->setCellValue('A6', 'Fulfilled Date');
        }else if($date === 'date_shipped'){
                $sheet->setCellValue('A6', 'Shipped Date');
        }else if($date === 'date_processed'){
                $sheet->setCellValue('A6', 'Processed Order Date');
        }else if($date === 'date_booking_confirmed'){
                 $sheet->setCellValue('A6', 'Booking Confirmed Date');  
        }else if($date === 'date_confirmed'){
                $sheet->setCellValue('A6', 'Confirmed Date');
        } else if($date === 'date_paid'){
                $sheet->setCellValue('A6', 'Paid Date');
        }
        else{
                $sheet->setCellValue('A6', 'Ordered Date');
        }

        $sheet->setCellValue('B6', 'Payment Date');
        $sheet->setCellValue('C6', 'Order Ref#');
        $sheet->setCellValue('D6', 'Customer');
        $sheet->setCellValue('E6', 'Contact No.');
        $sheet->setCellValue('F6', 'Amount');
        $sheet->setCellValue('G6', 'Payment');
        $sheet->setCellValue('H6', 'Status');
        $sheet->setCellValue('I6', 'Shop');
        $sheet->setCellValue('J6', 'Branch');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:J6')->getFont()->setBold(true);
        
        $exceldata= array();
        $international = c_international();
        $allow_cod = cs_clients_info()->c_allow_cod;
        foreach($query as $row){

            $payment_status = display_payment_status($row['payment_status'], $row['payment_method'], $allow_cod, true);
            $order_status   = display_order_status($row['order_status'], true);
            $branchname = $this->model_orders->get_branchname($row["reference_num"], $row['sys_shop']);


            if($international == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$subtotal_amount_conv           = $total_amount_item;
				$subtotal_converted             = displayCurrencyValue_withPHP($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
				
			}
			else if($international == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$subtotal_amount_conv           = $total_amount_item;
				$subtotal_converted             = displayCurrencyValue($row['total_amount'], $subtotal_amount_conv, $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($row['total_amount'], 2);
			}



            if($date == 'date_fulfilled'){
                 $dates = $row["date_fulfilled"];
            }else if($date === 'date_shipped'){
                 $dates = $row["date_shipped"];
            }else if($date === 'date_processed'){
                $dates = $row["date_order_processed"];
            }else if($date === 'date_booking_confirmed'){
                $dates = $row["date_booking_confirmed"];
            }else if($date === 'date_confirmed'){
                $dates = $row["date_confirmed"];
            }
            else{
                   $dates = $row["date_ordered"];
            }
            $resultArray = array(

                '1' => $dates,
                '2' => $row["payment_date"],
                '3' => $row["reference_num"],
                '4' => ucwords($row["name"]),
                '5' => $row["conno"],
                '6' => $subtotal_converted,
                '7' => $payment_status,
                '8' => $order_status,
                '9' => $row['shopname'],
                '10' => $branchname
            );
            
            $exceldata[] = $resultArray;
        }
      
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'orders_data';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Order List', 'Orders has been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function export_orders_backup()
    {
        $spreadsheet   = new Spreadsheet();
        $sheet         = $spreadsheet->getActiveSheet();
        $member_id     = $this->session->userdata('sys_users_id');
        $sys_shop      = $this->model_orders->get_sys_shop($member_id);
        $status        = $this->input->post('status_export');
        $date_from 	   = format_date_reverse_dash($this->input->post('date_from_export'));
		$date_to 	   = format_date_reverse_dash($this->input->post('date_to_export'));
        $requestData   = url_decode(json_decode($this->input->post("request_filter")));
        $filter_string = $this->audittrail->ordersFilterString($this->input->post());
        $date_from_2   = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_from.' 00:00:00')));
		$date_to_2     = $this->db->escape(date('Y-m-d H:i:s',strtotime($date_to.' 23:59:59')));
        $_name 		   = $this->input->post('_name_export');

        if($sys_shop == 0){
            $query = $this->model_orders->order_table_export($sys_shop);
        }else{
            $query = $this->model_orders->order_table_shop_export($sys_shop); 
        }

        $getShippingData    = $this->model_orders->getShippingData($_name, $date_from_2, $date_to_2)->result_array();
        $getShippingDataArr = [];
        $getShopData        = $this->model_orders->getShopData()->result_array();
		$getShopDataArr     = [];
        
        foreach($getShippingData as $row){
			$getShippingDataArr[strval($row['reference_num'])][strval($row['sys_shop'])]['delivery_amount'] = $row['delivery_amount'];
        }
        
        foreach($getShopData as $row){
			$getShopDataArr[strval($row['id'])]['shopname'] = $row['shopname'];
        }
        
        $sheet->setCellValue('B1', 'Order List');
        $sheet->setCellValue('B2', $this->input->post('date_from_export').' - '.$this->input->post('date_to_export'));
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);

        $sheet->setCellValue('A6', 'Date');
        $sheet->setCellValue('B6', 'Order Ref#');
        $sheet->setCellValue('C6', 'Customer');
        $sheet->setCellValue('D6', 'Contact No.');
        $sheet->setCellValue('E6', 'Amount');
        $sheet->setCellValue('F6', 'Voucher');
        $sheet->setCellValue('G6', 'Shipping');
        $sheet->setCellValue('H6', 'Total');
        $sheet->setCellValue('I6', 'Payment');
        $sheet->setCellValue('J6', 'Status');
        $sheet->setCellValue('K6', 'Shop');
        $sheet->setCellValue('L6', 'Branch');
        $sheet->setCellValue('M6', 'City');

        $sheet->getStyle('B1')->getFont()->setBold(true);
        $sheet->getStyle('A6:M6')->getFont()->setBold(true);
        
        $exceldata= array();
        foreach($query as $row){

            if($row["payment_status"] == 1 && $row["payment_method"] != 'Free Payment' && $row["payment_method"] != 'Prepayment' && $row["payment_method"] != 'PayPanda' && cs_clients_info()->c_allow_cod == 1) {
                $payment_status = "Paid(COD)";
            }
            else if($row["payment_status"] == 1) {
                $payment_status = "Paid";
            }
            else if($row["payment_status"] == 0 && $row["payment_method"] != 'Free Payment' && $row["payment_method"] != 'Prepayment' && $row["payment_method"] != 'PayPanda' && cs_clients_info()->c_allow_cod == 1) {
                $payment_status = "Pending(COD)";
            }
            else if($row["payment_status"] == 0) {
                $payment_status = "Pending";
            }
            else {
                $payment_status = "Pending";
            }
            
            switch ($row["order_status"]) {
				case 'p':
				$order_status = "Ready for Processing";
				break;
				case 'po':
				$order_status = "Processing Order";
				break;
				case 'rp':
				$order_status = "Ready for Pickup";
				break;
				case 'bc':
				$order_status = "Booking Confirmed";
				break;
				case 'f':
				$order_status = "Fulfilled";
                break;
                case 'rs':
                $order_status = "Return to Sender";
                break;
				case 's':
				$order_status = "Shipped";
				break;
				default:
				$order_status = "Ready for Processing";
				break;
			} 
            $branchname = $this->model_orders->get_branchname($row["reference_num"], $row['sys_shop']);

            if($row['sys_shop'] != 0) {
				$voucher_total_amount = $this->model_orders->get_vouchers_total_shop($row["reference_num"], $row["sys_shop"])->total_voucher_amount;
				$subtotal = max($row["total_amount"] - $voucher_total_amount, 0);
			}else{
				$voucher_total_amount = 0;
				$subtotal = $row["total_amount"];
            }
            
            if($status == ""){
				if($row['delivery_amount'] == 'none'){
					$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
				}
				else{
					$delivery_amount = $row['delivery_amount'];
				}
			}
			else if($status == 1 || $status == 'p' || $status == 'po' || $status == 'rp'|| $status == 'bc' || $status == 'f' || $status == 'rs' || $status == 's' || $status == '7'){
				$delivery_amount = (!empty($getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'])) ? $getShippingDataArr[$row['reference_num']][$row['sys_shop']]['delivery_amount'] : 0;
			}
			else if($status == 0 || $status == '6'){
				$delivery_amount = $row['delivery_amount'];
			}
            
            $shopname = ($row['shopname'] == 0 && $row['sys_shop'] != 0) ? $getShopDataArr[$row['sys_shop']]['shopname'] : $row['shopname'];

            if(c_international() == 1 && $sys_shop == 0){

				$getOrderLogs                   = $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue_withPHP($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue_withPHP($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue_withPHP($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue_withPHP(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
				
			}
			else if(c_international() == 1 && $sys_shop != 0){
				$getOrderLogs                   = $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array();
				$total_amount_item              = 0;
				foreach($getOrderLogs as $val){
					$total_amount_item += currencyConvertedRate_peritem($val['amount'], $val['currval'], $val['qty']);
				}
				$voucher_total_amount_conv      = currencyConvertedRate($voucher_total_amount, $row['currval']);
				$subtotal_amount_conv           = max($total_amount_item - $voucher_total_amount_conv, 0);
				$delivery_amount_conv           = currencyConvertedRate($delivery_amount, $row['currval']);

				$subtotal_converted             = displayCurrencyValue($subtotal, $subtotal_amount_conv, $row['currcode']);
				$voucher_total_amount_converted = displayCurrencyValue($voucher_total_amount, $voucher_total_amount_conv, $row['currcode']);
				$delivery_amount_converted      = displayCurrencyValue($delivery_amount, $delivery_amount_conv, $row['currcode']);
				$total_amount_converted         = displayCurrencyValue(max($subtotal+$delivery_amount, 0), max($subtotal_amount_conv+$delivery_amount_conv, 0), $row['currcode']);
			}
			else{
				$subtotal_converted             = number_format($subtotal, 2);
				$voucher_total_amount_converted = number_format($voucher_total_amount, 2);
				$delivery_amount_converted      = number_format($delivery_amount, 2);
				$total_amount_converted         = number_format(max($subtotal+$delivery_amount, 0), 2);
			}

            $resultArray = array(
                '1' => $row["date_ordered"],
                '2' => $row["reference_num"],
                '3' => ucwords($row["name"]),
                '4' => $row["conno"],
                '5' => $subtotal_converted,
                '6' => $voucher_total_amount_converted,
                '7' => $delivery_amount_converted,
                '8' => $total_amount_converted,
                '9' => $payment_status,
                '10' => $order_status,
                '11' => $shopname,
                '12' => $branchname,
                '13' => $this->model_orders->get_citymun($row['citymunCode'])
            );
            
            $exceldata[] = $resultArray;
        }
        $key = $requestData['order'][0]['column']+1;
        $dir = $requestData['order'][0]['dir'];
        uasort($exceldata, build_sorter($key, $dir));
        $sheet->fromArray($exceldata, null, 'A7');
        $row_count = count($exceldata)+7;
        for ($i=7; $i < $row_count; $i++) {
            $sheet->getStyle("D$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("E$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        
        $writer = new Xlsx($spreadsheet);
        $filename = 'orders_data';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); 
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $this->audittrail->logActivity('Order List', 'Orders has been exported into excel with filter '.$filter_string, 'Export', $this->session->userdata('username'));
        return $writer->save('php://output');
        exit();
    }

    public function print_order($token = '', $ref_num)
    {
        $this->isLoggedIn();
        if($this->loginstate->get_access()['overall_access'] == 1 || $this->loginstate->get_access()['transactions']['view'] == 1 || $this->loginstate->get_access()['pending_orders']['view'] == 1 || $this->loginstate->get_access()['paid_orders']['view'] == 1 || $this->loginstate->get_access()['readyforprocessing_orders']['view'] == 1 || $this->loginstate->get_access()['processing_orders']['view'] == 1 || $this->loginstate->get_access()['readyforpickup_orders']['view'] == 1 || $this->loginstate->get_access()['bookingconfirmed_orders']['view'] == 1 || $this->loginstate->get_access()['fulfilled_orders']['view'] == 1 || $this->loginstate->get_access()['shipped_orders']['view'] == 1 || $this->loginstate->get_access()['returntosender_orders']['view'] == 1) {
            error_reporting(0);
            $member_id = $this->session->userdata('sys_users_id');
            $sys_shop = $this->model_orders->get_sys_shop($member_id);

            if($sys_shop == 0) {
                $split         = explode("-",$ref_num);
                $sys_shop      = $split[0];
                $reference_num = $split[1];

                if($sys_shop == 0){
                    $get_vouchers = $this->model_orders->get_vouchers($reference_num);
                }else{
                    $get_vouchers = $this->model_orders->get_vouchers_shop($reference_num, $sys_shop);
                }
            }else{
                $reference_num = $ref_num;
                $get_vouchers  = $this->model_orders->get_vouchers_shop($reference_num, $sys_shop);
            }

            $row            = $this->model_orders->orders_details($reference_num, $sys_shop);
            $refcode        = $this->model_orders->get_referral_code($row['reference_num']);
            $branch_details = $this->model_orders->get_branchname_orders($reference_num, $sys_shop)->row();
            $order_items    = $this->model_orders->order_item_table_print($reference_num, $sys_shop);
            $refundedOrder  = $this->model_orders->get_refundedOrder($reference_num, $sys_shop)->result_array();

            if($sys_shop != 0){
                $mainshopname = $this->model_orders->get_mainshopname($sys_shop)->row()->shopname;
            }else{
                $mainshopname = get_company_name();
            }

            if(ini() == 'toktokmart'){
                $modified_order = $this->model_orders->modified_orders($reference_num, $sys_shop);
            }
            else{
                $modified_order = null;
            }

            $data_admin = array(
                'main_nav_categories' => $this->model_dev_settings->main_nav_categories()->result(),
                'shopid'              => $this->model_orders->get_sys_shop($member_id),
                'shopcode'            => $this->model_orders->get_shopcode($member_id),
                'reference_num'       => $reference_num,
                'order_details'       => $row,
                'referral'            => $refcode,
                'branch_details'      => $branch_details,
                'mainshopname'        => $mainshopname,
                'mainshopid'          => $sys_shop,
                'url_ref_num'         => $ref_num,
                'orders_history'      => $orders_history,
                'voucher_details'     => $get_vouchers,
                'order_items'         => $order_items,
                'refunded_order'      => $refundedOrder,
                'getOrderLogs'        => $this->model_orders->getOrderLogs_orderid($row['orderid'])->result_array(),
                'modified_order'      => $modified_order
            );
            
            $page = $this->load->view('orders/order_print', $data_admin, true);

            $this->load->library('Pdf');
            $obj_pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            $obj_pdf->SetCreator(PDF_CREATOR);
            $obj_pdf->SetTitle('Order Print');
            $obj_pdf->SetDefaultMonospacedFont('helvetica');
            $obj_pdf->SetFont('helvetica', '', 9);
            $obj_pdf->setFontSubsetting(false);
            $obj_pdf->setPrintHeader(false);
            $obj_pdf->AddPage();
            ob_start();

            $obj_pdf->writeHTML($page, true, false, true, false, '');
            ob_clean();
            $obj_pdf->Output("print_order.pdf", 'I');
            $this->audittrail->logActivity('Order List', $this->session->userdata('username').' printed order #'.$reference_num, 'Print', $this->session->userdata('username'));
        }else{
            $this->load->view('error_404');
        }
    }

    public function renameS3(){
        $this->load->library('s3_upload');

        $this->s3_upload->renameS3images('test', 'test');
    }

    public function sendProductVerifiedEmail($data){

        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['verified_email']);
        $this->email->subject(get_company_name()." | Product Verification Notification");
        $data['data']          = $data;
        $this->email->message($this->load->view("includes/emails/products__forVerfiefied_template", $data, TRUE));
        $this->email->send();
    }

    public function batch_delivery_confirmed_orders()
    {

        $ApproveAll    = $this->input->post('order');

        foreach($ApproveAll as $k => $v){


            $this->model_orders->batch_delivery_confirmed_orders($v['orderid']);
            
            $reference_num  = $this->model_orders->get_app_sales_order_details_reference_num($v['orderid']);
            $order_ID  = $this->model_orders->get_app_order_details_order_id($reference_num);
            
            $product_id  = $this->model_orders->getProductByOrderId($order_ID);
            $itemName   = $this->model_orders->getItemNameByProdId($product_id);
            $url = get_apiserver_link().'notify/logProductNotification';
            $data = array(
                'prod_id' => $product_id,
                'itemname' => $itemName,
                'module' => 'Delivery Confirmed Order List',
                'action' => 'Confirmed',
                'username' => $this->session->userdata('username'),
                'signature' => en_dec("en",md5($itemName."PRODUCTSNOTIFICATION".$product_id)),
                'link'  => 'confirmed_orders',
            );
            $result = $this->postCURL($url, $data);
      
        }

      
       
        $response['success'] = true;
        // // $this->audittrail->logActivity('Merchant Registration', "Shop ".$merchant_details['shop_name']." has been successfully added as a merchant.", 'Merchant Registration - Approve', $this->session->userdata('username'));
        echo json_encode($response);
    }
}
