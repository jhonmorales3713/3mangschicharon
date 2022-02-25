var base_url = $("body").data('base_url'); //base_url come from php functions base_url();
var edit_loadstate = true;
var infowindow = "";
get_record_details();
form_state(false);
function get_record_details(){
	var idno = $("#idno_hidden").val();
	$.ajax({
		type:'post',
		url: base_url+'Shopbranch/getrecorddetails',
		data:{'idno':idno},
		success:function(data){
			if (data.success == 1) {
				update_form(data.record_details);
			}else{
	            //messageBox(data.message,"Warning","warning");
	            showCpToast("warning", "Warning!", data.message);
			}
		}
	});
}

function update_form(record_details){
	$( "select[name*='entry-mainshop']" ).val(record_details.mainshopid);
	$( "select[name*='entry-mainshop']" ).select2().trigger('change');
	$( "input[name*='entry-branch']" ).val(record_details.branchname);
	$( "input[name*='entry-contactperson']" ).val(record_details.contactperson);
	$( "input[name*='entry-conno']" ).val(record_details.mobileno);
	$( "input[name*='entry-email']" ).val(record_details.email);
	$( "input[name*='entry-address']" ).val(record_details.address);
	$( "select[name*='entry-branch_region']" ).val(record_details.branch_region);
	$( "select[name*='entry-branch_region']" ).select2().trigger('change');
	get_city_of_region('edit', record_details.branch_city);
	set_selected('entry-city', record_details.city);
	set_selected('entry-province', record_details.province);
	set_selected('entry-region', record_details.region);
    if(record_details.isautoassign == 1){
        $("#checkbox-isautoassign").prop("checked", true);
        $("#entry-isautoassign").val(1);
    }else{
        $("#checkbox-isautoassign").prop("checked", false);
        $("#entry-isautoassign").val(0);
        taginput_state(true);
    }
    //Map Details
    $("#loc_latitude").val(record_details.latitude);
    $("#loc_longitude").val(record_details.longitude);

    //Bank Details
	$( "input[name*='entry-bankname']" ).val(record_details.bankname);
	$( "input[name*='entry-acctname']" ).val(record_details.accountname);
	$( "input[name*='entry-acctno']" ).val(record_details.accountno);
	$( "textarea[name*='entry-desc']" ).text(record_details.description);

	//Admin Settings
	$( "input[name*='entry-idnopb']" ).val(record_details.idnopb);
	$( "input[name*='entry-treshold']" ).val(record_details.inv_threshold);
    edit_loadstate = false;
}

$("#btn-edit").on("click", function(){
	 form_state(false);
	 $(this).attr('hidden', true);
	 $( "#btn-canceledit" ).attr('hidden', false);
	 //messageBox("You can now edit the form", "Note", "success");
	 showCpToast("success", "Note!", "You can now edit the form");
});

$("#btn-canceledit").on("click", function(){
	 get_record_details();
	 form_state(true);
	 $(this).attr('hidden', true);
	 $( "#btn-edit" ).attr('hidden', false);
	 //messageBox("Your changes have not been saved.", "Warning", "Warning");
	 showCpToast("warning", "Warning!", "Your changes have not been saved.");
});

$("#add-form").submit(function(e){
	e.preventDefault();
	checkbox_status();
	var thiss = $("#add-form");
	var serial = thiss.serialize();
    
	if(checkInputs("#add-form") == 0){
        $.ajax({
            type:'post',
            url: base_url+'Shopbranch/update',
            data: serial,
            beforeSend:function(data){
                $(".cancelBtn, .saveBtn").prop('disabled', true); 
                $(".saveBtn").text("Please wait...");
                $.LoadingOverlay("show");
            },
            success:function(data){
                $.LoadingOverlay("hide"); 
                $(".cancelBtn, .saveBtn").prop('disabled', false);
                $(".saveBtn").text("Save");
                if (data.success == 1) {
                    // location.reload();
                    //messageBox(data.message, "Success", "success");
                    showCpToast("success", "Success!", data.message);
					setTimeout(function(){location.reload()}, 2000);

                }else{
                    //messageBox(data.message, "Warning", "warning");
                    showCpToast("warning", "Warning!", data.message);
                }
            }
        });
	}
});

function set_selected(name, values){
	$.each(values.split(","), function(i,e){
    	$("select[name*='"+name+"'] option[value='" + e + "']").prop("selected", true).select2().trigger('change');
	});
}
