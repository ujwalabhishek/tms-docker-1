<style>

    .ui-highlight .ui-state-default{

        background:lightcoral!important;

        border-color: lightcoral!important;

        color: white !important;

    }
    .modal1_5551{
        overflow:scroll !important;
    }
</style>

<div class="col-md-12"  style="min-height: 390px;">

    <?php

    if ($this->session->flashdata('success')) {

        echo "<p class='success'>" . $this->session->flashdata('success') . "</p>";

    } else if ($this->session->flashdata('error')) {

        echo "<p class='error1'>" . $this->session->flashdata('error') . "</p>";

    }

    ?>

    <br>

    <h2 class="panel_heading_style">Trainings Completed</h2>

    <?php if (count($class_list) > 0) { ?>

        <div class="small_heading">

            Training Attended Details as on <?php echo date('M j Y l'); ?>

        </div>

    <?php } ?>

    <div class="table-responsive">

        <table class="table table-striped">

            <?php

            $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');

            $pageurl = $controllerurl;

//            print_r($class_list);

            if (count($class_list) > 0) {

                ?>  

                <thead>

                    <tr>

                        <th class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=class_name&o=" . $ancher; ?>" >Training Detail</a></th>

                        <th class=""><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?f=certificate_coll_on&o=" . $ancher; ?>" >Certificate Collected</a></th>

                        <th class=""><span style="color:#000000;">Valid Till</span></th>

                        <th class=""><span style="color:#000000;">Start Date and Time</span></th>

                        <th class=""><span style="color:#000000;">End Date and Time </span></th>

                        <th class=""><span style="color:#000000;">Attendance Status</span></th>

                        <th class=""><span style="color:#000000;">Result</span></th>

                        <th class=""><span style="color:#000000;">Class Status</span></th>

                        <th class=""><span style="color:#000000;">Action</span></th>

                        

                    </tr>

                </thead>

            <?php } ?>

            <tbody>                              

                <?php

                if (count($class_list) > 0) {

                    $feedback_class_array = array();

                    foreach ($class_list as $class_details) {

                        foreach ($feedback_status as $feedback_details) {

                            $feedback_class_array[] = $feedback_details['class_id'];

                        }

                        if (in_array($class_details['class_id'], $feedback_class_array)) {

                            $txt = 'View Your Feedback';

                            $feedback_details['class_id'];

                            $cls = 'view_feedback';

                        } else {

                            $txt = 'Update Your Feedback';

                            $cls = 'give_feedback';

                        }

                        ?>

                        <tr>

                            <td>

                                <?php if($class_details['att_status']==1){?>

                                <a href="#ex<?php echo $class_details['class_id']; ?>" rel="modal:open" class="small_text1">

                                    <?php echo $class_details['crse_name'] . '-' . $class_details['class_name']; ?>

                                </a><?php }

                                else{ echo $class_details['crse_name'] . '-' . $class_details['class_name'];}?>

                            </td>

                            <td>                         

                                <?php

                                $d = $class_details['certificate_coll_on'];

                                $col_date = date('d/m/Y', strtotime($class_details['certi_coll_date']));

                                $curnt_date = date('d/m/Y');



                                $format = "d/m/Y";

                                $date1 = DateTime::createFromFormat($format, $col_date);

                                $date2 = DateTime::createFromFormat($format, $curnt_date);

                                if (!empty($d) && ($d != '0000-00-00 00:00:00')) {

                                    if ($date1 < $date2) {

                                        $coll_on = date("(F d Y, l)", strtotime($d));

                                        echo 'Yes ' . $coll_on;

                                    }

                                }

                                else 

                                {

                                     if($class_details['att_status']==1 && $class_details['training_score'] == 'C'){

                                    ?>

                                    <span class="red blink">Pending Collection</span>   

                                <?php 

                                     }

                                     else{ echo "---------------------";}

                                }

                                ?>

                            </td>

                            <td>

                                <span>

                                    <?php

                                    $d = $class_details['crse_cert_validity'];

                                    if (empty($d)) {

                                        echo 'Life long';

                                    } else {

                                        $start = $class_details['class_start_datetime'];

                                        $end = $class_details['class_end_datetime'];

                                        $cert_end_date = gmdate("d/m/Y", strtotime("+" . $d . " days", strtotime($end)));

                                        $cur_date = date("d/m/Y");

                                        if ($cert_end_date >= $cur_date) { ### '=' added by sankar

                                            echo $cert_end_date;

                                        } else {

                                            

                                            ?>

                                            <span class="blink">Expired-Renewal Due</span>

                                            <?php

                                        }

                                    }

                                    ?>

                                </span>

                            </td>

            <a href="../../controllers/user.php"></a>

                            <td><?php echo date('d/m/Y h:i A', strtotime($class_details['class_start_datetime']));?></td>

                             <td><?php echo date('d/m/Y h:i A', strtotime($class_details['class_end_datetime']));?></td>

<!--                            <td>

                                <?php

                                if ($class_details['pre_requisite']) {

                                    $all_course_name = explode(",", $class_details['pre_requisite']);

                                    $course_names = '';

                                    foreach ($all_course_name as $course_id):

                                        $course_names .= $status_lookup_course_name[trim($course_id)] . ", ";

                                    endforeach;

                                    if ($course_names) {

                                        echo substr($course_names, 0, -2);

                                    } else {

                                        echo '-';

                                    }

                                } else {

                                    echo '-';

                                }

                                ?>

                            </td>-->

                            <td><?php

                                if($class_details['att_status']==1){

                                    echo "Present";

                                }else{

                                    echo "Absent";

                                }

                                

                            ?>

                            </td>

                            <td> <?php echo $class_details['training_score'];?> </td>                        

                            <td> 

                            <?php

                            $start = strtotime($class_details['class_start_datetime']); 

                            $end = strtotime($class_details['class_end_datetime']);

                            $cur_date = strtotime(date("Y-m-d H:i:s"));

                            if($class_details['class_status'] == 'COMPLTD'){

                                echo 'Completed';

                            }

                            else if ($start > $cur_date && $end > $cur_date) {

                                echo 'Yet to Start';

                            } else if ($start <= $cur_date && $end >= $cur_date) {

                                echo 'In-Progress';

                            } elseif ($end < $cur_date && $start < $cur_date) {

                                echo 'Completed';

                            }

                            

                            ?>

                            </td>

                            

                                <td>

                                <?php

                                if (empty($class_details['certi_coll_date']) || $class_details['certi_coll_date'] == '0000-00-00') {

                                    $cert_date = $class_details['class_end_datetime'];

                                } else {

                                    $cert_date = $class_details['certi_coll_date'];

                                }

                                $end = date('d-m-Y', strtotime($cert_date));

                                $sel = '';

                                if (!empty($class_details['certificate_coll_on'])) {

                                    $sel = date('d-m-Y', strtotime($class_details['certificate_coll_on']));

                                }

                                if($class_details['att_status']==1)
                                {

                                    if($class_details['training_score'] == 'C')
                                    {

                                ?>

                                    <a href="#" sel_cert_date="<?php echo $sel; ?>" cls_id="<?php echo $class_details['class_id']; ?>"

                                       end_date="<?php echo $end; ?>"  class="small_text1 cert_colln" ><u>Certificate</u></a>&nbsp;&nbsp;&nbsp;

                                    <?php //} ?>

                                  <br/>

                                  

                                  <a href="#" class_id="<?php echo $class_details['class_id']; ?>" course_id="<?php echo $class_details['course_id']; ?>" class="small_text1 <?php echo $cls; ?>"><u><?php echo $txt; ?></u></a>&nbsp;&nbsp;

                                  <br/>

                                  

                                  <?php
                                    }
                                    $start = strtotime($class_details['class_start_datetime']); 

                                    $end = strtotime($class_details['class_end_datetime']);

                                    $cur_date = strtotime(date("Y-m-d H:i:s"));                                    

                                    if ($end < $cur_date && $start < $cur_date)
                                    {    //complted                   

                                    ?>

                                    <a href="#" class="view_trainer_feedback small_text1" user_id="<?php echo $class_details['user_id'] ?>" course_id="<?php echo $class_details['course_id']; ?>" class_id="<?php echo $class_details['class_id']; ?>">

                                        <u>View Trainer Feedback</u>

                                    </a>

                                    <?php }?>

                                  

                                   

                                <?php 

                                }else { echo "---------------------";}?>

<!--                                    <a href="#" class_id="<?php echo $class_details['class_id']; ?>" 

                                   course_id="<?php echo $class_details['course_id']; ?>" class="small_text1 <?php echo $cls; ?>">

                                       <?php echo $txt; ?></a>&nbsp;&nbsp;

                                <a href="<?php echo base_url(); ?>trainings/generate_certificate/<?php echo $class_details['class_id']; ?>">

                                    LOC</a>-->

                            </td>

                            

                        </tr>

                        <?php

                    }

                } else {

                    ?>



                <div class='error' style="text-align:center"><label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png">You have not completed any classes yet.</label></div>  



            <?php } ?>

            </tbody>



        </table>

        <?php foreach ($class_list as $class): ?>

            <div class="modalnew modal13" id="ex<?php echo $class['class_id']; ?>" style="display:none;height:333px !important; min-height: 333px !important;">

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

                                <td><span class="crse_des">Classroom Location :</span></td>

                                <td><?php if($class['classroom_location'] == 'OTH'){echo $class['classroom_venue_oth']; }else{echo $status_lookup_location[$class['classroom_location']]; }?></td>

                            </tr>

                            <tr>

                                <td><span class="crse_des">Class Language :</span></td>

                                <td><?php echo $status_lookup_language[$class['class_language']]; ?></td>

                            </tr>



                            <tr>

                                <td><span class="crse_des">Class Status: </span></td>

                                <td style="height:25px;"><?php 

                                    $start = strtotime($class['class_start_datetime']); 

                                    $end = strtotime($class['class_end_datetime']);

                                    $cur_date = strtotime(date("Y-m-d H:i:s"));

                                    if($class['class_status'] == 'COMPLTD'){

                                    echo 'Completed';

                                     }

                                    else if ($start > $cur_date && $end > $cur_date) {

                                        echo 'Yet to Start';

                                    } else if ($start <= $cur_date && $end >= $cur_date) {

                                        echo 'In-Progress';

                                    } elseif ($end < $cur_date && $start < $cur_date) {

                                        echo 'Completed';

                                    }

                                

                                //echo $status_lookup_class_status[$class['class_status']]; 

                                ?></td>

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

    </div>  



    <ul class="pagination pagination_style"><?php echo $pagination; ?></ul>

    

    <br/><br/><br/><br/><br/><br/><br/>

    <span style="color:red;"> ***

        <p style="line-height: 0.428571">C - Competent</p>

        <p style="line-height: 0.928571">NYC - Not Yet Competent</p>  

        <p style="line-height: 0.928571">EX - Exempted</p>

        <p style="line-height: 0.928571">ABS - Absent</p>

        <p style="line-height: 0.928571">2NYC - Twice Not Competent</p>

    </span>

    

