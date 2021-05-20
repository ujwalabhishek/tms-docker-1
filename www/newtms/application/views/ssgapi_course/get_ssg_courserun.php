  
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course Detail</h2>   
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    
                    <tr>
                        <td class="td_heading" width="28%">Course Reference Number:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->crse_name; ?></label></td>
                        <td class="td_heading">TP UEN:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Mode Of Training:<span class="required">*</span></td>
                        <td colspan="3"> <label class="label_font"></label>(Mode of training code - Code Description,1 Classroom,2 Asynchronous eLearning,3 In-house,4 On-the-Job,5 Practical / Practicum,6 Supervised Field,7 Traineeship,8 Assessment,9 Synchronous eLearning)</td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Class Type:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                               
                            </label>
                        </td>
                        <td class="td_heading">GST Rate:</td>
                        <td>
                            
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">GST Rules:<span class="required">*</span></td>
                        <td> 
                                                                           
                           
                        </td>
                        <td colspan='2'>
                                                        
                        </td>                          
                    </tr>
                    <tr>
                        <td class="td_heading">Course Duration (in hrs):<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->crse_duration; ?></label></td>
                        <td class="td_heading">Course Reference Number:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->reference_num; ?></label></td>                        
                    </tr>
                    <tr>                        
                        <td class="td_heading">Course Competency Code:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Certification Code/ Level:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->certi_level), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>                        
                        <td class="td_heading">Course Manager:<span class="required">*</span></td>
                        <td colspan="3">
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_managers($course_data->crse_manager), ', '); ?>                                
                            </label>
                        </td>
                    </tr>
                    <tr>                        
                        <td class="td_heading">Course Description:<span class="required">*</span></td>
                        <td colspan="3">
                            <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                                <label class="label_font">
                                    <?php echo $course_data->description; ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" width="55%">Does this course have a validity period?</td>
                        <td colspan="2" width="45%">
                            <table>
                                <tr>
                                    
                                </tr>
                            </table>    
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>