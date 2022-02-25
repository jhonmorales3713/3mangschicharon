var base_url = $("body").data("base_url");
var s3_url = $("body").data("s3_url");
var cartItems = [];
var checkoutDetails = {};
var confirmed = false;
var refcode = "";
var validVouchers = []; // will contain all valid vouchers (if there's any) of the order
var provcodetoship="";
$(function () {
	$(document).ready(() => {
		loadCheckoutPage();

		if ($("#regCode").find(":selected").val() != "") {
			$("#regCode").change();
		}
	});

	// $('[data-toggle="tooltip"]').tooltip();
	$("body").tooltip({
		selector: '[data-toggle="tooltip"]',
	});

	// $("#confirm_checkout_email").keyup((e) => {
	// 	if ($("#confirm_checkout_email").val() == $("#checkout_email").val()) {
	// 		confirmed = true;
	// 	} else {
	// 		confirmed = false;
	// 	}
	// });

	// $("#checkout_email").keyup((e) => {
	// 	if ($("#confirm_checkout_email").val() == $("#checkout_email").val()) {
	// 		confirmed = true;
	// 	} else {
	// 		confirmed = false;
	// 	}
	// });

	$("#shipping").click(() => {
		var ckb = $("#signatureBox").is(":checked");
		var ckb2 = $("#signatureBox2").is(":checked");

		if (
			$("#checkout_name").val() == "" ||
			$("#checkout_conno").val() == "" ||
			$("#checkout_email").val() == "" ||
			$("#checkout_address").val() == "" ||
			// $("#checkout_areaid").find(":selected").val() == "" ||
			$("#regCode").find(":selected").val() == "" ||
			$("#citymunCode").find(":selected").val() == ""
		) {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Please fill up all required fields.",
			});
		} else if ($("#checkout_name").val().length < 5) {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Name must be at least 5 characters in length.",
			});
		} else if (!ValidateEmail($("#checkout_email").val())) {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Email should be a valid email address.",
			});
		} else if ($("#checkout_conno").val().slice(0, 2) != "09") {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Mobile number should be a valid mobile number (Format: 09XXXXXXXXX).",
			});
		} else if ($("#checkout_conno").val().length < 11) {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Mobile number should be 11 digits (Format: 09XXXXXXXXX).",
			});
		}
		else if (!($("#confirm_checkout_email").val().toUpperCase() == $("#checkout_email").val().toUpperCase())) {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Email and Confirm Email does not match.",
			});
		}
		else if (!($("#confirm_checkout_conno").val() == $("#checkout_conno").val())) {
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Mobile Number and Confirm Mobile Number does not match.",
			});
		}
		else {
			proceedToShipping(true);
		} //end of if-else
	});

	// $("#checkOut").click(() => {
	//  var ckb = $("#signatureBox").is(":checked");

	//  if (!ckb) {
	//    showToast({
	//      type: "warning",
	//      css: "toast-top-full-width mt-5",
	//      msg:
	//        "Please confirm that all details are entered correctly and that you have read and agreed with the terms and conditions.",
	//    });
	//  } else {
	//    $("#myModal").modal();
	//  }
	// });

	// $("#confirmation_modal_proceed").click(function () {
	$("#checkOut").click(function () {
		let allow_cod = $(this).data("allow_cod");
		if($("#confirmation").prop('checked')==true){
		// ALLOW COD
		if (allow_cod == 1) {
			$("#cod_modal").modal();
			// ONLINE PAYMENT VIA PAYPANDA
			$("#payment_paypanda").click(function () {
				fbq("track", "InitiateCheckout");
				$("#myModal").modal("hide");
				let latitude = $('#loc_latitude').val();
				let longitude = $('#loc_longitude').val();
				let register_upon_checkout = document.getElementById('register_upon_checkout');

				$("#checkOut").empty();
				$(
					"#checkOut"
				).append(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <span class="sr-only">Loading...</span>`);
				$("#checkOut").attr("disabled", true);
				checkoutDetails = {
					name: $("#checkout_name").val(),
					conno: $("#checkout_conno").val(),
					email: $("#checkout_email").val(),
					address: $("#checkout_address").val(),
					instructions: $("#instructions").val(),
					areaid: 0,
					area: $("#checkout_areaid").find(":selected").html(),
					regCode: $("#regCode").find(":selected").val(),
					regDesc: $("#regCode").find(":selected").html(),
					citymunCode: $("#citymunCode").find(":selected").val(),
					// citymunDesc: $("#citymunCode").find(":selected").html(),
					citymunDesc: $("#citymunCode").find(":selected").data('citymundesc'),
					payment_method: "PayPanda",
					shipping_fee: $("#shipping_fee").val(),
					// 'date_purchase': $("#checkout_date_purchase").val(),
					date_est_delivery: $("#checkout_date_est_delivery").val(),
				};

				if(register_upon_checkout !== null){
					checkoutDetails.register_upon_checkout = $('#register_upon_checkout').is(':checked') ? 1 : 0;
				}

				referralCode = $("#checkout_code").val();

				$.ajax({
					url: base_url + "api/Orders/checkoutOrder",
					method: "POST",
					data: {
						checkout_items: cartItems,
						checkout_details: checkoutDetails,
						referral_code: referralCode,
						vouchers: JSON.stringify(validVouchers),
						latitude: latitude,
						longitude: longitude
					},
					beforeSend: () => {
						showCover("Preparing for checkout...");
					},
					complete: () => {
						$("#checkOut").removeAttr("disabled");
						$("#checkOut").empty().html(`Checkout`);
					},
					success: (data) => {
						var response = JSON.parse(data);
						// console.log(response);
						if (response.status == 200) {
							$("#merchant_id").val(response.data.merchant_id);
							$("#reference_number").val(response.data.reference_number);
							$("#email_address").val($("#checkout_email").val());
							$("#payer_name").val($("#checkout_name").val());
							$("#mobile_number").val(
								$("#checkout_conno").val().replace(/\D/g, "").substring(0, 11)
							);
							$("#amount_to_pay").val(response.data.amount_to_pay);
							$("#signature").val(response.data.signature);
							// free shipping and zero total payment
							if (response.data.amount_to_pay == 0.0) {
								$("#z_merchant_id").val(response.data.merchant_id);
								$("#z_reference_number").val(response.data.reference_number);
								$("#z_email_address").val($("#checkout_email").val());
								$("#z_payer_name").val($("#checkout_name").val());
								$("#z_mobile_number").val($("#checkout_conno").val());
								$("#z_amount_to_pay").val(response.data.amount_to_pay);
								$("#z_signature").val(response.data.signature);
								$('#z_latitude').val(latitude);
								$('#z_longitude').val(longitude);
								$("#zero_payment_trans").submit();
								// normal process
							} else {
								$('#latitude').val(latitude);
								$('#longitude').val(longitude);
								$("#paypanda_trans").submit();
							}
						} else if (response.status == 201) {
							if (
								typeof response.message != undefined &&
								response.message.length
							) {
								showToast({
									type: "warning",
									css: "toast-top-full-width mt-5",
									msg: response.message,
								});
							}
							loadCheckoutPage(response.data);
						} else if (response.status == 204) {
							showToast({
								type: "error",
								css: "toast-top-full-width mt-5",
								msg: response.message,
							});
							setTimeout(() => {
								window.location.reload(true);
							}, 2000);
						} else {
							window.location.reload(true);
						}
					},
					error: (data) => {
						console.log(data);
						$("#checkOut").removeAttr("disabled");
					},
				}); //end of ajax
			});
			// CASH ON DELIVERY
			$("#payment_cod").click(function () {
				fbq("track", "InitiateCheckout");
				$("#myModal").modal("hide");
				let latitude = $('#loc_latitude').val();
				let longitude = $('#loc_longitude').val();
				let register_upon_checkout = document.getElementById('register_upon_checkout');

				$("#checkOut").empty();
				$(
					"#checkOut"
				).append(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <span class="sr-only">Loading...</span>`);
				$("#checkOut").attr("disabled", true);
				checkoutDetails = {
					name: $("#checkout_name").val(),
					conno: $("#checkout_conno").val(),
					email: $("#checkout_email").val(),
					address: $("#checkout_address").val(),
					instructions: $("#instructions").val(),
					areaid: 0,
					area: $("#checkout_areaid").find(":selected").html(),
					regCode: $("#regCode").find(":selected").val(),
					regDesc: $("#regCode").find(":selected").html(),
					citymunCode: $("#citymunCode").find(":selected").val(),
					// citymunDesc: $("#citymunCode").find(":selected").html(),
					citymunDesc: $("#citymunCode").find(":selected").data('citymundesc'),
					payment_method: "COD",
					shipping_fee: $("#shipping_fee").val(),
					// 'date_purchase': $("#checkout_date_purchase").val(),
					date_est_delivery: $("#checkout_date_est_delivery").val(),
				};

				if(register_upon_checkout !== null){
					checkoutDetails.register_upon_checkout = $('#register_upon_checkout').is(':checked') ? 1 : 0;
				}

				referralCode = $("#checkout_code").val();

				$.ajax({
					url: base_url + "api/Orders/checkoutOrder",
					method: "POST",
					data: {
						checkout_items: cartItems,
						checkout_details: checkoutDetails,
						referral_code: referralCode,
						vouchers: JSON.stringify(validVouchers),
						latitude: latitude,
						longitude: longitude
					},
					beforeSend: () => {
						showCover("Preparing for checkout...");
					},
					complete: () => {
						$("#checkOut").removeAttr("disabled");
						$("#checkOut").empty().html(`Checkout`);
					},
					success: (data) => {
						var response = JSON.parse(data);
						// console.log(response);
						if (response.status == 200) {
							$("#merchant_id").val(response.data.merchant_id);
							$("#reference_number").val(response.data.reference_number);
							$("#email_address").val($("#checkout_email").val());
							$("#payer_name").val($("#checkout_name").val());
							$("#mobile_number").val(
								$("#checkout_conno").val().replace(/\D/g, "").substring(0, 11)
							);
							$("#amount_to_pay").val(response.data.amount_to_pay);
							$("#signature").val(response.data.signature);
							window.location.href =
								base_url +
								"api/Orders/paypanda_return_url?refno=" +
								response.data.reference_number;
							// free shipping and zero total payment
							// if(response.data.amount_to_pay == 0.00){
							//   $("#z_merchant_id").val(response.data.merchant_id);
							//   $("#z_reference_number").val(response.data.reference_number);
							//   $("#z_email_address").val($("#checkout_email").val());
							//   $("#z_payer_name").val($("#checkout_name").val());
							//   $("#z_mobile_number").val(
							//     $("#checkout_conno").val()
							//   );
							//   $("#z_amount_to_pay").val(response.data.amount_to_pay);
							//   $("#z_signature").val(response.data.signature);
							//   $('#zero_payment_trans').submit();
							// // normal process
							// }
						} else if (response.status == 201) {
							if (
								typeof response.message != undefined &&
								response.message.length
							) {
								showToast({
									type: "warning",
									css: "toast-top-full-width mt-5",
									msg: response.message,
								});
							}
							loadCheckoutPage(response.data);
						} else if (response.status == 204) {
							showToast({
								type: "error",
								css: "toast-top-full-width mt-5",
								msg: response.message,
							});
							setTimeout(() => {
								window.location.reload(true);
							}, 2000);
						} else {
							window.location.reload(true);
						}
					},
					error: (data) => {
						console.log(data);
						$("#checkOut").removeAttr("disabled");
					},
				}); //end of ajax
			});

		} else {
			fbq("track", "InitiateCheckout");
			$("#myModal").modal("hide");
			let latitude = $('#loc_latitude').val();
			let longitude = $('#loc_longitude').val();
			let register_upon_checkout = document.getElementById('register_upon_checkout');

			$("#checkOut").empty();
			$("#checkOut")
				.append(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                      <span class="sr-only">Loading...</span>`);
			$("#checkOut").attr("disabled", true);
			checkoutDetails = {
				name: $("#checkout_name").val(),
				conno: $("#checkout_conno").val(),
				email: $("#checkout_email").val(),
				address: $("#checkout_address").val(),
				instructions: $("#instructions").val(),
				areaid: 0,
				area: $("#checkout_areaid").find(":selected").html(),
				regCode: $("#regCode").find(":selected").val(),
				regDesc: $("#regCode").find(":selected").html(),
				citymunCode: $("#citymunCode").find(":selected").val(),
				// citymunDesc: $("#citymunCode").find(":selected").html(),
				citymunDesc: $("#citymunCode").find(":selected").data('citymundesc'),
				brgyCode: $("#brgyCode").find(":selected").val(),
				brgyDesc: $("#brgyCode").find(":selected").html(),
				payment_method: "PayPanda",
				shipping_fee: $("#shipping_fee").val(),
				// 'date_purchase': $("#checkout_date_purchase").val(),
				date_est_delivery: $("#checkout_date_est_delivery").val(),
			};

			if(register_upon_checkout !== null){
				checkoutDetails.register_upon_checkout = $('#register_upon_checkout').is(':checked') ? 1 : 0;
			}

			referralCode = $("#checkout_code").val();

			$.ajax({
				url: base_url + "api/Orders/checkoutOrder",
				method: "POST",
				data: {
					checkout_items: cartItems,
					checkout_details: checkoutDetails,
					referral_code: referralCode,
					vouchers: JSON.stringify(validVouchers),
					latitude: latitude,
					longitude: longitude
				},
				beforeSend: () => {
					showCover("Preparing for checkout...");
				},
				complete: () => {
					$("#checkOut").removeAttr("disabled");
					$("#checkOut").empty().html(`Checkout`);
				},
				success: (data) => {
					var response = JSON.parse(data);
					// console.log(response);
					if (response.status == 200) {
						$("#merchant_id").val(response.data.merchant_id);
						$("#reference_number").val(response.data.reference_number);
						$("#email_address").val($("#checkout_email").val());
						$("#payer_name").val($("#checkout_name").val());
						$("#mobile_number").val(
							$("#checkout_conno").val().replace(/\D/g, "").substring(0, 11)
						);
						$("#amount_to_pay").val(response.data.amount_to_pay);
						$("#signature").val(response.data.signature);
						// free shipping and zero total payment
						if (response.data.amount_to_pay == 0.0) {
							$("#z_merchant_id").val(response.data.merchant_id);
							$("#z_reference_number").val(response.data.reference_number);
							$("#z_email_address").val($("#checkout_email").val());
							$("#z_payer_name").val($("#checkout_name").val());
							$("#z_mobile_number").val($("#checkout_conno").val());
							$("#z_amount_to_pay").val(response.data.amount_to_pay);
							$("#z_signature").val(response.data.signature);
							$('#z_latitude').val(latitude);
							$('#z_longitude').val(longitude);
							$("#zero_payment_trans").submit();
							// normal process
						} else {
							$('#latitude').val(latitude);
							$('#longitude').val(longitude);
							$("#paypanda_trans").submit();
						}
					} else if (response.status == 201) {
						if (
							typeof response.message != undefined &&
							response.message.length
						) {
							showToast({
								type: "warning",
								css: "toast-top-full-width mt-5",
								msg: response.message,
							});
						}
						loadCheckoutPage(response.data);
					} else if (response.status == 204) {
						showToast({
							type: "error",
							css: "toast-top-full-width mt-5",
							msg: response.message,
						});
						setTimeout(() => {
							window.location.reload(true);
						}, 2000);
					} else {
						window.location.reload(true);
					}
				},
				error: (data) => {
					console.log(data);
					$("#checkOut").removeAttr("disabled");
				},
			}); //end of ajax
		}
	 }else{
	  	showToast({
					type: "warning",
					css: "toast-top-full-width mt-5",
					msg: "You must agree to our terms and agreement before checking out.",
				});
	  }
	});

	// submit zero payment transaction form
	$(document).on("submit", "#zero_payment_trans", function (e) {
		e.preventDefault();
		let obj = new Date();
		let today = obj.getDate() + "" + obj.getMonth() + "" + obj.getYear();
		let paypanda_refno =
			"FREE" +
			today +
			"" +
			(Math.floor(Math.random() * (999999 - 100000 + 1)) + 100000);
		$.ajax({
			url: base_url + "api/Orders/paypanda_postback",
			type: "post",
			dataType: "json",
			data: {
				paypanda_refno: paypanda_refno,
				reference_number: $("#z_reference_number").val(),
				paid_amount: $("#z_amount_to_pay").val(),
				payment_status: "S",
				signature: "FREE",
				service_code: "",
				payment_portal_fee: 0.0,
				trigger: "manual_payment",
				payment_method: "Free Payment",
				payment_notes: "",
				action_by: null,
				latitude: $('#z_latitude').val(),
				longitude: $('#z_longitude').val()
			},
			beforeSend: function () {
				showCover("Processing ...");
			},
			success: function (data) {
				hideCover();
				if (data.status == "success") {
					window.location.href =
						base_url +
						"api/Orders/paypanda_return_url?refno=" +
						$("#z_reference_number").val();
				} else {
					showToast({
						type: "warning",
						css: "toast-top-full-width mt-5",
						msg: data.message,
					});
				}
			},
			error: function () {
				hideCover();
				showToast({
					type: "error",
					css: "toast-top-full-width mt-5",
					msg: "Something went wrong. Please try again.",
				});
			},
		});
	});

	$("#regCode").change(function () {
		showCover("Loading data...");
		$("#citymunCode option").remove();
		// provCode = $(this).val();
		regCode = $(this).val();

		$.ajax({
			type: "post",
			url: base_url + "sys/shipping_delivery/get_citymun",
			data: {
				regCode: $(this).val(),
			},
			success: function (data) {
				hideCover();
				var json_data = JSON.parse(data);

				if (json_data.success) {
					$("#citymunCode").append(
						$("<option></option>")
							.attr("value", "")
							.attr("readonly", "")
							.text("SELECT CITY")
					);
					if (
						zoneArray.filter(
							(e) =>
								parseFloat(e.citymunCode) != parseFloat(0) && e.status === 1
						).length > 0
					) {
					} else {
						// $('#citymunCode')
						//      .append($("<option></option>")
						//         .attr("value", "0")
						//         .text("ENTIRE PROVINCE"));
					}

					$.each(json_data.data, function (key, value) {
						if (
							zoneArray.filter(
								(e) =>
									parseFloat(e.regCode) === parseFloat(value.regCode) &&
									e.status === 1
							).length > 0 &&
							zoneArray.filter(
								(e) =>
									parseFloat(e.citymunCode) === parseFloat(value.citymunCode) &&
									e.status === 1
							).length > 0
						) {
						} else if (
							zoneArray.filter(
								(e) =>
									parseFloat(e.regCode) === parseFloat(value.regCode) &&
									e.status === 1
							).length > 0 &&
							zoneArray.filter(
								(e) =>
									parseFloat(e.citymunCode) === parseFloat(0) && e.status === 1
							).length > 0
						) {
						} else {
							//if NCR
							if (regCode == 13 && value.provCode != "1339") {
								cityDesc = value.citymunDesc;
							} else {
								if (value.provCode == "1339")
									cityDesc = value.citymunDesc + ", CITY OF MANILA";
								else cityDesc = value.provDesc + " (" + value.citymunDesc + ")";
							}
							if ($("#checkout_citymunCode").val() == value.citymunCode) {
								$("#citymunCode").append(
									$("<option></option>")
										.attr("value", value.citymunCode)
										.attr("selected", "")
										.text(cityDesc)
								);
							} else {
								$("#citymunCode").append(
									$("<option></option>")
										.attr("value", value.citymunCode)
										.text(cityDesc)
								);
							}
						}
					});
					$("#citymunCode").prop("disabled", false);
				} else {
					sys_toast_warning("No data found");
				}
			},
			error: function (error) {
				hideCover();
				sys_toast_error("Error");
			},
		});
	});
	let zoneArray = [];
});

$("#remove-shop-card-backbtn").click(() => {
	$("#contact_details_form").attr("hidden", false);
	$("#item-total-card").attr("hidden", false);
	$("#checkout-total").attr("hidden", false);
	$("#shipping").attr("hidden", false);
	$("#shipping").attr("disabled", true);
	if ($("#checkout_code").val() != "")
		$("#referral-card").attr("hidden", false);
	$("#checkout-card").attr("hidden", false);
	$("#checkout-card-s").attr("hidden", true);
	$("#confirmationBox").attr("hidden", true);
	$("#checkOut").attr("hidden", true);
	$("#shipping_details").attr("hidden", true);
	$("#confirmation_details").attr("hidden", true);
	$("#remove_shop_details").attr("hidden", true);
	$("#remove-shop-card-backbtn").attr("hidden", true);
	$("#remove-shop-card-continuebtn").attr("hidden", true);
	loadCheckoutPage();
	window.scrollTo(0, 0);
});

$("#remove-shop-card-continuebtn").click(() => {
	location.replace(base_url);
});

function proceedToShipping(scrollToTop = false) {
	$("#shipping").attr("disabled", true);
	
	$.ajax({
		url: base_url + "api/getShippingRate",
		method: "POST",
		data: {
			citymunCode: $("#citymunCode").find(":selected").val(),
			brgyCode: $("#brgyCode").find(":selected").val(),
			brgyDesc: $("#brgyCode").find(":selected").html(),
			name: $("#checkout_name").val(),
			email: $("#checkout_email").val(),
			conno: $("#checkout_conno").val(),
			address: $("#checkout_address").val(),
			latitude: $('#loc_latitude').val(),
			longitude: $('#loc_longitude').val(),
			postal: $("#checkout_postal").val(),
			landmark: $("#instructions").val(),
			vouchers: JSON.stringify(validVouchers),
		},
		beforeSend: () => {
			showCover("Calculating shipping fee...");
			$("#checkoutPage").empty();
			$("#checkoutPageUnavailable").empty();
			$("#checkoutPage").append(renderLoading());
		},
		success: (data) => {
			var response = JSON.parse(data);
			$("#checkoutPage").empty();

			$("#referral-card").attr("hidden", true);
			$("#contact_details_form").attr("hidden", true);

			$("#shipping").removeAttr("disabled");
			$("#shipping").attr("hidden", true);
			$("#remove-shop-card-backbtn").attr("hidden", false);

			$("#checkout_email-s").val($("#checkout_email").val());
			$("#checkout_name-s").val($("#checkout_name").val());
			$("#checkout_code-s").val($("#checkout_code").val());
			var landmark = $("#instructions").val()
				? " (" + $("#instructions").val() + ")"
				: "";
			$("#checkout_address-s").val(
				$("#checkout_address").val() +
					", " +
					// $("#brgyCode").find(":selected").html() +
					// ", " +
					$("#citymunCode").find(":selected").html() +
					landmark
			);

			$("#shipping_details").attr("hidden", false);
			$("#confirmation_details").attr("hidden", false);

			if (response.newCart.length <= 0) {
				$("#ordersLabel").attr("hidden", true);
				$("#item-total-card").attr("hidden", true);
				$("#checkout-total").attr("hidden", true);
				// $("#remove-shop-card-continuebtn").attr("hidden", false);
			} else {
				var shippingTotal = 0;
				var subtotalTotal = 0;
				console.log(response.newCart);
				$.each(response.newCart, (e, shop) => {
					$("#checkoutPage").append(addCartItem(shop, e, "shipping"));
					shippingTotal += (shop.items_availability === 1) ? parseFloat(shop.shippingfee) : 0.00;
					subtotalTotal += parseFloat(
						$("#subtotal-value-" + shop.shopid).val()
					);
				});

				$("#sub_total_amount_checkout").html(
					"₱ " + numberFormatter(subtotalTotal, false)
				);
				$("#shipping_fee").val(shippingTotal);
				var totalamount = subtotalTotal + shippingTotal;
				if (shippingTotal > 0) {
					$("#shipping_amount_checkout")
						.html("₱ " + numberFormatter(shippingTotal, false))
						.attr("data-amount", numberFormatter(shippingTotal, false));
				} else {
					$("#shipping_amount_checkout").html("Free Shipping");
				}
				$("#total_amount_checkout").html(
					"<span class='ml-2'>₱ " +
						numberFormatter(totalamount, false) +
						"</span>"
				);

				$("#confirmationBox").attr("hidden", false);
				if ($("#checkout_code").val() != "") {
					$("#referral-card-s").attr("hidden", false);
				}
				$("#checkOut").attr("hidden", false);

				// unhide shipping card per shop
				$("div[id^='shipping-card-']").each(function () {
					var shippingCard = $(this).attr("id");
					$("#" + shippingCard).attr("hidden", false);
				});

				// unhide voucher section per shop
				$("div[id^='discount-section-']").each(function () {
					var discountSection = $(this).attr("id");
					$("#" + discountSection).attr("hidden", false);
				});
			}

			//check if there are shops that cannot ship to area selected
			if (response.removedCart.length > 0) {
				if (response.newCart.length <= 0)
					$("#unavailableOrdersNote2").attr("hidden", false);
				else $("#unavailableOrdersNote").attr("hidden", false);

				$("#unavailableOrdersLabel").attr("hidden", false);
				$("#checkoutPageUnavailable").attr("hidden", false);

				$.each(response.removedCart, (e, shop) => {
					$("#checkoutPageUnavailable").append(
						addUnserviceableItem(shop, e, "unserviceable")
					);
				});
			}

			// check if has reseller or referral information
			var reseller = $("#checkoutPage").attr("data-reseller");
			var referral = $("#checkoutPage").attr("data-referral");
			// if no reseller and referral info, reprocess vouchers
			if (reseller == "" && referral == "") {
				// hide voucher section input per shop in shipping page

				if (response.newValidVouchers && response.newValidVouchers.length > 0) {
					$("#voucher-note").attr("hidden", false);
					validVouchers = response.newValidVouchers;
					$.each(response.newValidVouchers, function (key, val) {
						$.each(val.vouchers, function (key2, val2) {
							addShopVoucher(val2);
						});
					});
				}
			}

			$("div[id^='discount-section-']").each(function () {
				var discountSection = $(this).attr("id");
				$("#" + discountSection).attr("hidden", true);
			});

			if(response.available == 0){
				$("#checkOut").attr("hidden", true);
			}

			// update_variant_price(provcodetoship,"shipping");
			if (scrollToTop) window.scrollTo(0, 0);
			hideCover();
		},
		error: (data) => {
			console.log(data);
			$("#shipping").removeAttr("disabled");
		},
	}); //end of ajax
}
function loadCheckoutPage(additional_data = []) {
	$(".shipped-to-main-city").append('<option value='+'test'+' data-citymundesc='+'test'+' data-provcode='+'test'+' data-regcode='+'test'+'>'
	+ 'test' +', '+ 'test' +'</option>')   
	$.ajax({
		url: base_url + "api/getCartItems",
		beforeSend: () => {
			showCover("Loading cart...");
			$("#checkoutPage").empty();
			$("#checkoutPageUnavailable").empty();
			$("#unavailableOrdersLabel").attr("hidden", true);
			$("#unavailableOrdersNote").attr("hidden", true);
			$("#unavailableOrdersNote2").attr("hidden", true);
			$("#checkoutPageUnavailable").attr("hidden", true);
			$("#ordersLabel").attr("hidden", false);
			$("#checkoutPage").append(renderLoading());
		},
		success: (result) => {
			var data = JSON.parse(result);
			// console.log(data);
			var shippingfee = 0;
			var totalamount = 0;
			totalamount = parseFloat(data.total_amount) + shippingfee;
			if (data.cart.length <= 0) {
				location.replace(base_url);
			}
			$("#checkoutPage").empty();
			// $("#checkoutPage").append(renderHeaderCart());
			$("#shipping").removeAttr("disabled");
			cartItems = data.cart;
			// console.log(data);
			$("#sub_total_amount_checkout").html(
				"₱ " + numberFormatter(data.total_amount, false)
			);
			$("#shipping_fee").val(shippingfee);
			$("#total_amount").val(totalamount);
			$("#shipping_amount_checkout")
				.html(
					"<small style='font-size:12px; font-weight: bold; color:var(--gray);'>Calculated at next step</small>"
				)
				.attr("data-amount", "0");
			$("#total_amount_checkout").html(
				"<span class='ml-2'>₱ " +
					numberFormatter(totalamount, false) +
					"</span>"
			);
			$.each(data.cart, (e, item) => {
				$("#checkoutPage").append(addCartItem(item, e, "checkout"));
				if (additional_data != []) {
					$.each(additional_data, (es, items) => {
						if (item.id == items.id) {
							$("#shipping").attr("disabled", true);
							$("#cartRemove" + items.id).append(
								`<i class="fa fa-trash cart-table__delete-icon removeCart" data-id="` +
									e +
									`" ></i>`
							);
							$(".checkoutDangerColumns" + items.id).css(
								"color",
								"var(--danger)"
							);
						}
					});
				}
			});

			var reseller = $("#checkoutPage").attr("data-reseller");
			var referral = $("#checkoutPage").attr("data-referral");
			console.log(reseller, referral);

			// console.log(reseller);
			if (reseller == "" && referral == "") {
				console.log(reseller, referral);
				if (data.validVouchers && data.validVouchers.length > 0) {
					$.each(data.validVouchers, function (key, val) {
						$.each(val.vouchers, function (key2, val2) {
							addShopVoucher(val2);
						});
					});
				}
			} else {
				// hide voucher section input if has reseller info
				$("div[id^='discount-section-']").each(function () {
					var discountSection = $(this).attr("id");
					$("#" + discountSection).attr("hidden", true);
				});
			}
			$("#voucher-note").attr("hidden", true);

			// var provCode=$("#citymunCode").children('option:selected').data("provcode");
  	// 		provcodetoship=provCode;
			// update_variant_price(provcodetoship);
			hideCover();
		},
	});
}

