<?php

include "session_handler/session_service.php";
include "connection.php";

include "environment/ftp.php";
include "environment/wp_api_env.php";


include "nas_function/functions.php";
include "../global/timezone.php";

include "../global/file_paths.php";

// -------------- VARIABLE DECLARATION ----------------

// $local_archive_all_path = "E:/archive_data" ;
// $ftp_archive_all_path = "my_data/archive_data" ;



// $archive_path = 'my_data/archive_data';
// $archive_path_ftp = 'archive_data';

$archive_path = $local_archive_all_path ;
$archive_path_ftp = $ftp_archive_all_path ;
// ----------------------------------------------------


// print_r($_POST);
// print_r($_FILES);
// print_r($_FILES['pic']['name'][4]);

// print_r(array_keys($_FILES['pic']['name']));

// exit();




if(isset($_POST['submit']))
{

    $title = $_POST['title'];
    $title = mysqli_real_escape_string($connection, $title);
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

    $folder_root_exist_check_array = explode("/" ,$archive_path  );
    $drive_root = $folder_root_exist_check_array[0];
    if(!is_dir($drive_root))
    {
        $_SESSION['notice'] = 'Error';
        goto error ;
    }
    else
    {
        array_shift($folder_root_exist_check_array);
        $path_root_folders = $drive_root ;
        foreach($folder_root_exist_check_array as $folder_name)
        {
            if(empty($folder_name)) continue ;

            $path_root_folders = $path_root_folders.'/'.$folder_name ;
            if(!is_dir($path_root_folders))
            {
                mkdir($path_root_folders, 0777 , true);                        
                chmod($path_root_folders, 0777);
            }

        }
    }

   
    

    $title_directory = remove_special_chars($title);


    mkdir($path_root_folders.'/'.$title_directory, 0777 , true);    
    chmod($path_root_folders.'/'.$title_directory, 0777);

    $local_path = $path_root_folders."/".$title_directory ;
    $local_path_clean = $path_root_folders."/".$title_directory ;
    $ftp_path = $archive_path_ftp."/".$title_directory;


    $ftp = ftp_connect("$ftp_url");
    ftp_login($ftp, "$ftp_username", "$ftp_password");
    ftp_pasv($ftp, true);

    ftp_mkdir($ftp, "/".$ftp_path);

    


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
            $thumbnail_path =$local_path."/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;
            $thumbnail_local_path = $thumbnail_path ;

            $sourceName = $file_name."_thumbnail.".$fileActualExt_thumbImg;

            array_push($files_to_push , $sourceName) ;
            $thumb_py = $sourceName ;

            // if(ftp_put($ftp, $ftp_path."/".$sourceName, $thumbnail_path , FTP_BINARY))
            // {
                // $thumbnail_path ="$local_path_clean/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
                // $thumbnail_path_remote ="$ftp_path/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
                // $thumbnail_path_remote_sql = "'$thumbnail_path_remote'";

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

            // }
            // else
            // {
            //     $thumbnail_path_remote_sql = "NULL" ;
            //     $thumbnail_path_remote= "NULL" ;
            // }
            
          
            
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

        

        $data_vids_rows = array();

        $row_dir_name_root = "archive_videos";

        mkdir($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root, 0777 , true);    
        chmod($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root, 0777);

        ftp_mkdir($ftp, "/".$ftp_path.'/'.$row_dir_name_root);

        
        $folder_counter = 1 ;
        $file_counter = 1 ;


        foreach($_POST['video'] as $vid)
        {
    
           


       
            $desc = $vid['desc'];

            if(isset($desc) && !empty($desc))
            {
                $data_vids_row['desc'] = $desc ;
            }
            else
            {
                $data_vids_row['desc'] = "Archive video description." ;
            }




            // upload File of $_FILES['video']['name'][$counter]
            // push file to ftp 

                $fileName = $_FILES['video']['name'][$counter] ;      
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['video']['type'][$counter];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                $row_dir_name = "archive_video_$folder_counter";
                $row_file_path = $row_dir_name_root."/".$row_dir_name;  
                if(isset($fileName) && !empty($fileName) )
                {
                                  
                    mkdir($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777 , true);    
                    chmod($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777);    
                    ftp_mkdir($ftp,  "/".$ftp_path."/".$row_dir_name_root."/".$row_dir_name);
                    
                }

                if (in_array($file_type_explode[0] , $allowed ))
                {

                    $video_path =$local_path."/".$row_file_path."/".$file_name."_video_".$file_counter.".".$fileActualExt;
                    $video_tmp_name = $_FILES['video']['tmp_name'][$counter] ;
                    move_uploaded_file($video_tmp_name, $video_path) ;

                    $video_path_remote ="$ftp_path/".$row_file_path."/".$file_name."_video_".$file_counter.".".$fileActualExt; 
                    $video_path_remote_sql ="'$video_path_remote'";  
                    
                    $sourceName = $file_name."_video_".$file_counter.".".$fileActualExt;
                    $sourceName = $row_file_path."/".$sourceName ;

                    array_push($files_to_push , $sourceName) ;
                    array_push($videos_py , $sourceName) ;

                    $data_vids_row['video'] = $video_path_remote ;
                    $data_vids_row['video_local'] = $video_path ;

                    // if(ftp_put($ftp, $ftp_path."/".$sourceName, $video_path , FTP_BINARY))
                    // {
                   
                    //     $text = "";
                    //     fwrite($myfile, "\n ".$file_name."_video_".$counter.".".$fileActualExt." \n");
                    // }
                    // else {
                    //    // continue ;
                    // }
                    
                    
                }
                else {
                   // continue;
                }


            
            if(isset($data_vids_row['video'] ) && !empty($data_vids_row['video'] ))
            {
                array_push($data_vids_rows , $data_vids_row);

                // array_push($data_videos , $data);
                $counter++;
                $folder_counter++;
                $file_counter++;
            }
                

        }

        $data_videos = json_encode($data_vids_rows , JSON_UNESCAPED_UNICODE);

        
    }
    else
    {
        $data_videos = array() ;
        $data_videos = json_encode($data_videos , JSON_UNESCAPED_UNICODE);
    }

    if(count($_POST['pic']) == count($_FILES['pic']['name']))
    {
        $counter = 0 ;
        
    

        $data_pics_rows = array();

        $pic_key = array_keys($_FILES['pic']['name']);

        $row_dir_name_root = "archive_pictures";

        mkdir($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root, 0777 , true);    
        chmod($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root, 0777);

        ftp_mkdir($ftp, "/".$ftp_path.'/'.$row_dir_name_root);

        $file_name_counter = 1;
        $folder_counter = 1 ;


        foreach($_POST['pic'] as $pic)
        {
            // $data_pics_row = array();
            // if(count($_POST['pic']))

            



            $desc =  $pic['desc'];
            

            if(isset($desc) && !empty($desc))
            {
                $data_pics_row['desc'] = $desc ;
            }
            else
            {
                $data_pics_row['desc'] = "Archive picture description." ;
            }

            $data_pics_row['photos'] = array();
            $data_pics_row['photos_local'] = array();

            $key_file_pic = $pic_key[$counter];

            $multi_row = 0 ;
            
            $folder_made = 0  ;
            foreach($_FILES['pic']['name'][$key_file_pic] as $files_row)
            {
               

                $fileName = $_FILES['pic']['name'][$key_file_pic][$multi_row] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['pic']['type'][$key_file_pic][$multi_row];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('image' );

                $row_dir_name = "archive_picture_$folder_counter";
                $row_file_path = $row_dir_name_root."/".$row_dir_name;  
                if(isset($fileName) && !empty($fileName) && $folder_made == 0 )
                {                                  
                    mkdir($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777 , true);    
                    chmod($path_root_folders.'/'.$title_directory.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777);
                    ftp_mkdir($ftp,  "/".$ftp_path."/".$row_dir_name_root."/".$row_dir_name);
                    $folder_made = 1 ;
                }
                   

                if(empty($fileName)) continue ;

                if (in_array($file_type_explode[0] , $allowed ))
                {
                  

                    $pic_path =$local_path."/".$row_file_path."/".$file_name."_picture_".$file_name_counter.".".$fileActualExt;
                    $pic_tmp_name = $_FILES['pic']['tmp_name'][$key_file_pic][$multi_row];
                    move_uploaded_file($pic_tmp_name, $pic_path) ;

                    $pic_path_remote ="$ftp_path/".$row_file_path."/".$file_name."_picture_".$file_name_counter.".".$fileActualExt; 
                    $pic_path_remote_sql ="'$pic_path_remote'";  

                    $sourceName = $file_name."_picture_".$file_name_counter.".".$fileActualExt; 
                    $sourceName = $row_file_path."/".$sourceName ;

                    // if(ftp_put($ftp, $ftp_path."/".$sourceName, $pic_path , FTP_BINARY))
                    // {
                        // $text = "";

                        //     fwrite($myfile, "\n ".$file_name."_picture_".$file_name_counter.".".$fileActualExt." \n");

                    // }else {
                    //     continue ;
                    // }

                    array_push($files_to_push , $sourceName) ;
                    array_push($photos_py , $sourceName) ;

                    array_push($data_pics_row['photos'] , $pic_path_remote) ;

                    array_push($data_pics_row['photos_local'] , $pic_path) ;

                    
                }
                else {

                    // continue;
                }


                $file_name_counter++;
                $multi_row++;

            }

            // $data = $pic_path_remote."*`".$desc;

            // array_push($data_pics , $data);
            if(count($data_pics_row['photos'] > 0))
            {
                array_push($data_pics_rows , $data_pics_row);
                
                $counter++;
                $folder_counter++;
            }
              

        }

        $data_pics = json_encode($data_pics_rows , JSON_UNESCAPED_UNICODE);

    }
    else
    {
        $data_pics = array() ;
        $data_pics = json_encode($data_pics , JSON_UNESCAPED_UNICODE);

    }

    // echo $data_pics ;
    // echo $data_pics ;

    ftp_close($ftp); 




    $news_ftp_path_py  = "/$ftp_path";
    $news_ftp_path_py = str_replace(" ","`~",$news_ftp_path_py);

    // $local_file_py = '../my_data'.$news_ftp_path_py.'/';

    $local_file_py = $local_path.'/';

    $files_to_push_csv = implode("," , $files_to_push);

    $local_file_py = str_replace(" ","`~",$local_file_py);

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

    if (strpos( $push_remote_py_resp, $thumb_py) !== false)
    {
        $thumbnail_path ="$local_path_clean/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
        $thumbnail_path_remote ="$ftp_path/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
        $thumbnail_path_remote_sql = "'$thumbnail_path_remote'";

        fwrite($myfile, "\n $thumb_py Pushed to remote Success\n");;
    }
    else {
        
        $thumbnail_path_remote_sql = "NULL" ;
        $thumbnail_path_remote= "NULL" ;
        fwrite($myfile, "\n $thumb_py Pushed to remote Failes\n");;
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
            $series = "'$series'";
        }
        else {
            $series = 'null';
        }

        $connection= mysqli_connect($host , $user , $password , $db_name);
 
        $query_new_archives = "insert into archives(
            archive_id ,created_date ,  title , series ,tags ,
             thumbnail , categories ,archive_videos ,  archive_photos , published_date,
            wp_id , wp_media_id ,  thumbnail_local_path , local_dir , ftp_dir
            ) 
            VALUES 
            ('$archive_id',  '$created_at_db' , '$title'  , $series,
                '$tags' , $thumbnail_path_remote_sql , '$newsCategories' , '$data_videos' , '$data_pics' , '$created_at',
                $post_new_id_sql,$featured_media_id_sql , '$thumbnail_local_path' , '$archive_path' , '$archive_path_ftp'
                
                )"; 
                // echo   $query_new_archives ; 

            $run_query = mysqli_query($connection , $query_new_archives);

            // echo $query_new_archives ;



}
error:
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