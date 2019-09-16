<?php echo $this->load->view('common/refer_left_wrapper'); ?>
<style>
    .jquery-modal{background: none !important; width: 0px !important;}
</style>
<div class="ref_col ref_col_tax_code" style="min-height: 500px;">
    <div class="container_row">
        <div style="min-height:360px;">
            <div class="col-md-12 min-pad">

                <?php
                if (!empty($error_message)) {
                    echo '<div style="color:red;font-weight: bold;">' . $error_message . '</div>';
                }
                if ($this->session->flashdata('error')) {
                    echo '<div style="color:red;font-weight: bold;">
                            ' . $this->session->flashdata('error') . '
                        </div>';
                }
                if ($this->session->flashdata('success')) {
                    echo '<div  style="background-color:#CEFADE;padding:10px;">
                                <div style="color:green;font-weight: bold;">
                                    ' . $this->session->flashdata('success') . '
                                </div>
                            </div>';
                }
                ?>     

                <h2 class="panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> List of Available Classes Being Offered as on <?php echo date('F d Y, l'); ?></h2>
                <div class="reg_tbl_div">
                    <div class="bs-example" style="overflow-y: auto; max-height: 500px">
                        <div class="table-responsive">
                            <table class="table table-striped" id="class_schedule">
                                <thead>
                                    <?php
                                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                                    $pageurl = $controllerurl;
                                    ?>        
                                    <tr>
                                        <th width="25%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=class_name&o=" . $ancher; ?>" >Course-Class Description</a></th>
                                        <th width="20%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=class_start_datetime&o=" . $ancher; ?>" >Duration</a></th>
                                        <th width="11%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=class_fees&o=" . $ancher; ?>">Class Fees</a></th>
                                        <th width="44%" class="th_header">Payment Details</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    echo form_open('course/enrol_friend', 'method="post"');
                                    if (count($tabledata) > 0) {
                                        foreach ($tabledata as $class):
                                            ?>
                                            <tr>
                                                <td><a class="small_text1" rel="modal:open" data-hover="on" href="#course_clas<?php echo $class['class_id']; ?>"><?php echo $class['crse_name'] . '-' . $class['class_name']; ?></a></td>
                                                <td><?php echo date('d/m/Y h:i A', strtotime($class['class_start_datetime'])) . ' - ' . date('d/m/Y h:i A', strtotime($class['class_end_datetime'])); ?></td>

                                                <td>$ <?php echo number_format($class['class_fees'], 2, '.', ''); ?></td>
                                                <td>
                                                    <?php echo ($class['class_pymnt_enrol'] == PAY_D_ENROL) ? '<span style="color:blue;">Payment Required  During Enrolment</span>' : '<span style="color:red;">Payment Not  Required  During  Enrolment</span>'; ?>
                                                    <button name="class_id" type="submit" value="<?php echo $class['class_id']; ?>" class="btn btn-sm btn-info" style="float: right;"><strong>Enroll Now</strong></button>
                                                </td>
                                            </tr>  
                                            <?php
                                        endforeach;
                                    }else {
                                        echo "<tr><td class='danger' colspan='4'>No Class Available.</td></tr>";
                                    }
                                            if(!empty($user_id)) // skm when comes from enroll some one start
                                            {
                                            ?>
                                            <input type="hidden" name="user_id" value="<?php echo $user_id;?>">
                                            <input type="hidden" name="friend_id" value="<?php echo $friend_id;?>">
                                            <input type="hidden" name="relation" value="<?php echo $relation;?>">
                                            <?php
                                            } // end
                                    echo form_close();
                                    ?>
                                </tbody>
                            </table>
                            <?php foreach ($tabledata as $class): ?>
                                <div class="modalnew modal13" id="course_clas<?php echo $class['class_id']; ?>" style="display:none;max-height:130px !important;min-height: 124px !important;">
                                    <h2 class="panel_heading_style">Class Details for '<?php echo $class['class_name']; ?>' </h2>
                                    <div class="class_desc_course">
                                        <div class="table-responsive">
                                            <table class="table table-striped">                                                                                    
                                                <tr>
                                                    <td><span class="crse_des">Class Language :</span></td>
                                                    <td><?php echo $status_lookup_language[$class['class_language']]; ?></td>
                                                </tr>

                                                <tr>
                                                    <td><span class="crse_des">Total Duration:</span></td>
                                                    <td><?php echo $class['total_classroom_duration'] + $class['total_lab_duration'] + $class['assmnt_duration']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="crse_des">Course Manager:</span></td>
                                                    <td><?php echo $class['crse_manager']; ?></td>
                                                </tr>

                                            </table>
                                        </div>                                
                                    </div>
                                </div>
                                <?php
                            endforeach;
                            ?>
                            <div style="clear:both;"></div>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo form_open('course/refer', 'id="back_to_refer_form"');
echo form_hidden('user_id', $this->session->userdata('refer_user_id'));
echo form_hidden('submit_but', $this->session->userdata('submit_status'));
echo form_close();
?>
<script>
    $(document).ready(function() {
        $('#back_to_refer').click(function() {
            $('#back_to_refer_form')[0].submit();
        });
    });
</script>