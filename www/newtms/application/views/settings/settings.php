<?php     $this->load->helper('common_helper');
 ?>
<div class="col-md-10">
    <h2 class="panel_heading_style">
        <img src="<?php echo base_url(); ?>assets/images/settings.png" width="15" height="15"> Settings
    </h2>
    <div class="col-md-4">
        <div class="bor">
            <div class="bs-example">
                <div class="table-responsive">
                    <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="40%">Logo:</td>
                            <td><img src="<?php echo base_url(); ?>logos/<?php echo $table_data->Logo; ?>" /></td>
                        </tr>
                    </tbody>
                    </table>
                <div style="clear:both;"></div>
                </div>
            </div>
        </div>
    <br/>
        <div class="bor-log">
            <div class="bs-example">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td class="td_heading">Training Institute:</td>
                            </tr>
                            <tr>
                                <td><img src="<?php echo base_url(); ?>assets/images/<?php echo $table_data->ApplicationName; ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                <div style="clear:both;"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-8">
        <div class="bor1">
            <div class="bs-example">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>  
                            <tr>
                                <td class="td_heading">Paypal Email Id:</td>
                                <td>                                    
                                    <input type='text' name='paypal_email_id' id='paypal_email_id' maxlength='100'  value='<?php echo $table_data->paypal_email_id; ?>' />
                                    <span id="paypal_email_id_err"></span>
                                    <img id='paypal_img' src="<?php echo base_url(); ?>assets/images/ajax-loader.gif" style='display:none'/>
                                    <span>
                                        <a href='#' id='change' onclick='change_paypal();'>Update</a>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="td_heading">Invoice Name:</td>
                                <td>                                                                        
                                    <?php                                        
                                        $invoice_name = array(''=>'Select', 'Tax Invoice'=>'Tax Invoice','Training Bill' => 'Training Bill');                                           
                                        $class_types_js = 'id="invoice_name"';               
                                        echo form_dropdown('invoice_name', $invoice_name, $table_data->invoice_name, $class_types_js);
                                      ?>
                                    <span id="invoice_name_err"></span>
                                    <img id='invoice_img' src="<?php echo base_url(); ?>assets/images/ajax-loader.gif" style='display:none'/>
                                    <span>
                                        <a href='#' id='invoice_change' onclick='change_invoice();'>Update</a>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="td_heading">Invoice Footer Text:</td>
                                <td>                                                                                                            
                                    <?php
                                        $attr = array(
                                        'name' => 'invoice_footer_text',
                                        'id' => 'invoice_footer_text',
                                        'rows' => '7',                                                                                
                                        'value' => $table_data->invoice_footer_text,
                                        'class' => 'invoice'
                                        );
                                        echo form_textarea($attr);
                                    ?> 
                                    <span id="invoice_footer_text_err"></span>
                                    <img id='invoice_footer_text_img' src="<?php echo base_url(); ?>assets/images/ajax-loader.gif" style='display:none'/>
                                    <span>
                                        <a href='#' id='invoice_footer_text_change' onclick='change_invoice_footer_text();'>Update</a>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="td_heading">Tenant Name:</td>
                                <td><?php echo $table_data->tenant_name; ?></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Tenant Address:</td>
                                <td><?php echo $table_data->tenant_address.' '.$table_data->tenant_city; ?></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Tenant Email Id:</td>
                                <td><?php echo $table_data->tenant_email_id; ?></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Tenant Contact Name:</td>
                                <td><?php echo $table_data->tenant_contact_num; ?></td>
                            </tr>
                            
                            <tr>
                                <td class="td_heading">Currency:</td>
                                <td><?php echo $table_data->Currency; ?></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Date Format:</td>
                                <td>DD/MM/YYYY  </td>
                            </tr>
                            <tr>
                                <td class="td_heading">Country:</td>
                                <td><?php echo ($table_data->Country)?get_catname_by_parm($table_data->Country):''; ?></td>
                            </tr>
                            <tr>
                                <td class="td_heading">Copyright Details:</td>
                                <td><?php echo $table_data->CopyRightText; ?></td>
                            </tr>
                        </tbody>
                    </table><br>

                <div style="clear:both;"></div>
                </div>
            </div>
        </div>
    </div>		  
		  
    <div style="clear:both;"></div>
    <br>
</div>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/settings.js"></script>