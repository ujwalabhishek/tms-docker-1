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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"> Trainee</h2>

    <div class="table-responsive">
        <?php
        $attr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("trainee", $attr);
        ?>
        <table class="table table-striped">

            <tbody>    
                <tr>
                    <td class="td_heading">Search by Company:</td>
                    <?php
                    $compnies = getcompnies();
                    if ($user->role_id != 'COMPACT') {
                        $company_options[''] = 'All';
                    }

                    foreach ($compnies as $item):
                        $company_options[$item['company_id']] = $item['company_name'];
                    endforeach;
                    $attr = 'id="off_company_name" style="width:150px"';
                    ?>         
                    <td><?php echo form_dropdown('off_company_name', $company_options, $this->input->get('off_company_name'), $attr); ?></td>
                    </td>
                    <td class="td_heading">
                        <?php
                        $pers_radio = array(
                            'name' => 'search_radio',
                            'id' => 'pers_radio',
                            'value' => 'pers_radio',
                            'checked' => ($this->input->get('search_radio') == 'pers_radio') ? TRUE : TRUE,
                            'class' => 'search'
                        );
                        ?>
                        <?php echo form_radio($pers_radio); ?>
                        Trainee Name:
                    </td>
                    <?php
                    $fn = array(
                        'name' => 'pers_first_name',
                        'id' => 'trainee_name_list',
                        'value' => $this->input->get('pers_first_name')
                    );
                    ?>
                    <td><?php
                    
                    echo form_hidden('user_id', $this->input->get('user_id'), 'user_id');
                    echo form_input($fn);
                    ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>  
                    <div id="trainee_name_list_err"></div>
                    </td>
                    <td class="td_heading">
                        <?php
                        $tax_radio = array(
                            'name' => 'search_radio',
                            'id' => 'tax_radio',
                            'value' => 'tax_radio',
                            'checked' => ($this->input->get('search_radio') == 'tax_radio') ? TRUE : FALSE,
                            'class' => 'search'
                        );
                        ?>
