$(document).ready(function () {
	var base_url = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var filter = {
		search: "",
		shop: "",
		from: "",
		to: "",
	};

	function gen_order_history_tbl(search, id, total_amount) {
		var order_history_tbl = $("#history_tbl").DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			searching: false,
			destroy: true,
			order: [[5, "desc"]],
			columnDefs: [
				{ responsivePriority: 1, targets: 0 },
				{ responsivePriority: 2, targets: -1 },
			],
			ajax: {
				url: base_url + "admin/Main_customers/get_order_history",
				type: "post",
				data: {
					searchValue: search,
					id,
					total_amount,
				},
				beforeSend: function () {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
                    $("#history_modal").modal();
				},
				error: function () {
					$.LoadingOverlay("hide");
				},
			},
		});
	}

	function gen_login_history_tbl(id) {
		var order_history_tbl = $("#login_history_tbl").DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			searching: false,
			searching: false,
			destroy: true,
			order: [[2, "desc"]],
			ajax: {
				url: base_url + "admin/Main_customers/get_login_history",
				type: "post",
				data: {
					id: id,
				},
				beforeSend: function () {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
				},
				error: function () {
					$.LoadingOverlay("hide");
				},
			},
		});
	}

	// start - for loading a table
	function fillDatatable() {
		var _type = $("#search_type").val();
		var _name = $("#search_name").val();
		var _city = $("#search_city").val();

		var dataTable = $("#table-grid").DataTable({
			processing: false,
			destroy: true,
			searching: false,
			serverSide: true,
			responsive: true,
			columnDefs: [
				{ targets: [3], orderable: true, sClass: "text-center" },
				{ targets: [2, 4], orderable: false },
			],
			ajax: {
				type: "post",
				url: base_url + "admin/Main_customers/get_customers", // json datasource
				data: {
					_type: _type,
					_name: _name,
					_city: _city,
				}, // serialized dont work, idkw
				beforeSend: function (data) {
					$.LoadingOverlay("show");
				},
				complete: function (data) {
					$.LoadingOverlay("hide");
					var response = $.parseJSON(data.responseText);
					if (response.data.length > 0) {
						// console.log(response);
						$(".btnExport").show(100);
					} else {
						$("#btnExport").hide(100);
					}

					$("#_search").val(JSON.stringify(this.data));
				},
				error: function () {
					// error handling
					$(".table-grid-error").html("");
					$("#table-grid").append(
						'<tbody class="table-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
					);
					$("#table-grid_processing").css("display", "none");
				},
			},
		});
	}

	fillDatatable();
	// end - for loading a table

	// start - for search purposes
	$("#search_hideshow_btn").click(function (e) {
		e.preventDefault();

		var visibility = $("#card-header_search").is(":visible");

		if (!visibility) {
			//visible
			$("#search_hideshow_btn").html(
				'&ensp; <i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i>'
			);
		} else {
			//not visible
			$("#search_hideshow_btn").html(
				'<i class="fa fa-chevron-circle-up fa-lg" aria-hidden="true"></i>'
			);
		}

		$("#card-header_search").slideToggle("slow");
	});

	$(document).on("click", "#search_clear_btn", function () {
		$("#search_name").val("");
		$('#search_city option[value=""]').prop("selected", true);
		$('#search_type option[value=""]').prop("selected", true);
		fillDatatable();
	});

	$(document).on("click", "#btnSearch", function () {
		console.log("click");
		// e.preventDefault();
		fillDatatable();
	});
	// end - for search purposes

	$("#addBtnCustomer").click(function (e) {
		e.preventDefault();

		var _fname = $("#add_fname").val();
		var _lname = $("#add_lname").val();
		var _birthdate = $("#add_birthdate").val();
		var _gender = $("#add_gender").val();
		var _mobile = $("#add_mobile").val();
		var _email = $("#add_email").val();
		var _address1 = $("#add_address1").val();
		var _address2 = $("#add_address2").val();
		var _city = $("#add_city").val();

		if (
			_fname == "" ||
			_lname == "" ||
			_birthdate == "" ||
			_gender == "" ||
			_mobile == "" ||
			_email == "" ||
			_address1 == "" ||
			_city == ""
		) {
			$.toast({
				heading: "Note",
				text: "Please fill up the required fields",
				icon: "info",
				loader: false,
				stack: false,
				position: "top-center",
				bgColor: "#FFA500",
				textColor: "white",
			});
		} else {
			$.ajax({
				type: "post",
				url: base_url + "admin/Main_customers/add_customer",
				data: {
					_fname: _fname,
					_lname: _lname,
					_birthdate: _birthdate,
					_gender: _gender,
					_mobile: _mobile,
					_email: _email,
					_address1: _address1,
					_address2: _address2,
					_city: _city,
				},
				success: function (data) {
					var data = JSON.parse(data);

					if (data.success == 1) {
						fillDatatable(); //refresh datatable

						// $.toast({
						// 	heading: "Success",
						// 	text: data.message,
						// 	icon: "success",
						// 	loader: false,
						// 	stack: false,
						// 	position: "top-center",
						// 	bgColor: "#5cb85c",
						// 	textColor: "white",
						// 	allowToastClose: false,
						// 	hideAfter: 10000,
						// });

						showCpToast("success", "Success!", data.message);
          				setTimeout(function(){location.reload()}, 2000);

						$("#addCustomerModal").modal("toggle"); //close modal
					} else {
						// $.toast({
						// 	heading: "Note",
						// 	text: data.message,
						// 	icon: "info",
						// 	loader: false,
						// 	stack: false,
						// 	position: "top-center",
						// 	bgColor: "#FFA500",
						// 	textColor: "white",
						// });
						showCpToast("info", "Note!", data.message);
					}
				},
			});
		}
	});

	$.ajax({
		type: "post",
		url: base_url + "admin/Main_customers/get_cities",
		success: function (data) {
			$.each(data, function (key, value) {
				$("#search_city").append("<option>" + value.name + "</option>");
				$("#add_city").append(
					'<option value="' + value.id + '">' + value.name + "</option>"
				);
			});
		},
	});

	// customer history
	$(document).on("click", ".btn_history", function () {
		let id = $(this).data("id");
		let total_amount = $(this).data("total_amount");
		let name = $(this).data("name");
		gen_order_history_tbl(JSON.stringify(filter), id, total_amount);
		$("#c_name").text(name);
		$("#c_total_amount").text(accounting.formatMoney(total_amount));
		$("#history_modal").modal();
	});

	$(document).on("click", ".btn_login_history", function () {
		let id = $(this).data("id");
		let name = $(this).data("name");
		gen_login_history_tbl(id);
		$("#c_name").text(name);
		$("#login_history_modal").modal();
	});
});
