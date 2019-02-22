<?php 
  ob_start();
  session_start();
  
  include 'WebClientPrint.php';
  use Neodynamic\SDK\Web\WebClientPrint;
  use Neodynamic\SDK\Web\Utils;

  $title = 'WebClientPrint 4.0 for PHP - Printers Info';
  
  $style = '.glyphicon-refresh-animate {
            -animation: spin .7s infinite linear;
            -webkit-animation: spin2 .7s infinite linear;
        }

        @-webkit-keyframes spin2 {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: scale(1) rotate(0deg);
            }

            to {
                transform: scale(1) rotate(360deg);
            }
        }';
  
?>



<div class="container">
    <div class="row">
        
         <h2>Getting Printers Info</h2>

    <p>The following section shows you how to get useful info fron the client printers. Please click in the button <strong>Get Printers Info</strong> below.</p>

    <hr />
                
    <div class="container">
        <div class="row">

            <div class="col-md-3">
                <a onclick="javascript:jsWebClientPrint.getPrintersInfo(); $('#spinner').css('visibility', 'visible');" class="btn btn-success">Get Printers Info...</a>
            </div>
            <div class="col-md-9">
                <h3 id="spinner" style="visibility: hidden"><span class="label label-info"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>Please wait a few seconds...</span></h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <label for="lstPrinters">Printers:</label>
                <select name="lstPrinters" id="lstPrinters" onchange="showSelectedPrinterInfo();" class="form-control"></select>
            </div>
            <div class="col-md-4">
                <label for="lstPrinterTrays">Supported Trays:</label>
                <select name="lstPrinterTrays" id="lstPrinterTrays" class="form-control"></select>
            </div>
            <div class="col-md-4">
                <label for="lstPrinterPapers">Supported Papers:</label>
                <select name="lstPrinterPapers" id="lstPrinterPapers" class="form-control"></select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <br />
                <br />
                <div class="input-group">
                    <span class="input-group-addon" id="portName">Port Name:</span>
                    <input type="text" id="txtPortName" class="form-control" placeholder=" " aria-describedby="portName">
                </div>
                <br />
                <div class="input-group">
                    <span class="input-group-addon" id="hRes">Horiz Res (dpi):</span>
                    <input type="text" id="txtHRes" class="form-control" placeholder=" " aria-describedby="hRes">
                </div>
                <br />
                <div class="input-group">
                    <span class="input-group-addon" id="vRes">Vert Res (dpi):</span>
                    <input type="text" id="txtVRes" class="form-control" placeholder=" " aria-describedby="vRes">
                </div>
            </div>
            <div class="col-md-4">
                <br />
                <br />
                <h4><span id="isConnected" class="label label-default glyphicon glyphicon-minus">&nbsp;</span> Is Connected?</h4>
                <hr />
                <h4><span id="isDefault" class="label label-default glyphicon glyphicon-minus">&nbsp;</span> Is Default?</h4>
                <hr />
                <h4><span id="isBIDIEnabled" class="label label-default glyphicon glyphicon-minus">&nbsp;</span> Is BIDI Enabled?</h4>
            </div>
            <div class="col-md-4">
                <br />
                <br />
                <h4><span id="isLocal" class="label label-default glyphicon glyphicon-minus">&nbsp;</span> Is Local?</h4>
                <hr />
                <h4><span id="isNetwork" class="label label-default glyphicon glyphicon-minus">&nbsp;</span> Is Network?</h4>
                <hr />
                <h4><span id="isShared" class="label label-default glyphicon glyphicon-minus">&nbsp;</span> Is Shared?</h4>
            </div>
        </div>

    </div>


    <script type="text/javascript">

        var clientPrinters = null;

        var wcppGetPrintersTimeout_ms = 10000; //10 sec
        var wcppGetPrintersTimeoutStep_ms = 500; //0.5 sec
        function wcpGetPrintersOnSuccess() {
            $('#spinner').css('visibility', 'hidden');
            // Display client installed printers
            if (arguments[0].length > 0) {
                if (JSON) {
                    try {
                        clientPrinters = JSON.parse(arguments[0]);
                        if (clientPrinters.error) {
                            alert(clientPrinters.error)
                        } else {
                            var options = '';
                            for (var i = 0; i < clientPrinters.length; i++) {
                                options += '<option>' + clientPrinters[i].name + '</option>';
                            }
                            $('#lstPrinters').html(options);
                            $('#lstPrinters').focus();

                            showSelectedPrinterInfo();
                        }
                    } catch (e) {
                        alert(e.message)
                    }
                }


            } else {
                alert("No printers are installed in your system.");
            }
        }
        function wcpGetPrintersOnFailure() {
            $('#spinner').css('visibility', 'hidden');
            // Do something if printers cannot be got from the client
            alert("No printers are installed in your system.");
        }


        function showSelectedPrinterInfo() {
            // get selected printer index
            var idx = $("#lstPrinters")[0].selectedIndex;
            // get supported trays
            var options = '';
            if (clientPrinters[idx].trays) {
                for (var i = 0; i < clientPrinters[idx].trays.length; i++) {
                    options += '<option>' + clientPrinters[idx].trays[i] + '</option>';
                }
            }
            $('#lstPrinterTrays').html(options);
            // get supported papers
            options = '';
            if (clientPrinters[idx].papers) {
                for (var i = 0; i < clientPrinters[idx].papers.length; i++) {
                    options += '<option>' + clientPrinters[idx].papers[i] + '</option>';
                }
            }
            $('#lstPrinterPapers').html(options);
            // additional info...
            $('#txtPortName').val(clientPrinters[idx].portName);
            $('#txtHRes').val(clientPrinters[idx].hRes);
            $('#txtVRes').val(clientPrinters[idx].vRes);
            $('#isConnected').attr('class', (clientPrinters[idx].isConnected ? 'label label-info glyphicon glyphicon-ok' : 'label label-danger glyphicon glyphicon-remove'));
            $('#isDefault').attr('class', (clientPrinters[idx].isDefault ? 'label label-info glyphicon glyphicon-ok' : 'label label-danger glyphicon glyphicon-remove'));
            $('#isBIDIEnabled').attr('class', (clientPrinters[idx].isBIDIEnabled ? 'label label-info glyphicon glyphicon-ok' : 'label label-danger glyphicon glyphicon-remove'));
            $('#isLocal').attr('class', (clientPrinters[idx].isLocal ? 'label label-info glyphicon glyphicon-ok' : 'label label-danger glyphicon glyphicon-remove'));
            $('#isNetwork').attr('class', (clientPrinters[idx].isNetwork ? 'label label-info glyphicon glyphicon-ok' : 'label label-danger glyphicon glyphicon-remove'));
            $('#isShared').attr('class', (clientPrinters[idx].isShared ? 'label label-info glyphicon glyphicon-ok' : 'label label-danger glyphicon glyphicon-remove'));
            
        }

    </script>   
        
    </div>
