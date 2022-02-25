var loadScrolled = true;
var base_url = $("body").data("base_url");
var s3_url = $("body").data("s3_url");
var primary_color_accent = $("body").data("primary-color");

var placeholder_img = $("body").data("placeholder_img");
var page_type = $("#pageActive").data("page");
var page = 1;
var productsResult = {};
var productItems = [];
var draw = 1;
var data = {
	page: "shop",
	keyword: "",
	category: "",
	shipped: "",
	province: "",
	cities: "",
	shop: "",
	id: "",
  price_min: "",
  price_max: "",
  sort_value: "date_created",
  sort_order: "asc"
};

var active_search = decodeURIComponent(window.location.hash.substr(8));

$(function () {
	if (active_search) {
		// alert(active_search)
		if (active_search != "s_product") {
			trigger_search(active_search);
		}
	}

	$(".category_nav").click(function(){
		$("html").removeClass("header__bottom--show");
		let active_nav = $(this).attr('href').substr(8);
		trigger_search(active_nav);
		data["keyword"] = active_nav;
		getItems(data);
	});

	$(document).ready(() => {
		draw = 1;
		if (page_type == "product") {
			data["page"] = "products";
			data["shop"] = $(".add_to_cart").data("shop");
			data["category"] = $(".add_to_cart").data("category");
			data["id"] = $(".add_to_cart").data("productid");
		}
		else if (page_type == "search") {
      data['page'] = page_type;
			data["keyword"] = $("#pageActive").data("keyword");

			$(".search__input").val($("#pageActive").data("keyword"));
		}
		else if (page_type == "store") {
			data["page"] = "store";
			data["shop"] = $("#pageActive").data("shopid");
		}

		var get_shipping_locs = $("#get_shipping_locs").val();
		if(get_shipping_locs!=""){
			var ship_l=get_shipping_locs.split(",");
			ship_l[4]=decodeURIComponent(ship_l[4].replace(/comma/g,','));
			ship_l[4]=decodeURIComponent(ship_l[4].replace(/openp/g,'('));
			ship_l[4]=decodeURIComponent(ship_l[4].replace(/closep/g,')'));
			if(ship_l[4]=="SELECT CITY"){
				// $('#header-location').text("Philippines");
			}else{
				// $('#header-location').text(ship_l[4]);
				data['page'] = ship_l[0];
				data['cities'] = ship_l[1];
				data['province'] = ship_l[2];
				data['shipped'] = ship_l[3];
			}
		}
		//console.log(data);
		getItems(data);
		// getCartItems();
		// loadCart();
		$("#page_number").html(page);
	});

	fbq("track", "PageView");



	$(document).on("click", ".buy_now", (event) => {
		event.preventDefault();



		$("#cart_count").html(cart_count++);
		var itemid = event.currentTarget.dataset.id;
		var shop = event.currentTarget.dataset.shop;
		var productid = event.currentTarget.dataset.productid;
		var unit = event.currentTarget.dataset.unit;
		var category = event.currentTarget.dataset.category;
		var primary_pics = event.currentTarget.dataset.primary_pics;
		var max_qty_isset = event.currentTarget.dataset.max_qty_isset;
		var max_qty = event.currentTarget.dataset.max_qty;
		var variant_isset = event.currentTarget.dataset.variant_isset;
		var min = event.currentTarget.dataset.min;
		var max = event.currentTarget.dataset.max;
		var quantity = parseFloat($("#shop_quantity_id_" + productid).val())*1.00;
		var data = {
			item: {
				id: itemid,
				shop: shop,
				shopname: "",
				productid: productid,
				quantity: quantity,
				itemname: $("#productName_id_" + productid).html(),
				unit: unit,
				category: category,
				primary_pics,
				max_qty_isset,
				max_qty,
				variant_isset,
				min,
				max
			},
		};
		$.ajax({
			url: base_url + "api/insertCartItems",
			method: "POST",
			data: data,
			success: (response) => {
				var result = JSON.parse(response);
				// console.log(result);
				if(result.success) {
					$("#quantity_id_" + productid).val(quantity);
					loadCart(result.cart);
					getCartItems(result.cartData);
					showToast({
						type: "success",
						css: "toast-top-full-width mt-5",
						msg: "Successfully added item to the cart",
					});

					fbq("track", "AddToCart");

					window.location.replace(base_url + "shop/checkout");
				}
				else {
					showToast({
						type: "warning",
						css: "toast-top-full-width mt-5",
						msg: "You have reached the purchase limit for this item.",
					});
				}
			},
			error: (err) => {
				console.log(err);
			},
		});
	});


	$(document).on("click", ".add_to_cart", (event) => {
		event.preventDefault();

		$("#cart_count").html(cart_count++);
		var itemid = event.currentTarget.dataset.id;
		var shop = event.currentTarget.dataset.shop;
		var productid = event.currentTarget.dataset.productid;
		var unit = event.currentTarget.dataset.unit;
		var category = event.currentTarget.dataset.category;
		var primary_pics = event.currentTarget.dataset.primary_pics;
		var max_qty_isset = event.currentTarget.dataset.max_qty_isset;
		var max_qty = event.currentTarget.dataset.max_qty;
		var variant_isset = event.currentTarget.dataset.variant_isset;
		var min = event.currentTarget.dataset.min;
		var max = event.currentTarget.dataset.max;
		var quantity = parseFloat($("#shop_quantity_id_" + productid).val())*1.00;

		var data = {
			item: {
				id: itemid,
				shop: shop,
				shopname: "",
				productid: productid,
				quantity: quantity,
				itemname: $("#productName_id_" + productid).html(),
				unit: unit,
				category: category,
				primary_pics,
				max_qty_isset,
				max_qty,
				variant_isset,
				min,
				max
			},
		};

		$.ajax({
			url: base_url + "api/insertCartItems",
			method: "POST",
			data: data,
			success: (response) => {
				var result = JSON.parse(response);
				// console.log(result);

				if(result.success) {
					$("#quantity_id_" + productid).val(quantity);
					loadCart(result.cart);
					getCartItems(result.cartData);
					showToast({
						type: "success",
						css: "toast-top-full-width mt-5",
						msg: "Successfully added item to the cart",
					});

					fbq("track", "AddToCart");
				}
				else {
					showToast({
						type: "warning",
						css: "toast-top-full-width mt-5",
						msg: "You have reached the purchase limit for this item.",
					});
				}
			},
			error: (err) => {
				console.log(err);
			},
		});
	});
});

