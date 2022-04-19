<div class="container mt-5" id="contact_us_form">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4"></div>
        <div class="col-lg-4 col-md-4 col-sm-4">
            <center>
                <h5>Contact Us</h5>
                <hr>
            </center>            
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4"></div>
    </div>
    <br><br><br>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 right-bordered">
            <center>
            <?php if(get_address() != ''){?>
                <div class="col-12">
                    <div class="icon-container">
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                    </div>
                    <strong>Address</strong><br>
                    <small><?=get_address()?></small>
                </div>
            <?php }?>
            <?php if(get_company_phone() != ''){?>
                <div class="col-12">
                    <br>
                    <div class="icon-container">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                    </div>
                    <strong>Phone</strong><br>
                    <small><?=get_company_phone()?></small><br>
                    <small><?=get_telephone()?></small>
                </div>
            <?php } ?>
            <?php if(get_company_email() != ''){?>
            <div class="col-12">
                <br>
                <div class="icon-container">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </div>
                <strong>Email</strong><br>
                <small><?=get_company_email()?></small><br>
            </div>
            <?php } ?>
            </center>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-6 mt20" id="message_form">
            <div class="col-12">
                <strong>Send Us a message</strong><br>
                <p><small>If you have any concern please feel free to message us</small></p><br>
                <div class="form-group">
                    <input type="text" class="form-control" id="name" placeholder="Enter your name"/>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="email" placeholder="Enter your email"/>
                </div>
                <div class="form-group">
                    <textarea name="" cols="30" rows="7" class="form-control" id="message" placeholder="Enter your message"></textarea>
                </div>      
            </div>
            <div class="col-4">                
                <button class="btn btn-primary form-control" id="send_message">Submit</button>
            </div>   
        </div>
        <div class="col-lg-8 col-md-8 col-sm-6 mt20" id="message_success" style="display:none;">
            <div class="col-12">
                <strong>Message Sent!</strong>
                <p>Please wait atleast 24 hours for us to respond to your inquiry.</p><br>
                <p>Thank you</p>
            </div>
        </div>
        <div class="col-lg-8" id="sent_limit" style="display:none;">
            <div class="col-12">
                <p>You already reached maximum number of message sent today.</p>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/js/libs/user/home/contact_us.js'); ?>"></script>