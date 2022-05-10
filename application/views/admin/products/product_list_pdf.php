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

<table cellspacing="0" class="main">
    <thead>
        <tr>
            <th scope="col"><b>Product Name</b></th>
			<th scope="col"><b>Category</b></th>
            <th scope="col"><b>Price</b></th>
			<th scope="col"><b>No. of Stock</b></th>
			<th scope="col"><b>Status</b></th>
			<th scope="col"><b>Stock Status</b></th>			
        </tr>
    </thead>
    <tbody>
        <?php foreach($data as $key => $value){ ?>
            <tr>
                <td><?= $value[0] ?></td>
				<td><?= $value[1] ?></td>				
                <td><?= $value[2] ?></td>
				<td><?= $value[3] ?></td>
                <td><?= $value[4] ?></td>
                <td><?= $value[5] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>