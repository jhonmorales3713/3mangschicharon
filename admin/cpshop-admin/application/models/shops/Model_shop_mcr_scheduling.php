<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_shop_mcr_scheduling extends CI_Model {
  public function __construct()
  {
    parent::__construct();
    $this->load->model('Model_paid_orders_with_branch', 'model_powb');
    $this->db2 = $this->load->database('reports', TRUE);
  }

  function get_all_shop(){
        $sql="SELECT * FROM sys_shops WHERE status = ? ORDER BY shopname ASC";
      $data = array(1);
      return $this->db->query($sql, $data);
    }

 public function delete_mcr_schedule_record($id) {
        $query="UPDATE sys_shops_mcr_scheduling SET schedule_status = ? WHERE id = ?";

        $bind_data = array(
                2,
                $id
        );
        return $this->db->query($query,$bind_data); 
}

 public function get_shop_mcr_scheduling_data($filters,$requestData){

    $_record_status = $filters['_record_status'];
    $_mainshop = $filters['_mainshop'];
    $_effectivity_date = $filters['_effectivity_date'];

    $record_status=$this->db->escape($_record_status);
    $mainshop=$this->db->escape($_mainshop);
    $effectivity_date=$this->db->escape($_effectivity_date);
    if($this->session->userdata('sys_shop_id')!=""){
      $_mainshop=$this->session->userdata('shopname');
    }
    // $record_status_filter="";
    // $branchname_filter="";
    // $city_filter="";
    // if($_record_status==1){
    //     $record_status_filter="zone.`enabled`=1"; 
    // }else if($_record_status==2){
    //     $record_status_filter="zone.`enabled`=0";
    // }else{
    //     $record_status_filter="(zone.`enabled`=1 OR zone.`enabled`=0)";
    // }

    // if(strtolower($_branchname)=="main"){
    //   $branchname_filter='AND branch.branchname IS NULL';
    // }else if($_branchname==""){
    //   $branchname_filter="";
    // }else{
    //   $branchname_filter='AND branch.branchname LIKE "%'.$_branchname.'%"';
    // }

    // if($_city==""){
    //   $city_filter="";
    // }else{
    //   $city_filter='HAVING city_mun LIKE "%'.$_city.'%"';
    // }

    $columns = array(
      0 => 'shopname',
      1 => 'mcr',
      2 => 'startup',
      3 => 'jc',
      4 => 'mcjr',
      5 => 'mc',
      6 => 'mcsuper',
      7 => 'mcmega',
      8 => 'others',
      9 => 'process_date',
      10 => 'schedule_status'
    );

      $sql = 'SELECT *, a.id as trueid, b.id as shopid FROM `sys_shops_mcr_scheduling` a
      LEFT JOIN  `sys_shops` b ON a.shopid = b.id
      WHERE a.schedule_status != 2
     ';


     if($_mainshop!="Select Shop"){
        $sql.=" AND b.shopname=$mainshop"; 
     }

     if($_record_status!=""){
        $sql.=" AND a.schedule_status=$_record_status"; 
     }

     if($_effectivity_date!=""){
        $sql.=" AND DATE_FORMAT(a.`process_date`,'%m/%d/%Y')=$effectivity_date"; 
     }

    $query = $this->db2->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $total_count = $totalData;

    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding 

    $query = $this->db2->query($sql);

    $data = array();
    $count = 0;
    $total_amount = 0;
    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $count++;
      if($row['schedule_status']==0){
        $row['schedule_status']="Pending";
      }else{
        $row['schedule_status']="Applied";
      }
      // $nestedData[] = $count;
      $nestedData[] = $row['shopname'];
      $nestedData[] = $row['merchant_comrate'];
      $nestedData[] = $row['startup']; 
      $nestedData[] = $row['jc'];
      $nestedData[] = $row['mcjr'];
      $nestedData[] = $row['mc'];
      $nestedData[] = $row['mcsuper'];
      $nestedData[] = $row['mcmega'];
      $nestedData[] = $row['others'];
      $nestedData[] = $row['process_date'];
      $nestedData[] = $row['schedule_status'];
        $actions = '
            <div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            ';

            if ($this->loginstate->get_access()['shop_mcr_scheduling']['viewshop'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_view" data-value="'.en_dec('en', $row['shopid']).'"><i class="fa fa-pencil" aria-hidden="true"></i> View</a>
                    <div class="dropdown-divider"></div>';
            }

            if ($this->loginstate->get_access()['shop_mcr_scheduling']['delete'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_delete" data-value="'.$row['trueid'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
            }
            $actions .= '
                </div>
            </div>';
            
        $nestedData[] = $actions;
        $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "total_transaction" => $total_count,
      "data"            => $data
    );

    return $json_data;
  }

  public function shop_mcr_cron(){
    $datetimenow = date("Y-m-d H:i:s");
    $sql = "SELECT *, a.id as trueid, b.id as shopid, b.shopname FROM `sys_shops_mcr_scheduling` a
      LEFT JOIN  `sys_shops` b ON a.shopid = b.id WHERE a.schedule_status = 0 AND a.process_date <= ?";
    $data = array($datetimenow);
    $todo = $this->db->query($sql, $data)->result_array();

    if(!empty($todo)){
        foreach($todo as $k => $v){
            $sql="UPDATE 8_referralcom_rate_shops SET 
            merchant_comrate = ?,
            startup = ?,
            jc = ?,
            mcjr = ?,
            mc = ?,
            mcsuper = ?,
            mcmega = ?,
            others = ?,
            date_updated = ?
            WHERE shopid = ?";
            $data = array(
                $v['merchant_comrate'],
                $v['startup'],
                $v['jc'],
                $v['mcjr'],
                $v['mc'],
                $v['mcsuper'],
                $v['mcmega'],
                $v['others'],
                $datetimenow,
                $v['shopid']
            );
            $this->db->query($sql,$data); 

            $sql="UPDATE sys_shops_mcr_scheduling SET schedule_status = ? WHERE id = ?";
            $data = array(
                1,
                $v['trueid']
            );

            $this->db->query($sql,$data); 
            $this->audittrail->logActivity('Shop MCR Cron Job ', 'MCR for shop '.$v['shopname'].' successfully updated', 'update', $this->session->userdata('username')); 
            return 'MCR for shop '.$v['shopname'].' successfully updated!';
        }
    }else{
        return false;
    }
  }
}
?>