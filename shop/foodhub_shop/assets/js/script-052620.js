// console.log(document.querySelector("body").offsetWidth);
var s3_url = $("body").data("s3_url");
var citiesdata = [];
//uses classList, setAttribute, and querySelectorAll
//if you want this to work in IE8/9 youll need to polyfill these
(function () {
	var d = document,
		accordionToggles = d.querySelectorAll(".js-accordionTrigger"),
		setAria,
		setAccordionAria,
		switchAccordion,
		touchSupported = "ontouchstart" in window,
		pointerSupported = "pointerdown" in window;

	skipClickDelay = function (e) {
		e.preventDefault();
		e.target.click();
	};

	setAriaAttr = function (el, ariaType, newProperty) {
		el.setAttribute(ariaType, newProperty);
	};
	setAccordionAria = function (el1, el2, expanded) {
		switch (expanded) {
			case "true":
				setAriaAttr(el1, "aria-expanded", "true");
				setAriaAttr(el2, "aria-hidden", "false");
				break;
			case "false":
				setAriaAttr(el1, "aria-expanded", "false");
				setAriaAttr(el2, "aria-hidden", "true");
				break;
			default:
				break;
		}
	};
	//function
	switchAccordion = function (e) {
		console.log("triggered");
		e.preventDefault();
		var thisAnswer = e.target.parentNode.nextElementSibling;
		var thisQuestion = e.target;
		if (thisAnswer.classList.contains("is-collapsed")) {
			setAccordionAria(thisQuestion, thisAnswer, "true");
		} else {
			setAccordionAria(thisQuestion, thisAnswer, "false");
		}
		thisQuestion.classList.toggle("is-collapsed");
		thisQuestion.classList.toggle("is-expanded");
		thisAnswer.classList.toggle("is-collapsed");
		thisAnswer.classList.toggle("is-expanded");

		thisAnswer.classList.toggle("animateIn");
	};
	for (var i = 0, len = accordionToggles.length; i < len; i++) {
		if (touchSupported) {
			accordionToggles[i].addEventListener("touchstart", skipClickDelay, false);
		}
		if (pointerSupported) {
			accordionToggles[i].addEventListener(
				"pointerdown",
				skipClickDelay,
				false
			);
		}
		accordionToggles[i].addEventListener("click", switchAccordion, false);
	}
})();

// $('input[name="dates"]').daterangepicker();
var mobile = 0;
var pageNumberActive = $("#pageActive").data("num");
var base_url = $("body").data("base_url");
$(".sidenav__menu")
	.find(".sidenav__item")
	.each(function () {
		var activePage = $(this).data("num");
		if (pageNumberActive == activePage) {
			$(this).addClass("sidenav__item--active");
		}

		if (pageNumberActive != activePage) {
			$(this).removeClass("sidenav__item--active");
		}
	});

var pageNumberActiveMobile = $("#pageActiveMobile").data("num");

// header title - either title or search bar for mobile

const headerTitleSearch = $("#headerTitle").data("search");
const headerTitle = $("#headerTitle").data("title");
const headerTitleContainer = $(".search__mobile__container");
const searchMobile = document.querySelector(".search--mobile");
const pageTitle = document.querySelector(".page-title");
// pageTitle.innerHTML = headerTitle;

const h5 = document.createElement("H5");
// const textNode = document.createTextNode(headerTitle);
// h5.appendChild(textNode);
// h5.classList.add("header__mobile-title", "mb-0");

// if (headerTitleSearch) {
//     headerTitleContainer.addClass("search__mobile__container--display");
// } else {
//     searchMobile.appendChild(h5);
// }

$(".bottom-nav")
	.find(".bottom__item")
	.each(function () {
		var activePage = $(this).data("num");

		if (pageNumberActiveMobile == activePage) {
			$(this).addClass("bottom__item--active");
		}
		if (pageNumberActiveMobile != activePage) {
			$(this).removeClass("bottom__item--active");
		}
	});

$(function () {
	$(".unit").selectmenu();
});

$(".dropdown__click").click(function () {
	// $(".header__dropdown").toggleClass("header__dropdown--active");
	$(this).parent().toggleClass("header__dropdown--active");
	$(this)
		.parent()
		.siblings(".header__dropdown")
		.removeClass("header__dropdown--active");
});

$(".dropdown__close").click(function () {
	$(".header__dropdown").removeClass("header__dropdown--active");
});

$(".quantity__plus").click(function () {
	let val = $(this).parent().siblings(".quantity__input").val();
	val++;
	$(this).parent().siblings(".quantity__input").val(val);
});

$(".quantity__minus").click(function () {
	let val = $(this).parent().siblings(".quantity__input").val();
	if (val > 1) {
		val--;
		$(this).parent().siblings(".quantity__input").val(val);
	}
});
$(".cart__menu").click(function () {
	if (mobile) {
		window.location = base_url + "shop/cart";
	} else {
		$(".cart").toggleClass("cart--display");
	}
});

$(".cart__close-icon").click(function () {
	$(".cart").removeClass("cart--display");
});

$(".mobile-search-filter").click(function () {
	$(".search-filter").addClass("search-filter--show");
});

