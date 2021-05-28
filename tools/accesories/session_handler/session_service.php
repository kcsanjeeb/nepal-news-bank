<?php

session_start();

if(isset($_SESSION['user_auth']))
{
    if($_SESSION['user_auth'] != 'admin')
    {
        $location = '../login.php';      
    }
   
}
else
{
    $location = '../login.php';    
}

