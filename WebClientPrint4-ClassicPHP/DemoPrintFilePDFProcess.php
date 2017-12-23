<?php

include 'WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;
use Neodynamic\SDK\Web\Utils;
use Neodynamic\SDK\Web\DefaultPrinter;
use Neodynamic\SDK\Web\InstalledPrinter;
use Neodynamic\SDK\Web\PrintFile;
use Neodynamic\SDK\Web\PrintFilePDF;
use Neodynamic\SDK\Web\PrintRotation;
use Neodynamic\SDK\Web\ClientPrintJob;

// Process request
// Generate ClientPrintJob? only if clientPrint param is in the query string
$urlParts = parse_url($_SERVER['REQUEST_URI']);

if (isset($urlParts['query'])) {
    $rawQuery = $urlParts['query'];
    parse_str($rawQuery, $qs);
    if (isset($qs[WebClientPrint::CLIENT_PRINT_JOB])) {

        $fileName = uniqid();
        $filePath = 'files/GuidingPrinciplesBusinessHR_EN.pdf';
        
        //Create PrintFilePDF obj
        $myfile = new PrintFilePDF($filePath, $fileName, null);
        $myfile->printRotation = PrintRotation::parse($qs['printRotation']);
        $myfile->pagesRange = $qs['pagesRange'];
        $myfile->printAnnotations = ($qs['printAnnotations']=='true');
        $myfile->printAsGrayscale = ($qs['printAsGrayscale']=='true');
        $myfile->printInReverseOrder = ($qs['printInReverseOrder']=='true');
        
        //Create a ClientPrintJob obj that will be processed at the client side by the WCPP
        $cpj = new ClientPrintJob();
        $cpj->printFile = $myfile;
        
        //Create an InstalledPrinter obj
        $myPrinter = new InstalledPrinter(urldecode($qs['printerName']));
        $myPrinter->trayName = $qs['trayName'];
        $myPrinter->paperName = $qs['paperName'];
        
        $cpj->clientPrinter = $myPrinter;
        
        //Send ClientPrintJob back to the client
        ob_start();
        ob_clean();
        header('Content-type: application/octet-stream');
        echo $cpj->sendToClient();
        ob_end_flush();
        exit();
        
    }
}
    


 