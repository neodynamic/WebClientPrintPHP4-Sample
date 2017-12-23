<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - WebClientPrint 4.0 for PHP</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


    @yield('styles')

    <style type="text/css">
        .navbar-custom {
            background-color:#2196F3;
            color:#ffffff;
            border-radius:0;
        }
        
        .navbar-custom .navbar-nav > li > a {
            color:#fff;
        }
        .navbar-custom .navbar-nav > .active > a, .navbar-nav > .active > a:hover, .navbar-nav > .active > a:focus {
            color: #ffffff;
            background-color:transparent;
        }
            
        .navbar-custom .navbar-nav > li > a:hover, .nav > li > a:focus {
            text-decoration: none;
            background-color:#acdaee;
        }
            
        .navbar-custom .navbar-brand {
            color:#eeeeee;
        }
        .navbar-custom .navbar-toggle {
            background-color:#eeeeee;
        }
        .navbar-custom .icon-bar {
            background-color:#acdaee;
        }


        .round.white {
            background-color: #fff; color:#2196F3
        }
        .round {
            display: inline-block;
            height: 32px;
            width: 32px;
            line-height: 32px;
            -moz-border-radius: 16px;
            border-radius: 16px;
            background-color: #222;
            color: #FFF;
            text-align: center;
        }
    </style>


</head>
<body>
    
    <div class="navbar navbar-default navbar-custom navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <img alt="Neodynamic" src="//www.neodynamic.com/images/webclientprinticon.png" >
                </a>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="//www.neodynamic.com/products/printing/raw-data/php/download/" target="_blank" class="navbar-brand">WebClientPrint <span class="round white">4.0</span> for PHP - Laravel</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="{{action('HomeController@index')}}">Home</a></li>
                    <li><a href="{{action('HomeController@samples')}}">Samples</a></li>
                    <li><a href="{{action('HomeController@printersinfo')}}">Printers</a></li>
                    <li><a href="{{action('DemoPrintFileController@index')}}">Print Files</a></li>
                    <li><a href="{{action('DemoPrintFilePDFController@index')}}">Print PDF</a></li>
                    <li><a href="{{action('DemoPrintCommandsController@index')}}">Print RAW Commands</a></li>
                    <li><a href="//www.neodynamic.com/products/printing/raw-data/php/articles" target="_blank">Articles</a></li>
                    <li><a href="//www.neodynamic.com/products/printing/raw-data/php/" target="_blank">About</a></li>
                </ul>
            </div>
           
            <div class="pull-right">
                <a class="btn btn-primary" href="//www.neodynamic.com/products/printing/raw-data/php/download/" target="_blank"><i class="glyphicon glyphicon-download-alt"></i> Download SDK for PHP</a>
            </div>
            <hr />
             <p >
            <small><em>Cross-browser Client-side Printing Solution for Windows, Linux, Mac & Raspberry Pi</em></small>
            </p>
        </div>
    </div>
    <div class="container body-content">
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        <br />
        @yield('body')

        <footer>
            
            <br /><br /><br /><br /><hr />
            <p><a href="//www.neodynamic.com/products/printing/raw-data/php/" target="_blank">WebClientPrint for PHP</a>
                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                <i class="icon-user"></i> <a href="http://www.neodynamic.com/spport" target="_blank">Contact Tech Support</a>
            </p>
            <p>&copy; Copyright {{date('Y')}} - Neodynamic SRL<br />All rights reserved.</p>

        </footer>
    </div>


    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    @yield('scripts')

</body>
</html>
