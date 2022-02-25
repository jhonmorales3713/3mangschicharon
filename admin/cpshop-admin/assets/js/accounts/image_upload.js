function readURL(input,target) {
  if (input) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $(target).attr('src', e.target.result);
    }

    reader.readAsDataURL(input); // convert to base64 string
  }
}


$(function(){
  var base_url = $("body").data('base_url');
  var s3bucket_url = $("body").data('s3bucket_url');

  $(document).on('change', '#billing_attachment', function(){
    let count = 1;
    let error = 0;
    let allowed_extension = ['jpeg', 'jpg', 'png'];
    $('#img-upload-preview').html(''); // clear img preview every change

    // check allowed files extesion
    $.each(this.files, function(i, val){
      if($.inArray(val.name.split('.').pop().toLowerCase(), allowed_extension) == -1){
        //messageBox(val.name+' file format is not allowed.', 'Warning', 'warning');
        showCpToast("warning", "Warning!", val.name+' file format is not allowed.');
        error += 1;
        // this.files.splice(i,1);
      }
    });

    // check filesize
    $.each(this.files, function(i, val){
      if(parseFloat(val.size) / 1024 > 1024){
        //messageBox(val.name+' file size is to large.', 'Warning', 'warning');
        showCpToast("warning", "Warning!", val.name+' file size is to large.');
        error += 1;
        // this.files.splice(i,1);
      }
    });

    if(error == 0){
      $.each(this.files, function(i, val){

        $('#img-upload-preview').append(
          `
            <div class="col-md-4">
              <div class="img-thumbnail mb-2" style = "min-height:119px;">
                <img src="" alt="" class = "img-uploads" id = "img_${count}" />
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

});
