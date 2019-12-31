<div class="col-md-10">    
 <?php
    if ($this->session->flashdata('companyadded')) {
        echo '<div class="success">' . $this->session->flashdata('companyadded') . '!</div>';
    }
    if ($this->session->flashdata('companyupdated')) {
        echo '<div class="success">' . $this->session->flashdata('companyupdated') . '!</div>';
    } 
    if ($this->session->flashdata('company_deactivated')) {
        echo '<div class="success">' . $this->session->flashdata('company_deactivated') . '!</div>';
    }  
    if ($this->session->flashdata('company_db_error')) {
        echo '<div class="success">' . $this->session->flashdata('company_db_error') . '!</div>';
    } 
?>    
<h2 class="panel_heading_style"><img src="<?php  echo base_url(); ?>/assets/images/company.png" /> Company</h2>
		  <div class="table-responsive">
<?php
$atr = 'id="searchCompanyForm" name="searchCompanyForm" method="get"';
echo form_open("company", $atr); 
?>                      
    <table class="table table-striped">
      <tbody>
        <tr>
          <td class="td_heading">Search by Company Name:</td>
          <td colspan='3'>
              <input type="text" name="search_company_name" id="list_search_company_name" 
                     value="<?php echo $this->input->get('search_company_name'); ?>" style="width:550px"/>
              <input id="list_search_company_id" name="list_search_company_id" type="hidden" value="<?php echo $this->input->get('list_search_company_id') ?>"/>
              <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
              <span id="list_search_company_name_err"></span>
          </td>
        </tr>
        <tr>
          <td class="td_heading">Business Type:</td>
          <td colspan="2">
            <?php
            $btype = fetch_metavalues_by_category_id(Meta_Values::BUSINESS_TYPE);
            $btype_options[''] = 'Select';
            foreach ($btype as $item):
                $btype_options[$item['parameter_id']] = $item['category_name'];
            endforeach;

            $js = 'id="btype"';
            echo form_dropdown('business_type', $btype_options, $this->input->get('business_type'), $js);
            ?>              
          </td>
          <td align="center">
              <button type="submit" value="Search" title="Search" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
          </td>
        </tr>
        <tr>
          <td class="td_heading">Filter by Status:</td>
          <td colspan="4">
            <?php
            $status_options[''] = 'All';
            foreach ($filter_status as $status):
                $status_options[$status['parameter_id']] = $status['category_name'];
            endforeach;
            $attr_js = 'id="filter_status"';
            echo form_dropdown('filter_status', $status_options, $this->input->get('filter_status'), $attr_js);
            ?>           
          </td>
        </tr>
      </tbody>
    </table>
   <?php echo form_close(); ?>                          
	</div>
		  <div class="bs-example">
          <div class="table-responsive">
          <div class="add_button space_style">
              <?php if(count($tabledata)> 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['COMP'])){ ?>
              <a href="<?php echo site_url('company/export_company_page_fields' . $export_url)?>" class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export Page Fields</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('company/export_company_all_fields' . $export_url)?>" class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export All Fields</span></a>              
              <?php } ?>
          </div>
          <div style="clear:both;"></div>
    <table class="table table-striped">
      <thead>
              <?php  
               $ancher =  (($sort_order == 'asc') ? 'desc' : 'asc');
               $pageurl = $controllerurl;               
              ?>
        <tr>         
            <th width="13%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl."?".$query_string."&f=cm.company_name&o=" .$ancher; ?>" >Company Name</a></th>
            <th width="20%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl."?".$query_string."&f=tc.last_modified_on&o=" .$ancher; ?>" >Last Activity</a></th>
            <th width="40%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl."?".$query_string."&f=cm.comp_address&o=" .$ancher; ?>" >Contact Details</a></th>
            <th width="15%" class="th_header">Company Discount</th>
            <th width="8%" class="th_header">Registered Trainees</th>
            <th width="6%" class="th_header">Active Trainees</th>
            <th width="6%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl."?".$query_string."&f=cm.comp_scn&o=" .$ancher; ?>" >SCN</a></th>
            <th width="7%" class="th_header"><a style="color:#000000;" href="<?php  echo base_url().$pageurl."?".$query_string."&f=tc.comp_status&o=" .$ancher; ?>" >Status</a></th>
          </tr>
      </thead>
      <tbody>
