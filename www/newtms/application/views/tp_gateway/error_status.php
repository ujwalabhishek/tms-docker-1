<div class="col-md-10">
     <?php
   
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?> 
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Error Response</h2>   
    
    <h2 class="sub_panel_heading_style">Error Details</h2>
    
    <?php 
    if(!empty($error)){
    foreach($error as $err){
    ?>
    <div class="alert alert-danger dang">
        <strong><?php echo $err->field; ?></strong> <?php echo $err->message;?>
    </div>
    
    <?php 
    }
    }else{
    
    ?>
    
    <div class="alert alert-danger dang">
       <strong>Oops!</strong> You are doing something wrong! Contact System Administrator..
    </div>
    
    
    <?php } ?>
</div>
<style>
    
    .comment{
        color:grey;
    }
    .label_font{
        color:red;
    }
    
    .dang{
        padding: 20px !important;
    font-size: 14px !important;
    text-align: center;
    }
</style>