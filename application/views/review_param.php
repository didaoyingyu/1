<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Review Mode Parameters</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<script>
			function save() {
				document.getElementById("rmParamForm").submit();
			}
		</script>
	</head>
	<body>
		<div class="container">
			<!-- Header Section -->
			<div class="header">
				<div class="headerText">Flash Card Game - Review Mode Parameters</div>
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
			<!-- Login Window -->
			<div class="rmParamFormHolder">
				<?php echo validation_errors(); ?>
				<?php
				if (isset($sucess)) {
					echo $sucess;
				}
				?>
				<form method="POST" action="<?php echo base_url() ?>index.php/game/edit_rm_params" id="rmParamForm">
					<table>
						<tr>
							<td>
								<p>Max No Show Time (sec.)<span class="required" id="maxNoShowTimeTxt"></span></p>
								<input type="text" name="maxNoShowTime" id="maxNoShowTime" value="<?php echo set_value('maxNoShowTime', $maxNoShowTime); ?>" />
							</td>
							<td>
								<p>Min Repeat Time (sec.)<span class="required" id="minRepeatTimeTxt"></span></p>
								<input type="text" name="minRepeatTime" id="minRepeatTime" value="<?php echo set_value('minRepeatTime', $minRepeatTime); ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<p>Rank Increment Step<span class="required" id="rankIncTxt"></span></p>
								<input type="text" name="rankInc" id="rankInc" value="<?php echo set_value('rankInc', $rankInc); ?>" />
							</td>
							<td>
								<p>Rank Decrement Step<span class="required" id="rankDescTxt"></span></p>
								<input type="text" name="rankDesc" id="rankDesc" value="<?php echo set_value('rankDesc', $rankDesc); ?>" />
							</td>
						</tr>
						<tr>
							<td>
								<p>Correct count per increment<span class="required" id="correctCountForIncTxt"></span></p>
								<input type="text" name="correctCountForInc" id="correctCountForInc" value="<?php echo set_value('correctCountForInc', $correctCountForInc); ?>" />
							</td>
							<td>
								<p>Wrong count per decrement<span class="required" id="wrongCountForDescTxt"></span></p>
								<input type="text" name="wrongCountForDesc" id="wrongCountForDesc" value="<?php echo set_value('wrongCountForDesc', $wrongCountForDesc); ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<p>Average Time Exceed Decrement<span class="required" id="avgExceedRankDescTxt"></span></p>
								<input type="text" name="avgExceedRankDesc" id="avgExceedRankDesc" value="<?php echo set_value('avgExceedRankDesc', $avgExceedRankDesc); ?>"/>
							</td>
							<td>
								<p>Question Audio Loop Reset Interval (in ms)<span class="required" id="Q_AudioLoopResetInterval"></span></p>
								<input type="text" name="Q_AudioLoopResetInterval" id="Q_AudioLoopResetInterval" value="<?php echo set_value('Q_AudioLoopResetInterval', $Q_AudioLoopResetInterval); ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<p>Average Exceed percentage <span class="required" id="avgExceedPresntageTxt"></span></p>
								<input type="text" name="avgExceedPercentage" id="avgExceedPercentage" value="<?php echo set_value('avgExceedPercentage', $avgExceedPercentage); ?>"/>
							</td>
							<td>
								<p>Answer Audio Loop Reset Interval (in ms)<span class="required" id="A_AudioLoopResetInterval"></span></p>
								<input type="text" name="A_AudioLoopResetInterval" id="A_AudioLoopResetInterval" value="<?php echo set_value('A_AudioLoopResetInterval', $A_AudioLoopResetInterval); ?>"/>
							</td>
						</tr>
					</table>
					<!-- Login Error -->
					<div class="loginError" id="loginErrorBox"><?php
						if (isset($message)) {
							echo $message;
						}
						?></div>
					<div class="clearFloat"></div><br/>
					<div class="buttonHolder loginButton"><div class="buttonInner"><div class="button" onclick="save()" ><p>Save</p></div></div></div>
				</form>
				<br><br><p><?php echo anchor('', 'Home') ?></p> 
			</div>
		</div>
	</body>
</html>
