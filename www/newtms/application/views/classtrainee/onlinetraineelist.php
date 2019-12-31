<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
    $role_check = '<?php echo $this->data['user']->role_id; ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/onlinetraineelist.js"></script>
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/> Online Class Trainee Enrollment List</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("class_trainee/online_trainee", $atr);
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
                        $check = $this->input->get('search_select');
                        if ($check) {
                            $checked = ( $check == 1) ? TRUE : FALSE;
                        }
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
                            'class'=>'upper_case',
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
                            'class'=>'upper_case',
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
                        <span id="trainee_err"></span>
                    </td>
                </tr>
              
                
<!--                <tr>
                    <?php if ($this->data['user']->role_id != 'COMPACT') { ?>
                    <td class="td_heading" width="15%"> Company Name:</td>
                    <td colspan="4" width="47%">
                        <?php
                        $company = array(
                            'name' => 'company_name',
                            'id' => 'company_name',
                            'value' => $this->input->get('company_name'),
                            'style'=>'width:200px;',
                            'class'=>'upper_case',
                            'autocomplete'=>'off'
                        );
                        echo form_input($company);
                        echo form_hidden('company_id', $this->input->get('company_id'), $id='company_id');
                        ?>
                        <span id="company_name_err"></span>
                    <?php } else { ?>
                        
                    
                    <td colspan="5">
                    <?php } ?>
                        <span class="pull-right">
                            <button type="submit" value="Search"  class="btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </td>
                </tr>-->
              
                <tr>
                    <td class="td_heading">Total Trainee Enrolled from Public Portal : <?php echo $trainee_count;?></td>
                    <td><span id="search_error"></span></td>
                    <td colspan="2">
                        <span class="pull-right">
                            <button type="submit" value="Search"  class="btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
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
                    <a href="<?php echo base_url(); ?>class_trainee/export_online_classtrainee_page<?php echo $export_url; ?>" class="small_text1">
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export Page Fields</span>
                    </a> &nbsp;&nbsp; 
                    <a href="<?php echo base_url(); ?>class_trainee/export_online_classtrainee_full<?php echo $export_url; ?>" class="small_text1"><span class="label label-default black-btn">
                            <span class="glyphicon glyphicon-export"></span>Export All Fields</span>
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
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = 'class_trainee';
                    ?>
                    <tr>
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=tu.tax_code&o=" . $ancher; ?>">NRIC/FIN No.</a></th>
                        <th width="15%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=tup.first_name&o=" . $ancher; ?>">Name</a></th>
                        <th width="15%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=c.crse_name&o=" . $ancher; ?>">Course / Class Detail</a></th>
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=cc.class_start_datetime&o=" . $ancher; ?>">Class Duration</a></th>
<!--                        <th width="5%" class="th_header">Company Name</th>-->
                        <th width="10%" class="th_header">Referred By</th>
                        <th width="5%" class="th_header">Certi. Coll.</th>
                        <th width="20%" class="th_header">Class Status</th>
                        <th width="9%" class="th_header"><a href="<?php echo base_url() . $pageurl . $sort_url . "&f=ce.payment_status&o=" . $ancher; ?>">Payment</a></th>