function ValidateEmail(mail) {
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
		return true;
	}
	return false;
}

$("#confirm_checkout_email").bind("cut copy paste", function (e) {
	e.preventDefault();
});

function renderHeaderCart() {
	return `
  <div class="portal-table__titles col-12 mb-4 ">
    <div class="col-12 col-lg-2"></div>
    <div class="col-12 col-lg-2">Product</div>
    <div class="col-12 col-lg-2">Unit</div>
    <!-- <div class="col-1">Category</div> -->
    <div class="col-12 col-lg-2 text-right">Price</div>
    <div class="col-4 col-lg-2 text-right">Quantity</div>
    <div class="col-12 col-lg-2 text-right">Subtotal</div>
  </div>
  `;
}

function addCartItem(shop, e, display = "") {
	// $('#checkOut').show();
	let allow_voucher = $('#pageActive').data('allow_voucher');
	let voucher_section = "";
	// console.log(shop);
	if(allow_voucher == 1){
		voucher_section =
		`
			<div id="discount-section-${
				shop.shopid
			}" class="product-card-item add-voucher">
				<div class="col col-md-6 offset-md-5">
					<div class="input-group py-2">
						<input name="shop_${shop.shopid}_vcode" id="shop_${
	shop.shopid
	}_vcode" style="font-size: 12px;" type="text" class="rounded-left form-control input-voucher" placeholder="Add valid voucher">
						<div class="input-group-append">
							<button style="font-size: 12px;" class="btn btn-outline-secondary validate-voucher-btn" type="button" data-shopid="${
								shop.shopid
							}">Apply</button>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="row apply-voucher-code mt-3" hidden>
						<div class="col-12 col-md-6 offset-md-5">
						</div>
					</div>
				</div>
			</div>
		`;
	}
	var available = 0;
	var unavailable = 0;
	var productCard = "";
	productCard += `<div class="product-card" id="product-card-list">
            <div class="product-card-header">
                <div class="row">
                    <div class="col">
                        <div class="row no-gutters">
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <div><img class="img-thumbnail" style="width: 50px;" src="${s3_url}assets/img/shops-60/webp//${shop.logo
		.split(".")
		.slice(0, -1)
		.join(".")}.webp"
                                onerror="this.onerror=null; this.src='${s3_url}assets/img/shops-60/${shop.logo}'"></div>
                            </div>
                            <div class="col d-flex align-items-center">
                                <div class="product-card-title">${
																	shop.shopname
																}</div>
                            </div>
                          <div class="col-auto d-flex align-items-center justify-content-end" id="remove-shop-card-${display}">
                              <div class="product-card-delete removeAll btn" data-id="${e}" data-shopid="${
		shop.shopid
	}" data-display="${display}">
                                  Remove All
                              </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>`;

	items = shop.items;
	var shopVoucherDiscount = 0;
	var subtotal = 0;
	$.each(items, (e, item) => {
		if (
			display != "shipping" ||
			(item.available == 1 && display == "shipping")
		) {
			subtotal += parseFloat(item.price) * parseFloat(item.quantity);
			productCard += `<div class="product-card-body">
	                <div class="product-card-item">
	                    <div class="row no-gutters">
	                        <div class="col">
	                            <div class="row no-gutters">
	                                <div class="col-2 col-md-1">
	                                    <div class="product-card-image" style="background-image: url(${s3_url}assets/img/${
				shop.shopcode
			}/products-40/${item.productid}/${remove_ext(item.primary_pics)}.jpg)"></div>
	                                </div>
	                                <div class="col product-card-content">
	                                    <div class="product-card-name p-name">
	                                        ${item.itemname}
	                                    </div>
	                                    <div class="product-card-quantity">
																					<div class="row">`;
																					if(display != 'shipping'){
																						productCard +=
																						`
																						<div class="col-12 mb-1 unit-price ${(item.variant_isset == 1) ? 'check-unit-price unit-item-'+item.productid : ''}">
																							<span>Unit Price: ₱ ${numberFormatter(item.price, false)}</span>
																						</div>
																						<div class="col-11 col-xs-6 col-sm-9 col-md-5 col-lg-5 col-xl-4 text-left">
																								<div class="product-quantity" style = "padding-left:0px;padding-right:0px;width:120px;">
																										<div class="input-group">
																												<div class="input-group-prepend">
																														<button class="btn btn-outline-secondary quantity__minus btn_sub_item" type="button" data-id="${item.productid}" data-quan = "${item.quantity}" data-shop = "${shop.shopid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}"><i class="fa fa-minus"></i></button>
																												</div>
																												<input type="number" class="form-control text-center quantity__input checkout_input_qty shop-input_qty unit-quantity-${item.productid}" value="${item.quantity}" id="quantity_id_${item.productid}" data-shop = "${shop.shopid}" data-id="${item.productid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}">
																												<div class="input-group-append">
																														<button class="btn btn-outline-secondary quantity__plus btn_add_item" type="button" data-id="${item.productid}" data-quan = "${item.quantity}" data-shop = "${shop.shopid}" data-max_qty = "${item.max_qty}" data-max_qty_isset = "${item.max_qty_isset}"><i class="fa fa-plus"></i></button>
																												</div>
																										</div>
																								</div>
																						</div>

																						`;
																					}else{
																						productCard +=
																						`
																						<div class="col-12 ">
																							<span class ="checkout-res res-unit-quantity-${item.productid}">Quantity: ${item.quantity}</span>
																							<span class="checkout-res res-unit-price-${item.productid}">Unit Price: ₱ ${numberFormatter(item.price, false)}</span>
																						</div>
																						`;
																					}
																					productCard += ` </div>

 	                                    </div>
 	                                </div>
 	                            </div>
 	                        </div>
 	                        <div class="col-3 col-md-2 d-none d-md-block">
 	                            <div class="product-card-price" style = "white-space:${(item.unit.length > 40) ? 'normal' : 'nowrap'}">
 	                                ${item.unit}
 	                            </div>
 	                        </div>
 	                        <div class="col-4 col-md-2 col-lg-2 col-xl-2">
 	                            <div class="product-card-price d-sm-block get_all_price ${(item.variant_isset == 1) ? 'check-price item-'+item.productid : ''}" data-productid='${item.productid}' data-shopid="${shop.shopid}">
 	                                <span >₱ ${numberFormatter((item.price * item.quantity), false)}</span>
 	                            </div>
 	                        </div>`;

			if (display != "shipping") {
				productCard += `<div class="col-1" id="remove-item-card-${display}">
	                            <div class="product-card-delete removeCart" data-id="${e}" data-shopid="${shop.shopid}" data-display="${display}">
	                                <i class="fa fa-trash"></i>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>`;
			} else {
				productCard += `<div class="col-1">
	                        </div>
	                    </div>
	                </div>
	            </div>`;
			}
		} else if(item.available == 2){
			productCard += `<div class="product-card-body unserviceable">
	                <div class="product-card-item">
	                    <div class="row no-gutters">
	                        <div class="col">
	                            <div class="row no-gutters">
	                                <div class="col-2 col-md-1">
	                                    <div class="product-card-image" style="filter: grayscale(100%); background-image: url(${s3_url}assets/img/${
				shop.shopcode
			}/products-40/${item.productid}/${remove_ext(item.primary_pics)}.jpg)"></div>
	                                </div>
	                                <div class="col product-card-content">
	                                    <div class="product-card-name" style="color:LightGray;">
	                                        ${item.itemname}
	                                    </div>
	                                    <div class="product-card-quantity" style="color:LightGray;">
	                                        Quantity: ${item.quantity} &nbsp;
																					Unit Price: ₱ ${numberFormatter(item.price,false)}
	                                    </div>
	                                    <div class="product-card-quantity" style="color:red;">
	                                        There are not enough inventory in stock for this item. <br />(Available stocks: ${item.available_stocks})
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="col-3 col-md-2 d-none d-md-block">
	                            <div class="product-card-price" style="color:LightGray;white-space:${(item.unit.length > 40) ? 'normal;' : 'nowrap;'}">
	                                ${item.unit}
	                            </div>
	                        </div>
	                        <div class="col-2">
	                            <div class="product-card-price ${(item.variant_isset == 1) ? 'check-price item-'+item.productid : ''}" data-productid='${item.productid}' data-shopid="${shop.shopid}"  style="color:LightGray;">
	                                <span style="color: LightGray;">₱ ${numberFormatter((item.price * item.quantity), false)}</span>
	                            </div>
	                        </div>

	                        <div class="col-1">
	                        </div>
	                    </div>
	                </div>
	            </div>`;
		}else {
			productCard += `<div class="product-card-body unserviceable">
	                <div class="product-card-item">
	                    <div class="row no-gutters">
	                        <div class="col">
	                            <div class="row no-gutters">
	                                <div class="col-2 col-md-1">
	                                    <div class="product-card-image" style="filter: grayscale(100%); background-image: url(${s3_url}assets/img/${
				shop.shopcode
			}/products-40/${item.productid}/${remove_ext(item.primary_pics)}.jpg)"></div>
	                                </div>
	                                <div class="col product-card-content">
	                                    <div class="product-card-name" style="color:LightGray;">
	                                        ${item.itemname}
	                                    </div>
	                                    <div class="product-card-quantity" style="color:LightGray;">
	                                        Quantity: ${item.quantity} &nbsp;
																					Unit Price: ₱ ${numberFormatter(item.price,false)}
	                                    </div>
	                                    <div class="product-card-quantity" style="color:red;">
	                                        This product is not available on your selected shipping location.
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="col-3 col-md-2 d-none d-md-block">
	                            <div class="product-card-price" style="color:LightGray;">
	                                ${item.unit}
	                            </div>
	                        </div>
	                        <div class="col-2">
	                            <div class="product-card-price ${(item.variant_isset == 1) ? 'check-price item-'+item.productid : ''}" data-productid='${item.productid}' data-shopid="${shop.shopid}"  style="color:LightGray;white-space:${(item.unit.length > 40) ? 'normal;' : 'nowrap;'}">
	                               <span style="color:LightGray;white-space:${(item.unit.length > 40) ? 'normal;' : 'nowrap;'}"> ₱ ${numberFormatter((item.price * item.quantity), false)} </span>
	                            </div>
	                        </div>

	                        <div class="col-1">
	                        </div>
	                    </div>
	                </div>
	            </div>`;
		}
	});
	productCard += `<div hidden id="shipping-card-${
		shop.shopid
	}" class="product-card-footer">
                <div class="product-card-footer-content container-fluid">
                    <div class="row">
                        <div class="col-12 col-md">
                            <div class="row d-flex justify-content-end">
                                <div class="col col-md-6">
                              <div class="pb-2 col-12 text-left product-card-title">
                                  Shipping
                              </div>
                                    <div class="product-card-footer-option option--active">

                              <div hidden>
                                  <input id="shippingfee-value-${
																		shop.shopid
																	}" type="text" value="${parseFloat(
		shop.shippingfee
	)}">
                              </div>
                                        <div id="shippingfee-card-${
																					shop.shopid
																				}" class="font-weight-bold"> ${
		parseFloat(shop.shippingfee) != 0
			? "₱ " + numberFormatter(shop.shippingfee, false)
			: "Free Shipping"
	}</div>
                                        <div class="">Estimated Shipping Date: </div>

                              <div hidden>
                                  <input id="shippingdts-value-${
																		shop.shopid
																	}" type="text" value="${shop.shopdts}">
                              </div>
                                        <div id="shippingdts-card-${
																					shop.shopid
																				}" class="">${
																					shop.shopdts == '0' && shop.shopdts_to == '0'
																					? "Within 24 Hours"
																					: shop.shopdts == shop.shopdts_to
																					? "Within "+shop.shopdts_to+" day(s)"
																					: "("+shop.shopdts+"-"+shop.shopdts_to+" days)"
																				}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- voucher section -->
            ${voucher_section}
            <div class="product-card-body py-3 product-card-total">
                <div class="product-card-item">
                    <div class="row no-gutters">
                      <div hidden>
                          <input id="subtotal-value-${
														shop.shopid
													}" type="text" value="${parseFloat(subtotal)}">
                      </div>
                        <div class="col product-card-name text-md-right">
                            Sub-total:
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="product-card-price sub-total-card subtotal-all" id="subtotal-card-${
															shop.shopid
														}" data-shopid="${shop.shopid}">
                               <span style="font-weight: 700; margin-bottom: 2px; font-size: 15px;">₱ ${numberFormatter(
																	parseFloat(subtotal),
																	false
																)}</span>
                            </div>
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                    <div id="applied-vouchers-section-${shop.shopid}">

                    </div>
                    <div hidden class="row no-gutters" id="disc-holder-${
											shop.shopid
										}">
                      <div hidden>
                          <input id="disc-subtotal-value-${
														shop.shopid
													}" type="text" value="${parseFloat(subtotal)}">
                      </div>
                        <div class="col product-card-name text-md-right">
                            New Sub-total:
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="product-card-price disc-sub-total-card" id="disc-subtotal-card-${
															shop.shopid
														}" data-shopid="${shop.shopid}">
                                ₱ ${numberFormatter(
																	parseFloat(subtotal),
																	false
																)}
                            </div>
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                </div>
            </div>
            <!-- end voucher section -->
        </div>`;

	$('[data-toggle="tooltip"]').tooltip();
	return productCard;
}

