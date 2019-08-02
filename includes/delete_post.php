<?php

include('connect.php');

if(isset($_POST['result']) && isset($_GET['post_id']))
{
    $post_id = $_GET['post_id'];
    
    $delete_query_result = mysqli_query($conn , "UPDATE posts SET deleted = 'yes' WHERE id='$post_id'");
    //update the num_posts from users
}

?>