$(".search-filter-button").click(function () {
	$(".search-filter").removeClass("search-filter--show");
});

$(".footer__header--toggle").click(function () {
	$(".checkout__footer").toggleClass("checkout__footer--close");
});

$(".search--mobile button").click(function () {
	$(".search-container").toggleClass("search-container--display");
});

$(".search__close-icon").click(function () {
	$(".search-container").removeClass("search-container--display");
});

$(".search-container--overlay").click(function () {
	$(".search-container").removeClass("search-container--display");
});

// branch dropdown clone for mobile

const branchDropdown = document.querySelector(".branch__dropdown");
const headerMobileItem = document.querySelector(".mobile__branch");
// let dropdownClone = branchDropdown.cloneNode(true);
// dropdownClone.classList.add("branch__dropdown__mobile");
// headerMobileItem.appendChild(dropdownClone);

$(".mobile__branch > .branch__image").click(function () {
	$(this).toggleClass("mobile__dropdown--display");
});

$(".mobile__branch > .branch__image").click(function () {
	$(this).parent(".mobile__branch").toggleClass("mobile__dropdown--display");
	// alert("hello");
});

$(".branch__dropdown__mobile--overlay").click(function () {
	$(this).parent(".mobile__branch").removeClass("mobile__dropdown--display");
});

$(".mobile__branch .dropdown__close").click(function () {
	$(this).parents(".mobile__branch").removeClass("mobile__dropdown--display");
});

// profile clone
// const userProfile = document.querySelector(".profile__menu .dropdown__header");
// const sideNavMobileHeader = document.querySelector(".sidenav__mobile__header");
// let userProfileClone = userProfile.cloneNode(true);
// // dropdownClone.classList.add("branch__dropdown__mobile");
// sideNavMobileHeader.appendChild(userProfileClone);

$(".sidemenu").click(function () {
	$("html").toggleClass("header__bottom--show");
});

$(".sidemenu--close").click(function () {
	$("html").removeClass("header__bottom--show");
});

// $(".sidenav__mobile__close-icon").click(function () {
// 	$(".header__item__mobile").removeClass("sidenav__mobile--display");
// });

function getEstDateDelivery(dates) {
	var possibleDates = [];
	// console.log(dates)
	$.each(dates, (key, val) => {
		if (val == "true") {
			possibleDates.push(key);
		}
	});
	if (possibleDates != []) {
		$.each(possibleDates, (key, val) => {
			// console.log(val)
		});
	}
	// console.log(possibleDates);
}

var cart_count = 0;
function getCartItems(load) {
	var base_url = $("body").data("base_url");
	if (!load) {
		try {
			$.ajax({
				url: base_url + "api/getCartCount",
				method: "GET",
				beforeSend: () => {
					showCover("Getting cart count...");
				},
				success: (response) => {
					// console.log(response)
					var result = JSON.parse(response);
					// console.log(result)
					getEstDateDelivery(result.dates);
					cart_count = result.count;
					$(".cart_count").html(beautify(cart_count));
					$("#total_amount").html(
						"Total: <span>" +
							numberFormatter(result.total_amount, false) +
							"</span>"
					);
					$("#total_amount_cart").html(
						"Total: <span class='cartpage-highlight'>₱" +
							numberFormatter(result.total_amount, false) +
							"</span>"
					);
					if (result.total_amount == 0) {
						$("#proceedCheckout").addClass("disabled");
					}
					hideCover();
				},
				error: (error) => {
					$(".cart_count").html("2");
					$("#total_amount").html("Total: <span>0.00</span>");
					$("#total_amount_cart").html("Total: <span>0.00</span>");
					console.warn(error);
					hideCover();
				},
			});
		} catch (error) {
			console.log("err");
			$(".cart_count").html("1");
			$("#total_amount").html("Total: <span>0.00</span>");
		}
	} else {
		cart_count = load.count;
		$(".cart_count").html(beautify(cart_count));
		$("#total_amount").html(
			"Total: <span>" + numberFormatter(load.total_amount, false) + "</span>"
		);
		$("#total_amount_cart").html(
			"Total: <span>" + numberFormatter(load.total_amount, false) + "</span>"
		);
		if (load.total_amount == 0) {
			$("#proceedCheckout").addClass("disabled");
		}
	}
}

$("#logout_btn").click(() => {
	$.ajax({
		url: base_url + "Main/logout",
		success: (response) => {
			location.reload(true);
		},
	});
});
var branchSelected = 0;
$(".branch_select").click((e) => {
	// console.log(e.currentTarget.dataset.id)
	branchSelected = e.currentTarget.dataset.id;
	if (cart_count == 0) {
		changeBranch();
	} else {
		console.warn("There are items in your cart.");
		$("#changeBranchModal").modal("show");
	}
});

$("#changeBranchModalYes").click(() => {
	changeBranch();
});

$("#changeBranchModalNo").click(() => {
	$("#changeBranchModal").modal("hide");
});

