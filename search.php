<?php
include('includes/header.php');

if(!isset($_GET['search']) || (isset($_GET['search']) && str_replace(" " , "" , $_GET['search']) === ""))
    header('Location: home_page.php');

if(isset($_GET['search']) && str_replace(" " , "" , $_GET['search']) !== "")
{
    $search_str = $_GET['search'];
    $name = explode( " " , $search_str );
    
    $userLoggedIn_obj = new User($conn , $userLoggedIn);
    
    if(strpos($search_str , "_"))//if user searches via username
    {
        $result = mysqli_query($conn , "SELECT * FROM users WHERE username LIKE '$search_str%' AND user_closed = 'no'");
    }
    else if(count($name) == 3)
    {
        $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' AND last_name LIKE '%$name[2]%') AND user_closed = 'no'");
    }
    else if(count($name) == 2)
    {
        $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' OR last_name LIKE '%$name[1]%') AND user_closed = 'no'");
    }
    else
    {
        $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' OR last_name LIKE '%$name[0]%') AND user_closed = 'no'");
    }
}
?>
<div class="container-fluid" style="margin: 110px auto 0;">
    <div class="row">
        <div class="col-md-1 col-sm-12"></div>
        <div class="col-md-10 col-sm-12" style="background: #fff; border: solid 1px black;">
            <?php
            if(mysqli_num_rows($result) > 0){
            ?>
            <h4 id="search-found-h" class="search-h">You have following search results.</h4><hr />
            <?php
                while($row = mysqli_fetch_assoc($result))
                {
                    if($row['username'] != $userLoggedIn)
                    {
                        $id = $row['id'];
                        $username = $row['username'];
                        $fullName = $row['first_name'] . " " . $row['last_name'];
                        $profile_pic = $row['profile_pic'];
                        $num_mutualFriend = $userLoggedIn_obj->getNumMutualFriends($username);
            ?>
            <div id="search-results-details" class="my-1 px-md-5 px-sm-3 py-3 d-flex flex-wrap">
                <div class="flex-md-grow-1 flex-sm-grow-0">
                    <a href="<?php echo $username;?>" style='text-decoration: none'>
                        <img src="<?php echo $profile_pic;?>" alt="profile picture" class="rounded-circle img-fluid" width="50px">
                        <span class='text-success ml-3'><?php echo $fullName;?></span>
                    </a>
                    <span class="line-completer">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;have <span class="material-icons people-icon-px-18">people</span> <?php echo $num_mutualFriend;?> mutual friend
                        <?php if($num_mutualFriend <= 1) echo '';else echo 's';?>
                    </span>
                </div>
                <form action="" method="post" class="d-sm-flex form-inline">
                    <?php
                        
                    if($userLoggedIn_obj->isFriend($username))
                        echo "<button class='btn btn-light btn-block' name='unfriend$id'><span class='glyphicon glyphicon-remove'></span>&nbsp;Unfriend</button>";
                    elseif ($userLoggedIn_obj->didSend_friendRequest($username))
                        echo "<button class='btn btn-secondary btn-block'>Request Send</button>";
                    elseif($userLoggedIn_obj->didReceive_friendRequest($username))
                    {
                        echo "<button class='btn btn-md btn-success mx-2' name='accept_request$id'>Accept Request</button>";
                        echo "<button class='btn btn-md btn-danger' name='delete_request$id'>Decline Request</button>";
                    }
                    else
                        echo "<button class='btn btn-dark btn-block' name='send_request$id'><span class='glyphicon glyphicon-plus'></span>&nbsp;Friend</button>";
                        
                    ?>
                </form>
            </div>
            <?php
            if(isset($_POST['unfriend' . $id]))
            {
                $userLoggedIn_obj->removeFriend($username);
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            }
                        
            if(isset($_POST['accept_request' . $id]))
            {
                $userLoggedIn_obj->acceptRequest($username);
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            }
                        
            if(isset($_POST['delete_request' . $id]))
            {
                $userLoggedIn_obj->deleteRequest($username);
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            }
                        
            if(isset($_POST['send_request' . $id]))
            {
                $userLoggedIn_obj->sendRequest($username);
                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            }
            ?>
            <?php }}} else{ ?>
            <h3 id='search-absent-h' class="search-h">No result found!! Please try again...</h3>
            <?php } ?>
        </div>
        <div class="col-md-1 col-sm-12"></div>
    </div>
</div>



<?php include('includes/footer2.php');?> 
</body>

</html>
   
<?php
mysqli_close($conn);
?> 