<?php
    set_value('course_name');
    set_value('filter_options');
    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
    $pageurl = 'TMSAdmin/'. $controllerurl;
?>

<script type="text/javascript">
    var pageurl = '/<?php echo $pageurl; ?>';
    var ancher = '<?php echo $ancher; ?>';
</script>



<div class="col-md-10">
    
    
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course - Assessment Template</h2>
      
    <div class="bs-example">
        <div class="table-responsive">
             <?php
                $atr = 'id="search_form" name="search_form" method="GET"';
                echo form_open("course/assessment_templates?b=" . $sort_by . "&o=" . $sort_order, $atr);
            ?>
            <table class="table table-striped">      
                <tbody>
                    <tr>
                    <td width="15%" class="td_heading">
                        Search by Course Name:
                    </td>
                    
                    <td width="15%" colspan="2">          
                        <?php                        
                        $sel_value = $sel_course;
                        foreach ($courses as $course) {
                                                        
                            $course_options[$course->course_id] = $course->crse_name.' (#: '.$course->course_id.')';
                        }
                        
                        $attr_crse = 'id="course_name"';
                        echo form_dropdown('course_name', $course_options, $sel_value, $attr_crse);
                        ?>

                    </td>                    
               
                </tr>  
                  <tr>
                        <td class="td_heading">Filter by Status:</td>
                        <td >
                          <?php
                          $filterOptions = array(                           
                            'active'  => 'ACTIVE TEMPLATES',
                            'inactive'    => 'ARCHIVED TEMPLATES',
                           );
                          $attr_filter = 'id="filter_options"';
                          echo form_dropdown('filter_options', $filterOptions,$sel_status,$attr_filter);
                          ?>
                        </td>
                        <td align="center">
                            <button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                        </td>
                    </tr>
                </tbody>
            </table> 
            <?php echo form_close(); ?>
            
        </div>       
        <br>
        <div><br></div>
        <div class="add_button space_style">
            
            <a href="#" class="small_text1" onClick="launch_assmt_new(<?php echo "'" . $sel_value . "'" ?>);return false;">
                <span class="label label-default black-btn">
                    <span class="glyphicon glyphicon-export"></span>
                    Add Assessment Template</span>
            </a>                                                                
             <!--a href="#addnewassmnttemplatePopup"  rel="modal:open" class="small_text1">
                <span class="label label-default black-btn">
                    <span class="glyphicon glyphicon-export"></span>
                    Add Assessment Template</span>
            </a--> &nbsp;&nbsp;
        </div>  
        <div style="clear:both;"></div>
        
        <div class="bs-example">
        <div class="table-responsive">
            
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <?php
                            $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                            $pageurl = $controllerurl;
                    ?>
                    <tr>     
                       
                        <th width="40%" class="th_header" sort="temp.template_title">
                            <a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=temp.template_title&o=" .$ancher; ?>" >
                                Template Title 
                            </a>
                        </th>
                        <th width="12%" class="th_header" sort="temp.template_id">
                            <a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=temp.template_id&o=" .$ancher; ?>" >
                                Id & Version #
                            </a>
                        </th>
                        <th width="25%" class="th_header">Last Modified By</th>
                        <th width="28%" class="th_header">Action</th>
                     </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>                
                            <tr  <?php if ($data['template_status'] == 'INACTIVE') echo 'class="danger"'; ?>>
                            <td><?php echo $data['template_title'] ?></td>
                            <td><?php echo $data['template_id'].' - '.$data['templates_version_id'] ?></td>
                            <td><?php echo $data['first_name'] ?></td>
                            <td>
                                <?php
                                $file_name = 'uploads/files/assessment_templates/template_' . $data['template_id'] . '_' . $data['templates_version_id'] . '.pdf';
                                if(file_exists($file_name)) 
                                {
                                ?>    
                                    <img src="<?php echo base_url(); ?>/assets/images/viewicon.ico">
                                    <a href="<?php echo base_url() . 'uploads/files/assessment_templates/template_' . $data['template_id'] . '_' . $data['templates_version_id'] . '.pdf'; ?>" target="_blank">
                                        <b>View</b>
                                    </a>
                                <?php 
                                }
                                ?>
                                <?php 
                                if ($data['template_status'] != 'INACTIVE')
                                {
                                ?>
                                    &nbsp;<img src="<?php echo base_url(); ?>/assets/images/editpencil.ico">
                                    <a href="#" onClick="launch_assmt_update(<?php echo "'" . $data['template_id'] . "'" ?> ,
                                                                            <?php echo "'" . $data['template_title'] . "'" ?>);return false;">
                                   <b>Change</b>
                                    </a>
                                    &nbsp;  <img src="<?php echo base_url(); ?>/assets/images/remove-red.png">                                
                                    <a href="#" onClick="launch_assmt_deactivate(<?php echo "'" . $data['template_id'] . "'" ?> ,
                                                                            <?php echo "'" . $data['template_title'] . "'" ?>);return false;">
                                        <b>Deactivate</b>
                                    </a>
                                <?php 
                                    }
                                 ?>
                            </td>
                            </tr>
                        <?php
                        }
                }else {
                    echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no assessment templates available for the selected course.</label></td></tr>";
                }
                ?>

                </tbody>
            </table>
        </div>
        <div style="clear:both;"></div><br>    
        <ul class="pagination pagination_style">
            <?php echo $pagination; ?>
        </ul>
    </div>	
         <br>
    </div>          
