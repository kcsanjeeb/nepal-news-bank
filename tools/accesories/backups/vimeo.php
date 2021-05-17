<?php

require "vimeo/vendor/autoload.php" ;


$client_id = "292064cf0b8953a10ac9db92d7b2f5696f0dddee";
$client_secret = "hsqGeFV83euyE2ekqx0l/XaZC7u8wLtaLavF9JJYfp1sIUt68Yyk8QaK0ArAVnwh5a5e5GM3ddH5lTFy6U4FnB3oPjsUOGUPfCGYORDOe6HfxpGCA9w+SHzBnYMwDpWB";
$access_token = '698d0cd9e4ab6085e475de568500a613';
$client = new \Vimeo\Vimeo($client_id, $client_secret, $access_token);

$file_name = "../2021-04-03/20210403_212043_27542_videolong.mp4";
$uri = $client->upload($file_name, array(
  "name" => "Untitled",
  "description" => "The description goes here."
));

echo "Your video URI is: " . $uri;

$response = $client->request($uri . '?fields=transcode.status');
if ($response['body']['transcode']['status'] === 'complete') {
  print 'Your video finished transcoding.';
} elseif ($response['body']['transcode']['status'] === 'in_progress') {
  print 'Your video is still transcoding.';
} else {
  print 'Your video encountered an error during transcoding.';
}

$response = $client->request($uri . '?fields=link');
// echo "Your video link is: " . $response['body']['link'];

$id = explode("/" , $response['body']['link']);
$id = end($id);

echo "vimeo ID " + $id ;

// print_r($response);