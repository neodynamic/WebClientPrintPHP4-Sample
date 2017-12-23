<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//*********************************
// IMPORTANT NOTE 
// 1. In this sample we store users related stuff (like
// the list of printers and whether they have the WCPP 
// client utility installed) in the wcpcache folder BUT 
// you can change it to another different storage (like a DB)!
// which will be required in Load Balacing scenarios
// 
// 2. If your website requires user authentication, then
// THIS FILE MUST be set to ALLOW ANONYMOUS access!!!
//
// 3. EXCLUDE this Controller from CSRF Protection
// Ref https://laravel.com/docs/5.5/csrf#csrf-excluding-uris
//*********************************

//Includes WebClientPrint classes
include_once(app_path() . '\WebClientPrint\WebClientPrint.php');
use Neodynamic\SDK\Web\WebClientPrint;


class WebClientPrintController extends Controller
{

    public function processRequest(Request $request){

        
        //IMPORTANT SETTINGS:
        //===================
        //Set wcpcache folder RELATIVE to WebClientPrint.php file
        //FILE WRITE permission on this folder is required!!!
        WebClientPrint::$wcpCacheFolder = app_path() . '/WebClientPrint/wcpcache/';
        //===================

        // Clean built-in Cache
        // NOTE: Remove it if you implement your own cache system
        WebClientPrint::cacheClean(30); //in minutes

        //get session id from querystring if any
        $sid = NULL;
        if ($request->has(WebClientPrint::SID)){
            $sid = $request->input(WebClientPrint::SID);
        }
        
        try{
            //get query string from url
            $query = substr($request->fullUrl(), strpos($request->fullUrl(), '?')+1);
            //get request type
            $reqType = WebClientPrint::GetProcessRequestType($query);
            
            if($reqType == WebClientPrint::GenPrintScript ||
            $reqType == WebClientPrint::GenWcppDetectScript){
                //Let WebClientPrint to generate the requested script
                
                //Get Absolute URL of this file
                $currentAbsoluteURL = substr($request->fullUrl(), 0, strrpos($request->fullUrl(), '?'));
                
                // Return WebClientPrint's javascript code 
                return response(WebClientPrint::generateScript($currentAbsoluteURL, $query))
                        ->header('Content-Type', 'text/javascript');
                
            } 
            else if ($reqType == WebClientPrint::ClientSetWcppVersion)
            {
                //This request is a ping from the WCPP utility
                //so store the session ID indicating this user has the WCPP installed
                //also store the WCPP Version if available
                if($request->has(WebClientPrint::WCPP_SET_VERSION) && strlen($request->input(WebClientPrint::WCPP_SET_VERSION)) > 0){
                    WebClientPrint::cacheAdd($sid, WebClientPrint::WCP_CACHE_WCPP_VER, $request->input(WebClientPrint::WCPP_SET_VERSION));
                }
                return;
            }
            else if ($reqType == WebClientPrint::ClientSetInstalledPrinters)
            {
                //WCPP Utility is sending the installed printers at client side
                //so store this info with the specified session ID
                WebClientPrint::cacheAdd($sid, WebClientPrint::WCP_CACHE_PRINTERS, strlen($request->input(WebClientPrint::WCPP_SET_PRINTERS)) > 0 ? $request->input(WebClientPrint::WCPP_SET_PRINTERS) : '');
                return;
            }
            else if ($reqType == WebClientPrint::ClientSetInstalledPrintersInfo)
            {
                //WCPP Utility is sending the installed printers at client side with detailed info
                //so store this info with the specified session ID
                //Printers Info is in JSON format
                $printersInfo = $request->input('printersInfoContent');
                
                WebClientPrint::cacheAdd($sid, WebClientPrint::WCP_CACHE_PRINTERSINFO, $printersInfo);
                return;
            }
            else if ($reqType == WebClientPrint::ClientGetWcppVersion)
            {
                //return the WCPP version for the specified Session ID (sid) if any
                return response(WebClientPrint::cacheGet($sid, WebClientPrint::WCP_CACHE_WCPP_VER))
                        ->header('Content-Type', 'text/plain');                 
            }
            else if ($reqType == WebClientPrint::ClientGetInstalledPrinters)
            {
                //return the installed printers for the specified Session ID (sid) if any
                return response(base64_decode(WebClientPrint::cacheGet($sid, WebClientPrint::WCP_CACHE_PRINTERS)))
                        ->header('Content-Type', 'text/plain'); 
            }    
            else if ($reqType == WebClientPrint::ClientGetInstalledPrintersInfo)
            {
                //return the installed printers with detailed info for the specified Session ID (sid) if any
                return response(base64_decode(WebClientPrint::cacheGet($sid, WebClientPrint::WCP_CACHE_PRINTERSINFO)))
                        ->header('Content-Type', 'text/plain'); 
            }    
        }
        catch (Exception $ex)
        {
            throw $ex;
        }

    }
    
}