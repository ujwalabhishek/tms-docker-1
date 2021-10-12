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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class-trainee.png"/>Class Report</h2>
    <?php
    $atr = 'id="search_form" name="search_form" method="get"';
    echo form_open("reports/class_report", $atr);
    ?>  
    <div class="table-responsive">
        <h5  class="sub_panel_heading_style"><span class="glyphicon glyphicon-search"></span>Search By</h5>

        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">Course Name:</td>
                    <td colspan="4">
                        <?php
                        $options = array();
                        $options[''] = 'Select';
                       
                        foreach ($courses as $k => $v) {
                            $options[$k] = $v;
                        }                         
                        $js = 'id="course" ';
                        echo form_dropdown('course', $options, $this->input->get('course'), $js);
                        ?>
                        <span class="course_err"></span>
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
                        <span class="class_err"></span>
                    </td>
                    <td>
                    <span class="pull-right">
                            <button type="submit" value="Search" class="srch_btn btn btn-xs btn-primary no-mar" title="Search" /><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </td>
                </tr>               
            </tbody>
        </table>
    </div><br>
    <div class="bs-example">
        <div class="table-responsive">            
            <?php if (count($tabledata) > 0) { ?>                    
                <div class="add_button98 pull-right">
                    <a href="<?php echo base_url(); ?>reports/export_class_report<?php echo $export_url; ?>" class="small_text1" onclick="return exportValidate()">
                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export To XLS</span>
                    </a> &nbsp;&nbsp; 
                    
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
                        <th width="5%" class="">Trainee ID Type</th>
                        <th width="5%" class="">Trainee ID</th>
                        <th width="5%" class="">Trainee Name</th>
                        <th width="5%" class="">Gender</th>
                        <th width="5%" class="">Nationality</th>
                        <th width="8%" class="">Date Of Birth</th>
                        <th width="5%" class="">Race</th>
                        <th width="5%" class="">Trainee Contact No.</th>
                        <th width="5%" class="">Trainee Email</th>
                        <th width="5%" class="">Sponsorship Type</th>
                        <th width="5%" class="">Employer Name</th>
                        <th width="5%" class="">Employer UEN</th>
                        <th width="8%" class="">Registration Date</th>
                        <th width="5%" class="">Employer Contact Name</th>                                             
                        <th width="5%" class="">Employer Contact No.</th>
                        <th width="5%" class="">Employer Contact Email</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php                   
                    if (!empty($tabledata)) 
                    {
                        foreach ($tabledata as $row) 
                        {   
                            ?>
                        <tr>
                            <td><?php echo $row->TraineeIDType;?></td>
                            <td><?php echo $row->TraineeID ;?></td>
                            <td><?php echo $row->TraineeName ;?></td>
                            <td><?php echo $row->Gender;?></td>
                            <td><?php echo $row->CountryOfResidence;?></td>
                            <td><?php echo $row->DateofBirth;?></td>
                            <td><?php echo $row->Race;?></td>                                                      
                            <td><?php echo $row->TraineePhone;?></td>
                            <td><?php echo $row->TraineeEmail ;?></td>
                            <td><?php echo $row->SponsorshipType;?></td>
                            <td><?php echo $row->EmployerName;?></td>
                            <td><?php echo $row->EmployerUEN;?></td>
                            <td><?php echo $row->EmployerRegistrationDate;?></td>
                            <td><?php echo $row->EmployerContactName;?></td>                            
                            <td><?php echo $row->EmployerPhone;?></td>
                            <td><?php echo $row->EmployerContactEmail;?></td>                           
                        </tr>
                       <?php }
                    } 
                    else 
                    {
                        $err_msg=$error_msg;/// added by shubhranshu to remove the classtrainee list on 26/11/2018
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

<script>
      $('#search_form').on('submit',function() {
        form_check = 1;
        //alert("form click");
        var status=form_validate(true);//alert(status);
        if(status){
        var self = $(this),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').html('Please Wait..');
        return true;
       }else{
           return false;
       }
    });  
    
    function form_validate(){
        if($('#course').val()== ''){
            $('.course_err').html('<span style="color:red">[required]</span>');
            $status = false;
        }else{
            $('.course_err').html('');
            $status = true;
        }
        if($('#class').val()== ''){
             $('.class_err').html('<span style="color:red">[required]</span>');
             $status = false;
        }else{
            $('.class_err').html('');
            $status = true;
        }
        
        return $status;
    }
    
    $('#course').change(function() {
        $class = $('#class');
        $.ajax({
            type: 'post',
            url: $baseurl + 'classes/get_course_classes_json',
            data: {course_id: $('#course').val()},
            dataType: "json",
            beforeSend: function() {
                $class.html('<option value="">Select</option>');
            },
            success: function(res) {
                if (res != '') {
                    $class.html('<option value="">All</option>');
                    $class.removeAttr('disabled');
                } else {
                    $class.html('<option value="">Select</option>');
                    $class.attr('disabled', 'disabled');
                }
                $.each(res, function(i, item) {
                    $class.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    });
 </script>
    