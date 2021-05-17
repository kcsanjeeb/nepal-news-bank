<?php





require "vimeo/vendor/autoload.php" ;

$client_id = "292064cf0b8953a10ac9db92d7b2f5696f0dddee";
$client_secret = "hsqGeFV83euyE2ekqx0l/XaZC7u8wLtaLavF9JJYfp1sIUt68Yyk8QaK0ArAVnwh5a5e5GM3ddH5lTFy6U4FnB3oPjsUOGUPfCGYORDOe6HfxpGCA9w+SHzBnYMwDpWB";
$access_token = '698d0cd9e4ab6085e475de568500a613';
$client = new \Vimeo\Vimeo($client_id, $client_secret, $access_token);




$uri="/videos/533193944";
$response = $client->request($uri, [], 'DELETE');



