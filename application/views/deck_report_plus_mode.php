<div class="start_test_area">
    <div class='card'><label>Enter cards want to test this time</label><input type='text' id='card_to_test' /><input type="button" name="start_test" id="start_test" value="Start Test Auto" onClick="loadSuperVisedModeParams('automatic')"><input type="button" name="start_test" id="start_test" value="Start Test Manual" onClick="loadSuperVisedModeParams('manual')"></div>
</div>
<div class="header">
    <div class="headerText">Report</div>
</div>
<div class="container" style="width:100%">
    <div class="never_tested_cards">
        <div><label>Untested Cards: </label><?php echo $cardCount['N']; ?></div>
        <div><label>Prev XX Cards: </label><?php echo $cardCount['XX']; ?></div>
        <div><label>Prev X Cards: </label><?php echo $cardCount['X']; ?></div>
        <div><label>Prev write Cards: </label><?php echo $cardCount['O']; ?></div>
    </div>
	<?php
	$type = '';
	foreach ($allCards as $card) {
		if ($card->type == 'asupervised') {
			if ($type != $card->type) {
				?>
				<table border="1" style="width:100%" id="table" class="top-margin">
					<?php
					?>
					<thead>
						<tr>
							<th>
								Date
							</th>
							<th>
								Mark
							</th>
							<th >
								Time  
							</th>
							<th width="20%">
								Avg time   
							</th>
							<th>New   
							</th>
							<th>Prev XX   
							</th>
							<th>Prev X   
							</th>
							<th>Chnage   
							</th>
							<th>To date   
							</th>
							<th> Deck Tested</th>
						</tr>
					</thead>
					<?php
				}
				$type = $card->type;
				?>
				<tbody>
					<tr>
						<td>
							<?= date("d-m-Y", strtotime($card->game_date)) ?>
						</td>
						<td>
							<?= $card->correct_total ?>/<?= $card->card_count ?> 
							<?php
							if ($card->correct_total > 0 && $card->card_count > 0) {
								$perc = ($card->correct_total / $card->card_count) * 100;
								echo number_format((float) $perc, 0, '.', '') . '%';
							} else {
								echo '0%';
							}
							?>
						</td>
						<td>
							<?= $card->total_time ?>seconds
						</td>
						<td>
						   <!-- <?php /* $card->total_time/$card->card_count */ ?>seconds-->
							<?php
							if ($card->total_time > 0 && $card->card_count > 0) {
								$seconds = $card->total_time / $card->card_count;
								echo number_format((float) $seconds, 1, '.', '');
								echo 'seconds';
							} else {
								echo '0seconds';
							}
							?>
						</td>
						<td>
							<?= $card->new_card_correct_count ?>/<?= $card->new_card_count ?>
						</td>
						<td>
							<?= $card->current_true_prexx ?>/<?= $card->prexx ?>
						</td>
						<td>
							<?= $card->current_true_prex ?>/<?= $card->prex ?>
						</td>
						<td>
							+<?= $card->change_plus ?>-<?= $card->change_minus ?>
						</td>
						<td>
							<?= $card->correct_to_date_card_count ?>/<?= $card->TotalCardCount ?>
						</td>
						<td>
							<?= $card->decks_name ?>
						</td>
					</tr>
				</tbody>
				<?php
				if ($type != $card->type) {
					?>
				</table>
				<?php
			}
			?>
			<?php
		} else {
			if ($type != $card->type) {
				?>
				<table border="1" style="width:100%" id="table" class="top-margin">
					<thead>
						<tr>
							<th colspan="8" class="blue-bg"> 
								Review Sessions Since last Test :
							</th>
						</tr>
						<tr>
							<th>
								Date
							</th>
							<th>
								Length Played
							</th>
							<th >
								Decks   
							</th>
							<th >
								# of Cards   
							</th>
							<th>
								CORRECT   
							</th>
							<th>
								INCORRECT     
							</th>
							<th>
								%   
							</th>
							<th>
								Avg Time  
							</th>
						</tr>
					</thead>
					<?php
				}
				$type = $card->type;
				?>
				<tr>
					<td>
						<?= date("d-m-Y", strtotime($card->game_date)) ?>
					</td>
					<td>
						<?= $card->total_time ?>seconds
					</td>
					<td>
						<?= $card->decks_name ?>
					</td>
					<td>
						<?= $card->card_count ?>
					</td>
					<td>
						<?= $card->correct_total ?>
					</td>
					<td>
						<?= $card->wrong_total ?>
					</td>
					<td>
						<?php /* ($card->correct_total/$card->card_count)*100 */ ?>
						<?php
						if ($card->correct_total != 0 && $card->card_count) {
							$perc = ($card->correct_total / $card->card_count) * 100;
							echo number_format((float) $perc, 0, '.', '');
						} else {
							echo 0;
						}
						?>
					</td>
					<td>
						<!--<? /* $card->total_time/$card->card_count */ ?>seconds-->
						<?php
						if ($card->total_time != 0 && $card->card_count) {
							$seconds = $card->total_time / $card->card_count;
							echo number_format((float) $seconds, 1, '.', '');
							echo 'seconds';
						} else {
							echo '0seconds';
						}
						?>
					</td>
				</tr>
				<?php
				if ($type != $card->type) {
					?>
				</table>
				<?php
			}
			?>
			<?php
		}
		?>
		<?php
	}
	?>
</div>
