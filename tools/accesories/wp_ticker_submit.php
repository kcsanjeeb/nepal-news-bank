<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/wp_api_env.php";



$title = mysqli_real_escape_string($connection, $_POST['title']);
$content = mysqli_real_escape_string($connection, $_POST['content']);




$post = [
    'title' => $title,
    'content' => $content,
];

$ch = curl_init("$domain_url/custom_ticker_api.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);


$response = curl_exec($ch);


curl_close($ch);



if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = '../newsticker.php';
}

header("Location: ".$location_redirect);
exit();