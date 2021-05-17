<?php


// if( count($newsCategories) == 1 && in_array('172' , $newsCategories_final_array))
if( count($newsCategories) == 1 && in_array(    $_SESSION['interview_id'] , $newsCategories_final_array))
{
    
    $byLine = $_POST['byLine'] ;
    $byLine = mysqli_real_escape_string($connection, $byLine);
    $byLine = "'$byLine'";


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

    if(isset($_POST['lang_selec']))
    {
        $lang_selec = $_POST['lang_selec'];
        $lang_selec = mysqli_real_escape_string($connection, $lang_selec);    
        $lang_selec = "'$lang_selec'";
    }
    else
    {
        $lang_selec = "NULL";
    }   
    

    $newsdate = $_POST['newsdate'];
    if(validateDate(date('Y-m-d',strtotime($newsdate))) == 1 )
    {
        $date_status = true;
        $newsdate = mysqli_real_escape_string($connection, $newsdate);
        $newsdate = "'$newsdate'";
    }
    if($date_status)
    {
        if(!is_dir('../'.$newsdate))
        {
            mkdir('../'.$newsdate, 0700 , true);
        }
    }

    $date_file_name = str_replace("-","",$newsdate);
    $time_file_name = date('H:i:s');
    $time_file_name = str_replace(":","",$time_file_name);

    $news_id =  getId();
    $news_id = "'$news_id'";




    
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
            $audio_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_audio.".$fileActualExt_audio;
            $audio_tmp_name = $_FILES['audio']['tmp_name'] ;
            move_uploaded_file($audio_tmp_name, $audio_path) ;
            $audio_path =$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_audio.".$fileActualExt_audio;

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
        $allowed_body_type = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document' , 'text/plain' );


        if (in_array($file_type , $allowed_body_type  ))
        {
            $body_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;
            $body_tmp_name = $_FILES['descFile']['tmp_name'] ;
            move_uploaded_file($body_tmp_name, $body_path) ; 
            $body_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;

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
            $video_long_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolong.".$fileActualExt_videolong;
            $video_long_tmp_name = $_FILES['videoLongFile']['tmp_name'] ;
            move_uploaded_file($video_long_tmp_name, $video_long_path) ;  
            $video_long_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolong.".$fileActualExt_videolong;


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
            $thumbnail_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;
            $thumbnail_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_thumbnail.".$fileActualExt_thumbImg;

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
            $videolazy_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;
            $videolazy_tmp_name = $_FILES['videoLazy']['tmp_name'] ;
            move_uploaded_file($videolazy_tmp_name, $videolazy_path) ;
            $videoLazy_path =$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;

            $videoLazy_path = "'$videoLazy_path'";
        }
        else
        {
            $videoLazy_path = "NULL" ;
        }

    }
    else
    {
        $videoLazy_path = "NULL" ;
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
            $videolazy_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;
            $videolazy_tmp_name = $_FILES['videoLazy']['tmp_name'] ;
            move_uploaded_file($videolazy_tmp_name, $videolazy_path) ;
            $videoLazy_path =$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolazy.".$fileActualExt_videolazy;

            $videoLazy_path = "'$videoLazy_path'";
        }
        else
        {
            $videoLazy_path = "NULL" ;
        }

    }
    else
    {
        $videoLazy_path = "NULL" ;
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
            $videoExtra_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videoextra.".$fileActualExt_videoExtra;
            $videoExtra_tmp_name = $_FILES['videoExtra']['tmp_name'] ;
            move_uploaded_file($videoExtra_tmp_name, $videoExtra_path) ;
            $videoExtra_path =$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videoextra.".$fileActualExt_videoExtra;

            $videoExtra_path = "'$videoExtra_path'";
        }
        else
        {
            $videoExtra_path = "NULL" ;
        }

    }
    else
    {
        $videoExtra_path = "NULL" ;
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
            $gallery_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
            move_uploaded_file($fileTmpName_photo, $gallery_path) ;
            $gallery_path =$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
            array_push($gallery_arr , $gallery_path);            

        }
      

    }
    $gallery_csv = implode("," , $gallery_arr) ;
    $gallery_csv = "'$gallery_csv'";

    $created_at = date('Y-m-d H:i:s');
    $created_at = "'$created_at'";

    $query_new_news = "insert into nas(
        newsid , created_date ,  local_published_date ,
        byline ,  category_list ,                                       
        videolong,  videolazy  ,thumbnail ,
        audio ,  photos ,  newsbody ,  videoextra ,
        tag_list , uploaded_by , reporter ,
        camera_man , district , news_language 
        ) 
        VALUES 
        ($news_id, $created_at ,  $newsdate , 
        $byLine , $newsCategories,
        $video_long_path , $videoLazy_path  , $thumbnail_path,
        $audio_path , $gallery_csv , $body_path , $videoExtra_path ,
        $tags ,$uploaded_by ,  $reporter , 
        $camera_man , $district, $lang_selec
                    
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