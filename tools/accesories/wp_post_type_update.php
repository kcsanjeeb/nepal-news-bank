<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/wp_api_env.php";


$post_status = $_GET["wp_post_status"];
$post_id = $_GET["wp_post_id"];

$post_status = mysqli_real_escape_string($connection, $post_status);
$post_id = mysqli_real_escape_string($connection, $post_id);

$data="";
$curl = curl_init();
    curl_setopt_array($curl, array(                    
    CURLOPT_URL => "$domain_url/custom_news_status.php?wp_post_id=$post_id&wp_post_status=$post_status",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        'Authorization: Bearer '.$token_bearer.''                            ),
    ));

    $response = curl_exec($curl);
    $response = json_decode($response);
    $response = json_decode(json_encode($response) , true);
    $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);                    
curl_close($curl);


$query_wp_id_upd = "update  web set  wp_post_type='$post_status'  where wp_post_id = '$post_id'  ;";                  
$run_query_wp_id_upd = mysqli_query($connection , $query_wp_id_upd);