function addUnserviceableItem(shop, e, display = "") {
	var productCard = "";
	productCard += `<div class="product-card unserviceable" id="product-card-list">
            <div class="product-card-header">
                <div class="row">
                    <div class="col">
                        <div class="row no-gutters">
                            <div class="col-1 d-flex align-items-center justify-content-end">
                                <div><img class="img-thumbnail" style="width: 50px; filter: grayscale(100%);" src="${s3_url}assets/img/shops-60/webp//${shop.logo
		.split(".")
		.slice(0, -1)
		.join(".")}.webp"
                                onerror="this.onerror=null; this.src='${s3_url}assets/img/shops-60/${shop.logo}'"></div>
                            </div>
                            <div class="col d-flex align-items-center">
                                <div class="product-card-title">${
																	shop.shopname
																}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

	items = shop.items;
	var subtotal = 0;
	$.each(items, (e, item) => {
		productCard += `<div class="product-card-body">
                <div class="product-card-item">
                    <div class="row no-gutters">
                        <div class="col">
                            <div class="row no-gutters">
                                <div class="col-2 col-md-1">
                                    <div class="product-card-image" style="filter: grayscale(100%); background-image: url(${s3_url}assets/img/${
			shop.shopcode
		}/products-40/${item.productid}/${remove_ext(item.primary_pics)}.jpg)"></div>
                                </div>
                                <div class="col product-card-content">
                                    <div class="product-card-name">
                                        ${item.itemname}
                                    </div>
                                    <div class="product-card-quantity">
                                        Quantity: ${item.quantity}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 col-md-2 d-none d-md-block">
                            <div class="product-card-price" style = "white-space:${(item.unit.length > 40) ? 'normal' : 'nowrap'}">
                                ${item.unit}
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="product-card-price">
                                ₱ ${numberFormatter(item.price, false)}
                            </div>
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                </div>
            </div>`;
		subtotal += parseFloat(item.price) * parseFloat(item.quantity);
	});
	productCard += `<div hidden id="shipping-card-${
		shop.shopid
	}" class="product-card-footer">
                <div class="product-card-footer-content container-fluid">
                    <div class="row">
                        <div class="col-12 col-md">
                            <div class="row d-flex justify-content-end">
                                <div class="col col-md-6">
                              <div class="pb-2 col-12 text-left product-card-title">
                                  Shipping
                              </div>
                                    <div class="product-card-footer-option option--active">

                              <div hidden>
                                  <input id="shippingfee-value-${
																		shop.shopid
																	}" type="text" value="${parseFloat(
		shop.shippingfee
	)}">
                              </div>
                                        <div id="shippingfee-card-${
																					shop.shopid
																				}" class="font-weight-bold"> ${
		parseFloat(shop.shippingfee) != 0
			? "₱ " + numberFormatter(shop.shippingfee, false)
			: "Free Shipping"
	}</div>
                                        <div class="">Estimated Shipping Date: </div>

                              <div hidden>
                                  <input id="shippingdts-value-${
																		shop.shopid
																	}" type="text" value="${shop.shopdts}">
                              </div>
                                        <div id="shippingdts-card-${
																					shop.shopid
																				}" class="">(${shop.shopdts}-${
		shop.shopdts_to
	} days)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-card-body py-3 product-card-total">
                <div class="product-card-item">
                    <div class="row no-gutters">

                      <div hidden>
                          <input id="subtotal-value-${
														shop.shopid
													}" type="text" value="${parseFloat(subtotal)}">
                      </div>
                        <div class="col product-card-name text-right">
                            Sub-total:
                        </div>
                        <div class="col-5 col-md-4">
                            <div class="product-card-price" id="subtotal-card-${
															shop.shopid
														}" data-shopid="${shop.shopid}">
                                ₱ ${numberFormatter(
																	parseFloat(subtotal),
																	false
																)}
                            </div>
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

	return productCard;
}