function changeBranch() {
	$.ajax({
		url: base_url + "Main/changeBranch",
		method: "POST",
		data: {
			id: branchSelected,
		},
		success: (response) => {
			var res = JSON.parse(response);
			console.log(res);
			// console.log(JSON.stringify(response, 0, 2))
			location.reload(true);
		},
		error: (error) => {
			console.log(error);
		},
	});
}

$(document).ready(() => {
	loadCart(false);
	getCartItems(false);
	if ($(document).width() <= 992) {
		mobile = 1;
	}

	$(".autoplay").slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		arrows: null,
		responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 5,
					slidesToScroll: 1,
				},
			},
			{
				breakpoint: 576,
				settings: {
					slidesToShow: 3,
				},
			},
		],
	});
	// $.ajax({
	//   url: base_url + "api/orders",
	//   success: (result) => {
	//     console.log(result)
	//   }
	// });
});

$("#shop_proceed").click(() => {
	// if (mobile) {
	// 	window.location = base_url + "shop/cart";
	// } else {
	// 	window.location = base_url + "shop/checkout";
	// }
	window.location = base_url + "shop/checkout";
});

function numberFormatter(number, noZero, noTrailing = false) {
	let formatter;
	if (noTrailing) {
		formatter = new Intl.NumberFormat("en-US", {
			style: "currency",
			currency: "USD",
			minimumFractionDigits: 0,
		});
	} else {
		formatter = new Intl.NumberFormat("en-US", {
			style: "currency",
			currency: "USD",
			minimumFractionDigits: 2,
		});
	}

	var result = formatter.format(number).toString().substring(1);
	// console.log(result)
	result = isNaN(number)
		? "1.00"
		: result == ""
		? "1.00"
		: result == 0 && noZero
		? "1.00"
		: result;
	return result;
}

$(document).on("keypress", ".numberInput", (evt) => {
	evt = evt ? evt : window.event;
	var len = evt.currentTarget.value;
	var charCode = evt.which ? evt.which : evt.keyCode;
	if (charCode == 46) {
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	if (len.length >= 11) {
		return false;
	}
	return true;
});

$(document).on('keypress', '.email_input', function(evt){
	if(evt.which == 32){
		return false;
	}
});

$(document).on("cut copy paste", ".numberInput", function (e) {
	e.preventDefault();
});

$(document).on("focus", ".numberInput", function (e) {
	$(this).select();
});

//PAGINATION
function paginate(movement) {
	var lastPage = $("#lastPage").val();
	// console.log("paginate", lastPage, page)
	switch (movement) {
		case "next":
			page = page + 1;
			break;
		case "prev":
			page = page > 0 ? page - 1 : page;
			break;
		case "first":
			page = 1;
			break;
		case "last":
			page = lastPage;
			break;
		default:
			page = page;
	}
	if (page == lastPage) {
		$(".first").removeClass("disabled");
		$(".prev").removeClass("disabled");
		$(".next").addClass("disabled");
		$(".last").addClass("disabled");
		page = lastPage;
	} else if (page == 1) {
		$(".first").addClass("disabled");
		$(".prev").addClass("disabled");
		$(".next").removeClass("disabled");
		$(".last").removeClass("disabled");
		page = 1;
	} else {
		$(".first").removeClass("disabled");
		$(".prev").removeClass("disabled");
		$(".next").removeClass("disabled");
		$(".last").removeClass("disabled");
	}

	if (page == lastPage && page == 1) {
		$(".first").addClass("disabled");
		$(".prev").addClass("disabled");
		$(".next").addClass("disabled");
		$(".last").addClass("disabled");
	}
	$("#page_number").html(page);
}

function loadCart(load) {
	var page_type = $("#pageActive").data("page");
	// console.log("page",page_type);
	if(page_type == "checkout"){
		return;
	}
	if (!load) {
		$.ajax({
			url: base_url + "api/getCartItems",
			beforeSend: () => {
				showCover("Checking cart items...");
				$("#headerCart").empty();
				$("#headerCart").append(renderLoading());

				$("#cartPage").empty();
				$("#cartPage").append(renderLoading());
			},
			success: (result) => {
				var data = JSON.parse(result);
				$("#headerCart").empty();
				// $("#headerCart").append(cartHeader());
				$("#cartPage").empty();

				if (data.cart) {
					var emptycart = true;
					$.each(data.cart, (e, item) => {
						if (item.items.length > 0) {
							$("#shop_proceed").removeAttr("disabled");

							$("#proceedCheckout").removeClass("disabled");
							emptycart = false;
						}
						try {
							$("#headerCart").append(cartRender(item, e));

							$("#cartPage").append(addCartItem(item, e));
						} catch (err) {}
					});
					if (emptycart) {
						$("#shop_proceed").attr("disabled", true);
						$("#headerCart").html(cartEmpty());

						$("#proceedCheckout").addClass("disabled");
					}
				} else {
					$("#shop_proceed").attr("disabled", true);
					$("#headerCart").html(cartEmpty());

					$("#proceedCheckout").addClass("disabled");
				}
				hideCover();
			},
		});
	} else {
		$("#headerCart").empty();
		$("#headerCart").append(renderLoading());
		$("#headerCart").empty();

		$("#cartPage").empty();
		var emptycart = true;
		$.each(load.cart, (e, item) => {
			if (item.items.length > 0) {
				$("#shop_proceed").removeAttr("disabled");
				emptycart = false;
			}
			try {
				$("#headerCart").append(cartRender(item, e));

				$("#cartPage").append(addCartItem(item, e));
			} catch (err) {}
		});
		if (emptycart) {
			$("#shop_proceed").attr("disabled", true);
			$("#headerCart").html(cartEmpty());
			location.replace(base_url);
		}
	}
}

function loadCartPage(load) {
	if (!load) {
		$.ajax({
			url: base_url + "api/getCartItems",
			beforeSend: () => {
				$("#cartPage").empty();
				$("#cartPage").append(renderLoading());
			},
			success: (result) => {
				var data = JSON.parse(result);
				// console.log(data)
				$("#cartPage").empty();
				// try {
				// 	$("#cartPage").append(renderHeaderCart());
				// } catch (err) {
				// 	return;
				// }
				$("#est_date").html(data.est_date);
				var emptycart = true;
				$.each(data.cart, (e, item) => {
					if (item.items.length > 0) {
						$("#proceedCheckout").removeClass("disabled");
						emptycart = false;
					}
					try {
						$("#cartPage").append(addCartItem(item, e));
					} catch (err) {}
				});
				if (emptycart) {
					$("#proceedCheckout").addClass("disabled");
					location.replace(base_url);
				}
			},
		});
	} else {
		$("#cartPage").empty();
		$("#est_date").html(data.est_date);
		cartItems = data.cart;
		var emptycart = true;

		$.each(data.cart, (e, item) => {
			if (item.items.length > 0) {
				$("#shop_proceed").removeAttr("disabled");
				emptycart = false;
			}
			try {
				$("#cartPage").append(addCartItem(item, e));
			} catch (err) {}
		});
		if (emptycart) {
			$("#shop_proceed").attr("disabled", true);
			$("#headerCart").html(cartEmpty());
			location.replace(base_url);
		}
	}
}

$(document).on("click", ".removeCart", (event) => {
	$(".cart_count").html(beautify(cart_count));
	var id = event.currentTarget.dataset.id;
	var shopid = event.currentTarget.dataset.shopid;
	var display = event.currentTarget.dataset.display;
	$.ajax({
		url: base_url + "api/removeCartItem",
		method: "POST",
		data: {
			id: id,
			shopid: shopid,
		},
		beforeSend: () => {
			$("#headerCart").empty();
			$("#headerCart").append(renderLoading());
		},
		success: (result) => {
			var data = JSON.parse(result);
			loadCart(result.cart);
			getCartItems(result.cartData);
			try {
				if (display != "shipping") loadCheckoutPage(result.cart);
				else proceedToShipping();
			} catch (err) {}
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Item is removed from the cart.",
			});
			if (data.cartData.count <= 0) location.replace(base_url);
		},
	});
});

