<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_orderpostbacklogs extends CI_Model {

    public function get_postback_logs($fromdate, $todate, $search, $requestData, $exportable = false)
    {
        $fromdate = $this->db->escape($fromdate);
        $todate = $this->db->escape($todate);
        $columns = ['date_created', 'reference_num', 'return_data'];

        $sql = "SELECT DATE(a.date_created) AS date_created, b.reference_num, a.return_data
        FROM `sys_orderpostbacklogs` a
        LEFT JOIN `app_sales_order_details` b
           ON b.id = a.app_sales_id
        WHERE b.reference_num LIKE '%$search%' AND DATE(a.date_created) BETWEEN $fromdate AND $todate";

        $result = $this->db->query($sql);
        $totalData = $result->num_rows();
        $totalFiltered = $totalData;
        $total_count = $totalData;

        $sql.=" ORDER BY " . $columns[$requestData['order'][0]['column']] . " " . $requestData['order'][0]['dir'] ." ";
        if (!$exportable) {
            $sql .= " LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        }

        $query = $this->db->query($sql);
        // print_r($query);
        $data = [];
        foreach ($query->result_array() as $key => $value) {
            $data[] = [
                $value['date_created'],
                $value['reference_num'],
                ($value['return_data'] == '') ? '': ((!$exportable) ? "<textarea class='w-100 px-3 py-2 rounded-md bg-gray-200' style='height: 100px;' readonly>".$value['return_data']."</textarea>":$value['return_data'])
            ];
        }

        // print_r($data);
        // exit();
        $json_data = array(
        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "total_transaction" => $total_count,
        "data"            => $data
        );

        return $json_data;
    }

}