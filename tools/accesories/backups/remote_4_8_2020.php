<?php



include "session_handler/session_service.php";
include "connection.php";

    date_default_timezone_set("Asia/Kathmandu");

 

    // $remote_file_server_path = 'https://offixservices.com/localhostftp';

    $remote_file_server_path = 'https://sanjeebkc.com.np/nepalnewsclient/nepalnewsbank';

    require "vimeo/vendor/autoload.php" ;

    $client_id = "292064cf0b8953a10ac9db92d7b2f5696f0dddee";
    $client_secret = "hsqGeFV83euyE2ekqx0l/XaZC7u8wLtaLavF9JJYfp1sIUt68Yyk8QaK0ArAVnwh5a5e5GM3ddH5lTFy6U4FnB3oPjsUOGUPfCGYORDOe6HfxpGCA9w+SHzBnYMwDpWB";
    $access_token = '698d0cd9e4ab6085e475de568500a613';
    $client = new \Vimeo\Vimeo($client_id, $client_secret, $access_token);
    



function ftp_remote($folder , $DestName , $sourceName)
{
    // $ftp = ftp_connect("ftp.offixservices.com");
    // ftp_login($ftp, "offix@offixservices.com", "Offix_(*(*");
    $ftp = ftp_connect("ftp.sanjeebkc.com.np");
    ftp_login($ftp, "nepalnewsbank@sanjeebkc.com.np", "nepalnewsbank");
    ftp_pasv($ftp, true);
    $file_status = ftp_put($ftp, "/$folder/$sourceName", "$DestName", FTP_BINARY); 
    // ftp_remote('videolong' , '../'.$videolong_full , $sourceName)
    ftp_close($ftp); 

    if(isset($file_status))
    {
        return 1 ;
    }
    else
    {
        return 0 ;
    }

    
}


