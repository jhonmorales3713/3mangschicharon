let width = window.screen.width;

if(width < 768){
    $('#nav_toggle').removeClass('open');
    $('#nav_toggle').removeClass('fa-chevron-left');
    $('#nav_toggle').addClass('fa-bars');
}else{
    $('#nav_toggle').addClass('open');
    $('#nav_toggle').removeClass('fa-bars');
    $('#nav_toggle').addClass('fa-chevron-left');  
}

var active_page = $('#active_page').val();
$('.nav-'+active_page).addClass('active-link');


$(window).resize(function() {
    if (window.screen.width >= 768) { 
        $('#nav_toggle').addClass('open');
        $('#nav_toggle').removeClass('fa-bars');
        $('#nav_toggle').addClass('fa-chevron-left');  
    }
    if (window.screen.width < 768) {
        $('#nav_toggle').removeClass('open');
        $('#nav_toggle').removeClass('fa-chevron-left');
        $('#nav_toggle').addClass('fa-bars');
    }
});

$('#nav_toggle').on('click',function(){
    if($(this).hasClass('open')){
        $(this).removeClass('open');
        $(this).removeClass('fa-chevron-left');
        $(this).addClass('fa-bars');
        $('.admin-nav').css('margin-left','-250px');
        $('.main-content').css('padding-left','0');
        $('.admin-nav').css('box-shadow','none');
        $('.admin-nav').css('-webkit-box-shadow','none');
        $('.admin-nav').css('-moz-box-shadow','none');        
        if(width <= 748){
            $('.nav-cover').css('display','none');            
        }        
        
    }
    else{        
        $(this).addClass('open');
        $(this).removeClass('fa-bars');
        $(this).addClass('fa-chevron-left');  
        $('.admin-nav').css('margin-left','0');
        $('.main-content').css('padding-left','250px');                
        $('.admin-nav').css('box-shadow','3px 0 15px 0px rgba(0,0,0,0.1)');
        $('.admin-nav').css('-webkit-box-shadow','3px 0 15px 0px rgba(0,0,0,0.1)');
        $('.admin-nav').css('-moz-box-shadow','3px 0 15px 0px rgba(0,0,0,0.1)');
        if(width <= 748){
            $('.nav-cover').css('display','block');
        }
    }
});