$(".load-more").click(() => {
  paginate("next");
  draw = draw + 1;
	// getItems(data);
	loadProducts(true);
})

$(".next-btn").click(() => {
  paginate("next");
  draw = draw + 1;
	getItems(data)
})
$(".prev-btn").click(() => {
  paginate("prev");
  draw = draw + 1;
	getItems(data)
})
$(".last-btn").click(() => {
  paginate("last");
  draw = draw + 1;
	getItems(data)
})
$(".first-btn").click(() => {
  paginate("first");
  draw = draw + 1;
	getItems(data)
})

function getItems(data) {
	$("#items-table-body").empty();
	// console.log(page);

	$.ajax({
		url: base_url + "api/getItems",
		method: "POST",
		// method: "GET",
		data: {
			data: data,
			page: page,
		},
		beforeSend: () => {
			showCover("Loading items...");
			$("#productsTable").empty();
			if(page_type == 'product'){
				$('.related-product-div').show();
			}
		},
		success: (response) => {
			// result = JSON.parse(response);
			result = response;
			productsResult = result;
			loadProducts();
			$("#lastPage").val(result.totalRecords);
			paginate("default");
			loadScrolled = true;
			if(page_type == 'product' && productsResult.data.length == 0){
				$('.related-product-div').hide();
			}
			hideCover();
		},
		error: (error) => {
			console.log(error);
		},
	});
}

