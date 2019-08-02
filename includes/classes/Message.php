<?php

class Message
{
    private $conn;
    private $user_obj;
    
    public function __construct($conn , $userLoggedIn)
    {
        $this->conn = $conn;
        $this->user_obj = new User($conn , $userLoggedIn);
    }
    
    public function getMostRecentUser()
    {
        $userLoggedIn = $this->user_obj->getUsername();
        
        $query_result = mysqli_query($this->conn , "SELECT user_to , user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC LIMIT 1");
        
        if(mysqli_num_rows($query_result) == 0)
            return false;
        else
        {
            $row = mysqli_fetch_assoc($query_result);
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            
            if($user_to == $userLoggedIn)
                return $user_from;
            else
                return $user_to;
        }
     
    }
    
    public function sendMessage($user_to , $date , $message)
    {
        $user_from = $this->user_obj->getUsername();//this is the userLoggedIn
        #opened may be deleted
        $sendMessage_query_result = mysqli_query($this->conn , "INSERT INTO messages (id , user_to , user_from , body , date , opened , viewed , deleted) VALUES (NULL , '$user_to' , '$user_from' , '$message' , '$date' , 'no' , 'no' , 'no')");
    }
    
    public function loadMessage($other_user)
    {
        $userLoggedIn = $this->user_obj->getUsername();
        
        //if loading is done that means oviously the message is opened (opened may be replaced by viewed)
        $open_query_result = mysqli_query($this->conn , "UPDATE messages SET opened = 'yes' WHERE user_to='$userLoggedIn' AND user_from = '$other_user'");
   
        $getMessage = mysqli_query($this->conn , "SELECT * FROM messages WHERE (user_to = '$userLoggedIn' AND user_from = '$other_user') OR (user_from = '$userLoggedIn' AND user_to = '$other_user')");
        
        if( mysqli_num_rows($getMessage) > 0 )
        {
            $str = "";
            while( $row = mysqli_fetch_assoc($getMessage) )
            {
                $user_to = $row['user_to'];
                $user_from = $row['user_from'];
                $body = $row['body'];
                
                if($user_to == $userLoggedIn)
                {
                    $str.= "<div class='green message'>" . nl2br($body) . "</div><br>";
                }
                else
                    $str.= "<div class='clearfix'><div class='blue message float-right'>" . nl2br($body) . "</div></div>";
            }
        }
        return $str;
    }
    
    public function getLatestMessage($userLoggedIn , $chater)
    {
        $latest_message_query_result = mysqli_query($this->conn , "SELECT * FROM messages WHERE (user_to = '$userLoggedIn' AND user_from = '$chater') OR (user_from = '$userLoggedIn' AND user_to = '$chater') ORDER BY id DESC LIMIT 1");
        
        $time_message = ""; $days = "";
        
        if(mysqli_num_rows($latest_message_query_result) > 0)
        {
            $row =  mysqli_fetch_assoc($latest_message_query_result);
            $body = $row['body'];
            
            //*Timeframe

            $message_added_dateTime = new DateTime($row['date']); //date when message is done or start time
            $currentTime = new DateTime(date('Y-m-d H:i:s')); //current time or end time
            $interval = $message_added_dateTime->diff($currentTime); //difference between date
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
            $prepend = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";
            
            $message_body_detail = array('body' => $body , 'time' => $time_message , 'prepend' =>$prepend);
        }
        
        return $message_body_detail;
        
    }
    
    public function getConversation()
    {
        $userLoggedIn = $this->user_obj->getUsername();
        $chaters_array = array();
        $str = "";
        
        $getLatestChaters = mysqli_query($this->conn , "SELECT user_to , user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC");
        
        if(mysqli_num_rows($getLatestChaters) > 0)
        {
            while($row = mysqli_fetch_assoc($getLatestChaters))
            {
                $chater_to_push = ($row['user_to'] == $userLoggedIn) ? $row['user_from'] : $row['user_to'];
                if(!in_array($chater_to_push , $chaters_array))
                {
                    array_push($chaters_array , $chater_to_push);
                }
            }
        }
        
        foreach($chaters_array as $chater)
        {
            $chater_obj = new User($this->conn , $chater);
            
            $chater_detail = $chater_obj->First_Last_Name_Profilepicture();
            $chater_fullName = $chater_detail['first_name'] . " " . $chater_detail['last_name'];
            $chater_profilePic = $chater_detail['profile_pic'];
            
            $chater_name_append = (strlen($chater_fullName) >= 12) ? "..." : "";
            $chater_name_split = str_split($chater_fullName , 12);
            $chater_fullName = $chater_name_split[0] . $chater_name_append;
            
            //getting the latest message detail
            $latest_message_detail = $this->getLatestMessage($userLoggedIn , $chater);
            
            $dots = (strlen($latest_message_detail['body']) >= 12) ? "..." : "";
            $breaked_body = str_split($latest_message_detail['body'] , 12);//this is an array of two string breaked at 12
            
            $req_split_part = $breaked_body[0] . $dots;//message with 12 chars
            
            $time_message = $latest_message_detail['time'];
            $body_prepend = $latest_message_detail['prepend'];
            
            $str.= "<a href='messages.php?user_to=". $chater ."' style='text-decoration: none;'>
                        <div class='latest-chater mx-3 my-1'>
                            <div class='px-2 my-1'>
                                <img src='$chater_profilePic' class='img-fluid rounded-circle' width='50px'>
                                <span class='text-success'>$chater_fullName</span>
                                <span class='time-message'>&nbsp;&nbsp;&nbsp;$time_message</span>
                            </div>
                            <div class='px-3'>
                                <span class='text-secondary'>$body_prepend</span>&nbsp;<span class='text-dark'>$req_split_part</span>
                            </div>
                        </div>    
                    </a>";
        }
        
        return $str;
        
    }
    
