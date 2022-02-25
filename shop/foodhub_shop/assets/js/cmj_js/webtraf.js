$(document).ready(function(){

	var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
	
	setInterval(function()
	{ 
	 	
	 	$.ajax({
				type:'post',
				url: base_url+'cmj/Webtraff/webtraff',
				data: {get_online_visitor:"online_visitor"},
				success:function(data){													
				}
			});


	}, 30000)

}); 