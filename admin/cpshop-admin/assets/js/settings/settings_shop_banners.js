$(document).ready(function(){

    var base_url = $("body").data('base_url');
    
    let file_names = [];
    let to_delete = [];
    let sorting = [];

    setDraggables();

    set_remove_btn();

    function setDraggables(){
        $("#sortable").sortable();
        $("#sortable").disableSelection();        
    }    

    let count = 0;
    let upload_count = 0;
    let update_count = 0;
    let delete_count = 0;

    var exists = $('li.exists');
    count = exists.length;     

    if(exists.length > 0){
        for(x=0; x<exists.length; x++){
            var el = $(exists[x]);
            var fl = $(el).children('p');
            var fn = $(fl[0]).text();
            file_names.push(fn);
        }        
    }

    $('#dropzone').on("dragover", function(e){       
        e.preventDefault();        
        $(this).css("border","2px solid #808080");
    });

    $('#dropzone').on("dragenter", function(e){       
        e.preventDefault();        
        $(this).css("border","2px solid #808080");
    });

    $('#dropzone').on("dragleave", function(e){       
        e.preventDefault();    
        $(this).css("border","2px dashed #808080");
    });

    $('#dropzone').on("drop", function(e){
        $(this).css("border","2px dashed #808080");

        if(e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files.length) {
            
            e.preventDefault();
            e.stopPropagation();

            var files = e.originalEvent.dataTransfer.files;                
            
            if((Array.from(files)).length){                
                
                Array.from(files).forEach(previewImages);
                set_remove_btn();

            }
            else{
                showCpToast("info", "Upload Limit!", "Only 6 files are allowed");
            }
        }

    });

    $('#dropzone input').on("change", function(){
        var input = document.querySelector("#dropzone input");
        var files = input.files;    
            if(files.length){                
                Array.from(files).forEach(previewImages);
                
                set_remove_btn();

                $('#sortable li').click(function(){                        
                    var parent = $(this);
                    var form = parent.find("form");                    
                    var input = form.find("input");
                    var file = input[0].files;
                });

            }
            // else{
            //     showCpToast("info", "Upload Limit!", "Only 6 files are allowed");
            // }
        $("#banner_images").val("");
    });    

    $("#banner_images").on('change',function(){
        // console.log('asdjdjdj');
    });

    function previewImages(item, index){
        let counts = 0;
        if(item.type.includes("image")){
            if(item.size < 10000000){            
                if(!hasExtension(item.name, ['.jpg', '.png','.JPG','.PNG','.jpeg','.JPEG'])){
                    sys_toast_warning('Invalid file type. Only JPG/PNG are allowed');
                }
                else if(!checkFileNames(item.name)){
                    counts++;
                    var item_name = item.name.replace(/\s+/g, '').toLowerCase()+Math.floor(Math.random() * 10000);
                    var item_name = item_name.replace(/[^A-Z0-9]+/ig, "_");
                    var id = (item_name).replace(/\.[^/.]+$/,"")+Math.floor(Math.random() * 10000);
                    var id = id.replace(/[^A-Z0-9]+/ig, "_");
                    $('#dropzone p').text('Uploading ' + counts + ' banners');
                    
                    $('#sortable').append(generatePreview(item.name,id));                    
                    setImage(item,id);
                    file_names.push(item.name);                                       
                }
                else{
                    showCpToast("warning", "Warning!", 'File already exist');
                }            
            }
            else{
                showCpToast("info", "Upload Limit!", "Only 6 files are allowed");
            } 
        } else {
             showCpToast("info", "Invalid File!", "Only image files are accepted (e.g. JPG PNG)");
        }
    }    

    function generatePreview(image,id){
        htmlString = '<li id="' + id + '">'                                      
                   + '<button class="remove-btn">REMOVE BANNER</button>'
                   + '<p>' + image + '</p>'                   
                   + '<form action="" enctype="multipart/form-data">'
                   + '<input type="file" name="file" style="display: none;">' 
                   + '</form>'
                   + '</li>';
        return htmlString;        
    }      

    function checkFileNames(filename){
        return file_names.includes(filename);
    }

    function setImage(file,id) {        
        var reader = new FileReader();
        
        reader.onload = function(e) {
          $('li#'+id).css('background', 'url("' + e.target.result + '")');          
        }        
        var parent = $("li#"+id);
        var form = parent.find("form");
        var input = form.find("input");

        var list = new DataTransfer();
        list.items.add(file);

        input[0].files = list.files;
        
        reader.readAsDataURL(file);
    }
    
    function checkSorting(){
        sorting = [];
        
        var image_list = $('#sortable').children();        
        
        for(a=0; a<image_list.length; a++){
            getInfo(image_list[a]);
        }                
    }
    
    function set_remove_btn(){
        $("#sortable .remove-btn, #unsortable .remove-btn").click(function(){                        
            file_names.splice(file_names.indexOf($(this).next().text()),1);                    
            if(count == 0){
                count = 0;
                $('#dropzone p').text('Drag your banners here to upload. Maximum of 6'); 
            }
            else{
                count--;
                // $('#dropzone p').text('Upload ' + count + ' out of ' + count); 
                if($(this).parent().hasClass('exists')){
                    to_delete.push($(this).next().text());
                }
            }          
            $(this).parent().remove();     
        });
    }

    function getInfo(item){
        var id = item.id;
        var file_name = $("#" + id + " p").html();
        sorting.push(file_name);
    }

    $('#save').click(function(){
        $('#confirm_modal').modal("show");
    });

    $('#upload_confirm_btn').click(function(){
        $('#confirm_modal').modal("hide");
        checkSorting();

        var list = $('#sortable li');

        for(x=0; x<list.length; x++){           
            if($(list[x]).hasClass("exists")){
                var p = $(list[x]).children("p");
                var filename = $(p).text();
                var sort_num = (sorting.indexOf(filename)) + 1;
                update_sorting(filename,sort_num);
                update_count++;
            }
            else{               
                var form = $(list[x]).children("form");
                var formData = new FormData(form[0]);
                var p = $(list[x]).children("p");
                var filename = $(p).text();
                var id = (filename).replace(/\s+/g, '');
                var id = id.replace(/[^A-Z0-9]+/ig, "_");
               console.log(id);
                var sort_num = (sorting.indexOf(filename)) + 1;
                upload_banner(formData,sort_num,id);
                $('li#'+id).addClass("exists");
                upload_count++;
            }   
            if(x == list.length-1){
                $.LoadingOverlay("show");
            }
        }

        if(to_delete.length > 0){
            delete_count = to_delete.length;
            delete_banners();
        }        
    });    
    
    function upload_banner(formData,sort_num,id){
        $.LoadingOverlay("show");
        formData.append('sorting',sort_num);
        console.log(id);
        $.ajax({
            type: 'POST',
            url: base_url + 'settings/Shop_banners/upload_banner',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'JSON',
            success: function(data,status){
                if(status){
                    if(data.status){                                                
                        $('li#'+data.id).addClass("exists");                        
                        if(upload_count > 0){
                            upload_count--;
                        }      
                            showCpToast("success", "Uploaded!", "Files Successfully Uploaded!");
                            setTimeout(function(){location.reload()}, 2000);
                            $.LoadingOverlay("hide");
                    }      
                    else {
                        showCpToast("warning", "Warning!", data.error.error);
                        $.LoadingOverlay("hide");

                    }              
                }
            }
        });
    }

    function update_sorting(filename,sorting){
        $.ajax({
            type: 'POST',
            url: base_url + 'settings/Shop_banners/update_sorting',
            data: {
                filename: filename,
                sorting: sorting
            },                        
            dataType: 'JSON',
            success: function(data,status){
                if(status){
                    $.LoadingOverlay("hide"); 
                    if(data.status){       
                        if(update_count > 0){
                            update_count--;
                        }
                        if(update_count == 0){    
                            
                            showCpToast("success", "Updated!", "Shop Banner has been updated!");
                            setTimeout(function(){location.reload()}, 2000);
                            $.LoadingOverlay("hide");  
                            reset_variables();
                        }
                        
                    }                    
                }
            }
        });
    }

    
    function delete_banners(){
        $.LoadingOverlay("show"); 
        $.ajax({
            type: 'POST',
            url: base_url + 'settings/Shop_banners/delete_banners',
            data: {
                to_delete: to_delete
            },                        
            dataType: 'JSON',
            success: function(data,status){
                if(status){
                    if(data.status){                                                                     
                        $.LoadingOverlay("hide"); 
                        showCpToast("warning", "Deleted!", "Shop Banner has been deleted!");
                        setTimeout(function(){location.reload()}, 2000);
                        $.LoadingOverlay("hide");                    
                    }                    
                }
            }
        });
    }

    function reset_variables(){
        to_delete = [];
        sorting = [];
        count = 0;       
        
    }   
    
    function hasExtension(file, exts) {
        var fileName = file;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }




    $('#myFuture').on('show.bs.modal', function (e) {
        var myRoomNumber = $(e.relatedTarget).attr('data-id');
        $('#BannerId').val(myRoomNumber)
    });


    $("#CloseBtnLink").click(function(){
        $('#BannerLink').val('');
    });

    $("#AddlinkBtn").click(function(){
       BannerID = $('#BannerId').val()
       BannerLink = $('#BannerLink').val()

       $.LoadingOverlay("show");

       if(BannerLink != ''){
           $.ajax({
               type: 'post',
               url: base_url+"settings/Shop_banners/add_linkBanner",
               data:{
                   'BannerID': BannerID,
                   'BannerLink':BannerLink
               },
               success:function(data){
                   $.LoadingOverlay("hide");
                   var json_data = JSON.parse(data);
                   if(json_data.success){
                       $('#myFuture').modal('hide');
                       $('#BannerLink').val('');
                       showCpToast("success", "Added!", "Link has been Added to Banner.");
                       setTimeout(function(){location.reload()}, 2000);
                   }
                   else{
                       showCpToast("warning", "Warning!", json_data.message);
                       $('#myFuture').modal('hide');
                   }

               },
               error: function(error){
                   showCpToast("error", "Error!", 'Something went wrong. Please try again.');
               }
           });
        }else{
            $.LoadingOverlay("hide");
            showCpToast("warning", "Warning!", 'Please enter a link.');
        }

    });

    $("#setPostSchedule").on("shown.bs.modal", function(e) {
        $("#SchedBannerId").val($(e.relatedTarget).attr('data-id'));
        $("#date_from").val($(e.relatedTarget).attr('data-start-date'));
        $("#time_from").val($(e.relatedTarget).attr('data-start-time'));
        $("#date_to").val($(e.relatedTarget).attr('data-end-date'));
        $("#time_to").val($(e.relatedTarget).attr('data-end-time'));   
    });

    $("#setScheduleBtn, #UpdatesetScheduleBtn").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: base_url + 'settings/Shop_banners/setSchedPost_Banner',
            data: {
                banner_id: $("#SchedBannerId").val(),
                dateFrom: $("#date_from").val(),
                timeFrom: $("#time_from").val(),
                dateTo: $("#date_to").val(),
                timeTo: $("#time_to").val()
            },
            dataType: 'json',
            success: function(respo) {
                if (respo.statusCode == 201) {
                    $("#setPostSchedule, #UpdatesetPostSchedule").modal("hide");
                    showCpToast("success", "Success!", respo.statusText);
                    setTimeout(function(){location.reload()}, 2000);
                } else {
                    showCpToast("warning", "Warning!", respo.statusText);
                    $('#setPostSchedule').modal('hide');
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    });


    $('#UpdateLink').on('show.bs.modal', function (e) {
        var bannerID = $(e.relatedTarget).attr('data-id');
        var bannerLink = $(e.relatedTarget).attr('data-banner');
        $('#UpdateBannerId').val(bannerID);
        $('#UpdateBannerLink').val(bannerLink);
       
        
    });


    $("#UpdatelinkBtn").click(function(){
        BannerID = $('#UpdateBannerId').val()
        BannerLink = $('#UpdateBannerLink').val()

        $.LoadingOverlay("show");

        if(BannerLink != ''){
            $.ajax({
                type: 'post',
                url: base_url+"settings/Shop_banners/add_linkBanner",
                data:{
                    'BannerID': BannerID,
                    'BannerLink':BannerLink
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    var json_data = JSON.parse(data);
                    if(json_data.success){
                        $('#UpdateLink').modal('hide');
                        $('#BannerLink').val('');
                        showCpToast("success", "Updated!", "Link banner has been Updated.");
                        setTimeout(function(){location.reload()}, 2000);
                    }
                    else{
                        showCpToast("warning", "Warning!", json_data.message);
                        $('#UpdateLink').modal('hide');
                    }
 
                },
                error: function(error){
                    showCpToast("error", "Error!", 'Something went wrong. Please try again.');
                }
            });
         }else{
             $.LoadingOverlay("hide");
             showCpToast("warning", "Warning!", 'Please enter a link.');
         }
     
    });

    $("#deactModal").on("shown.bs.modal", function(e) {
        $("#deactId").val($(e.relatedTarget).attr('data-id'));
    });

    $("#activationModal").on("shown.bs.modal", function(e) {
        $("#activationId").val($(e.relatedTarget).attr('data-id'));
    });

    $(document).on("click", "#deactivate", function() {
        $.ajax({
            type: "post",
            url: base_url + 'settings/Shop_banners/deact_banner',
            data: { id: $("#deactId").val() },
            dataType: 'json',
            success: function(respo) {
                $("#deactModal").modal("hide");
                if (respo.success) {
                    showCpToast("success", "Deactivated!", respo.message);
                    setTimeout(function(){location.reload()}, 2000);
                } else {
                    showCpToast("warning", "Warning!", respo.message);
                }
            },
            error: function(err) {
                console.log(err);
            }
        })
    });

    $(document).on("click", "#activate", function() {
        $.ajax({
            type: "post",
            url: base_url + 'settings/Shop_banners/activate_banner',
            data: { id: $("#activationId").val() },
            dataType: 'json',
            success: function(respo) {
                $("#activationModal").modal("hide");
                if (respo.success) {
                    showCpToast("success", "Activated!", respo.message);
                    setTimeout(function(){location.reload()}, 2000);
                } else {
                    showCpToast("warning", "Warning!", respo.message);
                }
            },
            error: function(err) {
                console.log(err);
            }
        })
    });

    // for cronjob testing only  -- start
    // function test() {
    //     $.ajax({
    //         type: "post",
    //         url: base_url + 'settings/Shop_banners/shop_banner_functionality',
    //         dataType: 'json',
    //         success: function() {
    //             console.log('done!');
    //         },
    //         error: function(respo) {
    //             console.log(respo.statusText);
    //         }
    //     });
    // }

    // setInterval(function() {test();}, 20000);
    // for cronjob testing only  -- end

    
});