<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
?>
<?php
if ($this->session->flashdata('error_message')) {
    echo '<div class="error1">' . $this->session->flashdata('error_message') . '</div>';
}
$atr = 'id="creditForm" name="validate_form" onsubmit="return validate();"';
echo form_open_multipart("accounting/insert_credit_notes", $atr);
?>  
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/add_credit_notes.js"></script>
<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?>        
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Credit Notes</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Credit Note Details</h2>

    <div class="table-responsive credit_note_div">
        <table class="table table-striped">    
            <tbody>                
                <tr>
                    <td class="td_heading">Credit Note #:<span class="required">*</span></td>
                    <td width="29%">
                        <?php
                        $attr = array(
                            'name' => 'credit_note_number',
                            'id' => 'credit_note_number',
                            'maxlength' => '50',                            
                            'class' => 'upper_case',
                            'style' => 'width:200px',
                            'onblur' => 'javascript:isunique_credit_number(this.value,this.id);'
                        );
                        echo form_input($attr);
                        ?> 
                        <br/>
                        <span id="credit_note_number_err"></span>                
                    </td>
                    <td width="20%" class="td_heading">Credit Note Date:<span class="required">*</span></td>
                    <td width="30%">
                        <?php
                        $attr = array(
                            'name' => 'credit_note_date',
                            'id' => 'credit_note_date',                                                                                                                
                            'readonly' => 'readonly',
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($attr);
                        ?>   
                        <span id="credit_note_date_err"></span>                
                    </td>
                </tr>        
                <tr>
                    <td class="td_heading">Original Invoice #:<span class="required">*</span></td>
                    <td>
                         <?php
                        $attr = array(
                            'name' => 'ori_invoice_number',
                            'id' => 'ori_invoice_number',                                                        
                            'class' => 'upper_case',
                            'maxlength' => '50',
                            'style' => 'width:200px',                            
                        );
                        echo form_input($attr);
                        ?>   
                        <span id="ori_invoice_number_err"></span>                   
                    </td>
                    <td class="td_heading">Original Invoice Date:<span class="required">*</span></td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'ori_invoice_date',
                            'id' => 'ori_invoice_date',                            
                            'readonly' => 'readonly',
                            'placeholder' => 'dd-mm-yyyy'
                        );
                        echo form_input($attr);
                        ?> 
                        <span id="ori_invoice_date_err"></span>                
                    </td>
                </tr>          
                <tr>          
                    <td class="td_heading">Credit Note Amount:<span class="required">*</span></td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'credit_note_amount',
                            'id' => 'credit_note_amount',                                                        
                            'class' => 'float_number',
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?> 
                        <span id="credit_note_amount_err"></span>                
                    </td>
                    <td class="td_heading">Credit Note Issued By:</td>
                    <td>
                        <?php
                        $attr = array(
                            'name' => 'credit_note_issued_by',
                            'id' => 'credit_note_issued_by',
                            'maxlength' => '100', 
                            'class' => 'upper_case',
                            'style' => 'width:200px',
                        );
                        echo form_input($attr);
                        ?>    
                        <span id="credit_note_issued_by_err"></span>                
                    </td>
                </tr>                                       
                <tr>        
                    <td class="td_heading">Credit Note Issued Reason:</td>
                    <td colspan="3" class="textarea_inp">
                        <?php
                        $attr = array(
                            'name' => 'credit_note_issue_reason',
                            'id' => 'credit_note_issue_reason',
                            'rows' => '2',
                            'cols' => '90',
                            'maxlength' => '500',                            
                            'class' => 'upper_case',
                            'style' => 'width:650px',
                        );
                        echo form_textarea($attr);
                        ?>             
                    </td>      
                </tr>
                <tr>        
                    <td class="td_heading">TG Ref #:</td>
                    <td colspan="3" class="textarea_inp">
                        <?php
                        $attr = array(
                            'name' => 'tg_ref_number',
                            'id' => 'tg_ref_number',
                            'rows' => '2',
                            'cols' => '90',
                            'maxlength' => '750',                            
                            'class' => 'upper_case',
                            'style' => 'width:650px',
                        );
                        echo form_textarea($attr);
                        ?>             
                    </td>      
                </tr>
            </tbody>
        </table>
        <span class="required required_i">* Required Fields</span>
    </div>            
    <br>
    <div class="button_class99">
        <a class="small_text white_color">
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button>
        </a> &nbsp; &nbsp;
        <a class="small_text white_color" href="<?php echo base_url().'accounting/credit_note'?>">
            <span class="btn btn-primary">
                <span class="glyphicon glyphicon-saved"></span>&nbsp;Cancel
            </span>
        </a> &nbsp; &nbsp; 
    </div>    
</div>
<?php echo form_close(); ?>
