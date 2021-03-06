<?php


include "session_handler/session_service.php";
include "connection.php";

include "environment/ftp.php";
include "environment/wp_api_env.php";


include "nas_function/functions.php";
include "../global/timezone.php";


// -------------- VARIABLE DECLARATION ----------------
$archive_path_picture = 'my_data/archive_data/archive_picture';
$archive_path_picture_ftp = 'archive_data/archive_picture';
// ----------------------------------------------------


if(!isset($location))
{
    
    if(isset($_POST['submit']))
    {
        if( isset($_POST['title']) && isset($_POST['date']) ) 
        { 
            if( !empty($_POST['title']) && !empty($_POST['date']) )
            {
            
                
                    $title = $_POST['title'];
                    $title = mysqli_real_escape_string($connection, $title);
                    $title = rtrim ( $title ) ;

                    $date = $_POST['date'];
                    $date = mysqli_real_escape_string($connection, $date);
                    $date_db = $date ;
                    $date = str_replace("-","",$date);

                    $category = '174';

                    

                    
                    $series = $_POST['series'];
                    
                    $series_final_array = array();
                    
                    foreach($series as $s)
                    {
                        if($s != '')
                        {
                            array_push( $series_final_array ,$s );
                        }
                    }
                    $series = implode(',', $series_final_array);



                    $description = $_POST['description'];
                    $description = mysqli_real_escape_string($connection, $description);

                    if($_POST['description'])
                    {
                        $description = "'Description: $description'";
                    }
                    else
                    {
                        $description = "null" ;
                    }


                    $tags = $_POST['newsTag'];
                    $tags_final_array = array();
                    
                    foreach($tags as $t)
                    {
                        if($t != '')
                        {
                            array_push( $tags_final_array ,$t );
                        }
                    }
                    $tags = implode(',', $tags_final_array);



                    
                   


                    $fileName = $_FILES['thumb']['name'] ;
                    $fileExt = explode('.' , $fileName);
                    $fileActualExt_thumbImg = strtolower(end($fileExt));
        
                    $file_type = $_FILES['thumb']['type'] ;
                    $file_type_explode = explode("/" , $file_type);
                    $allowed = array('image' );
        
                    if (in_array($file_type_explode[0] , $allowed ))
                    {
                        $thumbImg_status = true ;               
                    }
                    $thumbImg_status = true ; 
                    $video_long_status = true;

                    
                    $myfile = fopen("../log/archive_picture_log.txt", "a") or die("Unable to open file!"); 

                    fwrite($myfile, "\n--------------- $date_db / $title  ------------------ \n");     
                                   
                    $logged_date = date('Y-m-d H:i:s');
                    fwrite($myfile, "\n Logged Date: $logged_date \n");

                    if( $video_long_status &&  $thumbImg_status )
                    {     
                        
                        $archive_id = getId('archive_photos');
                        $created_at = date('Y-m-d H:i:s');

                        $time_file_name = date('H:i:s');
                        $time_file_name = str_replace(":","",$time_file_name);

                        $date_file_name = $date ;
                        $date_file_name = str_replace("-","",$date_file_name);


                        $title_directory = remove_special_chars($title);
                        mkdir('../'.$archive_path_picture.'/'.$title_directory, 0777 , true);
                        
                    	chmod('../'.$archive_path_picture.'/'.$title_directory, 0777);

                        $text = $archive_path_picture."/".$title_directory ." : Folder Created\n";
                        fwrite($myfile, $text);
                        
                        
                        $gallery_arr = array();
                        $counter = 0 ;
                        foreach ($_FILES["galleryImage"]["name"] as $p => $name)
                        {        
                            
                            $fileName_photo= $_FILES['galleryImage']['name'][$p];
                            $fileTmpName_photo = $_FILES['galleryImage']['tmp_name'][$p];  
                            $fileSize_photo = $_FILES['galleryImage']['size'][$p];
                            // $filenotice_photo = $_FILES['galleryImage']['notice'][$p];
                            $fileType_photo = $_FILES['galleryImage']['type'][$p];            

                            $fileExt_photo = explode('.' , $fileName_photo);
                            $fileActualExt_photo = strtolower(end($fileExt_photo));

                            $file_type_explode = explode("/" , $fileType_photo);
                            $allowed = array('image'  );

                            if (in_array($file_type_explode[0] , $allowed ))
                            {                    
                                $fileTmpName_photo = $_FILES['galleryImage']['tmp_name'][$p];  
                                $fileActualExt_photo = strtolower(end($fileExt_photo));                     
                                // $gallery_path ="../archive/archive_picture/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                                $gallery_path ='../'.$archive_path_picture.'/'.$title_directory."/".$date_file_name."_".$time_file_name."_".$archive_id."_photo_".$counter.".".$fileActualExt_photo;

                                move_uploaded_file($fileTmpName_photo, $gallery_path) ;
                                // $gallery_path ="news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                                $gallery_sql =$archive_path_picture_ftp."/".$title_directory."/".$date_file_name."_".$time_file_name."_".$archive_id."_photo_".$counter.".".$fileActualExt_photo;

                                array_push($gallery_arr , $gallery_sql);      


                                $text = $archive_path_picture.'/'.$title_directory."/".$date_file_name."_".$time_file_name."_".$archive_id."_photo_".$counter.".".$fileActualExt_photo." : Gallery-$counter Uplaoded Succesfully\n";
                                fwrite($myfile, $text);


                                $counter++;      

                            }
                        

                        }
                        $gallery_csv = implode("," , $gallery_arr) ;
                        $gallery_csv_post = $gallery_csv;
                        $gallery_csv = "'$gallery_csv'";
                       
                        if($gallery_csv == '')
                        {
                            $gallery_csv = NULL ;
                        }

                        $fileName = $_FILES['thumb']['name'] ;
                        $fileExt = explode('.' , $fileName);
                        $fileActualExt_thumb = strtolower(end($fileExt));
                        $thumbpath ='../'.$archive_path_picture.'/'.$title_directory."/".$date_file_name."_".$time_file_name."_".$archive_id."_thumbnail.".$fileActualExt_thumb;
                        $thum_name_file =$date_file_name."_".$time_file_name."_".$archive_id."_thumbnail.".$fileActualExt_thumb;
                        $thumbpath_sql =$archive_path_picture_ftp."/".$title_directory."/".$date_file_name."_".$time_file_name."_".$archive_id."_thumbnail.".$fileActualExt_thumb;

                        $thumb_tmp_name = $_FILES['thumb']['tmp_name'] ;
                        move_uploaded_file($thumb_tmp_name, $thumbpath) ;

                        $text = $archive_path_picture.'/'.$title_directory."/".$date_file_name."_".$time_file_name."_".$archive_id."_thumbnail.".$fileActualExt_thumb." : Thumbnail Uplaoded Succesfully\n";
                        fwrite($myfile, $text);


                        // $thum_name = $_FILES['thumb']['tmp_name'] ;
                        // $fileName = $_FILES['thumb']['name'] ;
                        // $thum_name_file = $date."_".time()."_".$fileName;
                        // move_uploaded_file($thum_name, "holds/$thum_name_file") ;
                        //$thumb_send_path = file_get_contents( "holds/$thum_name_file");     
                        
                        $thumb_send_path = file_get_contents( "$thumbpath");  

                        $url = $domain_url.'/wp-json/wp/v2/media';
                        $ch = curl_init();
                            curl_setopt( $ch, CURLOPT_URL, $url );
                            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                            curl_setopt( $ch, CURLOPT_POST, 1 );
                            curl_setopt( $ch, CURLOPT_POSTFIELDS, $thumb_send_path );
                            curl_setopt( $ch, CURLOPT_HTTPHEADER, [

                                'Content-Disposition: form-data; filename="'.$thum_name_file.'"',
                                'Authorization: Bearer '.$token_bearer.''
                            
                                ] );
                            
                            $result = curl_exec( $ch );
                            $result = json_decode($result);
                            $result = json_decode(json_encode($result) , true);    
                            $respCodess = curl_getinfo($ch, CURLINFO_HTTP_CODE);                    
                        curl_close( $ch );

                                        


                        $featured_media_id  = $result['id'];


                        $thumb_send_path = $thumbpath;
                            

                        

                        $ftp = ftp_connect("$ftp_url");
                        ftp_login($ftp, "$ftp_username", "$ftp_password");
                        ftp_pasv($ftp, true);
                        $dir = $title_directory;
                        ftp_mkdir($ftp, "/".$archive_path_picture_ftp."/".$dir);
                        $file_name = end($thumbpath);

                        
                        $files_to_push = array();
                        $array_sent_gallery = array();
                        
                        // ftp_put($ftp, "/".$archive_path_picture_ftp."/$dir/$file_name", "../$thumbpath", FTP_BINARY);


                        // $text = $file_name." : Thumbnail  Pushed to Remote Succesfully\n";
                        // fwrite($myfile, $text);


                        
                        foreach($gallery_arr as $ar)
                        {
                            $file_name = explode("/" , $ar);
                            $file_name = end($file_name);
                            
                            array_push($files_to_push , $file_name);
                            array_push($array_sent_gallery , $file_name);
                            // ftp_put($ftp, "/".$archive_path_picture_ftp."/$dir/$file_name", "../my_data/$ar", FTP_BINARY); 

                            // $text = $file_name." : Gallery  Pushed to Remote Succesfully\n";
                            // fwrite($myfile, $text);

                        }

                        array_push($files_to_push , $thum_name_file);
                        $sent_thumb = $thum_name_file;

                        // ftp_put($ftp, "/$archive_path_picture_ftp/$dir/$thum_name_file", "$thumbpath", FTP_BINARY); 

                        ftp_close($ftp);



                        $news_ftp_path_py  = "/$archive_path_picture_ftp/$dir";
                        $news_ftp_path_py = str_replace(" ","`~",$news_ftp_path_py);
                        $local_file_py = '../my_data'.$news_ftp_path_py.'/';
                        $files_to_push_csv = implode("," , $files_to_push);

                        $sym = "$files_to_push_csv $ftp_url $ftp_username $ftp_password $news_ftp_path_py $local_file_py";

                        $push_remote_py_resp = shell_exec("python ftp_push.py $sym");
                        
                        foreach($array_sent_gallery as $asg)
                        {
                            if (strpos( $push_remote_py_resp, $asg) !== false)
                            {
                                $text = $asg." : Gallery  Pushed to Remote Succesfully\n";
                                fwrite($myfile, $text);
                            }
                            else {
                                
                                $text = $asg." : Gallery  Pushed to Remote Failed\n";
                                fwrite($myfile, $text);
                            }
                        }

                        if (strpos( $push_remote_py_resp, $sent_thumb) !== false)
                        {
                            $text = $sent_thumb." : Thumbnail  Pushed to Remote Succesfully\n";
                            fwrite($myfile, $text);
                        }
                        else {
                            
                            $text = $sent_thumb." : Thumbnail  Pushed to Remote Failed\n";
                            fwrite($myfile, $text);
                        }






                        if(isset($_POST['description']))
                        {
                            $data_array =  array(
                                "status" => "publish" , 
                                "title" => "$title",                    
                                "featured_media" => $featured_media_id,
                                "video_category" => $category,
                                "video_tag" => $tags,

                                "acf_fields" => array( "gallery" => $gallery_csv_post, 'video_thumbnail' => $thumbpath_sql
                            ),
                                
                                "cmb2" => array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
                                                                                'haru_video_url_type'=> 'insert',
                                                                                'haru_video_url' => array('mp4' => '' , 'webm' => '')
                                                                            ),
                                                'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series")

                                                ),
                                            
                                
                                                "content" => $description
                            
                            
                            
                                );
                        }
                        else
                        {
                            $data_array =  array(
                                "status" => "publish" , 
                                "title" => "$title",                    
                                "featured_media" => $featured_media_id,
                                "video_category" => $category,
                                "video_tag" => $tags,

                                "acf_fields" => array( "gallery" => $gallery_csv_post, 'video_thumbnail' => $thumbpath_sql
                            ),
                                
                                "cmb2" => array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
                                                                                'haru_video_url_type'=> 'insert',
                                                                                'haru_video_url' => array('mp4' => '' , 'webm' => '')
                                                                            ),
                                                'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series")

                                                ),
                                            
                                
                                
                            
                            
                            
                                );
                        }


                        $data = json_encode($data_array);

                        $curl = curl_init();
                            curl_setopt_array($curl, array(                    
                            CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_video/",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_POSTFIELDS => $data ,
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


                        $archive_footage_id = $response['id'] ;
                        // echo "Stock Footage ID: ".$response['id']."<br><br>";

                        $text =" Remote Post Created Succesfully\n";
                            fwrite($myfile, $text);

                        
                        
                        foreach($series_final_array as $sc)
                        {

                            $curl = curl_init();
                                curl_setopt_array($curl, array(                    
                                CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_series/$sc",
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
                                $response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] = array($archive_footage_id);
                            }
                            else
                            {
                                array_push($response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] , $archive_footage_id);

                            }

                            // array_push($response['cmb2']['haru_series_attached_videos_field']['haru_series_attached_videos'] , $archive_footage_id);
                            
                            
                            $data_update = json_encode($response);
                            $curl = curl_init();
                                curl_setopt_array($curl, array(                    
                                CURLOPT_URL => "$domain_url/wp-json/wp/v2/haru_series/$sc",
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



                        // store files in folder
                        // store in database 

                    
                            
                $data_blank = '';
                $curl = curl_init();
                curl_setopt_array($curl, array(                    
                CURLOPT_URL => "$domain_url/custom_post_level_creator.php?post_id_nas=".$archive_footage_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data_blank ,
                    CURLOPT_HTTPHEADER => array(
                        
                    ),
                ));

                $response_customapi = curl_exec($curl);
                // $response = json_decode($response);
                // $response = json_decode(json_encode($response) , true);
                // $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                // $err = curl_error($curl);                    
                curl_close($curl);

                        

                        $query_new_archive = "insert into archive_photos(
                            archive_id ,created_date ,  title , series ,tags ,
                            gallery   , thumbnail , published_date,
                            wp_id , wp_media_id , description
                            ) 
                            VALUES 
                            ('$archive_id',  '$date_db' , '$title'  , '$series',
                                '$tags' , $gallery_csv, '$thumbpath_sql' ,  '$created_at',
                                '$archive_footage_id','$featured_media_id' , $description
                                
                                )";    

                            $run_query = mysqli_query($connection , $query_new_archive);
                            echo $query_new_archive;
                    

                    $_SESSION['notice'] = 'success';
                    
                    

                    }
                    else
                    {
                        $_SESSION['notice'] = 'Error_check_file';
                    }




            }
            else
            {
                $_SESSION['notice'] = 'Error_check_file';
            }


        }
        else
        {
            $_SESSION['notice'] = 'Error_check_file';
        }

    }

}

fwrite($myfile, "------------------------------------------------------- "); 
fclose($myfile);

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = '../archivepicture.php';
}

header("Location: ".$location_redirect);
exit();
