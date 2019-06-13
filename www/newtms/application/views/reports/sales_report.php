<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common_helper');
$this->load->model('meta_values');
?>
<script>
    $siteurl = '<?php echo site_url(); ?>';
    $baseurl = '<?php echo base_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/reportpayments_due.js"></script>
<div class="col-md-10">
    <?php
    if ($this->session->flashdata('success_message')) {
        echo '<div class="success">' . $this->session->flashdata('success_message') . '!</div>';
    }
    if ($this->session->flashdata('error_message')) {
        echo '<div class="error1">' . $this->session->flashdata('error_message') . '!</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"> Reports - Download Sales Report</h2>
    <div class="table-responsive">
        <?php
        $attr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("reports/sales_report", $attr);
        ?>
        <table class="table table-striped">      
            <tbody>    
                <tr>
                    <td class="td_heading" style="width:15%;">Choose Tenant Name:</td>	          
                    <td>
                        <?php
                        $tenant_name_options[''] = 'Select';
                        foreach ($fetch_all_tenant as $item):
                            $tenant_name_options[$item['tenant_id']] = $item['tenant_name'];
                        endforeach;

                        $attr = 'id="tenant_name" class="tax_code"';
                        echo form_dropdown('tenant_name', $tenant_name_options, $this->input->post('tenant_name'), $attr);
                        ?>
                        <span id="tenant_name_err"></span>  
<!--                        <input type="hidden" name="invoice_no" id="invoice_no" value="">
                        <input type="hidden" name="payment_due_id" id="payment_due_id" value="">
                        <input type="hidden" name="inv_type" id="inv_type" value="">-->
                    </td>
                    <td class="td_heading">Payment Status:</td>
                    <td>              
                        <select name="payment_status" id="payment_status">
                            <option value="ALL">All</option>
                            <option value="NOTPAID">Not Paid</option>
                            <option value="PAID">Paid</option>
                        </select>
                    </td>
                </tr>  
                <tr>
                    <td class="td_heading">Class Start Date :</td>
                    <td><input type="text" name="start_date" autocomplete="off"id="start_date" class="date_picker" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('start_date'); ?>"><span id="start_date_err"></span></td>
                    <td class="td_heading">End Date :</td>
                    <td><input type="text" autocomplete="off"name="end_date"  id="end_date" class="date_picker" placeholder="dd/mm/yyyy" value="<?php echo $this->input->get('end_date'); ?>"><span id="end_date_err"></span></td>
                    <!--<td align="center"><button type="submit" class="btn btn-xs btn-primary no-mar pull-right"><span class="glyphicon glyphicon-search"></span> Search</button></td>-->
                </tr>
                <tr>
                    <td class="td_heading">Training Score:</td>
                    <td>              
                        <select name="training_score" id="training_score">
                            <option value="ALL">All</option>
                            <option value="C">Competent</option>
                            <option value="NYC">Not Yet Competent </option>
                            <option value="E">Exempted</option>
                            <option value="ABS">Absent</option>
                            <option value="TNC">Twice Not Competent </option>
                        </select>
                    </td>
                    <td colspan="2"align="center"><button type="submit" id="dwnreport" class="btn btn-xs btn-primary no-mar pull-right"> Download Report</button></td>
                </tr>
            </tbody>
        </table>
<?php echo form_close(); ?>
    </div>
    
</div>
<script>
    ////////added by shubhranshu to validate the form//////////////////
     $('#search_form').on('submit',function() {
        form_check = 1;
        //alert("form click");
        var status=validate();
        if(status){
        var self = $(this),
        button = self.find('input[type="submit"],button')
        button.attr('disabled','disabled').html('Please Wait..');
        setTimeout(
               function (){
                   $('#dwnreport').removeAttr('disabled');
                   $('#dwnreport').html('Download Report');
              
           },15000);
        return true;
       }else{
           return false;
       }
    });
    function validate(){
        $tn=$('#tenant_name').val();
        $sd=$('#start_date').val();
        $ed=$('#end_date').val();
        $status = true;
        if($tn == ''){
            $('#tenant_name_err').addClass('error').addClass('error3').html('Required');
             $status = false;
        }else{
            $('#tenant_name_err').removeClass('error').removeClass('error3').html('');
           
        }
        
        if($sd == ''){
            
            $('#start_date_err').addClass('error').addClass('error3').html('Required');
             $status = false;
        }else{
            $('#start_date_err').removeClass('error').removeClass('error3').html('');
           
        }
        
        if($ed == ''){
           $('#end_date_err').addClass('error').addClass('error3').html('Required');
           $status = false;
        }else{
            
             $('#end_date_err').removeClass('error').removeClass('error3').html('');
            
        }
        return $status;
    }
    ////////added by shubhranshu to validate the form//////////////////
</script>    