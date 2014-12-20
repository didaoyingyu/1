<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Delete Card Decks</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<script language="javascript">
			function deleteDeck(deckId) {
				if (confirm("Do you want to delete this deck? \n Note: All the information related will be deleted and this action cannot be undone!")) {
					window.location = "<?php echo base_url() ?>index.php/game/delete_decks/" + deckId;
				}
			}
			function editDeck(deckId) {
				window.location = "<?php echo base_url() ?>index.php/game/edit_decks/" + deckId;
			}
		</script>
	</head>
	<body>
		<div class="container">
			<!-- Header Section -->
			<div class="header">
				<div class="headerText">Modify Card Deck</div>
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
				<form method="post" action="<?php echo base_url(); ?>/index.php/">
					<table border="1" style="width:100%">
						<thead>
						<th>
							Name
						</th>
						<th colspan="2">
							Action
						</th>
						</thead>
						<?php
						foreach ($allDecks as $deck) {
							echo "<tr><td>" . $deck->deck_name . "</td><td><input type='button' class='btn-danger' onclick='deleteDeck(" . $deck->deck_id . ")' value='Delete'></intput></td><td><input type='button' class='btn-classic' onclick='editDeck(" . $deck->deck_id . ")' value='Edit'></intput></td></tr>";
						}
						?>
					</table>
				</form>
			</div>
			<p><?php echo anchor('', 'Home') ?></p>
		</div>
	</body>
</html>