<div class="container_nav_style">
    <div class="container_row">
        <div style="min-height:390px;">
            <div class="col-md-12 min-pad">
                <?php if (isset($course_name[0]['crse_name'])) { ?>
                    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> List of Available Classes for the Course -'<?php echo $course_name[0]['crse_name']; ?>' </h2>
                <?php } ?>

                <div class="bs-example">
                    <div class="table-responsive">
                        <table class="table table-striped" id="class_schedule">
                            <thead>
<?php
$ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
$pageurl = $controllerurl;
?>        
                                <tr>
                                    <th width="25%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=class_name&o=" . $ancher; ?>" >Class Details</a></th>
                                    <th width="15%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=class_start_datetime&o=" . $ancher; ?>" >Date &amp; Time</a></th>
                                    <th width="10%" class="th_header">Duration(hrs)</th>
                                    <th width="25%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=classroom_location&o=" . $ancher; ?>" >Location/Address</a></th>
                                    <th width="10%" class="th_header"><a  href="<?php echo base_url() . $pageurl . "?f=class_language&o=" . $ancher; ?>" >Language</a></th>
                                    <th width="15%" class="th_header">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($tabledata) > 0) {
                                    foreach ($tabledata as $class):
                                        ?>
                                        <tr>
                                            <td><a class="small_text1" rel="modal:open" href="#course_clas<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></a></td>
                                            <td><?php echo date('d/m/Y h:i A', strtotime($class['class_start_datetime'])); ?></td>
                                            <td><?php echo $class['total_classroom_duration'] + $class['total_lab_duration']; ?></td>
                                            <td><?php echo $status_lookup_location[$class['classroom_location']]; ?></td>
                                            <td><?php echo $status_lookup_language[$class['class_language']]; ?></td>
                                            <?php
                                            $total_available_seats = $class['total_seats'];
                                            $total_booked_seats = $class_count[$class['class_id']];
                                            $total_unbooked_seats = $total_available_seats - $total_booked_seats;
                                            $total_seats_of_15perc = ceil($total_available_seats * (15 / 100));
                                            if ($class['class_pymnt_enrol'] == PAY_DURING_ENROL) {
                                                if ($total_unbooked_seats == 0) {
                                                    echo '<td><span class="red">Class Full!</span></td>';
                                                } elseif ($total_unbooked_seats <= $total_seats_of_15perc) {
                                                    if (!empty($this->session->userdata('userDetails')->user_id)) {
                                                        echo '<td><a href="' . base_url() . 'user/enroll_now/?cls=' . $class['class_id'] . '&crs=' . $crid . '" title="Enroll Now">Enrol Now</a> <span class="blink">Hurry!</span><br>Limited Seats</td>';
                                                    } else {
                                                        echo '<td><a class="green" href="' . base_url() . 'user/login" title="Sign Up Now!">Sign Up Now!</a><span class="blink">Hurry!</span><br>Limited Seats</td>';
                                                    }
                                                } else {
                                                    if (!empty($this->session->userdata('userDetails')->user_id)) {
                                                        echo '<td><a class="green" href="' . base_url() . 'user/enroll_now/?cls=' . $class['class_id'] . '&crs=' . $class['course_id'] . '" title="Enroll Now">Enroll Now</a></td>';
                                                    } else {
                                                        echo '<td><a class="green" href="' . base_url() . 'user/login" title="Sign Up Now!">Sign Up Now!</a></td>';
                                                    }
                                                }
                                            } else {
                                                if (!empty($this->session->userdata('userDetails')->user_id)) {
                                                    echo '<td><a class="green" href="' . base_url() . 'user/enroll_now/?cls=' . $class['class_id'] . '&crs=' . $class['course_id'] . '" title="Enroll Now">Enroll Now</a></td>';
                                                } else {
                                                    echo '<td><a class="green" href="' . base_url() . 'user/login" title="Sign Up Now!">Sign Up Now!</a></td>';
                                                }
                                            }
                                            ?>
                                        </tr>                                    
                                        <?php endforeach;
                                    } else { ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">Classes  are  not  available!!</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                                <?php foreach ($tabledata as $class): ?>
                            <div class="modalnew modal13" id="course_clas<?php echo $class['class_id']; ?>" style="display:none;">
                                <h2 class="panel_heading_style">Class Details for '<?php echo $class['class_name']; ?>' </h2>
                                <div class="class_desc_course">
                                    <div class="table-responsive">
                                        <table class="table table-striped">                                                                                    
                                            <tr>
                                                <td width="40%"><span class="crse_des">Class Code :</span></td>
                                                <td><?php echo $class['class_id']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Name :</span></td>
                                                <td><?php echo $class['class_name']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Start Date and Time :</span></td>
                                                <td><?php echo date('d/m/Y h:i A', strtotime($class['class_start_datetime'])); ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class End Date and Time :</span></td>
                                                <td><?php echo date('d/m/Y h:i A', strtotime($class['class_end_datetime'])); ?></td>
                                            </tr>

                                            <tr>
                                                <td><span class="crse_des">Class Language :</span></td>
                                                <td><?php echo $status_lookup_language[$class['class_language']]; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Room Location :</span></td>
                                                <td><?php echo $status_lookup_location[$class['classroom_location']]; ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Status: </span></td>
                                                <td style="height:25px;"><?php echo $status_lookup_class_status[$class['class_status']]; ?></td>
                                            </tr>
                                        </table>
                                    </div>                                
                                </div>
                                <div class="popup_cancel11">
                                    <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
                                </div>
                            </div>
    <?php
endforeach;
?>
                        <div style="clear:both;"></div>
                        <ul class="pagination pagination_style"><?php echo $pagination; ?></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>