function validateRefCode() {
	var code = $("#checkout_code").val();
	if (code != "") {
		$.ajax({
			method: "POST",
			url: base_url + "api/validateRefCode",
			data: { referral_code: code },
			beforeSend: () => {
				$("#shipping").attr("disabled", true);
			},
			success: (data) => {
				var response = JSON.parse(data);

				if (response.success) {
					showToast({
						type: "success",
						css: "toast-top-full-width mt-5",
						msg: "Referral code verified successfully.",
					});
					$("#verifiedCode").attr("hidden", false);
				} else {
					showToast({
						type: "warning",
						css: "toast-top-full-width mt-5",
						msg:
							"The referral code you entered is invalid. Please check your code and re-enter again. <br><br>" +
							"If you don't have a referral code, you may leave it blank and proceed with payment.",
					});
					$("#checkout_code").val("");
					$("#verifiedCode").attr("hidden", true);
				}

				$("#shipping").attr("disabled", false);
				refcode = $("#checkout_code").val();
			},
			error: (data) => {
				showToast({
					type: "error",
					css: "toast-top-full-width mt-5",
					msg: "Something went wrong.",
				});
				$("#checkout_code").val("");

				$("#shipping").attr("disabled", false);
				refcode = $("#checkout_code").val();
				$("#verifiedCode").attr("hidden", true);
			},
		});
	} else {
		showToast({
			type: "warning",
			css: "toast-top-full-width mt-5",
			msg:
				"The referral code you entered is invalid. Please check your code and re-enter again. <br><br>" +
				"If you don't have a referral code, you may leave it blank and proceed with payment.",
		});
		$("#checkout_code").val("");
		$("#checkOut").attr("disabled", false);
		refcode = $("#checkout_code").val();
	}
}