    public function getMessageDropDown($limit , $data)
    {
        //make some changes
        $userLoggedIn = $data['userLoggedIn'];
        $page = $data['page'];
        
        if($page == 1)
            $num_query_tobeSkipped = 0;
        else
            $num_query_tobeSkipped = ($page - 1) * $limit;
        
        $msg_noti_viewed = mysqli_query($this->conn , "UPDATE messages SET viewed = 'yes' WHERE user_to='$userLoggedIn'");
        
        $chaters_array = array();
        $str = "";
        
        $getLatestChaters = mysqli_query($this->conn , "SELECT user_to , user_from FROM messages WHERE (user_to = '$userLoggedIn' OR user_from = '$userLoggedIn') ORDER BY id DESC");
        
        $skipped_query_num = 0;// how many times the query to be skipped
        $count = 1;//to track total number of posts not exceed the limit per page
        
        if(mysqli_num_rows($getLatestChaters) > 0)
        {
            while($row = mysqli_fetch_assoc($getLatestChaters))
            {
                if($skipped_query_num++ < $num_query_tobeSkipped)
                    continue;
                
                if($count > $limit)
                    break;
                else
                    $count++;
                
                $chater_to_push = ($row['user_to'] == $userLoggedIn) ? $row['user_from'] : $row['user_to'];
                if(!in_array($chater_to_push , $chaters_array))
                {
                    array_push($chaters_array , $chater_to_push);
                }
            }
        }
        
        
        foreach($chaters_array as $chater)
        {
            $is_opened_query_result =  mysqli_query($this->conn , "SELECT opened FROM messages WHERE user_to = '$userLoggedIn' AND user_from = '$chater' ORDER BY id DESC");
            
            $row = mysqli_fetch_assoc($is_opened_query_result);
            $style = ($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";
            
            $chater_obj = new User($this->conn , $chater);
            
            $chater_detail = $chater_obj->First_Last_Name_Profilepicture();
            $chater_fullName = $chater_detail['first_name'] . " " . $chater_detail['last_name'];
            $chater_profilePic = $chater_detail['profile_pic'];
            
            $chater_name_append = (strlen($chater_fullName) >= 12) ? "..." : "";
            $chater_name_split = str_split($chater_fullName , 12);
            $chater_fullName = $chater_name_split[0] . $chater_name_append;
            
            //getting the latest message detail
            $latest_message_detail = $this->getLatestMessage($userLoggedIn , $chater);
            
            $dots = (strlen($latest_message_detail['body']) >= 12) ? "..." : "";
            $breaked_body = str_split($latest_message_detail['body'] , 12);//this is an array of two string breaked at 12
            
            $req_split_part = $breaked_body[0] . $dots;//message with 12 chars
            
            $time_message = $latest_message_detail['time'];
            $body_prepend = $latest_message_detail['prepend'];
            
            $str.= "<li style='$style border-bottom: 1px solid grey;'>
                        <a href='messages.php?user_to=$chater' class='dropdown-item'>
                            <div class='p-0'>
                                <div>
                                    <img src='$chater_profilePic' class='rounded-circle img-fluid' width='40px'>
                                    <span class='text-success'>$chater_fullName</span>
                                    <span class='time-message' style='font-size: 9px;'>&nbsp;&nbsp$time_message</span>
                                </div>
                                <div>
                                    <span class='text-secondary'>$body_prepend</span>&nbsp;<span class='text-dark'>$req_split_part</span>
                                </div>
                            </div>
                        </a>
                    </li>";
        }
        
        if($count > $limit)
                $str.="<input type='hidden' class='next_page_dropDownMessages' value='" . ($page+1) ."'>
                        <input type='hidden' class='MoreMessages' value='true'>";  
        else
                $str.="<input type='hidden' class='MoreMessages' value='false'>";
        
        return $str;
        
    }
    
    public function getNum_unreadMessages()
    {
        $userLoggedIn = $this->user_obj->getUsername();
        
        $numUnread_message_query_result = mysqli_query($this->conn , "SELECT * FROM messages WHERE viewed = 'no' AND user_to = '$userLoggedIn'");
        
        return mysqli_num_rows($numUnread_message_query_result);
    }
}

?>