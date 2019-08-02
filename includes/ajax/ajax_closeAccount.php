<?php
include('../connect.php');

if(isset($_POST['result']) && isset($_POST['userLoggedIn']))
{
    $result = $_POST['result'];
    $userLoggedIn = $_POST['userLoggedIn'];
    
    if($result == true)
    {
        //making user_closed = 'yes'
        $update_userClosed = mysqli_query($conn , "UPDATE users SET user_closed = 'yes' WHERE username = '$userLoggedIn'");
        
    }
}

?>