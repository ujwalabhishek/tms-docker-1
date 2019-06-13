<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/view.png" /> View Trainees/ Employees</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="searchCompanyTraineeForm" name="searchCompanyTraineeForm" method="get"';
        echo form_open("company/trainees/$company_id", $atr);
        ?>   
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>" />             
        <table class="table table-striped">
            <tbody>
            <span class="error" id="validate_trainee_search_form_err"></span>
            <tr>
                <td width="18%" class="td_heading">Search by: </td>
                <td colspan="3">
                    <table>
                        <tr>
                            <td>
                                <?php
                                $search_by = array(
                                    'name' => 'search_by',
                                    'value' => 'trainee_name',
                                    'checked' => TRUE,
                                    'onclick' => "$('#search_company_trainee_taxcode').val(''); $('#search_company_trainee_taxcode').attr('disabled', true); $('#search_company_trainee_name').attr('disabled', false);"
                                );
                                echo form_radio($search_by);
                                ?>&nbsp;<span class="td_heading">Trainee Name: </span>                          
                            </td>
                            <td>
                                <?php
                                $trainee_name_disabled = '';
                                if ($this->input->get('search_by') == 'tax_code') {
                                    $trainee_name_disabled = 'disabled="true"';
                                }
                                ?>                          
                                <input type="text" size="50" value="<?php echo $this->input->get('search_company_trainee_name'); ?>" id="search_company_trainee_name" name="search_company_trainee_name" <?php echo $trainee_name_disabled; ?> >
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                $search_by = array(
                                    'name' => 'search_by',
                                    'value' => 'tax_code',
                                    'checked' => ( $this->input->get('search_by') == 'tax_code' ) ? TRUE : FALSE,
                                    'onclick' => "$('#search_company_trainee_name').val(''); $('#search_company_trainee_name').attr('disabled', true); $('#search_company_trainee_taxcode').attr('disabled', false);"
                                );
                                echo form_radio($search_by);
                                ?>&nbsp;              
                                <span class="td_heading"> NRIC/FIN No.:</span>
                            </td>
                            <td>
                                <?php
                                $tax_code_disabled = 'disabled="true"';
                                if ($this->input->get('search_by') == 'tax_code') {
                                    $tax_code_disabled = '';
                                }
                                ?>                          
                                <input type="text" size="50" value="<?php echo $this->input->get('search_company_trainee_taxcode'); ?>" id="search_company_trainee_taxcode" name="search_company_trainee_taxcode" <?php echo $tax_code_disabled; ?> >
                            </td>
                        </tr>                  
                    </table>
                </td>
                <td width="20%" align="center">
                    <?php if ($totalrows_no_search == 0) { ?>
                        <a href="#div_no_trainees_added" rel="modal:open">
                            <button type="button" value="Search" title="Search" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                        </a>
                    <?php } else { ?>               
                        <button type="submit" value="Search" title="Search" class="btn btn-xs btn-primary no-mar" onclick="return validate_trainee_search_form();"><span class="glyphicon glyphicon-search"></span> Search</button>
                    <?php } ?>
                </td>
            </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>   
    </div>
    <br><br>
    <div class="add_button space_style">
        <?php
        if (array_key_exists('EXP_XLS', $this->data['left_side_menu']['TRAINEE'])) {
            if (count($tabledata) > 0) {
                ?>
                <a href="<?php echo site_url('company/export_company_trainee_page/' . $company_id . $export_url) ?>" target="_blank" class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export Page Fields</span></a> &nbsp;&nbsp;
                <?php
            }
        }
        ?>
        &nbsp;
    </div>
    <div style="clear:both;"></div>
    <div class="table-responsive">
        <?php
        $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
        $pageurl = $controllerurl;
        ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $query_string . "&f=tup.first_name&o=" . $ancher; ?>" >Trainee Name</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $query_string . "&f=tu.country_of_residence&o=" . $ancher; ?>" >Nationality</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $query_string . "&f=tu.tax_code&o=" . $ancher; ?>" >NRIC/FIN No.</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $query_string . "&f=tu.registration_date&o=" . $ancher; ?>" >Registration Date</a></th>
                    <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $query_string . "&f=tup.dob&o=" . $ancher; ?>" >Date of Birth</a></th>
                    <th class="th_header"><a style="color:#000000;" href="#" >Contact Details</a></th>                    
                </tr>
            </thead>
            <tbody>
                <?php if (count($tabledata) == 0) { ?> 
                    <tr >
                        <td colspan="6" align="center">No trainees found!</td>
                    </tr>          
                <?php } ?>          
                <?php foreach ($tabledata as $data) { ?>      
                    <tr>
                        <td valign="top">
                            <a href="<?php echo base_url(); ?>trainee/view_trainee/<?php echo $data['user_id']; ?>"><?php echo $data['first_name'] . ' ' . $data['last_name']; ?></a>
                        </td>
                        <td valign="top">
                            <?php
                            $country = get_param_value(trim($data['country_of_residence']));
                            echo $country->category_name;
                            ?>
                        </td>
                        <td valign="top">
                            <?php
                            if ($data['tax_code_type'] && $data['tax_code']) {
                                if ($data['tax_code_type'] != 'OTHERS') {
                                    $type = get_param_value($data['tax_code_type']);
                                    echo $type->category_name . ' - ' . $data['tax_code'];
                                }
                            }
                            if ($data['other_identi_type'] && $data['other_identi_code']) {
                                $tax_code_type = get_param_value($data['tax_code_type']);
                                $type = get_param_value($data['other_identi_type']);
                                echo $tax_code_type->category_name . ' - ' . $type->category_name . ' - ' . $data['other_identi_code'];
                            }
                            ?>
                        </td>
                        <td valign="top">
                            <?php
                            $datetime = DateTime::createFromFormat('Y-m-d', $data['registration_date']);
                            echo $datetime->format('d/m/Y');
                            ?>
                        </td>
                        <td valign="top">
                            <?php
                            if (!empty($data['dob'])) {
                                $datetime = DateTime::createFromFormat('Y-m-d', $data['dob']);
                                echo $datetime->format('d/m/Y');
                            }
                            ?>
                        </td>
                        <td valign="top">
                            <?php
                            $contact_details = $data['personal_address_bldg'] . ", ";
                            if ($data['personal_address_city'] != '') {
                                $contact_details .=$data['personal_address_city'] . ", ";
                            }
                            if ($data['personal_address_state'] != '' && $data['personal_address_state'] != 0) {
                                echo $data['personal_address_state'];
                                $state = get_param_value($data['personal_address_state']);
                                $contact_details .= $state->category_name . ", ";
                            }
                            if ($data['personal_address_country'] != '') {
                                $country = get_param_value($data['personal_address_country']);
                                $contact_details .= $country->category_name . ", ";
                            }
                            if ($data['personal_address_zip'] != '') {
                                $contact_details .= $data['personal_address_zip'] . ", ";
                            }
                            $contact_details = rtrim($contact_details, ', ');
                            if (!empty($data['contact_number'])) {
                                if (!empty($contact_details)) {
                                  $contact_details .= "\n Phone: " . $data['contact_number'];
                                }else{
                                    $contact_details .= "Phone: " . $data['contact_number'];
                                }
                            }
                            if (!empty($data['registered_email_id'])) {
                                $contact_details .= "\n" . "Email: " . $data['registered_email_id'];
                            }
                            echo nl2br(trim($contact_details, ', '));
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody></table>
    </div>
    <div style="clear:both;"></div>
    <br>
    <ul class="pagination pagination_style">
        <?php echo $pagination; ?>
    </ul>
    <div style="clear:both;"></div>
</div>
<div class="modal0000" id="div_no_trainees_added" style="display:none;">
    <p>
    <h2 class="panel_heading_style">Alert Message</h2>
    There are no trainees added yet!
    <div class="popup_cancel popup_cancel001">
        <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
</p>
</div>
<script src="<?php echo base_url(); ?>assets/js/trainees.js" type="text/javascript" charset="utf-8"></script>
