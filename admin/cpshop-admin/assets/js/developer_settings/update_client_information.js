$(document).ready(function(){			
	imageBlob = '';
	var token    = $("body").data('token');

    $('#btn_open_modal_save').click(function(e){
        reset_form();
    })

    var reset_form = function(){
        $('#form_save')[0].reset();
        $('#modal_save').modal();
	}

	$('#form_save').submit(function(e){
		e.preventDefault();
        $.LoadingOverlay("show");
		var form = $(this);
		var form_data = new FormData(form[0]);
        //form_data.append([ajax_token_name],ajax_token);
        var endpoint = form.data('update_url');
        var close_modal = false;        
        
		$.ajax({
	  		type: form[0].method,
	  		url: endpoint,
	  		data: form_data,
	  		contentType: false,   
			cache: false,      
			processData:false,
	  		success:function(data){
                $.LoadingOverlay("hide");
	  			var json_data = JSON.parse(data);
	  			//sys_log(json_data.environment,json_data);
	  			update_token(json_data.csrf_hash);
	  			if(json_data.success) {
					// sys_toast_success(json_data.message);
					// location.reload();
					showCpToast("success", "Success!", json_data.message);
        			setTimeout(function(){location.reload()}, 2000);
	  			}else{
					//sys_toast_warning(json_data.message);
					showCpToast("warning", "Warning!", json_data.message);
	  			}
	  		},
	  		error: function(error){
	  			//sys_toast_error('Something went wrong. Please try again.');
	  			showCpToast("error", "Error!", json_data.message);
	  		}
	  	});

	});
    
	$(document).delegate('.btn_tbl_update','click',function(e){		
		window.location.assign(base_url+"developer_settings/Dev_settings_client_information/client_information");
	});

	$('#main_logo').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".main_logo" ).empty();
        imagesPreview(this, 'div.main_logo');
        $('.main_logo').hide('slow');
        $('.main_logo').show('slow');
        $('#file_label_main_logo').text(countFiles+' Attached Image(s)');
        $('#main_logo_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#secondary_logo').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".secondary_logo" ).empty();
        imagesPreview(this, 'div.secondary_logo');
        $('.secondary_logo').hide('slow');
        $('.secondary_logo').show('slow');
        $('#file_label_secondary_logo').text(countFiles+' Attached Image(s)');
        $('#secondary_logo_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#placeholder_img').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".placeholder_img" ).empty();
        imagesPreview(this, 'div.placeholder_img');
        $('.placeholder_img').hide('slow');
        $('.placeholder_img').show('slow');
        $('#file_label_placeholder_img').text(countFiles+' Attached Image(s)');
        $('#placeholder_img_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#fb_image').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".fb_image" ).empty();0
        imagesPreview(this, 'div.fb_image');
        $('.fb_image').hide('slow');
        $('.fb_image').show('slow');
        $('#file_label_fb_image').text(countFiles+' Attached Image(s)');
        $('#fb_image_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#favicon').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".favicon" ).empty();
        imagesPreview(this, 'div.favicon');
        $('.favicon').hide('slow');
        $('.favicon').show('slow');
        $('#file_label_favicon').text(countFiles+' Attached Image(s)');
        $('#favicon_checker').val('true')
        $.LoadingOverlay("hide");
    });


	var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img id="product_preview">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    $('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
                }

                reader.readAsDataURL(input.files[i]);

            }
        }

	};


var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var shop_url = $("body").data('shop_url');
var s3bucket_url = $("body").data('s3bucket_url');
var c_id = $('#c_id').val();

