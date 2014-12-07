<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Login</title>
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="headerText"><h3><?php echo lang('create_group_heading'); ?></h3></div></div>
            <p><?php echo lang('create_group_subheading'); ?></p>
            <div id="infoMessage"><?php echo $message; ?></div>
            <div class="genaricFormHolder">
				<?php echo form_open("auth/create_groupu"); ?>
                <p>
					<?php echo lang('create_group_name_label', 'group_name'); ?> <br />
					<?php echo form_input($group_name); ?>
                </p>
                <p>
					<?php echo lang('create_group_desc_label', 'description'); ?> <br />
					<?php echo form_input($description); ?>
                </p>
                <p>
					<?php //print_r($allDecks)?>
                <tr>
                    <td>Select deck: </td><br/>
                <td><select  name="deck[]"  id="deck" multiple="multiple"  style="width:870px">
						<?php
						foreach ($allDecks as $v) {
							?>
							<option value="<?php echo $v->deck_id ?>"><?php echo $v->deck_name ?></option>
						<?php } ?>
                    </select></td>
                </tr>
                </p>
                <p><?php echo form_submit('submit', lang('create_group_submit_btn')); ?></p>
                <p><?php echo anchor('', 'Admin Home') ?></p>
				<?php echo form_close(); ?>
            </div>
        </div>
    </div>
</body>
</html>