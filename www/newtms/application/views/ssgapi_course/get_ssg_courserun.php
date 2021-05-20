  
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
                        <td><label class="label_font"></label></td>
                        <td colspan="2"> <label class="label_font"></label>(Mode of training code - Code Description,1 Classroom,2 Asynchronous eLearning,3 In-house,4 On-the-Job,5 Practical / Practicum,6 Supervised Field,7 Traineeship,8 Assessment,9 Synchronous eLearning)</td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                        <td colspan="2"> <label class="label_font"></label>Course admin email is under course run level that can be received the email from 'QR code Attendance Taking','Course Attendance with error' and 'Trainer information not updated'</td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Registration Open Date:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                        <td class="td_heading">Registration Close Date:</td>
                         <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Course Start Date:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                        <td class="td_heading">Course End Date:</td>
                         <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Schedule Info Code:<span class="required">*</span></td>
                        <td><label class="label_font">01</label></td>
                        <td class="td_heading">Schedule Info Description:<span class="required">*</span></td>
                        <td><label class="label_font">Description</label></td>                        
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Schedule Info:<span class="required">*</span></td>
                        <td colspan='3'><label class="label_font">04Mar : Sat / 5 Sats / 9am - 6pm</label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading"> Venue Floor:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Venue Unit:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Venue Postal Code:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Venue Room:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Course Intake Size:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                          <td colspan="2"> <label class="label_font"></label>Course run intake size. It's maximum pax for a class</td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Course Vacancy Code:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">L</label>
                            <div style='color:grey'>A  - Available F  - Full L  - Limited Vacancy</div>
                        </td>
                        <td class="td_heading">Course Vacancy Description:<span class="required">*</span></td>
                        <td><label class="label_font">Limited Vacancy</label></td>
                    </tr>

                    <tr>                        
                        <td class="td_heading">Session Start Date:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Session End Date:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Session Start Time:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Session End Time:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Session Venue Floor:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Session Venue Unit:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Session Venue Postal Code:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Session Venue Room:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">Trainer Name:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->competency_code; ?></label></td>
                        <td class="td_heading">Trainer Email:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    
                    <tr>                        
                        <td class="td_heading">Trainer Type Code:<span class="required">*</span></td>
                        <td><label class="label_font">1</label>
                        <div style='color:grey'>1-(Existing) ,2-(New)</div>
                        </td>
                        <td class="td_heading">Trainer Description:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    <tr>                        
                        <td class="td_heading">inTrainingProviderProfile:<span class="required">*</span></td>
                        <td><label class="label_font">True(If training Type code 1)</label>
                        <div style='color:grey'>For trainerType as "1-Existing" trainer, fill up the Trainer name, email and leave the rest of the Trainer fields empty. API will get the details from the TP Profile,For trainerType as "2-New" trainer, please fill in all required details. If inTrainingProviderProfile is set to "true", the new added trainer will be saved into trainer profile as well as linked to this specific course run; otherwise, this trainer is linked to this specific course run only.</div>
                        </td>
                        <td class="td_heading">Trainer Email:<span class="required">*</span></td>
                        <td><label class="label_font"></label></td>
                    </tr>
                    
                    
                </tbody>
            </table>
        </div>
    </div>
</div>