<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Report</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body>
		<div class="header">
			<div class="headerText">Report - <?php echo $user;?></div>
		</div>
		<div class="container">
		
		<script type="text/javascript">
		/*"fix change bug new cards marked wrong showing as - "*/
			function deck_report()
			{

				
				if(document.getElementById("userid").selectedIndex == 0)
				{
				}
				else
				{
					var id = document.getElementById("userid").value;
					window.location = "<?php echo base_url()?>index.php/game/deck_report/"+id;
				}
				
				
			}
		</script>	
		<table border="0" cellspacing="0" cellpadding="2" style="width:30%;text-align:center" class="top-margin">
					<tr>
						<th>Select User</th>
						<td width="70%">
							<select id="userid" onchange="deck_report()"  style="width:100%">
								<option value="0">Select User</option>
								<?php
									/*"fix change bug new cards marked wrong showing as - "*/
										foreach($all_user as $row)
										{    
											echo '<option value="'.$row['id'].'">'.$row["username"].'</option>';
										}
								?>
							</select>
						</td>
					</tr>
		</table>	
		
		
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
									<th>Old   
									</th>
									<th>New   
									</th>
									<th>Prev XX   
									</th>
									<th>Prev X   
									</th>
									<th>Change   
									</th>
									<th>Total   
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
																		<!--<?php /* ($card->correct_total/$card->card_count)*100 */ ?> %-->
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
									<?php  
											$old1 = $card->correct_total  - $card->new_card_correct_count;
											
											$new1 = $card->card_count - $card->new_card_count;
											$o = $old1 - $card->current_true_prex;
											$n = $new1 - $card->prex;											
											

										if($card->current_true_prexx == 0 &&  $card->prexx == 0)
										{
											echo $o."/".$n;		
										}
										else
										{
											$odl3 = $o - $card->current_true_prexx;
											$new3 = $n - $card->prexx;
											
											echo $odl3."/".$new3;		
										}
										
										
										//$card->correct_total  $card->card_count
										
										//$card->new_card_correct_count  $card->new_card_count;
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
                                +<?php  echo $card->change_plus ?>-<?= $card->change_minus ?>
								
								
                                                                        <?php
                                                                        /*        $total_cards = $card->prex + $card->prexx + $card->new_card_count;
                                                                                $totalcard_attend = $total_cards;
                                                                                $total_cards = $total_cards - $card->wrong_total;
                                                                                if($total_cards < 0)
                                                                                {
                                                                                    echo "+0".$total_cards; 
                                                                                }
                                                                                else
                                                                                {
                                                                                    //echo "+".$card->change_plus."-".$card->wrong_total;
                                                                                    if($card->change_plus > $card->wrong_total && $totalcard_attend != $card->card_count )
                                                                                    {
                                                                                        echo "+".($card->change_plus - $card->wrong_total)."-0";
                                                                                    }
                                                                                    else
                                                                                        echo "+".$card->change_plus."-0";
                                                                                }
                                                                        
                                                                        */?>
                                            
                            
								</td>
								<td>
									<?php  echo $card->change_plus - $card->change_minus ?>
                            
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
	</body>
</html>