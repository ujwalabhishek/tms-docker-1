<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>  
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
                                    <div class="table-responsive">
                                        <?php
                                        if ($message == 'Y') {
                                            echo '<div class="success">Account has been activated. Trainee Interface coming soon!</div>';
                                        }else {                                             
                                           echo '<div class="error1">Account activation failed.Please contact your Administrator.</div>'; 
                                        }
                                        ?>
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
</body>
</html>

