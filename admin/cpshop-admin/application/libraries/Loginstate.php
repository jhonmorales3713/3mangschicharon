<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loginstate {

protected $CI;
                        
    public function __construct(){
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        // $this->CI->load->library('notification');
        $this->CI->load->helper('url');
    }

    public function login_state_check()
    {
       //initial checking: Check if a certain session variable is set
        if($this->CI->session->user_id)
        {
                return true;
        }
        else
        {
                header("location: ".base_url()."auth/authentication/");
        }
    }

    public function login_state_check_without_redirect()
    {
        //initial checking: Check if a certain session variable is set
        if($this->CI->session->user_id)
        {
            $loggedIn = true;
        }
        else
        {
            $loggedIn = false;
        }
        
        if($loggedIn === true)
        {
            return $loggedIn;
        }
        else
        {
            return false;
        }
    }
        

    public function get_access()
    {       
        if($this->CI->session->functions)
        {
            return  json_decode($this->CI->session->functions,true);
        }
    }

    public function get_report_access()
    {
        $arranged_rk = [];
        if($this->CI->session->functions)
        {
            $fxns = json_decode($this->CI->session->functions, true);
            $reports = $fxns['reports'];
            if ($reports) {
                $report_keys = ['aov','oscrr','ps','oblr','os','rbl','rbbr','rbsr','tps','tacr','to','po','tsr'];
                $res_key = array_keys(array_where($fxns, function ($v, $k) use ($report_keys) {
                    if (in_array($k, $report_keys)) {
                        return ($v['view'] == 1) ? true:false;
                    }
                }));
                $temp = array_intersect($report_keys, $res_key);
                $arranged_rk = $temp;
            }
        }
        return json_encode($arranged_rk);
    }

    public function get_project_access($project_id){
        $this->login_state_check();
        $this->CI->load->model('app/model_project');
        return $this->CI->model_project->get_project_access($this->CI->session->user_id,$project_id);
    }

    public function find_first_module()
    {
        if($this->get_access()['overall_access']==1||$this->get_access()['online_ordering']==1)
        {
            if ($this->get_access()['transactions']['view']==1)
                header("location: ".base_url('sys/transactions/view'));
            else if ($this->get_access()['products']['view']==1)
                header("location: ".base_url('sys/products/view'));
        }
        else {
            header("location: ".base_url('sys/settings'));
            die();
        }
    }
}