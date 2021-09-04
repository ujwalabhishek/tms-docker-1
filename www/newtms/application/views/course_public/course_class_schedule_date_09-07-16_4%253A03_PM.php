<div class="container_nav_style">
    <div class="container_row">
        <div style="min-height: 360px;">
            <div class="col-md-12 min-pad">
                <?php
                $timestamp = strtotime($course_date);
                $month = date('F', $timestamp);
                $day = date('d', $timestamp);
                $year = date('Y', $timestamp);
                $dayname = date('l', $timestamp);
                ?>
                <h2 class="panel_heading_style"><span class="glyphicon glyphicon-th-list"></span> List of Available Classes as on' <?php echo $month . ' ' . $day . ' ' . $year . ', ' . $dayname; ?>'</h2>
                <div class="bs-example">
                    <div class="table-responsive">
                        <table class="table table-striped" id="class_schedule">
                            <?php if (count($tabledata) > 0) { ?>
                                <thead>
                                    <?php
                                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                                    $pageurl = $controllerurl;
                                    ?>        
                                    <tr>
                                        <th width="20%" ><a  href="<?php echo base_url() . $pageurl . "?f=class_name&o=" . $ancher; ?>" >Class Details</a></th>
                                        <th width="10%" >Class Start Time</th>
                                        <th width="11%" >Duration(hrs)</th>
                                        <th width="11%" >Trainer Aide</th>
                                        <th width="10%" >Trainer</th>
                                        <th width="15%" ><a  href="<?php echo base_url() . $pageurl . "?f=classroom_location&o=" . $ancher; ?>" >Location/Address</a></th>
                                        <th width="10%" ><a  href="<?php echo base_url() . $pageurl . "?f=class_language&o=" . $ancher; ?>" >Language</a></th>
                                        <th width="5%" >Available<br/>Seats</th>
                                        <th width="20%">Status</th>
                                    </tr>                                
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($tabledata as $class):
                                        ?>
                                        <?php
                                        
                                        $user_id = $this->session->userdata('userDetails')->user_id; // get user_id from session
                                        
                                        $result = $this->course_model->is_user_enrolled1($user_id,$class['class_id'],$class['course_id']);// check user alreaady enrolled in particular class or not
                                        $class['user_id'] = $user_id;
                                        if($user_id!='')                                
                                        {  
                                            $enroll_link_prefix = '<a class="enroll_now_link" href="' . base_url() . 'course/class_enroll1/' . $class['course_id'] . '/' . $class['class_id'] . '" data-class="'.$class['class_id'].'" data-course="'.$class['course_id'].'" data-user="'.$class['user_id'].'">'; 
                                        
                                           
                                        }
                                        else{
                                            
                                            $enroll_link_prefix = '<a class="enroll_now_link" href="' . base_url() . 'course/class_member_check/' . $class['course_id']  . '" data-class="'.$class['class_id'].'" data-course="'.$class['course_id'].'">';;  
                                           
                                            
                                        }
                                        
                                        
//                                        $enroll_link_prefix = '<a class="enroll_now_link" href="' . base_url() . 'course/class_enroll/' . $class['course_id'] . '/' . $class['class_id'] . '" data-class="'.$class['class_name'].'" data-course="'.$class['crse_name'].'">';
                                        $enroll_link_label = 'Enroll Now';
                                        $enroll_link_suffix = '</a>';
                                        $enroll_link = $enroll_link_prefix . $enroll_link_label . $enroll_link_suffix;
                                        ?>
                                        <tr>
                                            <td>
                                                <a class="small_text1" rel="modal:open" href="#ex<?php echo $class['class_id']; ?>">
                                                    <?php echo $class['class_name']; ?>
                                                </a>
                                            </td>
                                            <td><?php echo substr($class['class_start_datetime'], -8);?> </td>
                                            <td><?php echo $class['total_classroom_duration'] + $class['total_lab_duration'] + $class['assmnt_duration']; ?></td>
                                            <td><div class="table-scrol" style="    height: 200px;"><?php echo $class['crse_manager']; ?></div></td>
                                            <td><div class="table-scrol" style="    height: 200px;"><?php echo $class['classroom_trainer']; ?></div></td>
                                            <td><?php echo $status_lookup_location[$class['classroom_location']]; ?></td>
                                            <td><?php echo $status_lookup_language[$class['class_language']]; ?></td>
                                            <td><?php echo $class['available']; ?></td>
                                            <?php
                                            $total_available_seats = $class['total_seats'];
                                            $total_booked_seats = $class_count[$class['class_id']];
                                            $total_unbooked_seats = $total_available_seats - $total_booked_seats;
                                            $total_seats_of_15perc = ceil($total_available_seats * (15 / 100));
                                            if ($class['class_pymnt_enrol'] == PAY_D_ENROL) {
                                                if ($total_unbooked_seats <= 0) {
                                                    echo '<td><span class="red">Class Full!</span></td>';
                                                } else if ($total_unbooked_seats <= $total_seats_of_15perc) {
                                                    echo '<td>' . $enroll_link_prefix . '<span class="orange1">Limited Seats</span> <span class="blink"> Hurry!</span> ' . $enroll_link_label . $enroll_link_suffix . '</td>';
                                                } else if ($total_unbooked_seats > 0) {
                                                    echo '<td>' . $enroll_link . '</td>';
                                                }
                                            } else {
                                                if ($total_unbooked_seats <= 0) {
                                                    echo "<td><span class='red'>Class Full!</span></td>";
                                                } else {
                                                    echo '<td>' . $enroll_link . '</td>';
                                                }
                                            }
                                            ?>
                                        </tr>

                                        <?php
                                    endforeach;
                                } else {
                                    ?>
                                    <tr class="danger">
                                        <td colspan="6" style="text-align: center;"><label>There are no classes available.</label></td>
                                    </tr>
<?php } ?>
                            </tbody>
                        </table>
