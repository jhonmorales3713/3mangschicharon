<style>
    table.main{
        width: 100%;
    }
    table.main tr th, table.main tr td{
        border: 1px solid #333;
        border-collapse: collapse;
        padding: 3px;
    }
</style>

<tr>
    <td>Sales from <?=$date_from;?> to <?=$date_to?></td>
</tr>
<table cellspacing="0" class="main">
    <thead>
        <tr>
            <th scope="col"><b>Date Ordered</b></th>
			<th scope="col"><b>Order ID</b></th>
            <th scope="col"><b>Customer Name</b></th>
			<th scope="col"><b>Contact No.</b></th>
			<th scope="col"><b>City</b></th>
			<th scope="col"><b>Order Value</b></th>
			<th scope="col"><b>Delivery Fee</b></th>
			<th scope="col"><b>Discount</b></th>
			<th scope="col"><b>Total Amount</b></th>			
        </tr>
    </thead>
    <tbody>
        <?php $total_amount = 0;
                $total_discount = 0;
            foreach($data as $key => $value){ 
            ?>
            <tr>
                <?php 
                $discounted = floatval(str_replace(',','',$value[6])) > 0 ? floatval(str_replace(',','',$value[5])-str_replace(',','',$value[6])) : 0;
                $total_discount += $discounted;
                $total_amount += floatval(str_replace(',','',$value[5])) - $discounted;?>
                <td><?= $value[0] ?></td>
				<td><?= $value[1] ?></td>				
                <td><?= $value[2] ?></td>
				<td><?= $value[3] ?></td>
                <td><?= $value[4] ?></td>
                <td><?= $value[5] ?></td>
				<td>50</td>
                <td><?= $discounted;?></td>
                <td><?= number_format(floatval(str_replace(',','',$value[5])) - $discounted,2); ?></td>
            </tr>
        <?php } ?>            
    </tbody>
</table>
        <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2">
                <b>Subtotal:</b><?=number_format(floatval($total_amount + $total_discount),2)?> <br>
                <b>Total Discount:</b><?=number_format($total_discount,2)?><br>
                <b>Total Amount:</b> <?=number_format($total_amount,2)?></td>
            </tr>