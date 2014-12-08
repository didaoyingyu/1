<!--ALTER TABLE `quick_review_log` ADD `itp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;-->
<!DOCTYPE html>
<html>
	<head>
		<title>Quick Review Log</title>
		<meta name="viewport" content="width=device-width" />
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body>
		<div class="header">
			<div class="headerText">Quick Review Report</div>
		</div>
		<div class="container">
			<table border="1" style="width:100%" id="table" class="top-margin">
				<?php ?>
				<thead>
					<tr>
						<th>
							Date
						</th>
						<th>
							Question
						</th>
						<th>
							Answer
						</th>
						<th>
							Deck Name
						</th>
						<th >
							Before History
						</th>
						<th>
							Before Rank
						</th>
						<th>
							Ans
						</th>
						<th>
							Reason
						</th>
						<th>
							Last Seen
						</th>
						<th>
							After History
						</th>
						<th>
							After Rank
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($logs as $log) {
						?>
						<tr>
							<td>
								<?php echo $log['itp']; ?>
							</td>
							<td>
								<?php echo $log['question']; ?>
							</td>
							<td>
								<?php echo $log['answer']; ?>
							</td>
							<td>
								<?php echo $log['deck_name']; ?>
							</td>
							<td>
								<?php echo $log['before_history']; ?>
							</td>
							<td>
								<?php echo $log['before_rank']; ?>
							</td>
							<td>
								<?php echo $log['ans']; ?>
							</td>
							<td>
								<?php echo $log['reason']; ?>  
							</td>
							<td>
								<script>
									var diffDays ='';
									var date1 = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($log['itp'])); ?>";
									var date = "<?php echo date("Y-m-d\TH:i:s\Z", strtotime($log['utp'])); ?>";
									var date = new Date(date);
									var date1 = new Date(date1);
									var timeDiff = Math.abs(date1.getTime() - date.getTime());
									diffDays = (timeDiff / (1000 *3600*24)).toFixed(5); 
									document.write(diffDays+" Days");
								</script>
							</td>
							<td>
								<?php echo $log['after_history']; ?>
							</td>
							<td>
								<?php echo $log['after_rank']; ?>
							</td>
						</tr>
						<?php
					}
					?> 
				</tbody>
			</table>
		</div>
	</body>
</html>