function loadProducts(click = false) {

	// var records = productsResult.data;
	// if (page_type == "search") {
	// 	// Display per page
		// var records = productsResult.data.slice((parseInt(page) - 1) * 20, 20 * page);
	// 	// Display load more

	// 	var records = productsResult.data.slice(0, 30 * page);

	// 	if(records.length % 30 > 0 || productsResult.data.length == 30 || records.length == productsResult.data.length){
	// 		$("#load-more").attr("hidden", true);
	// 	}
	// 	else{

	// 		$("#load-more").attr("hidden", false);
	// 	}
	// }
	// else {
		// Display load more
	// var records = productsResult.data.slice(0, 30 * page);
	var records=[];
	if(productsResult.data.length>30 ||Object.keys(productsResult.data).length>30 ){
		try{

			records = productsResult.data.slice((parseInt(page) - 1) * 30, 30 * page);
		}catch(e){
			var arr_temp=[];
			$.each(productsResult.data, (i, val) => {
				arr_temp.push(val);
			});
			//console.log(arr_temp);
			records=arr_temp;
		}
	}else if(Object.keys(productsResult.data).length<=30){
		records = productsResult.data;
	}
	if(records.length % 30 > 0 || productsResult.data.length == 30 || records.length == productsResult.data.length){
		$("#load-more").attr("hidden", true);
	}
	else{
		$("#load-more").attr("hidden", false);
	}
	// }
	if (records.length > 0||Object.keys(productsResult.data).length>0) {

		if (productItems.length == 0) {
			$.each(records, (i, val) => {
				productItems.push(val.itemname);
				$("#productsTable").append(renderTableItem(val));
			});
		} else {
			$.each(records, (i, val) => {
				$("#productsTable").append(renderTableItem(val));
			});
		}
	} else {
		if(click){
			$("#load-more").attr("hidden", true);
		}else{
			$("#productsTable").append(renderEmptySearch());
		}
	}

	observer.observe();
}

$(document).on("click", ".qty_plus_shop", (evt) => {
	var target = evt.currentTarget.dataset.id;
	var targetRowInput = "#quantity_id_" + target;
	var targetRowInput = "#shop_quantity_id_" + target;
	var currentValue = $(targetRowInput).val();
	$(targetRowInput).val(parseInt(currentValue) + 1);
});

$(document).on("click", ".qty_minus_shop", (evt) => {
	var target = evt.currentTarget.dataset.id;
	var targetRowInput = "#quantity_id_" + target;
	var targetRowInput = "#shop_quantity_id_" + target;
	var currentValue = $(targetRowInput).val();
	if (parseInt(currentValue) > 1) {
		$(targetRowInput).val(parseInt(currentValue) - 1);
	}
});

$(document).on("keypress", ".shop-input_qty", (evt) => {
	evt = evt ? evt : window.event;
	var len = evt.currentTarget.value;
	var charCode = evt.which ? evt.which : evt.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	if (len.length >= 11) {
		return false;
	}
	return true;
});

$(document).on("focusout", ".shop-input_qty", (evt) => {
	var len = evt.currentTarget.value;
	//console.log(len);
	if (!len || len == 0) {
		evt.currentTarget.value = 1;
	}
});


