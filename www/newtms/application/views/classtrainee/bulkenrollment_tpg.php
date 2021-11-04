<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
    var CLIENT_DATE_FORMAT = 'yy-mm-dd';           
</script>
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
<script>
    function selects(){
        var ele=document.getElementsByName('chk');
        for(var i=0; i<ele.length; i++){
            if(ele[i].type=='checkbox')
                ele[i].checked=true;
        }
    }
    function deSelect(){  
        var ele=document.getElementsByName('chk');
        for(var i=0; i<ele.length; i++){
            if(ele[i].type=='checkbox')
                ele[i].checked=false;
        }
    }
</script>
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> Class Trainee - Bulk Enrollment TPG</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("class_trainee/bulk_enrollment_tpg", $atr);
    ?>  
    <div class="table-responsive">
        <h5 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="15%">Course Name:</td>
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
                    <td colspan="3">
                        <span class="pull-right">
                            <button type="submit" value="Search" class="btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </td>                    
                </tr>
            </tbody>
        </table>
    </div><br>
    <?php echo form_close(); ?>
    <div class="bs-example">
        <div class="table-responsive">            
            <?php if (count($tabledata) > 0) { ?>                    
                <div class="add_button98 pull-right">
                    <?php
                    $atr = 'id="tpg_form" name="tpg_form" method="post"';
                    echo form_open("tp_gateway/bulk_enrollment_tpg", $atr);
                    ?>
                    <button type="submit" value="Submit" class="label label-default black-btn" title="Submit" />Bulk Upload</button>
                    <input type="hidden" name="courseRunId" value="<?php echo $tpg_course_run_id; ?>" id="courseRunId">
                    <input type="hidden" name="courseReferenceNumber" value="<?php echo $reference_num; ?>" id="courseReferenceNumber">                    
                    <input type="hidden" name="courseId" value="<?php echo $course_id; ?>" id="courseId">
                    <input type="hidden" name="classId" value="<?php echo $class_id; ?>" id="classId">
                    <?php echo form_close(); ?>
                </div>                  
            <?php } ?>                
        </div>
    </div>
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
                        <th width="5%"><input type="button" onclick='selects()' value="Select All"/></th>
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=tu.tax_code&o=" . $ancher; ?>">NRIC/FIN No.</a></th>
                        <th width="8%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=tup.first_name&o=" . $ancher; ?>">Name</a></th>
                        <th width="15%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=c.crse_name&o=" . $ancher; ?>">Course / Class Detail</a></th>
                        <th width="10%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=cc.class_start_datetime&o=" . $ancher; ?>">Class Duration</a></th>
                        <th width="6%" class="th_header">Company Name</th>
                        <th width="8%" class="th_header">Class Status</th>
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=ce.payment_status&o=" . $ancher; ?>">Payment</a></th>
                        <th width="11%" class="th_header">Enrollment ID</th>
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
                                <td>
                                    <input type="checkbox" id="chk" name="chk" value='<?php echo $row['taxcode']; ?>' >
                                </td>
                                <td><?php echo $row['taxcode']; ?></td>
                                <td class="name">                                    
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
                                <td><?php
                                    echo $row['status_text'] . '<br />' . $row['end_class'] . '<br />'
                                    . $result_text;
                                    ?>  
                                </td>
                                <td><?php echo $row['paid']; ?></td>
                                <td><?php echo $row['action_link']; ?></td>                                
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



