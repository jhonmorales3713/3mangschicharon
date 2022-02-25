<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_shop_utilities extends CI_Model {
    public function update_data($data)
	{
        $id = sanitize($data['id']);
        unset($data['id']);
        $sql = "UPDATE cs_utilities SET ";
        $ctr = 1;
        foreach ($data as $key => $value) {
            $sql .= "`$key` = '$value'";
            if($ctr < count($data)) $sql .= ", ";
            $ctr++;
        }
        $sql .= " WHERE id = ?";
        $bind_data = [$id];

        return db_core()->query($sql,$bind_data);
    }
}