</div>



<script>

    $(document).ready(function() {

               

         // modal for view trainer feed back skm start

        $(document).on("click", ".view_trainer_feedback", function(e) {

            e.preventDefault();

            var user_id = $(this).attr("user_id");

            var class_id = $(this).attr('class_id');

            var course_id = $(this).attr('course_id');



            $.post("<?php echo base_url(); ?>trainings/view_trainer_feedback", {user_id: user_id, class_id: class_id, course_id: course_id}, function(html) {



                $(html).appendTo('body').modal();

            });

        });

         

         

//        $('.view_trainer_feedback').click(function(event) {

//        event.preventDefault();

//        var user_id = $(this).attr("user_id");

//        var class_id = $(this).attr('class_id');

//        var course_id = $(this).attr('course_id');

//

//        $.get("view_trainer_feedback", {

//            user_id: user_id,

//            class_id: class_id,

//            course_id: course_id,

//            }, function(html) {

//                $(html).appendTo('body').modal();

//            });

//        });//end

        

        //modal  for  view feedback

        $(document).on("click", ".view_feedback", function(e) {

            e.preventDefault();

            var class_id = $(this).attr('class_id');

            var course_id = $(this).attr('course_id');



            $.post("<?php echo base_url(); ?>trainings/view_feedback", {class_id: class_id, course_id: course_id}, function(html) {



                $(html).appendTo('body').modal();

            });

        });



//modal  for  feedbackform

        $(document).on("click", ".give_feedback", function(e) {

            e.preventDefault();

            //code modification starts here, author: sankar, date: 02/02/2014, reason: multiple window jquery issue

            if($('#ex6').length > 0)

                $('#ex6').remove();

            //code modification ends here

            var class_id = $(this).attr('class_id');

            var course_id = $(this).attr('course_id');

            $.post("<?php echo base_url(); ?>trainings/give_feedback", {class_id: class_id, course_id: course_id}, function(html1) {



                $(html1).appendTo('body').modal();

            });

        });



//modal for certificate

        $(".cert_colln").click(function(e) {

            e.preventDefault();

            var class_id = $(this).attr('cls_id');

            var end_dt = $(this).attr('end_date');



            var sel_date = $(this).attr('sel_cert_date');

            $.post("<?php echo base_url(); ?>trainings/certi_collection", {class_id: class_id}, function(html2) {

                $(html2).appendTo('body').modal();

                var dates = [sel_date];

                $('#cert_colln_date_' + class_id).datepicker({dateFormat: 'dd-mm-yy', minDate: end_dt, maxDate: 0, changeMonth: true,

                    changeYear: true,

                    beforeShowDay: function(date) {

                        var y = date.getFullYear().toString(); // get full year

                        var m = (date.getMonth() + 1).toString(); // get month.

                        var d = date.getDate().toString(); // get Day

                        if (m.length == 1) {

                            m = '0' + m;

                        } // append zero(0) if single digit

                        if (d.length == 1) {

                            d = '0' + d;

                        } // append zero(0) if single digit

                        var currDate = d + '-' + m + '-' + y;

                        if (dates.indexOf(currDate) >= 0) {

                            return [true, "ui-highlight"];

                        } else {

                            return [true];

                        }

                    }

                });



            });

            $('#ex7').remove();

        });



    });

</script>

<script type="text/javascript">

            $(document).ready(function() {

                  

                var form_id = 'feedbackForm';

                function submit_showhide(){

               

                    var display_flag = 0;

                    $('#'+ form_id +' select').each(function() {

                        if ($(this).val().trim().length != 0) {

                            display_flag = 1;

                        }

                    });

                    $('#'+ form_id +' textarea').each(function() {

                        if ($(this).val().trim().length != 0) {

                            display_flag = 1;

                        }

                    });

                    if (display_flag == 1) {

                        //display

                       $('#save').show();

                    } else {

                        //hide

                         $('#save').hide();

                    }

                }

               

                $(document).on('change','#'+ form_id +' select',submit_showhide);

                $(document).on('keyup','#'+ form_id +' textarea',submit_showhide);

            });

        </script> 

<!--validate date -->

<script>

    function validate() {

        var stat_val = true;

        var val = $('.cert_col').val();

        if (val == '') {

            $('#certi_error').text("[required]").addClass('error');

            stat_val = false;

        }

        else {

            $('#certi_error').text(" ").removeClass('error');

        }

        return stat_val;

    }

</script>

