<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Edit Card Decks</title>
        <meta name="viewport" content="width=device-width" />
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <meta content="utf-8" http-equiv="encoding">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
        <script src="<?php echo base_url() ?>js/ajaxfileupload.js"></script>
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
        </script>
    </head>
    <body>
        <div class="container">
            <!-- Header Section -->
            <div class="header">
                <div class="headerText">Edit Card Deck</div>
                <div class="logOut">
					<?php
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
                        <input type="text" id="deck_name" value="<?= $allCards['deck_name'] ?>"/>
                    </div>
                    <table border="1" style="width:100%" id="table">
                        <thead>
                        <th>
                            Question
                        </th>
                        <th>
                            ||
                        </th>
                        <th >
                            Answer
                        </th>
                        <th width="20%">
                            Attachments
                        </th>
                        <th>Action
                        </th>
                        </thead>
						<?php
						$i = 1;
						foreach ($allCards['complete_cards'] as $card) {
							?>
							<tr data-id='<?= $card->card_id ?>' data-unchanged='unchanged' data-question='<?= $card->question ?>' data-answer='<?= $card->answer ?>' data-action='active' <?php
							if ($card->answer_upload_file != '') {
								echo 'data-answer_upload_file="' . $card->answer_upload_file . '"';
							}
							?>>
								<td>
									<input type='text' value='<?= $card->question ?>' onChange='getValuesOnTrOnQ(this)' />
								</td>
								<td class='center-align'>
									||
								</td>
								<td >
									<input type='text' value='<?= $card->answer ?>' onChange='getValuesOnTrOnA(this)' />
								</td>
								<td>
									<?php
									if ($card->answer_upload_file != '') {
										$url = base_url();
										?>
										<a class="upload_link" href='<?php echo $url . "/sound-files/" . $card->answer_upload_file; ?>'>See Mp3</a>
										<div class="delete_file_button" style="float:right;cursor:pointer" onClick="deleteFile(<?= $card->card_id ?>, this)">x</div> 
										<input type='file' class="sound_file" name="file_name_<?= $card->card_id ?>" id="file_name_<?= $card->card_id ?>" onChange="uploadFiles(<?= $card->card_id ?>, this)" style="display:none" />
								<marquee class="upload_bar" style="display:none">Uploading</marquee>
								<!--                                <a class="upload_link" style="display:none">Nothing To display</a>-->
								<?php
							} else {
								?>
								<!--                                 <a href='<?php echo $url . "/sound-files/" . $card->answer_upload_file; ?>' style="display:none">See Mp3</a>-->
								<input type='file' class="sound_file" name="file_name_<?= $card->card_id ?>" id="file_name_<?= $card->card_id ?>" onChange="uploadFiles(<?= $card->card_id ?>, this)" />
								<marquee class="upload_bar" style="display:none">Uploading</marquee>
								<a class="upload_link" style="display:none">Nothing To display</a>
								<div class="delete_file_button" style="float:right;cursor:pointer;display:none" onClick="deleteFileOnNewUpload(<?= $card->card_id ?>, this)">x</div> 
								<?php
							}
							?>
							</td>
							<td class='buttons'>
								<button type="button" class="btn-danger dis" onClick="deleteThis(this)" style="cursor:pointer" >Delete</button>
								<button type="button" class="btn-classic dis" onClick="undoDeleteThis(this)" style="cursor:pointer;display:none" >Undo Delete</button>
							</td>
							</tr>
							<?php
						}
						?>
                    </table>
                    <div><input type='button' class='btn-classic' value='Save' style="width:100px;float:right;margin-top:5px" onClick="save()"/></div>
                </form>
            </div>
            <p><?php echo anchor('game/home', 'Home') ?></p>
			<a href="#" onclick="incrementCount();
					getNewRow()">Add New Row</a>
        </div>
        <script>
			function incrementCount()
			{
				var hidden_count = $("#hidden_count").val();
				hidden_count++;
				$("#hidden_count").val(hidden_count);
			}
			function getNewRow()
			{
				var hidden_count = $("#hidden_count").val();
				//  $(obj).hide();
				$('.dis').removeAttr('disabled');
				$("#table ").find('tbody')
						.append($('<tr>')
								.attr("data-count", hidden_count)
								.attr("data-action", "active")
								.attr("data-unchanged", "changed")
								.attr("data-question", "")
								.attr("data-answer", "")
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
										.append($("<input>")
												.attr('type', 'text')
												.attr('onBlur', 'getValuesOnTrOnA(this)')
												)
										)
								.append($('<td>')
										.append($("<input>")
												.attr('type', 'file')
												.attr('class', 'sound_file')
												.attr('onChange', 'uploadFilesOnNewRow(' + hidden_count + ',this)')
												.attr("name", hidden_count + "file_name_")
												.attr("id", hidden_count + "file_name_")
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
												.attr('onClick', 'deleteFileOnNewUpload(' + hidden_count + ',this)')
												)
										).append($('<td>')
								.append($('<button>')
										.attr('type', 'button')
										.text('Delete')
										.addClass('btn-danger dis')
										.attr('onClick', 'deleteThisNew(this)')
										.attr('style', 'cursor:pointer')
										)
								)
								)
			}
			function deleteFile(id, obj)
			{

				var con = confirm("Are you sure you want to delete this?");
				if (con)
				{
					var tr = $(obj).parents('tr').first();
					var upload_file = $(tr).attr('data-answer_upload_file')
					var base_url = '<?php echo base_url(); ?>';
					$.post(base_url + "/index.php/game/deleteFileOnEdit", {"upload_file": upload_file, "id": id}, function(res) {
						alert(res);
						if (res == 'File deleted successfully') {
							location.reload();
						}
					});
				}
			}
			function deleteFileOnNewUpload(id, obj)
			{
				var con = confirm("Are you sure you want to delete this?");
				if (con)
				{
					var tr = $(obj).parents('tr').first();
					var upload_file = $(tr).attr('data-answer_upload_file')
					var base_url = '<?php echo base_url(); ?>';
					$.post(base_url + "/index.php/game/deleteFile", {"upload_file": upload_file}, function(res) {
						alert(res);
						if (res == 'File deleted successfully') {
							$(tr).find('.upload_link').html('');
							$(tr).find('.upload_link').hide();
							$(tr).find('.delete_file_button').hide();
							$(tr).find('.sound_file').show();
							$(tr).removeAttr("data-answer_upload_file");
						}
					});
				}
			}
			function uploadFilesOnNewRow(id, obj)
			{
				var base_url = '<?php echo base_url(); ?>';
				$(obj).hide();
				var tr = $(obj).parents('tr').first();
				//  $('.upload_bar').show();
				$(tr).find('.upload_bar').show();
				$(tr).attr('data-unchanged', 'changed');
				//   var now = new Date();
				//    var date_now =new Date();
				//      var outStr = now.getHours()+now.getMinutes()+now.getSeconds()+date_now.getFullYear()+(date_now.getMonth()+1)+date_now.getDate()+Math.floor(Math.random()*6)+Math.floor(Math.random()*6);
				//   var outStr=outStr+date_now.getFullYear();
				//  alert(outStr);
				//  alert(date_now.getFullYear());
				//   $(obj).attr("name",outStr);
				//   $(obj).attr("id",outStr);
				//  alert(outStr);
				$.ajaxFileUpload({
					url: base_url + "/index.php/game/upload_sound_on_new_row_in_edit",
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
			function uploadFiles(id, obj)
			{
				var base_url = '<?php echo base_url(); ?>';
				$(obj).hide();
				var tr = $(obj).parents('tr').first();
				//  $('.upload_bar').show();
				$(tr).find('.upload_bar').show();
				$(tr).attr('data-unchanged', 'changed');
				//   var now = new Date();
				//    var date_now =new Date();
				//      var outStr = now.getHours()+now.getMinutes()+now.getSeconds()+date_now.getFullYear()+(date_now.getMonth()+1)+date_now.getDate()+Math.floor(Math.random()*6)+Math.floor(Math.random()*6);
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
			function deleteThis(obj)
			{
				$(obj).hide();
				var td = $(obj).parents('tr').first();
				$(td).find('.btn-classic').show();
				$(td).attr('data-action', 'delete');
				$(td).attr('data-unchanged', 'changed');
			}
			function deleteThisNew(obj)
			{
				$(obj).closest('tr').remove();
			}
			function undoDeleteThis(obj)
			{
				$(obj).hide();
				var td = $(obj).parents('tr').first();
				$(td).find('.btn-danger').show();
				$(td).attr('data-action', 'active');
				$(td).attr('data-unchanged', 'changed');
			}
			function save() {
				var base_url = '<?php echo base_url(); ?>';
				var data = new Object();
				data['deck_name'] = $("#deck_name").val();
				data['deck_id'] = '<?php echo $allCards['deck_id'] ?>';
				var fields = new Array(
						"id",
						"question", "answer",
						"unchanged",
						"action",
						"answer_upload_file"
						);

				data['items'] = getPostData("#table", fields).length > 0 ? getPostData("#table", fields) : null;
				$.post(base_url + "index.php/game/update_cards",
						{"cards": data},
				function(res) {
					alert(res);
					if (res == 'Cards successfully updated')
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