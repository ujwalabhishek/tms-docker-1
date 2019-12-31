<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reporttraqom.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Reports - TRAQOM Report</h2>
    <h5 class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>

    <div class="table-responsive">
        <?php
        $course_id = $this->input->get_post("courseId");
        $class_id = $this->input->get_post("classId");
        $start_date1 = $this->input->get_post("start_date1");
        $start_date2 = $this->input->get_post("start_date2");
        $end_date = $this->input->get_post("end_date");

        $atr = array('id' => 'soa_report_form', 'method' => 'post');
        echo form_open("reports/traqom_report", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Generate TRAQOM Report by:</td>
                    <?php
                    $generateby = array(
                        '' => 'Select',
                        '1' => 'TRAQOM1',
                        '2' => 'TRAQOM2',
                    );
                    ?>
                    <td colspan="5"><?php echo form_dropdown("generateby", $generateby, $this->input->get('generateby'), 'id="generateby"') ?> 
                    </td>
                </tr>
                
                <tr class="generate1" style="display: none;">
                    <td class="td_heading">Select the Date for TRAQOM1:</td>
                    <td><input type="text" name="start_date1" id="start_date1" placeholder="dd/mm/yyyy" 
                               value="<?php echo $this->input->get('start_date1'); ?>">
                        <br/><span id="start_date1_err"></span>
                    </td>
                </tr>
                <tr class="generate2" style="display: none;">
                    <td class="td_heading">Select the Date for TRAQOM2 :</td>
                    <td>
                        <input type="text" name="start_date2" id="start_date2" placeholder="dd/mm/yyyy" 
                               value="<?php echo $this->input->get('start_date2'); ?>">
                        <br/><span id="start_date2_err"></span>
                    </td>
                </tr>
<!--                <tr class="generate2" style="display: none;">
                    <td class="td_heading">Class Completion Date From:</td>
                    <td><input type="text" name="start_date" id="start_date" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('start_date'); ?>">
                        <br/><span id="start_date_err"></span>
                    </td>
                    <td class="td_heading">To:</td>
                    <td colspan="3"><input type="text" name="end_date"  id="end_date" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('end_date'); ?>"></td>
                </tr>-->
                
                <tr class="search_but" style="display: none;">
                    <td align="right" colspan="6"><button type="submit" class="btn btn-xs btn-primary no-mar submit_but"><span class="glyphicon glyphicon-search"></span>Export</button></td>
                </tr>
            </tbody>
        </table>
        <div class="modal-inv" id="ex13" style="display:none;width:25%">
            <p>
            <h2 class="panel_heading_style">Select Export Type</h2>
            <div>
                <?php
                $data = array('name' => 'select_soa_print', 'class' => 'select_soa_print');
                echo form_radio($data, 1, TRUE, $extra);
                echo '&nbsp; &nbsp; XLS'
                ?>
            </div>
            <div>
                <?php
                $data = array('name' => 'select_soa_print', 'class' => 'select_soa_print');
                echo form_radio($data, 2, FALSE, $extra);
                echo '&nbsp; &nbsp; CSV';
                ?>
                <br/>
                <span id="xls_err"></span>
            </div>
            <div class="popup_cancel popup_cancel001">
                <span href="#" rel="modal:close"><button class="btn btn-primary print_soa_report" type="button">Print</button></span></div>
            </p>
        </div>
        <?php form_close(); ?>
    </div>
    <div style="clear:both;"></div><br/>    
</div>
<div class="modal0000" id="ex11" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    There are no data in the given date .<br>
        <div class="popup_cancel popup_cancel001">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button" id='okbtn'>Ok</button></a>
        </div>
    </p>
</div>



