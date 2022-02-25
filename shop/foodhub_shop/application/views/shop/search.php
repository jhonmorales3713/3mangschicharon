<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .sort-label {
        color: #b1b1b1;
        font-weight: 700;
        padding-left: 8px;
    }
    .sort-select {
        font-size: small;
    }
</style>

<div class="content-inner" id="pageActive" data-num="1" data-page="search" data-keyword="<?=$searchKey;?>"></div>
<div id="pageActiveMobile" data-num="2"></div>
<div id="headerTitle" data-title="" data-search="true"></div>

<div class="shop-container shop-container__web search-page">
    <!-- <div class="container-fluid">
        <div class="alert alert-warning" role="alert">
            <?//=shop_main_announcement();?>
        </div>
    </div> -->
    <div class="row">
        <div class="col-12 col-md-3 col-lg-2 search-filter">
            <div class="container-fluid search-filter-container">
                <div class="row">
                    <div class="col search-filter-title">
                        <i class="fa fa-filter mr-1"></i>Search Filter
                    </div>
                    <div class="col-auto d-md-none">
                        <i class="fa fa-times filter-close" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="filter-tabs">
                    <?php if (sizeof($categories) > 0): ?>
                        <!-- Use this class="tab" per filter -->
                        <div class="tab">
                            <input type="checkbox" id="chck1">
                            <label class="tab-label search-filter-title search-filter-title--small" for="chck1">By Category</label>
                            <div class="tab-content">
                                <div class="row">
                                    <input type="hidden" id="filter-checked-category">
                                    <?php foreach($categories as $key => $category): ?>
                                        <div class="search-filter-item col-6 col-md-12">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input category-checkbox" type="checkbox" id="cat-<?=$category['category_id']?>" value="<?=$category['category_id']?>">
                                                <label class="form-check-label" for="<?=$category['category_id']?>"><?=$category['category_name']?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Use this class="tab" per filter -->

                        <!-- <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12 search-filter-title search-filter-title--small">
                                        By Category
                                    </div>
                                    <input type="hidden" id="filter-checked-category">
                                    <?php foreach($categories as $key => $category): ?>
                                        <div class="col-6 col-md-12 search-filter-item">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input category-checkbox" type="checkbox" id="cat-<?=$category['category_id']?>" value="<?=$category['category_id']?>">
                                                <label class="form-check-label" for="<?=$category['category_id']?>"><?=$category['category_name']?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div> -->
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 search-filter-title search-filter-title--small">
                                    Shipped to
                                </div>
                                <div class="col-12 search-filter-item">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input shipped_category_checkbox" type="checkbox" id="inlineCheckbox1" value="13" data-region="metroManila">
                                        <label class="form-check-label" for="inlineCheckbox1">Metro Manila</label>
                                    </div>
                                    <div class="sub-checkboxes" data-region="metroManila">
                                        <!-- <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="metroManilaCityCheckbox0" value="0">
                                            <label class="form-check-label" for="metroManilaCityCheckbox0">All</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="metroManilaCityCheckbox1" value="0">
                                            <label class="form-check-label" for="metroManilaCityCheckbox1">Makati</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="metroManilaCityCheckbox2" value="0">
                                            <label class="form-check-label" for="metroManilaCityCheckbox2">Taguig</label>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="col-12 search-filter-item">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input shipped_category_checkbox" type="checkbox" id="inlineCheckbox2"  data-region="northLuzon" value="01,02,03,14">
                                        <label class="form-check-label" for="inlineCheckbox2">North Luzon</label>
                                    </div>
                                    <div class="sub-checkboxes" data-region="northLuzon"></div>
                                </div>
                                <div class="col-12 search-filter-item">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input shipped_category_checkbox" type="checkbox" id="inlineCheckbox3" value="13,04,17,05" data-region="southLuzon">
                                        <label class="form-check-label" for="inlineCheckbox3">South Luzon</label>
                                    </div>
                                    <div class="sub-checkboxes" data-region="southLuzon"></div>
                                </div>
                                <div class="col-12 search-filter-item">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input shipped_category_checkbox" type="checkbox" id="inlineCheckbox4" value="06,07,08" data-region="visayas">
                                        <label class="form-check-label" for="inlineCheckbox4">Visayas</label>
                                    </div>
                                    <div class="sub-checkboxes" data-region="visayas"></div>
                                </div>
                                <div class="col-12 search-filter-item mb-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input shipped_category_checkbox" type="checkbox" id="inlineCheckbox5" value="09,10,11,12,15,16" data-region="mindanao">
                                        <label class="form-check-label" for="inlineCheckbox5">Mindanao</label>
                                    </div>
                                    <div class="sub-checkboxes" data-region="mindanao"></div>
                                </div>
                                <div class="col-6 col-lg-12 search-filter-item" hidden>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                        <label class="form-check-label" for="inlineCheckbox1">Overseas</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 search-filter-title search-filter-title--small">
                                Price Range
                            </div>
                            <div class="col-12 search-filter-item">
                                <div class="row no-gutters mb-2">
                                    <div class="col">
                                        <div class="form-group mb-0">
                                            <input type="number" class="form-control" id="price_min" placeholder="₱ min price">
                                        </div>
                                    </div>

                                </div>
                                <div class="row no-gutters">
                                    <div class="col">
                                        <div class="form-group mb-0">
                                            <input type="number" class="form-control" id="price_max" placeholder="₱ max price">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid filter-button-container">
                <div class="row">
                    <div class="col-12 mt-2">
                        <button class="btn portal-primary-btn btn-block search-filter-button">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md">
            <div class="container-fluid">
            </div>
            <div class="product-section shop-section container-fluid">
                <div class="row mb-3">
                    <div class="col-12 col-md d-flex justify-content-between">
                        <p class="search-result-title"><i class="far fa-lightbulb mr-2"></i>Search result for "<span class="highlight"><?=$searchKey;?></span>"</p>
                        <div class="mobile-filter-toggle">Filter <i class="fa fa-filter" aria-hidden="true"></i></div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="row">
                            <div class="col">
                                <span class="sort-label">Sort by</span>
                                <select required class="sort-select form-control" name="" id="sort_value">
                                    <option value="date_created" selected>Latest</option>
                                    <option value="itemname">Name</option>
                                    <option value="price">Price</option>
                                </select>
                            </div>
                            <div class="col">
                                <span class="sort-label">Order</span>
                                <select required class="sort-select form-control" name="" id="sort_order">
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="productsTable">
                    <!-- Display products here -->
                </div>
                <div class="d-flex justify-content-center mb-4" >
                    <button hidden class="btn load-more" id="load-more">Load More</button>
                </div>
            </div>
        </div>
    </div>
</div>



<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
