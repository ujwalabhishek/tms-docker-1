<?php  
$this->load->helper('form');
?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/wedgit.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Widgets</h2>
   
    <div class="bs-example">
        <form method="post" action="">
            <?php $x = $_POST['width'];?>
         <table class="table table-striped">      
                <tbody>
                <tr>
                        <td class="td_heading">Widgets:<?php
                        $x = base_url();
                        $y = explode("//",$x);
                        $z = explode(".",$y[1]);
                    
                        ?><span class="required">*</span></td>
                        <td colspan="3">
<!--                            <blockquote cite="http://www.worldwildlife.org/who/index.html"></blockquote> -->
                            <?php
                                $widg = array(
                                    'name' => 'widgets',
                                    'id' => 'widgets',
                                    'rows' => '5',
                                    'cols' => '100',
                                    'value' => '<iframe src="http://'.$z[0].'.biipbyte.co/course/com_page" frameborder="0" styel="width:800px; height:500px;"></iframe>'
                                );
                                echo form_textarea($widg);
                                $company = array(
                                    'name' => 'comp',
                                    'id' => 'comp',
                                    'type' => 'hidden',
                                    'value' => $z[0]
                                );
                            ?>
                           
                            <?php echo form_input($company); ?>                                                                              
                        </td>                    
                    </tr>
                    
                     
                    <tr>
                        <td class="td_heading" width="20%">Width:</td>
                        <?php
                        $width_val = array(
                            'name' => 'width',
                            'id' => 'width',
                            'type' => 'text'
                        );
                        ?> 
                         <td width="30%">
                            <?php echo form_input($width_val); ?> px
                           
                        </td>
                    </tr>
                    
                     <tr>
                        <td class="td_heading" width="20%">Height:</td>
                        <?php
                        $height_val = array(
                            'name' => 'height',
                            'id' => 'height',
                            'type' => 'text'
                           
                        );
                        ?> 
                         <td width="30%">
                            <?php echo form_input($height_val); ?> px
                           
                        </td>
                    </tr>
                  
                  
                    <tr>         
                    </tr>        
               
                  
                </tbody>
            </table> 
        
       	
        <div class="button_class99">                
            <button class="btn btn-primary" type="submit" id="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Genrate Code</button> &nbsp; &nbsp;                
        </div>    
        </form>
    </div>          
</div>
<?php echo form_close(); ?>  
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-multiselect.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-2.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/add_new_course.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/course_common.js"></script>
