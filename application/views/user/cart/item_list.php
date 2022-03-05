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
                    <div class="col-6">                                
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