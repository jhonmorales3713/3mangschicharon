$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $('#token').val();
	var checkBoxChecker   = 0;
	var access_approved     = $("#access_approved").val();
    let productArray      = [];
    let addProdArr        = [];
	// var dataTable         = $('#table-grid-productss').DataTable();
	var checkBoxChecker   = 0;
	load_decline();



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
		}
	});


	$('#table-grid-productss').on('click', "input[name='checkbox_perprod']", function() {
		// console.log('checkBoxChecker');
        var value = $(this).val();
        if(this.checked){
			dataArr = {
					'product_id'          : $(this).val()
				};
			addProdArr.push(dataArr);
        }
		else{
			var index = addProdArr.findIndex(p => p.product_id == $(this).val());
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
			// console.log('lost_paradise');
				bootbox.confirm({
					title: 'Approval',
					message: "Do you want to approve selected products?",
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
		
							var url = base_url+"products/Products_approval/product_decline_all";
							$.post(url,{ product : addProdArr },function(rs){
					
								$.LoadingOverlay("hide");
					
								if(rs.success){
									$('#approveModal').modal('hide');
									// sys_toast_success(json_data.message);
									showCpToast("success", "Decline!", "Product changes has been approved.");
									setTimeout(function(){location.reload()}, 2000);
								}
								else{
									//sys_toast_warning(json_data.message);
									showCpToast("warning", "Warning!", json_data.message);
									$('#approveModal').modal('hide');
									// location.reload();
								}
					
							},'json');
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
		$(".search-input-text").val("");
		fillDatatable();
	})

	$(".enter_search").keypress(function(e) { 
        if (e.keyCode === 13) { 
			$("#btnSearch").click(); 
			return false;
        } 
    });

	$('#btnSearch').click(function(e){
		e.preventDefault();
		load_decline();
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
				url: base_url+"products/Products_approval/product_declined_to_approve_application",
				data:{
					'id': action_id,
				},
				success:function(data){
					$.LoadingOverlay("hide");
					var json_data = JSON.parse(data);
					if(json_data.success){
						$('#approveModal').modal('hide');
						// sys_toast_success(json_data.message);
						showCpToast("success", "Approve!", "Product changes has been approved.");
						setTimeout(function(){location.reload()}, 2000);
						window.location.assign(base_url+"Main_products/products_declined/"+token);
					}
					else{
						//sys_toast_warning(json_data.message);
						showCpToast("warning", "Warning!", json_data.message);
						$('#approveModal').modal('hide');
						// location.reload();
					}
				
				},
				error: function(error){
					//sys_toast_error('Something went wrong. Please try again.');
					showCpToast("error", "Error!", 'Something went wrong. Please try again.');
				}
			});
    
       
    });


	
	$(document).on('click', '#DeclineButton', function(e) {

        e.preventDefault();
        const id = $(this).data('prod-id');
		$('#proceedDecBtn').attr('data-id', id);
   
    })
	
 

});



