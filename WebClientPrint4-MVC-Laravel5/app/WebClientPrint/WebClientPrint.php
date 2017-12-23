<?php

namespace Neodynamic\SDK\Web;
use Exception;
use ZipArchive;

// Setting WebClientPrint
WebClientPrint::$licenseOwner = '';
WebClientPrint::$licenseKey = '';

//Set wcpcache folder RELATIVE to WebClientPrint.php file
//FILE WRITE permission on this folder is required!!!
WebClientPrint::$wcpCacheFolder = 'wcpcache/';

/**
 * WebClientPrint provides functions for registering the "WebClientPrint for PHP" solution 
 * script code in PHP web pages as well as for processing client requests and managing the
 * internal cache.
 * 
 * @author Neodynamic <http://neodynamic.com/support>
 * @copyright (c) 2018, Neodynamic SRL
 * @license http://neodynamic.com/eula Neodynamic EULA
 */
class WebClientPrint {
   
    const VERSION = '4.0.18.0';
    const CLIENT_PRINT_JOB = 'clientPrint';
    const WCP = 'WEB_CLIENT_PRINT';
    const WCP_SCRIPT_AXD_GET_PRINTERS = 'getPrinters';
    const WCP_SCRIPT_AXD_GET_PRINTERSINFO = 'getPrintersInfo';
    const WCPP_SET_PRINTERS = 'printers';
    const WCPP_SET_PRINTERSINFO = 'printersInfo';
    const WCP_SCRIPT_AXD_GET_WCPPVERSION = 'getWcppVersion';
    const WCPP_SET_VERSION = 'wcppVer';
    const GEN_WCP_SCRIPT_URL = 'u';
    const GEN_DETECT_WCPP_SCRIPT = 'd';
    const SID = 'sid';
    const PING = 'wcppping';
    
    const WCP_CACHE_WCPP_INSTALLED = 'WCPP_INSTALLED';
    const WCP_CACHE_WCPP_VER = 'WCPP_VER';
    const WCP_CACHE_PRINTERS = 'PRINTERS';
    const WCP_CACHE_PRINTERSINFO = 'PRINTERSINFO';
    
    
    /**
     * Gets or sets the License Owner
     * @var string 
     */
    static $licenseOwner = '';
    /**
     * Gets or sets the License Key
     * @var string
     */
    static $licenseKey = '';
    /**
     * Gets or sets the ABSOLUTE URL to WebClientPrint.php file
     * @var string
     */
    static $webClientPrintAbsoluteUrl = '';
    /**
     * Gets or sets the wcpcache folder URL RELATIVE to WebClientPrint.php file. 
     * FILE WRITE permission on this folder is required!!!
     * @var string
     */
    static $wcpCacheFolder = '';
    
