<!--ALTER TABLE `quick_review_log` ADD `itp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;-->
<!DOCTYPE html>
<html>
	<head>
		<title>Quick Review Log</title>
		<meta name="viewport" content="width=device-width" />
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>

	</head>
	<body>
		<div class="header">
			<div class="headerText">Quick Review Report - <?php echo $user ?></div>
		</div>
		
		<script type="text/javascript">
		/*"fix change bug new cards marked wrong showing as - "*/
			function quick_review_log_user()
			{

				
				if(document.getElementById("userid").selectedIndex == 0)
				{
				}
				else
				{
					var id = document.getElementById("userid").value;
					window.location = "<?php echo base_url()?>index.php/game/quick_review_log/"+id;
				}
				
				
			}
		</script>	
		<table border="0" cellspacing="0" cellpadding="2" style="width:30%;text-align:center" class="top-margin">
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
				<tbody id="logBody">
					<?php
					/*"fix change bug new cards marked wrong showing as - "*/
							foreach($logs1 as $r)
							{    
								   echo "<tr>
									
											<td>".$r->itp."</td>
											<td>".$r->question."</td>
											<td>".$r->answer."</td>
											<td>".$r->ans_userInput."</td>
											<td>".$r->deck_name."</td>
											<td>".$r->before_history."</td>
											<td>".$r->before_rank."</td>
											<td>".$r->ans."</td>
											<td>".$r->reason."</td>
											<td>".$r->utp."</td>
											<td>".$r->after_history."</td>
											<td>".$r->test_history."</td>
											<td>".$r->after_rank."</td>
											
										</tr>";
							}
					?>
				</tbody>
			</table>
			<input type="hidden" value="20" id="scroll_limit">
			<input type="hidden" value="<?php echo $user_Id?>" id="user_id">
		<!--</div>-->
		<script type="text/javascript">
		/*"fix change bug new cards marked wrong showing as - "*/
			$(document).ready(function(){
				
				$(window).scroll(function () {        	
					if ($(window).scrollTop() == ( $(document).height() - $(window).height())) {
						loadData();
					}
				});	 
			});
			/*"fix change bug new cards marked wrong showing as - "*/
			function loadData() {
    	
							var scroll_limit = document.getElementById("scroll_limit").value;
							var user_id = document.getElementById("user_id").value;
							
							if(scroll_limit == 0)
							{
							}
							else
							{
								
									$.ajax({
											type:"post",
											data:"scroll_limit="+scroll_limit+"&user_id="+user_id,
											url:"<?php echo base_url() ?>index.php/game/quick_review_log_scroll",
											dataType : 'json',
											cache: false,
											success:function(html){
							
											var len = html.length;
											
											var txt = "";
											var dds = "";
											var scrollno = "";
											
											if(len > 0)
											{
												
												for(var i=0;i<len;i++)
												{
													scrollno =  html[i].scroll_limit;
													
													txt += '<tr >';
													txt += "<td>"+html[i].itp+"</td>";
													txt += "<td>"+html[i].question+"</td>";
													txt += "<td>"+html[i].answer+"</td>";
													txt += "<td>"+html[i].deck_name+"</td>";
													txt += "<td>"+html[i].before_history+"</td>";
													txt += "<td>"+html[i].before_rank+"</td>";
													txt += "<td>"+html[i].ans+"</td>";
													txt += "<td>"+html[i].reason+"</td>";
													txt += "<td>"+html[i].utp+"</td>";
													txt += "<td>"+html[i].after_history+"</td>";
													txt += "<td>"+html[i].test_history+"</td>";
													txt += "<td>"+html[i].after_rank+"</td>";
													txt += '</tr>';
												}
												
												if(txt != "")
												{
													document.getElementById("scroll_limit").value = scrollno;
													$("#logBody").append(txt);
												}
												
											
											}
									
										}
							   
								});
						
							}
			}

			/*
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
		*/
		</script>
	</body>
</html>