$(document).on("click", ".removeAll", (event) => {
	$(".cart_count").html(beautify(cart_count));
	var id = event.currentTarget.dataset.id;
	var shopid = event.currentTarget.dataset.shopid;
	var display = event.currentTarget.dataset.display;
	$.ajax({
		url: base_url + "api/removeCartShop",
		method: "POST",
		data: {
			id: id,
			shopid: shopid,
		},
		beforeSend: () => {
			$("#headerCart").empty();
			$("#headerCart").append(renderLoading());
		},
		success: (result) => {
			var data = JSON.parse(result);
			loadCart(result.cart);
			getCartItems(result.cartData);
			try {
				if (display != "shipping") loadCheckoutPage(result.cart);
				else proceedToShipping();
			} catch (err) {}
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Items are removed from the cart.",
			});
			if (data.cartData.count <= 0) location.replace(base_url);
		},
	});
});

function cartRender(shop, e, display = "cartweb") {
	var productCard = "";
	productCard +=
		`<div class="product-card">
            <div class="product-card-header">
                <div class="row">
                    <div class="col">
                        <div class="row no-gutters">
                            <div class="col-1 d-flex align-items-center">
                                <div class="company-image" style="background-image: url(` +
		s3_url +
		`assets/img/shops-60/` +
		shop.logo +
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
		if(item.variant_isset == 1){
 			
	        if(item.min != null){
	            let min_arr = item.min.toString().split(',');
	        }else{
	            let min_arr = [];
	        }
	        
	        if(item.max != null){
	            let max_arr = item.max.toString().split(',');
	        }else{
	            let max_arr = [];
	        }
	        
	        if(item.min != null && item.max != null){
	            let min_arr = item.min.toString().split(',');
	            let max_arr = item.max.toString().split(',');

	            let min = 0;
	            let max = 0;

	            min = min_arr[0];
	            max = max_arr[0];

		         if($.isNumeric(min)){
		         	item.price = min;
		         }
	        }
	    }
		productCard += `<div class="product-card-body">
                <div class="product-card-item">
                    <div class="row">
                        <div class="col">
                            <div class="row no-gutters">
                                <div class="col-2">
                                    <div class="product-card-image" style="background-image: url(${s3_url}assets/img/${
			shop.shopcode
		}/products-50/${item.productid}/${remove_ext(item.primary_pics)}.jpg)"></div>
                                </div>
                                <div class="col product-card-content">
                                    <div class="product-card-name">
                                        ${item.itemname}
                                    </div>
                                    <div class="product-card-quantity">
																				<div class="row">
																						<div class="col-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
																								<div class="product-quantity" style = "padding-left:0px;padding-right:0px;">
																										<div class="input-group">
																												<div class="input-group-prepend">
																														<button class="btn btn-outline-secondary quantity__minus btn_minus_item" type="button" data-id="${item.productid}" data-shop = "${shop.shopid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}"><i class="fa fa-minus"></i></button>
																												</div>
																												<input type="number" class="form-control text-center quantity__input cart_input_qty shop-input_qty" value="${item.quantity}" id="quantity_id_${item.productid}" data-shop = "${shop.shopid}" data-id="${item.productid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}">
																												<div class="input-group-append">
																														<button class="btn btn-outline-secondary quantity__plus btn_plus_item" type="button" data-id="${item.productid}" data-shop = "${shop.shopid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}"><i class="fa fa-plus"></i></button>
																												</div>
																										</div>
																								</div>
																						</div>
																						<div class="col-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12">
																							Unit Price: ₱ ${numberFormatter(item.price, false)}
																						</div>
																				</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="product-card-price">
                                ₱ ${numberFormatter((item.price * item.quantity), false)}
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="product-card-delete removeCart" data-id="${e}" data-shopid="${
			shop.shopid
		}" data-display="${display}">
                                <i class="fa fa-trash"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
	});

	return productCard;
}

function cartHeader() {
	return `
    <div class="portal-table__titles cart-table__titles">
      <div class="col-4">Product</div>
      <div class="col-2">Unit</div>
      <div class="col-2">Price</div>
      <div class="col-2">Quantity</div>
      <div class="col-2">Shop</div>
    </div>
  `;
}

function cartEmpty() {
	return `
    <div class="portal-table__item cart-table__item">
        <div class="portal-table__column col-6 col-lg-12 portal-table__product text-center" style="color:var(--gray);font-size:18px;font-weight:100;">There are no items on cart...</div>
    </div>
  `;
}

function renderLoading() {
	return `
  <div class="col-12 col-md-12 col-lg-12">
    <div class="d-flex justify-content-center">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
  `;
}

beautify = (n) =>
	((Math.log10(n) / 3) | 0) == 0
		? n
		: Number((n / Math.pow(10, ((Math.log10(n) / 3) | 0) * 3)).toFixed(1)) +
		  ["", "K", "M", "B", "T"][(Math.log10(n) / 3) | 0];

function showToast(toast = { type, css, msg }) {
	toastr.options.extendedTimeOut = 0; //1000;
	toastr.options.timeOut = 2000;
	toastr.options.fadeOut = 250;
	toastr.options.fadeIn = 250;
	toastr.options.positionClass = toast.css;
	toastr[toast.type](toast.msg);
	toastr.options = {
		preventDuplicates: true,
		preventOpenDuplicates: true,
	};
}

// Inventory Item Quantity Change

// const adjustTitleContainer = document.querySelector(".quantity-adjust__title");
// const adjustPlus = document.querySelector(".quantity-adjust__icon-plus");
// const adjustMinus = document.querySelector(".quantity-adjust__icon-minus");

$(".quantity-adjust__icon").click(function () {
	$(".quantity-adjust__container").addClass(
		"quantity-adjust__container--display"
	);
	$(".quantity-adjust__title").html(`${$(this).data("title")} by`);
});

$(".quantity-adjust__close-icon, .quantity-adjust--overlay").click(function () {
	$(".quantity-adjust__container").removeClass(
		"quantity-adjust__container--display"
	);
});

$(".myhover").hover(function () {
	$(this).css("border", "1px solid #ff4444");
	$(this).siblings().css("border", "1px solid gainsboro");
});

// product gallery

function myFunction(imgs) {
	// Get the expanded image
	var expandImg = document.getElementById("expandedImg");
	// Get the image text
	var imgText = document.getElementById("imgtext");
	// Use the same src in the expanded image as the image being clicked on from the grid
	expandImg.src = imgs.src;
	// Use the value of the alt attribute of the clickable image as text inside the expanded image
	imgText.innerHTML = imgs.alt;
	// Show the container element (hidden with CSS)
	expandImg.parentElement.style.display = "block";
}

// function trigger_search(active_nav){
// 	$(window).scrollTop(0);
// 	$("#pageActive").data("page", 'search');
// 	page_type = 'search';
// 	$("#pageActive").data("keyword", active_nav);

// 	$(".ad-banner-section").hide();
// 	$("#searchDiv").removeAttr('hidden');
// 	$("#searchkey_span").text(active_nav);

// 	$(".search__input").val(active_nav);

// 	draw = 1;
// }

$(document).on('click', '.btn_plus_item', function(evt){
	var target = evt.currentTarget.dataset.id;
	var targetRowInput = "#quantity_id_" + target;
	var currentValue = $(targetRowInput).val();

	let productid = $(this).data('id');
	let quantity = parseFloat($(`#quantity_id_${productid}`).val());
	let shop = parseFloat($(this).data('shop'));
	let max_qty_isset = evt.currentTarget.dataset.max_qty_isset;
	let max_qty = evt.currentTarget.dataset.max_qty;
	let data = {
		item: {
			productid: productid,
			quantity: quantity,
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
				$(targetRowInput).val(parseInt(currentValue) + 1);
				loadCart();
				getCartItems();
	    }else{
				showToast({
					type: "warning",
					css: "toast-top-full-width mt-5",
					msg: data.message,
				});
	    }
	  },
	  error: function(){
	    hideCover();
	  }
	});
});

