<?php
//declaring connection variable

$conn = mysqli_connect("localhost" , "root" , "root" , "myglobalbook");

if(!$conn)
{
    die( "Connection failed:: ".mysqli_connect_errno() );
}

ob_start();

$timezone = date_default_timezone_set('Asia/Kolkata'); // just use to keep a record of how many hour before a status is posted
?>