<!--                        <th width="20%" class="th_header">Action</th>-->
                    </tr>
                </thead>
                 <tbody>
                    <?php
                    $err_msg = 'Kindly Apply Filter To Fetch The Trainees.';
                    if (!empty($_GET)) {
                        $err_msg = 'No data available for the search criteria entered.';
                    }
                    if (!empty($tabledata)) 
                    {
//                        print_r($tabledata);
                        foreach ($tabledata as $row) 
                        {   
                            if (!empty($row['SalesExec'])) 
                            {
                                $salesList ="";
                                foreach ($row['SalesExec'] as $rowdata) 
                                {
                                    $salesList = $rowdata['first_name'] . ' ' . $rowdata['last_name'] ; 
                                }
                            } 
                            else 
                            {
                                $salesList = 'No sales executive is assigned.';
                            }
                            $cur_date = strtotime(date('Y-m-d'));
                            $class_end_datetime = date("Y-m-d", strtotime($row['class_end_datetime']));
                            $class_end_datetime_str = strtotime($class_end_datetime);
                            $feedback = '';
                            $result_text='';
                             $row['taxcode'];
                            if ($cur_date >= $class_end_datetime_str) 
                            {
                                if ($this->data['user']->role_id == 'ADMN') 
                                {
                                    $feedback = '<br/><a id="training_update" href="#ex7" rel="modal:open" data-course="' . $row['course_id'] . '" data-class="' . $row['class_id'] . '" data-user="' . $row['user_id'] .  '"data-payment="'.$row['pymnt_due_id']. '" class="training_update small_text1">Feedback</a>';
                                }
                                    $result_text = !empty($row['feedback_answer']) ? '<br/><b>Result:</b> ' . $row['feedback_answer'] : '';
                            }  
                           
                             $name = json_decode($row['referrer']);
                             ?>
                            <tr>
                                <td><?php echo $row['taxcode'] ;?></td>

                                <td class="name">
                                    <?php // echo $row['name']. $ref_name;?> 
                                    <?php echo $row['name'];?> 
                                    
                                </td>
                                <td><?php echo $row['course_class'] ;?></td>
                                <td><?php echo $row['duration']; ?></td>
<!--                                <td><?php echo $row['enroll_mode'] ?></td>-->
                                <td><?php //echo $salesList;//implode("<br>",$salesList).
                                if($row['friend_id']=='' && $row['referrer']==''){
                                    echo 'SELF';
                                }
                                else if($row['friend_id']!='')
                                {
                                 
                                    ?>
                                    <a href="#ex145<?php echo $row['user_id'].''.$row['friend_id'].''.$row['class_id'];?>" rel="modal:open" 
                                       style="color: brown;">
                                        <span class=""><?php $friend_details = json_decode($row['friend_details']);
                                        echo $friend_details->first_name;?></span>
                                    </a>
                                    <div class="modal_333" id="ex145<?php echo $row['user_id'].''.$row['friend_id'].''.$row['class_id'];?>" style="display:none; max-height: 166px;width:45%;overflow-y: hidden;min-height: 155px;">
                                        <h2 class="panel_heading_style">Referrer`s Details</h2>
                                        <div class="table-responsive" style="height: 300px;">
                                            <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                                                <thead>
                                                    <tr>
                                                        <th width="30%">Name</th>
                                                        <th width="30%">Email</th>
                                                        <th width="20%">Contact</th>
                                                        <th width="20%">Company</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if($friend_details->company_name!='')
                                                    {
                                                        $company = $friend_details->company_name;
                                                    }else {
                                                        $company = $name->company;
                                                    }
                                                        echo "<tr>
                                                            <td>" . $friend_details->first_name . "</td>
                                                            <td>" . $friend_details->registered_email_id . " </td>
                                                            <td>" . $friend_details->contact_number . "</td>
                                                             
                                                            <td>" . $company . "</td>
                                                        </tr>";
                                                   
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>             
                                    
                                <?php }else{
                                
                                     
                                    if($name->name!=""){?>
                                    <a href="#ex144<?php echo $row['user_id'].''.$row['class_id'];?>" rel="modal:open" 
                                       style="color: brown;">
                                        <span class=""><?php echo Referrer;?></span>
                                    </a><?php }else { echo " "; } ?>                                    
                                    <div class="modal_333" id="ex144<?php echo $row['user_id'].''.$row['class_id'];?>" style="display:none; max-height: 166px;width:45%;overflow-y: hidden;min-height: 155px;">
                                        <h2 class="panel_heading_style">Referrer`s Details</h2>
                                        <div class="table-responsive" style="height: 300px;">
                                            <table style="width:100%; margin:0 auto;" class="table table-striped cpayment_received">
                                                <thead>
                                                    <tr>
                                                        <th width="30%">Name</th>
                                                        <th width="30%">Email</th>
                                                        <th width="20%">Contact</th>
                                                        <th width="20%">Company</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    
                                                        echo "<tr>
                                                            <td>" . $name->name . "</td>
                                                            <td>" . $name->email . " </td>
                                                            <td>" . $name->contact . "</td>
                                                            <td>" . $name->company . "</td>
                                                        </tr>";
                                                   
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                              
                                <?php } ?>
                                </td>
                                
                                <td><?php echo $row['certi_coll'] ;?></td>
                                <td><?php echo $row['status_text'].'<br />'.$row['end_class'].'<br />'                          
                                 . $result_text;?>  
                                </td>
                                <td><?php echo $row['paid'] ;?></td>
<!--                                <td><?php echo $row['action_link'];?></td>-->
                             </tr>
                
                           
                       <?php }
                    } 
                    else 
                    {
                        echo '<tr><td colspan="9" class="error" style="text-align: center">' . $err_msg . '</td></tr>';
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
<div class="modal1_0001" id="ex9" style="display:none;height:200px;min-height: 200px;">
    <h2 class="panel_heading_style">Update TG#</h2>
    <table class="table table-striped">
        <tbody>
            <?php
            $data = array(
                'id' => 'h_class',
                'type' => 'hidden',
                'name' => 'h_class',
            );
            echo form_input($data);
            $data = array(
                'id' => 'h_user',
                'type' => 'hidden',
                'name' => 'h_user',
            );
            echo form_input($data);
            ?>
            <tr>
                <td class="td_heading">TG#:</td>
                <td>
                    <?php
                        echo form_input('tg_number', $this->input->post('tg_number'), ' id="tg_number"');
                    ?>
                <span id="tg_number_err"></span>
                </td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cance89">
        <span href="#ex9" rel="modal:close"><button class="btn btn-primary subsidy_save" type="button">Save</button></span>
    </div>
</div>
<div class="modal_3" id="ex8" style="display:none;">
    <h2 class="panel_heading_style">Total Payment Received Details</h2>
    <table class="no_border_table">
        <tbody>
            <tr>
                <td class="td_heading">Payment Made On:</td>
                <td><span class="r_recd_on"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Mode of Payment:</td>
                <td><span class="r_mode"></span></td>
            </tr>
            <tr>
                <td class="td_heading">Unit Class Fees:</td>
                <td><span class="r_class_fees"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading"><span class="r_dis_label"></span>Total Discount @ <span class="r_dis_rate"></span>%:</td>
                <td><span class="r_dis_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Total Subsidy:</td>
                <td><span class="r_subsidy_amount"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Total Amount Due:</td>
                <td><span class="r_after_gst"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Total GST @ <span class="r_gst_rate"></span>% (<span class="r_gst_label"></span>):</td>
                <td><span class="r_total_gst"></span> SGD</td>
            </tr>
            <tr>
                <td class="td_heading">Net Due:</td>
                <td><span class="r_net_due"></span> SGD</td>
            </tr>
        </tbody>
    </table>
    <div class="popup_cance89">   <a class="payment_recd_href" href="#"><button type="button" class="btn btn-primary">Print</button></a></div>
</div>
<div class="modal_991" id="ex5" style="display:none;">
    <p>
    <div class="classtraineeexcel"><img src="<?php echo base_url(); ?>assets/images/classtraineeexcel.png" border="0" width="907px;"></div>
</p>
</div>
<div class="modal_991" id="ex4" style="display:none;">
    <p>
    <div class="classtraineeexcel1"><img src="<?php echo base_url(); ?>assets/images/classtraineeexcel1.png" border="0" width="1993px;"></div>
</p>
</div>
<?php
$atr = 'id="trainer_feedback_form" name="trainer_feedback_form" ';
echo form_open("class_trainee/trainer_feedback", $atr);
?>
<div class="modal1_050" id="ex7" style="width:50%">
    <h2 class="panel_heading_style">Trainer Feedback</h2>
    <center> <span id="skm" style="display:none"></span></center>
    
    <span id="tbl" style="display:none">
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('CERTCOLDT'); ?>:</td>
                <td>
                    <?php
                    $collected_on = array(
                        'name' => 'CERTCOLDT',
                        'id' => 'collected_on',
                        'placeholder' => 'dd-mm-yyyy',
                        'readonly' => 'readonly',
                    );
                    echo form_input($collected_on);
                    ?>                    
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('SATSRATE'); ?>:</td>
                <td>                    
                    <?php
                    $satisfaction_rating = array('' => 'Select', '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5);
                    $satisfaction_rating_attr = 'id="satisfaction_rating"';
                    echo form_dropdown('SATSRATE', $satisfaction_rating, '', $satisfaction_rating_attr);
                    ?>   
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('CERTCOM1'); ?>:</td>
                <td>
                    <?php
                    $CERTCOM1_YES = array(
                        'name' => 'CERTCOM1',
                        'value' => 'Y',
                        'id' => 'CERTCOM1_YES'
                    );
                    $CERTCOM1_NO = array(
                        'name' => 'CERTCOM1',
                        'id' => 'CERTCOM1_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($CERTCOM1_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($CERTCOM1_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('APPKNLSKL'); ?>?</td>
                <td>                    
                    <?php
                    $APPKNLSKL_YES = array(
                        'name' => 'APPKNLSKL',
                        'value' => 'Y',
                        'id' => 'APPKNLSKL_YES'
                    );
                    $APPKNLSKL_NO = array(
                        'name' => 'APPKNLSKL',
                        'id' => 'APPKNLSKL_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($APPKNLSKL_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($APPKNLSKL_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('EXPJOBSCP'); ?>?</td>
                <td>                    
                    <?php
                    $EXPJOBSCP_YES = array(
                        'name' => 'EXPJOBSCP',
                        'value' => 'Y',
                        'id' => 'EXPJOBSCP_YES'
                    );
                    $EXPJOBSCP_NO = array(
                        'name' => 'EXPJOBSCP',
                        'id' => 'EXPJOBSCP_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($EXPJOBSCP_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($EXPJOBSCP_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('RT3MNTHS'); ?>?</td>
                <td>                   
                    <?php
                    $RT3MNTHS_YES = array(
                        'name' => 'RT3MNTHS',
                        'value' => 'Y',
                        'id' => 'RT3MNTHS_YES'
                    );
                    $RT3MNTHS_NO = array(
                        'name' => 'RT3MNTHS',
                        'id' => 'RT3MNTHS_NO',
                        'value' => 'N',
                    );
                    ?>              
                    <?php echo form_radio($RT3MNTHS_YES); ?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($RT3MNTHS_NO); ?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('DTCOMMEMP'); ?>:</td>
                <td>
                    <?php
                    $new_entrance = array(
                        'name' => 'DTCOMMEMP',
                        'id' => 'new_entrance',
                        'placeholder' => 'dd-mm-yyyy',
                        'readonly' => 'readonly',
                    );
                    echo form_input($new_entrance);
                    ?>
                </td>
            </tr>

            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('COMYTCOM'); ?>?</td>
                <td>                    
                    <?php
                   $COMYTCOM_C = array(
                        'name' => 'COMYTCOM',
                        'value' => 'C',
                        'id' => 'COMYTCOM_C',
                    );
                    $COMYTCOM_NYC = array(
                        'name' => 'COMYTCOM',
                        'id' => 'COMYTCOM_NYC',
                        'value' => 'NYC',
                    );
                    
                    $COMYTCOM_EX = array(
                        'name' => 'COMYTCOM',
                        'id' => 'COMYTCOM_EX',
                        'value' => 'EX',
                    );
                    $COMYTCOM_ABS = array(
                        'name' => 'COMYTCOM',
                        'id' => 'COMYTCOM_ABS',
                        'value' => 'ABS',
                    );
                    $COMYTCOM_2NYC = array(
                        'name' => 'COMYTCOM',
                        'id' => 'COMYTCOM_2NYC',
                        'value' => '2NYC',
                    );
                    ?>              
                      <?php echo form_radio($COMYTCOM_C); ?> Competent <br/>
                    <?php echo form_radio($COMYTCOM_NYC); ?> Not Yet Competent <br/>
                  
                    <?php echo form_radio($COMYTCOM_EX); ?> Exempted <br/>                    
                    <?php echo form_radio($COMYTCOM_ABS); ?> Absent<br/>
                    <?php echo form_radio($COMYTCOM_2NYC); ?> Twice Not Competent
                </td>
               
            </tr>
            <tr>
                <td colspan="2" class="td_heading">
                    <span style="vertical-align:top;"><?php echo get_catname_by_parm('COMMNTS'); ?>:</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>                        
                        <?php
                        $data = array(
                            'name' => 'COMMNTS',
                            'id' => 'COMMNTS',
                            'rows' => '1',
                            'cols' => '60',
                            'style' => 'width:70%',
                            'class' => 'upper_case',
                            'maxlength' => '250'
                        );

                        echo form_textarea($data);
                        ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="color: blue;">
        <span>1 - Strongly Disagree</span>&nbsp;&nbsp;
        <span>2 - Disagree</span>&nbsp;&nbsp;
        <span>3 - Neutral</span>&nbsp;&nbsp;
        <span>4 - Agree</span><br/>
        <span>5 - Strongly Agree</span>
    </div>
    <div class="popup_cance89">        
        <div class="popup_cancel9">
            <div rel="modal:close">
                <button class="btn btn-primary" type="submit">Save</button>&nbsp;&nbsp;
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
    </div>
    </span>
    <br>
    <?php 
echo form_hidden('query_string', $export_url);
echo form_hidden('page', $this->uri->segment(2));
?>
<?php echo form_close(); ?>
</div>
<div id="ex6" class="modal1_trainee_feedback modal" width="85%">
    <?php
    $atr = 'id="traineefeedbackForm" name="validate_form"';
    echo form_open('class_trainee/trainee_feedback', $atr);
    ?> 
    <p>
    <h2 class="panel_heading_style" style = "width:100%" >Trainee Feedback</h2>  
    <div id ="trainee_fdbk">
    <table class="table table-striped">
        <?php
        $options = array('' => 'Select', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
        ?>
        <tbody>
            
            <tr>
                <td colspan="2" class="td_heading"><strong><u> <?php echo $trainee_feedback['FDBCK01']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>     
                <td colspan="2">1.<?php echo $trainee_feedback['Q01']['category_name']; ?>
                    <?php
                    $atr = 'id="Q01" class="feed"';
                    echo form_dropdown('Q01', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>     
                <td colspan="2">2.<?php echo $trainee_feedback['Q02']['category_name']; ?>
                    <?php
                    $atr = 'id="Q02" class="feed"';
                    echo form_dropdown('Q02', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $trainee_feedback['Q03']['category_name']; ?>
                    <?php
                    $atr = 'id="Q03" class="feed"';
                    echo form_dropdown('Q03', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $trainee_feedback['Q04']['category_name']; ?>
                    <?php
                    $atr = 'id="Q04" class="feed"';
                    echo form_dropdown('Q04', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">5.<?php echo $trainee_feedback['Q05']['category_name']; ?>
                    <?php
                    $atr = 'id="Q05" class="feed"';
                    echo form_dropdown('Q05', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">6.<?php echo $trainee_feedback['Q06']['category_name']; ?>
                    <?php
                    $atr = 'id="Q06" class="feed"';
                    echo form_dropdown('Q06', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading"><strong><u><?php echo $trainee_feedback['FDBCK02']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>
                <td colspan="2">1.<?php echo $trainee_feedback['Q07']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q07" class="feed"';
                    echo form_dropdown('Q07', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">2.<?php echo $trainee_feedback['Q08']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q08" class="feed"';
                    echo form_dropdown('Q08', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">3.<?php echo $trainee_feedback['Q09']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q09" class="feed"';
                    echo form_dropdown('Q09', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">4.<?php echo $trainee_feedback['Q10']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q10" class="feed"';
                    echo form_dropdown('Q10', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">5.<?php echo $trainee_feedback['Q11']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q11" class="feed"';
                    echo form_dropdown('Q11', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading"><strong><u>C. <?php echo $trainee_feedback['FDBCK03']['category_name'] ?></u></strong> </td>
            </tr>
            <tr>
                <td colspan="2">1. <?php echo $trainee_feedback['Q12']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q12" class="feed"';
                    echo form_dropdown('Q12', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">2.  <?php echo $trainee_feedback['Q13']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q13" class="feed"';
                    echo form_dropdown('Q13', $options, '', $atr);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">3.  <?php echo $trainee_feedback['Q14']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q14" class="feed"';
                    echo form_dropdown('Q14', $options, '', $atr);
                    ?> 
                </td>
            </tr>
            <tr>
                <td colspan="2">4.  <?php echo $trainee_feedback['Q15']['category_name']; ?>:
                    <?php
                    $atr = 'id="Q15" class="feed"';
                    echo form_dropdown('Q15', $options, '', $atr);
                    
                    ?> 
                    </td>
            </tr>
            <tr>
                <td colspan="2"><strong>Your satisfaction rating of the training program:</strong>
                    <?php 
//                     $atr = 'id="rating"';
//                    echo form_dropdown('rating', $options,'',$atr); ?>
                    <input type='text' name="rating" id='rating'  readonly/>
            </tr>
            <tr>
                <td class="td_heading">Any other remarks:</td>
                <td>
                    <textarea maxlength="500" rows="1" cols="100" name="remarks" id="remarks" class="upper_case"></textarea>                                   
                                     
                    <span style="float:right;">
                        <input type="hidden" id ="trainee_class_id" name="trainee_class_id" value=""/>
                        <input type="hidden" id ="trainee_course_id" name="trainee_course_id" value=""/>
                        <input type="hidden" id ="trainee_user_id" name="trainee_user_id" value=""/>
                        <input type="hidden" id ="action" name="action" value="save_trainee"/>
                        <button class="btn btn-primary" type="submit" id="save">Save</button>
                        <a href="#" rel="modal:close">
                            <button class="btn btn-primary" type="button">Close</button>
                        </a>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <?php 
echo form_hidden('query_string', $export_url);
echo form_hidden('page', $this->uri->segment(2));
?>
<?php form_close(); ?>
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>-->
 
<script>
    $('.training_update').click(function(){
        
   
            if($('.feed').val() == '')
            {
               
                $('#rating').val("");
            }
     });
// we used jQuery 'keyup' to trigger the computation as the user type
$('.feed').change(function () {
 
    // initialize the sum (total price) to zero
    var sum = 0;
   
     
    // we use jQuery each() to loop through all the textbox with 'price' class
    // and compute the sum for each loop
    var i=0;
        $('.feed').each(function() {
        sum += Number($(this).val());
        i++;
    });
     var average= Math.round(sum/i);
     
    // set the computed value to 'totalPrice' textbox
    
    $('#rating').val(average);
     
});
</script>
</p>
</div>
</div>