$(document).on('change', '.cart_input_qty', function(evt){
	var target = evt.currentTarget.dataset.id;
	var targetRowInput = "#quantity_id_" + target;
	var currentValue = $(targetRowInput).val();

	let productid = $(this).data('id');
	let quantity = parseFloat($(this).val());
	let shop = parseFloat($(this).data('shop'));
	let max_qty_isset = evt.currentTarget.dataset.max_qty_isset;
	let max_qty = evt.currentTarget.dataset.max_qty;
	quantity = (quantity <= 0) ?	1 : quantity;
	let data = {
		item: {
			productid: productid,
			quantity: quantity,
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
				loadCart();
				getCartItems();
				if (data.cart_count <= 0) location.replace(base_url);
	    }else{
				loadCart();
				getCartItems();
				showToast({
					type: "warning",
					css: "toast-top-full-width mt-5",
					msg: data.message,
				});
	    }
	  },
	  error: function(){
	    hideCover();
	  }
	});
});

$(document).on('click', '.btn_minus_item', function(evt){
	var target = evt.currentTarget.dataset.id;
	var targetRowInput = "#quantity_id_" + target;
	var currentValue = $(targetRowInput).val();


	let productid = $(this).data('id');
	let quantity = parseFloat($(`#quantity_id_${productid}`).val());
	let shop = parseFloat($(this).data('shop'));
	let max_qty_isset = evt.currentTarget.dataset.max_qty_isset;
	let max_qty = evt.currentTarget.dataset.max_qty;
	let data = {
		item: {
			productid: productid,
			quantity: quantity,
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
				if (parseInt(currentValue) > 1) {
					$(targetRowInput).val(parseInt(currentValue) - 1);
				}
				loadCart();
				getCartItems();
				if (data.cart_count <= 0) location.replace(base_url);


	    }else{
				showToast({
					type: "warning",
					css: "toast-top-full-width mt-5",
					msg: data.message,
				});
	    }
	  },
	  error: function(){
	    hideCover();
	  }
	});
});


