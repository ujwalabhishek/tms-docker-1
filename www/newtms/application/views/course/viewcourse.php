<?php
$CI = & get_instance();
$CI->load->model('course_model');
$CI->load->model('class_model');
$CI->load->model('settings_model');
$class_status = $this->input->get('class_status');
$role_array = array("COMPACT", "TRAINER");
?>    
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course Detail</h2>   
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Display on Landing Page:&nbsp;&nbsp;
                            <label class="label_font" style="color:blue"><b>
                                    <?php echo ($course_data->display_on_portal == 1) ? 'Yes' : 'No'; ?></b>
                            </label>
                        </td>
                        <?php
                        $div_class = 'green';
                        $div_colspan = '3';
                        if ($course_data->crse_status == 'INACTIV') {
                            $div_class = 'red';
                            $div_colspan = '1';
                        }
                        ?>
                        <td colspan='3' class="td_heading">Course Status:&nbsp;&nbsp;
                            <label class="label_font">
                                <span class="<?php echo $div_class; ?>">
                                    <b><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->crse_status), ', '); ?></b>                                
                                </span>
                            </label>
                            <?php if ($course_data->crse_status == 'INACTIV') { ?>
                                De-activation Reason:
                                <label class="label_font">
                                    <span class="<?php echo $div_class; ?>">
                                        <?php
                                        if ($course_data->deacti_reason == 'OTHERS') {
                                            echo $course_data->deacti_reason_oth;
                                        } else {
                                            echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->deacti_reason), ', ');
                                        }
                                        ?>                                
                                    </span>
                                </label>
                            <?php } ?>
                        </td> 
                    </tr>
                    <tr>
                        <td class="td_heading" width="28%">Course Name:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->crse_name; ?></label></td>
                        <td class="td_heading">Pre-requisite:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_pre_requisite($course_data->pre_requisite), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Language:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->language), ', '); ?>
                            </label>                        
                        </td>
                        <td class="td_heading">Course Type:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->crse_type), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Type:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($course_data->class_type), ', '); ?>
                            </label>
                        </td>
                        <td class="td_heading">GST Rate:</td>
                        <td>
                            <?php
                            $gst_rates_attributes = array('class' => 'label_font');
                            $gst_rates = $CI->settings_model->get_active_gst_rates($this->session->userdata('userDetails')->tenant_id);
                            if ($gst_rates->gst_rate != false) {
                                $gst = number_format($gst_rates->gst_rate, 2);
                                echo form_label("$gst %", '', $gst_rates_attributes);
                            } else
                                echo form_label("GST-Not Defined", '', $gst_rates_attributes);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">GST Rules:<span class="required">*</span></td>
                        <td> 
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            if ($course_data->gst_on_off == '1') {
                                $yes = TRUE;
                            } else {
                                $no = TRUE;
                            }
                            $gst_rules_yes = array(
                                'name' => 'gst_rules',
                                'value' => '1',
                                'id' => 'gst_rules_yes',
                                'checked' => $yes,
                                'disabled' => "disabled",
                            );
                            $gst_rules_yes_no = array(
                                'name' => 'gst_rules',
                                'id' => 'gst_rules_no',
                                'value' => '0',
                                'checked' => $no,
                                'disabled' => "disabled",
                            );
                            ?>              
                            <?php echo form_radio($gst_rules_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($gst_rules_yes_no); ?> No                                                            
                            <span id="class_type_err"></span>
                        </td>
                        <td colspan='2'>
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            if ($course_data->subsidy_after_before == 'GSTBSD') {
                                $yes = TRUE;
                            } else {
                                $no = TRUE;
                            }
                            $before_subsidy = array(
                                'name' => 'subsidy',
                                'value' => 'GSTBSD',
                                'id' => 'before_subsidy',
                                'checked' => $yes,
                                'disabled' => "disabled",
                            );
                            $after_subsidy = array(
                                'name' => 'subsidy',
                                'id' => 'after_subsidy',
                                'value' => 'GSTASD',
                                'checked' => $no,
                                'disabled' => "disabled",
                            );
                            ?>              
                            <?php echo form_radio($before_subsidy); ?>Apply GST before SUBSIDY. &nbsp;&nbsp; 
                            <?php echo form_radio($after_subsidy); ?> Apply GST after SUBSIDY.                               
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Course Duration (in hrs):<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->crse_duration; ?></label></td>
                        <td class="td_heading">Course Reference Number:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $course_data->reference_num; ?></label></td>                        
                    </tr>
                    <?php if ($course_data->tpg_crse) { ?>
                        <tr>
                            <td class="td_heading">External Reference Number:<span class="required">*</span></td>
                            <td><label class="label_font"><?php echo $course_data->external_reference_number; ?></label></td> 
                            <td class="td_heading">Course Admin Email:<span class="required">*</span></td>
                            <td><label class="label_font"><?php echo $course_data->crse_admin_email; ?></label></td> 
                        </tr>
                    <?php } ?>
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
                    <?php if (!in_array($this->session->userdata('userDetails')->role_id, $role_array)) { ?>
                        <tr>
                            <td class="td_heading">Default Sales Commission Rate:<span class="required">*</span></td>
                            <td colspan="3"><label class="label_font"><?php echo number_format($course_data->default_commission_rate, 2, '.', ''); ?>&nbsp;%</label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Sales Executive:<span class="required">*</span></td>
                            <td colspan="3"><label class="label_font"><?php echo $sales_exec; ?></label></td>
                        </tr>
                    <?php } ?>
                    <?php if ($course_data->copied_from_id) { ?>
                        <tr>
                            <td class="td_heading">                            
                                <label>
                                    Remarks: 
                                </label>                            
                            </td>
                            <td>
                                Course Copied from <?php echo $this->course->course_name($course_data->copied_from_id); ?>
                            </td>
                            <td>
                                <label>
                                    Copy Reason:
                                </label>
                            </td>
                            <td>
                                <?php echo $course_data->copy_reason; ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2" width="55%">Does this course have a validity period? 
                            &nbsp; &nbsp;
                            <input name="1" type='radio' <?php if ($course_data->crse_cert_validity != 0) { ?> checked <?php } ?> disabled='disabled' />Yes &nbsp;&nbsp;
                            <input name="1" type='radio' <?php if ($course_data->crse_cert_validity == 0) { ?> checked <?php } ?> disabled='disabled'/>No &nbsp;&nbsp; 
                            <?php
                            if ($course_data->crse_cert_validity != 0) {
                                echo $course_data->crse_cert_validity . ' days from date of certification';
                            }
                            ?> 
                        </td>
                        <td colspan="2" width="45%">
                            <table>
                                <tr>
                                    <?php if ($course_data->crse_content_path) { ?>
                                        <td class="td_heading" width="40%" style="vertical-align:top">Download Course Material:</td>
                                        <td class="td_heading" width="25%" style="vertical-align:top">
                                            <a href="<?php echo base_url() . 'course/download_course_content/?file_path=' . $course_data->crse_content_path . "&file_name=" . $course_data->crse_name; ?>">Download</a>
                                        </td>
                                    <?php } ?>
                                    <?php if ($course_data->crse_icon) { ?>
                                        <td align="center" width="35%">
                                            <div class="photo_icon">
                                                <img src="<?php echo base_url() . $course_data->crse_icon; ?>"> 
                                            </div>
                                            <p style="font-weight:bold">Course Icon:</p>
                                        </td>
                                    <?php } ?>
                                </tr>
                            </table>    
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h2 class="sub_panel_heading_style">
            <img src="<?php echo base_url(); ?>/assets/images/company-detail.png" /> Class List
            <?php if (!empty($tabledata) && !in_array($this->session->userdata('userDetails')->role_id, $role_array)) { ?>                
                <a href="<?php echo base_url() . 'course/export_course_class_full' . $export_url; ?>" id="sales_exec_addmore" class="label label-default push_right black-btn mar-right">
                    <span class="glyphicon glyphicon-export"></span> Export to XLS 
                </a> 
            <?php } ?>
        </h2>
    </div>
    <?php
    if (empty($tabledata) && empty($class_status)) {
        
    } else {
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("course/view_course/$course_id", $atr);
        ?>
        <span><strong>Filter On </strong> 
            <strong>Status:</strong> 
            <?php
            $cls_status_options[''] = 'All';
            $cls_status = fetch_metavalues_by_category_id(Meta_Values::CLASS_STATUS);
            foreach ($cls_status as $val):
                $cls_status_options[$val['parameter_id']] = $val['category_name'];
            endforeach;
            echo form_dropdown('class_status', $cls_status_options, $this->input->get('class_status'), 'id="class_status"');
            ?>
        </span>
        <?php
        echo form_close();
    }
    ?>    
    <div class="table-responsive">
        <table class="table table-striped">
            <?php
            if (empty($tabledata) && empty($class_status)) {
                
            } else {
                ?>
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th width="11%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_id&o=" . $ancher; ?>" >Class Code</a></th>
                        <th width="11%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_name&o=" . $ancher; ?>" >Class Name</a></th>
                        <th width="14%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_start_datetime&o=" . $ancher; ?>" >Start Date & Time</a></th>
                        <th width="14%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_end_datetime&o=" . $ancher; ?>" >End Date & Time</a></th>
                        <th class="th_header text_move" colspan="3">Seats</th>
                        <th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=classroom_trainer&o=" . $ancher; ?>" >Trainer</a></th>
                        <th width="9%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_language&o=" . $ancher; ?>" >Language</a></th>
                        <th width="13%" class="th_header">Status</th>
                    </tr>
                    <tr>
                        <th colspan="4">&nbsp;</th>
                        <th width="8%" class="text_move">Booked</th>
                        <th width="10%" class="text_move">Available</th>
                        <th width="8%" class="text_move">Total</th>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                </thead>
            <?php } ?>
            <tbody>
                <?php
                if (!empty($tabledata)) {
                    foreach ($tabledata as $data) {
                        $status = $CI->class_model->get_class_status($data['class_id'], $this->input->get('class_status'));
                        if ($status == 'Yet to Start')
                            $status_label = '<font color="green">Yet to Start</font>';
                        elseif ($status == 'Inactive')
                            $status_label = '<font color="blue">Inactive</font>';
                        else if ($status == 'Completed')
                            $status_label = '<font color="red">Completed</font>';
                        else
                            $status_label = '<font color="blue">In-Progress</font>';

                        $booked = $CI->class_model->get_class_booked($data['course_id'], $data['class_id'], $this->session->userdata('userDetails')->tenant_id);
                        if ($this->session->userdata('userDetails')->role_id == 'COMPACT') {
                            $booked_count_in_my_company = $CI->class_model->get_class_booked_count($data['course_id'], $data['class_id'], $this->session->userdata('userDetails')->tenant_id);
                        }
                        $available = $data['total_seats'] - $booked;
                        echo ($data['class_status'] == 'INACTIV') ? '<tr class="danger">' : '<tr>';
                        ?>
                    <td>
                        <a href="<?php echo base_url() . 'classes/view_class/' . $data['class_id']; ?>">
                            <?php echo $data['class_id']; ?></a>
                        <?php echo ($data['class_pymnt_enrol'] == 'PAENROL') ? '<span style="color:red;"> **</span>' : ''; ?>
                    </td>
                    <td><?php echo $data['class_name']; ?></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($data['class_start_datetime'])); ?></td>
                    <td><?php echo date('d/m/Y h:i A', strtotime($data['class_end_datetime'])); ?></td>
                    <td colspan="3">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="33%" align="center">
                                    <?php
                                    if ($booked == 0) {
                                        echo $booked;
                                    } else {
                                        if ($this->session->userdata('userDetails')->role_id == 'ADMN') {
                                            echo '<a href="' . base_url() . 'classes/seats_booked/' . $data['class_id'] . '">' . $booked . '</a>';
                                        } else if ($this->session->userdata('userDetails')->role_id == 'CRSEMGR') {
                                            $manager_array = explode(",", $course_data->crse_manager);
                                            if (in_array($this->session->userdata('userDetails')->user_id, $manager_array)) {
                                                echo '<a href="' . base_url() . 'classes/seats_booked/' . $data['class_id'] . '">' . $booked . '</a>';
                                            } else {
                                                echo $booked;
                                            }
                                        } else if ($this->session->userdata('userDetails')->role_id == 'TRAINER') {
                                            $trainer_array = explode(",", $data['classroom_trainer']);
                                            if (in_array($this->session->userdata('userDetails')->user_id, $trainer_array)) {
                                                echo '<a href="' . base_url() . 'classes/seats_booked/' . $data['class_id'] . '">' . $booked . '</a>';
                                            } else {
                                                echo $booked;
                                            }
                                        } else if ($this->session->userdata('userDetails')->role_id == 'COMPACT') {
                                            if (empty($booked_count_in_my_company)) {
                                                echo '<a class="small_text1 no_class" rel="modal:open" href="#alert">' . $booked . '</a>';
                                            } else {
                                                echo '<a href="' . base_url() . 'classes/seats_booked/' . $data['class_id'] . '">' . $booked . '</a>';
                                            }
                                        } else {
                                            echo $booked;
                                        }
                                    }
                                    ?>
                                </td>
                                <td width="35%" align="center"><?php echo ($available > 0 ) ? $available : 0; ?></td>
                                <td align="center"><?php echo $data['total_seats']; ?></td>
                            </tr>
                        </table>
                    </td>
                    <td><?php echo $CI->class_model->get_trainer_names($data['classroom_trainer']); ?></td>
                    <td><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($data['class_language']), ', '); ?></td>
                    <td><?php echo $status_label; ?></td>
                    </tr>
                    <?php
                }
                $note = '<span style="color:red">** Over Booking Allowed</span>';
            } else {
                $note = '';
                if ($this->data['user']->role_id == 'TRAINER') {
                    echo "<td colspan='10' class='error' style='text-align:center;'>You are not assigned as Trainer to any of the classes of this course.</td>";
                } else {
                    echo "<td colspan='10' class='error' style='text-align:center;'>There are no classes available.</td>";
                }
            }
            ?>
            </tbody>

        </table>
    </div>
    <?php
    echo $note;
    ?>
</div>
<div style="clear:both;"></div>
<br>
<ul class="pagination pagination_style">
    <?php echo $pagination; ?>
</ul>
</div>
<div class="button_class">
    <a href="<?php echo site_url(); ?>course">
        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back</button>
    </a>
</div>
<div class="alert" id="alert" style="display:none;max-height: 200px">
    <h2 class="panel_heading_style">Warning</h2>
    <div style="text-align:center" class="error1">
        <img src="<?php echo base_url(); ?>assets/images/alert.png"  alt="Warning" />
        There are no employees enrolled in this class from your organisation.</div>
    <div class="popup_cancel11">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
    </div>
</div>
<script type="text/javascript">
    $("#class_status").change(function () {
        $('#search_form').submit();
    });
</script>    
