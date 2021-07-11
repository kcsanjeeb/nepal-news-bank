<?php

$ftp_server = "ftp.nepalnewsbank.com";

// set up a connection or die
$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 

// $conn_id = ftp_ssl_connect($ftp_server  , 22) or die("Couldn't connect to $ftp_server"); 
