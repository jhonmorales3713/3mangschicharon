
// Start of csrf related codes

// var csrf_hash = $('#csrf_hash')

// $(document).on({
// 	ajaxSend: function (event, response, request) {
// 		if (request.type === "POST") {
// 			if (request.data instanceof FormData) {
// 				request.data.append(csrf_hash.attr('name'), csrf_hash.val())
// 			}
// 			else {
// 				request.contentType = 'x-www-form-urlencoded';
// 				if (request.data != undefined) {
// 					request.data += '&' + csrf_hash[0].name + '=' + encodeURIComponent(csrf_hash[0].value)
// 				}
// 				else {
// 					request.data = csrf_hash[0].name + '=' + encodeURIComponent(csrf_hash[0].value)
// 				}

// 			}
// 		}
// 	},
// 	ajaxComplete: function (event, response, request) {
// 		if (request.type === "POST") {
// 			$('input[name='+response.responseJSON.csrf_name+']').val(response.responseJSON.csrf_hash)
// 		}
// 	},
// 	ajaxError: function (event, response) {
// 		if (response.status == 403) {
// 			toastMessage('Note', 'Security token has been expired. This page is being reloaded.', 'error')

// 			setTimeout(function(){
// 				window.location.href = window.location.href;
// 			}, 1000)
// 		}
// 		else {
// 			toastMessage('Note', 'Something went wrong. Please try again. If the error persists, kindly contact the support@pandabooks.ph.', 'error')
// 		}
// 	}
// });

// End of csrf related codes

$('[data-toggle="tooltip"]').tooltip();

$("#pageNavigation").on("click", "#moduleLink", function(){
	window.open($(this).data("href"), '_self');
	localStorage.setItem("currentPageId", $(this).data("num"));
});

// back to top 
$('#easy-top').click(function () {
	$("html, body").animate({
		scrollTop: 0
	}, {
		duration: 500,
	});
});
$(window).scroll(function () {
	if ($(this).scrollTop() > 600) {
		$('#easy-top').fadeIn(100);
	} else {
		$('#easy-top').fadeOut(0);
	}
});

// allow number only ================
$(".numberonly").keypress(function (e) { //if the character is not digit then dont't display the error and don't type anything
if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
	return false; // 8 = backspace &  48-57 = digits 0-9
}
}).on("cut copy paste",function(e) {
	e.preventDefault();
});
// =====================================


// toggle password visibility ========
$('.toggle_password').on('mousedown', function(e) {
	$(this).find('.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');
	var x = $(this).closest('.parent_toggle_password').find('input');
	x.attr('type', "text");
});
$('*').on('mouseup', function() {
	if($('.parent_toggle_password')){
		$('.parent_toggle_password').find('.fa-eye').removeClass('fa-eye').addClass('fa-eye-slash');
		var x = $('.parent_toggle_password').find('input');
		x.attr('type', "password");
	}
});
// =====================================

// get the date today
var d = new Date();
var date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate();

function formatDate(date) {
	var d = new Date(date),
	month = '' + (d.getMonth() + 1),
	day = '' + d.getDate(),
	year = d.getFullYear();
	
	if (month.length < 2) month = '0' + month;
	if (day.length < 2) day = '0' + day;
	
	return [year, month, day].join('-');
}

function formatDate_slash(date) {
	var d = new Date(date),
	month = '' + (d.getMonth() + 1),
	day = '' + d.getDate(),
	year = d.getFullYear();
	
	if (month.length < 2) month = '0' + month;
	if (day.length < 2) day = '0' + day;
	
	return [month, day, year].join('/');
}

function removeFormatDate(date) {
	var d = new Date(date),
	month = '' + (d.getMonth() + 1),
	day = '' + d.getDate(),
	year = d.getFullYear();
	
	if (month.length < 2) month = '0' + month;
	if (day.length < 2) day = '0' + day;
	
	return [month, day, year].join('/');
}

toastBgColor = {
	info: "#5cb85c",
	error: "#f0ad4e"
}

