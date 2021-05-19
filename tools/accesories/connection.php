<?php

error_reporting(0);

$path_root = $_SERVER['DOCUMENT_ROOT'];

$path_uri = $_SERVER['REQUEST_URI'];
$path_uri_exp = explode("/" , $path_uri);
array_pop($path_uri_exp) ; array_pop($path_uri_exp) ;
$path_uri_imp = implode("/" , $path_uri_exp);


include $path_root."/".$path_uri_imp."/global/local_phpmyadmin.php" ;

        $host = "localhost";

        $user = $phpmyadmin_user;
        $password = $phpmyadmin_password;

        $db_name="nepalnewsbank";
        $connection= mysqli_connect($host , $user , $password , $db_name);





