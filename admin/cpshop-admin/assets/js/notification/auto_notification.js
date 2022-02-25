$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("body").data("token");
	var shop_id        = $("body").data("shop_id");
	var branch_id      = $("body").data("branch_id");
    var pusher_app_key = $("body").data("pusher_app_key");
    var notif_count = 0; 

    
    $.ajax({
        type: "post",
        url: base_url + "notification/Notification/get_notification",
        // data: { notiflogs_id: notiflogs_id },
        success: function (data) {
            var res = data.result;
            if(data.success == 1){
                // console.log(data.notif_data);    
                (data.notif_count == 0) ? $('#notif_count').hide():$('#notif_count').show();
                $('#notif_count').text(data.notif_count)  
                // string = "<div id='realtime_notification_menu'></div>";
                // $("#notification_menu").append(string);
                $.each(data.notif_data, function(key, value) {
                    notif_link = (shop_id == 0) ? value.link : value.link_shop;
                    notif_link = (notif_link != null) ? notif_link.replace("token", token) : '' ;
                    title_bold = (value.has_read == 0) ? "strong" : "label";
                    date_bold  = (value.has_read == 0) ? "style='font-weight: bold;'" : "";
                    string =    "<li id='notif_li_"+(notif_count+1)+"'>";
                    string += "<a href='"+base_url+notif_link+"' class='dropdown-messages-box no-padding readNotif' data-sys_notification_id='"+value.sys_notification_id+"' data-shop_id='"+value.shop_id+"' data-branch_id='"+value.branch_id+"' style='background:#e2e8f0;'>";
                    // string += '<div class="dropdown-avatar">';
                    // string += '<img alt="image" class="img-circle" src="#">';
                    // string += '</div>';
                    string += '<div class="media-body">';
                    // string += '<small class="pull-right">46h ago</small>';
                    string += "<"+title_bold+">"+value.activity_details+"</"+title_bold+"><br>";
                    // string += '<strong>Mike Loreipsum</strong> started following <strong>Monica Smith</strong>. <br>';
                    string += "<small class='text-muted no-margin' "+date_bold+">"+value.date_created+"</small>";
                    string += '</div>';
                    string += '</a>';
                    string += '</li>';
                    string += "<li class='divider' id='notif_divider_"+(notif_count+1)+"'></li>";
                    $("#notification_menu").append(string);
                    notif_count++;
                });            
                string = '<li>';
                string += '<div class="text-center link-block">';
                string += "<a href='"+base_url+"notification/Notification/notifications/"+token+"' style='background:#e2e8f0;'><strong>Read All Notifications</strong></a>";
                string += '</div>';
                string += '</li>';
                $("#notification_menu").append(string);         
            }
            else{
                //sys_toast_warning('Error fetching');
                showCpToast("warning", "Warning!", 'Error fetching');
            }
        },
    });

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = true;

    var pusher = new Pusher(pusher_app_key, {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('dropdown-notif');
    channel.bind('dropdown-notification', function(data) {
        if(shop_id == 0 && data.dataLog.branchid == 0){
            $('#notif_li_5').remove();
            $('#notif_divider_5').remove();
            $('#notif_li_4').prop('id', 'notif_li_5');
            $('#notif_divider_4').prop('id', 'notif_divider_5');
            $('#notif_li_3').prop('id', 'notif_li_4');
            $('#notif_divider_3').prop('id', 'notif_divider_4');
            $('#notif_li_2').prop('id', 'notif_li_3');
            $('#notif_divider_2').prop('id', 'notif_divider_3');
            $('#notif_li_1').prop('id', 'notif_li_2');
            $('#notif_divider_1').prop('id', 'notif_divider_2');
            (data.dataLog.total_unread_admin == 0) ? $('#notif_count').hide():$('#notif_count').show();
            $('#notif_count').text(data.dataLog.total_unread_admin); 
            appendNotif(data);
            $("#table-grid-notif").DataTable().draw(false);
            $("#table-grid-order").DataTable().draw(false);
            $("#table-grid-product").DataTable().draw(false);
        }
        else if(data.dataLog.sys_shop == shop_id && data.dataLog.branchid == branch_id){
            $('#notif_li_5').remove();
            $('#notif_divider_5').remove();
            $('#notif_li_4').prop('id', 'notif_li_5');
            $('#notif_divider_4').prop('id', 'notif_divider_5');
            $('#notif_li_3').prop('id', 'notif_li_4');
            $('#notif_divider_3').prop('id', 'notif_divider_4');
            $('#notif_li_2').prop('id', 'notif_li_3');
            $('#notif_divider_2').prop('id', 'notif_divider_3');
            $('#notif_li_1').prop('id', 'notif_li_2');
            $('#notif_divider_1').prop('id', 'notif_divider_2');
            (data.dataLog.total_unread == 0) ? $('#notif_count').hide():$('#notif_count').show();
            $('#notif_count').text(data.dataLog.total_unread);  
            appendNotif(data);
            $("#table-grid-notif").DataTable().draw(false);
            $("#table-grid-order").DataTable().draw(false);
            $("#table-grid-product").DataTable().draw(false);
        }
        // console.log(data);
		// console.clear();
    });

    function appendNotif(data){
        if(shop_id == 0 && data.dataLog.branchid == 0){
            notif_link = data.dataLog.link;
        }
        else if(data.dataLog.sys_shop == shop_id && data.dataLog.branchid == branch_id){
            notif_link = data.dataLog.link_shop;
        }
        notif_link = notif_link.replace("token", token);
        string =    "<li id='notif_li_1'>";
        string += "<a href='"+base_url+notif_link+"' class='dropdown-messages-box no-padding readNotif' data-sys_notification_id='"+data.dataLog.sys_notification_id+"' data-shop_id='"+data.dataLog.sys_shop+"' data-branch_id='"+data.dataLog.branchid+"' style='background:#e2e8f0;'>";
        string += '<div class="media-body">';
        string += "<strong>"+data.activity_details+"</strong><br>";
        string += "<small class='text-muted no-margin' style='font-weight: bold;'>"+data.dataLog.datetime_text+"</small>";
        string += '</div>';
        string += '</a>';
        string += '</li>';
        string += '<li class="divider" id="notif_divider_1"></li>';
        $("#notification_menu").prepend(string);
    }

    $("#notification_menu").delegate(".readNotif", "click", function () {
		// e.preventDefault();
		sys_notification_id = $(this).data("sys_notification_id");
		shop_id             = $(this).data("shop_id");
		branch_id           = $(this).data("branch_id");
        $.ajax({
			type: "post",
			url: base_url + "notification/Notification/read_notification",
			data: {
                sys_notification_id: sys_notification_id, 
                shop_id: shop_id, 
                branch_id: branch_id 
            },
			success: function (data) {
			
			},
		});
	});
});