// <a data-base_url="${base_url}" data-id="${value.Id}" class="single_product_click">
// <a href="${base_url}main/products/${value.Id}">
// <img data-src="${base_url}assets/img/products-250/webp/${value.Id}.webp" src="${placeholder_img}" onerror="this.onerror=null; this.src='${base_url}assets/img/products-250/${value.Id}.jpg'" class="lozad"/>
function renderTableItem(value, keyword='') {
	let com_price = '';
	// console.log(value);
	if(value.variant_isset == 1 && value.parent_product_id == null){
		// edited 05/29/21
		if(value.min != null){
			let min_arr = value.min.toString().split(',');
		}else{
			let min_arr = [];
		}
		//console.log("min_arr:"+min_arr);
		if(value.max != null){
			let max_arr = value.max.toString().split(',');
		}else{
			let max_arr = [];
		}
		//console.log("max_arr:"+max_arr);
		if(value.min != null && value.max != null){
			let min_arr = value.min.toString().split(',');
			let max_arr = value.max.toString().split(',');

			let min = 0;
			let max = 0;

			min = min_arr[0];
			max = max_arr[0];

			// if(user_type=="JC"||user_type=="Startup"||user_type=="MCMEGA"||user_type=="MCJR"||user_type=="MC"||user_type=="MCSUPER"||user_type=="Others"){
			// 	range_price = "₱"+numberFormatter(min,2);
			// }else{
			// 	if(min == max){
			// 		range_price = "₱"+numberFormatter(min,2);
			// 	}else{
			// 		range_price = `<span style="font-size:1rem;color: var(--primary-color);font-weight: 500;">
			// 		 ${"₱"+numberFormatter(min,2)+" - ₱"+numberFormatter(max,2)}</span>`;
			// 	}
		 //  }
		 value.price = min;
		}
	}
	if(parseFloat(value.compare_at_price) != 0.00 && parseFloat(value.compare_at_price) > 0 && parseFloat(value.compare_at_price) > parseFloat(value.price)){
		com_price =
		`
			<span class = "compared-price" style = "display:block;font-size:10px;padding:10px 0px 0px 10px !important;position: absolute;bottom:25px;">
				₱ <span style = "text-decoration:line-through;">${value.compare_at_price}</span>
				&nbsp;
				<span class="badge badge-primary" style = "background-color:#${primary_color_accent} !important;">
					${100 - (Math.round( (parseFloat(value.price) / parseFloat(value.compare_at_price)) * 100 ))}% OFF
				</span>
			</span>
		`;
	}

	if((value.tq_isset != null && value.tq_isset > 0 && value.no_of_stocks != null && value.no_of_stocks > 0) || value.tq_isset == null || value.tq_isset == 0 ||
		(value.cont_selling_isset != null && value.cont_selling_isset > 0))
	return `
	    <div class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-2 product-container">
            <div class="product-item">
                <a href="${base_url}products/${value.Id}">
                    <div class="product-image">
                        <img data-src="${s3_url}assets/img/${value.shopcode}/products-250/${value.Id}/${remove_ext(value.primary_pics)}.jpg" src="${placeholder_img}" onerror="this.onerror=null; this.src='${s3_url}assets/img/products/${value.Id}.png'" class="lozad"/>
                    </div>

                    <div hidden id="productName_id_${value.Id}">${value.itemname}</div>
                    <div class="product-content">
                        <h6 class="product-title">
                            ${value.itemname}
                        </h6>
                        <div class="product-detail">
                            ${value.otherinfo}
                        </div>
                    </div>
                </a>
                <div class="row">
                    <div class="col-12 col-md-9">
                        <div class="product-quantity">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary quantity__minus qty_minus_shop" type="button" data-id="${value.Id}"><i class="fa fa-minus"></i></button>
                                </div>
                                <input type="number" class="form-control text-center quantity__input shop-input_qty" value="1" id="shop_quantity_id_${value.Id}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary quantity__plus qty_plus_shop" type="button" data-id="${value.Id}"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="product-footer">
										${com_price}
                    <div class="row no-gutters">

                        <div class="col product-footer-content">
                            <span class="product-price">
                                ₱${numberFormatter(value.price, false)}
                            </span>
                        </div>
                        <div class="col-5 col-md-auto text-right">
                            <button class="btn product-button add_to_cart"
                                data-unit="${value.otherinfo}"
                                data-category="${value.category}"
                                data-id="${value.itemid}"
                                data-shop="${value.sys_shop}"
                                data-productid="${value.Id}"
																data-primary_pics="${value.primary_pics}"
																data-max_qty_isset = "${value.max_qty_isset}"
																data-max_qty = "${value.max_qty}"
																data-variant_isset= "${value.variant_isset}"
																data-min= "${value.min}"
																data-max= "${value.max}"
                            >
                                Add <i class="fa fa-cart-plus ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
	        </div>
	    </div>
        `;
    else
	return `
	    <div class="col-6 col-md-4 col-lg-3 col-xl-2 product-container">
            <div class="product-item">
                <a href="${base_url}main/products/${value.Id}">
                    <div class="product-image">
                        <img style="filter: grayscale(100%);" data-src="${s3_url}assets/img/${value.shopcode}/products-250/${value.Id}/${remove_ext(value.primary_pics)}.jpg" src="${placeholder_img}" onerror="this.onerror=null; this.src='${s3_url}assets/img/products/${value.Id}.png'" class="lozad"/>
                    </div>

                    <div hidden id="productName_id_${value.Id}">${value.itemname}</div>
                    <div class="product-content">
                        <h6 class="product-title">
                            ${value.itemname}
                        </h6>
                        <div class="product-detail">
                            ${value.otherinfo}
                        </div>
                    </div>
                </a>
                <div class="row">
                    <div class="col-12 col-md-9">
                        <div class="product-quantity">

	                        <h6 class="product-title" style="color: red;">
	                            Sold Out
	                        </h6>
                        </div>
                    </div>
                </div>
                <div class="product-footer">
										${com_price}
                    <div class="row no-gutters">

                        <div class="col product-footer-content">
                            <span class="product-price">
                                ₱${numberFormatter(value.price, false)}
                            </span>
                        </div>
                        <div class="col-5 col-md-auto text-right">

                        </div>
                    </div>
                </div>
	        </div>
	    </div>
        `;

}

