<?php
require_once(dirname(__FILE__).'/tcpdf.php');

class MYPDF extends TCPDF{

    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-10);
        // Page number
        //$this->Cell(6, 8, '   '.$this->getAliasNumPage(), $border, false, 'C', 0, '', 0, false);
        //var_dump($this->getNumPages());
        //exit;
        $this->SetFont('dejavusans', '', 13);
        $this->SetTextColor(255,255,255);
        $this->Rect(100, 287, 10, 10, 'F', array(), array(9,107,115));
        $this->SetFillColor(255,255,255);
        $this->MultiCell(30, 10, (string)$this->getAliasNumPage(), 0, 'C', $fill=true, $ln=1, 100, $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=10, $valign='M', $fitcell=false);

    }

} 