<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
</script>
<style>
    table td{
        font-size: 11px;
    }
</style>
<div class="col-md-10">
    <?php
    $class_status = $this->input->get('class_status');
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> Class Trainee Enrollment List</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("class_trainee", $atr);
    ?>  
    <div class="table-responsive">
        <h5  class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span> Search By</h5>

        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Course Name:</td>
                    <td colspan="3">
                        <?php
                        $options = array();
                        $options[''] = 'Select';

                        foreach ($courses as $k => $v) {
                            $options[$k] = $v;
                        }

                        $js = 'id="course" ';
                        echo form_dropdown('course', $options, $this->input->get('course'), $js);
                        ?>
                    </td>                    
                </tr>                
                <tr>
                    <td class="td_heading">Class Name:</td>
                    <td colspan='3'>
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $options[$k] = $v;
                        }
                        $js = 'id="class" ';
                        echo form_dropdown('class', $options, $this->input->get('class'), $js);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">
                        <?php
                        $checked = TRUE;
                        $checked = ($this->input->get('search_select') == 1) ? TRUE : FALSE;

                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 1,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp; NRIC/FIN No.:
                    </td>
                    <td width="32%">
                        <?php
                        $data = array(
                            'id' => 'taxcode',
                            'name' => 'taxcode',
                            'value' => $this->input->get('taxcode'),
                            'class' => 'upper_case',
                            'style' => 'width:200px;',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'taxcode_id',
                            'name' => 'taxcode_id',
                            'value' => $this->input->get('taxcode_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <br>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="taxcode_err"></span>
                    </td>
                    <td width="15%" class="td_heading">
                        <?php
                        $checked = ($this->input->get('search_select') == 2) ? TRUE : FALSE;
                        $data = array(
                            'id' => 'search_select',
                            'class' => 'search_select',
                            'name' => 'search_select',
                            'value' => 2,
                            'checked' => $checked
                        );
                        echo form_radio($data);
                        ?>
                        &nbsp;&nbsp; 
                        Trainee Name:</td>
                    <td colspan="2"><?php
                        $data = array(
                            'id' => 'trainee',
                            'name' => 'trainee',
                            'value' => $this->input->get('trainee'),
                            'class' => 'upper_case',
                            'style' => 'width:200px;',
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'trainee_id',
                            'name' => 'trainee_id',
                            'value' => $this->input->get('trainee_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        ?>
                        <br>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <span id="trainee_err"></span>
                    </td>
                </tr>
                <tr>
                    <?php if ($this->data['user']->role_id != 'COMPACT') { ?>
                        <td class="td_heading" width="15%"> Company Name:</td>
                        <td colspan="" width="30%">
                            <?php
                            $company = array(
                                'name' => 'company_name',
                                'id' => 'company_name',
                                'value' => $this->input->get('company_name'),
                                'style' => 'width:200px;',
                                'class' => 'upper_case',
                                'autocomplete' => 'off'
                            );
                            echo form_input($company);
                            echo form_hidden('company_id', $this->input->get('company_id'), $id = 'company_id');
                            ?>
                            <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                            <span id="company_name_err"></span>
                        <?php } else { ?>
                        <td colspan="5">
                        <?php } ?>

                    </td>

                    <?php if ($this->data['user']->role_id != 'COMPACT') { ?>
                        <td class="td_heading" width="15%"> Enrolment ID:</td>
                        <td colspan="" width="30%">
                            <?php
                            $enrol = array(
                                'name' => 'eidbox',
                                'id' => 'eidbox',
                                'value' => $this->input->get('eid'),
                                'style' => 'width:200px;',
                                'class' => 'upper_case',
                                'autocomplete' => 'off'
                            );
                            echo form_input($enrol);
                            echo form_hidden('eid', $this->input->get('eid'), $id = 'eid');
                            ?>
                            <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                            <span id="eid_err"></span>
                        <?php } ?>
                        <span class="pull-right">
                            <button type="submit" value="Search" class="btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div><br>
    <div class="bs-example">
        <div class="table-responsive">
            <?php if (!empty($tabledata) || !empty($class_status)) { ?>
                <strong>Filter by Class Status:</strong>
                <?php
                $cls_status_options[''] = 'All';
                $cls_status = fetch_metavalues_by_category_id(Meta_Values::CLASS_TRAINEE_FILTER);
                foreach ($cls_status as $val):
                    $cls_status_options[$val['parameter_id']] = $val['category_name'];
                endforeach;
                echo form_dropdown('class_status', $cls_status_options, $this->input->get('class_status'), 'id="class_status"');
            }
            ?> 

            <?php if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['CLTRAINE'])) { ?>                    
                <div class="add_button98 pull-right">
                    <a href="<?php echo base_url(); ?>class_trainee/export_classtrainee_page<?php echo $export_url; ?>" class="small_text1" onclick="return exportValidate()">
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export Page Fields</span>
                    </a> &nbsp;&nbsp; 
                    <a href="<?php echo base_url(); ?>class_trainee/export_classtrainee_full<?php echo $export_url; ?>" class="small_text1" onclick="return exportValidate() > < span class ="label label-default black-btn">

                       <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export All Fields</span>
                    </a>
                </div>                  
            <?php } ?>
        </div>

    </div>
    <?php echo form_close(); ?>    
</div>
