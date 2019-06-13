<script src="<?php echo base_url(); ?>assets/js/viewtenant.js" type="text/javascript"></script>
<?php
if ($tenant->account_status == 'ACTIVE') {
    $status_cls = 'green';
    $deactivation_reason = '';
    $deactivate_button = '<a href="#ex8" rel="modal:open" class="small_text check_deactivate">
            <button class="btn btn-primary" type="button">
                <span class="glyphicon glyphicon-remove-sign"></span></span>&nbsp;Deactivate</button>
        </a>';
} elseif ($tenant->account_status == 'INACTIV') {
    $status_cls = 'red';
    $deactivation_reason = '<tr class="danger">
                            <td class="td_heading" width="15%">Deactivation Reason:</td>
                            <td colspan="5">' . $meta_map[$tenant->account_deactivation_reason] . $tenant->account_deactivation_reason_oth . '</td>                    
                        </tr>';
    $deactivate_button = '';
}

function get_date($timestamp, $format = 'd/m/Y h:i A') {
    $timestamp = ($timestamp == '0000-00-00 00:00:00') ? '' : $timestamp;
    if ($timestamp) {
        $date = date($format, strtotime($timestamp));
        return $date;
    }
}
?>
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-eye-open"></span> View Tenant Details</h2>		  
    <div class="bs-example">
        <div class="table-responsive">          
            <table class="table table-striped">      
                <tbody>
                        <tr>                  
                            <td class="td_heading" width="15%">Logo:</td>
                            <td colspan="5"><?php echo '<img src="' . base_url() . 'logos/' . $tenant->Logo . '" title="' . $tenant->tenant_name . '" alt="' . $tenant->Logo . '"/>'; ?></td>                    
                        </tr>
                    <tr>
                        <td class="td_heading" width="15%">Tenant Name:</td>
                        <td><?php echo $tenant->tenant_name; ?></td>   
                        <td class="td_heading" width="15%">Address:</td>
                        <td><?php echo $tenant->tenant_address; ?></td>   
                        <td class="td_heading" width="15%">City:</td>
                        <td><?php echo $tenant->tenant_city; ?></td> 
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">State:</td>
                        <td><?php echo $meta_map[$tenant->tenant_state]; ?></td>    
                        <td class="td_heading" width="15%">Country:</td>
                        <td><?php echo $meta_map[$tenant->tenant_country]; ?></td>                    
                        <td class="td_heading" width="15%">Email Id:</td>
                        <td><?php echo $tenant->tenant_email_id; ?></td>    
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">Contact No.:</td>
                        <td><?php echo $tenant->tenant_contact_num; ?></td>                    
                        <td class="td_heading" width="15%">Created On:</td>
                        <td><?php echo get_date($tenant->account_created_on); ?></td>  
                        <td class="td_heading" width="15%">Activation Start Date:</td>
                        <td><?php echo get_date($tenant->account_activation_start_date, 'd/m/Y'); ?></td>
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">Activation End Date:</td>
                        <td><?php echo get_date($tenant->account_activation_end_date, 'd/m/Y'); ?></td>
                        <td class="td_heading" width="15%">Account Status:</td>
                        <td><?php echo '<span class="' . $status_cls . '">' . $meta_map[$tenant->account_status] . '</span>'; ?></td>  
                        <td class="td_heading" width="15%">Invoice Name:</td>
                        <td><?php echo $tenant->invoice_name; ?></td>  
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">Currency:</td>
                        <td><?php echo $meta_map[$tenant->Currency]; ?></td>                    
                        <td class="td_heading" width="15%">Country of Use:</td>
                        <td><?php echo $meta_map[$tenant->Country]; ?></td>                    
                        <td class="td_heading" width="15%">Paypal Email:</td>
                        <td><?php echo $tenant->paypal_email_id; ?></td>                    
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">Website URL:</td>
                        <td><?php echo '<a href="' . $tenant->website_url . '" target="_blank">' . $tenant->website_url . '</a>'; ?></td>                    
                        <td class="td_heading" width="15%">Company Reg. No.:</td>
                        <td><?php echo $tenant->comp_reg_no; ?></td>                    
                        <td class="td_heading" width="15%">GST Reg. No.:</td>
                        <td><?php echo $tenant->gst_reg_no; ?></td>                    
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">Invoice Footer Text:</td>
                        <td colspan="5"><?php echo nl2br($tenant->invoice_footer_text); ?></td>                    
                    </tr>
                    <tr>
                        <td class="td_heading">Director Name:</td>
                        <td colspan="2">
                           <?php echo $tenant->director_name; ?>
                        </td>                    
                        <td class="td_heading">Contact Name:</td>
                        <td colspan='2'>
                           <?php echo $tenant->contact_name; ?>
                        </td> 
                    </tr>
                    <tr>
                        <td class="td_heading" width="15%">Copyright Text:</td>
                        <td colspan="5"><?php echo $tenant->CopyRightText; ?></td>                    
                    </tr>
                    <?php echo $deactivation_reason; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="button_class">
        <?php echo $deactivate_button; ?>
        <a href="<?php echo base_url($controllerurl); ?>">
            <button class="btn btn-primary" type="button">
                <span class="glyphicon glyphicon-step-backward"></span></span>&nbsp;Back</button>
        </a>
    </div>
</div>
<?php
$form_attributes = array('name' => 'deactivate_form', 'id' => 'deactivate_form');
echo form_open($controllerurl . "/deactivate", $form_attributes);
?>
<div class="modal1_051" id="ex8" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Deactivate Tenant</h2>
    <span class="error"><b>De-Activation Date: </b>
        <?php echo date('d-m-Y'); ?>
    </span>
    <span id="deactivation_date_err"></span>
    <br><br>
    <strong>Reason for De-Activation:<span class="red">*</span></strong>  <?php
    $d_reasons = fetch_metavalues_by_category_id(Meta_Values::CLASS_DEACTIVATE_REASONS);
    $reasons_options[''] = 'Select';
    foreach ($d_reasons as $item):
        $reasons_options[$item['parameter_id']] = $item['category_name'];
    endforeach;
    $reasons_options['OTHERS'] = 'Others';
    $attr = 'id="reason_for_deactivation"';
    echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
    ?> <span id="reason_for_deactivation_err"></span>
    <div id="row_dim_new1" style="float:right; margin-right:20%;display:none;">
        <?php
        $attr = array(
            'name' => 'other_reason_for_deactivation',
            'id' => 'other_reason_for_deactivation',
            'style' => 'width:200%',
            'class' => 'upper_case',
            'maxlength' => '250',
        );
        echo form_input($attr);
        echo form_hidden('tenant_id', $tenant->tenant_id);
        ?>
    </div>
    <span id="other_reason_for_deactivation_err" style="float:right;clear:both;"></span>
    <br><br>
    Are you sure you want to deactivate this tenant? <br>
    <span class="required_i red">*Required Field</span>

    <div class="popup_cancel9">
        <span rel="modal:close"><button class="btn btn-primary" type="submit">Yes</button></span>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a></div></p>
</div>
<?php echo form_close(); ?>
