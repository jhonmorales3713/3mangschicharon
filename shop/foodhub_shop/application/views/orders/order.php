<div class="content-inner" id="pageActive" data-num="1"></div>
<div id="pageActiveMobile" data-num="3"></div>
<div id="headerTitle" data-title="My Orders" data-search="true"></div>


<div class="container-fluid shop-container order-page">
    <div class="portal-table col-lg-10">
        <div class="portal-table__header">
            <div class="row">
                <div class="col-12">
                    <div class="search-container">
                        <div class="search-container--overlay d-lg-none"></div>
                        <div class="search">
                            <i class="fa fa-times search__close-icon d-md-none"></i>
                            <form action="">
                                <div class="row search__content">
                                    <div class="col-12 d-lg-none">
                                        <h5 class="text-center mb-5 search__title">Search My Orders</h5>
                                    </div>
                                    <div class="mb-2 mb-lg-0 col-12 col-lg-4">
                                        <input type="text"  id = "search_ref" class="search__input" placeholder="Search...">
                                    </div>
                                    <input hidden  id = "active_franchise_userId"value = <?=$this->session->userdata('active_franchise')->userId?>>
                                    <!-- <div class="mb-2 mb-lg-0 col-12 col-md-6 col-lg">
                                        <select name="" id="" class="search__parameter">
                                            <option value="" selected hidden>Choose category</option>
                                            <option value="">1</option>
                                            <option value="">1</option>
                                            <option value="">1</option>
                                        </select>
                                    </div> -->
                                    
                                    <?php if($this->session->userdata('active_franchise')->isParent  == 1){?>
                                        <div class="mb-2 mb-lg-0 col-12 col-md-8 col-lg">
                                            <select name="" id="search_selected_branch" class="search__parameter">
                                                <option value="All" selected> Choose Branch </option>
                                                <?php foreach($userFranchise as $franchise){?>
                                                    <option  value="<?=$franchise->userId?>"><?=$franchise->branchname?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <div class="mb-2 mb-lg-0 col-12 col-md-6 col-lg">
                                        <select name="" id="search_status" class="search__parameter ">
                                            <option value="" selected hidden>Choose Order Status</option>
                                            <option value="p">Processed</option>
                                            <option value="s">Shipped</option>
                                            <option value="d">Delivered</option>
                                            <option value="r">Received</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2 mb-md-3 mb-lg-0 col-12 col-lg">
                                        <input type="text" name="dates" class="search__parameter date__filter" />
                                    </div>
                                    <col-12 class="col-lg-auto mt-3 mt-md-0">
                                        <button type = "button" class="btn portal-primary-btn search__button">Search</button>
                                    </col-12>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id = "order-table__container" class="portal-table__container portal-table__container--shadow row">

        </div>

        <div id = "pagination__container" class = "float-right pager">

        </div>

        <nav aria-label="..." class="float-right pager">
          <input type="hidden" name="lastPage" id="lastPage">
          <ul class="pagination">
            <li class="first page-item">
              <button type="button" class="first-btn page-link"  style="color: var(--primary-grey);"><i class="fa fa-angle-double-left"></i></button>
            </li>
            <li class="prev page-item">
              <button type="button" class="prev-btn page-link" style="color: var(--primary-grey);"><i class="fa fa-chevron-left"></i></button>
            </li>
            <li class="page-item disabled"><a class="page-link" style="color: var(--primary-color);" id="page_number"></a></li>
            <li class="next page-item">
              <button type="button" class="next-btn page-link"  style="color: var(--primary-color);"><i class="fa fa-chevron-right"></i></a>
            </li>
            <li class="last page-item">
              <button type="button" class="last-btn page-link" style="color: var(--primary-color);"><i class="fa fa-angle-double-right"></i></button>
            </li>
          </ul>
        </nav>
    </div>
</div>

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/orders/orders.js');?>"></script>

