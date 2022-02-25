
<aside class="sidebar">
    <!-- <div class="sidebar-header">
        <div class="row no-gutters">
            <div class="col-auto">
                <img class="sidebar-image" src="<?=base_url("assets/img/user-avatar.png")?>" alt="">
            </div>
            <div class="col pl-2 d-flex align-items-center">
                <h6 class="sidebar-name">John Doe</h6>
            </div>
        </div>
    </div> -->

    <div class="sidebar-body">
          <a href="<?=base_url("user/profile")?>" class="sidebar-item sidebar-item-0">
              <i class="fa fa-user-circle-o" aria-hidden="true"></i>
              Profile
          </a>
          <a href="<?=base_url("user/address")?>" class="sidebar-item sidebar-item-1">
              <i class="fa fa-address-book-o" aria-hidden="true"></i
              >Address
          </a>
          <?php if($this->session->web_login == 1):?>
          <a href="<?=base_url("user/password")?>" class="sidebar-item sidebar-item-2">
              <i class="fa fa-lock" aria-hidden="true"></i>
              Set Password
          </a>
          <?php endif;?>
        <a href="<?=base_url("user/purchases")?>" class="sidebar-item sidebar-item-3">
            <i class="fa fa-shopping-bag" aria-hidden="true"></i>
            My Orders
        </a>
    </div>
</aside>
