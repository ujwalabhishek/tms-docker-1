<style>
    .form_content{display: none;}
</style>
<?php
$cat = $this->input->get('cat');
$sub_cat = $this->input->get('sub_cat');
$add_text = 'Add Metadata';
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/course.png"> Manage Metadata
        <span style="float: right;cursor: pointer;" id="add_new"><span class="glyphicon glyphicon-plus glyphicon1"></span> <span><?php echo $add_text; ?></span></span>
    </h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open($controllerurl, $atr);
        ?>  
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%" class="td_heading">Select Metadata Category:</td>
                    <td>
                        <?php
                        $extra = ' id="search_cat"';
                        echo form_dropdown('cat', $cat_options, $this->input->get('cat'), $extra);
                        ?>
                        <?php if (empty($sub_cat) && !empty($cat)) { ?>
                            <span class="more">
                                <a href="<?php echo current_url(); ?>" title="Cancel Metadata Category"><span class="pull-right remove_img remove2"></span></a>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
                <?php if (!empty($sub_cat)) { ?>
                    <tr>
                        <td width = "24%" class = "td_heading">Select Metadata Subcategory:</td>
                        <td>
                            <?php
                            $extra = ' id="search_sub_cat"';
                            echo form_dropdown('cat', $subcat_options, $this->input->get('sub_cat'), $extra);
                            ?>
                            <span class="more">
                                <a href="<?php echo current_url() . '?cat=' . $cat; ?>" title="Cancel Metadata Subcategory"><span class="pull-right remove_img remove2"></span></a>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div class="table-responsive" id="add_new_div" style="display:none;">
        <br/>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/other_details.png"> <?php echo $add_text; ?></h2>
        <?php
        $atr = 'id="add_form" name="add_form" method="POST"';
        echo form_open($controllerurl, $atr);
        echo form_hidden('cat', $this->input->get('cat'));
        echo form_hidden('sub_cat', $this->input->get('sub_cat'));
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%" class="td_heading">Category Name:<span class="required">*</span></td> 
                    <td>
                        <?php
                        $data = array(
                            'id' => 'cat_name',
                            'name' => 'cat_name',
                            'maxlength' => '250',
                            'style' => 'width:400px;'
                        );
                        echo form_input($data);
                        ?>
                        <span id="cat_name_err"></span>
                    </td>
                </tr>
                <tr>
                    <td width="24%" class="td_heading">Description:</td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'desc',
                            'name' => 'desc',
                            'maxlength' => '250',
                            'style' => 'width:400px;'
                        );
                        echo form_input($data);
                        ?>
                        <span id="desc_err"></span>
                    </td>
                </tr>
                <?php if (!empty($cat)) { ?>
                    <tr>
                        <td width="24%" class="td_heading">Param Id:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'id' => 'param',
                                'name' => 'param',
                                'maxlength' => '250',
                                'style' => 'width:400px;',
                                'class' => 'upper_case'
                            );
                            echo form_input($data);
                            ?>
                            <span id="param_err"></span>
                        </td>
                    </tr>
                    <?php
                }
                if (empty($sub_cat)) {
                    ?>
                    <tr>
                        <td width="24%" class="td_heading">Do you want to add child category:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'class' => 'child_cat',
                                'name' => 'child_cat',
                                'value' => 1
                            );
                            echo form_radio($data);
                            ?> Yes 
                            <?php
                            $data = array(
                                'class' => 'child_cat',
                                'name' => 'child_cat',
                                'value' => 0,
                                'checked' => TRUE
                            );
                            echo form_radio($data);
                            ?> No 
                            <span id="desc_err"></span>
                        </td>
                    </tr>
                    <tr class="child_cat_div" style="display:none;">
                        <td width="24%" class="td_heading">Child Category Name:<span class="required">*</span></td> 
                        <td>
                            <?php
                            $data = array(
                                'id' => 'child_cat_name',
                                'name' => 'child_cat_name',
                                'maxlength' => '250',
                                'style' => 'width:400px;'
                            );
                            echo form_input($data);
                            ?>
                            <span id="child_cat_name_err"></span>
                        </td>
                    </tr>
                    <tr class="child_cat_div" style="display:none;">
                        <td width="24%" class="td_heading">Child Category Description:</td>
                        <td>
                            <?php
                            $data = array(
                                'id' => 'child_desc',
                                'name' => 'child_desc',
                                'maxlength' => '250',
                                'style' => 'width:400px;'
                            );
                            echo form_input($data);
                            ?>
                            <span id="child_desc_err"></span>
                        </td>
                    </tr>
                    <tr class="child_cat_div" style="display:none;">
                        <td width="24%" class="td_heading">Child Category Param Id:<span class="required">*</span></td>
                        <td>
                            <?php
                            $data = array(
                                'id' => 'child_param',
                                'name' => 'child_param',
                                'maxlength' => '250',
                                'style' => 'width:400px;',
                                'class' => 'upper_case'
                            );
                            echo form_input($data);
                            ?>
                            <span id="child_param_err"></span>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" class="no-bg">
                        <div class="button_class">
                            <div class="button_class99">
                                <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;Save</button> &nbsp; &nbsp;
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div style="clear:both;"></div><br>
    <div class="bs-example" style="<?php echo ($cat) ? '' : 'display:none;'; ?>">
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/company-detail.png"> Metadata List</h2>
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <tr>            
                        <th width="40%" class="th_header">Category Name</th>
                        <th width="50%" class="th_header">Description</th>
                        <th width="10%" class="th_header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($tabledata as $k => $row):
                        $prefix = '';
                        $suffix = '';
                        if ($row->child_category_id) {
                            $prefix = '<a href="javascript:;" class="sub_cat" data-id="' . $row->child_category_id . '">';
                            $suffix = '</a>';
                        }
                        ?>
                        <tr id="parent<?php echo $k; ?>">
                    <form class="update_form" data-key="<?php echo $k; ?>" id="update_form<?php echo $k; ?>" action="<?php echo base_url() . $controllerurl . 'update_meta' ?>"  method="POST">
                        <td>
                            <span class="def_content"><?php echo $prefix . $row->category_name . $suffix; ?></span>
                            <span class="form_content">
                                <input type="text" name="category_name" id="cat_name<?php echo $k; ?>" value="<?php echo $row->category_name; ?>" style="width: 400px;"/>
                                <span id="cat_name<?php echo $k; ?>_err"></span>
                            </span>
                        </td>
                        <td>
                            <span class="def_content"><?php echo $row->description; ?></span>
                            <span class="form_content"><input type="text" name="description" value="<?php echo $row->description; ?>" style="width: 400px;"/></span>
                        </td>
                        <td align="center">
                            <a href="javascript:;" class="def_content edit_form_open">Edit</a>
                            <span class="form_content">
                                <input type="hidden" name="parameter_id" value="<?php echo $row->parameter_id; ?>"/>
                                <input type="hidden" name="category_id" value="<?php echo $row->category_id; ?>"/>
                                <input type="hidden" name="cat_id" value="<?php echo $cat; ?>"/>
                                <input type="hidden" name="subcat_id" value="<?php echo $sub_cat; ?>"/>
                                <button class="btn btn-primary" type="submit">Update</button>
                            </span>
                        </td>
                    </form>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
            var form_id = $(this).attr('id');
            var key = $(this).data('key');
            var cat_name = $('#cat_name' + key).val().trim();
            if (cat_name.length == 0) {
                disp_err('#cat_name' + key);
                retval = false;
            } else {
                remove_err('#cat_name' + key);
            }
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