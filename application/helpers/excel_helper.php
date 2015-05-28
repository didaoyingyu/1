<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * -----------------------------------------------------------------------------
 * helper for save or move a uploaded file in specific folder
 * -----------------------------------------------------------------------------
 */

function import_excel($excel_data) {

    $data = array();

    $file = $excel_data['file'];
    $field = $excel_data['field'];
    $file_path = $excel_data['file_path'];
    $file_ext = get_extension($excel_data['file']);

    $ci = & get_instance();

    $ci->load->library('excel');

    $reader = ($file_ext == 'xlsx') ? 'Excel2007' : 'Excel5';

    $ci->excel->load($file_path, $reader);
    $workSheet = $ci->excel->setActiveSheetIndex(0);
    $worksheetTitle = $workSheet->getTitle();
    $highestRow = $workSheet->getHighestRow(); // e.g. 10
    $highestColumn = $workSheet->getHighestColumn(); // e.g 'F'
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $nrColumns = ord($highestColumn) - 64;
    for ($row = 1; $row <= $highestRow; $row++) {
        $invalid_data = array();
        $data[$row] = array();
        for ($col = 0; $col < count($field); $col++) {
            $val = $workSheet->getCellByColumnAndRow($col, $row)->getValue();
            $data[$row][$field[$col]] = $val ? $val : '';
        }
    }

    if (is_file($file_path))
        unlink($file_path);
    return $data;
}

function get_extension($file) {
    return end(explode(".", $file));
}

/* End of file file_helper.php */
/* Location: ./system/helpers/file_helper.php */