<div class="modal1_5" id="ex7">
    <?php  
        $attributes = array('name' => 'certi_colln_form', 'id' => 'certi_colln_form','onsubmit'=>"return(validate());");
        echo form_open('trainings/save_certi_colln_date',$attributes);
    ?> 
    <h2 class="panel_heading_style">Certificate</h2>
    <table class="table table-striped">      
        <tbody>
            <tr>
                <td class="td_heading">Certificate Collected On:</td>
                <td>
                  <?php
                        $cert = array(
                            'name' => 'cert_colln_date',
                            'id' => 'cert_colln_date_'.$class_id,
                            'maxlength' => 15,
                            'class'=>'cert_col',
                            'value'=>(!empty($certificate_coll_on) && ($certificate_coll_on != '0000-00-00 00:00:00')) ? date('d-m-Y',strtotime($certificate_coll_on)) : "",
                        );
                        echo form_input($cert);
                        ?><span id="certi_error"></span>

                    &nbsp;
                </td>
                 <input type="hidden" name="class_id" value="<?php echo $class_id;?>"/>
            </tr>
        </tbody>
    </table>
    <div class="popup_cance89">
        <a href="#" ><button class="btn btn-primary" type="submit">Save</button></a>
    </div>
</div>
<?php form_close();?>
