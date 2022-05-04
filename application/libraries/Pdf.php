<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';
class Pdf extends TCPDF{

    protected $CI;
    protected $img_file, $params;
    
    function __construct(){
        parent::__construct('p', 'pt', 'A4', true, 'UTF-8', false);
        $this->CI =& get_instance();

        $this->img_file = K_PATH_IMAGES.'jci_header.png';
        //$this->params = $this->CI->session->params;
    }

    // Page header
    public function Header() {
        $x = 0;
        $y = 0;
        $w = 980;
        $h = 980;        
        
        // Header Image
        $this->Image($this->img_file, $x, $y, '', 165, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        
    }
    // Page footer
    public function Footer() {
        $x = 0;
        $y = 775;
        $w = 980;
        $h = 800;
		
        $img_file = K_PATH_IMAGES.'jci_footer.png';
        // Position at 15 mm from bottom
        $this->SetY(-11);
        // Set font
        $this->SetFont('helvetica', 'I', 7);
        // Page footer image
        $this->Image($img_file, $x, $y, '', 165, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        // Page number
        $this->MultiCell(0, 15, '(Date Generated: '.format_shortdatetime(todaytime()) . ') Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 1, 15, '', true, 0, false, true, 0, 'T', false);
            
        
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
        $obj_pdf->Output($filename.".pdf", 'I');
    }


    //landscape header
    public function landscape_header(){
        $x = 163;
        $y = 12;
        $w = 590;
        $h = 980;
        
        $this->Image($this->img_file, $x+50, $y+15, '', 85, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
    }
    //landscape footer
    public function landscape_footer(){
        $x = 12;
        $y = 12;
        $w = 590;
        $h = 980;
        // Position at 15 mm from bottom
        $this->SetY(-20);
        // Set font
        $this->SetFont('helvetica', 'I', 7);
        // Page number
        $this->MultiCell(0, 15, '(Date Generated: '.format_shortdatetime(todaytime()) . ') Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 1, 15, '', true, 0, false, true, 0, 'T', false);
    }


    // clearance_certificate
    public function header_clearance_certificate(){
        $x = 12;
        $y = 12;
        $w = 587; //letter > 569 , legal > 587 , 8.5 x 13 > 587
        $h = 910; //letter > 820 , legal > 984 , 8.5 x 13 > 910
        
        $array = ['width' => 2.3, 'cap' => 'square', 'join' => 'miter', 'dash' => 0];
        $green = array(116, 178, 125);
        $blue  = array(107, 129, 175);
        $green_merged = array_merge($array, ['color' => $green]);
        $blue_merged  = array_merge($array, ['color' => $blue]);
        $green_border = array(
                'L' => $green_merged,
                'T' => $green_merged,
                'R' => $green_merged,
                'B' => $green_merged
            );
        $blue_border = array(
                'L' => $blue_merged,
                'T' => $blue_merged,
                'R' => $blue_merged,
                'B' => $blue_merged
            );

        $this->Rect($x, $y, $w, $h, '', $green_border, $green);
        $this->Rect($x+4, $y+4, $w-8, $h-8, '', $blue_border, $blue);
        $this->Rect($x+8, $y+8, $w-16, $h-16, '', $green_border, $green);
        $this->Image($this->img_file, $x+40, $y+15, '', 85, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
    }
    public function footer_clearance_certificate(){
        $x = 12;
        $y = 12;
        $w = 587; //letter > 569 , legal > 587 , 8.5 x 13 > 587
        $h = 910; //letter > 820 , legal > 984 , 8.5 x 13 > 910
        
        if(isset($this->params['qr_code'])){
            $this->Image($this->params['qr_code'], $w-48, $h-876, 40, 40, 'png'); //letter > h = 790 , legal > 952 , 8.5 x 13 > 876
        }

        $html = "**This document is electronically generated**";
        $this->SetXY($x+14, $h-6);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0,0,$html,0,1,'L',0);// space

        // $this->SetXY($w-$x,$h-6);
        // $html = utf8_decode($this->params['ctrl_number']);
        // $this->Cell(0,0,$html,0,1,'R',0);// space
    }
    public function load_clearance_certificate($title, $pages, $filename){
        $obj_pdf = new Pdf('p', 'pt', 'LEGAL', true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $obj_pdf->SetTitle($title);
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetFont('helvetica', '', 10);
        $obj_pdf->setFontSubsetting(false);
        $tagsVS = array(
            'p' => [
                array('n'=>1,'h'=>'1'), 
                array('n'=>1,'h'=>'1')
                ]
            );
        $obj_pdf->setHtmlVSpace($tagsVS);
        $obj_pdf->SetMargins(36, 115, 36, false);
        $obj_pdf->SetFooterMargin(52);
        $obj_pdf->SetAutoPageBreak(true, 52);
        $obj_pdf->SetDisplayMode('real', 'default');
        $obj_pdf->setCellHeightRatio(1.1);

        $this->show_output($obj_pdf, $pages, $filename);
    }  
    
    public function load_evaluation_report($title, $pages, $filename){
        $obj_pdf = new Pdf('p', 'pt', 'LEGAL', true, 'UTF-8', false);
        $obj_pdf->SetCreator(PDF_CREATOR);
        $obj_pdf->SetTitle($title);
        $obj_pdf->SetDefaultMonospacedFont('helvetica');
        $obj_pdf->SetFont('helvetica', '', 10);
        $obj_pdf->setFontSubsetting(false);
        $tagsVS = array(
            'p' => [
                array('n'=>1,'h'=>'1'), 
                array('n'=>1,'h'=>'1')
                ]
            );
        $obj_pdf->setHtmlVSpace($tagsVS);
        $obj_pdf->SetMargins(54, 115, 64, false);
        $obj_pdf->SetFooterMargin(52);
        $obj_pdf->SetAutoPageBreak(true, 52);
        $obj_pdf->SetDisplayMode('real', 'default');
        $obj_pdf->setCellHeightRatio(1.1);

        $this->show_output($obj_pdf, $pages, $filename);
    }  


    // transmittal letter
    public function load_transmittal_letter($title, $pages, $filename){
        $obj_pdf = new Pdf();
        $obj_pdf->SetCreator(PDF_CREATOR);
        $obj_pdf->SetTitle($title);
        // $obj_pdf->SetDefaultMonospacedFont('helvetica');
        // $obj_pdf->SetFont('helvetica', '', 10);
        // $obj_pdf->setFontSubsetting(false);
        $tagsVS = array(
            'p' => [
                array('n'=>1,'h'=>'1'), 
                array('n'=>1,'h'=>'1')
                ]
            );
        $obj_pdf->setHtmlVSpace($tagsVS);
        $obj_pdf->SetMargins(54, 115, 54, false);
        $obj_pdf->SetFooterMargin(50);
        $obj_pdf->SetAutoPageBreak(true, 85);
        // $obj_pdf->SetDisplayMode('real', 'default');

        $this->show_output($obj_pdf,$pages,$filename);        
    }
    
    
}