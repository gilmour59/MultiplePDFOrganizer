<?php

namespace App\Http\Controllers;

use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;

class pdfParserController extends Controller
{
    public function index(){
        
        $parser = new Parser();
        $pdf = $parser->parseFile('C:\laragon\www\OrganizerArchive\storage\app/public/OGM/Ingoing/41google_privacy_policy_en.pdf');
        $text = $pdf->getText();

        echo $text;
    }
}
