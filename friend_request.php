<?php
include('includes/header.php');

$userLoggedIn_obj = new User($conn , $userLoggedIn);
$friendRequest_query_result = mysqli_query($conn , "SELECT * FROM friend_request WHERE user_to='$userLoggedIn'");

if(isset($_GET['request']) && isset($_GET['name']))
{
    $request = $_GET['request'];
    $request_sender = $_GET['name'];
    
    if($request == 'accept')
        $message = "<div class='alert alert-primary mt-1'>Friend request from $request_sender is accepted!!<span class='close' data-dismiss='alert'>&times;</span></div>";
    
    if($request == 'decline')
        $message = "<div class='alert alert-warning mt-1'>Friend request from $request_sender is declined!!<span class='close' data-dismiss='alert'>&times;</span></div>";
}
    
?>

<div class="container-fluid main-content">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10 friend-request">
            <?php echo $message;?>
            <?php
            if(mysqli_num_rows($friendRequest_query_result) > 0)
            {
            ?>
            <h3 id='request-present-h' class="request-h">You have following friend request!!</h3><hr>
            
            <?php
                while($row = mysqli_fetch_assoc($friendRequest_query_result))
                {
                    $request_id = $row['id'];
                    $user_from = $row['user_from'];
                    $user_from_obj = new User($conn , $user_from);
                    $user_from_detail = $user_from_obj->First_Last_Name_Profilepicture();
                    $user_from_fullName = $user_from_detail['first_name'] . " " . $user_from_detail['last_name'];
                    $user_from_profilePic = $user_from_detail['profile_pic'];
            ?>
            
            <div class="request-container my-4 p-3 d-flex flex-wrap">
                <div class="flex-md-grow-1 flex-sm-grow-0">
                    <a href="<?php echo $user_from;?>">
                        <img src="<?php echo $user_from_profilePic;?>" alt="profile picture" class="img-fluid rounded-circle" width="50px">
                    </a>
                    <a href="<?php echo $user_from;?>" class='text-success ml-3' style='text-decoration: none'>
                        <?php echo $user_from_fullName;?>
                    </a>
                    <span class="line-completer">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sent a friend request!!</span>
                </div>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post" class="d-sm-flex form-inline">
                    <button class="btn btn-md btn-success mx-3 my-2" name="accept<?php echo $request_id?>">Accept</button>
                    <button class="btn btn-md btn-danger my-2 mx-3" name="decline<?php echo $request_id?>">decline</button>
                </form>
            </div>
            
            <?php 
                
                if(isset($_POST['accept' . $request_id]))
                {
                    $userLoggedIn_obj->acceptRequest($user_from);

                    header('Location: friend_request.php?request=accept&name=' . htmlspecialchars($user_from_fullName));
                }
                if(isset($_POST['decline' . $request_id]))
                {
                    $userLoggedIn_obj->deleteRequest($user_from);

                    header('Location: friend_request.php?request=decline&name=' . htmlspecialchars($user_from_fullName));
                }
            
            
            }
            }else {?><!--end of while loop-->       
                 
            <h3 id='request-absent-h' class="request-h">You don't have any friend request!!</h3>
            
            <?php }?>
        </div>
        <div class="col-md-1"></div>
    </div>
</div>


<?php include('includes/footer2.php');?> 
</body>

</html>
   
<?php
mysqli_close($conn);
?> 