<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Response {

    protected $CI;

    public function __construct(){
        $this->CI =& get_instance();
    }

    /**
     * @param  string   $type could be [success, failed, error]
     * @param  string   $mode could be [create, update, delete]
     * @param  string   $data [description]
     * @return string   Message e.g. 'Data created/updated/deleted successfully', 
     */
    public function message($type = '', $mode = '', $data = 'data') {
        $message = '';
        $data = ucfirst($data);
        switch($type) {
            case 'success':
                $message = $data . ' ' . $mode . 'd successfully';
                break;
            case 'failed':
                $message = $data . ' ' . $mode . 'd unsuccessfully';
                break;
            case 'error':
                $message = 'Something went wrong. Please try again later';
                break;
            default:
                $message = '';
        }
        return $message;
    }

    public function action_denied_message() {
        $response = [
            'success' => false,
            'message' => 'Action denied. You have no permission to access the requested resource',
            'environment' => ENVIRONMENT
        ];
        return $response;
    }

    public function access_forbidden_view($title = ''){
        if(!$title) $title = 'Forbidden Page';
        $data = array(
            'view' => $this->CI->load->view("templates/template_forbidden", '', true),
            'title' => $title,
            'token' => $this->CI->session->userdata('token')
        );
        return $data;
    }

    public function data_table($record_filtered, $total_record, $other_response = null) {
        if (!isset($this->CI->input->post()['draw']) && !isset($this->CI->input->post()['draw'])) return false;

        $draw = empty($this->CI->input->post()['draw']) ? $this->CI->input->post()['draw'] : $this->CI->input->post()['draw'];
        $response = array(
            'draw'            => intval($draw),
            'recordsTotal'    => intval($total_record),
            'recordsFiltered' => intval($total_record),
            'data'            => $record_filtered
        );

        if (!empty($other_response)) // for other response purpose
            if (is_assoc_array($other_response))
                foreach ($other_response as $key => $value) 
                    $response[$key] = $value;
            else 
                $response['other_response'] = $other_response;

        generate_json($response);
        die();
    }
}