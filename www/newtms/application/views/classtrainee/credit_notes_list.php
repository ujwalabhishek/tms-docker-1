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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"> Accounting - Credit Notes</h2>
    <div class="table-responsive">
        <?php
        $attr = 'id="search_form" name="search_form" method="GET" onsubmit="return validate()"';
        echo form_open("accounting/credit_note", $attr);
        ?>
        <table class="table table-striped">      
            <tbody>    
                <tr>
                    <td class="td_heading" style="width:22%;">Search by Credit Note Number:</td>	          
                    <td>
                        <?php
                        $fn = array(
                            'name' => 'credit_note_number',
                            'id' => 'credit_note_number',
                            'value' => $this->input->get('credit_note_number'),
                            'style' => 'width: 90%'
                        );
                        echo form_input($fn);
                        ?>
                        <span id="credit_note_number_err"></span>  
                    </td>
                    <td align="center">              
                        <button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span> Search
                        </button>
                    </td>
                </tr>        
            </tbody>
        </table>
<?php echo form_close(); ?>
    </div>
    <div class="bs-example" style="margin-top:25px">
        <div class="table-responsive">
            <div class="add_button space_style">                            
                <div class="add_button">
                    <?php if (count($tabledata) > 0): ?>
                        <a href="<?php echo site_url('/accounting/export_credit_note' . $export_url) ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to XLS</span></a> &nbsp;&nbsp;
                        <a href="<?php echo site_url('/accounting/pdf_credit_note') . '?' . $_SERVER['QUERY_STRING']; ?>"  class="small_text1">
                    <span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Export to PDF</span></a>                
<?php endif; ?>  
                    <a href="<?php echo site_url('/accounting/add_credit_note') ?>"  class="small_text1"><span class="label label-default black-btn"><span class="glyphicon glyphicon-export"></span>Add New</span></a>
                </div>                
            </div>    
            <div style="clear:both;"></div>
            <table id="testTable" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=CAST(crn.credit_note_number AS decimal)&o=" . $ancher; ?>" >Credit Note #</a></th>
                        <th width="15%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.credit_note_date&o=" . $ancher; ?>" >Credit Note Dt.</a></th>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=CAST(crn.ori_invoice_number AS decimal)&o=" . $ancher; ?>" >Ori. Inv. #</a></th>
                        <th width="10%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.ori_invoice_date&o=" . $ancher; ?>" >Ori. Inv. Dt.</a></th>                
                        <th width="15%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.credit_note_amount&o=" . $ancher; ?>" >Amt(SGD)</a></th>                
                        <th width="20%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.credit_note_issued_by&o=" . $ancher; ?>" >Issued By</a></th>                
                        <th width="30%" class="th_header td_center_align"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crn.tg_ref_number&o=" . $ancher; ?>" >TG Ref. #</a></th>                
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>
                            <tr>
                                <td class="td_center_align"><a href="<?php echo base_url()."accounting/view_credit_note/?q=".$data->credit_id ?>" ><?php echo $data->credit_note_number; ?></a></td>
                                <td class="td_center_align"><?php echo date('d-m-Y', strtotime($data->credit_note_date)); ?></td>
                                <td class="td_center_align"><?php echo $data->ori_invoice_number; ?></td>
                                <td class="td_center_align"><?php echo date('d-m-Y', strtotime($data->ori_invoice_date)); ?></td>
                                <td class="td_center_align">$ <?php echo number_format($data->credit_note_amount, 2); ?></td>
                                <td class="td_center_align"><?php echo $data->credit_note_issued_by; ?></td>
                                <td class="td_center_align"><?php echo $data->tg_ref_number; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no credit notes available.</label></td></tr>";
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
    ///////added by shubhranshu to prevent multiple clicks & validate the form////////////////
    function validate() {
        var retVal = true;
        var table_count = <?php echo count($tabledata); ?>;        
        var credit_note_number = $.trim($("#credit_note_number").val());
        if (credit_note_number.length == 0 && table_count != 0) {
            $("#credit_note_number_err").text("[required]").addClass('error');
            $("#credit_note_number").addClass('error');
            retVal = false;
        } else {
            $("#credit_note_number_err").text("").removeClass('error');
            $("#credit_note_number").removeClass('error');
            var self = $('#search_form'),
            button = self.find('input[type="submit"],button');
            button.attr('disabled','disabled').html('Please Wait..');
        }
        return retVal;
    }
    ////////////added by shubhranshu to prevent multiple clicks////////////////
</script>    