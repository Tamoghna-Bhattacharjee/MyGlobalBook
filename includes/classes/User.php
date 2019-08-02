<?php

class User
{
    private $conn;
    private $user_detail;
    
    public function __construct($conn , $username)
    {
        $this->conn = $conn;
        $User_query = "SELECT * FROM users WHERE username = '$username'";
        
        $User_result = mysqli_query($conn , $User_query);
        
        $this->user_detail = mysqli_fetch_assoc($User_result);
    }
        
    //To send username whenever needed
    public function getUsername()
    {
        return $this->user_detail['username'];
    }
    
    // send number of post of the user
    public function getNumPosts($posted_by_username)
    {

        return $this->user_detail['num_posts'];
    }
    
    // To get the full name of user
    public function getFullName()
    {
        return $this->user_detail['first_name']." ".$this->user_detail['last_name'];
    }
    
    //to get weather a account is closed or not
    public function isClosed()
    {
        if( $this->user_detail['user_closed'] == "yes")
            return true;
        else
            return false;
    }
    public function First_Last_Name_Profilepicture()
    {
        $info = array(
                    "first_name" => $this->user_detail['first_name'],
                    "last_name"  => $this->user_detail['last_name'],
                    "profile_pic"  => $this->user_detail['profile_pic']
                        );
        return $info;
    }
    
    public function isFriend($user_to_check)
    {
        $user_comma_string = "," . $user_to_check . ",";
        if(strstr($this->user_detail['friend_array'] , $user_comma_string) || $user_to_check == $this->user_detail['username'])
        {
            return true;
        }
        else
            return false;    
    }
    
    public function didReceive_friendRequest($user_from)
    {
        $user_to = $this->user_detail['username'];
        
        $check_request_query_result = mysqli_query($this->conn , "SELECT * FROM friend_request WHERE user_to = '$user_to' AND user_from='$user_from'");
        
        if(mysqli_num_rows($check_request_query_result) == 1)//may use >0
            return true;
        else
            return false;
    }
    
    public function didSend_friendRequest($user_to)
    {
        $user_from = $this->user_detail['username'];
        
        $check_request_query_result = mysqli_query($this->conn , "SELECT * FROM friend_request WHERE user_to = '$user_to' AND user_from='$user_from'");
        
        if(mysqli_num_rows($check_request_query_result) == 1)//may use >0
            return true;
        else
            return false;
    }
    
    public function getNumLikes()
    {
        return $this->user_detail['num_likes'];
    }
    
    public function acceptRequest($newFriend_username)
    {
        $userLoggedIn = $this->user_detail['username'];
        
        //add friend to userLoggedIn friend array
        $newFriend_array_for_userLoggedIn = $this->user_detail['friend_array'] . $newFriend_username . ',';
        
        $addFriend_for_userLoggedIn = mysqli_query($this->conn , "UPDATE users SET friend_array = '$newFriend_array_for_userLoggedIn' WHERE username='$userLoggedIn'");
        
        //add userLoggedIn as friend for the opposite person
        $row = mysqli_fetch_assoc(mysqli_query($this->conn , "SELECT friend_array FROM users WHERE username='$newFriend_username'"));
        
        $newFriend_array_for_oppositePerson = $row['friend_array'] . $userLoggedIn . ',';
        
        $addUserLoggedIn_as_friend_for_opposite = mysqli_query($this->conn , "UPDATE users SET friend_array = '$newFriend_array_for_oppositePerson' WHERE username='$newFriend_username'");
        
        //remove friend request
        $remove_friend_request = mysqli_query($this->conn , "DELETE FROM friend_request WHERE user_to='$userLoggedIn' AND user_from = '$newFriend_username'");
    }
    
    public function removeFriend($FriendtoRemove_username)
    {
        $userLoggedIn = $this->user_detail['username'];
        
        //to remove friend from userLoggedIn
        $newFriend_array_ForUserLoggedIn = str_replace($FriendtoRemove_username."," , "" , $this->user_detail['friend_array']);
        
        $removeFriend_from_userLoggedIn = mysqli_query($this->conn , "UPDATE users SET friend_array = '$newFriend_array_ForUserLoggedIn' WHERE username='$userLoggedIn'");
        
        //remove userLoggedIn as friend from FriendtoRemove_username
        $row = mysqli_fetch_assoc(mysqli_query($this->conn , "SELECT friend_array FROM users WHERE username='$FriendtoRemove_username'"));
        
        $newFriendArray_for_FriendtoRemove = str_replace($userLoggedIn . "," , "" , $row['friend_array']);
        
        $removeFriend_from_FriendtoRemove = mysqli_query($this->conn , "UPDATE users SET friend_array = '$newFriendArray_for_FriendtoRemove' WHERE username='$FriendtoRemove_username'");
    }
    
    public function deleteRequest($user_from)
    {
        $user_to = $this->user_detail['username'];
        
        $delete_request = mysqli_query($this->conn , "DELETE FROM friend_request WHERE user_to='$user_to' AND user_from = '$user_from'");
    }
    
    public function sendRequest($user_to)
    {
        $user_from = $this->user_detail['username'];
        
        $send_request_query_result = mysqli_query($this->conn , "INSERT INTO friend_request (id , user_to , user_from) VALUES (NULL , '$user_to' , '$user_from')");
    }
    
    public function getNumMutualFriends($user_to_check)
    {
        $mutual_friends = 0;
        $userLoggedIn_friends = explode( "," , $this->user_detail['friend_array'] );
        
        $query_result = mysqli_query($this->conn , "SELECT friend_array FROM users WHERE username = '$user_to_check'");
        $row = mysqli_fetch_assoc($query_result);
        
        $user_to_check_friends = explode("," , $row['friend_array']);
        
        foreach($userLoggedIn_friends as $userLoggedIn_friend)
        {
            foreach($user_to_check_friends as $user_to_check_friend)
            {
                if($userLoggedIn_friend == $user_to_check_friend && $userLoggedIn_friend!= "")
                {
                    $mutual_friends++;
                }
            }
        }
        
        return $mutual_friends;
    }

}

?>