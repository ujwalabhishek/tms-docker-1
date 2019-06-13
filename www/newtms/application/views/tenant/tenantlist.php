<script>
    $(document).ready(function() {
        var tenant_name = $('#tenant_name');
        var tenant_id = $('#tenant_id');
        $("#tenant_name").autocomplete({
            source: function(request, response) {
                tenant_name.val(tenant_name.val().toUpperCase())
                tenant_id.val('');
                $.ajax({
                    url: baseurl + "manage_tenant/get_all_tenant",
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
            }
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
        })
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
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/course.png"> Tenant List</h2>
    <div class="table-responsive">
        <?php
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("manage_tenant", $atr);
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
    <div class="bs-example">
        <div class="table-responsive">
            <div style="clear:both;"></div>
            <table class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>            
                        <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=tenant_name&o=" . $ancher; ?>" >Tenant Name</a></th>
                        <th width="30%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=tenant_address&o=" . $ancher; ?>" >Address</a></th>
                        <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=tenant_email_id&o=" . $ancher; ?>" >Email Id</a></th>
                        <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=tenant_contact_num&o=" . $ancher; ?>" >Contact No.</a></th>
                        <th width="10%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=account_status&o=" . $ancher; ?>" >Status</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            $address = $data['tenant_address'];
                            $address .=($data['tenant_city']) ? ', ' . $data['tenant_city'] : '';
                            $address .=($data['tenant_state']) ? ', ' . $meta_map[$data['tenant_state']] : '';
                            $address .=($data['tenant_country']) ? ', ' . $meta_map[$data['tenant_country']] : '';
                            $status_cls = ($data['account_status'] == 'ACTIVE') ? 'green' : 'red';
                            ?>    
                            <tr>
                                <td><?php echo '<a href="' . base_url() . $pageurl . 'view/' . $data['tenant_id'] . '">' . $data['tenant_name'] . '</a>'; ?></td>
                                <td><?php echo $address; ?></td>
                                <td><?php echo $data['tenant_email_id']; ?></td>
                                <td><?php echo $data['tenant_contact_num']; ?></td>
                                <td><?php echo '<span class="' . $status_cls . '">' . $meta_map[$data['account_status']] . '</span>'; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr class='danger'><td colspan='5' style='text-align:center'><label>There is no tenant available.</label></td></tr>";
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
    $('.search').change(function() {
        if ($("#course_name_radio").is(":checked")) {
            $('#course_code').attr('disabled', 'disabled');
            $('#course_name').removeAttr('disabled');
            $('#course_code').val('');
        }
        if ($("#course_code_radio").is(":checked")) {
            $('#course_name').attr('disabled', 'disabled');
            $('#course_code').removeAttr('disabled');
            $("#course_name").val($("#course_name option:first").val());
        }
    });
    $(document).ready(function() {
        if ($("#course_name_radio").is(":checked")) {
            $('#course_code').attr('disabled', 'disabled');
            $('#course_name').removeAttr('disabled');
        }
        if ($("#course_code_radio").is(":checked")) {
            $('#course_name').attr('disabled', 'disabled');
            $('#course_code').removeAttr('disabled');
        }
    });
</script> 