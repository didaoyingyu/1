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
					<?php echo lang('edit_user_password_label', 'password'); ?> <br />
					<?php echo form_input($password); ?>
				</p>
				<p>
					<?php echo lang('edit_user_password_confirm_label', 'password_confirm'); ?><br />
					<?php echo form_input($password_confirm); ?>
				</p>
				<h4><?php echo lang('edit_user_groups_heading'); ?></h4>
				<table width="500">
					<?php foreach ($groups as $group): ?>
						<tr>
							<?php
							$gID = $group['id'];
							$checked = null;
							$item = null;
							foreach ($currentGroups as $grp) {
								if ($gID == $grp->id) {
									$checked = ' checked="checked"';
									break;
								}
							}
							?>
							<td>  <?php echo $group['name']; ?></td><td><input type="checkbox" name="groups[]" value="<?php echo $group['id']; ?>"<?php echo $checked; ?>></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<td>Selected deck: </td>
						<td><select  name="deck[]"  id="deck" multiple="multiple"  style="width:870px">
								<?php
								$de = (explode(",", $Decks_value));
								foreach ($allDecks as $v) {
									if (in_array($v->deck_id, $de)) {
										?>
										<option value="<?php echo $v->deck_id ?>" selected="selected"><?php echo $v->deck_name ?></option>
										<?php
									} else {
										?>
										<option value="<?php echo $v->deck_id ?>"><?php echo $v->deck_name ?></option>
										<?php
									}
								}
								?>
							</select></td>
					</tr>
				</table>
				<?php echo form_hidden('id', $user->id); ?>
				<?php echo form_hidden($csrf); ?>
				<p><?php echo form_submit('submit', lang('edit_user_submit_btn')); ?></p>
				<?php echo form_close(); ?>
				<p><?php echo anchor('', 'Admin Home') ?></p>
			</div>
			<div id="infoMessage"><?php echo $message; ?></div>
		</div>
	</body>
</html>