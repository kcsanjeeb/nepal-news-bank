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

// exit();
if(isset($_POST['submit']))
{

    $title = $_POST['title'];
    $title = mysqli_real_escape_string($connection, $title);
    // $title = "Test Anjit";
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


    $archive_id = getId('archives');
    $created_at = date('Y-m-d H:i:s');

    $created_at_db = date('Y-m-d');

    $time_file_name = date('H:i:s');
    $time_file_name = str_replace(":","",$time_file_name);

    $date_file_name =  date('Y-m-d');
    $date_file_name = str_replace("-","",$date_file_name);

    $file_name = $date_file_name."_".$time_file_name."_".$archive_id;



    $myfile = fopen("../log/archive_log.txt", "a") or die("Unable to open file!"); 
    fwrite($myfile, "\n--------------- $created_at_db / $title  ------------------ \n");     
                   
    $logged_date = date('Y-m-d H:i:s');
    fwrite($myfile, "\n Logged Date: $logged_date \n");



    $title_directory = remove_special_chars($title);
    mkdir('../'.$archive_path.'/'.$title_directory, 0777 , true);    
    chmod('../'.$archive_path.'/'.$title_directory, 0777);

    $local_path = "../".$archive_path."/".$title_directory ;
    $local_path_clean = "../".$archive_path."/".$title_directory ;
    $ftp_path = $archive_path_ftp."/".$title_directory;


    $ftp = ftp_connect("$ftp_url");
    ftp_login($ftp, "$ftp_username", "$ftp_password");
    ftp_pasv($ftp, true);

    ftp_mkdir($ftp, "/".$ftp_path);

    
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
            $thumbnail_path =$local_path."/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;

            $sourceName = $file_name."_thumbnail.".$fileActualExt_thumbImg;

            if(ftp_put($ftp, $ftp_path."/".$sourceName, $thumbnail_path , FTP_BINARY))
            {
                $thumbnail_path ="$local_path_clean/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
                $thumbnail_path_remote ="$ftp_path/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
                $thumbnail_path_remote_sql = "'$thumbnail_path_remote'";

                        $wp_media_file = file_get_contents($thumbnail_path );
                        $wp_media_file_name = explode("/" ,$thumbnail_path);
                        $wp_media_file_name = end($wp_media_file_name);

                        $url = $domain_url.'/wp-json/wp/v2/media';
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

            }

            else
            {
                $thumbnail_path_remote_sql = "NULL" ;
                $thumbnail_path_remote= "NULL" ;
            }
            
            // send to ftp
            
        }
        else
        {
            $thumbnail_path_remote_sql = "NULL" ;
            $thumbnail_path_remote= "NULL" ;
        }

    }
    else
    {
        $thumbnail_path_remote_sql = "NULL" ;
        $thumbnail_path_remote= "NULL" ;
    }








    if(count($_POST['video']) == count($_FILES['video']['name']))
    {
        $data_videos = array();
        $counter = 0 ;

        foreach($_POST['video'] as $vid)
        {
       
            $desc = mysqli_real_escape_string($connection, $vid['desc']);

            // upload File of $_FILES['video']['name'][$counter]
            // push file to ftp 

                $fileName = $_FILES['video']['name'][$counter] ;      
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['video']['type'][$counter];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {

                    $video_path =$local_path."/".$file_name."_video_".$counter.".".$fileActualExt;
                    $video_tmp_name = $_FILES['video']['tmp_name'][$counter] ;
                    move_uploaded_file($video_tmp_name, $video_path) ;

                    $video_path_remote ="$ftp_path/".$file_name."_video_".$counter.".".$fileActualExt; 
                    $video_path_remote_sql ="'$video_path_remote'";  
                    
                    $sourceName = $file_name."_video_".$counter.".".$fileActualExt;

                    if(ftp_put($ftp, $ftp_path."/".$sourceName, $video_path , FTP_BINARY))
                    {
                        $text = "";
                        fwrite($myfile, "\n ".$file_name."_video_".$counter.".".$fileActualExt." \n");
                    }
                    else {
                       // continue ;
                    }
                    
                    
                }
                else {
                   // continue;
                }


  
            $data = $video_path_remote.":".$desc;

            array_push($data_videos , $data);
            $counter++;

        }

        
    }

    if(count($_POST['pic']) == count($_FILES['pic']['name']))
    {
        $counter = 0 ;
        $data_pics = array();

        foreach($_POST['pic'] as $pic)
        {
            $desc = mysqli_real_escape_string($connection, $pic['desc']);

            $fileName = $_FILES['pic']['name'][$counter] ;      
            $fileExt = explode('.' , $fileName);
            $fileActualExt = strtolower(end($fileExt));
            $file_type =    $_FILES['pic']['type'][$counter];
            $file_type_explode = explode("/" , $file_type);
            $allowed = array('image' );

            if (in_array($file_type_explode[0] , $allowed ))
            {
              

                $pic_path =$local_path."/".$file_name."_picture_".$counter.".".$fileActualExt;
                $pic_tmp_name = $_FILES['pic']['tmp_name'][$counter] ;
                move_uploaded_file($pic_tmp_name, $pic_path) ;

                $pic_path_remote ="$ftp_path/".$file_name."_picture_".$counter.".".$fileActualExt; 
                $pic_path_remote_sql ="'$pic_path_remote'";  

                $sourceName = $file_name."_picture_".$counter.".".$fileActualExt; 

                if(ftp_put($ftp, $ftp_path."/".$sourceName, $pic_path , FTP_BINARY))
                {
                    $text = "";
                        fwrite($myfile, "\n ".$file_name."_picture_".$counter.".".$fileActualExt." \n");
                }else {
                    continue ;
                }

                
                // send to ftp
            }
            else {
               continue;
            }


            $data = $pic_path_remote.":".$desc;

            array_push($data_pics , $data);
            $counter++;
        }


      
    }
    ftp_close($ftp); 

    $tags_array = explode("," , $tags);
    $newsCategories_array = explode("," , $newsCategories);
    $series_array = explode("," , $series);

    $data_videos = implode("," , $data_videos) ;
    $data_pics = implode("," , $data_pics);

    $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
                    'haru_video_url_type'=> 'insert',                  
                        ),

                'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series")
                );


    $data_array =  array(
        "status" => "draft" , 
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

        echo $data ;

        $curl = curl_init();
        curl_setopt_array($curl, array(                    
        CURLOPT_URL => $domain_url."/wp-json/wp/v2/haru_video/",
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
        
        $post_new_id = $response['id'];
        $post_new_id_sql = "'$post_new_id'";

        if(!isset($post_new_id)) $post_new_id = "NULL" ;
        


        $data_blank = '';
        $curl = curl_init();
        curl_setopt_array($curl, array(                    
        CURLOPT_URL => "$domain_url/custom_post_level_creator.php?post_id_nas=".$post_new_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data_blank ,
            CURLOPT_HTTPHEADER => array(
                
            ),
        ));

        $response_customapi = curl_exec($curl);
        curl_close($curl);


        if($series != null && $series != '' )
        {
                $series_exp = explode("," , $series);

                foreach($series_exp as $sn)
                {                  


                $curl = curl_init();
                curl_setopt_array($curl, array(                    
                CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_series/$sn",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                // CURLOPT_POSTFIELDS => $data ,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",
                        'Authorization: Bearer '.$token_bearer.''                            ),
                ));
            
                $response = curl_exec($curl);
                $response = json_decode($response);
                $response = json_decode(json_encode($response) , true);
                $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);                    
            curl_close($curl);





            if($response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] == '')
            {
                $response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] = array($post_new_id);
            }
            else
            {
                array_push($response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] , $post_new_id);

            }



            $data_update = json_encode($response);
            $curl = curl_init();
                curl_setopt_array($curl, array(                    
                CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_series/$sn",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data_update ,
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/json",
                        'Authorization: Bearer '.$token_bearer.''                            ),
                ));
            
                $response = curl_exec($curl);
                $response = json_decode($response);
                $response = json_decode(json_encode($response) , true);
                $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);                    
            curl_close($curl);


            }
        }

        $connection= mysqli_connect($host , $user , $password , $db_name);

        $query_new_archives = "insert into archives(
            archive_id ,created_date ,  title , series ,tags ,
             thumbnail , categories ,archive_videos ,  archive_photos , published_date,
            wp_id , wp_media_id
            ) 
            VALUES 
            ('$archive_id',  '$created_at_db' , '$title'  , '$series',
                '$tags' , $thumbnail_path_remote_sql , '$newsCategories' , '$data_videos' , '$data_pics' , '$created_at',
                $post_new_id_sql,$featured_media_id_sql 
                
                )";    

            $run_query = mysqli_query($connection , $query_new_archives);

            echo $query_new_archives ;



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

header("Location: ".$location_redirect);
exit();


// echo "WP ID : $post_new_id <br>";
// echo "WP ID : $featured_media_id <br>";