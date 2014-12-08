<!--ALTER TABLE `quick_review_log` ADD `itp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;-->
<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Report</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	</head>
	<body>
		<div class="header">
			<div class="headerText">Quick Review Log Control</div>
		</div>
		<div class="button green" onclick="log_control_save()" style="height: 20px;width: 80px;float: left;margin-bottom: 15px;margin-left: 15px;">
			<p>Save</p>
		</div>
		<?php echo form_open("", array("id" => "review_log_control")); ?>
		<div class="container">
			<table border="1" style="width:100%" id="table" class="top-margin">
				<?php ?>
				<thead>
					<tr>
						<th>
							ID
						</th>
						<th>
							UserName
						</th>
						<th>
							Email
						</th>
						<th >
							Record Status ?
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach ($users as $user) {
						?>
						<tr>
							<td>
								<?php echo $user['id'] ?>
								<input type='hidden' id='user_id' name='log[<?php echo $i; ?>][user_id]' value='<?php echo $user['id']; ?>' />
							</td>
							<td>
								<?php echo $user['username']; ?>
							</td>
							<td>
								<?php echo $user['email']; ?>
							</td>
							<td style="text-align: center">
								<label class="radio-inline" for="example-inline-radio1">
									<?php echo form_radio(array("name" => "log[$i][status]", "value" => "0", "checked" => "checked")); ?> Yes
								</label>
								<label class="radio-inline" for="example-inline-radio1">
									<?php echo form_radio(array("name" => "log[$i][status]", "value" => "1", "checked" => isset($user) && $user['review_log_status'] == '1' ? "checked" : "")); ?> No
								</label>
							</td>
						</tr>
						<?php
						$i++;
					}
					?> 
				</tbody>
			</table>
		</div>
		<?php echo form_close(); ?>
	</body>
	<script>
		function log_control_save() {
			///var log = new object();
			var base_url = '<?php echo base_url(); ?>';
			var saveUrl = base_url + "/index.php/auth/review_log_control";
			$.ajax({
				url: base_url + "/index.php/auth/review_log_control",
				data: $("#review_log_control").serialize(),
				type: "POST",
				dataType: "json"
			}).success(function(result) {
				if (result == null) {
					alert("The Data Has Been Saved");
				}
			}).fail(function() {
				alert("We were unable to get results.");
			});
		}
	</script>
</html>
