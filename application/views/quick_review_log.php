<!DOCTYPE html>
<html>
	<head>
		<title>Quick Review Log</title>
		<meta name="viewport" content="width=device-width" />
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<script type="text/javascript" src="<?php echo base_url(); ?>js/uajax.js"></script>
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
	</head>
	<body onload="setup()">
		<div class="header">
			<div class="headerText">Quick Review Report - <?php echo $user ?></div>
		</div>

		<script type="text/javascript">
		/*"fix change bug new cards marked wrong showing as - "*/
			function quick_review_log_user()
			{
				if(document.getElementById("userid").selectedIndex != 0) {
					var id = document.getElementById("userid").value;
					window.location = "<?php echo base_url()?>index.php/game/quick_review_log/"+id;
				}
			}
		</script>
		<table style="width:30%;text-align:center" class="top-margin">
			<tr>
				<th>Select User</th>
				<td width="70%">
					<select id="userid" onchange="quick_review_log_user()"  style="width:100%">
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
		<!--<div class="container">-->
			<table border="1" style="width:100%;text-align:center" class="top-margin">
				<thead>
					<tr>
						<th>Date</th>
						<th>Question</th>
						<th>Answer</th>
						<th>User Input</th>
						<th>Deck Name</th>
						<th>Before History</th>
						<th>Before Rank</th>
						<th>Ans</th>
						<th>Reason</th>
						<th>Last Seen</th>
						<th>After History</th>
						<th>Test History</th>
						<th>After Rank</th>
					</tr>
				</thead>
				<tbody id="logBody"><tr><td colspan="13">Loading...</td></tr></tbody>
			</table>
			<input type="hidden" value="0" id="scroll_limit">
			<input type="hidden" value="<?php echo $user_Id?>" id="user_id">
		<!--</div>-->
		<script type="text/javascript">
			/*"fix change bug new cards marked wrong showing as - "*/
			var scrollLimit, userId;
			var loading = false;
			function setup() {
				scrollLimit = document.getElementById("scroll_limit");
				userId = document.getElementById("user_id");
				loadData(false);
				window.onscroll = function () {
					if (window.scrollY + window.innerHeight >= document.body.scrollHeight) {
						loadData(true);
					}
				};
			}
			/*"fix change bug new cards marked wrong showing as - "*/
			function loadData(append) {
				if (loading)
					return;
				loading = true;
				var limit = parseInt(scrollLimit.value);
				new ajaxObject("<?php echo base_url() ?>index.php/game/quick_review_log_scroll",
					function(data) {
						try {
							logData = JSON.parse(data);
							if (logData.length == 0)
								return;
							var logBody = document.getElementById("logBody");
							var table = append ? logBody.innerHTML : "";
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
							scrollLimit.value = limit + logData.length;
						} finally {
							loading = false;
						}
					}).update("scroll_limit=" + limit + "&user_id=" + userId.value, "POST");
			}
		</script>
	</body>
</html>
