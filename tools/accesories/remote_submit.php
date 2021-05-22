<?php



include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/vimeo.php";
include "nas_function/functions.php";
include "../global/timezone.php";

 

set_time_limit(0);


 


// -------------- VARIABLE DECLARATION ----------------
$interview_path = 'interview_data';
$news_path = 'news_data';
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
                $videolong_full = $row_content['videolong'];
                $thumbnail_full = $row_content['thumbnail'];
                $videolazy_full = $row_content['videolazy'];                        
                $audio_full = $row_content['audio'];
                $videoextra_full = $row_content['videoextra'];       
                $newsbody_full = $row_content['newsbody'];
                $date_full = $row_content['created_date'];
                $category_full = $row_content['category_list'];
                $tags_full = $row_content['tag_list'];
                $video_type = $row_content['video_type'];
                $local_published_date = $row_content['local_published_date'];

               
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
                    $videolong_full_web = $row_content_web['videolong'];
                    $thumbnail_full_web = $row_content_web['thumbnail'];
                    $videolazy_full_web = $row_content_web['videolazy'];                            
                    $newsbody_full_web = $row_content_web['newsbody'];
                    $audio_full_web = $row_content_web['audio'];
                    $videoextra_full_web = $row_content_web['videoextra'];
                    $gallery_full_web = $row_content_web['photos'];
                    $gallery_full_web_arr = explode(',' ,  $gallery_full_web) ;

                    if($videolong_full_web == NULL)
                        $videolong_full_web = "NULL";
                    else
                        $videolong_full_web = "'$videolong_full_web'";

                  
                    if($thumbnail_full_web == NULL)
                        $thumbnail_full_web = "NULL";

                    else
                        $thumbnail_full_web = "'$thumbnail_full_web'";

                    if($videolazy_full_web == NULL)
                        $videolazy_full_web = "NULL";

                    else
                        $videolazy_full_web = "'$videolazy_full_web'";

                    if($newsbody_full_web == NULL)
                        $newsbody_full_web = "NULL";

                    else
                        $newsbody_full_web = "'$newsbody_full_web'";

                    if($audio_full_web == NULL)
                        $audio_full_web = "NULL";
                    else
                        $audio_full_web = "'$audio_full_web'";


                    if($videoextra_full_web == NULL)
                        $videoextra_full_web = "NULL";

                    else
                        $videoextra_full_web = "'$videoextra_full_web'";

                    if($gallery_full_web == NULL)
                        $gallery_full_web = "NULL";

                    else
                        $gallery_full_web = "'$gallery_full_web'";


                }
                else
                {
                    
                    $videolong_full_web = "NULL";
                    $thumbnail_full_web = "NULL";
                    $videolazy_full_web = "NULL";                            
                    $newsbody_full_web = "NULL";
                    $audio_full_web = "NULL";
                    $videoextra_full_web = "NULL";
                    $gallery_full_web = "NULL";
                    $gallery_full_web_arr = array();
                }


                
                $myfile = fopen("../log/remotecopy_log.txt", "a") or die("Unable to open file!");  
                fwrite($myfile, "\n---------------$date_full / $byline_full ---------------- \n"); 

                $logged_date = date('Y-m-d H:i:s');
                fwrite($myfile, "\n Logged Date: $logged_date \n");

                $dir_byline = remove_special_chars($byline_full) ;
               
                if($isInterview)
                {
                    $ftp = ftp_connect("$ftp_url");
                    ftp_login($ftp, "$ftp_username", "$ftp_password");
                    ftp_pasv($ftp, true);
                    ftp_mkdir($ftp, "/".$interview_path."/".$dir_byline);
                    ftp_close($ftp);

                    $news_ftp_path  = "/".$interview_path."/".$dir_byline;
                    $news_ftp_path_sql  = $interview_path."/".$dir_byline;

                }
                else
                {

                
                    $folder_exists = is_dir('ftp://'.$ftp_un_prefix.':'.$ftp_password.'@'.$ftp_url.'/'.$news_path.'/'.$local_published_date);
                    if(!$folder_exists)
                    {
                        echo "here !";
                        $ftp = ftp_connect("$ftp_url");
                        ftp_login($ftp, "$ftp_username", "$ftp_password");
                        ftp_pasv($ftp, true);
                        $dir = $local_published_date;
                        echo "/".$news_path."/".$dir;
                         ftp_mkdir($ftp, "/".$news_path."/".$dir);
                        // ftp_mkdir($ftp, "/hehehhe");
                        ftp_close($ftp);
                    }

                    $ftp = ftp_connect("$ftp_url");
                    ftp_login($ftp, "$ftp_username", "$ftp_password");
                    ftp_pasv($ftp, true);
                    $dir_byline = remove_special_chars($byline_full) ;
                    ftp_mkdir($ftp, "/".$news_path."/".$local_published_date."/".$dir_byline);
                    ftp_close($ftp);

                    $news_ftp_path  = "/".$news_path."/".$local_published_date."/".$dir_byline;
                    $news_ftp_path_sql  = "$news_path/".$local_published_date."/".$dir_byline;

                }
               
                if(isset($_POST['gall_img']))
                {
                    $gall_img = $_POST['gall_img'];
                
                    foreach($gall_img as $gal_img_arr)
                    {
                        if(file_exists('../'.$gal_img_arr))
                        {
                            $sourceName = explode("/" ,$gal_img_arr ) ;
                            $sourceName = end($sourceName );

                            if(ftp_remote($news_ftp_path."/".$sourceName , "../".$gal_img_arr))
                            {
                                $gal_img_arr = "$news_ftp_path_sql/$sourceName" ;
                                array_push($gallery_full_web_arr , $gal_img_arr);

                                $text = "$sourceName Gallery Photo Pushed\n";
                                fwrite($myfile, $text);

                                
                            }
                            else
                            {
                                $text = "$sourceName Gallery Photo Failed to Pushed\n";                           

                                fwrite($myfile, $text);
                                $_SESSION['notice_remote'] = "Error";
                            }

                            
                        }
                    }
    
                }
               
                $gall_img = implode(',' , $gallery_full_web_arr) ;





                $file_type = $_POST['file_name'];

                $pushed_by = $_POST['pushed_by'];
                $pushed_by = mysqli_real_escape_string($connection, $pushed_by);
                


                    if(in_array('newsbody' ,$file_type ))
                    {
                        if(file_exists('../'.$newsbody_full))
                        {
                            $sourceName = explode("/" ,$newsbody_full ) ;
                            $sourceName = end($sourceName );

                            // if(ftp_remote('newsbody' , '../'.$newsbody_full , $sourceName))
                            if(ftp_remote($news_ftp_path."/".$sourceName , "../".$newsbody_full))
                            { 

                                $text = "$sourceName News  Body Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_newsbody = "'$news_ftp_path_sql/$sourceName'" ;
                            }
                            else
                            {
                                $push_newsbody = "NULL";
                                $text = "$sourceName News Body Failed to Pushed\n";
                                fwrite($myfile, $text);                            

                                $_SESSION['notice_remote'] = "Error";
                            }
                        
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

                  
                    if(in_array('videoLong' ,$file_type ))
                    {
                        if(file_exists('../'.$videolong_full))
                        {
                            $sourceName = explode("/" ,$videolong_full ) ;
                            $sourceName = end($sourceName );


                            if($video_type == 'selfhost')
                            {
                                if(ftp_remote($news_ftp_path."/".$sourceName , "../".$videolong_full))
                                {

                                    $text = "$sourceName Video Long Pushed\n";
                                    fwrite($myfile, $text);                        


                                    $push_videoLong = "'$news_ftp_path_sql/$sourceName'" ;
                                }
                                else
                                {
                                    $push_videoLong = "NULL";
                                    $text = "$sourceName Video Long Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                                $vimeo_videolong =  "NULL" ;
                            }

                            if($video_type == 'vimeo')
                            {
                                echo "Video Long Vime<br>";

                                $file_name = '../'.$videolong_full;
                                $vimeo_vl_name = end(explode("/" ,$videolong_full ));
                                $uri = $client->upload($file_name, array(
                                "name" => $vimeo_vl_name,
                                "description" => ""
                                ));
    
    
                                $response = $client->request($uri . '?fields=transcode.status');
                            
                                $response = $client->request($uri . '?fields=link');
    
                                $id = explode("/" , $response['body']['link']);
                                $id = end($id);
    
                                $vimeo_videolong =  "'$id'" ;
                                print_r($response);
                                
                                echo "Video Long ID vimeo_videolong<br>";
                                if(isset($vimeo_videolong))
                                {

                                    $text = "$sourceName Video Long Pushed to VIMEO\n";
                                    fwrite($myfile, $text);                        


                                    $push_videoLong = "NULL" ;
                                }
                                else
                                {
                                    $push_videoLong = "NULL";
                                    $text = "$sourceName Video Long Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                            }


                        
                        }
                        else
                        {
                            $push_videoLong = "NULL";
                        }
                        

                    }
                    else
                    {
                        $push_videoLong = $videolong_full_web;
                        $vimeo_videolong =  "NULL" ;
                    }
                    
                    if(in_array('videoLazy' ,$file_type ))
                    {
                        if(file_exists('../'.$videolazy_full))
                        {
                            $sourceName = explode("/" ,$videolazy_full ) ;
                            $sourceName = end($sourceName );

                            if($video_type == 'selfhost')
                            {
                                if(ftp_remote($news_ftp_path."/".$sourceName , "../".$videolazy_full))
                                {
                                    $text = "$sourceName Video Lazy Pushed\n";
                                    fwrite($myfile, $text);                            

                                    $push_videoLazy = "'$news_ftp_path_sql/$sourceName'" ;

                                }
                                else
                                {
                                    $push_videoLazy = "NULL";
                                    $text = "$sourceName Video Lazy Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }

                                $vimeo_videolazy = "NULL";
                            }

                            if($video_type == 'vimeo')
                            {
                                $file_name = '../'.$videolazy_full;
                                $vimeo_vla_name = end(explode("/" ,$videolazy_full ));
                                $uri = $client->upload($file_name, array(
                                "name" => $vimeo_vla_name,
                                "description" => ""
                                ));
    
    
                                $response = $client->request($uri . '?fields=transcode.status');
                            
                                $response = $client->request($uri . '?fields=link');
    
                                $id = explode("/" , $response['body']['link']);
                                $id = end($id);
    
                                $vimeo_videolazy =  "'$id'" ;
                                print_r($response);

                                if(isset($vimeo_videolazy))
                                {

                                    $text = "$sourceName Video Lon:azyg Pushed to VIMEO\n";
                                    fwrite($myfile, $text);                        


                                    $push_videoLazy = "NULL" ;
                                }
                                else
                                {
                                    $push_videoLazy = "NULL";
                                    $text = "$sourceName Video Long Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                            }

                            
                        }
                        else
                        {
                            $push_videoLazy = "NULL";
                            $vimeo_videolazy = "NULL";
                        }
                        
                    }
                    else
                    {
                        $push_videoLazy = $videolazy_full_web;
                        $vimeo_videolazy = "NULL";
                    }

                    if(in_array('videoextra' ,$file_type ))
                    {
                        if(file_exists('../'.$videoextra_full))
                        {
                            $sourceName = explode("/" ,$videoextra_full ) ;
                            $sourceName = end($sourceName );

                            if($video_type == 'selfhost')
                            {
                                if(ftp_remote($news_ftp_path."/".$sourceName , "../".$videoextra_full))
                                {
                                    // $push_videoextra = "'$videoextra_full'" ;
                                    $text = "$sourceName Video Extra  Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $push_videoextra = "'$news_ftp_path_sql/$sourceName'" ;

                                }
                                else
                                {
                                    $push_videoextra = "NULL";
                                    $text = "$sourceName Video Extra Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                                $vimeo_videoextra = 'NULL';
                            }

                            if($video_type == 'vimeo')
                            {
                                $file_name = '../'.$videoextra_full;
                                $vimeo_ve_name = end(explode("/" ,$videoextra_full ));
                                $uri = $client->upload($file_name, array(
                                "name" => vimeo_ve_name,
                                "description" => ""
                                ));
    
    
                                $response = $client->request($uri . '?fields=transcode.status');
                            
                                $response = $client->request($uri . '?fields=link');
    
                                $id = explode("/" , $response['body']['link']);
                                $id = end($id);
    
                                $vimeo_videoextra =  "'$id'" ;
                                print_r($response);

                                if(isset($vimeo_videoextra))
                                {

                                    $text = "$sourceName Video Long Pushed to VIMEO\n";
                                    fwrite($myfile, $text);                        


                                    $push_videoextra = "NULL" ;
                                }
                                else
                                {
                                    $push_videoextra = "NULL";
                                    $text = "$sourceName Video Long Failed to Pushed\n";
                                    fwrite($myfile, $text);
                                    

                                    $_SESSION['notice_remote'] = "Error";
                                }
                            }
                            
                        }
                        else
                        {
                            $push_videoextra = "NULL";
                        }
                    }
                    else
                    {
                        $push_videoextra = $videoextra_full_web;
                        $vimeo_videoextra = 'NULL';
                    }

              
                    if(in_array('thumbnail' ,$file_type ))
                    {
                        if(file_exists('../'.$thumbnail_full))
                        {
                            $sourceName = explode("/" ,$thumbnail_full ) ;
                            $sourceName = end($sourceName );
                            if(ftp_remote($news_ftp_path."/".$sourceName , "../".$thumbnail_full))
                            {
                                // $push_thumbnail = "'$thumbnail_full'" ;
                                $text = "$sourceName Thumbnail Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_thumbnail = "'$news_ftp_path_sql/$sourceName'" ;

                            }
                            else
                            {
                                $push_thumbnail = "NULL";
                                $text = "$sourceName Thumbnail Failed to Pushed\n";
                                fwrite($myfile, $text);
                                

                                $_SESSION['notice_remote'] = "Error";
                            }

                            
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
                        if(file_exists('../'.$audio_full))
                        {
                            $sourceName = explode("/" ,$audio_full ) ;
                            $sourceName = end($sourceName );
                            if(ftp_remote($news_ftp_path."/".$sourceName , "../".$audio_full))
                            {
                                // $push_audio = "'$audio_full'" ;
                                $text = "$sourceName Audio Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_audio = "'$news_ftp_path_sql/$sourceName'" ;

                            }
                            else
                            {
                                $push_audio = "NULL";
                                $text = "$sourceName Audio Failed to Pushed\n";
                                fwrite($myfile, $text);
                                

                                $_SESSION['notice_remote'] = "Error";
                            }

                            
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


                    
                 

                    
               
                fwrite($myfile, "------------------------------------------------------- "); 
                fclose($myfile);


            

                $pushed_at = date('Y-m-d H:i:s');

                // For temporary use ,  will delete and insert if exist in remote

                $sql_test_local = "select * from web where newsid = '$news_id' ";
                $run_sql_test_local= mysqli_query($connection, $sql_test_local);
                $num_rows_content_local = mysqli_num_rows($run_sql_test_local);
                if($num_rows_content_local ==  1)
                {

                    $query_new_news_update = "update  web set 
                       videolong = $push_videoLong , videolazy = $push_videoLazy  ,thumbnail = $push_thumbnail ,
                        audio = $push_audio  , photos = '$gall_img' , videoextra = $push_videoextra, newsbody = $push_newsbody,  
                         pushed_by = '$pushed_by' ,   pushed_date = '$pushed_at' ,
                         vimeo_videolong = $vimeo_videolong , vimeo_videolazy = $vimeo_videolazy , vimeo_video_extra = $vimeo_videoextra
                         where newsid = '$news_id'  ;";
                        // ) 
                        // VALUES 
                        // ('$news_id',  $push_videoLong ,$push_videoLazy , $push_preview , $push_thumbnail,
                        //      $push_audio , '$gall_img', $push_videoextra , '$newsbody_full' , 
                        //     '$pushed_by' ,'$pushed_at'
                            
                        //     )";  
                        

                        $run_query = mysqli_query($connection , $query_new_news_update);
                }
                else
                {
                    $wp_post = "NULL";
                    $query_new_news_push = "insert into web(
                        newsid ,  videolong , videolazy ,thumbnail ,
                        audio   , photos , videoextra, newsbody ,  
                         pushed_by ,   pushed_date , wp_post_id,
                         vimeo_videolong , vimeo_videolazy , vimeo_video_extra
                        ) 
                        VALUES 
                        ('$news_id',  $push_videoLong ,$push_videoLazy  , $push_thumbnail,
                             $push_audio , '$gall_img', $push_videoextra , $push_newsbody , 
                            '$pushed_by' ,'$pushed_at' , $wp_post,
                            $vimeo_videolong , $vimeo_videolazy , $vimeo_videoextra
                            
                            )";    

                        $run_query = mysqli_query($connection , $query_new_news_push);
                }

                

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
                $videolong_full = $row_content['videolong'];
                $thumbnail_full = $row_content['thumbnail'];
                $videolazy_full = $row_content['videolazy'];                            
                $newsbody_full = $row_content['newsbody'];

                $vimeo_videolong_web = $row_content['vimeo_videolong'];
                $vimeo_videolazy_web = $row_content['vimeo_videolazy'];
                $vimeo_video_extra_web = $row_content['vimeo_video_extra'];

                $photos = $row_content['photos'];
                $photos_array = explode(',' , $photos);

                $audio = $row_content['audio'];
                $videoextra = $row_content['videoextra'];

                $wp_id = $row_content['wp_post_id'];

               

                if($category_nas ==  $_SESSION['interview_id'])
                {
                    $dir_del = "/".$interview_path."/$byline_ftp";

                }
                else
                {
                    $dir_del = "/".$news_path."/$date/$byline_ftp";

                }



                
                if($video_type_nas == 'selfhost')
                { 
                    if($videolong_full != NULL)
                    {
                        echo $videolong_full;                                     
                        ftp_delete_rem($videolong_full,'file');                                
                    }

                    if($videolazy_full != NULL)
                    {          
                        echo $videolazy_full;               
                        ftp_delete_rem($videolazy_full  ,'file');
                    }

                    if($videoextra != NULL)
                    {
                        // $file = explode("/" , $videoextra);
                        // $reverse_file = array_reverse($file);
                        // $last = $reverse_file[1];
                        // $end = $reverse_file[0];
                        // $path =  "$last/$end";
                        echo $videoextra; 
                        ftp_delete_rem($videoextra  ,'file');
                    }

                }
               


                if($video_type_nas == 'vimeo')
                {

                    if($vimeo_videolong_web != NULL)
                    {
                        $uri="/videos/$vimeo_videolong_web";
                        $response = $client->request($uri, [], 'DELETE');                           
                    }

                    if($vimeo_videolazy_web != NULL)
                    {
                        $uri="/videos/$vimeo_videolazy_web";
                        $response = $client->request($uri, [], 'DELETE'); 
                    }

                    if($vimeo_video_extra_web != NULL)
                    {
                        $uri="/videos/$vimeo_video_extra_web";
                        $response = $client->request($uri, [], 'DELETE'); 
                    }

                }



                   

                    if($thumbnail_full != NULL)
                    {
                        echo $thumbnail_full; 
                        // $file = explode("/" , $thumbnail_full);
                        // $reverse_file = array_reverse($file);
                        // $last = $reverse_file[1];
                        // $end = $reverse_file[0];
                        // $path =  "$last/$end";
                        
                        ftp_delete_rem($thumbnail_full , 'file');
                    }
                    

                   

                    if($newsbody_full != NULL)
                    {echo $newsbody_full; 
                        // $file = explode("/" , $newsbody_full);
                        // $reverse_file = array_reverse($file);
                        // $last = $reverse_file[1];
                        // $end = $reverse_file[0];
                        // $path =  "$last/$end";
                      
                        ftp_delete_rem($newsbody_full , 'file');
                    }


                    if($audio != NULL)
                    {
                        // $file = explode("/" , $audio);
                        // $reverse_file = array_reverse($file);
                        // $last = $reverse_file[1];
                        // $end = $reverse_file[0];
                        // $path =  "$last/$end";
                        echo $audio; 
                        ftp_delete_rem($audio , 'file');
                    }

                   

                    foreach($photos_array as $ph)
                    {
                        // $file = explode("/" , $ph);
                        // $reverse_file = array_reverse($file);
                        // $last = $reverse_file[1];
                        // $end = $reverse_file[0];
                        // $path =  "$last/$end";
                        echo $ph; 
                        ftp_delete_rem($ph , 'file');
                    }    
                    ftp_delete_rem($dir_del , 'folder');
                    

                    $sql_del_web = "update web set videolong = null , videolazy = null ,
                                    thumbnail = null, audio = null , photos = null , videoextra = null , newsbody = null where newsid = '$news_id' ";
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