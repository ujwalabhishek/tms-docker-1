<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tpg_assessment.js?0.00070111"></script>
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
     if(!empty($this->session->flashdata('resp_error'))){
        foreach($this->session->flashdata('resp_error') as $err){

        echo '<div class="alert alert-danger dang">
            <strong>'.$err->field.': </strong>'.$err->message.'
        </div>';
        }
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> TPG Assessment Trainee Lists</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("classes/tpg_assessments", $atr);
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
                    <td class="td_heading">Search Assessment In:</td>
                    <td>
                         <?php
                            $assm_options= array(
                                'tms' => 'TMS',
                                'tpg' => 'TPG'
                            );
                        
                        $asid = 'id="searchAssessment" ';
                        echo form_dropdown('searchAssessment', $assm_options, $this->input->get('searchAssessment'), $asid);
                        ?>
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
            <table class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = 'class_trainee';
                    ?>
                    <tr>
                        <th width="9%" class="th_header">NRIC/FIN No</th>
                        <th width="10%" class="th_header">Full Name</th>
                        <th width="10%" class="th_header">Assessment Date</th>
                        <th width="10%" class="th_header">Skill Code</th>
                        <th width="6%" class="th_header">Score</th>
                        <th width="6%" class="th_header">Grade</th>
                        <th width="6%" class="th_header">Result</th>
                        <th width="10%" class="th_header">Course Ref No</th>
                        <th width="9%" class="th_header">Assmt Ref No</th>
                        <th width="9%" class="th_header">Enlmt Ref No</th>
                        <th width="17%" class="th_header">TPG</th>
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
                                <td><?php echo $row->tax_code; ?></td>
                                <td class="name"><?php echo $row->fullname; ?></td>
                                <td><?php echo $row->assessmentDate; ?></td>
                                <td><?php echo $row->skillCode; ?></td>
                                <td><?php echo $row->feedback_score ?></td>
                                <td><?php echo $row->feedback_grade; ?></td>
                                <td><?php echo $row->result; ?></td>
                                <td><?php echo $row->reference_num; ?></td>
                                <td><?php echo $row->assessment_reference_No; ?></td>
                                <td><?php echo $row->eid_number; ?></td>
                                <td>
                                   <?php if(empty($row->assessment_reference_No)){?>
                                    <a href="<?php echo base_url() . 'tp_gateway/create_assessment/'.$row->course_id.'/'.$row->class_id.'/'.$row->user_id ; ?>"><span class="btnblue">Submit Assessment</span></a>
                                    <br>
                                   <?php }else{?>
                                    <a href="#update_void_assessment" rel="modal:open" id='update_assessment' data-refNo='<?php echo $row->assessment_reference_No;?>' data-userid="<?php echo $row->user_id;?>" data-courseid="<?php echo $row->course_id;?>" data-classid="<?php echo $row->class_id;?>"><span class="btnblue">Update/Void Assessment</span></a>
                                    <br>
                                    <a href="#view_assessment" rel="modal:open" id='click_assessment' data-refNo='<?php echo $row->assessment_reference_No;?>'><span class="btnblue">View Assessment</span></a>
                                   <?php }?>
                                </td>
                            </tr>
                            <?php
                        }
                    } 
                    
                    
                    //////data for tpg search
                     if (!empty($tabledata_tpg)) {
                        foreach ($tabledata_tpg->data as $row) {
                          
                            ?>
                                                                              
                            <tr>                        
                                <td><?php echo $row->trainee->id; ?></td>
                                <td class="name"><?php echo $row->trainee->fullName; ?></td>
                                <td><?php echo $row->assessmentDate; ?></td>
                                <td><?php echo $row->skillCode; ?></td>
                                <td><?php echo $row->score ?></td>
                                <td><?php echo $row->grade; ?></td>
                                <td><?php echo $row->result; ?></td>
                                <td><?php echo $row->course->referenceNumber; ?></td>
                                <td><?php echo $row->referenceNumber; ?></td>
                                <td><?php echo $row->enrolment->referenceNumber; ?></td>
                                <td>
                                    <input type='hidden' value='<?php echo $row->course->run->id;?>'>
                                    <span>No Action, While Viewing TPG Data</span>
                                </td>
                            </tr>
                            <?php
                        }
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

<div class="modal1_0001" id="update_void_assessment" style="display:none;height:350px;min-height: 200px;width:60%">
    <h2 class="panel_heading_style">TPG Update/Void Assessment</h2>
    <table class="table table-striped" id='tblarea'>
        <tbody>
            <tr>
                <td class="td_heading">Trainee Full Name:<span class="required">*</span></td>
                <td  colspan='3'> "<span id='fullname1'></span>"<span> Will Changed To : "</span> <b><span id="tms_fullname"></span>"</b></td>
            </tr>
            <tr>
                <td class="td_heading">Result:<span class="required">*</span></td>
                <td colspan='3'>"<span id='result1'></span>"<span> Will Changed To : "</span> <b><span id="tms_result"></span>"</b></td>
            </tr>
            <tr>
                <td class="td_heading">Skill Code:<span class="required">*</span></td>
                <td colspan='3'>"<span id='skill_code1'></span>"<span> Will Changed To : "</span> <b><span id="tms_skill_code"></span>"</b></td>
            </tr>
             <tr>
                <td class="td_heading">Score:<span class="required">*</span></td>
                <td colspan='3'>"<span id='score1'></span>"<span> Will Changed To : "</span> <b><span id="tms_score"></span>"</b></td>
            </tr>
             <tr>
                <td class="td_heading">Grade:<span class="required">*</span></td>
                <td colspan='3'>"<span id='grade1'></span>"<span> Will Changed To : "</span> <b><span id="tms_grade"></span>"</b></td>
            </tr>
            
            <tr>
                <td class="td_heading">Assessment Date:<span class="required">*</span></td>
                <td colspan="3"><input type="date" name="assessment_date" value="" id='ass_date1' style="line-height: 14px;" class="upper_case ui-autocomplete-input" autocomplete="off"></td>
               
            </tr>
            <tr>
                <td class="td_heading">Action:<span class="required">*</span></td>
                <td>
                    <select name="action" id="action">
                    <option value="" selected="selected">Select</option>
                    <option value="update">Update</option>
                    <option value="void">Void</option>
                    </select>
                </td>
                <td class="td_heading">Assessment Ref No:<span class="required">*</span></td>
                <td id='assmt_ref_no1'></td>
            </tr>
            
        </tbody>
    </table>
    <div class="required required_i">* To Update "Trainee Name" &  "Result" & "Skill Code" & "Score" & "Grade" fields, Update in edit trainee,trainer feedback,Edit Course,trainer feedback,trainer feedback for the same,Then Come to update this page.</div>
    <div id="status_msg"></div>
    <div class="popup_cance89" id="btnarea">
        <button class='btn btn-primary' id="updateAseessment">Update/Void</button>
    </div>
</div>







<div class="modal1_0001" id="view_assessment" style="display:none;height:370px;min-height: 200px;width:60%">
    <h2 class="panel_heading_style">TPG View Assessment</h2>
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
    $(document).ready(function() {
            var check = 0;
            $('#update_fee').submit(function() {
                check = 1;
                return validateFee(true);
            });
            $('#update_fee select').change(function() {
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