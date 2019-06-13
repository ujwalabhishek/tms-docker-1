<?php if($not_authorized_error !== '') {
    echo '<div class="error1">' . $not_authorized_error . '</div>';
} else { ?>
<?php
    $atr = 'id="companyDeactivateForm" name="companyDeactivateForm" ';
    echo form_open("company/deactivate_company/$company_id", $atr);
?>

<div class="col-md-10 col_10_height_other">
<h2 class="panel_heading_style">Deactivate Company</h2>
<div>
  <p>
 <strong>De-Activation Date:<span class="red"></span> </strong> 
<?php
echo date('d/m/Y');
 ?>
 <span id="deactivation_date_err"></span>
 <br><br>
 <strong>Reason for De-Activation:<span class="red">*</span></strong>
 
        <?php
            $d_reasons = fetch_metavalues_by_category_id(Meta_Values::COMPANY_DEACTIVATE_REASONS);
            $reasons_options[''] = 'Select';
            foreach ($d_reasons as $item):
                $reasons_options[$item['parameter_id']] = $item['category_name'];
            endforeach;
            $reasons_options['OTHERS'] = 'Others';
            $attr = 'id="reason_for_deactivation"';
            echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
        ?>
    <span id="reason_for_deactivation_err"></span>
  
    <br><br>
    <input id="other_reason_for_deactivation" name="other_reason_for_deactivation" type="text" value="" size="50" style="display:none;">
  <span id="other_reason_for_deactivation_err"></span>
  <br><br>
  
  Are you sure you want to deactivate this company?  
  <br>
  <span class="required_i red">*Required Field</span>
  
  <div class="pull-right">
      <button class="btn btn-primary" type="button" onclick="validate_company_deactivate_form();">Yes</button>&nbsp;&nbsp;<a href="<?php echo base_url(); ?>company/edit_company"><button class="btn btn-primary" type="button">No</button></a></div></p>
</div>
</div>
<?php echo form_close(); ?>

<?php } ?>

<script src="<?php echo base_url(); ?>assets/js/deactivatecompany.js" type="text/javascript" charset="utf-8"></script>