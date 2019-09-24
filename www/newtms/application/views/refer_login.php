<?php echo $this->load->view('common/refer_left_wrapper'); ?>
<div class="ref_col ref_col_tax_code">  
    <h2 class="panel_heading_style"><span aria-hidden="true" class="glyphicon glyphicon-user"></span> Enter your credentials to refer a friend</h2>

    <div class="tax_col">
        <h2 class="sub_panel_heading_style sub_panel_heading_ref">Are  you  an  existing  member?
            <button type="submit" class="btn btn-sm btn-info ref_col_botm no1_button" onclick="view_taxcode(0)" style="<?php echo ($this->input->post('yes_no') == 1) ? 'opacity:0.5;' : '' ?>"> <span aria-hidden="true" class="glyphicon glyphicon-remove remove_color"></span> <strong>No</strong></button> 
            <button type="submit" class="btn btn-sm btn-info ref_col_botm yes1_button" onclick="view_taxcode(1)" style="<?php echo ($this->input->post('yes_no') == 0 && !empty($_POST)) ? 'opacity:0.5;' : '' ?>"><span aria-hidden="true" class="glyphicon glyphicon-ok success_green_tick"></span> <strong>Yes</strong></button>
        </h2>
        <?php
        $formopen_atr = 'id="refer_friend" name="refer_friend" onsubmit="return validate_refer_friend();"';
        echo form_open("course/referral_credentials", $formopen_atr);
        echo form_hidden('yes_no', $this->input->post('yes_no'));
        ?>
        
        <div class="tax_code_col" id='tax_code_div' style="<?php echo ($post == 1) ? 'display:block;' : 'display:none'; ?>"><table class="table table-striped">      
                <tbody>                            
                    <tr>
                        <td width="40%" class="td_heading" align="right">Please Enter Your NRIC/FIN No.:</td>
                        <td width="15%">
                            <?php
                                $taxcode_tag = array(
                                'name' => 'taxcode',
                                'id' => 'taxcode',
                                'value' => $this->input->post('taxcode'),
                                'maxlength' => '25',
                                'oninput' => 'return reset_view();',
                                'class' => 'upper_case',
                                'autocomplete' => 'OFF',
                            );
                            echo form_input($taxcode_tag);
                            ?> 
                            <span id="taxcode_err"></span>
                        </td>   
                        <td  width="45%">
                            <button type="button" class="btn btn-sm btn-info" onclick="view_success()"> <strong>Submit</strong></button> 
                        </td>  
                    </tr>
                </tbody>
            </table></div>
        <div class="success_div sub_panel_heading_style sub_panel_heading_ref" style="<?php echo ($this->input->post('yes_no') == 0 && !empty($_POST) && !empty($yes_no_message)) ? '' : 'display:none;'; ?>margin-top: 15px;font-size:12px">
            <span id="msg"><?php echo $yes_no_message; ?></span><br>
            <button type="button" class="btn btn-sm btn-info ref_col_botm no2_button" onclick="color_div(0)" style="<?php echo ($post == 1 && $show_error_form == 0) ? 'opacity:0.5;' : ''; ?>"> <span aria-hidden="true" class="glyphicon glyphicon-remove remove_color"></span> <strong>No</strong></button> 
            <button type="button" class="btn btn-sm btn-info ref_col_botm yes2_button" onclick="color_div(1)" style="<?php echo ($post == 1 && $show_error_form == 1 && !empty($yes_no_message)) ? 'opacity:0.5;' : ''; ?>"><span aria-hidden="true" class="glyphicon glyphicon-ok success_green_tick"></span> <strong>Yes</strong></button>
        </div>
        <div class="capcth_col" id='success_div' style="background-color:#CEFADE; <?php echo ($post == 1 && $show_error_form == 0) ? 'display:block;' : 'display:none'; ?>"> 
            <span aria-hidden="true" class="glyphicon glyphicon-ok-sign success_green"></span>
            <p>Your  credentials  have  been  found  in  our  system. Thank you for being  registered  with  us. 
                Kindly  key in the  text shown  below and click  on submit to  start  registering  your  friends.
                <br/><br>
                <label>
                    <?php echo $captcha; ?>
                </label> 
                <?php
                $captcha_tag = array(
                    'name' => 'captcha',
                    'id' => 'captcha',
                    'maxlength' => '10',
                    'placeholder' => 'Enter text displayed'
                );
                echo form_input($captcha_tag);
                ?>
                <span id="captcha_err">
                    <?php echo form_error('captcha', '<span class="error">', '</span>'); ?>
                </span>
                <button type="submit" class="btn btn-sm btn-info"><strong>Enter</strong></button>
        </div>
        <?php
        echo form_close();
        ?>
        <div id='error_div' style="<?php echo ($show_error_form == 1) ? '' : 'display:none;'; ?>"> 
            <div class="capcth_col">
                <span aria-hidden="true" class="glyphicon glyphicon-remove-circle remove_color success_green"></span>
                <p style='background-color:#FAE7CE;'><span class="extra_info">Your credentials have NOT been found in our system. </span>Kindly provide us some information about yourself in the form given below:</p>
            </div>
            <?php $this->load->view('frnd_layout', array('captcha' => $captcha, 'error_message' => $error_message)); ?>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/validation.js" type="text/javascript"></script>
