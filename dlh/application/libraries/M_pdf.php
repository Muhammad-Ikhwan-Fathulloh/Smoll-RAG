<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/third_party/mpdf/mpdf.php';

class M_pdf {

    public $param;
    public $pdf;

    public function __construct($param) {
        $this->param = $param;
        $this->pdf = new mPDF("en-GB-x","F4",14,"Times New Roman",10,10,8,8,5,5);
    }
}