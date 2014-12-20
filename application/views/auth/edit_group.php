<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Login</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body>
		<div class="container">
			<div class="header"><div class="headerText"><?php echo lang('edit_group_heading'); ?></div></div>
			<div class="genaricFormHolder">
				<div id="infoMessage"><?php echo $message; ?></div>
				<?php echo form_open(current_url()); ?>
				<p>
					<?php echo lang('create_group_name_label', 'group_name'); ?> <br />
					<?php echo form_input($group_name); ?>
				</p>
				<p>
					<?php echo lang('edit_group_desc_label', 'description'); ?> <br />
					<?php echo form_input($group_description); ?>
				</p>
				<p>
				<tr>
					<?php
					//$str="$allDecks";
					//print_r(explode(",",$Decks_value));
					//$de=(explode(",",$Decks_value));
					//print_r($de);
					//print_r($allDecks);
					?>
					<td>Selected deck: </td><br/>
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
				</p>
				<p><?php echo form_submit('submit', lang('edit_group_submit_btn')); ?></p>
				<?php echo form_close(); ?>
				<p><?php echo anchor('', 'Admin Home') ?></p>	  
			</div>
		</div>
	</body>
</html>