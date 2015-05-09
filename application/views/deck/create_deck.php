<!DOCTYPE html>
<html>
	<head>
		<title>Flash Card Game - Add Card Decks</title>
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
		<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
		<script src="<?php echo base_url() ?>js/ajaxfileupload.js"></script>
		<style>
			.container{
				width:100%;
			}
		</style>
		<script>
			function getValuesOnTrOnQ(obj)
			{
				var que = $(obj).val();
				var tr = $(obj).parents('tr').first();
				$(tr).attr("data-question", que);
				$(tr).attr("data-unchanged", "changed");
			}
			function getValuesOnTrOnA(obj)
			{
				var ans = $(obj).val();
				var tr = $(obj).parents('tr').first();
				$(tr).attr("data-answer", ans);
				$(tr).attr("data-unchanged", "changed");
			}
			function getValuesOnTrOnQN(obj)
			{
				var que = $(obj).val();
				var tr = $(obj).parents('tr').first();
				$(tr).attr("data-question_note", que);
				$(tr).attr("data-unchanged", "changed");
			}
			function getValuesOnTrOnAN(obj)
			{
				var ans = $(obj).val();
				var tr = $(obj).parents('tr').first();
				$(tr).attr("data-answer_note", ans);
				$(tr).attr("data-unchanged", "changed");
			}
		</script>
	</head>
	<body>
		<div class="container">
			<!-- Header Section -->
			<div class="header">
				<div class="headerText">Add Card Deck</div>
				<div class="logOut">
					<?php
					$count = 1;
					if ($this->ion_auth->logged_in()) {
						$user = $this->ion_auth->user()->row();
						$userName = $user->first_name;
						echo "Logg out: " . anchor('game/logout', $userName);
					}
					?>
				</div>
				<div class="clearFloat"></div>
			</div>
			<div class="genaricFormHolder">
				<input type="hidden" value="1" id="hidden_count" />
				<form method="post" action="<?php echo base_url(); ?>/index.php/">
					<div class="deck_main_enter">
						<label>Deck</label>
						<input type="text" id="deck_name"/>
					</div>
					<table border="1" style="width:100%" id="table">
						<thead>
						<th>
							Question
						</th>
						<th>
							||
						</th>
						<th>
							Question Note
						</th>
						<th>
							||
						</th>
						<th >
							Answer
						</th>
						<th>
							||
						</th>
						<th>
							Answer Note
						</th>
						<th width="20%">
							Attachments
						</th>
						<th >
							Action
						</th>
						</thead>
						<tbody>
							<tr data-count="<?= $count ?>">
								<td cell-name='question'>
									<input type='text' class="question" onBlur='getValuesOnTrOnQ(this)' />
								</td>
								<td class='center-align'>
									||
								</td>
								<td cell-name='question_note'>
									<input type='text' class="question" onBlur='getValuesOnTrOnQN(this)' />
								</td>
								<td class='center-align'>
									||
								</td>
								
								<td cell-name='answer'>
									<input type='text' class="answer" onBlur='getValuesOnTrOnA(this)' />
								</td>
								<td class='center-align'>
									||
								</td>
								<td cell-name='answer_note'>
									<input type='text' class="answer_note" onBlur='getValuesOnTrOnAN(this)' />
								</td>
								<td>
									<input class="sound_file" type='file' name="file_name_<?= $count ?>" id="file_name_<?= $count ?>"   onChange="uploadFiles(<?= $count ?>, this)" />
						<marquee class="upload_bar" style="display:none">Uploading</marquee>
						<a class="upload_link" style="display:none">Nothing To display</a>
						<div class="delete_file_button" style="float:right;display:none;cursor:pointer" onClick="deleteFile(<?= $count ?>, this)">x</div> 
						</td>
						<td>
							<button type="button" class="btn-custom3" onClick="incrementCount();
									clone(this)" style="cursor:pointer">Add</button>
							<button type="button" class="btn-danger dis" onClick="deleteThis(this)" style="cursor:pointer" disabled>Delete</button>
						</td>
						</tr>
						</tbody>
					</table>
					<div><input type='button' class='btn-classic' value='Save' style="width:100px;float:right;margin-top:5px" onClick="save()"/></div>
				</form>
			</div>
			<p><?php echo anchor('', 'Home') ?></p>
		</div>
		<script>
			function incrementCount()
			{
				var hidden_count = $("#hidden_count").val();
				hidden_count++;
				$("#hidden_count").val(hidden_count);
			}
			function uploadFiles(id, obj)
			{
				var base_url = '<?php echo base_url(); ?>';
				$(obj).hide();
				var tr = $(obj).parents('tr').first();
				//  $('.upload_bar').show();
				$(tr).find('.upload_bar').show();
				$(tr).attr('data-unchanged', 'changed');
				//   var now = new Date();
				//	var date_now =new Date();
				//	  var outStr = now.getHours()+now.getMinutes()+now.getSeconds()+date_now.getFullYear()+(date_now.getMonth()+1)+date_now.getDate()+Math.floor(Math.random()*6)+Math.floor(Math.random()*6);
				//   var outStr=outStr+date_now.getFullYear();
				//  alert(outStr);
				//  alert(date_now.getFullYear());
				//   $(obj).attr("name",outStr);
				//   $(obj).attr("id",outStr);
				//  alert(outStr);
				$.ajaxFileUpload({
					url: base_url + "/index.php/game/upload_sound",
					secureuri: false,
					fileElementId: $(tr).find('.sound_file').attr('name'),
					dataType: 'json',
					data: {
						'id': id
					},
					success: function(data, status)
					{
						if (data.status != 'error')
						{
							var message = data.msg;
							var array_msg = message.split("_-_-0909//^%*(");
							$(tr).find('.upload_bar').hide();
							$(tr).attr("data-answer_upload_file", array_msg[1]);
							$(tr).find('.upload_link').html("See mp3");
							$(tr).find('.upload_link').attr("href", base_url + "sound-files/" + array_msg[1]);
							$(tr).find(".upload_link").show();
							$(tr).find(".delete_file_button").show();
							//   
							//   $(tr).find(".upload_link").show();
						}
						else
						{
							alert(data.msg);
							$(tr).find('.upload_bar').hide();
							$(tr).find('.sound_file').show();
						}
					}
				});
				return false;
			}
			function deleteFile(count, obj)
			{
				var con = confirm("Are you sure you want to delete this?");
				if (con)
				{
					var tr = $(obj).parents('tr').first();
					var upload_file = $(tr).attr('data-answer_upload_file')
					var base_url = '<?php echo base_url(); ?>';
					$.post(base_url + "/index.php/game/deleteFile", {"upload_file": upload_file}, function(res) {
						alert(res);
						if (res == 'File deleted successfully')
						{
							$(tr).find('.upload_link').html('');
							$(tr).find('.upload_link').hide();
							$(tr).find('.delete_file_button').hide();
							$(tr).find('.sound_file').show();
							$(tr).removeAttr("data-answer_upload_file");
						}
					});
				}
			}
			function initialClone(obj)
			{
				var hidden_count = $("#hidden_count").val();
				$(obj).hide();
				$('.dis').removeAttr('disabled');
				$("#table ").find('tbody')
						.append($('<tr>')
								.attr("data-count", hidden_count)
								.append($('<td>')
										.append($('<input>')
												.attr('type', 'text')
												.attr('onBlur', 'getValuesOnTrOnQ(this)')
												)
										)
								.attr("class", "last")
								.append($('<td>')
										.text('||')
										.addClass('center-align')
										)
								.append($('<td>')
										.append($('<input>')
												.attr('type', 'text')
												.attr('onBlur', 'getValuesOnTrOnQN(this)')
												)
										)
								.attr("class", "last")
								.append($('<td>')
										.text('||')
										.addClass('center-align')
										)
								.append($('<td>')
										.append($("<input>")
												.attr('type', 'text')
												.attr('onBlur', 'getValuesOnTrOnA(this)')
												)
										)
								.attr("class", "last")
								.append($('<td>')
										.text('||')
										.addClass('center-align')
										)
								.append($('<td>')
										.append($("<input>")
												.attr('type', 'text')
												.attr('onBlur', 'getValuesOnTrOnAN(this)')
												)
										)
								.append($('<td>')
										.append($("<input>")
												.attr('type', 'file')
												.attr('class', 'sound_file')
												.attr('onChange', 'uploadFiles(' + hidden_count + ',this)')
												.attr("name", "file_name_" + hidden_count)
												.attr("id", "file_name_" + hidden_count)
												)
										.append($("<marquee>")
												.text('Uploading')
												.attr('class', 'upload_bar')
												.attr('style', 'display:none')
												)
										.append($("<a>")
												.text('Nothing To display')
												.attr('class', 'upload_link')
												.attr('style', 'display:none')
												)
										.append($("<div>")
												.text('x')
												.attr('class', 'delete_file_button')
												.attr('style', 'float:right;display:none;cursor:pointer')
												.attr('onClick', 'deleteFile(' + hidden_count + ',this)')
												)
										)
								.append($('<td>')
										.append($('<button>')
												.attr('type', 'button')
												.text('Add')
												.addClass('btn-custom3')
												.attr('onClick', 'incrementCount();clone(this)')
												.attr('style', 'cursor:pointer')
												)
										.append($('<button>')
												.attr('type', 'button')
												.text('Delete')
												.addClass('btn-danger dis')
												.attr('onClick', 'deleteThis(this)')
												.attr('style', 'cursor:pointer')
												.attr("disabled", "disabled")
												)
										)
								)
			}
			function clone(obj)
			{
				var tr = $(obj).parents(tr).first();
				//			  if($(tr).find('cell-name=question')=='')
				//				  {
				//					  alert("Add Question");
				//				  }
				initialClone(obj);
				getValuesOnTrOnQ(this);
				getValuesOnTrOnQN(this);
				getValuesOnTrOnA(this);
				getValuesOnTrOnAN(this);
			}
			function deleteThis(obj)
			{
				$(obj).closest('tr').remove();
			}
			function save()
			{
				var base_url = '<?php echo base_url(); ?>';
				var data = new Object();
				data['deck_name'] = $("#deck_name").val();
				var fields = new Array(
						"question",
						"question_note",
						"answer",
						"answer_note",
						"answer_upload_file"
						);
				data['items'] = getPostData("#table", fields).length > 0 ? getPostData("#table", fields) : null;
				$.post(base_url + "/index.php/game/add_cards",
						{"cards": data},
				function(res) {
					alert(res);
					if (res == 'Cards successfully Added')
					{
						location.reload();
					}
				});
			}
			function getPostData(tableId, fields)
			{
				var trs = $(tableId).find("tr:gt(0)");
				var data = new Array();
				$.each(trs, function(i, tr) {
					var row = new Object();
					$.each(fields, function(j, field) {
						row[field] = $(tr).attr("data-" + field);
					});
					data.push(row);
				});
				return data;
			}
		</script>
	</body>
</html>