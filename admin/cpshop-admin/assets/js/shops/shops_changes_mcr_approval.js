$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var access_view       = $("#access_view").val();
    var access_approved     = $("#access_approved").val();
    var access_declined     = $("#access_declined").val();
	var checkBoxChecker   = 0;
    let productArray      = [];
    let addProdArr        = [];

	// var dataTable         = $('#table-grid-productss').DataTable();
	var checkBoxChecker   = 0;
	load_shop_for_Approval();

	$('#ApproveAll').prop('disabled', true);
	$('#DeclineALL').prop('disabled', true);

	$('#ApproveAll_Verify').prop('disabled', true);
	$('#DeclineALL_Verify').prop('disabled', true);

	
	$( "table" ).delegate( ".checkbox_perprod", "click", function() {

		   var  x = 0;
			var td = $("#table-grid-productss").find("input[type=checkbox]:checked");
		     	td.each(function(){
					if($(this).is(':checked')){
						x++;
						$('#ApproveAll').prop('disabled', false);
						$('#DeclineALL').prop('disabled', false);
					}
				});
				if(x == 0){
					$('#ApproveAll').prop('disabled', true);
					$('#DeclineALL').prop('disabled', true);
				}else{
					$('#ApproveAll').prop('disabled', false);
					$('#DeclineALL').prop('disabled', false);
				}
   });

   $( "table" ).delegate( ".checkbox_perprod", "click", function() {

		var  x = 0;
		var td = $("#table-grid-productss").find("input[type=checkbox]:checked");
			td.each(function(){
				if($(this).is(':checked')){
					x++;
					$('#ApproveAll_Verify').prop('disabled', false);
					$('#DeclineALL_Verify').prop('disabled', false);
				}
			});
			if(x == 0){
				$('#ApproveAll_Verify').prop('disabled', true);
				$('#DeclineALL_Verify').prop('disabled', true);
			}else{
				$('#ApproveAll_Verify').prop('disabled', false);
				$('#DeclineALL_Verify').prop('disabled', false);
			}
	});

	

	$('#table-grid-productss').on( 'page.dt', function () {
		if($("#check_all_items").prop("checked") == true){
			$( "#check_all_items" ).prop( "checked", false );
			checkBoxChecker = 0;
	
		}
		else{
			checkBoxChecker = 3;
			
		}
	 });

	$("#check_all_items").click(function(){
	    //   alert('test');
		if(checkBoxChecker == 0){
			$( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
			checkBoxChecker = 1;
			$('#ApproveAll').prop('disabled', false);
			$('#DeclineALL').prop('disabled', false);	
		}
		else if(checkBoxChecker == 2){
			$( ".checkbox_perprod:checkbox:checked" ).trigger( "click" );
			checkBoxChecker = 0;
		}
		else if(checkBoxChecker == 3){
			$( ".checkbox_perprod:checkbox:unchecked" ).trigger( "click" );
			checkBoxChecker = 1;
			
		}
		else if(checkBoxChecker == 1){
			$( ".checkbox_perprod" ).trigger( "click" );
			checkBoxChecker = 0;
			$('#ApproveAll').prop('disabled', true);
			$('#DeclineALL').prop('disabled', true);
		}
	});


	$('#table-grid-productss').on('click', "input[name='checkbox_perprod']", function() {
		// console.log('checkBoxChecker');
        var value = $(this).val();
        if(this.checked){
			dataArr = {
					'shop_id'          : $(this).val()
				};
			addProdArr.push(dataArr);
        }
		else{
			var index = addProdArr.findIndex(p => p.shop_id == $(this).val());
			if (index !== -1) {
				addProdArr.splice(index, 1);
			}
			if($("#check_all_items").prop("checked") == true){
				checkBoxChecker = 2;
			
			}
			else{
				checkBoxChecker = 3;
			
			}
        }

		
    });





	$(document).on('click', '#ApproveAll', function(e) {

		if(addProdArr.length == 0){
			sys_toast_warning('Please select a product.');	
		}else{
			// alert('approve');
				bootbox.confirm({
					title: 'Approve',
					message: " Are you sure? Shop mcr will be successfully Approved",
					buttons: {
						confirm: {
							label: 'Proceed',
							className: 'btn-success'
						},
						cancel: {
							label: 'Cancel',
							className: 'btn-danger'
						}
					},
					callback: function (result) {
						if(result == true){
							$.LoadingOverlay("show");
					
							var url = base_url+"shops/Main_shops_approval/shop_mcr_approve_all";
							$.post(url,{ shop_id : addProdArr },function(rs){
					
					
								if(rs.success){
							    	$.LoadingOverlay("hide");
									$('#approveModal').modal('hide');
									// sys_toast_success(json_data.message);
									showCpToast("success", "Approved!", "Shop mcr has been approved.");
									setTimeout(function(){location.reload()}, 2000);
								}
								else{
									sys_toast_warning(json_data.message);
									$('#approveModal').modal('hide');
									// location.reload();
								}
					
							},'json');
						}
					}
				});
	

		}
			
    })


	$(document).on('click', '#ApproveAll_Verify', function(e) {

		if(addProdArr.length == 0){
			sys_toast_warning('Please select a product.');	
		}else{
			// alert('approve');
				bootbox.confirm({
					title: 'Verify',
					message: " Are you sure? Shop mcr will be successfully Verified",
					buttons: {
						confirm: {
							label: 'Proceed',
							className: 'btn-success'
						},
						cancel: {
							label: 'Cancel',
							className: 'btn-danger'
						}
					},
					callback: function (result) {
						if(result == true){
							$.LoadingOverlay("show");
					
							var url = base_url+"shops/Main_shops_approval/shop_mcr_verify_all";
							$.post(url,{ shop_id : addProdArr },function(rs){
					
					
								if(rs.success){
							    	$.LoadingOverlay("hide");
									$('#VerifyModal').modal('hide');
									// sys_toast_success(json_data.message);
									showCpToast("success", "Approved!", "Shop mcr has been verified.");
									setTimeout(function(){location.reload()}, 2000);
								}
								else{
									sys_toast_warning(json_data.message);
									$('#VerifyModal').modal('hide');
									// location.reload();
								}
					
							},'json');
						}
					}
				});
	

		}
			
    })






	
	$(document).on('click', '#DeclineALL', function(e) {

		if(addProdArr.length == 0){
			sys_toast_warning('Please select a product.');	
		}else{
			// alert('Decline');
			
				bootbox.confirm({
					title: 'Declined',
					message: "<b>Are you sure?</b><br><label class=''>Shop mcr will be declined.</label>",
					buttons: {
						confirm: {
							label: 'Proceed',
							className: 'btn-success'
						},
						cancel: {
							label: 'Cancel',
							className: 'btn-danger'
						}
					},
					callback: function (result) {
						if(result == true){

					          textarea = $("#editComment").val()
							//   alert(textarea);
								if(textarea != ''){
										$.LoadingOverlay("show");
										var url = base_url+"shops/Main_shops_approval/shops_mcr_decline_all";
										$.post(url,{ shop_id : addProdArr},function(rs){
								
											if(rs.success){
												$.LoadingOverlay("hide");
												$('#approveModal').modal('hide');
												// sys_toast_success(json_data.message);
												showCpToast("success", "Declined!", "Shop mcr has been declined.");
												setTimeout(function(){location.reload()}, 2000);
											}
											else{
												sys_toast_warning(json_data.message);
												$('#approveModal').modal('hide');
												// location.reload();
											}
							
									},'json');

								}else{
									$.LoadingOverlay("hide");
									sys_toast_warning('Please enter notes.');
								}
						}
					}
				});
	

		}
			
    })



	$(document).on('click', '#DeclineALL_Verify', function(e) {

		if(addProdArr.length == 0){
			sys_toast_warning('Please select a product.');	
		}else{
			// alert('Decline');
			
				bootbox.confirm({
					title: 'Declined',
					message: "<b>Are you sure?</b><br><label class=''>Shop mcr will be declined.</label>",
					buttons: {
						confirm: {
							label: 'Proceed',
							className: 'btn-success'
						},
						cancel: {
							label: 'Cancel',
							className: 'btn-danger'
						}
					},
					callback: function (result) {
						if(result == true){

					          textarea = $("#editComment").val()
							//   alert(textarea);
								if(textarea != ''){
										$.LoadingOverlay("show");
										var url = base_url+"shops/Main_shops_approval/shops_mcr_decline_all";
										$.post(url,{ shop_id : addProdArr},function(rs){
								
											if(rs.success){
												$.LoadingOverlay("hide");
												$('#approveModal').modal('hide');
												// sys_toast_success(json_data.message);
												showCpToast("success", "Declined!", "Shop mcr has been declined.");
												setTimeout(function(){location.reload()}, 2000);
											}
											else{
												sys_toast_warning(json_data.message);
												$('#approveModal').modal('hide');
												// location.reload();
											}
							
									},'json');

								}else{
									$.LoadingOverlay("hide");
									sys_toast_warning('Please enter notes.');
								}
						}
					}
				});
	

		}
			
    })








	$("#_record_status").change(function(){
		$("#btnSearch").click();
	});

	$("#search_hideshow_btn").click(function(e){
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if(!visibility){
			//visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		}else{
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
   		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function(e){
	   window.location.reload();
	})

	$(".enter_search").keypress(function(e) {
        if (e.keyCode === 13) {
			$("#btnSearch").click();
			return false;
        }
    });

	$('#btnSearch').click(function(e){
		e.preventDefault();
		load_shop_for_Approval();
	});
	// end - for search purposes




	$(document).on('click', '#ApproveButton', function(e) {

        e.preventDefault();
        const id = $(this).data('prod-id');
		$('#proceedBtn').attr('data-id', id);

    })

    $('#proceedBtn').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');


			$.ajax({
				type: 'post',
				url: base_url+"shops/Main_shops_approval/shop_mcr_approve",
				data:{
					'id': action_id,
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#approveModal').modal('hide');
						// sys_toast_success(json_data.message);
						showCpToast("success", "Approved!", "Shop MCR has been approved.");
						setTimeout(function(){location.reload()}, 2000);
					}
					else{
						sys_toast_warning(json_data.message);
						$('#approveModal').modal('hide');
						// location.reload();
					}

				},
				error: function(error){
					sys_toast_error('Something went wrong. Please try again.');
				}
			});


    });


	$(document).on('click', '#VerifyButton', function(e) {

        e.preventDefault();
        const id = $(this).data('prod-id');
		$('#proceedBtnVerify').attr('data-id', id);

    })

    $('#proceedBtnVerify').click(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');


			$.ajax({
				type: 'post',
				url: base_url+"shops/Main_shops_approval/shop_mcr_verify",
				data:{
					'id': action_id,
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#VerifyModal').modal('hide');
						// sys_toast_success(json_data.message);
						showCpToast("success", "Approved!", "Shop MCR has been approved.");
						setTimeout(function(){location.reload()}, 2000);
					}
					else{
						sys_toast_warning(json_data.message);
						$('#VerifyModal').modal('hide');
						// location.reload();
					}

				},
				error: function(error){
					sys_toast_error('Something went wrong. Please try again.');
				}
			});


    });



	$(document).on('click', '#DeclineButton', function(e) {

        e.preventDefault();
        const id = $(this).data('prod-id');
		$('#proceedDecBtn').attr('data-id', id);

    })

    $('#proceedDecBtn').click(function(e){

        e.preventDefault();
        $.LoadingOverlay("show");
		const action_id = $(this).attr('data-id');
		// const reason = $('#dec_reason').val();

		// alert(action_id);

        // if(reason != ''){
		$.ajax({
			type: 'post',
			url: base_url+"shops/Main_shops_approval/shops_mcr_decline",
			data:{
				'id': action_id,
				// 'reason':reason
			},
			success:function(data){
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if(json_data.success){
					$('#declineModal').modal('hide');
					// sys_toast_success(json_data.message);
					showCpToast("warning", "Declined!", "Shop MCR has been declined.");
					setTimeout(function(){location.reload()}, 2000);
				}
				else{
					sys_toast_warning(json_data.message);
					$('#declineModal').modal('hide');
					// location.reload();
				}

			},
			error: function(error){
				sys_toast_error('Something went wrong. Please try again.');
			}
		});
		// }else{
		// 	$.LoadingOverlay("hide");
		// 	sys_toast_warning('Please enter notes.');
		// }



    });

	

});




