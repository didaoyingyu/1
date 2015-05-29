<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Edit Card Decks</title>
        <meta name="viewport" content="width=device-width" />
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <meta content="utf-8" http-equiv="encoding">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
        <script>
            var config = {
                base: "<?php echo base_url(); ?>"
            };
        </script>
        <style>
            .container{
                width:100%;
            }
        </style>
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
        <script src="<?php echo base_url() ?>js/ajaxfileupload.js"></script>
        <script src="<?php echo base_url() ?>js/recordmp3.js"></script>
        <script src="<?php echo base_url() ?>js/recorderWorker.js"></script>
        <script src="<?php echo base_url() ?>js/mp3Worker.js"></script>
        <!-- uploader -->
        <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/vendor/jquery.ui.widget.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/load-image.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/canvas-to-blob.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.blueimp-gallery.min.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.iframe-transport.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload-process.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload-image.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload-audio.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload-video.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload-validate.js'); ?>"></script>
        <script src="<?php echo base_url('js/jquery_uploader/js/jquery.fileupload-ui.js'); ?>"></script>

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
                var quea = $(obj).val();
                var tr = $(obj).parents('tr').first();
                $(tr).attr("data-question_note", quea);
                $(tr).attr("data-unchanged", "changed");
            }
            function getValuesOnTrOnAN(obj)
            {
                var ansa = $(obj).val();
                var tr = $(obj).parents('tr').first();
                $(tr).attr("data-answer_note", ansa);
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
                        <th width="30%">
                            Question  & Notes
                        </th>

                        <th>
                            Slow 
                        </th>
                        <th>
                            Fast 
                        </th>
                        <th  width="30%">
                            Answer & Notes
                        </th>
                        <th >
                            Slow 
                        </th>
                        <th >
                            Fast 
                        </th>
                        <th>Action
                        </th>
                        </thead>
                        <tbody id="tbody">
                            <?php
                            $i = 1;
                            foreach ($allCards['complete_cards'] as $card) {
                                ?>
                                <tr data-id='<?= $card->card_id ?>' data-unchanged='unchanged' data-question='<?= $card->question ?>' data-answer='<?= $card->answer ?>' data-question_note='<?= $card->question_note ?>' data-answer_note='<?= $card->answer_note ?>' data-action='active'

                                    <?php
                                    if ($card->answer_upload_file_slow != '') {
                                        echo 'data-answer_upload_file_slow ="' . $card->answer_upload_file_slow . '"';
                                    }
                                    if ($card->question_upload_file_slow != '') {
                                        echo 'data-question_upload_file_slow="' . $card->question_upload_file_slow . '"';
                                    }
                                    if ($card->answer_upload_file != '') {
                                        echo 'data-answer_upload_file="' . $card->answer_upload_file . '"';
                                    }
                                    if ($card->question_upload_file != '') {
                                        echo 'data-question_upload_file="' . $card->question_upload_file . '"';
                                    }
                                    ?> >
                                    <td valign="top">
                                        <div style="border:0px solid;width:100%;">
                                            <span style="float:left;width:12%;" >
                                                Q
                                            </span>
                                            <span style="float:left;width:87%;" >
                                                <textarea cols="10" style="width: 97%; height: 40px;" onChange='getValuesOnTrOnQ(this)' rows="4"><?= htmlspecialchars($card->question, ENT_QUOTES) ?></textarea>
                                            </span>
                                        </div>
                                        <div style="border:0px solid;width:100%;">
                                            <span style="float:left;width:12%;" >
                                                N
                                            </span>
                                            <span style="float:left;width:87%;" >
                                                <textarea cols="10"  style="width: 97%; height: 60px;" onChange='getValuesOnTrOnQN(this)'  rows="4"><?= htmlspecialchars($card->question_note, ENT_QUOTES) ?></textarea>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        if ($card->question_upload_file_slow != '') {
                                            $url = base_url();
                                            ?>
                                            <button onclick="startRecording(this,'slow_q');">Rec</button>
                                            <button class="sound_file slow_q" style="display:none;" name="slow_q_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'slow_q');" id="slow_q_file_name_<?= $card->card_id ?>" disabled>Stop</button>
                                            <button class="upload_link slow_q" name="<?php echo $url . "/sound-files/" . $card->question_upload_file_slow; ?>">Listen</button>
                                            <div class="delete_file_button slow_q" style="float:right;cursor:pointer" onClick="deleteFile(<?= $card->card_id ?>, this, 'slow_q')">x</div> 
                                            <span class="upload_bar slow_q" style="display:none">Encoding...</span>
                                            <!--								<a class="upload_link" style="display:none">Nothing To display</a>-->
                                            <?php
                                        } else {
                                            ?>
                                                    <!--<a href='<?php echo $url . "/sound-files/" . $card->answer_upload_file_slow; ?>' style="display:none">See Mp3</a>-->
                                            <button onclick="startRecording(this, 'slow_q');">Rec</button>
                                            <button class="sound_file slow_q" name="slow_q_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'slow_q');" id="slow_q_file_name_<?= $card->card_id ?>" disabled>Stop</button>
                                            <span class="upload_bar slow_q" style="display:none">Encoding...</span>
                                            <button class="upload_link slow_q" style="display:none">Nothing To display</button>
                                            <div class="delete_file_button slow_q" style="float:right;cursor:pointer;display:none" onClick="deleteFileOnNewUpload(<?= $card->card_id ?>, this, 'slow_q')">x</div> 
                                            <?php
                                        }
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        if ($card->question_upload_file != '') {
                                            $url = base_url();
                                            ?>
                                            <button onclick="startRecording(this,'q');">Rec</button>
                                            <button class="sound_file q" style="display:none;" name="q_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'q');" id="q_file_name_<?= $card->card_id ?>" disabled>Stop</button>
                                            <button class="upload_link q" name="<?php echo $url . "/sound-files/" . $card->question_upload_file; ?>">Listen</button>
                                            <div class="delete_file_button q" style="float:right;cursor:pointer" onClick="deleteFile(<?= $card->card_id ?>, this, 'q')">x</div> 
                                            <span class="upload_bar q" style="display:none">Encoding...</span>
                                            <!--<a class="upload_link" style="display:none">Nothing To display</a>-->
                                            <?php
                                        } else {
                                            ?>
                                                    <!--<a href='<?php echo $url . "/sound-files/" . $card->answer_upload_file; ?>' style="display:none">See Mp3</a>-->
                                            <button onclick="startRecording(this, 'q');">Rec</button>
                                            <button class="sound_file q" name="q_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'q');" id="q_file_name_<?= $card->card_id ?>" disabled>Stop</button>
                                            <span class="upload_bar q" style="display:none">Encoding...</span>
                                            <button class="upload_link q" style="display:none">Nothing To display</button>
                                            <div class="delete_file_button q" style="float:right;cursor:pointer;display:none" onClick="deleteFileOnNewUpload(<?= $card->card_id ?>, this, 'q')">x</div> 
                                            <?php
                                        }
                                        ?>
                                    </td>		

                                    <td>
                                        <div style="border:0px solid;width:100%;">
                                            <span style="float:left;width:12%;" >
                                                A
                                            </span>
                                            <span style="float:left;width:87%;" >
                                                <textarea cols="10" style="width: 97%; height: 40px;" onChange='getValuesOnTrOnA(this)' rows="4"><?= htmlspecialchars($card->answer, ENT_QUOTES) ?></textarea>
                                            </span>
                                        </div>
                                        <div style="border:0px solid;width:100%;">
                                            <span style="float:left;width:12%;" >
                                                N
                                            </span>
                                            <span style="float:left;width:87%;" >
                                                <textarea cols="10"  style="width: 97%; height: 60px;" onChange='getValuesOnTrOnAN(this)'  rows="4"><?= htmlspecialchars($card->answer_note, ENT_QUOTES) ?></textarea>
                                            </span>
                                        </div>

                                    </td>
                                    <td>
                                        <?php
                                        if ($card->answer_upload_file_slow != '') {
                                            $url = base_url();
                                            ?>
                                            <button onclick="startRecording(this,'slow_a');">Rec</button>
                                            <button class="sound_file slow_a" style="display:none;" name="slow_a_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'slow_a');" id="slow_a_file_name_<?= $card->card_id ?>" disabled>Stop</button>

                                            <button class="upload_link slow_a" name="<?php echo $url . "/sound-files/" . $card->answer_upload_file_slow; ?>">Listen</button>
                                            <div class="delete_file_button slow_a" style="float:right;cursor:pointer" onClick="deleteFile(<?= $card->card_id ?>, this, 'slow_a')">x</div> 
                                            <span class="upload_bar slow_a" style="display:none">Encoding...</span>
                                            <!--<a class="upload_link" style="display:none">Nothing To display</a>-->
                                            <?php
                                        } else {
                                            ?>
                                            <!--<a href='<?php echo $url . "/sound-files/" . $card->answer_upload_file_slow; ?>' style="display:none">See Mp3</a>-->
                                            <button onclick="startRecording(this, 'slow_a');">Rec</button>
                                            <button class="sound_file slow_a" name="slow_a_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'slow_a');" id="slow_a_file_name_<?= $card->card_id ?>" disabled>Stop</button>

                                            <span class="upload_bar slow_a" style="display:none">Encoding...</span>
                                            <button class="upload_link slow_a" style="display:none">Nothing To display</button>
                                            <div class="delete_file_button slow_a" style="float:right;cursor:pointer;display:none" onClick="deleteFileOnNewUpload(<?= $card->card_id ?>, this, 'slow_a')">x</div>
                                            <?php
                                        }
                                        ?>
                                    </td>


                                    <td>
                                        <?php
                                        if ($card->answer_upload_file != '') {
                                            $url = base_url();
                                            ?>
                                            <button onclick="startRecording(this,'a');">Rec</button>
                                            <button class="sound_file a" style="display:none;" name="a_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'a');" id="a_file_name_<?= $card->card_id ?>" disabled>Stop</button>
                                            <button class="upload_link a" name="<?php echo $url . "/sound-files/" . $card->answer_upload_file; ?>">Listen</button>
                                            <div class="delete_file_button a" style="float:right;cursor:pointer" onClick="deleteFile(<?= $card->card_id ?>, this, 'a')">x</div> 
                                            <span class="upload_bar a" style="display:none">Encoding...</span>
                                            <!--								<a class="upload_link" style="display:none">Nothing To display</a>-->
                                            <?php
                                        } else {
                                            ?>
                                            <!--<a href='<?php echo $url . "/sound-files/" . $card->answer_upload_file; ?>' style="display:none">See Mp3</a>-->
                                            <button onclick="startRecording(this, 'a');">Rec</button>
                                            <button class="sound_file a" name="a_file_name_<?= $card->card_id ?>" onclick="stopRecording(this, <?= $card->card_id ?>, 'a');" id="a_file_name_<?= $card->card_id ?>" disabled>Stop</button>

                                            <span class="upload_bar a" style="display:none">Encoding...</span>
                                            <button class="upload_link a" style="display:none">Nothing To display</button>
                                            <div class="delete_file_button a" style="float:right;cursor:pointer;display:none" onClick="deleteFileOnNewUpload(<?= $card->card_id ?>, this, 'a')">x</div>
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
                        </tbody>
                    </table>
                    <div><input type='button' class='btn-classic' value='Save' style="width:100px;float:right;margin-top:5px" onClick="save()"/></div>
                </form>
            </div>
            <p><?php echo anchor('', 'Home') ?></p>
            <a href="#" onclick="incrementCount();
                   getNewRow()">Add New Row</a><br /><br />
            <form id="import_form">
                <a href="javascript:void(0)" id="attach_file" ><span>Import New Row</span></a><br /a>
                    <a href="javascript:void(0)" id="attachment_name" ></a>
                <input type="file" name="files" id="fileupload" style="visibility: hidden; height:0; padding: 0;" /><br />
                <div class="row upload_text"></div>
                <input type="hidden" name='attachment' id="attachment" />
                <input type="hidden" name='deck_id' id="attachment" value="<?php echo $deck_id; ?>"/>
            </form>
        </div>
        <script>
            var audio_context;
            var recorder;
            
            function startUserMedia(stream) {
                var input = audio_context.createMediaStreamSource(stream);
                console.log('Media stream created.' );
                console.log("input sample rate " +input.context.sampleRate);
                
                //input.connect(audio_context.destination);
                //console.log('Input connected to audio context destination.');
                
                recorder = new Recorder(input);
                console.log('Recorder initialised.');
            }
            
            $(document).ready(function(){
                audio = document.createElement('audio');
                $('button').click(function(e){
                    e.preventDefault();
                    if ($(this).hasClass('upload_link')){
                        var button_name = $(this).attr('name');
                        if (audio.paused){
                            audio.setAttribute('src', button_name);
                            audio.play();
                        }
                        else if (!audio.paused && button_name == $(audio).attr('src')){
                            audio.pause();
                            audio.currentTime = 0;
                        }
                        else if (!audio.paused && button_name != $(audio).attr('src')){
                            audio.pause();
                            setTimeout(function(){
                                audio.setAttribute('src', button_name);
                                audio.play();
                            }, 100);
                        }
                    }
                });
                audio.addEventListener('pause', button_paused);
                audio.addEventListener('play', button_unpaused);
                function button_paused(){
                    $('button[name ="' + $(audio).attr('src') + '"]').html('Listen');
                }
                function button_unpaused(){
                    $('button[name ="' + $(audio).attr('src') + '"]').html('Listen');
                }
            });
            
            function startRecording(button,type) {
                recorder && recorder.record();
                button.disabled = true;
                var tr = $(button).parents('tr').first();
                $(tr).find('.sound_file.'+type).show();
                $(tr).find('.upload_link.'+type).hide();
                $(tr).find('.delete_file_button.'+type).hide();
                
                button.nextElementSibling.disabled = false;
                console.log('Recording...');
            }			
            
            function stopRecording(button, id, type) {
                recorder && recorder.stop();
                button.disabled = true;
                button.previousElementSibling.disabled = false;
                console.log('Stopped recording.');
                var base_url = '<?php echo base_url(); ?>';
                obj = "#"+$(button).attr("id");
                var tr = $(obj).parents('tr').first();
                $(tr).find('.sound_file.'+type).hide();
                $(tr).find('.upload_bar.'+type).show();
                $(tr).attr('data-unchanged', 'changed');
                createDownloadLink($(button).prop('outerHTML'), id, type);
                //createDownloadLinkSlow($(button).prop('outerHTML'), id, "slow_"+type);
                recorder.clear();
            }
            
            function createDownloadLink(obj, id, type) {
                recorder && recorder.exportWAV(function(blob){},'',obj,id,type);
            }
            window.onload = function init() {
                try {
                    // webkit shim
                    window.AudioContext = window.AudioContext || window.webkitAudioContext;
                    navigator.getUserMedia = ( navigator.getUserMedia ||
                        navigator.webkitGetUserMedia ||
                        navigator.mozGetUserMedia ||
                        navigator.msGetUserMedia);
                    window.URL = window.URL || window.webkitURL;
                    
                    audio_context = new AudioContext;
                    console.log('Audio context set up.');
                    console.log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
                } catch (e) {
                    
                }
                
                navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
                    console.log('No live audio input: ' + e);
                });
            };
            
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
                    $("#table ").find('#tbody')
                    .prepend($('<tr>')
                    .attr("data-count", hidden_count)
                    .attr("data-action", "active")
                    .attr("data-unchanged", "changed")
                    .attr("data-question", "")
                    .attr("data-question_note", "")
                    .attr("data-answer", "")
                    .attr("data-answer_note", "")
                    /*
                <div style="border:0px solid;width:100%;">
                                <span style="float:left;width:12%;" >
                                N
                                </span>
                                <span style="float:left;width:87%;" >
                                <textarea cols="10"  style="width: 97%; height: 60px;" 
                     *onChange='getValuesOnTrOnAN(this)'  rows="4">
            <?php //htmlspecialchars($card->answer_note, ENT_QUOTES)  ?></textarea>
                                </span>
                </div>
                     */	
                    .append($('<td>')
                    
                    .append($('<div style="border:0px solid;width:100%;">')
                    
                    .append($('<span style="float:left;width:12%;" >') .text('Q') )
                    .append($('<span style="float:left;width:87%;" >')
                    .append($('<textarea  style="width: 97%; height: 40px;" >')
                    .attr('onBlur', 'getValuesOnTrOnQ(this)')
                )
                )
                )
                    
                    .append($('<div style="border:0px solid;width:100%;">')
                    
                    .append($('<span style="float:left;width:12%;" >')
                    .text('N')
                )
                    .append($('<span style="float:left;width:87%;" >')
                    .append($('<textarea  style="width: 97%; height: 40px;" >')
                    .attr('onBlur', 'getValuesOnTrOnQN(this)')
                )
                )
                )
                    
                )
                    
                    .append($('<td>')
                    .append($("<button>")
                    .html('Rec')
                    .attr('onClick', 'startRecording(this, "slow_q");')
                )
                    
                    .append($("<button>")
                    .html("Stop")
                    .attr("style", "margin-left:5px;")
                    .attr("class", "sound_file slow_q")
                    .attr("name", "slow_q_file_name_" + hidden_count)
                    .attr("onClick", "stopRecording(this, " + hidden_count + ", 'slow_q');")
                    .attr("id", "slow_q_file_name_" + hidden_count)
                    .prop('disabled', true)
                )
                    .append($("<span>")
                    .text('Encoding...')
                    .attr('class', 'upload_bar slow_q')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<button>")
                    .text('Nothing To display')
                    .attr('class', 'upload_link slow_q')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<div>")
                    .text('x')
                    .attr('class', 'delete_file_button slow_q')
                    .attr('style', 'float:right;display:none;cursor:pointer')
                    .attr('onClick', 'deleteFileOnNewUpload(' + hidden_count + ',this, "slow_q")')
                )
                )
                    .append($('<td>')
                    .append($("<button>")
                    .html('Rec')
                    .attr('onClick', 'startRecording(this, "q");')
                )
                    .append($("<button>")
                    .html("Stop")
                    .attr("style", "margin-left:5px;")
                    .attr("class", "sound_file q")
                    .attr("name", "q_file_name_" + hidden_count)
                    .attr("onClick", "stopRecording(this, " + hidden_count + ", 'q');")
                    .attr("id", "q_file_name_" + hidden_count)
                    .prop('disabled', true)
                )
                    .append($("<span>")
                    .text('Encoding...')
                    .attr('class', 'upload_bar q')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<button>")
                    .text('Nothing To display')
                    .attr('class', 'upload_link q')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<div>")
                    .text('x')
                    .attr('class', 'delete_file_button q')
                    .attr('style', 'float:right;display:none;cursor:pointer')
                    .attr('onClick', 'deleteFileOnNewUpload(' + hidden_count + ',this, "q")')
                )
                )
                    .append($('<td>')
                    .append($('<div style="border:0px solid;width:100%;">')
                    
                    .append($('<span style="float:left;width:12%;" >')
                    .text('A')
                )
                    .append($('<span style="float:left;width:87%;" >')
                    .append($('<textarea  style="width: 97%; height: 40px;" >')
                    .attr('onBlur', 'getValuesOnTrOnA(this)')
                )
                )
                )
                    
                    .append($('<div style="border:0px solid;width:100%;">')
                    
                    .append($('<span style="float:left;width:12%;" >')
                    .text('N')
                )
                    .append($('<span style="float:left;width:87%;" >')
                    .append($('<textarea  style="width: 97%; height: 40px;" >')
                    .attr('onBlur', 'getValuesOnTrOnAN(this)')
                )
                )
                )
                    
                    
                )
                    .append($('<td>')
                    .append($("<button>")
                    .html('Rec')
                    .attr('onClick', 'startRecording(this, "slow_a");')
                )
                    .append($("<button>")
                    .html("Stop")
                    .attr("style", "margin-left:5px;")
                    .attr("class", "sound_file slow_a")
                    .attr("name", "slow_a_file_name_" + hidden_count)
                    .attr("onClick", "stopRecording(this, " + hidden_count + ", 'slow_a');")
                    .attr("id", "slow_a_file_name_" + hidden_count)
                    .prop('disabled', true)
                )
                    .append($("<span>")
                    .text('Encoding...')
                    .attr('class', 'upload_bar slow_a')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<button>")
                    .text('Nothing To display')
                    .attr('class', 'upload_link slow_a')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<div>")
                    .text('x')
                    .attr('class', 'delete_file_button slow_a')
                    .attr('style', 'float:right;display:none;cursor:pointer')
                    .attr('onClick', 'deleteFileOnNewUpload(' + hidden_count + ',this, "slow_a")')
                )
                )
                    .append($('<td>')
                    .append($("<button>")
                    .html('Rec')
                    .attr('onClick', 'startRecording(this, "a");')
                )
                    .append($("<button>")
                    .html("Stop")
                    .attr("style", "margin-left:5px;")
                    .attr("class", "sound_file a")
                    .attr("name", "a_file_name_" + hidden_count)
                    .attr("onClick", "stopRecording(this, " + hidden_count + ", 'a');")
                    .attr("id", "a_file_name_" + hidden_count)
                    .prop('disabled', true)
                )
                    .append($("<span>")
                    .text('Encoding...')
                    .attr('class', 'upload_bar a')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<button>")
                    .text('Nothing To display')
                    .attr('class', 'upload_link a')
                    .attr('style', 'display:none;margin-left:5px;')
                )
                    .append($("<div>")
                    .text('x')
                    .attr('class', 'delete_file_button a')
                    .attr('style', 'float:right;display:none;cursor:pointer')
                    .attr('onClick', 'deleteFileOnNewUpload(' + hidden_count + ',this, "a")')
                )
                )
                    
                    .append($('<td>')
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
    
    
    function deleteFile(id, obj, type)
    {
        var con = confirm("Are you sure you want to delete this?");
        if (con)
        {
            var tr = $(obj).parents('tr').first();
            var upload_file = "";
            if (type == "a")
            {
                upload_file = $(tr).attr('data-answer_upload_file');
            }
            else if (type == 'slow_a')
            {
                upload_file = $(tr).attr('data-answer_upload_file_slow');
            }
            else if (type == 'slow_q')
            {
                upload_file = $(tr).attr('data-question_upload_file_slow');
            }
            else
            {
                upload_file = $(tr).attr('data-question_upload_file');
            }
            
            
            var base_url = '<?php echo base_url(); ?>';
            $.post(base_url + "/index.php/game/deleteFileOnEdit", {"upload_file": upload_file, "id": id, "type":type}, function(res) {
                alert(res);
                if (res == 'File deleted successfully') {
                    $(tr).find('.upload_link.'+type).html('');
                    $(tr).find('.upload_link.'+type).hide();
                    $(tr).find('.delete_file_button.'+type).hide();
                    $(tr).find('.sound_file.'+type).show();
                    
                    if (type == "a")
                    {
                        $(tr).removeAttr("data-answer_upload_file");
                    }
                    else if (type == 'slow_a')
                    {
                        $(tr).removeAttr("data-answer_upload_file_slow");
                    }
                    else if (type == 'slow_q')
                    {
                        $(tr).removeAttr("data-question_upload_file_slow");
                    }
                    else
                    {
                        $(tr).removeAttr("data-question_upload_file");
                    }
                    
                }
            });
            
            
        }
    }
    function deleteFileOnNewUpload(id, obj, type)
    {
        var con = confirm("Are you sure you want to delete this?");
        if (con)
        {
            var tr = $(obj).parents('tr').first();
            var upload_file = "";
            if (type == "a")
            {
                upload_file = $(tr).attr('data-answer_upload_file');
            }
            else if (type == 'slow_a')
            {
                upload_file = $(tr).attr('data-answer_upload_file_slow');
            }
            else if (type == 'slow_q')
            {
                upload_file = $(tr).attr('data-question_upload_file_slow');
            }
            else
            {
                upload_file = $(tr).attr('data-question_upload_file');
            }
            
            
            var base_url = '<?php echo base_url(); ?>';
            $.post(base_url + "/index.php/game/deleteFile", {"upload_file": upload_file}, function(res) {
                alert(res);
                if (res == 'File deleted successfully') {
                    $(tr).find('.upload_link.'+type).html('');
                    $(tr).find('.upload_link.'+type).hide();
                    $(tr).find('.delete_file_button.'+type).hide();
                    $(tr).find('.sound_file.'+type).show();
                    if (type == "a")
                    {
                        $(tr).removeAttr("data-answer_upload_file");
                    }
                    else if (type == 'slow_a')
                    {
                        $(tr).removeAttr("data-answer_upload_file_slow");
                    }
                    else if (type == 'slow_q')
                    {
                        $(tr).removeAttr("data-question_upload_file_slow");
                    }
                    else
                    {
                        $(tr).removeAttr("data-question_upload_file");
                    }
                }
            });
            
        }
    }
    function uploadFiles(id, obj, type, data)
    {
        //                                        console.log("type "+type)
        obj = "#"+$(obj).attr("id");
        var base_url = '<?php echo base_url(); ?>';
        var tr = $(obj).parents('tr').first();
        $.ajax({
            url: config.base + "/index.php/game/upload_sound",
            dataType: 'json',
            type: 'POST',
            data: {
                'id': id,
                'type': type,
                'data': data
            },
            success: function(data, status)
            {
                if (data.status != 'error')
                {
                    var message = data.msg;
                    var array_msg = message.split("_-_-0909//^%*(");
                    $(tr).find('.upload_bar.'+type).hide();
                    if (type == "a")
                    {
                        $(tr).attr("data-answer_upload_file", array_msg[1]);
                    }
                    else if (type == 'slow_a')
                    {
                        $(tr).attr("data-answer_upload_file_slow", array_msg[1]);
                    }
                    else if (type == 'slow_q')
                    {
                        $(tr).attr("data-question_upload_file_slow", array_msg[1]);
                    }
                    else
                    {
                        $(tr).attr("data-question_upload_file", array_msg[1]);
                    }
                    
                    $(tr).find('.upload_link.'+type).html("Listen");
                    $(tr).find('.upload_link.'+type).attr("name", base_url + "sound-files/" + array_msg[1]);
                    $(tr).find(".upload_link."+type).show();
                    $(tr).find(".delete_file_button."+type).show();
                }
                else
                {
                    alert(data.msg);
                    $(tr).find('.upload_bar.'+type).hide();
                    $(tr).find('.sound_file.'+type).show();
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
        "question",
        "question_note",
        "answer",
        "answer_note",
        "unchanged",
        "action",
        "question_upload_file",
        "question_upload_file_slow",
        "answer_upload_file",
        "answer_upload_file_slow"
    );
        data['items'] = getPostData("#table", fields).length > 0 ? getPostData("#table", fields) : null;
        
        
        $.post(base_url + "index.php/game/update_cards",
        {"cards": data},
        function(res) {
            alert(res);
            //alert(JSON.stringify(data))
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
    /********************Row Import Code Starts********************/
    var i = 0, a_ele;
    
    // Click on attach file link.
    $("#attach_file").click(function(e) {
        //e.preventDefault();
        $("#fileupload").click();
    });
    // Initialize file Uploader
    $('#fileupload').fileupload({
        url: '<?php echo base_url('index.php/upload') ?>',
        autoUpload: true,
        acceptFileTypes: /(\.|\/)(xlsx?|csv)$/i,
        maxFileSize: 30 * 1024 * 1024,
        processfail: function(e, data) {
            alert(data.files[data.index].name + "\n" + data.files[data.index].error);
        },
        submit: function(e, data) {
            $("#attachment_name").html(data.files[0].name);
        },
        done: function(e, data) {
            $("#attachment").val(data._response.result.files[0].name);
            $.ajax({
                url: '<?php echo base_url("index.php/game/save_import_file") ?>',
                data: $("#import_form").serialize(),
                type: "post",
                dataType: "json",
                beforeSend: function() {
                    
                },
                success: function(result) {
                    location.reload();
                    $("#attachment").val("");
                    element.closest(".upload_text").find(".attachment_box_wrapper").remove();
                },
                error: function() {
                    
                }
            });
        },
        fail: function(e, data) {
            console.log(data);
        }
    });
    
    /********************Row Import Code Ends********************/
        </script>
    </body>
</html>
