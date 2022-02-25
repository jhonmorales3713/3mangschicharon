<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title></title>
</head>
<body data-base_url="<?=base_url();?>" style="font-family: Helvetica, sans-serif; ">
	<table border="0" style="margin: 100px auto;">
		<tbody>
			<tr>
				<td></td>
				<td style="text-align:center;" colspan="2"><label><img style="width: 300px;" src="<?=main_logo()?>" onerror="this.onerror=null; this.src='<?=main_logo()?>'"></label></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="4">
					<hr/>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br/>
					<b>Dear <?= $fullname; ?>,</b>
          <p>You are receiving this message following your request to Reset <?=get_company_name()?> Password .
If you made this request, please click the Reset Password button below.</p>
				</td>
			</tr>
      <tr>
        <td colspan="4">
          <p>Do not share this link to anyone under any circumstances!
If you did not request to Reset <?=get_company_name()?> Password , you may ignore this email..</p>
        </td>
      </tr>
      <tr>
				<td colspan="4" align = "center">
          <br/>
          <br/>
					<a href = "<?=$reset_link?>" style = "border: 2px solid #ff4444;background-color: #ff4444;color: #fff;font-weight:900;font-size:15px;padding:10px 40px;transition:0.3s;min-width: 992px;border-radius:5px;text-decoration:none;">
            Reset Password
          </a>
				</td>
			</tr>
			<tr>
				<td colspan="4">
          <br/>
					<br><br>
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
