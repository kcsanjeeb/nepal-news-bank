<?php

include "session_handler/session_service.php";
include "connection.php";

include "environment/ftp.php";
include "environment/wp_api_env.php";


include "nas_function/functions.php";
include "../global/timezone.php";

// -------------- VARIABLE DECLARATION ----------------
$archive_path = 'my_data/archive_data';
$archive_path_ftp = 'archive_data';
// ----------------------------------------------------



if(isset($_POST['newsid']))
{


    $news_id =  mysqli_real_escape_string($connection, $_POST['newsid']);
    $query_fetch_news = "select * from archives where archive_id = '$news_id'";
    $run_sql_fetch_news= mysqli_query($connection, $query_fetch_news);
    $num_rows_news = mysqli_num_rows($run_sql_fetch_news);
    $news_row_details = mysqli_fetch_assoc($run_sql_fetch_news);

    if($num_rows_news <  1)
    {
        exit("Error!");
    }


    $update_query = '';


    if(isset($_POST['type']) && $_POST['type'] == 'video' && isset($_POST['index']) )
    {
        $index = $_POST['index'] ;
        $videos = $news_row_details['archive_videos'] ;
        $videos_decode = json_decode($videos , true);
        $video_row = $videos_decode[$index] ;

        if(isset($_POST['attr_type']) )
        {  
            if( $_POST['attr_type'] == 'video_desc')
            {
                $new_desc = $_POST['row_desc'] ;
                $new_desc =  mysqli_real_escape_string($connection, $new_desc);
                $video_row['desc'] = $new_desc ;

                $data_videos_rows = array();
                $counter_desc = 0 ;
                foreach($videos_decode as $vd)
                {            
                    if($counter_desc == $index) 
                    {
                        array_push($data_videos_rows , $video_row);
                        $counter_desc++;
                        continue ;
                    }
    
                    array_push($data_videos_rows , $vd);
    
                    $counter_desc++;
                }

             
                

            }

        }
        else
        {
            $video_to_delete = $video_row['video'];  
            if(file_exists("../my_data/".$video_to_delete)) unlink("../my_data/".$video_to_delete);
            ftp_delete_rem($video_to_delete , 'file');

            $data_videos_rows = array();
            $counter = 0 ;
            foreach($videos_decode as $vd)
            {            
                if($counter == $index) 
                {
                    $counter++;
                    continue ;
                }

                array_push($data_videos_rows , $vd);

                $counter++;
            }
        }
        
           



            $data_videos = json_encode($data_videos_rows , JSON_UNESCAPED_UNICODE);
            $update_query .= "update archives set archive_videos = '$data_videos' where archive_id = '$news_id';";

    }
    else
    {
        $data_videos = $news_row_details['archive_videos'];
    }


    
    if(isset($_POST['type']) && $_POST['type'] == 'picture' && isset($_POST['index']) )
    {
        $index = $_POST['index'] ;

        $pictures = $news_row_details['archive_photos'] ;
        $pictures_decode = json_decode($pictures , true);

        $pictures_row = $pictures_decode[$index] ;

        $pictures_to_delete = $pictures_row['photos'];

   

        
        // delete in local
        // delete in ftp
        // update mysql
        // update acf

        if(isset($_POST['attr_type']) )
        {  
            if( $_POST['attr_type'] == 'pics_desc')
            {
                $new_desc = $_POST['row_desc'] ;
                $new_desc =  mysqli_real_escape_string($connection, $new_desc);

                $pictures_row['desc'] = $new_desc ;

                $data_pics_rows = array();
                $counter_desc = 0 ;
                foreach($pictures_decode as $vd)
                {            
                    if($counter_desc == $index) 
                    {
                      
                        array_push($data_pics_rows , $pictures_row);
                        $counter_desc++;
                        continue ;
                    }
    
                    array_push($data_pics_rows , $vd);
    
                    $counter_desc++;
                }
      
               


            }

        }

        else
        {        
                foreach($pictures_to_delete as $pics_del)
                {
                    if(file_exists("../my_data/".$pics_del)) unlink("../my_data/".$pics_del);
                    ftp_delete_rem($pics_del , 'file');
                }
            
                $data_pics_rows = array();

                $counter = 0 ;
                foreach($pictures_decode as $pd)
                {
                if($counter == $index) 
                    {
                        $counter++;
                        continue ;
                    }

                    array_push($data_pics_rows , $pd);
                    $counter++;
                }
        }



            $data_pics = json_encode($data_pics_rows , JSON_UNESCAPED_UNICODE);
            $update_query .= "update archives set archive_photos = '$data_pics' where archive_id = '$news_id';";

    }
    else
    {
        $data_pics = $news_row_details['archive_photos'];
    }



    
    $series = $news_row_details['series'];
    $title = $news_row_details['title'];
    $thumbnail_path_remote = $news_row_details['thumbnail'];    
    $featured_media_id = $news_row_details['wp_media_id'];  

    $cat_array = explode("," ,$news_row_details['categories'] );
    $tags_array = explode("," ,$news_row_details['tags'] );
    $newsCategories_array = $cat_array;
    $tags_array =  $tags_array;



        $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
        'haru_video_url_type'=> 'insert',                  
            ),

        'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series")
        );


        $data_array =  array(
        "status" => "publish" , 
        "title" => "$title",
        "slug" => "$title",
        "acf_fields" => array(  "archive_video_all" => $data_videos,
                        "archive_picture_all" => $data_pics,
                            
                        "video_thumbnail" => $thumbnail_path_remote
                    
        ),
        "featured_media" => $featured_media_id,
        "video_category" => $newsCategories_array,
        "video_tag" => $tags_array,

        "cmb2" => $cmb2 ,       



        );


        $data = json_encode($data_array);


        $curl = curl_init();
        curl_setopt_array($curl, array(                    
        CURLOPT_URL => $domain_url."/wp-json/wp/v2/haru_video/".$news_row_details['wp_id'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data ,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                'Authorization: Bearer '.$token_bearer.''
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response);
        $response = json_decode(json_encode($response) , true);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);                    
        curl_close($curl);



        $connection= mysqli_connect($host , $user , $password , $db_name);
        $run_query = mysqli_multi_query($connection, $update_query);







}