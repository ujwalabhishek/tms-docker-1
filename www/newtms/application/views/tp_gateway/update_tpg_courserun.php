  
<div class="col-md-10">
    <?php
        $atr = 'id="courserun_form" name="courserun_form"';
        echo form_open("tp_gateway/update_courserun_call_tpg", $atr);
    ?>  
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Crosscheck Data To Submit TPG</h2>   
    
    <h2 class="sub_panel_heading_style">COURSE</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading" width="20%">Course Reference Number:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[crse_ref_no]; ?></label>
                   
                </td>
                <td class="td_heading">TP UEN:<span class="required">*</span></td>
                <td>
                    <label class="label_font"></label><?php echo $dat[tp_uen]; ?>
                   
                </td>
                <input type="hidden" name="crse_ref_no" value="<?php echo $dat[crse_ref_no]; ?>" id="crse_ref_no">
                <input type="hidden" name="tp_uen" value="<?php echo $dat[tp_uen]; ?>" id="tp_uen">
            </tr>
        </tbody>
    </table> 
    
    <h2 class="sub_panel_heading_style">RUN</h2>
    
     <table class="table table-striped">
        <tbody>
            <tr>
                <td width="20%" class="td_heading">Mode Of Training:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[modeoftraining]; ?></label>
                <input type="hidden" name="modeoftraining" value="<?php echo $dat[modeoftraining]; ?>">
                <td colspan="2"> <label class="label_font"></label>
                    <div style='color:grey'>(Mode of training code - Code Description,1 Classroom,2 Asynchronous eLearning,3 In-house,4 On-the-Job,5 Practical / Practicum,6 Supervised Field,7 Traineeship,8 Assessment,9 Synchronous eLearning)</div>
                </td>
            </tr>

            <tr>
                <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[crs_admin_email]; ?></label>
                <input type="hidden" name="crs_admin_email" value="<?php echo $dat[crs_admin_email]; ?>">   
                <td colspan="2"> <label class="label_font"></label>
                    <div style='color:grey'>Course admin email is under course run level that can be received the email from 'QR code Attendance Taking','Course Attendance with error' and 'Trainer information not updated'</td></div>
            </tr>

            <tr>
                <td class="td_heading" width='25%'>Registration Open Date:<span class="required">*</span></td>
                <td width='25%'><label class="label_font"></label><?php echo $dat[reg_open_date]; ?>
                    <span id="reg_open_date_err"></span>
                </td>
                <td class="td_heading" width='25%'>Registration Close Date:<span class="required">*</span></td>
                <td width='25%'><label class="label_font"></label><?php echo $dat[reg_close_date]; ?>
                    <span id="reg_close_date_err"></span>
                </td>
                <input type="hidden" name="reg_open_date" value="<?php echo $dat[reg_open_date]; ?>" id="reg_open_date">
                <input type="hidden" name="reg_close_date" value="<?php echo $dat[reg_close_date]; ?>" id="reg_close_date">
            </tr>

            <tr>
                <td class="td_heading">Course Start Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo $dat[crse_start_date]; ?>
                    <span id="crse_start_date_err"></span>
                </td>
                <td class="td_heading">Course End Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo $dat[crse_end_date]; ?>
                    <span id="crse_end_date_err"></span>
                </td>
                <input type="hidden" name="crse_start_date" value="<?php echo $dat[crse_start_date]; ?>" id="crse_start_date">
                <input type="hidden" name="crse_end_date" value="<?php echo $dat[crse_end_date]; ?>" id="crse_end_date">
            </tr>

            <tr>
                <td class="td_heading">Schedule Info Code:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $this->input->post('schedule_info_code')??'01'; ?></label></td>
                <td class="td_heading">Schedule Info Description:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $this->input->post('schedule_info_des') ??'Description'; ?></label></td>
                <input type="hidden" name="schedule_info_code" value="<?php echo $dat[schedule_info_code];?>">
                <input type="hidden" name="schedule_info_des" value="<?php echo $dat[schedule_info_des];?>">
            </tr>

            <tr>
                <td class="td_heading">Schedule Info:<span class="required">*</span></td>
                <td colspan='3'><label class="label_font"><?php echo $dat[schedule_info]; ?></label></td>
                <input type="hidden" name="schedule_info" value="<?php echo $dat[schedule_info];?>">
            </tr>
            <tr>                        
                <td class="td_heading"> Venue Building:<span class="required">*</span></td>
                <td colspan='3'><label class="label_font"><?php echo $dat[venue_building]; ?></label></td>
                <input type="hidden" name="venue_building" value="<?php echo $dat[venue_building];?>">
            </tr>
            <tr>                        
                <td class="td_heading"> Venue Block:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[venue_block]; ?></label></td>
                <input type="hidden" name="venue_block" value="<?php echo $dat[venue_block];?>">
                
                <td class="td_heading">Venue Street:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[venue_street]; ?></label></td>
                <input type="hidden" name="venue_street" value="<?php echo $dat[venue_street];?>">
                
            </tr>
            
            <tr>                        
                <td class="td_heading"> Venue Floor:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[venue_floor]; ?></label></td>
                <input type="hidden" name="venue_floor" value="<?php echo $dat[venue_floor];?>">
                
                <td class="td_heading">Venue Unit:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[venue_unit]; ?></label></td>
                <input type="hidden" name="venue_unit" value="<?php echo $dat[venue_unit];?>">
                
            </tr>

            <tr>                        
                <td class="td_heading">Venue Postal Code:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[venue_postalcode]; ?></label></td>
                <input type="hidden" name="venue_postalcode" value="<?php echo $dat[venue_postalcode];?>">
                
                <td class="td_heading">Venue Room:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[venue_room]; ?></label></td>
                <input type="hidden" name="venue_room" value="<?php echo $dat[venue_room];?>">
               
            </tr>

            <tr>                        
                <td class="td_heading">Course Intake Size:<span class="required">*</span></td>
                
                 <td><label class="label_font"><?php echo $dat[crse_intake_size]; ?></label></td>
                <td colspan="2"> <label class="label_font"></label>Course run intake size. It's maximum pax for a class</td>
                <input type="hidden" name="crse_intake_size" value="<?php echo $dat[crse_intake_size]; ?>" id="crse_intake_size">
            </tr>

            <tr>                        
                <td class="td_heading">Course Vacancy Code:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[crse_vacancy_code]; ?></label></td>
                <td class="td_heading">Course Vacancy Description:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $dat[crse_vacancy_description]; ?></label></td>
                <input type="hidden" name="crse_vacancy_code" value="<?php echo $dat[crse_vacancy_code]; ?>" id="crse_vacancy_code">
                <input type="hidden" name="crse_vacancy_description" value="<?php echo $dat[crse_vacancy_description]; ?>" id="crse_vacancy_description">
            </tr>
        </tbody>
    </table>
    
     <h2 class="sub_panel_heading_style">SESSIONS</h2>
    
    <table class="table table-striped">
        <tbody>
            <?php 
            $ss = 1;
            foreach($sessions as $session){

               if($session['session_type_id'] != 'BRK'){
            ?>
            <tr><td style='background: #a4dfc4;'><i><b>Session-(<?php echo $ss.')'.$session['session_type_id'];?><b><i></td></tr>
            <tr width="20%">                        
                <td class="td_heading" width="20%">Session Start Date:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo date('Ymd', strtotime($session['class_date'])) ?? $this->input->post('sess_start_time'); ?></label></td>
                <td class="td_heading">Session End Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo date('Ymd', strtotime($session['class_date'])) ?? $this->input->post('sess_end_time'); ?></td>
            </tr>

            <tr>                        
                <td class="td_heading">Session Start Time:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo date('h:i A', strtotime($session['session_start_time'])) ?? $this->input->post('sess_start_time'); ?></label></td>
                <td class="td_heading">Session End Time:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo date('h:i A', strtotime($session['session_end_time'])) ?? $this->input->post('sess_end_time'); ?></td>
                
                <input type="hidden" name="sess_start_time[]" value="<?php echo date('h:i', strtotime($session['session_start_time'])) ?? $this->input->post('sess_start_time'); ?>" id="sess_start_time">
                <input type="hidden" name="sess_end_time[]" value="<?php echo date('h:i', strtotime($session['session_end_time'])) ?? $this->input->post('sess_end_time'); ?>" id="sess_end_time">
                <input type="hidden" name="sess_start_date[]" value="<?php echo date('Ymd', strtotime($session['class_date'])) ?? $this->input->post('sess_start_date'); ?>" id="sess_start_date">
                <input type="hidden" name="sess_end_date[]" value="<?php echo date('Ymd', strtotime($session['class_date'])) ?? $this->input->post('sess_end_date'); ?>" id="sess_end_date">
            </tr>
            <tr><td><br></td></tr>
            <?php 
               }
               $ss++;
            } ?>

            
        </tbody>
    </table>

    
    <h2 class="sub_panel_heading_style">TRAINERS</h2>
    <?php 
    $sl = 1;
    foreach($dat[trainer_name] as $trainer){
    ?>
    <table class="table table-striped">
        <tbody>
            <tr><td style='background: #a4dfc4;'><i><b>Trainer-<?php echo $sl;?><b><i></td></tr>
            <tr>                        
                <td class="td_heading" width="25%">Trainer Name:<span class="required">*</span></td>
                <td width='25%'><label class="label_font"><?php echo $trainer ?></label> <span id="trainer_name_err"></span></td>
                <input type="hidden" name="trainer_name" value="<?php echo $trainer; ?>" id="trainer_name">
                <td class="td_heading" width='25%'>Trainer Email:<span class="required">*</span></td>
                <td width='25%'><label class="label_font"><?php echo $dat[trainer_email][$sl]; ?></label> <span id="trainer_email_err"></span></td>
                <input type="hidden" name="trainer_email" value="<?php echo $dat[trainer_email][$sl]; ?>" id="trainer_email">
                <input type="hidden" name="trainer_id" value="<?php echo $dat[trainer_id][$sl]; ?>" id="trainer_id">
            </tr>

            <tr>                        
                <td class="td_heading">Trainer Type Code:<span class="required">*</span></td>
                <td width='40%'><label class="label_font"><?php echo $dat[ttcode][$sl]; ?></label></td>
                
                <td class="td_heading" width="13%">Trainer Description:<span class="required">*</span></td>
                <td><label class="label_font"></label>NEW</td>
            </tr>

            <tr>                        
                <td class="td_heading">inTrainingProviderProfile:<span class="required">*</span></td>
                 <td><label class="label_font"></label>1</td>
                <td class="td_heading">Trainer ID:<span class="required">*</span></td>
                <td><label class="label_font"></label>1</td>
            </tr>
             <tr><td><br></td></tr>
        </tbody>
    </table>
    
    <?php $sl++;
    } 
     
    ?>

<table class='table table-striped'>
    <tbody>
        <input type="hidden" name="course_id" value="<?php echo $dat[course_id];?>">
        <input type="hidden" name="class_id" value="<?php echo $dat[class_id]; ?>">
         <input type="hidden" name="courserun_id" value="<?php echo $dat[courserun_id]; ?>">   
        <tr>
            <td colspan='4' class='text-center'><button type='submit' id='crse_run_btn'>Submit Data To TPG</button></td>
        </tr>
    </tbody>
</table>
 <?php
 echo form_close();
?>
</div>
<script>
    $(document).ready(function() {

        $('#courserun_form').on('submit',function() {
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        });
    });  
    
    
</script>
<style>
    #crse_run_btn{
     text-align: center;
    -moz-box-shadow: inset 0px 1px 0px 0px #54a3f7;
    -webkit-box-shadow: inset 0px 1px 0px 0px #54a3f7;
    box-shadow: inset 0px 1px 0px 0px #54a3f7;
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #007dc1), color-stop(1, #0061a7));
    background: -moz-linear-gradient(top, #007dc1 5%, #0061a7 100%);
    background: -webkit-linear-gradient(top, #007dc1 5%, #0061a7 100%);
    background: -o-linear-gradient(top, #007dc1 5%, #0061a7 100%);
    background: -ms-linear-gradient(top, #007dc1 5%, #0061a7 100%);
    background: linear-gradient(to bottom, #007dc1 5%, #0061a7 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#007dc1', endColorstr='#0061a7',GradientType=0);
    background-color: #007dc1;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    border: 1px solid #124d77;
    display: inline-block;
    cursor: pointer;
    color: #ffffff;
    padding: 6px 17px;
    text-decoration: none;
    text-shadow: 0px 1px 0px #154682;
    text-transform: none;
    letter-spacing: normal;
    font-weight: normal;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    }
</style>