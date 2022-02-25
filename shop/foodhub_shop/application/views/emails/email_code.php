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
          <p>You are receiving this message following your request to Register in <?=get_company_name()?> .
If you made this request, please enter the 6 digit code on the Email Verification Page:</p>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<br/>
					<b>Email Code</b>
          <br>
          <h2><?=$email_code?></h2>
				</td>
			</tr>
      <tr>
        <td colspan="4">
          <p>Do not share this code to anyone under any circumstances!
If you did not request to register in <?=get_company_name()?> , you may ignore this email.</p>
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
