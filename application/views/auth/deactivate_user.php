<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Login</title>
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
    </head>
    <body>
        <div class="container">
            <div class="header"><div class="headerText"><?php echo lang('deactivate_heading'); ?></div></div>
            <div class="genaricFormHolder">
				<?php echo form_open("auth/deactivate/" . $user->id); ?>
                <p>
					<?php echo lang('deactivate_confirm_y_label', 'confirm'); ?>
                    <input type="radio" name="confirm" value="yes" checked="checked" />
					<?php echo lang('deactivate_confirm_n_label', 'confirm'); ?>
                    <input type="radio" name="confirm" value="no" />
                </p>
				<?php echo form_hidden($csrf); ?>
				<?php echo form_hidden(array('id' => $user->id)); ?>
                <p><?php echo form_submit('submit', lang('deactivate_submit_btn')); ?></p>
				<?php echo form_close(); ?>
                <p><?php echo anchor('game/home', 'Admin Home') ?></p>
            </div>
        </div>
    </body>
</html>