</div>
<?php echo form_close(); ?>

<!-- Add New Assessment Modal Window -->

<?php
    $form_attributes = array('name' => 'assmnt_template', 'id' => 'assmnt_template', 
                            "onsubmit" => "return(validate_assessment_template());",
                            'enctype' => 'multipart/form-data');
    echo form_open("course/insert_assmnt_template", $form_attributes);
    ?>
    <div class="modal1_077" id="addnewassmnttemplatePopup" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Import Assessment Template</h2>
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td class="td_heading">Select Course:<span class="required">*</span></td>
                        <td>
                            <?php
                               //$course_options_l[''] = 'Select Course Name ...';
                               foreach ($courses as $course):
                                   $course_options_l[$course->course_id] = $course->crse_name.' (#: '.$course->course_id.')';
                               endforeach;
                               $attr_crse = 'id="assmnt_course_name"';
                               echo form_dropdown('assmnt_course_name', $course_options_l, $sel_value, $attr_crse);
                           ?>   
                            <br/>
                             <span id="assmnt_course_name_err"></span>
                        </td>
                    </tr>  
                    
                    <tr>
                        <td class="td_heading">Template Display Name:<span class="required">*</span></td>
                        <td>
                            <input type="text" value="" maxlength='200' id='template_name' name='template_name' class='upper_case alphanumeric' style="width: 98%;"/><br/>
                            <span id="template_name_err"></span>
                        </td>
                    </tr>  
                    <tr>
                        <td class="td_heading">Import Assessment Template:<span class="required">*</span></td>
                        <td>
                            <input name="assmnt_upload" type="file" id="assmnt_upload">
                            <br>
                            <span id="assmnt_upload_err"></span>
                           
                        </td>
                         
     
                     </tr>
                   
                </tbody>
            </table>
        </div>
       
        <span class="required required_i">* Required Fields</span>

        <br>
        <div class="popup_cancel9">
            <div rel="modal:close">
                 <span>
                    <button name="upload" type="submit" id="submit" class="btn btn-xs btn-primary no-mar" value="upload"/>
                    <span class="glyphicon glyphicon-upload"></span> Upload</button>
                </span>
                &nbsp;&nbsp;
                <a href="#" rel="modal:close">
                <button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
        </p>
    </div>
    <?php
    echo form_hidden('course_id', $sel_value);
    echo form_hidden('filter_options', $sel_status);
    echo form_close();
    ?>
<!-- Add New Assessment Modal window ends here -->

