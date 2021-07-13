<div class="container container_style">
    <div class="masthead">
        <h3 class="text-muted h3_logo"><a href="<?php echo base_url();?>">
        <!--<img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $logo = ($this->session->userdata('public_tenant_details')->Logo) ? $this->session->userdata('public_tenant_details')->Logo : 'defaultlogo.png'; ?>" border="0"></a></h3>-->
                <?php
                if(base_url() == 'https://xprienz2.biipmi.co/'){?>                
                <img class="logo" src="<?php echo base_url(); ?>logos/arise.png" border="0" style="max-width: 200px;" height="64">
                <?php }else{?>
                <img class="logo" src="<?php echo base_url(); ?>logos/<?php echo $this->session->userdata('public_tenant_details')->Logo; ?>" border="0" style="max-width: 200px;" height="64">
                <?php }?>
                <div class="training1">
        <img src="<?php echo base_url(); ?>assets/images/<?php echo $this->session->userdata('public_tenant_details')->ApplicationName;?>" border="0" title="Training Management as a Service (TMaaS)">
    </div>       
        <div class="right_date">
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
                <tbody>
                    
                    <?php if($this->uri->segment(2) != "add_trainee" or $this->uri->segment(2) != "class_member_check"){?> 
                    <tr>
                        <td><img src="<?php echo base_url(); ?>assets/images/logout_icon.png" border="0"></td>
                        <td valign="top">Not a Member?</td>
                        <td valign="top"><a title="Join Now" href="<?php echo site_url();?>user/add_trainee">Join Now</a></td>
                        
                    </tr>
                    <?php }// endif;?>
                </tbody>
            </table>
            
        </div>
    </div>	
</div>