<?php foreach ($tabledata as $class): ?>
                            <div class="modalnew modal13" id="ex<?php echo $class['class_id']; ?>" style="display:none;">
                                <h2 class="panel_heading_style">Class Details for '<?php echo $class['class_name']; ?>'</h2>
                                <div class="class_desc_course">                                                                                       
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <td width="40%"><span class="crse_des">Course Name :</span></td>
                                                <td><?php echo $class['crse_name']; ?></td>                                            
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
                                                <td><span class="crse_des">Classroom Duration (hrs) :</span></td>
                                                <td><?php echo $class['total_classroom_duration']; ?></td>                                            
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Lab Duration (hrs) :</span></td>
                                                <td><?php echo $class['total_lab_duration']; ?></td>                                            
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Assessment Duration (hrs) :</span></td>
                                                <td><?php
                        if (empty($class['assmnt_duration'])) {
                            echo 0;
                        } else {
                            echo $class['assmnt_duration'];
                        }
    ?></td>
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Fees (SGD) :</span></td>
                                                <td><?php echo '$ ' . number_format($class['class_fees'], 2, '.', ''); ?></td>                                            
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Language :</span></td>
                                                <td><?php echo $status_lookup_language[$class['class_language']]; ?></td>                                            
                                            </tr>
                                            <tr>
                                                <td><span class="crse_des">Class Room Location :</span></td>
                                                <td><?php echo $status_lookup_location[$class['classroom_location']]; ?></td>                                            
                                            </tr>
                                            <?php if ($class['lab_location']): ?>
                                                <tr>
                                                    <td><span class="crse_des">Lab Location :</span></td>
                                                    <td><?php echo $status_lookup_location[$class['lab_location']]; ?></td>                                            
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td><span class="crse_des">Class Details/ Description :</span></td>
                                                <td><div class="table-content1"><?php echo $class['description']; ?></div></td>                                            
                                            </tr>
                                        </table>
                                    </div>                                        
                                </div>
                                <div class="popup_cancel11">
<!--                                    <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>-->
                                </div>
                            </div>
                            <?php
                        endforeach;
                        ?>                   
                        <div style="clear:both;"></div><br>
                        <ul class="pagination pagination_style"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--<div class="modal1_055" id="ex11" style="display:none;max-height: 200px;">
        <h2 class="panel_heading_style">Warning</h2>   
        <div>
            <p style="text-align: center;">
                <span style="font-weight:bold;font-size: 14px;">Do you want to enroll for this course <span style="color:red;">'<span class="course_name"></span> - <span class="class_name"></span>'</span>?</span>
            </p>
            <div class="popup_cancel9">
                <div rel="modal:close">
                    <a href="#" class="href_link"><button class="btn btn-primary trainee_deatils_yes" type="button">Yes</button></a>
                    <a href="#" rel="modal:close"><button class="btn btn-primary trainee_deatils_no" type="button">NO</button></a>
                </div>
            </div>
        </div>
    </div>-->
