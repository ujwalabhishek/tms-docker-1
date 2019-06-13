<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>    
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/comission.png"> Sales Commission Rate</h2>          
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("course/sales_commission_rate", $atr);
        ?>
        <table class="table table-striped">      
            <tbody>      
                <tr>
                    <td class="td_heading">Search by Course Name:</td>
                    <td>
                        <?php
                        $selected_course = $this->input->get('course_name');
                        $tenant_courses = $this->course->get_course_list_by_tenant($this->session->userdata('userDetails')->tenant_id, 1);
                        $tenant_courses = array('' => 'All') + $tenant_courses;
                        $tenant_courses_attr = 'id="course_name"';
                        echo form_dropdown('course_name', $tenant_courses, $selected_course, $tenant_courses_attr);
                        ?>                
                    </td>
                    <td class="td_heading">Sales Executive Name:</td>
                    <td>
                        <?php
                        $selected_sales_executives = $this->input->get('sales_executives');
                        $sales_executives = $this->course->get_tenant_users_by_role($this->session->userdata('userDetails')->tenant_id, 'SLEXEC');
                        $sales_executives = array('' => 'All') + $sales_executives;
                        $sales_executives_attr = 'id="sales_executives"';
                        echo form_dropdown('sales_executives', $sales_executives, $selected_sales_executives, $sales_executives_attr);
                        ?>
                    </td>          
                    <td align="center">                
                        <button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div><br></div>
    <div class="bs-example" <?php echo $form_style_attr ?> >
        <div class="table-responsive">
            <br>
            <?php if (!empty($table_data)) { ?>
                <div class="add_button space_style">
                    <a href="<?php echo site_url('/course/export_sales_rate_page_filed' . $export_url); ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export Page Fields</span></a> &nbsp;&nbsp;                                              
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
                        <th width="12%" class="th_header">
                            <a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crse.course_id&o=" . $ancher; ?>" >Course Code/ Name</a>
                        </th>
                        <th width="9%" class="th_header">
                            <a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.first_name&o=" . $ancher; ?>" >Sales Executive Name</a>
                        </th>
                        <th width="14%" class="th_header">
                            <a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=sales.commission_rate&o=" . $ancher; ?>" >Commission Rate</a>
                        </th>
                        <th width="7%" class="th_header">Action</th>
                    </tr>
                </thead>              
                <tbody>
                    <?php
                    $i = 0;
                    if (count($table_data) > 0) {
                        foreach ($table_data as $row):
                            ?>
                            <tr id="tbl_row_<?php echo $i; ?>" class="<?php echo ($row->crse_status == 'INACTIV') ? 'danger' : '' ?>" >
                                <td id="tbl_course_<?php echo $i; ?>"><a href="<?php echo site_url() . "course/view_course/" . $row->course_id; ?>">
        <?php echo $row->crse_name . "   (" . $row->course_id . ")"; ?>
                                    </a>
                                </td>
                                <td id="tbl_sales_<?php echo $i; ?>"><?php echo $row->first_name . " " . $row->last_name; ?></td>
                                <td id="tbl_rate_<?php echo $i; ?>"><?php echo number_format($row->commission_rate, 2, '.', ''); ?>%</td>
                                <td id="tbl_action_<?php echo $i; ?>">
                                    <?php if (($row->crse_status != 'INACTIV')) { ?>                                                                    
                                        <a href="#ex9" rel="modal:open" class="small_text1" onclick="update_rate(<?php echo $i; ?>,<?php echo $row->course_id; ?>,<?php echo $row->sales_user_id ?>);" >Update Rate</a>
        <?php } ?>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        endforeach;
                    } else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no sales executives available.</label></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div style="clear:both;"></div>
        <br>
        <ul class="pagination pagination_style">
<?php echo $pagination; ?>
        </ul>
    </div>
</div>

<?php
$form_attributes = array('name' => 'copy_course', 'id' => 'copy_course', "onsubmit" => "return(validate_sales_rate());");
echo form_open("course/update_sales_exec_rate", $form_attributes);
?>
<div class="modal48" id="ex9" style="display:none;">  
    <h5 class="panel_heading_style panel_heading_style1">Update Commission Rate for
        <label id='pop_up_sales_exec_name'></label> for the Course 
        <label id='pop_up_course_name'></label></h5>  
    <table class="table table-striped">      
        <tbody>
            <tr>
                <td>Sales Commission Rate:<span class="required">*</span></td>
                <td>
                    <?php
                    $attr = array(
                        'name' => 'new_sales_commition_rate',
                        'id' => 'new_sales_commition_rate',
                        'class' => 'float_number'
                    );
                    echo form_input($attr);
                    ?>%
                    <input  type="hidden" name='course_id' id='course_id' />
                    <input  type="hidden" name='sales_exec_id' id='sales_exec_id' />
                    <span id="new_sales_commition_rate_err"></span>                    
                </td>
            </tr>        
        </tbody>        
    </table>
    <span class="required required_i">* Required Fields</span>
    <div class="popup_cancel9">
        <div rel="modal:close">
            <button class="btn btn-primary" type="submit">Save</button></a>&nbsp;&nbsp;<a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Cancel</button>
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<script type="text/javascript">
    commission_rate = '';
    function update_rate(id, course_id, sales_user_id) {
        $("#new_sales_commition_rate_err").text("").removeClass('error');
        $("#new_sales_commition_rate").removeClass('error');

        var course_name = "'" + $('#tbl_course_' + id).text() + "'";
        commission_rate = $('#tbl_rate_' + id).text().replace('%', '');
        var sales_exec_name = "'" + $('#tbl_sales_' + id).text() + "'";
        $('#pop_up_sales_exec_name').text('');
        $('#pop_up_sales_exec_name').text(sales_exec_name);
        $('#pop_up_course_name').text('');
        $('#pop_up_course_name').text(course_name);
        $('#new_sales_commition_rate').val(commission_rate);
        $('#course_id').val(course_id);
        $('#sales_exec_id').val(sales_user_id);
    }

    $(".float_number").keydown(function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                        (e.keyCode == 65 && e.ctrlKey === true) ||
                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

    $(document).ready(function() {
        //////////////////////////////////added by shubhranshu to prevent multi click////////////////////////////////////////////////
        $('#search_form').submit(function() {
                var self = $(this),
                button = self.find('input[type="submit"],button');
                button.attr('disabled','disabled').html('Please Wait..');
                return true;
        });///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $siteurl = "<?php echo site_url(); ?>";
        $('#course_name').change(function() {
            ajax_call();
        });
        var course_name = $('#course_name').val();
        if (course_name != '') {
            ajax_call();
        }
    });
    function ajax_call() {
        $class = $('#sales_executives');
        $.ajax({
            type: 'post',
            url: $siteurl + '/course/get_sales_exec_json',
            data: {course_id: $('#course_name').val()},
            dataType: "json",
            beforeSend: function() {
                $class.html('<option value="">All</option>');
            },
            success: function(res) {
                $.each(res, function(i, item) {
                    $class.append('<option value="' + item.key + '">' + item.value + '</option>');
                });
            }
        });
    }
    function validate_sales_rate() {
        var retVal = true;
        var new_sales_commition_rate = $("#new_sales_commition_rate").val();
        if (new_sales_commition_rate == "") {
            $("#new_sales_commition_rate_err").text("[required]").addClass('error');
            $("#new_sales_commition_rate").addClass('error');
            retVal = false;
        } else if (commission_rate == new_sales_commition_rate) {
            $("#new_sales_commition_rate_err").text("[no change]").addClass('error');
            $("#new_sales_commition_rate").addClass('error');
            retVal = false;
        } else {
            $("#new_sales_commition_rate_err").text("").removeClass('error');
            $("#new_sales_commition_rate").removeClass('error');
        }
        if ($('span').hasClass('error')) {
            retVal = false;
        }
        return retVal;
    }
</script>    
