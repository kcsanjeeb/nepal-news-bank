<?php


include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/wp_api_env.php";
include "nas_function/functions.php";
include "../global/timezone.php";



if(isset($_POST['submit_push']))
{
    if(isset($_POST['news_id']) && !empty($_POST['news_id']))
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
            $extra_file_full = $row_content['extra_files'];
            $extra_files_description_full = $row_content['extra_files_description'];

            $gallery_description_full = $row_content['gallery_description'];

            $audio_description_full = $row_content['audio_description'];
            $audio_bites_description_full = $row_content['audio_bites_description'];


            if($extra_files_description_full == NULL)
                $extra_files_description_full = "NULL";
            else
                $extra_files_description_full = $extra_files_description_full ;
            
            if($audio_description_full == NULL)
                $audio_description_full = "NULL";
            else
                $audio_description_full = $audio_description_full ;

            if($audio_bites_description_full == NULL)
                $audio_bites_description_full = "NULL";
            else
                $audio_bites_description_full = $audio_bites_description_full ;
            

            if($gallery_description_full == NULL)
                $gallery_description_full = "NULL";
            else
                $gallery_description_full = $gallery_description_full ;

            
            $series_nas = $row_content['series'];

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

                        $extra_file_web = $row_content_web['extra_files'];

                        $audio_bites_web = $row_content_web['audio_bites'];


                        $audio_full_web = $row_content_web['audio_complete_story'];
                        $roughcut_full_web = $row_content_web['rough_cut'];
                        $gallery_full_web = $row_content_web['photos'];
                        $gallery_full_web_arr = explode(',' ,  $gallery_full_web) ;

                        $vimeo_rough_cut = $row_content_web['vimeo_rough_cut'];
                        $vimeo_readyversion = $row_content_web['vimeo_ready_version'];
                        $vimeo_regularfeed = $row_content_web['vimeo_regular_feed'];



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

                        if($gallery_full_web == '')
                            $gallery_full_web = "NULL";

                        else
                            $gallery_full_web = "'$gallery_full_web'";
                        
                        

                        if($extra_file_web == NULL)
                            $extra_file_web = "NULL";

                        else
                            $extra_file_web = "'$extra_file_web'";
                        
                        
                        if($audio_bites_web == NULL)
                            $audio_bites_web = "NULL";

                        else
                            $audio_bites_web = "'$audio_bites_web'";


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

                        $extra_file_web = "NULL";
                        $audio_bites_web = "NULL";


                        $gallery_full_web_arr = array();
                    }
                

            $push_newsbody = $newsbody_full_web;
            $push_regularFeed = $regularfeed_full_web;
            $push_extra = $extra_file_web;
            $push_readyVersion = $readyversion_full_web;
            $push_thumbnail = $thumbnail_full_web;
            $push_audio = $audio_full_web;
            $push_roughcut = $roughcut_full_web;

            $push_audio_bites = $audio_bites_web;

            
            $gallery_full_web_json = str_replace("'","",$gallery_full_web);
            $gallery_full_web_json_exp = explode("," ,$gallery_full_web_json);

            if(count($gallery_full_web_json_exp) == 0)
            {
                $gallery_full_web_json = "NULL";
            }

            // echo $gall_img ;
            // exit();



            $category_full_arr = explode("," , $category_full);
            $tags_full_arr = explode("," , $tags_full);


            if($thumbnail_full != null)
            {

            
                $thumbnail_path = $thumbnail_full ;


                $file = file_get_contents( $thumbnail_path );
                $file_name = explode("/" ,$thumbnail_path);
                $file_name = end($file_name);
                    
                    $url = $domain_url.'/wp-json/wp/v2/media';
                    $ch = curl_init();
                    curl_setopt( $ch, CURLOPT_URL, $url );
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch, CURLOPT_POST, 1 );
                    curl_setopt( $ch, CURLOPT_POSTFIELDS, $file );
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                        'Content-Disposition: form-data; filename="'.$file_name.'"',
                        'Authorization: Bearer '.$token_bearer.''
                        ] );
                    
                    $result = curl_exec( $ch );
                    $result = json_decode($result);
                    $result = json_decode(json_encode($result) , true);  
                    $respCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $err = curl_error($ch);   
                    echo "<br>POST:<br> Error: $err <br> Resp Code:$respCode <br><br> ";                       
                    curl_close( $ch );
                    
                    $featured_media_id  = $result['id'];

                }
                else
                {
                    $featured_media_id = null;
                }

                // echo "News Bodyyyyy $newsbody_full";

                if($newsbody_full != null)
                {
                    $push_newsbody_json = str_replace("'","",$push_newsbody);
                    $push_newsbody_disp =  $domain_url.'/'.$push_newsbody_json;
    
                    $content_json = "<div class='ead-preview'><div class='ead-document' style='position: relative;padding-top: 50%;'>
                                            <iframe src='//view.officeapps.live.com/op/embed.aspx?src=".$push_newsbody_disp."' 
                                                title='".$byline_full."' class='ead-iframe' style='width: 100%;height: 100%;border: none;position: absolute;left: 0;top: 0;'>
                                                </iframe></div></div>";
                }
                else
                {
                    $content_json = null;
                }

               


                $push_readyVersion_json = str_replace("'","",$push_readyVersion);
                $push_regularFeed_json = str_replace("'","",$push_regularFeed);
                $push_readyVersion_json = str_replace("'","",$push_readyVersion);
                $push_roughcut_json = str_replace("'","",$push_roughcut);
                $push_newsbody_json = str_replace("'","",$push_newsbody);
                $push_audio_json = str_replace("'","",$push_audio);
                $push_extra_json = str_replace("'","",$push_extra);
                $push_audio_bites_json = str_replace("'","",$push_audio_bites);
                

                


                $thumbnail_full_web_json = str_replace("'","",$push_thumbnail);

                $video_type = $video_type ;

                $push_regularfeed_disp =  $domain_url.'/'.$push_regularFeed_json;

                if($video_type == 'selfhost')
                {
                    $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
                    'haru_video_url_type'=> 'insert',
                    'haru_video_url' => array('mp4' => $push_regularfeed_disp , 'webm' => '')
                        ),

                'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series_nas")
                );

                    $push_roughcut_json = $push_roughcut_json ;
                    $push_readyVersion_json = $push_readyVersion_json ;

                }

                if($video_type == 'vimeo')
                {
                    $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'vimeo',
                    'haru_video_id'=> $vimeo_regularfeed
                
                            ),
                            'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series_nas")
                            );

                    $push_roughcut_json = "https://vimeo.com/".$vimeo_rough_cut ;
                    $push_readyVersion_json = "https://vimeo.com/".$vimeo_readyversion ;
                }
                // echo "<br>dadasdsadasd ".count($category_full_arr)."<br>";
                if(count($category_full_arr) == 1 && $category_full_arr[0] == '' )
                {
                    $category_full_arr = null ;
                }
                
                if(count($tags_full_arr) == 1 && $tags_full_arr[0] == '')
                {
                    $tags_full_arr = null ;
                }
                

             if($thumbnail_full != null)
            {
                $data_array =  array(
                    "status" => "draft" , 
                    "title" => "$byline_full",
                    "slug" => "$byline_full",
                    "acf_fields" => array('regular_feed_link'=>$push_regularFeed_json,'ready_version_link'=>$push_readyVersion_json,
                                            'rough_cut_link' => $push_roughcut_json ,   'news_body_file' => $push_newsbody_json ,
                                            'audio_complete_story' =>    $push_audio_json,
                                            "gallery" => $gallery_full_web_json,                                                    
                                            "video_thumbnail" => $thumbnail_full_web_json,
                                            "extra_files" => $push_extra_json,
                                            "extra_files_description" => $extra_files_description_full,
                                            "audio_description" =>$audio_description_full,
                                            "audio_bites_description" => $audio_bites_description_full,
                                            "audio_bites" =>$push_audio_bites_json,
                                            "gallery_description" =>$gallery_description_full

                                           
            ),
                    "featured_media" => $featured_media_id,
                    "video_category" => $category_full_arr,
                    "video_tag" => $tags_full_arr,

                    "cmb2" => $cmb2 ,
                    "content" => $content_json
                
                
                
                    );
            
            }
            else
            {
                $data_array =  array(
                    "status" => "draft" , 
                    "title" => "$byline_full",
                    "slug" => "$byline_full",
                    "acf_fields" => array('regular_feed_link'=>$push_regularFeed_json,'ready_version_link'=>$push_readyVersion_json,
                                            'rough_cut_link' => $push_roughcut_json ,   'news_body_file' => $push_newsbody_json ,
                                            'audio' =>    $push_audio_json,
                                            "gallery" => $gallery_full_web_json,                                                    
                                            "video_thumbnail" => $thumbnail_full_web_json,
                                            "extra_files" => $push_extra_json,
                                            "extra_files_description" => "$extra_files_description_full",
                                            "audio_description" =>$audio_description_full,
                                            "audio_bites_description" => $audio_bites_description_full,
                                            "audio_bites" =>$push_audio_bites_json,
                                            "gallery_description" =>$gallery_description_full
                                           
            ),
                   
                    "video_category" => $category_full_arr,
                    "video_tag" => $tags_full_arr,

                    "cmb2" => $cmb2 ,
                    "content" => $content_json
                
                
                
                    );
            }

           
         
            

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
                echo "<br>POST:<br> Error: $err <br> Resp Code:$respCode <br><br> ";                 
                curl_close($curl);
                // echo "POST NEW ID: <br> ".$response['id']." <br><br><br><br><br><br>";
                // echo "Response Code: $respCode";

                $post_new_id = $response['id'];

                // echo "Wordpress id: $post_new_id";
                // echo $err ;


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
                // $response = json_decode($response);
                // $response = json_decode(json_encode($response) , true);
                // $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                // $err = curl_error($curl);                    
                curl_close($curl);

                // $data_blank = '';
                // $curl = curl_init();
                // curl_setopt_array($curl, array(                    
                // CURLOPT_URL => "$domain_url/custom_post_level_creator.php?post_id_nas=".$post_new_id,
                // CURLOPT_RETURNTRANSFER => true,
                // CURLOPT_TIMEOUT => 30,
                // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                // CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS => $data_blank ,
                //     CURLOPT_HTTPHEADER => array(
                        
                //     ),
                // ));

                // $response_customapi = curl_exec($curl);
                // // $response = json_decode($response);
                // // $response = json_decode(json_encode($response) , true);
                // // $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                // // $err = curl_error($curl);                    
                // curl_close($curl);



                    if($series_nas != null)
                    {
                            $series_nas_exp = explode("," , $series_nas);

                            foreach($series_nas_exp as $sn)
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

                      
                        // echo "<br> $domain_url/wp-json/wp/v2/haru_series/$series_nas <br>";
                        //     echo "<br> $err <br>";
                            // echo "<br> ".print_r($response)." <br>";



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

                        // echo "<br> $domain_url/wp-json/wp/v2/haru_series/$series_nas <br>";
                        // echo "<br> $err <br>";
                        // echo "<br> $respCode <br>";
                        // echo "<br> ".print_r($response)." <br>";
                        }
                    }
           

                       

                        

                      


                if($respCode == 201 || $respCode == 200)
                {
                    $query_wp_id_upd = "update  web set  wp_post_id = '$post_new_id', wp_media_id='$featured_media_id', wp_post_type='draft'  where newsid = '$news_id'  ;";                  
                    $run_query_wp_id_upd = mysqli_query($connection , $query_wp_id_upd);
                    $_SESSION['notice_remote'] = "Success_push_post";
                }
                else
                {
                    $_SESSION['notice_remote'] = "Error_push_post";
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

       header("Location: ". $_SERVER['HTTP_REFERER']);
        // echo '<script> window.location.href = "'.$location_redirect.'"; </script>'; 

    exit();