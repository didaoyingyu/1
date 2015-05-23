<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        require('js/jquery_uploader/server/php/UploadHandler.php');
    }

    public function index() {
        $upload_handler = new UploadHandler();
    }
    public function upload_medium() {
        $upload_handler = new UploadHandler_medium();
    }
    
    public function upload_thumb_128() {
        $upload_handler = new UploadHandler_thumb_128();
    }
    
    public function delete_upload(){
        
        $file = $this->input->post("file");                
        if(file_exists("assets/files/".$file)){
            unlink("assets/files/".$file);
            unlink("assets/files/thumb/".$file);
            echo TRUE;
        }
        else
            echo FALSE;
    }
}