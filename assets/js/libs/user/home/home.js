
// Set the date we're counting down to
var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  $.each($(".timer"), function(key, value) {
    var countDownDate = new Date($(this).data('enddate')).getTime();
    var distance = countDownDate - now;
  
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor(((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
  
    // Display the result in the element with id="demo"
    $(this).html(days +" D "+hours+" H "+minutes+" "+seconds+" s");
  
    // If the count down is finished, write some text
    if (distance < 0) {
      // clearInterval(x);  
      // console.log($(this).data('id'));
      $("."+$(this).data('id')).hide(250).html('');
    }else{
      $(".flashsales").show(250);
    }
  });
}, 1000);

$('.product-img').click(function(){
  var product_id = $(this).data('product_id');
  window.location.href = base_url + 'products/'+product_id;
});
// $.ajax({
//   type:'post',
//   url:base_url+'Home/get_discounts',
//   beforeSend:function() {
//   },
//   success:function(data2){
// //     var data = JSON.parse(data2);
// //     var html='';
// //     html+=`<div class="col-12 text-white h3 d-flex justify-content-center ">
// //     Flash Sale
// //     </div>`;
// //     var variants2 = Array();
// //     // console.log(data);
// //     $(".flashsale").hide();
// //     $.each(data.discounts ,function(key, value) {
// //       $(".flashsale").show(250);
// //       // console.log(value);
// //       html+=`<div class="col-12 text-white d-flex justify-content-center  timer-${value.discount_info.id}">
// //           Promo Until<br>
// //       </div>
// //       <div class="col-12 h2 text-white d-flex justify-content-center timer timer-${value.discount_info.id}"  data-id="timer-${value.discount_info.id}" data-startdate='${value.discount_info.start_date}' data-enddate='${value.discount_info.end_date}' id="promodatetime" data-datetime="">
          
// //       </div>
// //       <div class="col-12">
// //           <div class="row d-flex justify-content-center  timer-${value.discount_info.id}">`;
// //           var count = 0;
// //           var discount_ids = Array();
// //           $.each(data.products ,function(key, product) {
// //             discount_ids.push(product.id);
// //           });
// //           $.each(data.categories ,function(index1, category) {
// //             $.each(data.products ,function(index2, product) {
// //               if(0 == product.category_id && !variants2.includes(product.id)&& !variants2.includes(product.parent_product_id) && count < 5){
// //                 variants2.push(product.id);
// //                 variants2.push(product.parent_product_id);
// //                 var badge ='';
// //                 count++;
// //                 $.ajax({
// //                   type:'post',
// //                   url:base_url+'admin/Main_products/get_productdetails/'+product.parent_product_id,
// //                   success:function(data3) {
// //                     var data1 = JSON.parse(data3);
// //                     // console.log(data1);
// //                     var products2 = data1.message;
// //                     $.ajax({
// //                       type:'post',
// //                       url:base_url+'Home/get_variants/'+product.parent_product_id,
// //                       success:function(data4) {
// //                         var data_v = JSON.parse(data4);
// //                         // console.log(data_v);
// //                         var variants = data_v.message;
// //                         var image_path = '';
// //                         // <?php $count =0; 
// //                         //     $discount_ids = array();
// //                         //     foreach($discount_['products'] as $product){ 
// //                         //         array_push($discount_ids,$product['id']);
// //                         //     }
// //                         //     foreach($categories as $category){ ?>    
// //                         //         <?php foreach($discount_['products'] as $product){ ?>
// //                         //             <!-- if variant is in discount -->
// //                         //             <?php if(0 == $product['category_id'] && !in_array($product['id'],$variants2)&& !in_array($product['parent_product_id'],$variants2) && $count < 5){ 
// //                         //                 array_push($variants2,$product['id']);
// //                         //                 array_push($variants2,$product['parent_product_id']);
// //                         //                 $product2 = $this->model_products->get_product_info($product['parent_product_id']);
// //                         //                 $variants = $this->model_products->get_variants($product['parent_product_id']);;
// //                         //                 $count ++;?>                   
// //                                         if(product.img == '' || product.img == null){
// //                                           image_path = base_url+'assets/img/shop_logo.png';
// //                                         }
// //                                         else{                                
// //                                           image_path = base_url+'assets/uploads/products/'+products2.img.replaceAll('==','');
// //                                         }
// //                                         html+=`<div class="col-lg-2  col-md-4 col-sm-6 mt10  bg-white m-1 p-1">
// //                                             <div class="product-img " style="background-image: url(${image_path}); width: 100%;" data-product_id="${product.id}">
// //                                                 <div class="product-info ${variants.length == 1 ? 'sing-variant':''}">
// //                                                 <strong class="product-name">${products2.name}</strong><br>`;
// //                                                 var newprice  = variants.price;
// //                                                 badge  = newprice;
// //                                                 $.each(variants,function(ind, variant) {
// //                                                   $.ajax({
// //                                                     method: "POST",
// //                                                     url:base_url+'Home/en_dec/en/'+variant.id
// //                                                   ,
// //                                                   success:function(encrypted) {
// //                                                     var enc = (JSON.parse(encrypted));
// //                                                     if(discount_ids.includes(enc)){
// //                                                       var discount = product.discount;
// //                                                       var discount_price = 0;
// //                                                       if(discount.discount_type == 1){
// //                                                         if(discount.disc_amount_type == 2){
// //                                                           newprice = product.price - (product.price * (discount.disc_amount/100));
// //                                                           discount_price = discount.disc_amount;
// //                                                           if(discount.max_discount_isset && newprice < discount.max_discount_price){
// //                                                             discount_price = discount.max_discount_price;
// //                                                           }
// //                                                           badge = newprice.toFixed(2)+ " <s><small>"+variant.price+`</small></s><span class=" mr-1 badge badge-danger">- ${discount_price}% off</span>`;
// //                                                         }else{
// //                                                           newprice = product.price - discount.disc_amount;
// //                                                           badge = newprice.toFixed(2)+ " <s><small>"+variant.price+`</small></s><span class=" mr-1 badge badge-danger">- &#8369; ${discount_price}</span>`;

// //                                                           if(discount.max_discount_isset && newprice < discount.max_discount_price){
// //                                                             badge = newprice.toFixed(2)+ " <s><small>"+variant.price+`</small></s><span class=" mr-1 badge badge-danger">- &#8369; ${max_discount_price}</span>`;
// //                                                             discount_price = discount.max_discount_price;
// //                                                           }
// //                                                         }
// //                                                       }
// //                                                     }
// //                                                   }});
// //                                                   // <?php foreach($variants as $variant){ 
// //                                                   //     $newprice = $variant['price'];
// //                                                   //     $badge = $newprice;
// //                                                   //     if(in_array(en_dec('en',$variant['id']),$discount_ids)){//
// //                                                   //         $discount = $product['discount'];
// //                                                   //         if($discount['discount_type'] == 1){
// //                                                   //             if($discount['disc_amount_type'] == 2){
// //                                                   //                 $newprice = $product['price'] - ($product['price'] * ($discount['disc_amount']/100));
// //                                                   //                 $discount_price = $discount['disc_amount'];
// //                                                   //                 if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
// //                                                   //                     $discount_price = $discount['max_discount_price'];
// //                                                   //                 }
// //                                                   //                 $badge =  number_format($newprice,2).' <s><small>'.$variant['price'].'</small></s><span class=" mr-1 badge badge-danger">- '.$discount_price.'% off</span>';
// //                                                   //             }else{
// //                                                   //                 $newprice = $product['price'] -$discount['disc_amount'];
// //                                                   //                 $badge =  number_format($newprice,2).'<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['disc_amount'].' off</span>';
// //                                                   //                 if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
// //                                                   //                     $badge = number_format($newprice,2).'<span class=" mr-1 badge badge-danger">- &#8369; '.$discount['max_discount_price'].' off</span>';
// //                                                   //                     // $newprice = $discount['max_discount_price'];
// //                                                   //                 }
// //                                                   //             }
// //                                                   //         }
// //                                                   //     }
// //                                                   // ?>
// //                                                 });
// //                       }
// //                     });
// //                   }
// //                 });
// //                 html+=`
// //                     <span class="badge badge-info size-select"><?= $variant['name']; ?></span> <span>&#8369; ${badge}</span><br>`;
// //                   }
// //                   html+=`
                
// //                 </div>   
// //             </div>  
// //             <div class="ml5">
// //                 <div class="row">
// //                     <div class="col-8">
// //                         <?php 
// //                             $inv = 0;
// //                             $price = 0;
// //                             if(isset($product['variants'][0])){
// //                                 foreach($product['variants'] as $variant){
// //                                     $inventories = $this->model_products->get_inventorydetails($variant['id']);
// //                                     foreach($inventories as $inventory){
// //                                         $now = time();
// //                                         $expiration = strtotime($inventory['date_expiration']);
// //                                         if(date('Y-m-d',$expiration) > date('Y-m-d') && $variant['id'] ==  $inventory['product_id']){
// //                                             $inv += $inventory['qty'];
// //                                             //(round(($expiration - $now)/(60*60*24))+1);
// //                                         }
// //                                     }
// //                                 }
// //                             ?>
// //                             <strong>&#8369; <?= number_format($product['variants'][0]['price'],2); ?></strong>
// //                         <?php } else {
// //                             $discount = $product['discount'];
// //                             $newprice = 0;
// //                             $badge = '';
// //                             if($discount['discount_type'] == 1){
// //                                 if($discount['disc_amount_type'] == 2){
// //                                     $newprice = $product['price'] - ($product['price'] * ($discount['disc_amount']/100));
// //                                     $badge = '- '.$discount['disc_amount'].'% off';
// //                                     if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
// //                                         $newprice = $discount['max_discount_price'];
// //                                     }
// //                                 }else{
// //                                     $newprice = $product['price'] -$discount['disc_amount'];
// //                                     $badge = '- &#8369; '.$discount['disc_amount'].' off';
// //                                     if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
// //                                         $newprice = $discount['max_discount_price'];
// //                                     }
// //                                 }
// //                             }
// //                             foreach($product['inventory'] as $inventory){
// //                                 $now = time();
// //                                 $expiration = strtotime($inventory['date_expiration']);
// //                                 if(date('Y-m-d',$expiration) > date('Y-m-d') && $product['id'] ==  en_dec('en',$inventory['product_id'])){
// //                                     $inv += $inventory['qty'];
// //                                     //(round(($expiration - $now)/(60*60*24))+1);
// //                                 }
// //                             }
// //                         ?>
// //                             <strong>&#8369; <?= number_format($product['price'],2); ?></strong>&nbsp;<span class=" mr-1 badge badge-danger"><?=$badge?></span> <br>
// //                             <!-- <span><small><s>&#8369; <?= number_format($product['price'],2); ?></s></small></span> -->
// //                             <!-- <span class="badge badge-success">New</span> -->
// //                         <?php } ?>
// //                     </div>
// //                     <div class="col-4 text-right">
// //                         <small><b>1 sold</b></small>
// //                     </div>
// //                     <?php if($inv==0){ ?>
// //                     <div class="col-12 text-right text-danger">
// //                         <small><b>SOLD OUT</b></small>
// //                     </div>
// //                     <?php } ?>
// //                 </div>

// //             </div>                
// //         </div>
// //     <?php } ?>
// //     <?php if($category['id'] == $product['category_id'] && $count < 5){ $count ++;?>                   
// //         <div class="col-lg-2  col-md-4 col-sm-6 mt10  bg-white m-1 p-1">
// //             <?php
// //                 if($product['img'] == '' || $product['img'] == NULL){
// //                     $image_path = base_url('assets/img/shop_logo.png');
// //                 }
// //                 else{                                
// //                     $image_path = base_url('assets/uploads/products/').str_replace('==','',$product['img']);
// //                 }
// //             ?>
// //             <div class="product-img " style="background-image: url(<?= $image_path; ?>); width: 100%;" data-product_id="<?= $product['id']; ?>">
// //                 <div class="product-info <?= sizeof($product['variants']) == 1 ? 'single-variant' : '';?>">
// //                 <strong class="product-name"><?= $product['name']; ?></strong><br>
// //                 <?php foreach($product['variants'] as $variant){ ?>
// //                     <span class="badge badge-info size-select"><?= $variant['name']; ?></span> <span>&#8369; <?= number_format($variant['price'],2); ?></span><br>
// //                 <?php } ?>
                
// //                 </div>   
// //             </div>  
// //             <div class="ml5">
            
// //                 <div class="row">
// //                     <div class="col-8">
// //                         <?php 
// //                             $inv = 0;
// //                             $price = 0;
// //                             if(isset($product['variants'][0])){
// //                                 foreach($product['variants'] as $variant){
// //                                     $inventories = $this->model_products->get_inventorydetails($variant['id']);
// //                                     foreach($inventories as $inventory){
// //                                         $now = time();
// //                                         $expiration = strtotime($inventory['date_expiration']);
// //                                         if(date('Y-m-d',$expiration) > date('Y-m-d') && $variant['id'] ==  $inventory['product_id']){
// //                                             $inv += $inventory['qty'];
// //                                             //(round(($expiration - $now)/(60*60*24))+1);
// //                                         }
// //                                     }
// //                                 }
// //                             ?>
// //                             <strong>&#8369; <?= number_format($product['variants'][0]['price'],2); ?></strong>
// //                         <?php } else {
// //                             $discount = $product['discount'];
// //                             $newprice = 0;
// //                             $badge = '';
// //                             if($discount['discount_type'] == 1){
// //                                 if($discount['disc_amount_type'] == 2){
// //                                     $newprice = $product['price'] - ($product['price'] * ($discount['disc_amount']/100));
// //                                     $badge = '- '.$discount['disc_amount'].'% off';
// //                                     if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
// //                                         $newprice = $discount['max_discount_price'];
// //                                     }
// //                                 }else{
// //                                     $newprice = $product['price'] -$discount['disc_amount'];
// //                                     $badge = '- &#8369; '.$discount['disc_amount'].' off';
// //                                     if($discount['max_discount_isset'] && $newprice < $discount['max_discount_price']){
// //                                         $newprice = $discount['max_discount_price'];
// //                                     }
// //                                 }
// //                             }
// //                             foreach($product['inventory'] as $inventory){
// //                                 $now = time();
// //                                 $expiration = strtotime($inventory['date_expiration']);
// //                                 if(date('Y-m-d',$expiration) > date('Y-m-d') && $product['id'] ==  en_dec('en',$inventory['product_id'])){
// //                                     $inv += $inventory['qty'];
// //                                     //(round(($expiration - $now)/(60*60*24))+1);
// //                                 }
// //                             }
// //                         ?>
// //                             <strong>&#8369; <?= number_format($newprice,2); ?></strong>&nbsp;<span class=" mr-1 badge badge-danger"><?=$badge?></span> <br>
// //                             <span><small><s>&#8369; <?= number_format($product['price'],2); ?></s></small></span>
// //                             <!-- <span class="badge badge-success">New</span> -->
// //                         <?php } ?>
// //                     </div>
// //                     <div class="col-4 text-right">
// //                         <small><b>1 sold</b></small>
// //                     </div>
// //                     <?php if($inv==0){ ?>
// //                     <div class="col-12 text-right text-danger">
// //                         <small><b>SOLD OUT</b></small>
// //                     </div>
// //                     <?php } ?>
// //                 </div>

// //             </div>                
// //         </div>
// //     <?php } ?>
// // <?php } ?>
// // <?php } ?>             
// // <?php if(count($products) > 5){ ?>
// // <div class="col-12  d-flex justify-content-center mt-2 mb-2">
// // <button class="btn btn-primary">Show More</button>
// // </div>
// // <?php } ?>
// // </div>
// // </div>
// // <?php } ?>`;
// //             });
// //             // discount_ids.push(product.id);
// //           });
// //     });
// //     $(".flashsale").html(html);
//   }
// });