<?php 
  class Emails extends CI_Controller {
    function __construct() {
      parent::__construct();
      date_default_timezone_set("Asia/Manila");
    }

    public function seller_order_done_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");
      $shop = $this->input->post("shop");
      $shopCode = $this->input->post("shopCode");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["shopItems"][$shop]["shopemail"]);
        $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $data['shop'] = $shop;
        $data['shopcode'] = $shopCode;
        $content['view'] = $this->load->view('emails/seller_order_processing', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      }
    }

    public function order_done_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["email"]);
        $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $content['view'] = $this->load->view('emails/order_processing', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      }
    }

    public function order_fulfilled_email_send(){

      $data = $this->input->post("data");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["email"]);
        $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
        $data['transaction'] = $data;
        $data['company_email'] = get_company_email();
        $content['view'] = $this->load->view('emails/order_fulfilled', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      }
    }

    public function order_failed_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["email"]);
        $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $content['view'] = $this->load->view('emails/order_failed', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      }
    }

    public function order_pending_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["email"]);
        $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $content['view'] = $this->load->view('emails/order_pending', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      //  return;
      }
    }

    public function processing_for_delivery_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["email"]);
        $this->email->subject(get_company_name()." | Order #".$data["reference_num"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $content['view'] = $this->load->view('emails/order_shipped', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      //  return;
      }
    }

    public function order_distributor_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");

      try
      {
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data["dis_email"]);
        $this->email->subject("Congratulations! You have a new order from your ".get_company_name()." Shoplink ".$data["idno"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $content['view'] = $this->load->view('emails/order_distributor', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      }
    }

    public function assigned_branch_email_send(){

      $data = $this->input->post("data");
      $orderStatus = $this->input->post("orderStatus");
      $paypandaRef = $this->input->post("paypandaRef");
      $shop = $this->input->post("shop");
      $shopCode = $this->input->post("shopCode");
      
      try
      { 
        $this->email->from(get_autoemail_sender(), get_company_name(), get_autoemail_sender());
        $this->email->to($data['branch_assigned_to']["email"]);
        $this->email->subject($data['branch_assigned_to']['branchname']." | Order #".$data["reference_num"]);
        $data["paypanda_ref"] = $paypandaRef;
        $data['transaction'] = $data;
        $data["order_status"] = $orderStatus;
        $data['company_email'] = get_company_email();
        $data['shop'] = $shop;
        $data['shopcode'] = $shopCode;
        $content['view'] = $this->load->view('emails/branch_order_processing', $data, TRUE);
        $message = $this->load->view('emails/template_email', $content, TRUE);
        $this->email->message($message);
        $this->email->send();
      }catch(Exception $err){
       echo "<script>console.log(".json_encode($err, JSON_HEX_TAG).")</script>";
      }
    }
  }