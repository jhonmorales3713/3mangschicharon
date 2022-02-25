<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/csr_ticketlog_img.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<style type="text/css">
    .disableddiv {
    pointer-events: none;
    opacity: 1;
}
</style>
<script src="<?= base_url('assets/js/ckeditor/ckeditor.js') ?>"></script>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/csr_section_home/'.$token);?>">Support</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Csr/ticket_history/'.$token);?>">Ticket History</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active"><?= $breadcrumbs ?></li>
        </ol>
    </div>
    <section class="tables" style="margin-bottom: -60px;">   
        <div class="container-fluid">
            <section class="tables">   
                <div class="container-fluid">
                    <div class="row justify-content-md-center">
                        <div class="col-lg-12">
                            <div class="card">
                                <form class="form-horizontal personal-info-css" id="entry-form">
                                    <div class="card-header">
                                        <h3><?= $breadcrumbs.'<strong> #'.$ticket_details->ticket_refno.'</strong>' ?></h3>
                                    </div>
                                    <?php if($ticket_details->ticket_status == 1 || $ticket_details->ticket_status == 3){ ?>
                                        <div class="card-body">
                                    <?php }else{ ?>
                                        <div class="card-body disableddiv">
                                    <?php } ?>
                                        <input type="hidden" value="<?= $idno ?>" name="idno_hidden" id="idno_hidden">
                                        <div class="col-md-12 row">
                                            <div class="col-md-4">
                                                <div>
                                                    <label class="form-control-label col-form-label-sm">Subject:</label>
                                                    <input type="text" class="form-control required_fields" name="entry-subject" value="<?= $ticket_details->subject ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div>
                                                    <label class="form-control-label col-form-label-sm">Assignee:</label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-agentid" data-reqselect2="yes">
                                                        <?php if(!empty($ticket_details->assignee)){ ?>
                                                                <option value="">Select Agent</option>
                                                        <?php }else{ ?>
                                                                <option value="" selected>Select Agent</option>
                                                        <?php } ?>
                                                        <?php foreach($agent_list as $row){ ?>
                                                            <?php if($ticket_details->assignee == $row->id){ ?>
                                                                    <option value="<?= $row->id ?>" selected><?= $row->description ?></option>
                                                            <?php }else{ ?>
                                                                    <option value="<?= $row->id ?>"><?= $row->description ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div>
                                                    <label class="form-control-label col-form-label-sm">Ticket type:</label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-ticket_type" data-reqselect2="yes">
                                                        <?php if(!empty($ticket_details->ticket_type)){ ?>
                                                                <option value="">Select Ticket Type</option>
                                                        <?php }else{ ?>
                                                                <option value="" selected>Select Ticket Type</option>
                                                        <?php } ?>
                                                        <?php foreach($ticket_type as $row){ ?>
                                                            <?php if($ticket_details->ticket_type == $row->id){ ?>
                                                                    <option value="<?= $row->id ?>" selected><?= $row->description ?></option>
                                                            <?php }else{ ?>
                                                                    <option value="<?= $row->id ?>"><?= $row->description ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <!-- else if(!empty($ticket_details->branchname))
                                                <label class="form-control-label col-form-label-sm">Branch:</label>
                                                <br>
                                                <label class="form-control-label col-form-label-sm">strtoupper($ticket_details->branchname)</label>
                                                }else{
                                                <label class="form-control-label col-form-label-sm">Shop: </label>
                                                <br>
                                                <label class="form-control-label col-form-label-sm">trtoupper($ticket_details->shopname)</label>
                                                } -->
                                            </div>
                                            <div class="col-md-4">
                                                <div>
                                                    <label class="form-control-label col-form-label-sm">Ticket Category:</label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-subcategory" data-reqselect2="yes">
                                                        <?php if(!empty($ticket_details->issuecatid)){ ?>
                                                                <option value="">Select Ticket Category</option>
                                                        <?php }else{ ?>
                                                                <option value="" selected>Select Ticket Category</option>
                                                        <?php } ?>
                                                        <?php foreach($sub_category as $row){ ?>
                                                            <?php if($ticket_details->issuecatid == $row->id){ ?>
                                                                    <option value="<?= $row->id ?>" selected><?= $row->description ?></option>
                                                            <?php }else{ ?>
                                                                    <option value="<?= $row->id ?>"><?= $row->description ?></option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-4">
                                                <div>
                                                    <label class="form-control-label col-form-label-sm">Ticket Status:</label>
                                                    <br>
                                                    <label class="form-control-label col-form-label-sm"><?= get_ticket_status($ticket_details->ticket_status) ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div>
                                                    <label class="form-control-label col-form-label-sm">Priority Level:</label>
                                                    <select class="select2 form-control form-control-sm required_fields form-state" name="entry-priolevel" data-reqselect2="yes">
                                                        <?php if(!empty($ticket_details->priority_level)){ ?>
                                                            <?php if($ticket_details->priority_level == 1){ ?>
                                                                    <option value="1" selected>Low</option>
                                                                    <option value="2">Medium</option>
                                                                    <option value="3">High</option>
                                                            <?php }else if($ticket_details->priority_level == 2){ ?>
                                                                    <option value="1" >Low</option>
                                                                    <option value="2" selected>Medium</option>
                                                                    <option value="3">High</option>
                                                            <?php }else{ ?>
                                                                    <option value="1" >Low</option>
                                                                    <option value="2">Medium</option>
                                                                    <option value="3" selected>High</option>
                                                            <?php } ?>
                                                        <?php }else{ ?>
                                                                <option value="1" selected>Low</option>
                                                                <option value="2">Medium</option>
                                                                <option value="3">High</option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($ticket_details->ticket_status == 1 || $ticket_details->ticket_status == 3){ ?>
                                            <div class="form-group row">
                                                <div class="col-md-12 mt-4">
                                                    <strong>Concern Details <span style="color:red">*</span></strong>
                                                    <textarea name="commentbox" id="commentbox" rows="10" cols="80">
                                                        
                                                    </textarea>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <!-- <div class="modal-footer">
                                            <div class="form-group row">       
                                                <div class="col-md-12">
                                                    <?php if($ticket_details->ticket_status == 1 || $ticket_details->ticket_status == 3){ ?>
                                                            <button type="submit" class="btn btn-success saveBtn" style="margin: 5px;">Send</button>
                                                    <?php }?>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
    <center><h1>LOG HISTORY</h1></center>
    <hr>
    <?php $count = count($log_details);?>
    <?php foreach($log_details as $row){ ?>
        <section class="tables" style="margin-bottom: -75px;">   
            <div class="container-fluid">
                <section class="tables">   
                    <div class="container-fluid">
                        <div class="row justify-content-md-center">
                            <div class="col-lg-12">
                                <div class="card">
                                    <form class="form-horizontal personal-info-css" id="entry-form">
                                        <div class="card-header">
                                            <div class="col-md-12">
                                                <h4 style="float: right;"><?= $row->date_created.' #'.$count ?></h4>
                                            </div>
                                            <div class="col-md-12">
                                                <label style="font-size:1.2rem">Log Details</label>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" value="<?= $idno ?>" name="idno_hidden" id="idno_hidden">
                                            <!-- <h3><?= $breadcrumbs ?></h3> -->
                                            <div class="form-group row">                                              
                                                <div class="justify-content-center text-center col-md-2">
                                                    <div class="forrm-control">
                                                        <strong><?= strtoupper($row->representative) ?></strong>
                                                    </div>
                                                    <div class="forrm-control">
                                                        <img src="<?= base_url('assets/uploads/avatars/'.$row->avatar)?>" style="width: 80px; height: auto;" class="img-raised rounded img-fluid">
                                                    </div>
                                                    <div class="forrm-control">
                                                        <label><?= strtoupper($row->csrname) ?></label>
                                                    </div>
                                                    <div class="forrm-control">
                                                        <label><?= strtoupper($row->membertype) ?></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="col-md-12">
                                                        <?= $row->description; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
        <?php $count--; ?>
    <?php } ?>
    <div class="form-group row">       
        <div class="col-md-12">
            <div style="float: right;">
                <!-- <a href="<?=base_url('Main_page/display_page/csr_section_home/'.$token);?>" type="button" class="btn btn-primary" style="margin: 5px;">Close</a> -->
                <?php if($ticket_details->ticket_status == 1 || $ticket_details->ticket_status == 3){ ?>
                    <button type="button" class="btn btn-success" id="btn-savedetails" style="margin: 5px;">Save</button>
                    <?php if($member_type == 4){ ?>
                        <button type="button" class="btn btn-success closeticketBtn" data-toggle="modal" data-target="#close_ticket_modal" style="margin: 5px;">Resolved</button>
                    <?php } ?>
                <?php }else if($ticket_details->ticket_status == 2 AND $member_type == 4){ ?>
                    <button type="button" class="btn btn-success reopenticketBtn" data-toggle="modal" data-target="#reopen_ticket_modal" style="margin: 5px;">Open Ticket</button>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="close_ticket_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Close Ticket</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to close this ticket?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-yesclose" data-value="<?= $idno?>">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="reopen_ticket_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header secondary-bg white-text d-flex align-items-center">
                    <h3 class="modal-title" id="exampleModalLabel">Close Ticket</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to re open this ticket?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-yesreopen" data-value="<?= $idno?>">Confirm</button>
                </div>
            </div>
        </div>
    </div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url($main_js)?>"></script>
<!-- <script type="text/javascript" src="<?=base_url('assets/js/csr/orders/csr_orders_view.js');?>"></script> -->
<script>
  // Replace the <textarea id="commentbox"> with a CKEditor
  // instance, using default configuration.
  CKEDITOR.replace( 'commentbox' );
</script>

<!-- end - load the footer here and some specific js -->
