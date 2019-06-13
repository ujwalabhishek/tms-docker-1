
<div class="col-md-10">
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-list-alt"></span> Manage Tenant - Monthly Enrollment Count</h2>
    <div class="table-responsive">
        <?php
          
            if ($this->session->flashdata('error')) {
                echo '<div class="error">' . $this->session->flashdata('error') . '</div>';
            }
            echo validation_errors('<div class="error">', '</div>');
            
?>
        <?php echo validation_errors(); ?>
        <?php
        $atr = array('id' => 'monthly_enrollment_form', 'name'=>'monthly_enrollment_form', 'method' => 'get');
        echo form_open("manage_tenant/enrollment_count", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Tenant: </td>
                    <td colspan="4">
                        <?php
                        $tenant_id[''] = 'Select';
                        foreach ($tenant_array as $act_module):
                            $tenant_id[$act_module['tenant_id']] = $act_module['tenant_name'];
                        endforeach;

                        $attr_js = 'id="tenant_id" ';
                       
                        echo form_dropdown('tenant_id', $tenant_id, $this->input->get('tenant_id'), $attr_js);
                         ?>
                      <span id="tenant_id_err"></span>   
                    </td>
                </tr>
                
                <tr>
                    <td class="td_heading">From:</td>
                    <td class="td_heading">Month: <span class="required" id="start_month">*</span>&nbsp;&nbsp; <?php echo form_dropdown("fmonth", $month_array, $this->input->get('fmonth'), 'id="fmonth"') ?>
                    <span id="fmonth_err"></span>
                    </td>
                    <td class="td_heading">Year: <span class="required" id="start_year">*</span>
                      
                        &nbsp;&nbsp;<?php echo form_dropdown("fyear", $year_arr, $this->input->get('fyear'), 'id="fyear"') ?>
                    <span id="fyear_err"></span>
                    </td>
                </tr>
                
                 <tr>
                    <td class="td_heading">To:</td>
                    <td class="td_heading">Month: &nbsp;&nbsp;   <?php echo form_dropdown("lmonth", $month_array, $this->input->get('lmonth'), 'id="lmonth"') ?></td>
                    <td class="td_heading">Year: <span class="required" id="last_year">*</span>
                        
                        &nbsp;&nbsp;<?php echo form_dropdown("lyear", $year_arr, $this->input->get('lyear'), 'id="lyear"') ?>
                    <span id="lyear_err"></span>
                    </td>
                </tr>
                
               
                
                
                
            </tbody>
        </table>
         <div class="push_right">
                            <button type="submit" id="skm" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                   
                        </div>
    </div>
    <div style="clear:both;"></div><br/>    
    <?php
       
    if(!empty($tenant_tabledata)){?>
    <div>
            <span class="pull-right">
                <a href="<?php echo site_url('/manage_tenant/total_tenant_enrollment_count_xls') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/manage_tenant/total_tenant_enrollment_count_pdf') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
            </span>
        </div>
        <br><br>
         <table class="table table-striped">
            <thead>
                <tr>
                    
                    <th width="10%" class="th_header">Tenant Name</th>
                  
                    <th width="15%" class="th_header">Total Enrollments</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenant_tabledata as $row) { ?>
                    <tr>
                        <td><?php echo $row->tenant_name; ?></td>
                      
                        <td><?php echo $row->total; ?></td>
                        <!--<td><?php // echo empty($att_data[$data->month])?0:$att_data[$data->month]; ?></td>-->
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
    <?php }else if (!empty($tabledata)) {
        $tenant_id = $this->input->get('tenant_id');
        $fmonth = $this->input->get('fmonth');
        $fyear = $this->input->get('fyear');
        $lmonth = $this->input->get('lmonth');
        $lyear = $this->input->get('lyear');
        
//        $tenant_id = array($tenant_id=>1);
//        print_r($tenant_id);
//        print_r($tenant_array);
//        $tenant_name = array_intersect_key($tenant_array, $tenant_id);
//        print_r($tenant_name);
//echo $tenant_name[$tenant_id]; 
  
  

?>

        <div class = "panel-heading panel_headingstyle" style = "width:100%;">Monthly Enrollment Count Report for
 <?php // echo $tenant_name[$tenant_id]; ?>
            <strong><?php 
            foreach($tenant_array as $val){
                if($tenant_id == $val['tenant_id']){
                   echo  $val['tenant_name'].'&nbsp;';
                }
                }?></strong>
            <?php
             $st_month = array($fmonth =>9);   
            $result = array_intersect_key($month_array, $st_month);
            
            echo $result[$fmonth].'-'.$fyear;
            
            
            if(!empty($lmonth) && !empty($lyear))
            {
                $lt_month = array($lmonth =>9);   
                $result = array_intersect_key($month_array, $lt_month);
                echo ' to '.$result[$lmonth].'-'.$lyear;
            }
            ?>
            
            </strong></div>
        <br>

        <div>
            <span class="pull-right">
                <a href="<?php echo site_url('/manage_tenant/monthly_tenant_enrollment_count_xls') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                <a href="<?php echo site_url('/manage_tenant/monthly_tenant_enrollment_count_pdf') . '?' . $_SERVER['QUERY_STRING']; ?>" class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
            </span>
        </div>
        <br><br>
        
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="10%">Tenant</th>
                    <th width="10%">Month</th>
                    <th width="10%">Year</th>
                    <th width="15%">Total Enrollments</th>
                    <!--<th width="15%" class="th_header">Present`s Attendance Count</th>-->
                </tr>
            </thead>
            <tbody>
                <?php 
                usort($tabledata, function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
                
                foreach ($tabledata as $data) { ?>
                    <tr>
                        <td><?php echo $data['tenant_name']; ?></td>
                        <td><?php echo $data['month']; ?></td>
                        <td><?php echo $data['year']; ?></td>
                        <td><?php echo $data['count']; ?></td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <br>
        <table class="table table-striped">
            <tr class="danger">
                <td style="color:red;text-align: center;">No data available.</td>
            </tr>
        </table>
    <?php } ?>
</div>

<script>
$(document).ready(function(){
    $('#fyear').on('change', function(){
    var value=$(this).val();
     $('select option').prop("disabled", false);
     $("#lyear option").each(function(){
         if($(this).val()<value){
             $(this).prop("disabled",true);
         }
     });
});
});

    
$(document).ready(function(){
        $('#start_month').hide();
        $('#start_year').hide();
        $('#last_year').hide();
         $tenant_id = $('#tenant_id').val();
         
        $('#tenant_id').change(function() {
                $tenant_id = $('#tenant_id').val();
                $fmonth = $('#fmonth').val('');
                $fyear = $('#fyear').val('');
                $lmonth = $('#lmonth').val('');
                $lyear = $('#lyear').val('');
                $('#last_year').hide();
                
                if($tenant_id.length==0){
                    $('#start_month').hide();
                }else{
                    $('#start_month').show(); 
                }
           
        });
        
        $fmonth = $('#fmonth').val();
        $('#fmonth').change(function() { 
                $fmonth = $('#fmonth').val();
                 if($fmonth=='01'||$fmonth=='02'||$fmonth=='03'||$fmonth=='04'||$fmonth=='05'||$fmonth=='06'||$fmonth=='07'||$fmonth=='08'||
                    $fmonth=='09'||$fmonth=='10'||$fmonth=='11'||$fmonth=='12'){

                    $('#start_year').show();

                 }else{
                    $('#start_year').hide();

                 }   
            });
        
        $lmonth = $('#lmonth').val();
        $('#lmonth').change(function() { 
                $lmonth = $('#lmonth').val();
                 if($lmonth=='01'||$lmonth=='02'||$lmonth=='03'||$lmonth=='04'||$lmonth=='05'||$lmonth=='06'||$lmonth=='07'||$lmonth=='08'||
                    $lmonth=='09'||$lmonth=='10'||$lmonth=='11'||$lmonth=='12'){

                    $('#last_year').show();

                 }else{
                    $('#last_year').hide();

                 }   
            });
        
        $('#monthly_enrollment_form').submit(function(){
       
            $retval = true; 
            
            $tenant_id = $('#tenant_id').val();
            $fmonth = $('#fmonth').val();
            $fyear = $('#fyear').val();
            $lmonth = $('#lmonth').val();
            if($tenant_id.length!=0)
            {
                if($fmonth=='')
                {               
                    $("#fmonth_err").text("[required]").addClass('error');
                    $("#fmonth").addClass('error');
                    $retval = false;

                }
                else
                {    
                    $("#fmonth_err").text("").removeClass('error');
                    $("#fmonth").removeClass('error');

                }
                
                if ($fyear.length == 0) 
                {   
                    $('#start_year').show();
                    $("#fyear_err").text("[required]").addClass('error');
                    $("#fyear").addClass('error');
                    $retval = false;
                } 
                else 
                {   
                    $('#start_year').hide();
                    $("#fyear_err").text("").removeClass('error');
                    $("#fyear").removeClass('error');
                }
                
                if($lmonth.length!=0 || $lmonth!='')
                {
                    $lyear = $('#lyear').val();
                    if ($lyear.length == 0) {
                       $('#last_year').show();
                       $("#lyear_err").text("[required]").addClass('error');
                       $("#lyear").addClass('error');
                       $retval = false;
                    } else {
                        $('#last_year').hide();
                        $("#lyear_err").text("").removeClass('error');
                        $("#lyear").removeClass('error');
                    }
                }
                    return $retval;
            }
            
     });
            
});
     

/*end */
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