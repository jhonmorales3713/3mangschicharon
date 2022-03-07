<?php 
    $date = new DateTime("now", new DateTimeZone('Asia/Manila') );
    $timezone = $date->format('Y-m-d H:i:s A');
    $special_upper = ["&NTILDE", "&NDASH", "|::PA::|"];
    $special_format = ["&Ntilde", "&ndash", ""];
    //transaction['name']= str_replace($special_upper, $special_format, $userObj->fname;);
    //$transaction['address']= str_replace($special_upper, $special_format, $transaction["address"]);
    $bg = strpos(base_url(),'localhost')?'https://static.vecteezy.com/system/resources/thumbnails/002/084/149/small/orange-and-yellow-banner-abstract-background-free-vector.jpg':'';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <meta name="viewport" content="width=600,initial-scale = 2.3,user-scalable=no">
    <title>TOKTOK PH</title>
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
</head>

<style>
    * {
        border: none;
        border-collapse: collapse;
    }
    @media only screen and (max-width:768px) {
        h3 {
            display: none;
        }
    }
    @media only screen and (min-width:768px) {
        h3 {
            display: block;
        }
    }
</style>
<body
        style="
            font-size:14px;
            background-color: rgba(249, 183, 26, 0.12);
        "
    >
    <table style="height: 20px">
        <tbody>
            <tr>
                <td>
                    
                </td>
            </tr>
        </tbody>
    </table>
        <table
            style="
                width: 100%;
                border-collapse: collapse;
            "
        >
            <tbody>
                <tr>
                    <td style="padding: 0 15px">
                        <table
                            style="
                                width: 100%;
                                max-width: 600px;
                                margin: 0 auto;
                                border-collapse: collapse;
                                border-collapse: collapse;
                                border-top-right-radius: 8px;
                                border-top-left-radius: 8px;
                                background-color: #fff;
                            "
                        >
                            <tbody>
                                <tr style="height: 100px; border: 0;">
                                    <td>
                                        <img src="<?= $bg?>" style="width: 100%; border-top-right-radius: 8px; border-top-left-radius: 8px;" alt="toktok banner">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; border-collapse: collapse">
            <tbody>
                <tr>
                    <td style="padding: 0 15px">
                        <table
                            style="
                                width: 100%;
                                max-width: 600px;
                                margin: 0 auto;
                                border-collapse: collapse;
                                border-collapse: collapse;
                                background-color: #fff;
                            "
                        >
                            <tbody>
                                <tr style="height: 100px; border: 0">
                                    <td style="padding: 20px 24px 0 24px">
                                        <table style="width: 100%; ">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 16px 0px 0 0">
                                                        <h3 style="color:#F6841F; margin-top: 0; font-size: 18px; font-family: 'Fira Sans', sans-serif;">Good day, ka-toktok!</h3>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="color: #222; margin-top: 0;">
                                                           <?=$email;?> is requesting to have an access on your shop.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="color: #222; margin-top: 0;">
                                                           If this was an authorized request, please provide the below verification code.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="color: #222; margin-top: 0;">
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 20px 0 0 0">
                                                        <i style="font-size: 13px; color:#525252; font-family: 'Fira Sans', sans-serif;">Note: This is an auto-generated email. Please do not reply to this email thread.</i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; border-collapse: collapse">
            <tbody>
                <tr>
                    <td style="padding: 0 15px">
                        <table
                            style="
                                width: 100%;
                                max-width: 600px;
                                margin: 0 auto;
                                border-collapse: collapse;
                                border-collapse: collapse;
                                background-color: #FCB71B;
                                border-bottom-right-radius: 8px;
                                border-bottom-left-radius: 8px;
                            "
                        >
                            <tbody>
                                <tr style="height: 100px; border: 0">
                                    <td style="padding: 16px 24px 16px 24px;">
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="color: #fff; margin-bottom: 0; margin-top: 0; text-align: center; font-size: 14px"><b>CONNECT PEOPLE THROUGH DOOR-TO-DOOR DELIVERIES AND PROMOTE EASIER ONLINE SHOPPING.</b></p>
                                                        <!-- <a href="" style="color: #fff; margin-top: 0; text-decoration: underline; text-align:center; display: block; margin-top: 3px;">Help Centre</a> -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: center;">
                                                        <!-- <a href="" target="_blank"><img style="margin-top: 12px;" src="<?= get_s3_imgpath_upload().'assets/img/email/fb.png'?>"></a>
                                                        <a href="" target="_blank"><img style="margin-top: 12px; margin-left: 4px" src="<?= get_s3_imgpath_upload().'assets/img/email/youtube.png'?>"></a>
                                                        <a href="" target="_blank"><img style="margin-top: 12px; margin-left: 4px" src="<?= get_s3_imgpath_upload().'assets/img/email/instagram.png'?>"></a> -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="height: 20px">
        <tbody>
            <tr>
                <td>
                    
                </td>
            </tr>
        </tbody>
    </table>
    </body>
</html>
