<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
    var CLIENT_DATE_FORMAT = 'yy-mm-dd';</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classtraineelist.js?1.007"></script>
<style>
    table td{
        font-size: 11px;
    }
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
<div class="col-md-10">
    <?php
    $class_status = $this->input->get('class_status');
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> Class Trainee Enrollment List</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("class_trainee", $atr);
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
                    </td>                    
                </tr>                
                <tr>
                    <td class="td_heading">Class Name:</td>
                    <td colspan='3'>
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $options[$k] = $v;
                        }
                        $js = 'id="class" ';
                        echo form_dropdown('class', $options, $this->input->get('class'), $js);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">
                        <?php
                        $checked = TRUE;
                        $checked = ($this->input->get('search_select') == 1) ? TRUE : FALSE;

                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 1,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp; NRIC/FIN No.:
                    </td>
                    <td width="32%">
                        <?php
                        $data = array(
                            'id' => 'taxcode',
                            'name' => 'taxcode',
                            'value' => $this->input->get('taxcode'),
                            'class' => 'upper_case',
                            'style' => 'width:200px;',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'taxcode_id',
                            'name' => 'taxcode_id',
                            'value' => $this->input->get('taxcode_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <br>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="taxcode_err"></span>
                    </td>
                    <td width="15%" class="td_heading">
                        <?php
                        $checked = ($this->input->get('search_select') == 2) ? TRUE : FALSE;
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 2,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp; 
                        Trainee Name:</td>
                    <td colspan="2"><?php
                        $data = array(
                            'id' => 'trainee',
                            'name' => 'trainee',
                            'value' => $this->input->get('trainee'),
                            'class' => 'upper_case',
                            'style' => 'width:200px;',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'trainee_id',
                            'name' => 'trainee_id',
                            'value' => $this->input->get('trainee_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <br>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="trainee_err"></span>
                    </td>
                </tr>
                <tr>
                    <?php if ($this->data['user']->role_id != 'COMPACT') { ?>
                        <td class="td_heading" width="15%"> Company Name:</td>
                        <td colspan="" width="30%">
                            <?php
                            $company = array(
                                'name' => 'company_name',
                                'id' => 'company_name',
                                'value' => $this->input->get('company_name'),
                                'style' => 'width:200px;',
                                'class' => 'upper_case',
                                'autocomplete' => 'off'
                            );
                            echo form_input($company);
                            echo form_hidden('company_id', $this->input->get('company_id'), $id = 'company_id');
                            ?>
                            <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                            <span id="company_name_err"></span>
                        <?php } else { ?>
                        <td colspan="5">
                        <?php } ?>

                    </td>

                    <?php if ($this->data['user']->role_id != 'COMPACT') { ?>
                        <td class="td_heading" width="15%"> Enrolment ID:</td>
                        <td colspan="" width="30%">
                            <?php
                            $enrol = array(
                                'name' => 'eidbox',
                                'id' => 'eidbox',
                                'value' => $this->input->get('eid'),
                                'style' => 'width:200px;',
                                'class' => 'upper_case',
                                'autocomplete' => 'off'
                            );
                            echo form_input($enrol);
                            echo form_hidden('eid', $this->input->get('eid'), $id = 'eid');
                            ?>
                            <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                            <span id="eid_err"></span>
                        <?php } ?>
                        <span class="pull-right">
                            <button type="submit" value="Search" class="btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div><br>
    <div class="bs-example">
        <div class="table-responsive">
            <?php if (!empty($tabledata) || !empty($class_status)) { ?>
                <strong>Filter by Class Status:</strong>
                <?php
                $cls_status_options[''] = 'All';
                $cls_status = fetch_metavalues_by_category_id(Meta_Values::CLASS_TRAINEE_FILTER);
                foreach ($cls_status as $val):
                    $cls_status_options[$val['parameter_id']] = $val['category_name'];
                endforeach;
                echo form_dropdown('class_status', $cls_status_options, $this->input->get('class_status'), 'id="class_status"');
            }
            ?> 

            <?php if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['CLTRAINE'])) { ?>                    
                <div class="add_button98 pull-right">
                    <a href="<?php echo base_url(); ?>class_trainee/export_classtrainee_page<?php echo $export_url; ?>" class="small_text1" onclick="return exportValidate()">
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export Page Fields</span>
                    </a> &nbsp;&nbsp; 
                    <a href="<?php echo base_url(); ?>class_trainee/export_classtrainee_full<?php echo $export_url; ?>" class="small_text1" onclick="return exportValidate() > < span class ="label label-default black-btn">

                       <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export All Fields</span>
                    </a>
                </div>                  
            <?php } ?>
        </div>

    </div>
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
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=tu.tax_code&o=" . $ancher; ?>">NRIC/FIN No.</a></th>
                        <th width="8%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=tup.first_name&o=" . $ancher; ?>">Name</a></th>
                        <th width="15%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=c.crse_name&o=" . $ancher; ?>">Course / Class Detail</a></th>
                        <th width="10%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=cc.class_start_datetime&o=" . $ancher; ?>">Class Duration</a></th>
                        <th width="6%" class="th_header">Company Name</th>
                        <!--<?php
                        $tenant_id = $this->session->userdata('userDetails')->tenant_id;
                        if ($tenant_id == 'T01') {
                            ?>
                                                                                           <th width="10%" class="th_header">Sales Executive</th>
                        <?php } ?>-->
                        <th width="8%" class="th_header">Sales Executive</th>
                        <th width="6%" class="th_header">Certi. Coll.</th>
                        <th width="8%" class="th_header">Class Status</th>
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=ce.payment_status&o=" . $ancher; ?>">Payment</a></th>
                        <th width="11%" class="th_header">Action</th>
                        <th width="16%" class="th_header">TPG</th>
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
                            if (!empty($row['SalesExec'])) {
                                $salesList = "";
                                foreach ($row['SalesExec'] as $rowdata) {
                                    $salesList = $rowdata['first_name'] . ' ' . $rowdata['last_name'];
                                }
                            } else {
                                $salesList = 'No sales executive is assigned.';
                            }
                            $cur_date = strtotime(date('Y-m-d'));
                            $class_end_datetime = date("Y-m-d", strtotime($row['class_end_datetime']));
                            $class_end_datetime_str = strtotime($class_end_datetime);
                            $feedback = '';
                            $result_text = '';
                            $row['taxcode'];
                            if ($cur_date >= $class_end_datetime_str) {
                                if ($this->data['user']->role_id == 'ADMN') {
                                    $feedback = '<br/><a id="training_update" href="#ex7" rel="modal:open" data-course="' . $row['course_id'] . '" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] . '"data-payment="' . $row['pymnt_due_id'] . '" class="training_update small_text1">Feedback</a>';
                                }
                                $result_text = !empty($row['feedback_answer']) ? '<br/><b>Result:</b> ' . $row['feedback_answer'] : '';
                            }
                            $name = json_decode($row['referrer']);
                            ?>                                                                                                  
                            <tr>                        
                                <td><?php echo $row['taxcode']; ?></td>

                                <td class="name">
                                    <?php // echo $row['name']. $ref_name; ?> 
                                    <?php echo $row['name']; ?> <br /> <br />
                                    <?php if ($name->name != "") { ?>
                                        <a href="#ex144<?php echo $row['user_id'] . '' . $row['class_id']; ?>" rel="modal:open" 
                                           style="color: brown;">
                                            <span class=""><?php echo Referrer; ?></span>
                                        </a><?php
                                    } else {
                                        echo " ";
                                    }
                                    ?>

                                    <div class="modal_333" id="ex144<?php echo $row['user_id'] . '' . $row['class_id']; ?>" style="display:none; max-height: 166px;width:45%;overflow-y: hidden;min-height: 155px;">
                                        <h2 class="panel_heading_style">Referrer`s Details</h2>
                                        <div class="table-responsive" style="height: 300px;">
                                            <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                                                <thead>
                                                    <tr>
                                                        <th width="30%">Name</th>
                                                        <th width="30%">Email</th>
                                                        <th width="20%">Contact</th>
                                                        <th width="20%">Company</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    echo "<tr>
                                                            <td>" . $name->name . "</td>
                                                            <td>" . $name->email . " </td>
                                                            <td>" . $name->contact . "</td>
                                                            <td>" . $name->company . "</td>
                                                        </tr>";
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $row['course_class']; ?></td>
                                <td><?php echo $row['duration']; ?></td>
                                <td><?php echo $row['enroll_mode'] ?></td>

                                <!--<?php if ($tenant_id == 'T01') { ?>
                                                                                                   <td><?php echo $salesList; //implode("<br>",$salesList).                 ?></td>
                                <?php } ?>-->

                                <td><?php echo $salesList; //implode("<br>",$salesList).                 ?></td>
                                <td><?php echo $row['certi_coll']; ?></td>
                                <td><?php
                                    echo $row['status_text'] . '<br />' . $row['end_class'] . '<br />'
                                    . $result_text;
                                    ?>  
                                </td>
                                <td><?php echo $row['paid']; ?></td>
                                <td><?php echo $row['action_link']; ?></td>
                                <td>
                                    <?php if ($row['tpg_crse']) { ?>
                                        <?php
                                        $atr = 'id="tpg_form" name="tpg_form" method="post"';
                                        echo form_open("tp_gateway/send_trainee_enrolment_data_tpg", $atr);
                                        ?>
                                        <input type="hidden" name="courseRunId" value="<?php echo $row['tpg_course_run_id']; ?>" id="courseRunId">
                                        <input type="hidden" name="courseReferenceNumber" value="<?php echo $row['reference_num']; ?>" id="courseReferenceNumber">
                                        <input type="hidden" name="userId" value="<?php echo $row['user_id']; ?>" id="userId">
                                        <input type="hidden" name="enrolmentMode" value="<?php echo $row['enrolment_mode']; ?>" id="enrolmentMode">
                                        <input type="hidden" name="paymentStatus" value="<?php echo $row['payment_status']; ?>" id="paymentStatus">
                                        <input type="hidden" name="companyId" value="<?php echo $row['company_id']; ?>" id="companyId">
                                        <input type="hidden" name="feeDiscountAmount" value="<?php echo $row['feeDiscountAmount']; ?>" id="feeDiscountAmount">

                                        <input type="hidden" name="courseId" value="<?php echo $row['course_id']; ?>" id="courseId">
                                        <input type="hidden" name="classId" value="<?php echo $row['class_id']; ?>" id="classId">

                                        <?php
                                        $enrolmentReferenceNumber = $row['enrolmentReferenceNumber'];
                                        $feecollectionStatus = $row['feecollectionStatus_options'];
                                        $feecollectionStatus_val = $row['feecollectionStatus_val'];
                                        $enrolmentStatus = $row['enrolmentStatus'];

                                        $tmsUserId = $row['user_id'];
                                        $tmsPaymentStatus = $row['payment_status'];
                                        $tpgCourseId = $row['course_id'];
                                        $tpgClassId = $row['class_id'];

                                        $editEnrolmentAction = $row['editEnrolmentAction'];
                                        if (empty($enrolmentReferenceNumber)) {
                                            ?>
                                            <button type="submit" value="Submit" class="btnblue" title="Submit" />Submit To TPG</button>
                                            <br>
                                            <?php
                                        }
                                        echo form_close();
                                        ?>                                    
                                        <?php
                                        if ($enrolmentReferenceNumber != '') {
                                            ?>                        
                                            <a href="<?php echo base_url() . 'tp_gateway/view_enrolment_tpg/' . $enrolmentReferenceNumber; ?>"><button class="btnblue">View Enrolment</button></a>
                                            <br>
                                            <?php if ($enrolmentStatus == "Cancelled") { ?>
                                                <span style="color:red"><i>This enrolment is Cancelled.</i></span>
                                            <?php } ?>
                                            <?php if ($enrolmentStatus == "Confirmed") { ?>
                                                <a href="javascript:;" class="edit_enrolment" data-class="<?php echo $tpgClassId; ?>" data-course="<?php echo $tpgCourseId; ?>" data-user="<?php echo $tmsUserId; ?>" data-paymentstatus="<?php echo $tmsPaymentStatus; ?>" data-enrolrefnum="<?php echo $enrolmentReferenceNumber; ?>"><button type="button" class="btnblue">Edit Enrolment</button></a>
                                                <br>
                                                <a href="javascript:;" class="abd" data-classfee="<?php echo $tpgClassId; ?>" data-coursefee="<?php echo $tpgCourseId; ?>" data-enrolrefnumfee="<?php echo $enrolmentReferenceNumber; ?>" data-paymentstatusfee="<?php echo $tmsPaymentStatus; ?>" data-feecollectval="<?php echo $feecollectionStatus_val; ?>"><button type="button" class="btnblue">Update Fee</button></a>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span style="color:red"><i>This is a non - tpg course.</i></span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        $err_msg = $error_msg ? $error_msg : $err_msg; /// added by shubhranshu to remove the classtrainee list on 26/11/2018
                        echo '<tr><td colspan="12" class="error" style="text-align: center">' . $err_msg . '</td></tr>';
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
<?php
$atr = 'id="edit_enrolment_action" name="edit_enrolment_action" method="post"';
echo form_open("tp_gateway/edit_enrolment_tpg", $atr);
?>
<div class="modal1_0001" id="edit_enrolment" style="display:none;height:200px;min-height: 200px;">
    <h2 class="panel_heading_style">Select Enrolment Action</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading">Choose action for selected enrolment:</td>
                <td>                    
                    <input type="hidden" name="tpgCourseId" value="" id="tpgCourseId">
                    <input type="hidden" name="tpgClassId" value="" id="tpgClassId">
                    <input type="hidden" name="tpgUserId" value="" id="tpgUserId">
                    <input type="hidden" name="tmsPaymentStatus" value="" id="tmsPaymentStatus">
                    <input type="hidden" name="tpgEnrolmentReferenceNumber" value="" id="tpgEnrolmentReferenceNumber">
                    <?php
                    $editEnrolmentAction_attr = 'id="edit_EnrolmentAction"';
                    echo form_dropdown('edit_EnrolmentAction', $editEnrolmentAction, '', $editEnrolmentAction_attr);
                    ?>
                    <span id="enrolment_action_err"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Note : Once canceled the enrolled trainee with TPG, cannot be rolled back.</span>
    <div class="popup_cance89">
        <span href="#edit_enrolment" rel="modal:close"><button class="btn btn-primary edit_enrolment_action" type="submit">Submit</button></span>
    </div>
</div>
<script>
    $(document).ready(function () {
        var check = 0;
        $('#edit_enrolment_action').submit(function () {
            check = 1;
            return validateAction(true);
        });
        $('#edit_enrolment_action select').change(function () {
            if (check == 1) {
                return validateAction(false);
            }
        });
    });
    $('.edit_enrolment').click(function () {
        $this = $(this);
        $course = $this.data('course');
        $class = $this.data('class');
        $user = $this.data('user');
        $paymentstatus = $this.data('paymentstatus');
        $enrolrefnum = $this.data('enrolrefnum');
        $('#tpgCourseId').val($course);
        $('#tpgClassId').val($class);
        $('#tpgUserId').val($user);
        $('#tmsPaymentStatus').val($paymentstatus);
        $('#tpgEnrolmentReferenceNumber').val($enrolrefnum);
        $('#edit_enrolment').modal();
    });
    function validateAction(retVal) {
        edit_enrolment = $.trim($("#edit_EnrolmentAction").val());
        if (edit_enrolment == "") {
            $("#enrolment_action_err").text("[required]").addClass('error');
            $("#enrolment_action_err").addClass('error');
            retVal = false;
        } else {
            $("#enrolment_action_err").text("").removeClass('error');
            $("#enrolment_action_err").removeClass('error');
            retVal = true;
        }
        return retVal;
    }
</script>
<?php echo form_close(); ?>

<?php
$atr = 'id="update_fee" name="update_fee" method="post"';
echo form_open("tp_gateway/update_fee_collection_tpg", $atr);
?>
<div class="modal1_0001" id="abd" style="display:none;height:200px;min-height: 200px;">
    <h2 class="panel_heading_style">Update Fee Collection Status</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading">Fee Collection Status:</td>
                <td>                    
                    <input type="hidden" name="tpgCourseIdfee" value="" id="tpgCourseIdfee">
                    <input type="hidden" name="tpgClassIdfee" value="" id="tpgClassIdfee">
                    <input type="hidden" name="tpgEnrolmentReferenceNumberfee" value="" id="tpgEnrolmentReferenceNumberfee">
                    <?php
                    //$fee_collectionStatus_attr = 'id="fee_collectionStatus"';
                    //echo form_dropdown('fee_collectionStatus', $feecollectionStatus, $feecollectionStatus_val, $fee_collectionStatus_attr);
                    ?>
                    <select id='fee_collectionStatus' name="fee_collectionStatus">
                        <option value="">Select</option>                        
                    </select>
                    <span id="fee_collection_err"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cance89">
        <span href="#abd" rel="modal:close"><button class="btn btn-primary enrollment_fee_save" type="submit">Submit</button></span>
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
    $('.abd').click(function () {
        $this = $(this);
        $coursefee = $this.data('coursefee');
        $classfee = $this.data('classfee');
        $enrolrefnumfee = $this.data('enrolrefnumfee');
        paymentstatusfee = $this.data('paymentstatusfee');
        $feecollectval = $this.data('feecollectval');

        $('#tpgCourseIdfee').val($coursefee);
        $('#tpgClassIdfee').val($classfee);
        $('#tpgEnrolmentReferenceNumberfee').val($enrolrefnumfee);
        //$('#fee_collectionStatus').val($feecollectst);
        //$('#fee_collectionStatus').val($feecollectval);
        //$('#fee_collectionStatus').prop('selected').val($feecollectval);

        $("#fee_collectionStatus").empty();
        if (paymentstatusfee == 'PAID') {
            var myOptions = {
                '': 'Select',
                'Pending Payment': 'Pending Payment',
                'Partial Payment': 'Partial Payment',
                'Full Payment': 'Full Payment',
                'Cancelled': 'Cancelled'
            };
        } else {
            var myOptions = {
                '': 'Select',
                'Pending Payment': 'Pending Payment',
                'Partial Payment': 'Partial Payment',
                'Cancelled': 'Cancelled'
            };
        }

        var mySelect = $('#fee_collectionStatus');
        //if ($('#fee_collectionStatus option[value=""]').length == 0) {
        $.each(myOptions, function (val, text) {
            mySelect.append(
                    $('<option></option>').val(val).html(text)
                    );
        });
        //}
        $("#fee_collectionStatus").val($feecollectval);

        $('#abd').modal();
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

<div class="modal1_0001" id="ex9" style="display:none;height:200px;min-height: 200px;">
    <h2 class="panel_heading_style">Update TG#</h2>
    <table class="table table-striped">
        <tbody>
            <?php
            $data = array(
                'id' => 'h_class',
                'type' => 'hidden',
                'name' => 'h_class',
            );
            echo form_input($data);
            $data = array(
                'id' => 'h_user',
                'type' => 'hidden',
                'name' => 'h_user',
            );
            echo form_input($data);
            ?>
            <tr>
                <td class="td_heading">TG#:</td>
                <td>
                    <?php
                    echo form_input('tg_number', $this->input->post('tg_number'), ' id="tg_number"');
                    ?>
                    <span id="tg_number_err"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cance89">
        <span href="#ex9" rel="modal:close"><button class="btn btn-primary subsidy_save" type="button">Save</button></span>
    </div>
</div>
<div class="modal1_0001" id="exeid" style="display:none;height:200px;min-height: 200px;">
    <h2 class="panel_heading_style">Update EID#</h2>
    <table class="table table-striped">
        <tbody>
            <?php
            $data = array(
                'id' => 'eid_class',
                'type' => 'hidden',
                'name' => 'eid_class',
            );
            echo form_input($data);
            $data = array(
                'id' => 'eid_user',
                'type' => 'hidden',
                'name' => 'eid_user',
            );
            echo form_input($data);
            ?>
            <tr>
                <td class="td_heading">EID#:</td>
                <td>
                    <?php
                    echo form_input('eid_number', $this->input->post('eid_number'), ' id="eid_number"');
                    ?>
                    <span id="eid_number_err"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cance89">
        <span href="#exeid" rel="modal:close"><button class="btn btn-primary eid_save" type="button">Save</button></span>
    </div>
</div>
<div class="modal_3" id="ex8" style="display:none;">
    <h2 class="panel_heading_style">Total Payment Received Details</h2>
    <table class="no_border_table">
        <tbody>
            <tr>
                <td class="td_heading">Payment Made On:</td>
                <td><span class="r_recd_on"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Mode of Payment:</td>
                <td><span class="r_mode"></span></td>
            </tr>
            <tr style='display:none' id='sfc_claim_id_tr'>
                <td class="td_heading">SFC Claim ID:</td>
                <td><span class="sfc_claim_id"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Other Mode of Payment:</td>
                <td><span class="othr_mode"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Unit Class Fees:</td>
                <td><span class="r_class_fees"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading"><span class="r_dis_label"></span>Total Discount @ <span class="r_dis_rate"></span>%:</td>
                <td><span class="r_dis_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Total Subsidy:</td>
                <td><span class="r_subsidy_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Total Amount Due:</td>
                <td><span class="r_after_gst"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Total GST @ <span class="r_gst_rate"></span>% (<span class="r_gst_label"></span>):</td>
                <td><span class="r_total_gst"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Net Due:</td>
                <td><span class="r_net_due"></span> SGD</td>
            </tr>
        </tbody>
    </table>
    <div class="popup_cance89">   <a class="payment_recd_href" href="#"><button type="button" class="btn btn-primary">Print</button></a></div>
</div>
<div class="modal_991" id="ex5" style="display:none;">
    <p>
    <div class="classtraineeexcel"><img src="<?php echo base_url(); ?>assets/images/classtraineeexcel.png" border="0" width="907px;"></div>
</p>
</div>
<div class="modal_991" id="ex4" style="display:none;">
    <p>
    <div class="classtraineeexcel1"><img src="<?php echo base_url(); ?>assets/images/classtraineeexcel1.png" border="0" width="1993px;"></div>
</p>
</div>
<?php
$atr = 'id="trainer_feedback_form" name="trainer_feedback_form" ';
echo form_open("class_trainee/trainer_feedback", $atr);
?>
<div class="modal1_050" id="ex7" style="width:50%">
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
                    <td class="td_heading">Feedback Score:</td>
                    <td>                    
                        <?php
                        $feedback_score = array('' => 'Select', '10' => 10, '20' => 20, '30' => 30, '40' => 40, '50' => 50, '60' => 60, '70' => 70, '80' => 80, '90' => 90, '100' => 100);
                        $feedback_score_attr = 'id="feedback_score" "required"';
                        echo form_dropdown('FSCORE', $feedback_score, '', $feedback_score_attr);
                        ?>   
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Feedback Grade:</td>
                    <td>                    
                        <?php
                        $feedback_grade = array('' => 'Select', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F');
                        $feedback_grade_attr = 'id="feedback_grade" "required"';
                        echo form_dropdown('FGRADE', $feedback_grade, '', $feedback_grade_attr);
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

                        <?php echo form_radio($COMYTCOM_EX); ?> Exempted <br/>                    
                        <?php echo form_radio($COMYTCOM_ABS); ?> Absent<br/>
                        <?php
                        if (TENANT_ID == 'T02') {/////below code was added by shubhranshu for xp2 for attrition option start-----
                            echo form_radio($COMYTCOM_ATTRITION);
                            echo "Attrition <br/>";
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
        <div class="popup_cance89">        
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <button class="btn btn-primary" id="lock_att" type="submit">Save</button>&nbsp;&nbsp;
                    <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
                </div>
            </div>
        </div>
    </span>
    <br>
    <?php
    echo form_hidden('query_string', $export_url);
    echo form_hidden('page', $this->uri->segment(2));
    ?>
    <?php echo form_close(); ?>

    <div class="attendance_lock" style="display: none; height: 50px;text-align: center">                    
        <span style="color:red;"> <i>Can`t update Competent or Not Yet Competent because class attendance is locked. To Update it please contact to Administrator.</i>
        </span>
        <br/>                       
    </div>
</div>

<?php
if ($tenant_id == 'T20' || $tenant_id == 'T17') {
    ?>
    <!--added by shubhranshu for the wablab trainee feedback-->

    <div id="ex6" class="modal1_trainee_feedback modal" width="85%">
        <?php
        $atr = 'id="traineefeedbackForm" name="validate_form"';
        echo form_open('class_trainee/trainee_feedback', $atr);
        ?> 
        <p>
        <h2 class="panel_heading_style" style = "width:100%" >Trainee Feedback</h2> 
        <center> <span id="ssp" style="display:none"></span></center>
        <div id ="trainee_fdbk">

            <table class="table table-striped">
                <?php
                $options = array('' => 'Select', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
                ?>
                <tbody>

                    <tr>
                        <td colspan="2" class="td_heading"><strong><u>A. <?php echo $trainee_feedback['FDBCK01']['category_name'] ?></u></strong> </td>
                    </tr>
                    <tr>     
                        <td colspan="2">1.<?php echo $trainee_feedback['Q01']['category_name']; ?>
                            <?php
                            $atr = 'id="Q01" class="feed"';
                            echo form_dropdown('Q01', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>     
                        <td colspan="2">2.<?php echo $trainee_feedback['Q02']['category_name']; ?>
                            <?php
                            $atr = 'id="Q02" class="feed"';
                            echo form_dropdown('Q02', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">3.<?php echo $trainee_feedback['Q03']['category_name']; ?>
                            <?php
                            $atr = 'id="Q03" class="feed"';
                            echo form_dropdown('Q03', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">4.<?php echo $trainee_feedback['Q04']['category_name']; ?>
                            <?php
                            $atr = 'id="Q04" class="feed"';
                            echo form_dropdown('Q04', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="td_heading"><strong><u>B. <?php echo $trainee_feedback['FDBCK02']['category_name'] ?></u></strong> </td>
                    </tr>
                    <tr>
                        <td colspan="2">5.<?php echo $trainee_feedback['Q05']['category_name']; ?>
                            <?php
                            $atr = 'id="Q05" class="feed"';
                            echo form_dropdown('Q05', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">6.<?php echo $trainee_feedback['Q06']['category_name']; ?>
                            <?php
                            $atr = 'id="Q06" class="feed"';
                            echo form_dropdown('Q06', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">7.<?php echo $trainee_feedback['Q07']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q07" class="feed"';
                            echo form_dropdown('Q07', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">8.<?php echo $trainee_feedback['Q08']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q08" class="feed"';
                            echo form_dropdown('Q08', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>


                    <tr>
                        <td colspan="2" class="td_heading"><strong><u>C. <?php echo $trainee_feedback['FDBCK03']['category_name'] ?></u></strong> </td>
                    </tr>
                    <tr>
                        <td colspan="2">9.<?php echo $trainee_feedback['Q09']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q09" class="feed"';
                            echo form_dropdown('Q09', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">10.<?php echo $trainee_feedback['Q10']['category_name']; ?>:
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
//                    echo form_dropdown('rating', $options,'',$atr); 
                            ?>
                            <input type='text' name="rating" id='rating'  readonly/>
                    </tr>
                    <tr>
                        <td class="td_heading">Other comments that you feel will help improve the course:</td>
                        <td>
                            <textarea maxlength="500" rows="5" cols="100" name="remarks" id="remarks" class="upper_case"></textarea>                                   

                            <span style="float:right;">
                                <input type="hidden" id ="trainee_class_id" name="trainee_class_id" value=""/>
                                <input type="hidden" id ="trainee_course_id" name="trainee_course_id" value=""/>
                                <input type="hidden" id ="trainee_user_id" name="trainee_user_id" value=""/>
                                <input type="hidden" id ="action" name="action" value="save_trainee"/>
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
        <?php
        echo form_hidden('query_string', $export_url);
        echo form_hidden('page', $this->uri->segment(2));
        ?>
        <?php form_close(); ?>
    <!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>-->

        <script>
            $('.training_update').click(function () {


                if ($('.feed').val() == '')
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
                var i = 0;
                $('.feed').each(function () {
                    sum += Number($(this).val());
                    i++;
                });
                var average = Math.round(sum / i);
                // set the computed value to 'totalPrice' textbox

                $('#rating').val(average);
            });</script>
    </p>
    </div>


<?php } else { ?>

    <div id="ex6" class="modal1_trainee_feedback modal" width="85%">
        <?php
        $atr = 'id="traineefeedbackForm" name="validate_form"';
        echo form_open('class_trainee/trainee_feedback', $atr);
        ?> 
        <p>
        <h2 class="panel_heading_style" style = "width:100%" >Trainee Feedback</h2>  
        <center> <span id="ssp" style="display:none"></span></center>
        <div id ="trainee_fdbk">
            <table class="table table-striped">
                <?php
                $options = array('' => 'Select', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
                ?>
                <tbody>

                    <tr>
                        <td colspan="2" class="td_heading"><strong><u> <?php echo $trainee_feedback['FDBCK01']['category_name'] ?></u></strong> </td>
                    </tr>
                    <tr>     
                        <td colspan="2">1.<?php echo $trainee_feedback['Q01']['category_name']; ?>
                            <?php
                            $atr = 'id="Q01" class="feed"';
                            echo form_dropdown('Q01', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>     
                        <td colspan="2">2.<?php echo $trainee_feedback['Q02']['category_name']; ?>
                            <?php
                            $atr = 'id="Q02" class="feed"';
                            echo form_dropdown('Q02', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">3.<?php echo $trainee_feedback['Q03']['category_name']; ?>
                            <?php
                            $atr = 'id="Q03" class="feed"';
                            echo form_dropdown('Q03', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">4.<?php echo $trainee_feedback['Q04']['category_name']; ?>
                            <?php
                            $atr = 'id="Q04" class="feed"';
                            echo form_dropdown('Q04', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">5.<?php echo $trainee_feedback['Q05']['category_name']; ?>
                            <?php
                            $atr = 'id="Q05" class="feed"';
                            echo form_dropdown('Q05', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">6.<?php echo $trainee_feedback['Q06']['category_name']; ?>
                            <?php
                            $atr = 'id="Q06" class="feed"';
                            echo form_dropdown('Q06', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="td_heading"><strong><u><?php echo $trainee_feedback['FDBCK02']['category_name'] ?></u></strong> </td>
                    </tr>
                    <tr>
                        <td colspan="2">1.<?php echo $trainee_feedback['Q07']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q07" class="feed"';
                            echo form_dropdown('Q07', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">2.<?php echo $trainee_feedback['Q08']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q08" class="feed"';
                            echo form_dropdown('Q08', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">3.<?php echo $trainee_feedback['Q09']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q09" class="feed"';
                            echo form_dropdown('Q09', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">4.<?php echo $trainee_feedback['Q10']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q10" class="feed"';
                            echo form_dropdown('Q10', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">5.<?php echo $trainee_feedback['Q11']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q11" class="feed"';
                            echo form_dropdown('Q11', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="td_heading"><strong><u>C. <?php echo $trainee_feedback['FDBCK03']['category_name'] ?></u></strong> </td>
                    </tr>
                    <tr>
                        <td colspan="2">1. <?php echo $trainee_feedback['Q12']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q12" class="feed"';
                            echo form_dropdown('Q12', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">2.  <?php echo $trainee_feedback['Q13']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q13" class="feed"';
                            echo form_dropdown('Q13', $options, '', $atr);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">3.  <?php echo $trainee_feedback['Q14']['category_name']; ?>:
                            <?php
                            $atr = 'id="Q14" class="feed"';
                            echo form_dropdown('Q14', $options, '', $atr);
                            ?> 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">4.  <?php echo $trainee_feedback['Q15']['category_name']; ?>:
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
//                    echo form_dropdown('rating', $options,'',$atr); 
                            ?>
                            <input type='text' name="rating" id='rating'  readonly/>
                    </tr>
                    <tr>
                        <td class="td_heading">Any other remarks:</td>
                        <td>
                            <textarea maxlength="500" rows="1" cols="100" name="remarks" id="remarks" class="upper_case"></textarea>                                   

                            <span style="float:right;">
                                <input type="hidden" id ="trainee_class_id" name="trainee_class_id" value=""/>
                                <input type="hidden" id ="trainee_course_id" name="trainee_course_id" value=""/>
                                <input type="hidden" id ="trainee_user_id" name="trainee_user_id" value=""/>
                                <input type="hidden" id ="action" name="action" value="save_trainee"/>
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
        <?php
        echo form_hidden('query_string', $export_url);
        echo form_hidden('page', $this->uri->segment(2));
        ?>
        <?php form_close(); ?>
    <!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>-->

        <script>
            $('.training_update').click(function () {


                if ($('.feed').val() == '')
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
                var i = 0;
                $('.feed').each(function () {
                    sum += Number($(this).val());
                    i++;
                });
                var average = Math.round(sum / i);
                // set the computed value to 'totalPrice' textbox

                $('#rating').val(average);
            });
            $(document).ready(function () {
                // $('#trainee').attr('disabled', 'disabled');
                // $('#taxcode').attr('disabled', 'disabled');
            });
        </script>
    </p>
    </div>

<?php } ?>
</div>