function ftp_delete_rem($file)
{
    // connect and login to FTP server
    $ftp = ftp_connect("ftp.sanjeebkc.com.np");
    ftp_login($ftp, "nepalnewsbank@sanjeebkc.com.np", "nepalnewsbank");
    ftp_pasv($ftp, true);

    $file = explode("/" , $file);
    $file = end($file);

    // $file = "php/test.txt";

    // try to delete file
    
        if (ftp_delete($ftp, $file))
        {
             return 1 ;
        }
        else
        {
            return 0;
        }
    
    

    // close connection
    ftp_close($ftp);

    return 0 ;

}




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
                $langauge_full = $row_content['news_language'];
                $video_type = $row_content['video_type'];
               


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

                    if($videolong_full_web == '')
                        $videolong_full_web = "NULL";
                    else
                        $videolong_full_web = "'$videolong_full_web'";

                  
                    if($thumbnail_full_web == '')
                        $thumbnail_full_web = "NULL";

                    else
                        $thumbnail_full_web = "'$thumbnail_full_web'";

                    if($videolazy_full_web == '')
                        $videolazy_full_web = "NULL";

                    else
                        $videolazy_full_web = "'$videolazy_full_web'";

                    if($newsbody_full_web == '')
                        $newsbody_full_web = "NULL";

                    else
                        $newsbody_full_web = "'$newsbody_full_web'";

                    if($audio_full_web == '')
                        $audio_full_web = "NULL";
                    else
                        $audio_full_web = "'$audio_full_web'";


                    if($videoextra_full_web == '')
                        $videoextra_full_web = "NULL";

                    else
                        $videoextra_full_web = "'$videoextra_full_web'";

                    if($gallery_full_web == '')
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


                
                $myfile = fopen("../pushlog.txt", "a") or die("Unable to open file!");  
                fwrite($myfile, "\n---------------$date_full / $byline_full ---------------- \n"); 
                

                if(isset($_POST['gall_img']))
                {
                    $gall_img = $_POST['gall_img'];
                
                    foreach($gall_img as $gal_img_arr)
                    {
                        if(file_exists('../'.$gal_img_arr))
                        {
                            $sourceName = explode("/" ,$gal_img_arr ) ;
                            $sourceName = end($sourceName );
                            if(ftp_remote('gallery' , '../'.$gal_img_arr , $sourceName))
                            {
                                $gal_img_arr = "$remote_file_server_path/gallery/$sourceName" ;
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

                if( isset($_POST['submit_push']))
                {
                    $file_type = array();
                }

                $pushed_by = $_POST['pushed_by'];
                $pushed_by = mysqli_real_escape_string($connection, $pushed_by);
                


                    if(in_array('newsbody' ,$file_type ))
                    {
                        if(file_exists('../'.$newsbody_full))
                        {
                            $sourceName = explode("/" ,$newsbody_full ) ;
                            $sourceName = end($sourceName );

                            if(ftp_remote('newsbody' , '../'.$newsbody_full , $sourceName))
                            { 

                                $text = "$sourceName News  Body Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_newsbody = "'$remote_file_server_path/newsbody/$sourceName'" ;
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

                            if(ftp_remote('videolong' , '../'.$videolong_full , $sourceName))
                            {

                                $text = "$sourceName Video Long Pushed\n";
                                fwrite($myfile, $text);                        


                                $push_videoLong = "'$remote_file_server_path/videolong/$sourceName'" ;
                            }
                            else
                            {
                                $push_videoLong = "NULL";
                                $text = "$sourceName Video Long Failed to Pushed\n";
                                fwrite($myfile, $text);
                                

                                $_SESSION['notice_remote'] = "Error";
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
                    }
                    

                    if(in_array('videoLazy' ,$file_type ))
                    {
                        if(file_exists('../'.$videolazy_full))
                        {
                            $sourceName = explode("/" ,$videolazy_full ) ;
                            $sourceName = end($sourceName );
                            if(ftp_remote('videolazy' , '../'.$videolazy_full , $sourceName))
                            {
                                $text = "$sourceName Video Lazy Pushed\n";
                                fwrite($myfile, $text);                            

                                $push_videoLazy = "'$remote_file_server_path/videolazy/$sourceName'" ;

                            }
                            else
                            {
                                $push_videoLazy = "NULL";
                                $text = "$sourceName Video Lazy Failed to Pushed\n";
                                fwrite($myfile, $text);
                                

                                $_SESSION['notice_remote'] = "Error";
                            }

                            
                        }
                        else
                        {
                            $push_videoLazy = "NULL";
                        }
                        
                    }
                    else
                    {
                        $push_videoLazy = $videolazy_full_web;
                    }

                
                    if(in_array('thumbnail' ,$file_type ))
                    {
                        if(file_exists('../'.$thumbnail_full))
                        {
                            $sourceName = explode("/" ,$thumbnail_full ) ;
                            $sourceName = end($sourceName );
                            if(ftp_remote('thumbnail' , '../'.$thumbnail_full , $sourceName))
                            {
                                // $push_thumbnail = "'$thumbnail_full'" ;
                                $text = "$sourceName Thumbnail Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_thumbnail = "'$remote_file_server_path/thumbnail/$sourceName'" ;

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
                            if(ftp_remote('audio' , '../'.$audio_full , $sourceName))
                            {
                                // $push_audio = "'$audio_full'" ;
                                $text = "$sourceName Audio Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_audio = "'$remote_file_server_path/audio/$sourceName'" ;

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


                    if(in_array('videoextra' ,$file_type ))
                    {
                        if(file_exists('../'.$videoextra_full))
                        {
                            $sourceName = explode("/" ,$videoextra_full ) ;
                            $sourceName = end($sourceName );
                            if(ftp_remote('videoextra' , '../'.$videoextra_full , $sourceName))
                            {
                                // $push_videoextra = "'$videoextra_full'" ;
                                $text = "$sourceName Video Extra  Pushed\n";
                                fwrite($myfile, $text);
                                

                                $push_videoextra = "'$remote_file_server_path/videoextra/$sourceName'" ;

                            }
                            else
                            {
                                $push_videoextra = "NULL";
                                $text = "$sourceName Video Extra Failed to Pushed\n";
                                fwrite($myfile, $text);
                                

                                $_SESSION['notice_remote'] = "Error";
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
                         pushed_by = '$pushed_by' ,   pushed_date = '$pushed_at' where newsid = '$news_id'  ;";
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
                         pushed_by ,   pushed_date , wp_post_id
                        ) 
                        VALUES 
                        ('$news_id',  $push_videoLong ,$push_videoLazy  , $push_thumbnail,
                             $push_audio , '$gall_img', $push_videoextra , $push_newsbody , 
                            '$pushed_by' ,'$pushed_at' , $wp_post
                            
                            )";    

                        $run_query = mysqli_query($connection , $query_new_news_push);
                }

                

                if($run_query)
                {
                    
                    $_SESSION['notice_remote'] = "Success";

                    
                // wp_create post starts

                    // if(isset($_POST['submit_push']))
                    // {
                       

                    //     $category_full_arr = explode("," , $category_full);
                    //     $tags_full_arr = explode("," , $tags_full);



                    //     $thumbnail_path = $thumbnail_full ;
                    //     $file = file_get_contents( '../'.$thumbnail_path );
                    //     $file_name = explode("/" ,$thumbnail_path);
                    //     $file_name = end($file_name);
                        
                    //     $url = 'https://nepalnewsclient.sanjeebkc.com.np/wp-json/wp/v2/media';
                    //     $ch = curl_init();
                    //     curl_setopt( $ch, CURLOPT_URL, $url );
                    //     curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    //     curl_setopt( $ch, CURLOPT_POST, 1 );
                    //     curl_setopt( $ch, CURLOPT_POSTFIELDS, $file );
                    //     curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                    //         'Content-Disposition: form-data; filename="'.$file_name.'"',
                    //         'Authorization: Bearer '.$token_bearer.''
                    //         ] );
                        
                    //     $result = curl_exec( $ch );
                    //     $result = json_decode($result);
                    //     $result = json_decode(json_encode($result) , true);                        
                    //     curl_close( $ch );
                        
                    //     $featured_media_id  = $result['id'];

                    //     $push_newsbody_json = str_replace("'","",$push_newsbody);

                    //     $content_json = "<div class='ead-preview'><div class='ead-document' style='position: relative;padding-top: 50%;'>
                    //                             <iframe src='//view.officeapps.live.com/op/embed.aspx?src=".$push_newsbody_json."' 
                    //                                 title='".$byline_full."' class='ead-iframe' style='width: 100%;height: 100%;border: none;position: absolute;left: 0;top: 0;'>
                    //                                 </iframe></div></div>";
    

                    //     $push_videoLazy_json = str_replace("'","",$push_videoLazy);

                    //     $push_videoLong_json = str_replace("'","",$push_videoLong);
                    //     $push_videoLazy_json = str_replace("'","",$push_videoLazy);
                    //     $push_videoextra_json = str_replace("'","",$push_videoextra);
                    //     $push_newsbody_json = str_replace("'","",$push_newsbody);

                    //     $push_audio_json = str_replace("'","",$push_audio);
                        
                    //     $gallery_full_web_json = str_replace("'","",$gall_img);
                    //     $gallery_full_web_json_exp = explode("," ,$gallery_full_web_json);
                    //     if(count($gallery_full_web_json_exp) == 0)
                    //     {
                    //         $gallery_full_web_json = "NULL";
                    //     }


                    //     $thumbnail_full_web_json = str_replace("'","",$push_thumbnail);

                    //     $video_type = $video_type ;

                    //     if($video_type == 'selfhost')
                    //     {
                    //         $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
                    //         'haru_video_url_type'=> 'insert',
                    //         'haru_video_url' => array('mp4' => $push_videoLong_json , 'webm' => '')
                    //         ));

                    //         $push_videoextra_json = $push_videoextra_json ;
                    //         $push_videoLazy_json = $push_videoLazy_json ;

                    //     }

                    //     if($video_type == 'vimeo')
                    //     {
                    //         $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'vimeo',
                    //         'haru_video_id'=> $vimeo_videolong
                           
                    //         ));

                    //         $push_videoextra_json = "https://vimeo.com/".$vimeo_video_extra ;
                    //         $push_videoLazy_json = "https://vimeo.com/".$vimeo_videolazy ;
                    //     }

                        
                     

                    //    $data_array =  array(
                    //         "status" => "publish" , 
                    //         "title" => "$byline_full",
                    //         "acf_fields" => array('video_long_link'=>$push_videoLong_json,'video_lazy_link'=>$push_videoLazy_json,
                    //                                 'video_extra_link' => $push_videoextra_json ,   'news_body_file' => $push_newsbody_json ,
                    //                                 'audio' =>    $push_audio_json,
                    //                                 "gallery" => $gallery_full_web_json,                                                    
                    //                                 "video_thumbnail" => $thumbnail_full_web_json,
                    //                                 "language" => $langauge_full
                    //    ),
                    //         "featured_media" => $featured_media_id,
                    //         "video_category" => $category_full_arr,
                    //         "video_tag" => $tags_full_arr,

                    //         "cmb2" => $cmb2 ,
                    //         "content" => $content_json
                        
                         
                        
                    //         );

                    //         $data = json_encode($data_array);


                    //     $curl = curl_init();
                    //     curl_setopt_array($curl, array(                    
                    //     CURLOPT_URL => "https://nepalnewsclient.sanjeebkc.com.np/wp-json/wp/v2/haru_video/",
                    //     CURLOPT_RETURNTRANSFER => true,
                    //     CURLOPT_TIMEOUT => 30,
                    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    //     CURLOPT_CUSTOMREQUEST => "POST",
                    //     CURLOPT_POSTFIELDS => $data ,
                    //         CURLOPT_HTTPHEADER => array(
                    //             "cache-control: no-cache",
                    //             "content-type: application/json",
                    //             'Authorization: Bearer '.$token_bearer.''
                    //         ),
                    //     ));
                    
                    //     $response = curl_exec($curl);
                    //     $response = json_decode($response);
                    //     $response = json_decode(json_encode($response) , true);
                    //     $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    //     $err = curl_error($curl);                    
                    //     curl_close($curl);
                    //     // echo "POST NEW ID: <br> ".$response['id']." <br><br><br><br><br><br>";
                    //     // echo "Response Code: $respCode";

                    //     $post_new_id = $response['id'];




                    //     $data_blank = '';
                    //     $curl = curl_init();
                    //     curl_setopt_array($curl, array(                    
                    //     CURLOPT_URL => "http://nepalnewsclient.sanjeebkc.com.np/custom_post_level_creator.php?post_id_nas=".$post_new_id,
                    //     CURLOPT_RETURNTRANSFER => true,
                    //     CURLOPT_TIMEOUT => 30,
                    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    //     CURLOPT_CUSTOMREQUEST => "POST",
                    //     CURLOPT_POSTFIELDS => $data_blank ,
                    //         CURLOPT_HTTPHEADER => array(
                                
                    //         ),
                    //     ));
                    
                    //     $response_customapi = curl_exec($curl);
                    //     // $response = json_decode($response);
                    //     // $response = json_decode(json_encode($response) , true);
                    //     // $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    //     // $err = curl_error($curl);                    
                    //     curl_close($curl);


                    //     if($respCode == 201)
                    //     {
                    //         $query_wp_id_upd = "update  web set  wp_post_id = '$post_new_id'  where newsid = '$news_id'  ;";                  
                    //         $run_query_wp_id_upd = mysqli_query($connection , $query_wp_id_upd);
                    //         $_SESSION['notice_remote'] = "Success_push_post";
                    //     }
                    //     else
                    //     {
                    //         $_SESSION['notice_remote'] = "Error_push_post";
                    //     }
                        


                       
                    // }
                    
                // wp create post ends



                        


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
                $row_content_nas = mysqli_fetch_assoc($run_sql_content);

                $video_type_nas = $row_content_nas['video_type'];
                $vimeo_long_nas = $row_content_nas['vimeo_videolong'];
                $vimeo_lazy_nas = $row_content_nas['vimeo_videolazy'];
                $vimeo_extra_nas = $row_content_nas['vimeo_video_extra'];


                $row_content = mysqli_fetch_assoc($run_sql_content);
                $videolong_full = $row_content['videolong'];
                // $preview_full = $row_content['previewgif'];
                $thumbnail_full = $row_content['thumbnail'];
                $videolazy_full = $row_content['videolazy'];                            
                $newsbody_full = $row_content['newsbody'];

                $photos = $row_content['photos'];
                $photos_array = explode(',' , $photos);

                $audio = $row_content['audio'];
                $videoextra = $row_content['videoextra'];

                $wp_id = $row_content['wp_post_id'];

                
                if($video_type_nas == 'selfhost')
                {

                    if($videolong_full != NULL)
                    {
                        ftp_delete_rem($videolong_full);                                
                    }

                    if($videolazy_full != NULL)
                    {
                        ftp_delete_rem($videolazy_full);
                    }

                    if($videoextra != NULL)
                    {
                        ftp_delete_rem($videoextra);
                    }

                }

                if($video_type_nas == 'vimeo')
                {

                    if($vimeo_long_nas != NULL)
                    {
                      // delete from vimeo                                
                    }

                    if($vimeo_lazy_nas != NULL)
                    {
                        // delete from vimeo   
                    }

                    if($vimeo_extra_nas != NULL)
                    {
                        // delete from vimeo   
                    }

                }



                   

                    if($thumbnail_full != NULL)
                    {
                        ftp_delete_rem($thumbnail_full);
                    }
                    

                   

                    if($newsbody_full != NULL)
                    {
                        ftp_delete_rem($newsbody_full);
                    }


                    if($audio != NULL)
                    {
                        ftp_delete_rem($audio);
                    }

                   

                    foreach($photos_array as $ph)
                    {
                        ftp_delete_rem($ph);
                    }                    

                    $sql_del_web = "update web set videolong = null , videolazy = null ,
                                    thumbnail = null, audio = null , photos = null , videoextra = null , newsbody = null where newsid = '$news_id' ";
                    $run_sql_del_web= mysqli_query($connection, $sql_del_web);


                    // if video type == vimeo
                    // if vimeo id != null
                    // delete from vimeo
                    // set attributes to null

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