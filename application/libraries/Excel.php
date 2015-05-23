<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* 
 *  ======================================= 
 *  Author     : Jagani Raj 
 *  License    : Protected  
 *  ======================================= 
 */  
//require_once APPPATH."/third_party/PHPExcel.php"; 
 
 
class Excel { 
  	private $excel;
    public function __construct() { 
       // parent::__construct(); 
		require_once APPPATH.'third_party/PHPExcel.php';
		$this->excel = new PHPExcel();    
    } 
    public function load($path,$reader='Excel5') {

        
        $objReader = PHPExcel_IOFactory::createReader($reader);
        $this->excel = $objReader->load($path);
    }

    public function save($path) {
        // Write out as the new file
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save($path);
    }

    public function stream($filename) {       
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=\"".$filename."\""); 
        header("Cache-control: private");        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');    
    }

    public function  __call($name, $arguments) {  
        // make sure our child object has this method  
        if(method_exists($this->excel, $name)) {  
            // forward the call to our child object  
            return call_user_func_array(array($this->excel, $name), $arguments);  
        }  
        return null;  
    }  
}