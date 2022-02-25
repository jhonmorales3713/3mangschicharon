$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var current_fs, next_fs, previous_fs; //fieldsets
var opacity;

$(".next").click(function(){

current_fs = $(this).parent();
next_fs = $(this).parent().next();

//Add Class Active
$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

//show the next fieldset
next_fs.show();
//hide the current fieldset with style
current_fs.animate({opacity: 0}, {
step: function(now) {
// for making fielset appear animation
opacity = 1 - now;

current_fs.css({
'display': 'none',
'position': 'relative'
});
next_fs.css({'opacity': opacity});
},
duration: 600
});
});

$(".previous").click(function(){

current_fs = $(this).parent();
previous_fs = $(this).parent().prev();

//Remove class active
$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

//show the previous fieldset
previous_fs.show();

//hide the current fieldset with style
current_fs.animate({opacity: 0}, {
step: function(now) {
// for making fielset appear animation
opacity = 1 - now;

current_fs.css({
'display': 'none',
'position': 'relative'
});
previous_fs.css({'opacity': opacity});
},
duration: 600
});
});

$('.radio-group .radio').click(function(){
$(this).parent().find('.radio').removeClass('selected');
$(this).addClass('selected');
});

$(".submit").click(function(){
return false;
})


$(".btn-dismiss").click(function(){
	$.ajax({
        type:'post',
        url: base_url+'Tips/turn_off',
        data:{"user_id":$(this).data('value')},
        beforeSend:function(data){
            $.LoadingOverlay("show");
        },
        success:function(data){
            $.LoadingOverlay("hide");
            $(".btn-save").prop('disabled', false); 
            $(".btn-save").text("Save");
            if (data.success == 1) {
                // location.reload();
                // messageBox(data.message, 'Success', 'success');
                showCpToast("success", "Success!", data.message);
                setTimeout(function(){location.reload()}, 2000);
            }else{
                //messageBox(data.message, 'Warning', 'warning');
                showCpToast("warning", "Warning!", data.message);
            }
        }
    });
})
});