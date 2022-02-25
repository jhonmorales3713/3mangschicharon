$(function () {
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();

	// var counts = 0;

	function fillDatatable() {
		var _name = $("input[name='_name']").val();
		var _select_shop = $("select[name='select_shop']").val();

		var dataTable = $('#table-list').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			searching: false,
			responsive: false,
			"columnDefs": [{
					targets: 4,
					orderable: false,
					"sClass": "text-center"
				},
				{
					targets: 5,
					orderable: false,
					"sClass": "text-center"
				},
				//{ responsivePriority: 1, targets: 0 }, { responsivePriority: 2, targets: -1 }
			],
			"ajax": {
				type: "post",
				url: base_url + "promotion/Main_promotion/shipping_fee_list", // json datasource
				data: {
					'_name': _name,
					'_select_shop': _select_shop
				}, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (res) {
					var filter = {
						'_name': _name,
						'_select_shop': _select_shop
					};
					$.LoadingOverlay("hide");
					$('#_search').val(JSON.stringify(this.data));
					$('#_filter').val(JSON.stringify(filter));
					if (res.responseJSON.data.length > 0) {
						$('#btnExport').show();
					} else {
						$('#btnExport').hide();
					}
				},
				error: function () { // error handling
					$(".table-list-error").html("");
					$("#table-list").append('<tbody class="table-list-error text-center"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
					$("#table-list_processing").css("display", "none");
				}
			}
		});
	}

	fillDatatable();
	// end - for loading a table

	// start - for search purposes

	$("#_record_status").change(function () {
		$("#btnSearch").click();
	});

	$("#search_hideshow_btn").click(function (e) {
		e.preventDefault();

		var visibility = $('#card-header_search').is(':visible');

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>');
		} else {
			//not visible
			$("#search_hideshow_btn").html('<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>');
		}

		$("#card-header_search").slideToggle("slow");
	});

	$("#search_clear_btn").click(function (e) {
		$(".search-input-text").val("");
		fillDatatable();
	})

	$(".enter_search").keypress(function (e) {
		if (e.keyCode === 13) {
			$("#btnSearch").click();
		}
	});

	$('#btnSearch').click(function (e) {
		e.preventDefault();
		fillDatatable();
	});

	$(document).delegate('.notallowzero', 'keypress keyup', function (e) {
		if ($(this).val() > 0 && $(this).val() != '.') {

		} else {
			$(this).val('');
		}
	});

	$(document).delegate('.notallowzero', 'paste', function (e) {
		if ($(this).val() > 0 && $(this).val() != '.') {

		} else {
			$(this).val('');
		}
	});

	$(document).delegate('.notallowzero', 'focusout', function (e) {
		if ($(this).val() > 0 && $(this).val() != '.') {

		} else {
			$(this).val('');
		}
	});

	// start of adding sf details
	$(document).delegate('#edit_require_code', "click", function () {
		if ($("#edit_require_code").is(":checked")) {
			$("#edit_sfd_code").show("slow");
		} else {
			$("#edit_sfd_code").hide("slow");
		}
	});

	$(document).delegate('#require_code', "click", function () {
		if ($("#require_code").is(":checked")) {
			$("#sfd_code").show("slow");
            $("#sfd_code").prop('required', true);
		} else {
			$("#sfd_code").hide("slow");
            $("#sfd_code").prop("required", false);
		}
	});

	$(document).delegate("#limit", "click", function () {
		if ($("#limit").is(":checked")) {
			$("#showLimitnum").show("slow");
			$("#usage_qty").val('');
		} else {
			$("#showLimitnum").hide("slow");
			$("#usage_qty").val('');
		}
	});

	$(document).delegate("#edit_limit", "click", function () {
		if ($("#edit_limit").is(":checked")) {
			$("#edit_showLimitnum").show("slow");
			$("#edit_usage_qty").val('');
		} else {
			$("#edit_showLimitnum").hide("slow");
			$("#edit_usage_qty").val('');
		}
	});

	$(document).delegate("#edit_setEndDate", "click", function () {
		if ($("#edit_setEndDate").is(":checked")) {
			$(".edit_showEndDate").show('slow');
		} else {
			$("#edit_date_to").val('');
			$("#edit_time_to").val('');
			$(".edit_showEndDate").hide('slow');
		}
	});

	$(document).delegate("#setEndDate", "click", function () {
		if ($("#setEndDate").is(":checked")) {
			$(".showEndDate").show('slow');
		} else {
			$("#date_to").val('');
			$("#time_to").val('');
			$(".showEndDate").hide('slow');
		}
	});

	$('input[name="requirement"]').on('click', function() {
		if ($('input[name="requirement"]:checked').val() == 1) {
			$(".showSFReq").show('slow');
		} else {
			$(".showSFReq").hide('slow');
		}
	});

	$('input[name="edit_requirement"]').on('click', function() {
		if ($('input[name="edit_requirement"]:checked').val() == 1) {
			$(".edit_showSFReq").show('slow');
		} else {
			$(".edit_showSFReq").hide('slow');
		}
	});

	// var subsidize = false;
	// var free = false; // free shipping

	$(document).delegate('.checkBtn', 'click', function () {
		var index = $(this).data('value');

		$(".checks"+index).not(this).prop("checked", false);

		if ($('#enabledId' + index).is(":checked")) {
			$('.show_sf' + index).show("slow");
            $("#isPercentage" + index).show("slow");
			$("#checkSf" + index).prop("checked", false);
			$(".fsBtn").attr("disabled", false);
			free = false;
		} else if ($("#checkSf" + index).is(":checked")) {
			free = true;
			$('.show_sf' + index).val('');
			$('.show_sf' + index).hide("slow");
            $("#isPercentage" + index).hide("slow");
			$('#enabledId' + index).prop("checked", false);
		} else {
			$(".sf_price_error span").remove();
			$('.show_sf' + index).val('');
			$('.show_sf' + index).hide("slow");
            $("#isPercentage" + index).hide("slow");
			$(".checks" + index).not(this).prop("checked", false);
		}
		
	});

	$(document).delegate('.fsBtn','click',function(){

        if($(this).is(":checked")){
            $('.fsBtn').attr("disabled", true);
            $(this).prop("disabled", false);
            
        }
        else{
            $('.fsBtn').prop("disabled", false);
        }

    });

	var max = 3;
	var total_element = 0;
	var count_row = 1;
	// var error_tier_count = 0;

	$(document).delegate('.add-record', 'click', function () {

		var id = $("#table-grid > tbody > tr").length;

		if (id < max) {
			if (free) {
				var markup = "<tr id='count_row" + total_element + "' rowIndex='" + total_element + "' class='product_tr_" + total_element + "'>";
				markup += '<td><input type="text" id="minimum_price" name="minimum_price[]" class="form-control material_josh form-control-sm search-input-text notallowzero m_input" placeholder="Amount"><p id="m_price_error" class="m_price_error" style="color:red"></p></td>';
				markup += '<td><input type="checkbox" id="enabledId' + total_element + '" name="is_subsidize[]" value="0" class="checkBtn sfd_input checks'+ total_element +'" data-value="' + total_element + '">';
				markup += '<span> Discount Amount </span><select name="isPercentage[]" style="display: none;" class="form-control select_element" id="isPercentage'+ total_element +'"><option value="0">Fixed</option><option value="1">Percentage</option></select>';
				markup += '<input type="text" id="sf_amount" name="sf_amount[]" style="display: none;" class="form-control show_sf' + total_element + ' sf_input" placeholder="Amount"><p id="sf_price_error" class="sf_price_error" style="color:red"></p>';
				markup += '<br><input type="checkbox" name="is_subsidize[]" value="1" class="checkBtn sfd_input fsBtn" id="checkSf' + total_element + '" data-value="'+ total_element +'" disabled="disabled"><label style="color:black"> Free Shipping</label><input type="hidden" name="count_row" value="'+count_row+++'"></td>';
				markup += '<td><a class="btn btn-xs delete-record" data-value="' + total_element + '"><i class="fa fa-trash"></i></a></td>';
				markup += "</tr>";	
				$("#table-grid").append(markup);
			} else {
				var markup = "<tr id='count_row" + total_element + "' rowIndex='" + total_element + "' class='product_tr_" + total_element + "'>";
				markup += '<td><input type="text" id="minimum_price" name="minimum_price[]" class="form-control material_josh form-control-sm search-input-text notallowzero m_input" placeholder="Amount"><p id="m_price_error" class="m_price_error" style="color:red"></p></td>';
				markup += '<td><input type="checkbox" id="enabledId' + total_element + '" name="is_subsidize[]" value="0" class="checkBtn sfd_input checks'+ total_element +'"  data-value="' + total_element + '">';
				markup += '<span> Discount Amount </span><select name="isPercentage[]" style="display: none;" class="form-control select_element" id="isPercentage'+ total_element +'"><option value="0">Fixed</option><option value="1">Percentage</option></select>';
				markup += '<input type="text" id="sf_amount" name="sf_amount[]" style="display: none;" class="form-control show_sf' + total_element + ' sf_input mt-1" placeholder="Amount"><p id="sf_price_error" class="sf_price_error" style="color:red"></p>';
				markup += '<br><input type="checkbox" name="is_subsidize[]" value="1" class="checkBtn sfd_input fsBtn" id="checkSf' + total_element + '" data-value="'+ total_element +'"><label> Free Shipping</label></td><input type="hidden" name="count_row" value="'+count_row+++'">';
				markup += '<td><a class="btn btn-xs delete-record" data-value="' + total_element + '"><i class="fa fa-trash"></i></a></td>';
				markup += "</tr>";
				$("#table-grid").append(markup);

			}
			total_element++;
		} 
        if(id == 3){
            $(".error_tier").append("<span>&nbsp;Maximum of 3 tier per promotion.</span>");
            document.getElementById("btnTier").disabled = true;
        }

	});

	var save1 = true;
	var save2 = true;
	var save3 = true;
	var check_price = 0;
    $("#table-grid").on("change", ".m_input", function() {
        
        var row = $(this).closest("tr");
        let mb_price = $(this).val();
        var index = row.attr('rowIndex');
        let sf_price = row.find(".sf_input").val();
		let err_counts = 0;
        var i = 0;
        let counter=0;
        let counter_higher = 0;
		count_higher = 0;
		let count = 0;

        
        $('#table-grid tr').each(function(row2, tr){

            if(i > 0){


                let s_price = $(this).find("td:eq(1) input[type='text']").val();
                let m_price = $(this).find("td:eq(0) input[type='text']").val();
                var index2=$(tr).attr("rowIndex");
				var row2 = $(this).closest("tr");
		
                if(row.find(".fsBtn").is(':checked') && index!=index2 && parseInt(m_price) > parseInt(mb_price)){
                    counter_higher++;
                }

				if(row2.find(".fsBtn").is(':checked') && index!=index2 && parseInt(m_price) < parseInt(mb_price)){
                    counter_higher++;
                    
                }

                if(index!=index2 && parseInt(mb_price) > parseInt(m_price) && parseInt(sf_price) <= parseInt(s_price)){
                    count++;
                }

                if(parseInt(m_price) == parseInt(mb_price) && index != index2){
                    counter++;
                }

                if(parseInt(m_price) < parseInt(mb_price) && index != index2){
                    check_price = 1;
                }

                if(parseInt(m_price) > parseInt(mb_price) && index != index2){
                    check_price = 2;
                }

				if (row.find($("#isPercentage"+index).val() == 1) && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

				if (row2.find($("#isPercentage"+index2).val() == 1) && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}
            }
            i++;
            
        });
        if(counter > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".m_price_error span").remove();
				row.find(".m_price_error").append("<span>Please avoid setting the same minimum cart amount as another tier.</span>");
				$("#btnTier").prop("disabled", true);	
			} else {
				row.find(".m_price_error span").remove();
			}
        } else {
			$(".m_price_error span").remove();
			$("#btnTier").prop("disabled", false);
			console.log(err_counts + '_b');
		}

        if(counter_higher > 0) {
			err_counts++;
			if (err_counts > 0) {
				row.find(".m_price_error span").remove();
				row.find(".m_price_error").append("<span>Free shipping should apply for the highest Minimum Cart Amount.</span>");
				$("#btnTier").prop("disabled", true);
			}
        }else {
			$("#btnTier").prop("disabled", false);
			$(".sf_price_error span").remove();
			console.log(err_counts + '_d');
		}

        if(count > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error span").remove();
				row.find(".sf_price_error").append("<span>Higher minimum spend should have higher shipping discount amount.</span>");
				$("#btnTier").prop("disabled", true);
			}
        }else {
			$("#btnTier").prop("disabled", false);
			console.log(err_counts + '_f');
		}

		if(count_higher > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error span").remove();
				row.find(".sf_price_error").append("<span>Discount amount is not applicable.</span>");
				$("#btnTier").prop("disabled", true);
			}
        }else {
			$("#btnTier").prop("disabled", false);
			console.log(err_counts + '_z');
		}

		if (err_counts === 0) {
			row.find(".m_price_error span").remove();
			save1 = true;
			save3 = true;
		} else {
			save1 = false;
		}

	});

	$("#table-grid").on("change", ".sf_input", function () {
        
        var row = $(this).closest("tr");
        let sf_price = $(this).val();
        let mb_price = row.find(".m_input").val();
        var index = row.attr('rowIndex');
        var i = 0;
        let counter=0;
        let count=0;
		let count_higher = 0;
		var err_counts = 0;

		$('#table-grid tr').each(function (row2, tr) {

            if(i > 0){

                let s_price = $(this).find("td:eq(1) input[type='text']").val();
                let m_price = $(this).find("td:eq(0) input[type='text']").val();

                var index2=$(tr).attr("rowIndex");

				if(!$(this).find(".fsBtn").is(':checked') && index!=index2 && parseInt(mb_price) < parseInt(m_price) && parseInt(sf_price) > parseInt(s_price)){
                    count++;
                }
                
                if(!$(this).find(".fsBtn").is(':checked') && index!=index2 && parseInt(mb_price) > parseInt(m_price) && parseInt(sf_price) <= parseInt(s_price)){
                    count++;
                }

                if(parseInt(s_price) == parseInt(sf_price) && index != index2){
                     count++;
                }
                if(index!=index2 && parseInt(mb_price) > parseInt(m_price) && parseInt(sf_price) <= parseInt(s_price)){
                    count++;
                }

				if ($("#isPercentage"+index).val() == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

				if ($("#isPercentage"+index2).val() == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

            }
            i++;
            
        });
``
        if(count>0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error span").remove();
            	row.find(".sf_price_error").append("<span>Higher minimum spend should have higher shipping discount amount.</span>");
				$("#btnTier").prop("disabled", true);
			}
        } else {
			$("#btnTier").prop("disabled", false);
			$(".sf_price_error span").remove();
			console.log(err_counts + '_j');
		}

		if(count_higher > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error span").remove();
            	row.find(".sf_price_error").append("<span>Discount amount is not applicable.</span>");
				$("#btnTier").prop("disabled", true);
			} else {
				row.find(".sf_price_error span").remove();
			}
        } else {
			$("#btnTier").prop("disabled", false);
			console.log(err_counts + '_zz');
		}

		if (err_counts === 0) {
			$(".sf_price_error span").remove();
			save1 = true;
			save2 = true;
		} else {
			save2 = false;
		}
	});

	$("#table-grid").on("change", ".sfd_input", function() {
        
        var row = $(this).closest("tr");
        //let mb_price = $(this).val();
        var index = row.attr('rowIndex');
        let mb_price = row.find(".m_input").val();
        let err_counts = 0;
        var i = 0;
        let counter_higher = 0;

        
        $('#table-grid tr').each(function(row2, tr){

            if(i > 0){

                let m_price = $(this).find("td:eq(0) input[type='text']").val();
                var index2=$(tr).attr("rowIndex");
                var row2 = $(this).closest("tr");
        
                if(row.find(".fsBtn").is(':checked') && index!=index2 && parseInt(m_price) > parseInt(mb_price)){
                    counter_higher++;
                }

                if(row2.find(".fsBtn").is(':checked') && index!=index2 && parseInt(m_price) < parseInt(mb_price)){
                    counter_higher++;
                    
                }
            }
            i++;
            
        });

        if(counter_higher > 0) {
            err_counts++;
            if (err_counts > 0) {
                row.find(".m_price_error span").remove();
                row.find(".m_price_error").append("<span>Free shipping should apply for the highest Minimum Cart Amount.</span>");
				$("#btnTier").prop("disabled", true);
            }
        }else {
			if (save1 === true) {
				$(".m_price_error span").remove();
			}
			$("#btnTier").prop("disabled", false);
            console.log(err_counts + '_k');
        }

        if (err_counts === 0) {
			console.log('lmao');
            save3 = true;
        } else {
            save3 = false;
        }

    });

	$("#table-grid").on("change", ".select_element", function() {

		var row = $(this).closest('tr');
		let percent = $(this).val();
		var index = row.attr("rowIndex");
		let sf_price = row.find(".sf_input").val();
		let i = 0;
		let count_higher = 0;
		var err_counts = 0;

		$('#table-grid tr').each(function (row2, tr) {

            if(i > 0){

                let s_price = $(this).find("td:eq(1) input[type='text']").val();

				if (percent == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

				if (percent == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

            }
            i++;
            
        });

		if(count_higher > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error span").remove();
            	row.find(".sf_price_error").append("<span>Discount amount is not applicable.</span>");
				$("#btnTier").prop("disabled", true);
			} else {
				row.find(".sf_price_error span").remove();
			}
        } else {
			$("#btnTier").prop("disabled", false);
			console.log(err_counts + '_zzz');
		}

		if (err_counts === 0) {
			row.find(".sf_price_error span").remove();
			save2 = true;
		} else {
			save2 = false;
		}

	});

	$(document).delegate('.delete-record', 'click', function () {
		var index = $(this).data('value');
		var value = $(this).val();

		$('.product_tr_' + index).remove();

		$(".error_tier span").remove();
		document.getElementById("btnTier").disabled = false;


	});

	$('#submitSfd').click(function (e) {
		e.preventDefault();

		var form = $('#form_promoprod');
		var form_data = new FormData(form[0]);

		console.log()

		if($('input[name="requirement"]:checked').val() == 0) {
			save1 = true;
			save2 = true;
			save3 = true;
		}

		if (save1 && save2 && save3) {
			$.ajax({
				type: form[0].method,
				url: base_url + 'promotion/Main_promotion/saveShippingDiscount',
				data: form_data,
				contentType: false,
				cache: false,
				processData: false,
				dataType: 'json',
				success: function (data) {
					$.LoadingOverlay("hide");

					if (data.success) {
						$("#add_modal").modal("hide");
						showCpToast("success", "Added!", data.message);
						setTimeout(function() {location.reload();}, 2500);
					} else {
						showCpToast("warning", "Warning!", data.message);
					}
				},
				error: function (error) {
					showCpToast("error", "Error!", error);
				}
			});
		} else {
			showCpToast("warning", "Warning!", 'Check all shipping fee condition error.');
		}

	});
	// end of adding sf details

	// delete sf details
	let delete_id;
	$('#table-list').delegate(".action_delete", "click", function () {
		delete_id = $(this).data('value');
	});

	$("#delete_modal_confirm_btn").click(function (e) {
		e.preventDefault();
		$.ajax({
			type: 'post',
			url: base_url + 'promotion/Main_promotion/sf_discount_delete_modal_confirm',
			data: {
				'delete_id': delete_id
			},
			success: function (data) {
				var res = data.result;
				if (data.success == 1) {
					fillDatatable(); //refresh datatable

					showCpToast("success", "Success!", data.message);
					$('#delete_modal').modal('toggle'); //close modal
				} else {
					showCpToast("info", "Note!", data.message);
				}
			}
		});
	});
	// end of deleting sf details

	// start of edit sf details
	$('#table-list').delegate(".action_edit", "click", function () {
		edit_id = $(this).data('value');

		$.ajax({
			type: 'post',
			url: base_url + 'promotion/Main_promotion/get_sf_data',
			data: {
				'edit_id': edit_id
			},
			success: function (data) {
				var result = data.result;
				if (data.success == 1) {

					update_form(data.result);

				} else {
                    showCpToast("info", "Note!", data.message);

				}
			}
		});
	});

	var edit_total_element = 0;
	var free = false;

	function update_form(result) {

		free = result.free;
		edit_total_element = result.total_element;
		$("#edit_id").val(result['id']);
		$("#edit_sfd_name").val(result['shipping_discount_name']);
		$("#edit_sfd_code").val(result['shipping_discount_code']);
		set_selected('edit_select_shop', result.shop_id);
		$("#edit_usage_qty").val(result['no_of_stocks']);

		if (result.is_sfCodeRequired == 1) {
			$("#edit_sfd_code").show();
			$("#edit_require_code").prop("checked", true);
		} else {
			$("#edit_sfd_code").hide();
			$("#edit_require_code").prop("checked", false);
		}

		$('.edit-promo-button').not(this).prop('checked', true);
		$("#edit_date_from").val(result['start_date']);
		$("#edit_time_from").val(result['start_time']);
		$("#edit_date_to").val(result['end_date']);
		$("#edit_time_to").val(result['end_time']);
		$('input[name="edit_requirement"][value="'+ result.requirement_isset +'"]').prop('checked', true);

		set_selected('edit_select_region', result.region);

		if (result.requirement_isset == 1) {
			$('.edit_showSFReq').show();
			$('.edit-requirement-button').not(this).prop('checked', true);
			$("#table-grid-edit").append(result.markup);
		}
		if (result.requirement_isset == 0) {
			$('.edit_showSFReq').hide();
		}

		if (result.no_of_stocks > 0) {
			$("#edit_limit").prop('checked', true);
			$("#edit_showLimitnum").show();
		}

		if (result.limitOne == 1) {
			$("#edit_limit_times").prop("checked", true);
		} else {
			$("#edit_limit_times").prop("checked", false);
		}

        if (result.end_date != '') {
			$("#edit_setEndDate").prop("checked", true);
            $(".edit_showEndDate").show();
        }

		if (result['is_sfCodeRequired'] == 1) {
			$("#edit_require_code").prop('checked', true);
		}
	}

	function set_selected(name, values) {
		$.each(values.split(","), function (i, e) {
			$("select[name*='" + name + "'] option[value='" + e + "']").prop("selected", true).select2().trigger('change');
		});
	}

	$("#edit_modal").on('hidden.bs.modal', function() {
		$("#table-grid-edit tbody tr").remove();
	});

	var subsidize = false;

	$(document).delegate('.edit_checkBtn', 'click', function () {
		var index = $(this).data('value');

		$(".edit_checks"+index).not(this).prop("checked", false);

		if ($('#edit_enabledId' + index).is(":checked")) {
			$('.edit_show_sf' + index).show("slow");
            $("#edit_isPercentage" + index).show("slow");
			$('#edit_checkSf' + index).prop("checked", false);
			$(".edit_fsBtn").attr("disabled", false);
			subsidize = true;
		} else if ($("#edit_checkSf" + index).is(":checked")) {
			free = true;
			$('.edit_show_sf' + index).val('');
			$('.edit_show_sf' + index).hide("slow");
            $("#edit_isPercentage" + index).hide("slow");
			$('#edit_enabledId' + index).prop("checked", false);
		} else {
			$(".sf_price_error_edit span").remove();
			$('.edit_show_sf' + index).val('');
			$('.edit_show_sf' + index).hide("slow");
            $("#edit_isPercentage" + index).hide("slow");
			$(".edit_checks" + index).not(this).prop("checked", false);
		}
		
	});

	$(document).delegate('.edit_fsBtn','click',function(){

        if($(this).is(":checked")){
            $('.edit_fsBtn').attr("disabled", true);
            $(this).prop("disabled", false);
            
        }
        else{
            $('.edit_fsBtn').prop("disabled", false);
        }

    });


	var max = 3;
	var edit_count_row = 1;

	$(document).delegate('.edit-add-record', 'click', function () {

		var id = $("#table-grid-edit > tbody > tr").length;

		if (id < max) {
			if (free) {
				var markup = "<tr id='edit_count_row' rowIndex='" + edit_total_element + "'  class='edit_product_tr_" + edit_total_element + "'>";
				markup += '<td><input type="text" id="edit_minimum_price"  name="edit_minimum_price[]" class="form-control material_josh notallowzero form-control-sm search-input-text m_input_edit" placeholder="Amount"><p id="m_price_error_edit"  class="m_price_error_edit" style="color:red"></p></td>';
				markup += '<td><input type="checkbox" id="edit_enabledId' + edit_total_element + '" name="edit_is_subsidize[]" value="0" class="edit_checkBtn sfd_input_edit edit_checks'+ edit_total_element +'"  data-value="' + edit_total_element + '">';
				markup += '<span> Discount Amount </span><select name="edit_isPercentage[]" style="display: none;" class="form-control edit_select_element" id="edit_isPercentage'+ edit_total_element +'"><option value="0">Fixed</option><option value="1">Percentage</option></select>';
                markup += '<input type="text" id="edit_sf_amount" name="edit_sf_amount[]" class="form-control edit_show_sf' + edit_total_element + ' sf_input_edit" placeholder="Amount" style="display:none;"><p id="sf_price_error_edit" class="sf_price_error_edit" style="color:red"></p>';
				markup += '<br><input type="checkbox" name="edit_is_subsidize[]" value="1" class="edit_checkBtn edit_fsBtn sfd_input_edit" id="edit_checkSf' + edit_total_element + '" data-value="'+ edit_total_element +'" disabled="disabled"><label style="color: black;"> Free Shipping</label><input type="hidden" name="edit_count_row" value="'+edit_count_row+++'"></td>';
				markup += '<td><a class="btn btn-xs edit-delete-record" data-value="' + edit_total_element + '"><i class="fa fa-trash"></i></a></td>';
				markup += "</tr>";
				$("#table-grid-edit").append(markup);
			} else {
				var markup = "<tr id='edit_count_row' rowIndex='" + edit_total_element + "'  class='edit_product_tr_" + edit_total_element + "'>";
				markup += '<td><input type="text" id="edit_minimum_price" name="edit_minimum_price[]" class="form-control material_josh notallowzero form-control-sm search-input-text m_input_edit" placeholder="Amount"><p id="m_price_error_edit"  class="m_price_error_edit" style="color:red"></p></td>';
				markup += '<td><input type="checkbox" id="edit_enabledId' + edit_total_element + '" name="edit_is_subsidize[]" value="0" class="edit_checkBtn sfd_input_edit edit_checks'+ edit_total_element +'"  data-value="' + edit_total_element + '">';
				markup += '<span> Discount Amount </span><select name="edit_isPercentage[]" style="display: none;" class="form-control edit_select_element" id="edit_isPercentage'+ edit_total_element +'"><option value="0">Fixed</option><option value="1">Percentage</option></select>';
                markup += '<input type="text" id="edit_sf_amount" name="edit_sf_amount[]" class="form-control edit_show_sf' + edit_total_element + ' sf_input_edit" placeholder="Amount" style="display:none;"><p id="sf_price_error_edit" class="sf_price_error_edit" style="color:red"></p>';
				markup += '<br><input type="checkbox" name="edit_is_subsidize[]" value="1" class="edit_checkBtn edit_fsBtn sfd_input_edit" id="edit_checkSf' + edit_total_element + '" data-value="'+ edit_total_element +'"><label> Free Shipping</label><input type="hidden" name="edit_count_row" value="'+edit_count_row+++'"></td>';
				markup += '<td><a class="btn btn-xs edit-delete-record" data-value="' + edit_total_element + '"><i class="fa fa-trash"></i></a></td>';
				markup += "</tr>";
				$("#table-grid-edit").append(markup);

			}
			edit_total_element++;
		}

		if(id == 3){
            $(".error_tier_edit").append("<span>&nbsp;Maximum of 3 tier per promotion.</span>");
            document.getElementById("btnTierEdit").disabled = true;
        }

	});

	var edit_save1 = true;
	var edit_save2 = true;
	var edit_save3 = true;
	var check_price = 0;
	$("#table-grid-edit").on("change", ".m_input_edit", function() {
        
        var row = $(this).closest("tr");
        let mb_price = $(this).val();
        var index = row.attr('rowIndex');
		let sf_price = row.find(".sf_input").val();
        var i = 0;
        let counter=0;
        let counter_higher = 0;
		let count_higher = 0;
		let err_counts = 0;
		let count = 0;

        $('#table-grid-edit tr').each(function(row2, tr){

			if (i > 0) {

				let s_price = $(this).find("td:eq(1) input[type='text']").val();
                let m_price = $(this).find("td:eq(0) input[type='text']").val();
                var index2=$(tr).attr("rowIndex");
				var row2 = $(this).closest("tr");

                if(row.find(".edit_fsBtn").is(':checked') && index!=index2 && parseInt(m_price) > parseInt(mb_price)){
                    counter_higher++;
                }

				if(row2.find(".edit_fsBtn").is(':checked') && index!=index2 && parseInt(m_price) < parseInt(mb_price)){
                    counter_higher++;
                }

				if(index!=index2 && parseInt(mb_price) > parseInt(m_price) && parseInt(sf_price) <= parseInt(s_price)){
                    count++;
                }

                if(parseInt(m_price) == parseInt(mb_price) && index != index2){
                    counter++;
                }

                if(parseInt(m_price) < parseInt(mb_price) && index != index2){
                    check_price = 1;
                }

                if(parseInt(m_price) > parseInt(mb_price) && index != index2){
                    check_price = 2;
                }

				if (row.find($("#edit_isPercentage").val() == 1) && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

				if (row2.find($("#edit_isPercentage").val() == 1) && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}
               
            }
            i++;
            
        });
        if(counter>0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".m_price_error_edit span").remove();
				row.find(".m_price_error_edit").append("<span>Please avoid setting the same minimum cart amount as another tier.</span>");
				$("#btnTierEdit").prop("disabled", true);	
			} else {
				row.find(".m_price_error_edit span").remove();
			}
        } else {
			$(".m_price_error_edit span").remove();
			$("#btnTierEdit").prop("disabled", false);
			console.log(err_counts + '_b');
		}

        if(counter_higher>0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".m_price_error_edit span").remove();
            	row.find(".m_price_error_edit").append("<span>Free shipping should apply for the highest Minimum Cart Amount.</span>");
				$("#btnTierEdit").prop("disabled", true);
			}
        }else {
			$("#btnTierEdit").prop("disabled", false);
			$(".sf_price_error_edit span").remove();
			console.log(err_counts + '_d');
		}

		if(count > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error_edit span").remove();
            	row.find(".sf_price_error_edit").append("<span>Higher minimum spend should have higher shipping discount amount.</span>");
				$("#btnTierEdit").prop("disabled", true);
			}
        }else {
			$("#btnTierEdit").prop("disabled", false);
			console.log(err_counts + '_f');
		}

		if(count_higher > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error_edit span").remove();
            	row.find(".sf_price_error_edit").append("<span>Discount Amount is not applicable.</span>");
				$("#btnTierEdit").prop("disabled", true);
			} else {
				row.find(".sf_price_error_edit span").remove();
			}
        }else {
			$("#btnTierEdit").prop("disabled", false);
			console.log(err_counts + '_z');
		}

		if (err_counts === 0) {
			row.find(".m_price_error_edit span").remove();
			$(".sf_price_error_edit span").remove();
			edit_save1 = true;
			edit_save3 = true;
		} else {
			edit_save1 = false;
		}
		
	});

	$("#table-grid-edit").on("change", ".sf_input_edit", function () {
        
        var row = $(this).closest("tr");
        let sf_price = $(this).val();
		let mb_price = row.find(".m_input_edit").val();
        var index = row.attr('rowIndex');
        var i = 0;
        let count=0;
		let count_higher = 0;
		var err_counts = 0;

		$('#table-grid-edit tr').each(function (row2, tr) {

			if (i > 0) {

                let s_price = $(this).find("td:eq(1) input[type='text']").val();
                let m_price = $(this).find("td:eq(0) input[type='text']").val();
                var index2 = $(tr).attr("rowIndex");

				if(!$(this).find(".edit_fsBtn").is(':checked') && index!=index2 && parseInt(mb_price) < parseInt(m_price) && parseInt(sf_price) > parseInt(s_price)){
                    count++;
                }
                
                if(!$(this).find(".edit_fsBtn").is(':checked') && index!=index2 && parseInt(mb_price) > parseInt(m_price) && parseInt(sf_price) <= parseInt(s_price)){
                    count++;
                }

				if(parseInt(s_price) == parseInt(sf_price) && index != index2){
					count++;
			   }
			   if(index!=index2 && parseInt(mb_price) > parseInt(m_price) && parseInt(sf_price) <= parseInt(s_price)){
				   count++;
			   }

			   if ($("#edit_isPercentage"+index).val() == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
				   count_higher++;
			   }
			   
			   if ($("#edit_isPercentage"+index2).val() == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
				   count_higher++;
			   }
			   
            }
            i++;
            
        });

        if(count>0){
			console.log(count);
			err_counts++;
			console.log(err_counts + '_i');
			if (err_counts > 0) {
				row.find(".sf_price_error_edit span").remove();
            	row.find(".sf_price_error_edit").append("<span>Higher minimum spend should have higher shipping discount amount.</span>");
				$("#btnTierEdit").prop("disabled", true);
			}
        } else {
			$("#btnTierEdit").prop("disabled", false);
			$(".sf_price_error_edit span").remove();
			console.log(err_counts + '_j');
		}

		if(count_higher > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error_edit span").remove();
            	row.find(".sf_price_error_edit").append("<span>Discount amount is not applicable.</span>");
				$("#btnTierEdit").prop("disabled", true);
			} else {
				row.find(".sf_price_error_edit span").remove();
			}
        } else {
			$("#btnTierEdit").prop("disabled", false);
			console.log(err_counts + '_zz');
		}
		
		if (err_counts === 0) {
			row.find(".sf_price_error_edit span").remove();
			edit_save1 = true;
			edit_save2 = true;
		} else {
			edit_save2 = false;
		}

	});

	$("#table-grid-edit").on("change", ".sfd_input_edit", function() {
        
        var row = $(this).closest("tr");
        var index = row.attr('rowIndex');
        let mb_price = row.find(".m_input_edit").val();
        let err_counts = 0;
        var i = 0;
        let counter_higher = 0;

        
        $('#table-grid-edit tr').each(function(row2, tr){

            if(i > 0){

                let m_price = $(this).find("td:eq(0) input[type='text']").val();
                var index2=$(tr).attr("rowIndex");
                var row2 = $(this).closest("tr");
        
                if(row.find(".edit_fsBtn").is(':checked') && index!=index2 && parseInt(m_price) > parseInt(mb_price)){
                    counter_higher++;
                }

                if(row2.find(".edit_fsBtn").is(':checked') && index!=index2 && parseInt(m_price) < parseInt(mb_price)){
                    counter_higher++;
                    
                }

            }
            i++;
            
        });

        if(counter_higher > 0) {
            err_counts++;
            if (err_counts > 0) {
                row.find(".m_price_error_edit span").remove();
                row.find(".m_price_error_edit").append("<span>Free shipping should apply for the highest Minimum Cart Amount.</span>");
				$("#btnTierEdit").prop("disabled", true);
            }
        }else {
			if (save1 === true) {
				$(".m_price_error span").remove();
			}
			$("#btnTierEdit").prop("disabled", false);
            console.log(err_counts + '_k');
        }

        if (err_counts === 0) {
            edit_save3 = true;
        } else {
            edit_save3 = false;
        }

    });

	$("#table-grid-edit").on("change", ".edit_select_element", function() {

		var row = $(this).closest('tr');
		let percent = $(this).val();
		var index = row.attr("rowIndex");
		let sf_price = row.find(".sf_input").val();
		let i = 0;
		let count_higher = 0;
		var err_counts = 0;

		$('#table-grid tr').each(function (row2, tr) {

            if(i > 0){

                let s_price = $(this).find("td:eq(1) input[type='text']").val();

				if (percent == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

				if (percent == 1 && parseInt(sf_price) >= 100 && parseInt(s_price) >= 100) {
					count_higher++;
				}

            }
            i++;
            
        });

		if(count_higher > 0){
			err_counts++;
			if (err_counts > 0) {
				row.find(".sf_price_error_edit span").remove();
            	row.find(".sf_price_error").append("<span>Discount amount is not applicable.</span>");
				$("#btnTier").prop("disabled", true);
			} else {
				row.find(".sf_price_error_edit span").remove();
			}
        } else {
			$("#btnTier").prop("disabled", false);
			console.log(err_counts + '_zzz');
		}

		if (err_counts === 0) {
			row.find(".sf_price_error_edit span").remove();
			edit_save2 = true;
		} else {
			edit_save2 = false;
		}

	});

	$(document).delegate('.edit-delete-record', 'click', function () {
		var index = $(this).data('value');
		var value = $(this).val();

		$('.edit_product_tr_' + index).remove();

		$(".error_tier_edit span").remove();
		document.getElementById("btnTierEdit").disabled = false;


	});

	$('#editSfd').click(function (e) {
		e.preventDefault();

		var form = $('#form_promoprod_edit');
		var form_data = new FormData(form[0]);
		
		if (edit_save1 && edit_save2 && edit_save3) {
			$.ajax({
				type: form[0].method,
				url: base_url + 'promotion/Main_promotion/updateShippingDiscount',
				data: form_data,
				contentType: false,
				cache: false,
				processData: false,
				dataType: 'json',
				success: function (data) {
					$.LoadingOverlay("hide");
	
					if (data.success) {
						$("#edit_modal").modal("hide");
						showCpToast("success", "Updated!", data.message);
						setTimeout(function() {location.reload()}, 2500);
					} else {
						showCpToast("warning", "Warning!", data.message);
					}
				},
				error: function (error) {
					showCpToast("error", "Error!", error);
				}
			});
		} else {
			showCpToast("warning", "Warning!", "Check all shipping fee condition error.");
		}


	});

	$(document).delegate('.edit-delete-record', 'click', function () {
		var index = $(this).data('value');
		var value = $(this).val();

		$('.edit_product_tr_' + index).remove();

		subsidize = false;
		free = false;

	});

	let save_id = "";
	let unsaved_id = "";

	$('#unsetFeadutedModal').on('show.bs.modal', function (e) {
		var id = $(e.relatedTarget).attr('data-id');
		$(this).find('#id').val(id);
		unsaved_id = id;
	});


	$('#setFeadutedModal').on('show.bs.modal', function (e) {
		var id = $(e.relatedTarget).attr('data-id');
		$(this).find('#id').val(id);
		saved_id = id;

	});

	$('#saveFeatureConfirm').click(function () {
		var id = saved_id;

		$.LoadingOverlay("Show");
		$.ajax({
			type: 'post',
			url: base_url + "promotion/Main_promotion/sf_set_to_all",
			dataType: "html", //  https://www.php.net/manual/en/function.date-default-timezone-set.php
			data: {
				'id': id,

			},
			success: function (data) {
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if (json_data.success) {
					$('#setFeadutedModal').modal('hide');
					showCpToast("success", "Added!", "Shipping Fee Discount set to all successfully.");
					setTimeout(function () {
						location.reload()
					}, 2000);
				} else {
					showCpToast("warning", "Warning!", json_data.message);
					$('#setFeadutedModal').modal('hide');
				}

			},
			error: function (error) {
				showCpToast("error", "Error!", "Something went wrong. Please try again.");
			}
		});


	});


	$('#unsaveFeatureConfirm').click(function () {

		var id = unsaved_id;

		$.LoadingOverlay("Show");
		$.ajax({
			type: 'post',
			url: base_url + "promotion/Main_promotion/sf_unset_to_all",
			dataType: "html", //  https://www.php.net/manual/en/function.date-default-timezone-set.php
			data: {
				'id': id,

			},
			success: function (data) {
				$.LoadingOverlay("hide");
				var json_data = JSON.parse(data);
				if (json_data.success) {
					$('#unsetFeadutedModal').modal('hide');
					showCpToast("success", "Removed!", "Shipping Fee Discount unset to all removed successfully.");
					setTimeout(function () {
						location.reload()
					}, 2000);
				} else {
					showCpToast("warning", "Warning!", json_data.message);
					$('#unsetFeadutedModal').modal('hide');
				}

			},
			error: function (error) {
				showCpToast("error", "Error!", "Something went wrong. Please try again.");
			}
		});


	});

	let saved_id_promo = "";
    let unsaved_id_promo = "";

    $('#unsetFeadutedModalPromo').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).attr('data-id');
        $(this).find('#id').val(id);
        unsaved_id_promo = id;
    });


    $('#setFeadutedModalPromo').on('show.bs.modal', function (e) {
        var id = $(e.relatedTarget).attr('data-id');
        $(this).find('#id').val(id);
        saved_id_promo = id;

    });

    $('#saveFeatureConfirmPromo').click(function(){
        var id = saved_id_promo;

        $.LoadingOverlay("Show");
            $.ajax({
                type: 'post',
                url: base_url+"promotion/Main_promotion/ct_set_shouldered_by",
                dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
                data:{
                    'id': id,
    
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    if(json_data.success){
                        $('#setFeadutedModal').modal('hide');
                        showCpToast("success", "Updated!", "Shouldered by set up successfully update.");
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
                                       
    
    });
    
    
    $('#unsaveFeatureConfirmPromo').click(function(){
    
        var id   = unsaved_id_promo;

        $.LoadingOverlay("Show");
        $.ajax({
            type: 'post',
            url: base_url+"promotion/Main_promotion/ct_unset_shouldered_by",
            dataType: "html",    //  https://www.php.net/manual/en/function.date-default-timezone-set.php
            data:{
                'id': id,
    
            },
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                if(json_data.success){
                    $('#unsetFeadutedModal').modal('hide');
                    showCpToast("success", "Updated!", "Shouldered by set up successfully update.");
                    setTimeout(function(){location.reload()}, 2000);
                }
                else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
                    $('#unsetFeadutedModal').modal('hide');
                }
               
            },
            error: function(error){
                //sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });
    
    
    });


});