function load_shop_for_Approval(){
      	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
		var _record_status 	= $("select[name='_record_status']").val();
		var _shops 			= $("select[name='_shops']").val();


		$.ajax({
			type: "post",
			url:base_url+"shops/Main_shops_approval/shop_changes_approval_tables", // json datasource
			data: {'_record_status':_record_status,'_shops':_shops}, // serialized dont work, idkw
			success: function(data){	
				
		
				var rs = JSON.parse(data);
				var result =  rs.productArr;

				Display_waiting_for_Approval_Table(result);

                if(result.lenght  != 0){
					$('.btnExport').show(100);
				} else{
					$('#btnExport').hide(100);

				}


				$("#_search").val(JSON.stringify(this.data));
				$("input#_record_status").val(_record_status);
				$("input#_name").val(_name);
				$("input#_shops").val(_shops);
				$(".table-grid-error").remove();	     
			},
			error: function(){  // error handling
				$(".table-grid-error").html("");
				$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#table-grid_processing").css("display","none");
			}
		});

}


function Display_waiting_for_Approval_Table(result,rsCategory)
	{	
       //  console.log(result);
		$('#table-grid-productss').DataTable().clear().destroy();
		
		var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
		var token = $('#token').val();
		var verify             = $("#verify").val();
		var approve           = $("#approve").val();
		var decline           = $("#decline").val();
		var edit              = $("#edit").val();
		var md5                 = $("#md5").val();
		var s3bucket_url = $("body").data('s3bucket_url');
		

	
        count = 1;
		
		var cat = "";
        let showtable = $('#table-grid-productss tbody');
        showtable.html('');
        $.each(result, function(index, resultdata) 
        {  


			if(resultdata['ChangesStatus'] == 3){
				$(".BatchApprove").css("display", "block");
				$(".BatchVerify").css("display", "none");
			}else if(resultdata['ChangesStatus'] == 2){
				$(".BatchVerify").css("display", "block");
				$(".BatchApprove").css("display", "none");
			}else if(resultdata['ChangesStatus'] == 0){
				$(".BatchVerify").css("display", "none");
				$(".BatchApprove").css("display", "none");
			}else if(resultdata['ChangesStatus'] == 1){
				$(".BatchVerify").css("display", "none");
				$(".BatchApprove").css("display", "none");
			}else{
				$(".BatchVerify").css("display", "none");
				$(".BatchApprove").css("display", "none");
			}

			status = ""
			if(resultdata['ChangesStatus'] == 0){
                status = '<label class="alert alert-danger">Declined</label>'
			}else if(resultdata['ChangesStatus'] == 3){
				status = '<label class="alert alert-primary">Waiting for a approval</label>'
			}else if(resultdata['ChangesStatus'] == 2){
				status = '<label class="alert alert-success">Approved</label>'
			}else{
				status = '<label class="alert alert-success">Verified</label>'
			}

			row = "";
			row += "<tr class='product_tr_"+resultdata['shopid']+"'>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'><input type='checkbox' class='checkbox_perprod'  id='checkbox_perprod'  name='checkbox_perprod' value='"+ resultdata['shopid'] +"' /></td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['shopname']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['merchant_comrate']+"</td>" ;
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['startup']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['jc']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['mcjr']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['mc']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['mcsuper']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['mcmega']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+resultdata['others']+"</td>";
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+status+"</td>";
			$buttons = "";
			$buttons += '<a class="btn btn-primary" data-prod-id="' +resultdata['shopid']+ '" href="'+base_url+('shops/Main_shops_approval/view_shop/' + resultdata['shop_enc_id'] + '/' +token ) + '"> View</a> &nbsp;';
			if(approve == 1  && resultdata['ChangesStatus'] == 3){
				$buttons += '<a class="btn btn-success" data-prod-id="' +resultdata['shopid']+'" id="ApproveButton" data-toggle="modal" data-target="#approveModal" style="color: #fff;"> Approve</a>  &nbsp;';
			}
			if(verify == 1  && resultdata['ChangesStatus'] == 2){
				$buttons += '<a class="btn btn-success" data-prod-id="' +resultdata['shopid']+'" id="VerifyButton" data-toggle="modal" data-target="#VerifyModal" style="color: #fff;"> Verify</a>  &nbsp;';
			}
			if(decline == 1 ){
			    $buttons += '<a class="btn btn-danger"  data-prod-id="' +resultdata['shopid']+'"  id="DeclineButton" data-toggle="modal" data-target="#declineModal" style="color: #fff;"> Decline</a>  &nbsp;';
			}
			if(edit == 1){
				$buttons += '<a class="btn btn-success"  href="'+base_url+('Shops/update_comrate_approval/' +  resultdata['shop_enc_id'] + '/' +token ) + '"> Edit </a> &nbsp;';
			}
			row += "<td class='product_tr_"+resultdata['shopid']+"'>"+$buttons+"</td>";
			row += "</tr>";

			showtable.append(row);

		 
		});


		var dataTable = $('#table-grid-productss').DataTable({

		  });
	


	
		
	}	




