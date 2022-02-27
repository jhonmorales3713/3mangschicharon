
    var base_url = $('body').data('base_url');
    var packages = []; 
    var sub_packages = []; 
    var package_groups = []; 

    var added_packages = [];

    function set_variables(p,sp,pg){
        this.packages = p;
        this.sub_packages = sp;
        this.package_groups = pg;
    }

    //package selection 
    
    function set_initial_package(package_id){
        $('#p'+package_id).prop('checked',true);
        $('#p'+package_id).trigger('change');
    }

    function set_input_event(){
        $('.package_input').on('change',function(){
            var package_id = $(this).data('package_id');
            var group_id = $(this).data('group_id');
            var selected = '';

            if($(this).is(':checked')){
                check_inputs(package_id,group_id);
                selected = package_id;
                packages.forEach(function(item){
                    if(item.id == package_id){
                        added_packages.push(item);
                    }
                });
            }
            else{                
                added_packages.forEach(function(item,index){
                    if(item.id == package_id){
                        added_packages.splice(index,1);                        
                    }
                });
                if(added_packages.length > 0){
                    selected = added_packages[0].id;
                }                
            }

            update_selected_packages();            
            update_booking_summary();              
            
            $('#package_id').val(selected);
            $('#package_id').trigger('change');

            //remove error class
            $('#package_id_array').removeClass('has-error');
            $('.err-package_id_array').remove();
        });
    }

    function check_inputs(package_id,group_id){        
        if(group_id == 4){
            added_packages = [];
            $('.g'+group_id).prop('checked',false);
            $('#p'+package_id).prop('checked',true);

            $('.g1').prop('checked',false);
            $('.g2').prop('checked',false);
            $('.g3').prop('checked',false);          
        }
        else{
            $('.g4').prop('checked',false);
            $('.g'+group_id).prop('checked',false);
            $('#p'+package_id).prop('checked',true);

            added_packages.forEach(function(item,index){
                if(item.package_group_id == group_id && item.id != package_id){
                    added_packages.splice(index,1);
                }
                if(item.package_group_id == 4){
                    added_packages.splice(index,1);
                }
            })
        }
    }

    function set_package_id_event(){
        $('#package_id').on('change',function(){
            var package_id = $(this).val();
            set_package_preview(package_id);
        });
    }    

    function update_selected_packages(){
        var selected_packages_string = '';
        if(added_packages.length > 0){
            added_packages.forEach(function(item){
                selected_packages_string += '<option value="'+item.id+'">'+item.package_name+'</option>';
            });
        }
        else{
            selected_packages_string = '<option>No selected Package yet</option>';
        }
        $('#package_id').html(selected_packages_string);
        set_package_id_event();
        $('#package_id').trigger('change');
    }

    function set_package_preview(selected_id){
        var string = '';                
        packages.forEach(function(item,index){
            if(item.id == selected_id){
                
                string += '<strong>Package Details</strong><br><hr>';
                string += '<div class="row">';
                    
                string += '<div class="col-lg-6 col-md-12 col-sm-12">';                              
                string += '<strong>'+ item.package_name +'</strong><br>'; 
                string += '<span>Php '+ format_number(parseFloat(item.price),2) +'</span><br>';       
                string += '<img src="'+base_url+'/uploads/package_img/'+item.package_img+'" width="100%"><br>';
                string += '</div>';

                if(item.package_group_id != 4){
                    
                    string += '<div class="col-lg-6 col-md-12 col-sm-12">';

                    if(item.package_inclusions != ''){
                        var inclusions = (item.package_inclusions).split(',');
                        string += '<strong> Inclusions: </strong><br>';
                        string += '<ul>';
                        inclusions.forEach(function(item){
                            string += '<li>'+ item +'</li>'; 
                        });
                        string += '</ul>'                        
                    }                       
                    string += '</div>';
                }
                else{
                    string += '<div class="col-lg-6 col-md-12 col-sm-12">';

                    sub_packages.forEach(function(item){
                        if(item.package_id == selected_id){
                            string += '<small><b>**'+item.sub_package_name+'**</b></small><br>';
                            if(item.inclusions != ''){
                                var inclusions = item.inclusions.split(',');
                                string += '<ul>';
                                inclusions.forEach(function(item){
                                    string += '<li>'+item+'</li>';
                                });
                                string += '</ul>';
                            }
                        }                        
                    });

                    string += '</div>';
                }

                if(item.notes != ''){
                    string += '<div class="col-12 mt5">';
                    var notes = (item.notes).split(',');

                    string += '<ul class="list-unstyled p5 notes">';
                    notes.forEach(function(item){
                        string += '<small>'+ item +'</small><br>';
                    });
                    string += '</ul>';
                    
                    string += '</div>';
                }
                

                string += '</div>';
                
            }
            $('#package_preview').html(string);            
        });        
    }    
    
    function update_booking_summary(){
        var summary_string = '<hr>';
        summary_string += '<div class="col-12 mt5 p10">';
        summary_string += '<h6><strong>Billing Summary</strong></h6><br>';

        var total = 0;
        
        added_packages.forEach(function(item){
            total += parseFloat(item.price);
            summary_string += '<div class="row">';
            summary_string += '<div class="col-7">'+item.package_name+'</div>';
            summary_string += '<div class="col-5 text-right">'+format_number(item.price,2)+'</div>';
            summary_string += '</div>';
        });

        summary_string += '<hr><div class="row">';
        summary_string += '<div class="col-7"><strong>Total Amount</strong></div>';
        summary_string += '<div class="col-5 text-right"><strong id="total_amount">Php '+format_number(total,2)+'</strong></div>';
        summary_string += '</div>';

        summary_string += '<br><small>Amount may still be adjusted depending in availability of items to be used</small>';
        summary_string += '<br><small>Amount will be final when the contract is issued</small>';

        summary_string += '</div>';
        $('#package_summary').html(summary_string);
    }

    //get data from form
    function get_selected_package_ids(){
        var packages_array = [];        

        added_packages.forEach(function(item){            
            packages_array.push(item.id);            
        });

        if(packages_array.length > 0){
            return packages_array;
        }        
    }

    function get_form_data(){
        var total_amount = $('#total_amount').text() != '' ? $('#total_amount').text().replace(',','') : '';        
        total_amount = total_amount.split(' ')[1];
        var form_data = {
            package_id_array: JSON.stringify(get_selected_package_ids()),
            full_name: $('#full_name').val(),
            address: $('#address').val(),
            contact_number: $('#contact_number').val(),
            email_address: $('#email_address').val(),
            event_date: $('#event_date').val(),
            venue: $('#venue').val(),
            venue_address: $('#venue_address').val(),
            remarks: $('#remarks').val(),
            status: 'Pending',
            total_amount: total_amount,
        }
        return form_data;
    }
    
    $('#btn_submit_booking').click(function(e){
        e.preventDefault();
        
        $.ajax({
            url: base_url+'save_booking',
            type: 'post',
            data: get_form_data(),
            success: function(response){
                clearFormErrors();
                if(response.success){
                    $('#message').text(response.message);
                    $('#ticket_num').text(response.ticket_num);
                    $('#booking_form').hide();
                    $('#ticket_info').show();
                }
                else{                    
                    show_errors(response,$('#booking_form'));
                    remove_error_on_keypress();
                }
            },
            error: function(response){

            },
        });
    });

    //remove error indicator after fill out
    function remove_error_on_keypress(){                
        $('#booking_form input').keyup(function(){
            var el_id = $(this).attr('id');
            if($(this).hasClass('has-error')){
                $(this).removeClass('has-error');
                $('.err-'+el_id).remove();
            }
        });        
        $('#booking_form textarea').keyup(function(){
            var el_id = $(this).attr('id');
            if($(this).hasClass('has-error')){
                $(this).removeClass('has-error');
                $('.err-'+el_id).remove();
            }
        });
        $('#event_date').mouseup(function(){
            var el_id = $(this).attr('id');
            if($(this).hasClass('has-error')){
                $(this).removeClass('has-error');
                $('.err-'+el_id).remove();
            }
        });              
    }



    

