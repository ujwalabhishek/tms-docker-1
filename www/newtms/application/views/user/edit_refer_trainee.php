<script>
    var refer_friend = 1;
</script>
<div class="container_nav_style">	
    <div class="container container_row">
        <!-- Example row of columns -->
        <div class="row row_pushdown">
            <div style="clear:both;"></div>
            <div class="col-md-12">
                <span class="error"><?php echo validation_errors(); ?></span>
                <?php
                //code modification starts here, author: Sankar, date: 02/02/2014, reason: on change not working
                $atr = 'id="trainee_form" name="validate_form"';  //onsubmit="return(validate());"
                //code modification ends here
                echo form_open_multipart("user/update_refer_trainee1", $atr);
                ?> 
                <br>
                <h2 class="panel_heading_style">
                    Profile of <?php echo $profile['userdetails']['first_name'] . ' ' . $profile['userdetails']['last_name']; ?>
                </h2>
                <?php
                if ($this->session->flashdata('Success')) {
                    echo "<p class='success'>" . $this->session->flashdata('Success') . "</p>";
                } else {
                    echo "<p class='error1'>" . $this->session->flashdata('Error') . "</p>";
                }
                ?>
                <h2 class="sub_panel_heading_style">Access Details</h2>
                <div class="bs-example">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>

                                    <td class="td_heading">Country of Residence:<span class="required">*</span></td>
                                    <td>  
                                        <?php
                                        $meta_result = fetch_all_metavalues();

                                        $countries = $meta_result[Meta_Values_Model::COUNTRIES];
                                        $country_options[''] = 'Select';
                                        foreach ($countries as $item):
                                            $country_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        $attr = 'id="country_of_residence"';
                                        echo form_dropdown('country_of_residence', $country_options, $profile['userdetails']['country_of_residence'], $attr);
                                        ?>
                                        <span id="country_of_residence_err"></span>
                                    </td>
                                    <td>
                                        <SPAN id="IND" style="<?php echo ($country == 'IND') ? '' : 'display:none;'; ?>">PAN : 
                                            <?php
                                            $attr = array('name' => 'PAN', 'autocomplete' => "off", 'class' => 'upper_case alphanumeric', 'id' => 'PAN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id,uid=\'REFER_TRAINEE\');', 'maxlength' => '50', 'oninput' => 'this.value=this.value.toUpperCase();');
                                            echo form_input($attr, $profile['userdetails']['tax_code']);
                                            ?>
                                            <span id="PAN_err"></span>
                                        </SPAN>
        <SPAN id="SGP" style="<?php echo ($country == 'SGP') ? '' : 'display:none;'; ?>">NRIC Type: <!-- ###commented by sankar on 07/01/2015<span class="required">* </span> -->                 
                                            <?php
                                            //  $nric_value = $this->input->post('NRIC');
                                            $nrics = $meta_result[Meta_Values_Model::NRIC];
                                            $nris_options[''] = 'Select';
                                            foreach ($nrics as $item):
                                                $nris_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;

                                            $attr = 'id="NRIC"';
                                            echo form_dropdown('NRIC', $nris_options, $profile['userdetails']['tax_code_type'], $attr);
                                            ?>
                                            <span id="NRIC_err"></span>
                                        </SPAN>
                                        <SPAN id="SGP_OTHERS" style="<?php echo ($nric_value == 'SNG_3') ? '' : 'display:none;'; ?>">

                                        <label id="SGP_OTHERS_label"></label><!-- ###commented by sankar on 07/01/2015<span class="required">* </span> -->               
                                            <?php
                                            // $nric_other_value = $this->input->post('NRIC_OTHER');
                                            $nric_other = $meta_result[Meta_Values_Model::NRIC_OTHER];
                                            $nric_other_options[''] = 'Select';
                                            foreach ($nric_other as $item):
                                                $nric_other_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;

                                            $attr = 'id="NRIC_OTHER"';
                                            echo form_dropdown('NRIC_OTHER', $nric_other_options, $profile['userdetails']['other_identi_type'], $attr);
                                            ?>
                                            <span id="NRIC_OTHER_err"></span>
                                        </SPAN>
                                        <SPAN id="SGP_ID" style="<?php echo (!empty($nric_value) && ($country == 'SGP')) ? '' : 'display:none;'; ?>">

                                        <label id="SGP_ID_label"></label><!-- ###commented by sankar on 07/01/2015<span class="required">* </span> -->         
                                            <?php
                                            $attr = array('name' => 'NRIC_ID', 'autocomplete' => "off", 'class' => 'upper_case alphanumeric', 'id' => 'NRIC_ID', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id,uid=\'REFER_TRAINEE\');', 'maxlength' => '50', 'oninput' => 'this.value=this.value.toUpperCase();');
                                            echo form_input($attr, $profile['userdetails']['tax_code']);
                                            ?>
                                            <span id="NRIC_ID_err"></span>
                                        </SPAN>
                                           <SPAN id="USA" style="<?php echo ($country == 'USA') ? '' : 'display:none;'; ?>">SSN : <!-- ###commented by sankar on 07/01/2015<span class="required">* </span> -->
                                            <?php
                                            $attr = array('name' => 'SSN', 'autocomplete' => "off", 'class' => 'upper_case alphanumeric', 'id' => 'SSN', 'onblur' => 'javascript:isunique_taxcode(this.value,this.id,uid=\'REFER_TRAINEE\');', 'maxlength' => '50', 'oninput' => 'this.value=this.value.toUpperCase();');
                                            echo form_input($attr, $profile['userdetails']['tax_code']);
                                            ?>    
                                            <span id="SSN_err"></span>
                                        </SPAN>

                                    </td>
                                </tr>
                                <tr>
                                    <td class="td_heading">Username:<span class="required">*</span></td>
                                    <td colspan="5"><label class="label_font"><?php echo $profile['userdetails']['user_name']; ?></label></td>
                                </tr>
                                <tr>
                                    <td class="td_heading">Your relationship with the trainee<span class="required">*</span></td>
                                    <td colspan="5">
                                        <input type="radio" name="relationship" value="KIN"  <?php echo ($profile['userdetails']['friend_relation'] == 'KIN') ? 'checked' : ''; ?> /> Kin &nbsp;&nbsp;
                                        <input type="radio" name="relationship" value="FRIEND"  <?php echo ($profile['userdetails']['friend_relation'] == 'FRIEND') ? 'checked' : ''; ?> /> Friend &nbsp;&nbsp;
                                        <input  type="radio" name="relationship" value="COLLEAGUE"  <?php echo ($profile['userdetails']['friend_relation'] == 'COLLEAGUE') ? 'checked' : ''; ?>  /> Colleague &nbsp;&nbsp;
                                        <!--code modification starts here, author: Sankar, date: 02/02/2014, reason: Others not working-->
                                        <?php
                                        $others_checked = '';
                                        $others_style = 'display:none;';
                                        $others_text = '';
                                        if(! in_array($profile['userdetails']['friend_relation'], array('KIN','FRIEND','COLLEAGUE'))){
                                            $others_checked='checked';
                                            $others_style='display:inline;';
                                            $others_text = $profile['userdetails']['friend_relation'];
                                        } 
                                        ?>
                                        <input type="radio" name="relationship" value="OTHERS" id="other_relation"  <?php echo  $others_checked; ?> /> Others&nbsp;<span id="others_span" style="<?php echo $others_style; ?>"><input type="text" name="others" id="others" value="<?php echo $others_text; ?>"/></span>
                                        <!--code modification ends here-->
                                    </td>
                                </tr>
                                <tr>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <h2 class="sub_panel_heading_style">Personal Details</h2>
                        <div class="table-responsive">
                            <table class="table table-striped" width="100%"  >      
                                <tr>
                                    <td class="td_heading" width="20%">Name:<span class="required">*</span></td>
                                    <td colspan="3">
                                        <?php
                                        $attr = array(
                                            'name' => 'pers_first_name',
                                            'id' => 'pers_first_name',
                                            'maxlength' => '50',
                                            'value' => $profile['userdetails']['first_name'],
                                            'class' => 'upper_case ',
                                            'autocomplete' => "off",
                                            'style' => 'width:200px',
                                        );
                                        echo form_input($attr, 'javascript: text-transform: uppercase;');
                                        ?>  
                                        <span id="pers_first_name_err"></span>
                                    </td>                                    
                                </tr>
                                <tr>
                                    <td class="td_heading" width="20%">Gender:<span class="required">*</span></td>
                                    <td>
                                        <?php
                                        $gender = $meta_result[Meta_Values_Model::GENDER];
                                        $gender_options = array();
                                        $gender_options[''] = 'Select';
                                        foreach ($gender as $item):
                                            $gender_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        echo form_dropdown('pers_gender', $gender_options, $profile['userdetails']['gender'], 'id="pers_gender" style="width:200px"');
                                        ?> 
                                        <span id="pers_gender_err"></span>
                                    </td>
                                    <td class="td_heading" width="20%">Contact Number:<span class="required">*</span></td>
                                    <td>
                                        <?php
                                        $attr = array(
                                            'name' => 'pers_contact_number',
                                            'id' => 'pers_contact_number',
                                            'maxlength' => '50',
                                            'value' => $profile['userdetails']['contact_number'],
                                            'onblur' => 'javascript:validate_pers_contact_number(this.value,this.id);',
                                            'class' => 'number',
                                            'style' => 'width:200px',
                                        );
                                        echo form_input($attr);
                                        ?> 
                                        <span id="pers_contact_number_err"></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="td_heading">Email Id:  <?php
                                        $email = array(
                                            'name' => 'frnd_registered_email',
                                            'id' => 'frnd_registered_email',
                                            'maxlength' => '50',
                                            'value' => $profile['userdetails']['registered_email_id'],
                                            'onblur' => 'javascript:isunique_email(this.value,this.id);',
                                            'style' => 'width:200px',
                                        );
                                        $conf_email = array(
                                            'name' => 'frnd_conf_email',
                                            'id' => 'frnd_conf_email',
                                            'maxlength' => '50',
                                            'value' => $profile['userdetails']['registered_email_id'],
                                            'onblur' => 'javascript:confirm_email(this.value,this.id);',
                                            'style' => 'width:200px',
                                        );
                                        ?>
                                    </td>
                                    <td><?php echo form_input($email); ?>
                                        <br><span id="frnd_registered_email_err"> </span>
                                    </td>
                                    <td class="td_heading">Confirm Email Id:</td>
                                    <td><?php echo form_input($conf_email); ?>
                                        <span id="frnd_conf_email_err"></span></td>
                                </tr>
                                <tr>
                                    <td width="10%" class="td_heading">Nationality:<span class="required">*</span></td>
                                    <td >
                                        <?php
                                        $nationality = $meta_result[Meta_Values_Model::NATIONALITY];
                                        $nationality_options = array();
                                        $nationality_options[''] = 'Select';
                                        foreach ($nationality as $item):
                                            $nationality_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        echo form_dropdown('nationality', $nationality_options, $profile['userdetails']['nationality'], 'id="nationality" style="width:200px"');
                                        ?>
                                        <span id="nationality_err"></span>
                                    </td>
                                    <td  class="td_heading">Highest Education:<span class="required">*</span></td>
                                    <td  >
                                        <?php
                                        $highest_educ_level = $meta_result[Meta_Values_Model::HIGHEST_EDUC_LEVEL];
                                        $highest_educ_level_options[''] = 'Select';
                                        $js = 'id="highest_educ_level" style="width:95% !important"';
                                        foreach ($highest_educ_level as $item):
                                            $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        ?>
                                        <?php echo form_dropdown('highest_educ_level', $highest_educ_level_options, $profile['userdetails']['highest_educ_level'], $js); ?>
                                        <span id="highest_educ_level_err"></span>

                                    </td>

                                </tr>
                                <tr>
                                    <td   class="td_heading">Occupation:<span class="required">*</span></td>
                                    <td colspan="3">
                                        <?php
                                        $occupation = $meta_result[Meta_Values_Model::DESIGNATION];
                                        $occupation_options[''] = 'Select';
                                        $js = 'id="occupation" style="width:45% !important"';

                                        foreach ($occupation as $item):
                                            $occupation_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        ?>
                                        <?php echo form_dropdown('occupation', $occupation_options, $profile['userdetails']['occupation_code'], $js); ?>
                                        <span id="occupation_err"></span>
                                    </td>
                                </tr>

                            </table>
                        </div>

                    </div>
                    <br>
                </div>
                <br/>
                <br/>
                <span class="required required_i">* Required Fields</span>

                <div class="button_class99">
                    <button class="btn btn-sm btn-info" type="submit"><span class="glyphicon glyphicon-pencil"></span> <strong>Update</strong></button>

                </div> 
                <?php
                echo form_hidden('userid', $profile[userdetails]['user_id']);
                echo form_hidden('task', 'update');
                echo form_close();
                ?>
            </div>
        </div>
    </div>
