<?php
class Post
{
    private $conn;
    private $user_obj;
    
    public function __construct($conn , $userLoggedIn)
    {
        $this->conn = $conn;
        $this->user_obj = new User($conn , $userLoggedIn);//object for logged in user
    }
    
    public function submitPost($post_content , $posted_to_username , $image_name)
    {
        $post_content = trim(strip_tags($post_content));
        
        //delete check_if_empty at the end
        $check_if_empty = preg_replace('/\s+/' , "" , $post_content);// can be done by str_replace();

        //check if the post is not empty
        if($check_if_empty != "" || $image_name != "")
        {
            //**for embeded system
            
            $content_array = explode(" " , $post_content);
            $new_content_array = array();
            
            foreach($content_array as $val)
            {
                if(strpos($val , "www.youtube.com/watch?v="))
                {
                    $link = explode("&" , $val);
                    $val = $link[0];
                    $val = str_replace("watch?v=" , "embed/" , $val);
                    $val = "<br><iframe width=\'100%\' height=\'315\' src=\'$val\' frameborder=\'0\' allowfullscreen></iframe><br>";
                }
                array_push($new_content_array , $val);
            }
            
            $post_content = implode(" " , $new_content_array);
            
            //**for embeded system
            
            
            //date when post is added
            $posting_date = date("Y-m-d H:i:s");
            
            //get username of posted by
            $posted_by_username = $this->user_obj->getUsername();
            
            //if user post to his own profile
            if($posted_by_username == $posted_to_username)
            {
                $posted_to_username = "none";
            }
            
            //insert post
            $insert_post_query = "INSERT INTO posts (id , post_content , posted_by , posted_to , posting_date , user_closed , 	deleted , likes , image) VALUES (NULL , '$post_content' , '$posted_by_username' , '$posted_to_username' , '$posting_date' , 'no' , 'no' , '0' , '$image_name')";
            
            $insert_post_result = mysqli_query($this->conn , $insert_post_query);
            
            //request posted id
            $post_id = mysqli_insert_id($this->conn);//return the id of last query
            
            //Insert Notification**
            
            //Update Post count for the  user table start
            
            if($insert_post_result == true)
            {
                $num_posts = $this->user_obj->getNumPosts($posted_by_username);
                $num_posts++;
                $update_query_result = mysqli_query($this->conn , "UPDATE users SET num_posts='$num_posts' WHERE username='$posted_by_username'");
            }
            
            header('Location: #');//refresh the page
            
            //Update Post count for the  user table end
        }
        //delete check_if_empty at the end
    }
    
    
    public function loadPost($data , $limit)
    {
        $page = $data['page'];//a set of 10 posts
        //$userLoggedIn = $data['username'];
        if($page == 1)
            $num_query_tobeSkipped = 0;
        else
            $num_query_tobeSkipped = ($page - 1) * $limit;
        
        
        $str = ""; //intialise a string
        //query for the selection of the post
        $postData_query_result = mysqli_query($this->conn , "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");
        
        if(mysqli_num_rows($postData_query_result) !=0)
        {
            $skipped_query_num = 0;// how many times the query to be skipped
            $count = 1;//to track total number of posts not exceed the limit per page
            
            //fetching post data
            while($row = mysqli_fetch_assoc($postData_query_result))
            {
                //$posted_by and $posted_to are username
                
                $id = $row['id'];
                $post_content = $row['post_content'];
                $posted_by = $row['posted_by'];
                $posted_to = $row['posted_to'];
                $posting_date = $row['posting_date'];//posting date-time
                $image = $row['image'];
                
                if($this->user_obj->isFriend($posted_by))
                {
                    if($image != "")
                    {
                        if($post_content == "")
                            $hr = "";
                        else
                            $hr = "<hr>";
                        
                        $img = "<div style='text-align: center;'>$hr<img src='$image' class='img-fluid post-img'></div>";
                    }
                    else
                        $img = "";
                    
                    //prepare posted_to string so that it can be included even if not posted to user timeline
                    if($posted_to == 'none')
                    {
                        $posted_to = "";
                    }
                    else
                    {
                        $posted_to_obj = new User($this->conn , $posted_to);
                        $posted_to_FullName = $posted_to_obj->getFullName();
                        $posted_to_profileLink = " to <a href='$posted_to' class='text-success' style='text-decoration: none'>$posted_to_FullName</a>";
                    }

                    //Check if the user who has posted has their account closed. posts of account closed user is not shown
                    $posted_by_obj = new User($this->conn , $posted_by);

                    if($posted_by_obj->isClosed())
                        continue;
                    
                    if($skipped_query_num++ < $num_query_tobeSkipped)
                        continue;

                    //Once 10 post have been loaded, break
                    if($count > $limit)
                        break;
                    else
                        $count++;

                    //getting posted by information

                    $posted_by_info = $posted_by_obj->First_Last_Name_Profilepicture();
                    $posted_by_firstName = $posted_by_info['first_name'];
                    $posted_by_lastName = $posted_by_info['last_name'];
                    $posted_by_profilePic = $posted_by_info['profile_pic'];

                    $posted_by_profileLink = "<a href='$posted_by' class='text-success ml-3' style='text-decoration: none'>".$posted_by_firstName." ".$posted_by_lastName."</a>";
                    //getting posted by information
                    
                    //adding the delete post button
                    if($this->user_obj->getUsername() == $posted_by)
                        $delete = "<button class='btn btn-sm btn-danger float-right' id='delete$id'><i class='material-icons mr-auto'>delete_forever</i></button>";
                    else
                        $delete = "";

                    ?>
                    
                    <script>
                        $(function(){
                            
                            $('#toggleComment<?php echo $id;?>').click(function(){
                                $('#toggleComment-box<?php echo $id;?>').slideToggle();
                            });
                            
                            $('#share<?php echo $id;?>').click(function(){
                               $('#share-frame<?php echo $id;?>').slideToggle(); 
                            });
                            
                            
                            $('#delete<?php echo $id?>').click(function(){
                               
                                bootbox.confirm({
                                    message: "Are you sure you want to delete the post?",
                                    buttons: {
                                        confirm: {
                                            label: '<i class="glyphicon glyphicon-trash"></i> Delete',
                                        },
                                        cancel: {
                                            label: '<span class="glyphicon glyphicon-remove"></span> Cancel'
                                        }
                                    },
                                    callback: function (result) {
                                        if(result)
                                        {
                                            $.post("includes/delete_post.php?post_id=<?php echo $id?>" , {result: result});
                                            location.reload();
                                        }       
                                    }
                                });
                                
                            });
                        });
                    </script>
                    
                    
                   <?php
                    //*Timeframe

                    $post_added_dateTime = new DateTime($posting_date); //date when post is done or start time
                    $currentTime = new DateTime(date('Y-m-d H:i:s')); //current time or end time
                    $interval = $post_added_dateTime->diff($currentTime); //difference between date
                    //diff is a function of php class DateTime:: syntax:: ob->diff(another ob);
                    if($interval->y >=1)
                    {
                        if($interval->y == 1)
                            $time_message = $interval->y . " year ago";
                        else
                            $time_message = $interval->y . " years ago";
                    }
                    elseif($interval->m >= 1)
                    {
                        if($interval->d == 0)
                            $days = " ago";
                        elseif($interval->d == 1)
                            $days = " " . $interval->d . " day ago";
                        else
                            $days = " " . $interval->d . " days ago";

                        if($interval->m == 1)
                            $time_message = $interval->m . " month" . $days;
                        else
                            $time_message = $interval->m . " months" . $days;
                    }
                    elseif($interval->d >= 1)
                    {
                        if($interval->d == 1)
                            $time_message = "Yesterday";
                        else
                            $time_message = $interval->d . " days ago";
                    }
                    elseif($interval->h >= 1)
                    {
                        if($interval->h == 1)
                            $time_message = $interval->h . " hour ago";
                        else
                            $time_message = $interval->h . " hours ago";
                    }
                    elseif($interval->i >= 1)
                    {
                        if($interval->i == 1)
                            $time_message = $interval->i . " minute ago";
                        else
                            $time_message = $interval->i . " minute ago";
                    }
                    else
                    {
                        if($interval->s < 30)
                            $time_message = "Just now";
                        else
                            $time_message = $interval->s . " seconds ago";
                    }

                    //*Timeframe
                    
                    //getting number of comment per post
                    
                    $n = mysqli_num_rows(mysqli_query($this->conn , "SELECT id FROM comments WHERE post_id='$id' AND comment_removed='no'"));
                    
                    //*creating post display string
                    $str.= "<div class='post my-3 pb-3 container'>
                                      <div class='post-header mt-2 mb-1'>
                                        <a href='$posted_by'><img src='$posted_by_profilePic' class='rounded-circle img-fluid' width='50px'></a>".
                                        $posted_by_profileLink . $posted_to_profileLink ."
                                        <span class='time-message'>&nbsp;&nbsp;&nbsp;&nbsp;$time_message</span>$delete
                                      </div>
                                      <div class='post-body py-3 px-2'>
                                        $post_content $img
                                      </div>
                                      <span class='post-footer px-2' id='toggleComment".$id."'>Comment($n)</span>
                                      <iframe src='like.php?post_id=$id' scrolling='no' class='like-unlike'></iframe>
                                      <span class='post-footer px-2' role='button' id='share$id'>Share</span>
                                      
                                      <div class='comment-box' id='toggleComment-box".$id."'>
                                        <iframe src='comment_frame.php?post_id=$id' width='100%'></iframe>
                                      </div>
                                      
                                      <div id='share-frame$id' class='share-iframe'>
                                         <iframe src='share.php?post_id=$id' frameborder='1' width='100%' height='150'></iframe>
                                      </div>
                                   </div>";
                    //*creating post display string
                    unset($posted_to_obj);unset($posted_to_FullName);unset($posted_to_profileLink);
                }
              
                
            }//END OF WHILE LOOP
            if($count > $limit)
                $str.="<input type='hidden' class='next-page' value='" . ($page+1) ."'>
                        <input type='hidden' class='MorePost' value='true'>";    
            else
                $str.="<input type='hidden' class='MorePost' value='false'><p>no more post</p>";
        }
            echo $str;

    }
    
