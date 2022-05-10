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
    <td>Inventory from <?=$date_from;?> to <?=$date_to?></td>
</tr>
<table cellspacing="0" class="main">
    <thead>
        <tr>
            <th scope="col"><b>Product Name</b></th>
			<th scope="col"><b>Category</b></th>
            <th scope="col"><b>Available Qty</b></th>
			<th scope="col"><b>Date Manufactured</b></th>
			<th scope="col"><b>Date Expiration</b></th>
			<th scope="col"><b>Deducted Qty</b></th>
			<th scope="col"><b>Total Qty</b></th>
			<th scope="col"><b>Status</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $key => $value){ 
            ?>
            <tr>
                <td><?= $value[0] ?></td>
				<td><?= $value[1] ?></td>
                <td><?= $value[2] ?></td>
				<td><?= $value[3] ?></td>
                <td><?= $value[4] ?></td>
                <td><?= $value[5] ?></td>
                <td><?= $value[6] ?></td>
                <td><?= $value[7] ?></td>
            </tr>
        <?php } ?>            
    </tbody>
</table>
        <!-- <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr> -->