function renderEmptySearch() {

	$("#load-more").attr("hidden", true);
	return `
    <div class="search-empty-result-section">
        <div>
            <img src="${base_url}assets/img/nosearch.png"
            class="search-empty-result-section__icon">
            <div class="search-empty-result-section__title">
                No products found
            </div>
            <div class="search-empty-result-section__hint">
                Try different or more general keywords
            </div>
        </div>
    </div>
    `;
}

var search_category = [];
$(document).on('click', '.category-checkbox', function(e) {
  var cat_id = $(this).val();
  if($(this).is(':checked')) {
    search_category.push(cat_id);
  } else {
    var i = search_category.indexOf(cat_id);
    search_category.splice(i, 1);
  }
});

var search_shippedfrom = [];
var search_prov = [];
var search_city = [];

$(document).on('click', '.shipped_category_checkbox', function(e){
	let reg_code = $(this).val();
	let region = $(this).data('region');
	if($(this).is(':checked')){
		$.ajax({
		  url: base_url+'api/Shop/get_cities',
		  type: 'post',
		  data:{reg_code},
		  beforeSend: function(){
		    showCover('Processing...');
		  },
		  success: function(data){
				hideCover();
		    if(data.success == 1){
					$(`.sub-checkboxes[data-region=${region}]`).html('');
					$.each(data.cities, function(i,val){
						// checkbox for cities
						if(val['type'] == 'city'){
							$(`.sub-checkboxes[data-region=${region}]`).append(
								`
									<div class="form-check">
										<input class="form-check-input checkbox_${val['type']}" data-type = "reg_city" data-reg = "${val['regDesc']}" data-prov = "${val['provCode']}" type="checkbox" id="${val['type']}_${val['value']}" value="${val['value']}">
										<label class="form-check-label" for="${val['type']}_${val['value']}">${val["name"]}</label>
									</div>
								`
							);
						}

						// checkbox for province
						if(val['type'] == 'prov'){
							$(`.sub-checkboxes[data-region=${region}]`).append(
								`
									<div class="form-check">
										<input class="form-check-input shipped_prov checkbox_${val['type']}" data-type = "reg_city" data-reg = "${val['regCode']}" data-prov = "${val['provCode']}" type="checkbox" id="${val['type']}_${val['value']}" value="${val['value']}">
										<label class="form-check-label" for="${val['type']}_${val['value']}">${val["name"]}</label>
										<div class="sub-checkboxes sub-checkboxes-prov" data-prov="${val['provCode']}" style = "left:-20px !important;">

										</div>
									</div>
								`
							);
						}
					});
		    }else{
					$(`.sub-checkboxes[data-region=${region}]`).removeClass("sub-checkboxes--show");
					// showToast({
					// 	type: "warning",
					// 	css: "toast-top-full-width mt-5",
					// 	msg: data.message,
					// });
		    }
		  },
		  error: function(){
				showToast({
					type: "error",
					css: "toast-top-full-width mt-5",
					msg: 'Oops! Something went wrong. Pleas try again',
				});
		    hideCover();
		  }
		});
	}

});

