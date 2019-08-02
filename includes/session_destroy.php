<?php 
// did the user's browser send a cookie for the session?
if(isset( $_COOKIE[session_name()] ))
{
    // empty the cookie
    setcookie( session_name() , '' , time()-86400 , '/');
    
}


//unset session variable
session_unset();
//destroy session
session_destroy();
?>