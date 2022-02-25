<!-- 
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->


<div class="content-inner" id="pageActive" data-num="99" data-namecollapse="" data-labelname="Sales">
	<div class="bc-icons-2 card mb-4">
		<ol class="breadcrumb mb-0 primary-bg px-4 py-3">
			<li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
			<li class="breadcrumb-item active">Profile Settings</li>
		</ol>
	</div>
	
    <div class="row">
    	<div class="col-sm-6">
    		<a href="<?=base_url('Main_profile_settings/change_pass/'.$token);?>" class="w-100">
    			<div class="card card-option card-hover white p-3 mb-3 w-100">
    				<div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
    				<div class="card-header-title font-weight-bold">Change Password</div>
    				<small class="card-text text-black-50">Change your existing password.</small>
    			</div>
    		</a>
            
    	</div>

    	<div class="col-sm-6"> 
    		<a href="<?=base_url('Main_profile_settings/change_personal_information/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Change Personal Information</div>
                    <small class="card-text text-black-50">Change your personal information.</small>
                </div>
            </a>
    	</div>
    </div>
    <?php $this->load->view('includes/footer'); ?>
