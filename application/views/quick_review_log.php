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
		<!--<div class="container">-->
			<table border="1" style="width:100%;text-align:center" class="top-margin">
				<thead>
					<tr>
						<th>Date</th>
						<th>Question</th>
						<th>Answer</th>
						<th>Deck Name</th>
						<th>Before History</th>
						<th>Before Rank</th>
						<th>Ans</th>
						<th>Reason</th>
						<th>Last Seen</th>
						<th>After History</th>
						<th>After Rank</th>
					</tr>
				</thead>
				<tbody id="logBody"><tr><td colspan="11">Loading...</td></tr></tbody>
			</table>
		<!--</div>-->
		<script type="text/javascript">
			var logData = <?php echo json_encode(array_map("filter", $logs));
				function filter($log) {
					$interval = strtotime($log['itp']) - strtotime($log['utp']);
					$days = floor($interval / 86400);
					return array($log['itp'], $log['question'], $log['answer'], 
						$log['deck_name'], $log['before_history'], 
						$log['before_rank'], $log['ans'], $log['reason'], 
						"$days, ".gmdate("H:i:s", $interval % 86400), 
						$log['after_history'], $log['after_rank']);
				} ?>;
			var logBody = document.getElementById("logBody");
			var table = "";
			for(i in logData) {
				var row = "<tr>";
				var rowData = logData[i];
				for (j in rowData) {
					row += "<td>" + rowData[j] + "</td>";
				}
				row += "</tr>";
				table += row;
			}
			logBody.innerHTML = table;
		</script>
	</body>
</html>