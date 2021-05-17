else
    {

        if( isset($_POST['byLine']) && isset($_POST['newsTag']) && isset($_FILES['descFile'])  && isset($_FILES['videoLongFile'])
            && isset($_FILES['galleryImage']) && isset($_FILES['thumbImg'])
            
            && isset($_POST['newsdate']) &&   isset($_POST['lang_selec'])

            && isset($_POST['uploaded_by']) && isset($_POST['reporter']) && isset($_POST['camera_man'])  
            && isset($_POST['district'])  && isset($_POST['newsCategories']) ) 
            
        {
            if(!empty($_POST['byLine']) && !empty($_POST['newsTag']) && !empty($_FILES['descFile']['name'])  && !empty($_FILES['videoLongFile']['name'])
            && count($_FILES['galleryImage']['name']) > 0 && !empty($_FILES['thumbImg']['name'])
        

            && !empty($_POST['uploaded_by']) && !empty($_POST['reporter']) && !empty($_POST['camera_man'])  
            && !empty($_POST['district']) 
            && !empty($_POST['newsdate']) &&   !empty($_POST['lang_selec']) && !empty($_POST['newsCategories']) 
            
            )
            {
                
                $byLine = $_POST['byLine'] ;
                $byLine = mysqli_real_escape_string($connection, $byLine);

                $byline_length = strlen($byLine) ;
                if($byline_length <= 300)
                {
                    $byline_lenght_status = true ;
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




                // new attributes 

                $uploaded_by = $_POST['uploaded_by'];
                $uploaded_by = mysqli_real_escape_string($connection, $uploaded_by);

                $reporter = $_POST['reporter'];
                $reporter = mysqli_real_escape_string($connection, $reporter);

                $camera_man = $_POST['camera_man'];
                $camera_man = mysqli_real_escape_string($connection, $camera_man);

                $district = $_POST['district'];
                $district = mysqli_real_escape_string($connection, $district);

                

                $lang_selec = $_POST['lang_selec'];
                $lang_selec = mysqli_real_escape_string($connection, $lang_selec);

                $newsdate = $_POST['newsdate'];
                if(validateDate(date('Y-m-d',strtotime($newsdate))) == 1 )
                {
                    $date_status = true;
                    $newsdate = mysqli_real_escape_string($connection, $newsdate);
                }




            
                

                
                if (in_array($lang_selec, $news_lang_array))
                {
                    $news_lang_status = true ;
                    if($lang_selec == 'nepali_uni')
                    {
                        $lang_selec = 'nepali' ;
                    }
                    
                }
            


                $news_id =  getId();
                $todaysdate = date("Y-m-d");

            
            if($date_status)
            {
                if(!is_dir('../'.$newsdate))
                {
                    mkdir('../'.$newsdate, 0700 , true);
                }
            }
                


                // $news_body_extract = kv_read_word($_FILES['descFile']['tmp_name']);

                // if($news_body_extract !== false) 
                // {	
                //     $newsbody_status = true ;	
                //     $news_body =  nl2br($news_body_extract);
                //     // $news_body = mysqli_real_escape_string($connection, $news_body);	
                //    echo $news_body ;
                // }
            
                $fileNameBody = $_FILES['descFile']['name'] ;

                $fileExt_body = explode('.' , $fileNameBody);
                $fileActualExt_body = strtolower(end($fileExt_body));

                $file_type = $_FILES['descFile']['type'] ;
                $allowed_body_type = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document' , 'text/plain' );


                if (in_array($file_type , $allowed_body_type ))
                {
                    $newsbody_status = true ;           
                }

                



                $fileName = $_FILES['videoLongFile']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_videolong = strtolower(end($fileExt));

                $file_type = $_FILES['videoLongFile']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    $video_long_status = true ;               
                }



                $fileName = $_FILES['thumbImg']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_thumbImg = strtolower(end($fileExt));

                $file_type = $_FILES['thumbImg']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('image' );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    $thumbImg_status = true ;               
                }



                // $fileName = $_FILES['videoLazy']['name'] ;
                // $fileExt = explode('.' , $fileName);
                // $fileActualExt_videoLazy = strtolower(end($fileExt));

                // $file_type = $_FILES['videoLazy']['type'] ;
                // $file_type_explode = explode("/" , $file_type);
                // $allowed = array('video'  );

                // if (in_array($file_type_explode[0] , $allowed ))
                // {
                //     $videoLazy_status = true ;
                
                // }

                
                if(isset($_FILES['videoLazy'])   && !empty($_FILES['videoLazy']['name']))
                {
                    $videolazy_exist = true ;

                    $fileName = $_FILES['videoLazy']['name'] ;
                    $fileExt_videolazy = explode('.' , $fileName);
                    $fileActualExt_videolazy = strtolower(end($fileExt_videolazy));

                    $file_type = $_FILES['videoLazy']['type'] ;
                    $file_type_explode = explode("/" , $file_type);
                    $allowed = array('video'  );

                    if (in_array($file_type_explode[0] , $allowed ))
                    {
                        $videoLazy_status = true ;

                    }


                }
                else
                {
                    $videoLazy_status = true ;
                    $videolazy_exist = false ;
                }


                if(isset($_FILES['audio'])   && !empty($_FILES['audio']['name']))
                {
                    $audio_exist = true ;

                    $fileName = $_FILES['audio']['name'] ;
                    $fileExt = explode('.' , $fileName);
                    $fileActualExt_audio = strtolower(end($fileExt));

                    $file_type = $_FILES['audio']['type'] ;
                    $file_type_explode = explode("/" , $file_type);
                    $allowed = array('audio'  );

                    if (in_array($file_type_explode[0] , $allowed ))
                    {
                        $audio_status = true ;

                    }


                }
                else
                {
                    $audio_status = true ;
                    $audio_exist = false ;
                }

                if(isset($_FILES['videoExtra'])   && !empty($_FILES['videoExtra']['name']))
                {
                    $videoExtra_exist = true ;

                    $fileName = $_FILES['videoExtra']['name'] ;
                    $fileExt = explode('.' , $fileName);
                    $fileActualExt_videoExtra = strtolower(end($fileExt));

                    $file_type = $_FILES['videoExtra']['type'] ;
                    $file_type_explode = explode("/" , $file_type);
                    $allowed = array('video'  );

                    if (in_array($file_type_explode[0] , $allowed ))
                    {
                        $videoExtra_status = true ;

                    }


                }
                else
                {
                    $videoExtra_status = true ;
                    $videoExtra_exist = false ;
                }



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
                        $gallery_status = true ;                  

                    }
                    else
                    {
                        $gallery_status = false ;
                        break ;
                    }




                }


                $fileName_vl_valid = $_FILES['videoLongFile']['name'] ;
                $fileName_vlazy_valid = $_FILES['videoLazy']['name'] ;

                if($fileName_vl_valid != $fileName_vlazy_valid)
                {
                    $news_long_lazy_valid = true;
                }



                if(is_dir('../'.$newsdate))
                {
                    if($byline_lenght_status && $news_lang_status && $news_long_lazy_valid && $newsbody_status && $video_long_status &&  $thumbImg_status  &&  $videoLazy_status && $gallery_status && $audio_status && $videoExtra_status)
                    {

                        $date_file_name = str_replace("-","",$newsdate);
                        $time_file_name = date('H:i:s');
                        $time_file_name = str_replace(":","",$time_file_name);

                        $body_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;
                        $body_tmp_name = $_FILES['descFile']['tmp_name'] ;
                        move_uploaded_file($body_tmp_name, $body_path) ; 
                        $body_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;


                        $video_long_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolong.".$fileActualExt_videolong;
                        $video_long_tmp_name = $_FILES['videoLongFile']['tmp_name'] ;
                        move_uploaded_file($video_long_tmp_name, $video_long_path) ;  
                        $video_long_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videolong.".$fileActualExt_videolong;

                    

                        $thumbnail_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_thumbnail.".$fileActualExt_thumbImg;
                        $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
                        move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;
                        $thumbnail_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_thumbnail.".$fileActualExt_thumbImg;


                        // $videolazy_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videoLazy.".$fileActualExt_videoLazy;
                        // $videolazy_tmp_name = $_FILES['videoLazy']['tmp_name'] ;
                        // move_uploaded_file($videolazy_tmp_name, $videolazy_path) ;
                        // $videolazy_path = $newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_videoLazy.".$fileActualExt_videoLazy;


                        $counter = 0 ;
                        $gallery_arr = array();
                        foreach ($_FILES["galleryImage"]["name"] as $p => $name)
                        {           
                            
                            $fileTmpName_photo = $_FILES['galleryImage']['tmp_name'][$p];  
                            $fileActualExt_photo = strtolower(end($fileExt_photo));                     
                            $gallery_path ="../".$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                            move_uploaded_file($fileTmpName_photo, $gallery_path) ;
                            $gallery_path =$newsdate."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                            array_push($gallery_arr , $gallery_path);               

                            $counter++ ;

                        }
                        $gallery_csv = implode("," , $gallery_arr) ;

                        if($audio_exist == true)
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

                        if($videoExtra_exist == true)
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

                    

                        if($videolazy_exist == true)
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

                        $created_at = date('Y-m-d H:i:s');

                        $query_new_news = "insert into nas(
                                            newsid , created_date ,  local_published_date ,
                                            byline ,  category_list ,                                       
                                            videolong,  videolazy  ,thumbnail ,
                                            audio ,  photos ,  newsbody ,  videoextra ,
                                            tag_list , uploaded_by , reporter ,
                                            camera_man , district , news_language 
                                            ) 
                                            VALUES 
                                            ('$news_id', '$created_at' ,  '$newsdate' , 
                                            '$byLine' , '$newsCategories',
                                            '$video_long_path' , $videoLazy_path  , '$thumbnail_path',
                                            $audio_path , '$gallery_csv' , '$body_path' , $videoExtra_path ,
                                            '$tags' ,'$uploaded_by' ,  '$reporter' , 
                                            '$camera_man' , '$district', '$lang_selec'
                                                        
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