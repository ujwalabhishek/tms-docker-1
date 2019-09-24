<div class="container_nav_style">	
    <div class="container_row">
        <!-- Example row of columns -->
            <div class="col-md-12 min-pad">                              
                <div id ='trainee_validation_div'>
                    <div class="bs-example">                    
                        <div class="table-responsive">
                            <div class="col-md-12 min-pad warng">
                                <?php if($message == 'success') { ?>
                                    <img src="<?php  echo base_url(); ?>assets/images/thank-you.png"/>
                                    <h3 class="success-title">For Registering with '<?php echo $tenant_details->tenant_name; ?>'.  </h3>
                                    <p class="success-text">Your account activation mail has been
                                     sent to your registered email Id.<br/>
                                     Kindly click on the activation link to activate your account. <br/>
                                     - <?php echo $tenant_details->tenant_name; ?> Administrator.    </p>                         
                                <?php } else { ?>
                                    <img src="<?php  echo base_url(); ?>assets/images/oops.jpg"/>
                                    <h3 class="warng-title"> We are sorry! </h3>
                                    <p class="warn-text">We haven't been able to process your request.<br/>
                                     Kindly try again or get in touch with us @ Phone:
                                     <?php echo $tenant_details->tenant_contact_num; ?> OR<br/> Email to us @
                                    <a href="mailto:<?php echo $tenant_details->tenant_email_id; ?>"> <?php echo $tenant_details->tenant_email_id; ?></a>. <br/>
                                    - <?php echo $tenant_details->tenant_name; ?> Administrator.</p>
                                <?php } ?>
                            </div>                            
                        </div>
                    </div>                 
                </div>
            </div>
    </div>
</div>    