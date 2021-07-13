 <?php     $this->load->helper('common_helper');
 ?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<div class="col-md-12">
        <?php        
        if ($this->session->flashdata('success')) {
            echo '<div class="success">' .$this->session->flashdata('success'). '</div>';
        }
        if ($this->session->flashdata('error')) {
            echo '<div class="success">' . $this->session->flashdata('error') . '</div>';
        }
        ?>
        <h2 class="panel_heading_style"><span class="glyphicon glyphicon-eye-open"></span> View Trainee Details</h2>		  
            <div class="bs-example">
                <div class="table-responsive">          
                    <table class="table table-striped">      
                    <tbody>
                        <tr>
                            <td width="23%" class="td_heading">Country of Residence:<span class="required">*</span></td>
                            <td width="16%"><label class="label_font"><?php echo ($trainee[userdetails]['country_of_residence'])?get_catname_by_parm($trainee[userdetails]['country_of_residence']):''; ?></label></td>
                            <?php if($trainee[userdetails]['country_of_residence'] == 'SGP') {?> 
                                <td colspan="2">                                    
                                    <?php if($trainee[userdetails]['tax_code_type'] != 'OTHERS' ) {?> 
                                        <strong>NRIC Type:</strong> <?php echo ($trainee[userdetails]['tax_code_type'])?get_catname_by_parm($trainee[userdetails]['tax_code_type']):''; ?>  &nbsp;&nbsp;&nbsp; 
                                        <strong>NRIC Code:</strong> <?php echo $trainee[userdetails]['tax_code']; ?>
                                    <?php } else { ?>                                       
                                        <strong>OTHER:</strong> <?php echo ($trainee[userdetails]['other_identi_type'])?get_catname_by_parm($trainee[userdetails]['other_identi_type']):''; ?>&nbsp;&nbsp;&nbsp;
                                        <strong>Code:</strong> <?php echo $trainee[userdetails]['other_identi_code']; ?>
                                    <?php } ?>    
                                </td>
                            <?php } else { ?>
                                <td colspan="2">
                                    <strong><?php echo $trainee[userdetails]['tax_code_type']; ?> No.:</strong>
                                    <?php echo $trainee[userdetails]['tax_code']; ?>
                                </td>
                            <?php } ?>
                        </tr>          
                        <tr>
                          <td class="td_heading">Username:<span class="required">*</span></td>
                          <td colspan="3"><label class="label_font"><?php echo $trainee[userdetails]['user_name']; ?></label></td> 
                        </tr>
                    </tbody>
                    </table>    
                </div>
            </div>
		  <br>
		  <h2 class="sub_panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/personal_details.png"> Personal Details</h2>
          
		  
		  <div class="table-responsive">
		  <table class="table table-striped">
      
      <tbody>
      
        <tr>
            <td class="td_heading" width="15%">First Name:<span class="required">*</span></td>
            <td><label class="label_font"><?php echo $trainee[userdetails]['first_name']; ?></label></td>
            <td class="td_heading" width="15%">Last Name:<span class="required">*</span></td>
            <td><label class="label_font"><?php echo $trainee[userdetails]['last_name']; ?></label></td>
            <td class="td_heading" width="15%">Gender:<span class="required">*</span></td>
            <td>
                <label class="label_font"><?php echo ($trainee[userdetails]['gender'])?get_catname_by_parm($trainee[userdetails]['gender']):''; ?></label>
            </td>
            <td rowspan="3" align="center">
            <div class="photo">
                <img src="<?php  echo site_url();?>/profileuploads/refer_trainee/<?php echo $trainee[userdetails]['photo_upload_path'] ?>" height="50px"width="100px"> 
            </div>
            </td>
            
        </tr>        
        <tr>
            <td class="td_heading">Date of Birth:<span class="required">*</span></td>
            <td><?php echo date('d/m/Y', strtotime($trainee[userdetails]['dob'])); ?></td>
            <td class="td_heading">Contact Number:<span class="required">*</span></td>
            <td><label class="label_font"><?php echo $trainee[userdetails]['contact_number']; ?></label></td>
            <td class="td_heading">Alternate Contact Number:</td>
            <td><label class="label_font"><?php if(!empty($trainee[userdetails]['alternate_contact_number'])){echo $trainee[userdetails]['alternate_contact_number']; }else{ echo 'Nil';}?></label></td>
        </tr> 
              <tr>
            <td class="td_heading">Email Id:</td>
            <td><label class="label_font"><?php echo $trainee[userdetails]['registered_email_id']; ?></label></td>
            <td class="td_heading">Confirm Email Id:</td>
            <td><label class="label_font"><?php echo $trainee[userdetails]['registered_email_id']; ?></label></td>
            <td class="td_heading">Alternate Email Id:</td>
            <td>
                <label class="label_font"><?php if(!empty($trainee[userdetails]['alternate_email_id'])){echo $trainee[userdetails]['alternate_email_id']; }else{echo 'Nil';}?></label>
            </td>
        </tr>
        <tr>
          
