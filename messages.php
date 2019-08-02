<?php
include('includes/header.php');

//$userLoggedIn_obj = new User($conn , $userLoggedIn);
//$loggedIn_detail = $userLoggedIn_obj->First_Last_Name_Profilepicture();
//$userLoggedIn_fullName = $loggedIn_detail['first_name'] . " " . $loggedIn_detail['last_name'];

$msg_obj = new Message($conn , $userLoggedIn);

if(isset($_GET['user_to']))
{
    $user_to = $_GET['user_to'];//if the page is visited for messeging a particular person
    if($user_to != 'new')
    {
        $user_to_query = mysqli_query($conn , "SELECT username FROM users WHERE username = '$user_to'");
        if(mysqli_num_rows($user_to_query) == 0 )
            header('Location: messages.php?user_to=new');
        else
            unset($user_to_query);
    }
}
else
{
    //if the page is visited from the top bar link or without GET query
    $user_to = $msg_obj->getMostRecentUser();
    
    if($user_to == false)
        $user_to = "new";
}

//redirect to home-page if user_to == userLoggedIn ** no haramigiri **
if($user_to == $userLoggedIn)
    header('Location: home_page.php');

if($user_to != "new")
{
    $user_to_obj = new User($conn , $user_to);
    $user_to_detail = $user_to_obj->First_Last_Name_Profilepicture();
    $user_to_fullName = $user_to_detail['first_name'] . " " . $user_to_detail['last_name'];
}

if(isset($_POST['send_message']))
{
    $message = trim(strip_tags(mysqli_real_escape_string($conn , $_POST['message_body'])));
    
    if( (str_replace(" " , "" , $message) != "") && ($message != "") )
    {
        $date = date('Y-m-d H:i:s');
        $msg_obj->sendMessage($user_to , $date , $message);
    }
}


?>

<div class="container-fluid main-content">
    <div class="row">
        <div class="col-1"></div>
        <div class="col-lg-3 col-md-3 col-sm-12">
           
            <div class="mini-detail">
                <img src="<?php echo $user_info['profile_pic']; ?>" alt="Profile Picture" class="img-fluid">
                <div id="user_detail">
                    <a href="<?php echo $user_info['username']; ?>" class="text-info" style="font-weight: 600; font-size: 18px; text-decoration: none;">
                        <?php echo $user_info['first_name']." ".$user_info['last_name']; ?>
                    </a>
                    <?php echo "<br>Posts:: ".$user_info['num_posts']."<br>"."Likes:: ".$user_info['num_likes']; ?>
                </div>
            </div><!--.mini-detail-->
            
            <div class="chaters-box mt-lg-3">
                <h4 style="text-align: center;">Conversations</h4>
                <div class="chaters-list">
                    <?php echo $msg_obj->getConversation(); ?>
                </div>
                <br>
                <div style="text-align: center;">
                    <a href="messages.php?user_to=new" style="text-decoration: none" class="text-info">New Message</a>
                </div>
            </div>
            
        </div>
        <div class="col-lg-7 col-md-7 col-sm-12 mt-sm-2 postfeed">
            <?php if($user_to != "new"){ ?>
                <h4 class="mt-lg-3 mt-md-2 mt-sm-0">
                   <span>You and </span>
                   <a href="<?php echo $user_to;?>" style="text-decoration: none;">
                       <?php echo $user_to_fullName;?>
                   </a>
                </h4><br /><hr />
            <?php } ?>
            
            <form action="#" method="post">

                <?php if($user_to != "new"){ ?>
                    <div class="load-post my-3 py-2 px-md-5 px-sm-2" id="scroll_message">
                        <?php echo $msg_obj->loadMessage($user_to);?>
                    </div><hr>
                    
                    <script>
                        var div = document.getElementById('scroll_message');
                        div.scrollTop = div.scrollHeight;
                    </script>
                <?php } ?>
                   
                <?php if($user_to == "new"){ ?>

                    <h4 class="mt-2" style="color: #a52a2a">Select the friend you would like to message.</h4>
                    <div class="form-group row mt-md-3">
                        <label for="friend" class="col-1 col-form-label"><h4 style="color: #e00b33;">TO: </h4></label>
                        <div class="col-10">
                            <input type="text" id="friend-liveSearch" class="form-control ml-2" autocomplete="off" name="friend_search" placeholder="Search your new chater...">
                        </div>
                    </div>
                    <div class="results"></div>

                <?php }else{ ?>
                <div class="form-group">
                    <div class="input-group">
                        <textarea name="message_body" type="text" class="form-control" placeholder="Write your message..." id="message_body" rows="1"></textarea>
                        
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary" name='send_message' id="send_message">
                                <span class="material-icons send-icon">send</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </form>
        </div>

    </div>
    <!--.row-->
</div><!--.container-fluid main-content--> 

  
<?php include('includes/footer2.php'); 
if($user_to == "new"){
?>


<script>
    $(function(){

       var userLoggedIn = "<?php echo $user_info['username'];?>";
 
       $('#friend-liveSearch').keyup(function(){
           var value = $(this).val();
           $.post('includes/ajax/ajax_friend_search.php' , {value: value , userLoggedIn: userLoggedIn} , function(data){
               $('.results').html(data);
           });
           
        }); 
    });
</script>



<?php } ?>
</body>

</html>
   
<?php
mysqli_close($conn);
?>     