    public function loadProfilePost($data , $limit)
    {
        $page = $data['page'];//a set of 10 posts
        //$userLoggedIn = $data['userLoggedIn'];
        $profile_username = $data['profile_username'];
        
        if($page == 1)
            $num_query_tobeSkipped = 0;
        else
            $num_query_tobeSkipped = ($page - 1) * $limit;
        
        
        $str = ""; //intialise a string
        $postData_query_result = mysqli_query($this->conn , "SELECT * FROM posts WHERE deleted = 'no' AND ((posted_by = '$profile_username' AND posted_to='none') OR posted_to = '$profile_username') ORDER BY id DESC");
        
        if(mysqli_num_rows($postData_query_result) !=0)
        {
            $skipped_query_num = 0;// how many times the query to be skipped
            $count = 1;//to track total number of posts not exceed the limit per page
            
            //fetching post data
            while($row = mysqli_fetch_assoc($postData_query_result))
            {
                //$posted_by and $posted_to are username
                
                $id = $row['id'];
                $post_content = $row['post_content'];
                $posted_by = $row['posted_by'];
                $posted_to = $row['posted_to'];
                $posting_date = $row['posting_date'];//posting date-time

                //Check if the user who has posted has their account closed. posts of account closed user is not shown
                $posted_by_obj = new User($this->conn , $posted_by);


                if($skipped_query_num++ < $num_query_tobeSkipped)
                    continue;

                //Once 10 post have been loaded, break
                if($count > $limit)
                    break;
                else
                    $count++;

                //getting posted by information

                $posted_by_info = $posted_by_obj->First_Last_Name_Profilepicture();
                $posted_by_firstName = $posted_by_info['first_name'];
                $posted_by_lastName = $posted_by_info['last_name'];
                $posted_by_profilePic = $posted_by_info['profile_pic'];

                $posted_by_profileLink = "<a href='$posted_by' class='text-success ml-3' style='text-decoration: none'>".$posted_by_firstName." ".$posted_by_lastName."</a>";
                //getting posted by information

                //adding the delete post button
                if($this->user_obj->getUsername() == $posted_by)
                    $delete = "<button class='btn btn-sm btn-danger float-right' id='delete$id'><i class='material-icons mr-auto'>delete_forever</i></button>";
                else
                    $delete = "";

                ?>

                <script>
                    $(function(){

                        $('#toggleComment<?php echo $id;?>').click(function(){
                            $('#toggleComment-box<?php echo $id;?>').slideToggle();
                        });

                        $('#share<?php echo $id;?>').click(function(){
                           $('#share-frame<?php echo $id;?>').slideToggle(); 
                        });


                        $('#delete<?php echo $id?>').click(function(){

                            bootbox.confirm({
                                message: "Are you sure you want to delete the post?",
                                buttons: {
                                    confirm: {
                                        label: '<i class="glyphicon glyphicon-trash"></i> Delete',
                                    },
                                    cancel: {
                                        label: '<span class="glyphicon glyphicon-remove"></span> Cancel'
                                    }
                                },
                                callback: function (result) {
                                    if(result)
                                    {
                                        $.post("includes/delete_post.php?post_id=<?php echo $id?>" , {result: result});
                                        location.reload();
                                    }       
                                }
                            });

                        });
                    });
                </script>


               <?php
                //*Timeframe

                $post_added_dateTime = new DateTime($posting_date); //date when post is done or start time
                $currentTime = new DateTime(date('Y-m-d H:i:s')); //current time or end time
                $interval = $post_added_dateTime->diff($currentTime); //difference between date
                //diff is a function of php class DateTime:: syntax:: ob->diff(another ob);
                if($interval->y >=1)
                {
                    if($interval->y == 1)
                        $time_message = $interval->y . " year ago";
                    else
                        $time_message = $interval->y . " years ago";
                }
                elseif($interval->m >= 1)
                {
                    if($interval->d == 0)
                        $days = " ago";
                    elseif($interval->d == 1)
                        $days = " " . $interval->d . " day ago";
                    else
                        $days = " " . $interval->d . " days ago";

                    if($interval->m == 1)
                        $time_message = $interval->m . " month" . $days;
                    else
                        $time_message = $interval->m . " months" . $days;
                }
                elseif($interval->d >= 1)
                {
                    if($interval->d == 1)
                        $time_message = "Yesterday";
                    else
                        $time_message = $interval->d . " days ago";
                }
                elseif($interval->h >= 1)
                {
                    if($interval->h == 1)
                        $time_message = $interval->h . " hour ago";
                    else
                        $time_message = $interval->h . " hours ago";
                }
                elseif($interval->i >= 1)
                {
                    if($interval->i == 1)
                        $time_message = $interval->i . " minute ago";
                    else
                        $time_message = $interval->i . " minute ago";
                }
                else
                {
                    if($interval->s < 30)
                        $time_message = "Just now";
                    else
                        $time_message = $interval->s . " seconds ago";
                }

                //*Timeframe

                //getting number of comment per post

                $n = mysqli_num_rows(mysqli_query($this->conn , "SELECT id FROM comments WHERE post_id='$id' AND comment_removed='no'"));

                //*creating post display string
                $str.= "<div class='post my-3 pb-3 container'>
                                  <div class='post-header mt-2 mb-1'>
                                    <a href='$posted_by'><img src='$posted_by_profilePic' class='rounded-circle img-fluid' width='50px'></a>".
                                    $posted_by_profileLink ."
                                    <span class='time-message'>&nbsp;&nbsp;&nbsp;&nbsp;$time_message</span>$delete
                                  </div>
                                  <div class='post-body py-3 px-2'>
                                    $post_content
                                  </div>
                                  <span class='post-footer px-2' id='toggleComment".$id."'>Comment($n)</span>
                                  <iframe src='like.php?post_id=$id' scrolling='no' class='like-unlike'></iframe>
                                  <span class='post-footer px-2' role='button' id='share$id'>Share</span>

                                  <div class='comment-box' id='toggleComment-box".$id."'>
                                    <iframe src='comment_frame.php?post_id=$id' width='100%'></iframe>
                                  </div>

                                  <div id='share-frame$id' class='share-iframe'>
                                     <iframe src='share.php?post_id=$id' frameborder='1' width='100%' height='150'></iframe>
                                  </div>
                               </div>";
                //*creating post display string
                unset($posted_to_obj);unset($posted_to_FullName);unset($posted_to_profileLink);
                
              
                
            }//END OF WHILE LOOP
            if($count > $limit)
                $str.="<input type='hidden' class='next-page' value='" . ($page+1) ."'>
                        <input type='hidden' class='MorePost' value='true'>";    
            else
                $str.="<input type='hidden' class='MorePost' value='false'><p>no more post</p>";
        }
            echo $str;

    }

}

?>