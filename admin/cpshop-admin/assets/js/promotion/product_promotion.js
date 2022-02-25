$(function(){
	var base_url          = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var shop_url          = $("body").data('shop_url');
    var s3bucket_url      = $("body").data('s3bucket_url');
    var token             = $("body").data('token');
    var ini               = $("body").data('ini');
    var shop_id           = $("body").data('shop_id');
    var branchid          = $("#branchid").val();
    var access_view       = $("#access_view").val();
    var access_create     = $("#access_create").val();
    var access_update     = $("#access_update").val();
    var access_delete     = $("#access_delete").val();
    var access_disable    = $("#access_disable").val();
    let productArray      = [];
    let addProdArr        = [];
	let deletedProductArr = [];
	var dataTable         = $('#table-grid-productpromo').DataTable();
	var checkBoxChecker   = 0;
	var showEntries       = 10;



	// if(shop_id != 0){
		// $('#promoProductDiv').show("slide", {direction: "right"}, 500);
	// }
	// else{
		// $('#promoProductDiv').hide("slide", {direction: "right"}, 500);
	// }

	$('#btnNext').click(function(){
		$.LoadingOverlay("show");
		$('#selectShopDiv').hide();
		$.LoadingOverlay("hide");
		$('#promoProductDiv').show("slide", {direction: "right"}, 500);
	});

	$('#addProductBtn').click(function(){
		$("#checkbox_all").prop("checked", false);
		populateProduct();
		$('#addProductModal').modal({
			backdrop: 'static',
			keyboard: false
		})
		// $('#addProductModal').modal();
	});

	$('#btnSearch').click(function(e){
		e.preventDefault();
		populateProduct();
		$( "#checkbox_all:checkbox:checked" ).trigger( "click" );
		
	});

	$('#table-grid-productpromo').on( 'page.dt', function () {
		if($("#checkbox_all").prop("checked") == true){
			$( "#checkbox_all" ).prop( "checked", false );
			checkBoxChecker = 0;
		}
		else{
			checkBoxChecker = 3;
		}
	 });

	$("#checkbox_all").click(function(){
		// $('.checkbox_perprod').not(this).prop('checked', this.checked);
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

	$(document).delegate("select[name='table-grid-productpromo_length']",'change',function(e){
		showEntries = $(this).val();
		$( "#checkbox_all:checkbox:checked" ).trigger( "click" );
		$( ".checkbox_perprod:checkbox:checked" ).trigger( "click" );
		populateProduct();
	});

	$('#table-grid-productpromo').on('click', "input[name='checkbox_perprod[]']", function() {
        var value = $(this).val();
        if(this.checked){
			dataArr = {
					'product_id'          : $(this).val(),
					'sys_shop'            : $(this).data('sys_shop'),
					'product_name'        : $(this).data('product_name'),
					'product_promo_type'  : 1,
					'product_promo_rate'  : 1,
					'product_orig_price'  : $(this).data('product_price'),
					'product_promo_price' : "1.00",
					'product_promo_stock' : 1,
					'product_curr_stock'  : $(this).data('product_stock'),
					'product_purch_limit' : 1,
					'product_status'      : 1
				};
			addProdArr.push(dataArr);
        }
		else{
			var index = addProdArr.findIndex(p => p.product_id == $(this).val());
			if (index !== -1) {
				addProdArr.splice(index, 1);
			}
			if($("#checkbox_all").prop("checked") == true){
				checkBoxChecker = 2;
			}
			else{
				checkBoxChecker = 3;
			}
        }
    });

	$("#btnConfirm").click(function(){
		if(productArray.length == 0){
			$('#tbody_prodpromo').empty();
		}
		counter = 0;
		$.each(addProdArr, function(key, value) {
			dataArr = {
				'product_id'          : value.product_id,
				'sys_shop'            : value.sys_shop,
				'product_name'        : value.product_name,
				'product_promo_type'  : value.product_promo_type,
				'product_promo_rate'  : value.product_promo_rate,
				'product_orig_price'  : value.product_orig_price,
				'product_promo_price' : value.product_promo_price,
				'product_promo_stock' : value.product_promo_stock,
				'product_curr_stock'  : value.product_curr_stock,
				'product_purch_limit' : value.product_purch_limit,
				'product_status'      : value.product_status
			};
			alignProductPromotion(dataArr, counter);
			productArray.push(dataArr);
			counter++;
		});
		if(addProdArr.length > 0){
			//sys_toast_success('Product has been successfully added to promotion table.');
			showCpToast("success", "Success!", 'Product has been successfully added to promotion table.');	
			$('#addProductModal').modal('hide');
			addProdArr = [];
		}
		else{
			//sys_toast_warning('You did not select any product.');
			showCpToast("warning", "Warning!", 'You did not select any product.');	
		}
		
	});

	$('#saveBtn').click(function(){
		$('#savePromoModal').modal();
	});

	$('#discardBtn').click(function(){
		$('#discardModal').modal();
	});

	$('#discardConfirm').click(function(){
		$.LoadingOverlay("show");
		location.reload();
	});

	$("#checkbox_all_prod").click(function(){
		$('.checkbox_prod').not(this).prop('checked', this.checked);
		batchUpdateSettings();
	});
	
	$(document).delegate('.checkbox_prod','click',function(e){
		batchUpdateSettings();
	});

	$('#batch_promo_price').on('input', function() {
		batchUpdateSettings();
	});

	$('#batch_purch_limit').on('input', function() {
		batchUpdateSettings();
	});

	$('#batch_promo_type').on('change', function() {
		batchUpdateSettings();
	});

	$('#batch_promo_rate').on('change', function() {
		batchUpdateSettings();
	});

	$('#batch_promo_stock').on('change', function() {
		value = $(this).val();
		if(value == 0){
			$('.batchPromoQtyDiv').hide(100);
			$('#batch_promo_stock_qty').val('');
		}
		else{
			$('.batchPromoQtyDiv').show(100);
		}
		batchUpdateSettings();
	});

	$('#batch_promo_stock_qty').on('input', function() {
		batchUpdateSettings();
	});

	$('#batch_purch_limit_select').on('change', function() {
		value = $(this).val();
		if(value == 0){
			$('.batchPurchLimitDiv').hide(100);
			$('#batch_purch_limit').val('');
		}
		else{
			$('.batchPurchLimitDiv').show(100);
		}
		batchUpdateSettings();
	});

	$(document).delegate('#removeProdPromo','click',function(e){
        var index = $(this).data('value');
        var key = $(this).data('key');
        $('#deleteProdPromoId').val(index);
        $('#deleteProdPromoKey').val(key);
        $('#deleteProdPromoModal').modal();
		console.log(productArray);
    });

	$(document).delegate('#deleteProdPromoConfirm','click',function(e){
        $.LoadingOverlay("show");
        var index = $('#deleteProdPromoId').val();
        var key = $('#deleteProdPromoKey').val();
        $('.product_tr_'+index).remove();
        $('#deleteProdPromoModal').modal();
        deletedProductArr.push(index);
		productArray.splice(key, 1);
        $.LoadingOverlay("hide");
		//sys_toast_success('Product successfully removed.');
		showCpToast("success", "Success!", 'Product successfully removed.');	
    });

	$(document).delegate('#enableProd','click',function(e){
        $.LoadingOverlay("show");
        var index = $(this).data('value');
        $('.disableProd'+index).show();
        $('.enableProd'+index).hide();
        $('.prod_status_label_'+index).text('Active');
        $('.prod_status_'+index).val(1);
        $.LoadingOverlay("hide");
		//sys_toast_success('Product successfully enabled.');
		showCpToast("success", "Success!", 'Product successfully enabled.');	
    });

    $(document).delegate('#disableProd','click',function(e){
        $.LoadingOverlay("show");
        var index = $(this).data('value');
        $('.enableProd'+index).show();
        $('.disableProd'+index).hide();
        $('.prod_status_label_'+index).text('Inactive');
        $('.prod_status_'+index).val(2);
        $.LoadingOverlay("hide");
		//sys_toast_success('Product successfully disabled.');
		showCpToast("success", "Success!", 'Product successfully disabled.');
    });

	$(document).delegate('#select_promo_stock','change',function(e){
		var index    = $(this).data('value');
		var value    = $(this).val();
		var curr_val = $('.product_promo_stock'+index).val();
		if(value == 1){
			$('.product_promo_stock'+index).show(100);
			$('.product_promo_stock'+index).val($('.prev_product_promo_stock'+index).val());
		}
		else{
			$('.product_promo_stock'+index).hide(100);
			$('.prev_product_promo_stock'+index).val(curr_val);
			$('.product_promo_stock'+index).val('');
		}
		console.log(value);
	});

	$(document).delegate('#select_purch_limit','change',function(e){
		var index = $(this).data('value');
		var value = $(this).val();
		var curr_val = $('.product_purch_limit'+index).val();
		if(value == 1){
			$('.product_purch_limit'+index).show(100);
			$('.product_purch_limit'+index).val($('.prev_product_purch_limit'+index).val());
		}
		else{
			$('.product_purch_limit'+index).hide(100);
			$('.prev_product_purch_limit'+index).val(curr_val);
			$('.product_purch_limit'+index).val('');
		}
	});

	$(document).delegate('.product_purch_limit_type','keypress keyup',function(e){
		product_id = $(this).data('value');
		value      = $(this).val();

		$('.prev_product_purch_limit'+product_id).val(value);
		
	});

	$(document).delegate('.product_promo_stock_type','keypress keyup focusout',function(e){
		product_id    = $(this).data('value');
		current_stock = $(this).data('current_stock');
		value         = $(this).val();

		if(parseInt(value) <= parseInt(current_stock)){
           console.log('in');
		}
		else{
			$(this).val('');  
			console.log('out');
		}

		console.log(value);
		console.log(current_stock);

		$('.prev_product_promo_stock'+product_id).val(value);
		
	});

	$('#savePromoConfirm').click(function(e){
		e.preventDefault();
        $.LoadingOverlay("show");

		var form      = $('#form_promoprod');
        var form_data = new FormData(form[0]);
        var save      = 1;
		date_to    = $('#start_date').val();
		date_from  = $('#end_date').val();
		start_time = $('#start_time').val();
		end_time   = $('#end_time').val();
		start_time = start_time.replace(":", "");
		end_time   = end_time.replace(":", "");
		set_time   = $('input[name="checkbox_time"]:checked').val();
        form_data.append('deletedProductArr', deletedProductArr);

		if(date_to == date_from && set_time == "on"){
			if(parseFloat(end_time) < parseFloat(start_time)){
				save = 2;
			}
		}

		if(set_time == "on"){
			if(start_time == "" || end_time == ""){
				save = 3;
			}
		}

        if(save == 2){
			//sys_toast_warning('Unable to select time that is not within the selected date.');
			showCpToast("warning", "Warning!", 'Unable to select time that is not within the selected date.');
			$.LoadingOverlay("hide");
		}
		else if(save == 2){
			//sys_toast_warning('Please input start and end time.');
			showCpToast("warning", "Warning!", 'Please input start and end time.');
			$.LoadingOverlay("hide");
		}
		else if(save == 1){
            $.ajax({
                type: form[0].method,
                url: base_url+'promotion/Main_promotion/update_promotion',
                data: form_data,
                contentType: false,   
                cache: false,      
                processData:false,
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    
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
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                }
            });
        }
        else{
            $.LoadingOverlay("hide");
            //sys_toast_warning('Error');
            showCpToast("error", "Error!", 'Something went wrong. Please try again.');
        }

    });

	$(document).delegate('#checkbox_time','click',function(e){
		setTime()
	});

	function populateProduct2(){
		var select_shop 	= $("#select_shop").val();
		var _name        	= $("input[name='_name']").val();

		var dataTable = $('#table-grid-productpromo').DataTable({
			"bProcessing": true,
			"destroy" : true,
			"bServerSide": true,
			"searching": false,
			"sPaginationType": "full_numbers",
			responsive: true,
			"language": {                
				"infoFiltered": ""
			},
			"columnDefs": [
				{ targets: [0, 1, 2, 3], orderable: false, "sClass":"text-center"},
				// { responsivePriority: 1, targets: 3 },
			],
			"ajax":{
				type: "post",
				url:base_url+"promotion/Main_promotion/product_table", // json datasource
				data: {
						'select_shop':select_shop,
						'_name':_name
				},
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}

	function alignProductPromotion(prodArr, key){
		// $.each(prodArr, function(key, value) {
			console.log(key);
			displayProductPromotion(prodArr, key);
		// });
	}

	function displayProductPromotion(value, key){


		if(value.product_id != null && value.product_name != null && value.product_id && 'null' && value.product_name != 'null'){

			select_promo_type = "<select name='product_promo_type[]' class='form-control material_josh form-control-sm search-input-text enter_search product_promo_type"+value.product_id+"'>"
			if(value.product_promo_type == 1){
				select_promo_type += "<option value='1' selected>Piso Deals</option>";
			}
			else{
				select_promo_type += "<option value='1'>Piso Deals</option>";
			}
			select_promo_type += "</select>"

			select_promo_rate = "<select name='product_promo_rate[]' class='form-control material_josh form-control-sm search-input-text enter_search product_promo_rate"+value.product_id+"'>"
			if(value.product_promo_rate == 1){
				select_promo_rate += "<option value='1' selected>Fixed</option>";
				// select_promo_rate += "<option value='2'>Percentage</option>";
			}
			else if(value.product_promo_rate == 2){
				select_promo_rate += "<option value='1'>Fixed</option>";
				// select_promo_rate += "<option value='2' selected>Percentage</option>";
			}
			
			select_promo_rate += "</select>"

			select_promo_stock = "<select id='select_promo_stock' class='form-control mb-2 select_promo_stock"+value.product_id+"' data-value='"+value.product_id+"'>";
			if(value.product_promo_stock == null){
				select_promo_stock += "<option value='0' selected>No Limit</option>";
				select_promo_stock += "<option value='1'>Limit</option>";
				promo_style        = "style='display:none'";
			}
			else{
				select_promo_stock += "<option value='0'>No Limit</option>";
				select_promo_stock += "<option value='1' selected>Limit</option>";
				promo_style        = "";
			}
			select_promo_stock += "</select>";

			select_purch_limit = "<select id='select_purch_limit' class='form-control mb-2 select_purch_limit"+value.product_id+"' data-value='"+value.product_id+"'>";
			if(value.product_purch_limit == null || value.product_purch_limit == 0){
				select_purch_limit += "<option value='0' selected>No Limit</option>";
				select_purch_limit += "<option value='1'>Limit</option>";
				purch_style        = "style='display:none'";
			}
			else{
				select_purch_limit += "<option value='0'>No Limit</option>";
				select_purch_limit += "<option value='1' selected>Limit</option>";
				purch_style        = "";
			}
			select_promo_stock += "</select>";

			buttons = "";

			if(value.product_status == 1){
				prod_status = "Active";
				if(access_disable == 1){
					buttons += "<button type='button' id='enableProd' class='btn btn-success mb-2 enableProd"+value.product_id+"' data-value='"+value.product_id+"'  style='display:none;'><i class='fa fa-check-circle'></i></button>";
					buttons += "<button type='button' id='disableProd' class='btn btn-warning mb-2 disableProd"+value.product_id+"' data-value='"+value.product_id+"'><i class='fa fa-times-circle'></i></button>";
				}
				if(access_delete == 1){
					buttons += "<button type='button' id='removeProdPromo' class='btn btn-danger mb-2' data-value='"+value.product_id+"' data-key='"+key+"'><i class='fa fa-trash'></i></button>";
				}
			}
			else if(value.product_status == 2){
				prod_status = "Inactive";
				if(access_disable == 1){
					buttons += "<button type='button' id='enableProd' class='btn btn-success mb-2 enableProd"+value.product_id+"' data-value='"+value.product_id+"'><i class='fa fa-check-circle'></i></button>";
					buttons += "<button type='button' id='disableProd' class='btn btn-warning mb-2 disableProd"+value.product_id+"' data-value='"+value.product_id+"' style='display:none;' ><i class='fa fa-times-circle'></i></button>";
				}
				if(access_delete == 1){
					buttons += "<button type='button' id='removeProdPromo' class='btn btn-danger mb-2' data-value='"+value.product_id+"' data-key='"+key+"'><i class='fa fa-trash'></i></button>";
				}
			}
			else{
				prod_status = "";
				buttons = "";
			}
			featured ="";
		  if(value.product_feauted == 0){
			  featured += "<label class='switch' data-toggle='modal' data-target='#setFeadutedModal' data-id='"+value.product_id+"'> <input type='checkbox'> <span class='slider round'></span></label>";

		  }else{
		     featured += "<label class='switch' data-toggle='modal' data-target='#unsetFeadutedModal' data-id='"+value.product_id+"'> <input checked  type='checkbox'> <span class='slider round'></span></label>";
		  }


			current_stock     = (value.product_curr_stock == null) ? 'None' : value.product_curr_stock;
			current_stock_num = (value.product_curr_stock == null) ? 0 : value.product_curr_stock;
			current_stock_num = String(current_stock_num).replace(",", "");
			const origprice = String(value.product_orig_price).replace(
				/^\d+/,
				number => [...number].map(
					(digit, index, digits) => (
						!index || (digits.length - index) % 3 ? '' : ','
					) + digit
				).join('')
			);

			str = "";
			str += "<tr class='product_tr_"+value.product_id+"'>";
			str += "<td class='product_tr_"+value.product_id+"' width='5%'><input type='checkbox' class='form-control checkbox_prod' name='checkbox_prod' value='"+value.product_id+"'></td>";
			str += "<td class='product_tr_"+value.product_id+"' width='12%'>"+value.product_name+"<input type='text' class='ddate' name='product_id[]' style='display:none' value='"+value.product_id+"'><input type='text' class='ddate' name='product_name[]' style='display:none' value='"+value.product_name+"'></td>";
			str += "<td class='product_tr_"+value.product_id+"' width='12%'>"+select_promo_type+"</td>";
			str += "<td class='product_tr_"+value.product_id+"' width='12%'>"+select_promo_rate+"</td>";
			str += "<td class='product_tr_"+value.product_id+"' width='9%'>₱"+origprice+"</td>";
			str += "<td class='product_tr_"+value.product_id+"' width='15%'><input type='number' min='0' class='form-control allownumericwithdecimal notallowzero product_promo_price"+value.product_id+"' name='product_promo_price[]' onkeypress='return isNumberKey(event)' placecholder='1.00' style='background-color:white;' value="+value.product_promo_price+" readonly></td>";
			// str += "<td class='product_tr_"+value.product_id+"'><input type='number' min='0' class='form-control allownumericwithdecimal' name='product_promo_stock[]' onkeypress='return isNumberKey(event)' placecholder='0.00' value="+value.product_promo_stock+"></td>";
			// str += "<td class='product_tr_"+value.product_id+"'>"+select_promo_stock+"<input type='number' min='0' class='form-control allownumericwithdecimal notallowzero mb-2 product_promo_stock_type product_promo_stock"+value.product_id+"' data-value='"+value.product_id+"' name='product_promo_stock[]' onkeypress='return isNumberKey(event)' placecholder='0.00' "+promo_style+" value="+value.product_promo_stock+"><input type='hidden' class='prev_product_promo_stock"+value.product_id+"' value="+value.product_promo_stock+"></td>";
			str += "<td class='product_tr_"+value.product_id+"' width='9%'><input type='text' min='0' class='form-control allownumericwithoutdecimal mb-2 product_promo_stock_type product_promo_stock"+value.product_id+"' data-value='"+value.product_id+"' data-current_stock='"+current_stock_num+"' name='product_promo_stock[]' onkeypress='return isNumberKey(event)' placecholder='0.00' value="+parseInt(value.product_promo_stock)+"><input type='hidden' class='prev_product_promo_stock"+value.product_id+"' value="+value.product_promo_stock+"></td>";
			str += "<td class='product_tr_"+value.product_id+"' width='6%'>"+current_stock+"</td>";
			str += "<td class='product_tr_"+value.product_id+"' width='10%'>"+select_purch_limit+"<input type='number' min='0' class='form-control allownumericwithdecimal notallowzero product_purch_limit_type product_purch_limit"+value.product_id+"' name='product_purch_limit[]' onkeypress='return isNumberKey(event)' placecholder='0.00' "+purch_style+" value="+value.product_purch_limit+"><input type='hidden' class='prev_product_purch_limit"+value.product_id+"' value="+value.product_purch_limit+"></td>";
			str += "<td class='product_tr_"+value.product_id+"' width='7%'><span class='prod_status_label_"+value.product_id+"'>"+prod_status+"</span><input type='text' class='prod_status_"+value.product_id+"' name='product_status[]' style='display:none' value='"+value.product_status+"'></td>";
			str += "<td class='product_tr_"+value.product_id+"' width='8%'>"+buttons+"</td>";
			str += "<td class='product_tr_"+value.product_id+"'>"+featured+"</td>";
			str += "</tr>";
			
		
			$('#tbody_prodpromo').prepend(str);
			// $('#tbody_prodpromo').append(str).find('.datepicker').datepicker();
			// $(str).find('.ddate').datepicker({
			// 	autoclose: true
			// });
		}
		
    }

	function populateProduct() {
		var select_shop 	= $("#select_shop").val();
		var select_category = $("#select_category").val();
		var _name        	= $("input[name='_name']").val();

		$.ajax({
		  url:base_url+"promotion/Main_promotion/product_table", // json datasource
		  type: 'post',
		  data: {
				'select_shop':select_shop,
				'select_category':select_category,
				'_name':_name,
				'productArray': JSON.stringify(productArray)
	 	  },
		  beforeSend: function () {
			$.LoadingOverlay("show");          
		  },
		  complete: function (data) {
			  $.LoadingOverlay("hide");          
			  var response = $.parseJSON(data.responseText);
			 
			  if(response.data.length > 0){
				
			  }
			  else{
				
			  } 

			  var dataTable = $('#table-grid-productpromo').DataTable({
				"processing": true,
				// "responsive": true,
				"searching": true,
				"destroy": true,
				"data": response.data,
				"order":[[0, 'asc']],
				"columnDefs": [
					{  orderable: false, targets: 0},
					{ responsivePriority: 1, width: "10%", targets: 0},
					{ type: 'sort-numbers-ignore-text', targets : 2 }
				],
				"order": [[ 1, "asc" ]],
				"lengthMenu": [[parseInt(showEntries), 10, 25, 50, 100], [parseInt(showEntries), 10, 25, 50, 100]]
			  });
		  },
		  error: function () {  // error handling
			$.LoadingOverlay("hide");
		  }
		})
	}

	function loadExistingData(){
		$.ajax({
            type:'post',
            url:base_url+'promotion/Main_promotion/fetch_productPromo',
            data:{
                'select_shop': $('#select_shop2').val() 
            },
            success:function(data){
                $.LoadingOverlay("hide"); 

                var json_data = JSON.parse(data);

                if(json_data.success){
					counter = 0;
					if(json_data.productArr.length > 0){
						$('#tbody_prodpromo').empty();
					}
					$.each(json_data.productArr, function(key, value) {
						otherinfo = (value.item_otherinfo != '' || value.item_otherinfo != null) ? ' ('+value.item_otherinfo+')' : '';
						dataArr = {
							'product_id'          : value.product_id,
							'sys_shop'            : value.sys_shop,
							'product_name'        : (value.parent_product_name != null) ? value.parent_product_name+" - "+value.itemname+otherinfo : value.itemname+otherinfo,
							'product_promo_type'  : value.promo_type,
							'product_promo_rate'  : value.promo_rate,
							'product_orig_price'  : value.price,
							'product_promo_price' : value.promo_price,
							'product_promo_stock' : value.promo_stock,
							'product_curr_stock'  : value.no_of_stocks,
							'product_purch_limit' : value.purchase_limit,
							'product_status'      : value.status,
							'product_feauted'     : value.is_featured,
							'product_arrangement' : value.arrangement
						};
						var date = new Date(value.startdate);
   						start_date = ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear();
						$('#start_date').datepicker('setDate', start_date);

						var date = new Date(value.enddate);
   						end_date = ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear();
						$('#end_date').datepicker('setDate', end_date);

						from_time = value.starttime.split(':');
						to_time   = value.endtime.split(':');
						from_time = from_time[0]+":"+from_time[1];
						to_time   = to_time[0]+":"+to_time[1];

						$("#start_time").val(from_time);
						$("#end_time").val(to_time);

						if(from_time == '00:00' && to_time == '23:59'){
							$("#checkbox_time").prop("checked", false);
							$(".timeDiv").hide(100);
						}
						else{
							$("#checkbox_time").prop("checked", true);
							$(".timeDiv").show(100);
						}

						alignProductPromotion(dataArr, counter);
						counter++;
						productArray.push(dataArr);
					});
                }else{

                }
            },
        });
	}

	function batchUpdateSettings(data){
			batch_promo_price        = $('#batch_promo_price').val();
			// batch_purch_limit     = $('#batch_purch_limit').val();
			batch_promo_type         = $('#batch_promo_type').val();
			batch_promo_rate         = $('#batch_promo_rate').val();
			batch_promo_stock        = $('#batch_promo_stock').val();
			batch_promo_stock_qty    = $('#batch_promo_stock_qty').val();
			batch_purch_limit_select = $('#batch_purch_limit_select').val();
			batch_purch_limit        = $('#batch_purch_limit').val();
			
		$('.checkbox_prod:checked').each(function() {
			product_id = $(this).val();
			$('.product_promo_price'+product_id).val(batch_promo_price);
			// $('.product_purch_limit'+product_id).val(batch_purch_limit);
			$('.product_promo_type'+product_id).val(batch_promo_type).trigger('change');
			$('.product_promo_rate'+product_id).val(batch_promo_rate).trigger('change');
			$('.select_promo_stock'+product_id).val(batch_promo_stock).trigger('change');
			$('.product_promo_stock'+product_id).val(batch_promo_stock_qty);
			$('.select_purch_limit'+product_id).val(batch_purch_limit_select).trigger('change');
			$('.product_purch_limit'+product_id).val(batch_purch_limit);
		});

	}

	function setTime(){
		if($("#checkbox_time").prop("checked") == true){
			$('.timeDiv').show(100);
			$('#start_time').val('00:00');
			$('#end_time').val('00:00');
		}
		else{
			$('.timeDiv').hide(100);
			$('#start_time').val('00:00:00');
			$('#end_time').val('23:59:59');
		}
	}
	loadExistingData();

	// $(document).delegate('.allownumericwithdecimal','cut copy paste',function(e){
	// 	e.preventDefault();
	// });

	$(document).delegate('.allownumericwithdecimal','paste',function(e){
		var self = $(this);
		self.val(self.val().replace(/[^0-9\.]/g, ''));
		if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
		{
		  evt.preventDefault();
		}
	  });

	$(document).delegate('.allownumericwithdecimal','input',function(e){
		var self = $(this);
		self.val(self.val().replace(/[^0-9\.]/g, ''));
		if ((e.which != 46 || self.val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) 
		{
			e.preventDefault();
		}
	});
	
	$(document).delegate('.notallowzero','keypress keyup',function(e){
		if($(this).val() > 0 && $(this).val() != '.'){

		}
		else{
			$(this).val('');  
		}
	});

	$(document).delegate('.notallowzero','paste',function(e){
		if($(this).val() > 0 && $(this).val() != '.'){
			
		}
		else{
			$(this).val('');  
		}
	});

	$(document).delegate('.notallowzero','focusout',function(e){
		if($(this).val() > 0 && $(this).val() != '.'){
			
		}
		else{
			$(this).val('');  
		}
	});

	// $(document).delegate('#end_time','focusout',function(e){
	// 	setTimeout(function(){
	// 		date_to    = $('#start_date').val();
	// 		date_from  = $('#end_date').val();
	// 		start_time = $('#start_time').val();
	// 		end_time   = $('#end_time').val();
	// 		start_time = start_time.replace(":", "");
	// 		end_time   = end_time.replace(":", "");

	// 		console.log(start_time);
	// 		console.log(end_time);

	// 		if(date_to == date_from){
	// 			if(parseFloat(end_time) < parseFloat(start_time)){
	// 				console.log('Test');
	// 			}
	// 		}
	// 	}, 200);
		
	// });

	$("#search_clear_btn").click(function (e) {
		$("#select_shop").prop("selectedIndex", 0);
		$("#select_category").prop("selectedIndex", 0);
		$( "#checkbox_all:checkbox:checked" ).trigger( "click" );
		showEntries = 10;
		populateProduct();
	});

	$(".clearTableFields").click(function (e) {
		$("#select_shop").prop("selectedIndex", 0);
		$("#select_category").prop("selectedIndex", 0);
		showEntries = 10;
	});

	function sortNumbersIgnoreText(a, b, high) {
		var reg = /[+-]?((\d+(\.\d*)?)|\.\d+)([eE][+-]?[0-9]+)?/;    
		a = a.replace(/,/g, '');
		a = a.match(reg);      
		a = a !== null ? parseFloat(a[0]) : high;
		b = b.replace(/,/g, '');
		b = b.match(reg);    
		b = b !== null ? parseFloat(b[0]) : high;
		return ((a < b) ? -1 : ((a > b) ? 1 : 0));    
	}
	jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"sort-numbers-ignore-text-asc": function (a, b) {
			return sortNumbersIgnoreText(a, b, Number.POSITIVE_INFINITY);
		},
		"sort-numbers-ignore-text-desc": function (a, b) {
			return sortNumbersIgnoreText(a, b, Number.NEGATIVE_INFINITY) * -1;
		}
	});

	$('#unsetFeadutedModal').on('show.bs.modal', function (e) {
		var Product_id = $(e.relatedTarget).attr('data-id');
		$(this).find('#product_ids').val(Product_id);
	});


	$('#setFeadutedModal').on('show.bs.modal', function (e) {
		var Product_id = $(e.relatedTarget).attr('data-id');
		$(this).find('#product_id').val(Product_id);
	});

	$('#saveFeatureConfirm').click(function(){

		var product_arrangement		= $('#entry-feat-product-arrangement').val();
		var product_id	         	= $('#product_id').val();
		$.LoadingOverlay("Show");
		$.ajax({
			type:'post',
			url:base_url+'promotion/Main_promotion/get_feutured_products_count_piso/',
			success:function(data){
				var res = data.result;
				if (data >= 7){
					$.toast({
						text: 'Warning!<br>You have reached the maximum of 7 featured products allowed.',
						icon: 'info',
						loader: false,  
						stack: false,
						position: 'top-center', 
						bgColor: '#FFA500',
						textColor: 'white',
						allowToastClose: false,
						hideAfter: 10000
					});
					$.LoadingOverlay("hide");
					$('#setFeadutedModal').modal('hide');
				}else{
				    
						if(product_arrangement == ''){
							sys_toast_warning('Please feature arrangement number.');
							$.LoadingOverlay("hide");
							$('#setFeadutedModal').modal('hide');
						}
						$.ajax({
							method: "POST",
							url: base_url+'promotion/Main_promotion/check_feutured_product_arrangementPiso/'+product_arrangement,
								success: function(data){
									if(data == 1) {             
										$.toast({
											text: 'Warning!<br>This number is already selected...',
											icon: 'info',
											loader: false,  
											stack: false,
											position: 'top-center', 
											bgColor: '#FFA500',
											textColor: 'white',
											allowToastClose: false,
											hideAfter: 10000
										});
										$("#entry-feat-product-arrangement").val("").change();
										$.LoadingOverlay("hide");
										$('#setFeadutedModal').modal('hide');
									}else{


										$.ajax({
											type: 'post',
											url: base_url+"promotion/Main_promotion/save_featured_piso",
											dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
											data:{
												'product_id': product_id,
												'product_arrangement':product_arrangement,
								
											},
											success:function(data){
												$.LoadingOverlay("hide");
												var json_data = JSON.parse(data);
												if(json_data.success){
													$('#setFeadutedModal').modal('hide');
													showCpToast("success", "Added!", "Featured product added successfully.");
													setTimeout(function(){location.reload()}, 2000);
												}
												else{
													//sys_toast_warning(json_data.message);
													showCpToast("warning", "Warning!", json_data.message);
													$('#setFeadutedModal').modal('hide');
												}
											   
											},
											error: function(error){
												//sys_toast_error('Something went wrong. Please try again.');
												showCpToast("error", "Error!", 'Something went wrong. Please try again.');
											}
										});
									   

									}
					
								}
						});

					   
				
				}
			}
		});


	});


	$('#unsaveFeatureConfirm').click(function(){

		var product_id	  = $('#product_ids').val();
		$.LoadingOverlay("Show");
		
		$.ajax({
			type: 'post',
			url: base_url+"promotion/Main_promotion/removed_featured_piso",
			dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
			data:{
				'product_id': product_id,

			},
			success:function(data){
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if(json_data.success){
					$('#unsetFeadutedModal').modal('hide');
					showCpToast("success", "Removed!", "Featured product removed successfully.");
					setTimeout(function(){location.reload()}, 2000);
				}
				else{
					sys_toast_warning(json_data.message);
					$('#unsetFeadutedModal').modal('hide');
				}
			   
			},
			error: function(error){
				sys_toast_error('Something went wrong. Please try again.');
			}
		});
	

	});


});

$('#start_date').datepicker().on('changeDate', (e) => {
	var todaydate = $('#todaydate').val();
	var new_start_date = moment(todaydate).subtract(0, 'day').format('MM/DD/YYYY');

	$('#end_date').datepicker('setStartDate', new_start_date);
});

$("#end_date").click(function (e) {
	var date_to = $('#todaydate').val();
	var new_start_date = moment(date_to).subtract(0, 'day').format('MM/DD/YYYY');
	$('#end_date').datepicker('setStartDate', new_start_date);
});

$("#start_date").click(function (e) {
	var todaydate = $('#todaydate').val();
	var new_start_date = moment(todaydate).subtract(0, 'day').format('MM/DD/YYYY');
	$('#start_date').datepicker('setStartDate', new_start_date);
});