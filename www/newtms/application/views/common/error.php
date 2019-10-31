<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TMS Access Denied</title>
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/glyphicons.css" rel="stylesheet">      
  </head>
  <body>
  <div class="container-fluid bg-3 text-center header-box">
    <div class="containers">
        <div class="row">
            <div class="col-sm-2">
               <a href="<?php echo base_url()?>" class='pull-left'>
                <img class="logo" src="<?php echo base_url()?>logos/t01.png" border="0">
                </a>
            </div>
            <div class="col-sm-7 header-ttl"><p>TRAINING MANAGEMENT SYSTEM</p></div>
            <div class="col-sm-3" style='font-size:34px'>
                <a title="Join Now" href="<?php echo site_url(); ?>login/administrator" class="btn btn-sm btn-primary"><span style='color:white'>Admin Login<span class="glyphicon glyphicon-chevron-right"></span></span> </a>
                <a title="Join Now" href="<?php echo site_url(); ?>course_public/class_member_check" class="btn btn-sm btn-primary"><span style='color:white'>Trainee LogIn<span class="glyphicon glyphicon-chevron-right"></span></span> </a>
            </div>
        </div>
    </div>
    <div style="clear:both;"></div>
    <hr>
    <footer>
    <!--<p><span>powered by biipmi</span></p>-->
        <?php $this->load->view('common/login_footer'); ?>
    </footer>
</div>
      
      
<!--      
  <div class="main_container_new_top">
      <div class="container container_style">
<div class="masthead">
<h3 class="text-muted h3_logo"><img class="logo" src="<?php //echo base_url(); ?>assets/images/biipmi.png" alt=""></h3>
<div class="training"><img src="<?php //echo base_url(); ?>assets/images/tms1.png" alt="" title="Training Management System"></div>
</div>
</div>  
<div class="container_nav_style">
    <div class="container container_row">
        <div class="row row_pushdown">
            <div class="col-md-12 cent">
                <img src="<?php //echo base_url(); ?>assets/images/access-denied.png"><br/><br/>
                <p class="error-p">You do not have access to the page you requested.<br/>
                    <?php //if ($login_link) { ?>
                        <a href="<?php //echo base_url() . 'login'; ?>">Please login</a></p>
                <?php //} else { ?>
                    <a href="<?php //echo base_url(); ?>">Home</a></p>
                <?php } ?>
            </div>
        </div>
    </div>
<div style="clear:both;"></div>
      <hr>
<footer>
<p><span>powered by biipmi</span></p>
    <?php ///$this->load->view('common/login_footer'); ?>
</footer>
    </div>
</div>-->
<div class="modal" id="ex1" style="display:none;">
  <h2 class="panel_heading_style">Heading Goes Here...</h2>
  Detail Goes here.  <br>
  <div class="popup_cancel">
  <a href="#" rel="modal:close" class="btn btn-primary">Cancel</a></div>
</div>
<div class="modal1" id="ex9" style="display:none;">
  <h2 class="panel_heading_style">Remove Contact</h2>
  Are you sure you want to remove this Contact?  <br>
  
  <div class="popup_cancel9">
  <a href="#" rel="modal:close" class="btn btn-primary">Yes</a>&nbsp;&nbsp;<a href="#" rel="modal:close" class="btn btn-primary">No</a></div>
</div>
<div class="modal0000" id="ex10" style="display:none;">
  <h2 class="panel_heading_style">Save Company</h2>
  Data has been saved successfully. 
  <div class="popup_cancel popup_cancel001">
  <a href="#" rel="modal:close" class="btn btn-primary">Ok</a></div>
</div>
  </body>
</html>
<?php die();
