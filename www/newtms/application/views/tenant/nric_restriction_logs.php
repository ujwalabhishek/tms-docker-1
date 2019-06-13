<style>
    .form_content{display: none;}




</style>
<?php
$cat = $this->input->get('cat');
$sub_cat = $this->input->get('sub_cat');
$add_text = 'Add NRIC To Blocked List';
$cat_options = array();
$cat_options[''] = 'Select';
foreach ($categories as $item):
    if (!empty($cat) && $item->child_category_id == $cat):
        $add_text = 'Add ' . $item->category_name;
    endif;
    $cat_options[$item->child_category_id] = $item->category_name;
endforeach;
if (!empty($sub_cat)) {
    $subcat_options = array();
    $subcat_options[''] = 'Select';
    foreach ($subcategories as $item):
        if ($item->child_category_id == $sub_cat) {
            $add_text = 'Add ' . $item->category_name;
        }
        if (!empty($item->child_category_id)) {
            $subcat_options[$item->child_category_id] = $item->category_name;
        }
    endforeach;
}
?>
<script>
    var cur_url = '<?php echo current_url() ?>';
    var cat = '<?php echo $cat; ?>';
    var sub_cat = '<?php echo $sub_cat; ?>';
    var add_text = '<?php echo $add_text; ?>';
    $(document).ready(function() {
        var tenant_name = $('#tenant_name');
        var tenant_id = $('#tenant_id');
        $("#tenant_name").autocomplete({
            source: function(request, response) {
                tenant_name.val(tenant_name.val().toUpperCase())
                tenant_id.val('');
                $.ajax({
                    url: baseurl + "manage_subsidy/get_all_tenant",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var id = ui.item.key;
                tenant_id.val(id);
            },
            minLength: 0
        });
        $('#search_form').submit(function() {
            var tenant_name_err = $('#tenant_name_err');
            if (tenant_id.val().length == 0 && tenant_name.val().trim().length != 0) {
                var message = 'Please select from auto-help.';
                tenant_name_err.addClass('error').html(message);
                return false;
            } else {
                tenant_name_err.removeClass('error').html('');
            }
        });                   
    });  
</script>
<div class="col-md-10" style="min-height: 460px;">
    <?php
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/course.png"> Blocked NRIC Logs
        <!--<span style="float: right;cursor: pointer;" id="add_new"><span class="glyphicon glyphicon-plus glyphicon1"></span> <span><?php echo $add_text; ?></span></span>-->
    </h2>
    
    <div class="table-responsive" id="div_list_search">
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("manage_block_nric/fetch_nric_restriction_log", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%" class="td_heading">Search by Tenant Name:</td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'tenant_name',
                            'name' => 'tenant_name',
                            'value' => $this->input->get('tenant_name'),
                            'style' => 'width:400px;'
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'tenant_id',
                            'name' => 'tenant_id',
                            'value' => $this->input->get('tenant_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        echo '<span id="tenant_name_err"></span>';
                        ?>
                    </td>
                    <td width="10%" align="center"><button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>
                            Search
                        </button></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div> 
    <div style="clear:both;"></div><br>
    <div class="bs-example" style="">
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/company-detail.png"> Blocked NRIC Logs</h2>
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <tr>            
                        <th width="10%" class="th_header">SL NO</th>
                        <th width="10%" class="th_header">TENANT ID</th>
			<th width="10%" class="th_header">TRAINEE NRIC NO</th>
			<th width="20%" class="th_header">TRAINEE FULL NAME</th>
                        <th width="10%" class="th_header">ENROLLED BY ID</th>
                        <th width="10%" class="th_header">ENROLLED BY ROLE ID</th>
                        <th width="10%" class="th_header">ENROLLED BY USER NAME</th>
                        <th width="10%" class="th_header">OPERATION</th>
                        <!--<th width="20%" class="th_header">LAST NAME</th>-->
                        
                        <th width="20%" class="th_header">DATE & TIME</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($tabledata as $k=> $row):
                        ?>
                      <tr id="parent<?php echo $k; ?>">
                        <form class="update_form" data-key="<?php echo $k; ?>" id="update_form<?php echo $k; ?>" action="<?php echo base_url() . $controllerurl . 'update_blocked_nric' ?>"  method="POST">

                            <td>
                                <span class=""><?php echo $row['id'];?></span>
                            
                            </td>
                            <td>
                                <span class=""><?php echo $row['tenant_id']; ?></span>
                                <!--<span class="form_content"><input type="text" name="change_nric" value="<?php echo $row['nric']; ?>" style="width: 400px;"/></span>-->
                            </td>
                            <td>
                                <span class=""><?php echo $row['nric_taxcode'];?></span>
                            
                            </td>
				<td>
                                <span class=""><?php echo $row['first_name'].' '.$row['last_name'];?></span>
                            
                            </td>
                            <td>
                                <span class=""><?php echo $row['enrolled_by_user_id'];?></span>
                            
                            </td>
                            <td>
                                <span class=""><?php echo $row['role_id'];?></span>
                            
                            </td>
                            <td>
                                <span class=""><?php echo $row['enrolled_by_user_name'];?></span>
                            
                            </td>
                            
<!--                            <td>
                                <span class=""><?php echo $row['last_name'];?></span>
                            
                            </td>-->
                            <td>
                                <span class=""><?php echo $row['operation'];?></span>
                            
                            </td>

                            <td>
                                <span class=""><?php echo $row['trigger_datetime'];?></span>
                            
                            </td>
<!--                            <td align="center">
                                <a href="javascript:;" class="def_content edit_form_open">Edit</a>
                                <span class="form_content">
                                    <input type="hidden" name="nric_id" value="<?php //echo $row['id']; ?>"/>
                                    <input type="hidden" name="nric" value="<?php //echo $row['nric']; ?>"/> 
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </span>
                            </td>-->
                        </form> 
                    </tr>
                <?php endforeach; ?>
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
    $(document).ready(function() {
        var check = 0;
        $('#search_cat').change(function() {
            var cat = $(this).val();
            window.location.href = cur_url + '?cat=' + cat;
        });
        $('#search_sub_cat').change(function() {
            var sub_cat = $(this).val();
            window.location.href = cur_url + '?cat=' + cat + '&sub_cat=' + sub_cat;
        });
        $('.sub_cat').click(function() {
            var sub_cat = $(this).data('id');
            window.location.href = cur_url + '?cat=' + cat + '&sub_cat=' + sub_cat;
        });
        $('#add_new').click(function() {
            var text_span = $(this).children('span:first');
            var text_div = text_span.next();
            if (text_div.text() == add_text) {
                text_span.css({
                    display: 'none'
                });
                text_div.text('Close');
            } else {
                text_span.css({
                    display: ''
                });
                text_div.text(add_text);
            }
            $('#add_new_div').slideToggle();
            remove_all();
            $('#add_form')[0].reset();
            $('.child_cat_div').css('display', 'none');
        });
        $('#add_form').submit(function() {
            check = 1;
            return validate(true);
        });
        $('#add_form input').change(function() {
            if (check == 1) {
                return validate(false);
            }
        });
        $('.child_cat').change(function() {
            var child_cat = $(this).val();
            if (child_cat == 1) {
                $('.child_cat_div').show();
            } else {
                $('.child_cat_div').hide();
                $('.child_cat_div input').val('');
            }
        });
        $('.edit_form_open').click(function() {
            var id = $(this).parents('tr').attr('id');
            $('#' + id + ' td .def_content').hide();
            $('#' + id + ' td .form_content').show();
        });
        $('.update_form').submit(function() {
            var retval = true;
//            var form_id = $(this).attr('id');
//            var key = $(this).data('key');
//            var cat_name = $('#cat_name' + key).val().trim();
//            if (cat_name.length == 0) {
//                disp_err('#cat_name' + key);
//                retval = false;
//            } else {
//                remove_err('#cat_name' + key);
//            }
            return retval;
        });
        function validate(retval) {
            var cat_name = $('#cat_name').val().trim();
            if (cat_name.length == 0) {
                disp_err('#cat_name');
                retval = false;
            } else {
                remove_err('#cat_name');
            }
            if (cat.length > 0) {
                var param = $('#param').val().trim();
                if (param.length == 0) {
                    disp_err('#param');
                    retval = false;
                } else if (unique_param_id(param) == false) {
                    disp_err('#param', '[Param Id is already exists]');
                    retval = false;
                } else {
                    remove_err('#param');
                }
            }
            if (sub_cat.length == 0) {
                var child_cat = $('.child_cat:checked').val();
                if (child_cat == 1) {
                    var child_cat_name = $('#child_cat_name').val().trim();
                    if (child_cat_name.length == 0) {
                        disp_err('#child_cat_name');
                        retval = false;
                    } else {
                        remove_err('#child_cat_name');
                    }
                    var child_param = $('#child_param').val().trim();
                    if (child_param.length == 0) {
                        disp_err('#child_param');
                        retval = false;
                    } else {
                        remove_err('#child_param');
                    }
                }
            }
            return retval;
        }
        function unique_param_id(param) {
            var output;
            $.ajax({
                url: baseurl + 'metadata/unique_param_check',
                type: 'post',
                data: {
                    cat: cat, sub_cat: sub_cat, param: param
                },
                async: false,
                success: function(res) {
                    output =  false;
                    if (res == 0) {
                         output = true;
                    }
                }
            });
            return output;
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
        function remove_all() {
            check = 0;
            $('.error3').text('').removeClass('error3');
            $('#ex2 .error, #ex3 .error').removeClass('error');
        }
    });
</script>