<script>
                function view_taxcode($val) {
                    if ($val == 1) {
                        $('.no1_button').css({opacity: '0.5'});
                        $('.yes1_button').css({opacity: '1'});
                    } else {
                        $('.yes1_button').css({opacity: '0.5'});
                        $('.no1_button').css({opacity: '1'});
                    }
                    $("input[name='yes_no']").val($val);
                    $('#tax_code_div').show();
                }
                function reset_view() {
                    $('#success_div').hide();
                    $('#error_div').hide();
                    $('.error_div').hide();
                    $('.success_div').hide();
                }
                function view_success() {
                    var taxcode = $('#taxcode').val().trim();
                    $('#taxcode_err').removeClass('error').text('');
                    if (taxcode.length == 0) {
                        $('#taxcode_err').addClass('error').text('[required]');
                        return false;
                    }
                    $.ajax({
                        url: baseurl + "user/check_taxcode_with_data",
                        type: "post",
                        data: {tax_code: taxcode},
                        dataType: 'JSON',
                        success: function(result) {
                            data = result.data;
                            res = result.val;
                            if (res == 1) {
                                if ($('input[name="yes_no"]').val() == 0) {
                                    $('.success_div').show();
                                    $('#success_div').hide();
                                    $('#error_div').hide();
                                    $('.error_div').hide();
                                    $gender = (data.gender == 'MALE') ? 'Mr. ' : 'Ms. ';
                                    $html = 'Your Credentials have been found in system. Are you ' + $gender + data.first_name + ' ' + data.last_name + data.suffix + ' ?';
                                    $('.success_div #msg').html($html);
                                    $('.yes2_button').css({opacity: '1'});
                                    $('.no2_button').css({opacity: '1'});
                                } else {
                                    $('#success_div').show();
                                    $('.success_div').hide();
                                    $('#error_div').hide();
                                    $('.error_div').hide();
                                }
                            } else {
                                if ($('input[name="yes_no"]').val() == 0) {
                                    $('.extra_info').hide();
                                } else {
                                    $('.extra_info').show();
                                }
                                $('.success_div').hide();

                                $('#error_div').show();
                                $('.error_div').show();
                                $('#success_div').hide();
                            }
                        }
                    });
                }
                function validate_refer_friend() {
                    var captcha = $('#captcha').val().trim();
                    $('#captcha_err').removeClass('error').text('');
                    if (captcha.length == 0) {
                        //error
                        $('#captcha_err').addClass('error').text('[required]');
                        return false;
                    }
                }
                function color_div($val) {
                    if ($val == 1) {
                        $('.no2_button').css({opacity: '0.5'});
                        $('.yes2_button').css({opacity: '1'});
                        $('#success_div').show();
                        $('#error_div').hide();
                        $('.error_div').hide();
                    } else {
                        $('.yes2_button').css({opacity: '0.5'});
                        $('.no2_button').css({opacity: '1'});
                        $('.error_div').show();
                        $('.extra_info').hide();
                        $('#error_div').show();
                        $('#success_div').hide();
                    }
                }
</script>