<?php if(count($tabledata) == 0){ ?>
      <tr class="danger">
          <td colspan="7" align="center" class="error">No companies found!</td>
      </tr>          
<?php } ?>          
      <?php foreach($tabledata as $data){ ?>
      <tr <?php if($data['comp_status'] == 'Inactive') echo 'class="danger"'; ?> >
      <td valign="top"><a href="<?php echo site_url(); ?>company/view_company/<?php echo $data['company_id'];?>"><?php echo $data['company_name']." (".$data['company_id'].")";?></a></td>
      <td><?php echo $data['last_activity_details'];?></td>
      <td>
          <?php  
            $contact_details = '';
            if($data['comp_address'] != '') {
                $contact_details = $data['comp_address'] . ', ';
            }            
            if($data['comp_city'] != '') {
                $contact_details .= $data['comp_city'] . ", ";
            }
            if($data['comp_state'] != '') {
                $contact_details .=  $data['comp_state'] . ", ";
            }
            if($data['comp_cntry'] != '') {
                $contact_details .= $data['comp_cntry'] . ", ";
            }
            if($data['comp_zip'] != '') {
                $contact_details .= $data['comp_zip'] . ", ";
            }
            if($data['comp_phone'] != '') {
                $contact_details .= "Phone : ". $data['comp_phone'];
            }             
            echo $contact_details;
          ?>
      </td>
      <td align="center">
        <a href="#ex144<?php echo $data['company_id'];?>" rel="modal:open" style="color: black;">
            <span class="glyphicon glyphicon-eye-open"></span>
        </a>
          <?php if(empty($data['company_discount'])){ ?>
              <div class="modal0000" id="ex144<?php echo $data['company_id'];?>" style="display:none;">
        <p>
        <h2 class="panel_heading_style">Alert Message</h2>
        There are no courses available.<br>
        <div class="popup_cancel popup_cancel001">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Ok</button></a></div>
    </p>
    </div>
          <?php }else{?>
          <div class="modal_333" id="ex144<?php echo $data['company_id'];?>" style="display:none;">
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
                                foreach ($data['company_discount'] as $row) {
                                    $k = $row['course_id'];
                                    echo "<tr>
                                        <td>" . $row['crse_name'] . "</td>
                                        <td>" . number_format($row['Discount_Percent'], 2, '.', '') . " %</td>
                                        <td>$ " . number_format($row['Discount_Amount'], 2, '.', '') . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    </p>
                </div>
          <?php } ?>
      </td>
      <td align="center"><?php echo $data['num_registered_trainees'];?></td>
      <td align="center"><?php echo $data['num_active_trainees'];?></td>
      <td><?php echo $data['SCN'];?></td>
      <td>
          <?php 
          $comp_status_class = '';
          if($data['comp_status_param_id'] == 'ACTIVE') {
              $comp_status_class = 'green';
          }
          if($data['comp_status_param_id'] == 'INACTIV') {
              $comp_status_class = 'red';
          }        
          if($data['comp_status_param_id'] == 'PENDACT') {
              $comp_status_class = 'blue';
          }          
          echo '<span class='.$comp_status_class.'>' . $data['comp_status'] . '</span>';            
          ?>
      </td>
      </tr>
      <?php } ?>
    </table>
    </div>
	<ul class="pagination pagination_style">
   <?php echo $pagination; ?>
   </ul>
  </div>
       </div>
      </div>
<script>
    $(document).ready(function(){
        var form_search=0;
//       $('#searchCompanyForm') .submit(function(){
//          form_search = 1; 
//          return validate(true);
//       });
       
       //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        $('#searchCompanyForm').on('submit',function() {
            form_search = 1;
            //alert("form click");
            var status=validate(true);
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
       $('#searchCompanyForm select, #searchCompanyForm input').submit(function(){
          if(form_search){
              return validate(false);
          } 
       });
       $('#list_search_company_name').on("blur", function() {
        $list_search_company_name = $('#list_search_company_name').val().trim();
            if($list_search_company_name ==''){
                $("#list_search_company_id").val("");
            }  
       });
       function validate(retval){
           var company_name = $('#list_search_company_name').val().trim();
           var company_id = $('#list_search_company_id').val();
           if(company_name.length >0 && company_id.length ==0){
               retval = false;
               disp_err('#list_search_company_name','[Select from auto-help.]');
           }else{
               remove_err('#list_search_company_name');
           }
           return retval;
       }
    });
    //added by shubhranshu for export validate
    function form_validates() {
        $company_name = $('#list_search_company_name').val();
       if($company_name != ''){
            remove_err('#list_search_company_name');
            return true;
        } 
        else {
            disp_err('#list_search_company_name', '[Select Company name from auto-complete]');
            return false;
        }
    }
    function disp_err($id, $text) {
        $text = typeof $text !== 'undefined' ? $text : '[required]';
        $($id).addClass('error');
        $($id + '_err').addClass('error').addClass('error3').html($text);
    }
    function remove_err($id) {
        $($id).removeClass('error');
        $($id + '_err').removeClass('error').removeClass('error3').text('');
    }
</script>