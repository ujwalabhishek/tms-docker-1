<?php
/*
 * Mir
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $page_title; ?></title> 
        <!--CSS and JS inclusion starts here-->
        <?php //$this->load->view('includes/common_file'); ?>
        <!--CSS and JS inclusion ends here-->  
       
<!--    
        This is commented because it disabled  previous button in browser skm
        <script>
            function preventBack(){window.history.forward();}
            setTimeout("preventBack()", 0);
            window.onunload=function(){null};
        </script>-->
        
    </head>
    <body>
        <div class="main_container_new_top">     
            <!--Header starts here-->
            <?php //$this->load->view('includes/header'); ?>
            <!--Header ends here-->                
            <div class="container_nav_style">	
                <div class="container container_row">
                    <div class="row row_pushdown">
                        <!--The main content/body starts here-->
                        <div class="container container_row">
                        <ol class="breadcrumb breadcrumb_style1">
                          <li><a href="<?php echo base_url();?>">Home</a></li>
                          <li >Login</li>      
                        </ol> 
                            <!-- Example row of columns -->
                            <div class="row row_pushdown">
                                                       
                                <div class="col-md-12">
                                   
                                    <div class="table-responsive">

                                        <table class="table main_table" align="center" border="0" cellspacing="0" cellpadding="5">
                                            <tbody><tr>
                                                    <td width="45%" valign="top" style="padding:10px;">
                                                        <br><?php
                                                            if ($this->session->flashdata('invalid')) {
                                                                echo '<div class="error">' . $this->session->flashdata('invalid') . '</div>';
                                                            }
                                                            if ($this->session->flashdata('success_message')) {
                                                                echo '<div class="success">' . $this->session->flashdata('success_message') . '</div>';
                                                            }
                                                            ?>
                                                        <div class="bs-example">
                                                            <h2 class="panel_heading_style">Sign In</h2>
                                                            
                                                            <div class="table-responsive">

                                                                <?php
                                                                $attr = 'id="signupForm"';
//                                                                if(isset($class_id))
//                                                                echo form_open('user/validate_public_user?cid=' . $class_id, $attr);
//                                                                else 
                                                                    echo form_open('user/validate_public_user', $attr);
                                                                ?>
                                                                <table class="table table-striped">

                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="td_heading" width="40%">Username:<span class="required" >*</span></td>
                                                                            <td width="60%"><input type="text" name="username" value="<?php
                                                                                if (isset($_COOKIE['remember_me'])) {
                                                                                    echo $_COOKIE['remember_me'];
                                                                                }
                                                                                ?>"/></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="td_heading">Password:<span class="required">*</span></td>
                                                                            <td><input type="password" name="password" /></td>
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
                                                                                <input type="hidden" name="course_id" value="<?php echo $course_id;?>" />
                                                                                <input type="hidden" name="class_id" value="<?php echo $class_id;?>" />
                                                                                <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-log-in"></span>&nbsp;Login</button></td>

                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">
                                                                                <a href="<?php echo site_url(); ?>user/forgot_password" class="small_text1">Forgot your username and password?</a>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="2">Cookies must be enabled in your browser</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td colspan="2"><span class="required required_i">* Required Fields</span></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <?php echo form_close(); ?>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td width="10%" valign="top"><br><center><img src="<?php echo base_url(); ?>assets/images/line.png" border="0" alt=""></center></td>
                                            <td width="45%" valign="top">

                                                <br>

                                                <h2 class="panel_heading_style">Is this your first time here?</h2>
                                                <div class="table-responsive">
                                                    <table class="table table-striped">

                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">For full access to courses you'll need to take a minute to register yourself on this website. Here are the steps:

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" align="right">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td>1.</td>
                                                                <td>Fill out the 
                                                                    <?php 
                                                                    if($class_id!='' && $course_id!='')
                                                                    {
                                                                    ?> 
                                                                    <a href="<?php echo site_url();?>course_public/register_enroll/<?php echo $course_id; ?>/<?php echo $class_id; ?>">Register Now</a> form with your details.
                                                                    <?php
                                                                    }else
                                                                    {?>
<!--                                                                      <a href="<?php echo site_url();?>user/add_trainee">Register Now</a> form with your details.-->
                                                                        <a href="<?php echo site_url();?>course_public/register">Register Now</a> form with your details.
                                                                    <?php 
                                                                    }?>
                                                                
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>2.</td>
                                                                <td>An email along with Username and Password will be sent to your email address.</td>
                                                            </tr>
                                                            

                                                            <tr>
                                                                <td colspan="2" align="right">
                                                                    <?php 
                                                                    if($class_id!='' && $course_id!='')
                                                                    {
                                                                    ?> 
                                                                    <a href="<?php echo site_url();?>course_public/register_enroll/<?php echo $course_id; ?>/<?php echo $class_id; ?>"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Register Now</button></a>
                                                                    <?php
                                                                    }else{?>
                                                                     <a href="<?php echo site_url();?>course_public/register"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Register Now</button></a>
                                                                    <?php }?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!--The main content/body ends here-->
                    </div>
                    <div style="clear:both;">
                    </div>
                    <!--Footer starts here-->
                    <?php //$this->load->view('includes/footer'); ?>
                    <!--Footer ends here-->
                </div>
            </div>	
        </div>


    </body>

    <script>

        (function($, W, D)
        {
            var JQUERY4U = {};

            JQUERY4U.UTIL =
                    {
                        setupFormValidation: function()
                        {
                            //form validation rules
                            $("#signupForm").validate({
                                rules: {
                                    username: "required",
                                    password: {
                                        required: true,
                                    },
                                },
                                messages: {
                                    username: " [required]",
                                    password: {
                                        required: " [required]",
                                    },
                                },
                                submitHandler: function(form) {
                                    form.submit();
                                }
                            });
                        }
                    }

            //when the dom has loaded setup form validation rules
            $(D).ready(function($) {
                JQUERY4U.UTIL.setupFormValidation();
            });

        })(jQuery, window, document);
    </script> 
