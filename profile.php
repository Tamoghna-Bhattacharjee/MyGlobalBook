<?php
include('includes/header.php');

if(isset($_GET['profile_username']))
{
    $profile_username = strtolower($_GET['profile_username']);
    
    $user_detail_query_result = mysqli_query($conn , "SELECT * FROM users WHERE username='$profile_username' AND user_closed='no'");
    
    if(mysqli_num_rows($user_detail_query_result) == 0)
        header('Location: user_closed.php');
    else
        $user_detail = mysqli_fetch_assoc($user_detail_query_result);
    
    $num_friends = (substr_count($user_detail['friend_array'] , ",")) - 1;
    $logged_in_user_obj = new User($conn , $userLoggedIn);
    
    //unfriend button is pressed
    if(isset($_POST['unfriend']))
    {
        $logged_in_user_obj->removeFriend($profile_username);
        header('Location: #');
    }
        
    //add friend button pressed
    if(isset($_POST['send_request']))
    {
        $logged_in_user_obj->sendRequest($profile_username);
    }
    
    //accept button is pressed
    if(isset($_POST['accept_request']))
    {
        $logged_in_user_obj->acceptRequest($profile_username);
        header('Location: #');
    }
    
    //delete_request button
    if(isset($_POST['delete_request']))
    {
        $logged_in_user_obj->deleteRequest($profile_username);
    }
        
}
?>

<div class="row profileCanvas">
    <div class="col-2" id="profile-left">
        <div class="user-info">
            <img src="<?php echo $user_detail['profile_pic']; ?>" alt="Profile Picture" class="img-fluid rounded-circle" id="profile-pic">
            <div id="user_detail">
                <span class="name">
                        <?php echo $user_detail['first_name']." ".$user_detail['last_name']; ?>
                </span>
            </div>
        </div><!--.user-info-->
        <div class="behave">
            <p class="btn btn-info btn-block my-0" style="cursor: default;">Likes: <?php echo $user_detail['num_likes'];?></p>
            <p class="btn btn-warning btn-block my-0" style="cursor: default;">Posts: <?php echo $user_detail['num_posts'];?></p>
            <p class="btn btn-danger btn-block my-0" style="cursor: default;">Friends: <?php echo $num_friends;?></p>
            
            <?php
            if($profile_username != $userLoggedIn)
                if($logged_in_user_obj->isFriend($profile_username))
                    echo "<button class='btn btn-dark btn-block' type='button' data-toggle='modal' data-target='#message'><span class='glyphicon glyphicon-envelope'></span>&nbsp;Message</button>";
            ?>
            
            <form action="<?php echo $profile_username?>" method="post">
               
                <?php
                //echo $profile_username;
                if($profile_username != $userLoggedIn)
                {
                    echo "<button class='btn mutual-friend btn-block' name='unfriend'>Mutual friends: ". $logged_in_user_obj->getNumMutualFriends($profile_username) ."</button><br>";
                    
                    if($logged_in_user_obj->isFriend($profile_username))
                    {
                        echo "<button class='btn btn-light btn-block' name='unfriend'><span class='glyphicon glyphicon-remove'></span>&nbsp;Unfriend</button>";
                    }
                    else if($logged_in_user_obj->didSend_friendRequest($profile_username))
                    {
                        echo "<button class='btn btn-secondary btn-block' name=''>Request Send</button>";
                    }
                    else if($logged_in_user_obj->didReceive_friendRequest($profile_username))
                    {
                        echo "<button class='btn btn-success btn-block' name='accept_request'>Accept Request</button>";
                        echo "<button class='btn btn-warning btn-block' name='delete_request'>Decline Request</button>";
                    }
                    else
                        echo "<button class='btn btn-dark btn-block' name='send_request'><span class='glyphicon glyphicon-plus'></span>&nbsp;Friend</button>";
                }
                ?>
            </form>
                     
        <button class="btn btn-primary btn-block mt-2" type="button" data-toggle="modal" data-target="#post_modal">Post Something!!</button>
         
        </div>
        
    </div>
    <div class="col-2"></div>
    <div class="col-8">
        <div class="bg-light mt-2 mini-nav">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a href="#timeline" class="nav-link active" data-toggle="tab">Timeline</a></li>
            <li class="nav-item"><a href="#aboutMe" class="nav-link" data-toggle="tab">About Me</a></li>
            <li class="nav-item"><a href="#friends" class="nav-link" data-toggle="tab">Friends</a></li>
        </ul>
        </div>
        
        <div class="tab-content">
           <!--Timeline-->
            <div id="timeline" class="tab-pane fade show active">
                <div class="post_area postfeed px-4"></div>
                <img src="images/loading.gif" alt="Loading" id="loading">
            </div>
            <!--About Me-->
            <div id="aboutMe" class="tab-pane fade">
                hi
            </div>
            <!--message-->
            <div id="friends" class="tab-pane fade">
                hi
            </div>
        </div>
    </div>
