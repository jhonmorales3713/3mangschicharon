<!-- for member details use --> 
<section>
    <div class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Account Details</h3>
                        </div>
                        <div class="card-body row">
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">Shop</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->shopname ?>" readonly>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">Shop Branch</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->branchname ?>" readonly>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">First Name</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->fname ?>" readonly>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">Middle Name / Initial</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->mname ?>" readonly>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">Last Name</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->lname ?>" readonly>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">User Email</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->email ?>" readonly>
                            </div>
                            <div class="form-group col-lg-6">
                                <label class="form-control-label col-form-label-sm">Mobile Number</label>
                                <input class="form-control form-control-sm" type="text" value="<?= $account_details->mobile_number ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>