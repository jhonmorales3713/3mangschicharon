<?php 
class Model_settings_city extends CI_Model {
	public function city_list($filters, $requestData, $exportable = false){
        // storing  request (ie, get/post) global array to a variable
        $_record_status = $filters['_record_status'];
        $_code      = $filters['_code'];
        $_name      = $filters['_name'];
        $_searchby      = $filters['_searchby'];
        
        $columns = array( 
            // datatable column index  => database column name for sorting
            0 => 'citymunCode',
            1 => 'citymunDesc',
            2 => 'provincename',
            3 => 'regionname'
        );

        $sql = "SELECT COUNT(*) as count 
                FROM sys_citymun    
                WHERE status IN (0, 1, 2)";

        $query = $this->db->query($sql);
        $totalData = $query->row()->count;
        $totalFiltered = $totalData;

        $sql = "SELECT city.*, reg.regDesc as regionname, prov.provDesc as provincename 
                FROM sys_citymun city LEFT JOIN sys_region reg ON city.regDesc = reg.regCode 
                LEFT JOIN sys_prov prov ON city.provCode = prov.provCode  
                WHERE city.status IN (0, 1, 2)";

        if($_code != "" AND $_searchby == "city"){
            $sql.=" AND city.citymunCode LIKE '%".$_code."%'";
        }
        if($_name != "" AND $_searchby == "city"){
            $sql.=" AND city.citymunDesc LIKE '%".$this->db->escape_like_str($_name)."%'";
        }
        if($_name != "" AND $_searchby == "province"){
            $sql.=" AND prov.provDesc LIKE '%".$this->db->escape_like_str($_name)."%'";
        }
        if($_name != "" AND $_searchby == "region"){
            $sql.=" AND reg.regDesc LIKE '%".$this->db->escape_like_str($_name)."%'";
        }
        // start - for default search
        if ($_record_status == 1) {
            $sql.=" AND city.status = " . $this->db->escape($_record_status) . "";
        }else if ($_record_status == 2){
            $sql.=" AND city.status = " . $this->db->escape($_record_status) . "";
        }else{
            $sql.=" AND city.status > 0 ";
        }
        // end - for default search

        $query = $this->db->query($sql);

        $totalFiltered = $query->num_rows(); // when there is a search parameter then we have to modify total number filtered rows as per search result.

        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir'];
        if (!$exportable) {
            $sql.=" LIMIT ".$requestData['start']." ,".$requestData['length']."   ";  // adding length
        }

        $query = $this->db->query($sql);

        $data = array();
        foreach( $query->result_array() as $row ) {  // preparing an array for table tbody
            $nestedData=array();
            
            $nestedData[] = strtoupper($row['citymunCode']);	
            $nestedData[] = strtoupper($row['citymunDesc']);
            $nestedData[] = strtoupper($row['provincename']);
            $nestedData[] = strtoupper($row['regionname']);
            $full_city = title_case(strtolower($row['citymunDesc'] .", ". $row['provincename']));
            if ($row['status'] == 1) {
                $record_status = 'Disable';
                $rec_icon = 'fa-ban';
            }else if ($row['status'] == 2) {
                $record_status = 'Enable';
                $rec_icon = 'fa-check-circle';
            }else{
                $record_status = 'Disable';
                $rec_icon = 'fa-ban';
            }

            // $nestedData[] = 
            // '<div class="dropdown">
            //     <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
            //     <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            //         <a class="dropdown-item action_edit" data-value="'.en_dec('en', $row['id']).'" data-toggle="modal" data-target="#edit_modal"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>
            //         <div class="dropdown-divider"></div>
            //         <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
            //         <div class="dropdown-divider"></div>
            //         <a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>
            //     </div>
            // </div>';
            $actions = '
            <div class="dropdown">
                <i class="fa fa-ellipsis-v fa-lg" id="dropdown_menu_button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-hidden="true"></i>
                <div class="dropdown-menu" aria-labelledby="dropdown_menu_button">
            ';
            if ($this->loginstate->get_access()['settings_city']['disable'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_disable" data-value="'.$row['id'].'" data-record_name="'.$full_city.'" data-record_status="'.$row['status'].'" data-toggle="modal" data-target="#disable_modal"><i class="fa '.$rec_icon.'" aria-hidden="true"></i> '.$record_status.'</a>
                    <div class="dropdown-divider"></div>';
            }
            if ($this->loginstate->get_access()['settings_city']['delete'] == 1) {
                $actions .= '
                    <a class="dropdown-item action_delete " data-value="'.$row['id'].'" data-record_name="'.$full_city.'" data-toggle="modal" data-target="#delete_modal"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';
            }
            $nestedData[] = $actions;
            $data[] = $nestedData;
        }
        
        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal"    => intval( $totalData ),  // total number of records
            "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data"            => $data,   // total data array
        );
        return $json_data;
    }
    function delete_modal_confirm($del_id){
        $sql="UPDATE sys_citymun SET `status` = ? WHERE `status` = ? AND id = ?";
        $data = array(0, 1, $del_id);
        $this->db->query($sql, $data);

        if ($this->db->query($sql, $data)) {
            return 1;
        }else{
            return 0;
        }
    }

    function disable_modal_confirm($disable_id, $record_status){
        $sql="UPDATE sys_citymun SET `status` = ? WHERE status > ? AND id = ?";
        $data = array($record_status, 0, $disable_id);
        $this->db->query($sql, $data);

        if ($this->db->query($sql, $data)) {
            return 1;
        }else{
            return 0;
        }
    }

    public function get_CityNameByCode($code)
    {
        $code = $this->db->escape($code);
        $sql = "SELECT citymunDesc as `name` FROM sys_citymun WHERE citymunCode = $code";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
?>