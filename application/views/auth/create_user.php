<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Login</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body>
		<div class="container">
			<!-- Auth login default code -->	
			<div class="header">
				<div class="headerText"><?php echo lang('create_user_heading'); ?></div>
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
				<?php echo form_open("auth/create_user"); ?>
				<p>
					<?php echo lang('create_user_fname_label', 'first_name'); ?> <br />
					<?php echo form_input($first_name); ?>
				</p>
				<p>
					<?php echo lang('create_user_lname_label', 'first_name'); ?> <br />
					<?php echo form_input($last_name); ?>
				</p>
				<p>
					<?php echo lang('create_user_company_label', 'company'); ?> <br />
					<?php echo form_input($company); ?>
				</p>
				<p>
					<?php echo lang('create_user_email_label', 'email'); ?> <br />
					<?php echo form_input($email); ?>
				</p>
				<p>
					<?php echo lang('create_user_phone_label', 'phone'); ?> <br />
					<?php echo form_input($phone); ?>
				</p>
				<p>
					<?php echo lang('create_user_password_label', 'password'); ?> <br />
					<?php echo form_input($password); ?>
				</p>
				<p>
					<?php echo lang('create_user_password_confirm_label', 'password_confirm'); ?> <br />
					<?php echo form_input($password_confirm); ?>
				</p>
				<p><?php echo form_submit('submit', lang('create_user_submit_btn')); ?></p>
				<?php echo form_close(); ?>
				<p><?php echo anchor('', 'Admin Home') ?></p>
			</div>
			<div id="infoMessage" class="infoMessage"><?php echo $message; ?></div>
		</div>
	</body>
</html>