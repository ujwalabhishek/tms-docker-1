<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tpg_mark_attendance.js?0.11"></script>
<style>
    table td{
        font-size: 11px;
    }
</style>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    /////display if any error from TPG site
    if (!empty($this->session->flashdata('resp_error'))) {
        foreach ($this->session->flashdata('resp_error') as $err) {

            echo '<div class="alert alert-danger dang">
            <strong>' . $err->field . ': </strong>' . $err->message . '
        </div>';
        }
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> TPG MARK ATTENDANCE</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("class_trainee/mark_attendance_tpg", $atr);
    ?>  
    <div class="table-responsive">
        <h5  class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>

        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Course Name:</td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';

                        foreach ($courses as $k => $v) {
                            $options[$k] = $v;
                        }

                        $js = 'id="course" ';
                        echo form_dropdown('course', $options, $this->input->get('course'), $js);
                        ?>
                        <span id="course_err"></span>
                    </td>                    
                </tr>                
                <tr>
                    <td class="td_heading">Class Name:</td>
                    <td colspan='3'>
                        <?php
                        $optionss = array();
                        $optionss[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $optionss[$k] = $v;
                        }
                        $js = 'id="class" ';
                        echo form_dropdown('class', $optionss, $this->input->get('class'), $js);
                        ?>
                        <span id="class_err"></span>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Trainee NRIC:</td>
                    <td>
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($nric as $k => $v) {
                            $options[$k] = $v;
                        }
                        $js = 'id="nric" ';
                        echo form_dropdown('nric', $options, $this->input->get('nric'), $js);
                        ?>
                        <input type='hidden' name='nric_id' id='nric_id' value>
                        <span id="nric_err"></span>
                    </td>

                </tr>

                <tr>
                    <td colspan='4'>
                        <span class="pull-right">
                            <button type="submit" value="Search" class="btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </td>
                </tr>

            </tbody>
        </table>
    </div><br>

    <?php echo form_close(); ?>
    <?php ?>
    <div class="bs-example">
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <div style='color:grey'><b>Attn Status Code:</b> 1-Confirmed,2-Unconfirmed,3-Rejected ,4-TP Voided</div>
            <div style='color:grey'><b>ID Type:</b> SB-Singapore Blue Identification Card, SP-Singapore Pink Identification Card, SO-Fin/Work Permit/SAF 11B, OT-Others</div>
            <table class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    ?>
                    <tr>
                        <th width="9%" class="th_header">Mode Of Training</th>
                        <th width="9%" class="th_header">Class Date</th>
                        <th width="9%" class="th_header">Session</th>
                        <th width="9%" class="th_header">Session ID</th>
                        <th width="10%" class="th_header">Attn Status Code</th>
                        <th width="10%" class="th_header">Name</th>
                        <th width="6%" class="th_header">Email</th>
                        <th width="6%" class="th_header">ID Type</th>
                        <th width="4%" class="th_header">MobileNo</th>
                        <th width="10%" class="th_header">No Of Hours</th>
                        <th width="9%" class="th_header">Survey Language</th>
                        <th width="9%" class="th_header">TPG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $err_msg = 'There are no trainees enrolled to any class currently.';
                    if (!empty($_GET)) {
                        $err_msg = 'No data available for the search criteria entered.';
                    }
                    if (!empty($tabledata)) {
                        foreach ($tabledata as $row) {
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    if ($row->mode_of_training == '1') {
                                        echo "Classroom";
                                    } else if ($row->mode_of_training == '2') {
                                        echo "Asynchronous eLearning";
                                    } else if ($row->mode_of_training == '3') {
                                        echo "In-house";
                                    } else if ($row->mode_of_training == '4') {
                                        echo "On-the-Job";
                                    } else if ($row->mode_of_training == '5') {
                                        echo "Practical / Practicum";
                                    } else if ($row->mode_of_training == '6') {
                                        echo "Supervised Field";
                                    } else if ($row->mode_of_training == '7') {
                                        echo "Traineeship";
                                    } else if ($row->mode_of_training == '8') {
                                        echo "Assessment";
                                    } else if ($row->mode_of_training == '9') {
                                        echo "Synchronous eLearning";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row->class_date; ?></td>
                                <td class="name"><?php
                                    if ($row->mode_of_training == '8') {
                                        echo "-";
                                    } else {
                                        if ($row->session_type_id == 'S1') {
                                            echo "Session 1";
                                        } else if ($row->session_type_id == 'S2') {
                                            echo "Session 2";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row->tpg_session_id; ?></td>
                                <td class="name"><?php
                                    if ($row->mode_of_training == '8') {
                                        if ($row->session_02 == 0) {
                                            $att_ses_status = '2';
                                            //echo '2';
                                            echo "Unconfirmed";
                                        } else {
                                            $att_ses_status = '1';
                                            //echo $row->session_02;
                                            echo "Confirmed";
                                        }
                                    } else {
                                        if ($row->session_type_id == 'S1') {
                                            if ($row->session_01 == 0) {
                                                $att_ses_status = '2';
                                                //echo '2';
                                                echo "Unconfirmed";
                                            } else {
                                                $att_ses_status = '1';
                                                //echo $row->session_01;
                                                echo "Confirmed";
                                            }
                                        } else if ($row->session_type_id == 'S2') {
                                            if ($row->session_02 == 0) {
                                                $att_ses_status = '2';
                                                //echo '2';
                                                echo "Unconfirmed";
                                            } else {
                                                $att_ses_status = '1';
                                                //echo $row->session_02;
                                                echo "Confirmed";
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row->fullname; ?></td>
                                <td><?php echo $row->registered_email_id; ?></td>
                                <td><?php
                            if ($row->tax_code_type = 'SNG_1' && $row->idtype == 'SG') {
                                $idtype = 'NRIC(SP)'; ///singaporean pink
                                $idtypes = 'SP';
                            } elseif ($row->tax_code_type = 'SNG_1' && $row->idtype == 'NS') {
                                $idtype = 'NRIC(SB)'; /// permanent residence
                                $idtypes = 'SB';
                            } else if ($row->tax_code_type = 'SNG_2') {
                                $idtype = 'FIN(SO)'; //// FIN
                                $idtypes = 'SO';
                            } else {
                                $idtype = 'Others(OT)'; /////Others
                                $idtypes = 'OT';
                            }
                            echo $idtype
                                    ?>
                                </td>
                                <td><?php echo $row->contact_number; ?></td>
                                <td><?php echo $row->total_classroom_duration; ?></td>
                                <td><?php echo $row->survey_language; ?></td>
                                <td>
                                    <?php if ($row->tpg_uploaded_status == 1) { ?>                                                                               
                                        <a href="<?php echo base_url() . 'tp_gateway/retrieve_course_sess_att/' . $row->tpg_course_run_id . '/' . $row->reference_num . '/' . $row->tpg_session_id . '/' . $row->tax_code; ?>"><button class="btnblue">View Session</button></a>
                                        <?php
                                    } else {
                                        $atr = 'id="submit_attendance_form" name="submit_attendance" method="post"';
                                        echo form_open("tp_gateway/submit_attendance", $atr);
                                        ?>
                                        <input type="hidden" name="tpg_session_id" value="<?php echo $row->tpg_session_id; ?>" id="tpg_session_id">                                    
                                        <?php if ($row->session_type_id == 'S1') { ?>
                                            <input type="hidden" name="attn_status_code" value="<?php echo $att_ses_status; ?>" id="attn_status_code">
                                        <?php } else { ?>
                                            <input type="hidden" name="attn_status_code" value="<?php echo $att_ses_status; ?>" id="attn_status_code">
                                        <?php } ?>                                                                                                            
                                        <input type="hidden" name="fullname" value="<?php echo $row->fullname; ?>" id="fullname">
                                        <input type="hidden" name="registered_email_id" value="<?php echo $row->registered_email_id; ?>" id="registered_email_id">
                                        <input type="hidden" name="idtype" value="<?php echo $idtypes; ?>" id="idtype">
                                        <input type="hidden" name="mobileNo" value="<?php echo $row->contact_number; ?>" id="mobileNo">
                                        <input type="hidden" name="noOfHours" value="<?php echo $row->total_classroom_duration; ?>" id="noOfHours">
                                        <input type="hidden" name="survey_language" value="<?php echo $row->survey_language; ?>" id="survey_language">
                                        <input type="hidden" name="class_id" value="<?php echo $row->class_id; ?>" id="class_id">
                                        <input type="hidden" name="course_id" value="<?php echo $row->course_id; ?>" id="course_id">
                                        <input type="hidden" name="user_id" value="<?php echo $row->user_id; ?>" id="user_id">
                                        <input type="hidden" name="crs_reference_num" value="<?php echo $row->reference_num; ?>" id="crs_reference_num">
                                        <input type="hidden" name="tax_code" value="<?php echo $row->tax_code; ?>" id="tax_code">
                                        <input type="hidden" name="tpg_course_run_id" value="<?php echo $row->tpg_course_run_id; ?>" id="tpg_course_run_id">
                                        <button type="submit" value="Submit" class="btnblue" title="Submit" />Submit To TPG</button>
                                        <?php
                                        echo form_close();
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
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
</div>

<div class="modal1_0001" id="view_session_attn" style="display:none;height:370px;min-height: 200px;width:60%">
    <h2 class="panel_heading_style">TPG Retrieve Course Session Attendance</h2>
    <table class="table table-striped" id='viewsection'>
        <tbody>
            <tr>
                <td class="td_heading">Assessment Reference No:</td>
                <td id='ass_ref_no'></td>
                <td class="td_heading">TP UEN:</td>
                <td id='tp_uen'></td>
            </tr>

            <tr>
                <td class="td_heading">TP Name:</td>
                <td id='tp_name'></td>
                <td class="td_heading">Course Reference No:</td>
                <td id='crs_ref_no'></td>
            </tr>
            <tr>
                <td class="td_heading">Course Name:</td>
                <td id='crs_name'></td>
                <td class="td_heading">Course Run ID:</td>
                <td id='crs_run_id'></td>
            </tr>

            <tr>
                <td class="td_heading">Course Run Start Date:</td>
                <td id='crs_run_start_date'></td>
                <td class="td_heading">Course Run End Date:</td>
                <td id='crs_run_end_date'></td>
            </tr>

            <tr>
                <td class="td_heading">Trainee ID Type:</td>
                <td id='trainee_id_type'></td>
                <td class="td_heading">Trainee Id:</td>
                <td id='trainee_id'></td>
            </tr>

            <tr>
                <td class="td_heading">Trainee Full Name:</td>
                <td id='fullname'></td>
                <td class="td_heading">Result:</td>
                <td id='result'></td>
            </tr>

            <tr>
                <td class="td_heading">Score:</td>
                <td id='score'></td>
                <td class="td_heading">Grade:</td>
                <td id='grade'></td>
            </tr>

            <tr>
                <td class="td_heading">Assessment Date:</td>
                <td id='ass_date'></td>
                <td class="td_heading">Skill Code:</td>
                <td id='skill_code'></td>
            </tr>
<!--            <tr>
                <td class="td_heading">Enrollment No:</td>
                <td id='enrol_no'></td>
            </tr>-->
            <tr>
                <td class="td_heading">Created On:</td>
                <td id='created_on'></td>
                <td class="td_heading">Updated On:</td>
                <td id='updated_on'></td>
            </tr>

        </tbody>
    </table>
    <div class="popup_cance89">
        <a href="#view_assessment" rel="modal:close"><button class='btn btn-primary'>Close</button></a>
    </div>
</div>
<script>
    $(document).ready(function () {
        var check = 0;
        $('#update_fee').submit(function () {
            check = 1;
            return validateFee(true);
        });
        $('#update_fee select').change(function () {
            if (check == 1) {
                return validateFee(false);
            }
        });
    });

    function validateFee(retVal) {
        fee_status = $.trim($("#fee_collectionStatus").val());
        if (fee_status == "") {
            $("#fee_collection_err").text("[required]").addClass('error');
            $("#fee_collection_err").addClass('error');
            retVal = false;
        } else {
            $("#fee_collection_err").text("").removeClass('error');
            $("#fee_collection_err").removeClass('error');
            retVal = true;
        }
        return retVal;
    }
</script>
<?php echo form_close(); ?>
</div>

<style>
    .btnblue{
        border: none;
        color: #008cff;
        font-size: 11px !important;
        margin-top: 0;
        padding: 3px 4px !important;
        cursor: pointer;
    }
    .btnblue:hover{
        color:white;
        background-image:-webkit-linear-gradient(top, #107ac6, #097d91);
        border-radius: 3px;
    }


</style>