// reuseable toast call function for easeness and shorter code
function toastMessage(heading, text, icon) {
	
	$.toast({
		heading: heading,
		text: text,
		icon: icon,
		loader: false,  
		stack: false,
		position: 'top-center', 
		allowToastClose: false,
		bgColor: toastBgColor[icon],
		textColor: 'white'  
	});
	
}

function check_if_null(str){
	return ( str ? str : 'N/A');
}

//allowing numeric with decimal 
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
	$(this).val($(this).val().replace(/[^0-9\.]/g,''));
	
	if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

$(document).delegate('.allownumericwithdecimal','keypress keyup blur',function(e){
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

$(document).delegate('.allownumericwithoutdecimal','keypress keyup blur',function(e){
	$(this).val($(this).val().replace(/[^\d].+/, ""));
	
	if ((event.which < 48 || event.which > 57)) {
		event.preventDefault();
	}
});

//prevent the dropdown-menus from closing upon clicking a label
$('.stop_close_dropdown').on(function(e){
	e.stopPropagation();
});

// // hiding datepicker after date selection
$('*[class*=datepicker]').datepicker({
	format: 'mm/dd/yyyy',
}).on('changeDate', function(e){
	$(this).datepicker('hide');
});

function lengthIsHigher(element, length) {
	if (element.length > length) {
		return true;
	}
	else {
		return false;
	}
}

function lengthIsLower(element, length) {
	if (element.length < length) {
		return true;
	}
	else {
		return false;
	}
}

function lengthIsEqual(element, length) {
	if (element.length == length) {
		return true;
	}
	else {
		return false;
	}
}

/* Regex validation Start */

function isEmail(email) {
	var regex = /^([ña-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test(email);
}

function isWholeOrDecimal(number) {
	const regex = /^(0|[1-9]\d*)(\.\d+)?$/
	return regex.test(number)
}

function isEmpty(value) {
	return value.trim().length < 1 ? true : false
}

// Read image in input file and display to an img element
function readURL(input, selector) {
	selector.attr('src', "");
	
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			selector.attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

function concatenateName(fname, mname, lname, supplement = "") {
	name = "";
	
	if (fname != "") {
		name += fname + " ";
	}
	
	if (mname != "") {
		name += mname + " ";
	}
	
	if (lname != "") {
		name += lname;
	}
	
	if (supplement != "" && supplement != "none") {
		name += " - " + supplement + "";
	}
	
	return name.toUpperCase();
}

function format_address(address) {
	result = $.trim(address.toString().replace(/\,/g, ''));
	return result;
}

function formatMoney(n,c, d, t) {
	c = isNaN(c = Math.abs(c)) ? 2 : c;
	d = d == undefined ? "." : d;
	t = t == undefined ? "," : t; 
	s = n < 0 ? "-" : "";
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

//for format Money
accounting.settings = {
	currency: {
		symbol : "",   // default currency symbol is '$'
		format: "%s%v", // controls output: %s = symbol, %v = value/number (can be object: see below)
		decimal : ".",  // decimal point separator
		thousand: ",",  // thousands separator
		precision : 2   // decimal places
	},
	number: {
		precision : 0,  // default precision on numbers is 0
		thousand: ",",
		decimal : "."
	}
}

function discount_representation(disctype, discamt) {
	if (disctype == 2) {
		discount = accounting.formatMoney(discamt) + "%";
	}
	else {
		discount = accounting.formatMoney(discamt)
	}
	
	return discount;
}

function discounted_total(price, qty, disc_val, disc_type) {	
	if (disc_type == 2){
		total_sum = price * parseFloat(qty);
		total     = parseFloat(total_sum) * (disc_val / 100);
		
	}else{
		total = (parseFloat(price) * parseFloat(qty)) - parseFloat(disc_val);
	}

	return parseFloat(total).toFixed(2);
}

function gen_discounted_total(subtotal, freight, disc_val, disc_type) {
	if (disc_type == 2) {
		percentage = (disc_val / 100).toFixed(2);
		total_discount = parseFloat(percentage) * parseFloat(total); 
		total = parseFloat(total) - parseFloat(total_discount);
	}
	else {
		total =  parseFloat(subtotal) - parseFloat(disc_val);
	}
	
	return (parseFloat(total) + parseFloat(freight));
}

// function final_grand_total(grand_total, shipping_fee, gen_disc_type, gen_disc_amt)
// {
// 	let percentage = 0

// 	if(gen_disc_type == 1) {
// 		grand_total += gen_disc_amt
// 	}
// 	else if(gen_disc_type == 1) {
// 		percentage = (gen_disc_amt / 100).toFixed(2)

// 		grand_total -= grand_total * percentage
// 	}

// 	if(shipping_fee > 0)
// 	{
// 		grand_total += shipping_fee
// 	}

// 	return grand_total
// }

function get_discount(val){
	var itemid = val;  
	var idno = $("#idno").val();
	if (itemid != "") {
		$.ajax({
			url: base_url+'Main_sales/get_customer_discount',
			data:{'itemid': itemid, 'idno': idno},
			beforeSend:function(data){
				$(".select_disc").LoadingOverlay("show"); 
			},
			complete: function(){
				$(".select_disc").LoadingOverlay("hide"); 
			},
			success:function(data){
				csrf_hash = data.csrf_hash;
				if (data.success == 1){
					var res = data.result;
					var disctype = data.disctype;
					
					if (disctype == 1) {
						$(".amount_div").show();
						$("#disc_amt").val(res);
						$("#discount_type_select").val(1);
						$(".percentage_div").hide();
					}
					else if (disctype == 2) {
						$(".percentage_div").show();
						$("#disc_percent").val();
						$("#disc_percent").val(res);
						$("#discount_type_select").val(2);
						$(".amount_div").hide();
					}  
				}
				else {
					$(".amount_div").hide();
					$("#disc_amt").val();
					$("#disc_percent").val();
					$(".percentage_div").hide();
				}
			}
		});
	}
}

function foreign_to_peso(currency_id, currency_val, currency_amt) {
	let converted_value
	converted_value = parseFloat(currency_amt) * parseFloat(currency_val)
	
	return converted_value
}

function peso_to_foreign(currency_id, currency_val, currency_amt) {
	let converted_value
	converted_value = parseFloat(currency_amt) / parseFloat(currency_val)
	
	return converted_value
}

$('.currency').keyup(function(event) {
	// skip for arrow keys
	if(event.which >= 37 && event.which <= 40){
		event.preventDefault()
	}
	
	$(this).val(function(index, value) {
		value = value.replace(/,/g,'')
		return numberWithCommas(value)
	})
});

$(".percentage").on('keyup', function() {
	var max = 100
	var min = 0
	
	if ($(this).val() > max) {
		$(this).val(max);
	}
	else if ($(this).val() < min) {
		$(this).val(min);
	}      
})

function numberWithCommas(number) {
	var parts = number.toString().split(".")
	parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",")
	return parts.join(".")
}

function remove_number_format(number) {
	return number.replace(/,/g, '')
}

function makeProgress(from, to) { //increase
	if(from < to){
		from = from + .20;
		$(".progress-bar").css("width", from + "%");
	}
	// Wait for sometime before running this script again
	setTimeout(function(){
		makeProgress(from, to);
	}, 1);
}

function makeRollback(from, to) { //decrease
	if(from > to){
		from = from - .20;
		$(".progress-bar").css("width", from + "%");
		
	}
	// Wait for sometime before running this script again
	setTimeout(function(){
		makeRollback(from, to);
	}, 1);
}

// Datatable Searching

column = ""
value = ""

column_counter = 0

$('.dropdown-menu.keep-open').on('click', function (e) {
  e.stopPropagation();
});

// start 041520
function generate_searches(table_id) {
	$count = 0;
	$(table_id + ' thead th').each(function () {
		$count++;

		var title = $(this).text();

		console.log(title);
		
		if (title != 'Action') {	
			$('#search_dropdown').append(
				'<div class="form-check mx-1">' + 
				'<input type="checkbox" value="' + title.replace(/\s/g, '') + 'div" class="form-check-input dropdownCheck_c" id="dropdownCheck'+$count+'">' + 
				'<label class="form-check-label" for="dropdownCheck'+$count+'">' + title + '</label>' + 
				'</div>'
			);
			
			$('#search_div').append(
				'<div class="form-group col-md-6 search_divs" id="' + title.replace(/\s/g, '') + 'div" style="display: none;">' +
				'<label class="form-control-label col-form-label-sm">' + title + '</label>' +
				'<input type="text" data-column="'+ column_counter +'" class="input-sm form-control search-input-text input_search" placeholder="' + title + '" />' +
				'</div>'
			);
		}
		column_counter++;
	});
}
$("#search_dropdown").click(function(e){
	e.stopPropagation();
});
		
$('#search_dropdown').on('click', '.dropdownCheck_c', function(){

	div = $(this).val();
	
	if (div != "") {
		if ( $( this ).prop( "checked" ) ) {
			$('#' + div).show('slow');
		}
		else {
			$('#' + div).hide('slow');
		}
		$('.check_all').prop('checked', false);
	}
	else {
		if ($('.check_all').prop('checked')) {
			$(".search_divs").show('slow');
			$('input:checkbox').not(this).prop('checked', true);
		}else{
			$(".search_divs").hide('slow');
			$('input:checkbox').not(this).prop('checked', false);
		}
	}
});
		
$('#search_filter').on('change', function(){
	div = $(this).val()
	
	$('#search_div div').each(function () {
		if (this.id == div)
		$(this).show('slow')
		else
		$(this).hide('slow')
	})
	
	$('#search_div').find('input:text').val('')
});

function search_table(table) {
	if ($('.check_all').prop('checked')) {
		table
		.search( '' )
		.columns().search( '' )
		.draw()
	}
	else {
		table
		.columns( column )
		.search( value, true, true )
		.draw()
	}
}
// end 041520	

// for required fields
		
function check_required_fields(selector) {
	$(''+ selector +'').each(function(){
		if ($(this).val() == "") {
			$(this).addClass("error")
		}
		else {
			$(this).removeClass("error")
		}
	})
}

$('.required').on("keypress keyup blur", function() {
	if ($(this).val() == "") {
		$(this).addClass("error")
	}
	else {
		$(this).removeClass("error")
	}
})
		
		// end for required fields
		
		function base64ToBlob(base64, mime) {
			mime = mime || '';
			var sliceSize = 1024;
			var byteChars = window.atob(base64);
			var byteArrays = [];
			
			for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
				var slice = byteChars.slice(offset, offset + sliceSize);
				
				var byteNumbers = new Array(slice.length);
				for (var i = 0; i < slice.length; i++) {
					byteNumbers[i] = slice.charCodeAt(i);
				}
				
				var byteArray = new Uint8Array(byteNumbers);
				
				byteArrays.push(byteArray);
			}
			
			return new Blob(byteArrays, {type: mime});
		}
		
		function get_discount(val){
			var itemid = val;  
			var idno = $("#idno").val();
			if (itemid != "") {
				$.ajax({
					url: base_url+'Main_sales/get_customer_discount',
					data:{'itemid': itemid, 'idno': idno},
					beforeSend:function(data){
						$(".select_disc").LoadingOverlay("show"); 
					},
					complete: function(){
						$(".select_disc").LoadingOverlay("hide"); 
					},
					success:function(data){
						csrf_hash = data.csrf_hash;
						if (data.success == 1){
							var res = data.result;
							var disctype = data.disctype;
							
							if (disctype == 1) {
								$(".amount_div").show();
								$("#disc_amt").val(res);
								$("#discount_type_select").val(1);
								$(".percentage_div").hide();
							}
							else if (disctype == 2) {
								$(".percentage_div").show();
								$("#disc_percent").val();
								$("#disc_percent").val(res);
								$("#discount_type_select").val(2);
								$(".amount_div").hide();
							}  
						}
						else {
							$(".amount_div").hide();
							$("#disc_amt").val();
							$("#disc_percent").val();
							$(".percentage_div").hide();
						}
					}
				});
			}
		}
		
		function po_price_formula(quantity, price) {
			return quantity * price
		}	
		function button_pre_process(button) {
			$(button).text('Please wait...')
			$(button).attr('disabled', true)
		}
		
		function button_post_process(button, default_text = '') {
			default_text = (default_text == '') ? 'Save' : default_text
			
			$(button).text(default_text)
			$(button).attr('disabled', false)
		}
		



// start 041520
function generate_searches_j(table_id) {
	column_counter = 0
	$count = 0;

	$('#search_div_j').append(
		'<div class="form-group col-xl-4 col-lg-6 search_divs" id="Daterangediv" style="display: none;">' +
		'<label class="form-control-label col-form-label-sm">' + "Date Range" + '</label>' +
		'<div class="input-daterange input-group " id="datepicker">' +
			'<input type="text" name="daterange_from" data-column="'+ column_counter++ +'" class="input-sm form-control search-input-date input_search" readonly="" id="daterange_from">' +
            '<span class="datetotext">to</span>' +
            '<input type="text" name="daterange_to" class="input-sm form-control search-input-date input_search" readonly="" id="daterange_to">' +
        '</div>' +
        '</div>'
	);
	$(table_id + ' thead th').each(function () {
		$count++;

		var title = $(this).text();
		
		if (title != 'Action') {	
			$('#search_dropdown_j').append(
				'<div class="form-check mx-1">' + 
				'<input type="checkbox" value="' + title.replace(/\s/g, '') + 'div" class="form-check-input dropdownCheck_c" id="dropdownCheck'+$count+'">' + 
				'<label class="form-check-label" for="dropdownCheck'+$count+'">' + title + '</label>' + 
				'</div>'
			);
			
			$('#search_div_j').append(
				'<div class="form-group col-xl-4 col-lg-6 search_divs" id="' + title.replace(/\s/g, '') + 'div" style="display: none;">' +
				'<label class="form-control-label col-form-label-sm">' + title + '</label>' +
				'<input type="text" name="'+title.replace(/\s/g, '').toLowerCase()+'" data-column="'+ column_counter +'" class="input-sm form-control search-input-text input_search '+title.replace(/\s/g, '')+'" placeholder="' + title + '" />' +
				'</div>'
			);
		}
		column_counter++;
	});
	
	
}
$("#search_dropdown_j").click(function(e){
	e.stopPropagation();
});
	

$('#search_dropdown_j').on('click', '.dropdownCheck_c', function(){

	div = $(this).val();
	// alert(div);
	if (div != "") {
		if ( $( this ).prop( "checked" ) ) {
			$('#' + div).show('slow');
		}
		else {
			if (div == 'Daterangediv') {
				$("#daterange_from, #daterange_to").val('');
				$('#datepicker').datepicker('setDate', null);
			}
			$('#' + div).hide('slow');
			$("."+div.slice(0, -3)).val('');

		}
		$('.check_all').prop('checked', false);
	}
	else {
		if ($('.check_all').prop('checked')) {
			$(".search_divs").show('slow');
			$('input:checkbox').not(this).prop('checked', true);
		}else{
			$(".search_divs").hide('slow');
			$('input:checkbox').not(this).prop('checked', false);
			$(".input_search").val('');
			$('#datepicker').datepicker('setDate', null);
		}
	}
});
		
$('#search_filter_j').on('change', function(){
	div = $(this).val()
	
	$('#search_div_j div').each(function () {
		if (this.id == div)
		$(this).show('slow')
		else
		$(this).hide('slow')
	})
	
	$('#search_div_j').find('input:text').val('')
});


function search_table_j(inputs_arr) {
	fillDatatable(inputs_arr);
}

var inputs_arr = []
$(".btn_searchby_j").on("click", function() {
	inputs_arr = [];
	$('.input_search').each(function(){
		// inputs_arr.push($(this).val());
		// alert($(this).attr("name"));
		var name = $(this).attr("name");
		var value = $(this).val();
		item = {}
		item [name] = value;

		inputs_arr.push(item);
	});
	search_table_j(inputs_arr);
	// fillDatatable();
});
// end 041520	

$(".select_year").datepicker({
	format: "yyyy",
	viewMode: "years", 
	minViewMode: "years"
});

$(function(){

	// toast options
	sys_toast_success = function(message)
	{
		sys_toast(message,'Success','success','top-center','yellowgreen','white');
	}

	sys_toast_error = function(message)
	{
		sys_toast(message,'Error','error','top-center','red','white');
	}

	sys_toast_warning = function(message)
	{
		sys_toast(message,'Warning','warning','top-center','orange','white');
	}

	sys_toast_warning_info = function(message)
	{
		sys_toast(message,'Note','info','top-center','orange','white');
	}

	sys_toast_info = function(message)
	{
		sys_toast(message,'Note','info','top-center','lightblue','white');
	}

	sys_toast = function(message,heading,icon,position,bgcolor,txtcolor)
	{
		$.toast({
		    heading: heading,
		    text: message,
		    icon: icon,
		    loader: false,  
		    stack: false,
		    position: position, 
			allowToastClose: false,
			bgColor: bgcolor,
			textColor: txtcolor  
		});
	}
});

function display_c(){
	var refresh=1000; // Refresh rate in milli seconds
	mytime=setTimeout('display_ct()',refresh)
}

function display_ct() {
	var weekday = new Array(7);
	weekday[0] = "Sunday";
	weekday[1] = "Monday";
	weekday[2] = "Tuesday";
	weekday[3] = "Wednesday";
	weekday[4] = "Thursday";
	weekday[5] = "Friday";
	weekday[6] = "Saturday";

	var month = new Array();
	month[0] = "January";
	month[1] = "February";
	month[2] = "March";
	month[3] = "April";
	month[4] = "May";
	month[5] = "June";
	month[6] = "July";
	month[7] = "August";
	month[8] = "September";
	month[9] = "October";
	month[10] = "November";
	month[11] = "December";

	var x = new Date()
	var x = "PH "+weekday[x.getDay()]+" "+month[x.getMonth()]+" "+x.getDate()+", "+x.getFullYear()+" "+x.getHours()+":"+x.getMinutes()+":"+x.getSeconds();
	$('.currentDateTime').text(x);
	display_c();
}

$(document).delegate('.timepickervalidation','keypress keyup',function(e){
	val = e.originalEvent.key;
	if(e.keyCode == 48 || e.keyCode == 49 || e.keyCode == 50 || e.keyCode == 51 || e.keyCode == 52 || e.keyCode == 53 || e.keyCode == 54 || e.keyCode == 55 || e.keyCode == 56 || e.keyCode == 57 || val == ":" || e.keyCode == 8 || e.keyCode == 13 || e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 16){

	}
	else{
		$(this).val('00:00');
	}

});

// Record time of log in and logout  of seller or the time the seller open i'ts browser
$(document).ready(function ()
{
	var base_url = $("body").data("base_url");

	$.ajax({
		url: base_url + 'activity/in',
		data:{'activity': 'in'},
		method: "POST",
		success: (response) => {
		
		},
		error: (err) => {
			console.log(err);
		},
	});
});

// avoid zero on text field
$(document).delegate('.notallowzero','keypress keyup paste focusout',function(e){
	if($(this).val() > 0 && $(this).val() != '.'){

	}
	else{
		$(this).val('');  
	}
});
// converts negative numbers into positiver numbers
function toPositive(n){
    if(n < 0){
        n = n * -1;
    }
    return n;
}

function number_format(n, d) {
	var parts = (+n).toFixed(d).split(".");
	var num = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (+parts[1] ? "." + parts[1] : ".00");

	return num;
}