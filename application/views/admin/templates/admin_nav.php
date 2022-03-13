<?php
    $main_nav = $this->model->getMainNav();
    $main_nav_arr = explode(',',$this->session->userdata('access_nav'));
    //if ($this->session->userdata('token_session') != en_dec("dec", $token)) {
        //header("Location:" . base_url('Main/logout')); /* Redirect to login */
        //exit();
    //}
    
    $access_nav = $this->session->userdata('access_nav');

    $company_logo = base_url('assets/img/logo.png');
    $company_logo_small = base_url('assets/img/logo.png');
    // avatar
    $primary_color = cs_clients_info()->primary_color;
    $button_radius_size = cs_clients_info()->button_radius_size;
    $button_text_color = cs_clients_info()->button_text_color;
    $button_primary_color = cs_clients_info()->button_primary_color;
    $header_bg_color = cs_clients_info()->header_bg_color;
    $middle_bg_color = cs_clients_info()->header_bg_color;
    $footer_bg_color = cs_clients_info()->footer_bg_color;
    $facebook_link = cs_clients_info()->facebook_link;
    $instagram_link = cs_clients_info()->instagram_link;
    $youtube_link = cs_clients_info()->youtube_link;
    $twitter_link = cs_clients_info()->twitter_link;
    $c_favicon = cs_clients_info()->c_favicon;
    $c_main_logo = cs_clients_info()->c_main_logo;
    $c_secondary_logo = cs_clients_info()->c_secondary_logo;
    $tagline = cs_clients_info()->tagline;
    $c_phone = cs_clients_info()->c_phone;
    $font_choice = cs_clients_info()->font_choice;
    $fonts_link=base_url('assets/fonts');
    $name = cs_clients_info()->name;
    $contact_us = cs_clients_info()->c_contact_us;
    $terms_and_conditions = cs_clients_info()->c_terms_and_condition;
?>
<div class="admin-nav bg-light"style="
  overflow-y: scroll;">
    <div class="row mt-5">
        <div class="col-12 mt-2">
            <img class="logo_login" src="<?=base_url('assets/img/'.$c_main_logo);?>" width=100%>
        </div>
        <div class="col-12" >
            Main<br>
            <div class="nav">
                <?php foreach($main_nav->result_array() as $nav){ 
                    if(in_array($nav["main_nav_id"] ,$main_nav_arr)){?>
                        <a class=" display-block mt-1 mb-1 color-primary small" href="<?= base_url($nav["main_nav_href"].'/'.$nav['main_nav_desc']); ?>"><span class="fa <?=$nav["main_nav_icon"]?>"> &nbsp;</span><?=$nav["main_nav_desc"]?></a>
                <?php }} ?>
            </div>
        </div>
    </div>    
</div>
