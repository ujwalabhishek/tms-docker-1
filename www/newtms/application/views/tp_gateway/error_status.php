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
    <table class="table table-striped">
        <tbody>
            <tr width="20%">                        
                <td class="td_heading" width="25%">Field:<span class="required">*</span></td>
                <td width='25%'><label class="label_font"><?php echo $err->field; ?></label></td>
            </tr>

            <tr>                        
                <td class="td_heading">Message:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $err->message;?></label></td>
               
            </tr>            
        </tbody>
    </table>
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