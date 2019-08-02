<?php
include('../connect.php');
include('../classes/User.php');

$search_str = trim($_POST['value']);
$userLoggedIn = $_POST['userLoggedIn'];

$userLoggedIn_obj = new User($conn , $userLoggedIn);

$name = explode( " " , $search_str );

if(strpos($search_str , "_") !== false)
{
    $result = mysqli_query($conn , "SELECT * FROM users WHERE username LIKE '$search_str%' AND user_closed = 'no' LIMIT 8");
}
else if(count($name) == 2)
{
    $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' AND last_name LIKE '%$name[1]%') AND user_closed = 'no' LIMIT 8");
}
else
{
    $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' OR last_name LIKE '%$name[0]%') AND user_closed = 'no' LIMIT 8");
}


if($search_str != "")
{
    while($row = mysqli_fetch_assoc($result))
    {
        if($userLoggedIn_obj->isFriend($row['username']) && $row['username'] != $userLoggedIn)
        {
            $fullName = $row['first_name'] . " " . $row['last_name'];
            $profile_pic = $row['profile_pic'];
            $username = $row['username'];
            echo "<a href='messages.php?user_to=$username' style='text-decoration: none;'>
                    <div class='searched-friends px-lg-3 px-sm-2 py-2'>
                        <img src='$profile_pic' class='img-fluid rounded-circle' width='50px'>
                        <span class='text-dark'>$fullName</span>
                    </div>
                  </a>";
        }
    }
}

?>