<!--            <td><label class="label_font"><?php echo (get_companyname($trainee[userdetails]['tenant_org_id']))?get_companyname($trainee[userdetails]['tenant_org_id']):'NA'; ?></label></td>-->
            <td class="td_heading">Race:</td>
            <td><label class="label_font"><?php echo ($trainee[userdetails]['race'])?get_catname_by_parm($trainee[userdetails]['race']):''; ?></label></td>
            <td class="td_heading">Salary Range:</td>
            <td colspan="3">
                <label class="label_font"><?php echo ($trainee[userdetails]['salary_range'])?get_catname_by_parm($trainee[userdetails]['salary_range']):''; ?></label>
            </td>
        </tr>        
 
    <tr>
       
        </tbody>
        </table>
        </div>
        <br>
		  <h2 class="sub_panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/address.png"> Address</h2>
        <div class="table-responsive">
		  <table class="table table-striped">
      
      <tbody>
        <tr>
          <td class="td_heading" width="20%">Building/Street:<span class="required">*</span></td>
          <td colspan="3"><label class="label_font"><?php echo $trainee[userdetails]['personal_address_bldg']; ?></label></td>
		  <td class="td_heading" width="20%">City:<span class="required">*</span></td>
          <td><label class="label_font"><?php echo $trainee[userdetails]['personal_address_city']; ?></label></td>
          
              </td>
        </tr>
        <tr>
        <td class="td_heading">Country:<span class="required">*</span></td>
          <td> <label class="label_font"><?php echo get_catname_by_parm($trainee[userdetails]['personal_address_country']); ?></label></td>
          <td class="td_heading">State:<span class="required">*</span></td>
          <td> <label class="label_font"><?php echo get_catname_by_parm($trainee[userdetails]['personal_address_state']); ?></label></td>
          
          <td class="td_heading">Zip Code:</td>
          <td colspan="3"><label class="label_font"><?php if(!empty($trainee[userdetails]['personal_address_zip'])){ echo $trainee[userdetails]['personal_address_zip'];}else{
        echo 'Nil';} ?></label></td>
		  
        </tr>
        <tr>
          <td class="td_heading">Activation Status:</td>
          <td class="td_heading"> <label class="">
                  <?php if($trainee[userdetails]['account_status'] == 'ACTIVE') 
                                echo "<font class='green-active'>Active</font>";
                        else if($trainee[userdetails]['account_status'] == 'INACTIVE')
                            echo "<font class='red-inactive'>In-Active</font>";
                        else 
                            echo "<font class='red-inactive'>Pending Activation</font>";
                        ?>
                  </label></td>
          <td colspan="4"></td>
        </tr>
      </tbody>
    </table>
	</div>
		  <br>
          <h2 class="sub_panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/education.png"> Educational Details</h2>
          <div class="table-responsive">
          <table class="table table-striped">
          <thead>
  <tr>
    <th>Level</th>
    <th>Year of Completion</th>
    <th>Score/Grade</th>
    <th>Remarks</th>
  </tr>
  </thead>
  <tbody>
      <?php if(!$trainee[edudetails]) {
          echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
          } ?>
      <?php foreach($trainee[edudetails] as $item): ?>
  <tr>
    <td><label class="label_font"><?php echo ($item['educ_level'])?get_catname_by_parm($item['educ_level']):''; ?></label>
    </td>
    <td>        
        <label class="label_font"><?php echo ($item['educ_yr_completion'])?$item['educ_yr_completion']:''; ?></label>    
    </td>
    <td><label class="label_font"><?php echo $item['educ_score']; ?></label></td>
    <td><label class="label_font"><?php echo $item['educ_remarks']; ?></label></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>

