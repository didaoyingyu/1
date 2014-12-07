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
                <div class="headerText"><?php echo lang('edit_user_heading'); ?></div>
                <div class="logOut">
					<?php
					if ($this->ion_auth->logged_in()) {
						$user = $this->ion_auth->user()->row();
						$userName = $user->first_name;
						echo "Logg out: " . anchor('game/logout', $userName);
					}
					?>
                </div>
            </div>
            <div class="genaricFormHolder">
				<?php echo form_open(uri_string()); ?>
                <p>
					<?php echo lang('edit_user_fname_label', 'first_name'); ?> <br />
					<?php echo form_input($first_name); ?>
                </p>
                <p>
					<?php echo lang('edit_user_lname_label', 'last_name'); ?> <br />
					<?php echo form_input($last_name); ?>
                </p>
                <p>
					<?php echo lang('edit_user_company_label', 'company'); ?> <br />
					<?php echo form_input($company); ?>
                </p>
                <p>
					<?php echo lang('edit_user_phone_label', 'phone'); ?> <br />
					<?php echo form_input($phone); ?>
                </p>
                <p>
					<?php echo lang('edit_user_password_label', 'password'); ?> <br />
					<?php echo form_input($password); ?>
                </p>
                <p>
					<?php echo lang('edit_user_password_confirm_label', 'password_confirm'); ?><br />
					<?php echo form_input($password_confirm); ?>
                </p>
                <h3><?php echo lang('edit_user_groups_heading'); ?></h3>
                <table width="870" border="1">
                    <tr>
                        <th>Admin</th>
                        <th>Savings</th>
                    </tr>
                    <tr>
                        <td>Group</td>
                        <td>fhsdgs</td>
                        </td>
                    </tr>
					<?php foreach ($groups as $group): ?>
						<tr><label class="checkbox">
							<?php
							$gID = $group['id'];
							$checked = null;
							$item = null;
							foreach ($currentGroups as $grp) {
								if ($gID == $grp->id) {
									$checked = ' checked="checked"';
									break;
								}
								//print_r($grp);
							}
							?>
							<?php echo $group['name']; ?><input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>"<?php echo $checked; ?>></label>
					<?php endforeach ?></tr>
                </table>
				<?php echo form_hidden('id', $user->id); ?>
				<?php echo form_hidden($csrf); ?>
                <p><?php echo form_submit('submit', lang('edit_user_submit_btn')); ?></p>
				<?php echo form_close(); ?>
                <p><?php echo anchor('game/home', 'Admin Home') ?></p>
            </div>
            <div id="infoMessage"><?php echo $message; ?></div>
        </div>
    </body>
</html>