<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
    $tenant_id = '<?php echo $this->data['user']->tenant_id; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classtraineelist.js"></script>
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> Enrolment Report TPG</h2>
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
               
               
            </tbody>
        </table>
    </div><br>
    <div class="bs-example">
        <div class="table-responsive">
            <?php
            if (!empty($tabledata) || !empty($class_status)) { ?>
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
                    <a href="<?php echo base_url(); ?>class_trainee/export_classtrainee_full<?php echo $export_url; ?>" class="small_text1" onclick="return exportValidate()><span class="label label-default black-btn">
                   
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export All Fields</span>
                    </a>
                </div>                  
            <?php } ?>
        </div>

    </div>
    <?php echo form_close(); ?>
    <?php ?>
    <div class="bs-example">
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                   
                    <tr>
                        <th width="5%" class="th_header">Trainee ID Type</th>
                        <th width="5%" class="th_header">Trainee ID</th>
                        <th width="5%" class="th_header">Date Of Birth</th>
                        <th width="5%" class="th_header">Trainee Name</th>
                        <th width="5%" class="th_header">Course Run</th>
                        <th width="5%" class="th_header">Trainee Email</th>
                        <th width="5%" class="th_header">Trainee Phone Country Code</th>
                        <th width="5%" class="th_header">Trainee Phone Area Code</th>
                        <th width="5%" class="th_header">Trainee Phone</th>
                        <th width="5%" class="th_header">Sponsorship Type</th>
                        
                        <th width="5%" class="th_header">Employer UEN</th>
                        <th width="5%" class="th_header">Employer Contact Name</th>
                        <th width="5%" class="th_header">Employer Phone Country Code</th>
                        <th width="5%" class="th_header">Employer Phone Area Code</th>
                        <th width="5%" class="th_header">Employer Phone</th>
                        <th width="15%" class="th_header">Employer Contact Email</th>
                        <th width="5%" class="th_header">Course Fee Discount Amount</th>
                        <!--<th width="5%" class="th_header">Fee Collection Status</th>-->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $err_msg = 'There are no trainees enrolled to any class currently.';
                    if (!empty($_GET)) {
                        $err_msg = 'No data available for the search criteria entered.';
                    }
                    if (!empty($tabledata)) 
                    {
                        foreach ($tabledata as $row) 
                        {   
                            ?>
                   <td><?php echo $row['Trainee ID Type'] ;?></td>
                   <td><?php echo $row['Trainee ID'] ;?></td>
                   <td><?php echo $row['Date of Birth'] ;?></td>
                   <td><?php echo $row['Trainee Name'] ;?></td>
                   <td><?php echo $row['Course Run'] ;?></td>
                   <td><?php echo $row['Trainee Email'] ;?></td>
                   <td><?php echo $row['Trainee Phone Country Code'] ;?></td>
                   <td><?php echo $row['Trainee Phone Area Code'] ;?></td>
                   <td><?php echo $row['Trainee Phone'] ;?></td>
                   <td><?php echo $row['Sponsorship Type'] ;?></td>
                   <td><?php echo $row['Employer UEN'] ;?></td>
                   <td><?php echo $row['Employer Contact Name'] ;?></td>
                   <td><?php echo $row['Employer Phone Country Code'] ;?></td>
                   <td><?php echo $row['Employer Phone Area Code'] ;?></td>
                   <td><?php echo $row['Employer Phone'] ;?></td>
                   <td><?php echo $row['Employer Contact Email'] ;?></td>
                   <td><?php echo $row['Course Fee Discount Amount'] ;?></td>
                   <!--<td><?php //echo $row['Fee Collection Status'] ;?></td>-->
                   
                       <?php }
                    } 
                    else 
                    {
                        $err_msg=$error_msg ? $error_msg : $err_msg;/// added by shubhranshu to remove the classtrainee list on 26/11/2018
                        echo '<tr><td colspan="17" class="error" style="text-align: center">' . $err_msg . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div style="clear:both;"></div><br>
        <ul class="pagination pagination_style">
            <?php echo $pagination; ?>
        </ul>
    </div>
</div>
</div>
