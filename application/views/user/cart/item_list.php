<div class="row">
    <div class="col-12 clearfix">
        <b class="float-left">Review Items</b><br>        
        <span class="a float-right" data-toggle="modal" data-target="#remove_item_modal">
            <i class="fa fa-trash" aria-hidden="true"></i>
            <small>DELETE ALL</small>
        </span>   
    </div>            
    <div class="container p20">                 
        <div class="col-12" id="cart_div">                  
            <?php foreach($_SESSION['cart'] as $key => $value){ ?>
                <div class="row p5" id="<?= $key; ?>"> 
                    <div class="col-1" id="is_included_container">                        
                        <input type="checkbox" data-product_id="<?= $key; ?>" class="is_included" <?= $_SESSION['cart'][$key]['is_included'] == 1 ? "checked" : ""; ?>/>
                    </div>
                    <div class="col-5">                            
                        <strong><?= $_SESSION['cart'][$key]['name'] ?> (<?= $_SESSION['cart'][$key]['size']; ?>)</strong> <br>
                    </div>
                    <div class="col-3">                            
                        <input type="number" value="<?= $_SESSION['cart'][$key]['quantity']; ?>" min="1" max="100" class="qty" data-target="<?= $key; ?>"/>
                    </div>    
                    <div class="col-3">
                        <span class="remove-item a" data-target="<?= $key; ?>"><i class="fa fa-times" aria-hidden="true"></i></span>
                    </div>            
                </div>
            <?php } ?>                    
        </div>
    </div>
</div>

<div id="remove_item_modal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">               
            <div class="modal-body">            
                <div class="row">
                    <div class="col-12">
                        <h5>Remove from cart</h5><hr>
                        <span id="remove_label">Are you sure you want to remove all items?</span>    
                        <input type="hidden" id="item_key" value="all">                              
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">CLOSE</button>
                <button type="button" class="btn btn-sm btn-danger" id="remove_from_cart">REMOVE</button>      
            </div>
        </div>
    </div>
</div>
