<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    $CI = & get_instance();
    $CI->load->model('course_model');
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Course</h2>
    <div class="table-responsive">  
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("course", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="15%" class="td_heading">
                        <?php
                        $course_name_radio = array(
                            'name' => 'search_radio',
                            'id' => 'course_name_radio',
                            'value' => 'course_name_radio',
                            'checked' => ($this->input->get('search_radio') == 'course_name_radio') ? TRUE : TRUE,
                            'class' => 'search'
                        );
                        ?>
                        <?php echo form_radio($course_name_radio); ?>&nbsp;&nbsp;&nbsp;
                        Search by Course Name:
                    </td>
                    <td width="15%" colspan="2">          
                        <?php
                        $course_options[''] = 'All';
                        foreach ($courses as $course):
                            $course_options[$course->crse_name] = $course->crse_name;
                        endforeach;
                        $attr_js = 'id="course_name"';
                        echo form_dropdown('course_name', $course_options, $this->input->get('course_name'), $attr_js);
                        ?>

                    </td>                    
                </tr>                
                <tr>
                    <td width="15%">
                        <?php
                        $course_code_radio = array(
                            'name' => 'search_radio',
                            'id' => 'course_code_radio',
                            'value' => 'course_code_radio',
                            'checked' => ($this->input->get('search_radio') == 'course_code_radio') ? TRUE : FALSE,
                            'class' => 'search'
                        );
                        ?>
                        <?php echo form_radio($course_code_radio); ?>&nbsp;&nbsp;&nbsp;
                        <span class="td_heading">Course Code:&nbsp;&nbsp;</span>                        
                    </td>
                    <td>
                        <input type="text" value="<?php echo $this->input->get('course_code'); ?>" name="course_code" id="course_code">
                        <input type="hidden" value="<?php echo $this->input->get('course_code'); ?>" name="course_code_id" id="course_code_id">
                        <span id='course_code_err'></span>
                    </td>
                    <td width="13%" align="center">
                        <button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr
                <?php if ($this->data['user']->role_id != 'COMPACT') { ?>
                    <tr>
                        <td class="td_heading">Filter by Status:</td>
                        <td colspan="2">
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
                <?php } ?>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div style="clear:both;"></div><br>
    <div class="bs-example">
        <div class="table-responsive">
            <?php
            $not_array = array("TRAINER", "CRSEMGR");
            if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['CRSE']) && (!in_array($this->session->userdata('userDetails')->role_id, $not_array))) {
                ?>
                <div class="add_button space_style">
                    <a href="<?php echo site_url('/course/export_course_page' . $export_url) ?>"  class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export Page Fields</span></a> &nbsp;&nbsp;
                    <a href="<?php echo site_url('/course/export_course_full' . $export_url) ?>"  class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export All Fields</span></a>                          
                </div>
            <?php } ?>
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>            
                        <th width="6%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=course_id&o=" . $ancher; ?>" >Code</a></th>
                        <th width="12%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crse_name&o=" . $ancher; ?>" >Course Name</a></th>
                        <th width="12%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=tpg_crse&o=" . $ancher; ?>" >TPG</a></th>
                        <th width="13%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crse_manager&o=" . $ancher; ?>" >Course Manager</a></th>
                        <th width="12%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crse_type&o=" . $ancher; ?>" >Course Type</a></th>
                        <th width="13%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=class_type&o=" . $ancher; ?>" >Class Type</a></th>
                        <th width="13%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=certi_level&o=" . $ancher; ?>" >Certification Level</a></th>
                        <th width="12%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=language&o=" . $ancher; ?>" >Language</a></th>
                        <th width="15%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=pre_requisite&o=" . $ancher; ?>" >Pre-Requisite</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            if ($data['crse_status'] == 'INACTIV')
                                echo "<tr class='danger'>";
                            else
                                echo "<tr>";
                            ?>                
                        <td><a href="<?php echo base_url() . $controllerurl . 'view_course/' . $data['course_id']; ?>"><?php echo $data['course_id']; ?></a></td>
                        <td><?php echo $data['crse_name']; ?></td>
                        <td><?php echo isset($data['tpg_crse']) ? "Yes": "No"; ?></td>
                        <td><?php echo rtrim($CI->course_model->get_managers($data['crse_manager']), ', '); ?></td>
                        <td><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($data['crse_type']), ', '); ?></td>
                        <td><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($data['class_type']), ', '); ?></td>
                        <td><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($data['certi_level']), ', '); ?></td>
                        <td><?php echo rtrim($CI->course_model->get_metadata_on_parameter_id($data['language']), ', '); ?></td>
                        <td><?php echo rtrim($CI->course_model->get_pre_requisite($data['pre_requisite']), ', '); ?></td>
                        </tr>
                        <?php
                    }
                }else {
                    echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no course available.</label></td></tr>";
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
<script>
    $('.search').change(function() {
        if ($("#course_name_radio").is(":checked")) {
            $('#course_code').attr('disabled', 'disabled');
            $('#course_name').removeAttr('disabled');
            $('#course_code').val('');
        }
        if ($("#course_code_radio").is(":checked")) {
            $('#course_name').attr('disabled', 'disabled');
            $('#course_code').removeAttr('disabled');
            $("#course_name").val($("#course_name option:first").val());
        }
    });
    ////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45P
    $('#course_code').on("blur", function() {
        $course_code = $('#course_code').val();
            if($course_code ==''){
                $("#course_code_id").val("");
            }  
       });
    function form_validates() {
        $course_code = $('#course_code').val();
        $off_company_name = $('#course_name').find(":selected").text();
        $status = $('#filter_status').find(":selected").text();
     
        var course_code_id = $('#course_code_id').val();
       if ($("#course_code_radio").is(":checked")) {
           
            if(course_code_id !=''){
                remove_err('#$course_code');
                return true;
            }else{
                disp_err('#course_code', '[Select course code from auto-complete]');
                return false;
            }
           
       }
//       if(course_code_id !='' || $off_company_name !='All' || $status !='All'){
//            remove_err('#course_code');
//            return true;
//        }else if($course_code != ''){
//            if(course_code_id !=''){
//                remove_err('#$course_code');
//                return true;
//            }else{
//                disp_err('#course_code', '[Select course code from auto-complete]');
//                return false;
//            }
//            
        else {
//            disp_err('#course_code', '[Select course code from auto-complete]');
            return true;
        }
    }
    $(document).ready(function() {
        if ($("#course_name_radio").is(":checked")) {
            $('#course_code').attr('disabled', 'disabled');
            $('#course_name').removeAttr('disabled');
        }
        if ($("#course_code_radio").is(":checked")) {
            $('#course_name').attr('disabled', 'disabled');
            $('#course_code').removeAttr('disabled');
        }
        
        
        //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#search_form').on('submit',function(){
        var status=form_validates();
        if(status){
        var self = $(this),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').html('Please Wait..');
        return true;
        }else{
            return false;
        }
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////
   
 });
    
    
</script> 