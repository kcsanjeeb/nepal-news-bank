<?php




ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
include "session_handler/session_service.php";
include "connection.php";
include "nas_function/functions.php";

include "../global/timezone.php";
include "environment/ftp.php";
include "../global/file_paths.php";






// -------------- VARIABLE DECLARATION ----------------
// $interview_path = 'my_data/interview_data';
// $news_path = 'my_data/news_data';

$interview_path = $local_interview_path;
$news_path = $local_news_collector_path ;
// ----------------------------------------------------


$myfile = fopen("../log/newscollector_log.txt", "a") or die("Unable to open file!");  


              
if(!isset($location))
{

    if(isset($_POST['submit']))
    {
        if(isset($_POST['byLine']) && isset($_POST['newsdate']) && isset($_POST['newsCategories']) && count($_POST['newsCategories']) > 0)   
        {

            $news_id =  mysqli_real_escape_string($connection, $_POST['newsid']);
            $query_fetch_news = "select * from nas where newsid = '$news_id'";
            $run_sql_fetch_news= mysqli_query($connection, $query_fetch_news);
            $num_rows_news = mysqli_num_rows($run_sql_fetch_news);
            $news_row_details = mysqli_fetch_assoc($run_sql_fetch_news);

            $news_local_existing_path = $news_row_details['dir_path'];
            $news_local_published_path = $news_row_details['local_published_date'];
           

            if($num_rows_news <  1)
            {
                exit("Error!");
            }

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
                $uploaded_by = $news_row_details['uploaded_by'];
                $uploaded_by = "'$uploaded_by'";
            }

            if(isset($_POST['reporter']))
            {
                $reporter = $_POST['reporter'];
                $reporter = mysqli_real_escape_string($connection, $reporter);    
                $reporter = "'$reporter'";
            }
            else
            {
                $reporter = $news_row_details['reporter'];
                $reporter = "'$reporter'";
            }
            


            if(isset($_POST['camera_man']))
            {
                $camera_man = $_POST['camera_man'];
                $camera_man = mysqli_real_escape_string($connection, $camera_man);    
                $camera_man = "'$camera_man'";
               
            }
            else
            {
                $camera_man = $news_row_details['camera_man'];
                $camera_man = "'$camera_man'";
            }

            if(isset($_POST['district']))
            {
                $district = $_POST['district'];
                $district = mysqli_real_escape_string($connection, $district);  
                $district = "'$district'";  
                
            }
            else
            {
                $district = $news_row_details['district'];
                $district = "'$district'";
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
                    $series_check = $series ;
                    $series = "'$series'";

            }
            else
            {
                $series = 'NULL';
            }



            if(isset($_POST['extra_files_description']))
            {
                $extra_files_description = $_POST['extra_files_description'];
                $extra_files_description = mysqli_real_escape_string($connection, $extra_files_description);    
                $extra_files_description = "'$extra_files_description'";
            }
            else
            {
                $extra_files_description = $news_row_details['extra_files_description'];
                $extra_files_description = "'$extra_files_description'";
            }


            if(isset($_POST['audio_desc']))
            {
                $audio_desc = $_POST['audio_desc'];
                $audio_desc = mysqli_real_escape_string($connection, $audio_desc);    
                $audio_desc = "'$audio_desc'";
            }
            else
            {
                $audio_desc = $news_row_details['audio_description'];
                $audio_desc = "'$audio_desc'";
            }


            if(isset($_POST['audio_bites_desc']))
            {
                $audio_bites_desc = $_POST['audio_bites_desc'];
                $audio_bites_desc = mysqli_real_escape_string($connection, $audio_bites_desc);    
                $audio_bites_desc = "'$audio_bites_desc'";
            }
            else
            {
                $audio_bites_desc = $news_row_details['audio_bites_description'];
                $audio_bites_desc = "'$audio_bites_desc'";
            }






            $newsdate = $news_local_published_path;
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
                $bonus_media_path = $news_local_existing_path.'/'.$newsdate."/".$byLine_directory_clean;

                if($isInterview == 1 )
                {
                    $bonus_media_path =$news_local_existing_path.'/'.$byLine_directory_clean;
                }
                else
                {              

                    $bonus_media_path = $news_local_existing_path.'/'.$newsdate."/".$byLine_directory_clean;                 
                }
                
                                            
                   
            }

            $date_file_name = str_replace("-","",$newsdate);

            $time_file_name = date('H:i:s');

            $created_at_nas = $news_row_details['created_date'];
            $time_file_name = end(explode(" " , $created_at_nas));
            
            $time_file_name = str_replace(":","",$time_file_name);


           

        
            if($isInterview == 1 )
            {
                $file_name_nas = $date_file_name."_".$time_file_name."_".$news_id ;
                $file_path_nas = $news_local_existing_path."/".$byLine_directory_clean ;

                $path_destination =$file_path_nas."/".$file_name_nas;
                $path_sql = $file_path_nas."/".$file_name_nas;


            }
            else
            {
                $file_name_nas = $date_file_name."_".$time_file_name."_".$news_id;
                $file_path_nas =$news_local_existing_path."/".$newsdate."/".$byLine_directory_clean ;

                $path_destination =$file_path_nas."/".$file_name_nas;
                $path_sql =$file_path_nas."/".$file_name_nas;

            }

            $files_to_push = array();
            $files_to_push_with_query = array();
            $update_query = '';



            if(isset($_FILES['descFile'])   && !empty($_FILES['descFile']['name']))
            {

                
                $fileNameBody = $_FILES['descFile']['name'] ;
                $fileExt_body = explode('.' , $fileNameBody);
                $fileActualExt_body = strtolower(end($fileExt_body));
                $file_type = $_FILES['descFile']['type'] ;
                $allowed_body_type = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document' , 'text/plain' , 'application/rtf', 'application/msword');

                
                if (in_array($file_type , $allowed_body_type  ))
                {
                    $body_path =$path_destination."_news_file.".$fileActualExt_body;
                    $body_path_web = explode("/" , $body_path);
                    $body_path_web = end($body_path_web);
                    $body_path_web = $news_remote_existing_path."/".$body_path_web;
                    
                    // array_shift($body_path_web);array_shift($body_path_web);
                    // $body_path_web = implode("/" , $body_path_web);

                    
                    $body_tmp_name = $_FILES['descFile']['tmp_name'] ;

                    if($news_row_details['news_file'] != null)
                    {
                        $existing_file_path = $news_row_details['news_file'] ;
                        $existing_file_path_exp = explode("/" , $existing_file_path);
                        $existing_file_name = end($existing_file_path_exp);
                        $existing_file_name_exp = explode("." , $existing_file_name);
                        $existing_file_name_ext = end($existing_file_name_exp);

                        if(file_exists($news_row_details['news_file'])) unlink($news_row_details['news_file']);

                            move_uploaded_file($body_tmp_name, $body_path) ; 
                            $body_path = $path_sql."_news_file.".$fileActualExt_body;
                            $text = "$body_path : News File Replaced\n";
                            fwrite($myfile, $text);
                            
                            if($existing_file_name_ext != $fileActualExt_body)
                            {
                                $update_file_name_everywhere = 1;
                            }

                            if($update_file_name_everywhere)
                                $update_query .= "update nas set news_file = '$body_path' where newsid = '$news_id' ;";


                        if($remote_pushed)
                        {
                            // Replace in WEB
                            // delete in remote
                            if($news_row_details_web['news_file'] != null)
                                ftp_delete_rem('/'.$news_row_details_web['news_file'] , 'file');


                            $file_name_to_push = end(explode("/" , $body_path)); 
                            array_push($files_to_push , $file_name_to_push );

                            if($update_file_name_everywhere)
                            {


                                $update_query_remote = "update web set news_file = '$body_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );
                            }

                            if($wp_post_created)
                            {
                                // update acf of wp post
                            }

                        }
                    }
                    else 
                    {
                        // Upload in NAS
                        move_uploaded_file($body_tmp_name, $body_path) ; 
                        $body_path = $path_sql."_news_file.".$fileActualExt_body;
                        $text = "$body_path : News File Uploaded\n";
                        fwrite($myfile, $text);

                        $update_query .= "update nas set news_file = '$body_path' where newsid = '$news_id' ;";

                        if($remote_pushed)
                        {
                            // Upload in Remote
                            $file_name_to_push = end(explode("/" , $body_path));
                            array_push($files_to_push , $file_name_to_push );

                            $update_query_news_file = "update web set news_file = '$body_path_web' where newsid = '$news_id' ;";
                            array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_news_file) );

                            if($wp_post_created)
                            {
                                // updated wp_post

                            }

                        }
                       
                    }

                
                }
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

                    $regular_filed_path_web = explode("/" , $regular_filed_path);
                    // array_shift($regular_filed_path_web);array_shift($regular_filed_path_web);
                    // $regular_filed_path_web = implode("/" , $regular_filed_path_web);

                    $regular_filed_path_web = end($regular_filed_path_web);
                    $regular_filed_path_web = $news_remote_existing_path."/".$regular_filed_path_web;

                    
                    if($news_row_details['regular_feed'] != null)
                    {
                        
                        $existing_file_path = $news_row_details['regular_feed'] ;
                        $existing_file_path_exp = explode("/" , $existing_file_path);
                        $existing_file_name = end($existing_file_path_exp);
                        $existing_file_name_exp = explode("." , $existing_file_name);
                        $existing_file_name_ext = end($existing_file_name_exp);

                        if(file_exists($news_row_details['regular_feed'])) unlink($news_row_details['regular_feed']);

                        move_uploaded_file($regular_field_tmp_name, $regular_filed_path) ;  
                        
                        $regular_filed_path = $path_sql."_regular_feed.".$fileActualExt_regularFeeddFile;
                        $text = "$regular_filed_path : Regular Feed Replaced\n";
                        fwrite($myfile, $text);
                      

                        if($existing_file_name_ext != $fileActualExt_regularFeeddFile)
                        {
                            $update_file_name_everywhere = 1;
                        }

                        if($update_file_name_everywhere)
                            $update_query .= "update nas set regular_feed = '$regular_filed_path' where newsid = '$news_id' ;";

                       
                        if($remote_pushed)
                        {
                           
                            // Replace in WEB
                            // delete in remote
                            if($news_row_details_web['regular_feed'] != null)
                                ftp_delete_rem('/'.$news_row_details_web['regular_feed'] , 'file');

                            $file_name_to_push = explode("/" , $regular_filed_path);
                            $file_name_to_push = end($file_name_to_push); 
                            echo "------------Checkkkk $file_name_to_push -------------------";
                            array_push($files_to_push , $file_name_to_push );

                            if($update_file_name_everywhere)
                            {
                                $update_query_remote = "update web set news_file = '$regular_filed_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );
                            }

                            if($wp_post_created)
                            {
                                // update acf of wp post
                            }
                        }

                    }
                    else 
                    {
                        move_uploaded_file($regular_field_tmp_name, $regular_filed_path) ;  
                        $regular_filed_path = $path_sql."_regular_feed.".$fileActualExt_regularFeeddFile;
                        $text = "$regular_filed_path : Regular Feed Uploaded\n";
                        fwrite($myfile, $text);
                        $update_query .= "update nas set regular_feed = '$regular_filed_path' where newsid = '$news_id' ;";
                        if($remote_pushed)
                        {
                            // Upload in Remote

                            $file_name_to_push = end(explode("/" , $regular_filed_path));
                            array_push($files_to_push , $file_name_to_push );
                            $update_query_remote = "update web set regular_feed = '$regular_filed_path_web' where newsid = '$news_id' ;";
                            array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );

                            if($wp_post_created)
                            {
                                // updated wp_post
                            }

                        }
                    }
                 
                }               

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
                    $thumbnail_path =$path_destination."_thumbnail.".$fileActualExt_thumbImg;
                    $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;

                    $thumbnail_path_web = explode("/" , $thumbnail_path);
                    // array_shift($thumbnail_path_web);array_shift($thumbnail_path_web);
                    // $thumbnail_path_web = implode("/" , $thumbnail_path_web);

                    
                    $thumbnail_path_web = end($thumbnail_path_web);
                    $thumbnail_path_web = $news_remote_existing_path."/".$thumbnail_path_web;

                    if($news_row_details['thumbnail'] != null)
                    {
                        $existing_file_path = $news_row_details['thumbnail'] ;
                        $existing_file_path_exp = explode("/" , $existing_file_path);
                        $existing_file_name = end($existing_file_path_exp);
                        $existing_file_name_exp = explode("." , $existing_file_name);
                        $existing_file_name_ext = end($existing_file_name_exp);



                        if($existing_file_name_ext != $fileActualExt_thumbImg)
                        {
                            $update_file_name_everywhere = 1;
                        }
                       
                        

                        if(file_exists($news_row_details['thumbnail'])) unlink($news_row_details['thumbnail']);

                            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;
                            $thumbnail_path = $path_sql."_thumbnail.".$fileActualExt_thumbImg;
                            $text = "$thumbnail_path : Thumbnail Replaced\n";
                            fwrite($myfile, $text);

                            if($update_file_name_everywhere)
                                $update_query .= "update nas set thumbnail = '$thumbnail_path' where newsid = '$news_id' ;";

                
                        if($remote_pushed)
                        {
                            // Replace in WEB
                            // delete in remote
                            if($news_row_details_web['thumbnail'] != null)
                                ftp_delete_rem('/'.$news_row_details_web['thumbnail'] , 'file');

                            $file_name_to_push = end(explode("/" , $thumbnail_path)); 
                            array_push($files_to_push , $file_name_to_push );

                            if($update_file_name_everywhere)
                            {
                                $update_query_remote = "update web set thumbnail = '$thumbnail_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );

                            }

                            if($wp_post_created)
                            {
                                // update acf of wp post
                            }

                        }
                        if($wp_media_posted)
                        {
                            // replace file in wp_post
                            //$update_query .= "update web set wp_media_id = 'new id' where newsid = '$news_id' ;";

                        }
                    }
                    else 
                    {
                            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;
                            $thumbnail_path = $path_sql."_thumbnail.".$fileActualExt_thumbImg;
                            $text = "$thumbnail_path : Thumbnail Uploaded\n";
                            fwrite($myfile, $text);

                            $update_query .= "update nas set thumbnail = '$thumbnail_path' where newsid = '$news_id' ;";

                        if($remote_pushed)
                        {
                            // Upload in Remote
                            $file_name_to_push = end(explode("/" , $thumbnail_path));
                            array_push($files_to_push , $file_name_to_push );
                            $update_query_remote = "update web set thumbnail = '$thumbnail_path_web' where newsid = '$news_id' ;";
                            array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );

                            if($wp_post_created)
                            {
                                // updated wp_post
                            }

                        }
                    }
                        

                }
                

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

                    $readyversion_path_web = explode("/" , $readyversion_path);
                    // array_shift($readyversion_path_web);array_shift($readyversion_path_web);
                    // $readyversion_path_web = implode("/" , $readyversion_path_web);

                    $readyversion_path_web = end($readyversion_path_web);
                    $readyversion_path_web = $news_remote_existing_path."/".$readyversion_path_web;

                    if($news_row_details['ready_version'] != null)
                    {
                        $existing_file_path = $news_row_details['ready_version'] ;
                        $existing_file_path_exp = explode("/" , $existing_file_path);
                        $existing_file_name = end($existing_file_path_exp);
                        $existing_file_name_exp = explode("." , $existing_file_name);
                        $existing_file_name_ext = end($existing_file_name_exp);

                        if($existing_file_name_ext != $fileActualExt_readyVersion)
                        {
                            $update_file_name_everywhere = 1;
                        }

                        if(file_exists($news_row_details['ready_version'])) unlink($news_row_details['ready_version']);

                        move_uploaded_file($readyversion_tmp_path, $readyversion_path) ;
                        $readyVersion_path =$path_sql."_ready_version.".$fileActualExt_readyVersion;
                        $text = "$readyVersion_path : Ready Version Replaced\n";
                        fwrite($myfile, $text);

                        if($update_file_name_everywhere)
                            $update_query .= "update nas set ready_version = '$readyversion_path' where newsid = '$news_id' ;";



                        if($remote_pushed)
                        {
                            // Replace in WEB
                            // delete in remote
                            
                            if($news_row_details_web['ready_version'] != null)
                                ftp_delete_rem('/'.$news_row_details_web['ready_version'] , 'file');

                            $file_name_to_push = end(explode("/" , $readyVersion_path)); 
                            array_push($files_to_push , $file_name_to_push );

                            if($update_file_name_everywhere)
                            {
                                $update_query_remote = "update web set ready_version = '$readyversion_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );
                            }

                            if($wp_post_created)
                            {
                                // update acf of wp post
                            }

                        }


                    }
                    else {
                        # code...                            
                        move_uploaded_file($readyversion_tmp_path, $readyversion_path) ;
                        $readyVersion_path =$path_sql."_ready_version.".$fileActualExt_readyVersion;
                        $text = "$readyVersion_path : Ready Version Uplaoded\n";
                        fwrite($myfile, $text);

                        $update_query .= "update nas set ready_version = '$readyVersion_path' where newsid = '$news_id' ;";


                        if($remote_pushed)
                        {
                            // Upload in Remote
                            $file_name_to_push = end(explode("/" , $readyVersion_path));
                            array_push($files_to_push , $file_name_to_push );
                            $update_query_remote = "update web set ready_version = '$readyversion_path_web' where newsid = '$news_id' ;";
                            array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );

                            if($wp_post_created)
                            {
                                // updated wp_post
                            }

                        }


                    }


                }
               

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

                    $roughCut_path_web = explode("/" , $roughCut_path);
                    // array_shift($roughCut_path_web);array_shift($roughCut_path_web);
                    // $roughCut_path_web = implode("/" , $roughCut_path_web);

                    $roughCut_path_web = end($roughCut_path_web);
                    $roughCut_path_web = $news_remote_existing_path."/".$roughCut_path_web;

                    if($news_row_details['rough_cut'] != null)
                    {
                        $existing_file_path = $news_row_details['rough_cut'] ;
                        $existing_file_path_exp = explode("/" , $existing_file_path);
                        $existing_file_name = end($existing_file_path_exp);
                        $existing_file_name_exp = explode("." , $existing_file_name);
                        $existing_file_name_ext = end($existing_file_name_exp);

                        if($existing_file_name_ext != $fileActualExt_roughcut)
                        {
                            $update_file_name_everywhere = 1;
                        }

                        if(file_exists($roughCut_path)) unlink($roughCut_path);

                        move_uploaded_file($roughcut_tmp_name, $roughCut_path) ;
                        $roughCut_path =$path_sql."_rough_cut.".$fileActualExt_roughcut;
                        $text = "$roughCut_path : Rough Cut Replaced\n";
                        fwrite($myfile, $text);

                        
                        if($update_file_name_everywhere)
                            $update_query .= "update nas set rough_cut = '$roughCut_path' where newsid = '$news_id' ;";


                        if($remote_pushed)
                        {
                            // Replace in WEB
                            // delete in remote
                            if($news_row_details_web['rough_cut'] != null)
                                ftp_delete_rem('/'.$news_row_details_web['rough_cut'] , 'file');

                            $file_name_to_push = end(explode("/" , $roughCut_path)); 
                            array_push($files_to_push , $file_name_to_push );

                            if($update_file_name_everywhere)
                            {
                                $update_query_remote = "update web set rough_cut = '$roughCut_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );
                            }

                            if($wp_post_created)
                            {
                                // update acf of wp post
                            }

                        }
                    }
              
                    else {
                    # code...
                        move_uploaded_file($roughcut_tmp_name, $roughCut_path) ;
                        $roughCut_path =$path_sql."_rough_cut.".$fileActualExt_roughcut;
                        $text = "$roughCut_path : Rough Cut Uploaded\n";
                        fwrite($myfile, $text);

                        $update_query .= "update nas set rough_cut = '$roughCut_path' where newsid = '$news_id' ;";


                        if($remote_pushed)
                        {
                            // Upload in Remote
                            $file_name_to_push = end(explode("/" , $roughCut_path));
                            array_push($files_to_push , $file_name_to_push );
                            $update_query_remote = "update web set rough_cut = '$roughCut_path_web' where newsid = '$news_id' ;";
                            array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );

                            if($wp_post_created)
                            {
                                // updated wp_post
                            }

                        }
                    }
                 }
                

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

                    
                    $audio_complete_story_path_web = explode("/" , $audio_complete_story_path);
                    // array_shift($audio_complete_story_path_web);array_shift($audio_complete_story_path_web);
                    // $audio_complete_story_path_web = implode("/" , $audio_complete_story_path_web);

                    $audio_complete_story_path_web = end($audio_complete_story_path_web);
                    $audio_complete_story_path_web = $news_remote_existing_path."/".$audio_complete_story_path_web;

                    if($news_row_details['audio_complete_story'] != null)
                    {
                        $existing_file_path = $news_row_details['audio_complete_story'] ;
                        $existing_file_path_exp = explode("/" , $existing_file_path);
                        $existing_file_name = end($existing_file_path_exp);
                        $existing_file_name_exp = explode("." , $existing_file_name);
                        $existing_file_name_ext = end($existing_file_name_exp);

                        if($existing_file_name_ext != $fileActualExt_audio)
                        {
                            $update_file_name_everywhere = 1;
                        }

                        if(file_exists($news_row_details['audio_complete_story'])) unlink($news_row_details['audio_complete_story']);

                      
                        move_uploaded_file($audio_complete_story_tmp_name, $audio_complete_story_path) ;
                        $audio_complete_story_path =$path_sql."_audio_complete_story.".$fileActualExt_audio;
                        $text = "$audio_complete_story_path : Audio Complete Story Replaced\n";
                        fwrite($myfile, $text);

                        if($update_file_name_everywhere)
                          $update_query .= "update nas set audio_complete_story = '$audio_complete_story_path' where newsid = '$news_id' ;";


                        if($remote_pushed)
                        {
                            // Replace in WEB
                            // delete in remote
                            if($news_row_details_web['audio_complete_story'] != null)
                                ftp_delete_rem('/'.$news_row_details_web['audio_complete_story'] , 'file');

                            $file_name_to_push = end(explode("/" , $audio_complete_story_tmp_name)); 
                            array_push($files_to_push , $file_name_to_push );

                            if($update_file_name_everywhere)
                            {
                                $update_query_remote = "update web set audio_complete_story = '$audio_complete_story_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );
                            }

                            if($wp_post_created)
                            {
                                // update acf of wp post
                            }

                        }
                    }
                    else 
                    {
                           
                            move_uploaded_file($audio_complete_story_tmp_name, $audio_complete_story_path) ;
                            $audio_complete_story_path =$path_sql."_audio_complete_story.".$fileActualExt_audio;
                            $text = "$audio_complete_story_path : Audio Complete Story Uploaded\n";
                            fwrite($myfile, $text);
    
                            $update_query .= "update nas set audio_complete_story = '$audio_complete_story_path' where newsid = '$news_id' ;";
    
    
                            if($remote_pushed)
                            {
                                // Upload in Remote
                                $file_name_to_push = end(explode("/" , $audio_complete_story_path));
                                array_push($files_to_push , $file_name_to_push );
                                $update_query_remote = "update web set audio_complete_story = '$audio_complete_story_path_web' where newsid = '$news_id' ;";
                                array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );
    
                                if($wp_post_created)
                                {
                                    // updated wp_post
                                }
    
                            }
                     }



                    
                    
                }
               

            }
           

            

            // ----------------- Multiple Files ---------------------------------


            $gallery_arr = array();
            $gallery_arr_push = array();

            if($news_row_details['photos'] == null)
            {
                $counter = 1 ;
                $ftp_created = 0 ;


            }
            else
            {
                $old_gallery_arr = explode("," ,  $news_row_details['photos']);
                $counter = count($old_gallery_arr) + 1 ; ;
            }

           
            foreach ($_FILES["galleryImage"]["name"] as $p => $name)
            {        
                
                $fileName_photo= $_FILES['galleryImage']['name'][$p];
                $fileTmpName_photo = $_FILES['galleryImage']['tmp_name'][$p];  
                $fileSize_photo = $_FILES['galleryImage']['size'][$p];
                $fileType_photo = $_FILES['galleryImage']['type'][$p];            

                $fileExt_photo = explode('.' , $fileName_photo);
                $fileActualExt_photo = strtolower(end($fileExt_photo));
                $file_type_explode = explode("/" , $fileType_photo);
                $allowed = array('image'  );

                if (in_array($file_type_explode[0] , $allowed ))
                { 
                   
                   
                    if (!is_dir($file_path_nas.'/gallery')) {
                        mkdir($file_path_nas.'/gallery', 0777 , true);                        
                        chmod($file_path_nas.'/gallery', 0777);
                    }

                    $fileTmpName_photo = $_FILES['galleryImage']['tmp_name'][$p];  
                    $fileActualExt_photo = strtolower(end($fileExt_photo));                     
                    

                    do
                    {
                        $gallery_path =$file_path_nas."/"."gallery/".$file_name_nas."_gallery_".$counter.".".$fileActualExt_photo;
                        if(file_exists($gallery_path))
                        {
                            $counter++;
                            $gallery_path =$file_path_nas."/"."gallery/".$file_name_nas."_gallery_".$counter.".".$fileActualExt_photo;
                            $status = 1 ;
                        }
                        else {
                            $status = 0 ;
                        }

                    }while($status);

                    if($gallery_path != '' && !empty($gallery_path))
                    {

                        move_uploaded_file($fileTmpName_photo, $gallery_path) ;
                        $gallery_path =$file_path_nas."/"."gallery/".$file_name_nas."_gallery_".$counter.".".$fileActualExt_photo;
                        array_push($gallery_arr , $gallery_path);                        
                        $text = "$gallery_path : Gallery-$counter Uploaded\n";
                        fwrite($myfile, $text);
                        $counter++;   


                        if($remote_pushed)
                        {
                            echo "------------FTP PUSHING-----------";
                            // Push files to remote

                            if(!isset($folder_created_in_ftp))
                            {
                            
                                $ftp = ftp_connect("$ftp_url");
                                ftp_login($ftp, "$ftp_username", "$ftp_password");
                                ftp_pasv($ftp, true);

                                    $contents =  ftp_mlsd($ftp, "/".$news_remote_existing_path);
                                    $folder_exists = in_array('gallery', array_column($contents, 'name'));

                                if(!$folder_exists)
                                {
                                    ftp_mkdir($ftp, "/".$news_remote_existing_path."/gallery");
                                    $folder_created_in_ftp = 1 ;

                                }

                                ftp_close($ftp); 

                            }
                        

                                $file_name_to_push = end(explode("/", $gallery_path));
                                $file_name_to_push = "gallery/$file_name_to_push";
                                // if(!empty($file_name_to_push) && $file_name_to_push != '')
                                // {
                                    array_push($files_to_push , $file_name_to_push );
                                    array_push($gallery_arr_push , $file_name_to_push );
                                // }
                        



                            

                            
                        }
                    }

                }
            

            }
            if(count($gallery_arr) > 0)
            {
                $new_gallery_csv_nas = implode("," , $gallery_arr);
                if($news_row_details['photos'] == null)
                {
                    $new_galleries_nas = $new_gallery_csv_nas ;
                    
                }
                else
                {
                    $new_galleries_nas = $news_row_details['photos'].",".$new_gallery_csv_nas ;

                }
                $update_query .= "update nas set photos = '$new_galleries_nas' where newsid = '$news_id' ;";

             

               
            }
       


            if(count($_FILES['extra_files']['name']) > 0)
            {

                foreach ($_FILES["extra_files"]["name"] as $p => $name)
                {
                    if(empty($_FILES['extra_files']['name'][$p])) continue ; 

                    $create_zip = 1;

                }


                if($create_zip)
                {

                        $zip = new ZipArchive(); // Load zip library 
                        $zip_name =$path_destination."_extra_files.zip";
                        $extra_path_sql_nc = $path_sql."_extra_files.zip"; 
                        $extra_path_sql = "'$extra_path_sql_nc'";

                        if($news_row_details['extra_files'] != null)
                        {
                            $zip_file = $zip->open($zip_name);
                            $new_file = 0 ;
                        }
                        else {
                            
                            $zip_file = $zip->open($zip_name, ZIPARCHIVE::CREATE) ;
                            $new_file = 1 ;
                        }
    

                        if($zip_file === TRUE)
                        { 
                            $total_files = $zip->numFiles;


                            $counter_file_zip = $total_files + 1 ;

                            if(!isset($total_files)) $counter_file_zip = 0 ;


                            foreach ($_FILES["extra_files"]["name"] as $p => $name)
                            { 
                                if(empty($_FILES['extra_files']['name'][$p])) continue ; 
                                
                                $fileName= $_FILES['extra_files']['name'][$p];
                                $fileTmpName = $_FILES['extra_files']['tmp_name'][$p];  
                                $fileSize = $_FILES['extra_files']['size'][$p];
                                $fileType = $_FILES['extra_files']['type'][$p]; 


                                $fileExt_exp = explode('.' , $fileName);
                                $fileActualExt = strtolower(end($fileExt_exp));                                      

                                $fileExt = explode('.' , $fileName); 

                                $new_zip_file_name = $date_file_name."_".$time_file_name."_".$news_id."_extra_files_".$counter_file_zip.".".$fileActualExt;
   
                                do
                                {
                                    $locator = $zip->locateName($new_zip_file_name);

                                    if(!empty($locator))
                                    {
                                        $counter_file_zip++;
                                        $new_zip_file_name = $date_file_name."_".$time_file_name."_".$news_id."_extra_files_".$counter_file_zip.".".$fileActualExt;
                                        $status = 1 ;
                                    }
                                    else {
                                        $status = 0 ;
                                    }
            
                                }while($status);
                                                                    
                                    $fileTmpName = $_FILES['extra_files']['tmp_name'][$p];                  
                                    $zip->addFile($fileTmpName , $new_zip_file_name);
                                    $counter_file_zip++ ;
                            }
                            

                        }

                    $zip->close();
                    

                    if($new_file)
                    {
                        $update_query .= "update nas set extra_files = $extra_path_sql where newsid = '$news_id' ;";
                        $text = "$zip_name Uploaded\n";
                        fwrite($myfile, $text);

                       
                    }
                    else {
                        $text = "$zip_name Updated\n";
                        fwrite($myfile, $text);               
                      

                    }

                    if($remote_pushed)
                    {                                       
                        if(!$new_file)
                        {
                            if($news_row_details_web['extra_files'] != null)
                            {
                                ftp_delete_rem('/'.$news_row_details_web['extra_files'] , 'file');                           
                            }

                            $file_name_to_push = end(explode("/" , $extra_path_sql_nc));
                            array_push($files_to_push , $file_name_to_push );
                            
                            
                        }
                        else
                        {
                            $file_name_to_push = end(explode("/" , $extra_path_sql_nc));
                            array_push($files_to_push , $file_name_to_push );

                            $extra_path_web = explode("/" , $extra_path_sql_nc);
                            array_shift($extra_path_web);
                            $extra_path_web = implode("/" , $extra_path_web);
                            
                                
                            $update_query_remote = "update web set extra_files = '$extra_path_web' where newsid = '$news_id' ;";
                            array_push($files_to_push_with_query , array('file_name' => $file_name_to_push , 'query' => $update_query_remote) );

                            if($wp_post_created)
                            {
                                // Update File_name in wordpress acf
                            }
                        }                        
                        
                
                    }



                }
            
            
            }
            


        

            if(count($_FILES['bonus_media']['name']) > 0)
            {

                if (!is_dir($file_path_nas.'/bonus_media')) 
                {
                    mkdir($file_path_nas.'/bonus_media', 0777 , true);                        
                    chmod($file_path_nas.'/bonus_media', 0777);
                }

                // mkdir($bonus_media_path.'/bonus_media', 0777 , true);                        
                // chmod($bonus_media_path.'/bonus_media', 0777);
                
            
                $counter = 1;
                foreach ($_FILES["bonus_media"]["name"] as $p => $name)
                {  
                    if(empty($_FILES['bonus_media']['name'][$p])) continue ;

                    
                    $fileName= $_FILES['bonus_media']['name'][$p];
                    $fileTmpName = $_FILES['bonus_media']['tmp_name'][$p];  
                    $fileSize = $_FILES['bonus_media']['size'][$p];
                    $fileType = $_FILES['bonus_media']['type'][$p];            

                    $fileExt = explode('.' , $fileName);            
                    $fileExt = end($fileExt);
                        
                        $fileTmpName = $_FILES['bonus_media']['tmp_name'][$p];  

                        $fileName = str_replace(" ","_",$fileName);
                        $newFile = "";

                        do{
                            $file_name_clean = explode("." ,$fileName);
                            array_pop($file_name_clean);
                            $file_name_clean = implode("." ,$file_name_clean).$newFile;


                            $file_name_final = $date_file_name."_".$time_file_name."_".$news_id."_".$file_name_clean.".".$fileExt;
                            $file_path =$file_path_nas.'/bonus_media'.'/'.$file_name_final;

                            if(file_exists($file_path))
                            {
                                $newFile = "_$counter";
                                $counter++;
                                $status = true ;
                            }
                            else
                            {
                                $newFile = "";
                                $status = false ;
                            }

                        }while($status);

                        

                       

                        move_uploaded_file($fileTmpName, $file_path) ;                            
                        
                        
            
                

                }
            
            
            }


            if(count($_FILES['audio_bites']['name']) > 0 )
            {
                $counter = 1 ;
                $audio_bites_arr = array();
                $audio_bites_arr_pushed = array();

                foreach ($_FILES["audio_bites"]["name"] as $p => $name)
                {      
                    if(empty($_FILES['audio_bites']['name'][$p])) continue ;                   
                                    
                    $fileName_photo= $_FILES['audio_bites']['name'][$p];
                    $fileTmpName_photo = $_FILES['audio_bites']['tmp_name'][$p];  
                    $fileSize_photo = $_FILES['audio_bites']['size'][$p];
                    $fileType_photo = $_FILES['audio_bites']['type'][$p];            

                    $fileExt_photo = explode('.' , $fileName_photo);
                    $file_actual_ext_ab = strtolower(end($fileExt_photo));

                    $file_type_explode = explode("/" , $fileType_photo);
                    $allowed = array('audio'  );

                    if (in_array($file_type_explode[0] , $allowed ))
                    {     
                        
                        if (!is_dir($file_path_nas.'/audio_bites')) {
                            mkdir($file_path_nas.'/audio_bites', 0777 , true);                        
                            chmod($file_path_nas.'/audio_bites', 0777);
                        }

                        $fileTmpName_photo = $_FILES['audio_bites']['tmp_name'][$p];  
                        $file_actual_ext_ab = strtolower(end($fileExt_photo));  


                        do
                        {
                            $path_clean = $file_path_nas."/"."audio_bites/"."$file_name_nas"."_audio_bites_".$counter.".".$file_actual_ext_ab;
                            $audio_bites_path =$path_clean;

                            if(file_exists($audio_bites_path))
                            {
                                $counter++;
                                $status = true ;
                            }
                            else
                            {
                                $status = false ;
                            }

    
                        }while($status);


                        move_uploaded_file($fileTmpName_photo, $audio_bites_path) ;
                        $audio_bites_path = $path_clean;
                       
                        array_push($audio_bites_arr , $audio_bites_path);    

                        $text = "$audio_bites_path : Audio Bites-$counter Uploaded\n";
                        fwrite($myfile, $text);

                        $counter++;                         
                        

                    }                

                }

                if($counter > 1)
                {

                    $audio_bites_csv = implode("," , $audio_bites_arr) ;
                    $old_audio_bites = $news_row_details['audio_bites'];
                    if($news_row_details['audio_bites'] == null)
                    {
                        $audio_bites_csv = $audio_bites_csv;
                    }
                    else
                    {
                        $audio_bites_csv = $old_audio_bites.",".$audio_bites_csv ;
                    }
                    $audio_bites_csv = "'$audio_bites_csv'";

                    $update_query .= "update nas set audio_bites = $audio_bites_csv where newsid = '$news_id' ;";
                    $text = "$zip_name Uploaded\n";
                    fwrite($myfile, $text);

                    /*
                        1. Check if folder exist in web
                        2. Push to remote 
                        3. Upddate query
                    */
                    if($remote_pushed )
                    {
                        
                        $ftp = ftp_connect("$ftp_url");
                        ftp_login($ftp, "$ftp_username", "$ftp_password");
                        ftp_pasv($ftp, true);

                            $contents =  ftp_mlsd($ftp, "/".$news_remote_existing_path);
                            $folder_exists = in_array('audio_bites', array_column($contents, 'name'));

                        if(!$folder_exists)
                        {
                            ftp_mkdir($ftp, "/".$news_remote_existing_path."/audio_bites");
                        }

                        ftp_close($ftp); 

                        foreach($audio_bites_arr as $aba)
                        {
                            $aba = explode("/" , $aba);
                            $aba = end($aba);
                            $file_name_to_push = "audio_bites/".$aba ;
                            array_push($files_to_push , $file_name_to_push);
                            array_push($audio_bites_arr_pushed , $file_name_to_push);

                        }

                    }


                }
                
            
            }





            $updated_at = date('Y-m-d H:i:s');
            $updated_at = "'$updated_at'";

            $newsdate = "'$newsdate'";
            $news_id = "'$news_id'";

 
            
            
            $update_query.= "update nas set local_published_date = $newsdate , byline = $byLine , category_list = $newsCategories , tag_list = $tags,
                                uploaded_by =$uploaded_by,  reporter = $reporter , camera_man = $camera_man, district = $district, 
                                video_type = $video_type , series = $series ,extra_files_description =  $extra_files_description ,
                                audio_description =  $audio_desc , audio_bites_description =  $audio_bites_desc where  newsid = $news_id;";
            
            
            if(count( $files_to_push) > 0)
            {                    
                $files_to_push_csv = implode("," , $files_to_push);    

                $news_ftp_path_py  = $news_remote_existing_path."/";
                $news_ftp_path_py = str_replace(" ","+-*",$news_ftp_path_py);

                $local_file_py = $file_path_nas."/";
                $local_file_py = str_replace(" ","+-*",$local_file_py);           

                

                $sym = "$files_to_push_csv $ftp_url $ftp_username $ftp_password $news_ftp_path_py $local_file_py";
                echo $sym ;
                $push_remote_py_resp = shell_exec("python ftp_push.py $sym");

                foreach($files_to_push as $ftp)
                {
                    if (strpos( $push_remote_py_resp, $ftp) !== false)
                    {
                        $key = array_search($ftp, array_column($files_to_push_with_query, 'file_name')); 
                        if(isset($key) && isset($files_to_push_with_query[$key]['query']))              
                        $update_query .= $files_to_push_with_query[$key]['query'];
                    
                        $text = "$ftp : Succesfully Pushed\n";
                        fwrite($myfile, $text);

                        
                    }
                }

                $pushed_gallery = array();
                foreach($gallery_arr_push as $gftp)
                {
                    if (strpos( $push_remote_py_resp, $gftp) !== false)
                    {
                       
                        array_push($pushed_gallery , $news_remote_existing_path.'/'.$gftp );
                        $text = "$gftp : Succesfully Pushed\n";
                        fwrite($myfile, $text);
                    }
                }
                if(count($pushed_gallery) > 0)
                {
                    $new_gallery_csv = implode("," , $pushed_gallery);

                    if($news_row_details_web['photos'] == null && $news_row_details_web['photos'] == '')
                    {
                        $old_file = '';
                    }
                    else
                    {
                        $old_file =  $news_row_details_web['photos'].",";
                    }

                    $new_galleries = $old_file.$new_gallery_csv ;
                    $update_query .= "update web set photos = '$new_galleries' where newsid = $news_id ;";

                    if($wp_post_created)
                    {
                        // update gallry in Wordpress
                    }

                }

                $pushed_audio_bites = array();
                foreach($audio_bites_arr_pushed as $abftp)
                {
                    if (strpos( $push_remote_py_resp, $abftp) !== false)
                    {
                        array_push($pushed_audio_bites , $news_remote_existing_path.'/'.$abftp );
                        $text = "$abftp : Succesfully Pushed\n";
                        fwrite($myfile, $text);
                    }
                }

                if(count($pushed_audio_bites) > 0)
                {
                    $new_ab_csv = implode("," , $pushed_audio_bites);
                    if($news_row_details_web['audio_bites'] == null && $news_row_details_web['audio_bites'] == '')
                    {
                        $old_file = '';
                    }
                    else
                    {
                        $old_file =  $news_row_details_web['audio_bites'].",";
                    }
                    $new_ab =$old_file.$new_ab_csv ;
                    $update_query .= "update web set audio_bites = '$new_ab' where newsid = $news_id ;";

                    if($wp_post_created)
                    {
                        // update audio bites in Wordpress
                    }

                }
            }
            
            
            //echo $update_query ;
            $connection= mysqli_connect($host , $user , $password , $db_name);
            mysqli_set_charset($connection ,"utf8");
            $run_query = mysqli_multi_query($connection, $update_query);

            if($wp_post_created)
            {
                $new_series = $series_check ;
                $new_series_array = explode("," ,$new_series );
                
                $old_series = $news_row_details['series'] ;
                $old_series_array = explode("," ,$old_series );

                $to_add_series = array_diff($new_series_array, $old_series_array);

                $to_add_series_csv = implode("," , $to_add_series);
                $news_id_update = str_replace("'", "" , $news_id ) ;

                $location = "wp_post_update.php?news_id=$news_id_update&new_series=$to_add_series_csv" ;


                
                
                
                
            }

            

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
$news_id_update = str_replace("'", "" , $news_id ) ;
if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = '../remotecopycreator.php?news_id='.$news_id_update;
}





fwrite($myfile, "------------------------------------------------------- "); 
fclose($myfile);

echo $location_redirect ;
// header("Location: ".$location_redirect);

exit();
