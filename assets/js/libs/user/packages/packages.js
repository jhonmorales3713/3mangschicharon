$(function(){
    
    setTimeout(function(){
        $('.ceremony').addClass("gallery-img-animation3");
    },50);
    
    var venue_start=$('.venue').offset().top-750
    ,venue = $('.venue')
    ,venue_opacity=0
    ;

    var entourage_start= $('.entourage').offset().top-750
    ,entourage_end=8000
    ,entourage = $('.entourage')
    ,entourage_opacity=0
    ;

    var wedding_start= $('.all-in').offset().top-750
    ,wedding_end=8000
    ,wedding = $('.all-in')
    ,wedding_opacity=0
    ;

    $(window).bind('scroll', function(){
        var offset = $(document).scrollTop();        

        if(offset > venue_start){
            if(venue_opacity<1){            
                venue.addClass("gallery-img-animation3");
            }
        }

        if(offset > entourage_start){
            if(entourage_opacity<1){            
                entourage.addClass("gallery-img-animation3");
            }
        }

        if(offset > wedding_start){
            if(wedding_opacity<1){            
                wedding.addClass("gallery-img-animation3");
            }
        }        
    });
    
})

