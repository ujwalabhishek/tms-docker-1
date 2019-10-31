







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="timezone" value="<?php echo $timezone = date_default_timezone_get()." / ". date('m/d/Y h:i:s a', time()); ?>">
    <title><?php echo $page_title; ?></title>  
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-78372106-1', 'auto');
  ga('send', 'pageview');

</script>
<style>
    input[type=text], input[type=password] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

/* Set a style for all buttons */
button {
  background-color: #428bca;
  color: white !important;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
}

button:hover {
  opacity: 0.8;
}

/* Extra styles for the cancel button */
.cancelbtn {
  width: auto;
  padding: 10px 18px;
  background-color: #f44336;
}

/* Center the image and position the close button */
.imgcontainer {
  text-align: center;
  /*margin: 10px 0 12px 0;*/
  position: relative;
}

img.avatar {
  width: 26%;
  border-radius: 50%;
}

.container {
  padding: 16px;
}

span.psw {
  float: right;
  padding-top: 16px;
}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
  padding-top: 60px;
}

/* Modal Content/Box */
.modal-content {
  background-color: #fefefe;
  margin: 0% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
  border: 1px solid #888;
  width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button (x) */
.close {
  position: absolute;
  right: 25px;
  top: 0;
  color: #000;
  font-size: 35px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: red;
  cursor: pointer;
}

/* Add Zoom Animation */
.animate {
  -webkit-animation: animatezoom 0.6s;
  animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
  from {-webkit-transform: scale(0)} 
  to {-webkit-transform: scale(1)}
}
  
@keyframes animatezoom {
  from {transform: scale(0)} 
  to {transform: scale(1)}
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
  span.psw {
     display: block;
     float: none;
  }
  .cancelbtn {
     width: 100%;
  }
}
.container-login{
    padding:0px 30px;
}
#login{
    font-size:15px;
}
.panel_heading_style{
        border-top-left-radius: 5px;
    border-top-right-radius: 5px;
}
.container-footer{
    background-color:#f1f1f1;
    border-bottom-left-radius: 7px;
    border-bottom-right-radius: 7px;
}
</style>
</head>
<body>
<?php $this->load->view('common/includes');?>
    
<div class="main_container_new_top"> 
            <?php $this->load->view('common/login_header'); ?>	  
            <div class="container_nav_style">
                <div class="container container_row">
                    <div class="row row_pushdown">
                        <div class="col-md-12 col_10_height_other">
                            <div class="makecenter" style="margin: 0px auto 0;">
                                <div class="bs-example">
                                    <!--<h2 class="panel_heading_style"><span class="glyphicon glyphicon-log-in"></span> Sign In</h2>-->
                                                           
                                    <div class="table-responsive">
                                         
                                        <!--added the new login form by shubhranshu-->
                                        
                                        <div class="row row_pushdown">
                                            <div class="col-md-12 cent">
                                                <img src="<?php echo base_url(); ?>assets/images/access-denied.png"><br/><br/>
                                                <p class="error-p">You do not have access to the page you requested.<br/>
                                                    <?php if ($login_link) { ?>
                                                        <a href="<?php echo base_url() . 'login'; ?>">Please login</a></p>
                                                <?php } else { ?>
                                                    <a href="<?php echo base_url(); ?>">Home</a></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                         <!--added the new login form by shubhranshu-->
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="clear:both;">
                    </div>
                    <?php $this->load->view('common/login_footer'); ?>
                </div>
            </div>
        </div>
    
</body>
</html>



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









<?php die();
