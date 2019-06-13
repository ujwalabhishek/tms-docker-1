<script src="<?php echo base_url() ?>/assets/js/addnewtenant.js" type="text/javascript"></script>
<div class="col-md-10">
    <?php
    $atr = 'id="tenant_form" name="tenant_form"';
    echo form_open_multipart($controllerurl . "add_new_tenant", $atr);
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/trainee.png"> Tenant - Add New</h2>		  
    <div class="bs-example">
        <div class="table-responsive">          
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td class="td_heading" >Tenant Name:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'tenant_name',
                                'id' => 'tenant_name',
                                'maxlength' => '50',
                                'value' => $this->input->post('tenant_name'),
                                'class' => 'upper_case',
                                'autocomplete' => "off",
                                'style' => 'width:250px',
                            );
                            echo form_input($data);
                            ?>  
                            <span id="tenant_name_err">
                                <?php echo form_error('tenant_name', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>   
                        <td class="td_heading" >Address:<span class="required">*</span></td>
                        <td colspan="3">
                            <?php
                            $data = array(
                                'name' => 'address',
                                'id' => 'address',
                                'maxlength' => '255',
                                'value' => $this->input->post('address'),
                                'rows' => '1',
                                'cols' => '70',
                                'class' => 'upper_case'
                            );
                            echo form_textarea($data);
                            ?>
                            <span id="address_err">
                                <?php echo form_error('address', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading" >Email Id:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'email',
                                'id' => 'email',
                                'maxlength' => '50',
                                'value' => $this->input->post('email'),
                                'style' => 'width:250px',
                            );
                            echo form_input($data);
                            ?>
                            <span id="email_err">
                                <?php echo form_error('email', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>  
                        <td class="td_heading" >Upload Logo:<span class="required">*</span></td>          
                        <td>
                            <input type="file" name="userfile" id="userfile" onchange="showimagepreview(this)" />
                            <label id="image_err">
                                <?php echo form_error('userfile', '<span class="error">', '</span>'); ?>
                            </label>                
                        </td>
                        <td colspan="2" class="td_heading">
                            <div  id="user_image_preview">
                                &nbsp;&nbsp;&nbsp;
                                <img height="120px" width="120px" id="imgprvw" border="0" />
                                &nbsp;&nbsp;&nbsp;
                                <span style="display: none;" id="removeimagebutton" onclick="remove_image();" class="glyphicon glyphicon-remove-circle"></span>                
                            </div>
                        </td> 
                    </tr>
                    <tr>                           
                        <td class="td_heading" >City:</td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'city',
                                'id' => 'city',
                                'maxlength' => '50',
                                'value' => $this->input->post('city'),
                                'class' => 'upper_case',
                                'autocomplete' => "off",
                            );
                            echo form_input($data);
                            ?>  
                            
                        </td> 
                        <td class="td_heading" >Country:<span class="required">*</span></td>
                        <td>
                            <?php
                            $country_options = array();
                            $country_options[''] = 'Select';
                            foreach ($countries as $item):
                                $country_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            $attr = 'id="country"';
                            echo form_dropdown('country', $country_options, $this->input->post('country'), $attr);
                            ?>
                            <span id="country_err">
                                <?php echo form_error('country', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>    
                        <td class="td_heading" >State:</td>
                        <td>
                            <?php
                            $state_options = array();
                            $state_options[''] = 'Select';
                            foreach ($states as $item):
                                $state_options[$item->parameter_id] = $item->category_name;
                            endforeach;
                            $attr = 'id="state"';
                            echo form_dropdown('state', $state_options, $this->input->post('state'), $attr);
                            ?>
                            
                        </td> 
                    </tr>
                    <tr> 
                        <td class="td_heading" >Contact No.:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'contact_num',
                                'id' => 'contact_num',
                                'maxlength' => '50',
                                'value' => $this->input->post('contact_num'),
                            );
                            echo form_input($data);
                            ?>
                            <span id="contact_num_err">
                                <?php echo form_error('contact_num', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>  
                        <td class="td_heading" >Activation Start Date:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'acti_start_date',
                                'id' => 'acti_start_date',
                                'maxlength' => '50',
                                'readonly' => 'readonly',
                                'value' => $this->input->post('acti_start_date'),
                            );
                            echo form_input($data);
                            ?>
                            <span id="acti_start_date_err">
                                <?php echo form_error('acti_start_date', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>
                        <td class="td_heading" >Activation End Date:</td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'acti_end_date',
                                'id' => 'acti_end_date',
                                'maxlength' => '50',
                                'readonly' => 'readonly',
                                'value' => $this->input->post('acti_end_date'),
                            );
                            echo form_input($data);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading" >Invoice Name:<span class="required">*</span></td>
                        <td>
                            <?php
                            $attr = ' id="inv_name"';
                            echo form_dropdown('inv_name', $inv_names, $this->input->post('inv_name'), $attr);
                            ?>
                            <span id="inv_name_err">
                                <?php echo form_error('inv_name', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>  
                        <td class="td_heading" >Currency:<span class="required">*</span></td>
                        <td>
                            <?php
                            $currency_options = array();
                            $currency_options[''] = 'Select';
                            foreach ($currencies as $item):
                                $currency_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;
                            $attr = ' id="currency"';
                            echo form_dropdown('currency', $currency_options, $this->input->post('currency'), $attr);
                            ?>
                            <span id="currency_err">
                                <?php echo form_error('currency', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>                    
                        <td class="td_heading" >Country of Use:<span class="required">*</span></td>
                        <td>
                            <?php
                            $attr = ' id="country_use"';
                            echo form_dropdown('country_use', $country_options, $this->input->post('country_use'), $attr);
                            ?>
                            <span id="country_use_err">
                                <?php echo form_error('country_use', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>      
                    </tr>
                    <tr>
                        <td class="td_heading" >Website URL:</td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'website',
                                'id' => 'website',
                                'maxlength' => '50',
                                'value' => $this->input->post('website'),
                            );
                            echo form_input($data);
                            ?>
                            <span id="website_err"></span>
                        </td>                    
                        <td class="td_heading" >Company Reg. No.:</td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'company_no',
                                'id' => 'company_no',
                                'maxlength' => '50',
                                'value' => $this->input->post('company_no'),
                                'class' => 'upper_case',
                            );
                            echo form_input($data);
                            ?>
                        </td>                    
                        <td class="td_heading" >GST Reg. No.:</td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'gst_no',
                                'id' => 'gst_no',
                                'maxlength' => '50',
                                'value' => $this->input->post('gst_no'),
                                'class' => 'upper_case',
                            );
                            echo form_input($data);
                            ?>
                        </td>                    
                    </tr>
                    <tr>
                        <td class="td_heading" >Paypal Email:</td>
                        <td>
                            <?php
                            $data = array(
                                'name' => 'paypal_email',
                                'id' => 'paypal_email',
                                'maxlength' => '50',
                                'value' => $this->input->post('paypal_email'),
                                'style' => 'width:250px',
                            );
                            echo form_input($data);
                            ?>
                            <span id="paypal_email_err"></span>
                        </td>
                        <td class="td_heading" >Copyright Text:<span class="required">*</span></td>
                        <td colspan="3">
                            <?php
                            $data = array(
                                'name' => 'copyright',
                                'id' => 'copyright',
                                'maxlength' => '255',
                                'value' => $this->input->post('copyright'),
                                'rows' => '1',
                                'cols' => '70',
                            );
                            echo form_textarea($data);
                            ?>
                            <span id="copyright_err">
                                <?php echo form_error('copyright', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>                        
                        <td class="td_heading">Director Name:<span class="required">*</span></td>
                        <td>
                           <?php
                            $data = array(
                                'name' => 'director_name',
                                'id' => 'director_name',
                                'maxlength' => '100',
                                'value' => $this->input->post('director_name'),
                                'style' => 'width:250px',
                                'class' => 'upper_case'
                            );
                            echo form_input($data);
                            ?>
                            <span id="director_name_err"></span>
                        </td>    
                        <td class="td_heading" >Contact Name:<span class="required">*</span></td>
                        <td colspan="3">
                           <?php
                            $data = array(
                                'name' => 'contact_name',
                                'id' => 'contact_name',
                                'maxlength' => '100',
                                'value' => $this->input->post('contact_name'),
                                'style' => 'width:250px',
                                'class' => 'upper_case'
                            );
                            echo form_input($data);
                            ?>
                            <span id="contact_name_err"></span>
                        </td>   
                    </tr>
                    <tr>
                        <td class="td_heading" >Invoice Footer Text:</td>
                        <td colspan="5">
                            <?php
                            $data = array(
                                'name' => 'inv_footer',
                                'id' => 'inv_footer',
                                'maxlength' => '255',
                                'value' => $this->input->post('inv_footer'),
                                'rows' => '4',
                                'cols' => '140',
                            );
                            echo form_textarea($data);
                            ?>
                        </td>                    
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="button_class">
        <div class="button_class99">
            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button> &nbsp; &nbsp;
        </div>
    </div>
    <?php echo form_close(); ?>
</div>