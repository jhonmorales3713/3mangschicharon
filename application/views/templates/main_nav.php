<link rel="stylesheet" href="<?=base_url('assets/css/libs/nav_styles.css')?>">

<input type="hidden" id="active_page" value="<?= $active_page; ?>">
    
</div>

<div id="navbar">    

    <div class="row">
        <div class="col-4">            
            <img class="shop-icon" src="<?= base_url('assets/img/favicon.png'); ?>" alt="">              
        </div>
        <div class="col-8">
            <div id="menu_btn_container">
                <button class="btn btn-light m5" id="menu_btn">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </button>        
            </div>
            <div class="nav-container">
                <center>  
                <a href="<?= base_url(); ?>" class="nav-home">HOME</a>
                <a href="<?= base_url('shop'); ?>" class="nav-shop">SHOP NOW</a>
                <a href="<?= base_url('about'); ?>" class="nav-about">ABOUT US</a>        
                <a href="<?= base_url('contact_us'); ?>" class="nav-contact_us">CONTACT US</a>      
                <a href="<?= base_url('admin'); ?>">ADMIN</a>  
                <a href="<?= base_url('signup'); ?>">Login/Sign Up</a>  
                </center>  
            </div>              
        </div>
    </div>

      

      
        
</div>


<script>

    //active page
    var active_page = $('#active_page').val();
    $('.nav-'+active_page).addClass('active');

    //navigation bar on scroll
    window.onscroll = function() {navigation()};

    var navbar = document.getElementById("navbar");
    var sticky = navbar.offsetTop;

    function navigation() {
        if (window.pageYOffset >= 60) {
            navbar.classList.add("sticky");
            $('#navbar').css('background-color','white');
            $('#navbar').addClass('shadow');            
            $('#shop_logo').show();
            $('#home_logo').css('display','inline');
            $('#menu_btn_container').css('background-color','rgba(255,255,255,0.9)');
        } else {
            navbar.classList.remove("sticky");            
            $('#navbar').css('background-color','white');
            $('#navbar').removeClass('shadow');            
            $('#shop_logo').hide();        
            $('#home_logo').css('display','none');
            $('#menu_btn_container').css('background-color','none');
        }
    }
    //menu btn event
    $('#menu_btn').on('click',function(){  
        var container = $('.nav-container');
        $('#navbar a').css('color','#333');
        if($(container).is(':visible')){
            $('.nav-container').slideUp(300);            
        }
        else{
            $('.nav-container').slideDown(300);
        }
        
    });
</script>