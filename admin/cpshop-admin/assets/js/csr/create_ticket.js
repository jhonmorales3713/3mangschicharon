$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');
	check_selected_tickettype();
	$('#btn-createticket').click(function(e){
        e.preventDefault();
        //This is the important part dont remove
     //    for (instance in CKEDITOR.instances) {
     //    CKEDITOR.instances['commentbox'].updateElement();
    	// }
    	/////////////////////////////////////////////////
        var serial = $("#entry-form").serialize();
        // var commentbox = CKEDITOR.instances.commentbox.getData();
        if(checkInputs("#entry-form") == 0){
            $.ajax({
                type:'post',
                url: base_url+'Csr/save_ticket',
                data: serial,
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $(".btn-save").prop('disabled', true); 
                    $(".btn-save").text("Please wait...");
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    $(".btn-save").prop('disabled', false); 
                    $(".btn-save").text("Save");
                    if (data.success == 1) {
                        $("#ticketrefno").text(data.ticketrefno);
                        $("#ticketrefno").attr("data-value", data.recordid);
                        $.LoadingOverlay("show");
                        if($("#member_type").val() == 4){
		  					window.open(base_url+"Csr/ticket_log/"+ data.recordid + "/" + token);
                        }else{
                        	//messageBox('Ticket Submitted', 'Success', 'success');
                        	showCpToast("success", "Success!", 'Ticket Submitted');
        					setTimeout(function(){location.reload()}, 2000);	
                        }
	  					$.LoadingOverlay("hide");
                    }else{
                        //messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
        				//setTimeout(2000);
                    }
                }
            });
        }
    });

	function search_customer_table(){
		var dataTable = $('#table-customer').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			responsive: true,
			"columnDefs": [
				{ targets: 1, orderable: true },
				{ targets: 2, orderable: true },
				{ targets: 3, orderable: true },
				{ targets: 4, orderable: true , "sClass":"text-center"},
				{ targets: 5, orderable: true},
				{ targets: 6, orderable: false }
			],
			"ajax":{
				type: "post",
				url:base_url+"Csr/checkcustomer", // json datasource
				data:{'search_val':$("#entry-searchval").val()}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	function search_account_table(){
		var dataTable = $('#table-account').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			responsive: true,
			"columnDefs": [
				{ targets: 1, orderable: true },
				{ targets: 2, orderable: true },
				{ targets: 3, orderable: true },
				{ targets: 4, orderable: true, "sClass":"text-center"},
				{ targets: 5, orderable: true},
				{ targets: 6, orderable: false },
			],
			"ajax":{
				type: "post",
				url:base_url+"Csr/checkaccount", // json datasource
				data:{'search_val':$("#entry-searchval").val()}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function() {
					$.LoadingOverlay("hide"); 
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	$("#btn-checkcustomer").click(function(){
		search_customer_table();
	});

	$("#btn-createnewticket").click(function(){
		location.reload();
	});

	$("#btn-vieworderdetails").click(function(){
		$.LoadingOverlay("show");
		window.location.href = base_url+"Csr/vieworder/"+token+"/0-"+$("#order_refno").val();
		//window.open();
		$.LoadingOverlay("hide");
	});

	$('#table-customer').delegate(".btn-selectcustomer", "click", function(e){
		e.preventDefault();
		let id = $(this).data('value');
	    $.LoadingOverlay("show");
		window.location.href=base_url+"Csr/view_customer/"+ token + "/" + $("#entry-maincategory").val() + "/" + id;
		$.LoadingOverlay("hide");
	});

	$('#table-account').delegate(".btn-selectaccount", "click", function(e){
		e.preventDefault();
		let id = $(this).data('value');	    
		$.LoadingOverlay("show");
		window.location.href=base_url+"Csr/view_account/"+ token + "/" + $("#entry-maincategory").val() + "/" + id;
		$.LoadingOverlay("hide");
	});


	$("#ticketrefno").click(function(){
		let id = $(this).data('value');
		$.LoadingOverlay("show");
	  	window.location.href=base_url+"Csr/ticket_log/"+ id + "/" + token;
	  	$.LoadingOverlay("hide");
	});

	$("#btn-search").click(function(e){
		e.preventDefault();
        if(checkInputs("#search-form") == 0){
        	if($("#entry-maincategory").val() == 1){//ORDERS CONCERN
				$.ajax({
	                type:'post',
	                url: base_url+'Csr/validate_order',
	                data:{'entry-orderrefno':$("#entry-searchval").val()},
	                beforeSend:function(data){
	                    $.LoadingOverlay("show");
	                    $(".btn-save").prop('disabled', true); 
	                    $(".btn-save").text("Please wait...");
	                },
	                success:function(data){
	                    $.LoadingOverlay("hide");
	                    $(".btn-save").prop('disabled', false); 
	                    $(".btn-save").text("Save");
	                    if (data.success == 1) {
	                        $.LoadingOverlay("show");
		  					window.location.href=base_url+"Csr/view_orders/"+ token + "/" + $("#entry-maincategory").val() + "/" + $("#entry-searchval").val();
		  					$.LoadingOverlay("hide");
	                    }else{
	                        //messageBox(data.message, 'Warning', 'warning');
	                        showCpToast("warning", "Warning!", data.message);
	                    }
	                }
	            });
        	}else if($("#entry-maincategory").val() == 2){//CUSTOMER CONCERN
        		search_customer_table();
        	}else if($("#entry-maincategory").val() == 3){//SHOP CONCERN
        		$.ajax({
	                type:'post',
	                url: base_url+'Csr/validate_shop',
	                data:{'entry-email':$("#entry-searchval").val()},
	                beforeSend:function(data){
	                    $.LoadingOverlay("show");
	                    $(".btn-save").prop('disabled', true); 
	                    $(".btn-save").text("Please wait...");
	                },
	                success:function(data){
	                    $.LoadingOverlay("hide");
	                    $(".btn-save").prop('disabled', false); 
	                    $(".btn-save").text("Save");
	                    if (data.success == 1) {
	                        $.LoadingOverlay("show");
							window.location.href=base_url+"Csr/view_shop/"+ token + "/" + $("#entry-maincategory").val() + "/" + data.shop_id;
							$.LoadingOverlay("hide");
	                    }else{
	                        //messageBox(data.message, 'Warning', 'warning');
	                        showCpToast("warning", "Warning!", data.message);
	                    }
	                }
	            });
        	}else if($("#entry-maincategory").val() == 4){//BRANCH CONCERN
        		$.ajax({
	                type:'post',
	                url: base_url+'Csr/validate_branch',
	                data:{'entry-email':$("#entry-searchval").val()},
	                beforeSend:function(data){
	                    $.LoadingOverlay("show");
	                    $(".btn-save").prop('disabled', true); 
	                    $(".btn-save").text("Please wait...");
	                },
	                success:function(data){
	                    $.LoadingOverlay("hide");
	                    $(".btn-save").prop('disabled', false); 
	                    $(".btn-save").text("Save");
	                    if (data.success == 1) {
	                        $.LoadingOverlay("show");
							window.location.href=base_url+"Csr/view_branch/"+ token + "/" + $("#entry-maincategory").val() + "/" + data.branch_id;
							$.LoadingOverlay("hide");
	                    }else{
	                        //messageBox(data.message, 'Warning', 'warning');
	                        showCpToast("warning", "Warning!", data.message);
	                    }
	                }
	            });
        	}else if($("#entry-maincategory").val() == 5){//ACCOUNT CONCERN
        		search_account_table();
        	}else{
        		$.LoadingOverlay("show");
				window.location.href=base_url+"Csr/ticketing/"+ token;
				$.LoadingOverlay("hide");
        		//do nothing
        	}
        }
	});

	$("#entry-maincategory").change(function(){
    	if($("#member_type").val() != 4){
    		$("#entry-tickettype").val($("#entry-maincategory").val());
    	}else{
    		if($("#entry-maincategory").val() == 1){//ORDERS CONCERN
				$('.searchfilter').attr('hidden', true);
				$('#default-search').attr('hidden', false);
	    	}else if($("#entry-maincategory").val() == 2){//CUSTOMER CONCERN
	    		$.LoadingOverlay("show");
				window.location.href=base_url+"Csr/customer_table/"+ token + "/" + $("#entry-maincategory").val();
				$.LoadingOverlay("hide");
	    	}else if($("#entry-maincategory").val() == 3){//SHOP CONCERN
	    		$('.searchfilter').attr('hidden', true);
				$('#default-search').attr('hidden', false);
	    	}else if($("#entry-maincategory").val() == 4){//BRANCH CONCERN
	    		$('.searchfilter').attr('hidden', true);
				$('#default-search').attr('hidden', false);
	    	}else if($("#entry-maincategory").val() == 5){//ACCOUNT CONCERN
	    		$.LoadingOverlay("show");
				window.location.href=base_url+"Csr/account_table/"+ token + "/" + $("#entry-maincategory").val();
				$.LoadingOverlay("hide");
	    	}else{
	    		//do nothing
	    	}
    	}
	});

	function check_selected_tickettype(){
		var selected_val = $("#selected_ticket_type").val();
		$('#entry-maincategory').val(selected_val);
    	$('#entry-maincategory').select2().trigger('change');
    	if($("#entry-maincategory").val() == 2){
    		search_customer_table();
    	}
    	if($("#entry-maincategory").val() == 5){
    		search_account_table();
    	}
	}
})