<div class="col-md-10" style="min-height: 400px;">
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"><?php echo $page_title; ?></h2>
    <div class="table-responsive" id="add_new_div">
        <br/>
        <h2 class="sub_panel_heading_style"><img src="<?php echo base_url(); ?>assets/images/other_details.png"><?php echo $page_title; ?></h2>
        <?php
        $atr = 'id="add_form" name="add_form" method="POST" onsubmit="return(validate());"';
        echo form_open($controllerurl, $atr);
        ?> 
        <table class="table table-striped">
            <tbody
            <?php
            $tenant_options[''] = 'Select';
            foreach ($tenant_details as $item):
                $tenant_options[$item['tenant_id']] = $item['tenant_name'];
            endforeach;
            ?>
                <tr>
                    <td width="24%" class="td_heading">Tenant Name:<span class="required">*</span></td>
                    <td colspan="3">
                        <?php echo form_dropdown('tenant_id', $tenant_options, $tenant_subsidy->tenant_id, 'id="tenant_id" style="width:450px"'); ?>
                        <span id="tenant_id_err"></span>
                    </td>
                </tr>
                <tr>
                    <td width="24%" class="td_heading">Subsidy Type:<span class="required">*</span></td> 
                    <td>
                        <?php
                        $data = array(
                            'id' => 'subsidy_type',
                            'name' => 'subsidy_type',
                            'maxlength' => '100',
                            'style' => 'width:250px;',
                            'value' => $tenant_subsidy->subsidy_type,
                        );
                        echo form_input($data);
                        ?>
                        <span id="subsidy_type_err"></span>
                    </td>
                    <td width="24%" class="td_heading">Subsidy Amount:<span class="required">*</span></td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'subsidy_amount',
                            'name' => 'subsidy_amount',
                            'style' => 'width:250px;',
                            'class' => 'float_number',
                            'value' => $tenant_subsidy->subsidy_amount,
                            'onkeyup' => "checkDec(this);"
                        );
                        echo form_input($data);
                        echo form_hidden('subsidy_id', $subsidy_id);
                        ?>
                        <span id="subsidy_amount_err"></span>
                    </td>
                </tr>                              
                <tr>
                    <td colspan="4" class="no-bg">
                        <div class="button_class">
                            <div class="button_class99">
                                <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-saved"></span>&nbsp;<?php echo $action; ?></button> &nbsp; &nbsp;
                                 <a href="<?php echo site_url(); ?>manage_subsidy/" class="btn btn-primary">
                                         <span class="glyphicon glyphicon-step-backward"></span>&nbsp;Back
                                
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    $(document).ready(function() {
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
    });
    $(document).ready(function() {
        var check = 0;
        $('#add_form').submit(function() {
            check = 1;
            return validate();
        });
        $('#add_form select,#add_form input').change(function() {
            if (check == 1) {
                return validate();
            }
        });
    });
    function validate() {
        var retVal = true;
        var $tenant_id = $('#tenant_id').val();
        if ($tenant_id == '') {
            $("#tenant_id_err").text("[required]").addClass('error');
            $("#tenant_id").addClass('error');
            retVal = false;
        } else {
            $("#tenant_id_err").text("").removeClass('error');
            $("#tenant_id").removeClass('error');
        }
        var $subsidy_type = $('#subsidy_type').val();
        if ($subsidy_type.length == 0) {
            $("#subsidy_type_err").text("[required]").addClass('error');
            $("#subsidy_type").addClass('error');
            retVal = false;
        } else {
            $("#subsidy_type_err").text("").removeClass('error');
            $("#subsidy_type").removeClass('error');
        }
        var $subsidy_amount = $('#subsidy_amount').val();
        if ($subsidy_amount.length == 0) {
            $("#subsidy_amount_err").text("[required]").addClass('error');
            $("#subsidy_amount").addClass('error');
            retVal = false;
            
        } else if(parseFloat($subsidy_amount)> 999999){
            $("#subsidy_amount_err").text("[Invalid]").addClass('error');
            $("#subsidy_amount").addClass('error');
            retVal = false;
        } else {
            $("#subsidy_amount_err").text("").removeClass('error');
            $("#subsidy_amount").removeClass('error');
        }
        return retVal;
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
    $(function() {
        $('#subsidy_amount').keyup(function() {
            if ($(this).val().indexOf('.') != -1) {
                if ($(this).val().split(".")[1].length > 2) {
                    if (isNaN(parseFloat(this.value)))
                        return;
                    this.value = parseFloat(this.value).toFixed(2);
                }
            }
            return this;
        });
    });
</script>