<!-- Confirmation Dialog for De-activation Start -->
<?php
$form_attributes = array('name' => 'deactivate_template_form', 'id' => 'deactivate_template_form', "onsubmit" => "return(validate_deactivate_template());");
echo form_open("course/remove_assmnt_template", $form_attributes);
?>
    <div class="modal1_077" id="deactivate_form" style="display:none;">
        <p>
            
        <h2 class="panel_heading_style">Deactivate Template</h2>         
        <strong> Template Id:<span class="red">*</span> </strong>
        <label id="de_template_id" name="de_template_id" class='error'></label>
        <span id="de_template_id_err"></span>
        <br><br>
                       
        <strong> Template Name:<span class="red">*</span> </strong>
        <label id="de_template_name" name="de_template_name" class='error'></label>
        <span id="de_template_name_err"></span>
        <br><br>

        Are you sure you want to deactivate this template?
        <br>
        <span class="required_i red">*Required Field</span>

        <div class="popup_cancel9">
            <div rel="modal:close"><button class="btn btn-primary" type="submit">Deactivate</button>&nbsp;&nbsp;
                            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
    </div>
<?php
echo form_hidden('sel_template_id', "");
echo form_hidden('course_id', $sel_value);
echo form_hidden('filter_options', $sel_status);
echo form_close();
?>

<!-- Confirmation Dialog for De-activation End -->


<!-- Update Assessment Modal Window -->

<?php
    $form_attributes = array('name' => 'assmnt_template_update', 'id' => 'assmnt_template_update', 
                            "onsubmit" => "return(validate_assessment_template_update());",
                            'enctype' => 'multipart/form-data');
    echo form_open("course/change_assmnt_template", $form_attributes);
    ?>
    <div class="modal1_088" id="updateassmnttemplatePopup" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Update Assessment Template</h2>
        <div class="table-responsive">
            <table class="table table-striped">      
                <tbody>
                    
                    <tr>
                        <td class="td_heading">Template Id:<span class="required">*</span></td>
                        <td>
                            <label id="up_template_id" name="up_template_id" class='label_font'></label> 
                            <br/>
                             <span id="up_template_id_err"></span>
                        </td>
                    </tr>  
                    
                    <tr>
                        <td class="td_heading">Course Name:<span class="required">*</span></td>
                        <td>
                            <label id="up_course_name" name="up_course_name" class='label_font'></label> 
                            <br/>
                             <span id="up_course_name_err"></span>
                        </td>
                    </tr>  
                    
                    <tr>
                        <td class="td_heading">Template Display Name:<span class="required">*</span></td>
                        <td>
                            <input type="text" value="" maxlength='200' id='up_template_name' name='up_template_name' class='upper_case alphanumeric' style="width: 98%;"/><br/>
                            <span id="up_template_name_err"></span>
                        </td>
                    </tr>  
                    <tr>
                        <td class="td_heading">Import Assessment Template:</td>
                        <td>
                            <input name="up_assmnt_upload" type="file" id="up_assmnt_upload">
                            <br>
                            <span id="up_assmnt_upload_err"></span>
                           
                        </td>
                         
     
                     </tr>
                   
                </tbody>
            </table>
        </div>
       
        <span class="required required_i">* Required Fields</span>

        <br>
        <div class="popup_cancel9">
            <div rel="modal:close">
                 <span>
                    <button name="upload" type="submit" id="submit" class="btn btn-xs btn-primary no-mar" value="upload"/>
                    <span class="glyphicon glyphicon-upload"></span> Update</button>
                </span>
                &nbsp;&nbsp;
                <a href="#" rel="modal:close">
                <button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
        </p>
    </div>
    <?php
    echo form_hidden('sel_up_template_id', "");
    echo form_hidden('course_id', $sel_value);
    echo form_hidden('filter_options', $sel_status);
    echo form_close();
    ?>
<!-- Update Assessment Modal window ends here -->
<script>
    //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#search_form').on('submit',function() {
        var self = $(this),
        button = self.find('input[type="submit"],button');
        button.attr('disabled','disabled').html('Please Wait..');
        return true;
       
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////////////
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/assessmenttemplate.js"></script>   
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-multiselect.css" type="text/css" />