<?php echo form_radio($tax_radio); ?>
                        NRIC/FIN No.:
                    </td>
                    <?php
                    echo form_hidden('tax_id', $this->input->get('tax_code'), 'tax_id');
                    $tax_code = array(
                        'name' => 'tax_code',
                        'id' => 'tax_code',
                        'value' => $this->input->get('tax_code')
                    );
                    ?>
                    <td><?php echo form_input($tax_code); ?>
                        <div style="color: #0c0c6e;font-size: 10px;text-shadow: 1px 1px 1px #fdfdfd;">Enter minimum of 4 characters to search</div>
                        <div id="tax_code_err"></div>
                    </td>

                    <td align="center">              
                        <button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span> Search
                        </button>
                    </td>
                </tr>


                <tr>
                    <td class="td_heading">Filter by Status:</td>
                    <td colspan="6">
                        <?php
                        $status_options = array('' => 'All');
                        $values = fetch_metavalues_by_category_id(Meta_Values::STATUS);
                        foreach ($filter_status as $status):
                            $status_options[$status['parameter_id']] = $status['category_name'];
                        endforeach;
                        $attr_js = 'id="status"';
                        echo form_dropdown('status', $status_options, $this->input->get('status'), $attr_js);
                        ?>
                    </td>          
                </tr>


            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>

    <div class="bs-example">

        <div class="table-responsive">

            <div class="add_button space_style">              
                <?php
                $not_array = array("TRAINER", "CRSEMGR");
                if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['TRAINEE']) && (!in_array($this->session->userdata('userDetails')->role_id, $not_array))):
                    ?>
                    <div class="add_button">
                        <a href="<?php echo site_url('/trainee/export_trainee_page' . $export_url) ?>"  class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export Page Fields</span></a> &nbsp;&nbsp;
                        <a href="<?php echo site_url('/trainee/export_trainee_full' . $export_url) ?>"  class="small_text1" onclick="return exportValidate()"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export All Fields</span></a>
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
                        <!--<th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >Field 1.</a></th>-->
                         <th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >NRIC/FIN No.</a></th>
                        <th width="15%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.country_of_residence&o=" . $ancher; ?>" >Country</a></th>
                        <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.registration_date&o=" . $ancher; ?>" >Registration Date</a></th>
                        <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=traineename&o=" . $ancher; ?>" >Trainee Name</a></th>
                        
                        <th width="20%" class="th_header">Account Type</th>
                        <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Status</a></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            /////added by shubhrashu for nric split to field1 and field 2
                            $nric_taxcode=$data['tax_code'];
                            $field[] = substr($nric_taxcode, 0, 5);
                            $field[] = substr($nric_taxcode, 5);
                            ?>
                            <tr <?php if ($data['account_status'] == 'INACTIV') echo "class='danger'"; ?> >
                                <!--<td><a href="<?php echo base_url() . $controllerurl . 'view_trainee/' . $data['user_id']; ?>"><?php echo $field[0]; ?></a></td>-->
                                <td><a href="<?php echo base_url() . $controllerurl . 'view_trainee/' . $data['user_id']; ?>"><?php echo $data['tax_code']; ?></a></td>
                                <td><?php echo ($data['country_of_residence']) ? get_catname_by_parm($data['country_of_residence']) : ''; ?></td>
                                <td>
                                    <?php
                                    echo date('d-m-Y H:i:s', strtotime($data['registration_date']));
                                    ?>
                                </td>
                                <td><?php echo $data['traineename'] . ' ' . $data['last_name']; ?></td>
                               
                                <td>
                                    <?php
                                    if ($data['company_id'] != NULL && $data['company_id'] != '') {
                                        echo $data['company_name'] . ' (Company)';
                                    } else {
                                        echo 'Individual';
                                    }
                                    ?>
                                </td>      
                                <td>
                                    <?php
                                    $account_status = ($data['account_status']) ? get_catname_by_parm($data['account_status']) : '';
                                    if ($account_status == 'Inactive')
                                        echo '<font color="red">' . $account_status . '</font>';
                                    if ($account_status == 'Pending Activation')
                                        echo '<font color="blue">' . $account_status . '</font>';
                                    if ($account_status == 'Active')
                                        echo '<font color="green">' . $account_status . '</font>';
                                    ?>
                                </td>
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
    function form_validates() {
        $trainee_name_list = $('#trainee_name_list').val();
        $tax_codev = $('#tax_code').val();
        $off_company_name = $('#off_company_name').find(":selected").text();
        $status = $('#status').find(":selected").text();
     
        var user_id = $('#user_id').val();
        var tax_id = $('#tax_id').val();
       if(tax_id !='' || user_id !='' || $off_company_name !='All' || $status !='All'){
            remove_err('#trainee_name_list');
            remove_err('#tax_code');
            
            return true;
        }else if($trainee_name_list != ''){
            if(user_id !=''){
                remove_err('#trainee_name_list');
                remove_err('#tax_code');
                return true;
            }else{
                disp_err('#trainee_name_list', '[Select Trainee from auto-complete]');
                remove_err('#tax_code');
                return false;
            }
            
        }else if($tax_codev != ''){
            if(tax_id !=''){
                remove_err('#trainee_name_list');
                remove_err('#tax_code');
                return true;
            }else{
                disp_err('#tax_code', '[Select NRIC from auto-complete]');
                remove_err('#trainee_name_list');
                return false;
            }
            
        }else {
            disp_err('#trainee_name_list', '[Select Trainee from auto-complete]');
            disp_err('#tax_code', '[Select NRIC from auto-complete]');
            return false;
        }
    }
    $(document).ready(function() {
        var search_check = 0;
//        $('#search_form').submit(function() {
//            search_check = 1;
//            return validate(true);
//        });

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
            return true;
           }else{
               return false;
           }
        }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
        $('#search_form input').change(function() {
            if (search_check == 1) {
                return validate(false);
            }
        });
         // added by shubhranshu for dynamic prevention of search form
        $('#trainee_name_list').on("blur", function() {
        $trainee_name_list = $('#trainee_name_list').val().trim();
            if($trainee_name_list ==''){
                $("#user_id").val("");
            }  
       });
       $('#tax_code').on("blur", function() {
        $tax_code = $('#tax_code').val().trim();
            if($tax_code ==''){
                $("#tax_id").val("");
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