<?php
include('includes/header.php');

//backend of text-area of the post
$post = new Post($conn , $user_info['username']);
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit_post']))
{
    $upload = true;
    $image_name = "";
    $upload_error_message = "";
    $image_name = $_FILES['imgToupload']['name'];
    
    if($image_name != "")
    {
        $target_dir = "images/img_upload/";
        $image_name = $target_dir . uniqid() . basename($image_name);
        //eg. uniqid() => 152444lhknj   basename: dog.png    finalname:  images/img_upload/152444lhknjdog.png
        $image_file_type = pathinfo($image_name , PATHINFO_EXTENSION);
        
        if($_FILES['imgToupload']['size'] > 10000000)
        {
            $upload_error_message = "<div class='alert alert-warning mt-1'>File size is too large.<span class='close' data-dismiss='alert'>&times;</span></div>";
            $upload = false;
        }
        
        if(strtolower($image_file_type) != "jpeg" && strtolower($image_file_type) != "png" && strtolower($image_file_type) != "jpg")
        {
            $upload_error_message = "<div class='alert alert-warning mt-1'>File type must be jpeg , png or jpg.<span class='close' data-dismiss='alert'>&times;</span></div>";
            $upload = false;
        }
        
        if($upload)
        {
            if(move_uploaded_file($_FILES['imgToupload']['tmp_name'] , $image_name))
            {
                $post_body = mysqli_real_escape_string($conn , $_POST['new_post']);
                $post->submitPost($post_body, 'none' , $image_name);
            }
            else
                $upload_error_message = "<div class='alert alert-warning mt-1'>$image_name<span class='close' data-dismiss='alert'>&times;</span></div>";
        }
    }
    else
    {
        if(str_replace(" ", "" , $_POST['new_post']) == "")
        {
            $noPost_error = "<div class='alert alert-warning mt-1'>Please share what is on your mind !!<span class='close' data-dismiss='alert'>&times;</span></div>";
        }
        else
        {
            $post_body = mysqli_real_escape_string($conn , $_POST['new_post']);
            $post->submitPost($post_body, 'none' , $image_name);
        }
    }
    
}

//str_replace(" ", "" , $_POST['new_post']) == ""
//$noPost_error = "<div class='alert alert-warning mt-1'>Please share what is on your mind !!<span class='close' data-dismiss='alert'>&times;</span></div>";
//$post_body = mysqli_real_escape_string($conn , $_POST['new_post']);
//            $post->submitPost($post_body, 'none');

?>

<div class="container-fluid main-content">
    <div class="row">
        <div class="col-lg-1 col-md-1 col-sm-0"></div>
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
            
        </div>
        <div class="col-lg-7 col-md-7 col-sm-12 mt-sm-2 postfeed">
           <?php echo $noPost_error;echo $upload_error_message; ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                   <input type="file" id="imgToupload" name="imgToupload" class="mt-2">
                    <textarea name="new_post" class="form-control mt-3" id="new_post" placeholder="say whatever is in your mind...."></textarea>
                </div>
                <button class="btn btn-success" type="submit" name="submit_post">Post</button><hr>
            </form>
            
            <div class="post_area"></div>
            <img src="images/loading.gif" alt="Loading" id="loading">
            
        </div>

    </div>
    <!--.row-->
</div><!--.container-fluid main-content-->  
    
      
        

<?php include('includes/footer2.php');?>        
<script>
    $(function() {
        //fetching data from database without reloading the page
        var username = "<?php echo $user_info['username'];?>";
        $('#loading').show();

        //original ajax request for loading first post 
        $.ajax({
            url: "includes/ajax/ajax_load_post.php", //URL to which the request is sent.
            type: "POST",
            data: "page=1&username=" + username,
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
                    url: "includes/ajax/ajax_load_post.php",
                    type: "POST",
                    data: "page=" + page + "&username=" + username,
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
        
    });
</script>
    
    
</body>

</html>
   
<?php
mysqli_close($conn);
?>     
         

