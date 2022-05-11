<style>
    table.main{
        width: 100%;
    }
    table.main tr th, table.main tr td{
        /* border: 1px solid #333;
        border-collapse: collapse;
        padding: 3px; */
    }
</style>

<table cellspacing="0" class="main">
    <tr>
        <td></td>
        <td style="text-align:center;"><?=get_company_name()?></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align:center;"><?=get_address()?></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align:center;">TIN #: 750-267-332</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="12" style="text-align:center;">Email: <?=get_company_email()?></td>
    </tr>
    <tr>
        <td></td>
        <td style="text-align:center;">Contact #: <?=get_company_phone()?></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="12" style="text-align:left;font-size:12px;">Order #:<?=$data['order_id']?></td>
    </tr>
    <tr>
        <td colspan="12" style="text-align:left;;">Order Date:<?=$data['date_created']?></td>
    </tr>
    <tr>
        <td colspan="2">Invoice To:<?=json_decode($data['shipping_data'])->full_name;?></td>
        <td colspan="3">Payment Method:<?=strtoupper(json_decode($data['payment_data'])->payment_method_name);?></td>
    </tr>
    <tr>
        <td>Contact #:<?=json_decode($data['shipping_data'])->contact_no;?></td>
    </tr>
    <tr>
        <td colspan="12">Address:<?=json_decode($data['shipping_data'])->address.', Brgy '.json_decode($data['shipping_data'])->barangay.', '.json_decode($data['shipping_data'])->city.' '.json_decode($data['shipping_data'])->zip_code.', '.json_decode($data['shipping_data'])->province;?></td>
    </tr>
    <tr><td></td></tr>
</table>
<table cellspacing="0" class="main">
        <tr style="font-weight:bold">
            <th scope="col"><b>Product</b></th>
            <th scope="col"><b>Desc</b></th>
            <th scope="col"><b>Price</b></th>
			<th scope="col"><b>Qty</b></th>
			<th scope="col"><b>Disc. Amt</b></th>
			<th scope="col"><b>Total</b></th>
        </tr>
    <tbody>
        <?php
        
		$total_amount = 0;
		$sub_total_converted = 0; 
		$discount_total = 0;
		$qty = 0;
		foreach(json_decode($data['order_data']) as $key => $row ){
			$total_amount = $row->qty * $row->amount;
			$discount_info = $row->discount_info;
			$qty += $row->qty;
			$amount = $row->amount;
            $discount_price = 0;
			$badge = '';?>
            <tr>
                <td><?=$row->name.' - '.$row->size?></td>
                <td><?=$row->summary?></td>
                <td><?=$row->amount?></td>
                <td><?=$row->qty?></td>
            <?php
			if($discount_info != '' && $discount_info != null){
				if(in_array($key,json_decode($discount_info->product_id))){
					$discount_id = $discount_info->id;
					if($discount_info->discount_type == 1){
						if($discount_info->disc_amount_type == 2){
							$newprice = $amount - ($amount * ($discount_info->disc_amount/100));
							$discount_price = ($amount * ($discount_info->disc_amount/100));
							if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
								$discount_price = $discount_info->max_discount_price;
								$newprice = $discount_info->max_discount_price;
							}
							$badge =  '<span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span> <s><small>'.$amount.'</small></s>'.number_format($newprice,2);
						}else{
							$newprice = $amount - $discount_info->disc_amount;
							$badge = '<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->disc_amount.' off</span>'.number_format($newprice,2);
							if($discount_info->max_discount_isset && $newprice < $discount_info->max_discount_price){
								$discount_price = $discount_info->max_discount_price;
								$newprice = $discount_info->max_discount_price;
								$badge ='<span class=" mr-1 badge badge-danger">- &#8369; '.$discount_info->max_discount_price.' off</span>'.number_format($newprice,2);
								// $newprice = $discount['max_discount_price'];
							}
						}
						$amount = $newprice;
						$discount_total += $discount_price* floatval($row->qty);
						$sub_total_converted += floatval($newprice) * floatval($row->qty); 
					}
				}
			}else{
				$sub_total_converted += floatval($row->amount) * floatval($row->qty); 
			}
            ?>
            <td><?=$discount_price?></td>
            <td><?=number_format($discount_price == 0? $amount * $row->qty: ($amount/*$discount_price*/) *$row->qty,2)?></td>
                <!-- <td><?=print_r($discount_info)?></td> -->
            </tr>
        <?php } ?>            
    </tbody>
    <tr><td colspan="12" style="text-align:right">Subtotal: <?=number_format($sub_total_converted,2)?></td></tr>
    <tr><td colspan="12" style="text-align:right">Discount: <?=number_format($discount_total,2)?></td></tr>
    <tr><td colspan="12" style="text-align:right">Shipping Fee: 50.00</td></tr>
    <tr><td colspan="12" style="text-align:right">Grand Total: <?=number_format($sub_total_converted+50,2)?></td></tr>
    <tr><td></td></tr>
    <tr><td colspan="12" style="text-align:center"><i><?=get_tag_line()?></i></td></tr>
</table>