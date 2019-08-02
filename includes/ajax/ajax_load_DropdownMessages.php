<?php
include('../connect.php');
include('../classes/User.php');
include('../classes/Message.php');

$limit = 7; // number of unread message to be loaded at a time
//print_r($_REQUEST);
$msg_obj = new Message($conn , $_REQUEST['userLoggedIn']);

echo $msg_obj->getMessageDropDown($limit , $_REQUEST);

?>