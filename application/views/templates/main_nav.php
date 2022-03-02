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
                <a href="<?= base_url('registration'); ?>" class="nav-registration">Login/Sign Up</a>  
                </center>  
            </div>              
        </div>        
    </div>
    <div class="top-nav">
        <div class="top-nav-container">
            <?php if(isset($has_search)){ ?>
                <div class="top-nav-icon">
                    <div class="searchbar">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-sm" placeholder="Search here..." aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>  
                </div> 
            <?php } ?>
            <div class="top-nav-icon">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <b>2</b>
            </div>      
            <div class="top-nav-icon">
                <i class="fa fa-user" id="user_option" aria-hidden="true"></i>                
            </div>               
        </div>
        <div class="user-option p20">
            <span class="close" id="close_user_options">&times;</span>
            <br>
            <div class="col-12">
                
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

    function toggle_options(el_id, target_el, action){
        if(action == 'show'){
            $('#'+el_id).addClass('open');
            $(target_el).css('z-index','5');
            $(target_el).css('opacity','1');
        }
        else if(action == 'hide'){
            $('#'+el_id).removeClass('open');
            $(target_el).css('z-index','-1');
            $(target_el).css('opacity','0');
        }
    }

    //user option
    $('#user_option').click(function(){
        if($(this).hasClass('open')){
            toggle_options('user_options','.user-option','hide');
        }
        else{
            toggle_options('user_options','.user-option','show');            
        }        
    });

    $('#close_user_options').click(function(){
        toggle_options('user_options','.user-option','hide');
    });

</script>