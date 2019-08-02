<?php
include('includes/connect.php');

include('includes/classes/User.php');
include('includes/classes/Post.php');
include('includes/classes/Comment.php');
include('includes/header2.php');

if(!$_GET['post_id'])
    header('Location: home_page.php');
$post_id = $_GET['post_id'];

$comment_obj = new Comment($conn , $post_id);
if(isset($_POST['submit_comment' . $post_id]))
{
    $commnet_body = trim(strip_tags($_POST['comment'.$post_id]));
    if(str_replace(" ", "" , $commnet_body) == "")
    {
        $noCommentError = "<div class='alert alert-warning mt-1'>Please share your comment !!<span class='close' data-dismiss='alert'>&times;</span></div>";
    }
    else
    {
        $comment_obj->submitComment($commnet_body , $user_info['username']);
    }
}
    
?>
<body class="bg-light">
<?php echo $noCommentError;?>
<form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF'])."?post_id=".$post_id;?>' method='post' class='px-2'>
    <div class='form-group'>
        <textarea class='form-control mt-3' placeholder='comment here' rows='2' name='comment<?php echo $post_id;?>'></textarea>
    </div>
    <button class='btn btn-success btn-sm' type='submit' name='submit_comment<?php echo $post_id?>'>Comment</button>
    <hr>
</form>

<!--LOAD COMMENT-->
<?php
$comment_obj->loadComment();
?>
<!--LOAD COMMENT-->

<?php
include('includes/footer.php');
?>