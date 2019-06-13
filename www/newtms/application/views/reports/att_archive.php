<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common_helper');
$this->load->model('meta_values');
?>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
    <script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/attendance_archive.js"></script>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png">Class Trainee Attendance Archive</h2>
 <span class="error" id="submit_btn"></span>
    <div class="table-responsive">
        <?php
        $attr = 'id="search_form" name="search_form" method="GET" onsubmit="return disable_button()"';
        echo form_open("reports/attendance_archive", $attr);
        ?>
        <table class="table table-striped">

            <tbody>    
                <tr class="class_course_list">
                    <td id="td_heading"> 
                        <strong>Select Course Name</strong>
                    </td>
                    <td colspan="3">
                        <?php  
                        $crs[''] = 'Select';
                        foreach ($course_list as $course):
                            $crs[$course['course_id']] = $course['crse_name'];
                        endforeach;
                        $attr_js = 'id="course_id" ';
                        echo form_dropdown('course_id', $crs, $this->input->get('course_id'), $attr_js);
                        
                        ?>
                    </td>
                </tr>  
                
                 <tr class="course_class_list">
                    <td id="td_heading"> 
                        <strong>Select Class Name</strong>
                    </td>
                     <td >
                        <?php
                        $cls_name[''] = 'Select';
                        foreach ($classes as $k => $v) {
                            $cls_name[$k] = $v;
                        }

                        $js = 'id="cls_id" ';
                        echo form_dropdown('cls_id', $cls_name, $this->input->get('cls_id'), $js);
                        ?>
                    </td>  
                    
                </tr>
                
                       <tr>
                        <td class="td_heading">Subsidy</td>
                        <td <?php echo ($is_report_page)?'':"colspan='2'"; ?>>                        
                            <?php
                            $subsidy_array = array(''=>'All','ws'=>'With Subsidy','wts'=>'Without Subsidy','fr'=>'Foreginer');
                            $attr_js = 'id="subsidy">';
                            echo form_dropdown('subsidy', $subsidy_array, $this->input->get('subsidy'), $attr_js);
                            ?>
                        </td>
                       
                        <td><button type="submit" id="submit_btn" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Submit
                            </button></td>
                    </tr>
                
                


            </tbody>
        </table>

        <?php echo form_close(); ?>
    </div>

    <div class="bs-example">

        <div class="table-responsive">

            <div class="add_button space_style">   <br/>           
                <?php
                $not_array = array("TRAINER", "CRSEMGR");
                if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['TRAINEE']) && (!in_array($this->session->userdata('userDetails')->role_id, $not_array))):
                    ?>
                    <div class="add_button">
                        <a href="<?php echo site_url('/reports/export_archive_page' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                        <a href="<?php echo site_url('/reports/export_archive_pdf' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>
                    </div>
<?php endif; ?>
            </div>    
            <div style="clear:both;"></div>
            <table id="testTable" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th width="15%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >NRIC/FIN No.</a></th>
                        <th width="15%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >Tarinee Name.</a></th>
                        <th width="15%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >Invoice</a></th>
                </thead>
                <tbody>

                    <?php
//                    print_r($tabledata);
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>
                        <tr>
                            <td><?php echo $data['taxcode'];?></td>
                            <td><?php echo $data['first_name'];?></td>
                            <td><?php echo $data['invoice_id'];?></td>
                                
                            
                        </tr>
                        <?php
                        }
                    } else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no trainee available.</label></td></tr>";
                    }
                    ?>        
                </tbody>     
            </table>          
        </div>
        <div style="clear:both;"></div><br>

        <ul class="pagination pagination_style">            
<?php
echo $pagination;
?>
        </ul>

    </div>
</div>
<script>
    $(document).ready(function() {
        var search_check = 0;
        $('#search_form').submit(function() {
            search_check = 1;
            return validate(true);
        });
        $('#search_form input').change(function() {
            if (search_check == 1) {
                return validate(false);
            }
        });
        function validate(retval) {
            var trainee_name_list = $('#trainee_name_list').val().trim();
            var tax_code = $('#tax_code').val().trim();
            var user_id = $('#user_id').val();
            if (trainee_name_list.length > 0 && user_id.length == 0) {
                disp_err('#trainee_name_list', 'Select from auto-help');
                retval = false;
            } else {
                remove_err('#trainee_name_list');
            }
            if (tax_code.length > 0 && user_id.length == 0) {
                disp_err('#tax_code', 'Select from auto-help');
                retval = false;
            } else {
                remove_err('#tax_code');
            }
            return retval;
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
        $('.search').change(function() {
            if ($("#pers_radio").is(":checked")) {
                $('#tax_code').attr('disabled', 'disabled');
                $('#trainee_name_list').removeAttr('disabled');
                $('#user_id').val('');
                $('#tax_code').val('');
                validate(false);
            }
            if ($("#tax_radio").is(":checked")) {
                $('#trainee_name_list').attr('disabled', 'disabled');
                $('#tax_code').removeAttr('disabled');
                $('#user_id').val('');
                $('#trainee_name_list').val('');
                validate(false);
            }
        });
        if ($("#pers_radio").is(":checked")) {
            $('#tax_code').attr('disabled', 'disabled');
            $('#trainee_name_list').removeAttr('disabled');
        }
        if ($("#tax_radio").is(":checked")) {
            $('#trainee_name_list').attr('disabled', 'disabled');
            $('#tax_code').removeAttr('disabled');
        }
    });
</script>