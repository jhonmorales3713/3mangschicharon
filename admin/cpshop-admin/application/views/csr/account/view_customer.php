<section>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Customer Details</h3>
                        </div>
                        <div class="card-body row">
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">First Name</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->first_name ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Last Name</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->last_name ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Email</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->email ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Mobile Number</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->conno ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Main Address</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->address1 ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Sub Address</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->address2 ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Birthdate</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $customer_details->birthdate ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-control-label col-form-label-sm">Gender</label>
                                <?php if($customer_details->gender == 'M'){ ?>
                                    <?php $gender = 'MALE' ?>
                                <?php }else{ ?>
                                    <?php $gender = 'FEMALE' ?>
                                <?php } ?>
                                <input class="form-control form-control-sm" type="text" value="<?= $gender ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>