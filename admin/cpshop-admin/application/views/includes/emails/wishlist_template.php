<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title></title>
	<style>
		.order-status{
			font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; color: lightgray;
		}
	</style>
</head>
<link href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css' rel='stylesheet' id='bootstrap-css'>
<script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js'></script>
<script src='//code.jquery.com/jquery-1.11.1.min.js'></script>

<body data-base_url="<?=base_url();?>" style="font-family: Helvetica, sans-serif; ">
	<table border="0" style="margin: 100px auto;">
		<tbody>
			<tr>
				<td></td>
				<td style="text-align:center;" colspan="2"><label><img style="width: 300px;" src="<?=main_logo()?>"></label></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="4">
					<hr/>
				</td>
			</tr>
			<tr>
				<td colspan="3" width="100%">
					<br/>
					<b>Hi <?=$first_name;?>,</b><p>
                    <p><br />One of your wishlist product is now available. Click <a href="<?=get_shop_url('main/products/'.$product_id);?>">here</a> to avail product.</p>
				</td>
			</tr>
            <tr>
				<td colspan="4">
					<table border='0' style="width:100%;">
						<tbody>
                            <tr>
                                <td></td>
                                <td width="10%">
                                    <div style="padding-right:10px">
                                        <a href=""><img src="<?= get_s3_imgpath_upload()."assets/img/".$shopcode."/products-250/".$product_id."/".removeFileExtension($primary_pic).".jpg" ?>" style="width:100%;min-width:100px;max-width:160px" class=""></a>
                                    </div>
                                </td>
                                <td colspan="2" width="100%;">
                                    <div class="col-12 col-md-6 col-lg-12 mb-3">
                                        <div class="portal-table__item">
                                            <div class="portal-table__column col-12 col-lg portal-table__product"><?=$itemname;?></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
				<td colspan="4">
					<br>
					<br>
					<p>If you have any concerns, please do contact us at <?= get_company_email()?>. Thank you.</p>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br>
					<small><i>Note: This is an auto-generated email. Please do not reply to this email thread.</i></small>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<hr/>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>
