<?php


include "session_handler/session_service.php";
include "connection.php";
include "nas_function/functions.php";

include "../global/timezone.php";








// -------------- VARIABLE DECLARATION ----------------
$interview_path = 'my_data/interview_data';
$news_path = 'my_data/news_data';
// ----------------------------------------------------


$myfile = fopen("../log/newscollector_log.txt", "a") or die("Unable to open file!");  


              
if(!isset($location))
{

    if(isset($_POST['submit']))
    {
        if(isset($_POST['byLine']) && isset($_POST['newsdate']) && isset($_POST['newsCategories']) && count($_POST['newsCategories']) > 0)   
        {

            
            
            $byLine = $_POST['byLine'] ;
            $byLine = rtrim ( $byLine ) ;


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



            if(isset($_POST['additional_files_description']))
            {
                $additional_files_description = $_POST['additional_files_description'];
                $additional_files_description = mysqli_real_escape_string($connection, $additional_files_description);    
                $additional_files_description = "'$additional_files_description'";
            }
            else
            {
                $additional_files_description = "NULL";
            }


            if(isset($_POST['audio_desc']))
            {
                $audio_desc = $_POST['audio_desc'];
                $audio_desc = mysqli_real_escape_string($connection, $audio_desc);    
                $audio_desc = "'$audio_desc'";
            }
            else
            {
                $audio_desc = "NULL";
            }


            if(isset($_POST['audio_bites_desc']))
            {
                $audio_bites_desc = $_POST['audio_bites_desc'];
                $audio_bites_desc = mysqli_real_escape_string($connection, $audio_bites_desc);    
                $audio_bites_desc = "'$audio_bites_desc'";
            }
            else
            {
                $audio_bites_desc = "NULL";
            }






            $newsdate = $_POST['newsdate'];
            if(validateDate(date('Y-m-d',strtotime($newsdate))) == 1 )
            {
                $date_status = true;
                $newsdate = mysqli_real_escape_string($connection, $newsdate);
            
            }
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
                        mkdir('../'.$interview_path.'/'.$byLine_directory_clean, 0777 , true);
                        
                        chmod('../'.$interview_path.'/'.$byLine_directory_clean, 0777);
                     
                        $text = "$byLine_directory_clean : Folder Created\n";
                        fwrite($myfile, $text);

                        $bonus_media_path = '../'.$interview_path.'/'.$byLine_directory_clean;
                    }
                }
                else
                {
                 
                    if(!is_dir('../'.$news_path.'/'.$newsdate))
                    {
                    
                   
                   
                        mkdir('../'.$news_path.'/'.$newsdate, 0777 , true);
                        
                        chmod('../'.$news_path.'/'.$newsdate, 0777);
                        
                       
                        mkdir('../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean, 0777 , true);
                        
                        chmod('../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean, 0777);
                        
                        $text = "$newsdate : Folder Created\n";
                        fwrite($myfile, $text);
                        $text = $newsdate."/".$byLine_directory_clean." : Folder Created\n";
                        fwrite($myfile, $text);

                        $bonus_media_path = '../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean;
                       

                    }
                    else
                    {
                    
                        $byLine_directory_clean = remove_special_chars($byLine_directory);
                        mkdir('../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean, 0777 , true);
                        
                        chmod('../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean, 0777);
                           
                        $text = "$byLine_directory_clean Folder Created\n";
                        fwrite($myfile, $text);

                        $bonus_media_path = '../'.$news_path.'/'.$newsdate."/".$byLine_directory_clean;
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
                $file_name_nas = $date_file_name."_".$time_file_name."_".$news_id ;
                $file_path_nas = $interview_path."/".$byLine_directory_clean ;

                $path_destination ="../".$file_path_nas."/".$file_name_nas;
                $path_sql = $file_path_nas."/".$file_name_nas;


            }
            else
            {
                $file_name_nas = $date_file_name."_".$time_file_name."_".$news_id;
                $file_path_nas = $news_path."/".$newsdate."/".$byLine_directory_clean ;

                $path_destination ="../".$file_path_nas."/".$file_name_nas;
                $path_sql =$file_path_nas."/".$file_name_nas;

            }



            if(isset($_FILES['audio_complete_story'])   && !empty($_FILES['audio_complete_story']['name']))
            {
                $fileName = $_FILES['audio_complete_story']['name'] ;

                $fileExt = explode('.' , $fileName);
                $fileActualExt_audio = strtolower(end($fileExt));

                $file_type = $_FILES['audio_complete_story']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('audio'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    $audio_complete_story_path =$path_destination."_audio_complete_story.".$fileActualExt_audio;
                    $audio_complete_story_tmp_name = $_FILES['audio_complete_story']['tmp_name'] ;
                    move_uploaded_file($audio_complete_story_tmp_name, $audio_complete_story_path) ;
                    $audio_complete_story_path =$path_sql."_audio_complete_story.".$fileActualExt_audio;

                    $text = "$audio_complete_story_path : Audio Complete Story Uploaded\n";
                    fwrite($myfile, $text);

                    
                    $audio_complete_story_path = "'$audio_complete_story_path'";
                }
                else
                {
                    $audio_complete_story_path = "NULL" ;
                }

            }
            else
            {
                $audio_complete_story_path = "NULL" ;
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
                    $body_path =$path_destination."_news_file.".$fileActualExt_body;

                    $body_tmp_name = $_FILES['descFile']['tmp_name'] ;
                    move_uploaded_file($body_tmp_name, $body_path) ; 
                    // $body_path = "news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_body.".$fileActualExt_body;
                    $body_path = $path_sql."_news_file.".$fileActualExt_body;
                    $text = "$body_path : News File Uploaded\n";
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


            if(isset($_FILES['regularFeeddFile'])   && !empty($_FILES['regularFeeddFile']['name']))
            {
                $fileName = $_FILES['regularFeeddFile']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_regularFeeddFile = strtolower(end($fileExt));

                $file_type = $_FILES['regularFeeddFile']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );


                if (in_array($file_type_explode[0] , $allowed ))
                {
                    $regular_filed_path =$path_destination."_regular_feed.".$fileActualExt_regularFeeddFile;

                    $regular_field_tmp_name = $_FILES['regularFeeddFile']['tmp_name'] ;
                    move_uploaded_file($regular_field_tmp_name, $regular_filed_path) ;  
                    $regular_filed_path = $path_sql."_regular_feed.".$fileActualExt_regularFeeddFile;

                    $text = "$regular_filed_path : Regular Field Uploaded\n";
                    fwrite($myfile, $text);


                    $regular_filed_path = "'$regular_filed_path'";
                }
                else
                {
                    $regular_filed_path = "NULL" ;
                }

            }
            else
            {
                $regular_filed_path = "NULL" ;
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


            if(isset($_FILES['readyVersionFile'])   && !empty($_FILES['readyVersionFile']['name']))
            {
                $fileName = $_FILES['readyVersionFile']['name'] ;
                $fileExt_readyVersion = explode('.' , $fileName);
                $fileActualExt_readyVersion = strtolower(end($fileExt_readyVersion));

                $file_type = $_FILES['readyVersionFile']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    $readyversion_path =$path_destination."_ready_version.".$fileActualExt_readyVersion;

                    $readyversion_tmp_path = $_FILES['readyVersionFile']['tmp_name'] ;
                    move_uploaded_file($readyversion_tmp_path, $readyversion_path) ;
                    $readyVersion_path =$path_sql."_ready_version.".$fileActualExt_readyVersion;

                    $text = "$readyVersion_path : Ready Version Uploaded\n";
                    fwrite($myfile, $text);

                    $readyVersion_path = "'$readyVersion_path'";

                }
                else
                {
                    $readyVersion_path = "NULL" ;
                }

            }
            else
            {
                $readyVersion_path = "NULL" ;
            }


            
            if(isset($_FILES['roughCutFile'])   && !empty($_FILES['roughCutFile']['name']))
            {
                $fileName = $_FILES['roughCutFile']['name'] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt_roughcut = strtolower(end($fileExt));

                $file_type = $_FILES['roughCutFile']['type'] ;
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    $roughCut_path =$path_destination."_rough_cut.".$fileActualExt_roughcut;

                    $roughcut_tmp_name = $_FILES['roughCutFile']['tmp_name'] ;
                    move_uploaded_file($roughcut_tmp_name, $roughCut_path) ;
                    $roughCut_path =$path_sql."_rough_cut.".$fileActualExt_roughcut;

                    $text = "$roughCut_path : Rough Cut Uploaded\n";
                    fwrite($myfile, $text);

                    $roughCut_path = "'$roughCut_path'";

                }
                else
                {
                    $roughCut_path = "NULL" ;
                }

            }
            else
            {
                $roughCut_path = "NULL" ;
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
                    
                    if($counter == 0)
                    {
                        mkdir('../'.$file_path_nas.'/gallery', 0777 , true);                        
                        chmod('../'.$file_path_nas.'/gallery', 0777);
                    }


                    $fileTmpName_photo = $_FILES['galleryImage']['tmp_name'][$p];  
                    $fileActualExt_photo = strtolower(end($fileExt_photo));                     
                    // $gallery_path ="../news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                    $gallery_path ="../".$file_path_nas."/"."gallery/".$file_name_nas."_gallery_".$counter.".".$fileActualExt_photo;

                    move_uploaded_file($fileTmpName_photo, $gallery_path) ;
                    // $gallery_path ="news_data/".$newsdate."/".$byLine_directory_clean."/".$date_file_name."_".$time_file_name."_".$news_id."_gallery_".$counter.".".$fileActualExt_photo;
                    $gallery_path =$file_path_nas."/"."gallery/".$file_name_nas."_gallery_".$counter.".".$fileActualExt_photo;

                    array_push($gallery_arr , $gallery_path);    
                    
                    $text = "$gallery_path : Gallery-$counter Uploaded\n";
                    fwrite($myfile, $text);


                    $counter++;   
                    
                    

                }
            

            }
            $gallery_csv = implode("," , $gallery_arr) ;
            $gallery_csv = "'$gallery_csv'";

        

        if(!empty($_FILES['bonus_media']['name'][0]))
        {
            mkdir($bonus_media_path.'/bonus_media', 0777 , true);                        
            chmod($bonus_media_path.'/bonus_media', 0777);
            
           
          
            foreach ($_FILES["bonus_media"]["name"] as $p => $name)
            {  
                if(empty($_FILES['bonus_media']['name'][$p])) continue ;

                
                $fileName= $_FILES['bonus_media']['name'][$p];
                $fileTmpName = $_FILES['bonus_media']['tmp_name'][$p];  
                $fileSize = $_FILES['bonus_media']['size'][$p];
                $fileType = $_FILES['bonus_media']['type'][$p];            

                $fileExt = explode('.' , $fileName);            

                    
                    $fileTmpName = $_FILES['bonus_media']['tmp_name'][$p];  

                    $fileName = str_replace(" ","_",$fileName);

                    $file_name_final = $date_file_name."_".$time_file_name."_".$news_id."_".$fileName;

                    $file_path =$bonus_media_path.'/bonus_media'.'/'.$file_name_final;

                    move_uploaded_file($fileTmpName, $file_path) ;                            
                    
              
           
            

            }
        
        
        }


        
        if(!empty($_FILES['additional_files']['name'][0]))
        {
            $zip = new ZipArchive(); // Load zip library 
            $zip_name =$path_destination."_additional_files.zip";
            $additional_path_sql = $path_sql."_additional_files.zip"; 
            $additional_path_sql = "'$additional_path_sql'";

            /*
                 1. create zip folder -- done
                 2. add all files to zip -- done
                 3. store zip -- done
                 4. add attribute in nas db
                 5  store path in nas db
            */ 
            if($zip->open($zip_name, ZIPARCHIVE::CREATE)===TRUE)
            { 
                $counter_file_zip = 0;
                foreach ($_FILES["additional_files"]["name"] as $p => $name)
                { 
                    if(empty($_FILES['additional_files']['name'][$p])) continue ; 
                    
                    $fileName= $_FILES['additional_files']['name'][$p];
                    $fileTmpName = $_FILES['additional_files']['tmp_name'][$p];  
                    $fileSize = $_FILES['additional_files']['size'][$p];
                    $fileType = $_FILES['additional_files']['type'][$p]; 


                    $fileExt_exp = explode('.' , $fileName);
                    $fileActualExt = strtolower(end($fileExt_exp));
                               

                    $fileExt = explode('.' , $fileName);    
                    
                    $new_zip_file_name = $date_file_name."_".$time_file_name."_".$news_id."_additional_file_".$counter_file_zip.".".$fileActualExt;

                        
                        $fileTmpName = $_FILES['additional_files']['tmp_name'][$p];                  
                        $zip->addFile($fileTmpName , $new_zip_file_name);
                        $counter_file_zip++ ;
                }
            

            }

            $zip->close();
        
        
        }
        else 
        {
            $additional_path_sql = 'NULL';
        }

        if(!empty($_FILES['audio_bites']['name'][0]))
        {
            $counter = 0 ;
            $audio_bites_arr = array();
            foreach ($_FILES["audio_bites"]["name"] as $p => $name)
            {      
                if(empty($_FILES['audio_bites']['name'][$p])) continue ;  
                
               
                
                $fileName_photo= $_FILES['audio_bites']['name'][$p];
                $fileTmpName_photo = $_FILES['audio_bites']['tmp_name'][$p];  
                $fileSize_photo = $_FILES['audio_bites']['size'][$p];
                // $filenotice_photo = $_FILES['galleryImage']['notice'][$p];
                $fileType_photo = $_FILES['audio_bites']['type'][$p];            

                $fileExt_photo = explode('.' , $fileName_photo);
                $file_actual_ext_ab = strtolower(end($fileExt_photo));

                $file_type_explode = explode("/" , $fileType_photo);
                $allowed = array('audio'  );

                if (in_array($file_type_explode[0] , $allowed ))
                {     
                    if($counter == 0)
                    {
                        mkdir('../'.$file_path_nas.'/audio_bites', 0777 , true);                        
                        chmod('../'.$file_path_nas.'/audio_bites', 0777);
                    }

                    $fileTmpName_photo = $_FILES['audio_bites']['tmp_name'][$p];  
                    $file_actual_ext_ab = strtolower(end($fileExt_photo));  

                    
                    $audio_bites_path ="../".$file_path_nas."/"."audio_bites/"."$file_name_nas"."_audio_bites_".$counter.".".$file_actual_ext_ab;

                    move_uploaded_file($fileTmpName_photo, $audio_bites_path) ;
                    $audio_bites_path =$file_path_nas."/"."audio_bites/"."$file_name_nas"."_audio_bites_".$counter.".".$file_actual_ext_ab;

                    array_push($audio_bites_arr , $audio_bites_path);    
                    
                    $text = "$audio_bites_path : Audio Bites-$counter Uploaded\n";
                    fwrite($myfile, $text);


                    $counter++;   
                    
                    

                }
            

            }
            $audio_bites_csv = implode("," , $audio_bites_arr) ;
            $audio_bites_csv = "'$audio_bites_csv'";
        
        }
        else 
        {
            $audio_bites_csv = "NULL";
        }




            $created_at = date('Y-m-d H:i:s');
            $created_at = "'$created_at'";

            $newsdate = "'$newsdate'";
            $news_id = "'$news_id'";


            $query_new_news = "insert into nas(
                newsid , created_date ,  local_published_date ,
                byline ,  category_list ,                                       
                regular_feed,  ready_version  ,thumbnail ,
                audio_complete_story ,  photos ,  news_file ,  rough_cut ,
                tag_list , uploaded_by , reporter ,
                camera_man , district , video_type, series ,  additional_file , additional_files_description,
                audio_description , audio_bites_description , audio_bites
              
                ) 
                VALUES 
                ($news_id, $created_at ,  $newsdate , 
                $byLine , $newsCategories,
                $regular_filed_path , $readyVersion_path  , $thumbnail_path,
                $audio_complete_story_path , $gallery_csv , $body_path , $roughCut_path ,
                $tags ,$uploaded_by ,  $reporter , 
                $camera_man , $district,  $video_type, $series , $additional_path_sql , $additional_files_description,
                $audio_desc , $audio_bites_desc , $audio_bites_csv
              
                
                            
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