$("#checkout_code").change(function () {
	validateRefCode();
});

$("#checkout_code").focus(function () {
	$("#shipping").attr("disabled", true);
});

$("#checkout_code").focusout(function () {
	if (refcode == $("#checkout_code").val())
		$("#shipping").attr("disabled", false);
});

async function getValidVouchers() {
	var result = await $.ajax({
		url: base_url + "api/getValidVouchers",
		method: "GET",
		data: null,
	});

	return result;
}

getValidVouchers().then(function (res) {
	validVouchers = res.validVouchers;
});

function newVoucherCode(voucher) {
	return `<span hidden style="font-size: 10px; background-color: #eee;" class="badge badge-pill border p-2 mb-1 ml-1 apply-valid-voucher" id="apply-valid-voucher-${voucher.vcode}">
          <i style="font-size: inherit;" class="fa fa-tag mr-1" aria-hidden="true"></i>
          ${voucher.vcode}
          <i style="font-size: inherit; cursor: pointer;" class="fa fa-times ml-1 delete-apply-voucher" aria-hidden="true" data-shopid="${voucher.shopid}" data-vcode="${voucher.vcode}" data-key="${voucher.key}"></i>
        </span>`;
}

function newAppliedVoucherCode(voucher) {
	var length = $(`#applied-vouchers-section-${voucher.shopid}`).find(
		".discount-item"
	).length;
	// console.log('length', length);
	return `<div class="row no-gutters discount-item mb-1">
            <div class="col product-card-name applied-voucher-code text-md-right">
              ${length == 0 ? "Voucher:" : ""}
            </div>
            <div class="col-11 col-md-4">
              <div class="inline-block pull-left">
                <span style="font-size: 10px; background-color: #eee;" class="badge badge-pill border p-2 ml-1">
                  <i style="font-size: inherit;" class="fa fa-tag mr-1" aria-hidden="true"></i>
                  ${voucher.vcode}
                  <i style="font-size: inherit; cursor: pointer;" class="fa fa-times ml-1 delete-apply-voucher" aria-hidden="true" data-shopid="${
										voucher.shopid
									}" data-vcode="${voucher.vcode}" data-key="${
		voucher.key
	}"></i>
                </span>
              </div>
              <div class="inline-block pull-right total-discount-per-shop">
                <div class="product-card-price">
                    - ₱ ${numberFormatter(parseFloat(voucher.amount), false)}
                </div>
              </div>
            </div>
            <div class="col-1">
            </div>
          </div>`;
}

