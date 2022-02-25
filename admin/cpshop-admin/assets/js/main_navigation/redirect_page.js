$(document).ready(function(){
    var countmodule = $("#checkModule").data('countmodule');
    var redirecturl = $("#checkModule").data('redirecturl');
    var labelname   = $("#checkModule").val();

    if(parseInt(countmodule) == 1){
        $.LoadingOverlay("show");
        window.location.assign(redirecturl);
    }

    console.log(countmodule);
    console.log(redirecturl);

});