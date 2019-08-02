<?php
include('includes/connect.php');
include('includes/classes/User.php');
include('includes/classes/Post.php');
include('includes/classes/Message.php');

session_start();
if(!$_SESSION)
    header('Location: index.php');

$userLoggedIn = $_SESSION['username'];

$user_detail_query = "SELECT * FROM users WHERE username = '$userLoggedIn'";
$user_detail_result = mysqli_query($conn , $user_detail_query);

$user_info = mysqli_fetch_assoc($user_detail_result);
$userLoggedIn = $user_info['username'];

$msg_obj_header = new Message($conn , $userLoggedIn);
$num_unread_message = $msg_obj_header->getNum_unreadMessages();
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
    <link rel="icon" type="image" href="images/brand_icon.jpg">
    <link rel="stylesheet" href="CSS/glyphicon.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="CSS/style.css">
    
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
     <nav class="navbar navbar-expand-md navbar-light bg-info fixed-top">
         <div class="container-fluid">
             <div class="navbar-header">
                 <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar-collapse">
                     <span class="sr-only">Collapse button</span>
                     <span class="navbar-toggler-icon"></span>
                 </button>
                 <a href="home_page.php" class="navbar-brand"><span id="brand-title" class="ml-2">MyGlobalBook</span></a>
                 <?php 
                    echo '<a href="'.$user_info['username'].'" class="navbar-brand" title="go to your Timeline"><span class="mx-3" id="user">Hello\'&nbsp;<img src="'.$user_info['profile_pic'].'" class="rounded-circle img-fluid mb-1" width="30px" alt="profile pic">&nbsp;'.$user_info['first_name'].'</span></a>';
                 ?>
             </div><!--.navbar-header-->
             
             <div class="collapse navbar-collapse" id="navbar-collapse">
                <div class="form-group mr-auto" id="live-search">
                    <form action="search.php" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search anything..." autocomplete="off" name="search" id="live_search_field">
                            <div class="input-group-append"><button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-search"></span></button></div>
                        </div>
                    </form>
                    <div class="search_results mt-1" style="background: #fff;"></div>
                </div>
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item"><a href="home_page.php" class="nav-link"><span class="glyphicon glyphicon-home"></span></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" id="envelope-dropdown">
                            <span class="glyphicon glyphicon-envelope"></span>
                            <?php
                            if($num_unread_message > 0)
                                echo '<span class="badge" id="message_badge">'.$num_unread_message.'</span>';
                            ?>    
                        </a>
                        <ul class="dropdown-menu dropdown-menu-messages" style="width: 250px;">
                             <div class="dropdown_items"></div>
                             <div class="dropdown-divider"></div>
                             <li><a href="messages.php" class="dropdown-item">See all messages...</a></li>
                         </ul>
                    </li>
                    
                    <li class="nav-item"><a href="#" class="nav-link"><span class="glyphicon glyphicon-bell"></span></a></li>
                    <li class="nav-item"><a href="friend_request.php" class="nav-link"><span class="material-icons">people</span></a></li>
                    <li class="nav-item"><a href="settings.php" class="nav-link"><span class="glyphicon glyphicon-cog"></span></a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link"><span class="glyphicon glyphicon-log-out"></span></a></li>
                </ul>
             </div><!--.collapse .navbar-collapse-->
             
         </div><!--.container-fluid-->
         
     </nav>
     <!--<div class="dropDown_content"></div>
     <input type="hidden" id="dropDown_data_type" value="">-->
