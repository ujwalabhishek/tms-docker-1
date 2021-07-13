<script type="text/javascript" src="<?php echo base_url(); ?>assets/public_js/jquery.validate.js"></script> 
<div class="container container_style">
    <div class="masthead">
        <h3 class="text-muted h3_logo">
            <a href="<?php echo base_url(); ?>" title="Home" ><img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $this->session->userdata('public_tenant_details')->Logo; ?>" border="0" width="64" height="64"></a>
        </h3>
        <div class="training1">
            <img src="<?php echo base_url(); ?>assets/images/<?php echo $this->session->userdata('public_tenant_details')->ApplicationName; ?>" border="0" title="Training Management System">
        </div>
        <?php 
        $url_array = array("add_trainee","login");
        if (!in_array($this->uri->segment(2), $url_array)) { ?>
            <div class="right_date">
                <table   border="0" cellspacing="0" cellpadding="2" style="    float: right;">
                    <tbody >
                        <tr >
                            <td>
                                <a title="Join Now" href="<?php echo site_url(); ?>course_public/class_member_check" class="btn btn-sm btn-info"><strong>Sign In</strong> <span class="glyphicon glyphicon-chevron-right"></span>
                                </a> 
                                
                            </td>
                            <td>
                                <a href="<?php echo base_url(); ?>" title="Home"><img src="<?php echo base_url(); ?>assets/images/home.png" border="0"  style="margin-top:4px;"/></a>
                            </td>
                           
                        </tr>  
<!--                         <tr >
                            <td>
                                <a title="Enroll For Someone" href="<?php echo site_url(); ?>course/referral_credentials1" style="color: black; text-decoration: none"><strong>Enroll For Someone</strong>
                              
                            </td>
                           
                        </tr>  -->
                        
                    </tbody>
                </table> 
                  
            </div>
        <?php } else if($this->uri->segment(2) == "login") {
            ?>
            <div class="right_date">
                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                    <tbody>
                        <tr>
                            <td rowspan="3">
                                <img src="<?php echo base_url(); ?>assets/images/join_now.png" border="0" />
                            </td>
                            <td valign="middle"><strong>Not a Member?</strong></td>
                            <td valign="middle">
                                <a title="Join Now" href="<?php echo site_url(); ?>user/add_trainee" class="btn btn-sm btn-info"><strong>Join Now</strong> <span class="glyphicon glyphicon-chevron-right"></span></a> <a href="<?php echo base_url(); ?>" title="Home"><img src="<?php echo base_url(); ?>assets/images/home.png" border="0" class="pull-right" style="margin-top:4px;"/></a>
                            </td>
                        </tr>
                    </tbody>
                </table>    
                  
            </div>
        <?php
        }else { ?>
                <div class="right_date">
                    <table width="100%" border="0" cellspacing="0" cellpadding="2">
                        <tbody>
                            <tr>
                                <td rowspan="3">
                                    <img src="<?php echo base_url(); ?>assets/images/join_now.png" border="0" />
                                </td>                        
                            </tr>   
                            <tr>
                                <td><strong>Already a Member?</strong></td>
                                <td>
                                    <a href="<?php echo base_url(); ?>user/login"><strong>Sign In!</strong></a>
                                    <a href="<?php echo base_url(); ?>" title="Home">
                                        <img src="<?php echo base_url(); ?>assets/images/home.png" border="0" class="pull-right" />
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                    
                </div>
        <?php } ?>
    </div>	
</div>