<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="timezone" value="<?php echo $timezone = date_default_timezone_get()." / ". date('m/d/Y h:i:s a', time()); ?>">
    <title><?php echo $page_title; ?></title> 
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">      
        <?php $this->load->view('common/includes');?>
     <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-78372106-1', 'auto');
  ga('send', 'pageview');

</script>  
  </head>
  <body>
 <div class="main_container_new_top">     
            
            <?php $this->load->view('common/header');?>
            
	  <div class="container_nav_style">	  
            
            <?php 
                $this->load->helper('breadcrumb');
                echo create_breadcrumb(); 
            ?>    
            
		<div class="container container_row">

                 <div class="row row_pushdown">
            
                    <?php
                        $this->load->view('common/sidemenu');
                    ?>
                    
		    <?php  $this->load->view($main_content); ?> 
                    
      </div>
<div style="clear:both;">
</div>
               
                <?php $this->load->view('common/footer'); ?>
               
    </div>
</div>	
</div>
 
<div class="modal" id="ex1" style="display:none;">
  <p>
  <h2 class="panel_heading_style">Heading Goes Here...</h2>
  Detail Goes here.  <br> 
  <div class="popup_cancel">
  <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
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
