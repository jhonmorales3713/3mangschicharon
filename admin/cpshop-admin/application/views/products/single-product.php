<div class="content-inner">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="bc-icons-2 card mb-4">
                    <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
                        <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/products_home/' . $token);?>">Products</a></li>
                        <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
        <form action="">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h2 class="my-3 font-weight-bold">Add Product</h2>
                    <div class="card p-4">
                        <div class="form-group mb-4">
                            <label for="exampleFormControlSelect1" class="font-weight-bold">Shop Name</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label for="exampleFormControlSelect1" class="font-weight-bold">Category</label>
                            <select class="form-control" id="exampleFormControlSelect1">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                            </select>
                        </div>

                        <label for="form-bro" class="font-weight-bold">Product Images</label>
                        <div class="file-input-container mb-4" >
                            <input type="file" id="form-bro" />
                            <p id="input-file"></p>
                        </div>
                        <div class="form-group mb-4">
                            <label for="productName" class="font-weight-bold">Product Name</label>
                            <input type="text" class="form-control" id="productName">
                        </div>
                        <div class="form-group mb-4">
                            <label for="productDescription" class="font-weight-bold">Product Description</label>
                            <textarea class="form-control" id="productDescription" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-4">
                                    <label for="productName" class="font-weight-bold">Other Info (Packing/Vairations)</label>
                                    <input type="text" class="form-control" id="productName" placeholder="250g Pack, Small size">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-4">
                                    <label for="productName" class="font-weight-bold">Price</label>
                                    <input type="number" class="form-control" id="productName">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="productTag" class="font-weight-bold">Product Tags (Optional)</label>
                            <input type="text" class="form-control" id="productTag" placeholder="Separate each tag by a comma ( , )">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <h2 class="my-3 font-weight-bold">Inventory</h2>
                    <div class="card p-4">
                        <div class="form-group mb-4">
                            <label for="sku" class="font-weight-bold">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control" id="sku">
                        </div>
                        <div class="form-group mb-4">
                            <label for="sku" class="font-weight-bold">Barcode (ISBN, UPC, GTIN, etc.)</label>
                            <input type="text" class="form-control" id="sku">
                        </div>
                        <div class="form-group mb-4">
                            <label for="uom" class="font-weight-bold">UOM ID</label>
                            <input type="text" class="form-control" id="uom">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="" id="trackQuality">
                            <label class="form-check-label font-weight-bold" for="trackQuality">
                                Track Quantity
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="continueSelling">
                            <label class="form-check-label font-weight-bold" for="continueSelling">
                                Continue selling when out of stock
                            </label>
                        </div>
                        <div class="form-group mb-4">
                            <label for="uom" class="font-weight-bold">UOM ID</label>
                            <input type="text" class="form-control" id="uom">
                        </div>
                    </div>
                    <h2 class="mb-3 mt-4 font-weight-bold">Shipping</h2>
                    <div class="card p-4">
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="trackQuality">
                            <label class="form-check-label font-weight-bold" for="trackQuality">
                                This is a physical product
                            </label>
                        </div>
                        <h4 class="font-weight-bold">Weight</h4>
                        <p class="mb-3">Used to calculate shipping rates at checkout and label prices during fulfillment.</p>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-4">
                                    <label for="weight" class="font-weight-bold">Weight</label>
                                    <input type="number" class="form-control" id="weight">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-4">
                                    <label for="exampleFormControlSelect1" class="font-weight-bold">Unit of Measurement</label>
                                    <select class="form-control" id="exampleFormControlSelect1">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-right mt-4">
                    <button class="btn btn-secondary">Back</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
