
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
  
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/internal_user.png"/>  TMS REPORT - PAID/NOTPAID</h2>
    <div class="table-responsive">
        <?php
//            print_r($tabledata);
       $atr = 'id="search_form" name="search_form" method="POST"';
//        echo form_open("internal_user/activity_log", $atr);
        echo form_open("reports_finance/tms_report", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td class="td_heading">                               
                        Select Any Year
                    </td>
                    <td>
                        <select id='gYear' name='yearVal'>
                            <option value=''>--Select Year--</option>
                            <option value='2016'>2016</option>
                            <option value='2017'>2017</option>
                            <option value='2018'>2018</option>
                            <option value='2019'>2019</option>
                            <option value='2020'>2020</option>
                        </select> 
                    </td>
                    <td class="td_heading">                               
                        Select Any Month
                    </td>
                    <td>
                        <select id='gMonth' name='monthVal'>
                            <option value=''>--Select Month--</option>
                            <option value='1'>Janaury</option>
                            <option value='2'>February</option>
                            <option value='3'>March</option>
                            <option value='4'>April</option>
                            <option value='5'>May</option>
                            <option value='6'>June</option>
                            <option value='7'>July</option>
                            <option value='8'>August</option>
                            <option value='9'>September</option>
                            <option value='10'>October</option>
                            <option value='11'>November</option>
                            <option value='12'>December</option>
                        </select> 
                    </td>
                     <td class="td_heading">                               
                        Payment Status
                    </td>
                    <td>
                        <select id='payStatus' name='payStatus'>
                            <option value=''>--Select Payment Status--</option>
                            <option value='1'>Paid</option>
                            <option value='2'>Not - Paid</option>
                            <option value='3'>Part - Paid</option>
                        </select> 
                    </td>
                </tr>
            </tbody>
        </table>
               <div class="push_right btn_srch">
                            <button type="submit" class="search_button btn btn-xs btn-primary no-mar">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                   
                        </div>
        <?php echo form_close(); ?>
   
    
    <div class="bs-example">
        <div class="table-responsive">
<!--            <div class="add_button space_style">
                <?php if (count($tabledata) > 0 && array_key_exists('EXP_XLS', $this->data['left_side_menu']['INTUSR'])) { ?>
                    <a href="<?php echo site_url('/internal_user/export_users_page' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export Page Fields</span></a> &nbsp;&nbsp;
                    <a href="<?php echo site_url('/internal_user/export_users_full' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span> Export All Fields</span></a>
                <?php } ?>
            </div>-->
            <div style="clear:both;"></div>
            <table id="listview" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.tax_code&o=" . $ancher; ?>" >Module</a></th> 
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Activity On</a></th>
                        <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=role.role_name&o=" . $ancher; ?>" >Updated BY</a></th>
                        
                         <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Date </a></th>
                            <th class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=usr.account_status&o=" . $ancher; ?>" >Action </a></th>
                    </tr>
                </thead>
                <tbody>
                        
                </tbody>      
            </table>
        </div>
        <div style="clear:both;"></div><br>
        <ul class="pagination pagination_style">
            <?php echo $pagination; 
//            echo $this->input->get('cls_name');
           ?>
        </ul>
    </div>
</div>
<script>
$("#search_form").submit(function(){
    ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
    check_remove_id();
        var self = $(".btn_srch"),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
        ///////added by shubhranshu to prevent multiple clicks////////////////  ////////////////////
});
function check_remove_id(){
        $staff = $('#internal_staff').val();
        if($staff == ''){
           $('#user_id').val(''); 
        }
       
    }
</script>