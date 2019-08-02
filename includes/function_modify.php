<?php
function modify($data)
{
    $a = array("(" , ")" , "{" , "}" , "[" , "]" , ";" ,",");
    
    $data = ucwords(strtolower(trim(stripcslashes(htmlspecialchars(strip_tags(str_replace($a , "" , $data)) , ENT_QUOTES)))));
    
    return $data;
}

function modifyEmail($data)
{
    $a = array("(" , ")" , "{" , "}" , "[" , "]" , ";" ,",");
    
    $data = trim(stripcslashes(htmlspecialchars(strip_tags(str_replace($a , "" , $data)) , ENT_QUOTES)));
    
    return $data;
}
?>
