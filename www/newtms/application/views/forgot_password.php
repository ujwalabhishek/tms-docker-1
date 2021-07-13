<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title> 
   <!--CSS and JS inclusion starts here-->
        <?php $this->load->view('includes/common_file'); ?>
        <!--CSS and JS inclusion ends here-->   
</head>
<body>
    <div class="main_container_new_top"> 
            <?php $this->load->view('common/login_header_public');  ?>	  
            <div class="container_nav_style">
                <div class="container container_row" style="min-height: 420px;">
                    <div class="row row_pushdown">
                        <div class="col-md-12 col_10_height_other">
                            <br><br><br>
                            <div class="makecenter">
                                
                               
                                
                                
                                <?php
                                    $atr = 'id="forgot_password_form" name="forgot_password_form"';
                                    echo form_open("user/get_forgot_password", $atr);
                                ?>
                                <div>    
                                    <p>
                                    <h2 class="panel_heading_style">Forgot Username / Password</h2>                                    
                                    <table class="table table-striped" width="100%" border="0" cellspacing="0" cellpadding="5">
                                        <?php   if($this->session->flashdata('success')){ 
                                                    //success message for gst editing.
                                                    echo "<p style='color:green'>".$this->session->flashdata('success')."</p>";             
                                                }else if($this->session->flashdata('error')){
                                                    //error message for gst editing.
                                                    echo "<p style='color:red'>".$this->session->flashdata('error')."</p>"; 
                                                }
                                        ?>                                            
                                        <tr>    
                                            <td colspan="2">
                                                <?php
                                                $attr = array(
                                                    'name' => 'forgot',
                                                    'checked' => FALSE,
                                                    'value' => 'Username'
                                                );
                                                echo form_radio($attr);
                                                ?> 
                                            <span class="required">*</span>Username &nbsp;&nbsp; 
                                                <?php
                                                $attr = array(
                                                    'name' => 'forgot',
                                                    'checked' => FALSE,
                                                    'value' => 'Password'
                                                );
                                                echo form_radio($attr);
                                                ?>
                                            <span class="required">*</span>Password
                                            <span id="forgot_err"></span>
                                            </td>                                            
                                        </tr>
                                        <tr>
                                            <td class="td_heading">Email Id:<span class="required">*</span> </td>
                                            <td>        
                                                <?php
                                                $attr = array(
                                                    'name' => 'email',
                                                    'type' => 'email',
                                                    'class'=> 'form-control',
                                                    'maxlength' => '150',
                                                    'id' => 'email'
                                                );
                                                echo form_input($attr);
                                                ?>
                                                <span id="email_err"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>Captcha:</div>
                                                <?php echo $captcha;?>
                                                 <a href="forgot_password" title="Refresh">
                                                 &nbsp;<span class="glyphicon glyphicon-refresh" style="font-size: 20px;color: #486d90;font-weight:bold;top:6px;"></span>
                                                 </a>
                                            </td>
                                            <td>
                                                <span>Enter Captcha:</span>
                                                <span> <input type="captcha" placeholder="Enter captcha code" name="captcha" id='captcha' class='form-control' value="<?php //echo $this->session->userdata('captcha_key')?>" required></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div>
                                                     <?php
                                                        if ($this->session->flashdata('invalid_captcha')) {
                                                            echo '<div class="error">' . $this->session->flashdata('invalid_captcha') . '</div>';
                                                        }
                                                        ?>	
                                                </div>
                                            </td>
                                        </tr>
<!--                                        <tr>
                                            <td class="td_heading">Date of Birth:<span class="required">*</span></td>
                                            <td>        
                                                <?php
                                                $attr = array(
                                                    'name' => 'dob',
                                                    'id'   =>'dob',
                                                    'type' => 'text'                                                                
                                                );
                                                echo form_input($attr);
                                                ?>
                                                <span id="dob_err"></span>
                                            </td>
                                        </tr>-->

                                    </table>        
                                    <span class="required required_i">* Required Fields</span>
                                    <div class="popup_cance89">
                                        <button class="btn btn-primary" type="button" onclick="validate_form()" >Submit</button>&nbsp;&nbsp;
                                        <a href="<?php echo base_url(); ?>"><button class="btn btn-primary" type="button" >Cancel</button></a>
                                    </div>
                                    <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div style="clear:both;"></div>
        <?php $this->load->view('includes/footer'); ?>
    </div>
</div>        
    <!-- for date picker -->    
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/public_css/jquery-ui.css">
    <script src="<?php echo base_url(); ?>assets/public_js/jquery-ui.js"></script>        
    <!--changed by Bineesh -->
    <script>        
        var d = new Date();        
        var currentYear = d.getFullYear(); 
        var currenyMonth=d.getMonth();
        var CurrentDate=d.getDay();
        var startYear = currentYear - 90;
        var endYear = currentYear - 10; 
        $(function() {          
          $( "#dob" ).datepicker({ 
              dateFormat: 'dd/mm/yy',
              minDate: new Date(startYear,currenyMonth,CurrentDate),
              maxDate: new Date(endYear,currenyMonth,CurrentDate),
              changeMonth: true,
              changeYear: true,
              yearRange: '-100:+0'              
          });
        });

    </script>
    <script>
    /* This function for validating the email id.
     * Author: Bineesh M
     * Date: Aug 05 2014
    */  
    function valid_email_address(emailAddress){
        var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);        
        return pattern.test(emailAddress);
    } 
    /* This function for validating the date.
     * Author: Bineesh M
     * Date: Aug 06 2014
    */ 
    function isDate(txtDate){        
        //var reg=/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/;
        var reg=/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d+$/; 
        return reg.test(txtDate);
    }
    /* This function for validating the date formate.
     * Author: Bineesh M
     * Date: Aug 06 2014
    */
    function validDate(dob){
        var res = dob.split('/');                 
        if(res[2]>=startYear && res[2]<=endYear){
            return true;
        }else{            
            return false;
        }    
    }       
    /* This function for validating the form
     * Author: Bineesh M
     * Date: Aug 05 2014
    */  
    function validate_form(){
        var email= $.trim($("#email").val());
        //var dob= $.trim($("#dob").val());
        retVal = true;
        if(!$("input[name='forgot']").is(':checked')) {            
            $("#forgot_err").text("[required]").addClass('error');                        
            retVal = false;
        } else {
            $("#forgot_err").text("").removeClass('error'); 
        }
        if(email=="") {
            $("#email_err").text("[required]").addClass('error');            
            retVal = false;
        } else if(valid_email_address(email) == false) {
            $("#email_err").text("[invalid]").addClass('error');            
            retVal = false;
        } else {
            $("#email_err").text("").removeClass('error'); 
        } 
//        if(dob=="") {
//            $("#dob_err").text("[required]").addClass('error');            
//            retVal = false;
//        } else if(isDate(dob)==false) {
//            $("#dob_err").text("[invalid format(dd/mm/yyyy)]").addClass('error');
//            retVal = false;
//        } else if(validDate(dob)==false) {
//            $("#dob_err").text("[invalid]").addClass('error');
//            retVal = false;
//        }
//        else {
//            $("#dob_err").text("").removeClass('error'); 
//        }
        if(retVal==true) {            
            $('#forgot_password_form').submit();
        } else {
            return retVal;
        }          
    }       
    </script>          
</body>
</html>

