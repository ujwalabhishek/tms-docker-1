<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/change_payment_mode.js"></script>
<div class="col-md-10 right-minheight">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-refresh"></span>Class Trainee Enrollment - Change Payment Mode</h2>
    <div class="table-responsive">      
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading" width="20%">Select Account Type:</td>
                    <td colspan="3">
                        <?php
                        $options = array(
                            'individual' => 'Individual',
                            'company' => 'Company'
                        );
                        $js = 'id="account_type"';
                        echo form_dropdown('account_type', $options, '', $js);
                        ?>
                    </td>
                </tr>                
                <tr class="row_dimm9">
                    <td class="td_heading" >
                        &nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '2',
                            'checked' => 'checked'
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;NRIC/FIN No.:
                    </td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'taxcode',
                            'name' => 'taxcode',
                            'class' => 'upper_case',
                            'value' => $this->input->post('taxcode'),
                            'style' => 'width:200px',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'taxcode_id',
                            'name' => 'taxcode_id',
                            'type' => 'hidden',
                            'value' => $this->input->post('taxcode_id')
                        );
                        echo form_input($data);
                        ?>
                        <span id="taxcode_err"></span>
                    </td>
                    <td class="td_heading">&nbsp;&nbsp;
                        <?php
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => '3',
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp;Trainee Name.:
                    </td>
                    <td ><?php
                        $data = array(
                            'id' => 'trainee',
                            'name' => 'trainee',
                            'class' => 'upper_case',
                            'value' => $this->input->post('trainee'),
                            'style' => 'width:300px',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'trainee_id',
                            'name' => 'trainee_id',
                            'type' => 'hidden',
                            'value' => $this->input->post('trainee_id')
                        );
                        echo form_input($data);
                        ?>
                        <span id="trainee_err"></span>
                    </td>
                </tr>
                <tr class="company_td" style="display: none;">
                    <td class="td_heading">Company :</td>
                    <td colspan="3">                                              
                        <?php
                        $company = array(
                            'name' => 'company_name',
                            'id' => 'company_name',
                            'value' => $this->input->get('company_name'),
                            'style'=>'width:650px;',
                            'class'=>'upper_case',
                            'autocomplete'=>'off'
                        );
                        echo form_input($company);
                        echo form_hidden('company', $this->input->get('company'), $id='company');
                        ?>
                        <span id="company_name_err"></span>
                        <span id="company_err"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4"> <div class="pull-right">
                            <button type="button" class="search_button btn btn-xs btn-primary no-mar" id="srch_btn">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                        </div></td>
                </tr>
            </tbody>
        </table>        
    </div>
    <br/>
    <div class="trainee_div" style="display: none;">
        <h2 class="sub_panel_heading_style"><span class="glyphicon glyphicon-user"></span> Enrollment details for <span id='trainee_name'></span></h2> 
        <div class="trainee_alert"></div>
        <div class="table-responsive">
            <div class="border_table_div table-responsive d-table-scroll">
                <table class="table table-striped trainee_table">
                    <thead>
                        <tr>
                            <th class="th_header">Sl#.</th>
                            <th class="th_header">Course Name</th>                            
                            <th class="th_header">Class Name</th>
                            <th class="th_header">Payment Mode</th>
                            <th class="th_header">Invoice No./ Invoice Date</th>                                                   
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
    <div style="clear:both;"></div><br>
    <div class="amountdetails_div companyamounts_display common_pay" style="display: none;">        
        <div class="button_class99">
            <button type="button" class="change_paymode btn btn-primary"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Change Payment Mode</button> &nbsp; &nbsp;            
        </div>
    </div>
    <br>
    <div class="confirm_div" style="display: none; background-color: #f4fcff; height: 50px;text-align: center">  
        <?php
        $atr = 'id="change_payment_mode_individual" name="change_payment_mode_individual" method="post"';
        echo form_open("class_trainee/change_payment_mode_update", $atr);
        ?>  
        <input type="hidden" value="" name="trainee_user_id" id="trainee_user_id"/>
        <input type="hidden" value="" name="payment_due_id" id="payment_due_id"/>
        <input type="hidden" value="" name="payment_mode" id="payment_mode"/>
        <input type="hidden" value="" name="course_id" id="course_id"/>
        <input type="hidden" value="" name="class_id" id="class_id"/>        
        <input type="hidden" value="" name="company_id" id="company_id"/>        
        <?php form_close(); ?>
        <span style="color:red;font-weight: bold">Do you want to change to '<label id="paymode"></label>'? </span>                       
        <br/>
        <button type="button" class="btn btn-xs btn-primary no-mar yes_button">
            <span class="glyphicon glyphicon-retweet"></span>
            Yes
        </button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-xs btn-primary no-mar no_button">
            <span class="glyphicon glyphicon-retweet"></span>
            No
        </button>                
    </div>
</div>