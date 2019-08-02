<?php

include('../connect.php');
include('../classes/User.php');
include('../classes/Post.php');

if(isset($_REQUEST['post_body']))
{
    $post_body = mysqli_real_escape_string($conn , trim(strip_tags($_REQUEST['post_body'])));
    $post_to = $_REQUEST['user_to'];
    $post_from = $_REQUEST['user_from'];
    
    if( (str_replace(" " , "" , $post_body) != "") && $post_body != "" )
    {
        $post_obj = new Post($conn , $post_from);
        $post_obj->submitPost($post_body , $post_to);
    } 
}

?>