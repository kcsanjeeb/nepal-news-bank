<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/wp_api_env.php";
include "nas_function/functions.php";
include "../global/timezone.php";






// -------------- VARIABLE DECLARATION ----------------
$archive_path_picture = 'my_data/archive_data';
$archive_path_ftp = 'archive_data';
// ----------------------------------------------------




$archive_id = $_POST['archive_id'] ;
$archive_id = mysqli_real_escape_string($connection, $archive_id);


$byline = $_POST['byline'] ;
$byline = mysqli_real_escape_string($connection, $byline);
$byline_dir = remove_special_chars($byline);

$wp_id = $_POST['wp_id'] ;
$wp_id = mysqli_real_escape_string($connection, $wp_id);

$wp_media_id = $_POST['wp_media_id'] ;
$wp_media_id = mysqli_real_escape_string($connection, $wp_media_id);


$query_fetch_news = "select * from archives where archive_id = '$archive_id'";
$run_sql_fetch_news= mysqli_query($connection, $query_fetch_news);
$num_rows_news = mysqli_num_rows($run_sql_fetch_news);
$news_row_details = mysqli_fetch_assoc($run_sql_fetch_news);
$archive_path = $news_row_details['local_dir'] ;
$archive_path_ftp = $news_row_details['ftp_dir'] ;

$glob_path = $archive_path."/".$byline_dir ;


if($num_rows_news <  1)
{
    exit("Error!");
}



// delete media

$data = '';

if($wp_id != 'NULL' && $wp_id != null)
{
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

}


if($wp_media_id != 'NULL' && $wp_media_id != null)
{
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
}



$query_del_archive = "delete from archives where archive_id = '$archive_id'";
$run_query = mysqli_query($connection , $query_del_archive);



$dir = $glob_path;
$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($it,
             RecursiveIteratorIterator::CHILD_FIRST);
foreach($files as $file) {
    if ($file->isDir()){
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
}
rmdir($dir);




  $ftp = ftp_connect("$ftp_url");
  ftp_login($ftp, "$ftp_username", "$ftp_password");
  ftp_pasv($ftp, true);


  if(recursive_ftp_folder($ftp ,  $archive_path_ftp."/".$byline_dir))
  {
      ftp_rmdir($ftp, $archive_path_ftp."/".$byline_dir);
  }


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