$(document).delegate(".delete-apply-voucher", "click", function (e) {
	e.preventDefault();
	var thiss = this;
	shopid = $(this).data("shopid");
	vcode = $(this).data("vcode");
	key = $(this).data("key");

	var index = $(thiss).closest("div.discount-item").index();
	var tag2 = $(`#applied-vouchers-section-${shopid}`)
		.find("div.discount-item")
		.eq(index);

	tag2.remove();

	var amount;
	$.each(validVouchers, function (key, item) {
		if (item.shopid == shopid) {
			amount = item.vouchers[index].amount;
			item.vouchers.splice(index, 1);
		}

		// if shops vouchers is empty, delete the shop
		if (item.vouchers.length == 0) {
			validVouchers.splice(key, 1);
		}
	});

	// update shop subtotal
	var shopSubTotal = $(`#disc-subtotal-card-${shopid}`).text();
	shopSubTotal = parseFloat(shopSubTotal.replace(/[^0-9.]/gi, ""));
	var newShopSubTotal = parseFloat(shopSubTotal) + parseFloat(amount);
	$(`#disc-subtotal-card-${shopid}`).text(
		`₱ ${numberFormatter(parseFloat(newShopSubTotal), false)}`
	);

	updateSubTotalAmountCheckout();
	updateTotalAmountCheckout();

	updateVoucherSession().then(function (res) {
		// console.log(res);
	});

	showToast({
		type: "success",
		css: "toast-top-full-width mt-5",
		msg: "Voucher has been removed",
	});

	if (validVouchers.length == 0) {
		$(`#disc-holder-${shopid}`).attr("hidden", true);
		$("#voucher-note").attr("hidden", true);
	}
});

