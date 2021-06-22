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
        $video_to_delete = $video_row['video'];
        
        // delete in local
        // delete in ftp
        // update mysql
        // update acf
        if(file_exists("../my_data/".$video_to_delete)) unlink("../my_data/".$video_to_delete);
        ftp_delete_rem($video_to_delete , 'file');

        



    }
    else
    {
        $data_videos = $news_row_details['archive_videos'];
    }





}