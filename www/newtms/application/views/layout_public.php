<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="timezone" value="<?php echo $timezone = date_default_timezone_get()." / ". date('m/d/Y h:i:s a', time()); ?>">
        <title><?php echo $page_title; ?></title> 
        <?php $this->load->view('common/includes_public'); ?>
    </head>
    <body>
        <div class="main_container_new_top">    
            <?php $data = $this->session->userdata('userDetails'); ?>
            <?php 
            if (isset($data->user_id)) {
                $this->load->view('common/loged_in_header_public');
            } else {
                
                $this->load->view('common/login_header_public'); 
            }
            ?>
            <style>
    .ui-datepicker{
        width:auto !important;
    }
</style>
            <div class="container_nav_style">	
                <div class="container container_row">
                    <div class="row row_pushdown">
                         <?php if (isset($data->user_id)) :?>
                        <?php 
                            $this->load->view('common/navigation_public'); 
                            $this->load->helper('breadcrumb_public');
                            echo create_breadcrumb();
                        ?>
                    <?php endif; ?>
                        
                        <?php $this->load->view($main_content); ?> 
                    </div>
                    <div style="clear:both;">
                    </div>
                    <?php $this->load->view('common/footer_public'); ?>
                </div>
            </div>	
        </div>
        <div class="modal_991" id="ex9" style="display:none;">
            <p> 
            <div class="courseexcel"><img src="<?php echo base_url(); ?>assets/images/courseexcel.png" border="0" width="860px;"></div> 
            </p>
        </div>     
        <div class="modal_991" id="ex8" style="display:none;">
            <p>  
            <div class="courseexcel1"><img src="<?php echo base_url(); ?>assets/images/courseexcel1.png" border="0" width="2412px;"></div> 
            </p>
        </div>
    </body>
</html>
