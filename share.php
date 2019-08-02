<?php
include('includes/connect.php');

include('includes/classes/User.php');
include('includes/header2.php');
?>

<?php
//get post id
if(!$_GET['post_id'])
    header('Location: home_page.php');
$post_id = $_GET['post_id'];

if(isset($_POST['yes']))
{
    $post_info = mysqli_fetch_assoc(mysqli_query($conn , "SELECT post_content FROM posts WHERE id='$post_id'"));
    $post_content = $post_info['post_content'];
    $share_dateTime = date('Y-m-d H:i:s');
    
    $insert_query_result = mysqli_query($conn , "INSERT INTO posts (id , post_content , posted_by , posted_to , posting_date , user_closed , 	deleted , likes) VALUES (NULL , '$post_content' , '$userLoggedIn' , 'none' , '$share_dateTime' , 'no' , 'no' , '0')");//put another column of share in database
    if($insert_query_result)
    {
        $success_message = "<div class='alert alert-success mt-1'>The Post is shared into your timeline.<span class='close' data-dismiss='alert'>&times;</span></div>";
    }
}
?>
<body style='background: #fff;' id='share-1'>
    <span class="text-success share-header">Do you want to share this post?</span>
    <form action="share.php?post_id=<?php echo $post_id?>" method="post" class="p-2">
        <button class="btn btn-info btn-block mt-1" name='yes'><span class="glyphicon glyphicon-share">&nbsp;</span>Share</button>
        <?php echo $success_message;?>
    </form>
    
<?php
include('includes/footer.php');
?>






















