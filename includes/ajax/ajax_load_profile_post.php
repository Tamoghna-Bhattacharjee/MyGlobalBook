<?php

//infinite scrolling system 
include('../connect.php');
include('../classes/User.php');
include('../classes/Post.php');

$limit = 10; // number of post loaded first

$post = new Post($conn , $_REQUEST['userLoggedIn']);
$post->loadProfilePost($_REQUEST , $limit);
?>