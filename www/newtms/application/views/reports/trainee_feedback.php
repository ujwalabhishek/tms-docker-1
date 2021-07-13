<?php
$course_id = set_value('course_id');
if (empty($course_id)) {
    $course_id = key($courses);
}

$class_id = set_value('class_id');
if (empty($class_id)) {
    $class_id = key($classes);
}


?>
<script type="text/javascript">
    var SITE_URL = '<?php echo site_url(); ?>';
    var class_id = <?php echo $class_id ?>;
    var course_id = <?php echo $course_id ?>;
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/trainee_feedback_report.js?0.00000001"></script>

<div class="col-md-10">
    <?php
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    if (!empty($success)) {
        echo '<div class="success">' . $success . '</div>';
    }
    if (!empty($error)) {
        echo '<div class="error1">' . $error . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-comment"></span> Reports - Trainee Feedbacks</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("reports/trainee_feedback", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Course Name:<span class="required">*</span></td>
                    <td>
                        <?php
                        $attr_js = 'id="select_course_id"';
                        echo form_dropdown('course_id', $courses, $course_id, $attr_js);
                        ?>
                        <span id="select_course_id_err"></span>
                    </td>
                    <td class="td_heading">Class Name:<span class="required">*</span></td>
                    <td>
                        <?php
                        if ($class_id > 0) {
                            $attr_js = 'id="select_class_id"';
                        } else {
                            $attr_js = 'id="select_class_id" disabled="disabled"';
                        }
                        echo form_dropdown('class_id', $classes, $class_id, $attr_js);
                        ?>
                        <span id="select_class_id_err"></span>
                    </td>                    
                    <td align="center">
                        <button type="submit" class="btn btn-xs btn-primary no-mar" name="feedback_search_button" title="Generate Report" value="Generate Report"><span class="glyphicon glyphicon-search"></span>Search</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div style="clear:both;"></div>
    <br/>
    <div style="clear:both;"></div>
    <?php
    unset($courses[0]);
    if (empty($courses)) {
        ?>
        <script>
    $(document).ready(function() {
        $('#select_course_id').attr('disabled', 'disabled');
        $('#select_class_id').attr('disabled', 'disabled');
        $('button[name="feedback_search_button"]').attr('disabled', 'disabled');
    });
        </script>
        <table class="table table-striped">
            <tr class="danger">
                <td colspan="4" align="center" class="error">There are no trainee feedbacks available.</td>
            </tr>
        </table>
        <?php
    }
    ?>
    <?php if (empty($tabledata) && !empty($_POST)) { ?>
        <table class="table table-striped">
            <tr class="danger">
                <td colspan="4" align="center" class="error">There are no feedback yet.</td>
            </tr>
        </table>
    <?php } if (!empty($tabledata)) {
        ?>
        <div class="pull-right">
            <a href="print_feedback_form" class="small_text1">
                <span class="label label-default black-btn"><span class="glyphicon glyphicon-print"></span> Print Feedback Form</span>
            </a>
            &nbsp;&nbsp;
            <a href="xls_trainee_feedback?course_id=<?php echo $course_id ?>&class_id=<?php echo $class_id ?>"
               class="small_text1 export_feedback">
                <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export to XLS Feedback</span>
            </a>
        </div>
        <div style="clear:both;"></div>
        <br>
        <?php
        $i = 0;
        foreach ($tabledata as $data) {

            $color = $i % 2 == 0 ? 'box_color1' : '';
            ?>
            <div class="panel panel-default <?php echo $color ?>">
                <div class="panel-body box_color1_padding">
                    <div class="table-responsive">
                        <table class="no_border_table table" >
                            <tbody>
                                <tr>
                                    <td width="15%"><strong>Trainee Name: </strong></td>
                                    <td width="25%"><span class="trainee_name_<?php echo $data['user_id'] ?>"><?php echo $data['name'] ?></span></td>
                                    <td width="15%"><strong>NRIC/FIN No.:</strong></td>
                                    <td width="45%"><?php echo $data['tax_code'] ?>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($data['feedback_question_id'])) {
                                    ?>
                                    <tr>
                                        <td width="20%"><strong>Feedback Date: </strong></td>
                                        <td width="30%" colspan='3'><?php echo $data['feedback_date'] ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td><strong>Training Score:</strong></td>
                                    <td><?php
                                        if ($data['training_score'] == 'NYC') {
                                            echo "<label style='color:red'>Not Yet Competent</label>";
                                        } else if ($data['training_score'] == 'C') {
                                            echo "<label style='color:green'>Competent</label>";
                                        } else if ($data['training_score'] == 'EX') {
                                            echo "<label style='color:blue'>Exempted</label>";
                                        } else if ($data['training_score'] == 'ABS') {
                                            echo "<label style='color:red'>Absent</label>";
                                        } else if ($data['training_score'] == '2NYC') {
                                            echo "<label style='color:red'>Twice Not Competent</label>";
                                        }else if ($data['training_score'] == 'ATR') {////added by shubhranshu for attrition
                                            echo "<label style='color:red'>Attrition</label>";
                                        } else {
                                            echo "<label>Rating not available</label>";
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if (!empty($data['feedback_question_id'])) {
                                        ?>
                                        <td>
                                            <strong>Other Remarks:</strong>
                                        </td>
                                        <td colspan="2"> <?php echo (strlen($data['other_remarks_trainee']) > 70) ? substr($data['other_remarks_trainee'], 0, 70) . '...' : $data['other_remarks_trainee']; ?>
                                            <?php
                                        } else {
                                            ?>
                                        <td colspan="3">
                                        <?php } ?>
                                        &nbsp;&nbsp;&nbsp;
                                        <div class="pull-right">
                                            <?php
                                            if (!empty($data['feedback_question_id'])) {
                                                ?>
                                                <a href="#" class="view_feedback small_text1" user_id="<?php echo $data['user_id'] ?>">
                                                    View Trainee Feedback
                                                </a>
                                                <?php
                                            } else {
                                                echo '<a id="trainee_update" href="#ex6" rel="modal:open" data-user="' . $data['user_id'] . '" class="trainee_update small_text1">'
                                                . '<span>Update Trainee Feedback</span>'
                                                . '</a>';
                                            }
                                            ?>
                                            &nbsp;&nbsp;&nbsp;
                                            <?php
                                            if (!empty($data['feedback_question'])) {
                                                ?>
                                                <a href="#" class="view_trainer_feedback small_text1" user_id="<?php echo $data['user_id'] ?>">
                                                    View Trainer Feedback
                                                </a>
                                                <?php
                                            } else 
                                        {
                                           
                                                echo '<a id="training_update" href="#ex7" rel="modal:open" data-user="' . $data['user_id'] . '" '
                                                        . 'data-course="' . $data['course_id'] . '" '
                                                        . 'data-class="' . $data['class_id'] .'" '
                                                        . 'data-payment="' . $data['pymnt_due_id'] . '" class="training_update small_text1">'
                                                . '<span>Update Trainer Feedback</span>'
                                                . '</a>';
                                        }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $i++;
        }
        ?>
    <?php } ?>

</div>

<div style="clear:both;"></div><br>

<div style="clear:both;"></div><br>
<script>
    $(document).ready(function() {
        $('#search_form').submit(function() {
            $retval = true;
            $course_id = $('#select_course_id').val();
            if ($course_id == 0) {
                $retval = false;
                disp_err('#select_course_id');
            } else {
                remove_err('#select_course_id');
            }
            $class_id = $('#select_class_id').val();
            if ($class_id == 0) {
                $retval = false;
                disp_err('#select_class_id');
            } else {
                remove_err('#select_class_id');
            }
            if($retval){
                var self = $(this),
                button = self.find('input[type="submit"],button');
                button.attr('disabled','disabled').html('Please Wait..');
            }
            return $retval;
        });
    });
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error_text').html($text);
    }

    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').text('');
    }

