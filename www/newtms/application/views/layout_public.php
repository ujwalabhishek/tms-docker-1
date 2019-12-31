<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="timezone" value="<?php echo $timezone = date_default_timezone_get()." / ". date('m/d/Y h:i:s a', time()); ?>">
        <title><?php echo $page_title; ?></title> 
         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
        <?php $this->load->view('common/includes_public'); ?>
        <style>
            #wrapper{
                margin:0;
                padding:0;
            }

            .header-box{
                background-color: rgba(255, 255, 255, 0.2);
                -webkit-box-shadow: 0 3px 1px rgba(0, 0, 0, 0.2);
                padding: 20px;

            }
            .header-div{margin:20px 0;}

            .header-ttl{color:#446cb3;
                    font-size: 34px;
                    font-weight: 600;
                    text-shadow: 0px 1px 0px #161163;
                font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, "sans-serif";

            }

            .col-desg{
                padding:10px;
                height:100%;
                height:150px;
            }

            .col-desg-tlt{
                background: #446cb3;
                padding: 10px;
                margin: 10px;
                height:40px;
                color: #fff;
                box-shadow: 0px -2px 6px 1px rgba(0, 0, 255, .2);
            }

            .col-desg-tlt:p{
                text-align: center;
                color: #fff;
                margin-top: 30px;	
            }
            .welcome {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
        </style>
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
