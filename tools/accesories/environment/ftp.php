<?php

$ftp_folder_name_in_server = "my_data";
$domain_name_ftp = "nepalnewsbank.com";

$ftp_url = "ftp.".$domain_name_ftp;

// $ftp_un_prefix = "nepalnewsbank";
if(isset($_SESSION['fuser']) && isset($_SESSION['fpass']))
{
    $ftp_un_prefix = $_SESSION['fuser'];
    $ftp_password  = $_SESSION['fpass'] ;
}
else
{
    $ftp_un_prefix = NULL;
    $ftp_password  = NULL;
}

$ftp_un_suffix = $domain_name_ftp;

$ftp_username = "$ftp_un_prefix@$ftp_un_suffix";
// $ftp_password = "nepalnewsbank";

