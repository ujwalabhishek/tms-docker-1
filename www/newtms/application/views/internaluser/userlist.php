<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  Internal Staff</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("internal_user", $atr);
        ?>                       
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                
                        <?php
                        $user_role_radio = array(
                            'name' => 'search_radio',
                            'id' => 'user_role_radio',
                            'value' => 'user_role_radio',
                            'checked' => ($this->input->get('search_radio') == 'user_role_radio') ? TRUE : TRUE,
                            'class' => 'search'
                        );
                        ?>
                        <?php echo form_radio($user_role_radio); ?>&nbsp;&nbsp;&nbsp;              
                        Search by Staff Role:
                    </td>
                    <td>
                        <?php
                        $role_options[''] = 'All';
                        foreach ($roles as $role):
                            $role_options[$role->role_id] = $role->role_name;
                        endforeach;
                        unset($role_options['COMPACT']);
                        $disable = ($this->input->get('search_radio') == 'first_last_name_radio') ? 'disabled="disabled"' : '';
                        $attr_js = 'id="user_role" ' . $disable;
                        echo form_dropdown('user_role', $role_options, $this->input->get('user_role'), $attr_js);
                        ?>
                        <div id="user_role_err"></div>
                    </td>
                    <td class="td_heading">
                        <?php
                        $first_last_name_radio = array(
                            'name' => 'search_radio',
                            'id' => 'first_last_name_radio',
                            'value' => 'first_last_name_radio',
                            'checked' => ($this->input->get('search_radio') == 'first_last_name_radio') ? TRUE : FALSE,
                            'class' => 'search'
                        );
                        ?>
                        <?php echo form_radio($first_last_name_radio); ?>&nbsp;&nbsp;&nbsp;
                        By Staff Name:
                    </td>
                    <td>               
                        <?php
                        echo form_hidden('user_id', $this->input->get('user_id'), 'user_id');
                        ?>
                        <input  id="first_last_name" name="first_last_name" type="text" value="<?php echo $this->input->get('first_last_name'); ?>">
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <div id="first_last_name_err"></div>
                    </td>
                    <td align="center">
                        <button type="submit" id='srch_btn' value="Search" title="Search" class="btn btn-xs btn-primary no-mar"><span class="glyphicon glyphicon-search"></span> Search</button>
                    </td>
                </tr>
                <tr>
                    <td class="td_heading">Filter by Status:</td>
                    <td colspan="4">
                        <?php
                        $status_options[''] = 'All';
                        foreach ($filter_status as $status):
                            $status_options[$status['parameter_id']] = $status['category_name'];
                        endforeach;
                        $attr_js = 'id="filter_status"';
                        echo form_dropdown('filter_status', $status_options, $this->input->get('filter_status'), $attr_js);
                        ?> 
                        <div id="filter_status_err"></div>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <div class="bs-example">
        <div class="table-responsive">
            <div class="add_button space_style">
                <?php if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['INTUSR'])) { ?>
                <a href="<?php echo site_url('/internal_user/export_users_page' . $export_url) ?>"  class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export Page Fields</span></a> &nbsp;&nbsp;
                    <a href="<?php echo site_url('/internal_user/export_users_full' . $export_url) ?>"  class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export All Fields</span></a>
                <?php } ?>
            </div>
            <div style="clear:both;"></div>
            <table id="listview" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >NRIC/FIN No.</a></th> 
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=pers.first_name&o=" . $ancher; ?>" >Staff Name</a></th> 
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=role.role_name&o=" . $ancher; ?>" >Role</a></th>
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Current Status</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>
                            <tr <?php if ($data['account_status'] == 'INACTIV') echo 'class="danger"'; ?>>
                                <td><a href="<?php echo base_url() . $controllerurl . 'view_user/' . $data['user_id']; ?>">
                                        <?php
                                        if ($data['tax_code_type'] && $data['tax_code']) {
                                            if ($data['tax_code_type'] != 'SNG_3') {
                                                $type = get_param_value($data['tax_code_type']);
                                                echo $type->category_name . ' - ' . $data['tax_code'];
                                            }
                                        }
                                        if ($data['other_identi_type'] && $data['other_identi_code']) {
                                            $tax_code_type = get_param_value($data['tax_code_type']);
                                            $type = get_param_value($data['other_identi_type']);
                                            echo $tax_code_type->category_name . ' - ' . $type->category_name . ' - ' . $data['other_identi_code'];
                                        }
                                        ?>
                                    </a></td>
                                <td><?php echo $data['first_name'] . ' ' . $data['last_name']; ?></td>
                                <td><?php echo $data['role_name']; ?></td>
                                <td>
                                    <?php
                                    if ($status_lookup[$data['account_status']] == 'Active')
                                        echo '<font color="green">' . $status_lookup[$data['account_status']] . '</font>';
                                    if ($status_lookup[$data['account_status']] == 'Inactive')
                                        echo '<font color="red">' . $status_lookup[$data['account_status']] . '</font>';
                                    if ($status_lookup[$data['account_status']] == 'Pending Activation')
                                        echo '<font color="blue">' . $status_lookup[$data['account_status']] . '</font>';
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no users available.</label></td></tr>";
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
    $(document).ready(function() {
        var search_check = 0;
        //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
        $('#search_form').on('submit',function() {
            search_check = 1;
            //alert("form click");
            var status=form_validates();
            if(status){
            var self = $(this),
            button = self.find('input[type="submit"],button'),
            submitValue = button.data('submit-value');
            button.attr('disabled','disabled').html('Please Wait..');
            
            //$("#srch_btn").val('Please wait..');
            return true;
           }else{
               return false;
           }
        }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
//        $('#search_form').submit(function() {
//            search_check = 1;
//            return validate(true);
//        });
        $('#search_form input').change(function() {
            if (search_check == 1) {
                return validate(false);
            }
        });
        function validate(retval) {
            var first_last_name = $('#first_last_name').val().trim();
            var user_id = $('#user_id').val();
            if (first_last_name.length > 0 && user_id.length == 0) {
                disp_err('#first_last_name', 'Select from auto-help');
                retval = false;
            } else {
                remove_err('#first_last_name');
            }
            return retval;
        }
        // added by shubhranshu for dynamic prevention of search form
    $('#first_last_name').on("blur", function() {
        $first_last_name = $('#first_last_name').val();
            if($first_last_name ==''){
                $("#user_id").val("");
            }  
       });
    function form_validates() {
        $user_role = $('#user_role').find(":selected").text();
        $filter_status = $('#filter_status').find(":selected").text();
        $first_last_name = $('#first_last_name').val();
        var user_id = $('#user_id').val();
       if(user_id !='' || $filter_status !='All' || $user_role !='All'){
            remove_err('#user_role');
            remove_err('#filter_status');
            remove_err('#first_last_name');
            return true;
        }else if($first_last_name != ''){
            if(user_id !=''){
                remove_err('#user_role');
                remove_err('#filter_status');
                remove_err('#first_last_name');
                return true;
            }else{
                disp_err('#first_last_name', '[Select Trainee from auto-complete]');
                remove_err('#user_role');
                remove_err('#filter_status');
                return false;
            }
            
        }else{
            disp_err('#first_last_name', '[Select Trainee from auto-complete]');
            disp_err('#user_role', '[Select User role from dropdown]');
            disp_err('#filter_status', '[select filter status from auto-complete]');

            return false;
        }
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
            if ($("#user_role_radio").is(":checked")) {
                $('#first_last_name').attr('disabled', 'disabled');
                $('#first_last_name').val('');
                $('#user_id').val('');
                $('#user_role').removeAttr('disabled');
                validate(false);
            }
            if ($("#first_last_name_radio").is(":checked")) {
                $('#first_last_name').removeAttr('disabled');
                $('#user_role').attr('disabled', 'disabled');
                $('#user_id').val('');
                $("#user_role").val($("#user_role option:first").val());
                validate(false);
            }
        });
        if ($("#user_role_radio").is(":checked")) {
            $('#first_last_name').attr('disabled', 'disabled');
            $('#first_last_name').val('');
            $('#user_role').removeAttr('disabled');
        }
        if ($("#first_last_name_radio").is(":checked")) {
            $('#first_last_name').removeAttr('disabled');
            $('#user_role').attr('disabled', 'disabled');
            $("#user_role").val($("#user_role option:first").val());
        }
    });
</script>