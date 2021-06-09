<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/wp_api_env.php";
include "environment/vimeo.php";
include "nas_function/functions.php";
include "../global/timezone.php";
    // ftp_remote($news_ftp_path."/".$sourceName , "../".$gal_img_arr)
  
    
    require "vimeo/vendor/autoload.php" ;

    $client_id = "292064cf0b8953a10ac9db92d7b2f5696f0dddee";
    $client_secret = "hsqGeFV83euyE2ekqx0l/XaZC7u8wLtaLavF9JJYfp1sIUt68Yyk8QaK0ArAVnwh5a5e5GM3ddH5lTFy6U4FnB3oPjsUOGUPfCGYORDOe6HfxpGCA9w+SHzBnYMwDpWB";
    $access_token = '698d0cd9e4ab6085e475de568500a613';
    $client = new \Vimeo\Vimeo($client_id, $client_secret, $access_token);

    
    // -------------- VARIABLE DECLARATION ----------------
$interview_path = 'interview_data';
$news_path = 'news_data';
// ----------------------------------------------------


    
   
    
    
    
 

if(isset($_POST['news_id']))
{        

    $news_id = $_POST['news_id'];
    $news_id = mysqli_real_escape_string($connection, $news_id);


    $sql_content = "select * from web where newsid = '$news_id' ";
    $run_sql_content= mysqli_query($connection, $sql_content);
    $num_rows_content = mysqli_num_rows($run_sql_content);

    if($num_rows_content == 1)
    {
        $sql_content_nas = "select * from nas where newsid = '$news_id' ";
        $run_sql_content_nas = mysqli_query($connection, $sql_content_nas);
        $num_rows_content_nas = mysqli_num_rows($run_sql_content_nas);
        $row_content_nas = mysqli_fetch_assoc($run_sql_content_nas); 

        $video_type_nas =  $row_content_nas['video_type'];  
        $date =  $row_content_nas['local_published_date'];
        $byline =  $row_content_nas['byline'];
        $category_nas =  $row_content_nas['category_list'];



        
        $byline_ftp  = remove_special_chars($byline) ;


        $row_content = mysqli_fetch_assoc($run_sql_content);
        $videolong_full = $row_content['videolong'];
        $thumbnail_full = $row_content['thumbnail'];
        $videolazy_full = $row_content['videolazy'];                            
        $newsbody_full = $row_content['newsbody'];
        $additional_file_full = $row_content['additional_file'];

        $photos = $row_content['photos'];
        $photos_array = explode(',' , $photos);

        $audio = $row_content['audio'];
        $videoextra = $row_content['videoextra'];

        $wp_id = $row_content['wp_post_id'];
        

        if($category_nas == $_SESSION['interview_id'])
        {
            $dir_del = "/".$interview_path."/$byline_ftp";

        }
        else
        {
            $dir_del = "/".$news_path."/$date/$byline_ftp";

        }



        
        if($video_type_nas == 'selfhost')
        { 
            if($videolong_full != NULL)
            {
                echo $videolong_full;                                     
                ftp_delete_rem($videolong_full,'file');                                
            }

            if($videolazy_full != NULL)
            {          
                echo $videolazy_full;               
                ftp_delete_rem($videolazy_full  ,'file');
            }

            if($videoextra != NULL)
            {
                // $file = explode("/" , $videoextra);
                // $reverse_file = array_reverse($file);
                // $last = $reverse_file[1];
                // $end = $reverse_file[0];
                // $path =  "$last/$end";
                echo $videoextra; 
                ftp_delete_rem($videoextra  ,'file');
            }

        }

        if($video_type_nas == 'vimeo')
        {

            if($vimeo_videolong_web != NULL)
            {
                $uri="/videos/$vimeo_videolong_web";
                $response = $client->request($uri, [], 'DELETE');                           
            }

            if($vimeo_videolazy_web != NULL)
            {
                $uri="/videos/$vimeo_videolazy_web";
                $response = $client->request($uri, [], 'DELETE'); 
            }

            if($vimeo_video_extra_web != NULL)
            {
                $uri="/videos/$vimeo_video_extra_web";
                $response = $client->request($uri, [], 'DELETE'); 
            }

        }



           

            if($thumbnail_full != NULL)
            {
                echo $thumbnail_full; 
                // $file = explode("/" , $thumbnail_full);
                // $reverse_file = array_reverse($file);
                // $last = $reverse_file[1];
                // $end = $reverse_file[0];
                // $path =  "$last/$end";
                
                ftp_delete_rem($thumbnail_full , 'file');
            }
            

           

            if($newsbody_full != NULL)
            {echo $newsbody_full; 
                // $file = explode("/" , $newsbody_full);
                // $reverse_file = array_reverse($file);
                // $last = $reverse_file[1];
                // $end = $reverse_file[0];
                // $path =  "$last/$end";
              
                ftp_delete_rem($newsbody_full , 'file');
            }


            if($audio != NULL)
            {
                // $file = explode("/" , $audio);
                // $reverse_file = array_reverse($file);
                // $last = $reverse_file[1];
                // $end = $reverse_file[0];
                // $path =  "$last/$end";
                echo $audio; 
                ftp_delete_rem($audio , 'file');
            }

           

            foreach($photos_array as $ph)
            {
                // $file = explode("/" , $ph);
                // $reverse_file = array_reverse($file);
                // $last = $reverse_file[1];
                // $end = $reverse_file[0];
                // $path =  "$last/$end";
                echo $ph; 
                ftp_delete_rem($ph , 'file');
            }    
            

            
            if($additional_file_full != NULL)
            {
                // $file = explode("/" , $audio);
                // $reverse_file = array_reverse($file);
                // $last = $reverse_file[1];
                // $end = $reverse_file[0];
                // $path =  "$last/$end";
                echo $additional_file_full; 
                ftp_delete_rem($additional_file_full , 'file');
            }

            
            ftp_delete_rem($dir_del , 'folder');
            

            $sql_del_web = "update web set videolong = null , videolazy = null ,
                            thumbnail = null, audio = null , photos = null , videoextra = null , newsbody = null where newsid = '$news_id' ";
            $run_sql_del_web= mysqli_query($connection, $sql_del_web);



            if($run_sql_del_web)
            {
                $_SESSION['notice_remote'] = "success_remotefile_delete";
            }
            else
            {
                $_SESSION['notice_remote'] = "failed_remotefile_delete";
            }

            
    }
}