$(".search__button_old").click((e) => {
	var searchKey = $(".search__input").val();

	window.location = base_url + "search?keyword=" + searchKey;
});

$(".search__input_old").keyup(function (event) {
	if (event.keyCode === 13) {
		$(".search__button_old").click();
	}
});

$(window).resize(function () {
	//console.log($("body").width());
	if($("body").width()<=767 && ($("#get_shipping_locs").val()=='' || $("#get_shipping_locs").val().includes('Select your'))){
		// $("header,footer,.cart,main,.shop-container").css('display','none');
		$("#popup_desktop").addClass('modal-full');
		$(".md2").addClass('modal-dialog-2');
		$(".mc2").addClass('mc-2');
		$("#popup_desktop").modal("show");
		// $(".pop_up").css('display','block');
	}else if($("body").width()>=767 && ($("#get_shipping_locs").val()=='' || $("#get_shipping_locs").val().includes('Select your'))){
		// $("header,footer,.cart,main,.shop-container").css('display','block');
		// $(".pop_up").css('display','none');
		$("#popup_desktop").removeClass('modal-full');
		$(".md2").removeClass('modal-dialog-2');
		$(".mc2").removeClass('mc-2');
		$("#popup_desktop").modal("show");
	}else{
		$("#popup_desktop").modal("hide");
	}
	;
	// if($("body").width()>767 && ($("#get_shipping_locs").val()=='' || $("#get_shipping_locs").val().includes('Select your'))){
	// 	$("#popup_desktop").modal("show");
	// }else{
	// 	$("#popup_desktop").modal("hide");
	// }
	if ($("body").width() < 600) {
		$(".order-status-caret").attr("hidden", false);
		$("#order_status_parent_collapse")
			.css({
				cursor: "pointer",
			})
			.attr("data-toggle", "collapse");
	} else {
		$(".order-status-caret").attr("hidden", true);
		$("#order_status_parent_collapse")
			.css({
				cursor: "auto",
			})
			.attr("data-toggle", false);
		$(".collapse").addClass("show");
	}
});

$(window).ready(function () {
	console.log("here");
	load();
	if ($("body").width() < 600) {
		$(".order-status-caret").attr("hidden", false);
		$("#order_status_parent_collapse")
			.css({
				cursor: "pointer",
			})
			.attr("data-toggle", "collapse");
	} else {
		$(".order-status-caret").attr("hidden", true);
		$("#order_status_parent_collapse")
			.css({
				cursor: "auto",
			})
			.attr("data-toggle", false);
	}
});

