<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/wp_api_env.php";
include "nas_function/functions.php";

date_default_timezone_set("Asia/Kathmandu");

print_r($_POST);



$archive_id = $_POST['archive_id'] ;
$archive_id = mysqli_real_escape_string($connection, $archive_id);


$byline = $_POST['byline'] ;
$byline = mysqli_real_escape_string($connection, $byline);
$byline_dir = remove_special_chars($byline);

$wp_id = $_POST['wp_id'] ;
$wp_id = mysqli_real_escape_string($connection, $wp_id);

$wp_media_id = $_POST['wp_media_id'] ;
$wp_media_id = mysqli_real_escape_string($connection, $wp_media_id);

$video = $_POST['video'] ;
$video = mysqli_real_escape_string($connection, $video);


$thumbnail = $_POST['thumbnail'] ;
$thumbnail = mysqli_real_escape_string($connection, $thumbnail);



// delete media

$data = '';

$url = "$domain_url/wp-json/wp/v2/haru_video/$wp_id" ;
$curl = curl_init();
curl_setopt_array($curl, array(                    
CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'DELETE',
CURLOPT_POSTFIELDS => $data ,
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        'Authorization: Bearer '.$token_bearer.''
    ),
));

$response = curl_exec($curl);           
$respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);                    
curl_close($curl);

echo "<br>Hary Video Responce Code: $respCode <br>";

// wp-json/wp/v2/media/5?force=true


$url = "$domain_url/wp-json/wp/v2/media/$wp_media_id?force=true" ;
$curl = curl_init();
curl_setopt_array($curl, array(                    
CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'DELETE',
CURLOPT_POSTFIELDS => $data ,
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        'Authorization: Bearer '.$token_bearer.''
    ),
));

$response = curl_exec($curl);           
$respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);                    
curl_close($curl);

echo "Hary Media Responce Code: $respCode <br>";

// -------------- VARIABLE DECLARATION ----------------
$archive_path_video = 'my_data/archive_data/archive_video';
$archive_path_video_ftp = 'archive_data/archive_video';

// ----------------------------------------------------


$query_del_archive = "delete from archive_video where archive_id = '$archive_id'";
$run_query = mysqli_query($connection , $query_del_archive);


$files = glob('../'.$archive_path_video.'/'.$byline_dir.'/*'); // get all file names
  foreach($files as $file){ // iterate files
    if(is_file($file)) {
      unlink($file); // delete file
    }
  }
  rmdir('../'.$archive_path_video.'/'.$byline_dir);


  $ftp = ftp_connect("$ftp_url");
  ftp_login($ftp, "$ftp_username", "$ftp_password");
  ftp_pasv($ftp, true);

  $dir = $thumbnail ;
  ftp_delete($ftp, $dir);
  $dir = $video ;
  ftp_delete($ftp, $dir);
  $dir = $archive_path_video_ftp.'/'.$byline_dir ;
  ftp_rmdir($ftp, $dir);

  ftp_close($ftp);

  if(isset($location))
  {
      $location_redirect = $location;
  }
  else
  {
      $location_redirect = $_SERVER['HTTP_REFERER'];
  }
  
  
  header("Location: ". $location_redirect);