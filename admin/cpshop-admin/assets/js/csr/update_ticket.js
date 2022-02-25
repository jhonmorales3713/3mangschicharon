$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token = $("body").data('token');

	$('#entry-form').submit(function(e){
        e.preventDefault();
        var serial = $("#entry-form").serialize();
        //var commentbox = CKEDITOR.instances.commentbox.getData();
        if(checkInputs("#entry-form") == 0){
            $.ajax({
                type:'post',
                url: base_url+'Csr/update',
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
                        // window.location.reload();
                        // messageBox(data.message, 'Success', 'success');
                        showCpToast("success", "Success!", data.message);
          				setTimeout(function(){location.reload()}, 2000);
                    }else{
                        messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
        }
    });

	function search_customer_table(){
		var search_name 	= $("input[name='search_customer']").val();
		var search_email 	= $("input[name='search_email']").val();
		
		var dataTable = $('#table-customer').DataTable({
			"processing": false,
			destroy: true,
			"serverSide": true,
			responsive: true,
			"columnDefs": [
				{ targets: 4, orderable: false, "sClass":"text-center"}
			],
			"ajax":{
				type: "post",
				url:base_url+"Csr/checkcustomer", // json datasource
				data:{'search_name':search_name, 'search_email':search_email}, // serialized dont work, idkw
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

	$("#btn-vieworderdetails").click(function(){
		$.LoadingOverlay("show");
		window.open(base_url+"Csr/vieworder/"+token+"/0-"+$("#order_refno").val());
		$.LoadingOverlay("hide");
	});

	$('#table-customer').delegate(".btn-selectcustomer", "click", function(){
		let id = $(this).data('value');
		let fullname = $(this).data('fullname');
	    $("input[name='entry-customerid']").val(id);
	    $("#customer_name").text(fullname);
	    $("#customer_modal").modal('toggle');
	});
	// $("select[name='entry-issuecat']").change(function(){
	// 	$("#order_refno").val('');
	// 	if($("select[name='entry-issuecat']").val() == 3){
	// 		$("#order_ref_section").attr('hidden', false);
	// 	}else{
	// 		$("#order_ref_section").attr('hidden', true);
	// 	}
	// });
})