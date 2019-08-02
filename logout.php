<?php
session_start();
if(!$_SESSION)
    header('Location: index.php');

$fullName = $_SESSION['fname']. " " . $_SESSION['lname'];
?>


<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title></title>

    <!--BOOTSTRAP CSS CDN-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <!--ORIGINAL BOOTSTRAP CSS-->
    <link rel="stylesheet" href="Bootstrap-file/css/bootstrap.min.css">
    
    <!--JQUERY UI CSS-->
    <link rel="stylesheet" href="JS/jquery-ui-1.12.1/jquery-ui.min.css">
    
    <!--LOADING CUSTOM STYLESHEET-->
    <link rel="stylesheet" href="CSS/glyphicon.css">
    <link rel="stylesheet" href="CSS/index.css">
    
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <!-- content -->
    <div class="content">
        <h1 id="brand" class="pb-4">MyGlobalBook</h1>
        <div class="container px-3">
            <div id="dialog" style="text-align: center;font-size: 36px;"><!--form canvas-->
              <h2 style="font-size: 40px; color: white;">Congrats</h2>
                <p class="px-5 pt-3 pb-5 text-success" style="font-size: 36px; font-weight: 500; font-style: italic;">
                    Hey you'r successfully logged out from MyGlobalBook Community.Enjoy and connect with your friends all over the world.Go and <a href="index.php" style="color: rgb(255, 235, 0);"> login again</a>
                </p>
            </div>
        </div>
    </div>

    <!-- particles.js container -->
    <div id="particles-js"></div>
    
    <?php include('includes/session_destroy.php'); ?>
     
         
    <!--JQUERY-->

    <!-- FIRST LOAD JQUERY CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <!--SECOND LOAD THE LOCAL VERSION IF JQUERY CDN FAILS-->
    <script>
        window.jQuery || document.write('<script src="JS/jquery-3.3.1.min.js"><\/script>');
    </script>

    <!--JQUERY-->
    <!--BOOTSTRAP CDN-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) CDN-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="Bootstrap-file/js/bootstrap.min.js"></script>

    <!--JQUERY UI LOADING-->
    <script src="JS/jquery-ui-1.12.1/jquery-ui.min.js"></script>

    <script src="JS/particles.min.js"></script>
    <script src="JS/app.js"></script>
</body>

</html>
