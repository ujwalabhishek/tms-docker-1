<?php
$this->load->helper('common');
$role_check = $this->data['user']->role_id;
$role_array = array("COMPACT", "SLEXEC", "TRAINER", "CRSEMGR");
$style = '';
if (in_array($role_check, $role_array))
    $style = 'style="width:100%"';

?>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-dashboard"></span> &nbsp;Welcome to Training Management System   
        <span class="label label-default pull-right white-btn">
        
            <?php if ($role_check == 'COMPACT') { ?>
            <a href="<?php echo site_url() . 'profile/change_password/'; ?>">
                <span class="glyphicon glyphicon-retweet"></span> Change Password
            </a>
            <?php } else { ?>
                <a href="<?php echo site_url() . 'profile/'; ?>">
                    <span class="glyphicon glyphicon-user"></span> My Profile
                </a>
            <?php } ?>
        </span>
        <a href="<?php echo base_url();?>login/dashboard" style="float:right; color:#ffffff;margin-top:5px;margin-right:35px;">Dashboard Details</a>
        
    </h2>       
    
  <?php $user_role = $this->session->userdata('userDetails')->role_id; ?> 
 
<?php if($user_role == 'ADMN'||$user_role =='CRSEMGR'){?>
  <div class="row">
      
     <div class="col-md-3">
         <a class="btn btn-block btn-sm btn-success" id="newclass" href="<?php echo base_url();?>classes/add_new_class"><br>
            <span class="fa fa-calendar-check-o" id="icone_grande"></span><br><br>          
            <span class="texto_grande"> Create New Class</span><br><br></a>
      </div>
          
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-danger" id="newtrainee" href="<?php echo base_url();?>trainee/add_new_trainee"><br>
          <span class="fa fa-user-plus" id="icone_grande"></span><br><br>
            <span class="texto_grande">Add New Trainee </span><br><br></a>
      </div>
      
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-primary" data-toggle="modal" data-target="#mymodal" id="newenrollment" href="<?php echo base_url();?>class_trainee/add_new_enrol"><br>
            <span class="fa fa-pencil-square-o" id="icone_grande"></span><br><br>
            <span class="texto_grande">Class Trainee Enrollment </span><br><br></a>
      </div>
      
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-warning" id="markattendence" href="<?php echo base_url();?>class_trainee/mark_attendance"><br>
             <span class="fa fa-check-square-o" id="icone_grande"></span><br><br>
            <span class="texto_grande"><i class="fa fa-list-ul"></i> Mark Attendance </span><br><br></a>
      </div> 
    
</div>  
    <br>
  <div class="row">
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-success" id="newenrollment" href="<?php echo base_url();?>accounting/generate_invoice"><br>
            <span class="fa fa-file-text-o" id="icone_grande"></span><br><br>          
            <span class="texto_grande">Print & Generate Invoice</span><br><br></a>
      </div>
          
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-danger" data-toggle="modal" data-target="#mymodal" id="markattendence" href="<?php echo base_url();?>accounting/update_payment"><br>
          <span class="fa fa-credit-card" id="icone_grande"></span><br><br>
            <span class="texto_grande"> Update Payment</span><br><br></a>
      </div>
      
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-primary" data-toggle="modal" data-target="#mymodal" id="newclass" href="<?php echo base_url();?>accounting/refund_payment"><br>
            <span class="fa fa-exchange" id="icone_grande"></span><br><br>
            <span class="texto_grande">Refund Payment </span><br><br></a>
      </div>
      
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-warning" id="newtrainee" href="<?php echo base_url();?>reports_finance/invoice_reg_list"><br>
             <span class="fa fa-file-text-o" id="icone_grande"></span><br><br>
            <span class="texto_grande"> Regenerated & Deleted Invoice </span><br><br></a>
      </div> 
    </div>

<?php }else{ ?>
    <!--tariner-->
      <div class="row">
      <?php $tenant_id = $this->session->userdata('userDetails')->tenant_id;
	  if($tenant_id=='T20' || $tenant_id=='T17'){ ?>
		<?php if($user_role == 'SLEXEC' || $user_role =='TRAINER'){?>
	  
		  
		<?php } else { ?>
		<div class="col-md-3">
			<a class="btn btn-block btn-sm btn-primary" data-toggle="modal" data-target="#mymodal" id="newenrollment" href="<?php echo base_url();?>class_trainee/add_new_enrol"><br>
				<span class="fa fa-pencil-square-o" id="icone_grande"></span><br><br>
				<span class="texto_grande">Class Trainee Enrollment </span><br><br></a>
		  </div>
		<?php } ?>
	  <?php }else{ ?>
	  <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-primary" data-toggle="modal" data-target="#mymodal" id="newenrollment" href="<?php echo base_url();?>class_trainee/add_new_enrol"><br>
            <span class="fa fa-pencil-square-o" id="icone_grande"></span><br><br>
            <span class="texto_grande">Class Trainee Enrollment </span><br><br></a>
      </div>
	  <?php } ?>
     <?php if($user_role == 'TRAINER'){?>
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-warning" id="markattendence" href="<?php echo base_url();?>class_trainee/mark_attendance"><br>
             <span class="fa fa-check-square-o" id="icone_grande"></span><br><br>
            <span class="texto_grande"><i class="fa fa-list-ul"></i> Mark Attendance </span><br><br></a>
      </div> 
     <?php }else{ ?>
	 
		 <?php if($tenant_id=='T20' || $tenant_id=='T17'){ ?>
			<?php if($user_role == 'SLEXEC' || $user_role =='TRAINER'){?>
			
			<?php } else { ?>
				<div class="col-md-3">
					<a class="btn btn-block btn-sm btn-danger" id="newtrainee" href="<?php echo base_url();?>trainee/add_new_trainee"><br>
					<span class="fa fa-user-plus" id="icone_grande"></span><br><br>
					<span class="texto_grande">Add New Trainee </span><br><br></a>
				</div>  
			<?php } ?>
		<?php } else { ?>
			<div class="col-md-3">
				<a class="btn btn-block btn-sm btn-danger" id="newtrainee" href="<?php echo base_url();?>trainee/add_new_trainee"><br>
				<span class="fa fa-user-plus" id="icone_grande"></span><br><br>
				<span class="texto_grande">Add New Trainee </span><br><br></a>
			</div>  
		<?php } ?>
     <?php } ?>
          <div class="col-md-3">
         <a class="btn btn-block btn-sm btn-success" id="newclass" href="<?php echo base_url();?>reports/attendance"><br>
            <span class="fa fa-calendar-check-o" id="icone_grande"></span><br><br>          
            <span class="texto_grande">Attendance Reports</span><br><br></a>
      </div>
          
      <div class="col-md-3">
        <a class="btn btn-block btn-sm btn-danger" id="newtrainee" href="<?php echo base_url();?>reports/certificates"><br>
          <span class="fa fa fa-file-text-o" id="icone_grande"></span><br><br>
            <span class="texto_grande">Certificates Report </span><br><br></a>
      </div> 
    <div  class="col-md-12" style="margin-top: 20%;">
              
    </div>
          
          
    
</div>  
<?php
}
?>
   
   
    
    
</div>

<!--<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">-->
<style>
.texto_grande {
    font-size: 14px;
    color: white;
} 
#icone_grande {
    font-size: 4em;
    /*font-size: 10px;*/
    color:#fff;
} 
#newclass{
    background-color:#6e7d8a !important;
    border:none;
    box-shadow: 3px 3px 5px #888888;
}
#newtrainee{
   background-color:#656280 !important;
   border:none;
   box-shadow: 3px 3px 5px #888888;
}
#newenrollment{
    background-color:#60a591 !important;
    border:none;
    box-shadow: 3px 3px 5px #888888;
}
#markattendence{
    background-color:#446cb3 !important;
    border:none;
    box-shadow: 3px 3px 5px #888888;
}
.texto_grande{
     text-shadow: 1px 1px #8a7e7e;
}

</style>