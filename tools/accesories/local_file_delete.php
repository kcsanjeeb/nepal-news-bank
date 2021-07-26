<?php




ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
include "session_handler/session_service.php";
include "connection.php";
include "nas_function/functions.php";

include "../global/timezone.php";
include "environment/ftp.php";
include "../global/file_paths.php";


if(isset($_POST['newsid']) && isset($_POST['attr']) && isset($_POST['type']))
{
    if(!empty($_POST['newsid']) && !empty($_POST['attr']) && !empty($_POST['type']))
    {
        $attr =  mysqli_real_escape_string($connection, $_POST['attr']);
        $type =  mysqli_real_escape_string($connection, $_POST['type']);

        $news_id =  mysqli_real_escape_string($connection, $_POST['newsid']);
        $query_fetch_news = "select * from nas where newsid = '$news_id'";
        $run_sql_fetch_news= mysqli_query($connection, $query_fetch_news);
        $news_row_details = mysqli_fetch_assoc($run_sql_fetch_news);

        $query_fetch_news_web = "select * from web where newsid = '$news_id'";
        $run_sql_fetch_news_web= mysqli_query($connection, $query_fetch_news_web);
        $num_rows_news_web = mysqli_num_rows($run_sql_fetch_news_web);
        $news_row_details_web = mysqli_fetch_assoc($run_sql_fetch_news_web);
        
        if($num_rows_news_web > 0)
        {
            $remote_pushed = 1 ;
            
            $wp_post_id = $news_row_details_web['wp_post_id'];
            $wp_post_media_id = $news_row_details_web['wp_media_id'];

            $news_remote_existing_path = $news_row_details_web['ftp_dir'];;

            if(isset($wp_post_id))
                $wp_post_created = 1 ;

        }




        if($type == 'single')
        {
            $nas_file = $news_row_details[$attr];  
            $remote_file = $news_row_details_web[$attr]; 
        }
        else
        {
            $nas_file = $news_row_details[$attr];  
            $remote_file = $news_row_details_web[$attr]; 
        }
     

 

        if(file_exists($nas_file)) unlink($nas_file);

        $update_query = '' ;
        $update_query .= "update nas set $attr = null where newsid = '$news_id' ;";


       

        if($remote_pushed)
        {

            if($remote_file != null)
            {
                ftp_delete_rem('/'.$remote_file , 'file');
                $update_query .= "update web set $attr = null where newsid = '$news_id' ;";
            }


            if($wp_post_created)
            { 
                if($attr == 'thumbnail')
                {
                    $update_query .= "update web set wp_media_id = null where newsid = '$news_id' ;";

                    $url_api = $domain_url.'/wp-json/wp/v2/media/'.$wp_post_media_id ;
                    $curl = curl_init();
                    curl_setopt_array($curl, array(                    
                    CURLOPT_URL => $url_api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
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

                $location = "wp_post_update.php?news_id=$news_id&redirect=".$_SERVER['HTTP_REFERER'] ;
            }
            
            
        }

        $connection= mysqli_connect($host , $user , $password , $db_name);
        mysqli_set_charset($connection ,"utf8");
        $run_query = mysqli_multi_query($connection, $update_query);


        
    }
}

if(!isset($location))
{
    $location = $_SERVER['HTTP_REFERER'] ;
}

header("Location: ".$location);
