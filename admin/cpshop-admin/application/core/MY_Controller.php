<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Core Controller works on controllers when extended
//Use for common functions for every controller -> reduced repetitiveness on code writing
//for easy edit in common functions
//serves as a helper for controllers with functionalities of CI_Controller

class MY_Controller extends CI_Controller {

    function __construct(){
        parent::__construct();        
        $this->load->model('shops/Model_shops', 'model_shops');
        $this->load->model('shop_branch/Model_shopbranch', 'model_branch');
    }    

    //checks if any user is logged in
    public function isLoggedIn() {
        if($this->session->userdata('isLoggedIn') == false) {
            header("location:".base_url('Main/logout'));
        }
    }

    //returns shops as result array
    public function getShops(){
        return $shops = $this->model_shops->get_shop_opts_oderbyname();
    }

    //get shop options via ajax -> returns html formatted result
    public function getShopOptions(){
        $shops = $this->model_shops->get_shop_opts_oderbyname();                
        $options = '<option value="all">All Shops</option>';
        if($shops){
            foreach($shops as $shop){
                $options.='<option value="'.$shop['id'].'">'.$shop['shopname'].'</option>';
            }            
            $result = array(
                'status' => 'success',
                'options' => $options
            );
        }
        else{
            $result = array(
                'status' => 'fail',
                'options' => null
            );
        }
        echo json_encode($result);
    }

    //get branch options via ajax -> returns html formatted result
    public function getBranchOptions($shop_id){
        $branches = $this->model_branch->get_all_branch($shop_id)->result_array();
        $options = '<option value="all">All Branches</option>';
        $options .= '<option value="main">Main</option>';
        if($branches){
            foreach($branches as $branch){
                $options.='<option value="'.$branch['id'].'">'.$branch['branchname'].'</option>';
            }            
            $result = array(
                'status' => 'success',
                'options' => $options
            );
        }
        else{
            $result = array(
                'status' => 'fail',
                'options' => $options
            );
        }
        echo json_encode($result);
    }    

    //checks restriction of user
    public function views_restriction($content_url){        
        $access_content_nav = $this->session->userdata('access_content_nav');
        $arr_ = explode(', ', $access_content_nav); 
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

    //returns shopname string using shop id -> commonly used in audit trail
    public function getShopName($shop_id){
        $row = $this->db->query("SELECT * FROM sys_shops where id=$shop_id")->row();
        return $row->shopname;
    }

    //returns branchname using branch id -> commonly used in audit trail
    public function getBranchName($branch_id){
        $row = $this->db->query("SELECT * FROM sys_branch_profile WHERE id = $branch_id")->row();
        if($branch_id){
            return $row->branchname;
        }
        else{
            return 'Main';
        }        
    }
}
