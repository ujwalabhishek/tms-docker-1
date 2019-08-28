<?php echo $this->load->view('common/refer_left_wrapper'); ?>
<div class="ref_col ref_col_tax_code">  
    <h2 class="panel_heading_style">
        <span aria-hidden="true" class="glyphicon glyphicon-user"></span> 
        Class Enrollment
    </h2>
    <div class="tax_col">
        <?php
        if ($this->session->flashdata('error')) {
            echo '<div style="color:red;font-weight: bold;padding:10px;background:#f2dede;">' . $this->session->flashdata('error') . '</div>';
        }
        if (!empty($success)) {
            echo '<div style="color:green;font-weight: bold;background-color: rgb(206, 250, 222); padding: 10px;">
                                    ' . $success . '
                            </div>';
        }
        ?><br/>
        <h4 class="sub_panel_heading_style sub_panel_heading_ref">Please Enroll Through Your NRIC No.
        </h4>
        <?php
        $formopen_atr = 'id="refer_friend" name="refer_friend" onsubmit="return validate_refer_friend();"';
        echo form_open($_SERVER['SELF'], $formopen_atr);
        echo form_hidden('yes_no', $this->input->post('yes_no'));
      
        ?>
        <div class="tax_code_col" id='tax_code_div'>
           
            <table class="table table-striped" style="width:100%">      
                <tbody>                            
                    <tr>
                        <td width="30%" class="td_heading">Please Enter Your NRIC/FIN No.: </td>
                        <td width="70%" colspan="2">
                            <?php                            
                            $taxcode_tag = array(
                                'name' => 'taxcode',
                                'id' => 'taxcode',
                                'value' => $this->session->userdata('prev_tax_code'),
                                'maxlength' => '25',
                                'class' => 'upper_case',
                                'autocomplete' => 'OFF',
                                'style' => 'width:200px;'
                            );
                            echo form_input($taxcode_tag);
    
                            $this->session->unset_userdata('prev_tax_code')
                            ?> 
                            <span id="taxcode_err"></span>
                        </td>  
                    </tr>
                    <tr>
                        <td> <?php echo $captcha; ?></td>
                        <td><?php
                            $captcha_tag = array(
                                'name' => 'captcha',
                                'id' => 'captcha',
                                'maxlength' => '10',
                                'style' => 'margin:10px 0px;',
                                'placeholder' => 'Enter text displayed'
                            );
                            echo form_input($captcha_tag);
                            ?> <span id="captcha_err">
                            <?php echo form_error('captcha', '<span class="error">', '</span>'); ?>
                            </span>
                        </td>
                        <td><span class="pull-right" style="float: left;padding: 10px 0px;"><button type="submit" class="btn btn-sm btn-info" name="submit" value="taxcode_verification"><strong>Submit</strong></button> </span></td>
                    </tr> 
                </tbody>
            </table>
        </div>     
        <?php
        echo form_close();
        ?>
    </div>
</div>
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
    function validate_refer_friend() {
        var taxcode = $('#taxcode').val().trim();
        $('#taxcode_err').removeClass('error').text('');
        if (taxcode.length == 0) {
            $('#taxcode_err').addClass('error').text('[required]');
            return false;
        }
        var captcha = $('#captcha').val().trim();
        $('#captcha_err').removeClass('error').text('');
        if (captcha.length == 0) {
            $('#captcha_err').addClass('error').text('[required]');
            return false;
        }
    }

</script>




