<?php
$ci = & get_instance();
$ci->load->model('Activity_Log_Model', 'activitylog');
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/activity.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
  
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  User Activity Log</h2>
    <div class="table-responsive">
        <?php
//            print_r($tabledata);
       $atr = 'id="search_form" name="search_form" method="GET"';
//        echo form_open("internal_user/activity_log", $atr);
        echo form_open("reports_finance/activity_log", $atr);
        ?>   

        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                
                                   
                        Select Any One
                    </td>
                    <td>
                        <?php
                     
                        
                        $module[''] = 'Select';
                        foreach ($activity_module as $act_module):
                            $module[$act_module['module_id']] = $act_module['module_name'];
                        endforeach;
//                        unset($module_options['COMPACT']);
//                        print_r($module_options);
                        $attr_js = 'id="module_id" ';
                        echo form_dropdown('module', $module, $this->input->get('module'), $attr_js);
                        ?>
                    </td>
                    
                </tr>
                
                <tr class ="staff" style="display:none">
                    <td id="td_heading"> 
                        <strong>Search By Internal Staff Name</strong>
                    </td>
                    
                    
                    <td>
                        <?php                           
                        $internal_staff = array(
                                                'name'=>'internal_staff',
                                                'id'=>'internal_staff',
                                                'value'=>$this->input->get('internal_staff')
                                               );
                                            echo form_hidden('user_id', $this->input->get('user_id'), 'user_id');
                                            echo form_input($internal_staff);
                        
                        ?>
                       <div style='color:blue; font-size:10px;'>Enter minimum of 4 characters to search</div>
                        <span id="internal_staff_list_err"></span>
                    </td>
                </tr>
                
                 <tr class='acc_type'>
                    <td class="td_heading" width="20%">Select Account Type:</td>
                    <td colspan="3">
                        <?php
                        $account_type = array(
                            '' => 'Select',
                            '1' => 'Individual',
                            '2' => 'Company'
                        );
                        $js = 'id="account_type"';
                        echo form_dropdown('account_type', $account_type,$this->input->get('account_type'), $js);
                        ?>
                    </td>
                </tr>  
                
                <tr class="company_list" style="display:none">
                     <td id="td_heading"> 
                        <strong>Search By Company Name</strong>
                    </td>
                    <td>
                        <?php                     
//                        
                        $company = array(
                                        'name'=>'company',
                                        'id'=>'company',
                                        'value'=>$this->input->get('company')
                                        );
                                            echo form_hidden('com_id', $this->input->get('com_id'), 'com_id');
                                            echo form_input($company);
                        
                        ?>
                    </td>
                </tr>
                
               
                <tr id="trainee_list" style="display:none">
                    <td><strong>Search Trainee by NRIC/FIN No</strong></td>
                    <td colspan="3" width="65%">
                        <?php
                        $indv = array(
                            'id' => 'inv_taxcode',
                            'name' => 'inv_taxcode',
                            'class'=>'upper_case',
                            'value' => $this->input->get('inv_taxcode'),
                            'style' => 'width:200px',
                        );
                        echo form_input($indv);
                         echo form_hidden('invid', $this->input->get('invid'), 'invid');
                        ?>
                         <span id="taxcode_err"></span>
                    </td> 
                </tr>
                
                
                <tr class="course_list" style="display:none">
                     <td id="td_heading"> 
                        <strong>Search By Course Name</strong>
                    </td>
                    <td>
                        <?php                                      
                         $course = array(
                                                'name'=>'course',
                                                'id'=>'course',
                                                'value'=>$this->input->get('course')
                                               );
                                            echo form_hidden('crse_id', $this->input->get('crse_id'), 'crse_id');
                                            echo form_input($course);
                        ?>
                    </td>
                </tr>
                
                <tr class="class_course_list" style="display:none">
                    <td id="td_heading"> 
                        <strong>Select Course Name</strong>
                    </td>
                    <td>
                        <?php  
                        $crs[''] = 'Select';
                        foreach ($course_list as $course):
                            $crs[$course['course_id']] = $course['crse_name'];
                        endforeach;
                        $attr_js = 'id="course_id" ';
                        echo form_dropdown('crs', $crs, $this->input->get('crs'), $attr_js);
                        
                        ?>
                    </td>
                </tr>
           
                <tr class="course_class_list" style="display:none">
                    <td id="td_heading"> 
                        <strong>Select Class Name</strong>
                    </td>
                     <td colspan='3'>
                        <?php
                        $cls_name[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $cls_name[$k] = $v;
                        }

                        $js = 'id="cls_id" ';
                        echo form_dropdown('cls_name', $cls_name, $this->input->get('cls_name'), $js);
                        ?>
                    </td>                    
                </tr>
                <!-- Update Payment module!-->
                
                 <tr class="invoice" style="display:none">
                    <td><strong> Search By Invoice No./ Receipt No.: </td>
                    <td colspan="3" width="65%">
                        <?php
                        $inv = array(
                            'id' => 'inv',
                            'name' => 'inv',
                            'class'=>'upper_case',
                            'value' => $this->input->get('inv'),
                            'style' => 'width:200px',
                        );
                        echo form_input($inv);
                         echo form_hidden('inv', $this->input->get('inv'), 'inv');
                        ?>
                         <span id="taxcode_err"></span>
                    </td> 
                </tr>
                
                 <tr class="password" style="display:none">
                    <td><strong>Search By Username : </td>
                    <td colspan="3" width="65%">
                        <?php
                        $inv = array(
                            'id' => 'pass',
                            'name' => 'pass',
                            'class'=>'upper_case',
                            'value' => $this->input->get('pass'),
                            'style' => 'width:200px',
                        );
                        echo form_input($inv);
                         //echo form_hidden('invoice', $this->input->get('invoice'), 'invoice');
                        ?>
                         <span id="taxcode_err"></span>
                    </td> 
                </tr>
           
               
                
            </tbody>
        </table>
               <div class="push_right btn_srch">
                            <button type="submit" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                   
                        </div>
        <?php echo form_close(); ?>
   
    
    <div class="bs-example">
        <div class="table-responsive">
