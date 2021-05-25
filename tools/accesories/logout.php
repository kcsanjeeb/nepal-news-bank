<?php

include "session_handler/session_service.php";
include "../global/timezone.php";


if(!isset($location))
{
    if(isset($_POST['logout']))
    {
        session_destroy();
        setcookie("wptoken", "", time() - 3600);

        $cookie_name = "wptoken";
        $cookie_value = '';
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

        $location_log = "../login.php";
    }

    if(isset($location_log))
    {
        $location_redirect = $location_log ;
    }
    else
    {
        $location_redirect = $_SERVER['HTTP_REFERER'];
    }
}
else
{
    if(isset($location))
    {
        $location_redirect = $location;
    }
    else
    {
        $location_redirect = '../login.php';
    }
}


header("Location: ". $location_redirect);