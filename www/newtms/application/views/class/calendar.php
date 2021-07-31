<?php
$CI = & get_instance();
$CI->load->model('class_model');
?>


<script>
    $siteurl = '<?php echo site_url(); ?>';
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/classlist.js"></script>
<script type="text/javascript">

$(document).ready(function() {
    
    $month = $('#month').val();
    $year = $('#year').val();
    
    if($month =='' && $year =='')
    {
         var d = new Date(),
    
        n = d.getMonth(),// it calculate month from 0 to 11, that's why i used +1 in month

        y = d.getFullYear();    

        $('#month option:eq('+(n+1)+')').prop('selected', true);

        $('#year option[value="'+y+'"]').prop('selected', true);
    }
   
    
});

</script>
<div class="col-md-10">
   
    <h2 class="panel_heading_style"><img src="<?php echo base_url(); ?>/assets/images/class.png"> Calendar Schedule </h2>
    <div class="col-md-12">
        
        <div class="table-responsive">
        <?php
        $this->load->helper('form');
        $this->load->helper('metavalues_helper');
        $this->load->model('meta_values');
        $atr = 'id="search_form" name="search_form" method="GET"';
        echo form_open("classes/calendar", $atr);
        ?> 
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="24%"  class="td_heading">Month : <?php echo form_dropdown("month", $month_array, $this->input->get('month'), 'id="month"'); ?></td>
                   
                    
                    <td width="24%" class="td_heading">Year : <?php echo form_dropdown("year", $year_array, $this->input->get('year'), 'id="year"'); ?></td>
                   
                    
                    
                    <td width="52%">
                        <input type="submit" class="search_button btn btn-xs btn-primary no-mar" name="submit" id="submit" value="Submit">
                    </td>
                    
                </tr>
            
            </tbody>
        </table>
            
        <?php echo form_close(); ?>
    </div>
        <br/>
        <div class="table-responsive">
                <?php 
                
                        $dt = DateTime::createFromFormat('!m', $month);
                        $month_name = $dt->format('F');
                ?>
            <table class="table table-striped">
                <?php
                for($i=1;$i<=$days_in_month;$i++)
                {
                    $date_format = $year.'-'.$month.'-'.$i; 
                    $datetime = DateTime::createFromFormat('Y-m-d', $date_format);
                    $day_name =  $datetime->format('l');
                    $day_numeric =  $datetime->format('d');                    
                    $date = $year.' '.$month_name.', '.$day_numeric.' '.$day_name; 
                    ?>
                <tr>
                    
                    
                    <td width="25%"><div class="col-md-12" style="padding:10px;margin:10px;"><b><?php echo $date; ?></b></div></td>
                    
                    <td width="75%" style="<?php echo ($day_name == 'Saturday' || $day_name == 'Sunday')?'background-color:#FFCFCF':'';?>">
                        <div class="col-md-12" style=" margin-right:0px;padding-right:0px;">
                            <?php 
                                $res = $CI->class_model->get_schedule_class($date_format); 
//                                echo $this->db->last_query();
                                foreach($res as $row)
                                {?>
                                    <div class="col-md-3" style="background-color: <?php echo $row['color']; ?>; color:white; width: 150px; margin: 10px;padding:10px;">
                                        <p><?php echo '<b><u>'.$row['crse_name'].' - '.$row['class_name'].'</u></b>'; ?></p>
                                  
                                        <p style="font-size:11px;">
                                            <?php   if($row['session_type_id']=='S1')
                                            {
                                                echo"<b>SESSION 1</b>";
                                                
                                            }elseif($row['session_type_id']=='S2'){
                                                echo"<b>SESSION 2</b>";
                                            }else{
                                                echo "<b>BREAK</b>";
                                            }?>
                                        </p> 
                                        <p style="font-size: 10px;">
                                            <?php
                                             echo date('h:ia', strtotime($row['session_start_time'])).' - '.date('h:ia', strtotime($row['session_end_time']));
                                            ?>
                                        </p>
                                           
                       
                                            
                                    </div>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
                <?php } ?>    
            </table>
        </div>
    </div>
</div>

  
