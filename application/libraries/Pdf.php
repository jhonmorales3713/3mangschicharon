<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
class Pdf extends TCPDF{

    protected $CI;
    protected $img_file, $params;
    
    function __construct(){
        parent::__construct('p', 'pt', 'Letter', true, 'UTF-8', false);
        $this->CI =& get_instance();

        $this->img_file = K_PATH_IMAGES.'header.jpg';
        //$this->flower_line = K_PATH_IMAGES.'flower_line.png';
        //$this->params = $this->CI->session->params;
    }

    // Page header
    public function Header() {
        $x = 0;
        $y = 0;
        $w = 980;
        $h = 980;        
        
        // Header Image
        $this->Image($this->img_file, $x, $y, '', 180, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
        
    }
    // Page footer
    public function Footer() {
        $x = 35;
        $y = 440;
        $w = 980;
        $h = 980;
		
        //$img_file = K_PATH_IMAGES.'jci_footer.png';
        // Position at 15 mm from bottom
        $this->SetY(-11);
        // Set font
        $this->SetFont('helvetica', 'I', 7);
        // Page footer image
        //$this->Image($this->flower_line, $x, $y, '', 28, 'PNG', '', '', true, 70, '', false, false, 0, false, false, false);
        // Page number
        $this->MultiCell(0, 15, '(Date Generated: '.(todaytime()) . ') Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 1, 15, '', true, 0, false, true, 0, 'T', false);
            
        
    }


    public function load_pdf($title, $pages, $filename, $use_template = false, $filter = '', $orientation = 'p'){
        if($use_template == TRUE){
            $data['view']   = $pages;
            $data['title']  = $title;
            $data['filter'] = $filter;
            $pages = $this->CI->load->view('templates/template_report.php', $data, TRUE);
        }        
        $obj_pdf = new Pdf($orientation, 'pt', 'FOLIO', true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $obj_pdf->SetTitle($title);
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetFont('helvetica', '', 9);
        $obj_pdf->setFontSubsetting(false);
        $obj_pdf->SetMargins(28, 115, 28, false);
        $obj_pdf->SetFooterMargin(80);
        $obj_pdf->SetAutoPageBreak(true, 31);
        $obj_pdf->SetDisplayMode('real', 'default');

        $this->show_output($obj_pdf, $pages, $filename, $orientation);
    }    

    public function show_output($obj_pdf,$pages,$filename, $orientation = 'p'){
        if(is_array($pages)){
            foreach($pages as $page) {
                $obj_pdf->AddPage($orientation,'FOLIO');
                //solution to page size default LETTER into LEGAL
                ob_start();
                echo $page;
                $content = ob_get_contents();
                ob_end_clean();
                $obj_pdf->writeHTML($content, true, false, true, false, '');
            }
        }else{
            $obj_pdf->AddPage($orientation,'FOLIO');
            ob_start();
            echo $pages;
            $content.= ob_get_contents();
            ob_end_clean();
            $obj_pdf->writeHTML($content, true, false, true, false, '');
        }
        ob_end_clean();
        $obj_pdf->Output($filename.".pdf", 'I');
    }


    
    
    
}