function load_decline(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
		var _record_status 	= $("select[name='_record_status']").val();
		var _name 			= $("input[name='_name']").val();
		var _shops 			= $("select[name='_shops']").val();
		

		$.ajax({
			type: "post",
			url:base_url+"products/Products_approval/products_declined_table", // json datasource
			data: {'_record_status':_record_status, '_name':_name, '_shops':_shops}, // serialized dont work, idkw
			success: function(data){	
				
		
				var result = JSON.parse(data);

				Display_decline_Table(result);

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


function Display_decline_Table(result)
	{	

		$('#table-grid-productss').DataTable().clear().destroy();
		
		var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
		var token = $('#token').val();
		var access_approved     = $("#access_approved").val();
        count = 1;
		
        let showtable = $('#table-grid-productss tbody');
        showtable.html('');
        $.each(result, function(index, resultdata) 
        {  

		   //product comrate
		   Merchant_Comrate = resultdata["disc_rate"] * 100;
		   start_up = resultdata["startup"] * 100;
		   jc = resultdata["jc"] * 100;
		   mcjr = resultdata["mcjr"] * 100;
		   mc = resultdata["mc"] * 100;
		   mcsuper = resultdata["mcsuper"] * 100;
		   mcmega = resultdata["mcmega"] * 100;
		   others = resultdata["others"] * 100;

		   /// shop comrate
		   Shop_Merchant_Comrate = resultdata["shop_disc_rate"] * 100;
		   Shop_start_up = resultdata["shop_startup"] * 100;
		   Shop_jc = resultdata["shop_jc"] * 100;
		   Shop_mcjr = resultdata["shop_mcjr"] * 100;
		   Shop_mc = resultdata["shop_mc"] * 100;
		   Shop_mcsuper = resultdata["shop_mcsuper"] * 100;
		   Shop_mcmega = resultdata["shop_mcmega"] * 100;
		   Shop_others = resultdata["shop_others"] * 100;
		   
			

			if(resultdata['parentIDproduct'] != null && resultdata['parentIDproduct']  != 0){
				row = "";

				row += "<tr class='product_tr_"+resultdata['Id']+"'>";
				// row += "<td class='product_tr_"+resultdata['Id']+"'><input type='checkbox' class='checkbox_perprod' name='checkbox_perprod' value='"+ resultdata['Id'] +"' /></td>";
				row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['parent_product_name']+" - "+resultdata['itemname']+"</td>" ;
				row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['shopname']+"</td>";
				row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['reason']+"</td>";

				// if(Merchant_Comrate == 0 &&  start_up == 0 &&  jc == 0 &&  mcjr == 0 &&  mc == 0 &&  mcsuper == 0 &&  mcmega == 0 &&   others == 0){

				if(parseInt(Merchant_Comrate) == parseInt('0') &&  parseInt(start_up) == parseInt('0') &&  parseInt(jc) == parseInt('0') &&  parseInt(mcjr) == parseInt('0') &&  parseInt(mc) == parseInt('0') &&  parseInt(mcsuper) == parseInt('0') &&  parseInt(mcmega) == parseInt('0') &&   parseInt(others) == parseInt('0')){


					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_Merchant_Comrate.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_start_up.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_jc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mcjr.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mcsuper.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mcmega.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_others.toFixed(2)+"</td>";

				}else{

					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Merchant_Comrate.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+start_up.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+jc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mcjr.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mcsuper.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mcmega.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+others.toFixed(2)+"</td>";
			   }

			   row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['approval_price']+"</td>";

				$buttons = "";
				    $buttons += '<a class="btn btn-primary" data-prod-id="' +resultdata['Id']+ '" href="'+base_url+('Products_approval/view_products_declined/' + token + '/' + resultdata['Id']) + '"> View</a> &nbsp;';
				if(access_approved == 1){
					
					$buttons += '<a class="btn btn-success"  href="'+base_url+('Main_products/update_variants/' + token + '/' + resultdata['Id'] + '/' + resultdata['parent_product_id']) + '"> Update</a> &nbsp;';
				}
				row += "<td class='product_tr_"+resultdata['Id']+"'>"+$buttons+"</td>";
				row += "</tr>";
				showtable.append(row);
		  }else{

			row = "";

			row += "<tr class='product_tr_"+resultdata['Id']+"'>";
			// row += "<td class='product_tr_"+resultdata['Id']+"'><input type='checkbox' class='checkbox_perprod' name='checkbox_perprod' value='"+ resultdata['Id'] +"' /></td>";
			row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['itemname']+"</td>" ;
			row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['shopname']+"</td>";
			row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['reason']+"</td>";
			
				// if(Merchant_Comrate == 0 &&  start_up == 0 &&  jc == 0 &&  mcjr == 0 &&  mc == 0 &&  mcsuper == 0 &&  mcmega == 0 &&   others == 0){

				if(parseInt(Merchant_Comrate) == parseInt('0') &&  parseInt(start_up) == parseInt('0') &&  parseInt(jc) == parseInt('0') &&  parseInt(mcjr) == parseInt('0') &&  parseInt(mc) == parseInt('0') &&  parseInt(mcsuper) == parseInt('0') &&  parseInt(mcmega) == parseInt('0') &&   parseInt(others) == parseInt('0')){


					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_Merchant_Comrate.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_start_up.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_jc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mcjr.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mcsuper.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_mcmega.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Shop_others.toFixed(2)+"</td>";

				}else{

					row += "<td class='product_tr_"+resultdata['Id']+"'>"+Merchant_Comrate.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+start_up.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+jc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mcjr.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mc.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mcsuper.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+mcmega.toFixed(2)+"</td>";
					row += "<td class='product_tr_"+resultdata['Id']+"'>"+others.toFixed(2)+"</td>";
			   }
			   row += "<td class='product_tr_"+resultdata['Id']+"'>"+resultdata['approval_price']+"</td>";

			$buttons = "";
			$buttons += '<a class="btn btn-primary" data-prod-id="' +resultdata['Id']+ '" href="'+base_url+('Products_approval/view_products_declined/' + token + '/' + resultdata['Id']) + '"> View</a> &nbsp;';
			if(access_approved == 1){
	     		// $buttons += '<a class="btn btn-success" data-prod-id="' +resultdata['Id']+'" id="ApproveButton" data-toggle="modal" data-target="#approveModal" style="color: #fff;"> Approve</a>  &nbsp;';
				 $buttons += '<a class="btn btn-success"  href="'+base_url+('Main_products/update_products/' + token + '/' + resultdata['Id']) + '"> Update</a> &nbsp;';
            }
			row += "<td class='product_tr_"+resultdata['Id']+"'>"+$buttons+"</td>";
			row += "</tr>";
			showtable.append(row);
		  }


		});

		var dataTable = $('#table-grid-productss').DataTable({
			responsive: true
		  });
	

	}	





