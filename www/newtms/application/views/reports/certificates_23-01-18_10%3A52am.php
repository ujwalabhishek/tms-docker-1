<?php $check_startdate = $this->input->get('start_date'); ?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $get_startdate = '<?php echo $start_date; ?>';
    $get_enddate = $max_date = '<?php echo $end_date; ?>';
    $check_startdate = '<?php echo $check_startdate; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reportcertificates.js"></script>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - Certificates Report</h2>
    <h5 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>
    <div class="table-responsive">
        <?php
        $course_id = $this->input->get_post("courseId");
        $class_id = $this->input->get_post("classId");
        $trainee = $this->input->get_post("trainee_id");
        $status = $this->input->get_post("status");
        $start_date = $this->input->get_post("start_date");
        $end_date = $this->input->get_post("end_date");

        $atr = array('id' => 'wda_report_form', 'method' => 'get');
        echo form_open("reports/certificates", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Select Course Name:</td>
                    <td width="30%"><?php echo form_dropdown("courseId", $courses, $this->input->get('courseId'), 'id="courseId"') ?> </td>
                    <td class="td_heading" width="20%">Class Name:</td>
                    <td colspan="3" width="30%"><?php echo form_dropdown("classId", $classes, $this->input->get('classId'), 'id="classId"') ?> </td>
                </tr>
                <tr>
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '1',
                            'checked' => true
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Trainee Name.:</td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'trainee',
                            'name' => 'trainee',
                            'class'=>'upper_case',
                            'value' => $this->input->get('trainee')
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'trainee_id',
                            'name' => 'trainee_id',
                            'type' => 'hidden',
                            'value' => $this->input->get('trainee_id')
                        );
                        echo form_input($data);
                        ?>
                        <span id="trainee_err"></span>
                    </td>                
                        <td  colspan="4"/>
                </tr>
                <tr>
                    <td class="td_heading">Course Completion Date From:</td>
                    <td><input type="text" name="start_date" id="start_date" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('start_date'); ?>"></td>
                    <td class="td_heading">To:</td>
                    <td colspan="2"><input type="text" name="end_date"  id="end_date" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('end_date'); ?>"></td>
                    <td align="center"><button type="submit" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="clear:both;"></div><br/>    
    <?php
    if (!empty($tabledata)) {
        if (empty($start_date) && empty($end_date)) {
            $period = ' for ' . date('F d Y, l');
        } else {
            $period = 'for the period';
            if (!empty($start_date))
                $period .= ' from ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $start_date)->getTimestamp());
            if (!empty($end_date))
                $period .= ' to ' . date('F d Y', DateTime::createFromFormat('d-m-Y', $end_date)->getTimestamp());
        }
        if (!empty($course_id)) {
            $period .= ' \'' . $courses[$course_id] . '\'';
        }
        if (!empty($class_id)) {
            $period .= ' \'' . $classes[$class_id] . '\'';
        }
        if (!empty($trainee_name)) {
            $period .= ' \'' . $trainee_name->first . ' ' . $trainee_name->last . ' ' . $trainee_name->tax_code . '\'';
        }
        if (!empty($status)) {
            if ($status == 'PENDCOLL') {
                $period .= ' \' Pending Collection \'';
            } elseif ($status == 'EXPIRD') {
                $period .= ' \' Expired / Due for Renewal \'';
            }
        }
        ?>
        <div class = "panel-heading panel_headingstyle" style = "width:100%;"><strong>Certificates Report <?php echo $period ?></strong></div>
        <br>
        <div>
            <span style="float: left;color: blue;">**Coll. Dt.: Certificate Available From</span>
            <span class="pull-right">
                <a href="<?php echo site_url('/reports/certificates_export_xls') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/reports/report_certificates_pdf') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
            </span>
        </div>
        <br><br>
        <table class="table table-striped">
            <thead>
                <?php
                $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                $pageurl = $controllerurl;
                ?>
                <tr>
                    <th width="10%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=crse.crse_name&o=" .$ancher; ?>" >Course Name</a></th>
                    <th width="15%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=cls.class_name&o=" .$ancher; ?>" >Class Name</a></th>
                    <th width="10%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=usrs.tax_code&o=" .$ancher; ?>" >NRIC/FIN No.</a></th>
                    <th width="15%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=pers.first_name&o=" .$ancher; ?>" >Trainee Name</a></th>
                    <th width="15%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=cls.class_end_datetime&o=" .$ancher; ?>" >Compl. Dt. (Coll. Dt)</a></th>
                    <th width="10%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=cls.certi_coll_date&o=" .$ancher; ?>" >Collected On</a></th>
                    <th width="15%" class="th_header">Status (Expiry)</th>
                    <th width="15%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl. "?" . $sort_link."&f=enrol.payment_status&o=" .$ancher; ?>" >Pymnt. Status</a></th>
                    <th width="5%" class="th_header">LOC</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($tabledata as $data) {
                    $certi_coll_date = (empty($data->certi_coll_date)) ? '' : ' (' . date('d/m/Y', strtotime($data->certi_coll_date)) . ')';
                    $certified_date = (empty($data->certificate_coll_on)) ? '' : date('d/m/Y', strtotime($data->certificate_coll_on));
                    $validity_date = '';
                    $status = '';
                    if ($data->crse_cert_validity > 0) {
                        $date = strtotime("+" . $data->crse_cert_validity . " days", strtotime($data->class_end_datetime));
                        $validity_datecheck = date('d-m-Y', $date);
                        $validity_date = ' <span style="color:blue;">(' . date('d/m/Y', $date) . ')</span>';
                        $cur_date = date('d-m-Y');
                        if (strtotime($cur_date) > strtotime($validity_datecheck)) {
                            $status = '<span class="red">Expired/ Due for Renewal</span>';
                        }else if (strtotime($cur_date) <= strtotime($validity_datecheck)) {
                            $status = '<span class="green">ACTIVE</span>';
                        }
                    } else {
                        $status = 'Life Long';
                    }
                    $paid_arr = array('PAID' => 'Paid', 'PARTPAID' => 'Part Paid', 'NOTPAID' => 'Not Paid','PYNOTREQD' => 'PAYMENT NOT REQUIRED');
                    $paid_sty_arr = array('PAID' => 'color:green;', 'PARTPAID' => 'color:red;', 'NOTPAID' => 'color:red;','PYNOTREQD' => 'color:#ffcc66;');
                    echo '<tr>
                            <td>' . $data->crse_name . '</td>
                            <td>' . $data->class_name . '</td>
                            <td>' . $data->tax_code . '</td>
                            <td>' . $data->first_name . ' ' . $data->last_name . '</td>
                            <td>' . date('d/m/Y', strtotime($data->class_end_datetime)) . ' <span style="color:blue;">' . $certi_coll_date . '</span></td>
                            <td>' . $certified_date . '</td>
                            <td>' . $status . $validity_date . '</td>
                            <td><span style="'.$paid_sty_arr[$data->payment_status].'">' . $paid_arr[$data->payment_status] . '</span></td>
                            <td><a href="' . base_url() . 'trainee/print_loc/' . $data->class_id . '/' . $data->user_id . '" style="color:red;">LOC</a></td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>
    <?php } else { ?>
        <br>
        <table class="table table-striped">
            <tr class="danger">
                <td colspan="10" style="color:red;text-align: center;">No data available.</td>
            </tr>
        </table>
    <?php } ?>
    <br>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
</div>


