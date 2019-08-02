<?php
include('../connect.php');
include('../classes/User.php');

$search_str = trim($_POST['value']);
$userLoggedIn = $_POST['userLoggedIn'];

$userLoggedIn_obj = new User($conn , $userLoggedIn);

$name = explode( " " , $search_str );

if(strpos($search_str , "_"))//if user searches via username
{
    $result = mysqli_query($conn , "SELECT * FROM users WHERE username LIKE '$search_str%' AND user_closed = 'no' LIMIT 6");
}
else if(count($name) == 2)
{
    $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' AND last_name LIKE '%$name[1]%') AND user_closed = 'no' LIMIT 6");
}
else
{
    $result = mysqli_query($conn , "SELECT * FROM users WHERE (first_name LIKE '%$name[0]%' OR last_name LIKE '%$name[0]%') AND user_closed = 'no' LIMIT 6");
}

$return_str = "";

if($search_str != "")
{
    $count = 0;
    while($row = mysqli_fetch_assoc($result))
    {
        if($row['username'] != $userLoggedIn)
        {
            $count++;
            if($count > 5)
                break;
            
            $fullName = $row['first_name'] . " " . $row['last_name'];
            $profile_pic = $row['profile_pic'];
            $username = $row['username'];
            
            $return_str.= "<a href='$username' style='text-decoration: none;'>
                              <div class='searched-friends px-1'>
                                <img src='$profile_pic' class='img-fluid rounded-circle' width='50px'>
                                <span class='text-dark'>$fullName</span>
                              </div>
                            </a>";
        }
    }
    if($count > 5)
        $return_str.= "<a href='search.php?search=$search_str' class='mt-1' style='text-decoration: none;'>
                            <div style='text-align: center' class='bg-dark'>See All...</div>
                        </a>";
    echo $return_str;
}
?>