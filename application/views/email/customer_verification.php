
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
                                                        <h3 style="color:#F6841F; margin-top: 0; font-size: 18px; font-family: 'Fira Sans', sans-serif;">Good day, Ka-chicharon!</h3>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p style="color: #222; margin-top: 0;">You are receiving this email because you submitted documents for Profile verification for your account <?=$email;?>.</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table style="width: 100%">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <p style="color: #222; margin-top: 0;">We would like to inform you that your account has been  <b><?=$status?></b></p><br>
                                                            <?php if($status == 'Declined'){?>
                                                                Reason: <?=$reason?>
                                                            <?php }else{ ?>
                                                                You can now log in at <a href="<?=base_url()?>" target="_blank"><?=base_url()?></a> and enjoy our member perks such as COD Order Type!
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                        </table>
                                    
                                        <table style="width: 100%">
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 20px 0 0 0">
                                                        <i style="font-size: 13px; color:#525252; font-family: 'Fira Sans', sans-serif;">Note: If you didn't reqest this email, please contact our system administrator at <span style="font-weight:bold"><?=get_email()?></span>. This is an auto-generated email. Please do not reply to this email thread.</i>
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