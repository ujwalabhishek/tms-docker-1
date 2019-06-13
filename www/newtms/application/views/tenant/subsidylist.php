<script>
    $(document).ready(function() {
        var tenant_name = $('#tenant_name');
        var tenant_id = $('#tenant_id');
        $("#tenant_name").autocomplete({
            source: function(request, response) {
                tenant_name.val(tenant_name.val().toUpperCase())
                tenant_id.val('');
                $.ajax({
                    url: baseurl + "manage_subsidy/get_all_tenant",
                    type: "post",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                var id = ui.item.key;
                tenant_id.val(id);
            },
            minLength: 0
        });
        $('#search_form').submit(function() {
            var tenant_name_err = $('#tenant_name_err');
            if (tenant_id.val().length == 0 && tenant_name.val().trim().length != 0) {
                var message = 'Please select from auto-help.';
                tenant_name_err.addClass('error').html(message);
                return false;
            } else {
                tenant_name_err.removeClass('error').html('');
            }
        });                   
    });    
</script>
<div class="col-md-10" style="min-height: 400px;">
    <?php    
    if ($this->session->flashdata('success')) {
        echo '<div class="success">' . $this->session->flashdata('success') . '</div>';
    }
    if ($this->session->flashdata('error')) {
        echo '<div class="error1">' . $this->session->flashdata('error') . '</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Manage Subsidy
        <a href="<?php echo base_url()?>manage_subsidy/new_subsidy" style="color:white">
            <span style="float: right;cursor: pointer;" id="add_new"><span class="glyphicon glyphicon-plus glyphicon1"></span> <span>Add Subsidy Type</span></span>
        </a>
    </h2>    
    <div class="table-responsive" id="div_list_search">
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("manage_subsidy", $atr);
        ?>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%" class="td_heading">Search by Tenant Name:</td>
                    <td>
                        <?php
                        $data = array(
                            'id' => 'tenant_name',
                            'name' => 'tenant_name',
                            'value' => $this->input->get('tenant_name'),
                            'style' => 'width:400px;'
                        );
                        echo form_input($data);
                        $data = array(
                            'id' => 'tenant_id',
                            'name' => 'tenant_id',
                            'value' => $this->input->get('tenant_id'),
                            'type' => 'hidden'
                        );
                        echo form_input($data);
                        echo '<span id="tenant_name_err"></span>';
                        ?>
                    </td>
                    <td width="10%" align="center"><button title="Search" value="Search" type="submit" class="btn btn-xs btn-primary no-mar">
                            <span class="glyphicon glyphicon-search"></span>
                            Search
                        </button></td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>    
    <div style="clear:both;"></div><br>
    <div class="bs-example" id="div_list">
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>            
                        <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=tm.tenant_name&o=" . $ancher; ?>" >Tenant Name</a></th>
                        <th width="30%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=ts.subsidy_type&o=" . $ancher; ?>" >Subsidy Type</a></th>
                        <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=ts.subsidy_amount&o=" . $ancher; ?>" >Subsidy Amount</a></th>
                        <th width="10%" class="th_header">Last Modified by</th>
                        <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=ts.last_modified_on&o=" . $ancher; ?>" >Last Modified On</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {                            
                            ?>    
                            <tr>
                                <td><?php echo $data['tenant_name']; ?></td>
                                <td><?php echo '<a href="' . base_url() . $pageurl . 'edit_subsidy/' . $data['subsidy_id'] . '">' . $data['subsidy_type'] . '</a>'; ?></td>                                
                                <td><?php echo number_format($data['subsidy_amount'],2); ?></td>
                                <td>ISV Administrator</td>
                                <td><?php echo ($data['last_modified_on'] && $data['last_modified_on']!='0000-00-00 00:00:00') ? date('d-m-Y',strtotime($data['last_modified_on'])) : ''; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr class='danger'><td colspan='5' style='text-align:center'><label>There is no subsidy available.</label></td></tr>";
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