//// update product	
if(c_id != ""){
	$.LoadingOverlay("show");
	$.ajax({
		 type:'get',
		 url:base_url+'developer_settings/Dev_settings_client_information/get_client_info/'+c_id,
		 success:function(data){				 
			 var json_data = JSON.parse(data);
			 if(json_data.success){
				 $.LoadingOverlay("hide");
				 $('#f_id').val(json_data.message.c_id);
				 $('#f_name').val(json_data.message.c_name);
				 $('#f_initial').val(json_data.message.c_initial);
				 $('#f_id_key').val(json_data.message.id_key);
				 $('#f_tag_line').val(json_data.message.c_tag_line);
				 $('#f_email').val(json_data.message.c_email);
				 $('#f_phone').val(json_data.message.c_phone);
				 $('#f_auto_email_sender').val(json_data.message.c_auto_email_sender);
				 $('#f_social_media_fb_link').val(json_data.message.c_social_media_fb_link);
				 $('#f_social_media_ig_link').val(json_data.message.c_social_media_ig_link);
				 $('#f_social_media_youtube_link').val(json_data.message.c_social_media_youtube_link);
				 $('#f_faqs_link').val(json_data.message.c_faqs_link);
				 $('#f_url_shop_live').val(json_data.message.c_url_shop_live);
				 $('#f_url_shop_test').val(json_data.message.c_url_shop_test);
				 $('#f_url_shop_local').val(json_data.message.c_url_shop_local);
				 $('#f_url_admin_live').val(json_data.message.c_url_admin_live);
				 $('#f_url_admin_test').val(json_data.message.c_url_admin_test);
				 $('#f_url_admin_local').val(json_data.message.c_url_admin_local);
				 $('#f_url_root_live').val(json_data.message.c_url_root_live);
				 $('#f_url_root_test').val(json_data.message.c_url_root_test);
				 $('#f_url_root_local').val(json_data.message.c_url_root_local);
				 $('#f_url_root_segment_live').val(json_data.message.c_url_root_segment_live);
				 $('#f_url_root_segment_test').val(json_data.message.c_url_root_segment_test);
				 $('#f_url_root_segment_local').val(json_data.message.c_url_root_segment_local);
				 $('#c_google_api_key').val(json_data.message.c_google_api_key);
				 $('#c_apiserver_link_live').val(json_data.message.c_apiserver_link_live);
				 $('#c_apiserver_link_test').val(json_data.message.c_apiserver_link_test);
				 $('#c_apiserver_link_local').val(json_data.message.c_apiserver_link_local);
				 $('#c_s3bucket_link_live').val(json_data.message.c_s3bucket_link_live);
				 $('#c_s3bucket_link_test').val(json_data.message.c_s3bucket_link_test);
				 $('#c_s3bucket_link_local').val(json_data.message.c_s3bucket_link_local);
				 $('#c_cpshop_api_url').val(json_data.message.c_cpshop_api_url);
				 $('#c_jcfulfillment_shopidno').val(json_data.message.c_jcfulfillment_shopidno);
				 $('#f_privacy_policy').val(json_data.message.c_privacy_policy);
				 $('#f_terms_and_condition').val(json_data.message.c_terms_and_condition);
				 $('#f_contact_us').val(json_data.message.c_contact_us);
				 $('#f_fb_pixel_id_live').val(json_data.message.c_fb_pixel_id_live);
				 $('#f_fb_pixel_id_test').val(json_data.message.c_fb_pixel_id_test);
				 $('#f_get_seller_reg_form').val(json_data.message.c_get_seller_reg_form);
				 $('#f_google_site_verification').val(json_data.message.google_site_verification);
				 $('#c_order_ref_prefix').val(json_data.message.c_order_ref_prefix);
				 $('#c_order_so_ref_prefix').val(json_data.message.c_order_so_ref_prefix);
				 $('#c_seo_website_desc').val(json_data.message.c_seo_website_desc);
				 $('#c_inv_threshold').val(json_data.message.c_inv_threshold);
				 $('#c_order_threshold').val(json_data.message.c_order_threshold);
				 $('#c_comingsoon_password_local').val(json_data.message.c_comingsoon_password_local);
				 $('#c_comingsoon_password_test').val(json_data.message.c_comingsoon_password_test);
				 $('#c_comingsoon_password_live').val(json_data.message.c_comingsoon_password_live);
				 $('#f_ofps').val(json_data.message.c_ofps);
				 $('#f_startup').val(json_data.message.c_startup);
				 $('#f_jc').val(json_data.message.c_jc);
				 $('#f_mcjr').val(json_data.message.c_mcjr);
				 $('#f_startup').val(json_data.message.c_startup);
				 $('#f_mc').val(json_data.message.c_mc);
				 $('#f_mcsuper').val(json_data.message.c_mcsuper);
				 $('#f_mcmega').val(json_data.message.c_mcmega);
				 $('#f_others').val(json_data.message.c_others);
				 $('#c_toktok_authorization_key').val(json_data.message.c_toktok_authorization_key);
				 $('#c_pusher_app_key_local').val(json_data.message.c_pusher_app_key_local);
				 $('#c_pusher_app_key_test').val(json_data.message.c_pusher_app_key_test);
				 $('#c_pusher_app_key_live').val(json_data.message.c_pusher_app_key_live);
				 $('#c_api_auth_key_local').val(json_data.message.c_api_auth_key_local);
				 $('#c_api_auth_key_test').val(json_data.message.c_api_auth_key_test);
				 $('#c_api_auth_key_live').val(json_data.message.c_api_auth_key_live);
				 $('#c_toktok_api_endpoint').val(json_data.message.c_toktok_api_endpoint);
				 $('#c_toktokwallet_api_endpoint').val(json_data.message.c_toktokwallet_api_endpoint);
				 $('#c_toktokwallet_authorization_key').val(json_data.message.c_toktokwallet_authorization_key);
				 $('#c_shop_main_announcement').val(json_data.message.c_shop_main_announcement);
				 console.log(json_data.message);
				 $('#c_404page').val(json_data.message.c_404page);
				 $('#c_shop_faqs').val(json_data.message.c_shop_faqs);
				 //color options > checks color values from db > disabled when none
				 if(json_data.message.header_upper_bg == ''){
					$('input[name="f_header_upper_bg_no_color"]').prop('checked','true');
					$('#f_header_upper_bg').attr('readonly','true');
				 }
				 else{
					$('#f_header_upper_bg').val(json_data.message.header_upper_bg);
				 }

				 if(json_data.message.header_upper_txtcolor == ''){
					$('input[name="f_header_upper_txtcolor_no_color"]').prop('checked','true');
					$('#f_header_upper_txtcolor').attr('readonly','true');
				 }
				 else{
					$('#f_header_upper_txtcolor').val(json_data.message.header_upper_txtcolor);
				 }

				 if(json_data.message.header_middle_bg == ''){
					$('input[name="f_header_middle_bg_no_color"]').prop('checked','true');
					$('#f_header_middle_bg').attr('readonly','true');
				 }
				 else{
					$('#f_header_middle_bg').val(json_data.message.header_middle_bg);
				 }

				 if(json_data.message.header_middle_txtcolor == ''){
					$('input[name="f_header_middle_txtcolor_no_color"]').prop('checked','true');
					$('#f_header_middle_txtcolor').attr('readonly','true');
				 }
				 else{
					$('#f_header_middle_txtcolor').val(json_data.message.header_middle_txtcolor);
				 }

				 if(json_data.message.header_middle_icons == ''){
					$('input[name="f_header_middle_icons_no_color"]').prop('checked','true');
					$('#f_header_middle_icons').attr('readonly','true');
				 }
				 else{
					$('#f_header_middle_icons').val(json_data.message.header_middle_icons);
				 }
				 
				 if(json_data.message.header_bottom_bg == ''){
					$('input[name="f_header_bottom_bg_no_color"]').prop('checked','true');
					$('#f_header_bottom_bg').attr('readonly','true');
				 }
				 else{
					$('#f_header_bottom_bg').val(json_data.message.header_bottom_bg);
				 }

				 if(json_data.message.header_bottom_textcolor == ''){
					$('input[name="f_header_bottom_textcolor_no_color"]').prop('checked','true');
					$('#f_header_bottom_textcolor').attr('readonly','true');
				 }
				 else{
					$('#f_header_bottom_textcolor').val(json_data.message.header_bottom_textcolor);
				 }
				 
				 if(json_data.message.footer_bg == ''){
					$('input[name="f_footer_bg_no_color"]').prop('checked','true');
					$('#f_footer_bg').attr('readonly','true');
				 }
				 else{
					$('#f_footer_bg').val(json_data.message.footer_bg);
				 }
				 
				 if(json_data.message.footer_textcolor == ''){
					$('input[name="f_footer_textcolor_no_color"]').prop('checked','true');
					$('#f_footer_textcolor').attr('readonly','true');
				 }
				 else{
					$('#f_footer_textcolor').val(json_data.message.footer_textcolor);
				 }
				 
				 if(json_data.message.footer_titlecolor == ''){
					$('input[name="f_footer_titlecolor_no_color"]').prop('checked','true');
					$('#f_footer_titlecolor').attr('readonly','true');
				 }
				 else{
					$('#f_footer_titlecolor').val(json_data.message.footer_titlecolor);
				 }
				 
				 if(json_data.message.primaryColor_accent == ''){
					$('input[name="f_primaryColor_accent_no_color"]').prop('checked','true');
					$('#f_primaryColor_accent').attr('readonly','true');
				 }
				 else{
					$('#f_primaryColor_accent').val(json_data.message.primaryColor_accent);
				 }
				 //end of color options
				 $('#f_fontChoice').val(json_data.message.fontChoice);
				 $($.parseHTML('<img id="product_preview">')).attr('src', s3bucket_url + 'assets/img/'+json_data.message.c_main_logo).appendTo('div.main_logo');
				 $('.main_logo').show('slow');
				 $($.parseHTML('<img id="product_preview">')).attr('src', s3bucket_url + 'assets/img/'+json_data.message.c_secondary_logo).appendTo('div.secondary_logo');
				 $('.secondary_logo').show('slow');
				 $($.parseHTML('<img id="product_preview">')).attr('src', s3bucket_url + 'assets/img/'+json_data.message.c_placeholder_img).appendTo('div.placeholder_img');
				 $('.placeholder_img').show('slow');
				 $($.parseHTML('<img id="product_preview">')).attr('src', s3bucket_url + 'assets/img/'+json_data.message.c_fb_image).appendTo('div.fb_image');
				 $('.fb_image').show('slow');
				 $($.parseHTML('<img id="product_preview">')).attr('src', s3bucket_url + 'assets/img/'+json_data.message.c_favicon).appendTo('div.favicon');
				 $('.favicon').show('slow');
				 $('input#f_allow_login').val(json_data.message.c_allow_login);
				 set_checkbox('#f_allow_login');
				 $('input#f_allow_shop_page').val(json_data.message.c_allow_shop_page);
				 set_checkbox('#f_allow_shop_page');
				 $('input#f_allow_facebook_login').val(json_data.message.c_allow_facebook_login);
				 set_checkbox('#f_allow_facebook_login');
				 $('input#f_allow_gmail_login').val(json_data.message.c_allow_gmail_login);
				 set_checkbox('#f_allow_gmail_login');
				 $('input#f_allow_connect_as_online_reseller').val(json_data.message.c_allow_connect_as_online_reseller);
				 set_checkbox('#f_allow_connect_as_online_reseller');
				 $('input#f_allow_physical_login').val(json_data.message.c_allow_physical_login);
				 set_checkbox('#f_allow_physical_login');
				 $('input#f_continue_as_guest_button').val(json_data.message.c_continue_as_guest_button);
				 set_checkbox('#f_continue_as_guest_button');
				 $('input#f_allow_registration').val(json_data.message.c_allow_registration);
				 set_checkbox('#f_allow_registration');
				 $('input#f_default_order').val(json_data.message.c_default_order);
				 set_checkbox('#f_default_order');	
				 $('input#f_allow_sms').val(json_data.message.c_allow_sms);
				 set_checkbox('#f_allow_sms');
				 $('input#f_allow_cod').val(json_data.message.c_allow_cod);
				 set_checkbox('#f_allow_cod');
				 $('input#c_allow_google_addr').val(json_data.message.c_allow_google_addr);
				 set_checkbox('#c_allow_google_addr');
				 $('input#c_allow_preorder').val(json_data.message.c_allow_preorder);
				 set_checkbox('#c_allow_preorder');
				 $('input#c_allow_voucher').val(json_data.message.c_allow_voucher);
				 set_checkbox('#c_allow_voucher');
				 $('input#c_allow_toktok_shipping').val(json_data.message.c_allow_toktok_shipping);
				 set_checkbox('#c_allow_toktok_shipping');

				 $('input#c_international').val(json_data.message.c_international);
				 set_checkbox('#c_international');
				 $('input#c_allow_pickup').val(json_data.message.c_allow_pickup);
				 set_checkbox('#c_allow_pickup');
				 $('input#c_with_comingsoon_cover_local').val(json_data.message.c_with_comingsoon_cover_local);
				 set_checkbox('#c_with_comingsoon_cover_local');
				 $('input#c_with_comingsoon_cover_test').val(json_data.message.c_with_comingsoon_cover_test);
				 set_checkbox('#c_with_comingsoon_cover_test');
				 $('input#c_with_comingsoon_cover_live').val(json_data.message.c_with_comingsoon_cover_live);
				 set_checkbox('#c_with_comingsoon_cover_live');
				 $('input#c_realtime_notif').val(json_data.message.c_realtime_notif);
				 set_checkbox('#c_realtime_notif');

				 $('input#c_allow_whats_new').val(json_data.message.c_allow_whats_new);
				 set_checkbox('#c_allow_whats_new');

				 $('input#c_allow_promotions').val(json_data.message.c_allow_promotions);
				 set_checkbox('#c_allow_promotions');

				 $('input#c_allow_following').val(json_data.message.c_allow_following);
				 set_checkbox('#c_allow_following');

				 $('input#c_allow_flash_sale').val(json_data.message.c_allow_flash_sale);
				 set_checkbox('#c_allow_flash_sale');

				 $('input#c_allow_mystery_coupon').val(json_data.message.c_allow_mystery_coupon);
				 set_checkbox('#c_allow_mystery_coupon');

				 $('input#c_allow_piso_deal').val(json_data.message.c_allow_piso_deal);
				 set_checkbox('#c_allow_piso_deal');

				 $('input#c_allow_categories_section').val(json_data.message.c_allow_categories_section);
				 set_checkbox('#c_allow_categories_section');

				 $('input#c_allow_promo_featured_items').val(json_data.message.c_allow_promo_featured_items);
				 set_checkbox('#c_allow_promo_featured_items');
				 
			 }else{
				$.LoadingOverlay("hide");
				// $.toast({
				// 	heading: 'Note',
				// 	text: json_data.message,
				// 	icon: 'info',
				// 	loader: false,
				// 	stack: false,
				// 	position: 'top-center',
				// 	bgColor: '#FFA500',
				// 	textColor: 'white'
				// });
				showCpToast("info", "Note!", json_data.message);
			 }
		 },
		 error: function(error){
			$.LoadingOverlay("hide");
			// $.toast({
			// 	heading: 'Note',
			// 	text: json_data.message,
			// 	icon: 'info',
			// 	loader: false,
			// 	stack: false,
			// 	position: 'top-center',
			// 	bgColor: '#FFA500',
			// 	textColor: 'white'
			// });
			showCpToast("info", "Note!", json_data.message);
		 }
		});
	}

	function set_checkbox(e_id){
		var value = $(e_id)	.val();
		if(value==1){
			$(e_id).next().find('input[type=checkbox]').prop('checked',true);
		}
	}

	$('#backBtn').click(function(){
		var token = $("body").data('token');
		window.location.assign(base_url+"Dev_settings_client_information/client_information/"+token);
	});

});

$('input[type=checkbox]').click(function(){
	if($(this).prop('checked') == true){
		$(this).parent().prev().val('1');
	}
	else{
		$(this).parent().prev().val('0');
	}
});

//no color option
$('.no-color input[type=checkbox]').on('change',function(){
	if($(this).prop('checked') == true){
		$(this).parent().next().attr('readonly','true');
	}
	else{
		$(this).parent().next().removeAttr('readonly');
	}
});



