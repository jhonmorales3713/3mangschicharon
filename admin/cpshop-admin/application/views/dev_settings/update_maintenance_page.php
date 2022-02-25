<style>
/* .imageprevies{
    width: 100%;
    height: auto;
    margin: 0 auto 0 auto;
    background-color: #b5b5b5;
    overflow: hidden;
    position: relative;
} */
.divclose {
  position: relative;
}
.deleteimg {
  position: absolute;
  margin-bottom: 75px;
  margin-left: -28px;
  font-size: 18px;
  cursor:pointer;
  background-color:white;
  /* border-radius:50px; */
  opacity:0.9;
  padding:10px;
}
.img_preview{
    margin-top: 10px;
    margin-bottom: 10px;
    max-height: 200px;
    max-width: 250px;
}

/*toggle styles*/
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 54px;
  height: 27px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
    background-color: var(--primary-color) !important
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

div.toggle-switch{
    width: 100%;
    padding: 10px;
}

div.toggle-switch *{        
    vertical-align: middle;
    margin-top: auto;
    margin-bottom: auto;
    display: inline-block;
}

.no-color{
    display: inline-block;
    position: absolute;
    right: 15px;
    top: 0;       
}

@media screen and (max-width:1280px) {
    .no-color{
        position: relative;
        display: block;
        right: 0;
    }
} 
</style>
<input style="display:none;" id="c_id" value="<?=$c_id?>">;
<div class="content-inner" id="pageActive" data-num="9" data-namecollapse="" data-labelname="Client Information">
    <div class="bc-icons-2 card mb-4">
        <div class="row">
            <div class="col">
                <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
                    <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a></li>
                    <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                    <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Dev_settings_maintenance_page/maintenance_page/'.$token);?>">Maintenance Page</a></li>
                    <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                    <li class="breadcrumb-item active">Coming Soon Page</li>
                </ol>
            </div>
            <!--
            <div class="col-auto text-right d-none d-md-flex align-items-center">
                <?php if($prev_product != '0'){?>
                    <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$prev_product)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a>
                <?php } ?>
                <?php if($next_product != '0'){?>
                    <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$next_product)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a>
                <?php } ?>
            </div>
            -->
        </div>
    </div>


         <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Controls</h3>
                </div>
                <div class="card-body">
           
                <strong><?php echo $coming_soon_cover->c_name ?></strong>
                <input type="hidden" id="c_id" name="c_id" value="<?php echo $coming_soon_cover->c_id?>">
                <br><br>
                    <div class="row">
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Local</strong><br>
                                <label class="switch">                                    
                                
                             <?php if($coming_soon_cover->c_with_comingsoon_cover_local == 0){ ?>
                                     <input type="checkbox"    id="csc_local" name="csc_local" >
                               <?php }else{ ?>
                                   <input  type="checkbox" checked  class="form-control-input" name="csc_local" id="csc_local">
                             <?php } ?>
                                    <span class="slider round"></span>
                                </label>
                            </div>               
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Test</strong><br>
                                <label class="switch">                                    
            

                             <?php if($coming_soon_cover->c_with_comingsoon_cover_test == 0){ ?>
                                     <input type="checkbox"   name="csc_test" id="csc_test"  >
                               <?php }else{ ?>
                                   <input  type="checkbox"  checked  class="form-control-input" name="csc_test" id="csc_test"  >
                             <?php } ?>
                                    <span class="slider round"></span>
                                </label>
                            </div>                                           
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Live </strong><br>
                                <label class="switch">                                    
                                  
                             <?php if($coming_soon_cover->c_with_comingsoon_cover_live == 0){ ?>
                                     <input type="checkbox"    name="csc_live" id="csc_live" >
                               <?php }else{ ?>
                                   <input  type="checkbox" checked   class="form-control-input" name="csc_live" id="csc_live">
                             <?php } ?>
                                    <span class="slider round"></span>
                                </label>
                            </div>                                        
                        </div>      
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Local Password: </strong><br>
                                <input type="text"  name="csc_local_pass" id="csc_local_pass" value="<?php echo $coming_soon_cover->c_comingsoon_password_local  ?>"> 
                            </div>                                        
                        </div>   
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Test Password: </strong><br>
                                <input type="text"  name="csc_test_pass" id="csc_test_pass" value="<?php echo $coming_soon_cover->c_comingsoon_password_test  ?>"> 
                            </div>                                        
                        </div>       
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Live Password: </strong><br>
                                <input type="text"  name="csc_live_pass" id="csc_live_pass" value="<?php echo $coming_soon_cover->c_comingsoon_password_live  ?>"> 
                            </div>                                        
                        </div>     
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12 col-lg-12 text-right">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</button>
                            <button type="button" id="edit_client_info" class="btn btn-success saveBtn">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- end of row -->

        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
        </div>
    </form>
</div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/update_maintenance_page.js');?>"></script>