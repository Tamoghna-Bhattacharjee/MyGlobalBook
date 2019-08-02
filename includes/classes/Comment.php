<?php
class Comment
{
    private $conn;
    private $post_id;
    private $post_infoById;
    
    public function __construct($conn , $post_id)
    {
        $this->conn = $conn;
        $this->post_id = $post_id;
        $this->post_infoById = mysqli_fetch_assoc(mysqli_query($conn , "SELECT posted_by , posted_to FROM posts WHERE id='$post_id'"));
    }
    
    
    public function submitComment($comment_body , $userLoggedIn)
    {
        $comment_to = $this->post_infoById['posted_by'];
        $comment_by = $userLoggedIn;
        $comment_date = date('Y-m-d H:i:s');
        
        $insert_comment_query = "INSERT INTO comments (id , comment_body , comment_by , comment_to , comment_date , comment_removed , post_id) VALUES (NULL , '$comment_body' , '$comment_by' , '$comment_to' , '$comment_date' , 'no' , '$this->post_id')";
        
        $insert_comment_result = mysqli_query($this->conn , $insert_comment_query);
        
    }
    
    
    public function loadComment()
    {
        $str = "";
        $time_message = "";
        $commentData_query_result = mysqli_query($this->conn , "SELECT * FROM comments WHERE post_id='$this->post_id' AND comment_removed='no' ORDER BY id DESC");
        
        if(mysqli_num_rows($commentData_query_result) != 0)
        {
            while($row = mysqli_fetch_assoc($commentData_query_result))
            {
                $comment_body = $row['comment_body'];
                $comment_by = $row['comment_by'];
                $comment_to = $row['comment_to'];
                $comment_date = $row['comment_date'];
                
                $comment_byObj = new User($this->conn , $comment_by);
                
                if($comment_byObj->isClosed())
                    continue;
                
                //fetching comment_by information
                $comment_by_info = $comment_byObj->First_Last_Name_Profilepicture();
                $comment_by_fname = $comment_by_info['first_name'];
                $comment_by_lname = $comment_by_info['last_name'];
                $comment_by_profilePic = $comment_by_info['profile_pic'];
                $comment_by_link = "<a href='$comment_by' target='_parent' class='text-success ml-3' style='text-decoration: none'>".$comment_by_fname." ".$comment_by_lname."</a>";
                
                
            //**timeframe
                
                $comment_dateTime = new DateTime($comment_date);
                $currentTime = new DateTime(date('Y-m-d H:i:s')); //current time or end time
                $interval = $comment_dateTime->diff($currentTime); //difference between date
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
            //**timeframe
                
                
            //Creating comment string
                
                $str.="<div class='comment-postArea px-3 mx-3' style='border: solid 1px black; background: white;'>
                            <div class='comment-header mt-2'>
                                <a href='$comment_by' target='_parent'><img src='$comment_by_profilePic' class='rounded-circle img-fluid' width='50px'></a>$comment_by_link<span class='time-message'>&nbsp;&nbsp;&nbsp;&nbsp;$time_message</span>
                            </div>
                            <div class='comment-body'>
                                $comment_body
                            </div>
                         </div><br>";
                
            //Creating comment string

            }//End of while loop
            echo $str;
        }//end of if loop
    }
}
?>