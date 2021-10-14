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
    ////added by shubhranshu for multiclick & validate input field
    function validate_search(){
        $compname=$('#edit_search_company_name').val();
        $compid=$('#edit_search_company_id').val();
        
        if($compname == '' || $compid == ''){
            disp_err('#edit_search_company_name', '[Select Company name from auto-complete]');
            return false;
        }else{
             remove_err('#edit_search_company_name');
             return true;
        }
    }
    $(document).ready(function() {
        $('.payment_scroll input').change(function() {
            return comp_disc_validate(false);
        });
        $('.disc_save').click(function() {
            return comp_disc_validate(true);
        });
        function comp_disc_validate($retval) {
            remove_err('#popup_error');
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
            if($retval == true){
                var disc_perc = [];
                var disc_amt = [];
                $('.comp_disc_perc').each(function(){
                    var same = $(this);
                    var key = same.data('key');
                   
                    if(same.val().trim() == '' || same.val().trim() == 0){
                        disc_perc[key] = '0.00';
                    } else {
                        disc_perc[key] = same.val();
                    }
                    disc_amt[key] = $('#comp_disc_amt_' + key).val();
                });
                $.ajax({
                    url: '<?php echo base_url(); ?>' + "company/update_companydiscount",
                    type: "post",
                    dataType: "json",
                    async: false,
                    data: {
                        disc_perc:disc_perc,
                        disc_amt:disc_amt,
                        companyid:$('#edit_search_company_id').val()
                    },
                    success: function(data) {
                       if(data == 0){
                            disp_err('#popup_error', '[Something went wrong. Please try again.]');
                            $retval = false;  
                        }
                    }
                });
            }
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
        
         //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        $('#search_form').on('submit',function() {
            form_search = 1;
            //alert("form click");
            var status=validate_search();
            if(status){
            var self = $(this),
            button = self.find('input[type="submit"],button'),
            submitValue = button.data('submit-value');
            button.attr('disabled','disabled').html('Please Wait..');
            return true;
           }else{
               return false;
           }
        }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
    });
    $tenant_id ='<?php echo $this->session->userdata('userDetails')->tenant_id; ?>';
