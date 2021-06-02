  
<div class="col-md-10">
    <?php
        $atr = 'id="courserun_form" name="courserun_form"';
        echo form_open("tp_gateway/verify_courserun?status=update", $atr);
    ?>  
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Add Course Run Details</h2>   
    <div><?php echo validation_errors('<span class="error">', '</span>'); ?></div>
    <h2 class="sub_panel_heading_style">COURSE</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading" width="20%">Course Reference Number:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $coursedetails->reference_num ?? $this->input->post('crse_ref_no'); ?></label>
                    <span id="crse_ref_no_err"></span>
                </td>
                <td class="td_heading">TP UEN:<span class="required">*</span></td>
                <td>
                    <label class="label_font"></label><?php echo $tenant->comp_reg_no ?? $this->input->post('tp_uen'); ?>
                    <span id="tp_uen_err"></span>
                </td>
                <input type="hidden" name="crse_ref_no" value="<?php echo $coursedetails->reference_num?? $this->input->post('crse_ref_no'); ?>" id="crse_ref_no">
                <input type="hidden" name="tp_uen" value="<?php echo $tenant->comp_reg_no??$this->input->post('tp_uen'); ?>" id="tp_uen">
            </tr>
        </tbody>
    </table> 
    
    <h2 class="sub_panel_heading_style">RUN</h2>
    
     <table class="table table-striped">
        <tbody>
            <tr>
                <td width="20%" class="td_heading">Mode Of Training:<span class="required">*</span></td>
                <td width="20%">
                     <?php
                        
                        $modeoftraining = array();
                        $modeoftraining[''] = 'Please Choose';
                        $modeoftraining[1] = '1-Classroom';
                        $modeoftraining[2] = '2-Asynchronous eLearning';
                        $modeoftraining[3] = '3-In-house';
                        $modeoftraining[4] = '4-On-the-Job';
                        $modeoftraining[5] = '5-Practical / Practicum';
                        $modeoftraining[6] = '6-Supervised Field';
                        $modeoftraining[7] = '7-Traineeship';
                        $modeoftraining[8] = '8-Assessment';
                        $modeoftraining[9] = '9-Synchronous eLearning';
                        echo form_dropdown('modeoftraining', $modeoftraining, $this->input->post('modeoftraining')??'9', 'id="modeoftraining"');
                        ?>
                    
                    <span id="modeoftraining_err"></span>
                
                </td>
                <td colspan="2"> <label class="label_font"></label>
                    <div style='color:grey'>(Mode of training code - Code Description,1 Classroom,2 Asynchronous eLearning,3 In-house,4 On-the-Job,5 Practical / Practicum,6 Supervised Field,7 Traineeship,8 Assessment,9 Synchronous eLearning)</div>
                </td>
            </tr>

            <tr>
                <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                    <td>
                    <label class="label_font">
                        <?php
                        $crs_admin_email = array(
                            'name' => 'crs_admin_email',
                            'id' => 'crs_admin_email',
                            'value' => $this->input->post('crs_admin_email')??'tmsadmin@mailinator.com',
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($crs_admin_email);
                        ?>
                    </label>
                    <span id="crs_admin_email_err"></span>
                    </td>
                <td colspan="2"> <label class="label_font"></label>
                    <div style='color:grey'>Course admin email is under course run level that can be received the email from 'QR code Attendance Taking','Course Attendance with error' and 'Trainer information not updated'</td></div>
            </tr>

            <tr>
                <td class="td_heading">Registration Open Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo date('Ymd', strtotime($class->class_start_datetime))??$this->input->post('reg_open_date'); ?>
                    <span id="reg_open_date_err"></span>
                </td>
                <td class="td_heading">Registration Close Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo date('Ymd', strtotime($class->class_end_datetime))??$this->input->post('reg_close_date'); ?>
                    <span id="reg_close_date_err"></span>
                </td>
                <input type="hidden" name="reg_open_date" value="<?php echo date('Ymd', strtotime($class->class_start_datetime))??$this->input->post('reg_open_date'); ?>" id="reg_open_date">
                <input type="hidden" name="reg_close_date" value="<?php echo date('Ymd', strtotime($class->class_end_datetime))??$this->input->post('reg_close_date'); ?>" id="reg_close_date">
            </tr>

            <tr>
                <td class="td_heading">Course Start Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo date('Ymd', strtotime($class->class_start_datetime))??$this->input->post('crse_start_date'); ?>
                    <span id="crse_start_date_err"></span>
                </td>
                <td class="td_heading">Course End Date:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo date('Ymd', strtotime($class->class_end_datetime))??$this->input->post('crse_end_date'); ?>
                    <span id="crse_end_date_err"></span>
                </td>
                <input type="hidden" name="crse_start_date" value="<?php echo date('Ymd', strtotime($class->class_start_datetime))??$this->input->post('crse_start_date'); ?>" id="crse_start_date">
                <input type="hidden" name="crse_end_date" value="<?php echo date('Ymd', strtotime($class->class_end_datetime))??$this->input->post('crse_end_date'); ?>" id="crse_end_date">
            </tr>

            <tr>
                <td class="td_heading">Schedule Info Code:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $this->input->post('schedule_info_code')??'01'; ?></label></td>
                <td class="td_heading">Schedule Info Description:<span class="required">*</span></td>
                <td><label class="label_font"><?php echo $this->input->post('schedule_info_des') ??'Description'; ?></label></td>
                <input type="hidden" name="schedule_info_code" value="<?php echo $this->input->post('schedule_info_code') ?? '01'; ?>">
                <input type="hidden" name="schedule_info_des" value="<?php echo $this->input->post('schedule_info_des') ?? 'Description'; ?>">
            </tr>

            <tr>
                <td class="td_heading">Schedule Info:<span class="required">*</span></td>
                <td colspan='3'><label class="label_font"><?php echo $this->input->post('crse_end_date') ?? date('dM', strtotime($class->class_start_datetime)).' : '.date('D', strtotime($class->class_start_datetime)).' / '.date('h:i A', strtotime($class->class_start_datetime)).' - '.date('h:i A', strtotime($class->class_end_datetime)); ?></label></td>
                <input type="hidden" name="schedule_info" value="<?php echo $this->input->post('schedule_info') ?? date('dM', strtotime($class->class_start_datetime)).' : '.date('D', strtotime($class->class_start_datetime)).' / '.date('h:i A', strtotime($class->class_start_datetime)).' - '.date('h:i A', strtotime($class->class_end_datetime)); ?>" id="schedule_info">
            </tr>

            <tr>
                <td class="td_heading">Classroom Venue(TMS):</td>
                <td colspan='3'><?php echo rtrim($ClassLoc, ', '); ?></td>
            </tr>
            <tr>                        
                <td class="td_heading"> Venue Building:<span class="required">*</span></td>
                <td colspan='3'>
                    <label class="label_font">
                        <?php
                        $venue_building = array(
                            'name' => 'venue_building',
                            'id' => 'venue_building',
                            'value' => $this->input->post('venue_building')??'HONG LIM COMPLEX',
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_building);
                        ?>
                    </label>
                    <span id="venue_building_err"></span>
                </td>
             </tr>
            <tr>                        
                <td class="td_heading"> Venue Block:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                        <?php
                        $venue_block = array(
                            'name' => 'venue_block',
                            'id' => 'venue_block',
                            'value' => $this->input->post('venue_block')??'531',
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_block);
                        ?>
                    </label>
                    <span id="venue_block_err"></span>
                </td>
                <td class="td_heading">Venue Street:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <?php
                    $venue_street = array(
                        'name' => 'venue_street',
                        'id' => 'venue_street',
                        'value' => $this->input->post('venue_street')??'Upper Cross Street',
                        'maxlength' => 50,
                        "class" => "upper_case"
                    );
                    echo form_input($venue_street);
                    ?>
                    </label>
                     <span id="venue_street_err"></span>
                </td>
            </tr>
            
            <tr>                        
                <td class="td_heading"> Venue Floor:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                        <?php
                        $venue_floor = array(
                            'name' => 'venue_floor',
                            'id' => 'venue_floor',
                            'value' => $this->input->post('venue_floor')??'03',
                            'maxlength' => 50,
                            "class" => "upper_case"
                        );
                        echo form_input($venue_floor);
                        ?>
                    </label>
                    <span id="venue_floor_err"></span>
                </td>
                <td class="td_heading">Venue Unit:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <?php
                    $venue_unit = array(
                        'name' => 'venue_unit',
                        'id' => 'venue_unit',
                        'value' => $this->input->post('venue_unit')??'40',
                        'maxlength' => 50,
                        "class" => "upper_case"
                    );
                    echo form_input($venue_unit);
                    ?>
                    </label>
                     <span id="venue_unit_err"></span>
                </td>
            </tr>

            <tr>                        
                <td class="td_heading">Venue Postal Code:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <?php
                    $venue_postalcode = array(
                        'name' => 'venue_postalcode',
                        'id' => 'venue_postalcode',
                        'value' => $this->input->post('venue_postalcode')??'050531',
                        'maxlength' => 50,
                        "class" => "upper_case"
                    );
                    echo form_input($venue_postalcode);
                    ?>
                    </label>
                    <span id="venue_postalcode_err"></span>
                </td>
                <td class="td_heading">Venue Room:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <?php
                    $venue_room = array(
                        'name' => 'venue_room',
                        'id' => 'venue_room',
                        'value' => $this->input->post('venue_room')??'HL2',
                        'maxlength' => 50,
                        "class" => "upper_case"
                    );
                    echo form_input($venue_room);
                    ?>
                    </label>
                     <span id="venue_room_err"></span>
                </td>
            </tr>

            <tr>                        
                <td class="td_heading">Course Intake Size:<span class="required">*</span></td>
                <td><label class="label_font"></label><?php echo $class->total_seats ?? $this->input->post('crse_intake_size'); ?>
                    <span id="crse_intake_size_err"></span>
                </td>
                <td colspan="2"> <label class="label_font"></label>Course run intake size. It's maximum pax for a class</td>
                <input type="hidden" name="crse_intake_size" value="<?php echo $class->total_seats ?? $this->input->post('crse_intake_size'); ?>" id="crse_intake_size">
                
            </tr>

            <tr>                        
                <td class="td_heading">Course Vacancy Code:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                        <?php 
                        if($booked_seats >= $class->total_seats){
                            $crse_vacancy_code= "F";
                        }elseif($booked_seats < $class->total_seats && $booked_seats > ($class->total_seats/2)){
                            $crse_vacancy_code= "L";
                        }elseif($booked_seats < $class->total_seats && $booked_seats < ($class->total_seats/2)){
                            $crse_vacancy_code= "A";
                        }
                        echo $crse_vacancy_code ?? $this->input->post('crse_vacancy_code');
                        ?>
                    </label>
                    <span id="crse_vacancy_code_err"></span>
                    <div style='color:grey'>A  - Available ,F  - Full, L  - Limited Vacancy</div>
                </td>
                <td class="td_heading">Course Vacancy Description:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <?php 
                        if($booked_seats >= $class->total_seats){
                            $crse_vacancy_description= "Full";
                        }elseif($booked_seats < $class->total_seats && $booked_seats > ($class->total_seats/2)){
                            $crse_vacancy_description= "Limited Vacancy";
                        }elseif($booked_seats < $class->total_seats && $booked_seats < ($class->total_seats/2)){
                            $crse_vacancy_description= "Available";
                        }
                        echo $crse_vacancy_description ?? $this->input->post('crse_vacancy_description');
                        ?>
                                
                    </label>
                    <span id="crse_vacancy_description_err"></span>
                </td>
                <input type="hidden" name="crse_vacancy_code" value="<?php echo $crse_vacancy_code ?? $this->input->post('crse_vacancy_code'); ?>" id="crse_vacancy_code">
                <input type="hidden" name="crse_vacancy_description" value="<?php echo $crse_vacancy_description ?? $this->input->post('crse_vacancy_description'); ?>" id="crse_vacancy_description">
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

    
    <h2 class="sub_panel_heading_style">TRAINER</h2>
    <?php 
    $sl = 1;
    foreach($ClassTrainer as $trainer){
    ?>
    <table class="table table-striped">
        <tbody>
             <tr><td style='background: #a4dfc4;'><i><b>Trainer-<?php echo $sl;?><b><i></td></tr>
            <tr>                        
                <td class="td_heading" width="20%">Trainer Name:<span class="required">*</span></td>
                <td width='40%'>
                    <label class="label_font"><?php echo $this->input->post('trainer_name') ?? $trainer->first_name.' '.$trainer->last_name; ?>
                    </label> 
                    <span id="trainer_name_err"></span></td>
                <input type="hidden" name="trainer_name[]" value="<?php echo $this->input->post('trainer_name') ?? $trainer->first_name.' '.$trainer->last_name; ?>" id="trainer_name">
                <input type="hidden" name="trainer_id[]" value="<?php echo $this->input->post('trainer_id') ?? $trainer->user_id; ?>" id="trainer_id">
                <td class="td_heading">Trainer Email:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <?php
                    $trainer_email = array(
                        'name' => 'trainer_email',
                        'id' => 'trainer_emailh',
                        'value' =>'tmstrainer@mailinator.com',//($trainer->registered_email_id ?? $this->input->post('trainer_email'))?? 
                        'maxlength' => 50,
                        'disabled' =>'disabled',
                        "class" => "upper_case"
                    );
                    echo form_input($trainer_email);
                    ?>
                    </label>
                     <input type="hidden" name="trainer_email[]" value="<?php echo 'tmstrainer@mailinator.com';//($trainer->registered_email_id ?? $this->input->post('trainer_email'))??;?>" id="trainer_email">
                    <span id="trainer_email_err"></span>
                </td>
            </tr>

            <tr>                        
                <td class="td_heading">Trainer Type Code:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                    <select name="ttcode[]" id="ttcode<?php echo $sl;?>">
                            <option value="1">1-Existing</option>
                            <option value="2" selected="selected">2-New</option>
                        </select>
                    
                    <span id="ttcode<?php echo $sl;?>_err"></span>
                    </label>
                    <div style='color:grey'>1-(Existing) ,2-(New)</div>
                </td>
                <td class="td_heading" width="13%">Trainer Description:<span class="required">*</span></td>
                <td><label class="label_font"></label>NEW</td>
                <input type="hidden" name="trainer_des[]" value="NEW" id="trainer_des">
            </tr>

            <tr>                        
                <td class="td_heading">inTrainingProviderProfile:<span class="required">*</span></td>
                <td>
                    <label class="label_font">
                        <select name="itpf[]" id="itpf<?php echo $sl;?>">
                            <option value="1" selected="selected">True</option>
                            <option value="2">False</option>
                        </select>
                    
                    <span id="itpf<?php echo $sl;?>_err"></span>
                    </label>
                    <div style='color:grey'>For trainerType as "1-Existing" trainer, fill up the Trainer name, email and leave the rest of the Trainer fields empty. API will get the details from the TP Profile,For trainerType as "2-New" trainer, please fill in all required details. If inTrainingProviderProfile is set to "true", the new added trainer will be saved into trainer profile as well as linked to this specific course run; otherwise, this trainer is linked to this specific course run only.</div>
                </td>
                <td class="td_heading">Trainer ID:<span class="required">*</span></td>
                <td><label class="label_font">
                    <?php
                    $trainer_id = array(
                        'name' => 'trainer_id[]',
                        'id' => 'trainer_idh',
                        'value' => $this->input->post('trainer_id[]'),
                        'maxlength' => 50,
                        'disabled' =>'disabled',
                        "class" => "upper_case"
                    );
                    echo form_input($trainer_id);
                    ?>
                    </label>
                    <span id="trainer_id_err"></span>
                    <input type="hidden" name="trainer_id[]" value="" id="trainer_id">
                    <div style='color:grey'>The unique Trainer id for existing trainer. For new trainer, leave it blank.</div>
                </td>
            </tr>
            <tr><td colspan='4'><br></td></tr>
             
        </tbody>
    </table>
    <script>
    $(document).ready(function() {
        $('#ttcode<?php echo $sl;?>').on('change', function() { 
            if($('#ttcode<?php echo $sl;?>').val() == 2){
                $('#itpf<?php echo $sl;?>').val('1');
            }else{
                $('#itpf<?php echo $sl;?>').val('2');
            }
        }); 
        $('#itpf<?php echo $sl;?>').on('change', function() { 
            if($('#itpf<?php echo $sl;?>').val() == 2){
                $('#ttcode<?php echo $sl;?>').val('1');
            }else{
                $('#ttcode<?php echo $sl;?>').val('2');
            }
        }); 
    });   

    </script>
    <?php $sl++;
    } 
     
    ?>

<table class='table table-striped'>
    <tbody>
        <input type="hidden" name="course_id" value="<?php echo $class->course_id ?? $this->input->post('course_id'); ?>">
        <input type="hidden" name="class_id" value="<?php echo $class->class_id ?? $this->input->post('class_id'); ?>">
         <input type="hidden" name="courserun_id" value="<?php echo $courserun_id ?? $this->input->post('courserun_id'); ?>">   
        <tr>
            <td colspan='4' class='text-center'><button type='submit' id='crse_run_btn'>Verify & Proceed</button></td>
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
        if(validate_courserun()){
            var self = $(this),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
        }else{
            return false;
        }
        
        });
    });  
    function validate_courserun(){
        var retVal = true;
        $crsrefno=$('#crse_ref_no').val();
        if ($crsrefno == null || $crsrefno == '') {
            $("#crse_ref_no_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#crse_ref_no_err").text("").removeClass('error');
        }
        $tp_uen=$('#tp_uen').val();
        if ($crsrefno == null || $tp_uen == '') {
            $("#tp_uen_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#tp_uen_err").text("").removeClass('error');
        }
        $modeoftraining=$('#modeoftraining').val();
        if ($modeoftraining == null || $modeoftraining == '') {
            $("#modeoftraining_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#modeoftraining_err").text("").removeClass('error');
        }
        $crs_admin_email=$('#crs_admin_email').val();
        if ($crs_admin_email == null || $crs_admin_email == '') {
            $("#crs_admin_email_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            if(!valid_email_address($crs_admin_email)){
                $("#crs_admin_email_err").text("[Invalid Email]").addClass('error');
                 retVal = false;
            }else{
                $("#crs_admin_email_err").text("").removeClass('error');
            }
        }
        $reg_open_date=$('#reg_open_date').val();
        if ($reg_open_date == null || $reg_open_date == '') {
            $("#reg_open_date_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#reg_open_date_err").text("").removeClass('error');
        }
        $reg_close_date=$('#reg_close_date').val();
        if ($reg_close_date == null || $reg_close_date == '') {
            $("#reg_close_date_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#reg_close_date_err").text("").removeClass('error');
        }
        
        $crse_start_date=$('#crse_start_date').val();
        if ($crse_start_date == null || $crse_start_date == '') {
            $("#crse_start_date_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#crse_start_date_err").text("").removeClass('error');
        }
        
        $crse_end_date=$('#crse_end_date').val();
        if ($crse_end_date == null || $crse_end_date == '') {
            $("#crse_end_date_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#crse_end_date_err").text("").removeClass('error');
        }
        $venue_building=$('#venue_building').val();
        if ($venue_building == null || $venue_building == '') {
            $("#venue_building_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_building_err").text("").removeClass('error');
        }
        $venue_floor=$('#venue_floor').val();
        if ($venue_floor == null || $venue_floor == '') {
            $("#venue_floor_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_floor_err").text("").removeClass('error');
        }
        $venue_unit=$('#venue_unit').val();
        if ($venue_unit == null || $venue_unit == '') {
            $("#venue_unit_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_unit_err").text("").removeClass('error');
        }
        $venue_postalcode=$('#venue_postalcode').val();
        if ($venue_postalcode == null || $venue_postalcode == '') {
            $("#venue_postalcode_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_postalcode_err").text("").removeClass('error');
        }
        
        $venue_room = $('#venue_room').val();
        if ($venue_room == null || $venue_room == '') {
            $("#venue_room_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_room_err").text("").removeClass('error');
        }
        
        $venue_block = $('#venue_block').val();
        if ($venue_block == null || $venue_block == '') {
            $("#venue_block_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_block_err").text("").removeClass('error');
        }
        
        $venue_street = $('#venue_street').val();
        if ($venue_street == null || $venue_street == '') {
            $("#venue_street_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#venue_street_err").text("").removeClass('error');
        }
        
        $crse_vacancy_code = $('#crse_vacancy_code').val();
        if ($crse_vacancy_code == null || $crse_vacancy_code == '') {
            $("#crse_vacancy_code_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#crse_vacancy_code_err").text("").removeClass('error');
        }
        
        $crse_vacancy_description = $('#crse_vacancy_description').val();
        if ($crse_vacancy_description == null || $crse_vacancy_description == '') {
            $("#crse_vacancy_description_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#crse_vacancy_description_err").text("").removeClass('error');
        }
        $trainer_name = $('#trainer_name').val();
        if ($trainer_name == null || $trainer_name == '') {
            $("#trainer_name_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            $("#trainer_name_err").text("").removeClass('error');
        }
        
        $trainer_email = $('#trainer_email').val();
        if ($trainer_email == null || $trainer_email == '') {
            $("#trainer_email_err").text("[required]").addClass('error');
            retVal = false;
        } else {
            if(!valid_email_address($trainer_email)){
                $("#trainer_email_err").text("[Invalid Email]").addClass('error');
                 retVal = false;
            }else{
                $("#trainer_email_err").text("").removeClass('error');
            }
            
        }
        
        
        
        
        
        return retVal;
    }
    
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