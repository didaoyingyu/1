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
                <div class="headerText"><?php echo lang('index_heading'); ?></div>
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
            <form id="import_form">
                <a href="javascript:void(0)" id="attach_file" title="Click To Import User File"><span>Import New Users</span></a>
                <a href="<?php echo base_url('files/excelsheet_demo_files/users.xlsx') ?>" target='_blank'title="Click To Download User Demo File"><span>Download Demo File</span></a>
                <a href="javascript:void(0)" id="attachment_name" ></a>
                <input type="file" name="files" class="form-control" id="fileupload" style="visibility: hidden; height:0; padding: 0;" />
                <div class="row upload_text"></div>
                <input type="hidden" name='attachment' id="attachment" />
                <input type="hidden" name='deck_id' id="attachment" value="//<?php //echo $deck_id;   ?>"/>
            </form> 
            <div class="userIndexFormHolder">
                <table cellpadding=0 cellspacing=10>
                    <tr>
                        <th><?php echo lang('index_fname_th'); ?></th>
                        <th><?php echo lang('index_lname_th'); ?></th>
                        <th><?php echo lang('index_email_th'); ?></th>
                        <th><?php echo lang('index_groups_th'); ?></th>
                        <th><?php echo lang('index_status_th'); ?></th>
                        <th><?php echo lang('index_action_th'); ?></th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user->first_name; ?></td>
                            <td><?php echo $user->last_name; ?></td>
                            <td><?php echo $user->email; ?></td>
                            <td>
                                <?php foreach ($user->groups as $group): ?>
                                    <?php echo anchor("auth/edit_group/" . $group->id, $group->name); ?><br />
                                <?php endforeach ?>
                            </td>
                            <td><?php echo ($user->active) ? anchor("auth/deactivate/" . $user->id, lang('index_active_link')) : anchor("auth/activate/" . $user->id, lang('index_inactive_link')); ?></td>
                            <td><?php echo anchor("auth/edit_user/" . $user->id, 'Edit'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <p><?php echo anchor('auth/create_user', lang('index_create_user_link')) ?>  <?php /* echo anchor('auth/create_group', lang('index_create_group_link')) */ ?>| <?php echo anchor('', 'Admin Home') ?></p>
                <div id="infoMessage"><?php echo $message; ?>	   </div>
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
                            url: '<?php echo base_url("index.php/auth/save_import_file") ?>',
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
            });
    
            /********************Row Import Code Ends********************/
        </script>
    </body>
</html>