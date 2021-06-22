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


// print_r($_POST);
// print_r($_FILES);
// print_r($_FILES['pic']['name'][4]);

// print_r(array_keys($_FILES['pic']['name']));

// exit();




if(isset($_POST['submit']))
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


    $title = $news_row_details['title'];
    $title = rtrim ( $title ) ;



    $tags = $_POST['newsTag'];
    $tags_final_array = array();
    
    foreach($tags as $t)
    {
        if($t != '')
        {
            $t = mysqli_real_escape_string($connection, $t ) ;
            array_push( $tags_final_array ,$t );
        }
    }
    $tags = implode(',', $tags_final_array);
    if($tags == '') $tags = null ;

    $tags_sql = "'$tags'";



    $newsCategories = $_POST['newsCategories'];
    $newsCategories_final_array = array();

    foreach($newsCategories as $nc)
    {
        if($nc != '')
        {
            $nc = mysqli_real_escape_string($connection, $nc ) ;
            array_push( $newsCategories_final_array ,$nc );
        }
    }
    $newsCategories = implode(',', $newsCategories_final_array);
    if($newsCategories == '') $newsCategories = null ;
    $newsCategories_sql = "'$newsCategories'";


    $series_name = $_POST['series'];                    
    $series_final_array = array();
    
    foreach($series_name as $s)
    {
        if($s != '')
        {
            $s = mysqli_real_escape_string($connection, $s ) ;
            array_push( $series_final_array ,$s );
        }
    }
    $series = implode(',', $series_final_array);
    if($series == '') $series = null ;
    $series_sql = "'$series'";


    $archive_id = $news_id;
    $created_at = $news_row_details['published_date'];

    $created_at_db = $news_row_details['created_date'];

    $time_file_name = end(explode(" " ,$created_at ));
    $time_file_name = str_replace(":","",$time_file_name);

    $date_file_name =  $news_row_details['created_date'];
    $date_file_name = str_replace("-","",$date_file_name);

    $file_name = $date_file_name."_".$time_file_name."_".$archive_id;

 

    $myfile = fopen("../log/archive_log.txt", "a") or die("Unable to open file!"); 
    fwrite($myfile, "\n--------------- $created_at_db / $title  ------------------ \n");     
                   
    $logged_date = date('Y-m-d H:i:s');
    fwrite($myfile, "\n Logged Date: $logged_date \n");



    $title_directory = remove_special_chars($title);


    $local_path = "../".$archive_path."/".$title_directory ;
    $local_path_clean = "../".$archive_path."/".$title_directory ;
    $ftp_path = $archive_path_ftp."/".$title_directory;

    $update_query = '';

    $files_to_push = array();
    $videos_py = array();
    $photos_py = array();

    if(isset($_FILES['thumbImg'])   && !empty($_FILES['thumbImg']['name']))
    {
        $fileName = $_FILES['thumbImg']['name'] ;
        $fileExt = explode('.' , $fileName);
        $fileActualExt_thumbImg = strtolower(end($fileExt));

        $file_type = $_FILES['thumbImg']['type'] ;
        $file_type_explode = explode("/" , $file_type);
        $allowed = array('image' );


        if (in_array($file_type_explode[0] , $allowed ))
        {
            if($news_row_details['thumbnail'] != null )
            {
                if(file_exists('../my_data/'.$news_row_details['thumbnail']))
                {
                    unlink('../my_data/'.$news_row_details['thumbnail']);
                }
                if($news_row_details['wp_media_id'] != null)
                    $wp_thumbnail_url = $domain_url.'/wp-json/wp/v2/media/'.$news_row_details['wp_media_id'];
                else
                    $wp_thumbnail_url = $domain_url.'/wp-json/wp/v2/media';
            }
            else
                $wp_thumbnail_url = $domain_url.'/wp-json/wp/v2/media';


            $thumbnail_path =$local_path."/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;

            

            $sourceName = $file_name."_thumbnail.".$fileActualExt_thumbImg;


            ftp_delete_rem('/'.$news_row_details['thumbnail'] , 'file');

            array_push($files_to_push , $sourceName) ;
            $thumb_py = $sourceName ;



                        $wp_media_file = file_get_contents($thumbnail_path );
                        $wp_media_file_name = explode("/" ,$thumbnail_path);
                        $wp_media_file_name = end($wp_media_file_name);

                        $url = $wp_thumbnail_url;
                        $ch = curl_init();
                        curl_setopt( $ch, CURLOPT_URL, $url );
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch, CURLOPT_POST, 1 );
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, $wp_media_file );
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                            'Content-Disposition: form-data; filename="'.$wp_media_file_name.'"',
                            'Authorization: Bearer '.$token_bearer.''
                            ] );
                        
                        $result = curl_exec( $ch );
                        $result = json_decode($result);
                        $result = json_decode(json_encode($result) , true);                        
                        curl_close( $ch );
                        
                        $featured_media_id  = $result['id'];
                        $featured_media_id_sql = "'$featured_media_id'";

                        if(!isset($featured_media_id)) $featured_media_id = "NULL";

                        if($news_row_details['wp_media_id'] == null )
                        {
                            $update_query .= "update archives set wp_media_id = '$featured_media_id' where archive_id = '$news_id' ;" ;
                        }
          
            
        }
      

    }
    else
    {
        $featured_media_id = $news_row_details['wp_media_id'];
    }
  
    
    if(count($_POST['video']) == count($_FILES['video']['name']))
    {
        $data_videos = array();
        $counter = 0 ;
        $counter_file = 0 ;
        $data_vids_rows = array();

        foreach($_POST['video'] as $vid)
        {
    
            $desc = $vid['desc'];
            $data_vids_row['desc'] = $desc ;


            // upload File of $_FILES['video']['name'][$counter]
            // push file to ftp 

                $fileName = $_FILES['video']['name'][$counter] ;      
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['video']['type'][$counter];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if(empty($fileName)) continue ;

                if (in_array($file_type_explode[0] , $allowed ))
                {

                    do{

                        $video_path =$local_path."/".$file_name."_video_".$counter_file.".".$fileActualExt;
                        if(file_exists($video_path)){
                            $status = 1;
                            $counter_file++;
                        } 
                        else $status = 0 ;

                    }while($status);
                    
                        $video_tmp_name = $_FILES['video']['tmp_name'][$counter] ;
                        move_uploaded_file($video_tmp_name, $video_path) ;

                    $video_path_remote ="$ftp_path/".$file_name."_video_".$counter_file.".".$fileActualExt; 
                    $video_path_remote_sql ="'$video_path_remote'";  
                    
                    $sourceName = $file_name."_video_".$counter_file.".".$fileActualExt;

                    array_push($files_to_push , $sourceName) ;
                    array_push($videos_py , $sourceName) ;

                    $data_vids_row['video'] = $video_path_remote ;

                    $counter_file++;

                    
                    
                }            


            array_push($data_vids_rows , $data_vids_row);
            $counter++;

        }

        if($news_row_details['archive_videos'] != null)
        {
            $json_existing_video = $news_row_details['archive_videos'];
            $json_existing_video_decoded = json_decode($json_existing_video , true);

            foreach($data_vids_rows as $newdata)
            {
                array_push($json_existing_video_decoded , $newdata);
            }
            $data_vids_rows = $json_existing_video_decoded ;

        }

        $data_videos = json_encode($data_vids_rows , JSON_UNESCAPED_UNICODE);
        $update_query .= "update archives set archive_videos = '$data_videos' where archive_id = '$news_id';";
        
    }    

    if(!isset($data_videos)) $data_videos = $news_row_details['archive_videos'] ;





    if(count($_POST['pic']) == count($_FILES['pic']['name']))
    {
        $counter = 0 ;
        $file_name_counter = 0;
    

        $data_pics_rows = array();

        $pic_key = array_keys($_FILES['pic']['name']);

        foreach($_POST['pic'] as $pic)
        {
            // $data_pics_row = array();

            $desc =  $pic['desc'];
            $data_pics_row['desc'] = $desc ;

            $data_pics_row['photos'] = array();

            $key_file_pic = $pic_key[$counter];

            $multi_row = 0 ;
              
            foreach($_FILES['pic']['name'][$key_file_pic] as $files_row)
            {
                

                $fileName = $_FILES['pic']['name'][$key_file_pic][$multi_row] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['pic']['type'][$key_file_pic][$multi_row];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('image' );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    if(empty($fileName)) continue ;


                    do{

                        $pic_path =$local_path."/".$file_name."_picture_".$file_name_counter.".".$fileActualExt;
                        if(file_exists($pic_path)){
                            $status = 1;
                            $file_name_counter++;
                        } 
                        else $status = 0 ;

                    }while($status);

                    $pic_tmp_name = $_FILES['pic']['tmp_name'][$key_file_pic][$multi_row];
                    move_uploaded_file($pic_tmp_name, $pic_path) ;

                    $pic_path_remote ="$ftp_path/".$file_name."_picture_".$file_name_counter.".".$fileActualExt; 
                    $pic_path_remote_sql ="'$pic_path_remote'";  

                    $sourceName = $file_name."_picture_".$file_name_counter.".".$fileActualExt; 

    
                    array_push($files_to_push , $sourceName) ;
                    array_push($photos_py , $sourceName) ;

                    array_push($data_pics_row['photos'] , $pic_path_remote) ;

                    
                }
              


                $file_name_counter++;
                $multi_row++;

            }

            // $data = $pic_path_remote."*`".$desc;

            // array_push($data_pics , $data);
            if(count($data_pics_row['photos'] > 0))
                array_push($data_pics_rows , $data_pics_row);
            $counter++;

        }

        if($news_row_details['archive_photos'] != null)
        {
            $json_existing_pics = $news_row_details['archive_photos'];
            $json_existing_pics_decoded = json_decode($json_existing_pics , true);


                foreach($data_pics_rows as $newdata)
                {
                    array_push($json_existing_pics_decoded , $newdata);
                }

            $data_pics_rows = $json_existing_pics_decoded ;

        }

        $data_pics = json_encode($data_pics_rows , JSON_UNESCAPED_UNICODE);
        $update_query .= "update archives set archive_photos = '$data_pics' where archive_id = '$news_id';";



    }
    
    if(!isset($data_pics))  $data_pics= $news_row_details['archive_photos'] ; ;
   

    // echo $data_pics ;



    $news_ftp_path_py  = "/$ftp_path";
    $news_ftp_path_py = str_replace(" ","`~",$news_ftp_path_py);
    $local_file_py = '../my_data'.$news_ftp_path_py.'/';

    $files_to_push_csv = implode("," , $files_to_push);

    $sym = "$files_to_push_csv $ftp_url $ftp_username $ftp_password $news_ftp_path_py $local_file_py";
    $push_remote_py_resp = shell_exec("python ftp_push.py $sym");



    foreach($videos_py as $asg)
    {
        if (strpos( $push_remote_py_resp, $asg) !== false)
        {
                fwrite($myfile, "\n $asg Pushed to remote Success\n");
        }
        else {
            
            fwrite($myfile, "\n $asg Pushed to remote Failes\n");;
        }
    }

    foreach($photos_py as $asg)
    {
        if (strpos( $push_remote_py_resp, $asg) !== false)
        {
                fwrite($myfile, "\n $asg Pushed to remote Success\n");
        }
        else {
            
            fwrite($myfile, "\n $asg Pushed to remote Failes\n");;
        }
    }

    if(isset($thumb_py))
    {
        if (strpos( $push_remote_py_resp, $thumb_py) !== false)
        {
            $thumbnail_path ="$local_path_clean/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_path_remote ="$ftp_path/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_path_remote_sql = "'$thumbnail_path_remote'";

            if($news_row_details['thumbnail'] == null)
                $update_query .= "update archives set thumbnail = $thumbnail_path_remote_sql where archive_id = '$news_id' ;" ;

            fwrite($myfile, "\n $thumb_py Pushed to remote Success\n");;
        }
        else {
            
        
            fwrite($myfile, "\n $thumb_py Pushed to remote Failed\n");;
        }
    }
    else
    {
        $thumbnail_path_remote = $news_row_details['thumbnail']; ;
    }



   

    $tags_array = explode("," , $tags);
    $newsCategories_array = explode("," , $newsCategories);
    $series_array = explode("," , $series);

    // $data_videos = implode("~~" , $data_videos) ;
    // $data_pics = implode("~~" , $data_pics);

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

        // print_r($data_videos);
        // print_r($data_pics);

        $data = json_encode($data_array);

        // echo $data ;

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
        

        $post_new_id = $news_row_details['wp_id'];
        $post_new_id_sql = "'$post_new_id'";

        


        


        // if($series != null && $series != '' )
        // {
        //         $series_exp = explode("," , $series);

        //         foreach($series_exp as $sn)
        //         {                  


        //         $curl = curl_init();
        //         curl_setopt_array($curl, array(                    
        //         CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_series/$sn",
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_TIMEOUT => 30,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "GET",
        //         // CURLOPT_POSTFIELDS => $data ,
        //             CURLOPT_HTTPHEADER => array(
        //                 "cache-control: no-cache",
        //                 "content-type: application/json",
        //                 'Authorization: Bearer '.$token_bearer.''                            ),
        //         ));
            
        //         $response = curl_exec($curl);
        //         $response = json_decode($response);
        //         $response = json_decode(json_encode($response) , true);
        //         $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //         $err = curl_error($curl);                    
        //     curl_close($curl);





        //     if($response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] == '')
        //     {
        //         $response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] = array($post_new_id);
        //     }
        //     else
        //     {
        //         array_push($response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] , $post_new_id);

        //     }



        //     $data_update = json_encode($response);
        //     $curl = curl_init();
        //         curl_setopt_array($curl, array(                    
        //         CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_series/$sn",
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_TIMEOUT => 30,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS => $data_update ,
        //             CURLOPT_HTTPHEADER => array(
        //                 "cache-control: no-cache",
        //                 "content-type: application/json",
        //                 'Authorization: Bearer '.$token_bearer.''                            ),
        //         ));
            
        //         $response = curl_exec($curl);
        //         $response = json_decode($response);
        //         $response = json_decode(json_encode($response) , true);
        //         $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //         $err = curl_error($curl);                    
        //     curl_close($curl);


        //     }
        //     $series = "'$series'";
        // }
        // else {
        //     $series = 'null';
        // }



        $connection= mysqli_connect($host , $user , $password , $db_name);


    $update_query .= "update archives set series =  $series ,tags =  '$tags' , categories = '$newsCategories' where archive_id = '$news_id'; ";

    $run_query = mysqli_multi_query($connection, $update_query);


            // echo $query_new_archives ;



}

fwrite($myfile, "------------------------------------------------------- "); 
fclose($myfile);

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = '../archive.php';
}

// header("Location: ".$location_redirect);
exit();


// echo "WP ID : $post_new_id <br>";
// echo "WP ID : $featured_media_id <br>";