$(document).on('click', '.shipped_prov', function(e){
	let prov = $(this).data('prov');
	if($(this).is(':checked')){
		$.ajax({
		  url: base_url+'api/Shop/get_shipping_city_w_prov',
		  type: 'post',
		  data:{prov_code: prov},
		  beforeSend: function(){
		    showCover('Processing...');
				$(`.sub-checkboxes-prov[data-prov=${prov}]`).html('');
		  },
		  success: function(data){
		    hideCover();
		    if(data.success == 1){
					$.each(data.cities, function(i,val){
						// checkbox for cities
						if(val['type'] == 'city'){
							$(`.sub-checkboxes-prov[data-prov=${prov}]`).append(
								`
									<div class="form-check">
										<input class="form-check-input checkbox_${val['type']}" data-type = "prov_city" data-reg = "${val['regDesc']}" data-prov = "${val['provCode']}" type="checkbox" id="${val['type']}_${val['value']}" value="${val['value']}">
										<label class="form-check-label" for="${val['type']}_${val['value']}">${val["name"]}</label>
									</div>
								`
							);
						}
					});
		    }else{
					showToast({
						type: "warning",
						css: "toast-top-full-width mt-5",
						msg: data.message,
					});
		    }
		  },
		  error: function(){
				showToast({
					type: "error",
					css: "toast-top-full-width mt-5",
					msg: 'Oops! Something went wrong. Pleas try again',
				});
		    hideCover();
		  }
		});
	}
});
// get all check regions
$(document).on('click', '.shipped_category_checkbox', function(e) {
  var cat_id = $(this).val();
	cat_id = cat_id.split(',');
  if($(this).is(':checked')) {
		cat_id.forEach((reg) => {
			if($.inArray(reg,search_shippedfrom) == -1){
				search_shippedfrom.push(reg);
			}
		});
  } else {
		cat_id.forEach((reg) => {
			if($.inArray(reg,search_shippedfrom) != -1){
				var i = search_shippedfrom.indexOf(reg);
				search_shippedfrom.splice(i, 1);
			}
		});
  }

});
// get all check province
$(document).on('click', '.checkbox_prov', function(){
	let prov = $(this).val();
	let reg = $(this).data('reg');
	if($(this).is(':checked')){
		search_prov.push(prov);
		var i = search_shippedfrom.indexOf(reg);
		if(i > 0){
			search_shippedfrom.splice(i,1);
		}
	}else{
		var i = search_prov.indexOf(prov);
		search_prov.splice(i, 1);
		var selected_prov = $(`.checkbox_prov[data-reg=${reg}]`).filter(function(){ return this.checked});
		if(selected_prov.length == 0){
			search_shippedfrom.push(reg);
		}
	}
});
// get all check city
$(document).on('click', '.checkbox_city', function(){
	let city = $(this).val();
	let reg = $(this).data('reg');
	let prov = $(this).data('prov');
	let type = $(this).data('type');
	if($(this).is(':checked')){
		search_city.push(city);
		var i = search_shippedfrom.indexOf(reg);
		if(i > 0){
			search_shippedfrom.splice(i, 1);
		}

		if(type == "prov_city"){
			var x = search_prov.indexOf(prov);
			if(x > 0){
				search_prov.splice(x, 1);
			}
		}
	}else{
		var i = search_city.indexOf(city);
		search_city.splice(i, 1);
		var selected_city = $(`.checkbox_city[data-reg=${reg}]`).filter(function(){ return this.checked});
		if(selected_city.length == 0){
			search_shippedfrom.push(reg);
		}

		if(type == "prov_city"){
			var selected_city2 = $(`.checkbox_city[data-prov=${prop}]`).filter(function(){ return this.checked});
			if(selected_city2.length == 0){
				search_prov.push(prov);
			}
		}
	}

});

$(document).on('click', '.search-filter-button', function(e) {
  data['price_min'] = $('#price_min').val();
  data['price_max'] = $('#price_max').val();
  data['category'] = search_category.join();
	data['shipped'] = search_shippedfrom.join();
	data['province'] = search_prov.join();
	data['cities'] = search_city.join();
  getItems(data);
});

$(document).on('change', '.search__input', function(){
	$("#pageActive").data("keyword", $(this).val());
	$('.highlight').text($(this).val());
	data['keyword'] = $(this).val();
});

$(document).on('change', '#sort_value, #sort_order', function(e) {
  data['sort_value'] = $('#sort_value').val();
  data['sort_order'] = $('#sort_order').val();
  getItems(data);
})
/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById("search__input"), productItems);


// for lozad lazy loaading initialization
const observer = lozad(); // passing a `NodeList` (e.g. `document.querySelectorAll()`) is also valid
observer.observe();


// ... trigger the load of a image before it appears on the viewport

//end for lozad lazy loaading initialization
