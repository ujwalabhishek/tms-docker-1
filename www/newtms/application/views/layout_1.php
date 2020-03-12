<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="timezone" value="<?php echo $timezone = date_default_timezone_get()." / ". date('m/d/Y h:i:s a', time()); ?>">
    <title><?php echo $page_title; ?></title>  
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
                        <div class="col-md-12 col_10_height_other" style='height: 520px;'>
                            <div class="makecenter" style="margin: 0px auto 0;width:455px">
                                <div class="bs-example">
                                    <!--<h2 class="panel_heading_style"><span class="glyphicon glyphicon-log-in"></span> Sign In</h2>-->
                                                           
                                    <div class="table-responsive">
                                         
                                        <!--added the new login form by shubhranshu-->
                                        <form class="modal-content animate" action="<?php echo site_url() ?>login/validate_user"  method="post" id="signupForm">
                                            <h2 class="panel_heading_style" style='text-align:center'><strong>Internal Staff Use Only</strong></h2>
                                            <div class="imgcontainer">
                                              
                                              <img src="../assets/images/user2.png" alt="Avatar" class="avatar">
                                            </div>

                                            <div class="container-login">
                                                <div><label for="uname" style='font-size: 13px;'><b>Username</b></label></div>
                                                <div> <input type="text" placeholder="Enter Username" id='uname' name="username" class='form-control' value="<?php
                                                            if (isset($_COOKIE['remember_me'])) {
                                                                echo $_COOKIE['remember_me'];
                                                            }
                                                            ?>" required></div>
                                              <div><span id="uname_err"></span></div>
                                              <div><label for="psw" style='font-size: 13px;'><b>Password</b></label></div>
                                             <div> <input type="password" placeholder="Enter Password" name="password" id='pwd' class='form-control' required></div>
                                               <div><span id="pass_err"></span></div>
<!--                                               <div class='row'>
                                                   <div class='col-sm-6'>
                                                       <label for="psw"><b>Captcha Code</b></label>
                                                        <div><?php //echo $captcha;?>
                                                            <a href="administrator" title="Refresh">
                                                            &nbsp;<span class="glyphicon glyphicon-refresh" style="font-size: 20px;color: #486d90;font-weight:bold;top:6px;"></span>
                                                            </a>
                                                        </div>
                                                   </div>
                                                   <div class='col-sm-6'>
                                                       <label for="psw"><b>Enter Captcha Code</b></label>
                                                       <input type="captcha" placeholder="Enter captcha code" name="captcha" id='captcha' class='form-control' value="<?php echo $this->session->userdata('captcha_key')?>" required>
                                                        <div><span id="captcha_err"></span>
                                                            <?php
//                                                            if ($this->session->flashdata('invalid_captcha')) {
//                                                                echo '<div class="error">' . $this->session->flashdata('invalid_captcha') . '</div>';
//                                                            }
                                                            ?>	
                                                        </div>
                                                   </div>
                                               </div>-->
                                               <div class='row'>
                                                    <div class='col-sm-12'>
                                                       <div><span id="captcha_err"></span>
                                                            <?php
                                                            if ($this->session->flashdata('invalid_captcha')) {
                                                                echo '<div class="error">' . $this->session->flashdata('invalid_captcha') . '</div>';
                                                            }
                                                            ?>	
                                                        </div>
                                                       
                                                   </div>
                                               </div>
                                               <div class='row'>
                                                    <div class='col-sm-12'>
                                                       <div class="g-recaptcha" data-sitekey="<?php echo GOOGLE_CAPTCHA_SITEKEY;?>"></div>
                                                       
                                                   </div>
                                               </div>
                                                    <?php
                                                    if ($this->session->flashdata('invalid')) {
                                                        echo '<center><div class="error">' . $this->session->flashdata('invalid') . '</div></center>';
                                                    }
                                                    ?>	
                                                    <?php
                                                    if ($this->session->flashdata('warning')) {
                                                        echo '<center><div class="error">' . $this->session->flashdata('warning') . '</div></center>';
                                                    }
                                                    ?>          
                                               
                                               <br>
                                              <button type="submit" onclick="return validate_form();" id='login'><span class="glyphicon glyphicon-log-in"></span>&nbsp;Login</button>
                                              <br>
                                              <label>
                                                <input type="checkbox" name="remember" value="1" <?php
                                                            if (isset($_COOKIE['remember_me'])) {
                                                                echo 'checked="checked"';
                                                            } else {
                                                                echo '';
                                                            }
                                                            ?>> Remember me
                                                
                                              </label>
                                              <?php 
                                                if(!isset($message)){                                                        
                                                ?>
                                              <label class="pull-right">
                                                  <span><a href="<?php echo site_url();?>login/forgot_password">Forgot password?</a></span>
                                              </label>
                                                <?php } ?>
                                              <br>
                                            </div>

                                            <div class="container-footer">
                                              <!--<button type="button" class="cancelbtn">Cancel</button>-->
                                              <br>
                                            </div>
                                          </form>
                                        
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
    <script>
    function validate_form(){
        var uname= $("#uname").val();
        var pwd= $("#pwd").val();
        var captcha= $("#captcha").val();
        retVal = true;
        
        if(uname==""){
            $("#uname_err").text("[required]").addClass('error');            
            retVal = false;
        }else{
            $("#uname_err").text("").removeClass('error'); 
        }
        if(pwd==""){
            $("#pass_err").text("[required]").addClass('error');            
            retVal = false;
        }else{
            $("#pass_err").text("").removeClass('error'); 
        }
        if(captcha==""){
            $("#captcha_err").text("[required]").addClass('error');            
            retVal = false;
        }else{
            $("#captcha_err").text("").removeClass('error'); 
        }
        if(retVal==true){ 
            //added by shubhranshu for disable of login button once clicked login
            $('#login').attr('disabled','disabled').css('background-color','#aeb4ba').html('Please Wait..');
            
            $('#signupForm').submit();
        }
        else{
            return retVal;
        }        
    }   
    </script>
</body>
</html>

