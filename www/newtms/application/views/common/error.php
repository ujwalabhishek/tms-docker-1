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
  <div class="main_container_new_top">
      <div class="container container_style">
<div class="masthead">
<h3 class="text-muted h3_logo"><img class="logo" src="<?php echo base_url(); ?>assets/images/biipmi.png" alt=""></h3>
<div class="training"><img src="<?php echo base_url(); ?>assets/images/TMS1.png" alt="" title="Training Management System"></div>
</div>
</div>  
	  <div class="container_nav_style">
<div class="container container_row">
      <div class="row row_pushdown">
      <div class="col-md-12 cent">
       <img src="<?php echo base_url(); ?>assets/images/access-denied.png"><br/><br/>
      <p class="error-p">You do not have access to the page you requested.<br/>
          <?php if ($login_link) {?>
        <a href="<?php echo base_url() . 'login'; ?>">Please login</a></p>
          <?php } else { ?>
        <a href="<?php echo base_url(); ?>">Home</a></p>
          <?php } ?>
       </div>
       </div>
      </div>
<div style="clear:both;"></div>
      <hr>
<footer>
<p><span>powered by dummy</span></p>
</footer>
    </div>
</div>
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
