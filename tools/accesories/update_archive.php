<?php

include "session_handler/session_service.php";
include "connection.php";

include "environment/ftp.php";
include "environment/wp_api_env.php";


include "nas_function/functions.php";
include "../global/timezone.php";

include "../global/file_paths.php";


// -------------- VARIABLE DECLARATION ----------------
// $archive_path = 'my_data/archive_data';
// $archive_path_ftp = 'archive_data';




// ----------------------------------------------------


// print_r($_POST);
// print_r($_FILES);
// print_r($_FILES['pic']['name'][4]);

// print_r(array_keys($_FILES['pic']['name']));

// exit();




if(isset($_POST['submit']))
{
    $news_id =  mysqli_real_escape_string($connection, $_POST['newsid']);
    $query_fetch_news = "select * from archives where archive_id = '$news_id'";
    $run_sql_fetch_news= mysqli_query($connection, $query_fetch_news);
    $num_rows_news = mysqli_num_rows($run_sql_fetch_news);
    $news_row_details = mysqli_fetch_assoc($run_sql_fetch_news);

    $archive_path = $news_row_details['local_dir'] ;
    $archive_path_ftp = $news_row_details['ftp_dir'] ;

    $thumbnail_local_path = $news_row_details['thumbnail_local_path'] ;

  

    if($num_rows_news <  1)
    {
        exit("Error!");
    }


    $title = $news_row_details['title'];
    $title = rtrim ( $title ) ;



    $tags = $_POST['newsTag'];
    $tags_final_array = array();
    
    foreach($tags as $t)
    {
        if($t != '')
        {
            $t = mysqli_real_escape_string($connection, $t ) ;
            array_push( $tags_final_array ,$t );
        }
    }
    $tags = implode(',', $tags_final_array);
    if($tags == '') $tags = null ;

    $tags_sql = "'$tags'";



    $newsCategories = $_POST['newsCategories'];
    $newsCategories_final_array = array();

    foreach($newsCategories as $nc)
    {
        if($nc != '')
        {
            $nc = mysqli_real_escape_string($connection, $nc ) ;
            array_push( $newsCategories_final_array ,$nc );
        }
    }
    $newsCategories = implode(',', $newsCategories_final_array);
    if($newsCategories == '') $newsCategories = null ;
    $newsCategories_sql = "'$newsCategories'";


    $series_name = $_POST['series'];                    
    $series_final_array = array();
    
    foreach($series_name as $s)
    {
        if($s != '')
        {
            $s = mysqli_real_escape_string($connection, $s ) ;
            array_push( $series_final_array ,$s );
        }
    }
    $series = implode(',', $series_final_array);
    if($series == '') $series = null ;
    $series_sql = "'$series'";


    $archive_id = $news_id;
    $created_at = $news_row_details['published_date'];

    $created_at_db = $news_row_details['created_date'];

    $created_at_fetch = explode(" " , $created_at );
    $time_file_name = end($created_at_fetch);
    $time_file_name = str_replace(":","",$time_file_name);

    $date_file_name =  $news_row_details['created_date'];
    $date_file_name = str_replace("-","",$date_file_name);

    $file_name = $date_file_name."_".$time_file_name."_".$archive_id;

 

    $myfile = fopen("../log/archive_log.txt", "a") or die("Unable to open file!"); 
    fwrite($myfile, "\n--------------- $created_at_db / $title  ------------------ \n");     
                   
    $logged_date = date('Y-m-d H:i:s');
    fwrite($myfile, "\n Logged Date: $logged_date \n");



    $title_directory = remove_special_chars($title);


    $local_path = $archive_path."/".$title_directory ;
    $local_path_clean = $archive_path."/".$title_directory ;
    $ftp_path = $archive_path_ftp."/".$title_directory;

  


    $update_query = '';

    $files_to_push = array();
    $videos_py = array();
    $photos_py = array();

    $ftp = ftp_connect("$ftp_url");
    ftp_login($ftp, "$ftp_username", "$ftp_password");
    ftp_pasv($ftp, true);

    
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
            if($news_row_details['thumbnail'] != null )
            {
                if(file_exists($thumbnail_local_path))
                {
                    unlink($thumbnail_local_path);
                }
                if($news_row_details['wp_media_id'] != null)
                    $wp_thumbnail_url = $domain_url.'/wp-json/wp/v2/media/'.$news_row_details['wp_media_id'];
                else
                    $wp_thumbnail_url = $domain_url.'/wp-json/wp/v2/media';
            }
            else
                $wp_thumbnail_url = $domain_url.'/wp-json/wp/v2/media';


            $thumbnail_path =$local_path."/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_tmp_name = $_FILES['thumbImg']['tmp_name'] ;
            move_uploaded_file($thumbnail_tmp_name, $thumbnail_path) ;

            

            $sourceName = $file_name."_thumbnail.".$fileActualExt_thumbImg;


            ftp_delete_rem('/'.$news_row_details['thumbnail'] , 'file');

            array_push($files_to_push , $sourceName) ;
            $thumb_py = $sourceName ;



                        $wp_media_file = file_get_contents($thumbnail_path );
                        $wp_media_file_name = explode("/" ,$thumbnail_path);
                        $wp_media_file_name = end($wp_media_file_name);

                        $url = $wp_thumbnail_url;
                        $ch = curl_init();
                        curl_setopt( $ch, CURLOPT_URL, $url );
                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                        curl_setopt( $ch, CURLOPT_POST, 1 );
                        curl_setopt( $ch, CURLOPT_POSTFIELDS, $wp_media_file );
                        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                            'Content-Disposition: form-data; filename="'.$wp_media_file_name.'"',
                            'Authorization: Bearer '.$token_bearer.''
                            ] );
                        
                        $result = curl_exec( $ch );
                        $result = json_decode($result);
                        $result = json_decode(json_encode($result) , true);                        
                        curl_close( $ch );
                        
                        $featured_media_id  = $result['id'];
                        $featured_media_id_sql = "'$featured_media_id'";

                        if(!isset($featured_media_id)) $featured_media_id = "NULL";

                        if($news_row_details['wp_media_id'] == null )
                        {
                            $update_query .= "update archives set wp_media_id = '$featured_media_id' where archive_id = '$news_id' ;" ;
                        }
          
            
        }
      

    }
    else
    {
        $featured_media_id = $news_row_details['wp_media_id'];
    }
  
    
    if(count($_POST['video']) == count($_FILES['video']['name']))
    {
        $data_videos = array();
        $counter = 0 ;
        $counter_file = 0 ;
        $data_vids_rows = array();

        $row_dir_name_root = "archive_videos";

        $json_existing_video_all = $news_row_details['archive_videos'];
        $json_existing_video_decoded_all = json_decode($json_existing_video_all , true);
        $file_names = array_column($json_existing_video_decoded_all, 'video');



        if(!is_dir($local_path.'/'.$row_dir_name_root ))
        {

            mkdir($local_path.'/'.$row_dir_name_root, 0777 , true);    
            chmod($local_path.'/'.$row_dir_name_root, 0777);

            ftp_mkdir($ftp, "/".$ftp_path.'/'.$row_dir_name_root);

        }

        $folder_counter = 1 ;
        $file_counter = 1 ;

        foreach($_POST['video'] as $vid)
        {


            


    
            $desc = $vid['desc'];
            $data_vids_row['desc'] = $desc ;

            if(isset($desc) && !empty($desc))
            {
                $data_vids_row['desc'] = $desc ;
            }
            else
            {
                $data_vids_row['desc'] = "Archive video description." ;
            }

            



            // upload File of $_FILES['video']['name'][$counter]
            // push file to ftp 

                $fileName = $_FILES['video']['name'][$counter] ;      
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['video']['type'][$counter];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('video'  );

                if(empty($fileName) || !isset($fileName)) continue ;

                do{
                    $row_dir_name = "archive_video_$folder_counter";
                    if(!is_dir($local_path.'/'.$row_dir_name_root.'/'.$row_dir_name ))
                    {
                        $status_dir = 0 ;
                    }
                    else
                    {
                        $folder_counter++;
                        $status_dir = 1 ;
                    }
    
    
                }while($status_dir);
    
                $row_file_path = $row_dir_name_root."/".$row_dir_name;
                
                
                mkdir($local_path.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777 , true);    
                chmod($local_path.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777);
                ftp_mkdir($ftp,  "/".$ftp_path."/".$row_dir_name_root."/".$row_dir_name);

                if (in_array($file_type_explode[0] , $allowed ))
                {
                    // cheeck file name here !!!!!
                


                    do{

                        // $path_to_check = "$ftp_path/".$row_dir_name_root."/".$row_dir_name."/".$file_name."_video_".$counter_file.".".$fileActualExt ;
                        // if(file_exists($video_path)){
                        //     $status = 1;
                        //     $counter_file++;
                        // } 
                        // else $status = 0 ;

                        // echo "<br>---------------------------<br>";

                        //     print_r($file_names);
                        //     echo "Value to check: $path_to_check";

                        // echo "<br>---------------------------<br>";

                        // if(in_array($path_to_check , $file_names))
                        // foreach($file_names as $fn)
                        // {
                            $fn = implode("," ,$file_names );
                            if (strpos( $fn, $file_name."_video_".$file_counter.".".$fileActualExt) !== false)
                            {
                                $status = 1;
                                $file_counter++;
                            }
                            else
                            {
                                $status = 0 ;
                            }
                           
                        // }
                       

                    }while($status);


                        $video_path =$local_path."/".$row_dir_name_root."/".$row_dir_name."/".$file_name."_video_".$file_counter.".".$fileActualExt;
                       
                        // echo $video_path ;
                        // exit();


                        $video_tmp_name = $_FILES['video']['tmp_name'][$counter] ;
                        move_uploaded_file($video_tmp_name, $video_path) ;

                    $video_path_remote ="$ftp_path/".$row_dir_name_root."/".$row_dir_name."/".$file_name."_video_".$file_counter.".".$fileActualExt; 
                    $video_path_remote_sql ="'$video_path_remote'";  

                                        
                    $sourceName = $file_name."_video_".$file_counter.".".$fileActualExt;
                    $sourceName = $row_dir_name_root."/".$row_dir_name."/".$file_name."_video_".$file_counter.".".$fileActualExt;

                    array_push($files_to_push , $sourceName) ;
                    array_push($videos_py , $sourceName) ;

                    $data_vids_row['video'] = $video_path_remote ;

                    $counter_file++;
                    $file_counter++ ;

                    
                    
                }            


            array_push($data_vids_rows , $data_vids_row);
            $counter++;
            $folder_counter++ ;
            

        }

        if($news_row_details['archive_videos'] != null)
        {
            $json_existing_video = $news_row_details['archive_videos'];
            
            if($json_existing_video == 'null' || $json_existing_video == null  ) 
                $json_existing_video_decoded = arary();
            else 
                $json_existing_video_decoded = json_decode($json_existing_video , true);

            foreach($data_vids_rows as $newdata)
            {
                array_push($json_existing_video_decoded , $newdata);
            }
            $data_vids_rows = $json_existing_video_decoded ;

        }

        $data_videos = json_encode($data_vids_rows , JSON_UNESCAPED_UNICODE);
        $update_query .= "update archives set archive_videos = '$data_videos' where archive_id = '$news_id';";
        
    }    

    if(!isset($data_videos)) $data_videos = $news_row_details['archive_videos'] ;





    if(count($_POST['pic']) == count($_FILES['pic']['name']))
    {
        $counter = 0 ;
    

        $data_pics_rows = array();

        $pic_key = array_keys($_FILES['pic']['name']);




        $row_dir_name_root = "archive_pictures";

        $json_existing_pics_all = $news_row_details['archive_photos'];
        $json_existing_pics_decoded_all = json_decode($json_existing_pics_all , true);
        $file_names = array_column($json_existing_pics_decoded_all, 'photos');
        $file_names_arrs = array();

        foreach($file_names as $pfna)
        {
            foreach($pfna as $pfn)
            {
                array_push($file_names_arrs , $pfn);
            }
        }




        if(!is_dir($local_path.'/'.$row_dir_name_root ))
        {

            mkdir($local_path.'/'.$row_dir_name_root, 0777 , true);    
            chmod($local_path.'/'.$row_dir_name_root, 0777);

            ftp_mkdir($ftp, "/".$ftp_path.'/'.$row_dir_name_root);

        }

        $file_name_counter = 1;
        $folder_counter = 1 ;

        foreach($_POST['pic'] as $pic)
        {
            // $data_pics_row = array();


          
            
            
           


            $desc =  $pic['desc'];
       
            
            if(isset($desc) && !empty($desc))
            {
                $data_pics_row['desc'] = $desc ;
            }
            else
            {
                $data_pics_row['desc'] = "Archive picture description." ;
            }



            $data_pics_row['photos'] = array();

            $key_file_pic = $pic_key[$counter];

            $multi_row = 0 ;
            $folder_made = 0 ;
              
            foreach($_FILES['pic']['name'][$key_file_pic] as $files_row)
            {

                               

                $fileName = $_FILES['pic']['name'][$key_file_pic][$multi_row] ;
                $fileExt = explode('.' , $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $file_type =    $_FILES['pic']['type'][$key_file_pic][$multi_row];
                $file_type_explode = explode("/" , $file_type);
                $allowed = array('image' );

                if(isset($fileName) && !empty($fileName) && $folder_made == 0)
                {
                    do{
                        $row_dir_name = "archive_picture_$folder_counter";
                        if(!is_dir($local_path.'/'.$row_dir_name_root.'/'.$row_dir_name ))
                        {
                            $status_dir = 0 ;
                        }
                        else
                        {
                            $folder_counter++;
                            $status_dir = 1 ;
                        }
        
        
                    }while($status_dir);

                    
                    mkdir($local_path.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777 , true);    
                    chmod($local_path.'/'.$row_dir_name_root.'/'.$row_dir_name, 0777);
                    ftp_mkdir($ftp,  "/".$ftp_path."/".$row_dir_name_root."/".$row_dir_name);  

                    $folder_made = 1 ;
                }

             
               
    
    
                $row_file_path = $row_dir_name_root."/".$row_dir_name;



                if (in_array($file_type_explode[0] , $allowed ))
                {
                    if(empty($fileName)) continue ;



                    $fn = implode("," ,$file_names_arrs );
                 



                    do{

                        if (strpos( $fn, $file_name."_picture_".$file_name_counter.".".$fileActualExt) !== false)
                        {
                            $status = 1;
                            $file_name_counter++;
                        }
                        else
                        {
                            $status = 0 ;
                        }


                        // if(file_exists($pic_path)){
                        //     $status = 1;
                        //     $file_name_counter++;
                        // } 
                        // else $status = 0 ;


                    }while($status);


                    $pic_path =$local_path."/".$row_file_path."/".$file_name."_picture_".$file_name_counter.".".$fileActualExt;



                    $pic_tmp_name = $_FILES['pic']['tmp_name'][$key_file_pic][$multi_row];
                    move_uploaded_file($pic_tmp_name, $pic_path) ;

                    $pic_path_remote ="$ftp_path/".$row_file_path."/".$file_name."_picture_".$file_name_counter.".".$fileActualExt; 
                    $pic_path_remote_sql ="'$pic_path_remote'";  

                    $sourceName = $file_name."_picture_".$file_name_counter.".".$fileActualExt; 
                    $sourceName = $row_file_path."/".$file_name."_picture_".$file_name_counter.".".$fileActualExt; 

    
                    array_push($files_to_push , $sourceName) ;
                    array_push($photos_py , $sourceName) ;

                    array_push($data_pics_row['photos'] , $pic_path_remote) ;

                    
                }
              


                $file_name_counter++;
                $multi_row++;

            }

            // $data = $pic_path_remote."*`".$desc;

            // array_push($data_pics , $data);
            if(count($data_pics_row['photos'] > 0))
                array_push($data_pics_rows , $data_pics_row);
            $counter++;
            $folder_counter++;

        }

        if($news_row_details['archive_photos'] != null)
        {
            $json_existing_pics = $news_row_details['archive_photos'];
            $json_existing_pics_decoded = json_decode($json_existing_pics , true);


                foreach($data_pics_rows as $newdata)
                {
                    array_push($json_existing_pics_decoded , $newdata);
                }

            $data_pics_rows = $json_existing_pics_decoded ;

        }

        $data_pics = json_encode($data_pics_rows , JSON_UNESCAPED_UNICODE);
        $update_query .= "update archives set archive_photos = '$data_pics' where archive_id = '$news_id';";



    }
    
    if(!isset($data_pics))  $data_pics= $news_row_details['archive_photos'] ; ;
   

    // echo $data_pics ;
    ftp_close($ftp); 


    $news_ftp_path_py  = "/$ftp_path";
    $news_ftp_path_py = str_replace(" ","+-*",$news_ftp_path_py);
    // $local_file_py = '../my_data'.$news_ftp_path_py.'/';
    $local_file_py = $local_path.'/';
    $local_file_py = str_replace(" ","+-*",$local_file_py);

    $files_to_push_csv = implode("," , $files_to_push);

    $sym = "$files_to_push_csv $ftp_url $ftp_username $ftp_password $news_ftp_path_py $local_file_py";
    $push_remote_py_resp = shell_exec("python ftp_push.py $sym");



    foreach($videos_py as $asg)
    {
        if (strpos( $push_remote_py_resp, $asg) !== false)
        {
                fwrite($myfile, "\n $asg Pushed to remote Success\n");
        }
        else {
            
            fwrite($myfile, "\n $asg Pushed to remote Failes\n");;
        }
    }

    foreach($photos_py as $asg)
    {
        if (strpos( $push_remote_py_resp, $asg) !== false)
        {
                fwrite($myfile, "\n $asg Pushed to remote Success\n");
        }
        else {
            
            fwrite($myfile, "\n $asg Pushed to remote Failes\n");;
        }
    }

    if(isset($thumb_py))
    {
        if (strpos( $push_remote_py_resp, $thumb_py) !== false)
        {
            $thumbnail_path ="$local_path_clean/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_path_remote ="$ftp_path/".$file_name."_thumbnail.".$fileActualExt_thumbImg;
            $thumbnail_path_remote_sql = "'$thumbnail_path_remote'";

            if($news_row_details['thumbnail'] == null)
                $update_query .= "update archives set thumbnail = $thumbnail_path_remote_sql where archive_id = '$news_id' ;" ;

            fwrite($myfile, "\n $thumb_py Pushed to remote Success\n");;
        }
        else {
            
        
            fwrite($myfile, "\n $thumb_py Pushed to remote Failed\n");;
        }
    }
    else
    {
        $thumbnail_path_remote = $news_row_details['thumbnail']; ;
    }



   

    $tags_array = explode("," , $tags);
    $newsCategories_array = explode("," , $newsCategories);
    $series_array = explode("," , $series);

    // $data_videos = implode("~~" , $data_videos) ;
    // $data_pics = implode("~~" , $data_pics);

    $cmb2  = array('haru_video_metabox' => array('haru_video_server' => 'selfhost',
                    'haru_video_url_type'=> 'insert',                  
                        ),

                'haru_video_attached_data_field' => array('haru_video_attached_seriess' => "$series")
                );


    $data_array =  array(
        "status" => "publish" , 
        "title" => "$title",
        "slug" => "$title",
        "acf_fields" => array(  "archive_video_all" => $data_videos,
                                 "archive_picture_all" => $data_pics,
                                    
                                "video_thumbnail" => $thumbnail_path_remote
                               
        ),
        "featured_media" => $featured_media_id,
        "video_category" => $newsCategories_array,
        "video_tag" => $tags_array,

        "cmb2" => $cmb2 ,       
    
    
    
        );

        // print_r($data_videos);
        // print_r($data_pics);

        $data = json_encode($data_array);

        // echo $data ;

        $curl = curl_init();
        curl_setopt_array($curl, array(                    
        CURLOPT_URL => $domain_url."/wp-json/wp/v2/haru_video/".$news_row_details['wp_id'],
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
        curl_close($curl);
        

        $post_new_id = $news_row_details['wp_id'];
        $post_new_id_sql = "'$post_new_id'";

        


        


        if($series != null && $series != '' )
        {       
                $existing_series = $news_row_details['series'];
                $existing_series_exp = explode("," , $existing_series);

                $series_exp = explode("," , $series);

                foreach($series_exp as $sn)
                {                  
                    if(in_array( $sn , $existing_series_exp)) continue ;

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


            }
            $series = "'$series'";
        }
        

        $update_query .= "update archives set series = $series  where archive_id = '$news_id';";


    $connection= mysqli_connect($host , $user , $password , $db_name);


    $update_query .= "update archives set series =  $series ,tags =  '$tags' , categories = '$newsCategories' where archive_id = '$news_id'; ";

    $run_query = mysqli_multi_query($connection, $update_query);


            // echo $query_new_archives ;



}

fwrite($myfile, "------------------------------------------------------- "); 
fclose($myfile);

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = $_SERVER['HTTP_REFERER'];
}

// header("Location: ".$location_redirect);
exit();


// echo "WP ID : $post_new_id <br>";
// echo "WP ID : $featured_media_id <br>";