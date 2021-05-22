<?php

error_reporting(0);

$path_root = $_SERVER['DOCUMENT_ROOT'];

$path_uri = $_SERVER['REQUEST_URI'];
$path_uri_exp = explode("/" , $path_uri);


include $path_root."/".$path_uri_exp[1]."/".$path_uri_exp[2]."/global/local_phpmyadmin.php";





        // $host = "localhost";
        // $user = 'root';
        // $password = 'root';
        // $db_name="nepalnewsbank";
        // $connection= mysqli_connect($host , $user , $password , $db_name);


        $host = $phpmyadmin_remote_host;
        $user = $phpmyadmin_user;
        $password = $phpmyadmin_password;
        $db_name= $phpmyadmin_db_name;
        $connection= mysqli_connect($host , $user , $password , $db_name);





