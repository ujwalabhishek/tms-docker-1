<?php
$CI = & get_instance();
$CI->load->model('course_model');
$CI->load->model('class_model');
?>
<!-- AJAX Script to post to the assessment form converter - ENTUTO -->
<script>
    
    function show_batch_confirm() {
        
         $("#submit_batch").modal('show');
    }
    
    function process_forms_batch(tenant_id,course_id,class_id,user_id) {        
        
        var actionUrl ="https://www.biipmi.co/tmsadmin/android/__tms_assessment.php";
        
        var action_data = '{"tenant_id":"' + tenant_id + '","course_id":"' + course_id + '","class_id":"' + 
                                class_id + '","user_id":"' + user_id +  
                                '","_mode":"batch_convert","_submit_check":"1"}';
        var params = JSON.parse(action_data);
       
        $.ajax( {
            type: "POST",
            url: actionUrl,
            dataType: 'json',
            data: params,            
            async: true      
          });
          
          alert("The Batch Job was submitted successfully");
    }
</script>
<!-- End conversion Script - ENTUTO -->

<script>
    $siteurl = '<?php echo site_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classlist.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    /////display if any error from TPG site
     if(!empty($this->session->flashdata('resp_error'))){
        foreach($this->session->flashdata('resp_error') as $err){

        echo '<div class="alert alert-danger dang">
            <strong>'.$err->field.'</strong>'.$err->message.'
        </div>';
        }
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class.png"> Class List</h2>
    <div class="table-responsive">
        <?php
        $this->load->helper('form');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values');
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("classes", $atr);
        ?> 
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%" class="td_heading">Search by Course Name:</td>
                    <td colspan="4">
                        <?php
                        $course_options = array();
                        $course_options[''] = 'Select';
                        foreach ($courses as $k => $v) {
                            $course_options[$k] = $v;
                        }
                        echo form_dropdown('course_id', $course_options, $this->input->get('course_id'), 'id="course_id"');
                        ?>
                        <span id="course_id_err"></span>
                    </td>
                    
                </tr>
                <tr>
                    <td width="15%" colspan="-1" class="td_heading">Class Name:</td>
                    <td colspan="3">
                        <?php
                        $class_options = array();
                        $class_js = 'id="class_id"';
                        $class_options[''] = 'Select';
                        if (!empty($classes)) {
                            $class_js = $class_js;
                            $class_options[''] = 'All';
                        } else {
                            $class_js = $class_js . ' disabled="disabled"';
                        }
                        foreach ($classes as $k => $v) {
                            $class_options[$k] = $v;
                        }
                        echo form_dropdown('class_id', $class_options, $this->input->get('class_id'), $class_js);
                        ?>
                    </td>
                    <td width="10%" align="center"><button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>
                            Search
                        </button>
                    </td>
                </tr>
<!--                <tr>
                    <td width="15%" colspan="-1" class="td_heading">Course Run ID:</td>
                    <td colspan="4">
                        <?php
//                        $class_options = array();
//                        $crsrunid_js = 'id="tpg_course_run_id"';
//                        $tpg_course_run_id_options[''] = 'Select';
//                        if (!empty($courseRunId)) {
//                            $crsrunid_js = $crsrunid_js;
//                            $tpg_course_run_id_options[''] = 'All';
//                        } else {
//                            //$crsrunid_js = $crsrunid_js . ' disabled="disabled"';
//                        }
//                        foreach ($courseRunId as $k => $v) {
//                            $tpg_course_run_id_options[$k] = $v;
//                        }
//                        echo form_dropdown('tpg_course_run_id', $tpg_course_run_id_options, $this->input->get('tpg_course_run_id'), $crsrunid_js);
                        ?>
                    </td>
                    
                </tr>-->
                <tr>
                    <td width="24%" class="td_heading">Filter by Class Status:</td>
                    <td colspan="6">
                        <?php
                        $cls_status_options[''] = 'All';
                        $cls_status = fetch_metavalues_by_category_id(Meta_Values::CLASS_STATUS);
                        foreach ($cls_status as $val):
                            $cls_status_options[$val['parameter_id']] = $val['category_name'];
                        endforeach;
                        echo form_dropdown('class_status', $cls_status_options, $this->input->get('class_status'), 'id="class_status"');
                        ?>                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <br>
    <?php
    if (!empty($courseDetails)) {
        $div_class = 'green';
        if ($courseDetails->crse_status == 'INACTIV') {
            $div_class = 'red';
        }
        ?>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company-detail.png"> Course Details</h2>
        <div class="bs-example">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading">Course Status:</td>
                            <td colspan="3"><label class="label_font <?php echo $div_class; ?>">
                                    <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($courseDetails->crse_status), ', '); ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Course Name:</td>
                            <td><label class="label_font"><?php echo $courseDetails->crse_name; ?></label></td>
                            <td class="td_heading">Pre-Requisite:</td>
                            <td>
                                <label class="label_font"><?php echo rtrim($coursePreReq, ', '); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading">Language:</td>
                            <td><label class="label_font"><?php echo rtrim($courseLang, ', '); ?></label>
                                </select></td>
                            <td class="td_heading">Course Type:</td>
                            <td><label class="label_font"><?php echo rtrim($courseType, ', '); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Class Type:</td>
                            <td><label class="label_font"><?php echo rtrim($courseClassType, ', '); ?></label></td>
                            <td class="td_heading">GST Rate:</td>
                            <td><label class="label_font"><?php echo $GstRates; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Course Duration (in hrs):</td>
                            <td><label class="label_font"><?php echo $courseDetails->crse_duration; ?></label></td>
                            <td class="td_heading">Sales Executive:</td>
                            <td><label class="label_font"><?php echo $SalesExec; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Course Reference Number:</td>
                            <td><label class="label_font"><?php echo $courseDetails->reference_num; ?></label></td>
                            <td class="td_heading">Course Competency Code:</td>
                            <td><label class="label_font"><?php echo $courseDetails->competency_code; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Certification Level:</td>
                            <td><label class="label_font"><?php echo rtrim($certiLevel, ', '); //echo $courseDetails->certi_level;    ?></label></td>
                            <td class="td_heading">Course Manager:</td>
                            <td><label class="label_font"><?php echo rtrim($courseManager, ', '); ?></label></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </td>
            <div><br><br></div>
            <?php
            $not_array = array("TRAINER", "CRSEMGR");
            if (count($tabledata) > 0 && 
                    array_key_exists('EXP_XLS', $this->data['left_side_menu']['CLSS']) 
                    && (!in_array($this->session->userdata('userDetails')->role_id, $not_array))) {
                ?>
                <div class="add_button space_style">
                    <a href="<?php echo base_url() . 'classes/export_class_page' . $export_url; ?>" class="small_text1">
                        <span class="label label-default black-btn">
                        <span class="glyphicon glyphicon-export"></span>Export Page Fields</span>
                    </a> &nbsp;&nbsp; 
                    <a href="<?php echo base_url() . 'classes/export_class_full' . $export_url; ?>" class="small_text1">
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export">
                        </span>Export All Fields</span>
                    </a>
                </div>
            <?php } ?>
            <div style="clear:both;"></div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <?php
                    if (!empty($tabledata)) {
                        ?>
                        <thead>
                            <?php
                            $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                            $pageurl = $controllerurl;
                            ?>
                           <tr>
                                <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_name&o=" . $ancher; ?>" >Class Name</a></th>
                                <th width="16%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_start_datetime&o=" . $ancher; ?>" >Start Date & Time</a></th>
                                <th width="15%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_end_datetime&o=" . $ancher; ?>" >End Date & Time</a></th>
                                <th class="th_header text_move" colspan="2">Seats</th>
                                <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=classroom_trainer&o=" . $ancher; ?>" >Trainee Aide</a></th>
                                <th width="10%" class="th_header text_move"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=classroom_trainer&o=" . $ancher; ?>" >Trainer</a></th>
                                <th width="9%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_language&o=" . $ancher; ?>" >Language</a></th>
                                <th width="9%" class="th_header">Status</th>
                            </tr>
                            <tr>
                                <th colspan="3">&nbsp;</th>
                                <th width="8%" class="text_move">Booked</th>
                                <th width="11%" class="text_move">Available</th>
                                <!--<th width="10%" class="text_move">Total</th>-->
                                <th colspan="4">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($tabledata as $data) {
                                $booked = $CI->class_model->get_class_booked($data['course_id'], $data['class_id'], $this->session->userdata('userDetails')->tenant_id);
                                if ($this->session->userdata('userDetails')->role_id == 'COMPACT') {
                                    $booked_count_in_my_company = $CI->class_model->get_class_booked_count($data['course_id'], $data['class_id'], $this->session->userdata('userDetails')->tenant_id);
                                }
                                $available = $data['total_seats'] - $booked;
                                echo ($data['class_status'] == 'INACTIV') ? '<tr class="danger">' : '<tr>';

                                if ($booked == 0) {
                                    $booked_label = $booked;
                                }else {
                                        if ($this->session->userdata('userDetails')->role_id == 'ADMN'){
                                            $booked_label = '<a href="'.base_url() . 'classes/seats_booked/' . $data['class_id'].'">'.$booked.'</a>';                                                                                     
                                        }else if($this->session->userdata('userDetails')->role_id == 'CRSEMGR'){
                                        $manager_array = explode(",", $courseDetails->crse_manager);
                                            if(in_array($this->session->userdata('userDetails')->user_id, $manager_array)){
                                            $booked_label ='<a href="'.base_url() . 'classes/seats_booked/' . $data['class_id'].'">'.$booked.'</a>';
                                            }else{       
                                            $booked_label = $booked;
                                        }
                                        }else if($this->session->userdata('userDetails')->role_id == 'TRAINER'){
                                        $trainer_array = explode(",", $data['classroom_trainer']);
                                            if(in_array($this->session->userdata('userDetails')->user_id, $trainer_array)){
                                            $booked_label = '<a href="'.base_url() . 'classes/seats_booked/' . $data['class_id'].'">'.$booked.'</a>';
                                            }else{
                                             $booked_label = $booked;
                                        }
                                         }else if ($this->session->userdata('userDetails')->role_id == 'COMPACT'){
                                            if(empty ($booked_count_in_my_company)){ 
                                          $booked_label = '<a class="small_text1 no_class" rel="modal:open" href="#alert">'.$booked.'</a>';
                                        }
                                            else{
                                           $booked_label = '<a href="'.base_url() . 'classes/seats_booked/' . $data['class_id'].'">'.$booked.'</a>';
                                        }
                                         }else {
                                        $booked_label = $booked;
                                    }
                                }                                
                                $status = $CI->class_model->get_class_status($data['class_id'], $this->input->get('class_status'));
                                if ($status == 'Yet to Start')
                                    $status_label = '<font color="green">Yet to Start</font>';
                                elseif ($status == 'Inactive')
                                    $status_label = '<font color="blue">Inactive</font>';
                                else if ($status == 'Completed')
                                    $status_label = '<font color="red">Completed</font>';
                                else
                                    $status_label = '<font color="blue">In-Progress</font>';
                                ?>
                                                                                                                                                                                
                            <td><a href="<?php echo base_url() . $controllerurl . 'view_class/' . $data['class_id']; ?>">
                                <?php echo $data['class_name']; ?> (<?php echo $data['class_id']; ?>)</a> 
                                    <?php echo ($data['class_pymnt_enrol'] == 'PAENROL') ? '<span style="color:red;"> **</span>' : ''; ?>
                                <!-- Link to the conversion of forms - ENTUTO Start -->
                                <br><a href="#" onclick="show_batch_confirm();return(false);">Process Assessment Forms</a>
                                <!-- Link to the conversion of forms - ENTUTO End -->
                            </td>
                            <td><?php echo date('d/m/Y h:i A', strtotime($data['class_start_datetime'])); ?></td>
                            <td><?php echo date('d/m/Y h:i A', strtotime($data['class_end_datetime'])); ?></td>
                            <td colspan="2">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="33%" align="center"><?php echo $booked_label; ?></td>
                                        <td width="35%" align="center"><?php echo ($available < 0) ? 0 : $available; ?></td>
                                        <!--<td align="center"><?php echo $data['total_seats']; ?></td>-->
                                    </tr>
                                </table>
                            </td>
                            <td><?php echo $CI->class_model->get_training_aide($data['training_aide']); ?></td>
                             <td><?php echo $CI->class_model->get_trainer_names($data['classroom_trainer']); ?></td>
                            
                            <td><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($data['class_language']), ', '); ?></td>
                            <td><?php echo $status_label; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                        <?php
                        $note = '<span style="color:red">** Over Booking Allowed</span>';
                    } else {
                        $note = '';
                        echo "<tr><td class='no-bor' colspan='10'>&nbsp;</td></tr><tr class='danger'><td colspan='10' class='error' style='text-align:center;'>There are no classes available for this course.</td></tr>";
                    }
                    ?>

                </table>
            </div>
            <?php echo $note; ?>
        </div><br>
        <ul class="pagination pagination_style">
            <?php echo $pagination; ?>
        </ul>
    </div>
    <div class="modal" id="ex1" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Heading Goes Here...</h2>
        Detail Goes here.  <br>
        <div class="popup_cancel">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
    </div>
    <div class="alert" id="alert" style="display:none;max-height: 200px">
        <h2 class="panel_heading_style">Warning</h2>
        <div style="text-align:center" class="error1">
            There are no employees enrolled in this class from your organisation.</div>
        <div class="popup_cancel11">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
        </div>
    </div>
<?php } ?>

