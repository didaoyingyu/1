<div class="header">
	<div class="headerText">Report</div>
</div>
<div class="container" style="width:100%">
<?php if (isset($cardCount)) { ?>
	<div class="never_tested_cards">
		<div><label>Untested Cards: </label><?php echo $cardCount['N']; ?></div>
		<div><label>Prev XX Cards: </label><?php echo $cardCount['XX']; ?></div>
		<div><label>Prev X Cards: </label><?php echo $cardCount['X']; ?></div>
		<div><label>Prev write Cards: </label><?php echo $cardCount['O']; ?></div>
	</div>
<?php }
	$type = '';
	foreach ($allCards as $card) {
		if ($card->type == 'asupervised') {
			if ($type != $card->type) {
				?>
	<table border="1" style="width:100%" id="table" class="top-margin">
		<thead>
			<tr>
				<th>Date</th>
				<th>Mark</th>
				<th>Time</th>
				<th width="20%">Avg Time</th>
				<th>New</th>
				<th>Prev XX</th>
				<th>Prev X</th>
				<th>Change</th>
				<th>To date</th>
				<th>Deck Tested</th>
			</tr>
		</thead>
<?php
	}
	$type = $card->type; ?>
		<tbody>
			<tr>
				<td><?= date("d-m-Y", strtotime($card->game_date)) ?></td>
				<td><?= $card->correct_total ?>/<?= $card->card_count ?> <?php
					if ($card->correct_total > 0 && $card->card_count > 0) {
						$perc = ($card->correct_total / $card->card_count) * 100;
						echo number_format((float) $perc, 0, '.', '') . '%';
					} else {
						echo '0%';
					}
					?></td>
				<td><?= $card->total_time ?> seconds</td>
				<td><?php
					if ($card->total_time > 0 && $card->card_count > 0) {
						$seconds = $card->total_time / $card->card_count;
						echo number_format((float) $seconds, 1, '.', '');
						echo ' seconds';
					} else {
						echo '0 seconds';
					}
					?></td>
				<td><?= $card->new_card_correct_count ?>/<?= $card->new_card_count ?></td>
				<td><?= $card->current_true_prexx ?>/<?= $card->prexx ?></td>
				<td><?= $card->current_true_prex ?>/<?= $card->prex ?></td>
				<td><?php
$total_cards = $card->prex + $card->prexx + $card->new_card_count;
$totalcard_attend = $total_cards;
$total_cards = $total_cards - $card->wrong_total;
if($total_cards < 0) {
	echo "+0".$total_cards;
} else {
	if($card->change_plus > $card->wrong_total && $totalcard_attend != $card->card_count) {
		echo "+".($card->change_plus - $card->wrong_total)."-0";
	} else {
		echo "+".$card->change_plus."-0";
	}
}
?></td>
				<td><?= $card->correct_to_date_card_count ?>/<?= $card->TotalCardCount ?></td>
				<td><?= $card->decks_name ?></td>
			</tr>
		</tbody>
	</table>
<?php
		} else {
			if ($type != $card->type) { ?>
	<table border="1" style="width:100%" id="table" class="top-margin">
		<thead>
			<tr>
				<th colspan="8" class="blue-bg">Review Sessions Since last Test :</th>
			</tr>
			<tr>
				<th>Date</th>
				<th>Length Played</th>
				<th>Decks</th>
				<th># of Cards</th>
				<th>CORRECT</th>
				<th>INCORRECT</th>
				<th>%</th>
				<th>Avg Time</th>
			</tr>
		</thead>
<?php
	}
	$type = $card->type;
	?>
		<tr>
			<td><?= date("d-m-Y", strtotime($card->game_date)) ?></td>
			<td><?= $card->total_time ?> seconds</td>
			<td><?= $card->decks_name ?></td>
			<td><?= $card->card_count ?></td>
			<td><?= $card->correct_total ?></td>
			<td><?= $card->wrong_total ?></td>
			<td><?php
				if ($card->correct_total != 0 && $card->card_count) {
					$perc = ($card->correct_total / $card->card_count) * 100;
					echo number_format((float) $perc, 0, '.', '');
				} else {
					echo 0;
				}
				?></td>
			<td><?php
				if ($card->total_time != 0 && $card->card_count) {
					$seconds = $card->total_time / $card->card_count;
					echo number_format((float) $seconds, 1, '.', '');
					echo ' seconds';
				} else {
					echo '0 seconds';
				}
				?></td>
		</tr>
<?php
		}
	}
	?>
	</table>
</div>
