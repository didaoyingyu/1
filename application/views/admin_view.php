<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Login</title>
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
        <script>
			function createDeck() {
				window.location = "<?php echo base_url() ?>index.php/deck/";
			}
			function newDeck() {
				window.location = "<?php echo base_url() ?>index.php/deck/create_deck";
			}
			function createUser() {
				window.location = "<?php echo base_url() ?>index.php/auth/create_user";
			}
			function createGroup() {
				window.location = "<?php echo base_url() ?>index.php/auth/create_group";
			}
			function createGroupu() {
				window.location = "<?php echo base_url() ?>index.php/auth/create_groupu";
			}
			function manageUser() {
				window.location = "<?php echo base_url() ?>index.php/auth/";
			}
			function playGame() {
				window.location = "<?php echo base_url() ?>index.php/game/game_view/";
			}
			function editRModeParams() {
				window.location = "<?php echo base_url() ?>index.php/game/edit_rm_params/";
			}
			function editSModeParams()
			{
				window.location = "<?php echo base_url() ?>index.php/game/edit_sm_params/";
			}
			function deleteDecks() {
				window.location = "<?php echo base_url() ?>index.php/game/delete_decks/-1";
			}
        </script>
    </head>
    <body>
        <div class="container">
            <!-- Header Section -->
            <div class="header">
                <div class="headerText">Flash Card Game - Admin</div>
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
            <!-- Admin Window -->
            <div class="adminScreen" id="adminScreen">
                <div class="buttonHolder"><div class="buttonInner"><a class="button green" href="<?php echo base_url() ?>index.php/game/supervised_mode_plus" style="text-decoration:none;color:black" ><p>Supervised Test</p></a></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:createDeck();"><p>Upload New Card Deck</p></div></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:newDeck();"><p>Create New Card Deck</p></div></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:createUser();"><p>Create New User</p></div></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:createGroupu();"><p>Create Group</p></div></div></div> 
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:manageUser();"><p>User Management</p></div></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:editRModeParams();"><p>Edit Review Mode</p></div></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:editSModeParams();"><p>Edit Supervised Mode</p></div></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:playGame();"><p>Play Game</p></div></div></div>
                <br/><br/><br/>
                <div class="buttonHolder"><div class="buttonInner"><div class="button green" onclick="javascript:deleteDecks();"><p>Modify Decks</p></div></div></div>
            </div>
        </div>
    </body>
</html>
