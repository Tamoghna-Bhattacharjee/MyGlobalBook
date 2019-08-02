<?php
include('includes/connect.php');

include('includes/classes/User.php');
include('includes/classes/Post.php');
include('includes/classes/Comment.php');
include('includes/header2.php');
?>
<body style='background: #fff;' id='like_unlike'>
<?php
//get post id
if(!$_GET['post_id'])
    header('Location: home_page.php');
$post_id = $_GET['post_id'];

//get post info
$post_info_query_result = mysqli_query($conn , "SELECT posted_by , likes FROM posts WHERE id = '$post_id'");
$post_info = mysqli_fetch_assoc($post_info_query_result);
$likes = $post_info['likes'];
$posted_by = $post_info['posted_by'];

$posted_byObj = new User($conn , $posted_by);//check userLoggedIn should be replaced by posted_by
$total_num_likes = $posted_byObj->getNumLikes();

//like button handling
if(isset($_POST['like']))
{
    $likes++;
    $total_num_likes++;
    $update_postTable = mysqli_query($conn , "UPDATE posts SET likes = '$likes' WHERE id = '$post_id'");
    $update_userTable = mysqli_query($conn , "UPDATE users SET num_likes = '$total_num_likes' WHERE username = '$posted_by'");
    $insert_likeTable = mysqli_query($conn , "INSERT INTO likes (id , username , post_id) VALUES (NULL , '$userLoggedIn' , '$post_id')");
}

//unlike handling
if(isset($_POST['unlike']))
{
    $likes--;
    if($likes < 0)
        $likes = 0;
    $total_num_likes--;
    
    if($total_num_likes < 0)
        $total_num_likes = 0;
    
    $update_postTable = mysqli_query($conn , "UPDATE posts SET likes = '$likes' WHERE id = '$post_id'");
    $update_userTable = mysqli_query($conn , "UPDATE users SET num_likes = '$total_num_likes' WHERE username = '$posted_by'");
    $insert_likeTable = mysqli_query($conn , "DELETE FROM likes WHERE post_id='$post_id'");
}


//posted_by query
//$posted_by_query = mysqli_query($conn , "SELECT * FROM users WHERE username = '$posted_by'");
//$posted_by_info = mysqli_fetch_assoc($posted_by_query);



//check if the user had already liked the post or not and based on show like or unlike
$check = mysqli_query($conn , "SELECT * FROM likes WHERE username = '$userLoggedIn' AND post_id = '$post_id'");

if(mysqli_num_rows($check) > 0 )
    echo "<form action='like.php?post_id=$post_id' method='post'>
            <button type='submit' name='unlike' class='btn' style='background: #fff;'><span class='post-footer'>Unlike ($likes likes)</span></button>  
          </form>";
else
    echo "<form action='like.php?post_id=$post_id' method='post'>
            <button type='submit' name='like' class='btn' style='background: #fff;'><span class='post-footer'>Like ($likes likes)</span></button>  
          </form>";




include('includes/footer.php');
?>