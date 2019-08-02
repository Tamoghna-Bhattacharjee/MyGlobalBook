<?php

//infinite scrolling system 
include('../connect.php');
include('../classes/User.php');
include('../classes/Post.php');

$limit = 10; // number of post loaded first

$post = new Post($conn , $_REQUEST['username']);
$post->loadPost($_REQUEST , $limit);
?>