    /**
     * Adds a new entry to the built-in file system cache. 
     * @param string $sid The user's session id
     * @param string $key The cache entry key
     * @param string $val The data value to put in the cache
     * @throws Exception
     */
    public static function cacheAdd($sid, $key, $val){
        if (Utils::isNullOrEmptyString(self::$wcpCacheFolder)){
            throw new Exception('WebClientPrint wcpCacheFolder is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sid)){
            throw new Exception('WebClientPrint FileName cache is missing, please specify it.');
        }
        $cacheFileName = (Utils::strEndsWith(self::$wcpCacheFolder, '/')?self::$wcpCacheFolder:self::$wcpCacheFolder.'/').$sid.'.wcpcache';
        $dataWCPP_VER = '';
        $dataPRINTERS = '';
        $dataPRINTERSINFO = '';
            
        if(file_exists($cacheFileName)){
            $cache_info = parse_ini_file($cacheFileName);
            
            $dataWCPP_VER = $cache_info[self::WCP_CACHE_WCPP_VER];
            $dataPRINTERS = $cache_info[self::WCP_CACHE_PRINTERS];
            $dataPRINTERS = $cache_info[self::WCP_CACHE_PRINTERSINFO];
        }
        
        if ($key === self::WCP_CACHE_WCPP_VER){
            $dataWCPP_VER = self::WCP_CACHE_WCPP_VER.'='.'"'.$val.'"';
            $dataPRINTERS = self::WCP_CACHE_PRINTERS.'='.'"'.$dataPRINTERS.'"';
            $dataPRINTERSINFO = self::WCP_CACHE_PRINTERSINFO.'='.'"'.$dataPRINTERSINFO.'"';
        } else if ($key === self::WCP_CACHE_PRINTERS){
            $dataWCPP_VER = self::WCP_CACHE_WCPP_VER.'='.'"'.$dataWCPP_VER.'"';
            $dataPRINTERS = self::WCP_CACHE_PRINTERS.'='.'"'.$val.'"';
            $dataPRINTERSINFO = self::WCP_CACHE_PRINTERSINFO.'='.'"'.$dataPRINTERSINFO.'"';
        } else if ($key === self::WCP_CACHE_PRINTERSINFO){
            $dataWCPP_VER = self::WCP_CACHE_WCPP_VER.'='.'"'.$dataWCPP_VER.'"';
            $dataPRINTERS = self::WCP_CACHE_PRINTERS.'='.'"'.$dataPRINTERS.'"';
            $dataPRINTERSINFO = self::WCP_CACHE_PRINTERSINFO.'='.'"'.$val.'"';
        }

        $data = $dataWCPP_VER.chr(13).chr(10).$dataPRINTERS.chr(13).chr(10).$dataPRINTERSINFO;
        $handle = fopen($cacheFileName, 'w') or die('Cannot open file:  '.$cacheFileName);  
        fwrite($handle, $data);
        fclose($handle);
        
    }
    
    /**
     * Gets a value from the built-in file system cache based on the specified sid & key 
     * @param string $sid The user's session id
     * @param string $key The cache entry key
     * @return string Returns the value from the cache for the specified sid & key if it's found; or an empty string otherwise.
     * @throws Exception
     */
    public static function cacheGet($sid, $key){
        if (Utils::isNullOrEmptyString(self::$wcpCacheFolder)){
            throw new Exception('WebClientPrint wcpCacheFolder is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sid)){
            throw new Exception('WebClientPrint FileName cache is missing, please specify it.');
        }
        $cacheFileName = (Utils::strEndsWith(self::$wcpCacheFolder, '/')?self::$wcpCacheFolder:self::$wcpCacheFolder.'/').$sid.'.wcpcache';
        if(file_exists($cacheFileName)){
            $cache_info = parse_ini_file($cacheFileName, FALSE, INI_SCANNER_RAW);
                
            if($key===self::WCP_CACHE_WCPP_VER || $key===self::WCP_CACHE_WCPP_INSTALLED){
                return $cache_info[self::WCP_CACHE_WCPP_VER];
            }else if($key===self::WCP_CACHE_PRINTERS){
                return $cache_info[self::WCP_CACHE_PRINTERS];
            }else if($key===self::WCP_CACHE_PRINTERSINFO){
                return $cache_info[self::WCP_CACHE_PRINTERSINFO];
            }else{
                return '';
            }
        }else{
            return '';
        }
    }
    
    /**
     * Cleans the built-in file system cache
     * @param integer $minutes The number of minutes after any files on the cache will be removed.
     */
    public static function cacheClean($minutes){
        if (!Utils::isNullOrEmptyString(self::$wcpCacheFolder)){
            $cacheDir = (Utils::strEndsWith(self::$wcpCacheFolder, '/')?self::$wcpCacheFolder:self::$wcpCacheFolder.'/');
            if ($handle = opendir($cacheDir)) {
                 while (false !== ($file = readdir($handle))) {
                    if ($file!='.' && $file!='..' && (time()-filectime($cacheDir.$file)) > (60*$minutes)) {
                        unlink($cacheDir.$file);
                    }
                 }
                 closedir($handle);
            }
        }
    }
    
    /**
     * Returns script code for detecting whether WCPP is installed at the client machine.
     *
     * The WCPP-detection script code ends with a 'success' or 'failure' status.
     * You can handle both situation by creating two javascript functions which names 
     * must be wcppDetectOnSuccess() and wcppDetectOnFailure(). 
     * These two functions will be automatically invoked by the WCPP-detection script code.
     * 
     * The WCPP-detection script uses a delay time variable which by default is 10000 ms (10 sec). 
     * You can change it by creating a javascript global variable which name must be wcppPingDelay_ms. 
     * For example, to use 5 sec instead of 10, you should add this to your script: 
     *   
     * var wcppPingDelay_ms = 5000;
     *    
     * @param string $webClientPrintControllerAbsoluteUrl The Absolute URL to the WebClientPrintController file.
     * @param string $sessionID The current Session ID.
     * @return string A [script] tag linking to the WCPP-detection script code.
     * @throws Exception
     */
    public static function createWcppDetectionScript($webClientPrintControllerAbsoluteUrl, $sessionID){
        
        if (Utils::isNullOrEmptyString($webClientPrintControllerAbsoluteUrl) || 
            !Utils::strStartsWith($webClientPrintControllerAbsoluteUrl, 'http')){
            throw new Exception('WebClientPrintController absolute URL is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sessionID)){
            throw new Exception('Session ID is missing, please specify it.');
        }
        
        $url = $webClientPrintControllerAbsoluteUrl.'?'.self::GEN_DETECT_WCPP_SCRIPT.'='.$sessionID;
        return '<script src="'.$url.'" type="text/javascript"></script>';
         
    }
    
    
    /**
     * Returns a [script] tag linking to the WebClientPrint script code by using 
     * the specified URL for the client print job generation.
     * 
     * @param string $webClientPrintControllerAbsoluteUrl The Absolute URL to the WebClientPrintController file.
     * @param string $clientPrintJobAbsoluteUrl The Absolute URL to the PHP file that creates ClientPrintJob objects.
     * @paran string $sessionID The current Session ID.
     * @return string A [script] tag linking to the WebClientPrint script code by using the specified URL for the client print job generation.
     * @throws Exception
     */
    public static function createScript($webClientPrintControllerAbsoluteUrl, $clientPrintJobAbsoluteUrl, $sessionID){
        if (Utils::isNullOrEmptyString($webClientPrintControllerAbsoluteUrl) || 
            !Utils::strStartsWith($webClientPrintControllerAbsoluteUrl, 'http')){
            throw new Exception('WebClientPrintController absolute URL is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($clientPrintJobAbsoluteUrl) || 
            !Utils::strStartsWith($clientPrintJobAbsoluteUrl, 'http')){
            throw new Exception('ClientPrintJob absolute URL is missing, please specify it.');
        }
        if (Utils::isNullOrEmptyString($sessionID)){
            throw new Exception('Session ID is missing, please specify it.');
        }
        
        
        $wcpHandler = $webClientPrintControllerAbsoluteUrl.'?';
        $wcpHandler .= self::VERSION;
        $wcpHandler .= '&';
        $wcpHandler .= microtime(true);
        $wcpHandler .= '&sid=';
        $wcpHandler .= $sessionID;
        $wcpHandler .= '&'.self::GEN_WCP_SCRIPT_URL.'=';
        $wcpHandler .= base64_encode($clientPrintJobAbsoluteUrl);
        return '<script src="'.$wcpHandler.'" type="text/javascript"></script>';
    }
    
    
    /**
     * Generates the WebClientPrint scripts based on the specified query string. Result is stored in the HTTP Response Content
     * 
     * @param type $webClientPrintControllerAbsoluteUrl The Absolute URL to the WebClientPrintController file.
     * @param type $queryString The Query String from current HTTP Request.
     */
    public static function generateScript($webClientPrintControllerAbsoluteUrl, $queryString)
    {
        if (Utils::isNullOrEmptyString($webClientPrintControllerAbsoluteUrl) || 
            !Utils::strStartsWith($webClientPrintControllerAbsoluteUrl, 'http')){
            throw new Exception('WebClientPrintController absolute URL is missing, please specify it.');
        }
        
        parse_str($queryString, $qs);
    
        if(isset($qs[self::GEN_DETECT_WCPP_SCRIPT])){
            
            $curSID = $qs[self::GEN_DETECT_WCPP_SCRIPT];
            $dynamicIframeId = 'i'.substr(uniqid(), 0, 3);
            $absoluteWcpAxd = $webClientPrintControllerAbsoluteUrl.'?'.self::SID.'='.$curSID;
            
            $s1 = 'dmFyIGpzV0NQUD0oZnVuY3Rpb24oKXt2YXIgc2V0PDw8LU5FTy1IVE1MLUlELT4+Pj1mdW5jdGlvbigpe2lmKHdpbmRvdy5jaHJvbWUpeyQoJyM8PDwtTkVPLUhUTUwtSUQtPj4+JykuYXR0cignaHJlZicsJ3dlYmNsaWVudHByaW50aXY6Jythcmd1bWVudHNbMF0pO3ZhciBhPSQoJ2EjPDw8LU5FTy1IVE1MLUlELT4+PicpWzBdO3ZhciBldk9iaj1kb2N1bWVudC5jcmVhdGVFdmVudCgnTW91c2VFdmVudHMnKTtldk9iai5pbml0RXZlbnQoJ2NsaWNrJyx0cnVlLHRydWUpO2EuZGlzcGF0Y2hFdmVudChldk9iail9ZWxzZXskKCcjPDw8LU5FTy1IVE1MLUlELT4+PicpLmF0dHIoJ3NyYycsJ3dlYmNsaWVudHByaW50aXY6Jythcmd1bWVudHNbMF0pfX07cmV0dXJue2luaXQ6ZnVuY3Rpb24oKXtpZih3aW5kb3cuY2hyb21lKXskKCc8YSAvPicse2lkOic8PDwtTkVPLUhUTUwtSUQtPj4+J30pLmFwcGVuZFRvKCdib2R5Jyl9ZWxzZXskKCc8aWZyYW1lIC8+Jyx7bmFtZTonPDw8LU5FTy1IVE1MLUlELT4+PicsaWQ6Jzw8PC1ORU8tSFRNTC1JRC0+Pj4nLHdpZHRoOicxJyxoZWlnaHQ6JzEnLHN0eWxlOid2aXNpYmlsaXR5OmhpZGRlbjtwb3NpdGlvbjphYnNvbHV0ZSd9KS5hcHBlbmRUbygnYm9keScpfX0scGluZzpmdW5jdGlvbigpe3NldDw8PC1ORU8tSFRNTC1JRC0+Pj4oJzw8PC1ORU8tUElORy1VUkwtPj4+JysoYXJndW1lbnRzLmxlbmd0aD09MT8nJicrYXJndW1lbnRzWzBdOicnKSk7dmFyIGRlbGF5X21zPSh0eXBlb2Ygd2NwcFBpbmdEZWxheV9tcz09PSd1bmRlZmluZWQnKT8wOndjcHBQaW5nRGVsYXlfbXM7aWYoZGVsYXlfbXM+MCl7c2V0VGltZW91dChmdW5jdGlvbigpeyQuZ2V0KCc8PDwtTkVPLVVTRVItSEFTLVdDUFAtPj4+JyxmdW5jdGlvbihkYXRhKXtpZihkYXRhLmxlbmd0aD4wKXt3Y3BwRGV0ZWN0T25TdWNjZXNzKGRhdGEpfWVsc2V7d2NwcERldGVjdE9uRmFpbHVyZSgpfX0pfSxkZWxheV9tcyl9ZWxzZXt2YXIgZm5jV0NQUD1zZXRJbnRlcnZhbChnZXRXQ1BQVmVyLHdjcHBQaW5nVGltZW91dFN0ZXBfbXMpO3ZhciB3Y3BwX2NvdW50PTA7ZnVuY3Rpb24gZ2V0V0NQUFZlcigpe2lmKHdjcHBfY291bnQ8PXdjcHBQaW5nVGltZW91dF9tcyl7JC5nZXQoJzw8PC1ORU8tVVNFUi1IQVMtV0NQUC0+Pj4nLHsnXyc6JC5ub3coKX0sZnVuY3Rpb24oZGF0YSl7aWYoZGF0YS5sZW5ndGg+MCl7Y2xlYXJJbnRlcnZhbChmbmNXQ1BQKTt3Y3BwRGV0ZWN0T25TdWNjZXNzKGRhdGEpfX0pO3djcHBfY291bnQrPXdjcHBQaW5nVGltZW91dFN0ZXBfbXN9ZWxzZXtjbGVhckludGVydmFsKGZuY1dDUFApO3djcHBEZXRlY3RPbkZhaWx1cmUoKX19fX19fSkoKTskKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpe2pzV0NQUC5pbml0KCk7anNXQ1BQLnBpbmcoKX0pOw==';
                    
            $s2 = base64_decode($s1);
            $s2 = str_replace('<<<-NEO-HTML-ID->>>', $dynamicIframeId, $s2);
            $s2 = str_replace('<<<-NEO-PING-URL->>>', $absoluteWcpAxd.'&'.self::PING, $s2);
            $s2 = str_replace('<<<-NEO-USER-HAS-WCPP->>>', $absoluteWcpAxd, $s2);
            
            return $s2;
            
        }else if(isset($qs[self::GEN_WCP_SCRIPT_URL])){
            
            $clientPrintJobUrl = base64_decode($qs[self::GEN_WCP_SCRIPT_URL]);
            if (strpos($clientPrintJobUrl, '?')>0){
                $clientPrintJobUrl .= '&';
            }else{
                $clientPrintJobUrl .= '?';
            }
            $clientPrintJobUrl .= self::CLIENT_PRINT_JOB;
            $absoluteWcpAxd = $webClientPrintControllerAbsoluteUrl;
            $wcppGetPrintersParam = '-getPrinters:'.$absoluteWcpAxd.'?'.self::WCP.'&'.self::SID.'=';
            $wcpHandlerGetPrinters = $absoluteWcpAxd.'?'.self::WCP.'&'.self::WCP_SCRIPT_AXD_GET_PRINTERS.'&'.self::SID.'=';
            $wcppGetPrintersInfoParam = '-getPrintersInfo:'.$absoluteWcpAxd.'?'.self::WCP.'&'.self::SID.'=';
            $wcpHandlerGetPrintersInfo = $absoluteWcpAxd.'?'.self::WCP.'&'.self::WCP_SCRIPT_AXD_GET_PRINTERSINFO.'&'.self::SID.'=';
            $wcppGetWcppVerParam = '-getWcppVersion:'.$absoluteWcpAxd.'?'.self::WCP.'&'.self::SID.'=';
            $wcpHandlerGetWcppVer = $absoluteWcpAxd.'?'.self::WCP.'&'.self::WCP_SCRIPT_AXD_GET_WCPPVERSION.'&'.self::SID.'=';
            $sessionIDVal = $qs[self::SID];
        
            $s1 = 'dmFyIGpzV2ViQ2xpZW50UHJpbnQ9KGZ1bmN0aW9uKCl7dmFyIHNldEE9ZnVuY3Rpb24oKXt2YXIgZV9pZD0naWRfJytuZXcgRGF0ZSgpLmdldFRpbWUoKTtpZih3aW5kb3cuY2hyb21lKXskKCdib2R5JykuYXBwZW5kKCc8YSBpZD1cIicrZV9pZCsnXCI+PC9hPicpOyQoJyMnK2VfaWQpLmF0dHIoJ2hyZWYnLCd3ZWJjbGllbnRwcmludGl2OicrYXJndW1lbnRzWzBdKTt2YXIgYT0kKCdhIycrZV9pZClbMF07dmFyIGV2T2JqPWRvY3VtZW50LmNyZWF0ZUV2ZW50KCdNb3VzZUV2ZW50cycpO2V2T2JqLmluaXRFdmVudCgnY2xpY2snLHRydWUsdHJ1ZSk7YS5kaXNwYXRjaEV2ZW50KGV2T2JqKX1lbHNleyQoJ2JvZHknKS5hcHBlbmQoJzxpZnJhbWUgbmFtZT1cIicrZV9pZCsnXCIgaWQ9XCInK2VfaWQrJ1wiIHdpZHRoPVwiMVwiIGhlaWdodD1cIjFcIiBzdHlsZT1cInZpc2liaWxpdHk6aGlkZGVuO3Bvc2l0aW9uOmFic29sdXRlXCIgLz4nKTskKCcjJytlX2lkKS5hdHRyKCdzcmMnLCd3ZWJjbGllbnRwcmludGl2OicrYXJndW1lbnRzWzBdKX1zZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JCgnIycrZV9pZCkucmVtb3ZlKCl9LDUwMDApfTtyZXR1cm57cHJpbnQ6ZnVuY3Rpb24oKXtzZXRBKCdVUkxfUFJJTlRfSk9CJysoYXJndW1lbnRzLmxlbmd0aD09MT8nJicrYXJndW1lbnRzWzBdOicnKSl9LGdldFByaW50ZXJzOmZ1bmN0aW9uKCl7c2V0QSgnVVJMX1dDUF9BWERfV0lUSF9HRVRfUFJJTlRFUlNfQ09NTUFORCcrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nKTt2YXIgZGVsYXlfbXM9KHR5cGVvZiB3Y3BwR2V0UHJpbnRlcnNEZWxheV9tcz09PSd1bmRlZmluZWQnKT8wOndjcHBHZXRQcmludGVyc0RlbGF5X21zO2lmKGRlbGF5X21zPjApe3NldFRpbWVvdXQoZnVuY3Rpb24oKXskLmdldCgnVVJMX1dDUF9BWERfR0VUX1BSSU5URVJTJysnPDw8LU5FTy1TRVNTSU9OLUlELT4+PicsZnVuY3Rpb24oZGF0YSl7aWYoZGF0YS5sZW5ndGg+MCl7d2NwR2V0UHJpbnRlcnNPblN1Y2Nlc3MoZGF0YSl9ZWxzZXt3Y3BHZXRQcmludGVyc09uRmFpbHVyZSgpfX0pfSxkZWxheV9tcyl9ZWxzZXt2YXIgZm5jR2V0UHJpbnRlcnM9c2V0SW50ZXJ2YWwoZ2V0Q2xpZW50UHJpbnRlcnMsd2NwcEdldFByaW50ZXJzVGltZW91dFN0ZXBfbXMpO3ZhciB3Y3BwX2NvdW50PTA7ZnVuY3Rpb24gZ2V0Q2xpZW50UHJpbnRlcnMoKXtpZih3Y3BwX2NvdW50PD13Y3BwR2V0UHJpbnRlcnNUaW1lb3V0X21zKXskLmdldCgnVVJMX1dDUF9BWERfR0VUX1BSSU5URVJTJysnPDw8LU5FTy1TRVNTSU9OLUlELT4+PicseydfJzokLm5vdygpfSxmdW5jdGlvbihkYXRhKXtpZihkYXRhLmxlbmd0aD4wKXtjbGVhckludGVydmFsKGZuY0dldFByaW50ZXJzKTt3Y3BHZXRQcmludGVyc09uU3VjY2VzcyhkYXRhKX19KTt3Y3BwX2NvdW50Kz13Y3BwR2V0UHJpbnRlcnNUaW1lb3V0U3RlcF9tc31lbHNle2NsZWFySW50ZXJ2YWwoZm5jR2V0UHJpbnRlcnMpO3djcEdldFByaW50ZXJzT25GYWlsdXJlKCl9fX19LGdldFByaW50ZXJzSW5mbzpmdW5jdGlvbigpe3NldEEoJ1VSTF9XQ1BfQVhEX1dJVEhfR0VUX1BSSU5URVJTSU5GT19DT01NQU5EJysnPDw8LU5FTy1TRVNTSU9OLUlELT4+PicpO3ZhciBkZWxheV9tcz0odHlwZW9mIHdjcHBHZXRQcmludGVyc0RlbGF5X21zPT09J3VuZGVmaW5lZCcpPzA6d2NwcEdldFByaW50ZXJzRGVsYXlfbXM7aWYoZGVsYXlfbXM+MCl7c2V0VGltZW91dChmdW5jdGlvbigpeyQuZ2V0KCdVUkxfV0NQX0FYRF9HRVRfUFJJTlRFUlNJTkZPJysnPDw8LU5FTy1TRVNTSU9OLUlELT4+PicsZnVuY3Rpb24oZGF0YSl7aWYoZGF0YS5sZW5ndGg+MCl7d2NwR2V0UHJpbnRlcnNPblN1Y2Nlc3MoZGF0YSl9ZWxzZXt3Y3BHZXRQcmludGVyc09uRmFpbHVyZSgpfX0pfSxkZWxheV9tcyl9ZWxzZXt2YXIgZm5jR2V0UHJpbnRlcnNJbmZvPXNldEludGVydmFsKGdldENsaWVudFByaW50ZXJzSW5mbyx3Y3BwR2V0UHJpbnRlcnNUaW1lb3V0U3RlcF9tcyk7dmFyIHdjcHBfY291bnQ9MDtmdW5jdGlvbiBnZXRDbGllbnRQcmludGVyc0luZm8oKXtpZih3Y3BwX2NvdW50PD13Y3BwR2V0UHJpbnRlcnNUaW1lb3V0X21zKXskLmdldCgnVVJMX1dDUF9BWERfR0VUX1BSSU5URVJTSU5GTycrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nLHsnXyc6JC5ub3coKX0sZnVuY3Rpb24oZGF0YSl7aWYoZGF0YS5sZW5ndGg+MCl7Y2xlYXJJbnRlcnZhbChmbmNHZXRQcmludGVyc0luZm8pO3djcEdldFByaW50ZXJzT25TdWNjZXNzKGRhdGEpfX0pO3djcHBfY291bnQrPXdjcHBHZXRQcmludGVyc1RpbWVvdXRTdGVwX21zfWVsc2V7Y2xlYXJJbnRlcnZhbChmbmNHZXRQcmludGVyc0luZm8pO3djcEdldFByaW50ZXJzT25GYWlsdXJlKCl9fX19LGdldFdjcHBWZXI6ZnVuY3Rpb24oKXtzZXRBKCdVUkxfV0NQX0FYRF9XSVRIX0dFVF9XQ1BQVkVSU0lPTl9DT01NQU5EJysnPDw8LU5FTy1TRVNTSU9OLUlELT4+PicpO3ZhciBkZWxheV9tcz0odHlwZW9mIHdjcHBHZXRWZXJEZWxheV9tcz09PSd1bmRlZmluZWQnKT8wOndjcHBHZXRWZXJEZWxheV9tcztpZihkZWxheV9tcz4wKXtzZXRUaW1lb3V0KGZ1bmN0aW9uKCl7JC5nZXQoJ1VSTF9XQ1BfQVhEX0dFVF9XQ1BQVkVSU0lPTicrJzw8PC1ORU8tU0VTU0lPTi1JRC0+Pj4nLGZ1bmN0aW9uKGRhdGEpe2lmKGRhdGEubGVuZ3RoPjApe3djcEdldFdjcHBWZXJPblN1Y2Nlc3MoZGF0YSl9ZWxzZXt3Y3BHZXRXY3BwVmVyT25GYWlsdXJlKCl9fSl9LGRlbGF5X21zKX1lbHNle3ZhciBmbmNXQ1BQPXNldEludGVydmFsKGdldENsaWVudFZlcix3Y3BwR2V0VmVyVGltZW91dFN0ZXBfbXMpO3ZhciB3Y3BwX2NvdW50PTA7ZnVuY3Rpb24gZ2V0Q2xpZW50VmVyKCl7aWYod2NwcF9jb3VudDw9d2NwcEdldFZlclRpbWVvdXRfbXMpeyQuZ2V0KCdVUkxfV0NQX0FYRF9HRVRfV0NQUFZFUlNJT04nKyc8PDwtTkVPLVNFU1NJT04tSUQtPj4+Jyx7J18nOiQubm93KCl9LGZ1bmN0aW9uKGRhdGEpe2lmKGRhdGEubGVuZ3RoPjApe2NsZWFySW50ZXJ2YWwoZm5jV0NQUCk7d2NwR2V0V2NwcFZlck9uU3VjY2VzcyhkYXRhKX19KTt3Y3BwX2NvdW50Kz13Y3BwR2V0VmVyVGltZW91dFN0ZXBfbXN9ZWxzZXtjbGVhckludGVydmFsKGZuY1dDUFApO3djcEdldFdjcHBWZXJPbkZhaWx1cmUoKX19fX0sc2VuZDpmdW5jdGlvbigpe3NldEEuYXBwbHkodGhpcyxhcmd1bWVudHMpfX19KSgpOw==';
    
            $s2 = base64_decode($s1);
            $s2 = str_replace('URL_PRINT_JOB', $clientPrintJobUrl, $s2);
            $s2 = str_replace('URL_WCP_AXD_WITH_GET_PRINTERSINFO_COMMAND', $wcppGetPrintersInfoParam, $s2);
            $s2 = str_replace('URL_WCP_AXD_GET_PRINTERSINFO', $wcpHandlerGetPrintersInfo, $s2);
            $s2 = str_replace('URL_WCP_AXD_WITH_GET_PRINTERS_COMMAND', $wcppGetPrintersParam, $s2);
            $s2 = str_replace('URL_WCP_AXD_GET_PRINTERS', $wcpHandlerGetPrinters, $s2);
            $s2 = str_replace('URL_WCP_AXD_WITH_GET_WCPPVERSION_COMMAND', $wcppGetWcppVerParam, $s2);
            $s2 = str_replace('URL_WCP_AXD_GET_WCPPVERSION', $wcpHandlerGetWcppVer, $s2);
            $s2 = str_replace('<<<-NEO-SESSION-ID->>>', $sessionIDVal, $s2);
            
            return $s2;
        }
        
    }
    
       
    /**
     * Generates printing script.
     */
    const GenPrintScript = 0;
    /**
     * Generates WebClientPrint Processor (WCPP) detection script.
     */ 
    const GenWcppDetectScript = 1;
    /**
     * Sets the installed printers list in the website cache.
     */        
    const ClientSetInstalledPrinters = 2;
    /**
     * Gets the installed printers list from the website cache.
     */
    const ClientGetInstalledPrinters = 3;
    /**
     * Sets the WebClientPrint Processor (WCPP) Version in the website cache.
     */
    const ClientSetWcppVersion = 4;
    /**
     * Gets the WebClientPrint Processor (WCPP) Version from the website cache.
     */
    const ClientGetWcppVersion = 5;
    /**
     * Sets the installed printers list with detailed info in the website cache.
     */
    const ClientSetInstalledPrintersInfo = 6;
    /**
     * Gets the installed printers list with detailed info from the website cache.
     */
    const ClientGetInstalledPrintersInfo = 7;
       
    
    /**
     * Determines the type of process request based on the Query String value. 
     * 
     * @param string $queryString The query string of the current request.
     * @return integer A valid type of process request. In case of an invalid value, an Exception is thrown.
     * @throws Exception 
     */
    public static function GetProcessRequestType($queryString){
        parse_str($queryString, $qs);
    
        if(isset($qs[self::SID])){
            if(isset($qs[self::PING])){
                return self::ClientSetWcppVersion;
            } else if(isset($qs[self::WCPP_SET_VERSION])){
                return self::ClientSetWcppVersion;
            } else if(isset($qs[self::WCPP_SET_PRINTERS])){
                return self::ClientSetInstalledPrinters;
            } else if(isset($qs[self::WCPP_SET_PRINTERSINFO])){
                return self::ClientSetInstalledPrintersInfo;
            } else if(isset($qs[self::WCP_SCRIPT_AXD_GET_WCPPVERSION])){
                return self::ClientGetWcppVersion;
            } else if(isset($qs[self::WCP_SCRIPT_AXD_GET_PRINTERS])){
                return self::ClientGetInstalledPrinters;
            } else if(isset($qs[self::WCP_SCRIPT_AXD_GET_PRINTERSINFO])){
                return self::ClientGetInstalledPrintersInfo;
            } else if(isset($qs[self::GEN_WCP_SCRIPT_URL])){
                return self::GenPrintScript;
            } else {
                return self::ClientGetWcppVersion;
            }
        } else if(isset($qs[self::GEN_DETECT_WCPP_SCRIPT])){
            return self::GenWcppDetectScript;
        } else {
            throw new Exception('No valid ProcessRequestType was found in the specified QueryString.');
        }
    }
    
}

/**
 * The base class for all kind of printers supported at the client side.
 */
abstract class ClientPrinter{
    
    public $printerId;
    public function serialize(){
        
    }
}

/**
 * It represents the default printer installed in the client machine.
 */
class DefaultPrinter extends ClientPrinter{
    public function __construct() {
        $this->printerId = chr(0);
    }
    
    public function serialize() {
        return $this->printerId;
    }
}

/**
 * It represents a printer installed in the client machine with an associated OS driver.
 */
class InstalledPrinter extends ClientPrinter{
    
    /**
     * Gets or sets the name of the printer installed in the client machine. Default value is an empty string.
     * @var string 
     */
    public $printerName = '';

    /**
     * Gets or sets whether to print to Default printer in case of the specified one is not found or missing. Default is False.
     * @var boolean 
     */
    public $printToDefaultIfNotFound = false;
    
    
    /**
     * Gets or sets the name of the tray supported by the client printer. Default value is an empty string.
     * @var string 
     */
    public $trayName = '';
    
    /**
     * Gets or sets the name of the Paper supported by the client printer. Default value is an empty string.
     * @var string 
     */
    public $paperName = '';
    
    
    /**
     * Creates an instance of the InstalledPrinter class with the specified printer name.
     * @param string $printerName The name of the printer installed in the client machine.
     */
    public function __construct($printerName) {
        $this->printerId = chr(1);
        $this->printerName = $printerName;
    }
    
    public function serialize() {
        
        if (Utils::isNullOrEmptyString($this->printerName)){
             throw new Exception("The specified printer name is null or empty.");
        }
        
        $serData = $this->printerId.$this->printerName;
        
        if ($this->printToDefaultIfNotFound){
            $serData .= Utils::SER_SEP.'1';     
        } else {
            $serData .= Utils::SER_SEP.'0';    
        }      
        
        if ($this->trayName){
            $serData .= Utils::SER_SEP.$this->trayName;     
        } else {
            $serData .= Utils::SER_SEP.'def';    
        }
        
        if ($this->paperName){
            $serData .= Utils::SER_SEP.$this->paperName;     
        } else {
            $serData .= Utils::SER_SEP.'def';    
        }
        
        return $serData;
    }
}

/**
 * It represents a printer which is connected through a parallel port in the client machine.
 */
class ParallelPortPrinter extends ClientPrinter{
    
    /**
     * Gets or sets the parallel port name, for example LPT1. Default value is "LPT1"
     * @var string 
     */
    public $portName = "LPT1";

    /**
     * Creates an instance of the ParallelPortPrinter class with the specified port name.
     * @param string $portName The parallel port name, for example LPT1.
     */
    public function __construct($portName) {
        $this->printerId = chr(2);
        $this->portName = $portName;
    }
    
    public function serialize() {
        
        if (Utils::isNullOrEmptyString($this->portName)){
             throw new Exception("The specified parallel port name is null or empty.");
        }
        
        return $this->printerId.$this->portName;
    }
}

/**
 * It represents a printer which is connected through a serial port in the client machine.
 */
class SerialPortPrinter extends ClientPrinter{
    
    /**
     * Gets or sets the serial port name, for example COM1. Default value is "COM1"
     * @var string 
     */
    public $portName = "COM1";
    /**
     * Gets or sets the serial port baud rate in bits per second. Default value is 9600
     * @var integer 
     */
    public $baudRate = 9600;
    /**
     * Gets or sets the serial port parity-checking protocol. Default value is NONE = 0
     * NONE = 0, ODD = 1, EVEN = 2, MARK = 3, SPACE = 4
     * @var integer 
     */
    public $parity = SerialPortParity::NONE;
    /**
     * Gets or sets the serial port standard number of stopbits per byte. Default value is ONE = 1
     * ONE = 1, TWO = 2, ONE_POINT_FIVE = 3
     * @var integer
     */
    public $stopBits = SerialPortStopBits::ONE;
    /**
     * Gets or sets the serial port standard length of data bits per byte. Default value is 8
     * @var integer
     */
    public $dataBits = 8;
    /**
     * Gets or sets the handshaking protocol for serial port transmission of data. Default value is XON_XOFF = 1
     * NONE = 0, REQUEST_TO_SEND = 2, REQUEST_TO_SEND_XON_XOFF = 3, XON_XOFF = 1
     * @var integer
     */
    public $flowControl = SerialPortHandshake::XON_XOFF;
    
    /**
     * Creates an instance of the SerialPortPrinter class wiht the specified information.
     * @param string $portName The serial port name, for example COM1.
     * @param integer $baudRate The serial port baud rate in bits per second.
     * @param integer $parity The serial port parity-checking protocol.
     * @param integer $stopBits The serial port standard number of stopbits per byte.
     * @param integer $dataBits The serial port standard length of data bits per byte.
     * @param integer $flowControl The handshaking protocol for serial port transmission of data.
     */
    public function __construct($portName, $baudRate, $parity, $stopBits, $dataBits, $flowControl) {
        $this->printerId = chr(3);
        $this->portName = $portName;
        $this->baudRate = $baudRate;
        $this->parity = $parity;
        $this->stopBits = $stopBits;
        $this->dataBits = $dataBits;
        $this->flowControl = $flowControl;
    }
    
    public function serialize() {
        
        if (Utils::isNullOrEmptyString($this->portName)){
             throw new Exception("The specified serial port name is null or empty.");
        }
        
        return $this->printerId.$this->portName.Utils::SER_SEP.$this->baudRate.Utils::SER_SEP.$this->dataBits.Utils::SER_SEP.((int)$this->flowControl).Utils::SER_SEP.((int)$this->parity).Utils::SER_SEP.((int)$this->stopBits);
    }
}

/**
 * It represents a Network IP/Ethernet printer which can be reached from the client machine.
 */
class NetworkPrinter extends ClientPrinter{
    
    /**
     * Gets or sets the DNS name assigned to the printer. Default is an empty string
     * @var string 
     */
    public $dnsName = "";
    /**
     * Gets or sets the Internet Protocol (IP) address assigned to the printer. Default value is an empty string
     * @var string 
     */
    public $ipAddress = "";
    /**
     * Gets or sets the port number assigned to the printer. Default value is 0
     * @var integer 
     */
    public $port = 0;
    
    /**
     * Creates an instance of the NetworkPrinter class with the specified DNS name or IP Address, and port number.
     * @param string $dnsName The DNS name assigned to the printer.
     * @param string $ipAddress The Internet Protocol (IP) address assigned to the printer.
     * @param integer $port The port number assigned to the printer.
     */
    public function __construct($dnsName, $ipAddress, $port) {
        $this->printerId = chr(4);
        $this->dnsName = $dnsName;
        $this->ipAddress = $ipAddress;
        $this->port = $port;
    }
    
    public function serialize() {
        
        if (Utils::isNullOrEmptyString($this->dnsName) && Utils::isNullOrEmptyString($this->ipAddress)){
             throw new Exception("The specified network printer settings is not valid. You must specify the DNS Printer Name or its IP address.");
        }
        
        return $this->printerId.$this->dnsName.Utils::SER_SEP.$this->ipAddress.Utils::SER_SEP.$this->port;
    }
}

/**
 *  It represents a printer which will be selected by the user in the client machine. The user will be prompted with a print dialog.
 */
class UserSelectedPrinter extends ClientPrinter{
    public function __construct() {
        $this->printerId = chr(5);
    }
    
    public function serialize() {
        return $this->printerId;
    }
}

/**
 * Specifies the parity bit for Serial Port settings. 
 */
class SerialPortParity{
    const NONE = 0;
    const ODD = 1;
    const EVEN = 2;
    const MARK = 3;
    const SPACE = 4;
    public static function parse($val){
        if($val === 'NONE') return 0;
        if($val === 'ODD') return 1;
        if($val === 'EVEN') return 2;
        if($val === 'MARK') return 3;
        if($val === 'SPACE') return 4;
        throw new Exception('Invalid value');
    }
}

/**
 * Specifies the number of stop bits used for Serial Port settings.
 */
class SerialPortStopBits{
    const NONE = 0;
    const ONE = 1;
    const TWO = 2;
    const ONE_POINT_FIVE = 3;
    public static function parse($val){
        if($val === 'NONE') return 0;
        if($val === 'ONE') return 1;
        if($val === 'TWO') return 2;
        if($val === 'ONE_POINT_FIVE') return 3;
        throw new Exception('Invalid value');
    }
}

/**
 * Specifies the control protocol used in establishing a serial port communication.
 */
class SerialPortHandshake{
    const NONE = 0;
    const REQUEST_TO_SEND = 2;
    const REQUEST_TO_SEND_XON_XOFF = 3;
    const XON_XOFF = 1;
    public static function parse($val){
        if($val === 'NONE') return 0;
        if($val === 'XON_XOFF') return 1;
        if($val === 'REQUEST_TO_SEND') return 2;
        if($val === 'REQUEST_TO_SEND_XON_XOFF') return 3;
        throw new Exception('Invalid value');
    }
}

/**
 * It represents a file in the server that will be printed at the client side.
 */
class PrintFile{
    
    /**
     * Gets or sets the path of the file at the server side that will be printed at the client side.
     * @var string 
     */
    public $filePath = '';
    /**
     * Gets or sets the file name that will be created at the client side. 
     * It must include the file extension like .pdf, .txt, .doc, .xls, etc.
     * @var string 
     */
    public $fileName = '';
    /**
     * Gets or sets the binary content of the file at the server side that will be printed at the client side.
     * @var string 
     */
    public $fileBinaryContent = '';
    
    /**
     * Gets or sets the num of copies for printing this file. Default is 1.
     * @var integer
     */
    public $copies = 1;
    
    const PREFIX = 'wcpPF:';
    const SEP = '|';
        
    /**
     * 
     * @param string $filePath The path of the file at the server side that will be printed at the client side.
     * @param string $fileName The file name that will be created at the client side. It must include the file extension like .pdf, .txt, .doc, .xls, etc.
     * @param string $fileBinaryContent The binary content of the file at the server side that will be printed at the client side.
     */
    public function __construct($filePath, $fileName, $fileBinaryContent) {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->fileBinaryContent = $fileBinaryContent;
        
    }
    
    public function serialize() {
        $file = str_replace('\\', 'BACKSLASHCHAR',$this->fileName );
        if($this->copies > 1){
             $pfc = 'PFC='.$this->copies;
             $file = substr($file, 0, strrpos($file, '.')).$pfc.substr($file, strrpos($file, '.'));
        }
        return self::PREFIX.$file.self::SEP.$this->getFileContent();
    }
    
    public function getFileContent(){
        $content = $this->fileBinaryContent;
        if(!Utils::isNullOrEmptyString($this->filePath)){
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        }
        return $content;
    }
}

/**
 * Specifies the print rotation.
 */
class PrintRotation
{
    /**
     * Print page without rotation.
     */
    const None = 0;
    /**
     * Print page rotated by 90 degrees clockwise.
     */
    const Rot90 = 1;
    /**
     * Print page rotated by 180 degrees.
     */
    const Rot180 = 2;
    /**
     * Print page rotated by 270 degrees clockwise.
     */
    const Rot270 = 3;
    
    public static function parse($val){
        if($val === 'None') return 0;
        if($val === 'Rot90') return 1;
        if($val === 'Rot180') return 2;
        if($val === 'Rot270') return 3;
        throw new Exception('Invalid value');
    }
}

/**
 * It represents a PDF file in the server that will be printed at the client side.
 */
class PrintFilePDF extends PrintFile{
    
    /**
     * Gets or sets whether to print the PDF document with color images, texts, or other objects as shades of gray. Default is False.
     * @var boolean 
     */
    public $printAsGrayscale = false;
 
    /**
     * Gets or sets whether to print any annotations, if any, available in the PDF document. Default is False.
     * @var boolean 
     */
    public $printAnnotations = false;
    
    /**
     * Gets or sets a subset of pages to print. It can be individual page numbers, a range, or a combination. For example: 1, 5-10, 25, 50. Default is an empty string which means print all pages.
     * @var string 
     */
    public $pagesRange = '';
    
    /**
     * Gets or sets whether pages are printed in reverse order. Default is False.
     * @var boolean 
     */
    public $printInReverseOrder = false;
    
    /**
     * Gets or sets the print rotation. Default is None.
     * @var integer 
     */
    public $printRotation = PrintRotation::None;
    
    
    public function serialize() {
        $file = str_replace('\\', 'BACKSLASHCHAR', $this->fileName);
        if($this->copies > 1){
             $pfc = 'PFC='.$this->copies;
             $file = substr($file, 0, strrpos($file, '.')).$pfc.substr($file, strrpos($file, '.'));
        }
        
        return self::PREFIX.$file.'.wpdf'.self::SEP.$this->getFileContent();
    }
    
    public function getFileContent(){
 
        $pr = urldecode($this->pagesRange);
        if (!Utils::isNullOrEmptyString($pr)){
            if (preg_match('/^(?!([ \d]*-){2})\d+(?: *[-,] *\d+)*$/', $pr))
            {
                //validate range
                $ranges = explode(',',str_replace(' ', '', $pr)); //remove any space chars
                
                for ($i = 0; $i < count($ranges); $i++)
                {
                    if (strpos($ranges[$i], '-') > 0)
                    {
                        $pages = explode('-', $ranges[$i]);
                        if (intval($pages[0]) > intval($pages[1]))
                        {
                            throw new Exception("The specified PageRange is not valid.");
                        }
                    }
                }
            }
            else
                throw new Exception("The specified PageRange is not valid.");
            
        }
        
        $metadata = ($this->printAsGrayscale ? '1' : '0');
        $metadata .= Utils::SER_SEP.($this->printAnnotations ? '1' : '0');
        $metadata .= Utils::SER_SEP.(Utils::isNullOrEmptyString($pr) ? 'A' : $pr);
        $metadata .= Utils::SER_SEP.($this->printInReverseOrder ? '1' : '0');
        $metadata .= Utils::SER_SEP.$this->printRotation;
        
        $content = $this->fileBinaryContent;
        if(!Utils::isNullOrEmptyString($this->filePath)){
            $handle = fopen($this->filePath, 'rb');
            $content = fread($handle, filesize($this->filePath));
            fclose($handle);
        }
        return $metadata.chr(10).$content;
    }
}

/**
 * Specifies the print orientation.
 */
class PrintOrientation
{
    /**
     * Print the document vertically.
     */
    const Portrait = 0;
    /**
     *  Print the document horizontally.
     */
    const Landscape = 1;
    
    public static function parse($val){
        if($val === 'Portrait') return 0;
        if($val === 'Landscape') return 1;
        throw new Exception('Invalid value');
    }
}

/**
 * Specifies the text alignment
 */
class TextAlignment
{
    /**
     * Left alignment
     */
    const Left = 0;
    /**
     * Right alignment
     */
    const Right = 2;
    /**
     * Center alignment
     */
    const Center = 1;
    /**
     * Justify alignment
     */
    const Justify = 3;
    
    public static function parse($val){
        if($val === 'Left') return 0;
        if($val === 'Center') return 1;
        if($val === 'Right') return 2;
        if($val === 'Justify') return 3;
        throw new Exception('Invalid value');
    }
}
    
/**
 * It represents a plain text file in the server that will be printed at the client side.
 */
class PrintFileTXT extends PrintFile{
    
    /**
     * Gets or sets the Text content to be printed. Default is an empty string.
     * @var string 
     */
     public $textContent = '';
     
     /**
      * Gets or sets the alignment of the text content. Default is Left alignment.
      * @var integer 
      */
     public $textAlignment = TextAlignment::Left;

     /**
      * Gets or sets the font name. Default is Arial.
      * @var string 
      */
     public $fontName = 'Arial';
     
     /**
      * Gets or sets whether the text is bold. Default is False.
      * @var boolean 
      */
     public $fontBold = false;
        
     /**
      * Gets or sets whether the text has the italic style applied. Default is False.
      * @var boolean 
      */
     public $fontItalic = false;
      
     /**
      * Gets or sets whether the text is underlined. Default is False.
      * @var boolean 
      */
     public $fontUnderline = false;
        
     /**
      * Gets or sets whether the text is printed with a horizontal line through it. Default is False.
      * @var boolean
      */
     public $fontStrikeThrough = false;
        
     /**
      * Gets or sets the font size in Points unit. Default is 10pt. 
      * @var float 
      */
     public $fontSizeInPoints = 10.0;
        
     /**
      * Gets or sets the Color for the printed text. Color must be specified in Hex notation for RGB channels respectively e.g. #rgb or #rrggbb. Default is #000000.
      * @var string 
      */
     public $textColor = "#000000";
        
     /**
      * Gets or sets the print orientation. Default is Portrait.
      * @var integer 
      */
     public $printOrientation = PrintOrientation::Portrait;
        
     /**
      * Gets or sets the left margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
      * @var float 
      */
     public $marginLeft = 0.5;
        
     /**
      * Gets or sets the right margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
      * @var float 
      */
     public $marginRight = 0.5;
     
     /**
      * Gets or sets the top margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
      * @var float 
      */
     public $marginTop = 0.5;
        
     /**
      * Gets or sets the bottom margin for the printed text. Value must be specified in Inch unit. Default is 0.5in
      * @var float 
      */
     public $marginBottom = 0.5;
     
     
     public function serialize() {
        $file = str_replace('\\', 'BACKSLASHCHAR',  $this->fileName);
        if($this->copies > 1){
             $pfc = 'PFC='.$this->copies;
             $file = substr($file, 0, strrpos($file, '.')).$pfc.substr($file, strrpos($file, '.'));
        }
        
        return self::PREFIX.$file.'.wtxt'.self::SEP.$this->getFileContent();
     }
    
     public function getFileContent(){
        
        $metadata = $this->printOrientation;
        $metadata .= Utils::SER_SEP.$this->textAlignment;
        $metadata .= Utils::SER_SEP.$this->fontName;
        $metadata .= Utils::SER_SEP.strval($this->fontSizeInPoints);
        $metadata .= Utils::SER_SEP.($this->fontBold ? '1' : '0');
        $metadata .= Utils::SER_SEP.($this->fontItalic ? '1' : '0');
        $metadata .= Utils::SER_SEP.($this->fontUnderline ? '1' : '0');
        $metadata .= Utils::SER_SEP.($this->fontStrikeThrough ? '1' : '0');
        $metadata .= Utils::SER_SEP.$this->textColor;
        $metadata .= Utils::SER_SEP.strval($this->marginLeft);
        $metadata .= Utils::SER_SEP.strval($this->marginTop);
        $metadata .= Utils::SER_SEP.strval($this->marginRight);
        $metadata .= Utils::SER_SEP.strval($this->marginBottom);
        
        $content = $this->textContent;
        if (Utils::isNullOrEmptyString($content)){
            $content = $this->fileBinaryContent;
            if(!Utils::isNullOrEmptyString($this->filePath)){
                $handle = fopen($this->filePath, 'rb');
                $content = fread($handle, filesize($this->filePath));
                fclose($handle);
            }
        }
     
        if (Utils::isNullOrEmptyString($content))
            throw new Exception('The specified Text file is empty and cannot be printed.');
        
        return $metadata.chr(10).$content;
    }
}

/**
 * Some utility functions used by WebClientPrint for PHP solution.
 */
class Utils{
    const SER_SEP = '|';
    
    static function isNullOrEmptyString($s){
        return (!isset($s) || trim($s)==='');
    }
    
    static function formatHexValues($s){
        
        $buffer = '';
            
        $l = strlen($s);
        $i = 0;

        while ($i < $l)
        {
            if ($s[$i] == '0')
            {
                if ($i + 1 < $l && ($s[$i] == '0' && $s[$i + 1] == 'x'))
                {
                    if ($i + 2 < $l &&
                        (($s[$i + 2] >= '0' && $s[$i + 2] <= '9') || ($s[$i + 2] >= 'a' && $s[$i + 2] <= 'f') || ($s[$i + 2] >= 'A' && $s[$i + 2] <= 'F')))
                    {
                        if ($i + 3 < $l &&
                           (($s[$i + 3] >= '0' && $s[$i + 3] <= '9') || ($s[$i + 3] >= 'a' && $s[$i + 3] <= 'f') || ($s[$i + 3] >= 'A' && $s[$i + 3] <= 'F')))
                        {
                            try{
                                $buffer .= chr(intval(substr($s, $i, 4),16));
                                $i += 4;
                                continue;
                                
                            } catch (Exception $ex) {
                                throw new Exception("Invalid hex notation in the specified printer commands at index: ".$i);
                            }
                                
                            
                        }
                        else
                        {
                            try{
                                
                                $buffer .= chr(intval(substr($s, $i, 3),16));
                                $i += 3;
                                continue;
                                
                            } catch (Exception $ex) {
                                throw new ArgumentException("Invalid hex notation in the specified printer commands at index: ".$i);
                            }
                        }
                    }
                }
            }

            $buffer .= substr($s, $i, 1);
            
            $i++;
        }

        return $buffer;
        
    }
    
    public static function intToArray($i){
        return pack('C4',
            ($i >>  0) & 0xFF,
            ($i >>  8) & 0xFF,
            ($i >> 16) & 0xFF,
            ($i >> 24) & 0xFF
         );
    }
        
    public static function strleft($s1, $s2) {
	return substr($s1, 0, strpos($s1, $s2));
    }
    
    public static function strContains($s1, $s2){
        return (strpos($s1, $s2) !== false);
    }
    
    public static function strEndsWith($s1, $s2)
    {
        return substr($s1, -strlen($s2)) === $s2;
    }
    
    public static function strStartsWith($s1, $s2)
    {
        return substr($s1, 0, strlen($s2)) === $s2;
    }
    
}

/**
 * Specifies information about the print job to be processed at the client side.
 */
class ClientPrintJob{
    
    /**
     * Gets or sets the ClientPrinter object. Default is NULL.
     * The ClientPrinter object refers to the kind of printer that the client machine has attached or can reach.
     * - Use a DefaultPrinter object for using the default printer installed in the client machine.
     * - Use a InstalledPrinter object for using a printer installed in the client machine with an associated Windows driver.
     * - Use a ParallelPortPrinter object for using a printer which is connected through a parallel port in the client machine.
     * - Use a SerialPortPrinter object for using a printer which is connected through a serial port in the client machine.
     * - Use a NetworkPrinter object for using a Network IP/Ethernet printer which can be reached from the client machine.
     * @var ClientPrinter 
     */
    public $clientPrinter = null;
    /**
     * Gets or sets the printer's commands in text plain format. Default is an empty string.
     * @var string 
     */
    public $printerCommands = '';
    /**
     * Gets or sets the num of copies for Printer Commands. Default is 1.
     * Most Printer Command Languages already provide commands for printing copies. 
     * Always use that command instead of this property. 
     * Refer to the printer command language manual or specification for further details.
     * @var integer 
     */
    public $printerCommandsCopies = 1;
    /**
     * Gets or sets whether the printer commands have chars expressed in hexadecimal notation. Default is false.
     * The string set to the $printerCommands property can contain chars expressed in hexadecimal notation.
     * Many printer languages have commands which are represented by non-printable chars and to express these commands 
     * in a string could require many concatenations and hence be not so readable.
     * By using hex notation, you can make it simple and elegant. Here is an example: if you need to encode ASCII 27 (escape), 
     * then you can represent it as 0x27.        
     * @var boolean 
     */
    public $formatHexValues = false;
    /**
     * Gets or sets the PrintFile object to be printed at the client side. Default is NULL.
     * @var PrintFile 
     */
    public $printFile = null;
    /**
     * Gets or sets an array of PrintFile objects to be printed at the client side. Default is NULL.
     * @var array 
     */
    public $printFileGroup = null;
    
    
    /**
     * Sends this ClientPrintJob object to the client for further processing.
     * The ClientPrintJob object will be processed by the WCPP installed at the client machine.
     * @return string A string representing a ClientPrintJob object.
     */
    public function sendToClient(){
        
        $cpjHeader = chr(99).chr(112).chr(106).chr(2);
        
        $buffer = '';
        
        if (!Utils::isNullOrEmptyString($this->printerCommands)){
            if ($this->printerCommandsCopies > 1){
                $buffer .= 'PCC='.$this->printerCommandsCopies.Utils::SER_SEP;
            }
            if($this->formatHexValues){
                $buffer .= Utils::formatHexValues ($this->printerCommands);
            } else {
                $buffer .= $this->printerCommands;
            }
        } else if (isset ($this->printFile)){
            $buffer = $this->printFile->serialize();
        } else if (isset ($this->printFileGroup)){
            $buffer = 'wcpPFG:';
            $zip = new ZipArchive;
            $cacheFileName = (Utils::strEndsWith(WebClientPrint::$wcpCacheFolder, '/')?WebClientPrint::$wcpCacheFolder:WebClientPrint::$wcpCacheFolder.'/').'PFG'.uniqid().'.zip';
            $res = $zip->open($cacheFileName, ZipArchive::CREATE);
            if ($res === TRUE) {
                foreach ($this->printFileGroup as $printFile) {
                    $file = $printFile->fileName;
                    if($printFile->copies > 1){
                        $pfc = 'PFC='.$printFile->copies;
                        $file = substr($file, 0, strrpos($file, '.')).$pfc.substr($file, strrpos($file, '.'));
                    }  
                    if(is_a($printFile, 'PrintFilePDF')) $file .= '.wpdf';
                    if(is_a($printFile, 'PrintFileTXT')) $file .= '.wtxt';
                    
                    $zip->addFromString($file, $printFile->getFileContent());
                }
                $zip->close();
                $handle = fopen($cacheFileName, 'rb');
                $buffer .= fread($handle, filesize($cacheFileName));
                fclose($handle);
                unlink($cacheFileName);
            } else {
                $buffer='Creating PrintFileGroup failed. Cannot create zip file.';
            }
        }
        
        $arrIdx1 = Utils::intToArray(strlen($buffer));
        
        if (!isset($this->clientPrinter)){
            $this->clientPrinter = new UserSelectedPrinter();
        }    
        
        $buffer .= $this->clientPrinter->serialize();
        
        $arrIdx2 = Utils::intToArray(strlen($buffer));
        
        $lo = '';
        if(Utils::isNullOrEmptyString(WebClientPrint::$licenseOwner)){
            $lo = substr(uniqid(), 0, 8);
        }  else {
            $lo = 'php>'.base64_encode(WebClientPrint::$licenseOwner);
        }
        $lk = '';
        if(Utils::isNullOrEmptyString(WebClientPrint::$licenseKey)){
            $lk = substr(uniqid(), 0, 8);
        }  else {
            $lk = WebClientPrint::$licenseKey;
        }
        $buffer .= $lo.chr(124).$lk;
        
        return $cpjHeader.$arrIdx1.$arrIdx2.$buffer;
    }
    
}

/**
 * Specifies information about a group of ClientPrintJob objects to be processed at the client side.
 */
class ClientPrintJobGroup{
    
    /**
     * Gets or sets an array of ClientPrintJob objects to be processed at the client side. Default is NULL.
     * @var array 
     */
    public $clientPrintJobGroup = null;
    
    /**
     * Sends this ClientPrintJobGroup object to the client for further processing.
     * The ClientPrintJobGroup object will be processed by the WCPP installed at the client machine.
     * @return string A string representing a ClientPrintJobGroup object.
     */
    public function sendToClient(){
        
        if (isset ($this->clientPrintJobGroup)){
            $groups = count($this->clientPrintJobGroup);
            
            $dataPartIndexes = Utils::intToArray($groups);
            
            $cpjgHeader = chr(99).chr(112).chr(106).chr(103).chr(2);
        
            $buffer = '';
            
            $cpjBytesCount = 0;
            
            foreach ($this->clientPrintJobGroup as $cpj) {
                $cpjBuffer = '';
                
                if (!Utils::isNullOrEmptyString($cpj->printerCommands)){
                    if ($cpj->printerCommandsCopies > 1){
                        $cpjBuffer .= 'PCC='.$cpj->printerCommandsCopies.Utils::SER_SEP;
                    }
                    if($cpj->formatHexValues){
                        $cpjBuffer .= Utils::formatHexValues ($cpj->printerCommands);
                    } else {
                        $cpjBuffer .= $cpj->printerCommands;
                    }
                } else if (isset ($cpj->printFile)){
                    $cpjBuffer = $cpj->printFile->serialize();
                } else if (isset ($cpj->printFileGroup)){
                    $cpjBuffer = 'wcpPFG:';
                    $zip = new ZipArchive;
                    $cacheFileName = (Utils::strEndsWith(WebClientPrint::$wcpCacheFolder, '/')?WebClientPrint::$wcpCacheFolder:WebClientPrint::$wcpCacheFolder.'/').'PFG'.uniqid().'.zip';
                    $res = $zip->open($cacheFileName, ZipArchive::CREATE);
                    if ($res === TRUE) {
                        foreach ($cpj->printFileGroup as $printFile) {
                            $file = $printFile->fileName;
                            if($printFile->copies > 1){
                                $pfc = 'PFC='.$printFile->copies;
                                $file = substr($file, 0, strrpos($file, '.')).$pfc.substr($file, strrpos($file, '.'));
                            }
                            if(is_a($printFile, 'PrintFilePDF')) $file .= '.wpdf';
                            if(is_a($printFile, 'PrintFileTXT')) $file .= '.wtxt';
                    
                            $zip->addFromString($file, $printFile->getFileContent());
                        }
                        $zip->close();
                        $handle = fopen($cacheFileName, 'rb');
                        $cpjBuffer .= fread($handle, filesize($cacheFileName));
                        fclose($handle);
                        unlink($cacheFileName);
                    } else {
                        $cpjBuffer='Creating PrintFileGroup failed. Cannot create zip file.';
                    }
                }

                $arrIdx1 = Utils::intToArray(strlen($cpjBuffer));

                if (!isset($cpj->clientPrinter)){
                    $cpj->clientPrinter = new UserSelectedPrinter();
                }    

                $cpjBuffer .= $cpj->clientPrinter->serialize();
                    
                $cpjBytesCount += strlen($arrIdx1.$cpjBuffer);
 
                $dataPartIndexes .= Utils::intToArray($cpjBytesCount);
 
                $buffer .= $arrIdx1.$cpjBuffer;
            }
                    
            
            $lo = '';
            if(Utils::isNullOrEmptyString(WebClientPrint::$licenseOwner)){
                $lo = substr(uniqid(), 0, 8);
            }  else {
                $lo = 'php>'.base64_encode(WebClientPrint::$licenseOwner);
            }
            $lk = '';
            if(Utils::isNullOrEmptyString(WebClientPrint::$licenseKey)){
                $lk = substr(uniqid(), 0, 8);
            }  else {
                $lk = WebClientPrint::$licenseKey;
            }
            $buffer .= $lo.chr(124).$lk;

            return $cpjgHeader.$dataPartIndexes.$buffer;    
        
        
        } else {
            
            return NULL;
        }
            
        
    }
}