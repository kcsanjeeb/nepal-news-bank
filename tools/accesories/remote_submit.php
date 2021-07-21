<?php


set_time_limit(0);

include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/vimeo.php";
include "nas_function/functions.php";
include "../global/timezone.php";

include "../global/file_paths.php";



// -------------- VARIABLE DECLARATION ----------------
// $interview_path_ftp = 'interview_data';
// $news_path_ftp = 'news_data';

$news_path_ftp = $ftp_news_collector_path ;
$interview_path_ftp = $ftp_interview_path ;
// ----------------------------------------------------








if(!isset($location))
{ 

    if(isset($_POST['submit']) || isset($_POST['submit_push']))
    {
        if(isset($_POST['news_id']) &&  !empty($_POST['news_id']))
        {  
            $news_id = $_POST['news_id'] ;
            $news_id = mysqli_real_escape_string($connection, $news_id);

            $sql_content = "select * from nas where newsid = '$news_id' ";
            $run_sql_content= mysqli_query($connection, $sql_content);
            $num_rows_content = mysqli_num_rows($run_sql_content);

            if($num_rows_content == 1)
            {
                $row_content = mysqli_fetch_assoc($run_sql_content);
                $byline_full = $row_content['byline'];
                $regularfeed_full = $row_content['regular_feed'];
                $thumbnail_full = $row_content['thumbnail'];
                $readyversion_full = $row_content['ready_version'];                        
                $audio_full = $row_content['audio_complete_story'];
                $roughcut_full = $row_content['rough_cut'];       
                $newsbody_full = $row_content['news_file'];
                $date_full = $row_content['created_date'];
                $category_full = $row_content['category_list'];
                $tags_full = $row_content['tag_list'];
                $video_type = $row_content['video_type'];
                $local_published_date = $row_content['local_published_date'];

                $extra_file_full = $row_content['extra_files'];

                $local_dir_path  = $row_content['dir_path'];

               
                // if($category_full == '172')
                if($category_full == $_SESSION['interview_id'])
                {
                    $isInterview = 1;
                }
                else
                {
                    $isInterview = 0;
                }

               


                $sql_content_web = "select * from web where newsid = '$news_id' ";
                $run_sql_content_web= mysqli_query($connection, $sql_content_web);
                $num_rows_content_web = mysqli_num_rows($run_sql_content_web);

                // to handle re push condition for files seperately.

                if($num_rows_content_web == 1)
                {
                   
                    $row_content_web = mysqli_fetch_assoc($run_sql_content_web);                
                    $regularfeed_full_web = $row_content_web['regular_feed'];
                    $thumbnail_full_web = $row_content_web['thumbnail'];
                    $readyversion_full_web = $row_content_web['ready_version'];                            
                    $newsbody_full_web = $row_content_web['news_file'];
                    $audio_full_web = $row_content_web['audio_complete_story'];
                    $roughcut_full_web = $row_content_web['rough_cut'];

                    $gallery_full_web = $row_content_web['photos'];
                    $gallery_full_web_arr = explode(',' ,  $gallery_full_web) ;

                    $audio_bites_full_web = $row_content_web['audio_bites'];
                    if($row_content_web['audio_bites'] == null)
                    {
                        $audio_bites_web_arr = array();
                    }
                    else
                    {
                        $audio_bites_web_arr = explode(',' ,  $audio_bites_full_web) ;

                    }

                    

                    $extra_files_full_web = $row_content_web['extra_files'];

                    if($regularfeed_full_web == NULL)
                        $regularfeed_full_web = "NULL";
                    else
                        $regularfeed_full_web = "'$regularfeed_full_web'";

                  
                    if($thumbnail_full_web == NULL)
                        $thumbnail_full_web = "NULL";

                    else
                        $thumbnail_full_web = "'$thumbnail_full_web'";

                    if($readyversion_full_web == NULL)
                        $readyversion_full_web = "NULL";

                    else
                        $readyversion_full_web = "'$readyversion_full_web'";

                    if($newsbody_full_web == NULL)
                        $newsbody_full_web = "NULL";

                    else
                        $newsbody_full_web = "'$newsbody_full_web'";

                    if($audio_full_web == NULL)
                        $audio_full_web = "NULL";
                    else
                        $audio_full_web = "'$audio_full_web'";


                    if($roughcut_full_web == NULL)
                        $roughcut_full_web = "NULL";

                    else
                        $roughcut_full_web = "'$roughcut_full_web'";

                    if($gallery_full_web == NULL)
                        $gallery_full_web = "NULL";

                    else
                        $gallery_full_web = "'$gallery_full_web'";

                    
                    if($extra_files_full_web == NULL)
                        $extra_files_full_web = "NULL";

                    else
                        $extra_files_full_web = "'$extra_files_full_web'";
                    
                       
                    if($audio_bites_full_web == NULL)
                        $audio_bites_full_web = "NULL";
                    else {
                        $audio_bites_full_web = "'$audio_bites_full_web'";
                    }

            


                }
                else
                {
                    
                    $regularfeed_full_web = "NULL";
                    $thumbnail_full_web = "NULL";
                    $readyversion_full_web = "NULL";                            
                    $newsbody_full_web = "NULL";
                    $audio_full_web = "NULL";
                    $roughcut_full_web = "NULL";
                    $gallery_full_web = "NULL";
                    $extra_files_full_web = "NULL";
                    $gallery_full_web_arr = array();
                    $audio_bites_full_web = "NULL";

                    $audio_bites_web_arr = array();
                }




                
                $myfile = fopen("../log/remotecopy_log.txt", "a") or die("Unable to open file!");  
                fwrite($myfile, "\n---------------$date_full / $byline_full ---------------- \n"); 


                $ftp = ftp_connect("$ftp_url");
                ftp_login($ftp, "$ftp_username", "$ftp_password");
                ftp_pasv($ftp, true);

                



                $logged_date = date('Y-m-d H:i:s');
                fwrite($myfile, "\n Logged Date: $logged_date \n");

                $dir_byline = remove_special_chars($byline_full) ;
               
                if($isInterview)
                {
                    $ftp_path_exp = explode("/" , $interview_path_ftp);

                    $folder = "/";
                    foreach($ftp_path_exp as $fn)
                    {
                        $folders_in_ftp =  ftp_mlsd($ftp, $folder);
                        $folder_exists = in_array($folders_in_ftp, array_column($fn, 'name'));
                        if(!$folder_exists)
                        {
                            ftp_mkdir($ftp, "/".$fn);

                        }
                        $folder = $folder."/".$fn;

                    }

                    $news_path_ftp = $ftp_news_collector_path ;
                    $interview_path_ftp = $ftp_interview_path ;


                    // $ftp = ftp_connect("$ftp_url");
                    // ftp_login($ftp, "$ftp_username", "$ftp_password");
                    // ftp_pasv($ftp, true);
                    ftp_mkdir($ftp, "/".$interview_path_ftp."/".$dir_byline);
                    // ftp_close($ftp);

                    $news_ftp_path  = "/".$interview_path_ftp."/".$dir_byline;
                    $news_ftp_path_sql  = $interview_path_ftp."/".$dir_byline;

                    $local_root_path = $local_dir_path."/".$dir_byline."/" ;

                }
                else
                {

                    // Folder Exist
                    $contents =  ftp_mlsd($ftp, "/$news_path_ftp");

                    $ftp_path_exp = explode("/" , $news_path_ftp);
                    $folder = "/";
                    foreach($ftp_path_exp as $fn)
                    {
                        // $folders_in_ftp =  ftp_mlsd($ftp, $folder);
                        // $folder_exists = in_array($folders_in_ftp, array_column($fn, 'name'));
                        $folder_exists = 0 ;

                        if(!$folder_exists)
                        {
                            ftp_mkdir($ftp, $folder.$fn);

                        }

                        $folder = $folder.$fn."/";

                    }


                    // $folder_exists = is_dir('ftp://'.$ftp_un_prefix.':'.$ftp_password.'@'.$ftp_url.'/'.$news_path_ftp.'/'.$local_published_date);
                    // $folder_exists = in_array($local_published_date, array_column($contents, 'name'));
                    $folder_exists = false ;
                    
                    if(!$folder_exists)
                    {
                        // // echo "here !";
                        // $ftp = ftp_connect("$ftp_url");
                        // ftp_login($ftp, "$ftp_username", "$ftp_password");
                        // ftp_pasv($ftp, true);
                        $dir = $local_published_date;
                        // // echo "/".$news_path_ftp."/".$dir;
                         ftp_mkdir($ftp, "/".$news_path_ftp."/".$dir);
                        // ftp_mkdir($ftp, "/hehehhe");
                        // ftp_close($ftp);
                    }

                    // $ftp = ftp_connect("$ftp_url");
                    // ftp_login($ftp, "$ftp_username", "$ftp_password");
                    // ftp_pasv($ftp, true);
                    $dir_byline = remove_special_chars($byline_full) ;
                    ftp_mkdir($ftp, "/".$news_path_ftp."/".$local_published_date."/".$dir_byline);
                    // ftp_close($ftp);

                    $news_ftp_path  = "/".$news_path_ftp."/".$local_published_date."/".$dir_byline;
                    $news_ftp_path_sql  = "$news_path_ftp/".$local_published_date."/".$dir_byline;

                    $local_root_path = $local_dir_path."/".$local_published_date."/".$dir_byline."/";

                }

                $files_to_push = array();
                $file_to_push_gall = array();

               
                if(isset($_POST['gall_img']))
                {
                    $gall_img = $_POST['gall_img'];
                    $count_gall_push = 0 ;
                
                    foreach($gall_img as $gal_img_arr)
                    {
                        if($count_gall_push == 0)
                        {
                            ftp_mkdir($ftp, "/".$news_path_ftp."/".$local_published_date."/".$dir_byline."/gallery");
                        }
                        if(file_exists($gal_img_arr))
                        {

                            $sourceName = explode("/" ,$gal_img_arr ) ;
                            $sourceName = end($sourceName );
                            $sourceName = "gallery/".$sourceName ;

                            


                            // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$gal_img_arr))

                            array_push($files_to_push , $sourceName );
                            array_push($file_to_push_gall , $sourceName );

                            // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$gal_img_arr , FTP_BINARY))
                            // {
                            //     $gal_img_arr = "$news_ftp_path_sql/$sourceName" ;
                            //     array_push($gallery_full_web_arr , $gal_img_arr);

                            //     $text = "$sourceName Gallery Photo Pushed\n";
                            //     fwrite($myfile, $text);

                                
                            // }
                            // else
                            // {
                            //     $text = "$sourceName Gallery Photo Failed to Pushed\n";                           

                            //     fwrite($myfile, $text);
                            //     $_SESSION['notice_remote'] = "Error";
                            // }

                            
                        }
                        $count_gall_push++ ;
                    }
    
                }
               
                $file_to_push_audio_bites= array();
                if(isset($_POST['audio_bites']))
                {
                    $audio_bites = $_POST['audio_bites'];
                    $count_ab_push = 0 ;
                
                    foreach($audio_bites as $ab)
                    {
                        if($count_ab_push == 0)
                        {
                            ftp_mkdir($ftp, "/".$news_path_ftp."/".$local_published_date."/".$dir_byline."/audio_bites");
                        }

                        if(file_exists($ab))
                        {
                            $sourceName = explode("/" ,$ab ) ;
                            $sourceName = end($sourceName );
                            $sourceName = "audio_bites/".$sourceName;


                            array_push($files_to_push , $sourceName );
                            array_push($file_to_push_audio_bites , $sourceName );

                            
                        }
                        $count_ab_push++;
                    }
    
                }
               
                

                $file_type = $_POST['file_name'];

                $pushed_by = $_POST['pushed_by'];
                $pushed_by = mysqli_real_escape_string($connection, $pushed_by);
                

                if(!isset($file_type) || empty($file_type))
                {
                    $file_type = array();
                }

                    if(in_array('news_file' ,$file_type ))
                    {
                        if(file_exists($newsbody_full))
                        {
                            $sourceName = explode("/" ,$newsbody_full ) ;
                            $sourceName = end($sourceName );

                            // if(ftp_remote('newsbody' , '../'.$newsbody_full , $sourceName))
                            // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$newsbody_full))

                            array_push($files_to_push , $sourceName );
                            $news_body_py =  $sourceName ;
                            // ======= old ftp -------------------

                                // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$newsbody_full , FTP_BINARY))
                                // { 

                                //     $text = "$sourceName News  Body Pushed\n";
                                //     fwrite($myfile, $text);
                                    

                                //     $push_newsbody = "'$news_ftp_path_sql/$sourceName'" ;
                                // }
                                // else
                                // {
                                //     $push_newsbody = "NULL";
                                //     $text = "$sourceName News Body Failed to Pushed\n";
                                //     fwrite($myfile, $text);                            

                                //     $_SESSION['notice_remote'] = "Error";
                                // }

                            // ======= old ftp -------------------
                        
                        }
                        else
                        {
                            $push_newsbody = "NULL";
                        }
                        

                    }
                    else
                    {
                        $push_newsbody = $newsbody_full_web;
                    }

                  
                    if(in_array('regularFeed' ,$file_type ))
                    {

                        if(file_exists($regularfeed_full))
                        {
                            $sourceName = explode("/" ,$regularfeed_full ) ;
                            $sourceName = end($sourceName );


                            if($video_type == 'selfhost')
                            {
                                // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$regularfeed_full))


                                array_push($files_to_push , $sourceName );

                                $regularfeed_py =  $sourceName ;
                                // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$regularfeed_full , FTP_BINARY))
                                // {

                                //     $text = "$sourceName Video Long Pushed\n";
                                //     fwrite($myfile, $text);                        


                                //     $push_regularFeed= "'$news_ftp_path_sql/$sourceName'" ;
                                // }
                                // else
                                // {
                                //     $push_regularFeed= "NULL";
                                //     $text = "$sourceName Video Long Failed to Pushed\n";
                                //     fwrite($myfile, $text);
                                    

                                //     $_SESSION['notice_remote'] = "Error";
                                // }
                                $vimeo_regularfeed =  "NULL" ;
                            }

                            if($video_type == 'vimeo')
                            {
                                // echo "Video Long Vime<br>";

                                $file_name = '../'.$regularfeed_full;
                                $vimeo_vl_name = end(explode("/" ,$regularfeed_full ));
                                $uri = $client->upload($file_name, array(
                                "name" => $vimeo_vl_name,
                                "description" => ""
                                ));
    
    
                                $response = $client->request($uri . '?fields=transcode.status');
                            
                                $response = $client->request($uri . '?fields=link');
    
                                $id = explode("/" , $response['body']['link']);
                                $id = end($id);
    
                                $vimeo_regularfeed =  "'$id'" ;
                                print_r($response);
                                
                                // echo "Video Long ID vimeo_regularfeed<br>";
                                if(isset($vimeo_regularfeed))
                                {

                                    $text = "$sourceName Regular Feed Pushed to VIMEO\n";
                                    fwrite($myfile, $text);                        


                                    $push_regularFeed= "NULL" ;
                                }
                                else
                                {
                                    $push_regularFeed= "NULL";
                                    $text = "$sourceName Regular Feed Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                            }


                        
                        }
                        else
                        {
                            $push_regularFeed= "NULL";
                        }
                        

                    }
                    else
                    {
                        $push_regularFeed= $regularfeed_full_web;
                        $vimeo_regularfeed =  "NULL" ;
                    }
                    
                    if(in_array('ReadyVersion' ,$file_type ))
                    {
                        if(file_exists($readyversion_full))
                        {
                            $sourceName = explode("/" ,$readyversion_full ) ;
                            $sourceName = end($sourceName );

                            if($video_type == 'selfhost')
                            {
                                // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$readyversion_full))

                                array_push($files_to_push , $sourceName );
                                $readyversion_py =  $sourceName ;
                                // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$readyversion_full , FTP_BINARY))
                                // {
                                //     $text = "$sourceName Video Lazy Pushed\n";
                                //     fwrite($myfile, $text);                            

                                //     $push_readyVesion = "'$news_ftp_path_sql/$sourceName'" ;

                                // }
                                // else
                                // {
                                //     $push_readyVesion = "NULL";
                                //     $text = "$sourceName Video Lazy Failed to Pushed\n";
                                //     fwrite($myfile, $text);
                                    

                                //     $_SESSION['notice_remote'] = "Error";
                                // }

                                $vimeo_readyversion = "NULL";
                            }

                            if($video_type == 'vimeo')
                            {
                                $file_name = '../'.$readyversion_full;
                                $vimeo_vla_name = end(explode("/" ,$readyversion_full ));
                                $uri = $client->upload($file_name, array(
                                "name" => $vimeo_vla_name,
                                "description" => ""
                                ));
    
    
                                $response = $client->request($uri . '?fields=transcode.status');
                            
                                $response = $client->request($uri . '?fields=link');
    
                                $id = explode("/" , $response['body']['link']);
                                $id = end($id);
    
                                $vimeo_readyversion =  "'$id'" ;
                                print_r($response);

                                if(isset($vimeo_readyversion))
                                {

                                    $text = "$sourceName Video Lon:azyg Pushed to VIMEO\n";
                                    fwrite($myfile, $text);                        


                                    $push_readyVesion = "NULL" ;
                                }
                                else
                                {
                                    $push_readyVesion = "NULL";
                                    $text = "$sourceName Video Long Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                            }

                            
                        }
                        else
                        {
                            $push_readyVesion = "NULL";
                            $vimeo_readyversion = "NULL";
                        }
                        
                    }
                    else
                    {
                        $push_readyVesion = $readyversion_full_web;
                        $vimeo_readyversion = "NULL";
                    }

                    if(in_array('RoughCut' ,$file_type ))
                    {
                        if(file_exists($roughcut_full))
                        {
                            $sourceName = explode("/" ,$roughcut_full ) ;
                            $sourceName = end($sourceName );

                            if($video_type == 'selfhost')
                            {
                                // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$roughcut_full))

                                array_push($files_to_push , $sourceName );
                                $roughcut_py =  $sourceName ;

                                // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$roughcut_full , FTP_BINARY))
                                // {
                                //     // $push_roughcut = "'$roughcut_full'" ;
                                //     $text = "$sourceName Video Extra  Pushed\n";
                                //     fwrite($myfile, $text);
                                    

                                //     $push_roughcut = "'$news_ftp_path_sql/$sourceName'" ;

                                // }
                                // else
                                // {
                                //     $push_roughcut = "NULL";
                                //     $text = "$sourceName Video Extra Failed to Pushed\n";
                                //     fwrite($myfile, $text);
                                    

                                //     $_SESSION['notice_remote'] = "Error";
                                // }
                                $vimeo_roughcut = 'NULL';
                            }

                            if($video_type == 'vimeo')
                            {
                                $file_name = '../'.$roughcut_full;
                                $vimeo_ve_name = end(explode("/" ,$roughcut_full ));
                                $uri = $client->upload($file_name, array(
                                "name" => vimeo_ve_name,
                                "description" => ""
                                ));
    
    
                                $response = $client->request($uri . '?fields=transcode.status');
                            
                                $response = $client->request($uri . '?fields=link');
    
                                $id = explode("/" , $response['body']['link']);
                                $id = end($id);
    
                                $vimeo_roughcut =  "'$id'" ;
                                print_r($response);

                                if(isset($vimeo_roughcut))
                                {

                                    $text = "$sourceName Rough Cut Pushed to VIMEO\n";
                                    fwrite($myfile, $text);                        


                                    $push_roughcut = "NULL" ;
                                }
                                else
                                {
                                    $push_roughcut = "NULL";
                                    $text = "$sourceName Rough Cut Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                            }
                            
                        }
                        else
                        {
                            $push_roughcut = "NULL";
                        }
                    }
                    else
                    {
                        $push_roughcut = $roughcut_full_web;
                        $vimeo_roughcut = 'NULL';
                    }

              
                    if(in_array('thumbnail' ,$file_type ))
                    {
                        if(file_exists($thumbnail_full))
                        {
                            $sourceName = explode("/" ,$thumbnail_full ) ;
                            $sourceName = end($sourceName );
                            // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$thumbnail_full))

                            array_push($files_to_push , $sourceName );
                            $thumb_py =  $sourceName ;
                            // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$thumbnail_full , FTP_BINARY))
                            // {
                            //     // $push_thumbnail = "'$thumbnail_full'" ;
                            //     $text = "$sourceName Thumbnail Pushed\n";
                            //     fwrite($myfile, $text);
                                

                            //     $push_thumbnail = "'$news_ftp_path_sql/$sourceName'" ;

                            // }
                            // else
                            // {
                            //     $push_thumbnail = "NULL";
                            //     $text = "$sourceName Thumbnail Failed to Pushed\n";
                            //     fwrite($myfile, $text);
                                

                            //     $_SESSION['notice_remote'] = "Error";
                            // }

                            
                        }
                        else
                        {
                            $push_thumbnail = "NULL";
                        }
                    }
                    else
                    {
                        $push_thumbnail = $thumbnail_full_web;
                    }


                    if(in_array('audio' ,$file_type ))
                    {
                        if(file_exists($audio_full))
                        {
                            $sourceName = explode("/" ,$audio_full ) ;
                            $sourceName = end($sourceName );
                            // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$audio_full))

                            array_push($files_to_push , $sourceName );
                            $audio_py =  $sourceName ;

                            // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$audio_full , FTP_BINARY))
                            // {
                            //     // $push_audio = "'$audio_full'" ;
                            //     $text = "$sourceName Audio Pushed\n";
                            //     fwrite($myfile, $text);
                                

                            //     $push_audio = "'$news_ftp_path_sql/$sourceName'" ;

                            // }
                            // else
                            // {
                            //     $push_audio = "NULL";
                            //     $text = "$sourceName Audio Failed to Pushed\n";
                            //     fwrite($myfile, $text);
                                

                            //     $_SESSION['notice_remote'] = "Error";
                            // }

                            
                        }
                        else
                        {
                            $push_audio = "NULL";
                        }
                    }
                    else
                    {
                        $push_audio = $audio_full_web;
                    }


                    
                    if(in_array('extra_files' ,$file_type ))
                    {
                        if(file_exists($extra_file_full))
                        {
                            $sourceName = explode("/" ,$extra_file_full ) ;
                            $sourceName = end($sourceName );

                            // if(ftp_remote('newsbody' , '../'.$newsbody_full , $sourceName))
                            // if(ftp_remote($news_ftp_path."/".$sourceName , "../".$newsbody_full))

                            array_push($files_to_push , $sourceName );
                            $extra_file_py =  $sourceName ;
                            // ======= old ftp -------------------

                                // if(ftp_put($ftp, $news_ftp_path."/".$sourceName, "../".$newsbody_full , FTP_BINARY))
                                // { 

                                //     $text = "$sourceName News  Body Pushed\n";
                                //     fwrite($myfile, $text);
                                    

                                //     $push_newsbody = "'$news_ftp_path_sql/$sourceName'" ;
                                // }
                                // else
                                // {
                                //     $push_newsbody = "NULL";
                                //     $text = "$sourceName News Body Failed to Pushed\n";
                                //     fwrite($myfile, $text);                            

                                //     $_SESSION['notice_remote'] = "Error";
                                // }

                            // ======= old ftp -------------------
                        
                        }
                        else
                        {
                            $push_extra_file = "NULL";
                        }
                        

                    }
                    else
                    {
                        $push_extra_file = $extra_files_full_web;
                    }

               
                    

                    ftp_close($ftp); 
                
                /*
                    1. execute python
                    2. get responve
                    4, evaluate and logs
                */

                 $files_to_push_csv = implode("," , $files_to_push);
                

                 $news_ftp_path_py  = $news_ftp_path;
                 $news_ftp_path_py = str_replace(" ","+-*",$news_ftp_path_py);

                //  $local_file_py = '../my_data'.$news_ftp_path_py.'/';

                 $local_file_py = $local_root_path;
                 $local_file_py = str_replace(" ","+-*",$local_file_py);
                 

                 

                 $sym = "$files_to_push_csv $ftp_url $ftp_username $ftp_password $news_ftp_path_py $local_file_py";

                //  // echo "<br>";// echo "<br>";
                // // echo $sym ; 

                // // echo "<br>";// echo "<br>";
                 $push_remote_py_resp = shell_exec("python ftp_push.py $sym");
                 $push_remote_py_resp_arr = explode("," , $push_remote_py_resp);
                
                // // echo "<br>";// echo "<br>";
                // // echo $push_remote_py_resp ;
                // // echo "<br>";// echo "<br>";

              

                
                 
                if(in_array($news_body_py , $files_to_push))
                {
                    // echo "1a<br>";
                    // // echo $news_body_py;
                    // echo '<br>';
                    // print_r($push_remote_py_resp_arr);
                    
                    // if(in_array($news_body_py , $push_remote_py_resp_arr)) 
                    if (strpos( $push_remote_py_resp, $news_body_py) !== false)        
                    {
                        // echo "<br>1b<br>";

                        $text = "$news_body_py News  Body Pushed\n";
                        fwrite($myfile, $text);
                        

                        $push_newsbody = "'$news_ftp_path_sql/$news_body_py'" ;
                    }

                    else
                    {
                        // echo "1c<br>";
                        $push_newsbody = "NULL";
                        $text = "$sourceName News Body Failed to Pushed\n";
                        fwrite($myfile, $text);                            

                        $_SESSION['notice_remote'] = "Error";
                    }
                }
           
                if(in_array($regularfeed_py , $files_to_push))
                {
                    // echo "2a<br>";
                    if (strpos( $push_remote_py_resp, $regularfeed_py) !== false)
                    {
                        // echo "2b<br>";

                            $text = "$regularfeed_py Regular Feed Pushed\n";
                            fwrite($myfile, $text);                        


                            $push_regularFeed= "'$news_ftp_path_sql/$regularfeed_py'" ;
                    }
                    else
                    {
                        // echo "2c<br>";
                        $push_regularFeed= "NULL";
                        $text = "$regularfeed_py Regular Field Failed to Pushed\n";
                        fwrite($myfile, $text);
                        

                        $_SESSION['notice_remote'] = "Error";
                    }
   
                }


                if(in_array($readyversion_py , $files_to_push))
                {
                    // echo "3a<br>";
                    // if(in_array($readyversion_py , $push_remote_py_resp_arr))
                    if (strpos( $push_remote_py_resp, $readyversion_py) !== false)
                    {
                        // echo "3b<br>";
                            $text = "$readyversion_py Ready Version Pushed\n";
                            fwrite($myfile, $text);                        


                            $push_readyVesion = "'$news_ftp_path_sql/$readyversion_py'" ;
                    }
                    else
                    {
                        // echo "3c<br>";
                        $push_readyVesion = "NULL";
                        $text = "$readyversion_py Ready Version Failed to Pushed\n";
                        fwrite($myfile, $text);
                        

                        $_SESSION['notice_remote'] = "Error";
                    }
   
                }

                if(in_array($roughcut_py , $files_to_push))
                {
                    // echo "4a<br>";
                    // if(in_array($roughcut_py , $push_remote_py_resp_arr))
                    if (strpos( $push_remote_py_resp, $roughcut_py) !== false)
                    {
                        // echo "4b<br>";

                            $text = "$roughcut_py Rough Cut Pushed\n";
                            fwrite($myfile, $text);                        


                            $push_roughcut = "'$news_ftp_path_sql/$roughcut_py'" ;
                    }
                    else
                    {
                        // echo "4c<br>";
                        $push_roughcut = "NULL";
                        $text = "$roughcut_py Rough Cut Failed to Pushed\n";
                        fwrite($myfile, $text);
                        

                        $_SESSION['notice_remote'] = "Error";
                    }
   
                }

                if(in_array($thumb_py , $files_to_push))
                {
                    // echo "5a<br>";
                    // if(in_array($thumb_py , $push_remote_py_resp_arr))
                    if (strpos( $push_remote_py_resp, $thumb_py) !== false)
                    {

                        // echo "5b<br>";
                            $text = "$thumb_py Thumbnail Pushed\n";
                            fwrite($myfile, $text);                        


                            $push_thumbnail = "'$news_ftp_path_sql/$thumb_py'" ;
                    }
                    else
                    {
                        // echo "5c<br>";
                        $push_thumbnail = "NULL";
                        $text = "$thumb_py Thumbnail Failed to Pushed\n";
                        fwrite($myfile, $text);
                        

                        $_SESSION['notice_remote'] = "Error";
                    }
   
                }

                if(in_array($audio_py , $files_to_push))
                {
                    // echo "6a<br>";
                    // if(in_array($audio_py , $push_remote_py_resp_arr))
                    if (strpos( $push_remote_py_resp, $audio_py) !== false)
                    {
                        // echo "6b<br>";
                            $text = "$audio_py Audio Pushed\n";
                            fwrite($myfile, $text);                        


                            $push_audio = "'$news_ftp_path_sql/$audio_py'" ;
                    }
                    else
                    {
                        // echo "6c<br>";
                        $push_audio = "NULL";
                        $text = "$audio_py Audio Failed to Pushed\n";
                        fwrite($myfile, $text);
                        

                        $_SESSION['notice_remote'] = "Error";
                    }
   
                }

                foreach($file_to_push_gall as $sent_gal)
                {
                    // echo "7aa<br>";
                    // if(in_array($sent_gal ,$push_remote_py_resp_arr ))
                    if (strpos( $push_remote_py_resp, $sent_gal) !== false)
                    {
                        // echo "7b<br>";
                            $gal_img_arr = "$news_ftp_path_sql/$sent_gal" ;
                            if(!empty($sent_gal) && isset($sent_gal))
                            {
                                array_push($gallery_full_web_arr , $gal_img_arr);

                                $text = "$sent_gal Gallery Photo Pushed\n";
                                fwrite($myfile, $text);
                            }
                                
                    }
                    else 
                    {
                        // echo "7c<br>";
                            $text = "$sent_gal Gallery Photo Failed to Pushed\n";                       
                            fwrite($myfile, $text);
                            $_SESSION['notice_remote'] = "Error";
                    }
                }
               
                // echo "<br>----------------------<br>";
                // echo "Gal push count ".count($gallery_full_web_arr) ;
                // echo "<br>----------------------<br>";

                if(count($gallery_full_web_arr) == 0)
                {
                    $gall_img = "NULL";
                }
                else
                {
                    $gallery_full_web_arr_hold = array();
                    foreach($gallery_full_web_arr as $gal)
                    {
                        if(!empty($gal)) array_push($gallery_full_web_arr_hold , $gal);
                    }

                    $gall_img = implode(',' , $gallery_full_web_arr_hold) ;
                    $gall_img = "'$gall_img'";
                }

                // echo "<br>----------------------<br>";
                // echo "gal image db: ".$gall_img ;
                // echo "<br>----------------------<br>";

                
                if(in_array($extra_file_py , $files_to_push))
                {
                    
                    
                    // if(in_array($news_body_py , $push_remote_py_resp_arr)) 
                    if (strpos( $push_remote_py_resp, $extra_file_py) !== false)        
                    {
                        // echo "<br>1b<br>";

                        $text = "$extra_file_py Extra Files Pushed\n";
                        fwrite($myfile, $text);
                        

                        $push_extra_file = "'$news_ftp_path_sql/$extra_file_py'" ;
                    }

                    else
                    {
                        // echo "1c<br>";
                        $push_extra_file = "NULL";
                        $text = "$sourceName News Body Failed to Pushed\n";
                        fwrite($myfile, $text);                            

                        $_SESSION['notice_remote'] = "Error";
                    }
                }
                
                $push_audio_bites_array = array();
                if(count($file_to_push_audio_bites) > 0)
                {

                    foreach($file_to_push_audio_bites as $sent_ab)
                    {
                        // echo "7aa<br>";
                        // if(in_array($sent_gal ,$push_remote_py_resp_arr ))
                        if (strpos( $push_remote_py_resp, $sent_ab) !== false)
                        {
                            // echo "7b<br>";
                                $ab_arr = "$news_ftp_path_sql/$sent_ab" ;

                               
                                    array_push($push_audio_bites_array , $ab_arr);
    
                                $text = "$sent_ab Audio Bites Pushed\n";
                                fwrite($myfile, $text);
                        }
                        else 
                        {
                            // echo "7c<br>";
                                $text = "$sent_ab Audio Bites Failed to Pushed\n";                       
                                fwrite($myfile, $text);
                                $_SESSION['notice_remote'] = "Error";
                        }
                    }

                }



                if(count($push_audio_bites_array) > 0)
                {
                    // if(count($audio_bites_web_arr > 0))
                    if($row_content_web['audio_bites'] != null)
                    {
                        foreach($audio_bites_web_arr as $old_values)
                        {
                            if(!empty($old_values) && isset($old_values))
                                array_push($push_audio_bites_array ,$old_values );
                        }

                    }
                   

                    $push_audio_bites = implode("," , $push_audio_bites_array);
                    $push_audio_bites = "'$push_audio_bites'";
                  
                }
                else {

                    $push_audio_bites = $audio_bites_full_web;
                }
                
               

               
               
                fwrite($myfile, "------------------------------------------------------- "); 
                fclose($myfile);

         
            

                $pushed_at = date('Y-m-d H:i:s');

                // For temporary use ,  will delete and insert if exist in remote
                // if(!$connection)
                // {
                    $connection= mysqli_connect($host , $user , $password , $db_name);
                    mysqli_set_charset($connection ,"utf8");
                // }

                $sql_test_local = "select * from web where newsid = '$news_id' ";
                $run_sql_test_local= mysqli_query($connection, $sql_test_local);
                $num_rows_content_local = mysqli_num_rows($run_sql_test_local);
                if($num_rows_content_local ==  1)
                {

                    $query_new_news_update = "update  web set 
                       regular_feed = $push_regularFeed, ready_version = $push_readyVesion  ,thumbnail = $push_thumbnail ,
                       audio_complete_story = $push_audio  , photos = $gall_img , rough_cut = $push_roughcut, news_file = $push_newsbody,  
                         pushed_by = '$pushed_by' ,   pushed_date = '$pushed_at' ,
                         vimeo_regular_feed = $vimeo_regularfeed , vimeo_ready_version = $vimeo_readyversion , vimeo_rough_cut = $vimeo_roughcut, extra_files = $push_extra_file,
                         ftp_dir = '$news_ftp_path' ,
                         audio_bites = $push_audio_bites
                         where newsid = '$news_id'  ;";
                        // ) 
                        // VALUES 
                        // ('$news_id',  $push_regularFeed,$push_readyVesion , $push_preview , $push_thumbnail,
                        //      $push_audio , '$gall_img', $push_roughcut , '$newsbody_full' , 
                        //     '$pushed_by' ,'$pushed_at'
                            
                        //     )";  
                        

                        $run_query = mysqli_query($connection , $query_new_news_update);
                }
                else
                {
                    $wp_post = "NULL";
                    $query_new_news_push = "insert into web(
                        newsid ,  regular_feed , ready_version ,thumbnail ,
                        audio_complete_story   , photos , rough_cut, news_file ,  
                         pushed_by ,   pushed_date , wp_post_id,
                         vimeo_regular_feed , vimeo_ready_version , vimeo_rough_cut , extra_files , audio_bites , ftp_dir
                        ) 
                        VALUES 
                        ('$news_id',  $push_regularFeed,$push_readyVesion  , $push_thumbnail,
                             $push_audio , $gall_img, $push_roughcut , $push_newsbody , 
                            '$pushed_by' ,'$pushed_at' , $wp_post,
                             $vimeo_regularfeed , $vimeo_readyversion , $vimeo_roughcut , $push_extra_file , $push_audio_bites , '$news_ftp_path'
                            
                            )";    

                        $run_query = mysqli_query($connection , $query_new_news_push);
                }

                echo $query_new_news_push ;

                

                if(count($push_remote_py_resp_arr) == count($files_to_push))
                {
                    if($run_query)
                    {
                        
                        $_SESSION['notice_remote'] = "Success";
                
                    }
                    else
                    { 
                    
                        $_SESSION['notice_remote'] = "Error";
                    }

                }
                else
                {
                    $_SESSION['notice_remote'] = "Error";
                }



            }
            else
            {
               
                $_SESSION['notice_remote'] = "Error";
            }

        }
        else
        {
            
            $_SESSION['notice_remote'] = "Error";
        }
    }
    else
    {
        
        $_SESSION['notice_remote'] = "Error";
    }




    if(isset($_POST['del_remote_files']))
    {
        if(isset($_POST['news_id']))
        {        

            $news_id = $_POST['news_id'];
            $news_id = mysqli_real_escape_string($connection, $news_id);


            $sql_content = "select * from web where newsid = '$news_id' ";
            $run_sql_content= mysqli_query($connection, $sql_content);
            $num_rows_content = mysqli_num_rows($run_sql_content);

            if($num_rows_content == 1)
            {
                $sql_content_nas = "select * from nas where newsid = '$news_id' ";
                $run_sql_content_nas = mysqli_query($connection, $sql_content_nas);
                $num_rows_content_nas = mysqli_num_rows($run_sql_content_nas);
                $row_content_nas = mysqli_fetch_assoc($run_sql_content_nas); 

                $video_type_nas =  $row_content_nas['video_type'];  
                $date =  $row_content_nas['local_published_date'];
                $byline =  $row_content_nas['byline'];
                $category_nas =  $row_content_nas['category_list'];



                
                $byline_ftp  = remove_special_chars($byline) ;


                $row_content = mysqli_fetch_assoc($run_sql_content);
                $regularfeed_full = $row_content['regular_feed'];
                $thumbnail_full = $row_content['thumbnail'];
                $readyversion_full = $row_content['ready_version'];                            
                $newsbody_full = $row_content['news_file'];
                $extra_file_full = $row_content['extra_files'];

                $vimeo_regularfeed_web = $row_content['vimeo_regular_feed'];
                $vimeo_readyversion_web = $row_content['vimeo_ready_version'];
                $vimeo_rough_cut_web = $row_content['vimeo_rough_cut'];

                $photos = $row_content['photos'];
                $photos_array = explode(',' , $photos);

                $audio = $row_content['audio_complete_story'];
                $roughcut = $row_content['rough_cut'];

                $wp_id = $row_content['wp_post_id'];

                $ftp_dir = $row_content['ftp_dir'];
                $dir_del = "/".$ftp_dir;


                $audio_bites = $row_content['audio_bites'];
               

                if($category_nas ==  $_SESSION['interview_id'])
                {
                    $dir_del = "/".$interview_path_ftp."/$byline_ftp";

                }
                else
                {
                    $dir_del = "/".$news_path_ftp."/$date/$byline_ftp";

                }



                if($video_type_nas == 'vimeo')
                {

                    if($vimeo_regularfeed_web != NULL)
                    {
                        $uri="/videos/$vimeo_regularfeed_web";
                        $response = $client->request($uri, [], 'DELETE');                           
                    }

                    if($vimeo_readyversion_web != NULL)
                    {
                        $uri="/videos/$vimeo_readyversion_web";
                        $response = $client->request($uri, [], 'DELETE'); 
                    }

                    if($vimeo_rough_cut_web != NULL)
                    {
                        $uri="/videos/$vimeo_rough_cut_web";
                        $response = $client->request($uri, [], 'DELETE'); 
                    }

                }


            
                $ftp = ftp_connect("$ftp_url");
                ftp_login($ftp, "$ftp_username", "$ftp_password");
                ftp_pasv($ftp, true);
        
        
                if(recursive_ftp_folder($ftp ,  $dir_del))
                {
                    ftp_rmdir($ftp, $dir_del);
                }
        
        
                ftp_close($ftp);
                   

                   
                    

                    $sql_del_web = "delete from web where newsid = '$news_id' ";
                    $run_sql_del_web= mysqli_query($connection, $sql_del_web);



                    if($run_sql_del_web)
                    {
                        $_SESSION['notice_remote'] = "success_remotefile_delete";
                    }
                    else
                    {
                        $_SESSION['notice_remote'] = "failed_remotefile_delete";
                    }

                    
            }
        }
    }

}

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = $_SERVER['HTTP_REFERER'];
}

       header("Location: ". $location_redirect);

    exit();