if( isset($_POST['news_id']) && isset($_POST['wp_id']))
{
    if( !empty($_POST['news_id']) && !empty($_POST['wp_id']))
    {
        $news_id = $_POST['news_id'];
        $news_id = mysqli_real_escape_string($connection, $news_id);

        $wp_id = $_POST['wp_id'];


        $sql_content = "select * from web where newsid = '$news_id' ";
        $run_sql_content= mysqli_query($connection, $sql_content);
        $num_rows_content = mysqli_num_rows($run_sql_content);

        if($num_rows_content == 1)
        {

            $row_content = mysqli_fetch_assoc($run_sql_content);
            $videolong_full = $row_content['videolong'];
            $preview_full = $row_content['previewgif'];
            $thumbnail_full = $row_content['thumbnail'];
            $videolazy_full = $row_content['videolazy'];                            
            $newsbody_full = $row_content['newsbody'];
            $wp_media_id = $row_content['wp_media_id'];

            $photos = $row_content['photos'];
            $photos_array = explode(',' , $photos);

            $audio = $row_content['audio'];
            $videoextra = $row_content['videoextra'];

            $wp_id = $row_content['wp_post_id'];

            $data = array();
            $data = json_encode($data);
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

            if($wp_media_id != null)
            {

           
                $response = curl_exec($curl);           
                $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);                    
                curl_close($curl);

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

            if($respCode == 200 || $respCode == 202  || $respCode == 204 )
            {
                

                $sql_del_web = "update web set wp_post_id = null where newsid = '$news_id' ";
                $run_sql_del_web= mysqli_query($connection, $sql_del_web);

                if($run_sql_del_web)
                {
                    $_SESSION['notice_remote'] = "success_remote_delete";
                }

            }
            else
            {
                $_SESSION['notice_remote'] = "Error_remote_delete";
            }






        }


        
    }
}


if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = $_SERVER['HTTP_REFERER'];
}


header("Location: ". $location_redirect);

exit();

