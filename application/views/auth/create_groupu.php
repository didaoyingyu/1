<!DOCTYPE html>
<html>
    <head>
        <title>Flash Card Game - Login</title>
        <meta name="viewport" content="width=device-width" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css">
        <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
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
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="headerText"><h3><?php echo lang('create_group_heading'); ?></h3></div></div>
            <form id="import_form">
                <a href="javascript:void(0)" id="attach_file" title="Click To Import Group File"><span>Import New Groups</span></a>
                <a href="<?php echo base_url('files/excelsheet_demo_files/groups.xlsx') ?>" target='_blank'title="Click To Download Group Demo File"><span>Download Demo File</span></a>
                <a href="javascript:void(0)" id="export_group" title="Export Groups" >Export Groups</a>
                <a href="javascript:void(0)" id="attachment_name" ></a>
                <input type="file" name="files" class="form-control" id="fileupload" style="visibility: hidden; height:0; padding: 0;" />
                <div class="row upload_text"></div>
                <input type="hidden" name='attachment' id="attachment" />
                <input type="hidden" name='deck_id' id="attachment" value="//<?php //echo $deck_id;     ?>"/>
            </form> 	
            <p><?php echo lang('create_group_subheading'); ?></p>
            <div id="infoMessage"><?php echo $message; ?></div>
            <div class="genaricFormHolder">
                <?php echo form_open("auth/create_groupu"); ?>
                <p>
                    <?php echo lang('create_group_name_label', 'group_name'); ?> <br />
                    <?php echo form_input($group_name); ?>
                </p>
                <p>
                    <?php echo lang('create_group_desc_label', 'description'); ?> <br />
                    <?php echo form_input($description); ?>
                </p>
                <p>
                    <?php //print_r($allDecks)?>
                <tr>
                    <td>Select deck: </td><br/>
                <td><select  name="deck[]"  id="deck" multiple="multiple"  style="width:870px">
                        <?php
                        foreach ($allDecks as $v) {
                            ?>
                            <option value="<?php echo $v->deck_id ?>"><?php echo $v->deck_name ?></option>
                        <?php } ?>
                    </select></td>
                </tr>
                </p>
                <p><?php echo form_submit('submit', lang('create_group_submit_btn')); ?></p>
                <p><?php echo anchor('', 'Admin Home') ?></p>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
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
                        url: '<?php echo base_url("index.php/auth/save_import_group_file") ?>',
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
            $(document).off("click", "#export_group").on('click', '#export_group', function() {
                    var ele = $(this);
                    ele.attr("disabled", "disabled");
                    $.ajax({
                        url: '<?php echo base_url("index.php/auth/export_group")?>',
                        beforeSend: function() {
                            //$('#group_table').html('<div class="loader_large"><i class="hi hi-refresh fa-spin fa-3x"></i></div>');
                        },
                        dataType: 'json',
                        success: function(result) {
                            ele.removeAttr("disabled");
                            if (result["status"] == "success") {
                                window.location.href = "<?php echo base_url("index.php/auth/export_group_file_download/"); ?>" + "/" + result["file_name"];
                            } else {
                                alert('Something Went Wrong Please refresh the page.');
                            }
                        },
                        error: function() {
                            //$("#group_table").html("Some Error! Please refresh the page.");
                        }
                    });
                });
        });
    
        /********************Row Import Code Ends********************/
    </script>
</body>
</html>