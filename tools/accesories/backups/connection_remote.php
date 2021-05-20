<?php


$path_root = $_SERVER['DOCUMENT_ROOT'];

$path_uri = $_SERVER['REQUEST_URI'];
$path_uri_exp = explode("/" , $path_uri);


include $path_root."/".$path_uri_exp[1]."/".$path_uri_exp[2]."/global/local_phpmyadmin.php";

$host = "162.214.81.12:3306";

$user = 'nepalnew_nasadmin';
$password = 'nas_admin';

$db_name="nepalnew_nepalnewsbank";
$connection= mysqli_connect($host , $user , $password , $db_name);

if($connection)
{
    echo "Connected Succesfully";
}
else
{
    echo "Connection Failed";
}

