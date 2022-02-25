Dropzone.autoDiscover = false;
Dropzone.autoDiscover = false;

$(document).ready(function(){
    var base_url = $("body").data('base_url');
    var shop_url = $("body").data('shop_url');

    var has_create_access =  $("#for_create_access").data('has_create_access');
    if (has_create_access == 1) {
        has_create_access = true;
    }else{
        has_create_access = false;
    }

    var has_remove_access =  $("#dropzone_div").data('has_remove_access');
    if (has_remove_access == 1) {
        has_remove_access = true;
    }else{
        has_remove_access = false;
    }

    var completed = 0;
    var arr_names = [];
    var banner_count_server = 0;
    var banner_count_client = 0;
    var total_banner_count  = 0;

    var myDropzone = new Dropzone("#myawesomedropzone", { 
        autoProcessQueue: false,
        parallelUploads: 10,
        url: base_url+'settings/shop_banners/fileupload',
        // params:{'arr_names':arr_names},
        dictDefaultMessage: "Drag your banners or click here to upload. Maximum of 6",
        dictRemoveFile: "Remove Banner",
        clickable: has_create_access,
        enqueueForUpload: true,
        maxFilesize: 1,
        uploadMultiple: false,
        addRemoveLinks: has_remove_access,
        thumbnailWidth: null,
        thumbnailHeight: null,
        maxFiles: 6,
        init: function() {
            $.ajax({
                type: 'post',
                url: base_url+'settings/shop_banners/fileupload',
                data: {request: 2},
                dataType: 'json',
                success: function(response){
                    $.each(response, function(key,value) {
                        var mockFile = { name: value.name, size: value.size };

                        myDropzone.emit("addedfile", mockFile);
                        myDropzone.emit("thumbnail", mockFile, shop_url+'assets/img/ad-banner/'+value.name);
                        myDropzone.emit("complete", mockFile);                       

                        banner_count_server = value.count;
                        total_banner_count = parseFloat(banner_count_server) + parseFloat(banner_count_client);
                    });
                }
            });
        },
        accept: function(file, done) {
            banner_count_client++;
            total_banner_count++;           
            
            // total_banner_count = parseFloat(banner_count_server) + parseFloat(banner_count_client);
            if (total_banner_count > 6) {
                done('Only (6) banners are required. \n remove this banner and click the save button.');
            }else{
                done();
            }
        },
        removedfile: function(file) {
            var name = file.name;  

            if(total_banner_count > 0){
                total_banner_count -= 1;
            }
            
            arr_names.push(name);

            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;   
        },
        queuecomplete:function (file) {
            if (completed > 0) {
                if (arr_names.length == 0) {
                    location.reload();
                }
            }
        },
        drop:function(e){
            if (has_create_access == false) {
                location.reload();
            }
        }
    });

    $('#upload_btn_banner').click(function(){
        $('#confirm_modal').modal("show");
    });

    $("#upload_confirm_btn").click(function(){
        
        completed++;        
        myDropzone.processQueue(); //trigger the dropzone

        if (arr_names.length > 0) {
            $.ajax({
                type: 'post',
                url: base_url+'settings/shop_banners/deleteupload',
                data: {'arr_names': arr_names},
                dataType: 'json',
                success: function(data){
                    location.reload();
                }
            });
        }        
    });
    
});