$(document).delegate(".validate-voucher-btn", "click", function (e) {
	e.preventDefault();

	var input = $(this).closest(".input-group").find("input");
	shopid = $(this).data("shopid");

	var data = new FormData();
	data.set("shopid", shopid);
	data.set(input.attr("name"), input.val());

	$.ajax({
		url: base_url + "api/validateVoucher",
		method: "POST",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: () => {
			showCover("Validating voucher(s)...");
		},
		success: (result) => {
			if (result.success == true) {
				clearFormErrors($(`#discount-section-${shopid}`));

				var temp = {};
				temp.shopid = shopid;
				temp.vouchers = [];
				temp.vouchers.push(result.voucher);

				if (validVouchers.length > 0) {
					// check if validVouchers array already contains shopid
					var validVoucherContains = validVouchers.some(function (e) {
						return e.shopid === shopid;
					});

					// if valid vouchers from another shop validation push data
					if (!validVoucherContains) {
						// if (!checkTotalVoucher(shopid, result.voucher)) {
						//   validVouchers.push(temp);
						//   addShopVoucher(result.voucher)
						// }
						validVouchers.push(temp);
						addShopVoucher(result.voucher);

						// else just ammend the shops voucher data
					} else {
						$.each(validVouchers, function (key, val) {
							if (val.shopid === shopid) {
								var codeExistInShop = validVouchers[key].vouchers.some(
									function (e) {
										return e.vcode === result.voucher.vcode;
									}
								);

								if (!codeExistInShop) {
									// if shops subtotal is less than the total voucher amount, do not go through
									// if (!checkTotalVoucher(shopid, result.voucher)) {
									//   validVouchers[key].vouchers.push(result.voucher);
									//   addShopVoucher(result.voucher);
									// }
									validVouchers[key].vouchers.push(result.voucher);
									addShopVoucher(result.voucher);
								}
							}
						});
					}
				} else {
					// if (!checkTotalVoucher(shopid, result.voucher)) {
					//   validVouchers.push(temp);
					//   addShopVoucher(result.voucher);
					// }
					validVouchers.push(temp);
					addShopVoucher(result.voucher);
				}
			} else {
				show_errors(result, $(`#discount-section-${shopid}`));
			}

			updateVoucherSession().then(function (res) {
				// console.log(res);
			});
			hideCover();
		},
		complete: () => {
			input.val("");
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
			loadCheckoutPage();
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
				loadCheckoutPage();
				if (data.cart_count <= 0) location.replace(base_url);
	    }else{
				showToast({
					type: "warning",
					css: "toast-top-full-width mt-5",
					msg: data.message,
				});
				loadCheckoutPage();
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
			loadCheckoutPage();
	  },
	  error: function(){
	    hideCover();
	  }
	});
});

