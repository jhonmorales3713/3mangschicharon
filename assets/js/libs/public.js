$(document).ready(function(){

	base_url = $("body").data('base_url');
	shop_url = $("body").data('shop_url');
	ajax_token = $("body").data('token_value');
	ajax_token_name = $("body").data('token_name');

	showCover = function(message){

		$('#current-activity').html(message);
	    $('#transparent-cover').css({'display':'table'});

	}

	hideCover = function(){

		$('#current-activity').html('');
	    $('#transparent-cover').css({'display':'none'});
	    
	}

	sys_log = function(env,data){
		if(env=="development"){
			console.log(data);
		}
		// revise this function by passing only parameter, check if env key exists then do the following conditions
	}
	sys_toast = function(message,heading,theme)
	{
        //Msg.show(message, type, timeout);
        $.alert({
            title: heading,
            content: message,
            theme: theme,
            autoClose: 'cancel|1000',
            backgroundDismiss: true,
            buttons: {
                cancel: function () {
                }
            }
        });
        //$('.jconfirm-box-container').css('transform','translate(0px,-350px)');
        
	}


	tofixed = function(x){
		return numberWithCommas(parseFloat(x).toFixed(2));
	}
	numberWithCommas = function(x){
	  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}


	// toast options
	sys_toast_success = function(message)
	{
		sys_toast(message,'&#10004; Success','success');
	}

	sys_toast_error = function(message)
	{
		sys_toast(message,'&#x2715; Error','danger');
	}

	sys_toast_warning = function(message)
	{
		sys_toast(message,'&#9888; Warning','warning');
	}

	sys_toast_warning_info = function(message)
	{
		sys_toast(message,'&#8505; Note','info');
	}

	sys_toast_info = function(message)
	{
		sys_toast(message,'&#8505; Note','info');
	}
	//close the transparent cover once the page was successfully loaded

	hideCover();


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
		else 
		{
			element = "<label class='badge badge-danger'> Unpaid</label>";

			return element;
		}
	}

	update_token = function(value)
	{
		$('#template_body').data('token_value',value);
		ajax_token = $("body").data('token_value');
	}

	$('#refresh_trigger_btn').click(function()
	{
		refresh_filters();
	});

	refresh_filters = function()
	{
		var table = $('#table-grid');
		var filters = table.find('input, select');

		filters.map(function()
		{
			var $this = $(this);
			if ($this.is('input'))
			{
				$this.val('');
			}
			else if ($this.is('select'))
			{
				$this[0].selectedIndex = 0;
			}
		})
	}

	// Reset form
	reset_form = function (modal, hide_modal = false)
	{
		console.log(modal);
	    var form = modal.find('form')[0];
	    if(form)
	    {
	        form.reset();
	        if( typeof CKEDITOR !== "undefined" )
	        { // check if ckeditor exists in the page
	            for(var instanceName in CKEDITOR.instances)
	            { 
	                CKEDITOR.instances[instanceName].setData('');
	            }
	        }
	    }
	    
	    if(hide_modal == true)
	    {
	        modal.modal('hide');
	    }
	}

	 get_url_parameter = function(param_name) {
	    var re_param = new RegExp( '(?:[\?&]|&)' + param_name + '=([^&]+)', 'i' );
	    var match = window.location.search.match( re_param );

	    return ( match && match.length > 1 ) ? match[1] : null;
	}


	// popover-menu-details
	$('.task-member-icon img').on('click', function(e){
		e.stopPropagation();
		$('.popover-menu-details').removeAttr( 'style' );
		$(this).parent().find('.popover-menu-details').css('display', 'block');
	})
	$('.task-member-icon .icon-close').on('click', function(e){
		$('.popover-menu-details').removeAttr('style');
	})


	
}).on('click', '.kanban-link', function(){
	window.open(this.href);
});


$(document).click(function(){
	$(".popover-menu-details").hide();
});





// charts
var color_solid = {
    pink:   'rgb(255, 99, 132, 1)',
    red: 	'rgb(244, 67, 54, 1)',
	orange: 'rgb(255, 159, 64, 1)',
    yellow: 'rgb(255, 205, 86, 1)',
    lime:   'rgb(205, 220, 57, 1)',
    green: 	'rgb(0, 200, 81, 1)',
    teal: 	'rgb(0, 150, 136, 1)',
    cyan:   'rgb(0, 188, 212, 1)',
    blue:   'rgb(54, 162, 235, 1)',
    purple: 'rgb(153, 102, 255, 1)',
    indigo: 'rgb(63, 81, 181, 1)',
    stylish:'rgb(62, 69, 81, 1)',
    grey:   'rgb(75, 81, 93, 1)',
    blue_grey: 'rgb(96, 125, 139, 1)',
    light_grey:'rgb(158, 158, 158, 1)',
}
var color_0pt6 = {
    pink:   'rgb(255, 99, 132, 0.6)',
    red: 	'rgb(244, 67, 54, 0.6)',
	orange: 'rgb(255, 159, 64, 0.6)',
    yellow: 'rgb(255, 205, 86, 0.6)',
    lime:   'rgb(205, 220, 57, 0.6)',
    green: 	'rgb(0, 200, 81, 0.6)',
    teal: 	'rgb(0, 150, 136, 0.6)',
    cyan:   'rgb(0, 188, 212, 0.6)',
    blue:   'rgb(54, 162, 235, 0.6)',
    purple: 'rgb(153, 102, 255, 0.6)',
    indigo: 'rgb(63, 81, 181, 0.6)',
    stylish:'rgb(62, 69, 81, 0.6)',
    grey:   'rgb(75, 81, 93, 0.6)',
    blue_grey: 'rgb(96, 125, 139, 0.6)',
    light_grey:'rgb(158, 158, 158, 0.6)',
}
var chartOptions_pie = {
	responsive: true,
	maintainAspectRatio: false,
	legend: {
		display: false,
	},
	animation: {
		animateScale: true,
		animateRotate: true
	},
}
var chartOptions_line_bar = {
	responsive: true,
	maintainAspectRatio: false,
	legend: {
		position: 'bottom',
	},
	scales: {
		yAxes: [{
			id: 'y-axis-0',
			stacked: false,
			gridLines: {
				display: true,
				lineWidth: 1,
				color: "rgba(0,0,0,0.60)"
			},
			ticks: {
				beginAtZero:true,
				mirror:false,
				suggestedMin: 0,
				suggestedMax: 3,
			}
		}],
		xAxes: [{
			id: 'x-axis-0',
			stacked: false,
			gridLines: {
				display: false
			},
			ticks: {
				beginAtZero: true
			}
		}]
	},
}; 
var chartOptions_stacked = {
	responsive: true,
	maintainAspectRatio: false,
	legend: {
		position: 'bottom',
	},
	scales: {
		yAxes: [{
			id: 'y-axis-0-s',
			stacked: true,
			gridLines: {
				display: true,
				lineWidth: 1,
				color: "rgba(0,0,0,0.60)"
			},
			ticks: {
				beginAtZero:true,
				mirror:false,
				suggestedMin: 0,
				suggestedMax: 3,
			}
		}],
		xAxes: [{
			id: 'x-axis-0-s',
			stacked: true,
			gridLines: {
				display: false
			},
			ticks: {
				beginAtZero: true
			}
		}]
	},
}; 
