
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>



    <title><?php echo $title; ?></title>
    

    <!--[if lt IE 9]>
    	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />   	
    <!--[if IE 6]>    
    	<link href="https://raw.github.com/empowering-communities/Bootstrap-IE6/master/ie6.min.css" rel="stylesheet">
    <![endif]-->
    
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

        <?php echo isset($style)?$style:''; ?>
      
      </style>

      <?php echo isset($head)?$head:''; ?>
      
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
                <a href="//www.neodynamic.com/products/printing/raw-data/aspnet-mvc/download/" target="_blank" class="navbar-brand">WebClientPrint <span class="round white">4.0</span> for PHP</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="Index.php">Home</a></li>
                    <li><a href="Samples.php">Samples</a></li>
                    <li><a href="PrintersInfo.php">Printers</a></li>
                    <li><a href="DemoPrintFile.php">Print Files</a></li>
                    <li><a href="DemoPrintFilePDF.php">Print PDF</a></li>
                    <li><a href="DemoPrintCommands.php">Print RAW Commands</a></li>
                    <li><a href="//www.neodynamic.com/products/printing/raw-data/php/articles" target="_blank" >Articles</a></li>
                    <li><a href="//www.neodynamic.com/products/printing/raw-data/php/" target="_blank" >About</a></li>

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
        <div>
            <?php echo isset($content)?$content:''; ?>

        </div>
        
        <footer>
            
            <br /><br /><br /><br /><hr />
            <p><a href="//www.neodynamic.com/products/printing/raw-data/php/" target="_blank">WebClientPrint for PHP</a>
                &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
                <i class="icon-user"></i> <a href="http://www.neodynamic.com/spport" target="_blank">Contact Tech Support</a>
            </p>
            <p>&copy; Copyright <?php echo date('Y'); ?> - Neodynamic SRL<br />All rights reserved.</p>

        </footer>
    </div>

    
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <?php echo isset($script)?$script:''; ?>
      
</body>
</html>
