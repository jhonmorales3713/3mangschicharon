<br><br><br><br>
<div class="row">
    <div class="col-12">
        <div class="alert alert-secondary ml-4 color-dark" role="alert">
            <span class="font-weight-bold"><?=$active_page?></span>
        </div>
    </div>

    <?php
    $access_content_nav
        = $this->session->userdata('access_content_nav') == null ? ""
        : $this->session->userdata('access_content_nav');
    $arr_ = explode(', ', $access_content_nav);
    $labelname = $active_page; //check the labelname in the top div
    $main_nav = $this->model->get_main_nav_id($active_page)->row();
    $cn = $this->model->get_content_navigation($main_nav->main_nav_id)->result();
    $cn2 = $cn;
    $cn3 = $cn;
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
    <div class="col-sm-5 mt-4 ml-4">
        <?php foreach($cn as $cn){ ?>
            <?php if (in_array($cn->id, $arr_)){ ?>
                <a href="<?=base_url($cn->cn_url.$token);?>" class="">
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