<div class="table-responsive">
<br>
<h2 class="sub_panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/personal_details.png"> Other Certificates and Trainings</h2>
          
          <table class="table table-striped">
          <thead>
  <tr>
    <th width="22%">Certificate Name</th>
    <th width="25%">Year of Certification</th>
    <th width="19%">Validity</th>
    <th width="34%">Remarks</th>
  </tr>
  </thead>
  <tbody>
      <?php if(!$trainee[otherdetails]) {
          echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
          
      } 
   
      ?>
       <?php foreach($trainee[otherdetails] as $item): ?>
  <tr>
    <td><label class="label_font"><?php echo $item['cert_name']; ?></label>
    </td>
    <td><label class="label_font"><?php echo ($item['yr_completion'])?$item['yr_completion']:''; ?></label></td>
    <td><label class="label_font"><?php echo ($item['valid_till'])?date('d/m/Y',strtotime($item['valid_till'])):''; ?></label></td>
    <td><label class="label_font"><?php echo $item['oth_remarks']; ?></label></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>

</div>

<div class="table-responsive"><br>
		<h2 class="sub_panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/officail_details.png"> Work Experience</h2>
          
    <table class="table table-striped">
        <thead>
              
            <tr>
              <th width="22%">Name of Organization</th>
              <th width="25%">Employment From</th>
              <th width="19%">Employment To</th>
              <th width="34%">Designation/ Remarks</th>
            </tr>
        </thead>
        <tbody>      
          <?php 
          
        
          if(!$trainee[workdetails]) {
              echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
          } 
         
          ?>
          <?php foreach($trainee[workdetails] as $item): ?>
              <tr>
                <td><label class="label_font"><?php echo $item['org_name']; ?></label>
                </td>
                <td><label class="label_font"><?php echo ($item['emp_from_date'])?date('d/m/Y', strtotime($item['emp_from_date'])):''; ?></label></td>
                <td><label class="label_font"><?php echo ($item['emp_to_date'])?date('d/m/Y', strtotime($item['emp_to_date'])):''; ?></label></td>
                <td><label class="label_font"><?php echo ($item['designation'])?get_catname_by_parm($item['designation']):''; ?></label></td>
              </tr>
          <?php endforeach; ?>
        </tbody>
    </table>  
</div>


		  <br>
<!--    <div class="small_heading">
        Training Attended Details as on <?php echo date('M j Y, l'); ?>

    </div>		  
    <div class="table-responsive">        
        <table class="table table-striped">
            <thead>
                <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                ?>
              <tr>
                  <th width="19%" class="th_header"><a style="color:#000000;" href="">Training Name</a></th>
                <th width="12%" class="th_header"><a style="color:#000000;" href="">Enrollment Date</a></th>
                <th width="12%" class="th_header"><a style="color:#000000;" href="">Enrollment Type</a></th>
                <th width="13%" class="th_header"><a style="color:#000000;" href="">Training End Date</a></th>
                <th width="6%" class="th_header">Validity</th>
                <th width="5%" class="th_header">Status</th>
                <th width="35%" class="th_header">Action</th>
              </tr>
            </thead>
            <tbody>
                <?php 
                if(count($training_details)>0) {
                    foreach ($training_details as $item ) :?>
                        <tr>                
                            <td><?php echo $item->crse_name."-".$item->class_name; ?></td>
                            <td><?php echo ($item->enrolled_on)?date('d/m/Y',strtotime($item->enrolled_on)):''; ?></td>
                            <td><?php echo ($item->enrolment_mode != '') ? get_catname_by_parm($item->enrolment_mode) : ''; ?></td>
                            <td><?php echo ($item->class_end_datetime)?date('d/m/Y',strtotime($item->class_end_datetime)):''; ?></td>
                            <td>
                                <?php 
                                if($item->crse_cert_validity !=0) {
                                    $class_end_datetime = date("Y/m/d", strtotime($item->class_end_datetime));                            
                                    $date = strtotime("+".$item->crse_cert_validity." days", strtotime($class_end_datetime));
                                    $validity = date('d/m/Y',$date); 
                                    echo $validity;
                                }else {
                                    echo "Life Long";
                                }
                                ?>
                            </td>
                            <td>                        
                                <?php                         
                                if($date >=  strtotime(date('Y/m/d')) && $item->crse_cert_validity !=0) {
                                    echo "Active";
                                } else if($item->crse_cert_validity !=0) {
                                    echo "<span class='blink'>Renewal Due</span>";
                                } else {
                                    echo "Life Long";
                                }

                                ?>
                            </td>
                            <td>
                                <?php
                                    $paid = '<a href="' . base_url() . 'class_trainee/update_payment" style="color:red" >Not Paid</a>';
                                    if ($item->payment_status == 'PAID') {
                                        $paid = '<a href="javascript:;" class="small_text1 paid_href" data-class="' . $item->class_id . '" data-user="' . $item->user_id . '">Paid</a>';
                                        ;
                                    }
                                
                                ?>                                
                                <a href="javascript:;" class="get_update" data-class="<?php echo $item->class_id; ?>" data-user="<?php echo $item->user_id; ?>">Subsidy & TG# </a>&nbsp;&nbsp;&nbsp;&nbsp;                                
                                <?php echo $paid; ?>&nbsp;&nbsp;&nbsp;
                                <?php if (strtotime($class_end_datetime) <= strtotime(date('Y/m/d'))) { ?>
                                    <a id="training_update" href="#ex7" rel="modal:open" data-course="<?php echo $item->course_id; ?>" data-class="<?php echo $item->class_id; ?>" data-user="<?php echo $item->user_id; ?>" class="training_update small_text1">Training Update</a>&nbsp;&nbsp;&nbsp;
                                <?php } ?>
                                <a href="#ex6" data-payid="<?php echo $item->pymnt_due_id; ?>" class="receipt small_text1">Receipt</a>
                            </td>                 
                        </tr>
            <?php 
                endforeach;
            } else {
                echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>No data available.</label></td></tr>";
            }
            ?>
            </tbody>      
        </table>    -->
    </div> 
                  <br>