<!--<script>
    $(document).ready(function(){
        $('.enroll_now_link').click(function(){
            var course = $(this).data('course');
            var cls = $(this).data('class');
            var href_link = $(this).attr('href');
            $('.course_name').text(course);
            $('.class_name').text(cls);
            $('.href_link').attr('href',href_link);
            $('#ex11').modal();
            return false;
        });
    });
</script>-->
<!--SKM START-->

<div class="modal1_055" id="ex11" style="display:none;max-height: 200px;   ">
    <h2 class="panel_heading_style">Confirmation <?php echo $class1['class_id']; ?></h2>   
        <div>
            <div class="popup_cancel9" style=" width: 100%;">
                <div rel="modal:close" style="text-align:center;">
                    <span class="enrol_for_self"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="enrol_for_someone"></span>
                </div>
            </div>
        </div>
</div>


<?php if(!empty($user_id)){?>
<script>
    $(document).ready(function(){
       
        $('.enroll_now_link').click(function(){
           
           
            var course = $(this).data('course');
            var cls = $(this).data('class');
             var uid = $(this).data('user');
            var href_link = $(this).attr('href');
           
            $.ajax({
                        url: baseurl + "user/check_user_enrollment",
                        type: "post",
                       
                        data: {user_id: uid, course_id: course, class_id: cls},
                        success: function(res)
                        {
                            if(res == 1) {
                               $('.enrol_for_self').html("<button class='btn btn-primary btn1' type='button' style='background-color: gray;cursor:default;    border-color: gray;'>Already Enrolled</button>");
                                $('.enrol_for_someone').html("<a id='skm' href='<?php echo base_url();?>course/register_enroll/"+course+"/"+cls+"'> <button class='btn btn-primary btn1' type='button'>Enroll For Someone</button></a>");
                            }else{
                                $('.enrol_for_self').html("<a id='skm' href='<?php echo base_url();?>course/create_enroll_self_loggedin/"+course+"/"+cls+"'> <button class='btn btn-primary btn1' type='button'>Enroll For Self</button></a>");
                                 $('.enrol_for_someone').html("<a id='skm' href='<?php echo base_url();?>course/register_enroll/"+course+"/"+cls+"'> <button class='btn btn-primary btn1' type='button'>Enroll For Someone</button></a>");
                            }


                        }                
                    });
            
           
           
            $('.course_name').text(course);
            $('.class_name').text(cls);
            $('.href_link').attr('href',href_link);
            $('#ex11').modal();
            return false;
        });
    });
</script>
<?php
}else{
?>
<script>
    $(document).ready(function(){
       
        $('.enroll_now_link').click(function(){
           
           
            var course = $(this).data('course');
            var cls = $(this).data('class');
            var href_link = $(this).attr('href');
           
           //$('.enrol_for_self').html("<a id='skm' href='<?php echo base_url();?>course/register_enroll/"+course +"/"+cls+"'> <button class='btn btn-primary' type='button'>Enroll For Self</button></a>");
         $('.enrol_for_self').html("<a id='skm' href='<?php echo base_url();?>course/class_member_check/"+course +"/"+cls+"'> <button class='btn btn-primary' type='button'>Enroll For Self</button></a>");
           
            
            $('.enrol_for_someone').html("<a id='skm' href='<?php echo base_url();?>course/referral_credentials1/"+course +"/"+cls+"'> <button class='btn btn-primary' type='button'>Enroll For Someone</button></a>");
            $('.course_name').text(course);
            $('.class_name').text(cls);
            $('.href_link').attr('href',href_link);
            $('#ex11').modal();
            return false;
        });
    });
</script>
<?php } ?>

<style>
    .modal1_055 {
    margin-left: -125.5px !important;
}
.btn1{
    padding: 6px 30px !important;
    font-size: 13px !important;
}
</style>
<!--SKM END-->