</div>    
</div>          
<script type="text/javascript">
    edituser_country_of_residence = '<?php echo $profile[userdetails]['country_of_residence']; ?>';

    tax_code_type = '<?php echo $profile[userdetails]['tax_code_type']; ?>';
    tax_code_type = (tax_code_type == 'SNG_3') ? 'OTHERS' : tax_code_type;
    $("#" + edituser_country_of_residence).show();
    $("#" + edituser_country_of_residence + "_ID").show();
    $("#SGP_" + tax_code_type).show();
    other_relation = '<?php echo $profile['userdetails']['friend_relation']; ?>';
//code modification starts here, author: sankar, date: 02/02/2014, reason: edit page js style removed
//    if (other_relation == 'OTHERS') {
//        $('#others_span').show();
//    }
//    else {
//        $('#others_span').hide();
//        $('#others').val('');
//    }
//code modification ends here
    $(document).ready(function() {
        country_of_residence = $('#country_of_residence').val();
        other_type = $("#NRIC").val();
        if (country_of_residence) {
            $('#' + country_of_residence).show();
            $("#" + country_of_residence + "_ID").show();
            $("#SGP_" + other_type).show();
        }
        //code commented by sankar, date: 15/03/2015, reason: nric type changing bydefault
        //$('#country_of_residence').trigger('change');
        //code commented ends here
    });
    //for on page load date and time picker            
</script>
<script>
    $("input:radio[name=relationship]").click(function() {
        var value = $(this).val();
        if (value == 'OTHERS') {
            $('#others_span').show();
        }
        else {
            $('#others_span').hide();
            $('#others').val('');
        }

    });
</script>
<script src="<?php echo base_url(); ?>assets/public_js/validation.js" type="text/javascript"></script>

<!--<script src="<?php echo base_url(); ?>assets/js/my_profile.js" type="text/javascript"></script>-->