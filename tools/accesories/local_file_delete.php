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


        if($type == 'bonus')
        {
            $file =  mysqli_real_escape_string($connection, $_POST['file']);
            if(file_exists($file)) unlink($file);
            goto end ;
        }





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



        $update_query = '' ;

        if($type == 'single')
        {
            $nas_file = $news_row_details[$attr];  
            $remote_file = $news_row_details_web[$attr]; 
            $value = 'null';
            $value_nas = 'null';          
     
        }

        if($type == 'multiple')
        {
            if(isset($_POST['file']))
            {
                $file =  mysqli_real_escape_string($connection, $_POST['file']);

                $file_name_explode = explode("/" , $file);
                $file_name_only = end($file_name_explode);
                echo $file_name_only ; 

                $nas_files = $news_row_details[$attr];  
                $remote_files = $news_row_details_web[$attr]; 

                $nas_file_explode = explode("," , $nas_files);
                $remote_files_explode = explode("," , $remote_files);

                $counter = -1 ;
                foreach($remote_files_explode as $rf)
                {
                    $each_value = explode("/" ,$rf );
                    $each_value_file_name = end($each_value);

                    $counter++ ;
                    if($each_value_file_name == $file_name_only)
                    {
                        
                        break ;
                    }
                }

                if($counter >= 0)
                {
                    $remote_file = $remote_files_explode[$counter] ;
                    unset($remote_files_explode[$counter]);
                    $remote_files_explode = array_values($remote_files_explode); 
                    if(count($remote_files_explode) > 0)
                    {
                        $value = implode("," , $remote_files_explode);
                        $value = "'$value'";
                    }
                    else
                    {
                        $value  = 'null';
                    }
                    
              
                    
                }

                $counter_nas = -1 ;
                foreach($nas_file_explode as $nf)
                {
                    $each_value = explode("/" ,$nf );
                    $each_value_file_name = end($each_value);
                    $counter_nas++ ;
                    if($each_value_file_name == $file_name_only)
                    {                        
                        break ;
                    }
                }

                if($counter_nas >= 0)
                {
                    $nas_file = $nas_file_explode[$counter_nas] ;
                    unset($nas_file_explode[$counter_nas]);
                    $nas_file_explode = array_values($nas_file_explode); 
                    if(count($nas_file_explode) > 0)
                    {
                        $value_nas = implode("," , $nas_file_explode);
                        $value_nas = "'$value_nas'";
                    }
                    else
                    {
                        $value_nas  = 'null';
                    }
             
                }




            }
        }
        
       

  
        if(file_exists($nas_file)) unlink($nas_file);

        $update_query .= "update nas set $attr = $value_nas where newsid = '$news_id' ;";

       

        if($remote_pushed)
        {

            if($remote_file != null)
            {
                ftp_delete_rem('/'.$remote_file , 'file');
                $update_query .= "update web set $attr = $value where newsid = '$news_id' ;";
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

end: 

if(!isset($location))
{
    $location = $_SERVER['HTTP_REFERER'] ;
}

header("Location: ".$location);
