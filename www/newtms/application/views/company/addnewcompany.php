<script>
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error_text').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').text('');
    }
    $(document).ready(function() {
        $('.payment_scroll input').change(function() {
            return comp_disc_validate(false);
        });
        $('.disc_save').click(function() {
            return comp_disc_validate(true);
        });
        function comp_disc_validate($retval) {
            $('.comp_disc_perc').each(function(i) {
                $this = $(this);
                $key = $this.data('key');
                $val = $this.val().trim();
                if (isNaN($val) == true && $val != '') {
                    disp_err('#comp_disc_perc' + $key, '[invalid]');
                    $retval = false;
                } else if ($val.length > 0 && (parseFloat($val) >= 100 || parseFloat($val) < 0)) {
                    disp_err('#comp_disc_perc_' + $key, '[invalid]');
                    $retval = false;
                } else {
                    remove_err('#comp_disc_perc_' + $key);
                }
            });
            return $retval;
        }
        $('.comp_disc_amt,.comp_disc_perc').keydown(function(e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        }); 
    });
</script>
<?php 
if (!empty($tax_error)) { 
    echo '<div class="error1">' . $tax_error . '</div>';
}
?>
<div class="col-md-10">
    <?php
    $atr = 'id="companyForm" name="companyForm" '; 
    ?>
    <?php echo form_open("company/add_new_company", $atr); ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company.png" /> Company - Add New</h2>
    
    <h2 class="sub_panel_heading_style">
        <img src="<?php echo base_url(); ?>/assets/images/company-detail.png" /> Company Details
        <span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex3" rel="modal:open" style="color: blue;">Company Discount</a></span>
    </h2>
    <div class="bs-example">
        <div class="table-responsive" id="company_details">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td width="9%" class="td_heading">Name:<span class="required">*</span></td>
                        <?php
                        $fn = array(
                            'name' => 'company_name',
                            'id' => 'companyname',
                            'value' => $this->input->post('company_name'),
                            'maxlength' => '250',
                            'class' => "upper_case",
                            'onblur' => 'this.value=this.value.toUpperCase();',
                            'style' => "width:700px",
                        );
                        ?>
                        <td width="90%" colspan='5'>
                            <?php echo form_input($fn); ?>
                            <?php echo form_error('company_name', '<div class="error">', '</div>'); ?>                        
                        </td>
                    </tr>
                    <tr>
                        <td width="18%" class="td_heading">Registration No.:<span class="required">*</span></td>
                        <td width="26%" id="td_regno" colspan='2' >
                            <?php
                            $fn = array(
                                'name' => 'regno',
                                'id' => 'regno',
                                'value' => $this->input->post('regno'),
                                'maxlength' => '50',
                                'class' => "upper_case",
                                'onblur' => "this.value=this.value.toUpperCase(); check_registration_number(this.value, 'td_regno');",
                                'style' => "width:200px",
                            );
                            echo form_input($fn);
                            ?>
                            <?php echo form_error('regno', '<div class="error">', '</div>'); ?> 
                        </td>
                        <td width="14%" class="td_heading">Registration Type:<span class="required">*</span></td>
                        <td colspan='2'>
                            <?php
                            $btype = fetch_metavalues_by_category_id(Meta_Values::BUSINESS_TYPE);
                            $btype_options[''] = 'Select';
                            foreach ($btype as $item):
                                $btype_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $js = 'id="btype"';
                            echo form_dropdown('business_type', $btype_options, $this->input->post('business_type'), $js);
                            ?>
                            <?php echo form_error('business_type', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr> 
                    <tr>
                        <td class="td_heading">Size:<?php if($tenant_id != 'T25') { ?><span class="required">*</span><?php } ?></td>
                        <td>
                            <?php
                            $business = fetch_metavalues_by_category_id(Meta_Values::BUSINESS_SIZE);
                            $business_options[''] = 'Select';
                            foreach ($business as $item):
                                $business_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $js = 'id="business_size"';
                            echo form_dropdown('business_s', $business_options, $this->input->post('business_s'), $js);
                            ?>
                            <?php echo form_error('business_s', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading" width="14%">Phone Number:<span class="required">*</span></td>
                        <?php
                        $fn = array(
                            'name' => 'phoneno',
                            'id' => 'phoneno',
                            'value' => $this->input->post('phoneno'),
                            'maxlength' => '50',
                        );
                        ?>
                        <td width="22%">
                            <?php echo form_input($fn); ?>
                            <?php echo form_error('phoneno', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Fax Number:</td>
                        <?php
                        $fn = array(
                            'name' => 'faxno',
                            'id' => 'faxno',
                            'value' => $this->input->post('faxno'),
                            'maxlength' => '50',
                            "class" => 'upper_case'
                        );
                        ?>
                        <td>
                            <?php echo form_input($fn); ?>
                            <?php echo form_error('faxno', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Country:<span class="required">*</span></td>
                        <td colspan="2">   
                            <?php
                            $countries = fetch_metavalues_by_category_id(Meta_Values::COUNTRIES);
                            $country_options[''] = 'Select';
                            foreach ($countries as $item):
                                $country_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $js = 'id="country_of_residence"';
                            echo form_dropdown('country_of_residence', $country_options, $this->input->post('country_of_residence'), $js);
                            ?>
                            <?php echo form_error('country_of_residence', '<div class="error">', '</div>'); ?>
                            <?php
                            $SGP_scn_style = 'none';
                            if ($this->input->post('country_of_residence') == 'SGP') {
                                $SGP_scn_style = 'inline';
                            }
                            ?>                            
                            <div id="SGP" style="float:middle; width:60%;display:<?php echo $SGP_scn_style; ?>;">
                                <strong>SCN:</strong><span class="required">*</span>
                                <input type="radio" name="comp_scn" checked="checked" value="Yes"> Yes&nbsp;&nbsp;<input type="radio" name="comp_scn" value="No" <?php if ($this->input->post('comp_scn') == 'No') echo 'checked="checked"'; ?> > No
                            </div>                            
                        </td>
                <span id="ind_pan1"></span>
                <td class="td_heading">SME Type:</td>
                <td colspan="2">
                    <?php
                    $sme = array(
                        'name' => 'sme_type',
                        'id' => 'sme_type',
                        'class' => 'sme_type',
                        'checked' => TRUE,
                    );
                    $non_sme = array(
                        'name' => 'sme_type',
                        'id' => 'sme_type',
                        'class' => 'sme_type',
                    );
                    echo form_radio($sme, 'SME', set_radio('sme_type', 'SME'));
                    ?>
                    SME &nbsp;&nbsp; <?php echo form_radio($non_sme, 'NONSME', set_radio('sme_type', 'NONSME')); ?> NON-SME</td>

                </tr>
                <tr>
                    <td class="td_heading">Company Attn.:<span class="required">*</span></td>
                    <td colspan='2'>
                        <?php
                        $fn = array(
                            'name' => 'comp_attn',
                            'id' => 'comp_attn',
                            'value' => $this->input->post('comp_attn'),
                            'maxlength' => '250',
                            'style' => "width:200px",
                            "class" => 'upper_case'
                        );
                        echo form_input($fn);
                        echo form_error('comp_attn', '<div class="error">', '</div>');
                        ?>
                    </td>
                    <td class="td_heading">Company Email:<span class="required">*</span></td>
                    <td colspan="2">
                        <?php
                        $fn = array(
                            'name' => 'comp_email',
                            'id' => 'comp_email',
                            'value' => $this->input->post('comp_email'),
                            'maxlength' => '50',
                            'style' => "width:200px",
                        );
                        echo form_input($fn);
                        echo form_error('comp_email', '<div class="error">', '</div>');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Comments / Special Instructions:</td>
                    <?php
                    $fn = array(
                        'name' => 'comments',
                        'rows' => '2',
                        'cols' => '145',
                        'maxlength' => '250',
                        'class' => "upper_case",
                        'onblur' => 'this.value=this.value.toUpperCase();',
                        value => $this->input->post('comments')
                    );
                    ?>
                    <td colspan="5"><?php echo form_textarea($fn); ?></td>          
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/address.png"> Address</h2>
        <div class="table-responsive" id="company_address">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Street / Building:</td>
                        <?php
                        $fn = array(
                            'name' => 'street',
                            'id' => 'street',
                            'value' => $this->input->post('street'),
                            'maxlength' => '200',
                            'size' => '50',
                            'class' => "upper_case",
                            'onblur' => 'this.value=this.value.toUpperCase();',
                        );
                        ?>                        
                        <td>
                            <?php echo form_input($fn); ?>
                            <?php echo form_error('street', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">City:</td>
                        <?php
                        $fn = array(
                            'name' => 'city',
                            'id' => 'city',
                            'value' => $this->input->post('city'),
                            'maxlength' => '50',
                            'size' => '50',
                            'class' => "upper_case",
                            'onblur' => 'this.value=this.value.toUpperCase();',
                        );
                        ?>
                        <td>
                            <?php echo form_input($fn); ?>
                            <?php echo form_error('city', '<div class="error">', '</div>'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Country:</td>
                        <td>  
                            <?php
                            foreach ($countries as $item):
                                $country_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $addr = 'id="addr_country"';
                            echo form_dropdown('company_country', $country_options, $this->input->post('company_country'), $addr);
                            ?>
                            <?php echo form_error('company_country', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading" >State:</td>
                        <td id="append_company_states">
                            <?php
                            if ($this->input->post('company_country') != "") {
                                $attr = array('' => 'Select');
                                $country_param = $this->input->post('company_country');
                                if ($country_param != '') {
                                    $states = $this->internaluser->get_states($country_param);
                                    foreach ($states as $item) {
                                        $attr[$item->parameter_id] = $item->category_name;
                                    }
                                }
                                $attr_js = 'id="pers_states"';
                                echo form_dropdown('pers_states', $attr, $this->input->post(pers_states), $attr_js);
                            } else {
                                ?>
                                <select name="pers_states" id="pers_states">
                                    <option value="">Select</option>
                                </select>
                            <?php } ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Postal Code:</td>
                        <?php
                        $fn = array(
                            'name' => 'zipcode',
                            'id' => 'zipcode',
                            'value' => $this->input->post('zipcode'),
                            'maxlength' => '10',
                            'class' => 'upper_case',
                            'onblur' => 'this.value=this.value.toUpperCase();',
                        );
                        ?>
                        <td colspan="5"><?php echo form_input($fn); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/contact.png"> Contact Details  
            <div class="a_class">
                <span class="label label-default push_right black-btn" id="addMore"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span>                 
            </div>
        </h2>

        <div class="multi-field-wrapper">
            <div class="multi-fields">
                <div class="table-responsive multi-field">
                    <div id="contact_details">
                        <?php if (!$this->input->post('fname')) { ?>                        
                            <table class="table table-striped" id="contact_tbl">
                                <tbody>
                                    <tr>
                                        <td width="15%" class="td_heading">Primary Contact Name:<span class="required">*</span></td>
                                        <?php
                                        $fn = array(
                                            'name' => 'fname[]',
                                            'id' => 'fname_0',
                                            'class' => "upper_case",
                                            'onblur' => 'this.value=this.value.toUpperCase();',
                                            'maxlength' => '100',
                                             'style' => "width:250px",
                                        );
                                        ?>

                                        <td colspan="5">
                                            <?php echo form_input($fn); ?>
                                            <span id="fname_0_err"></span>
                                        </td>
                                        <td  rowspan="3" valign="middle">
                                            <img src="<?php echo base_url(); ?>assets/images/remove-red.png" id="remInput" title="Remove" style="display: none;" onclick="$(this).parent().parent().parent().parent().remove();
                                                        removeUserContact();" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="td_heading">Gender:</td>
                                        <td >
                                            <?php
                                            $gender = fetch_metavalues_by_category_id(Meta_Values::GENDER);
                                            $gender_options = array();
                                            $gender_options[''] = 'Select';
                                            foreach ($gender as $item):
                                                $gender_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;
                                            echo form_dropdown('gender[]', $gender_options, set_value('gender'), ' id=gender_0');
                                            ?>
                                            <span id="gender_0_err"></span>
                                        </td>
                                        <td class="td_heading">Contact Number [O]:<span class="required">*</span></td>
                                        <?php
                                        $fn = array(
                                            'name' => 'contactno[]',
                                            'id' => 'contactno_0',
                                            'maxlength' => '50',
                                        );
                                        ?>
                                        <td>
                                            <?php echo form_input($fn); ?>
                                            <span id="contactno_0_err"></span>
                                        </td>
                                        <td class="td_heading">Mobile Number [O]:</td>
                                        <?php
                                        $fn = array(
                                            'name' => 'mobileno[]',
                                            'id' => 'mobileno_0',
                                            'maxlength' => '50',
                                        );
                                        ?>                                    
                                        <td>
                                            <?php echo form_input($fn); ?>
                                            <span id="mobileno_0_err"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="td_heading">Email Id 01 (O):<span class="required">*</span></td>                                       
                                        <td id="td_email_01_0" colspan="2">
                                            <?php
                                            $fn = array(
                                                'name' => 'email_01[]',
                                                'id' => 'email_01_0',
                                                'class' => 'contact_reg_email',
                                                'maxlength' => '50',
                                                 'style' => "width:250px",
                                            );
                                            echo form_input($fn);
                                            ?>
                                        </td>
                                        <td class="td_heading">Email Id 02:</td>
                                        <?php
                                        $fn = array(
                                            'name' => 'email_02[]',
                                            'id' => 'email_02_0',
                                            'maxlength' => '50',
                                             'style' => "width:250px",
                                        );
                                        ?>                                    
                                        <td colspan="2">
                                            <?php echo form_input($fn); ?>
                                            <span id="email_02_0_err"></span>
                                        </td>
                                   </tr>
                                   <tr>
                                        <td class="td_heading">Username:<span class="required">*</span></td>                                         
                                        <td colspan="5" id="td_username_0">
                                            <?php
                                            $fn = array(
                                                'name' => 'username[]',
                                                'id' => 'username_0',
                                                'onblur' => "javascript:check_username(this.value, 'td_username_0');",
                                                'maxlength' => '15',
                                                 'style' => "width:250px",
                                            );
                                            echo form_input($fn);
                                            ?>
                                        </td>
                                   </tr>
                                    <tr>
                                        <td colspan="7">&nbsp;&nbsp;&nbsp;</td>
                                    </tr>                                
                                </tbody>
                            </table>   
                        <?php } else { ?>
                            <?php for ($i = 0; $i < count($fname); $i++) { ?>
                                <table class="table table-striped" id="contact_tbl">
                                    <tbody>
                                        <tr>
                                            <td width="15%" class="td_heading">Name:<span class="required">*</span></td>
                                            <?php
                                            $fn = array(
                                                'name' => 'fname[]',
                                                'id' => 'fname_' . $i,
                                                'class' => 'upper_case',
                                                'value' => $fname[$i],
                                                'onblur' => 'this.value=this.value.toUpperCase();',
                                                 'style' => "width:250px",
                                            );
                                            ?>
                                            <td colspan="5">
                                                <?php echo form_input($fn); ?>
                                                <?php echo form_error('fname[' . $i . ']', '<div class="error">', '</div>'); ?>
                                                <span id="fname_<?php echo $i; ?>_err"></span>
                                            </td>
                                            <td  rowspan="3" valign="middle">
                                                <img src="<?php echo base_url(); ?>assets/images/remove-red.png" id="remInput" title="Remove" onclick="$(this).parent().parent().parent().parent().remove();
                                                                removeUserContact();" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="15%" class="td_heading">Gender:</td>
                                            <td width="20%">
                                                <?php
                                                $gender_meta = fetch_metavalues_by_category_id(Meta_Values::GENDER);
                                                $gender_options = array();
                                                $gender_options[''] = 'Select';
                                                foreach ($gender_meta as $item):
                                                    $gender_options[$item['parameter_id']] = $item['category_name'];
                                                endforeach;
                                                echo form_dropdown('gender[]', $gender_options, $gender[$i], ' id=gender_' . $i);
                                                ?>
                                                <span id="gender_<?php echo $i; ?>_err"></span>
                                            </td>
                                            <td class="td_heading">Contact Number [O]:<span class="required">*</span></td>
                                            <?php
                                            $fn = array(
                                                'name' => 'contactno[]',
                                                'id' => 'contactno_' . $i,
                                                'value' => $contactno[$i]
                                            );
                                            ?>
                                            <td>
                                                <?php echo form_input($fn); ?>
                                                <?php echo form_error('contactno[' . $i . ']', '<div class="error">', '</div>'); ?>
                                                <span id="contactno_<?php echo $i; ?>_err"></span>
                                            </td>
                                            <td class="td_heading">Mobile Number [O]:</td>
                                            <?php
                                            $fn = array(
                                                'name' => 'mobileno[]',
                                                'id' => 'mobileno_' . $i,
                                                'value' => $mobileno[$i]
                                            );
                                            ?>                                    
                                            <td>
                                                <?php echo form_input($fn); ?>
                                                <span id="mobileno_<?php echo $i; ?>_err"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="td_heading">Email Id 01 (O):<span class="required">*</span></td>                                        
                                            <td colspan="2" id="td_email_01_<?php echo $i; ?>">
                                                <?php
                                                $fn = array(
                                                    'name' => 'email_01[]',
                                                    'id' => 'email_01_' . $i,
                                                    'class' => 'contact_reg_email',
                                                    'value' => $email_01[$i],
                                                     'style' => "width:250px",
                                                );
                                                echo form_input($fn);
                                                ?>
                                                <?php echo form_error('email_01[' . $i . ']', '<div class="error">', '</div>'); ?>
                                            </td>
                                            <td class="td_heading">Email Id 02:</td>
                                            <?php
                                            $fn = array(
                                                'name' => 'email_02[]',
                                                'id' => 'email_02_' . $i,
                                                'value' => $email_02[$i],
                                                 'style' => "width:250px",
                                            );
                                            ?>                                    
                                            <td colspan="2">
                                                <?php echo form_input($fn); ?>
                                                <span id="email_02_<?php echo $i; ?>_err"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="td_heading">Username:<span class="required">*</span></td>
                                            <td colspan="5" id="td_username_<?php echo $i; ?>">
                                                <?php
                                                $fn = array(
                                                    'name' => 'username[]',
                                                    'id' => 'username_' . $i,
                                                    'value' => $username[$i],
                                                    'onblur' => "javascript:check_username(this.value, 'td_username_'.$i);",
                                                     'style' => "width:250px",
                                                );
                                                echo form_input($fn);
                                                ?>
                                                <?php echo form_error('username[' . $i . ']', '<div class="error">', '</div>'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">&nbsp;&nbsp;&nbsp;</td>
                                        </tr>                                
                                    </tbody>
                                </table>
                            <?php } ?>
                        <?php } ?>                        
                    </div>
                    <div>
                        <table>
                            <tr>
                                <td colspan="7">
                                    <?php
                                    $now = array(
                                        'name' => 'activate_company',
                                        'checked' => TRUE,
                                        'value' => 'ACTIVE'
                                    );
                                    $later = array(
                                        'name' => 'activate_company',
                                        'value' => 'PENDACT'
                                    );
                                    ?>
                                    <?php echo form_radio($now); ?>Activate Company Now &nbsp;&nbsp;
                                    <?php echo form_radio($later); ?> Activate Later
                            </tr>
                            <tr>
                                <td colspan="7"><i>(On company activation, mail will be sent to the user’s official email Id, with the username.)</i></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <span class="required required_i">* Required Fields</span>
        </div>

        <div class="button_class99">
            <button class="btn btn-primary" type="submit" name="submit_button" id="submit_button"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button></a> &nbsp; &nbsp;
        </div>

    </div>
    <?php if (empty($courses)) { ?>
        <div class="modal0000" id="ex3" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Alert Message</h2>
            There are no courses available.<br>
            <div class="popup_cancel popup_cancel001">
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
            </p>
        </div>
    <?php } else { ?>
        <div class="modal_333" id="ex3" style="display:none;">
            <p>
            <h2 class="panel_heading_style">Company Discount by Course</h2>
            <div class="table-responsive payment_scroll" style="height: 250px;">
                <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                    <thead>
                        <tr>
                            <th width="54%">Course</th>
                            <th width="23%">Discount %</th>
                            <th width="23%">Discount Amt. (SGD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($courses as $k => $row):
                            echo "<tr>
                            <td>" . $row . "</td>
                            <td><input type='text' maxlength='10' id='comp_disc_perc_" . $k . "' class='comp_disc_perc' data-key='" . $k . "' value='' name='comp_disc_perc[" . $k . "]'>% <span id='comp_disc_perc_" . $k . "_err'></span></td>
                            <td>$ <input type='text' maxlength='10' id='comp_disc_amt_" . $k . "' class='comp_disc_amt' data-key='" . $k . "' value='' name='comp_disc_amt[" . $k . "]'> <span id='comp_disc_amt_" . $k . "_err'></span></td>
                            </tr>";
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="popup_cance89">
                <a rel="modal:close" href="#"><button type="button" class="btn btn-primary disc_save">Save</button></a>
            </div>
            </p>
        </div>
    <?php } ?>
    <?php echo form_close(); ?>
</div>

<div id="contact_details_data" style="display:none;">
    <table class="table table-striped">
        <tbody>
            <tr>
                <td width="15%" class="td_heading">Name:<span class="required">*</span></td>
                <?php
                $fn = array(
                    'name' => 'fname[]',
                    'id' => 'fname_',
                    'class' => 'upper_case',
                    'maxlength'=>'100',
                    'onblur' => 'this.value=this.value.toUpperCase();',
                    'style' => "width:250px",
                );
                ?>                           
                <td colspan="5">
                    <?php echo form_input($fn); ?>
                    <span id="fname_err"></span>
                </td>                
                <td rowspan="3" valign="middle">
                    <img  src="<?php echo base_url(); ?>assets/images/remove-red.png" id="remInput" title="Remove" onclick="$(this).parent().parent().parent().parent().remove();
                            removeUserContact();" />
                </td>
               
            </tr>
            <tr>
                 <td width="15%" class="td_heading">Gender:</td>
                <td >
                    <?php
                    $gender = fetch_metavalues_by_category_id(Meta_Values::GENDER);
                    $gender_options = array();
                    $gender_options[''] = 'Select';
                    foreach ($gender as $item):
                        $gender_options[$item['parameter_id']] = $item['category_name'];
                    endforeach;
                    echo form_dropdown('gender[]', $gender_options, set_value('gender'), 'id=gender_');
                    ?>
                    <span id="gender_err"></span>  
                </td>
                
                <td class="td_heading">Contact Number [O]:<span class="required">*</span></td>
                <?php
                $fn = array(
                    'name' => 'contactno[]',
                    'id' => 'contactno_',
                );
                ?>

                <td>
                    <?php echo form_input($fn); ?>
                    <span id="contactno_err"></span>
                </td>

                <td class="td_heading">Mobile Number [O]:</td>
                <?php
                $fn = array(
                    'name' => 'mobileno[]',
                    'id' => 'mobileno_',
                );
                ?>                  
                <td>
                    <?php echo form_input($fn); ?>
                    <span id="mobileno_err"></span>
                </td>

            </tr>
            <tr>
                <td class="td_heading">Email Id 01 (O):<span class="required">*</span></td>                       
                <td id="td_email_01_" colspan="2">
                    <?php
                    $fn = array(
                        'name' => 'email_01[]',
                        'id' => 'email_01_',
                        'class' => 'contact_reg_email',
                         'style' => "width:250px",
                    );
                    echo form_input($fn);
                    ?>
                </td>
                <td class="td_heading">Email Id 02:</td>
                <?php
                $fn = array(
                    'name' => 'email_02[]',
                    'id' => 'email_02_',
                    'style' => "width:250px",
                );
                ?>                  
                <td colspan="2">
                    <?php echo form_input($fn); ?>
                    <span id="email_02_err"></span>
                </td>
             </tr>
             <tr>
                <td class="td_heading">Username:<span class="required">*</span></td>                       
                <td id="td_username_" colspan="5">
                    <?php
                    $fn = array(
                        'name' => 'username[]',
                        'id' => 'username_',
                        'onblur' => "javascript:check_username(this.value, 'td_username_');",
                        'style' => "width:250px",
                    );
                    echo form_input($fn);
                    ?>
                    <span id="username_err"></span>
                </td>
             </tr>
            <tr>
                <td colspan="7">&nbsp;&nbsp;&nbsp;</td>
            </tr>                                
        </tbody>
    </table>                        
</div>

<div class="modal0000" id="min_one_contact_req" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    At least one contact is required!
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>

<script src="<?php echo base_url(); ?>assets/js/addnewcompany.js" type="text/javascript" charset="utf-8"></script>