$(".mobile-filter-toggle").click(function(){
	$(".search-filter").toggleClass("search-filter--show");
	$("html").toggleClass("html--hidden");
})

$(".filter-close").click(function(){
	$(".search-filter").toggleClass("search-filter--show");
	$("html").toggleClass("html--hidden");
})

// SEARCH JS
$(document).ready(function(){
	$(".shipped_category_checkbox").on("change", function(){
		if($(this).is(":checked")) {
			let region = $(this).data("region");
			$(`.sub-checkboxes[data-region=${region}]`).addClass("sub-checkboxes--show");
		} else {
			let region = $(this).data("region");
			$(`.sub-checkboxes[data-region=${region}]`).removeClass("sub-checkboxes--show");
		}
	})
	$(document).on("change", ".shipped_prov", function(){
		console.log('taena nmn eh ');
		if($(this).is(":checked")) {
			let prov = $(this).data("prov");
			console.log('province',prov);
			$(`.sub-checkboxes-prov[data-prov=${prov}]`).addClass("sub-checkboxes--show");
		} else {
			let prov = $(this).data("prov");
			$(`.sub-checkboxes-prov[data-prov=${prov}]`).removeClass("sub-checkboxes--show");
		}
	})
})

// SHIPPED TO MAIN PAGE JS
$(".select2").select2({});

$(document).on('change', '.shipped-to-main-city', function(){
	let provCode = $(this).find(':selected').data('provCode');
	let regCode = $(this).find(':selected').data('regCode');
	$(this).data('provCode',provCode);
	$(this).data('regCode',regCode);
});

$(document).on('change', '.shipped-to-main-city_mobile', function(){
	let provCode = $(this).find(':selected').data('provCode');
	let regCode = $(this).find(':selected').data('regCode');
	$(this).data('provCode',provCode);
	$(this).data('regCode',regCode);
});

$(document).on('change', '.shipped-to-main-city_desktop', function(){
	let provCode = $(this).find(':selected').data('provCode');
	let regCode = $(this).find(':selected').data('regCode');
	$(this).data('provCode',provCode);
	$(this).data('regCode',regCode);
});

$('#shipped_to_main .dropdown-menu .select2').on({
	"click":function(e){
      e.stopPropagation();
    }
});

$(document).on('click focus dblclick mousedown mouseleave change keydown keypress keyup', '.select2-search__field', function(e){
     e.stopPropagation();
});

$('#shipped_to_main_mobile .dropdown-menu .select2').on({
	"click":function(e){
      e.stopPropagation();
    }
});

$('.closer-shipped-to').on('click', function () {
    $('.select-city-main').removeClass('show');
});

$('.closer-shipped-to_mobile').on('click', function () {
    $('.select-city-main').removeClass('show');
});
$(".select2").on('click', function () {
	//console.log($(".select2-results__option").length);
	//console.log(citiesdata.length);
	if(citiesdata.length==0){
		$.ajax({
			type:'get',
			url: base_url+'api/Shop/get_shipping_locations',
			dataType: 'json',
			beforeSend: () => {
						showCover("Loading Cities");
					},
			success:function(data){
				citiesdata=data;
				$.each(data, function(i, val) {
						$(".shipped-to-main-city_2").append('<option value='+val.citymunCode+' data-citymundesc='+val.citymunDesc+' data-provcode='+val.provCode+' data-regcode='+val.regDesc+'>'
						+ val.citymunDesc +', '+ val.provDesc +'</option>')   
				
				});
				hideCover();
			}
		});
	}
});
function load(){
		let location=$('#header-location').text();
		if($("body").width()<=767 && ($("#get_shipping_locs").val()=='' || $("#get_shipping_locs").val().includes('Select your'))){
			// $("header,footer,.cart,main,.shop-container").css('display','none');
			// $(".pop_up").css('display','block');
			$("#popup_desktop").addClass('modal-full');
			$(".md2").addClass('modal-dialog-2');
			$(".mc2").addClass('mc-2');
			$("#popup_desktop").modal("show");
		}else if($("body").width()>=767 && ($("#get_shipping_locs").val()=='' || $("#get_shipping_locs").val().includes('Select your'))){
			// $("header,footer,.cart,main,.shop-container").css('display','block');
			// $(".pop_up").css('display','none');
			$("#popup_desktop").removeClass('modal-full');
			$(".md2").removeClass('modal-dialog-2');
			$(".mc2").removeClass('mc-2');
			$("#popup_desktop").modal("show");
		}else{
			$("#popup_desktop").modal("hide");
		}
		
		// if($("body").width()>=767 && ($("#get_shipping_locs").val()=='' || $("#get_shipping_locs").val().includes('Select your'))){
			
		// 	$("#popup_desktop").modal("show");
		// }else{ 
		// 	//$("#popup_desktop").modal("show");
		// 	$("#popup_desktop").modal("hide");
		// }
		$.ajax({
			type:'get',
			url: base_url+'api/Shop/get_shipping_locations',
			dataType: 'json',
			beforeSend: () => {
						showCover("Loading Cities");
					},
			success:function(data){
				citiesdata=data;
				$.each(data, function(i, val) {
						$(".shipped-to-main-city_2").append('<option value='+val.citymunCode+' data-citymundesc='+val.citymunDesc+' data-provcode='+val.provCode+' data-regcode='+val.regDesc+'>'
						+ val.citymunDesc +', '+ val.provDesc +'</option>')   
				
				});
				hideCover();
			}
		});

}
$(document).on('click', '.shipped_to_main_wrapper_text', function(){
	let location=$('#header-location').text();
	$(".shipped-to-main-city").append('<option value="hide" data-citymundesc="hide" data-provcode="hide" data-regcode="hide" disabled selected>'
         			+location+'</option>')
	$.ajax({
        type:'get',
        url: base_url+'api/Shop/get_shipping_locations',
        dataType: 'json',
        beforeSend: () => {
					showCover("Loading Cities");
				},
        success:function(data){
        	hideCover();
        	$.each(data, function(i, val) {
         			$(".shipped-to-main-city").append('<option value='+val.citymunCode+' data-citymundesc='+val.citymunDesc+' data-provcode='+val.provCode+' data-regcode='+val.regDesc+'>'
         			+ val.citymunDesc +', '+ val.provDesc +'</option>')   
         	
     		});
        }
    });
});


