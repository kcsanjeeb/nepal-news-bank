<?php


include "session_handler/session_service.php";
include "connection.php";
include "nas_function/functions.php";

include "../global/timezone.php";











// -------------- VARIABLE DECLARATION ----------------
$interview_path = 'my_data/interview_data';
$news_path = 'my_data/news_data';
// ----------------------------------------------------







if(!isset($location))
{
   
    if(isset($_POST['submit']))
    {
        if(isset($_POST['byLine']) && isset($_POST['newsdate']) && isset($_POST['newsCategories']) && count($_POST['newsCategories']) > 0)   
        {

    
            
            $byLine = $_POST['byLine'] ;
            $byLine = mysqli_real_escape_string($connection, $byLine);
            $byLine_directory = $byLine ;
            $byLine = "'$byLine'";

            $video_type = $_POST['video_type'] ;
            $video_type = mysqli_real_escape_string($connection, $video_type);
            $video_type = "'$video_type'";


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
            $tags = "'$tags'";


            $newsCategories = $_POST['newsCategories'];
            $newsCategories_final_array = array();
            
            if(count($newsCategories) == 1 && $newsCategories[0] == $_SESSION['interview_id'])
            {
                $isInterview = 1 ;
            }
            else
            {
                $isInterview = 0 ;
            }
            
            foreach($newsCategories as $nc)
            {
                if($nc != '')
                {
                    array_push( $newsCategories_final_array ,$nc );
                }
            }
            $newsCategories = implode(',', $newsCategories_final_array);
            $newsCategories = "'$newsCategories'";


            if(isset($_POST['uploaded_by']))
            {
                $uploaded_by = $_POST['uploaded_by'];
                $uploaded_by = mysqli_real_escape_string($connection, $uploaded_by);    
                $uploaded_by = "'$uploaded_by'";
            }
            else
            {
                $uploaded_by = "NULL";
            }

            if(isset($_POST['reporter']))
            {
                $reporter = $_POST['reporter'];
                $reporter = mysqli_real_escape_string($connection, $reporter);    
                $reporter = "'$reporter'";
            }
            else
            {
                $reporter = "NULL";
            }

            if(isset($_POST['camera_man']))
            {
                $camera_man = $_POST['camera_man'];
                $camera_man = mysqli_real_escape_string($connection, $camera_man);    
                $camera_man = "'$camera_man'";
            }
            else
            {
                $camera_man = "NULL";
            }

            if(isset($_POST['district']))
            {
                $district = $_POST['district'];
                $district = mysqli_real_escape_string($connection, $district);    
                $district = "'$district'";
            }
            else
            {
                $district = "NULL";
            }

            // if(isset($_POST['lang_selec']))
            // {
            //     $lang_selec = $_POST['lang_selec'];
            //     $lang_selec = mysqli_real_escape_string($connection, $lang_selec);    
            //     $lang_selec = "'$lang_selec'";
            // }
            // else
            // {
            //     $lang_selec = "NULL";
            // }   
            

            $newsdate = $_POST['newsdate'];
            if(validateDate(date('Y-m-d',strtotime($newsdate))) == 1 )
            {
                $date_status = true;
                $newsdate = mysqli_real_escape_string($connection, $newsdate);
            
            }

            $myfile = fopen("../log/newscollector_log.txt", "a") or die("Unable to open file!");  
                fwrite($myfile, "\n---------------$newsdate / $byLine_directory ---------------- \n");
                $logged_date = date('Y-m-d H:i:s');
                fwrite($myfile, "\n Logged Date: $logged_date \n"); 


            if($date_status)
            {
                $byLine_directory_clean = remove_special_chars($byLine_directory);

                if($isInterview == 1 )
                {
                    if(!is_dir('../'.$interview_path.'/'.$byLine_directory_clean))
                    {
                        mkdir('../'.$interview_path.'/'.$byLine_directory_clean, 777 , true);
                        $text = "$byLine_directory_clean Folder Created\n";
                        fwrite($myfile, $text);
                    }
                }
                else
                {
                    if(!is_dir('../'.$news_path.'/'.$newsdate))
                    {
                        mkdir('../'.$news_path.'/'.$newsdate, 777 , true);
                        mkdir('../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean, 777 , true);
                        $text = "$newsdate Folder Created\n";
                        fwrite($myfile, $text);
                        $text = "$newsdate."/".$byLine_directory_clean Folder Created\n";
                        fwrite($myfile, $text);

                    }
                    else
                    {
                        $byLine_directory_clean = remove_special_chars($byLine_directory);
                        mkdir('../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean, 777 , true);
                        $text = "$byLine_directory_clean Folder Created\n";
                        fwrite($myfile, $text);
                    }
                }
                   
            }

            $date_file_name = str_replace("-","",$newsdate);
            $time_file_name = date('H:i:s');
            $time_file_name = str_replace(":","",$time_file_name);

            $news_id =  getId('nas');
        

            $video_type_i = $_POST['video_type'] ;

        
            if($isInterview == 1 )
            {
                $path_destination ="../".$interview_path."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id;
                $path_sql = $interview_path."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id;

            }
            else
            {
                $path_destination ="../".$news_path."/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id;
                $path_sql =$news_path."/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id;

            }


            
            if(isset($_FILES['audio'])   && !empty($_FILES['audio']['name']))
            {
                $fileName = $_FILES['audio']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_audio = strtolower(end($fileExt));

                $file_type = $_FILES['audio']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('audio'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    // $audio_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_audio.".$fileActualExt_audio;
                    $audio_path =$path_destination."_audio.".$fileActualExt_audio;
                    $audio_tmp_name = $_FILES['audio']['tmp_name'] ;
                    move_uploaded_file($audio_tmp_name, $audio_path) ;
                    // $audio_path ="news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_audio.".$fileActualExt_audio;
                    $audio_path =$path_sql."_audio.".$fileActualExt_audio;

                    $text = "$audio_path : Audio Uploaded\n";
                    fwrite($myfile, $text);



                    $audio_path = "'$audio_path'";
                }
                else
                {
                    $audio_path = "NULL" ;
                }

            }
            else
            {
                $audio_path = "NULL" ;
            }


            if(isset($_FILES['descFile'])   && !empty($_FILES['descFile']['name']))
            {
                $fileNameBody = $_FILES['descFile']['name'] ;

                $fileExt_body = explode('.' , $fileNameBody);
                $fileActualExt_body = strtolower(end($fileExt_body));

                $file_type = $_FILES['descFile']['type'] ;
                $allowed_body_type = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document' , 'text/plain' , 'application/rtf', 'application/msword');


                if (in_array($file_type , $allowed_body_type  ))
                {
                    // $body_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;
                    $body_path =$path_destination."_body.".$fileActualExt_body;

                    $body_tmp_name = $_FILES['descFile']['tmp_name'] ;
                    move_uploaded_file($body_tmp_name, $body_path) ; 
                    // $body_path = "news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;
                    $body_path = $path_sql."_body.".$fileActualExt_body;
                    $text = "$body_path : News Body Uploaded\n";
                    fwrite($myfile, $text);

                    $body_path = "'$body_path'";
                }
                else
                {
                    $body_path = "NULL" ;
                }

            }
            else
            {
                $body_path = "NULL" ;
            }


            if(isset($_FILES['videoLongFile'])   && !empty($_FILES['videoLongFile']['name']))
            {
                $fileName = $_FILES['videoLongFile']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_videolong = strtolower(end($fileExt));

                $file_type = $_FILES['videoLongFile']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );


                if (in_array($file_type_explode[0] , $allowed ))
                {
                    // $video_long_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_videolong.".$fileActualExt_videolong;
                    $video_long_path =$path_destination."_videolong.".$fileActualExt_videolong;

                    $video_long_tmp_name = $_FILES['videoLongFile']['tmp_name'] ;
                    move_uploaded_file($video_long_tmp_name, $video_long_path) ;  
                    $video_long_path = $path_sql."_videolong.".$fileActualExt_videolong;

                    $text = "$video_long_path : Video Long Uploaded\n";
                    fwrite($myfile, $text);

                    

                    // if($video_type_i == 'vimeo')
                    // {
                        
                    //         $video_long_path_vim ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolong.".$fileActualExt_videolong;
                    
                    //         $file_name = $video_long_path_vim;
                    //         $uri = $client->upload($file_name, array(
                    //         "name" => "$news_id"."_videolong",
                    //         "description" => ""
                    //         ));


                    //         $response = $client->request($uri . '?fields=transcode.status');
                        
                    //         $response = $client->request($uri . '?fields=link');

                    //         $id = explode("/" , $response['body']['link']);
                    //         $id = end($id);

                    //         $vimeo_videolong =  "'$id'" ;
                    // }
                    // else
                    // {
                    //     $vimeo_videolong = "NULL";
                    // }

                    $video_long_path = "'$video_long_path'";
                }
                else
                {
                    $video_long_path = "NULL" ;
                }

            }
            else
            {
                $video_long_path = "NULL" ;
                // $vimeo_videolong = "NULL";
            }


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
                    // $thumbnail_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_thumbnail.".$fileActualExt_thumbImg;
                    $thumbnail_path =$path_destination."_thumbnail.".$fileActualExt_thumbImg;
                    $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
                    move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;
                    // $thumbnail_path = "news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_thumbnail.".$fileActualExt_thumbImg;
                    $thumbnail_path = $path_sql."_thumbnail.".$fileActualExt_thumbImg;

                    $text = "$thumbnail_path : Thumbnail Uploaded\n";
                    fwrite($myfile, $text);

                    $thumbnail_path = "'$thumbnail_path'";
                }
                else
                {
                    $thumbnail_path = "NULL" ;
                }

            }
            else
            {
                $thumbnail_path = "NULL" ;
            }


            if(isset($_FILES['videoLazy'])   && !empty($_FILES['videoLazy']['name']))
            {
                $fileName = $_FILES['videoLazy']['name'] ;
                $fileExt_videolazy = explode('.' , $fileName);
                $fileActualExt_videolazy = strtolower(end($fileExt_videolazy));

                $file_type = $_FILES['videoLazy']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    // $videolazy_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;
                    $videolazy_path =$path_destination."_videolazy.".$fileActualExt_videolazy;

                    $videolazy_tmp_name = $_FILES['videoLazy']['tmp_name'] ;
                    move_uploaded_file($videolazy_tmp_name, $videolazy_path) ;
                    // $videoLazy_path ="news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;
                    $videoLazy_path =$path_sql."_videolazy.".$fileActualExt_videolazy;

                    $text = "$videoLazy_path : Video Lazy Uploaded\n";
                    fwrite($myfile, $text);

                    $videoLazy_path = "'$videoLazy_path'";


                    // if($video_type_i == 'vimeo')
                    // {
                        
                    //         $videolazy_path_vim ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;
                    
                            // $file_name = $videolazy_path_vim;
                            // $uri = $client->upload($file_name, array(
                            // "name" => "$news_id"."_videolazy",
                            // "description" => ""
                            // ));


                            // $response = $client->request($uri . '?fields=transcode.status');
                        
                            // $response = $client->request($uri . '?fields=link');

                            // $id = explode("/" , $response['body']['link']);
                            // $id = end($id);

                            // $vimeo_videolazy =  "'$id'" ;
                    // }
                    // else
                    // {
                    //     $vimeo_videolazy = "NULL";
                    // }
                }
                else
                {
                    $videoLazy_path = "NULL" ;
                }

            }
            else
            {
                $videoLazy_path = "NULL" ;
                // $vimeo_videolazy = "NULL";
            }


            
            if(isset($_FILES['videoExtra'])   && !empty($_FILES['videoExtra']['name']))
            {
                $fileName = $_FILES['videoExtra']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_videoExtra = strtolower(end($fileExt));

                $file_type = $_FILES['videoExtra']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    // $videoExtra_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_videoextra.".$fileActualExt_videoExtra;
                    $videoExtra_path =$path_destination."_videoextra.".$fileActualExt_videoExtra;

                    $videoExtra_tmp_name = $_FILES['videoExtra']['tmp_name'] ;
                    move_uploaded_file($videoExtra_tmp_name, $videoExtra_path) ;
                    // $videoExtra_path ="news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_videoextra.".$fileActualExt_videoExtra;
                    $videoExtra_path =$path_sql."_videoextra.".$fileActualExt_videoExtra;

                    $text = "$videoExtra_path : Video Extra Uploaded\n";
                    fwrite($myfile, $text);

                    $videoExtra_path = "'$videoExtra_path'";

                    // if($video_type_i == 'vimeo')
                    // {
                        
                    //     $videoExtra_path_vim ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videoextra.".$fileActualExt_videoExtra;
                    
                    //         $file_name = $videoExtra_path_vim;
                    //         $uri = $client->upload($file_name, array(
                    //         "name" => "$news_id"."_videoextra",
                    //         "description" => ""
                    //         ));


                    //         $response = $client->request($uri . '?fields=transcode.status');
                        
                    //         $response = $client->request($uri . '?fields=link');

                    //         $id = explode("/" , $response['body']['link']);
                    //         $id = end($id);

                    //         $vimeo_videoextra =  "'$id'" ;
                    // }
                    // else
                    // {
                    //     $vimeo_videoextra = "NULL";
                    // }
                }
                else
                {
                    $videoExtra_path = "NULL" ;
                }

            }
            else
            {
                $videoExtra_path = "NULL" ;
                // $vimeo_videoextra = "NULL";
            }

            if(isset($_POST['series']))
            {
                // $series_name = $_POST['series'] ;
                // $series = "'$series_name'";

                $series_name = $_POST['series'];
                    
                    $series_final_array = array();
                    
                    foreach($series_name as $s)
                    {
                        if($s != '')
                        {
                            array_push( $series_final_array ,$s );
                        }
                    }
                    $series = implode(',', $series_final_array);
                    $series = "'$series'";

            }
            else
            {
                $series = 'NULL';
            }




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
                    // $gallery_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                    $gallery_path =$path_destination."_gallery_".$counter.".".$fileActualExt_photo;

                    move_uploaded_file($fileTmpName_photo, $gallery_path) ;
                    // $gallery_path ="news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                    $gallery_path =$path_sql."_gallery_".$counter.".".$fileActualExt_photo;

                    array_push($gallery_arr , $gallery_path);    
                    
                    $text = "$gallery_path : Gallery-$counter Uploaded\n";
                    fwrite($myfile, $text);


                    $counter++;   
                    
                    

                }
            

            }
            $gallery_csv = implode("," , $gallery_arr) ;
            $gallery_csv = "'$gallery_csv'";

            $created_at = date('Y-m-d H:i:s');
            $created_at = "'$created_at'";

            $newsdate = "'$newsdate'";
            $news_id = "'$news_id'";


            $query_new_news = "insert into nas(
                newsid , created_date ,  local_published_date ,
                byline ,  category_list ,                                       
                videolong,  videolazy  ,thumbnail ,
                audio ,  photos ,  newsbody ,  videoextra ,
                tag_list , uploaded_by , reporter ,
                camera_man , district , video_type, series
              
                ) 
                VALUES 
                ($news_id, $created_at ,  $newsdate , 
                $byLine , $newsCategories,
                $video_long_path , $videoLazy_path  , $thumbnail_path,
                $audio_path , $gallery_csv , $body_path , $videoExtra_path ,
                $tags ,$uploaded_by ,  $reporter , 
                $camera_man , $district,  $video_type, $series
              
                
                            
            )";    

            $run_query = mysqli_query($connection , $query_new_news);

            if(!$run_query)
            {
                $_SESSION['notice'] = 'Error';

            }
            else
            {
                $_SESSION['notice'] = 'Success';
            }





            
        }
        else 
        {
            $_SESSION['notice'] = 'Error';
        }

    
        
    }
    else 
    {
        $_SESSION['notice'] = 'Error';
    }

}

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = '../newscollector.php';
}





fwrite($myfile, "------------------------------------------------------- "); 
fclose($myfile);


header("Location: ".$location_redirect);

exit();