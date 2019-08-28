<?php echo $this->load->view('common/refer_left_wrapper'); ?>
<div class="ref_col ref_col_tax_code">  
    <h2 class="panel_heading_style">
        <span aria-hidden="true" class="glyphicon glyphicon-user"></span> 
        Class Member
    </h2>
    <div style="width: 100%; ">
        <?php $class['class_id'] = $class_id;
                   $class['course_id'] = $course_id;
            ?>  
    <div class="tax_col" style="width: 45%;float: left; margin-left:20px;">
    <br/>
        <h2 class="sub_panel_heading_style sub_panel_heading_ref">Are  you  an  existing  member?
            
        </h2>
        
        <div class="tax_code_col" id='tax_code_div'>
     
            
    <table  style="width:70%; margin-top:20px; margin-left:40px; " bordr="3">      
                                
        <tr>
            <td>   
                <button type="submit"  class="btn btn-sm btn-info"> <a style="text-decoration:none;color:black" href="<?php echo base_url();?>course/class_enroll/<?php echo $class['course_id']; ?>/<?php echo $class['class_id']; ?>"><strong>Existing Member</strong></a>
                 <span class="glyphicon glyphicon-chevron-right"></span>
                 <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
               
            </td>
           
        </tr>
   
    </table>
         
    <br/>  
        </div>     
        <?php
        echo form_close();
        ?>
    </div>
    
     <div class="tax_col" style="width: 45%;float: right; margin-right:20px;"">
    <br/>
        <h2 class="sub_panel_heading_style sub_panel_heading_ref">New Member
            
        </h2>
        
        <div class="tax_code_col" id='tax_code_div'>
     
            
    <table  style="width:40%;  margin-left:25%; " bordr="3">      
                              
        <tr>
           
            <td>           
                <button type="submit"  class="btn btn-sm btn-info"><a style="text-decoration:none;color:black" href="<?php echo base_url();?>user/add_trainee/<?php echo $class['course_id']; ?>/<?php echo $class['class_id']; ?>"><strong>New Member</strong></a>  
                <span class="glyphicon glyphicon-chevron-right"></span>
                <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
            </td>
        </tr>
   
    </table>
         
    <br/>  
        </div>     
        <?php
        echo form_close();
        ?>
    </div>
        <div>
</div>
<script>
    $(document).ready(function(){
        $('.enroll_now_link').click(function(){
            var course = $(this).data('course');
            var cls = $(this).data('class');
            var href_link = $(this).attr('href');
            $('.course_name').text(course);
            $('.class_name').text(cls);
            $('.href_link').attr('href',href_link);
            $('#ex11').modal();
            return false;
        });
    });
</script>