$(document).on('click', '#filter-shipped-to-main_2 ', function(){
	let city = $('#shipped-to-main-city_popup').val();

	let provCode = $('#shipped-to-main-city_popup option:selected').attr('data-provcode');
	let regCode = $('#shipped-to-main-city_popup option:selected').attr('data-regcode');
	let location_name = $('#shipped-to-main-city_popup option:selected').text();
	let header_location= $('#header-location').text();
	if(location_name.toLowerCase().includes( 'Ship To' )){
		location_name=header_location;
		$("#error_location").html('Please select location first.').css('color','red');
	}else{
		$("#error_location").html('').css('color','red');
		let location=location_name.replace(/,/g,'comma');
		location=location.replace("(", "openp");
		location=location.replace(")", "closep");
		location=location.replace(/\W/g, " ");
		$('#header-location').text(location_name);
		let page = 'search';
		let cities = city;
		let province = provCode;
		let shipped = regCode;
	
	
		$.post(base_url+"api/Shop/set_session_shipping_location/" + page+"/"+cities+"/"+province+"/"+shipped+"/"+location, function (result) {
			showCover("Loading...");
			window.location= base_url;
		});
	}
	
});

$(document).on('click', '#filter-shipped-to-main_3 ', function(){
	let city = $('#shipped-to-main-city_desktop').val();

	let provCode = $('#shipped-to-main-city_desktop option:selected').attr('data-provcode');
	let regCode = $('#shipped-to-main-city_desktop option:selected').attr('data-regcode');
	let location_name = $('#shipped-to-main-city_desktop option:selected').text();
	let header_location= $('#header-location').text();
	if(location_name.toLowerCase().includes( 'ship to' )){
		location_name=header_location;
		$("#error_location_2").html('Please select location first.').css('color','red');
	}else{
		$("#error_location_2").html('').css('color','red');
		let location=location_name.replace(/,/g,'comma');
		location=location.replace("(", "openp");
		location=location.replace(")", "closep");
		location=location.replace(/\W/g, " ");
		$('#header-location').text(location_name);
		let page = 'search';
		let cities = city;
		let province = provCode;
		let shipped = regCode;
	
	
		$.post(base_url+"api/Shop/set_session_shipping_location/" + page+"/"+cities+"/"+province+"/"+shipped+"/"+location, function (result) {
			showCover("Loading...");
			window.location= base_url;
		});
	}
	
});
$(document).on('click', '#filter-shipped-to-main-clear', function(){
	showCover("Loading...");
	$.post(base_url+"api/Shop/clear_session_shipping_location/", function (result) {
		window.location= base_url;
	});
});
$(document).on('click', '#filter-shipped-to-main ', function(){
	let city = $('.shipped-to-main-city').val();

	let provCode = $('.shipped-to-main-city option:selected').attr('data-provcode');
	let regCode = $('.shipped-to-main-city option:selected').attr('data-regcode');
	let location_name = $('.shipped-to-main-city option:selected').text();
	let header_location= $('#header-location').text();
	if(location_name.includes( 'Current Location' )){
		location_name=header_location;
	}
	let location=location_name.replace(/,/g,'comma');
	location=location.replace("(", "openp");
	location=location.replace(")", "closep");
	location=location.replace(/\W/g, " ");
	$('#header-location').text(location_name);
	let page = 'search';
	let cities = city;
	let province = provCode;
	let shipped = regCode;


	$.post(base_url+"api/Shop/set_session_shipping_location/" + page+"/"+cities+"/"+province+"/"+shipped+"/"+location, function (result) {
		console.log(result);
		showCover("Loading...");
		window.location= base_url;
	});
});