<div class="button_class">
        <a href="<?php echo site_url(); ?>user/dashboard">
            <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back</button>
        </a>
</div>
       </div>

<div class="modal" id="ex1" style="display:none;">
  <p>
  <h2 class="panel_heading_style">Heading Goes Here...</h2>
  Detail Goes here.  <br>
  
  <div class="popup_cancel">
  <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a></div></p>
</div>

<div class="modal1_0001" id="ex9" style="display:none;height:250px;">
    <h2 class="panel_heading_style">Update Subsidy</h2>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading">Subsidy Date:<span class="red">*</span></td>
                <td> <?php
                    $subsidy_date = array(
                        'name' => 'subsidy_date',
                        'id' => 'subsidy_date',
                        'readonly' => 'readonly',
                        'style' => 'width:45%',
                    );
                    echo form_input($subsidy_date);
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
                    <span id="subsidy_date_err"></span>
                </td>
            </tr>
            <tr>
                <td class="td_heading">Subsidy Amount:<span class="red">*</span></td>
                <td>$ <?php
                    echo form_input('subsidy_amount', $this->input->post('subsidy_amount'), ' id="subsidy_amount"');
                    ?> SGD &nbsp;&nbsp;<strong>OR</strong>&nbsp;&nbsp; <?php
                    echo form_input('subsidy_per', $this->input->post('subsidy_per'), ' id="subsidy_per"');
                    ?> %
                    <span id="subsidy_amount_err"></span>
                </td>
            </tr>
            <tr>
                <td class="td_heading">TG#:</td>
                <td><?php
                    echo form_input('tg_number', $this->input->post('tg_number'), ' id="tg_number"');
                    ?></td>
            </tr>
        </tbody>
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cance89">
        <span href="#ex9" rel="modal:close"><button class="btn btn-primary subsidy_save" type="button">Save</button></span>
    </div>
  
</div>


<div class="modal_3" id="ex8" style="display:none;">
    <p>  
        <h2 class="panel_heading_style">Payment Received Details</h2>  
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
                    <td class="td_heading">Class Fees:</td>
                    <td><span class="r_class_fees"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading"><span class="r_dis_label"></span> Discount @ <span class="r_dis_rate"></span>%:</td>
                    <td><span class="r_dis_amount"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading">Subsidy:</td>
                    <td><span class="r_subsidy_amount"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading">Amount Due:</td>
                    <td><span class="r_after_gst"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading">GST @ <span class="r_gst_rate"></span>% (<span class="r_gst_label"></span>):</td>
                    <td><span class="r_total_gst"></span> SGD</td>
                </tr>
                <tr>
                    <td class="td_heading">Net Due:</td>
                    <td><span class="r_net_due"></span> SGD</td>
                </tr>
            </tbody>
        </table><br />
        <div class="popup_cance89">
            <a class="payment_recd_href" href="#"><button type="button" class="btn btn-primary">Print</button></a>
        </div>
    </p>
