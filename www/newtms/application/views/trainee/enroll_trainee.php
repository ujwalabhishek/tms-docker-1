<?php
$this->load->helper('form');
$this->load->helper('metavalues_helper');
$this->load->helper('common_helper');
$this->load->model('meta_values');
//Added by abdulla
//$this->load->library('session');
//$trainee_id = $this->session->userdata('new_trainee_user_id');

?>
<div class="col-md-10">
    <?php
    if ($success_message) {
        echo '<div class="success">' . $success_message . '!</div>';
    }
    ?>
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/trainee.png"> Trainee</h2>

    <div class="table-responsive">
        <div style="background-color: #f4fcff; height: 50px;text-align: center">
            <p>
                Please click on enroll now in the class list shown below to enroll trainee to a class OR 
                Click <a href="<?php echo base_url() . 'trainee' ?>">here</a> to go back the trainee list page.
            </p>
        </div>
    </div>

    <div class="bs-example">
        <div class="table-responsive">              
            <div style="clear:both;"></div>
            <table id="testTable" class="table table-striped">
                <thead>
                    <?php
                    $ancher = (($sort_order == 'asc') ? 'desc' : 'asc');
                    $pageurl = $controllerurl;
                    ?>
                    <tr>
                        <th width="25%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=crse.crse_name&o=" . $ancher; ?>" >Course-Class</a></th>
                        <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=cls.class_start_datetime&o=" . $ancher; ?>" >Class Duration</a></th>
                        <th width="8%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=cls.class_fees&o=" . $ancher; ?>" >Class Fees(SGD)</a></th>
                        <th width="20%" class="th_header"><a style="color:#000000;" href="<?php echo base_url() . $pageurl . "?" . $sort_link . "&f=cls.class_pymnt_enrol&o=" . $ancher; ?>" >Payment Details</a></th>                        
                        <th width="10%" class="th_header"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($tabledata) > 0) {
                        foreach ($tabledata as $data) {
                            ?>
                            <tr>
                                <td><?php echo $data->crse_name . "-" . $data->class_name; ?></td>
                                <td>
                                    <?php
                                    $duration = $data->total_classroom_duration + $data->total_lab_duration + $data->assmnt_duration;
                                    echo date('d-m-Y H:i:s', strtotime($data->class_start_datetime)) . " - " . date('d-m-Y H:i:s', strtotime($data->class_end_datetime)) . " (" . $duration . " hours)";
                                    ?>
                                </td>
                                <td>
                                    $ <?php echo number_format($data->class_fees, 2, ".", ""); ?>
                                </td>
                                <td>
                                    <?php
                                    $text = "";
                                    if ($data->class_pymnt_enrol == "PDENROL") {
                                        $text = "<span style='color:Red'>Payment Required during enrollment</span>";
                                    } else {
                                        $text = "<span>Payment Not Required during enrollment</span>";
                                    }
                                    echo $text;
                                    ?>
                                </td>
                                <td>
                                    <?php  
                                    $this->load->model('class_trainee_model', 'classtraineemodel');
                                    $res = $this->classtraineemodel->schedule_chck($data->class_id);
                                    if($res == 1)
                                    {
                                    $atr = 'id="enroll_trainee_form" name="enroll_trainee_form"';
                                    //Commented by abdulla
                                    echo form_open("trainee/enroll_trainee", $atr);
                                    //echo form_open("class_trainee/individual_enrollment_view_page", $atr);
                                    $data_course = array(
                                        'id' => 'course_id',
                                        'name' => 'course_id',
                                        'type' => 'hidden',
                                        'value' => $data->course_id
                                    );
                                    echo form_input($data_course);
                                    $data_class = array(
                                        'value' => $data->class_id,
                                        'id' => 'class_id',
                                        'name' => 'class_id',
                                        'type' => 'hidden',                                        
                                    );
                                    echo form_input($data_class);
                                    $data_class_pymnt_enrol = array(
                                        'value' => $data->class_pymnt_enrol,
                                        'id' => 'class_pymnt_enrol',
                                        'name' => 'class_pymnt_enrol',
                                        'type' => 'hidden',                                        
                                    );
                                    echo form_input($data_class_pymnt_enrol);
//                                    $data_user_id = array(
//                                            'value' => $trainee_id,
//                                            'id' => 'trainee_id',
//                                            'name' => 'trainee_id',
//                                            'type' => 'hidden',  
//                                            );
//                                    echo form_input($data_user_id); //Added by abdulla
//                                    $data_account_type = array(
//                                            'value' => 'individual',
//                                            'id' => 'account_type',
//                                            'name' => 'account_type',
//                                            'type' => 'hidden',
//                                            );
//                                    echo form_input($data_account_type); //Added by abdulla

                                    if($data->lock_status==1)
                                    {?>
                                        <button type="submit" class="btn btn-xs  no-mar" disabled="">
                                            <span class="glyphicon glyphicon-retweet"></span>
                                            Class Att. Locked
                                        </button>
                                    <?php 
                                    }else{
                                    ?>
									<input type="hidden" class="privilage" name="privilage"  value="0" />
                                    <button type="submit" class="btn btn-xs btn-primary no-mar">
                                        <span class="glyphicon glyphicon-retweet"></span>
                                        Individual Enrollment
                                    </button>
                                       <?php }
                                    echo form_close();  
				    }
                                    else
                                    {?>
        <form action="<?php echo base_url();?>classes/edit_class" method="post" name ="my_form" id ="my_form">   
            <table class="table table-striped"> 
                 <tr class="danger">
                   <td style="color:red;text-align: center;">
                       <input type="hidden" class="class_id"  name="class_id" value="<?php echo $data->class_id;?>"  />
                    <input type="hidden" class="course_id" name="course_id"  value="<?php echo $data->course_id;?>" />
                   
                     &nbsp;&nbsp;&nbsp;<input type="submit"  value ="Schedule Class" /></td>
                </tr>
            </table>
       </form>     
                                    <?php }?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr class='danger'><td colspan='8' style='text-align:center'><label>There are no Course-Class available.</label></td></tr>";
                    }
                    ?>        
                </tbody>     
            </table>          
        </div>
        <div style="clear:both;"></div><br>

        <ul class="pagination pagination_style">            
            <?php
            echo $pagination;
            ?>
        </ul>
    </div>
</div>  



<script>
//////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM////////////////////////////////////
    $('#enroll_trainee_form').on('submit',function() {
        //search_check = 1;
        //alert("form click");
        var self = $(this),
        button = self.find('input[type="submit"],button'),
        submitValue = button.data('submit-value');
        button.attr('disabled','disabled').html('Please Wait..');
        return true;
    }); //////////////////////////////////////shubhranshu fixed to prevent multiple clicks 14/11/2018 AT 3:45PM///////////////////////
</script>