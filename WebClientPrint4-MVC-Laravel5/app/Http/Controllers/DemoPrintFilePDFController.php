<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


//*********************************
// IMPORTANT NOTE 
// ==============
// If your website requires user authentication, then
// THIS FILE MUST be set to ALLOW ANONYMOUS access!!!
//
//*********************************

//Includes WebClientPrint classes
include_once(app_path() . '\WebClientPrint\WebClientPrint.php');
use Neodynamic\SDK\Web\WebClientPrint;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\DefaultPrinter;
use Neodynamic\SDK\Web\InstalledPrinter;
use Neodynamic\SDK\Web\PrintFile;
use Neodynamic\SDK\Web\PrintFilePDF;
use Neodynamic\SDK\Web\PrintRotation;
use Neodynamic\SDK\Web\ClientPrintJob;

use Session;

class DemoPrintFilePDFController extends Controller
{
    public function index(){

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('DemoPrintFilePDFController@printFile'), Session::getId());    

        return view('DemoPrintFilePDF.index', ['wcpScript' => $wcpScript]);
    }

    public function printFile(Request $request){
        
       if ($request->exists(WebClientPrint::CLIENT_PRINT_JOB)) {

            $fileName = uniqid();
            $filePath = public_path().'/files/GuidingPrinciplesBusinessHR_EN.pdf';
        
            //Create PrintFilePDF obj
            $myfile = new PrintFilePDF($filePath, $fileName, null);
            $myfile->printRotation = PrintRotation::parse($request->input('printRotation'));
            $myfile->pagesRange = $request->input('pagesRange');
            $myfile->printAnnotations = ($request->input('printAnnotations')=='true');
            $myfile->printAsGrayscale = ($request->input('printAsGrayscale')=='true');
            $myfile->printInReverseOrder = ($request->input('printInReverseOrder')=='true');
            
            //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
            $cpj = new ClientPrintJob();
            $cpj->printFile = $myfile;
            
            //Create an InstalledPrinter obj
            $myPrinter = new InstalledPrinter(urldecode($request->input('printerName')));
            $myPrinter->trayName = $request->input('trayName');
            $myPrinter->paperName = $request->input('paperName');
            
            $cpj->clientPrinter = $myPrinter;
            
            //Send ClientPrintJob back to the client
            return response($cpj->sendToClient())
                        ->header('Content-Type', 'application/octet-stream');
            
        
        }
    }    

    
}