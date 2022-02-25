$("#menu-toggle").click(function(e) {
	e.preventDefault();
	$("#sideNav").toggleClass("sidebar-toggle");
	$("#overlay").toggleClass("overlay-toggle");
});

$("#overlay").click(function() {
	$("#sideNav").toggleClass("sidebar-toggle");
	$("#overlay").toggleClass("overlay-toggle");
});

$(function() {
	// id="pageActive" data-num="2" data-subnum="0"
	// pageNavigation
	// class="active"
	var base_url = $("body").data("base_url"); //base_url come from php functions base_url();

	var pageNumberActive = $("#pageActive").data("num");
	var collapseActive = $("#pageActive").data("namecollapse");
	var labelname = $("#pageActive").data("labelname");

	$(".pageNavigation")
		.find("li")
		.each(function() {
			var activePage = $(this).data("num");
			if (pageNumberActive == activePage) {
				$(this).addClass("active");
				$(collapseActive).attr("aria-expanded", "true");
				$(collapseActive)
					.closest("li")
					.find(".select-collapse")
					.addClass("show");
				$(collapseActive)
					.closest("li")
					.find("a")
					.each(function() {
						var subnavname = $(this).text();
						if (labelname == subnavname) {
							$(this).css("background", "#2b90d9");
							$(this).css("color", "#fff");
							$(this).css("border-left", "4px solid #1c669c");
						}
					});
			}
		});

	// Added by Rick
	$("#qr-info").focus();

	$("#qr-info").focusout(function() {
		$("#qr-info").focus();
	});

	$("#qr-info").on("change", function() {
		var data = {
			qrData: $(this).val(),
			token: $("#token").val()
		};

		$.ajax({
			url: base_url + "Main_QR/processQR",
			type: "POST",
			data: data,
			beforeSend: function() {
				$("body").LoadingOverlay("show");
			},
			success: function(result) {
				$("body").LoadingOverlay("hide");
				if (result == "error") {
					$("#no-result").removeClass("hide-elements");
					$("#no-result").addClass("show-elements");
					$("#qr-info").val("");
				} else {
					window.location = "" + result + "";
				}
			}
		});
	});

	$(".select2").select2({});

	$(".datepicker-normal").datepicker({
		todayBtn: "linked"
	});

	$(".datepicker").datepicker({
		todayBtn: "linked",
		autoclose: true,
	});

	$('.date_input_from').datepicker({
	  format: 'yyyy-mm-dd',
	  autoclose: true,
	  todayHighlight: true,
	  todayBtn: "linked",
	  startDate:'+0d',
	  endDate:'+7d'
	}).datepicker("setDate", new Date());

	$('.date_input').datepicker({
	  format: 'yyyy-mm-dd',
	  autoclose: true,
	  todayHighlight: true,
	  todayBtn: "linked",
	}).datepicker("setDate", new Date());

	$('.date_input_today_only').datepicker({
	  format: 'yyyy-mm-dd',
	  autoclose: true,
	  todayHighlight: true,
	  todayBtn: "linked",
		startDate: "+0d"
	}).datepicker("setDate", new Date());

	$(".datepicker-before").datepicker({
		todayBtn: "linked",
		endDate: "+0d"
	});

	$(".datepicker-after").datepicker({
		todayBtn: "linked",
		startDate: "+0d"
	});

	$(".input-daterange").datepicker({
		todayBtn: "linked",
		autoclose: true,
	});

	$("#f2_quantity").keyup(function() {
		var val = $("#f2_quantity").val();
		if (parseInt(val) < 0 || isNaN(val)) {
			$("#f2_quantity").val("");
			$("#f2_quantity").focus();
		}
	});

	//for current balance of player

	accounting.settings = {
		currency: {
			symbol: "", // default currency symbol is '$'
			format: "%s%v", // controls output: %s = symbol, %v = value/number (can be object: see below)
			decimal: ".", // decimal point separator
			thousand: ",", // thousands separator
			precision: 2 // decimal places
		},
		number: {
			precision: 0, // default precision on numbers is 0
			thousand: ",",
			decimal: "."
		}
	};
});

update_token = function(value)
{
	$('#template_body').data('token_value',value);
	ajax_token = $("body").data('token_value');
}

draw_transaction_status = function(status){
	var element = "";
	if(status =='1')
	{
		element = "<label class='badge badge-success'> Paid</label>";

		return element;
	}
	else if(status=='f' || status=='d')
	{
		element = "<label class='badge badge-success'> Fulfilled</label>";

		return element;
	}
	else if(status == '0')
	{
		element = "<label class='badge badge-info'> Pending</label>";

		return element;
	}
	else if(status == 'p')
	{
		element = "<label class='badge badge-warning'> Unfulfilled</label>";

		return element;
	}
	else if(status == 'On Process')
	{
		element = "<label class='badge badge-info'> On Process</label>";

		return element;
	}
	else if(status == 'Settled')
	{
		element = "<label class='badge badge-success'> Settled</label>";

		return element;
	}
	else if(status == 'Unsettled')
	{
		element = "<label class='badge badge-danger'>Unsettled</label>";

		return element;
	}
	else
	{
		element = "<label class='badge badge-danger'> Unpaid</label>";

		return element;
	}
}

function getCurrentBalance() {
	var player_id = $("body").data("player_id");
	var base_url = $("body").data("base_url"); //base_url come from php functions base_url();

	if ($("body").attr("data-player_id")) {
		if (player_id != "" || player_id != null || player_id != "undefined") {
			$.ajax({
				type: "post",
				url: base_url + "Main/getCurrentBalance",
				data: { player_id: player_id },
				success: function(data) {
					if (data.success == 1) {
						var res = data.result;
						$("#current_balance_header").text(
							accounting.formatMoney(res[0].current_balance)
						);
						$("#current_balance_val").val(res[0].current_balance);
					} else {
						$("#current_balance_header").text("");
						$("#current_balance_val").val("");
					}
				}
			});
		}
	}
}
getCurrentBalance();

