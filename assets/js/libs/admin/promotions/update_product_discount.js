$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var token    = $('#token').val();
    var ini      = $("body").data('ini');
    var loaded = false;
    $(".addProductBtn").on('click',function(){
        
        if($("#set_max_amount").prop('checked') && parseFloat($("#disc_ammount_limit").val()) > parseFloat($("#disc_ammount").val())){
            sys_toast_warning("Maximum Discount must be less than discount amount");
        }else
        if($("#disc_ammount").val() != ""){
            $("#addProductModal").modal('show');
            fillDatatable();
        }else{
            sys_toast_error("Please set Discount Amount first");
        }
    });
    $("#disc_ammount_type").on('change',function(){
        if($(this).val() == "2"){
            $(".discount_type_label").html("Discount Amount(%):");
        }else{
            $(".discount_type_label").html("Discount Amount:");
        }
        loadselectedproducts(); 
    });
    $("#set_max_amount").on('change',function(){
        $("#maximum_discount_price").toggle(250);
    });
    $(".saveBtn").click(function(){
        var form = $("form[name=form_discount]");
        var form_data = new FormData(form[0]);
        id = $(this).data('id');
        form_data.append('update',true);
        form_data.append('id',id);
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/Main_promotions/save_discount',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    sys_toast_success(json_data.message);
                    console.log(token);
                    // window.location.assign(base_url+"admin/Main_promotions/products_discount_list/"+token);
                }else{
                    //sys_toast_warning(json_data.message);
                    sys_toast_warning(json_data.message);
                    if(json_data.products != ''){
                        loadselectedproducts(json_data.products);
                    }
                }
            }
        });
    });
    $(document).on('click', '.removeSelectedbtn', function(){
        var form = $("form[name=form_products]");
        var form_data = new FormData(form[0]);
        id = $(this).data('id');
        form_data.append('product',id);
        $.ajax({
            type: 'post',
            url: base_url+'admin/Main_promotions/remove_selectedproducts',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                loadselectedproducts();
            }
        });
    });
    $(document).on('click', '#btnSearch', function(){
        fillDatatable();
    });
    
    $(document).on('change', '#selectAll', function(){
        $("input:checkbox[name=product_checkbox]").each(function(){
            $(this).prop('checked',true);
        });
    });

    Number.prototype.format = function(n, x) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
        return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
    };
    $("#btnAddProducts").click(function(){
        var form = $("form[name=form_products]");
        var form_data = new FormData(form[0]);
        var selected_products = Array();
        $("input:checkbox[name=product_checkbox]:checked").each(function(){
            selected_products.push($(this).data('id'));
        });
        form_data.append('selected_products',selected_products);
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/Main_promotions/store_selectedproducts',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                $("#addProductModal").modal('hide');
                // var json_data = JSON.parse(data);
                loadselectedproducts();
                // if(json_data.success) {
                //     //sys_toast_success(json_data.message);
                //     sys_toast_success(json_data.message);
                //     $("#inventory_modal").modal('hide');
                //     $("#f_no_of_stocks").val(json_data.qty);
                //     //setTimeout(function(){location.reload()}, 2000);
                //     //window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                // }else{
                //     //sys_toast_warning(json_data.message);
                //     sys_toast_warning(json_data.message[0]);
                // }
            },
            error: function(error){
                sys_toast_warning(json_data.message);
                //showCpToast("warning", "Warning!", json_data.message);
            }
        });
    });

    loadselectedproducts();

    function loadselectedproducts(existedproducts = Array()){
        $.LoadingOverlay("show");
        var form = $("form[name=form_products]");
        var form_data = new FormData(form[0]);
        $.ajax({
            type: 'post',
            url: base_url+'admin/Main_promotions/get_selectedproducts',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                var html='';
                $.each(data, function(key, value) {
                    var additional_css = '';
                    if(existedproducts.includes(value.id)){
                        additional_css = "bg-danger text-white";
                    }
                    html+="<tr class='"+additional_css+"'>";
                    html+="<td>"+(value.parent_name!=null?value.parent_name + " - ":'')+value.name+"</td>";
                    html+="<td>"+value.price+"</td>";
                    html+="<td>"+getDiscount(value.price).format(2)+"</td>";
                    html+="<td><button class='btn btn-danger removeSelectedbtn' data-id="+value.id+" type='button'><i class='fa fa-trash-o'></i>"+"</button></td>";
                    html+="</tr>";
                });
                $("#tbody_prodpromo").html(html);
                $.LoadingOverlay("hide");
            }
        });
    }
    function getDiscount(price){
        percentage = $("#disc_ammount").val() == ""?0:$("#disc_ammount").val();
        if($("#set_max_amount").prop('checked')){
            if($("#disc_ammount_type").val() == "1"){
                difference = (parseFloat(price)-parseFloat(percentage));
                console.log(difference);
                return difference<parseFloat($("#disc_ammount_limit").val()) ? (parseFloat(price) < parseFloat($("#disc_ammount_limit").val()) ? parseFloat(price) : parseFloat($("#disc_ammount_limit").val())): difference;
            }else{
                difference = price-(parseFloat(percentage/100)*parseFloat(price));
                console.log(difference);
                return difference<parseFloat($("#disc_ammount_limit").val()) ? (parseFloat(price) < parseFloat($("#disc_ammount_limit").val()) ? parseFloat(price) : parseFloat($("#disc_ammount_limit").val())) : difference;
            }
        }else{
            if($("#disc_ammount_type").val()=="1"){
                return parseFloat(price)-parseFloat(percentage);
            }else{
                return price-(parseFloat(percentage/100)*parseFloat(price));
            }
        }
    }
	function fillDatatable(){
		var _record_status 	= '';
		var _name 			= $("input[name='_name']").val();
		var _categories     = $("select[name='_categories']").val();
		var date_from       = $("#date_from").val();
		var dataTable = $('#table-grid-product').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			"searching": false,
			responsive: true,
			"language": {                
				"infoFiltered": ""
			},
			"columnDefs": [
				{ targets: 0, orderable: false, "sClass":"text-center"},
				{ responsivePriority: 1, targets: 4 },
			],createdRow: function( row, data, dataIndex ) {
				//console.log(row);
				var data2 = $('#table-grid-product').DataTable().row(row).data();
				if(data2[5]=='Expired Stocks'){
					$(row).addClass( 'bg-danger text-white' );
			   	}
				if(data2[5]=='Expiring Soon'){
					$(row).addClass( 'bg-warning' );
		   		}
				if(data2[5]=='Out of Stocks'){
					$(row).addClass( 'bg-secondary text-white' );
				}
				// if ( data['jobStatus'] == "red" ) {
				// 	$(row).addClass( 'lightRed' );
				// }else if(data['jobStatus'] == "green"){
				// 	$(row).addClass( 'lightGreen' );
				// }else if(data['jobStatus'] == "amber"){
				// 	$(row).addClass( 'lightAmber' );
				// }
			},
			"ajax":{
				type: "post",
				url:base_url+"admin/Main_products/product_table_active", // json datasource
				data: {'_record_status':_record_status, 
				        '_name':_name, 
						'_categories':_categories,
                        'loaded' : loaded
					}, // serialized dont work, idkw
				beforeSend:function(data) {
					$.LoadingOverlay("show"); 
				},
				complete: function(data) {
                    loaded = true;
					$.LoadingOverlay("hide");
					$("#_search").val(JSON.stringify(this.data));
					$("input#_name").val(_name);
					$(".table-grid-error").remove();
				},
				error: function(){  // error handling
					$(".table-grid-error").html("");
					$("#table-grid-product").append('<tbody class="table-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$("#table-grid_processing").css("display","none");
				}
			}
		});
	}
});