var base_url = $('body').data('base_url');

function format_number(num,decimal = 0){
    if(decimal != 0){
        num = parseFloat(num).toFixed(decimal);
    }        
    if(num){
        return num.
            toString()
            .replace(/[^\d\.\,\-]/g, "")
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            ;
    }
}

function php_money(number){
    return '&#8369;' + format_number(number, 2);
}

// Display Form Errors
function show_errors(data, container){
    if ('field_errors' in data) { // Check field errors exists in response
        
        // Write errors to UI
        Object.keys(data.field_errors).forEach(function(key,index) {
            var opts = {
                'class' : 'small field_error text-danger err-'+key,
            };
            var el = container.find('#'+key);
            var er_msg = data.field_errors[key].replace(' field','');
            er_msg = er_msg.replace('The ','');
            var span_field_error = $('<span >').attr(opts).text(er_msg);

            if(el){
                var parent = el.parent();
                if(parent.is('.input-daterange, .input-group')){ // mostly used by datepicker
                    parent.addClass('has-error');
                    // var text = el.parent().next('.field_error').text();
                    // text+=data.field_errors[key]+" ";
                    // el.parent().next('.field_error').text(text);
                    container.find('#'+key).parent().after( span_field_error );
                } else if (el.is('select')) { // select2
                    if (el.hasClass('select2')) {
                        container.find('#'+key).siblings('.select2-container').addClass('has-error').after( span_field_error );
                    } else {
                        container.find('#'+key).addClass('has-error').after( span_field_error );
                    }
                } else {  // normal input fields
                    el.addClass('has-error');
                    container.find('#'+key).after( span_field_error );
                }                
            }
            if( typeof CKEDITOR !== "undefined" ){ // check if ckeditor exists in the page
                for(var instanceName in CKEDITOR.instances) { 
                    if(key == instanceName){
                        var keys = container.find('#cke_'+key+' .cke_contents');
                        keys.addClass('has-error');
                        keys.closest('#cke_'+key).after( span_field_error );
                    }
                }
            }
        });
        // focus first field with error
        var objectKeys = Object.keys(data.field_errors);
        $('html, body').animate({ scrollTop: $('#'+objectKeys[0]).offset().top - 120 }, 'slow');
        if ('#'+objectKeys[0]) $('#'+objectKeys[0]).focus();
    }
}

// Clear Form Errors
function clearFormErrors(){
    $('.has-error').removeClass('has-error');
    $('.field_error').remove();
}

function loading_screen(action){
    if(action == 'show'){
        $('.loading-screen').css('z-index','10');
        $('.loading-screen').css('opacity',1);
    }
    else if(action == 'hide'){
        $('.loading-screen').css('z-index','-1');
        $('.loading-screen').css('opacity',0);
    }
}

//loading screen on ajax - 
$(document).ajaxSend(function(){
    loading_screen('show');
});

$(document).ajaxStop(function(){
    loading_screen('hide');
});

$('#signout').click(function(){
    window.location.href = base_url + 'signout';
})
