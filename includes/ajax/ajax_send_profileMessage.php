<?php

include('../connect.php');
include('../classes/User.php');
include('../classes/Message.php');

if(isset($_POST['message_body']))
{
    $message_to = $_POST['message_to'];
    $message_from = $_POST['message_from'];//=== userLoggedIn
    $message_body = $_POST['message_body'];
    $date = date('Y-m-d H:i:s');
    
    $msg_obj = new Message($conn , $message_from);
    
    $msg_obj->sendMessage($message_to , $date , $message_body);
}

?>