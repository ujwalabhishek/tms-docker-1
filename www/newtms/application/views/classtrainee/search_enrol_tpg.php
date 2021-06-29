<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
    
    var CLIENT_DATE_FORMAT = 'yy-mm-dd';            
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classtraineelist.js?0.0311111111111111"></script>
<style>
    table td{
        font-size: 11px;
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
    echo form_open("class_trainee/tpg_search_enrolment", $atr);
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
                    <td class="td_heading">Period From:</td>
                    <td>
                        <?php
                        $attr_js = 'id="input_from_date" placeholder="yyyy-mm-dd"';
                        echo form_input('from_date', $this->input->get('from_date'), $attr_js);
                        ?>
                    </td>
                    <td class="td_heading">To:</td>
                    <td>
                        <?php
                        $attr_js = 'id="input_to_date" placeholder="yyyy-mm-dd"';
                        echo form_input('to_date', $this->input->get('to_date'), $attr_js);
                        ?>
                    </td>                    
                </tr>
                <tr>
                    <td class="td_heading">Trainee ID Type:</td>
                    <td>
                        <?php
                        $attr = 'id="idType"';
                        echo form_dropdown('idType', $idType_options, '', $attr);
                        ?>                       
                        <?php echo form_error('idType', '<div class="error">', '</div>'); ?>
                    </td>
                    <td class="td_heading">Sponsorship Type:</td>
                    <td>
                        <?php
                        $attr = 'id="sponsorshipType"';
                        echo form_dropdown('sponsorshipType', $sponsorshipType_options, '', $attr);
                        ?>                        
                        <?php echo form_error('sponsorshipType', '<div class="error">', '</div>'); ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Payment Status:</td>
                    <td>
                        <?php
                        $attr = 'id="feeCollectionStatus"';
                        echo form_dropdown('feeCollectionStatus', $feeCollectionStatus_options, '', $attr);
                        ?>                        
                        <?php echo form_error('feeCollectionStatus', '<div class="error">', '</div>'); ?>
                    </td>
                    <td class="td_heading">Enrolment Date:</td>
                    <td>
                        <?php
                        $attr_js = 'id="input_enrol_date" placeholder="yyyy-mm-dd"';
                        echo form_input('enrol_date', $this->input->get('enrol_date'), $attr_js);
                        ?>
                    </td> 
                </tr>
                <tr>
                    <td class="td_heading">Enrolment Status:</td>
                    <td>
                        <?php
                        $attr = 'id="enrolmentStatus"';
                        echo form_dropdown('enrolmentStatus', $enrolmentStatus_options, '', $attr);
                        ?>                        
                        <?php echo form_error('enrolmentStatus', '<div class="error">', '</div>'); ?>
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
                    <td colspan="3">
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
                </tr>
                <tr>
                    <td class="td_heading">No. Of Results Per Page:</td>
                    <td colspan="3">
                        <?php
                        $attr = 'id="noResultsPerPage"';
                        echo form_dropdown('noResultsPerPage', $noResultsPerPage_options, '', $attr);
                        ?>                        
                        <?php echo form_error('noResultsPerPage', '<div class="error">', '</div>'); ?>
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
                        <th width="10%" class="th_header">Trainee Name</th>
                        <th width="8%" class="th_header">Trainee ID</th>
                        <th width="10%" class="th_header">Course Ref No</th>
                        <th width="12%" class="th_header">Course Title</th>
                        <th width="10%" class="th_header">Course Run ID</th>
                        <th width="8%" class="th_header">Course Start Date</th>
                        <th width="8%" class="th_header">Course End Date</th>
                        <th width="9%" class="th_header">Enrolment Reference No.</th>
                        <th width="6%" class="th_header">Sponsorship Type</th>
                        <th width="6%" class="th_header">Discount</th>
                        <th width="6%" class="th_header">Fee Collection Status</th>                        
                        <th width="6%" class="th_header">Enrolment Date</th>                                                                  
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $err_msg = 'There are no trainees enrolled to any class currently.';
                    if (!empty($_GET)) {
                        $err_msg = 'No data available for the search criteria entered.';
                    }
                    //////data for tpg search
                    if (!empty($tabledata_tpg)) {
                        foreach ($tabledata_tpg->data as $row) {
                            ?>
                            <tr>
                                <td class="name" ><?php echo $row->enrolment->trainee->fullName; ?></td>
                                <td><?php echo $row->enrolment->trainee->id; ?></td>
                                <td><?php echo $row->enrolment->course->referenceNumber; ?></td>
                                <td><?php echo $row->enrolment->course->title; ?></td>
                                <td><?php echo $row->enrolment->course->run->id; ?></td>
                                <td><?php echo $row->enrolment->course->run->startDate; ?></td>
                                <td><?php echo $row->enrolment->course->run->endDate; ?></td>
                                <td><?php echo $row->enrolment->referenceNumber; ?></td>
                                <td><?php echo $row->enrolment->trainee->sponsorshipType; ?></td>
                                <td><?php echo $row->enrolment->trainee->fees->discountAmount; ?></td>
                                <td><?php echo $row->enrolment->trainee->fees->collectionStatus; ?></td>
                                <td><?php echo $row->enrolment->trainee->enrolmentDate; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="17" class="error" style="text-align: center">' . $error_msg . '</td></tr>';
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