</div><!-- .row-->

<!--modal window for newsfeed-->
<!--modal window form can not be submitted by php only... it need ajax-->
<div class="modal fade" id="post_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Post Something</h4>
                <button type="button" data-dismiss="modal" class="close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>This will appear on user's profile and your newsfeed for your friend to see!!</p>
                
                <form action="#" class="profile_post">
                    <div class="form-group">
                        <textarea name="post_body" class="form-control mt-3" id="new_post" placeholder="Share whatever is in your mind...."></textarea>
                        
                        <input type="hidden" name="user_from" value="<?php echo $userLoggedIn?>">
                        <input type="hidden" name="user_to" value="<?php echo $profile_username?>">
                    </div>
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="submit_profile_post" id="submit_profile_post">Post</button>
            </div>
        </div>
    </div>
</div>

<!--modal window for message-->
<div class="modal fade" id="message">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Message Here</h4>
                <button type="button" data-dismiss="modal" class="close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="#" class="new_message">
                    <div class="form-group">
                        <textarea name="message_body" class="form-control mt-3" id="new_message" placeholder="Start your conversation"></textarea>
                        
                        <input type="hidden" name="message_from" value="<?php echo $userLoggedIn?>">
                        <input type="hidden" name="message_to" value="<?php echo $profile_username?>">
                    </div>
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" name="submit_message" id="submit_message">Send</button>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer2.php');?>
<script>
    $(function() {
        //fetching data from and to a page without reloading the page
        var userLoggedIn = "<?php echo $user_info['username'];?>";
        var profile_username = "<?php echo $profile_username;?>";
        $('#loading').show();

        //original ajax request for loading first post 
        $.ajax({
            url: "includes/ajax/ajax_load_profile_post.php", //URL to which the request is sent.
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profile_username=" + profile_username,
            cache: false,
            success: function(data) {
                $('#loading').hide();
                $('.post_area').html(data);
                //if text is used instead of html then all html chrecter are converted to corresponding text charecter i.e < = &gt;
                //html() method is used to directly incorporate the html entities within selection
            }
        });

        $(window).scroll(function() {
            //        var height = $('.post_area').height();
            //        var scrollTop = $(this).scrollTop();
            var page = $('.post_area').find('.next-page').val();
            var MorePost = $('.post_area').find('.MorePost').val();

            if ((document.body.scrollHeight <= document.documentElement.scrollTop + window.innerHeight) && MorePost == 'true') {
                $('#loading').show();

                var ajaxRequest = $.ajax({
                    url: "includes/ajax/ajax_load_profile_post.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profile_username=" + profile_username,
                    cache: false,
                    success: function(data) {
                        $('.post_area').find('.next-page').remove(); //remove current .next-page
                        $('.post_area').find('.MorePost').remove();
                        $('#loading').hide();
                        $('.post_area').append(data);
                    }
                });
            } //END if
            //return false;
        }); //End  $(window).scroll(function())
        
        
        $('#submit_profile_post').click(function(){
            
            var message = $('#new_post').val();
            var message_ifOnlySpace = message.replace(/^\s*/, "").replace(/\s*$/, "");

            if(message != "" && message_ifOnlySpace != "")
               {
                    $.ajax({
                        url: 'includes/ajax/ajax_submit_profile_post.php',
                        type: 'POST',
                        data: $('.profile_post').serialize(),
                        cache: false,
                        success: function(data){
                            $('#post_modal').modal('hide');
                            location.reload();
                        },
                        error: function(){
                            alert('Failed to post.');
                        }

                    });
               }
                else
                    alert('You have not written anything');

        });
        
        $('#submit_message').click(function(){
            var message_body = $('#new_message').val();
            var message_ifOnlySpace = message_body.replace(/^\s*/, "").replace(/\s*$/, "");

            if(message_body != "" && message_ifOnlySpace != "")
            {
                $.ajax({
                    url: 'includes/ajax/ajax_send_profileMessage.php',
                    type: 'POST',
                    data: $('.new_message').serialize(),
                    cache: false,
                    success: function(){
                        $('#message').modal('hide');
                        location.reload();
                    },
                    error: function(){
                        alert('Failed to send the message.');
                    }
                });
            }
            else
                alert('You have not written anything');
        });
        
    });
</script>


</body>

</html>
   
<?php
mysqli_close($conn);

?>