function validate_strong_password(password) {
	var regex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\\d]{8,}$");

	if (regex.test(password)) {
		return true;
	} else {
		return false;
	}
}

//FIXX ISSUE IN NAV

$("#toggle-btn").click(function(e) {
	if ($(this).hasClass("active")) {
		$(".pageNavigation")
			.find("a")
			.css("word-break", "normal");
	} else {
		$(".pageNavigation")
			.find("a")
			.css("word-break", "break-all");
	}
});

function today_now() {
	var currentdate = new Date();
	var today =
		("0" + (currentdate.getMonth() + 1)).slice(-2) +
		"/" +
		("0" + currentdate.getDate()).slice(-2) +
		"/" +
		currentdate.getFullYear();
	return today;
}

function todaytime_slash_proper() {
	return moment().format("MM/DD/YYYY hh:mm A");
}
function format_datetime_slash_proper($date) {
	return moment($date).format("MM/DD/YYYY hh:mm A");
}
function format_date_full($date) {
	return moment($date).format("MMMM D, YYYY - hh:mm:ss A");
}

function format_time_full($date) {
	return moment($date).format("hh:mm:ss A");
}

// setFileName();

// document.getElementById("form-bro").addEventListener("change", handleChange);

// function handleChange(event) {
// 	setFileName(event.target.value);
// }

// function setFileName(fileName) {
// 	document.getElementById("input-file").innerHTML =
// 		fileName || "Click or Drop to upload file.";
// }

//allowing numeric with decimal
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));

	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//allowing numeric without decimal
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {
	$(this).val($(this).val().replace(/[^\d].+/, ""));

	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//Author: Trostam general function (ALL) 25-5-2021 3:12pm
function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode != 46 && charCode > 31
	&& (charCode < 48 || charCode > 57))
		return false;

	return true;
}

//Author: Ram general function (ALL) 10-3-2018 3:12pm
function checkInputs(formname){
	var errorCounter = 0;
	var errorFound = 0;
	$(formname).find('.required_fields:visible').each(function(){ //loop all input field then validate
		if ($(this).val() == ""){
			if($(this).data('reqselect2') == "yes"){
    			$(this).select2({
				    theme: 'default selecttwo-empty'
				});
			}else{
				$(this).css("cssText", "border-color: #d9534f !important"); //change all empty to color red
			}
			errorCounter++;
		}else{
			if($(this).data('reqselect2') == "yes"){
    			$(this).select2().removeClass("selecttwo-empty"); //change all empty to color red
			}else{
				$(this).css("cssText", "border-color: #eee !important");  //rollback when not empty
			}
			errorFound = 0;
		}
	});

	$(formname).find('.required_fields:visible').each(function(){ //loop all input field then validate
		if ($(this).val() == ""){ // if empty show error
			errorFound = 1; //update error to 1
				// $(this).css("border-color","#d9534f");
			if($(this).data('reqselect2') == "yes"){
    			$(this).select2({
				    theme: 'default selecttwo-empty'
				}); //change all empty to color red
			}else{
				$(this).css("cssText", "border-color: #d9534f !important"); //change all empty to color red
			}
			$(this).focus();
			if(errorCounter > 1){

				//messageBox('Please fill out all required fields',"Warning","warning");
				showCpToast("warning", "Warning!", 'Please fill out all required fields');

			}else{
				//messageBox('Please fill out all required fields',"Warning","warning");
				showCpToast("warning", "Warning!", 'Please fill out all required fields');
			}
			//errorFound = true;
			errorFound = 1;
			return false; //focus first empty fields

		}else{
			errorFound = 0;
		}

	});
	return errorFound;
}//this validation will check for null values of required fields

function messageBox(message,header,msgType){//Message to display, Message heading note!:leave blank if error, Message type eg:Success, Warning or Error leave blank if error
	var x = msgType.toLowerCase();
	switch(x) {
	  case "success":
	    $.toast({
		    text: message,
		    icon: 'success',
		    loader: false,
		    stack: false,
		    position: 'top-center',
		    bgColor: '#5cb85c',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 4000
		});
	    break;
	  case "warning":
	    $.toast({
		    text: message,
		    icon: 'warning',
		    loader: false,
		    stack: false,
		    position: 'top-center',
		    bgColor: '#f0ad4e;',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 4000
		});
	    break;
	  default:
	    $.toast({
		    text: message,
		    icon: 'error',
		    loader: false,
		    stack: false,
		    position: 'top-center',
		    bgColor: '#ff0000;',
			textColor: 'white',
			allowToastClose: false,
			hideAfter: 4000
		});
	}


}

function rollback_required_fields(formname){
$(formname).find('.required_fields:visible').each(function(){ //loop all input field then validate
            $(this).css("border-color", "#eee");
        });
}


function alphaOnly(event) {
	var key = event.keyCode;
	return ((key >= 65 && key <= 90) || key == 8 || key == 32 || key == 9);
};

function alphanumericOnly(event) {
	var key = event.key;
	if(/[^a-zA-Z0-9]/.test(key)){
		return false;
	}
};

function shopcodeKeytrapped(event) {
	var key = event.key;
	if(!/^[A-Z0-9]+|[\b]+$/.test(key)){
		return false;
	}
};

function shopurlKeytrapped(event) {
	var key = event.key;
	if(!/^[A-Z0-9_]+|[\b\s]+$/.test(key)){
		return false;
	}
};
