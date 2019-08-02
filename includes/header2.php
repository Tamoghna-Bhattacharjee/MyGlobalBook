<?php

session_start();
if(!$_SESSION)
    header('Location: index.php');

$user_email = $_SESSION['email'];

$user_detail_query = "SELECT * FROM users WHERE email = '$user_email'";
$user_detail_result = mysqli_query($conn , $user_detail_query);

$user_info = mysqli_fetch_assoc($user_detail_result);
$userLoggedIn = $user_info['username'];
?>
<!DOCTYPE html>
<html lang="" class="bg-light">

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
    <link rel="icon" type="image" href="images/brand_icon.jpg">
    <link rel="stylesheet" href="CSS/glyphicon.css">
    <link rel="stylesheet" href="CSS/style.css">
    
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>