<!--            <div class="add_button space_style">
                <?php if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['INTUSR'])) { ?>
                    <a href="<?php echo site_url('/internal_user/export_users_page' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export Page Fields</span></a> &nbsp;&nbsp;
                    <a href="<?php echo site_url('/internal_user/export_users_full' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export All Fields</span></a>
                <?php } ?>
            </div>-->
            <div style="clear:both;"></div>
            <table id="listview" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >Module</a></th> 
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Activity On</a></th>
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=role.role_name&o=" . $ancher; ?>" >Updated BY</a></th>
                        
                         <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Date </a></th>
                            <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Action </a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
//                    print_r($tabledata);
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>
                            <tr>
                                <td> 
                                    <?php 
                                        if($data['module_id'] == '1')                                        {
                                            echo "Internal User";
                                        }else if($data['module_id'] == '2'){
                                            echo "Company";
                                        }else if($data['module_id'] == '3'){
                                            echo "trainee";
                                        }else if($data['module_id'] == '4'){
                                            echo "Course";
                                        }else if($data['module_id'] == '5'){
                                            echo "Class";
                                        }else if($data['module_id'] == '6'){
                                            echo "Mark Attendance";
                                        }else if($data['module_id'] == '10'){
                                            echo "Change Payment Mode";
                                        }else if($data['module_id'] == '11'){
                                            echo "Trainer Feedback";
                                        }else if($data['module_id'] == '12'){
                                            echo "My Profile";
                                        }else if($data['module_id'] == '13'){
                                            echo "User Profile";
                                        }else if($data['module_id'] == '14'){
                                            echo "Update Payment";
                                        }else if($data['module_id'] == '15'){
                                            echo "Refund Payment";
                                        }else if($data['module_id'] == '16'){
                                            echo "Reset Password";
                                        }else if($data['module_id'] == '17'){
                                            echo "Discount";
                                        }else if($data['module_id'] == '18'){
                                            echo "TG Number";
                                        }
                                    ?>
                                </td>   
                                <td> <?php 
                                $res = $ci->activitylog->get_act_name($data['module_id'],$data['act_on'],$data['account_type']);
                               if($data['module_id'] == 1 || $data['module_id'] == 12 || $data['module_id'] == 13){
                                echo $res->first_name.'('.$res->tax_code.')';
                               }else if($data['module_id'] == 4){
                                   echo $res->crse_name.'('.$res->course_id.')';
                               }else if($data['module_id'] == 2 || $data['module_id'] == 17 && $data['account_type'] == 2 ){
                                   echo $res->company_name.'('.$res->company_id.')';
                               }else if($data['module_id'] == 3 || $data['module_id'] == 17 && $data['account_type'] == 1){
                                echo $res->first_name.'('.$res->tax_code.')';
                               }else if($data['module_id'] == 5 || $data['module_id'] == 6){
                                echo $res->class_name.'('.$res->class_id.')';
                               }else if($data['module_id'] == 10 && $data['account_type'] == 1){
                                   echo $res->first_name .'('.$res->tax_code.')';
                               }else if($data['module_id'] == 10 && $data['account_type'] == 2){
                                   echo $res->company_name .'('.$res->company_id.')';
                               }else if($data['module_id'] == 11 || $data['module_id'] == 18){
                                    echo $res->first_name .'('.$res->tax_code.')';
                               }else if($data['module_id'] == 14 && $data['account_type']==2 || $data['module_id'] == 15 && $data['account_type']==2){
                                   echo $data['act_on'].'('.' Company Invoice '.')';
                               }else if($data['module_id'] == 14 && $data['account_type']==1 || $data['module_id'] == 15 && $data['account_type']==1){
                                   echo $data['act_on'].'('.' Individual Invoice '.')';
                               }else if($data['module_id'] == 16){
                                     echo $res->first_name.'('.$res->user_name.')';
                               }
                                ?></td>
                                <td> <?php echo $data['first_name'];?></td>                                
                                <td> <?php echo $data['trigger_datetime'];?></td>
                                <td> <a href="<?php echo base_url() . 'reports_finance/activity_log_view/' .$data['id']. '/'.$data['module_id'].'/'. $data['act_on']; ?>">View</a></td>
                            </tr>
                            <?php
                        }
                    }else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>".$err."</label></td></tr>";
                    }
                    ?>      
                </tbody>      
            </table>
        </div>
        <div style="clear:both;"></div><br>
        <ul class="pagination pagination_style">
            <?php echo $pagination; 
//            echo $this->input->get('cls_name');
           ?>
        </ul>
    </div>
</div>
<script>
$("#search_form").submit(function(){
    ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    check_remove_id();
        var self = $(".btn_srch"),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
        ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
});
function check_remove_id(){
        $staff = $('#internal_staff').val();
        if($staff == ''){
           $('#user_id').val(''); 
        }
       
    }
</script>