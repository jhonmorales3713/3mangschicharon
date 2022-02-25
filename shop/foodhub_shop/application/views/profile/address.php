<link rel="stylesheet" href="<?=base_url("assets/css/profile/profile.css")?>">
<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .sidebar-item.sidebar-item-1 {
        color: #222;
        border-right: 4px solid var(--primary-color);
        font-weight: 700;
    }

    .sidebar-item.sidebar-item-1 i {
        color: var(--primary-color);
    }

    @media only screen and (max-width: 767px) {
            .sidebar-item.sidebar-item-1 {
            color: #fff;
            border-right: none;
        }
    }
</style>

<div class="shop-container profile-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <?php include 'sidebar.php'?>
            </div>
            <div class="col-12 col-md-9">
                <div class="profile-page-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="profile-title">My Addresses</h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn portal-primary-btn" type="button" data-toggle="modal" data-target="#addAddress">
                                <i class="fa fa-plus mr-2" aria-hidden="true"></i>
                                Add Address
                            </button>
                        </div>
                    </div>
                </div>
                <div class="profile-page-body">
                  <?php if($addresses->num_rows() > 0):?>
                    <?php foreach($addresses->result_array() as $add):?>
                      <div class="address-item">
                        <div class="row">
                          <div class="col-12 col-md-8">
                              <div class="row address-content">
                                <div class="col-1">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </div>
                                <div class="col">
                                    <p class="font-weight-bold"><?=$add['receiver_name']?>  <?=($add['default_add'] == 1) ? '<span>( Default )</span>' : ''?></p>
                                </div>
                              </div>
                              <div class="row address-content">
                                <div class="col-1">
                                    <i class="fa fa-phone" aria-hidden="true"></i>

                                </div>
                                <div class="col">
                                    <p><?=$add['receiver_contact']?></p>
                                </div>
                              </div>
                              <div class="row address-content">
                                <div class="col-1">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                </div>
                                <div class="col">
                                    <!-- <p>Unit B 10 Floor Inoza Tower, Bonifacio Global City, Fort Bonifacio, Taguig, Metro Manila 1634, Philippines</p> -->
                                    <p><?=$add['address']?>, <?=$add['citymunDesc']?>, <?=($add['postal_code'] == 0) ? '' : $add['postal_code'].' ,'?> <?=$add['provDesc']?>, <?=$add['regDesc']?>, PHILIPPINES</p>
                                    <p><?=$add['landmark']?></p>
                                </div>
                              </div>
                          </div>
                          <div class="col text-right">
                              <button class="btn profile-btn btn_edit mb-2" data-uid = "<?=en_dec('en',$add['id'])?>"
                                data-receiver_name = "<?=$add['receiver_name']?>"
                                data-receiver_contact = "<?=$add['receiver_contact']?>"
                                data-address = "<?=$add['address']?>"
                                data-regcode = "<?=$add['region_id']?>"
                                data-citymuncode = "<?=$add['municipality_id']?>"
                                data-postal_code = "<?=$add['postal_code']?>"
                                data-landmark = "<?=$add['landmark']?>"
                              >
                                Edit
                              </button>
                              <button class="btn profile-btn btn_delete mb-2" data-delid = "<?=en_dec('en',$add['id'])?>">Delete</button>
                              <?php if($add['default_add'] == 0):?>
                                <button class="btn profile-btn btn_default mb-2" data-defaultid = "<?=en_dec('en',$add['id'])?>">Set Default</button>
                              <?php endif;?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach;?>
                  <?php else:?>
                    <div class="address-item">
                      <div class="row">
                        <div class="col-12 text-center">
                          <h5>No Address Available</h5>
                        </div>
                      </div>
                    </div>
                  <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title font-weight-bold">Add Address</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
          $url = base_url('profile/Customer_profile/add_address');
          echo form_open($url,array('id' => 'address_form', 'method' => 'post'));
        ?>
          <div class="modal-body">
              <div class="form-group">
                  <label for="fullName">Receiver's Name</label>
                  <input type="text" class="detail-input" name = "receiver_name" id="receiver_name" required>
              </div>
              <div class="form-group">
                  <label for="mobileNumber">Receiver's Mobile Number</label>
                  <input type="number" class="detail-input contactNumber" name = "receiver_no" id="receiver_no" required>
              </div>
              <div class="form-group">
                  <label for="receiverAddress">Address <small>(HOUSE #, STREET, VILLAGE)</small></label>
                  <textarea class="detail-input" name = "receiver_address" id="receiver_address" rows="3" required></textarea>
              </div>
              <div class="form-group">
                <label for="Region">Region</label>
                <select name="region" id="regCode" class="detail-input select2" required>
                  <option value="">SELECT STATE/REGION</option>
                  <?php foreach($regions as $region){ ?>
                      <option value="<?=$region['regCode']?>"><?=$region['regDesc']?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="City">City</label>
                <select name="city" id="citymunCode" class="detail-input select2" required>
                  <option value="">SELECT CITY</option>
                </select>
              </div>
              <div class="form-group row">
                <div class="col-lg-6">
                  <label for="Country">Country</label>
                  <select name="country" id="country" class="detail-input select2">
                    <option value="PH">PHILIPPINES</option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label for="Postal Code">Postal Code <small>(optional)</small></label>
                  <input type="text" id = "postal_code" name = "postal_code" class="detail-input">
                </div>
              </div>
              <div class="form-group">
                <label for="Landmark">Landmark</label>
                <textarea name="landmark" id = "landmark" rows="3" cols="80" class = "detail-input"></textarea>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn portal-primary-btn">Save changes</button>
          </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT MODAL -->
<div class="modal fade" id="editAddress" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title font-weight-bold">Update Address</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
          $url = base_url('profile/Customer_profile/update_address');
          echo form_open($url,array('id' => 'address_update_form', 'method' => 'post'));
        ?>
          <div class="modal-body">
              <div class="form-group">
                  <label for="fullName">Receiver's Name</label>
                  <input type="text" class="detail-input" name = "update_receiver_name" id="update_receiver_name" required>
                  <input type="hidden" id = "city_code">
                  <input type="hidden" name = "uid" id = "uid">
              </div>
              <div class="form-group">
                  <label for="mobileNumber">Receiver's Mobile Number</label>
                  <input type="number" class="detail-input contactNumber" name = "update_receiver_no" id="update_receiver_no" required>
              </div>
              <div class="form-group">
                  <label for="receiverAddress">Address <small>(HOUSE #, STREET, VILLAGE)</small></label>
                  <textarea class="detail-input" name = "update_receiver_address" id="update_receiver_address" rows="3" required></textarea>
              </div>
              <div class="form-group">
                <label for="Region">Region</label>
                <select name="update_region" id="update_regCode" class="detail-input select2" required>
                  <option value="">SELECT STATE/REGION</option>
                  <?php foreach($regions as $region){ ?>
                      <option value="<?=$region['regCode']?>"><?=$region['regDesc']?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label for="City">City</label>
                <select name="update_city" id="update_citymunCode" class="detail-input select2" required>
                  <option value="">SELECT CITY</option>
                </select>
              </div>
              <div class="form-group row">
                <div class="col-lg-6">
                  <label for="Country">Country</label>
                  <select name="update_country" id="update_country" class="detail-input select2">
                    <option value="PH">PHILIPPINES</option>
                  </select>
                </div>
                <div class="col-lg-6">
                  <label for="Postal Code">Postal Code <small>(optional)</small></label>
                  <input type="text" id = "update_postal_code" name = "update_postal_code" class="detail-input">
                </div>
              </div>
              <div class="form-group">
                <label for="Landmark">Landmark</label>
                <textarea name="update_landmark" id = "update_landmark" rows="3" cols="80" class = "detail-input"></textarea>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn portal-primary-btn">Save changes</button>
          </div>
        </form>
        </div>
    </div>
</div>
<!-- DELETE MODAL -->
<div class="modal fade" id="deleteAddress" tabindex="-1">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title font-weight-bold">Confirmation</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
          $url = base_url('profile/Customer_profile/delete_address');
          echo form_open($url,array('id' => 'address_delete_form', 'method' => 'post'));
        ?>
          <div class="modal-body">
            <div class="form-group row">
              <div class="col-12">
                <p>Are you sure you want to delete this ?</p>
                <input type="hidden" id = "delid" name = "delid">
              </div>
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn portal-primary-btn">Yes</button>
          </div>
        </form>
        </div>
    </div>
</div>


<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
<script src="<?=base_url('assets\js\profile\jquery.alphanum.js');?>"></script>
<script src="<?=base_url('assets\js\profile\profile_helper.js');?>"></script>
<script src="<?=base_url('assets\js\profile\address.js');?>"></script>
