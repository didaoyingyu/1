<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Mistakes Report</title>
		<meta name="viewport" content="width=device-width" />
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body>
		<div class="header">
			<div class="headerText">Mistakes Report</div>
		</div>
		<div class="container">
			<table border="1" style="width:100%" id="table" class="top-margin">
				<thead>
					<tr>
						<th>
							si
						</th>
						<th>
							Question
						</th>
						<th >
							Answer  
						</th>					   
					</tr>
				</thead>
				<?php
				$type = '';
				$si = 1;
				$deck_id = 0;
				$game_date = '0';
				foreach ($allCards as $card) {
					if ($deck_id != $card->deck_id || $game_date != $card->game_date) {
						?>
						<tr>
							<td colspan="2" class="blue-bg">
								Date: <?= date("d-m-Y", strtotime($card->game_date)) ?>&nbsp;&nbsp;
								Time: <?= date("H:i:s", strtotime($card->game_date)) ?>
							</td>
							<td  class="blue-bg">
								Deck:   <?=
								$card->deck_name;
								$deck_id = $card->deck_id;
								$game_date = $card->game_date
								?>
							</td>
						</tr>
						<?php
					}
					?>
					<tbody>
						<tr>
							<td>
								<?= $si++; ?>
							</td>
							<td>
								<?= $card->question ?>
							</td>
							<td>
								<?= $card->answer ?> 
							</td>
						</tr>
					</tbody>
					<?php
				}
				?>
			</table>
		</div>
	</body>
</html>