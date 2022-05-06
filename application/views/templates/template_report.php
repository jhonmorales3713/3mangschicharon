<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap');

        body{
            font-family: Montserrat, "Helvetica Neue", Arial, sans-serif;
            color: #3c4043;
            font-size: 13px;
            margin:0; 
            padding:0;            
        }
        .header{
            font-size: 8px;
            line-height:8px !important;
            text-align:left !important;
        }
        .page_title{
            font-size: 8px;
            line-height:8px !important;
            text-align:center !important;
        }
        a {
            text-decoration: none !important;
            color:black;            
        }
        .indent{
            margin-left: 120px !important;
        }
        
    </style>    
</head>
<body>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td class="page_title">
                    <br><br>
                    <b style="font-size: 20px;"><u><?= $title ?></u></b>
                    <?php if(array_key_exists('date_start', $filter) && array_key_exists('date_end', $filter)): ?>
                        <?php if($filter['date_start'] != "" && $filter['date_end'] != ""): ?>
                            <h3><?= format_fulldate($filter['date_start']) ?> - <?= format_fulldate($filter['date_end']) ?></h3>
                        <?php endif; ?>
                    <?php endif; ?>
                    <br>
                </td>
            </tr>
        </tbody>
    </table>
    <?=  $view; ?>
</body>
</html>