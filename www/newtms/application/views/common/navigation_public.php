<?php
$action_name = explode('/', $_SERVER['PATH_INFO']);

?>
<nav id="menu_wrapper">
    <ul class="mcd-menu">

        
        <li>
            <a class="<?php echo ($action_name[1] == 'user' && $action_name[2] == 'myprofile')?'active':'' ?>" 
               href="<?php echo base_url(); ?>user/myprofile">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/profile1.png"></span>
                <strong class="menu">&nbsp;&nbsp;My Profile</strong>
            </a>
        </li>

        <li>
            <!--<a class="<?php echo (($action_name[1] == 'available_courses')||$action_name[1] == 'available_courses' 
                    && in_array($action_name[2], array('classes','classes_list_by_date')) )?'active':'' ?>" 
               accesskey=""href="<?php echo base_url(); ?>available_courses">
                
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/classes.png"></span>
                <strong class="menu">&nbsp;&nbsp;Available Courses and Classes</strong>

            </a>on doubt-->
            <a class="<?php echo ($action_name[1] == 'course' 
                && in_array($action_name[2],array('course_list','course_class_schedule','classes_list_by_date')) )?'active':'' ?>" 
                             href="<?php echo base_url(); ?>course_public/course_list">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/classes.png"></span>
                <strong class="menu">&nbsp;&nbsp;Available Courses and Classes</strong>
                
            </a>       
        </li>
        <li>
            <a class="<?php echo ($action_name[1] == 'trainings' && $action_name[2] == '')?'active':'' ?>" 
               href="<?php echo base_url(); ?>trainings/">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/trainingicon.png"></span>
                <strong class="menu">&nbsp;&nbsp;Trainings Completed</strong>

            </a>
        </li>


        <li>
            <a class="<?php echo ($action_name[1] == 'payments' && $action_name[2] == '')?'active':'' ?>" 
               href="<?php echo base_url(); ?>payments/">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/paymenticon.png"></span>
                <strong class="menu">&nbsp;&nbsp;Payments and Invoices</strong>

            </a>
        </li>

<!--        <li>
             <a class="<?php echo ($action_name[1] == 'settings' && $action_name[2] == 'change_password')?'active':'' ?>"
                href="<?php echo base_url(); ?>change_password">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/passwordicon.png"></span>
                <strong class="menu">&nbsp;&nbsp;Change Password.</strong>

            </a>
        </li>-->
<!--        <li>
             <a class="<?php echo ($action_name[1] == 'course' && $action_name[2] == 'register_enroll')?'active':'' ?>"
                href="<?php echo base_url(); ?>course/register_enroll">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/profile1.png"></span>
                <strong class="menu">&nbsp;&nbsp;Enroll For Someone</strong>

            </a>
        </li>-->
        <li style="margin-left:12px">
            <a class="<?php echo ($action_name[1] == 'user' && $action_name[2] == 'referral_list')?'active':'' ?>" 
            href="<?php echo base_url(); ?>user/referral_list">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/dashboard1.png"></span>
                  <strong class="menu">&nbsp;&nbsp;Referrar List</strong>
            </a>
        </li>
         <li style="margin-left:12px">
            <a class="<?php echo ($action_name[1] == 'user' && $action_name[2] == 'refer_trainee')?'active':'' ?>" 
            href="<?php echo base_url(); ?>user/refer_trainee">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/refer-refer.png"></span>
                  <strong class="menu">&nbsp;&nbsp;Refer Trainee</strong>
            </a>
        </li>
<!--        <li>
            <a class="<?php echo ($action_name[1] == 'settings' && $action_name[2] == '')?'active':'' ?>" href="<?php echo base_url(); ?>settings">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/settingsicon.png"></span>
                <strong class="menu">&nbsp;&nbsp;My Settings</strong>

            </a>
        </li>-->


    </ul>
</nav> 


<script>
/*    
    $(document).ready(function() {
        var url = window.location.href; 
        $('ul li a').removeClass('active');
        $('ul li a[href="' + url + '"]').addClass('active');
        $('ul li a').filter(function() {
            return this.href == actual_url;
        }).addClass('active');
    });
*/    
</script>