$(document).on('click', '#register_upon_checkout', function(){
	console.log('click');
	if($(this).is(':checked')){
		let email = $("#checkout_email").val();
		if(email == ""){
			showToast({
				type: "warning",
				css: "toast-top-full-width mt-5",
				msg: "Please fill up all required fields",
			});
			return;
		}

		$.ajax({
		  url: base_url+'user/check_email',
		  type: 'post',
		  data:{email},
		  beforeSend: function(){
		    showCover('Checking email ...');
				$('#register_upon_checkout').prop('checked',false);
		  },
		  success: function(data){
				hideCover()
		    if(data.success == 1){
					$('#register_upon_checkout').prop('checked',true);
		    }else{
					console.log(data.message);
					$('#register_upon_checkout').prop('checked',false);
					showToast({
						type: "warning",
						css: "toast-top-full-width mt-5",
						msg: data.message,
					});
		    }
		  },
		  error: function(){
				hideCover();
				showToast({
					type: "warning",
					css: "toast-top-full-width mt-5",
					msg: "Something went wrong. Please try again",
				});
		  }
		});
	}
});

async function removeVoucher(shopid, vcode, key) {
	var data = new FormData();
	data.set("shopid", shopid);
	data.set("vcode", vcode);
	data.set("key", key);

	var result = await $.ajax({
		url: base_url + "api/makeAvailableVoucher",
		method: "POST",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: () => {
			showCover("Removing voucher(s)...");
		},
		complete: (result) => {
			hideCover();
		},
	});

	return result;
}

async function updateVoucherSession() {
	var data = new FormData();
	data.set("validVouchers", JSON.stringify(validVouchers));

	var result = await $.ajax({
		url: base_url + "api/updateValidVouchers",
		method: "POST",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		beforeSend: () => {},
		complete: (result) => {},
	});

	return result;
}

function addShopVoucher(voucher) {
	// add tags below the input field, and new applied vouchers row
	$(`#discount-section-${voucher.shopid}`)
		.find(".apply-voucher-code > div")
		.append(newVoucherCode(voucher));
	$(`#applied-vouchers-section-${voucher.shopid}`).append(
		newAppliedVoucherCode(voucher)
	);

	// update shop subtotal
	var shopSubTotal = $(`#disc-subtotal-card-${voucher.shopid}`).text();
	shopSubTotal = parseFloat(shopSubTotal.replace(/[^0-9.]/gi, ""));
	console.log('subtotal', shopSubTotal);
	var newShopSubTotal = parseFloat(shopSubTotal) - parseFloat(voucher.amount);
	newShopSubTotal = newShopSubTotal < 0 ? 0 : newShopSubTotal;
	$(`#disc-subtotal-card-${voucher.shopid}`).text(
		`₱ ${numberFormatter(parseFloat(newShopSubTotal), false)}`
	);

	updateSubTotalAmountCheckout();
	updateTotalAmountCheckout();

	if (validVouchers.length > 0) {
		$(`#disc-holder-${voucher.shopid}`).attr("hidden", false);
	}
}

function checkTotalVoucher(shopid, voucher) {
	var shopSubTotal = $(`#disc-subtotal-card-${shopid}`).text();
	shopSubTotal = parseFloat(shopSubTotal.replace(/[^0-9.]/gi, ""));
	if (parseFloat(shopSubTotal) < parseFloat(voucher.amount)) {
		showToast({
			type: "warning",
			css: "toast-top-full-width mt-5",
			msg: "Total voucher discount should not exceed shop's cart subtotal",
		});
		return true;
	}

	return false;
}

function updateSubTotalAmountCheckout() {
	var holder = $(".disc-sub-total-card");
	var newOrderSubTotal = 0.0;

	$.each(holder, function (key, val) {
		var shopSubTotal = $(val).text();
		shopSubTotal = parseFloat(shopSubTotal.replace(/[^0-9.]/gi, ""));

		newOrderSubTotal = parseFloat(newOrderSubTotal) + parseFloat(shopSubTotal);
	});
	// console.log('newOrderSubTotal', newOrderSubTotal);
	$("#sub_total_amount_checkout").text(
		`₱ ${numberFormatter(parseFloat(newOrderSubTotal), false)}`
	);
}

function updateTotalAmountCheckout() {
	var subTotalAmount = $("#sub_total_amount_checkout").text();
	subTotalAmount = parseFloat(subTotalAmount.replace(/[^0-9.]/gi, ""));

	var shippingAmount = $("#shipping_amount_checkout").attr("data-amount");
	shippingAmount = parseFloat(shippingAmount.replace(/[^0-9.]/gi, ""));

	// console.log(subTotalAmount, shippingAmount);
	var totalAmountCheckout;
	totalAmountCheckout = subTotalAmount + shippingAmount;
	$("#total_amount_checkout").html(
		`<span class="ml-2">₱ ${numberFormatter(
			parseFloat(totalAmountCheckout),
			false
		)}</span>`
	);
}

// $("#citymunCode").change(function() {
//   var provCode=$(this).children('option:selected').data("provcode");
//   provcodetoship=provCode;
//   update_variant_price(provCode);
// });

function update_variant_price(provCode,display="") {
	var item = {};
    $('.check-price').each(function(index){
      item[index]={
  	  				productid:$(this).data("productid"),
  	  				shopid:$(this).data("shopid")
  		}
    });
	$.ajax({
      url: base_url+"api/Orders/get_prov_variant_price",
      type: 'post',
      data: {item: item, provCode: provCode, display: display},
      cache: false,
      beforeSend: function(){
        showCover('Loading data ...');
      },
      success: (result)=>{
        hideCover();
        $.each(result, function(key, val) {
        	if(display=="shipping"){
        		$('.res-unit-price-'+val.productid).text("Unit Price: ₱ " + numberFormatter(val.price, false));
			    let unit_quantity=$('.res-unit-quantity-'+val.productid).text();
			    unit_quantity=unit_quantity.replace("Quantity: ","");
			    final_price=val.price*unit_quantity;
			    $('.item-'+val.productid+' span').text("₱ " + numberFormatter(final_price, false));
        	}
        	else{
			    $('.unit-item-'+val.productid+' span').text("Unit Price: ₱ " + numberFormatter(val.price, false));
			    let unity_quantity=$('.unit-quantity-'+val.productid).val();
			    final_price=val.price*unity_quantity;
			    $('.item-'+val.productid+' span').text("₱ " + numberFormatter(final_price, false));
			}
		});
		let total_amount=0;
		$('.subtotal-all').each(function(index){
		  	shopid=$(this).data("shopid");
		  	subtotal_amount=0;
		  	$('.get_all_price').each(function(index){
			  	if(shopid==$(this).data("shopid")){
			  		unit_price=$(this).find('span').text();
			  		unit_price=unit_price.replace(/,/g, "");
			  		unit_price=unit_price.replace("₱ ", "");
			  		subtotal_amount+=parseFloat(unit_price);
			  	}
			});
			total_amount+=subtotal_amount;
		  	$("#subtotal-card-"+shopid+" span").text("₱ " +numberFormatter(subtotal_amount, false));
		 });
		$("#sub_total_amount_checkout").text("₱ " +numberFormatter(total_amount, false));
		if(display=="shipping"){
			shipping_amount_checkout=$("#shipping_amount_checkout").text();
			shipping_amount_checkout=shipping_amount_checkout.replace(/,/g, "");
			shipping_amount_checkout=shipping_amount_checkout.replace("₱ ", "");
			if(shipping_amount_checkout=="Free Shipping"){
				shipping_amount_checkout=0;
			}
			total_amount+=parseFloat(shipping_amount_checkout);
		}
		$("#total_amount_checkout").text("₱ " +numberFormatter(total_amount, false));
      }
    });
}
