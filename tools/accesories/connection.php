<?php

error_reporting(0);

include __DIR__."/nepal-news-bank/tools/global/local_phpmyadmin.php" ;

        $host = "localhost";
        $user = $phpmyadmin_user;
        $password = $phpmyadmin_password;
        $db_name="nepalnewsbank";
        $connection= mysqli_connect($host , $user , $password , $db_name);





