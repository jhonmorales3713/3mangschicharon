$(function () {
	var base_url       = $("body").data("base_url"); //base_url came from built-in CI function base_url();
	var token          = $("#token").val();
	var shop_id        = $("body").data("shop_id");
	var url_ref_num = $('#url_ref_num').val();
	
    var fillDataTableProducts = function(reference_num) {
        dataTable = $('#table-item').DataTable({
            destroy: true,
            "serverSide": true,
            "searching": false,
             "columnDefs": [
                { "orderable": false, "targets": [ 0 ], "className": "text-center" },
                { "targets": [ 3, 4 ], "className": "text-right" }
            ],
            responsive: true,
            "ajax":{
                url:base_url+"admin/Main_orders/order_item_table", // json datasource
                type: "post",  // method  , by default get
                data: {
                    'reference_num': reference_num 
                },
                beforeSend:function(data){
                    $.LoadingOverlay("show"); 
                },
                complete: function(data) {  
                    $.LoadingOverlay("hide"); 
                },
                error: function(){  // error handling
                    $.LoadingOverlay("hide"); 
                    $(".table-grid-error").html("");
                    $("#table-grid").append('<tbody class="table-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
                    $("#table-grid_processing").css("display","none");
                }
            }
        });
    }
    $('#processBtn').click(function(e){
        loadinfo();

    });
    $('#readyforpickupBtn').click(function(e){
        loadinfo();
    });
    $('#fulfillmentBtn').click(function(e){
        loadinfo();
    });


    $('#confirmedBtn').click(function(e){
        loadinfo();
    });
    $("#shipping_partner").change(function(){
        if($(this).val()==""){
            $(".note_notinternal").css('display','none');
        }else{
            $(".note_notinternal").css('display','block');
        }
    });
    function loadinfo(){
        ref_num = $(this).data('value');
        $.LoadingOverlay("show");
        $('#order_id').val(ref_num);
         $('.id').val(ref_num);
         $('.header_ref').html('Processing Order for Ref # '+ $('#tm_order_reference_num').html());
        $('.order_date').html(' '+ $('#tm_order_date').html());
        $('.order_reference_num').html(' '+$('#tm_order_reference_num').html());
        $('.amount').html(' '+$('#tm_amount').html());
        $('.order_status').html(' '+$('#tm_order_status').html());
        $('.payment_date').html(' '+$('#tm_payment_date').html());
        $('.payment_ref_num').html(' '+$('#tm_payment_ref_num').html());
        $('.payment_status').html(' '+$('#tm_payment_status').html());
        $.LoadingOverlay("hide");
        $('#order_modal').modal();
        $(".note_notinternal").css('display','none');
    }

    

    $('#form_save_process,#form_save_ready_pickup,#form_save_fulfillment_modal,#form_save_delivery_confirmed').submit(function(e){
        e.preventDefault();
        $.LoadingOverlay("show");
        var form = $(this);
        var form_data = new FormData(form[0]);
        var url='';
        if(form[0].id == 'form_save_process'){
             url = base_url+"admin/Main_orders/processOrder";
        }else if(form[0].id == 'form_save_ready_pickup'){
            url= base_url+"admin/Main_orders/readyfordeliveryOrder";
        }else if(form[0].id == 'form_save_fulfillment_modal'){
            url= base_url+"admin/Main_orders/fulFillOrder";
        }else if(form[0].id == 'form_save_delivery_confirmed'){
            url= base_url+"admin/Main_orders/confirmOrder";
        }
        
        $.ajax({
            type: form[0].method,
            url: url,
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    $('#order_modal').modal('hide');
                    sys_toast_success(json_data.message);
                    //showCpToast("success", "Success!", json_data.message);
                    // window.location.assign(base_url+"Main_orders/orders_view/"+token+'/'+url_ref_num);
                    setTimeout(function(){location.reload()}, 2000);
                }else{
                    //showCpToast("warning", "Warning!", json_data.message);
                     sys_toast_warning(json_data.message);
                    //setTimeout(function(){location.reload()}, 2000);
                    // setInterval(
                    //     function(){ 
                    //         location.reload(); 
                    //     },
                    // 2000);
                }
            },
            error: function(error){
                sys_toast_error('Something went wrong. Please try again.');
                //showCpToast("error", "Error!", 'Something went wrong. Please try again.');
            }
        });

    });

   
    $(document).on('change', '#order_attachment', function(){
        let count = 1;
        let error = 0;
        let allowed_extension = ['image/jpg','image/jpeg','image/png'];
        $('#img-upload-preview').html(''); // clear img preview every change
    
        $.each(this.files, function(i, val){
        });
    
        // check allowed files extesion
        $.each(this.files, function(i, val){
          
          if(!allowed_extension.includes(val.type) || val.type == ''){
            //console.log(val.type);
            sys_toast_error( 'Only jpeg, jpg and png are allowed to upload.');
            error += 1;
          }else 
          // check filesize
          if(parseFloat(val.size) / 1024 > 1024){
            //messageBox(val.name+' file size is to large.', 'Warning', 'warning');
            sys_toast_error( 'Please upload an Image with a valid size no larger than 2MB');
            //showCpToast("warning", "Note",'Please upload an Image with a valid size no larger than 2MB');
            error += 1;
            // this.files.splice(i,1);
          }
          // else
          // if($.inArray(val.name.split('.').pop().toLowerCase(), allowed_extension) == -1){
          //   //messageBox(val.name+' file format is not allowed.', 'Warning', 'warning');
          //   showCpToast("warning", "Note",'Only jpeg, jpg and png are allowed to upload.');
          //   error += 1;
          //   // this.files.splice(i,1);
          // }
        });
    
        if(error == 0){
          $.each(this.files, function(i, val){
    
            $('#img-upload-preview').append(
              `
                <div class="col-md-4 mt-2">
                  <div class="img-thumbnail mb-2" style = "min-height:119px;">
                    <img src="" alt="" width=100% class = "img-uploads" id = "img_${count}" />
                  </div>
                </div>
              `
            );
    
            readURL(val,'#img_'+count);
            count++;
          });
        }else{
          $(this).val("");
        }
    
      });
      function readURL(input,target) {
        if (input) {
          var reader = new FileReader();
      
          reader.onload = function(e) {
            $(target).attr('src', e.target.result);
          }
      
          reader.readAsDataURL(input); // convert to base64 string
        }
      }
      
    fillDataTableProducts(url_ref_num);
});