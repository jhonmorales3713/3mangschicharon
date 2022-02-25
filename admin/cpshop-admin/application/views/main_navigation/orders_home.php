<!-- 
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->

<div class="content-inner" id="pageActive" data-num="2" data-namecollapse="" data-labelname="Products">
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </div>
    <!-- Page Header-->
    <div class="row">

        <?php 
            $is_seller = $this->loginstate->get_access()['seller_access'] ?? 0;
            $access_content_nav 
            = $this->session->userdata('access_content_nav') == null ? "" 
            : $this->session->userdata('access_content_nav');

            $arr_ = explode(', ', $access_content_nav);
            $labelname = "Orders"; //check the labelname in the top div
            $main_nav = $this->model->get_main_nav_id($labelname)->row();
            $cn = $this->model->get_content_navigation($main_nav->main_nav_id)->result();

            // Merchant should have a way to view the Merchant Order List even without the Order List access
            if ($is_seller != 0 && ! empty($merchant_order_list)) {
                $mol_id = $merchant_order_list['id'] ?? 0;
                if ($mol_id != 0) {
                    $has_mol_access = false;
                    foreach($cn as $__cn){
                        if (in_array($mol_id, $arr_)) {
                            $has_mol_access = true;
                            break;
                        }
                    }

                    if ($has_mol_access == false) {
                        array_push($arr_, $mol_id);
                    }
                }
                
            }

            $cn2 = $cn;
            $cn3 = $cn;
        ?>
        <?php
            $main_counter = 0;
            foreach ($cn3 as $cn3) {
                if (in_array($cn3->id, $arr_)){
                    $main_counter++;
                }
            }
            $total = $main_counter; 
            $total_devided =  ceil($total / 2);
            $counter = 0;
            $counter2 = 0;
            $no_of_submod = 0;
            $redirecturl  = '';
        ?>
        <div class="col-sm-6"> 
            <?php foreach($cn as $cn){ ?>
                <?php if (in_array($cn->id, $arr_)){
                    if ($cn->cn_name == 'Order List' && $is_seller == 1) continue;
                    ?>
                    <a href="<?=base_url($cn->cn_url.$token);?>" class="w-100">
                        <div class="card card-option card-hover white p-3 mb-3 w-100">
                            <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                            <div class="card-header-title font-weight-bold"><?=$cn->cn_name;?></div>
                            <small class="card-text text-black-50"><?=$cn->cn_description;?></small>
                        </div>
                    </a>
                    <?php $no_of_submod++;?>
                    <?php $redirecturl = base_url($cn->cn_url.$token);?>
                    <?php
                    $counter++;
                    if ($total_devided == $counter) {
                        break;
                    }
                    ?>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="col-sm-6">
            <?php foreach($cn2 as $cn2){ ?>
                <?php if (in_array($cn2->id, $arr_)){ ?>
                    <?php $counter2++; ?> 
                    <?php if ($counter < $counter2){ ?>
                        <a href="<?=base_url($cn2->cn_url.$token);?>" class="w-100">
                            <div class="card card-option card-hover white p-3 mb-3 w-100">
                                <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                                <div class="card-header-title font-weight-bold"><?=$cn2->cn_name;?></div>
                                <small class="card-text text-black-50"><?=$cn2->cn_description;?></small>
                            </div>
                        </a>
                        <?php $no_of_submod++;?>
                        <?php $redirecturl = base_url($cn2->cn_url.$token);?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <input type="hidden" id="checkModule" name="checkModule" value="<?=$labelname;?>" data-countmodule="<?=$no_of_submod;?>" data-redirecturl="<?=$redirecturl;?>">
    <?php $this->load->view('includes/footer'); ?>
    <script src="<?=base_url('assets/js/main_navigation/redirect_page.js');?>"></script>
