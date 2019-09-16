<div class="col-md-2 col_2_style">
    
    <ul class="ad">
        <li><div id="datepicker" class="date_top"></div></li>
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad1.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad2.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
    </ul>
</div>
<?php
if (count($notifications) > 0) {
    $style = "";
    $class = "col-md-8";
    $class_style = "";
} else {
    $style = "style = 'display:none;'";
    $class = "col-md-10";
    $class_style = "style = 'padding-right:0;'";
}
if (count($course_list) == 0) {
    $message = ($this->input->get('search_value')) ? 'No course found' : 'We are in the process of creating courses for you. Please visit us again!';
    $form_style = ($this->input->get('search_value')) ? "" : "style = 'display:none;'";
}
?>
<div class="<?php echo $class; ?>"  <?php echo $class_style; ?>>    
    <div class="table-responsive">
        <?php
        $attr = 'onsubmit = "return validate_search()" method="GET"';
        echo form_open('courses', $attr);
        ?>
        <table class="table table-striped" <?php echo $form_style; ?> >
            <tbody>
                <tr>
                    <td width="30%" class="td_heading">Search on Course Name:</td>
                    <td colspan="3" width="35%"><input class="inputsearchbox_course" type="text" name="search_value" id="course_name" value="<?php echo ($this->input->get('search_value')) ? $this->input->get('search_value') : $search_value; ?>" placeholder="Course Name"><span id="crse_id"></span>
                    </td>
                    <td width="35%" align="center">                        
                        <button title="Search" value="Search" type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-search"></span> <strong>Search</strong></button>
                        <a href="<?php echo site_url(); ?>courses" style="text-decoration:none !important; color:#000;">
                            <button type="button" value="All" title="All" id="srch_all"  class="btn btn-sm btn-info"><span class="glyphicon glyphicon-refresh"></span> <strong>All</strong></button></a>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <br>

    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-book"></span> Available Courses</h2>
    <span id="not_found"></span>
    <div id="replace_srch_result">
        <div class="table-responsive">
            <div class="col-md-12 min-pad bor-box">
                <?php
                if (count($course_list) > 0) {
                    foreach ($course_list as $crse):
                        ?>                                        
                        <!--Naveen 13-10-14-->                    
                        <div class="col-md-12 min-pad content">
                            <h3 class="course_heading">
                                <?php echo $crse->crse_name; ?>                               
                                <?php if ($course_class_count[$crse->course_id] > 0) { ?>
                                    <a href="<?php echo site_url(); ?>courses/course_class_schedule/<?php echo $crse->course_id; ?>" class="small_text1 pull-right btn1">
                                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-ok"></span> Check Class Schedule</span>
                                    </a>
                                <?php } else { ?>                                    
                                    <a class="small_text1 no_class pull-right" rel="modal:open" href="#alert"><span class="label label-default disable-btn">
                                            <span class="glyphicon glyphicon-ok"></span> Check Class Schedule</span></a>
                                <?php } ?>
                            </h3>
                            <div class="content-des">
                                <span class="pull-left mar-right">
                                    <?php if (!empty($crse->crse_icon)) { ?>
                                        <img src="<?php echo ADMIN_PATH . $crse->crse_icon; ?>" width="80">
                                    <?php } else { ?>
                                        <img src="<?php echo base_url() . "assets/images/default_course.jpg"; ?>" width="80">
                                    <?php } ?>
                                </span>
                                <?php
                                if (strlen($crse->description) > 250) {
                                    echo substr($crse->description, 0, 250) . "..." . '<span><a class="small_text" rel="modal:open" href="#ex' . $crse->course_id . '">View More</a></span>';
                                } else {
                                    echo $crse->description;
                                }
                                ?>
                            </div>
                        </div>                    
                        <!--Naveen 13-10-14 ends-->                    
                        <div class="modalnew" id="ex<?php echo $crse->course_id; ?>" style="display:none;">
                            <h2 class="panel_heading_style"><?php echo $crse->crse_name; ?></h2>
                            <div class="class_desc"><?php echo nl2br($crse->description); ?></div>
                            <div class="popup_cancel11">
                                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
                            </div>
                        </div>
                        <hr>
                        <?php
                    endforeach;
                } else {
                    ?>
                    <div class='error' style="text-align:center"><label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png"> <?php echo $message; ?></label></div> 
                <?php } ?>
                <ul class="pagination pagination_style"><?php echo $pagination; ?></ul>
            </div>
        </div>
    </div>
    <!-- added by bineesh for alert box. starts -->
    <div class="alert" id="alert" style="display:none;">
        <h2 class="panel_heading_style">Warning</h2>
        <div style="text-align:center" class="error1">
            <img src="<?php echo base_url(); ?>assets/images/alert.png"  alt="Warning" />
            There are no classes available.</div>
        <div class="popup_cancel11">
            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
        </div>
    </div>

</div>

<div class="col-md-2 col_2_style" <?php echo $style; ?>>
    <h2 class="panel_heading_style"><span class="glyphicon glyphicon-bullhorn"></span> Announcements</h2>
    <div class=" col-right">

        <ul class="notifi">
            <?php
            if (count($notifications) > 0) {
                $noti_type_array = array(LNDPGE, LNDEMALDB, LNDEMAIL);
                foreach ($notifications as $notify):
                    if (in_array($notify['noti_type'], $noti_type_array)) {
                        ?>

                        <li>
                            <div class="p_padding">                        
                                <?php
                                if (strlen($notify['noti_msg_txt']) > 250) {
                                    echo '<span class="notify_desc">' . substr($notify['noti_msg_txt'], 0, 250) . "..." . '</span><span><a class="small_text" rel="modal:open" href="#notify' . $notify['notification_id'] . '">View More</a></span>';
                                } else {
                                    echo '<span class="notify_desc">' . $notify['noti_msg_txt'] . '</span>';
                                }
                                ?> 
                            </div>
                        </li> 
                        <?php
                    }
                endforeach;
                ?>
            <?php } ?>
        </ul>
    </div>
</div>





