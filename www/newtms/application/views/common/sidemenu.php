<div class="col-md-2 col_2_style">
    <h2 class="panel_heading_style">
    <img src="<?php  echo base_url(); ?>/assets/images/menu.png"/> Main Menu</h2>
    <div class="link">
        <ul id="red" class="treeview-red treeview">
            <?php foreach ($this->data['left_side_menu'] as $key => $link_category):?>
                <?php foreach ($link_category as $link_name => $link):?>
                    <?php if (empty($link)):?>
                        <?php continue; ?>
                    <?php endif;?>
                    <?php if($key == $link_name):?>
                        <?php echo '<li><span>' . $link . '</span><ul>'; ?> 
                        <?php else:?>
                        <?php echo '<li><span>' . $link . '</span></li>'; ?>  
                    <?php endif;?>
                   
                <?php endforeach;?>  
                <?php echo '</ul></li>';?>
            <?php endforeach;?>
        </ul>
    </div>
</div> 