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

  <body data-base_url="<?=base_url();?>" style="font-family: Helvetica, sans-serif; ">
    <table border="0" style="margin: 100px 0px; width: 100%;">
      <tbody>
        <tr>
          <td></td>
          <td style="text-align:center;" colspan="2"><label><img style="width: 300px;" src="<?=main_logo()?>"></label></td>
          </td>
          <td></td>
        </tr>
        <tr>
          <td colspan="4">
            <hr />
          </td>
        </tr>
        <?= $view ?>
        <!-- set your view here -->
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