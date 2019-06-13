<?php
$user = $this->session->userdata('userDetails');
$tenant_id = $user->tenant_id;
$ci = & get_instance();
$ci->load->model('Activity_Log_Model', 'activitylog');
$ci->load->model('Internal_User_Model', 'internaluser');
$ci->load->model('course_model');
$ci->load->model('class_model');
$ci->load->model('settings_model');
$this->load->helper('common_helper');
$this->load->helper('metavalues_helper');

if($res->module_id == 4){
    $role_array = array("COMPACT","TRAINER"); 
}

$result = (array)json_decode($res->previous_details);
$pers_detail = $ci->activitylog->get_personal_details($res->act_on,$res->tenant_id);
$act_by = $ci->activitylog->get_personal_details($res->act_by,$res->tenant_id);

//print_r($result);
//print_r($res);

//get_course_class_name_autocompleteget_course_class_name_autocomplete_r($result);
?>
<!--<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>-->
<?php if($res->module_id == 18){?>
    <div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - TG Number</h2>
      <div class="bs-example">
        <div class="table-responsive">
              <?php $act_on = $ci->activitylog->user_details($result['user_id']);?>
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                             
                             <strong style="color:green;"> TG Number of Trainee <?php echo $act_on->first_name; ?> (<?php echo $act_on->tax_code; ?> )
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                        
                    </tr>
                    <tr>
                        <td><strong>Trainee Name : </strong><?php echo $act_on->first_name; ?> (<?php echo $act_on->tax_code; ?> )</td>
                    </tr>
                     <tr>
                        <td><strong>Class Name : </strong><?php 
                       $class = $ci->activitylog->class_name($result['class_id']) ;
                        echo $class->class_name; ?></td>
                    </tr>
                    <tr>
                        <td><strong>TG Number : </strong><?php  echo $result['tg']; ?> </td>
                    </tr>
                    
                  
                </tbody>
            </table><br/>
                    
                   
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 17 && $res->account_type ==1){?>
    <div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Trainee Discount</h2>
      <div class="bs-example">
        <div class="table-responsive">
              <?php $act_on = $ci->activitylog->user_details($res->act_on);?>
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                              
                             <strong style="color:green;">
                             Trainee Discount - <?php $trainee = $ci->activitylog->user_details($result['individual_id']);
                             echo $trainee->first_name  .'('.$trainee->tax_code.')';
                             ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                    </tr>
                </tbody>
            </table>
           <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Trainee Individual Discount </h2>
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
                  foreach($result['individual_discount'] as $row){
                        $val = explode('_',$row->discount);
                        $course_name = $ci->activitylog->get_course_name($val[0]);
                        $discount = explode('#',$val[1]); 
                        $Discount_Percent = $discount[0]; 
                        $Discount_Amount  = $discount[1];
                       
                        echo "<tr>
                                        <td>" . $course_name->crse_name . "</td>
                                        <td>" . number_format($Discount_Percent, 2, '.', '') . " %</td>
                                        <td>$ " . number_format($Discount_Amount, 2, '.', '') . "</td>
                                    </tr>";
                    }
                ?>
                </tbody>
            </table>
                    
                   
        </div>
    </div>


</div>

<div style="clear:both;"></div>
<?php }else if($res->module_id == 17 && $res->account_type ==2){?>
    <div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Company Discount</h2>
      <div class="bs-example">
        <div class="table-responsive">
              <?php $act_on = $ci->activitylog->user_details($res->act_on);?>
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                              
                             <strong style="color:green;">
                             Company Discount - <?php $company = $ci->activitylog->company_name($result['company_id']);
                             echo $company->company_name  .'('.$result['company_id'].')';
                             ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                    </tr>
                </tbody>
            </table>
           <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png">Company Discount on Courses</h2>
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
//                     print_r($result['company_discount']);                  
                  foreach($result['company_discount'] as $row){
                        $val = explode('_',$row->discount);
                        $course_name = $ci->activitylog->get_course_name($val[0]);
                        $discount = explode('#',$val[1]); 
                        $Discount_Percent = $discount[0]; 
                        $Discount_Amount  = $discount[1];
                       
                        echo "<tr>
                                        <td>" . $course_name->crse_name . "</td>
                                        <td>" . number_format($Discount_Percent, 2, '.', '') . " %</td>
                                        <td>$ " . number_format($Discount_Amount, 2, '.', '') . "</td>
                                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
                    
                   
        </div>
    </div>


</div>

<div style="clear:both;"></div>
<?php }else if($res->module_id == 16){?>
    <div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Password Reset</h2>
      <div class="bs-example">
        <div class="table-responsive">
              <?php 
//              $act_on = $ci->activitylog->($res->act_on);
                $user = $ci->activitylog->user_details($result['user_id']);
              ?>
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                           
                             <strong style="color:green;">Password of - <?php echo $user->first_name; ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo$result['date_time'];?></strong>
                         </td>
                        
                    </tr>
                    <tr>
                    <?php if($result['company_id']!=''){
                        $company = $ci->activitylog->company_name($result['company_id']);
                        ?>
                  
                        <td><strong>company name : <?php echo $company->company_name.' ('.$company->company_id.')'; ?></strong></td>
                    </tr>
                   
                  
                    <tr>
                        <td><strong>Person Name: </strong><?php  echo $user->first_name; ?> &nbsp;(<?php echo $user->user_id;?>)</td>
                    </tr>
                    
                    <?php }else{?>
                    <tr>
                        <td><strong>Person Name: </strong><?php  echo $user->first_name; ?> &nbsp;(<?php echo $user->tax_code;?>)</td>
                    </tr>
                    <?php } ?>
                   
                    
           
                </tbody>
            </table><br/>
                    
                   
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 15 && $res->account_type ==2){?>
    <div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Refund Payment - Company Invoice</h2>
      <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                             <strong style="color:green;">Individual Invoice - <?php echo $result['company_details']->invoice_id; ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                        
                    </tr>
                    
                    <tr>
                        <td><strong>Invoice Id: <?php echo $result['company_details']->invoice_id;; ?></strong></td>
                    </tr>
                  
                    <tr>
                        <td><strong>Mode of Payment: </strong><?php echo $result['company_details']->mode_of_refund; ?></td>
                    </tr>
                    
                   
                     <tr>
                        <td> <strong>Amount Received : </strong><?php echo $result['company_details']->amount_refund; ?></td>
                    </tr>
                    <?php if($result['company_details']->mode_of_refund == 'CHQ'){?>
                    <tr>
                        <td> <strong>Bank Name : </strong><?php echo $result['company_details']->bank_name; ?></td>
                    </tr>
                    <tr>
                        <td> <strong>Cheque No : </strong><?php echo $result['company_details']->cheque_number; ?></td>
                    </tr>
                    <tr>
                        <td> <strong>Cheque Date : </strong><?php echo $result['company_details']->cheque_date; ?></td>
                    </tr>
                     <tr>
                        <td><strong>Refund Reason: </strong>
                      <?php 
                       if ($result['company_details']->refnd_reason =='OTHERS') {
                        echo $result['company_details']->refnd_reason_ot;
                        
                       }else{
                         $reason = get_param_value($result['company_details']->refnd_reason);
                        echo $reason->category_name;
                       }
                      ?></td>
                    </tr>
           
                </tbody>
            </table><br/>
                    
                    <?php } ?>
                    <table class="table table-striped">
                        <tr><td>Trainee Name</td><td>Received Amount</td><td>Date</td></tr>
                            <?php foreach($result['details'] as  $row){?>
                        <tr><td><?php 
                           $trainee_name = $ci->activitylog->user_details($row->user_id);
                                          echo $trainee_name->first_name;?> ( <?php echo $trainee_name->tax_code; ?> )</td><td> <?php echo $row->refund_amount;?> </td><td><?php echo $row->refund_date;?></td></tr>
                        <?php $i++;}?>
                        </table>
                 
            
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 15 && $res->account_type ==1){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Refund Payment - Individual Invoice</h2>
      <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                             <strong style="color:green;">Individual Invoice - <?php echo $result['invoice_id']; ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                        
                    </tr>
                    
                    <tr>
                        <td><strong>Invoice Id: <?php echo $result['invoice_id']; ?></strong></td>
                    </tr>
                    <tr>
                         <td><strong>Trainee Name: </strong><?php $act_on = $ci->activitylog->user_details($result['user_id']);
                                  echo $act_on->first_name;?> ( <?php echo $act_on->tax_code; ?> )</td>                        
                    </tr>
                    <tr>
                        <td><strong>Mode of Payment: </strong><?php echo $result['mode_of_refund']; ?></td>
                    </tr>
                    <?php
                    if($result['mode_of_refund'] == 'SFC_SELF' || $result['mode_of_refund'] == 'SFC_ATO'){
                    ?>
                    <tr>
                        <td><strong>Other Mode of Payment: </strong><?php echo $result['other_amount_refund']; ?></td>
                    </tr>
<!--                    <tr>
                        <td><strong> Amount Received : </strong><?php echo $result['amount_recd']; ?></td>
                    </tr>-->
                    <tr>
                        <td><strong>SFC Claimed Amount: </strong><?php echo $result['sfc_claimed']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cash Amount: </strong><?php echo $result['other_amount_refund']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Bank Name: </strong><?php echo $result['bank_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Number: </strong><?php echo $result['cheque_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Date: </strong><?php echo $result['cheque_date']; ?></td>
                    </tr>
                     
                    
                <?php }else if($result['mode_of_refund'] == 'CHQ'){ ?>
                    <tr>
                        <td><strong>Bank Name: </strong><?php echo $result['bank_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Number: </strong><?php echo $result['cheque_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Date: </strong><?php echo $result['cheque_date']; ?></td>
                    </tr>
                <?php }?>
                     <tr>
                        <td> <strong>Amount Received : </strong><?php echo $result['amount_refund']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Refund Reason: </strong>
                      <?php 
                       if ($result['refnd_reason'] =='OTHERS') {
                        echo $result['refnd_reason_ot'];
                        
                       }else{
                         $reason = get_param_value($result['refnd_reason']);
                        echo $reason->category_name;
                       }
                      ?></td>
                    </tr>
             
                    
                   
                    
                     
                </tbody>
            </table><br/>
            
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php } else if($res->module_id == 14 && $res->account_type == 2){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log View - Update Payment - Company Invoice</h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                             <strong style="color:green;">Company Invoice - <?php echo $result['company_details']->invoice_id; ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                        
                    </tr>
                    
                    <tr>
                        <td><strong>Invoice Id: <?php echo $result['company_details']->invoice_id; ?></strong></td>
                    </tr>                   
                    <tr>
                        <td><strong>Mode of Payment: </strong><?php echo $result['company_details']->mode_of_pymnt;?></td>
                    </tr>
                    
                    <?php if($result['company_details']->othr_mode_of_payment != ''){ ?>
                    <tr>
                        <td><strong>Other Mode of Payment: </strong><?php echo $result['company_details']->othr_mode_of_payment; ?></td>
                    </tr>
                    <tr>
                        <td><strong> Amount Received : </strong><?php echo $result['company_details']->total_paid_amount; ?></td>
                    </tr>
                    <tr>
                        <td><strong>SFC Claimed Amount: </strong><?php echo $result['company_details']->sfc_claimed; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cash Amount: </strong><?php echo $result['company_details']->othr_mode_of_payment; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Bank Name: </strong><?php echo $result['company_details']->bank_name; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Number: </strong><?php echo $result['company_details']->cheque_number; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Date: </strong><?php echo $result['company_details']->cheque_date; ?></td>
                    </tr>
                <?php } else if($result['company_details']->mode_of_pymnt == 'CHQ'){ ?>
                    <tr>
                        <td><strong>Total Amount: </strong><?php echo $result['company_details']->total_paid_amount; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Number: </strong><?php echo $result['company_details']->cheque_number; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Date: </strong><?php echo $result['company_details']->cheque_date; ?></td>
                    </tr>
                <?php }else{ ?>
                     <tr>
                        <td> <strong>Amount Received : </strong><?php echo $result['company_details']->total_paid_amount; ?></td>
                    </tr>
                <?php }?>
                    
                </tbody>
            </table><br/>
             <table class="table table-striped">
                <tr><td>Trainee Name</td><td>Received Amount</td><td>Date</td></tr>
                <?php foreach($result['details'] as  $row){?>
                   <tr><td><?php 
                   $trainee_name = $ci->activitylog->user_details($row->user_id);
                                  echo $trainee_name->first_name;?> ( <?php echo $trainee_name->tax_code; ?> )</td><td> <?php echo $row->amount_recd;?> </td><td><?php echo $row->recd_on;?></td></tr>
                <?php $i++;}?>
            </table>
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 14 && $res->account_type == 1){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log View - Update Payment - Individual Invoice</h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                     <tr>
                         <td colspan="4">
                             <strong style="color:green;">Individual Invoice - <?php echo $result['invoice_id']; ?> 
                             Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;
                             ?>
                             On date : <?php echo $res->trigger_datetime;?></strong>
                         </td>
                        
                    </tr>
                    
                    <tr>
                        <td><strong>Invoice Id: <?php echo $result['invoice_id']; ?></strong></td>
                    </tr>
                    <tr>
                         <td><strong>Trainee Name: </strong><?php $act_on = $ci->activitylog->user_details($result['user_id']);
                                  echo $act_on->first_name;?> ( <?php echo $act_on->tax_code; ?> )</td>                        
                    </tr>
                    <tr>
                        <td><strong>Mode of Payment: </strong><?php echo $result['mode_of_pymnt']; ?></td>
                    </tr>
                    <?php
                  if($result['mode_of_pymnt'] == 'SFC_SELF' || $result['mode_of_pymnt'] == 'SFC_ATO'){
                    ?>
                    <tr>
                        <td><strong>Other Mode of Payment: </strong><?php echo $result['othr_mode_of_payment']; ?></td>
                    </tr>
<!--                    <tr>
                        <td><strong> Amount Received : </strong><?php echo $result['amount_recd']; ?></td>
                    </tr>-->
                    <tr>
                        <td><strong>SFC Claimed Amount: </strong><?php echo $result['sfc_claimed']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cash Amount: </strong><?php echo $result['other_amount_recd']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Bank Name: </strong><?php echo $result['bank_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Number: </strong><?php echo $result['cheque_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Date: </strong><?php echo $result['cheque_date']; ?></td>
                    </tr>
                <?php }else if($result['mode_of_pymnt'] == 'CHQ'){ ?>
                    <tr>
                        <td><strong>Total Amount: </strong><?php echo $result['bank_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Number: </strong><?php echo $result['cheque_number']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cheque Date: </strong><?php echo $result['cheque_date']; ?></td>
                    </tr>
                <?php }?>
                     <tr>
                        <td> <strong>Amount Received : </strong><?php echo $result['amount_recd']; ?></td>
                    </tr>
                     <tr>
                        <td> <strong>Amount Received Date : </strong><?php echo $result['recd_on']; ?></td>
                    </tr>
                   
                    
                     
                </tbody>
            </table><br/>
            
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 11){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Trainer Feedback </h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="td_heading" style="color:green" colspan="4"> <strong>
                       Trainer Feedback of Trainee : <?php $trainee = $ci->activitylog->trainee_name($result['details']->user_id);
                       echo $trainee->first_name;?> (  <?php echo $trainee->tax_code; ?>)&nbsp;
                        Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;?>&nbsp;
                        On date : <?php echo $res->trigger_datetime;?></strong>
                        </td>
                     </tr>
                     <tr>
                        <td> Course Name </td>
                        <td><?php $res = $ci->activitylog->course_name($result['details']->course_id);
                        echo $res->crse_name;
                        ?></td>
                    </tr>
                    
                     <tr>
                        <td>Class Name </td>
                        <td><?php $class = $ci->activitylog->class_name($result['details']->class_id);
                        echo $class->class_name
                        ?></td>
                    </tr>
                    
                     <tr>
                        
                         <td> Class Status </td>
                         <td> <?php if($result['details']->class_lock_status == 1){ echo "Class Locked";}else{ echo "Class Unlocked";}?></td>
                    </tr>
                    
                     <tr>
                        <td>Trainee Name </td>
                       <td><?php 
                       $trainee = $ci->activitylog->trainee_name($result['details']->user_id);
                       echo $trainee->first_name;?>
                    </tr>
                   <?php

                    foreach($result['trainer_feedback'] as $r1){
                        $activity_log_question_id[] = $r1->feedback_question_id;
                    }
                    $question_id_array = array('CERTCOLDT','SATSRATE','CERTCOM1','APPKNLSKL','EXPJOBSCP','RT3MNTHS','DTCOMMEMP','COMYTCOM');

                    $diff_question_id = array_diff($question_id_array, $activity_log_question_id);
//                    print_r($diff_question_id);
                    foreach($result['trainer_feedback'] as $tf)
                    { ?>
                        <?php 
                         if($tf->feedback_question_id == 'CERTCOLDT'){?>
                        <tr>
                            <td>Certificate Collected On </td>
                            <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                      
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'SATSRATE'){?>
                        <tr>
                            <td>Satisfaction rating of training programe:</td>
                             <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                  
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'CERTCOM1'){?>
                        <tr>
                            <td>Certified competent on first try:</td>
                             <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                   
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'APPKNLSKL'){?>
                        <tr>
                            <td>Applied knowledge and skills learnt? </td>
                             <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                    
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'EXPJOBSCP'){?>
                        <tr>
                            <td>Expanded job scope/ increased skill set?</td>
                             <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>               
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'RT3MNTHS'){?>
                        <tr>
                            <td>Retained for 3 months?</td>
                            <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                     
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'DTCOMMEMP'){?>
                        <tr>
                            <td>Date of commencement of employment(New Entrance): </td>
                             <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                    
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'COMYTCOM'){?>
                        <tr>
                            <td>Competent or Not Yet Competent? </td>
                              <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                     
                        </tr>
                        <?php 
                        }else if($tf->feedback_question_id == 'COMMNTS'){?>
                        <tr>
                            <td>Comments:</td>
                             <td><?php if($tf->feedback_answer!=''){echo $tf->feedback_answer;}else{echo"N/A";}  ?></td>                   
                        </tr>
                        <?php 
                        }
                   $i++; }
                   
                   ?>
                        <?php
                        if(count($diff_question_id)>0){?>
                        <?php foreach($diff_question_id as $row){?>
                        <tr>
                            <td> <?php echo get_catname_by_parm($row); ?></td>
                            <td>N/A</td>                      
                        </tr>
                        <?php } }?>
                        
                </tbody>
            </table><br/>
    <div style="color: blue;">
        <span>1 - Strongly Disagree</span>&nbsp;&nbsp;
        <span>2 - Disagree</span>&nbsp;&nbsp;
        <span>3 - Neutral</span>&nbsp;&nbsp;
        <span>4 - Agree</span><br/>
        <span>5 - Strongly Agree</span>
    </div>
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 10 && $res->account_type == 2){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Change Payment Mode (Company)</h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <tr>
                            <td class="td_heading" style="color:green" colspan="6"> <strong>
                            Company Name :  <?php  echo $result['company_name'].'('.$result['company_id'].')';?> &nbsp;
                            payment Mode Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                                 echo $act_by->first_name;?>&nbsp;
                            On date : <?php echo $res->trigger_datetime;?></strong>
                            </td>
                        </tr>
                        
                        <td class="td_heading">Company Name</td>
                        <td colspan="6">
                        <?php  echo $result['company_name'].'('.$result['company_id'].')';?>
                       </td>
                     </tr>
                 
                <tr>
                        <td class="td_heading">Course Name</td>
                        <td >
                            <?php
                              echo $result['course_name'].'('.$result['course_id'].')';
                            ?>
                        </td>   
                        <td class="td_heading">Class Name</td>
                        <td colspan="3">
                       <?php
                              echo $result['class_name'].'('.$result['class_id'].')';
                            ?>
                       </td>
                    </tr>
                    <tr>
                        <td colspan="6"> <strong>Enrolled Trainee </strong></td>
                        
                    </tr>
                     <tr>
                        <td colspan="2"> <strong>Trainee name</strong></td>
                        <td colspan="2"> <strong>Payment Status</strong></td>
                        <td colspan="2"> <strong>Class Status</strong></td>
                        
                    </tr>
                   <?php foreach($result['details'] as $row){?>
                    <tr>
                     
                        <td colspan="2">
                            <?php $trainee_name = $ci->activitylog->user_details($row->user_id);
                                  echo $trainee_name->first_name;?> ( <?php echo $trainee_name->tax_code; ?> )
                        </td>  
                        
                        
                        <td colspan="2">
                            <?php if($row->payment_status == 'PYNOTREQD'){ 
                                echo"Payment Not required";
                            }else if($row->payment_status == 'NOTPAID'){
                                echo"Not Paid";
                            }else{
                                echo "Paid";
                            }?>
                        </td> 
                       
                        <td colspan="2">
                            <?php if($row->class_status =='COMPLTD'){
                                  echo "Complete";
                              }else if ($result->class_status =='IN_PROG'){
                                  echo "in-Progress";
                              }else{
                                    echo"Yet To Start";
                              } ?>
                        </td> 
                        
                    </tr>
                   <?php }?>
                   
                </tbody>
            </table>
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 10 && $res->account_type == 1){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Change Payment Mode (Individual) </h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                            <td class="td_heading" style="color:green" colspan="6"> <strong>
                            Trainee :  <?php echo $result['trainee_name'];?> &nbsp;
                             payment Mode Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                                 echo $act_by->first_name;?>&nbsp;
                            On date : <?php echo $res->trigger_datetime;?></strong>
                            </td>
                        </tr>
                    <tr>
                          
                        <td class="td_heading">Trainee Name</td>
                        <td colspan="6">
                        <?php echo $result['trainee_name'];?>
                       </td>
                     </tr>
                 
                    <tr>
                        <td class="td_heading">Course Name</td>
                        <td >
                            <?php
                              echo $result['course_name'].'('.$result['course_id'].')';
                            ?>
                        </td>   
                        <td class="td_heading">Class Name</td>
                        <td colspan="3">
                       <?php
                              echo $result['class_name'].'('.$result['class_id'].')';
                            ?>
                       </td>
                    </tr>
                    
                    <tr>
                        <td class="td_heading">Payment Mode</td>
                        <td colspan="3">
                            <?php
                              if($result['details'][0]->payment_status =='PYNOTREQD'){
                                  echo "Payment Not Required";
                              }else{
                                    echo "Payment Required";
                              }
                            ?>
                        </td>  
                        
                        <td class="td_heading">Class Status</td>
                        <td colspan="3">
                            <?php
                              if($result['details'][0]->class_status =='COMPLTD'){
                                  echo "Complete";
                              }else if ($result['details'][0]->class_status =='IN_PROG'){
                                  echo "in-Progress";
                              }else{
                                    echo"Yet To Start";
                              }
                            ?>
                        </td> 
                        
                    </tr>
                    
                
                   
                </tbody>
            </table>
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 6){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Mark Attendance </h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                            <td class="td_heading" style="color:green" colspan="4"> <strong>
                            Course Name : <?php echo $result['course_name']; ?> and class Name : <?php  echo $result['class_name']; ?>&nbsp;
                            Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                                 echo $act_by->first_name;?>&nbsp;
                            On date : <?php echo $res->trigger_datetime;?></strong>
                            </td>
                        </tr>
                    <tr>
                        <td class="td_heading">Course Name</td>
                        <td >
                            <?php
                              echo $result['course_name'];
                            ?>
                        </td>   
                        <td class="td_heading">Class Name</td>
                        <td colspan="3">
                                            <?php echo $result['class_name'];?>
                       </td>
                     </tr>
                    <tr>
                
                   
                </tbody>
            </table>
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 5){?>
<div class="col-md-10">
   <h2 class="panel_heading_style"><img src="http://tmsadmin.biipbyte.co//assets/images/class.png"> Activity Log - Class View </h2>
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                            <td class="td_heading" style="color:green" colspan="6"> <strong>
                            Class Name : <?php echo $result['class_name']; ?>(<?php echo $result['class_id'];?>)&nbsp;
                            Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                                 echo $act_by->first_name;?>&nbsp;
                            On date : <?php echo $res->trigger_datetime;?></strong>
                            </td>
                        </tr>
                    <tr>
                        <td class="td_heading">Public Registration</td>
                        <td >
                            <?php
                            echo ($result['display_class_public'] == '1') ? 'Yes' : 'No';
                            ?>
                        </td>   
                        <td class="td_heading">Class Status</td>
                        <td colspan="3">
                         <?php   if ($result['class_status'] == 'YTOSTRT')
                            $status_label = '<font color="green">Yet to Start</font>';
                        elseif ($result['class_status'] == 'INACTIV')
                            $status_label = '<font color="red">Inactive</font>';
                        else if ($result['class_status'] == 'COMPLTD')
                            $status_label = '<font color="red">Completed</font>';
                        else if ($result['class_status'] == 'IN_PROG')
                            $status_label = '<font color="blue">In-Progress</font>';
                        else
                            $status_label = 'Unknown'; ?>
                            <?php echo $status_label; ?>
                          
                            <?php
                            if (!empty($result['deacti_reason'])) {?>
                              by:
                             <span class="red"><?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;?></span>&nbsp;
                            De-activation Reason:&nbsp; 
                            <span class="red"><?php 
                          
                            //echo $data['deactivate_reason'] = ($result['deacti_reason'] != 'OTHERS') ? $ci->coursemodel->get_metadata_on_parameter_id($result['deacti_reason']) : 'Others (' . $result['deacti_reason_oth'] . ')';
                            if($result['deacti_reason'] == 'OTHERS'){
                               $deactive_reason =  $result['deacti_reason_oth']; 
                            }else{
         
                                $deactive_reason = get_param_value($result['deacti_reason']);
                                echo $deactive_reason->category_name;
                            }
                            ?></span>
                            <?php
                        }
                        ?>
                       </td>
                     </tr>
                    <tr>
                        <td width="18%" class="td_heading">Class Name:</td>
                        <td width="14%"><label class="label_font"><?php echo $result['class_name']; ?></label></td>
                        <td width="19%" class="td_heading">Start Date & Time:</td>
                        <td width="16%"><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($result['class_start_datetime'])); ?></label></td>
                        <td width="19%" class="td_heading">End Date & Time:</td>
                        <td width="14%"><label class="label_font"><?php echo date('d/m/Y h:i A', strtotime($result['class_end_datetime'])); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Total Seats:</td>
                        <td><label class="label_font"><?php echo $result['total_seats']; ?></label></td>
                        <td class="td_heading">Minimum  Students:</td>
                        <td><label class="label_font"><?php echo $result['min_reqd_students']; ?></label></td>
                        <td colspan="2">
                            <?php
                            if ($class->min_reqd_noti_freq1) {
                                $days = ($class->min_reqd_noti_freq1 > 1) ? 'days' : 'day';
                                echo '1st Reminder : ' . $class->min_reqd_noti_freq1 . ' ' . $days;
                            }
                            if ($class->min_reqd_noti_freq2) {
                                $days = ($class->min_reqd_noti_freq2 > 1) ? 'days' : 'day';
                                echo ', 2nd Reminder : ' . $class->min_reqd_noti_freq2 . ' ' . $days;
                            }
                            if ($class->min_reqd_noti_freq3) {
                                $days = ($class->min_reqd_noti_freq3 > 1) ? 'days' : 'day';
                                echo ', 3rd Reminder : ' . $class->min_reqd_noti_freq3 . ' ' . $days;
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Duration (hrs):</td>
                        <td><label class="label_font"><?php echo $result['total_classroom_duration']; ?></label></td>
                        <td class="td_heading">Lab Duration (hrs):</td>
                        <td><label class="label_font"><?php echo $result['total_lab_duration']; ?></label></td>
                        <td class="td_heading">Assmnt. Duration (hrs):</td>
                        <td><label class="label_font"><?php echo $result['assmnt_duration']; ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Fees:</td>
                        <td><label class="label_font">$<?php echo number_format($result['class_fees'], 2, '.', ''); ?> SGD</label></td>
                        <td class="td_heading">Class Discount:</td>
                        <td><label class="label_font"><?php echo number_format($result['class_discount'], 2, '.', ''); ?>%</label></td>
                        <td class="td_heading">Cert. Collection Date:</td>
                        <td><label class="label_font"><?php
                                if ($result['certi_coll_date'] != '0000-00-00' && $result['certi_coll_date'] != NULL) {
                                    echo date('d/m/Y', strtotime($result['certi_coll_date']));
                                }
                                ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Language:</td>
                        <td ><?php
                        $language = $ci->course_model->get_metadata_on_parameter_id($result['class_language']);
                        echo rtrim($language); ?></td>
                        <td class="td_heading">Payment Details:	</td>
                        <td colspan="3" style="color:blue;"><?php
                         $ClassPay = $ci->course_model->get_metadata_on_parameter_id($result['class_pymnt_enrol']);
                        echo rtrim($ClassPay, ', '); ?></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Venue:</td>
                        <td colspan="5"><label class="label_font"><?php 
                        $ClassLoc = $ci->activitylog->get_classroom_location($result['classroom_location'], $result['classroom_venue_oth']);
                        echo rtrim($ClassLoc, ', '); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Lab Venue:</td>
                        <td colspan="5"><label class="label_font"><?php 
                      $LabLoc =  $ci->activitylog->get_classroom_location($result['lab_location'], $result['lab_venue_oth']);
                        echo rtrim($LabLoc, ', '); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Classroom Trainer:</td>
                        <td><label class="label_font"><?php 
                        $ClassTrainer = $ci->class_model->get_trainer_names($result['classroom_trainer']);
                        echo rtrim($ClassTrainer, ', '); ?></label></td>
                        <td class="td_heading">Lab Trainer:</td>
                        <td><label class="label_font"><?php 
                        $LabTrainer = $ci->class_model->get_trainer_names($result['lab_trainer']);
                        echo rtrim($LabTrainer, ', '); ?></label></td>
                        <td class="td_heading">Assessor:</td>
                        
                        <td><label class="label_font"><?php 
                        $Assessor = $ci->class_model->get_trainer_names($result['assessor']);
                        echo rtrim($Assessor, ', '); ?></label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Training Aide:</td>
                        <td><label class="label_font"><?php 
                        $TrainingAide = $ci->class_model->get_trainer_names($result['training_aide']);
                        echo rtrim($TrainingAide, ', '); ?></label></td>
                        <td class="td_heading">No. of sessions per day:</td>
                        <td colspan="3"><label class="label_font"> <?php echo ($result['class_session_day'] == 1) ? 'One Session' : 'Two Sessions'; ?> </label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Description:</td>
                        <td colspan="5" width="83%">
                            <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                                <?php echo $result['description']; ?>
                            </div>
                        </td>
                    </tr>
                   
                    <?php
//                    if (!empty($copy_reason)) {
                        ?>
<!--                    <td class="td_heading">Copied Reason:</td>
                    <td colspan="5">
                        Class copied from Class Id: <?php echo $result['class_copied_from']; ?>. 
                        Class Copied by User '<?php echo $copied_user; ?>'. 
                        Copy Reason: <?php echo $copy_reason; ?>.
                    </td>-->
                <?php // } ?>
                </tbody>
            </table>
        </div>
    </div>


</div>
<div style="clear:both;"></div>
<?php }else if($res->module_id == 3){
    //$company = $ci->activitylog->get_company_details($res->act_on,$tenant_id);
    ?>
<div class="col-md-10">
 <h2 class="panel_heading_style"><span class="glyphicon glyphicon-eye-open"></span> Activity Log - Trainee View</h2>		  
    <div class="bs-example">
        <div class="table-responsive">          
            <table class="table table-striped">      
                <tbody>
                    <tr>
                        <td class="td_heading" style="color:green" colspan="6"> <strong>
                        Trainee Name : <?php echo $pers_detail->first_name; ?> (<?php echo $result['tax_code'];?>) &nbsp;
                        Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;?>&nbsp;
                        On date : <?php echo $res->trigger_datetime;?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="23%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                        <td width="16%"><label class="label_font"><?php echo ($result['country_of_residence']) ? get_catname_by_parm($result['country_of_residence']) : ''; ?></label></td>
                        <?php if ($result['country_of_residence'] == 'SGP') { ?> 
                            <td colspan="2">               


                                <?php if ($result['tax_code_type'] != 'SNG_3') { ?> 


                                    <strong>NRIC Type:</strong> <?php echo ($result['tax_code_type']) ? get_catname_by_parm($result['tax_code_type']) : ''; ?>  &nbsp;&nbsp;&nbsp; 
                                    <strong>NRIC Code:</strong> <?php echo $result['tax_code']; ?>
                                <?php } else { ?>                                       
                                    <strong>OTHER:</strong> <?php echo ($result['other_identi_type']) ? get_catname_by_parm($result['other_identi_type']) : ''; ?>&nbsp;&nbsp;&nbsp;
                                    <strong>Code:</strong> <?php echo $result['other_identi_code']; ?>
                                <?php } ?>    
                            </td>
                        <?php } else { ?>
                            <td colspan="2">
                                <strong><?php echo $result['tax_code_type']; ?> No.:</strong>
                                <?php echo $result['tax_code']; ?>
                            </td>
                        <?php } ?>
                        <td class="td_heading">Username:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $result['user_name']; ?></label></td> 
                    </tr>          
                    <tr>
                        <td class="td_heading">Activation Status:</td>


                        <td class="td_heading" colspan="5"> 
                            <label class="">
                                <?php
                                if ($result['account_status'] == 'ACTIVE') {
                                    echo "<font class='green-active'>Active</font>";
                                } else if ($result['account_status'] == 'INACTIV') {
                                    $deactivated_by = $ci->activitylog->user_details($result['deacti_by']);
                                    
                                    $deactivation_details = '';
                                    $deactivation_details = ' - Deactivated by ' . $deactivated_by->first_name . ' ' . $deactivated_by->last_name . '(' . $deactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($result['acct_deacti_date_time'])) . '.';
                                    if ($result['deacti_reason'] == 'OTHERS') {
                                        $deactivation_details .= ' Reason: ' . $result['deacti_reason_oth'] . '.';
                                    } else {
                                        $user_deactivation_reason = get_param_value($result['deacti_reason']);
                                        $deactivation_details .= ' Reason: ' . $user_deactivation_reason->category_name . '.';
                                    }
                                    echo "<font color='red'>In-Active " . $deactivation_details . "</font>";
                                } else if ($result['account_status'] == 'PENDACT') {
                                    echo "<font color='blue'>Pending Activation</font>";
                                }
                                ?>
                            </label>
                        </td>
                        
                        </td>
                    </tr>
                    
                     <?php if ($result['account_status'] == 'ACTIVE' && $result['reactivation_reason_id']!='') {?>
                        <tr>
                        <td class="td_heading">Reactivation Reason:</td>


                        <td class="td_heading" colspan="5"> 
                            <label class="">
                                <?php
                              $reactivated_by = $ci->activitylog->user_details($result['reactivated_by']);
                                    
                                    $reactivation_details = '';
                                    $reactivation_details = ' - Reactivated by ' . $reactivated_by->first_name . ' ' . $reactivated_by->last_name . '(' . $reactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($result['reactivation_date_time'])) . '.';
                                    if ($result['reactivation_reason_id'] == 'OTHERS') {
                                        $reactivation_details .= ' Reason: ' . $result['reactivation_reason_others'] . '.';
                                    } else {
                                        $user_reactivation_reason = get_param_value($result['reactivation_reason_id']);
                                        $reactivation_details .= ' Reason: ' . $user_reactivation_reason->category_name . '.';
                                    }
                                    echo "<font color='black'>Re-Active " . $reactivation_details . "</font>";
                               
                                   ?>
                                
                            </label>
                        </td>
                        
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>    
        </div>
    </div>
 
 <h2 class="sub_panel_heading_style">
        <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> Personal Details
        <!--<span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex144" rel="modal:open" style="color: blue;">Individual Discount</a></span>-->
    </h2>
    <div class="table-responsive">
        <table class="table table-striped">

            <tbody>

                <tr>
                    <td class="td_heading" width="15%">Name:<span class="required">*</span></td>
                    <td colspan="3"><label class="label_font"><?php echo $pers_detail->first_name; ?></label></td>                    
                    <td class="td_heading">Nationality:<span class="required">*</span></td>
                    <td><label class="label_font">


                            <?php


                                $nationalityLabel = '';


                                $nationality = fetch_metavalues_by_category_id(Meta_Values::NATIONALITY);


                                foreach ($nationality as $item):


                                    if($item['parameter_id'] ==  $result['nationality']){


                                        $nationalityLabel = $item['category_name'];


                                        break;


                                    }


                                endforeach;


                             ?>    


                            <?php echo ($nationalityLabel); ?></label>


                    </td>                    
                    <td rowspan="3" align="center">


                        <div class="userphoto">


                            <?php if ($pers_detail->photo_upload_path): ?> 
                                <img src="<?php echo base_url() . $pers_detail->photo_upload_path; ?>"/> 
                            <?php else: ?>
                                <img src="<?php echo base_url(); ?>assets/images/photo.jpg"/> 
                            <?php endif; ?>
                        </div>
                    </td>

                </tr>        
                <tr>
                    <td class="td_heading" width="15%">Gender:<span class="required">*</span></td>
                    <td>
                        <label class="label_font"><?php echo ($result['gender']) ? get_catname_by_parm($result['gender']) : ''; ?></label>
                    </td>
                    <td class="td_heading">Date of Birth:</td>
                    <td><?php echo empty($pers_detail->dob) ? '' : date('d-m-Y', strtotime($pers_detail->dob)); ?></td>
                    <td class="td_heading">Contact Number:<span class="required">*</span></td>
                    <td><label class="label_font"><?php echo $result['contact_number']; ?></label></td>                    
                </tr>        
                <tr>
                    <td class="td_heading">Alt. Contact #:</td>
                    <td><label class="label_font"><?php echo $result['alternate_contact_number']; ?></label></td>
                    <td class="td_heading">Race:</td>
                    <td><label class="label_font"><?php echo ($result['race']) ? get_catname_by_parm($result['race']) : ''; ?></label></td>                    
                    <!--add by pritam-->
                    <?php 
                    //echo "com".$trainee['company']['company_name'];
                    
                    if($result['trainee_type']=="NA")
                    {
                        if($result['trainee_type'])
                        {?>
                            <td class="td_heading">Certificate Sent To:</td>
                           <td><label class="label_font"><?php //echo $company['company_name']; 
                           if($result['trainee_type']!=''){echo $result['trainee_type'];}else{ echo "Individual";}
                           ?></label></td> 
                        <?php 
                        }
                        else
                        {
                        ?>
                           <td class="td_heading">Assign Trainee to Company:</td>
                         <td><label class="label_font">
                             <?php if($result['trainee_type']!=''){echo $result['trainee_type'];}else{ echo "Individual";}
                                //echo $company['company_name']; ?></label></td>
                        <?php    
                        }
                   }
                    else
                    {?>
                    <td class="td_heading">Assign Trainee to Company:</td>
                    <td><label class="label_font"><?php if($result['trainee_type']!=''){echo $result['trainee_type'];}else{ echo "Individual";}//echo $company['company_name']; ?></label></td>
                    <?php
                    }
                   
                    ?>
                    <!--end-->
                </tr>        
                <tr>
                    <td class="td_heading">Salary Range:</td>
                    <td>
                        <label class="label_font"><?php echo ($result['salary_range']) ? get_catname_by_parm($result['salary_range']) : ''; ?></label>
                    </td>
                    <td class="td_heading">Designation:</td>
                    <td colspan="4">
                        <label class="label_font"><?php echo ($result['occupation_code']) ? get_catname_by_parm($result['occupation_code']) : ''; ?></label>
                    </td>                    		                      
                </tr>
                <tr>


                    <td class="td_heading">Email Activation:<span class="required">*</span></td>
                    <td colspan="6">
                        <label class="label_font"><?php
                            if ($result['acc_activation_type'] == 'BPEMAC')
                                echo "By-pass email activation";
                            else
                                echo "Do not By-pass email activation";
                            ?>
                        </label>
                    </td>
                </tr>
                <tr>                    
                    <td class="td_heading">Email Id:</td>
                    <td><label class="label_font"><?php echo $result['registered_email_id']; ?></label></td>
                    <td class="td_heading">Confirm Email Id:</td>
                    <td><label class="label_font"><?php echo $result['registered_email_id']; ?></label></td>
                    <td class="td_heading">Alternate Email Id:</td>
                    <td colspan="2">
                        <label class="label_font"><?php echo $result['alternate_email_id']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading" colspan="2">Highest Education Level:<span class="required">*</span></td>
                    <td colspan="2">
                        <label class="label_font"><?php echo ($result['highest_educ_level']) ? get_catname_by_parm($result['highest_educ_level']) : ''; ?></label>
                    </td>
                    <td class="td_heading" colspan="1">Certificate Pickup Detail:</td>
                    <td colspan="2">
                        <label class="label_font">
                            <?php
                            if ($result['certificate_pick_pref'] == 'cerit_self')
                                echo "I will pickup myself";
                            else if ($result['certificate_pick_pref'] == 'cerit_mail')
                                echo "Mail to my personal email Id";
                            else if ($result['certificate_pick_pref'] == 'cerit_post')
                                echo "Mail to my postal address";
                            else
                                echo "NA";
                            ?>
                        </label>    
                    </td>

                </tr>
          </tbody>
        </table>
    </div>

 
</div>
<div class="modal_333" id="ex144" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Individual Discount by Course</h2>
        <div class="table-responsive payment_scroll" style="height: 300px;">
            <?php if($result['individual_discount']!=''){?>
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
                  foreach($result['individual_discount'] as $row){
                        $val = explode('_',$row->discount);
                        $course_name = $ci->activitylog->get_course_name($val[0]);
                        $discount = explode('#',$val[1]); 
                        $Discount_Percent = $discount[0]; 
                        $Discount_Amount  = $discount[1];
                       
                        echo "<tr>
                                        <td>" . $course_name->crse_name . "</td>
                                        <td>" . number_format($Discount_Percent, 2, '.', '') . " %</td>
                                        <td>$ " . number_format($Discount_Amount, 2, '.', '') . "</td>
                                    </tr>";
                    }
                ?>
                </tbody>
            </table>
            <?php }else{?>
            No Discount Applicable On This Trainee.
            <?php }?>
        </div>
    </p>
    </div>
 <div style="clear:both;"></div>
<?php }else if($res->module_id == 4){?>
<div class="col-md-10">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Activity Log - Course View</h2>   
    <div class="bs-example">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                        <tr>
                            <td class="td_heading" style="color:green" colspan="4"> <strong>
                            Course Name : <?php echo $result['crse_name']; ?>(<?php echo $result['course_id'];?>)&nbsp;
                            Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                                 echo $act_by->first_name;?>&nbsp;
                            On date : <?php echo $res->trigger_datetime;?></strong>
                            </td>
                        </tr>
                        <tr>
                         <td class="td_heading">Display on Landing Page:&nbsp;&nbsp;
                             <label class="label_font" style="color:blue"><b>
                                <?php echo ($result['display_on_portal'] == 1)?'Yes':'No';?></b>
                            </label>
                       </td>
                        <?php
                        $div_class = 'green';
                        $div_colspan = '3';
                        if ($result['crse_status'] == 'INACTIV') {
                            $div_class = 'red';
                            $div_colspan = '1';
                        }
                        ?>
                        <td colspan='3' class="td_heading">Course Status:&nbsp;&nbsp;
                            <label class="label_font">
                                <span class="<?php echo $div_class; ?>">
                                    <b><?php echo rtrim($ci->course_model->get_metadata_on_parameter_id($result['crse_status']), ', '); ?></b>                                
                                </span>
                            </label>
                           <?php if ($result['crse_status'] == 'INACTIV') { ?>
                                De-activation Reason:
                                 <label class="label_font">
                                    <span class="<?php echo $div_class; ?>">
                                        <?php                                        
                                        if ($result['deacti_reason'] == 'OTHERS') {
                                            echo $result['deacti_reason_oth'];
                                        } else {
                                            echo rtrim($ci->course_model->get_metadata_on_parameter_id($result['deacti_reason']), ', ');
                                        }
                                        ?>                                
                                    </span>
                                </label>
                            <?php } ?>
                          </td> 
                    </tr>
                    <tr>
                        <td class="td_heading" width="28%">Course Name:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $result['crse_name']; ?></label></td>
                        <td class="td_heading">Pre-requisite:</td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($ci->course_model->get_pre_requisite($result['pre_requisite']), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Language:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($ci->course_model->get_metadata_on_parameter_id($result['language']), ', '); ?>
                            </label>                        
                        </td>
                        <td class="td_heading">Course Type:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($ci->course_model->get_metadata_on_parameter_id($result['crse_type']), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">Class Type:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($ci->course_model->get_metadata_on_parameter_id($result['class_type']), ', '); ?>
                            </label>
                        </td>
                        <td class="td_heading">GST Rate:</td>
                        <td>
                            <?php
                            $gst_rates_attributes = array('class' => 'label_font');
                            $gst_rates = $ci->settings_model->get_active_gst_rates($this->session->userdata('userDetails')->tenant_id);
                            if ($gst_rates->gst_rate != false) {
                                $gst = number_format($gst_rates->gst_rate, 2);
                                echo form_label("$gst %", '', $gst_rates_attributes);
                            }else
                                echo form_label("GST-Not Defined", '', $gst_rates_attributes);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="td_heading">GST Rules:<span class="required">*</span></td>
                        <td> 
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            if ($result['gst_on_off'] == '1') {
                                $yes = TRUE;
                            } else {
                                $no = TRUE;
                            }
                            $gst_rules_yes = array(
                                'name' => 'gst_rules',
                                'value' => '1',
                                'id' => 'gst_rules_yes',
                                'checked' => $yes,
                                'disabled' => "disabled",
                            );
                            $gst_rules_yes_no = array(
                                'name' => 'gst_rules',
                                'id' => 'gst_rules_no',
                                'value' => '0',
                                'checked' => $no,
                                'disabled' => "disabled",
                            );
                            ?>              
                            <?php echo form_radio($gst_rules_yes); ?>Yes &nbsp;&nbsp; 
                            <?php echo form_radio($gst_rules_yes_no); ?> No                                                            
                            <span id="class_type_err"></span>
                        </td>
                        <td colspan='2'>
                            <?php
                            $yes = FALSE;
                            $no = FALSE;
                            if ($result['subsidy_after_before'] == 'GSTBSD') {
                                $yes = TRUE;
                            } else {
                                $no = TRUE;
                            }
                            $before_subsidy = array(
                                'name' => 'subsidy',
                                'value' => 'GSTBSD',
                                'id' => 'before_subsidy',
                                'checked' => $yes,
                                'disabled' => "disabled",
                            );
                            $after_subsidy = array(
                                'name' => 'subsidy',
                                'id' => 'after_subsidy',
                                'value' => 'GSTASD',
                                'checked' => $no,
                                'disabled' => "disabled",
                            );
                            ?>              
                            <?php echo form_radio($before_subsidy); ?>Apply GST before SUBSIDY. &nbsp;&nbsp; 
                            <?php echo form_radio($after_subsidy); ?> Apply GST after SUBSIDY.                               
                        </td>                          
                    </tr>
                    <tr>
                        <td class="td_heading">Course Duration (in hrs):<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $result['crse_duration']; ?></label></td>
                        <td class="td_heading">Course Reference Number:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $result['reference_num']; ?></label></td>                        
                    </tr>
                    <tr>                        
                        <td class="td_heading">Course Competency Code:<span class="required">*</span></td>
                        <td><label class="label_font"><?php echo $result['competency_code']; ?></label></td>
                        <td class="td_heading">Certification Code/ Level:<span class="required">*</span></td>
                        <td>
                            <label class="label_font">
                                <?php echo rtrim($ci->course_model->get_metadata_on_parameter_id($result['certi_level']), ', '); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>                        
                        <td class="td_heading">Course Manager:<span class="required">*</span></td>
                        <td colspan="3">
                            <label class="label_font">
                                <?php echo rtrim($ci->course_model->get_managers($result['crse_manager']), ', '); ?>                                
                            </label>
                        </td>
                    </tr>
                  <tr>                        
                        <td class="td_heading">Course Description:<span class="required">*</span></td>
                        <td colspan="3">
                            <div class="table-responsive payment_scroll" style="height: 87px;min-height: 87px;">
                                <label class="label_font">
                                    <?php echo $result['description']; ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php if(!in_array($this->session->userdata('userDetails')->role_id,$role_array)) { ?>
                    <tr>
                        <td class="td_heading">Default Sales Commission Report:<span class="required">*</span></td>
                        <td colspan="3"><label class="label_font"><?php echo number_format($result['default_commission_rate'],2,'.',''); ?>&nbsp;%</label></td>
                    </tr>
                    <tr>
                        <td class="td_heading">Sales Executive:<span class="required">*</span></td>
                        <td colspan="3"><label class="label_font">
                                <?php foreach($result['sales_exe_id'] as $username){ 
                                    $user_id = explode('_',$username->user_id);
                                    
                                    $res = $ci->activitylog->get_personal_details($user_id[0],$tenant_id);
                                    $sales_exec[] = $res->first_name.'('.number_format($user_id[1], 2, '.', '').')';
                                    
                                }
                             $sales_executive = implode(',',$sales_exec);
                             echo rtrim($sales_executive);
                             ?>
                            </label></td>
                    </tr>
                    <?php } ?>
                    <?php if($result['copied_from_id']) { ?>
                    <tr>
                        <td class="td_heading">                            
                            <label>
                                Remarks: 
                            </label>                            
                        </td>
                        <td>
                            Course Copied from <?php echo $this->course->course_name($result['copied_from_id']); ?>
                        </td>
                        <td>
                            <label>
                                Copy Reason:
                            </label>
                        </td>
                        <td>
                            <?php echo $result['copy_reason']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2" width="55%">Does this course have a validity period? 
                             &nbsp; &nbsp;
                            <input name="1" type='radio' <?php if ($result['crse_cert_validity'] != 0) { ?> checked <?php } ?> disabled='disabled' />Yes &nbsp;&nbsp;
                            <input name="1" type='radio' <?php if ($result['crse_cert_validity'] == 0) { ?> checked <?php } ?> disabled='disabled'/>No &nbsp;&nbsp; 
                            <?php
                            if ($result['crse_cert_validity'] != 0) {
                                echo $result['crse_cert_validity'] . ' days from date of certification';
                            }
                            ?> 
                        </td>
                        <td colspan="2" width="45%">
                            <table>
                                <tr>
                                    <?php if ($result['crse_content_path']) { ?>
                                        <td class="td_heading" width="40%" style="vertical-align:top">Download Course Material:</td>
                                        <td class="td_heading" width="25%" style="vertical-align:top">
                                            <a href="<?php echo base_url() . 'course/download_course_content/?file_path=' . $result['crse_content_path'] . "&file_name=" . $result['crse_name']; ?>">Download</a>
                                        </td>
                                    <?php } ?>
                                    <?php if ($result['crse_icon']) { ?>
                                        <td align="center" width="35%">
                                            <div class="photo_icon">
                                                <img src="<?php echo base_url() . $$result['crse_icon']; ?>"> 
                                            </div>
                                            <p style="font-weight:bold">Course Icon:</p>
                                        </td>
                                    <?php } ?>
                                </tr>
                            </table>    
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
     
    
</div>
<?php }else if($res->module_id==2){?>
<div class="col-md-10">
   
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/company.png" /> Activity Log - Company Details
       
    </h2>
    <h2 class="sub_panel_heading_style">
        <img src="<?php echo base_url(); ?>/assets/images/company-detail.png" /> Company Details
        <!--<span class="pull-right"> <img src="<?php echo base_url(); ?>/assets/images/personal_details.png"> <a href="#ex144" rel="modal:open" style="color: blue;">Company Discount</a></span>-->
    </h2>
    <div class="bs-example">
        <?php
    
            ?>        
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading" style="color:green" colspan="6"> <strong>
                                Company Name:<?php echo $result['company_name']; ?>(<?php echo $result['company_id'];?>)&nbsp;
                                Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                                     echo $act_by->first_name;?>&nbsp;
                                On date : <?php echo $res->trigger_datetime;?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_heading" width="15%">Company Name:</td>
                            <td><label class="label_font"><?php echo $result['company_name']; ?></label></td>
                            <td class="td_heading" width="15%">Registration Number:</td>
                            <td><label class="label_font"><?php echo trim($result['comp_regist_num']); ?></label></td>
                            <td class="td_heading" width="15%">Registration Type:</td>
                            <td><label class="label_font">
                                    <?php
                                    $business_type = ($result['business_type'])?get_param_value(trim($result['business_type'])):'';
                                    echo $business_type->category_name;
                                    ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Size:</td>
                            <td><label class="label_font">
                                    <?php
                                    $business_size = ($result['business_size'])?get_param_value(trim($result['business_size'])):'';
                                    echo $business_size->category_name;
                                    ?>
                                </label></td>
                            <td class="td_heading">Phone Number:</td>
                            <td><label class="label_font"><?php echo trim($result['comp_phone']); ?></label></td>
                            <td class="td_heading">Fax Number:</td>
                            <td><label class="label_font"><?php echo trim($result['comp_fax']); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Country:</td>
                            <td><label class="label_font">
                                    <?php
                                    $country = ($result['comp_cntry_scn'])?get_param_value(trim($result['comp_cntry_scn'])):'';
                                    echo $country->category_name;
                                    ?>
                                </label>
                                <div id="" style="width:50%; float:right;">
                                    <?php if ($company->comp_cntry_scn == 'SGP') { ?>
                                        <strong>SCN:</strong>
                                        <label class="label_font"><?php echo trim($company->comp_scn); ?></label>
                                    <?php } ?>
                                </div>  
                            </td>
                            <td class="td_heading">SME Type:</td>
                            <td colspan="3"><label class="label_font"><?php echo $result['sme_nonsme']; ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Company Attn.:</td>
                            <td><label class="label_font"><?php echo $result['comp_attn']; ?></label></td>
                            <td class="td_heading">Company Email:</td>
                            <td colspan="3"><label class="label_font"><?php echo $result['comp_email']; ?></label></td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
            <br>
            <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/address.png" /> Address</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="20%">Street / Building:</td>
                            <td width="30%"><label class="label_font"><?php echo trim($result['comp_address']); ?></label></td>
                            <td class="td_heading" width="20%">City:</td>
                            <td width="30%"><label class="label_font"><?php echo trim($result['comp_city']); ?></label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Country:</td>
                            <td><label class="label_font">
                                    <?php
                                    if ($result['comp_cntry'] != '') {
                                        $country = get_param_value(trim($result['comp_cntry']));
                                        echo $country->category_name;
                                    }
                                    ?>
                                </label></td>
                            <td class="td_heading">State:</td>
                            <td><label class="label_font">
                                    <?php
                                    if ($result['comp_state'] != '') {
                                        $state = get_param_value(trim($result['comp_state']));
                                        echo $state->category_name;
                                    }
                                    ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Postal Code:</td>
                            <td colspan="5"><label class="label_font"><?php echo trim($result['comp_zip']); ?></label></td>
                        </tr>
                    </tbody>
                </table>
            </div>
          
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/contact.png" /> Contact Details </h2>
           <div class="table-responsive">
               <?php
            if(empty($result['user_details'])){
                echo '<table class="table table-striped"><tr class=danger><td style="color:red;text-align: center;"> No Company Users available. </td></tr></table>';
            }
            foreach ($result['user_details'] as $values) {
                ?>            
                <table class="table table-striped" border="0">
                    <tbody>
                        <tr>
                            <td class="td_heading" width="15%">Name:</td>
                            <td colspan="3"><label class="label_font" width="15%"><?php echo $values->first_name; ?></label></td>                            
                            <td class="td_heading" width="15%">Gender:</td>
                            <td width="15%"><label class="label_font">
                                    <?php
                                    if ($values->gender != '') {
                                        $gender = get_param_value(trim($values->gender));
                                        echo $gender->category_name;
                                    }
                                    ?>
                                </label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Contact Number [O]:</td>
                            <td><label class="label_font"><?php echo $values->contact_number; ?></label></td>
                            <td class="td_heading">Mobile Number [O]:</td>
                            <td><label class="label_font"><?php echo $values->alternate_contact_number; ?></label></td>
                            <td class="td_heading"><!--Mobile Number [P]-->&nbsp;</td>
                            <td><label class="label_font">&nbsp;</label></td>
                        </tr>
                        <tr>
                            <td class="td_heading">Email Id 01:</td>
                            <td><label class="label_font"><?php echo $values->registered_email_id; ?></label></td>
                            <td class="td_heading">Email Id 02:</td>
                            <td><label class="label_font"><?php echo $values->alternate_email_id; ?></label></td>
                            <td class="td_heading">Username:</td>
                            <td><label class="label_font"><?php echo $values->user_name; ?></label></td>
                        </tr>
                        <tr>
                            <td><strong>Contact Status:</strong></td>
                            <td   colspan="4"><?php
                                $user_acct_status = ($values->user_acct_status)?get_param_value(trim($values->user_acct_status)):'';
                                if ($values->user_acct_status == 'INACTIV') {
                                    echo '<span class="red">' . $user_acct_status->category_name . '</span>';
                                    if ($values->deacti_reason == 'OTHERS') {
                                        $reason = $values->deacti_reason_oth;
                                    } else {
                                        $meta_details = ($values->deacti_reason)?get_param_value($values->deacti_reason):'';
                                        $reason = $meta_details->category_name;
                                    }
                                    $deactivated_by = $this->internaluser->get_user_details($tenant_id, $values->deacti_by);
                                    echo '<span> ( <strong>Deactivation Reason:</strong> ' . $reason . '. Deactivated by ' . $deactivated_by->user_name . ' on ' . $values->acct_deacti_date_time . ')</span>';
                                } else if ($values->user_acct_status == 'ACTIVE') {
                                    echo '<span class="green">' . $user_acct_status->category_name . '</span>';
                                } else if ($values->user_acct_status == 'PENDACT') {
                                    echo '<span class="blue">' . $user_acct_status->category_name . '</span>';
                                } else {
                                    echo $user_acct_status->category_name;
                                }
                                ?></td>
                           
                        </tr>
                        <TR><TD colspan="6">&nbsp;</TD></TR>
                </table> 
                <?php
            }
            ?>
           </div>
            
        <h2 class="sub_panel_heading_style"> Company Status </h2>
                   <div class="table-responsive">
            <table>
                <tr><td>
            <strong>Company Status:</strong></td>
            <td >   <?php
                $company_acct_status = ($result['comp_status'])?get_param_value(trim($result['comp_status'])):'';
                
                if ($result['comp_status'] == 'INACTIV') {
                    echo '<span class="red">' . $company_acct_status->category_name . '</span>';
                    
                    if ($result['deacti_reason']== 'OTHERS') {
//                        $reason = $company->deacti_reason_oth;
                         $reason = $result['deacti_reason_oth'];
                    } else {
                        $meta_details = ($result['deacti_reason'])?get_param_value(trim($result['deacti_reason'])):'';
                        $reason = $meta_details->category_name;
                    }
                    $deactivated_by = $this->activitylog->user_details($result['deacti_by']);
                    echo '<span> ( <strong>Deactivation Reason: </strong>' . $reason . '. Deactivated by ' . $deactivated_by->user_name . ' on ' . $result['acct_deacti_date_time'] . ')</span>';
                }
                
                else if ($result['comp_status'] == 'ACTIVE' && $result['reactivation_reason_id']!='') {
                    echo '<span class="green">' . $company_acct_status->category_name . '</span>';
                    
                    if($result['reactivation_reason_id'] == 'OTHERS'){
                         $reason_reactive = $result['deacti_reason_oth'];
                    }else{
                        $meta_details_reactive = ($result['reactivation_reason_id'])?get_param_value(trim($result['reactivation_reason_id'])):'';
                        $reason_reactive = $meta_details_reactive->category_name;
                    }
                    $reactivated_by = $this->activitylog->user_details($result['reactivated_by']);
                    echo '<span> ( <strong>Reactivation Reason: </strong>' . $reason_reactive . '. Reactivated by ' . $reactivated_by->user_name . ' on ' . $result['reactivation_date_time'] . ')</span>';
                    
                }
                
                else if ($result['comp_status']== 'PENDACT') {
                    echo '<span class="blue">' . $company_acct_status->category_name . '</span>';
                } else {
                      echo '<span class="green">' . $company_acct_status->category_name . '</span>';
                }
                
                ?></td>
           
            </tr>                    
          
            </table>
        </div>
        <br>
    </div>
    
</div>
<?php if (empty($result['company_discount'])) { ?>
    <div class="modal0000" id="ex144" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Alert Message</h2>
        There are no courses available.<br>
        <div class="popup_cancel popup_cancel001">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
    </p>
    </div>
<?php } else { ?>
    <div class="modal_333" id="ex144" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Company Discount by Course</h2>
        <div class="table-responsive payment_scroll" style="height: 300px;">
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
                  foreach($result['company_discount'] as $row){
                        $val = explode('_',$row->discount);
                        $course_name = $ci->activitylog->get_course_name($val[0]);
                        $discount = explode('#',$val[1]); 
                        $Discount_Percent = $discount[0]; 
                        $Discount_Amount  = $discount[1];
                       
                        echo "<tr>
                                        <td>" . $course_name->crse_name . "</td>
                                        <td>" . number_format($Discount_Percent, 2, '.', '') . " %</td>
                                        <td>$ " . number_format($Discount_Amount, 2, '.', '') . "</td>
                                    </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </p>
    </div>
<?php } ?>
    <div style="clear:both;"></div>
<?php }else if($res->module_id ==1 || $res->module_id == 13){?>
<div class="col-md-10">
  
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Activity Log - Internal Staff Details</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details</h2>
    <div class="table-responsive">
        <?php  $act_by = $ci->activitylog->user_details($res->act_by);?>
        <table class="table table-striped">
            <tbody>
                <tr><td class="td_heading" style="color:green" colspan="4"> <strong>
                            Internal Staff:<?php echo $pers_detail->first_name; ?>(<?php echo $result['tax_code'];?>)&nbsp;
                        Updated By : <?php
                             echo $act_by->first_name;?>&nbsp;
                        On date : <?php echo $res->trigger_datetime;?></strong>
                    </td>
                </tr>
              
                <tr>
                    <td class="td_heading" width="20%">Staff Name:</td>
                    <td colspan="3" width="80%"><label class="label_font"><?php echo $pers_detail->first_name; ?></label></td>
                    
                    <td rowspan="5" align="center">
                        <div class="userphoto">
                            <?php if ($user_list_values->photo_upload_path): ?> 
                                <img src="<?php echo base_url() . $user_list_values->photo_upload_path; ?>"/> 
                            <?php else: ?>
                                <img src="<?php echo base_url(); ?>assets/images/photo.jpg"/> 
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Gender:</td>
                    <td><label class="label_font"><?php echo $pers_detail->gender; ?></label></td>
                    <td class="td_heading">Date of Birth:</td>
                    <td><label class="label_font"><?php echo ($pers_detail->dob == '0000-00-00')?'':formated_date($pers_detail->dob, '-'); ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Country of Residence:</td>
                    <td><label class="label_font"><?php if($result['country_of_residence'] == 'SGP'){echo "Singapore";} ?></label></td>
                    <td>
                        <strong>NRIC/FIN No. Type:</strong> 
                    </td>
                    <td>
                        <?php
                            if ($result['other_identi_type'] != NULL && $result['other_identi_type'] != '')
                            {
                                $other_identi_type = get_param_value($result['other_identi_type']);
                                $other_identi_type= " (" . $other_identi_type->category_name . " )";
                            }
                            echo get_catname_by_parm($result['tax_code_type']) . $other_identi_type;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">NRIC/FIN No.:</td>
                    <td><label class="label_font"><?php echo $result['tax_code']; ?></label></td>
                    <td class="td_heading">Contact Number(P):</td>
                    <td><label class="label_font"><?php echo $pers_detail->contact_number; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Contact Number(M):</td>
                    <td><label class="label_font"><?php echo $pers_detail->alternate_contact_number; ?></label></td>
                    <td class="td_heading">Email Id(P):</td>
                    <td><label class="label_font"><?php echo $result['registered_email_id']; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Alternate Email Id(P):</td>
                    <td colspan='4'><label class="label_font"><?php echo $pers_detail->alternate_email_id; ?></label></td>
                </tr>
                 <tr>
                    <td class="td_heading">Updation Done By:</td>
                    <td colspan='4'><label class="label_font"><?php echo $act_by->first_name; ?></label></td>
                </tr>
                
                      
            </tbody>
        </table>
    </div>      
   
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/>  Staff Other Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <?php if($res->module_id == 1){?>
                <tr>
                    <td class="td_heading" width="20%">Role:</td>
                    <td colspan="3"><label class="label_font"><?php 
                    foreach($result['role_id'] as $role)
                    {
                       $str[] = $role->role_id;
                        
                    }
                    $rols = implode(',',$str);
                    echo $rols;
                     ?></label>
                    
                    </td>
                </tr><?php }?>
               
                <tr>
                    <td class="td_heading">Username/ Login Name:</td>
                    <td colspan="3"><label class="label_font"><?php echo $result['user_name']; ?></label>
                   
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Account Status : </td>
                    <td colspan="3" class="td_heading">
                       <?php if($result['account_status']=='INACTIV'){
                           echo "<font color='red'>" . $result['account_status'] . "</font>";
                           }else if($result['account_status'] == 'ACTIVE'){ 
                               echo "<font color='green'>" . $result['account_status'] . "</font>";
                               
                           }else if($result['account_status'] == 'PENDACT'){
                                 echo "<font color='red'> Activation Pending </font>";
                           }
                           
                           
                           ?>
                       
                       
                    </td>
                </tr>  
                <?php if( $result['account_status'] == 'ACTIVE' && $result['reactivation_reason_id']!=''){?>
                 <tr>
                    <td class="td_heading" colspan="3"> 
                       
                        <?php 
                            $reactivation_details = '';
                            $reactivated_by = $this->activitylog->user_details($result['reactivated_by']);
                            $reactivation_details = '  Activated by ' . $reactivated_by->first_name . ' ' . $reactivated_by->last_name . '(' . $reactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($result['reactivation_date_time'])) . '.';
                            if ($result['reactivation_reason_id'] == 'OTHERS') {
                                $reactivation_details .= ' Reason: ' . $result['reactivation_reason_others'].'.';
                            } else {
                                $user_reactivation_reason = get_param_value($result['reactivation_reason_id']);
                                $reactivation_details .= ' Reason: ' . $rser_deactivation_reason->category_name.'.';
                            }
                            echo "<font color='green'>" . $reactivation_details . "</font>";?></td>
                    
                </tr>
                <?php } ?>
                
                
                
                 <?php if( $result['account_status'] == 'INACTIV'){?>
                <tr>
                    <td class="td_heading" colspan="3"> 
                       
                        <?php 
                            $deactivation_details = '';
                            $deactivated_by = $this->activitylog->user_details($result['deacti_by']);
                            $deactivation_details = '  Deactivated by ' . $deactivated_by->first_name . ' ' . $deactivated_by->last_name . '(' . $deactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($result['acct_deacti_date_time'])) . '.';
                            if ($result['deacti_reason'] == 'OTHERS') {
                                $deactivation_details .= ' Reason: ' . $result['deacti_reason_oth'].'.';
                            } else {
                                $user_deactivation_reason = get_param_value($result['deacti_reason']);
                                $deactivation_details .= ' Reason: ' . $user_deactivation_reason->category_name.'.';
                            }
                            echo "<font color='red'>" . $deactivation_details . "</font>";?></td>
                    
                </tr><?php }?>
               
            </tbody>
        </table>
    </div>
    <br>
   
</div>

<?php }else if($res->module_id == 12){?>
<div class="col-md-10">
  
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Activity Log - Personal Details</h2>
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/personal_details.png"/> Personal Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <tr><td class="td_heading" style="color:green" colspan="4"> <strong>
                            Internal Staff: <?php echo  $result['first_name']; ?>(<?php echo $result['tax_code'];?>)&nbsp;
                        Updated By : <?php $act_by = $ci->activitylog->user_details($res->act_by);
                             echo $act_by->first_name;?>&nbsp;
                        On date : <?php echo $res->trigger_datetime;?></strong>
                    </td></tr>
                <tr>
                    <td class="td_heading" width="20%">Staff Name:</td>
                    <td colspan="3" width="80%"><label class="label_font"><?php echo $result['first_name']; ?></label></td>
                    
                    <td rowspan="5" align="center">
                        <div class="userphoto">
                            <?php if ($user_list_values->photo_upload_path): ?> 
                                <img src="<?php echo base_url() . $user_list_values->photo_upload_path; ?>"/> 
                            <?php else: ?>
                                <img src="<?php echo base_url(); ?>assets/images/photo.jpg"/> 
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Gender:</td>
                    <td><label class="label_font"><?php echo $result['gender']; ?></label></td>
                    <td class="td_heading">Date of Birth:</td>
                    <td><label class="label_font"><?php echo ($result['dob'] == '0000-00-00')?'':formated_date($result['dob'], '-'); ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Country of Residence:</td>
                    <td><label class="label_font"><?php if($result['country_of_residence'] == 'SGP'){echo "Singapore";} ?></label></td>
                    <td>
                        <strong>NRIC/FIN No. Type:</strong> 
                    </td>
                    <td>
                        <?php
                            if ($result['other_identi_type'] != NULL && $result['other_identi_type'] != '')
                            {
                                $other_identi_type = get_param_value($result['other_identi_type']);
                                $other_identi_type= " (" . $other_identi_type->category_name . " )";
                            }
                            echo get_catname_by_parm($result['tax_code_type']) . $other_identi_type;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">NRIC/FIN No.:</td>
                    <td><label class="label_font"><?php echo $result['tax_code']; ?></label></td>
                    <td class="td_heading">Contact Number(P):</td>
                    <td><label class="label_font"><?php echo $result['contact_number']; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Contact Number(M):</td>
                    <td><label class="label_font"><?php echo $result['alternate_contact_number']; ?></label></td>
                    <td class="td_heading">Email Id(P):</td>
                    <td><label class="label_font"><?php echo $result['registered_email_id']; ?></label></td>
                </tr>
                <tr>
                    <td class="td_heading">Alternate Email Id(P):</td>
                    <td colspan='4'><label class="label_font"><?php echo $result['alternate_email_id']; ?></label></td>
                </tr>
                 <tr>
                    <td class="td_heading">Updation Done By:</td>
                    <td colspan='4'><label class="label_font"><?php echo $act_by->first_name; ?></label></td>
                </tr>
                
                      
            </tbody>
        </table>
    </div>      
   
    <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/other_details.png"/>  Staff Other Details</h2>
    <div class="table-responsive">
        <table class="table table-striped">
            <tbody>
                <?php if($res->module_id == 1){?>
                <tr>
                    <td class="td_heading" width="20%">Role:</td>
                    <td colspan="3"><label class="label_font"><?php 
                    foreach($result['role_id'] as $role)
                    {
                       $str[] = $role->role_id;
                        
                    }
                    $rols = implode(',',$str);
                    echo $rols;
                     ?></label>
                    
                    </td>
                </tr><?php }?>
               
                <tr>
                    <td class="td_heading">Username/ Login Name:</td>
                    <td colspan="3"><label class="label_font"><?php echo $result['user_name']; ?></label>
                   
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Account Status : </td>
                    <td colspan="3" class="td_heading">
                       <?php if($result['account_status']=='INACTIV'){
                           echo "<font color='red'>" . $result['account_status'] . "</font>";
                           }else if($result['account_status'] == 'ACTIVE'){ echo "Active";}?>
                       
                       
                    </td>
                </tr>  
                
                 <?php if( $result['account_status'] == 'INACTIV'){?>
                <tr>
                    <td class="td_heading" colspan="3"> 
                       
                        <?php 
                            $deactivation_details = '';
                            $deactivated_by = $this->activitylog->user_details($result['deacti_by']);
                            $deactivation_details = '  Deactivated by ' . $deactivated_by->first_name . ' ' . $deactivated_by->last_name . '(' . $deactivated_by->user_name . ') on ' . date("d/m/Y", strtotime($result['acct_deacti_date_time'])) . '.';
                            if ($result['deacti_reason'] == 'OTHERS') {
                                $deactivation_details .= ' Reason: ' . $result['deacti_reason_oth'].'.';
                            } else {
                                $user_deactivation_reason = get_param_value($result['deacti_reason']);
                                $deactivation_details .= ' Reason: ' . $user_deactivation_reason->category_name.'.';
                            }
                            echo "<font color='red'>" . $deactivation_details . "</font>";?></td>
                    
                </tr><?php }?>
               
            </tbody>
        </table>
    </div>
    <br>
   
</div>
    <?php }?>
        <div style="clear:both;"></div>

</div>
