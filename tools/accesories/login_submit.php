<?php

include "connection.php";
include "nas_function/functions.php";
session_start();
include "../global/timezone.php";
// implement csrf


if(isset($_POST['submit']))
{
    if(isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password']) )
    {
        $username_sent = $_POST['username'];
        $password_sent = $_POST['password'];
    
        $username_sent = mysqli_real_escape_string($connection, $username_sent);
        $password_sent = mysqli_real_escape_string($connection, $password_sent);


        // $query = "SELECT username , password from login_nas where username = '$username_sent';" ;
        // $run_query = mysqli_query($connection, $query);

        // if(mysqli_num_rows($run_query)>0)
        // {
            // $row = mysqli_fetch_assoc($run_query);
            // $username = $row["username"];
            // $password = $row["password"];  
                if( ftp_login_nas($username_sent, $password_sent))
                {
                    if(wp_token() == 200)
                    {
                        $_SESSION['user_auth'] = 'admin' ;
                        $_SESSION['fuser'] = $username_sent ;
                        $_SESSION['fpass'] = $password_sent ;

                        $location = "../global/wp_category_id.php" ; 
                        
                    }  
                    else
                    {
                        $_SESSION['error'] =  "Login Error. Please try again.";
                        $location = $_SERVER['HTTP_REFERER'] ;
                    }                  

                }

                else
                {
                    $_SESSION['error'] =  "Wrong Username or password.";
                    $location = $_SERVER['HTTP_REFERER'] ;
                }


        // }
        // else
        // {
        //     $_SESSION['error'] =  "Wrong Username or password.";
        //     $location = $_SERVER['HTTP_REFERER'] ;
        // }


    }
    else
    {
        $_SESSION['error'] =  "Wrong Username or password.";
        $location = $_SERVER['HTTP_REFERER'] ;
    }



}
else
{
    $_SESSION['error'] =  "Wrong Username or password.";
    $location = $_SERVER['HTTP_REFERER'] ;
}




header("Location: ". $location);
exit();