</div>

<?php
    $atr = 'id="trainer_feedback_form" name="trainer_feedback_form" ';
    echo form_open("trainee/trainer_feedback/$item->user_id/$item->course_id/$item->class_id", $atr);
?>
<div class="modal1_050" id="ex7" style="display:none; height:415px;">
    <h2 class="panel_heading_style">Training Update</h2>    
    <table class="table table-striped">
        <tbody>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('CERTCOLDT');?>:</td>
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
                <td class="td_heading"><?php echo get_catname_by_parm('SATSRATE');?>:</td>
                <td>                    
                    <?php                                                     
                        $satisfaction_rating = array('' =>'Select', '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5);
                        $satisfaction_rating_attr = 'id="satisfaction_rating"';
                        echo form_dropdown('SATSRATE', $satisfaction_rating, '', $satisfaction_rating_attr);
                    ?>   
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('CERTCOM1');?>:</td>
                <td>
                    <?php 
                    $CERTCOM1_YES = array(
                                'name'       => 'CERTCOM1',
                                'value'      => 'Y',                                
                                'id'         => 'CERTCOM1_YES'                                
                                );
                     $CERTCOM1_NO = array(
                                'name'       => 'CERTCOM1',
                                'id'         => 'CERTCOM1_NO',
                                'value'      => 'N',                                
                                );
                    ?>              
                    <?php echo form_radio($CERTCOM1_YES);?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($CERTCOM1_NO);?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('APPKNLSKL');?>?</td>
                <td>                    
                    <?php 
                    $APPKNLSKL_YES = array(
                                'name'       => 'APPKNLSKL',
                                'value'      => 'Y',                                
                                'id'         => 'APPKNLSKL_YES'                                
                                );
                     $APPKNLSKL_NO = array(
                                'name'       => 'APPKNLSKL',
                                'id'         => 'APPKNLSKL_NO',
                                'value'      => 'N',                                
                                );
                    ?>              
                    <?php echo form_radio($APPKNLSKL_YES);?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($APPKNLSKL_NO);?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('EXPJOBSCP');?>?</td>
                <td>                    
                    <?php 
                    $EXPJOBSCP_YES = array(
                                'name'       => 'EXPJOBSCP',
                                'value'      => 'Y',                                
                                'id'         => 'EXPJOBSCP_YES'                                
                                );
                     $EXPJOBSCP_NO = array(
                                'name'       => 'EXPJOBSCP',
                                'id'         => 'EXPJOBSCP_NO',
                                'value'      => 'N',                                
                                );
                    ?>              
                    <?php echo form_radio($EXPJOBSCP_YES);?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($EXPJOBSCP_NO);?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('RT3MNTHS');?>?</td>
                <td>                   
                    <?php 
                    $RT3MNTHS_YES = array(
                                'name'       => 'RT3MNTHS',
                                'value'      => 'Y',                                
                                'id'         => 'RT3MNTHS_YES'                                
                                );
                    $RT3MNTHS_NO = array(
                                'name'       => 'RT3MNTHS',
                                'id'         => 'RT3MNTHS_NO',
                                'value'      => 'N',                                
                                );
                    ?>              
                    <?php echo form_radio($RT3MNTHS_YES);?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($RT3MNTHS_NO);?> No
                </td>
            </tr>
            <tr>
                <td class="td_heading"><?php echo get_catname_by_parm('DTCOMMEMP');?>:</td>
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
                <td class="td_heading"><?php echo get_catname_by_parm('COMYTCOM');?>?</td>
                <td>                    
                    <?php 
                    $COMYTCOM_YES = array(
                                'name'       => 'COMYTCOM',
                                'value'      => 'Y',                                
                                'id'         => 'COMYTCOM_YES'                                
                                );
                    $COMYTCOM_NO = array(
                                'name'       => 'COMYTCOM',
                                'id'         => 'COMYTCOM_NO',
                                'value'      => 'N',                                
                                );
                    ?>              
                    <?php echo form_radio($COMYTCOM_YES);?> Yes &nbsp;&nbsp;
                    <?php echo form_radio($COMYTCOM_NO);?> No
                </td>
            </tr>
            <tr>
                <td colspan="2" class="td_heading">
                    <span style="vertical-align:top;"><?php echo get_catname_by_parm('COMMNTS');?>:</span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <span>                        
                        <?php 
                        $data = array(
                            'name'        => 'COMMNTS',
                            'id'          => 'COMMNTS',                            
                            'rows'        => '1',
                            'cols'        => '60',
                            'style'       => 'width:70%',
                            'maxlength'   => '250'
                          );

                        echo form_textarea($data);
                        ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>    
    <div class="popup_cance89">        
        <div class="popup_cancel9">
            <div rel="modal:close">
                <button class="btn btn-primary" type="submit">Save</button>&nbsp;&nbsp;
                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button></a>
            </div>
        </div>
    </div>
    </p>
</div>
<?php     
    echo form_close();
?>


<div class="modal_333" id="ex3" style="display:none;">
  <p>
  
  <h2 class="panel_heading_style">Payment Received Details</h2>
  
  <table class="table">
  <tbody>
  
  <tr>
  <td class="td_heading">Invoice #:</td>
  <td>INV001</td>
  <td class="td_heading">Invoice Dt:</td>
  <td>27/03/2014</td>
  <td class="td_heading">Company Name:</td>
  <td>Xprienz</td>
  </tr>
  
  <tr>
  <td class="td_heading">Invoice Amount:</td>
  <td>$337.05 SGD</td>
  <td class="td_heading">Discount @10.00%:</td>
  <td>$35.00 SGD</td>
  <td class="td_heading">Subsidy:</td>
  <td>$00.00 SGD</td>
  
  </tr>
  
  <tr>
  <td class="td_heading">GST @7.00%:</td>
  <td>$22.05 SGD</td>
  <td class="td_heading">Total Fees:</td>
  <td colspan="3">$350.00 SGD</td>
  </tr>
  </tbody>
  </table><br>
  
  <table style="width:60%; margin:0 auto;" class="table table-striped">
  <thead>
  
  <tr>
  <th>Payment Recd. On</th>
  <th>Mode</th>
  <th>Amt. Recd.</th>
  </tr>
  </thead>
  
  <tbody>
  <tr>
  <td>01/04/2014</td>
  <td>Online Transfer</td>
  <td>$168.55 SGD</td>
  </tr>
  
  <tr>
  <td>20/04/2014</td>
  <td>Cheque (Chq#:023456,Chq Dt:20/04/2014)</td>
  <td>$168.50 SGD</td>
  </tr>
  
  </tbody>
  </table>
  <br>
  <div class="popup_cance89">
  <a rel="modal:close" href="#"><button type="button" class="btn btn-primary">Print</button></a></div>

  </p>
</div>
<div class="modal_020" id="ex6" style="display:none;">
    <p>

    <h2 class="panel_heading_style">Payment Receipt</h2><br>
    <table width="100%">
        <tbody>
            <tr>
                <td rowspan="4"><img src="" class="logo" border="0" /></td>
                <td colspan="2"><span class="r_tenant_name"></span></td>
                <td class="td_heading"></td>
                <td><span class="r_invoice_no"></span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="r_tenant_address"></span></td>
                <td class="td_heading"></td>
                <td><span class="r_invoice_recd"></span></td>
            </tr>
            <tr>
                <td colspan="4"><span class="r_tenant_phone"></span></td>
            </tr>

        </tbody>
    </table><br>

    <p><font style="font-size:14px; font-weight:bold;">Received</font> with thanks SGD <span class="r_invoice_amount_recd"></span> from <span class="r_invoice_trainee"></span> for <strong><i>'<span class="r_course"></span>-<span class="r_class"></span>-<span class="r_certilevel"></span>'</i></strong>. Mode of payment: <span class="r_invoice_mode"></span></p>

    <table class="table table-bordered">

        <tbody>
            <tr>
                <td class="td_heading">Class Start Date:</td>
                <td><span class="r_class_start"></span></td>
            </tr>

            <tr>
                <td class="td_heading">Location:</td>
                <td><span class="r_class_loc"></span></td>
            </tr>

            <tr>
                <td class="td_heading">Contact Details:</td>
                <td><span class="r_course_manager"></span>, <span class="r_tenant_phone"></span>, <span class="r_tenant_email"></span></td>
            </tr>
        </tbody>
    </table><br><br>
    <p><i>This is a computer generated receipt and doesn't require a seal or signature.</i></p>


    <div style="clear:both;"></div><br>

    <div class="popup_cance89">
        <a href="#" class="print_receipt"><button class="btn btn-primary" type="button">Print</button></a>
    </div>
</p>    
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/public_js/classtraineelist.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/public_js/view_trainee.js"></script>