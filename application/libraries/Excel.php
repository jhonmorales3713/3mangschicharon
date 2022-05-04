<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* 
 *  ======================================= 
 *  Author     : Muhammad Surya Ikhsanudin 
 *  License    : Protected 
 *  Email      : mutofiyah@gmail.com 
 *   
 *  Dilarang merubah, mengganti dan mendistribusikan 
 *  ulang tanpa sepengetahuan Author 
 *  ======================================= 
 */  
// require_once APPPATH."/third_party/PHPExcel.php"; 
require_once APPPATH."libraries/phpexcel/Classes/PHPExcel.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
 
class Excel extends PHPExcel { 
	private $index = 0;
	private $alphas = 'A';
	private $info = null;
	private $foot = null;
	private $column = null;
	private $row = null;

    public function __construct() { 
        parent::__construct(); 
        $this->info = array();	
        $this->foot = array();	
    } 

    public function break_space() {
    	$this->index++;
    }

    public function insert_info($name, $value) {
        $row_num = $this->index + 1;
		$this->info['A' . $row_num] = $name;
		$this->info['B' . $row_num] = $value;
    	$this->index = $row_num;
    }

    public function insert_footer($name, $value) {
        $alphas = range('A', 'Z');
        $get_key = array_search($this->alphas, $alphas);
        $row_num = $this->index + 1;
		$this->foot[$alphas[$get_key - 1] . $row_num] = $name;
		$this->foot[$alphas[$get_key] . $row_num] = $value;
    	$this->index = $row_num;
    }

    public function set_column_header($column = array()) {
        $alphas = range('A', 'Z');
        $this->column = array();
        $row_num =  $this->index + 1;
    	$key_num = 0;
    	foreach ($column as $key => $get_column) {
    		if ($key == count($alphas) - 1) {
		    	$row_num++;
		    	$key_num = 0;
    		}
    		$this->column[$alphas[$key_num] .$row_num] = $get_column;
			$this->getActiveSheet()->getColumnDimension($alphas[$key_num])->setAutoSize(true);
			$this->getActiveSheet()->getStyle($alphas[$key_num])->getAlignment()->setWrapText(true);
			$key_num++;
    	}
    	$this->index = $row_num;
    }

    public function set_row_data($row = array()) {
        $alphas = range('A', 'Z');
        $this->row = array();
        $row_num = $this->index + 1;
    	$key_num = 0;
    	foreach ($row as $key => $get_column) {
    		foreach ($get_column as $column) {
	    		$this->row[$alphas[$key_num] .$row_num] = $column;
	    		$this->alphas = $alphas[$key_num];
		    	$this->index = $row_num;
				$key_num++;
    		}
	    	$key_num = 0;
	    	$row_num++;
    	}

    }

    public function prepare_to_export() {
    	foreach ($this->info as $key => $value) { // for header
	    	$this->getActiveSheet()->SetCellValue($key, $value);
			$this->getActiveSheet()->getStyle($key)->applyFromArray(
			    array(
			        'borders' => array(
			            'allborders' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN,
			                'color' => array('rgb' => '787878')
			            )
			        )
			    )
			);
    	}

    	foreach ($this->column as $key => $value) { // for header
	    	$this->getActiveSheet()->SetCellValue($key, $value);
	    	$this->getActiveSheet()
	    		 ->getStyle($key)
		         ->getFill()
		         ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		         ->getStartColor()
		         ->setRGB('b6b6cc');

			$this->getActiveSheet()->getStyle($key)->applyFromArray(
			    array(
			        'borders' => array(
			            'allborders' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN,
			                'color' => array('rgb' => '787878')
			            )
			        )
			    )
			);
    	}

    	foreach ($this->row as $key => $value) { // for data
	    	$this->getActiveSheet()->SetCellValue($key, $value);
	    	if (preg_replace("/[^0-9]/", "", $key) % 2 == 1) {
		    	$this->getActiveSheet()
		    		 ->getStyle($key)
			         ->getFill()
			         ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			         ->getStartColor()
			         ->setRGB('e6e6f2');
	    	}
			$this->getActiveSheet()->getStyle($key)->applyFromArray(
			    array(
			        'borders' => array(
			            'allborders' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN,
			                'color' => array('rgb' => '787878')
			            )
			        ),
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			        )
			    )
			);
			$this->getActiveSheet()->getColumnDimension($key[0])->setAutoSize(true);
    	}

    	foreach ($this->foot as $key => $value) { // for data
	    	$this->getActiveSheet()->SetCellValue($key, $value);
			$this->getActiveSheet()->getStyle($key)->applyFromArray(
			    array(
			        'borders' => array(
			            'allborders' => array(
			                'style' => PHPExcel_Style_Border::BORDER_THIN,
			                'color' => array('rgb' => '787878')
			            )
			        )
			    )
			);
		}
    }

    public function export_xlsx($filename) {
    	$this->prepare_to_export();
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=$filename.xlsx");
		header("Content-Transfer-Encoding: binary ");
		$objWriter = new PHPExcel_Writer_Excel2007($this); 
		$objWriter->setOffice2003Compatibility(true);
		$objWriter->save('php://output');
    }

    public function export_csv($filename) {
    	$this->prepare_to_export();
    	header('Content-Type: application/vnd.ms-excel'); 
        header("Content-Disposition: attachment;filename=$filename.csv");
        header('Cache-Control: max-age=0'); 
        $objWriter = PHPExcel_IOFactory::createWriter($this, 'CSV');  
        $objWriter->save('php://output'); 
    }
}