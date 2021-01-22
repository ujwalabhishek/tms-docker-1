<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>

<div class="container-fluid bg-3 text-center header-box">
    <div class="containers">
        <div class="row">
            <div class="col-sm-2">
               <a href="<?php echo base_url()?>" class='pull-left'>
                <img class="logo" src="<?php echo base_url()?>logos/<?php echo TENANT_LOGO;?>" border="0">
                </a>
            </div>
            <div class="col-sm-7 header-ttl"><p>Training Management as a Service (TMaaS)</p></div>
            <div class="col-sm-3" style='font-size:34px'>
                <a title="Join Now" href="<?php echo site_url(); ?>login/administrator" class="btn btn-sm btn-primary"><span style='color:white'>Admin Login<span class="glyphicon glyphicon-chevron-right"></span></span> </a>
                <a title="Join Now" href="<?php echo site_url(); ?>course_public/class_member_check" class="btn btn-sm btn-primary"><span style='color:white'>Trainee LogIn<span class="glyphicon glyphicon-chevron-right"></span></span> </a>
                <a href="<?php echo site_url(); ?>" title="Home" style="width: 50px;"><img src="<?php echo site_url(); ?>assets/images/home.png" border="0" style="margin-top:4px;"></a>
            </div>
        </div>
    </div>
</div>





 

<!--<div class="container container_style">
    <div class="masthead">
        <h3 class="text-muted h3_logo"><a href="<?php echo site_url(); ?>" title="Home" >
                <?php
                if(base_url() == 'https://xprienz.biipbyte.co/'){?>
                
                <img class="logo" src="<?php echo base_url(); ?>logos/arise.png" border="0" style="max-width: 200px;" height="64">
                <?php }else{?>
                <img class="logo" src="<?php echo base_url(); ?>logos/t01.png" border="0" style="max-width: 200px;" height="64">
                <?php }?>
            </a></h3>
        
        <div class="training1">
            <a href="<?php echo site_url(); ?>" title="Home" >
            <img src="<?php echo base_url(); ?>assets/images/tms1.png" border="0" title="Training Management System">
        </a>
        </div>
                <?php 
        echo $data->user_id;
        if ($this->uri->segment(2) !== "add_trainee" && $this->uri->segment(2) !== "class_member_check" && $this->uri->segment(2) !== "class_enroll") { ?>
            <div class="right_date">
                
                <table   border="0" cellspacing="0" cellpadding="2" style="    float: right;">
                    <tbody >
                        <tr>
                            <td>
                                <a title="Join Now" href="<?php echo site_url(); ?>login/administrator" class="btn btn-sm btn-info"><strong style='color:white'>Admin Login<span class="glyphicon glyphicon-chevron-right"></span></strong> 
                                </a> 
                                
                            </td>
                            <td>
                                <a title="Join Now" href="<?php echo site_url(); ?>course_public/class_member_check" class="btn btn-sm btn-info"><strong style='color:white'>Trainee LogIn<span class="glyphicon glyphicon-chevron-right"></span></strong> 
                                </a> 
                                
                            </td>
                            <td>
                                <a href="<?php echo base_url(); ?>" title="Home"><img src="<?php echo base_url(); ?>assets/images/home.png" border="0"  style="margin-top:4px;"/></a>
                            </td>
                           
                        </tr>  
                         <tr >
                            <td>
                                <a title="Enroll For Someone" href="<?php echo site_url(); ?>course/referral_credentials1" style="color: black; text-decoration: none"><strong>Enroll For Someone</strong>
                              
                            </td>
                           
                        </tr>  
                        
                    </tbody>
                </table> 
           
                
               
                
               
                <table width="100%" align="left"> <tr>
                        <td rowspan="4" align="right" width="12%"> <img src="<?php echo base_url(); ?>assets/images/ref_frnd.png" align="left" /></td>
                        <td align="left" width="88%">
                            <a href="<?php echo base_url(); ?>course/referral_credentials">Enroll for Someone </a></td>
                    </tr> 
                </table>
                
                
            </div>
        <?php } else { ?>
            <a href="<?php echo site_url(); ?>" title="Home" >
                <img src="<?php echo base_url(); ?>assets/images/home.png" class="home-icon pull-right" />
            </a>
        <?php } ?>
    </div>	
</div>-->