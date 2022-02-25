<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_tools extends CI_Controller {

    public function __construct(){
        parent::__construct();        
        $this->load->model('shops/Model_shops', 'model_shops');
        $this->load->model('shop_branch/Model_shopbranch', 'model_branch');
    }

    public function getShopOptions(){
        $shops = $this->model_shops->get_shop_opts_oderbyname();                
        $options = '<option value="all">All</option>';
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

    public function getBranchOptions($shop_id){
        $branches = $this->model_branch->get_branch_options($shop_id);
        $options = '<option value="all">All Branches</option>';
        $options .= '<option value="main">Main</option>';
        if(!empty($branches)){
            foreach($branches as $branch){
                $options.='<option value="'.$branch['id'].'">'.$branch['branchname'].'</option>';
            }            
            $result = array(
                'status' => 'success',
                'total_opts' => count($branches),
                'options' => $options
            );
        }
        else if(empty($branches)){
            $options = '<option value="all">All Branches</option>';
            $result = array(
                'status' => 'success',
                'total_opts' => 0,
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
}
