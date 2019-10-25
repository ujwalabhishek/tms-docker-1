<?php
if (empty($details['invoice_id'])) {
    $invoice_data = '';
} else {
    $invoice_data = ' (Inv. Id: ' . $details['invoice_id'] . ')';
}
?>
<style>
    #course{
        width:470px;
    }
    #class{
        width:200px;
    }
</style>
<script>
    $role_id = "<?php echo $this->session->userdata('userDetails')->role_id;?>";//added by shubhranshu
    $privilage = "<?php echo $privilage;?>"; //added by shubhranshu
    $export_url = '<?php echo base_url() . "trainee/download_import_xls/"; ?>';
    $files = '<?php echo $files; ?>'
    $filesa = '<?php echo $filesa; ?>'
    $filesb = '<?php echo $filesb; ?>'
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $salesexec_check = '<?php echo $salesexec_check; ?>';
    $login_user_id = '<?php echo $this->session->userdata('userDetails')->user_id; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bulkenrollment.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }

    if ($error)
        echo '<div class="error1">' . $error . '</div>';
    echo validation_errors('<div class="error1">', '</div>');
    ?> 
    <?php
    $atr = 'id="bulkenrollment" name="bulkenrollment" method="post"';
    echo form_open_multipart("class_trainee/bulk_enrollment", $atr);
    ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Class Trainee Enrollment - Bulk Enrollment</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="30%" class="td_heading">Select Company:<span class="required">*</span></td>
                    <td colspan="3" width="50%">
                        <?php
                        $options = array();
                        if($this->data['user']->role_id != 'COMPACT') {
                            $options[''] = 'Select';
                        }
                        foreach ($companies as $row) {
                            $options[$row->company_id] = $row->company_name;
                        }
                        $role_array = array("COMPACT");
                        if(!in_array($this->session->userdata('userDetails')->role_id,$role_array)) {
                            $tenant_details = fetch_tenant_details($this->session->userdata('userDetails')->tenant_id);
                            $options[$tenant_details->tenant_id] = $tenant_details->tenant_name;
                        }
                        echo form_dropdown('company', $options, $this->input->post('company'), 'id="company" style="width:700px;"')
                        ?>
                        <span id="company_err"></span>
                    </td>
                </tr>
                <tr>
                    <td width="20%" class="td_heading">Select Course:<span class="required">*</span></td>
                    <td width="30%">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($courses as $k => $v) {
                            $options[$k] = $v;
                        }
                        echo form_dropdown('course', $options, $this->input->post('course'), 'id="course"')
                        ?>
                        <span id="course_err"></span>
                    </td>
                    <td width="20%" class="td_heading">Select Class:<span class="required">*</span></td>
                    <td width="30%"><?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $options[$v->class_id] = $v->class_name;
                        }
                        echo form_dropdown('class', $options, $this->input->post('class'), 'id="class"')
                        ?>
                        <span id="class_err"></span>
                    </td>
                </tr>
                <?php
                $style = '';
                if (!empty($salesexec_check) || $this->data['user']->role_id == 'COMPACT') {
                    $style = 'display:none;';
                }
                ?>
                <tr style="<?php echo $style ?>">
                    <td width="20%" class="td_heading">Select Sales Executive:</td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($salesexec as $k => $row) {
                            $options[$row['user_id']] = $row['first_name'] . ' ' . $row['last_name'];
                        }
                        echo form_dropdown('salesexec', $options, $this->input->post('salesexec'), 'id="salesexec"')
                        ?>
                        <span id="salesexec_err"></span>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Import Trainee Detail:<span class="required">*</span></td>
                    <td><?php
                        ?>
                        <input name="userfile" type="file" id="upload">
                        <br>
                        <span id="upload_err"></span>
                    </td>
                    <td><span> <input name="upload" type="hidden" value="upload">
                            <button name="upload" type="submit" id="submit" class="btn btn-xs btn-primary no-mar" value="upload"/><span class="glyphicon glyphicon-upload"></span> Upload</button>
                            (xls or xlsx)
                        </span></td>
                    <td><a href="<?php echo base_url() . '/uploads/bulk_enrollment.xls'; ?>"><span class="label label-default black-btn pull-right"><span class="glyphicon glyphicon-download-alt"></span> Download Import XLS</span></a></td>
                </tr>

            </tbody>
        </table>
    </div>
    <?php echo form_close(); ?>
    <div style="clear:both;"></div><br>
    <?php
    unset($details['invoice_id']);
    if (!empty($details)) {
        ?>
        <div class="panel-heading panel_headingstyle" style="margin-top:0px;"><strong>Filter On Import Status:</strong> <select id="filter_status">
                <option>All</option>
                <option>Success</option>
                <option>Failure</option>
            </select>
        </div>
    <?php } ?>
    <div style="clear:both;"></div>
    <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-import"></span> Import Preview <?php echo $invoice_data; ?>
        <?php
        if (!empty($details)) {
            ?>
            <div class="add_button">
                <span class="label label-default black-btn pull-right">
                    <a href="<?php echo base_url() . "trainee/download_import_xls/" . $files; ?>" class="small_text1 export_but">
                        <span class="glyphicon glyphicon-export"></span> Export to XLS
                    </a>
                </span>
            </div>  
        <?php } ?>
    </h2>

    <div class="excel">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th> NRIC/FIN No.</th>
                        <th>Enroll Type</th>
                        <th>TG Number</th>
                        <th>Status</th>
                        <th>Failure Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($details as $k => $data) {
                        $class = 'nodanger';
                        if ($data['status'] == 'FAILED')
                            $class = 'danger';
                        
                        ?>
                    <?php if($k != 'flag'){?>  
                        <tr class="<?php echo $class; ?>">
                            <td><?php echo $data['taxcode']; ?> </td>
                            <td> <?php echo $data['enrollment_type']; ?></td>
                            <td><?php echo $data['tg_number']; ?> </td>
                            <td> <?php
                                if ($data['status'] == 'FAILED')
                                    echo '<font color="red" > Fail. </font>';
                                else
                                    echo '<font color="green"> Success </font>';
                                ?></td>
                            <td> <?php echo $data['failure_reason']; ?></td>
                        </tr>
                        <?php
                    }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div style="clear:both;"></div><br>
    <span class="required required_i">* Required Fields</span>
</div>
<div class="modal" id="ex1" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Heading Goes Here...</h2>
    Detail Goes here.  <br>
    <div class="popup_cancel">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
</div>
<div class="modal_991" id="ex9" style="display:none;">
    <p>
    <div class="excel11"><img src="<?php echo base_url(); ?>assets/images/excel111.png" border="0" width="1279px;"></div>
</p>
</div>
<div class="modal_991_99" id="ex10" style="display:none;">
    <p>
    <h4>Class Trainee Bulk Enrollment Import from XLS as on  May 26 2014</h4>
    <div class="bulkenrollmentexcel"><img src="<?php echo base_url(); ?>assets/images/bulkenrollmentexcel.png" border="0" width="592px;"></div>
</p>
</div>

<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->
<div class="modal1_055" id="ex22" <?php if($details['flag']==false) {echo "style='display:none'";}?>>         
    <h2 class="panel_heading_style">Warning</h2>   
    <div  style="margin-top:7%">
        <p style="text-align: center;" >
            The trainees whom you are about to enrol is part of the restricted list. Please acknowledge to continue !!!
        </p>
        <?php 
//        if($details['restrict_arr'] != ''){
//            foreach($details['restrict_arr'] as $s) { $j=1;?>
        
                <!--<span id="nric_list" style="font-weight:bold;color:red"><?php //echo $j.'.'.$s;?></span>-->
       <?php   //$j++;}
        
        //}
        ?>
<!--         <p style="text-align: center;font-weight:bold;" >
            Do you wants to enroll the above NRIC Now?
        </p>-->
        <div class="popup_cancel9">
            <span rel="modal:close">
                <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_yes" type="button">Yes, I understand.</button></a>
            </span>
            
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $flag = "<?php echo $details['flag'];?>";
        if($privilage == '0'){///added by shubhranshu
            if($role_id == 'ADMN'){///added by shubhranshu
                 if($flag == 'true'){$('#ex22').modal();}  ///added by shubhranshu
            }///added by shubhranshu
        }else {
            if($privilage == '1'){
                if($flag == 'true'){
                    $('#ex22').modal();
                 }
            }///added by shubhranshu
        }
    });
    
</script>

<!---------- /*  added by shubhranshu for client requirement on 21/03/2019 */-->

