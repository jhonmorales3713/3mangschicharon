<?php 
class Model_customers extends CI_Model {
    

    

  public function get_order_history($search,$user_id,$total_amount){
    $requestData = $_REQUEST;
    $user_id = $this->db->escape($user_id);
    $columns = array(
      0 => 'reference_num',
      2 => 'Address',
      3 => 'b.total_amount',
      4 => 'c.delivery_amount',
      5 => 'date_ordered',
      6 => 'date_shipped',
      7 => 'b.payment_status'
    );


    $sql = "SELECT a.*, 100 as delivery_amount
    FROM app_sales_order_details a
    WHERE a.user_id = $user_id";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $status = '';
      switch ($row['payment_status']) {
        case 1:
          $status = "<label class='badge badge-success text-center' style = 'width:53px;'> Paid</label>";
          break;
        case 2:
          $status = "<label class='badge badge-danger text-center' style = 'width:53px;'> Un Paid</label>";
          break;
        default:
        $status = "<label class='badge badge-warning text-center' style = 'width:53px;'>Processing</label>";
          break;
      }
      $nestedData[] = $row['reference_num'];
      $nestedData[] = $row['address'];
      $nestedData[] = $row['total_amount'];
      $nestedData[] = $row['delivery_amount'];
      $nestedData[] = $row['date_ordered'];
      $nestedData[] = $row['date_shipped'];
      $nestedData[] = $status;

      $data[] = $nestedData;
    }
    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }
  public function get_customers($_type, $_name, $_status, $requestData, $exportable = false){

    $columns = array(
    // datatable column index  => database column name for sorting
        0 => 'full_name',
        1 => 'user_type_id',
        2 => 'status_id',
        3 => 'date_created',
    );

    $sql = "SELECT * from sys_customers WHERE user_type_id != 0";

    // getting records as per search parameters
    if($_type != ""){
       if($_type == 3){
            $sql .= " AND user_type_id = 0 ";
        }else{
            $sql .= " AND user_type_id = $_type";
        }
    }

    if($_name != ""){
        $sql .= " AND full_name LIKE '%".$_name."%'";
    }
    if($_status != ""){
        $sql .= " AND status_id = ".$_status;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;
    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." ";
    if (!$exportable) {
    $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    }
    $query = $this->db->query($sql);

    $data = array();
    foreach( $query->result_array() as $row ) {
        //$history = $this->get_customer_history($row["id"],$row["userid"]);

        // //dummy
        // $history = array('amount'=>0,'count'=>0);
        // //end dummy
        // $spent = "0.00";
        // // if($history["payment_status"] == 1){
        // $spent = number_format($history["amount"],2);
        // // }

        
        $nestedData = array();
        $nestedData[] = $row["full_name"];
        //$nestedData[] = $row["city"];
        //$nestedData[] = $history["count"].' order(s) with '.$spent.' spent';

        if($row["status_id"] != 0 ) {
            $nestedData[] = (!$exportable) ? "<label class='badge badge-success'> Verified</label>":"Verified";
        }else{
            $nestedData[] = (!$exportable) ? "<label class='badge badge-info'> Guest</label>":"Guest";
        }
        if($row["status_id"] == 0){

            $nestedData[] = 'Inactive';
        }else{
            
            $nestedData[] ='Active';
        }
        
        $nestedData[] = date('m-d-Y',strtotime($row["date_created"]));
            $nestedData[] =
            '
            <div class="dropdown text-center">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
                    <a class="dropdown-item btn_history"
                    data-id="'.en_dec('en', $row['id']).'"
                    data-name = "'.$row['full_name'].'"
                    >
                    <i class="fa fa-ye" aria-hidden="true"></i> Order History
                    </a>
                    <a class="dropdown-item btn_sendemail"
                    data-id="'.en_dec('en', $row['id']).'"
                    data-name = "'.$row['full_name'].'"
                    >
                    <i class="fa fa-ye" aria-hidden="true"></i> Send Email
                    </a>
                </div>
            </div>
            ';
        // if($row['userid'] != 0){
        //     $nestedData[] =
        //     '
        //     <div class="dropdown text-center">
        //         <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
        //         <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
        //             <a class="dropdown-item btn_history"
        //               data-id="'.en_dec('en', $row['id']).'"
        //               data-total_amount = "'.$history["amount"].'"
        //               data-name = "'.$row['name'].'"
        //             >
        //               <i class="fa fa-ye" aria-hidden="true"></i> Order History
        //             </a>
        //         </div>
        //     </div>
        //     ';
        // }else{
        //     $nestedData[] = '<center>---</center>';
        // }

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

public function add_customer($_fname, $_lname, $_birthdate, $_gender, $_mobile, $_email, $_address1, $_address2, $_city) {

    $sql = "INSERT INTO app_customers (`first_name`, `last_name`, `conno`, `email`, `address1`, `address2`, `areaid`, `birthdate`, `gender`, `created`, `updated`, `status`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ";

    $data = array($_fname, $_lname, $_mobile, $_email, $_address1, $_address2, $_city, $_birthdate, $_gender, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), 1);

    return $this->db->query($sql, $data);
}

}?>