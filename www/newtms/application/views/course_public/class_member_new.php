<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
?>
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

<div class="col-md-12 col_10_height_other" style='height: 520px;'>
    <div class="makecenter" style="margin: 0px auto 0;">
        <div class="bs-example">
            <!--<h2 class="panel_heading_style"><span class="glyphicon glyphicon-log-in"></span> Sign In</h2>-->

            <div class="table-responsive">

                <!--added the new login form by shubhranshu-->
                <form class="modal-content animate" action="<?php echo site_url() ?>user/validate_public_user"  method="post" id="signupForm">
                    <h2 class="panel_heading_style" style='text-align:center'><strong>Trainee Use Only</strong></h2>
                    <div class="imgcontainer">

                        <img src="../assets/images/user2.png" alt="Avatar" class="avatar">
                    </div>

                    <div class="container-login">
                        <label for="uname"><b>Username</b></label>
                        <input type="text" placeholder="Enter Username" id='uname' name="username" class='form-control' value="<?php
if (isset($_COOKIE['remember_me'])) {
    echo $_COOKIE['remember_me'];
}
?>" required>
                        <div><span id="uname_err"></span></div>
                        <label for="psw"><b>Password</b></label>
                        <input type="password" placeholder="Enter Password" name="password" id='pwd' class='form-control' required>
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>" />
                        <div><span id="pass_err"></span></div>
                        <div class='row'>
                            <div class='col-sm-6'>
                                <label for="psw"><b>Captcha Code</b></label>
                                <div><?php echo $captcha; ?>
                                    <a href="administrator" title="Refresh">
                                        &nbsp;<span class="glyphicon glyphicon-refresh" style="font-size: 20px;color: #486d90;font-weight:bold;top:6px;"></span>
                                    </a>
                                </div>
                            </div>
                            <div class='col-sm-6'>
                                <label for="psw"><b>Enter Captcha Code</b></label>
                                <input type="captcha" placeholder="Enter captcha code" name="captcha" id='captcha' class='form-control' required>
                                <div><span id="captcha_err"></span>
                                    <?php
                                    if ($this->session->flashdata('invalid_captcha')) {
                                        echo '<div class="error">' . $this->session->flashdata('invalid_captcha') . '</div>';
                                    }
                                    ?>	
                                </div>
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
                        if (!isset($message)) {
                            ?>
                            <label class="pull-right">
                                <span><a href="<?php echo site_url(); ?>user/forgot_password">Forgot password?</a></span>
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

