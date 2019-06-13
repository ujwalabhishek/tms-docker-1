<style>
    .form_content{display: none;}


.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 16px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 13px;
  width: 13px;
  left: 4px;
  bottom: 2px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}


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
    $(document).ready(function(){
        $("#privilage").click(function(){
            $status = '';
            if($('#privilage').prop("checked") == true){  
               $status = '1';
            }else{
               $status = '0';
            }
            $.ajax({
                    url: 'manage_block_nric/update_privilage',
                    data: {'status': $status},
                    type: 'post',
                    dataType: 'json',
                    success: function(res) {
                        
                }
            });
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/course.png"> Manage NRIC Blocked List
        <span style="float: right;cursor: pointer;" id="add_new"><span class="glyphicon glyphicon-plus glyphicon1"></span> <span><?php echo $add_text; ?></span></span>
    </h2>
    <div style='font-weight:bold;'>Restriction NRIC Popup (ON/OFF):
        <label class="switch">
            <input id="privilage" type="checkbox" <?php if($privilage_for_all == 1){echo "checked";} ?>>
            <span class="slider round"></span>
      </label>
    </div>
	<div style='font-size:10px;color: #4d9be0;'>On = Display pop-up for Admin, Sales Executive, Course Manager and Trainer.</div>
	<div style='font-size:10px;color: #4d9be0;'>Off = Display pop-up only to Admin. </div>
    <div class="table-responsive" id="add_new_div" style="display:none;">
        <br/>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/other_details.png"> <?php echo $add_text; ?></h2>
        <?php
        $atr = 'id="add_form" name="add_form" method="POST"';
        echo form_open($controllerurl.'add_blocked_nric', $atr);
        echo form_hidden('cat', $this->input->get('cat'));
        echo form_hidden('sub_cat', $this->input->get('sub_cat'));
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%" class="td_heading">Enter NRIC No:<span class="required">*</span></td> 
                    <td>
                        <?php
                        $data = array(
                            'id' => 'cat_name',
                            'name' => 'nric_name',
                            'maxlength' => '250',
                            'style' => 'width:400px;'
                        );
                        echo form_input($data);
                        ?>
                        <span id="cat_name_err"></span>
                    </td>
                </tr>
                
                
         
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
    <div class="bs-example" style="">
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/company-detail.png"> Blocked NRIC List</h2>
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <tr>            
                        <th width="30%" class="th_header">ID</th>
                        <th width="50%" class="th_header">NRIC</th>
                        <th width="20%" class="th_header">Actions</th>
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
                                <span class="def_content"><?php echo $row['nric']; ?></span>
                                <span class="form_content"><input type="text" name="change_nric" value="<?php echo $row['nric']; ?>" style="width: 400px;"/></span>
                            </td>
                            <td align="center">
                                <a href="javascript:;" class="def_content edit_form_open">Edit</a>
                                <span class="form_content">
                                    <input type="hidden" name="nric_id" value="<?php echo $row['id']; ?>"/>
                                    <input type="hidden" name="nric" value="<?php echo $row['nric']; ?>"/> 
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </span>
                            </td>
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