<!-- Confirmation Dialog for job submission - ENTUTO -->
<?php
$form_attributes = array('name' => 'submit_batch_form', 'id' => 'submit_batch_form', "onsubmit" => "return(validate_deactivate_template());");
echo form_open("", $form_attributes);
?>
    <div class="modal1_077" id="submit_batch" style="display:none;">
        <p>
            
        <h2 class="panel_heading_style">Confirm Batch Job Submission</h2>         
        <br><br>                                   

        <b>Are you sure you want to submit the batch job for processing the assessment forms?</b>
        <br><br>
        <b>Please note the following:</b><br>
        - The background job once submitted cannot be stopped<br>
        - The conversion process takes a while to complete depending on the number of students<br>
        - Please do not resubmit this job immediately
        <br><br>
       
        <div class="popup_cancel9">
            <div rel="modal:close">
                
                <a href="#" onclick="process_forms_batch('<?php echo $this->session->userdata('userDetails')->tenant_id; ?>',
                                                                             '<?php echo $data['course_id']; ?>',
                                                                             '<?php echo $data['class_id']; ?>',
                                                                             '<?php echo $this->session->userdata('userDetails')->user_id; ?>');return(false);" rel="modal:close">
                                        <button class="btn btn-primary" type="button" >Submit Job</button></a>&nbsp;&nbsp;
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
    </div>
<?php
echo form_close();
?>

<!-- END Confirmation Dialog - ENTUTO -->
