<!-- MultiStep Form -->
<link rel="stylesheet" href="<?= base_url('assets/css/tips.css'); ?>">
<div class="container-fluid" id="grad1">
    <div class="row justify-content-center mt-0">
        <div class="col-11 pt-5 tips-container">
            <div class="card p-3 pt-4 p-md-5">
                <div class="row mb-5">
                    <div class="col">
                        <h1 class="text-left font-weight-bold tips-title">Welcome! Skip these tips when you're ready</h1>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-secondary btn-dismiss" data-value="<?= $user_id ?>" >Skip</button>
                    </div>
                </div>
                <?php if($this->session->userdata('sys_shop') == 0){ ?>
                <div class="row">
                    <div class="col-md-12 mx-0">
                        <form id="msform">
                            <!-- progressbar -->
                            <ul id="progressbar" class="d-flex justify-content-center">
                                <li class="active checkicon" id="checkicon"><strong>Shop</strong></li>
                                <li class="checkicon"><strong>Branches</strong></li>
                                <li class="checkicon"><strong>Product Category</strong></li>
                                <li class="checkicon"><strong>Product</strong></li>
                                <li class="checkicon"><strong>Shipping & Delivery</strong></li>
                                <li class="checkicon"><strong>Banner</strong></li>
                                <li class="checkicon"><strong>Done</strong></li>
                            </ul> <!-- fieldsets -->
                            <fieldset>
                                <div class="step-content p-3 p-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="mb-5 pb-3 text-left font-weight-bold border-bottom">Setup Shop</h2>
                                        </div>
                                        <div class="col-md-7 d-flex align-items-center">
                                            <div>
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
        
                                                <a href="<?=base_url('Shops/new/'.$token);?>" class="btn btn-primary" style="float: left;">Setup Shop</a>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-1">
                                            <img class="square" src="<?=base_url("assets/img/setup-shop.svg")?>">
                                        </div>
                                    </div>
                                </div>
                                <input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                            </fieldset>
                            <fieldset>
                                <div class="step-content p-3 p-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="mb-5 pb-3 text-left font-weight-bold border-bottom">Setup Shop Branches</h2>
                                        </div>
                                        <div class="col-md-7 d-flex align-items-center">
                                            <div>
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
    
                                                <a href="<?=base_url('Shopbranch/create/'.$token);?>" class="btn btn-primary" style="float: left;">Setup Branch</a>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-1">
                                            <img class="square" src="<?=base_url("assets/img/setup-branch-2.svg")?>">
                                        </div>
                                    </div>
                                </div>
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;" /> <input type="button" name="next" class="next action-button" value="Next Step" style="float: right;" />
                            </fieldset>
                            <fieldset>
                                <div class="step-content p-3 p-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="mb-5 pb-3 text-left font-weight-bold border-bottom">Setup Product Category</h2>
                                        </div>
                                        <div class="col-md-7 d-flex align-items-center">
                                            <div>
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
    
                                                <a href="<?=base_url('Main_settings/product_category/'.$token);?>" class="btn btn-primary" style="float: left;">Add New Category</a>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-1">
                                            <img class="square" src="<?=base_url("assets/img/setup-category-2.svg")?>">
                                        </div>
                                    </div>
                                </div>
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;" /><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                            </fieldset>
                            <fieldset>
                                <div class="step-content p-3 p-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="mb-5 pb-3 text-left font-weight-bold border-bottom">Add Product</h2>
                                        </div>
                                        <div class="col-md-7 d-flex align-items-center">
                                            <div>
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
    
                                                <a href="<?=base_url('Main_products/add_products/'.$token);?>" class="btn btn-primary" style="float: left;">Add New Product</a>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-1">
                                            <img class="square" src="<?=base_url("assets/img/add-product.svg")?>">
                                        </div>
                                    </div>
                                </div>
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                            </fieldset>
                            <fieldset>
                                <div class="step-content p-3 p-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="mb-5 pb-3 text-left font-weight-bold border-bottom">Setup Shipping & Delivery</h2>
                                        </div>
                                        <div class="col-md-7 d-flex align-items-center">
                                            <div>
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
    
                                                <a href="<?=base_url('Settings_shipping_delivery/general_list/'.$token);?>" class="btn btn-primary" style="float: left;">Add General Shipping</a>
                                                <a href="<?=base_url('Settings_shipping_delivery/custom_list/'.$token);?>" class="btn btn-primary">Add Custom Shipping</a>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-1">
                                            <img class="square" src="<?=base_url("assets/img/setup-shipping-2.svg")?>">
                                        </div>
                                    </div>
                                </div>
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                            </fieldset>
                            <fieldset>
                                <div class="step-content p-3 p-md-5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h2 class="mb-5 pb-3 text-left font-weight-bold border-bottom">Add Banner</h2>
                                        </div>
                                        <div class="col-md-7 d-flex align-items-center">
                                            <div>
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
    
                                                <a href="<?=base_url('settings/Shop_banners/view/'.$token);?>" class="btn btn-primary" style="float: left;">Add New Banner</a>
                                            </div>
                                        </div>
                                        <div class="col-md-4 offset-md-1">
                                            <img class="square" src="<?=base_url("assets/img/setup-banner.svg")?>">
                                        </div>
                                    </div>
                                </div>
                                <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                            </fieldset>
                            <fieldset>
                                    <h2 class="fs-title text-center">Success !</h2> <br><br>
                                    <div class="row justify-content-center">
                                        <div class="col-3"> <img src="<?=base_url("assets/img/setup-complete.svg")?>" class="fit-image"> </div>
                                    </div> <br><br>
                                    <div class="row justify-content-center">
                                        <div class="col-7 text-center">
                                            <h5>You Have Successfully Setup Your Account</h5>
                                        </div>
                                    </div>
                                    <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <?php }else{ ?>
                    <div class="row">
                        <div class="col-md-12 mx-0">
                            <form id="msform">
                                <!-- progressbar -->
                                <ul id="progressbar" class="row justify-content-center">
                                    <li class="active checkicon"><strong>Product Category</strong></li>
                                    <?php if($product_category->num_rows() > 0){ ?>
                                            <li class="active checkicon"><strong>Product</strong></li>
                                    <?php }else{ ?>
                                            <li class="checkicon"><strong>Product</strong></li>
                                    <?php } ?>
                                    <?php if($product->num_rows() > 0){ ?>
                                            <li class="active checkicon"><strong>Shipping & Delivery</strong></li>
                                    <?php }else{ ?>
                                            <li class="checkicon"><strong>Shipping & Delivery</strong></li>
                                    <?php } ?>
                                    <?php if($shipping->num_rows() > 0){ ?>
                                            <li class="active checkicon"><strong>Banner</strong></li>
                                    <?php }else{ ?>
                                            <li class="checkicon"><strong>Banner</strong></li>
                                    <?php } ?>
                                    <li class="checkicon"><strong>Done</strong></li>
                                </ul> <!-- fieldsets -->
                                <?php if($product_category->num_rows() > 0){ ?>
                                <fieldset style="display: none;">
                                <?php }else{ ?>
                                <fieldset>
                                <?php } ?>
                                        <div class="row" style="margin:20px;">
                                            <div class="col-md-12">
                                                <strong style="float: left;font-size: 24px;">Setup Product Category</strong>
                                            </div>
                                            <div class="col-md-8">
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>

                                                <a href="<?=base_url('Main_settings/product_category/'.$token);?>" class="btn btn-primary" style="float: left;">Add New Category</a>
                                            </div>
                                            <div class="col-md-4">
                                                <?php if($product_category->num_rows() > 0){ ?>
                                                    <img class="square" src="https://www.netclipart.com/pp/m/437-4374482_mark-clip-art-vector-check-vector-transparent.png">
                                                <?php }else{ ?>
                                                    <img class="square" src="https://worldwellnessgroup.org.au/wp-content/uploads/2020/07/placeholder.png">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                                </fieldset>
                                <?php if($product->num_rows() > 0){ ?>
                                <fieldset style="display: none;">
                                <?php }else if($product_category->num_rows() > 0){ ?>
                                <fieldset style="display: block;">
                                <?php }else{ ?>
                                <fieldset>
                                <?php } ?>
                                        <div class="row" style="margin:20px;">
                                            <div class="col-md-12">
                                                <strong style="float: left;font-size: 24px;">Add Product</strong>
                                            </div>
                                            <div class="col-md-8">
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>

                                                <a href="<?=base_url('Main_products/add_products/'.$token);?>" class="btn btn-primary" style="float: left;">Add New Product</a>
                                            </div>
                                            <div class="col-md-4">
                                                <?php if($product->num_rows() > 0){ ?>
                                                    <img class="square" src="https://www.netclipart.com/pp/m/437-4374482_mark-clip-art-vector-check-vector-transparent.png">
                                                <?php }else{ ?>
                                                    <img class="square" src="https://worldwellnessgroup.org.au/wp-content/uploads/2020/07/placeholder.png">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                                </fieldset>
                                <?php if($shipping->num_rows() > 0){ ?>
                                <fieldset style="display: none;">
                                <?php }else if($product->num_rows() > 0){ ?>
                                <fieldset style="display: block;">
                                <?php }else{ ?>
                                <fieldset>
                                <?php } ?>
                                        <div class="row" style="margin:20px;">
                                            <div class="col-md-12">
                                                <strong style="float: left;font-size: 24px;">Setup Shipping & Delivery</strong>
                                            </div>
                                            <div class="col-md-8">
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>

                                                <a href="<?=base_url('Settings_shipping_delivery/general_rates/'.$token.'/'.md5($this->session->userdata('sys_shop')));?>" class="btn btn-primary" style="float: left;">Add General Shipping</a>
                                                <a href="<?=base_url('Settings_shipping_delivery/custom_profile_list/'.$token.'/'.md5($this->session->userdata('sys_shop')));?>" class="btn btn-primary">Add Custom Shipping</a>
                                            </div>
                                            <div class="col-md-4">
                                                <?php if($shipping->num_rows() > 0){ ?>
                                                    <img class="square" src="https://www.netclipart.com/pp/m/437-4374482_mark-clip-art-vector-check-vector-transparent.png">
                                                <?php }else{ ?>
                                                    <img class="square" src="https://worldwellnessgroup.org.au/wp-content/uploads/2020/07/placeholder.png">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                                </fieldset>
                                <?php if($banners->num_rows() > 0){ ?>
                                <fieldset style="display: none;">
                                <?php }else if($shipping->num_rows() > 0){ ?>
                                <fieldset style="display: block;">
                                <?php }else{ ?>
                                <fieldset>
                                <?php } ?>
                                        <div class="row" style="margin:20px;">
                                            <div class="col-md-12">
                                                <strong style="float: left;font-size: 24px;">Add Banner</strong>
                                            </div>
                                            <div class="col-md-8">
                                                <p style="text-align: left;float: left;">"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>

                                                <a href="<?=base_url('settings/Shop_banners/view/'.$token);?>" class="btn btn-primary" style="float: left;">Add New Banner</a>
                                            </div>
                                            <div class="col-md-4">
                                                <?php if($banners->num_rows() > 0){ ?>
                                                    <img class="square" src="https://www.netclipart.com/pp/m/437-4374482_mark-clip-art-vector-check-vector-transparent.png">
                                                <?php }else{ ?>
                                                    <img class="square" src="https://worldwellnessgroup.org.au/wp-content/uploads/2020/07/placeholder.png">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/><input type="button" name="next" class="next action-button" value="Next Step" style="float: right;"/>
                                </fieldset>
                                <?php if($banners->num_rows() > 0 AND $shipping->num_rows() > 0 AND $product->num_rows() > 0 AND $product_category->num_rows() > 0){ ?>
                                <fieldset style="display: block;">
                                <?php }else{ ?>
                                <fieldset>
                                <?php } ?>
                                        <h2 class="fs-title text-center">Success !</h2> <br><br>
                                        <div class="row justify-content-center">
                                            <div class="col-3"> <img src="https://img.icons8.com/color/96/000000/ok--v2.png" class="fit-image"> </div>
                                        </div> <br><br>
                                        <div class="row justify-content-center">
                                            <div class="col-7 text-center">
                                                <h5>You Have Successfully Setup Your Account</h5>
                                            </div>
                                        </div>
                                        <input type="button" name="previous" class="previous action-button-previous" value="Previous" style="float: left;"/>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer');?>
<script src="<?=base_url('assets/js/tips/tips_core_function.js');?>"></script>