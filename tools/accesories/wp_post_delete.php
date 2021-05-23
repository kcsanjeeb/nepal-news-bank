<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/wp_api_env.php";
include "nas_function/functions.php";
include "../global/timezone.php";



if(!isset($location))
{ 
    if(isset($_POST['del_remote']) && isset($_POST['news_id']) && isset($_POST['wp_id']))
    {
        if(!empty($_POST['del_remote']) && !empty($_POST['news_id']) && !empty($_POST['wp_id']))
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

                $response = curl_exec($curl);           
                $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);                    
                curl_close($curl);


                if($wp_media_id != null)
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


              
                    echo $respCode;
                }
                // echo "<br> Responce:  $response";

                // if($respCode == 200 || $respCode == 202  || $respCode == 204 )
                // {
                    

                    $sql_del_web = "update web set wp_post_id = null, wp_media_id = null,  wp_post_type= null  where newsid = '$news_id' ";
                    $run_sql_del_web= mysqli_query($connection, $sql_del_web);

                    if($run_sql_del_web)
                    {
                        $_SESSION['notice_remote'] = "success_remote_delete";
                    }

                // }
                // else
                // {
                //     $_SESSION['notice_remote'] = "Error_remote_delete";
                // }






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