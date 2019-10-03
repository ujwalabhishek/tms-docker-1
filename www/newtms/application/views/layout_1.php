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
</head>
<body>
<?php $this->load->view('common/includes');?>
    
<div class="main_container_new_top"> 
            <?php $this->load->view('common/login_header'); ?>	  
            <div class="container_nav_style">
                <div class="container container_row">
                    <div class="row row_pushdown">
                        <div class="col-md-12 col_10_height_other">
                            <div class="makecenter">
                                <div class="bs-example">
                                    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-log-in"></span> Sign In</h2>
                                    <?php
                                    if ($this->session->flashdata('invalid')) {
                                        echo '<div class="error">' . $this->session->flashdata('invalid') . '</div>';
                                    }
                                    ?>	
                                    <?php
                                    if ($this->session->flashdata('warning')) {
                                        echo '<div class="error">' . $this->session->flashdata('warning') . '</div>';
                                    }
                                    ?>                                   
                                    <div class="table-responsive">
                                        <form action="<?php echo site_url() ?>login/validate_user"  method="post" id="signupForm">
                                            
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td class="td_heading" width="40%">Username:<span class="required" >*</span></td>
                                                        <td width="60%"><input type="text" name="username"  class='form_control' id="uname"  value="<?php
                                                            if (isset($_COOKIE['remember_me'])) {
                                                                echo $_COOKIE['remember_me'];
                                                            }
                                                            ?>"/>
                                                        <span id="uname_err"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="td_heading">Password:<span class="required">*</span></td>
                                                        <td><input type="password" class='form_control' name="password" id="pwd"/>
                                                         <span id="pass_err"></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="checkbox" name="remember" value="1" <?php
                                                            if (isset($_COOKIE['remember_me'])) {
                                                                echo 'checked="checked"';
                                                            } else {
                                                                echo '';
                                                            }
                                                            ?>/> Remember Username</td>
                                                        <td>
                                                            <button class="btn btn-primary" type="submit" onclick="return validate_form();" ><span class="glyphicon glyphicon-log-in"></span>&nbsp;Login</button>
                                                        </td>

                                                    </tr>
                                                    <?php 
                                                    if(!isset($message)){                                                        
                                                    ?>
                                                    <tr>
                                                        <td colspan="2">                                                            
                                                            <a href="<?php echo site_url();?>login/forgot_password" class="small_text1">Forgot your password?</a>
                                                        </td>
                                                    </tr>
                                                    <?php }
                                                    ?>
                                                    <tr>
                                                        <td colspan="2">Cookies must be enabled in your browser</td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="2"><span class="required required_i">* Required Fields</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </form>
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
        if(retVal==true){            
            $('#signupForm').submit();
        }
        else{
            return retVal;
        }        
    }   
    </script>
</body>
</html>

