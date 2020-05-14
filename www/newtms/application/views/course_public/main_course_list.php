<?php  
if(count($notifications) > 0) {
    $style = "";
    $class = "col-md-8";
    $class_style = "style = 'margin-top:0px;'"; 
}else {
    $style = "style = 'display:none;'";
    $class = "col-md-10";    
    $class_style = "style = 'padding-right:0;margin-top:-12px;'";    
}
if(count($course_list) == 0) {
    $message = ($this->input->get('search_value')) ? 'No course found' : 'We are in the process of creating courses for you. Please visit us again!';
    $form_style = ($this->input->get('search_value')) ? "" : "style = 'display:none;'";
}

?>

<style>
    .ui-datepicker{
        width:auto !important;
    }
    .td_style{
        height: 33px;
    padding: 7px !important;
    }
    #course_name{
            width: 100%;
    padding: 4px;
    border-radius: 3px;
    }
    .min-pad > .min-pad:first-child{
            margin-top: 10px;
    border-top: 1px solid #ddd;
    }
    
</style>
<div class="col-md-2 col_2_style">
    <ul class="ad">
        <li><div id="datepicker" class="date_top"></div></li>
        <li <?php echo $style;?>>
            <h2 class="panel_heading_style"><span class="glyphicon glyphicon-bullhorn"></span> Announcements</h2>
            <div class=" col-right">
            <?php foreach ($notifications as $notify): ?>
                    <ul class="notifi">
                        <li>
                            <div class="p_padding">                        
                                <?php
                                if (strlen($notify->noti_msg_txt) > 250) {
                                    echo '<span class="notify_desc">' . substr($notify->noti_msg_txt, 0, 250) . "..." . '</span><span><a class="small_text" rel="modal:open" href="#notify' . $notify->notification_id . '">View More</a></span>';
                                } else {
                                    echo '<span class="notify_desc">' . $notify->noti_msg_txt . '</span>';
                                }
                                ?>
                            </div>
                        </li>
                    </ul>
                    <div class="modalnew modal12" id="notify<?php echo $notify->notification_id; ?>" style="display:none;" >
                        <h2 class="panel_heading_style">Notification</h2>
                        <div class="class_desc"><?php echo nl2br($notify->noti_msg_txt); ?></div>
                        <div class="popup_cancel11">
                            <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
                        </div>
                    </div>
            <?php endforeach; ?>
            </div>
        </li>
        <li><div class='panel_heading_style'><a style='color:white;' href='/course_public/all_course_class'>Course Class Schedule</a></div></li>
        <li><a href="#" class="thumbnail"><img src="<?php echo base_url(); ?>assets/images/ad2.jpg" style="display: block;" data-src="holder.js/100%x180" alt="100%x180"></a></li>
    </ul>
</div>

<div class="col-md-10">    
    <div class="table-responsive" style="padding: 10px 0px;padding-top: 0px;">
        <?php
        $attr = 'onsubmit = "return validate_search()" method="GET"';
        echo form_open('course_public', $attr);
        ?>
        <table class="table table-striped" <?php echo $form_style; ?> >
            <tbody>
                <tr>
                    <td width="30%" class="td_heading td_style">Search on Course Name:</td>
                    <td colspan="3" width="35%"><input class="inputsearchbox_course" type="text" name="search_value" id="course_name" value="<?php echo ($this->input->get('search_value')) ? $this->input->get('search_value') : $search_value; ?>" placeholder="Enter Course Name to Search"><span id="crse_id"></span>
                    </td>
                    <td width="35%" align="center">                        
                        <button title="Search" value="Search" type="submit" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-search"></span> <strong>Search</strong></button>
                        <a href="<?php echo site_url(); ?>course_public" style="text-decoration:none !important;" title="All" id="srch_all" class="btn btn-sm btn-primary">
                            <span class="glyphicon glyphicon-refresh"></span> <strong>All</strong>
                        </a>
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
            <?php if (count($course_list) > 0) {
                foreach ($course_list as $crse):
                    ?>                                        
                        <div class="col-md-12 min-pad content">
                            <h3 class="course_heading">
                                <?php echo $crse->crse_name; ?>   </h3>                            
                                    <a href="<?php echo site_url(); ?>course_public/course_class_schedule/<?php echo $crse->course_id; ?>" class="small_text1 pull-right btn1">
                                        <span class="label label-default black-btn"><span class="glyphicon glyphicon-ok"></span> Check Class Schedule</span>
                                    </a>
                                
                            
                            <div class="content-des">
                            <span class="pull-left mar-right">
                                <?php if(!empty($crse->crse_icon)) { ?>
                                <img src="<?php echo base_url(). $crse->crse_icon; ?>" width="80">
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
                        <div class="modalnew" id="ex<?php echo $crse->course_id; ?>" style="display:none;">
                            <h2 class="panel_heading_style"><?php echo $crse->crse_name; ?></h2>
                            <div class="class_desc"><?php echo nl2br($crse->description); ?></div>
                            <div class="popup_cancel11">
                                <a href="#" rel="modal:close"><button class="btn btn-primary" type="button">Close</button></a>
                            </div>
                        </div>
                    <hr>
                <?php endforeach;
            } else {
                ?>
                <div class='error' style="text-align:center"><label class="no-result"><img src="<?php echo base_url(); ?>assets/images/no-result.png"> <?php echo $message; ?></label></div> 
<?php } ?>
        <ul class="pagination pagination_style"><?php echo $pagination; ?></ul>
        </div>
        </div>
    </div>
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

<!--<div class="col-md-2 col_2_style" <?php echo $style; ?>>
    
</div>-->