</script>
<div class="modal0000 modal-al" id="ex231" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    There are no trainee feedbacks available for this class.<br>
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<?php
$atr = 'id="trainer_feedback_form" name="trainer_feedback_form" ';
echo form_open("reports/trainee_feedback", $atr);
?>
<div class="modal1_050" id="ex7" style="display:none; height:535px;">
    <h2 class="panel_heading_style">Trainer Feedback</h2>   
        <center> <span id="skm" style="display:none"></span></center>
            <span id="tbl" style="display:none">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('CERTCOLDT'); ?>:</td>
                            <td>
                                <?php
                                $collected_on = array(
                                    'name' => 'CERTCOLDT',
                                    'id' => 'collected_on',
                                    'placeholder' => 'dd-mm-yyyy',
                                    'readonly' => 'readonly',
                                );
                                echo form_input($collected_on);
                                ?>                    
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('SATSRATE'); ?>:</td>
                            <td>                    
                                <?php
                                $satisfaction_rating = array('' => 'Select', '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5);
                                $satisfaction_rating_attr = 'id="satisfaction_rating"';
                                echo form_dropdown('SATSRATE', $satisfaction_rating, '', $satisfaction_rating_attr);
                                ?>   
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('CERTCOM1'); ?>:</td>
                            <td>
                                <?php
                                $CERTCOM1_YES = array(
                                    'name' => 'CERTCOM1',
                                    'value' => 'Y',
                                    'id' => 'CERTCOM1_YES'
                                );
                                $CERTCOM1_NO = array(
                                    'name' => 'CERTCOM1',
                                    'id' => 'CERTCOM1_NO',
                                    'value' => 'N',
                                );
                                ?>              
                                <?php echo form_radio($CERTCOM1_YES); ?> Yes &nbsp;&nbsp;
                                <?php echo form_radio($CERTCOM1_NO); ?> No
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('APPKNLSKL'); ?>?</td>
                            <td>                    
                                <?php
                                $APPKNLSKL_YES = array(
                                    'name' => 'APPKNLSKL',
                                    'value' => 'Y',
                                    'id' => 'APPKNLSKL_YES'
                                );
                                $APPKNLSKL_NO = array(
                                    'name' => 'APPKNLSKL',
                                    'id' => 'APPKNLSKL_NO',
                                    'value' => 'N',
                                );
                                ?>              
                                <?php echo form_radio($APPKNLSKL_YES); ?> Yes &nbsp;&nbsp;
                                <?php echo form_radio($APPKNLSKL_NO); ?> No
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('EXPJOBSCP'); ?>?</td>
                            <td>                    
                                <?php
                                $EXPJOBSCP_YES = array(
                                    'name' => 'EXPJOBSCP',
                                    'value' => 'Y',
                                    'id' => 'EXPJOBSCP_YES'
                                );
                                $EXPJOBSCP_NO = array(
                                    'name' => 'EXPJOBSCP',
                                    'id' => 'EXPJOBSCP_NO',
                                    'value' => 'N',
                                );
                                ?>              
                                <?php echo form_radio($EXPJOBSCP_YES); ?> Yes &nbsp;&nbsp;
                                <?php echo form_radio($EXPJOBSCP_NO); ?> No
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('RT3MNTHS'); ?>?</td>
                            <td>                   
                                <?php
                                $RT3MNTHS_YES = array(
                                    'name' => 'RT3MNTHS',
                                    'value' => 'Y',
                                    'id' => 'RT3MNTHS_YES'
                                );
                                $RT3MNTHS_NO = array(
                                    'name' => 'RT3MNTHS',
                                    'id' => 'RT3MNTHS_NO',
                                    'value' => 'N',
                                );
                                ?>              
                                <?php echo form_radio($RT3MNTHS_YES); ?> Yes &nbsp;&nbsp;
                                <?php echo form_radio($RT3MNTHS_NO); ?> No
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('DTCOMMEMP'); ?>:</td>
                            <td>
                                <?php
                                $new_entrance = array(
                                    'name' => 'DTCOMMEMP',
                                    'id' => 'new_entrance',
                                    'placeholder' => 'dd-mm-yyyy',
                                    'readonly' => 'readonly',
                                );
                                echo form_input($new_entrance);
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td class="td_heading"><?php echo get_catname_by_parm('COMYTCOM'); ?>?</td>
                            <td>                    
                                <?php
                                $COMYTCOM_C = array(
                                    'name' => 'COMYTCOM',
                                    'value' => 'C',
                                    'id' => 'COMYTCOM_C',
                                    "checked" => TRUE
                                );
                                $COMYTCOM_NYC = array(
                                    'name' => 'COMYTCOM',
                                    'id' => 'COMYTCOM_NYC',
                                    'value' => 'NYC',
                                );
                                $COMYTCOM_EX = array(
                                    'name' => 'COMYTCOM',
                                    'id' => 'COMYTCOM_EX',
                                    'value' => 'EX',
                                );
                                $COMYTCOM_ABS = array(
                                    'name' => 'COMYTCOM',
                                    'id' => 'COMYTCOM_ABS',
                                    'value' => 'ABS',
                                );
                                $COMYTCOM_2NYC = array(
                                    'name' => 'COMYTCOM',
                                    'id' => 'COMYTCOM_2NYC',
                                    'value' => '2NYC',
                                );
                                $COMYTCOM_ATTRITION = array(
                                    'name' => 'COMYTCOM',
                                    'id' => 'COMYTCOM_ATTRITION',
                                    'value' => 'ATR',
                                );
                                ?>              
                                <?php echo form_radio($COMYTCOM_C); ?> Competent <br/>
                                <?php echo form_radio($COMYTCOM_NYC); ?> Not Yet Competent <br/>                    
                                <?php echo form_radio($COMYTCOM_EX); ?> Exempted<br/>                    
                                <?php echo form_radio($COMYTCOM_ABS); ?> Absent<br/>
                                <?php 
                                if(TENANT_ID == 'T02'){/////below code was added by shubhranshu for xp for attrition option start-----
                                    echo form_radio($COMYTCOM_ATTRITION); echo "Attrition <br/>";
                                }
                                ?> 
                                <?php echo form_radio($COMYTCOM_2NYC); ?> Twice Not Competent                    
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="td_heading">
                                <span style="vertical-align:top;"><?php echo get_catname_by_parm('COMMNTS'); ?>:</span>&nbsp;&nbsp;&nbsp;&nbsp;
                                <span>                        
                                    <?php
                                    $data = array(
                                        'name' => 'COMMNTS',
                                        'id' => 'COMMNTS',
                                        'rows' => '1',
                                        'cols' => '60',
                                        'style' => 'width:70%',
                                        'class' => 'upper_case',
                                        'maxlength' => '250'
                                    );

                                    echo form_textarea($data);
                                    ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="color: blue;">
                    <span>1 - Strongly Disagree</span>&nbsp;&nbsp;
                    <span>2 - Disagree</span>&nbsp;&nbsp;
                    <span>3 - Neutral</span>&nbsp;&nbsp;
                    <span>4 - Agree</span><br/>
                    <span>5 - Strongly Agree</span>
                </div>
                <?php
                echo form_hidden('trainee_user_id', "", 'trainee_user_id');
                echo form_hidden('type', "trainer", 'type');
                echo form_hidden('course_id', $course_id, 'course_id');
                echo form_hidden('class_id', $class_id, 'class_id');
                ?>
                <div class="popup_cance89">        
                    <div class="popup_cancel9">
                        <div rel="modal:close">
                            <button class="btn btn-primary" type="submit" id="lock_att">Save</button>&nbsp;&nbsp;
                            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
                        </div>
                    </div>
                </div>    
                 <div class="attendance_lock" style="display: none; height: 50px;text-align: center">                    
                    <span style="color:red;"> <i>Can`t update the trainer feedback because class attendance is locked. To change it please contact to Administrator.</i>
                    </span>
                    <br/>                       
            </div>
            </span>
</div>
<?php
echo form_close();
?>
<?php
///added by shubhranshu to display the trainee form for wablab
if($tenant_id == 'T20' || $tenant_id == 'T17'){
?>
<div class="modal1_trainee_feedback" id="ex6">
    <?php
    $atr = 'id="feedbackForm" name="validate_form"';
    echo form_open('reports/trainee_feedback', $atr);
    ?> 
    <p>
    <h2 class="panel_heading_style">Trainee Feedback Form <span class="required_i">(5. Strongly agree, 4. Agree, 3. Neutral, 2. Disagree,  1. Strongly disagree )</span></h2>
   <center> <span id="ssp" style="display:none"></span></center>
    <div id ="trainee_fdbk">
    <table class="table table-striped">
        <?php
        $options = array('' => 'Select', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
        ?>
        <tbody>
            
            <tr>
                <td colspan="2" class="td_heading"><strong><u><?php echo  $feedback['FDBCK01']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>     
                <td colspan="2">1.<?php echo $feedback['Q01']['category_name']; ?>
                    <?php
                    $atr = 'id="Q01" class="feed"';
                    echo form_dropdown('Q01', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>     
                <td colspan="2">2.<?php echo $feedback['Q02']['category_name']; ?>
                    <?php
                    $atr = 'id="Q02" class="feed"';
                    echo form_dropdown('Q02', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $feedback['Q03']['category_name']; ?>
                    <?php
                    $atr = 'id="Q03" class="feed"';
                    echo form_dropdown('Q03', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $feedback['Q04']['category_name']; ?>
                    <?php
                    $atr = 'id="Q04" class="feed"';
                    echo form_dropdown('Q04', $options, '', $atr);
                    ?> 
                </td>
            </tr>
          
            <tr>
                <td colspan="2" class="td_heading"><strong><u> <?php echo $feedback['FDBCK02']['category_name'] ?></u></strong> </td>
            </tr>
              <tr>
                <td colspan="2">5.<?php echo $feedback['Q05']['category_name']; ?>
                    <?php
                    $atr = 'id="Q05" class="feed"';
                    echo form_dropdown('Q05', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">6.<?php echo $feedback['Q06']['category_name']; ?>
                    <?php
                    $atr = 'id="Q06" class="feed"';
                    echo form_dropdown('Q06', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">7.<?php echo $feedback['Q07']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q07" class="feed"';
                    echo form_dropdown('Q07', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">8.<?php echo $feedback['Q08']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q08" class="feed"';
                    echo form_dropdown('Q08', $options, '', $atr);
                    ?> 
                </td>
            </tr>
          
            <tr>
                <td colspan="2" class="td_heading"><strong> <u><?php echo $feedback['FDBCK03']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>
                <td colspan="2">9.<?php echo $feedback['Q09']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q09" class="feed"';
                    echo form_dropdown('Q09', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">10.<?php echo $feedback['Q10']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q10" class="feed"';
                    echo form_dropdown('Q10', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Your satisfaction rating of the training program:</strong>
                    <?php 
//                     $atr = 'id="rating"';
//                    echo form_dropdown('rating', $options,'',$atr); ?>
                    <input type='text' name="rating" id='rating'  readonly/>
            </tr>
            <tr>
                <td class="td_heading">11. Other comments thal you feel will help improve the course:</td>
                <td>                   
                    <textarea rows="5" cols="100" name="remarks" id="remarks" class="upper_case" maxlength="500"></textarea>                                   
                    <?php
                    echo form_hidden('trainee_id', "", 'trainee_id');
                    echo form_hidden('type', "trainee", 'type');
                    echo form_hidden('course_id', $course_id, 'course_id');
                    echo form_hidden('class_id', $class_id, 'class_id');
                    ?>                    
                    <span style="float:right;">
                        <button class="btn btn-primary" type="submit" id="save">Save</button>
                        <a href="#" rel="modal:close">
                            <button class="btn btn-primary" type="button">Close</button>
                        </a>
                    </span>
                    
                </td>
            </tr>
        </tbody>
    </table>
    </div>
<?php form_close(); ?>
</p>
</div>
<?php }else{?>
<div class="modal1_trainee_feedback" id="ex6">
    <?php
    $atr = 'id="feedbackForm" name="validate_form"';
    echo form_open('reports/trainee_feedback', $atr);
    ?> 
    <p>
    <h2 class="panel_heading_style">Trainee Feedback Form <span class="required_i">(1. Strongly disagree, 2. Disagree, 3. Neutral, 4. Agree, 5. Strongly agree)</span></h2>
    <center> <span id="ssp" style="display:none"></span></center>
    <div id ="trainee_fdbk">
    <table class="table table-striped">
        <?php
        $options = array('' => 'Select', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
        ?>
        <tbody>
            
            <tr>
                <td colspan="2" class="td_heading"><strong><u> <?php echo $feedback['FDBCK01']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>     
                <td colspan="2">1.<?php echo $feedback['Q01']['category_name']; ?>
                    <?php
                    $atr = 'id="Q01" class="feed"';
                    echo form_dropdown('Q01', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>     
                <td colspan="2">2.<?php echo $feedback['Q02']['category_name']; ?>
                    <?php
                    $atr = 'id="Q02" class="feed"';
                    echo form_dropdown('Q02', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $feedback['Q03']['category_name']; ?>
                    <?php
                    $atr = 'id="Q03" class="feed"';
                    echo form_dropdown('Q03', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $feedback['Q04']['category_name']; ?>
                    <?php
                    $atr = 'id="Q04" class="feed"';
                    echo form_dropdown('Q04', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">5.<?php echo $feedback['Q05']['category_name']; ?>
                    <?php
                    $atr = 'id="Q05" class="feed"';
                    echo form_dropdown('Q05', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">6.<?php echo $feedback['Q06']['category_name']; ?>
                    <?php
                    $atr = 'id="Q06" class="feed"';
                    echo form_dropdown('Q06', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading"><strong><u><?php echo $feedback['FDBCK02']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>
                <td colspan="2">1.<?php echo $feedback['Q07']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q07" class="feed"';
                    echo form_dropdown('Q07', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">2.<?php echo $feedback['Q08']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q08" class="feed"';
                    echo form_dropdown('Q08', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $feedback['Q09']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q09" class="feed"';
                    echo form_dropdown('Q09', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $feedback['Q10']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q10" class="feed"';
                    echo form_dropdown('Q10', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">5.<?php echo $feedback['Q11']['category_name']; ?>:
                <?php
                $atr = 'id="Q11" class="feed"';
                echo form_dropdown('Q11', $options, '', $atr);
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading"><strong><u>C. <?php echo $feedback['FDBCK03']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>
                <td colspan="2">1. <?php echo $feedback['Q12']['category_name']; ?>:
                <?php
                $atr = 'id="Q12" class="feed"';
                echo form_dropdown('Q12', $options, '', $atr);
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">2.  <?php echo $feedback['Q13']['category_name']; ?>:
                <?php
                $atr = 'id="Q13" class="feed"';
                echo form_dropdown('Q13', $options, '', $atr);
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">3.  <?php echo $feedback['Q14']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q14" class="feed"';
                    echo form_dropdown('Q14', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">4.  <?php echo $feedback['Q15']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q15" class="feed"';
                    echo form_dropdown('Q15', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Your satisfaction rating of the training program:</strong>
                    <?php 
//                     $atr = 'id="rating"';
//                    echo form_dropdown('rating', $options,'',$atr); ?>
                    <input type='text' name="rating" id='rating'  readonly/>
            </tr>
            <tr>
                <td class="td_heading">Any other remarks:</td>
                <td>                   
                    <textarea rows="1" cols="100" name="remarks" id="remarks" class="upper_case" maxlength="500"></textarea>                                   
                    <?php
                    echo form_hidden('trainee_id', "", 'trainee_id');
                    echo form_hidden('type', "trainee", 'type');
                    echo form_hidden('course_id', $course_id, 'course_id');
                    echo form_hidden('class_id', $class_id, 'class_id');
                    ?>                    
                    <span style="float:right;">
                        <button class="btn btn-primary" type="submit" id="save">Save</button>
                        <a href="#" rel="modal:close">
                            <button class="btn btn-primary" type="button">Close</button>
                        </a>
                    </span>
                    
                </td>
            </tr>
        </tbody>
    </table>
    </div>
<?php form_close(); ?>
</p>
</div>
<?php } form_close(); ?>
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>-->
 
<script>
    $('.training_update').click(function(){
        
   
            if($('.feed').val() == '')
            {
               
                $('#rating').val("");
            }
     });
// we used jQuery 'keyup' to trigger the computation as the user type
$('.feed').change(function () {
 
    // initialize the sum (total price) to zero
    var sum = 0;
   
     
    // we use jQuery each() to loop through all the textbox with 'price' class
    // and compute the sum for each loop
    var i=0;
        $('.feed').each(function() {
        sum += Number($(this).val());
        i++;
    });
     var average= Math.round(sum/i);
     
    // set the computed value to 'totalPrice' textbox
    
    $('#rating').val(average);
     
});
</script>