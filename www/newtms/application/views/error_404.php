<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TMS Admin: Error</title>  
</head>
<body>
<div class="main_container_new_top"> 
<link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/glyphicons.css" rel="stylesheet">

<script src="<?php echo base_url(); ?>assets/js/ajax.jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.modal.js" type="text/javascript" charset="utf-8"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.modal.css" type="text/css" media="screen" />

<link rel="stylesheet" href="<?php  echo base_url(); ?>assets/css/jquery.treeview.css" />

<script src="<?php echo base_url(); ?>assets/js/jquery.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.treeview.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/demo.js" type="text/javascript" charset="utf-8"></script>
	
<script type="text/javascript" src="<?php  echo base_url(); ?>assets/js/menuactive.js"></script>
    
<script src="<?php echo base_url(); ?>assets/js/date_time.js" type="text/javascript" charset="utf-8"></script>
<div class="container container_style">
    <div class="masthead">
        <h3 class="text-muted h3_logo">
            
            <?php if($tenant_details->logo){ ?>
            <img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $tenant_details->logo; ?>" border="0" width="64" height="64" />
            <?php  } else {
                ?>
<img class="logo" src="<?php echo base_url(); ?>logos/t01.png" border="0" width="64" height="64" />        
           <?php } ?>
        </h3>
        <div class="training">
<?php if($tenant_details->applicationname) { ?>            
       <img src="<?php echo base_url(); ?>assets/images/<?php echo $tenant_details->applicationname; ?>" border="0" title="Training Management System" />
<?php } else { ?>
       <img src="<?php echo base_url(); ?>assets/images/training.png" border="0" title="Training Management System" />       
<?php } ?>       
        </div>
    </div>
</div>
<div class="container_nav_style">
    <div class="container container_row">
      <div class="row row_pushdown">
      <div class="col-md-12 cent">
       <img src="<?php echo base_url(); ?>assets/images/error.jpg">
      <h1 class="error-title"> We have not been able to process your request.</h1>
      <p class="error-p"> Please try again later or get in touch with your administrator <br/>
     <?php if($tenant_details->tenant_id) { ?>
      <a href="<?php echo base_url(); ?>">Back to Home</a>
     <?php } else { ?>
      <a href="<?php echo base_url(); ?>">Back to Login</a>
     <?php } ?> 
      </p>
       </div>
       </div>
        
      </div>
        
</div>

<br><br><br><br>
<hr>
 <footer>
        <p>&copy; <?php if($tenant_details->copyrighttext) { echo $tenant_details->copyrighttext; } else { echo 'BIIPIdea 2014'; } ?><font style="font-size:9px; color:#ac0000; font-weight:bold; float:right;">powered by dummy</font></p>
</footer>