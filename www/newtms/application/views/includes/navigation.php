<?php
$action_name = explode('/', $_SERVER['PATH_INFO']);

?>
<nav id="menu_wrapper">
    <ul class="mcd-menu">

<!--        <li style="margin-left:12px">
            <a class="<?php echo ($action_name[1] == 'user' && $action_name[2] == 'dashboard')?'active':'' ?>" href="<?php echo base_url(); ?>user/dashboard">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/dashboard1.png"></span>
            </a>
        </li>-->
        <li>

            <a class="<?php echo ($action_name[1] == 'user' && $action_name[2] == 'myprofile')?'active':'' ?>" 
               href="<?php echo base_url(); ?>user/myprofile">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/profile1.png"></span>
                <strong class="menu">&nbsp;&nbsp;My Profile</strong>

            </a>
        </li>

        <li>
            <a class="<?php echo (($action_name[1] == 'available_courses')||$action_name[1] == 'available_courses' && 
                    in_array($action_name[2], array('classes','classes_list_by_date')) )?'active':'' ?>" 
               href="<?php echo base_url(); ?>available_courses">
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

        <li>
             <a class="<?php echo ($action_name[1] == 'settings' && $action_name[2] == 'change_password')?'active':'' ?>"
                href="<?php echo base_url(); ?>change_password">
                <span class="profile"><img border="0" src="<?php echo base_url(); ?>assets/images/passwordicon.png"></span>
                <strong class="menu">&nbsp;&nbsp;Change Password</strong>

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