<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Create Card Deck</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body>
		<div class="container">
			<!-- Header Section -->
			<div class="header">
				<div class="headerText">Create New Card Deck</div>
				<div class="logOut">
					<?php
					if ($this->ion_auth->logged_in()) {
						$user = $this->ion_auth->user()->row();
						$userName = $user->first_name;
						echo "Logg out: " . anchor('game/logout', $userName);
					}
					?>
				</div>
				<div class="clearFloat"></div>
			</div>
			<div class="genaricFormHolder">
				<?php echo $error; ?>
				<?php echo form_open_multipart('deck/new_deck_upload'); ?>
				<p>Deck Name</p>
				<input type="text" name="deck_name"/>
				<p><input type="file" name="userfile" size="20" /></p>
				<p><input type="submit" value="Create Deck" /></p>
				<p>
				<pre>
					<center>Input File format is as followed.</center>
					 
					Question 1 || Question_Notes 1 || Answer 1 || Answer_Notes 1
					Question 2 || Question_Notes 2 || Answer 2 || Answer_Notes 2
					Question 3 || Question_Notes 3 || Answer 3 || Answer_Notes 3
					Question 4 || Question_Notes 4 || Answer 4 || Answer_Notes 4
					 
				</pre>
				</p>
				</form>
				<p><?php echo anchor('', 'Home') ?></p>
			</div>
		</div>
	</body>
</html>