</script>
<?php 
if (!empty($tax_error)) { 
    echo '<div class="error1">' . $tax_error . '</div>';
}
?>
<div class="col-md-10">
    <?php echo validation_errors('<div class="error1">', '</div>'); ?> 
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company.png" /> Company - Edit/ Deactivate</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form"';
        echo form_open("company/edit_company", $atr);
        ?>    
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Search by Company Name:</td>
                    <td>
                        <input size="100" type="text" name="edit_search_company_name" id="edit_search_company_name" value="<?php echo $this->input->post('edit_search_company_name'); ?>">              
                        <input type="hidden" name="edit_search_company_id" id="edit_search_company_id" value="<?php echo $this->input->post('edit_search_company_id'); ?>" />
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <div id="edit_search_company_name_err"></div>
                    </td>      
                    <td align="center">
                        <button type="submit" value="Search" title="Search" name="edit_company_search_button" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>

                <?php if ($no_company_found != '') { ?>
                    <tr><td colspan="3" align="center">
                            <?php echo '<div class="error1">' . $no_company_found . '!</div>'; ?>                   
                        </td></tr>            
                <?php } ?>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div><br>

    <?php
    
    if($tenant_id != 'T25') {
        $atr = 'id="companyEditForm" name="companyEditForm"  ' . $form_style_attr; 
    } else {
        $atr = 'id="companyFondaEditForm" name="companyFondaEditForm"  ' . $form_style_attr; 
    }            
    echo form_open("company/edit_company", $atr);
    ?>
    <?php ?>
    <?php
   
    if ($this->session->userdata('contact_deactivated_success') != '') {
        echo '<div class="success">' . $this->session->userdata('contact_deactivated_success') . '</div>';
        $this->session->set_userdata('contact_deactivated_success', '');
    }
    ?>    
    <input type="hidden" name="edit_search_company_id" id="edit_search_company_id" value="<?php echo $this->input->post('edit_search_company_id'); ?>" />
    <input type="hidden" name="edit_search_company_name" id="edit_search_company_name" value="<?php echo $this->input->post('edit_search_company_name'); ?>" />

    <input type="hidden" name="deactivate_user_id" id="deactivate_user_id" value="" />
    <input type="hidden" name="deactivate_reason" id="deactivate_reason" value="" />
    <input type="hidden" name="deactivate_other_reason" id="deactivate_other_reason" value="" />

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
                        <td width="24%" colspan="5">
                            <?php
                            $fn = array(
                                'name' => 'company_name',
                                'id' => 'companyname',
                                'value' => $company_info->company_name,
                                'maxlength' => '250',
                                'class' => "upper_case",
                                'onblur' => 'this.value=this.value.toUpperCase();',
                                'style' => "width:700px",
                            );
                            echo form_input($fn);
                            ?>
                            <?php echo form_error('company_name', '<div class="error">', '</div>'); ?>     
                            <span id="companyname_err"></span>
                        </td>
                    </tr>
                    <tr>
                      
                        <td width="14%" class="td_heading">Registration No.:<span class="required">*</span></td>
                        <?php
                        $fn = array(
                            'name' => 'regno',
                            'id' => 'regno',
                            'value' => $company_info->comp_regist_num,
                            'maxlength' => '50',
                            'class' => 'upper_case',
                            'onblur' => "this.value=this.value.toUpperCase(); check_registration_number(this.value, this.id, 'td_regno');",
                            'style' => "width:200px",
                        );
                        ?>
                        <td width="22%" id="td_regno">
                            <?php echo form_input($fn); ?>
                            <?php echo form_error('regno', '<div class="error">', '</div>'); ?>
                            <input type="hidden" name="regno_current" id="regno_current" value="<?php echo $company_info->comp_regist_num; ?>" />
                        </td>
                        <td width="14%" class="td_heading">Registration Type:<span class="required">*</span></td>
                        <td colspan='5'>
                            <?php
                            $btype = fetch_metavalues_by_category_id(Meta_Values::BUSINESS_TYPE);
                            $btype_options[''] = 'Select';
                            foreach ($btype as $item):
                                $btype_options[$item['parameter_id']] = $item['category_name'];
                            endforeach;

                            $js = 'id="btype"';
                            echo form_dropdown('business_type', $btype_options, $company_info->business_type, $js);
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
                            echo form_dropdown('business_s', $business_options, $company_info->business_size, $js);
                            ?>
                            <?php echo form_error('business_s', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading">Phone Number:<span class="required">*</span></td>
                        <?php
                        $fn = array(
                            'name' => 'phoneno',
                            'id' => 'phoneno',
                            'value' => $company_info->comp_phone,
                            'maxlength' => '50',
                        );
                        ?>
                        <?php echo form_error('phoneno', '<div class="error">', '</div>'); ?>
                        <td><?php echo form_input($fn); ?></td>
                        <td class="td_heading">Fax Number:</td>
                        <?php
                        $fn = array(
                            'name' => 'faxno',
                            'id' => 'faxno',
                            'value' => $company_info->comp_fax,
                            'maxlength' => '50',
                            "class" => "upper_case"
                        );
                        ?>
                        <td><?php echo form_input($fn); ?></td>
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
                            echo form_dropdown('country_of_residence', $country_options, $company_info->comp_cntry_scn, $js);
                            ?>
                            <?php echo form_error('country_of_residence', '<div class="error">', '</div>'); ?>
                            <?php
                            $SGP_scn_style = 'none';
                            if ($company_info->comp_cntry_scn == 'SGP') {
                                $SGP_scn_style = 'inline';
                            }
                            ?>                            
                            <div id="SGP" style="float:middle; width:60%;display:<?php echo $SGP_scn_style; ?>;">
                                <strong>SCN:</strong><span class="required">*</span>
                                <input type="radio" name="comp_scn"  checked="checked" value="Yes"> Yes&nbsp;&nbsp;<input type="radio" name="comp_scn" value="No" <?php if ($company_info->comp_scn == 'No') echo 'checked="checked"'; ?> > No
                            </div>                            
                        </td>
                <span id="ind_pan1"></span>
                <td class="td_heading">SME Type:</td>
                <td colspan="2">
                    <?php
                    if ($company_info->sme_nonsme == 'NONSME') {
                        $checked1 = TRUE;
                        $checked2 = FALSE;
                    } else {
                        $checked2 = TRUE;
                        $checked1 = FALSE;
                    }
                    $sme = array(
                        'name' => 'sme_type',
                        'id' => 'sme_type',
                        'class' => 'sme_type',
                        'value' => 'SME',
                        'checked' => $checked2
                    );
                    $non_sme = array(
                        'name' => 'sme_type',
                        'id' => 'sme_type',
                        'class' => 'sme_type',
                        'value' => 'NONSME',
                        'checked' => $checked1
                    );
                    echo form_radio($sme);
                    ?>
                    SME &nbsp;&nbsp; <?php echo form_radio($non_sme); ?> NON-SME</td>
                </tr>
                <tr>
                    <td class="td_heading">Company Attn.:<span class="required">*</span></td>
                    <td colspan="2">
                        <?php
                        $fn = array(
                            'name' => 'comp_attn',
                            'id' => 'comp_attn',
                            'value' => $company_info->comp_attn,
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
                            'value' => $company_info->comp_email,
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
                        'class' => 'upper_case',
                        'onblur' => 'this.value=this.value.toUpperCase();',
                        value => $company_info->remarks
                    );
                    ?>
                    <td colspan="5"><?php echo form_textarea($fn); ?></td>          
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/address.png" /> Address</h2>
        <div class="table-responsive" id="company_address">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading">Street / Building:</td>
                        <?php
                        $fn = array(
                            'name' => 'street',
                            'id' => 'street',
                            'value' => $company_info->comp_address,
                            'maxlength' => '200',
                            'size' => '50',
                            'class' => 'upper_case',
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
                            'value' => $company_info->comp_city,
                            'maxlength' => '50',
                            'size' => '50',
                            'class' => 'upper_case',
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
                            echo form_dropdown('company_country', $country_options, $company_info->comp_cntry, $addr);
                            echo form_hidden('current_company_country', $company_info->comp_cntry, 'current_company_country');
                            ?>
                            <?php echo form_error('company_country', '<div class="error">', '</div>'); ?>
                        </td>
                        <td class="td_heading" >State:</td>
                        <td id="append_company_states">
                            <?php
                            if ($company_info->comp_cntry != "") {
                                $attr = array('' => 'Select');
                                $country_param = $company_info->comp_cntry;
                                if ($country_param != '') {
                                    $states = $this->internaluser->get_states($country_param);
                                    foreach ($states as $item) {
                                        $attr[$item->parameter_id] = $item->category_name;
                                    }
                                }
                                $attr_js = 'id="pers_states"';
                                echo form_dropdown('pers_states', $attr, $company_info->comp_state, $attr_js);
                                echo form_hidden('current_pers_states', $company_info->comp_state, 'current_pers_states');
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
                            'value' => $company_info->comp_zip,
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
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/contact.png" /> Contact Details  
            <div class="a_class">
                <span class="label label-default push_right black-btn" id="addMore"><span class="glyphicon glyphicon-plus glyphicon1"></span> Add More</span>                 
            </div>
        </h2>
        <div class="multi-field-wrapper">
            <div class="multi-fields">
                <div class="table-responsive multi-field">
                    <div id="contact_details">
                        <?php
                        if (empty($company_users_details)) {
                            ?>                        
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
                                                'onblur' => "javascript:check_username(this.value, this.id, 'td_username_0');",
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
                        <?php } else {
                            $k=1;
                            for ($i = 0; $i < count($company_users_details); $i++) {
                                if ($company_users_details[$i]->user_acct_status != 'INACTIV') {
                                    ?>                        
                                    <table class="table table-striped" id="contact_tbl">
                                        <tbody>
                                            <tr>
                                                <td width="15%" class="td_heading"><?php echo ($k==1)?'Primary Contact Name':'Name';?>:<span class="required">*</span></td>
                                                <?php
                                                $fn = array(
                                                    'name' => 'fname[]',
                                                    'id' => 'fname_' . $i,
                                                    'class' => 'upper_case',
                                                    'value' => $company_users_details[$i]->first_name,
                                                    'onblur' => 'this.value=this.value.toUpperCase();',
                                                    'maxlength' => '100',
                                                    'style' => "width:250px",
                                                );
                                                ?>
                                                <td colspan="5">
                                                    <?php echo form_input($fn); ?>
                                                    <?php echo form_error('fname[' . $i . ']', '<div class="error">', '</div>'); ?>
                                                    <span id="fname_<?php echo $i; ?>_err"></span>
                                                </td>
                                                <td rowspan="3" valign="middle">
                                                    <?php if ($company_users_details[$i]->user_acct_status == 'ACTIVE' && $active_company_contacts_num > 1) { ?>
                                                        <a class="small_text white_color" href="#ex8" rel="modal:open" onClick="javascript:set_deactivate_company_user_id(<?php echo $company_users_details[$i]->user_id; ?>);"><u>Deactivate</u></a>
                                                    <?php } ?>
                                                    &nbsp;
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
                                                    echo form_dropdown('gender[]', $gender_options, $company_users_details[$i]->gender, ' id=gender_' . $i);
                                                    ?>
                                                    <span id="gender_<?php echo $i; ?>_err"></span>
                                                </td>
                                                <td class="td_heading">Contact Number [O]:<span class="required">*</span></td>
                                                <?php
                                                $fn = array(
                                                    'name' => 'contactno[]',
                                                    'id' => 'contactno_' . $i,
                                                    'value' => $company_users_details[$i]->contact_number,
                                                    'maxlength' => '50',
                                                    'style' => "width:180px",
                                                );
                                                ?>
                                                <td >
                                                    <?php echo form_input($fn); ?>
                                                    <?php echo form_error('contactno[' . $i . ']', '<div class="error">', '</div>'); ?>
                                                    <span id="contactno_<?php echo $i; ?>_err"></span>
                                                </td>
                                                <td class="td_heading">Mobile Number [O]:</td>
                                                <?php
                                                $fn = array(
                                                    'name' => 'mobileno[]',
                                                    'id' => 'mobileno_' . $i,
                                                    'value' => $company_users_details[$i]->alternate_contact_number,
                                                    'maxlength' => '50',
                                                    'style' => "width:180px",
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
                                                        'value' => $company_users_details[$i]->registered_email_id,
                                                        'maxlength' => '50',
                                                        'style' => "width:250px",
                                                    );
                                                    echo form_input($fn);
                                                    echo form_hidden('email_01_hidden[]', $company_users_details[$i]->registered_email_id, FALSE);
                                                    ?>
                                                    <?php echo form_error('email_01[' . $i . ']', '<div class="error">', '</div>'); ?>
                                                    <input type="hidden" name="email_01_current[]" id="email_01_<?php echo $i; ?>_current" value="<?php echo $company_users_details[$i]->registered_email_id; ?>" />
                                                    <span id="email_01_<?php echo $i; ?>_err"></span>
                                                </td>
                                                <td class="td_heading">Email Id 02:</td>
                                                <?php
                                                $fn = array(
                                                    'name' => 'email_02[]',
                                                    'id' => 'email_02_' . $i,
                                                    'value' => $company_users_details[$i]->alternate_email_id,
                                                    'maxlength' => '50',
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
                                                <td colspan="5">
                                                    <?php
                                                    $fn = array(
                                                        'name' => 'username[]',
                                                        'id' => 'username_' . $i,
                                                        'value' => $company_users_details[$i]->user_name,
                                                        'readonly' => 'readonly',
                                                        'maxlength' => '15',
                                                        'style' => "width:250px",
                                                        'type' => 'hidden',
                                                    );
                                                    echo form_input($fn);
                                                    echo $company_users_details[$i]->user_name;
                                                    ?>
                                                    <input type="hidden" name="username_current[]" id="username_<?php echo $i; ?>_current" value="<?php echo $company_users_details[$i]->user_name; ?>" />
                                                    <input type="hidden" name="username_userids[]" value="<?php echo $company_users_details[$i]->user_id; ?>" />   
                                                    <span id="username_<?php echo $i; ?>_err"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td_heading">Contact Status: </td>
                                                <td colspan="6">
                                                    <?php
                                                    $user_current_status = get_param_value(trim($company_users_details[$i]->user_acct_status));
                                                    if ($company_users_details[$i]->user_acct_status == 'INACTIV') {
                                                        echo '<div class="red">' . $user_current_status->category_name . '</div>';

                                                        if ($company_users_details[$i]->deacti_reason == 'OTHERS') {
                                                            $reason = $company_users_details[$i]->deacti_reason_oth;
                                                        } else {
                                                            $meta_details = get_param_value($company_users_details[$i]->deacti_reason);
                                                            $reason = $meta_details->category_name;
                                                        }
                                                        $deactivated_by = $this->internaluser->get_user_details($tenant_id, $company_users_details[$i]->deacti_by);
                                                        echo '<span> (' . $reason . '. Deactivated by ' . $deactivated_by->user_name . ' on ' . $company_users_details[$i]->acct_deacti_date_time . ')</span>';
                                                    } else if ($company_users_details[$i]->user_acct_status == 'ACTIVE') {
                                                        echo '<span class="green">' . $user_current_status->category_name . '</span>';
                                                    } else if ($company_users_details[$i]->user_acct_status == 'PENDACT') {
                                                        echo '<span class="blue">' . $user_current_status->category_name . '</span>';
                                                    } else {
                                                        echo $user_current_status->category_name;
                                                    }
                                                    ?>
                                                    <input type="hidden" name="username_status[]" value="<?php echo $company_users_details[$i]->user_acct_status; ?>" />  
                                                </td>
                                            </tr>                                 
                                            <tr>
                                                <td colspan="7">&nbsp;&nbsp;&nbsp;</td>
                                            </tr>                                
                                        </tbody>
                                    </table>   
                                <?php } else { ?>

                                    <table class="table table-striped" id="contact_tbl">
                                        <tbody>
                                            <tr>
                                                <td width="15%" class="td_heading">Name:</td>
                                                <td colspan="5">
                                                    <?php echo $company_users_details[$i]->first_name; ?>
                                                </td>
                                                <td  rowspan="3" valign="middle">
                                                    <a class="small_text white_color" href="#ex88" rel="modal:open" onClick="#"><u></u></a>
                                                    &nbsp;
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="15%" class="td_heading">Gender:</td>
                                                <td >
                                                    <?php
                                                    echo ($company_users_details[$i]->gender) ? get_param_value($company_users_details[$i]->gender)->category_name : '&nbsp; &nbsp; &nbsp; &nbsp; ';
                                                    ?>
                                                </td>
                                                <td class="td_heading">Contact Number [O]:</td>
                                                <td>
                                                    <?php echo $company_users_details[$i]->contact_number; ?>
                                                </td>

                                                <td class="td_heading">Mobile Number [O]:</td>                                   
                                                <td>
                                                    <?php echo $company_users_details[$i]->alternate_contact_number; ?>
                                                    <span id="mobileno_<?php echo $i; ?>_err"></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td_heading">Email Id 01 (O):</td>                                       
                                                <td colspan="2">
                                                    <?php echo $company_users_details[$i]->registered_email_id; ?>
                                                </td>
                                                <td class="td_heading">Email Id 02:</td>                                  
                                                <td colspan="2">
                                                    <?php echo $company_users_details[$i]->alternate_email_id; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td_heading">Username:</td>                                        
                                                <td colspan="5">
                                                    <?php echo $company_users_details[$i]->user_name; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="td_heading">User Status: </td>
                                                <td colspan="6">
                                                    <?php
                                                    $user_current_status = get_param_value(trim($company_users_details[$i]->user_acct_status));
                                                    echo '<div class="red"><strong>' . $user_current_status->category_name . '</strong></div>';

                                                    if ($company_users_details[$i]->deacti_reason == 'OTHERS') {
                                                        $reason = $company_users_details[$i]->deacti_reason_oth;
                                                    } else {
                                                        $meta_details = get_param_value($company_users_details[$i]->deacti_reason);
                                                        $reason = $meta_details->category_name;
                                                    }
                                                    $deactivated_by = $this->internaluser->get_user_details($tenant_id, $company_users_details[$i]->deacti_by);
                                                    echo '<span> (' . $reason . '. Deactivated by ' . $deactivated_by->user_name . ' on ' . $company_users_details[$i]->acct_deacti_date_time . ')</span>';
                                                    ?>
                                                    <input type="hidden" name="username_status[]" value="<?php echo $company_users_details[$i]->user_acct_status; ?>" />  
                                                </td>
                                            </tr>                                 
                                            <tr>
                                                <td colspan="7">&nbsp;&nbsp;&nbsp;</td>
                                            </tr>                                
                                        </tbody>
                                    </table>                        
                                    <?php
                                }
                            $k++;}
                        }
                        ?>
                    </div>
                    <div>
                        <table border="0">
                            <?php if ($company_info->comp_status == 'PENDACT') { ?>
                                <tr>
                                    <td colspan="7">
                                        <?php
                                        $now = array(
                                            'name' => 'activate_company',
                                            'value' => 'ACTIVE'
                                        );
                                        $later = array(
                                            'name' => 'activate_company',
                                            'checked' => TRUE,
                                            'value' => 'PENDACT'
                                        );
                                        ?>
                                        <?php echo form_radio($now); ?>Activate Company Now &nbsp;&nbsp;
                                        <?php echo form_radio($later); ?> Activate Later
                                </tr>
                                <tr>
                                    <td colspan="7"><i>(On company activation, mail will be sent to the user’s official email Id, with the username.)</i></td>
                                </tr>
                            <?php } ?>

                            <?php if ($company_info->comp_status != 'PENDACT') { ?>
                                <tr>
                                    <td class="td_heading">Company Status: </td>
                                    <td colspan="6">
                                        <?php
                                        $company_current_status = get_param_value(trim($company_info->comp_status));
                                        if ($company_info->comp_status == 'INACTIV') {
                                            echo '<div class="red">' . $company_current_status->category_name . '</div>';
                                        } else if ($company_info->comp_status == 'ACTIVE') {
                                            echo '<span class="green">' . $company_current_status->category_name . '</span>';
                                        } else {
                                            echo $company_current_status->category_name;
                                        }
                                        ?>
                                        <input type="hidden" name="activate_company" value="<?php echo $company_info->comp_status; ?>" />
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <span class="required required_i">* Required Fields</span>
        </div>

        <div class="button_class99">
            <button class="btn btn-primary" type="submit" name="submit_button" id="submit_button" value="Update"><span class="glyphicon glyphicon-retweet"></span>&nbsp;Update</button></a>&nbsp;&nbsp;&nbsp;
            <?php if ($company_info->comp_status == 'ACTIVE') { ?>
                <?php if ($number_trainees_payment_pending > 0) { ?>
                    <a href="#ex10" rel="modal:open">
                    <?php } else { ?>
                        <a href="#ex9" rel="modal:open">
                        <?php } ?>   
                        <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-remove-sign"></span>&nbsp;Deactivate</button>
                    </a>&nbsp;&nbsp;&nbsp;
                <?php } ?>
        </div>
    </div>
    <?php if (empty($company_discount)) { ?>
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
                            <th width="60%">Course</th>
                            <th width="20%">Discount %</th>
                            <th width="20%">Discount Amt. (SGD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($company_discount as $row) {
                            $k = $row['course_id'];
                            echo "<tr>
                                        <td>" . $row['crse_name'] . "</td>
                                        <td><input type='text' maxlength='10' id='comp_disc_perc_" . $k . "' class='comp_disc_perc' data-key='" . $k . "' value='" . number_format($row['Discount_Percent'], 2, '.', '') . "' name='comp_disc_perc[" . $k . "]'>% <span id='comp_disc_perc_" . $k . "_err'></span></td>
                                        <td>$ <input type='text' maxlength='10' id='comp_disc_amt_" . $k . "' class='comp_disc_amt' data-key='" . $k . "' value='" . number_format($row['Discount_Amount'], 2, '.', '') . "' name='comp_disc_amt[" . $k . "]'><span id='comp_disc_amt_" . $k . "_err'></span></td>
                                    </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <br>
             <span id="popup_error_err"></span>
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
                    'maxlength' => '100',
                    'class' => 'upper_case',
                    'onblur' => 'this.value=this.value.toUpperCase();',
                    'style' => "width:250px",
                );
                ?>                           
                <td colspan="5">
                    <?php echo form_input($fn); ?>
                    <span id="fname_err"></span>
                </td>               
                <td  rowspan="3" valign="middle">
                    <img  src="<?php echo base_url(); ?>assets/images/remove-red.png" id="remInput" title="Remove" onclick="$(this).parent().parent().parent().parent().remove
                                    ();" />
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
                    'style' => "width:180px",
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
                    'style' => "width:180px",
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
                <td colspan="5" id="td_username_">
                    <?php
                    $fn = array(
                        'name' => 'username[]',
                        'id' => 'username_',
                        'onblur' => "javascript:check_username(this.value, this.id, 'td_username_');",
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
</div>
<div class="modal1_051" id="ex8" style="display:none;">
    <h2 class="panel_heading_style">Deactivate Company Contact</h2>  
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading"> De-Activation Date:<span class="red"></span> </td>
                    <td><?php echo date('d/m/Y'); ?></td>
                </tr>            
                <tr>
                    <td class="td_heading" width="30%">Reason for De-Activation:<span class="red">*</span></td>
                    <td> 
                        <?php
                        $d_reasons = fetch_metavalues_by_category_id(Meta_Values::DEACTIVATE_REASONS);
                        $reasons_options[''] = 'Select';
                        foreach ($d_reasons as $item):
                            $reasons_options[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        $reasons_options['OTHERS'] = 'Others';
                        $attr = 'id="reason_for_deactivation"';
                        echo form_dropdown('reason_for_deactivation', $reasons_options, $this->input->post('reason_for_deactivation'), $attr);
                        ?> &nbsp; 
                        <span id="reason_for_deactivation_err"></span>
                        <div id="other_reason" style="display:none;">
                            <?php
                            $attr = array(
                                'name' => 'other_reason_for_deactivation',
                                'id' => 'other_reason_for_deactivation',
                                'size' => 28,
                                'class' => 'upper_case'
                            );
                            echo form_input($attr);
                            ?>  
                            <span id="other_reason_for_deactivation_err"></span>
                        </div> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div>       
    <br>
    Are you sure you want to deactivate this User?
    <br>
    <span class="required_i red">*Required Field</span>
    <div class="popup_cancel9">
        <div rel="modal:close">
            <?php if($tenant_id != 'T25') { ?>
                    <a onclick="javscript:validate_deactivate_company_user_form();"><button class="btn btn-primary">Yes</button></a>&nbsp;&nbsp;
            <?php } else { ?>
                    <a onclick="javscript:validate_deactivate_fonda_company_user_form();"><button class="btn btn-primary">Yes</button></a>&nbsp;&nbsp;          
            <?php } ?>
            <a rel="modal:close" onclick="javascript:unset_deactivate_company_user_id
                            ();"><button class="btn btn-primary" type="button">No</button></a>
        </div>
    </div>
</div>
<?php
$form_attributes = array('name' => 'companyDeactivateForm', 'id' => 'companyDeactivateForm', "onsubmit" => "return validate_deactivate_company_form();");
echo form_open("company/deactivate_company", $form_attributes);
?>

<div class="modal1_055" id="ex9" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Deactivate Company</h2>  
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading" width="30%"> De-Activation Date:<span class="red">*</span> </td>
                    <td> 
                        <?php
                        echo date('d/m/Y');
                        ?>
                    </td>
                </tr>            
                <tr>
                    <td class="td_heading">Reason for De-Activation:<span class="red">*</span></td>
                    <td> <?php
                        $d_reasons = fetch_metavalues_by_category_id(Meta_Values::COMPANY_DEACTIVATE_REASONS);
                        $reasons_options_comp[''] = 'Select';
                        foreach ($d_reasons as $item):
                            $reasons_options_comp[$item['parameter_id']] = $item['category_name'];
                        endforeach;
                        $reasons_options_comp['OTHERS'] = 'Others';
                        $attr = 'id="company_reason_for_deactivation"';
                        echo form_dropdown('company_reason_for_deactivation', $reasons_options_comp, $this->input->post('reason_for_deactivation'), $attr);
                        ?> &nbsp; 
                        <span id="company_reason_for_deactivation_err"></span>
                        <?php
                        $attr = array(
                            'name' => 'company_other_reason_for_deactivation',
                            'id' => 'company_other_reason_for_deactivation',
                            'size' => 27,
                            'style' => 'display:none;',
                            'class' => 'upper_case'
                        );
                        echo form_input($attr);
                        echo form_hidden('deactivate_company_id', $company_id);
                        ?>  
                        <span id="company_other_reason_for_deactivation_err"></span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>       
    Are you sure you want to deactivate this company? 
    <br>
    <span class="required_i red">*Required Field</span>
    <div class="popup_cancel9">
        <button class="btn btn-primary" type="submit">Yes</button>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">No</button></a>
    </div>
</div>
<div class="modal1_055" id="ex10" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Deactivate Company</h2>  
    <div class="table-responsive">
        <table class="table table-striped">      
            <tbody>
                <tr>
                    <td class="td_heading" width="30%" colspan="2">There are trainees enrolled in classes
                        with payments due. Please clear any payments, before deactivating the company
                        account.</td>
                </tr>            
            </tbody>
        </table>
    </div>
    <div class="popup_cancel9">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a>            
    </div>
</div>
<?php
echo form_close();
?>
<script src="<?php echo base_url(); ?>assets/js/editcompany.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url(); ?>assets/js/deactivatecompany.js" type="text/javascript" charset="utf-8"></script>