</div>


<?php
  $content = ob_get_contents();
  ob_clean();
?>    


<?php
  
    //Get Absolute URL of this page
    $currentAbsoluteURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    $currentAbsoluteURL .= $_SERVER["SERVER_NAME"];
    if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
    {
        $currentAbsoluteURL .= ":".$_SERVER["SERVER_PORT"];
    } 
    $currentAbsoluteURL .= $_SERVER["REQUEST_URI"];
    
    //WebClientPrinController.php is at the same page level as WebClientPrint.php
    $webClientPrintControllerAbsoluteURL = substr($currentAbsoluteURL, 0, strrpos($currentAbsoluteURL, '/')).'/WebClientPrintController.php';
    
    //PrintersInfo.php is at the same page level as WebClientPrint.php
    $printersInfoAbsoluteURL = substr($currentAbsoluteURL, 0, strrpos($currentAbsoluteURL, '/')).'/PrintersInfo.php';
    
    //Specify the ABSOLUTE URL to the WebClientPrintController.php and to the file that will create the ClientPrintJob object
    echo WebClientPrint::createScript($webClientPrintControllerAbsoluteURL, $printersInfoAbsoluteURL, session_id());
?>


<?php
  $script = ob_get_contents();
  ob_clean();
  
  
  include("template.php");
?>

