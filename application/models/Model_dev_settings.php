
<?php
    class Model_dev_settings extends CI_Model {

    # Start - Content Navigation

    public function main_nav_categories() {
        $sql = "SELECT `main_nav_id`, `main_nav_desc` FROM `cp_main_navigation` WHERE `enabled` >= 1";

        return $this->db->query($sql);
    }
}
?>