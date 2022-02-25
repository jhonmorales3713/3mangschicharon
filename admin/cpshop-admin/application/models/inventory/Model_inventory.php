<?php
class model_inventory extends CI_Model
{
    private $prods = "sys_products";
    private $invtrans = 'sys_products_invtrans';
    private $invtrans_branch = 'sys_products_invtrans_branch';

    public function createInventoryLog($data, $shop)
    {
        if ($this->db->insert($this->invtrans, $data)) {
            $prod = $this->db->get_where($this->invtrans_branch, ['product_id' => $data['product_id'], 'shopid' => $shop, 'branchid' => $data['branchid']]);
            if ($prod->num_rows() > 0) {
                $stocks = $prod->result_array()[0]['no_of_stocks'] + $data['quantity'];
                $inv_b_data = [
                    'shopid' => $shop,
                    'branchid' => $data['branchid'],
                    'product_id' => $data['product_id'],
                    'no_of_stocks' => $stocks,
                ];
    
                return $this->db->set($inv_b_data)->where('product_id',$data['product_id'])->where('shopid',$shop)->where('branchid',$data['branchid'])->update($this->invtrans_branch);
            } else {
                $stocks = $this->db->select('no_of_stocks as qty')->where(['id' => $data['product_id']])->get($this->prods);
                if ($stocks->num_rows() > 0) {
                    $stocks = $stocks->result_array()[0]['qty'] + $data['quantity'];
                    $inv_b_data = [
                        'shopid' => $shop,
                        'branchid' => $data['branchid'],
                        'product_id' => $data['product_id'],
                        'no_of_stocks' => $stocks,
                    ];
        
                    return $this->db->insert($this->invtrans_branch, $inv_b_data);
                }
            }
            
        }
        return false;
    }
    
}

?>