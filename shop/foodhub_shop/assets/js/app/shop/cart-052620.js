var base_url = $("body").data("base_url");
var s3_url = $("body").data("s3_url");

var cartItems = [];
$(function () {
    $(".search__button_old").click((e) => {
        var searchKey = $(".search__input").val();
        window.location = base_url + "#search=" + searchKey;
    });

    $(".search__input_old").keyup(function (event) {
        if (event.keyCode === 13) {
            $(".search__button_old").click();
            // closeAllLists();
        }
    });

	$(document).ready(() => {
		showCover("Loading cart...");
		loadCartPage(false);
		hideCover();
	});

	var loading = false;

	$(document).on("select", ".quantity__input", (event) => {
		$("#proceedCheckout").addClass("disabled");
	});

	$(document).on("blur", ".quantity__input", (event) => {
		if (!loading) {
			$("#proceedCheckout").removeClass("disabled");
		}
	});

	$(document).on("change", ".quantity__input", (event) => {
		var id = event.currentTarget.dataset.id;
		var input = event.currentTarget.dataset.input;
		var value = event.currentTarget.value;
		// console.log(id, input, value)
		var oldQuantity = cartItems[id]["quantity"];
		cartItems[id]["quantity"] = value;
		cartItems[id]["total_amount"] =
			parseInt(value) * parseInt(cartItems[id]["price"]);
		$(event).data("input", value);
		// console.log(cartItems)
		$.ajax({
			url: base_url + "api/Shop/changeQuantityInCart",
			method: "POST",
			data: {
				cart: cartItems,
				oldQuantity: oldQuantity,
				newQuantity: value,
			},
			beforeSend: () => {
				$("#proceedCheckout").addClass("disabled");
				$("#total_amount_cart").html(
					`Total:<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
				);
				loading = true;
			},
			success: (data) => {
				var result = JSON.parse(data);
				// console.log(data)
				loadCart(result.cart);
				getCartItems(result.cartData);
				// console.log('gg')
				$("#proceedCheckout").removeClass("disabled");
				loading = false;
			},
		});
	});

  $(document).on('click', '.btn_add_item', function(evt){
  	var target = evt.currentTarget.dataset.id;
  	var targetRowInput = "#quantity_id_" + target;
  	var currentValue = evt.currentTarget.dataset.quan;
  	// console.log('no',currentValue);
  	let productid = $(this).data('id');
  	let quantity = parseFloat($(`#quantity_id_${productid}`).val());
  	let shop = parseFloat($(this).data('shop'));
  	let max_qty_isset = evt.currentTarget.dataset.max_qty_isset;
  	let max_qty = evt.currentTarget.dataset.max_qty;
  	let data = {
  		item: {
  			productid: productid,
  			quantity: currentValue,
  			shop: shop,
  			max_qty_isset,
  			max_qty
  		}
  	}
  	$.ajax({
  	  url: base_url+'api/Shop/add_cart_item',
  	  type: 'post',
  	  data:data,
  	  beforeSend: function(){
  	    showCover("Updating Cart ...");
  	  },
  	  success: function(data){
  	    hideCover();
  	    if(data.success == 1){
  				// $(targetRowInput).val(parseInt(currentValue) + 1);
  	    }else{
  				showToast({
  					type: "warning",
  					css: "toast-top-full-width mt-5",
  					msg: data.message,
  				});
  	    }
  			loadCartPage();
			  console.log(data);
        //$('.cartpage-highlight').html(numberFormatter(data.total_checkout_amount));
  	  },
  	  error: function(){
  	    hideCover();
  	  }
  	});
  });

  $(document).on('focusout', '.checkout_input_qty', function(evt){
  	var target = evt.currentTarget.dataset.id;
  	var targetRowInput = "#quantity_id_" + target;
  	var currentValue = evt.currentTarget.value;;
  	// console.log('no',currentValue);
  	let productid = $(this).data('id');
  	let quantity = parseFloat($(this).val());
  	let shop = parseFloat($(this).data('shop'));
  	let max_qty_isset = evt.currentTarget.dataset.max_qty_isset;
  	let max_qty = evt.currentTarget.dataset.max_qty;
    currentValue = (currentValue <= 0) ?	1 : currentValue;
  	let data = {
  		item: {
  			productid: productid,
  			quantity: currentValue,
  			shop: shop,
  			max_qty_isset,
  			max_qty
  		}
  	}

  	$.ajax({
  	  url: base_url+'api/Shop/change_cart_item',
  	  type: 'post',
  	  data:data,
  	  beforeSend: function(){
  	    showCover("Updating Cart ...");
  	  },
  	  success: function(data){
  	    hideCover();
  	    if(data.success == 1){
  				// $(targetRowInput).val(parseInt(currentValue) + 1);
  				loadCartPage();
  				if (data.cart_count <= 0) location.replace(base_url);
  	    }else{
  				showToast({
  					type: "warning",
  					css: "toast-top-full-width mt-5",
  					msg: data.message,
  				});
  				loadCartPage();
          $('.cartpage-highlight').html(numberFormatter(data.total_checkout_amount));
  	    }

  			// $('.btn_add_item').prop('disabled',false);
  			// $('.btn_sub_item').prop('disabled',false);
  	  },
  	  error: function(){
  	    hideCover();
  	  }
  	});
  });

  $(document).on('click', '.btn_sub_item', function(evt){
  	var target = evt.currentTarget.dataset.id;
  	var targetRowInput = "#quantity_id_" + target;
  	var currentValue = evt.currentTarget.dataset.quan;
  	// console.log('no',currentValue);

  	let productid = $(this).data('id');
  	let quantity = parseFloat($(`#quantity_id_${productid}`).val());
  	let shop = parseFloat($(this).data('shop'));
  	let max_qty_isset = evt.currentTarget.dataset.max_qty_isset;
  	let max_qty = evt.currentTarget.dataset.max_qty;
  	let data = {
  		item: {
  			productid: productid,
  			quantity: currentValue,
  			shop: shop,
  			max_qty_isset,
  			max_qty
  		}
  	}

  	$.ajax({
  	  url: base_url+'api/Shop/subtract_cart_item',
  	  type: 'post',
  	  data:data,
  	  beforeSend: function(){
  	    showCover("Updating Cart ...");
  	  },
  	  success: function(data){
  	    hideCover();
  			if(data.success == 1){
  				// if (parseInt(currentValue) > 1) {
  				// 	$(targetRowInput).val(parseInt(currentValue) - 1);
  				// }
  	    }else{
  				showToast({
  					type: "warning",
  					css: "toast-top-full-width mt-5",
  					msg: data.message,
  				});
  	    }
  			loadCartPage();
        $('.cartpage-highlight').html(numberFormatter(data.total_checkout_amount));
  	  },
  	  error: function(){
  	    hideCover();
  	  }
  	});
  });
});

function noCartItems() {
	var renderItem = "";
	renderItem +=
		'<div class="card" style="background-color;border-radius:12px;">';
	renderItem += '<div class="card-body justify-content-center d-flex">';
	renderItem += "<h4>No items available...</h4>";
	renderItem += "</div>";
	return renderItem;
}

function renderHeaderCart() {
	return `
    <div class="col-12">
      <div class="portal-table__titles">
          <div class="col-1"></div>
          <div class="col-1">ID</div>
          <div class="col">Product</div>
          <div class="col-1">Unit</div>
          <div class="col-1">Category</div>
          <div class="col-2">Price</div>
          <div class="col-2 text-center">Quantity</div>
      </div>
    </div>
  `;
}

function addCartItem(shop, e, display = "mobilecart") {
  // console.log(shop);
  let total_amount = 0;
	var productCard = "";
	productCard +=
		`<div class="product-card">
            <div class="product-card-header">
                <div class="row">
                    <div class="col">
                        <div class="row no-gutters">
                            <div class="col-2 d-flex align-items-center">
                                <div class="company-image" style="background-image: url(` +
		s3_url +
		`assets/img/shops-60/` +
		shop.logo+
		`)"></div>
                            </div>
                            <div class="col d-flex align-items-center">
                                <div class="product-card-title">${shop.shopname}</div>
                            </div>

                            <div class="col-auto d-flex align-items-center justify-content-end">
                                <div class="product-card-delete removeAll btn" data-id="${e}" data-shopid="${shop.shopid}" data-display="${display}">
                                    Remove All
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

	cartItems = shop.items;
	$.each(cartItems, (e, item) => {
    total_amount += parseFloat(item.quantity * item.price);
		productCard +=
			`<div class="product-card-body">
                <div class="product-card-item">
                    <div class="row">
                        <div class="col">
                            <div class="row no-gutters">
                                <div class="col-4">
                                    <div class="product-card-image" style="background-image: url(` +s3_url +`assets/img/${shop.shopcode}/products-250/` +item.productid +`/` +remove_ext(item.primary_pics) +`.jpg)"></div>
                                </div>
                                <div class="col product-card-content">
                                    <div class="product-card-name">
                                        ${item.itemname}
                                    </div>
                                    <div class="product-card-quantity">
                                        <span class="unit-price">Unit Price: &#8369; ${numberFormatter(item.price,false)}</span>
                                    </div>
                                    <div class="product-quantity" style = "padding-left:0px;padding-right:0px;">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-secondary quantity__minus btn_sub_item" type="button" data-id="${item.productid}" data-quan = "${item.quantity}" data-shop = "${shop.shopid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}"><i class="fa fa-minus"></i></button>
                                            </div>
                                            <input type="number" class="form-control text-center quantity__input checkout_input_qty shop-input_qty" value="${item.quantity}" id="quantity_id_${item.productid}" data-shop = "${shop.shopid}" data-id="${item.productid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary quantity__plus btn_add_item" type="button" data-id="${item.productid}" data-quan = "${item.quantity}" data-shop = "${shop.shopid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="product-card-price">
                                &#8369; ${numberFormatter(item.quantity * item.price, false)}
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="product-card-delete removeCart" data-id="${e}" data-shopid="${shop.shopid}" data-display="${display}">
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
	});
  $()
	return productCard;
}
