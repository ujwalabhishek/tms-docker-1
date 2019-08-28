<div class="container_nav_style">	
    <div class="container container_row">
        <div class="row row_pushdown">
            <div style="clear:both;"></div>
            <div class="col-md-12">
                <h2 class="panel_heading_style">
                    Profile of <?php echo $profile['userdetails']['first_name'] . ' ' . $profile['userdetails']['last_name']; ?>
<!--                    <span class="pull-right"><a style="color: #ffffff;"
                        href="<?php echo base_url() ?>user/edit_refer_trainee/?t=<?php echo $profile['userdetails']['user_id']; ?>"><span class="glyphicon glyphicon-pencil"></span> Edit</a>
                                
                    </span>-->
                </h2>
                <h2 class="sub_panel_heading_style">Access Details</h2>
                <div class="bs-example">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td class="td_heading" width="25%">Country of Residence:</td>
                                    <td>  
                                        <?php
                                        $meta_result = fetch_all_metavalues();

                                        $countries = $meta_result[Meta_Values_Model::COUNTRIES];
                                        $country_options[''] = '';
                                        foreach ($countries as $item):
                                            $country_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        echo $country_options[$profile['userdetails']['country_of_residence']];
                                        ?>
                                    </td>
                                    <td>
                                        <SPAN id="IND" style="<?php echo ($country == 'IND') ? '' : 'display:none;'; ?>">PAN : 
                                            <?php
                                            echo $profile['userdetails']['tax_code'];
                                            ?>
                                        </SPAN>
                                        <SPAN id="SGP" style="<?php echo ($country == 'SGP') ? '' : 'display:none;'; ?>">NRIC Type: 
                                            <?php
                                            $nrics = $meta_result[Meta_Values_Model::NRIC];
                                            $nris_options[''] = '';
                                            foreach ($nrics as $item):
                                                $nris_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;
                                            echo $nris_options[$profile['userdetails']['tax_code_type']];
                                            ?>
                                        </SPAN>
                                        <SPAN id="SGP_OTHERS" style="<?php echo ($nric_value == 'SNG_3') ? '' : 'display:none;'; ?>">

                                            <label id="SGP_OTHERS_label"></label>
                                            <?php
                                            $nric_other = $meta_result[Meta_Values_Model::NRIC_OTHER];
                                            $nric_other_options[''] = '';
                                            foreach ($nric_other as $item):
                                                $nric_other_options[$item['parameter_id']] = $item['category_name'];
                                            endforeach;
                                            echo $nric_other_options[$profile['userdetails']['other_identi_type']];
                                            ?>
                                        </SPAN>
                                        <SPAN id="SGP_ID" style="<?php echo (!empty($nric_value) && ($country == 'SGP')) ? '' : 'display:none;'; ?>">
                                            <label id="SGP_ID_label"></label>
                                            <?php
                                            echo $profile['userdetails']['tax_code'];
                                            ?>
                                        </SPAN>
                                           <SPAN id="USA" style="<?php echo ($country == 'USA') ? '' : 'display:none;'; ?>">SSN : <!-- ###commented by sankar on 07/01/2015<span class="required">* </span> -->
                                            <?php
                                            echo $profile['userdetails']['tax_code'];
                                            ?>    
                                        </SPAN>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td_heading">Username:</td>
                                    <td colspan="5"><label class="label_font"><?php echo $profile['userdetails']['user_name']; ?></label></td>
                                </tr>
                                <tr>
                                    <td class="td_heading">Your relationship with the trainee</td>
                                    <td colspan="5">
                                        <?php
                                        echo ucfirst(strtolower($profile['userdetails']['friend_relation']));
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <h2 class="sub_panel_heading_style">Personal Details</h2>
                        <div class="table-responsive">
                            <table class="table table-striped" width="100%"  >      
                                <tr>
                                    <td class="td_heading" width="20%">Name:</td>
                                    <td colspan="3">
                                        <?php
                                        echo $profile['userdetails']['first_name'];
                                        ?>  
                                    </td>                                    
                                </tr>
                                <tr>
                                    <td class="td_heading" width="20%">Gender:</td>
                                    <td>
                                        <?php
                                        $gender = $meta_result[Meta_Values_Model::GENDER];
                                        $gender_options = array();
                                        $gender_options[''] = '';
                                        foreach ($gender as $item):
                                            $gender_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        echo $gender_options[$profile['userdetails']['gender']];
                                        ?> 
                                    </td>
                                    <td class="td_heading" width="20%">Contact Number:</td>
                                    <td>
                                        <?php
                                        echo $profile['userdetails']['contact_number'];
                                        ?> 
                                        <span id="pers_contact_number_err"></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="td_heading">Email Id: </td>
                                    <td><?php echo $profile['userdetails']['registered_email_id']; ?>
                                    </td>
                                    <td class="td_heading">Confirm Email Id:</td>
                                    <td><?php echo $profile['userdetails']['registered_email_id']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="10%" class="td_heading">Nationality:</td>
                                    <td >
                                        <?php
                                        $nationality = $meta_result[Meta_Values_Model::NATIONALITY];
                                        $nationality_options = array();
                                        $nationality_options[''] = '';
                                        foreach ($nationality as $item):
                                            $nationality_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        echo $nationality_options[$profile['userdetails']['nationality']];
                                        ?>
                                    </td>
                                    <td  class="td_heading">Highest Education:</td>
                                    <td  >
                                        <?php
                                        $highest_educ_level = $meta_result[Meta_Values_Model::HIGHEST_EDUC_LEVEL];
                                        $highest_educ_level_options[''] = 'Select';
                                        $js = 'id="highest_educ_level" style="width:95% !important"';
                                        foreach ($highest_educ_level as $item):
                                            $highest_educ_level_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        ?>
                                        <?php echo $highest_educ_level_options[$profile['userdetails']['highest_educ_level']]; ?>
                                    </td>

                                </tr>
                                <tr>
                                    <td   class="td_heading">Occupation:</td>
                                    <td colspan="3">
                                        <?php
                                        $occupation = $meta_result[Meta_Values_Model::DESIGNATION];
                                        $occupation_options[''] = '';

                                        foreach ($occupation as $item):
                                            $occupation_options[$item['parameter_id']] = $item['category_name'];
                                        endforeach;
                                        ?>
                                        <?php echo $occupation_options[$profile['userdetails']['occupation_code']]; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                </div>
                <br/>
                <div class="button_class99">
                    <a href="<?php echo base_url().'user/referral_list'; ?>"><button class="btn btn-sm btn-info" type="button"><span class="glyphicon glyphicon-step-backward"></span> <strong>Back</strong></button></a>
                </div> 
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

    $(document).ready(function() {
        country_of_residence = $('#country_of_residence').val();
        other_type = $("#NRIC").val();
        if (country_of_residence) {
            $('#' + country_of_residence).show();
            $("#" + country_of_residence + "_ID").show();
            $("#SGP_" + other_type).show();
        }

        $('#country_of_residence').trigger('change');
    });
</script>