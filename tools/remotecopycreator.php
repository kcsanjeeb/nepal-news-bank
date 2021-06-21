<?php


include "accesories/session_handler/session_views.php";

  
include "global/timezone.php";

$selected_date = date("Y-m-d");

if(isset($_GET['date']))
{
    $selected_date  = $_GET['date'] ;
}

if(isset($_GET['news_id']))
{
    $news_id_get  = $_GET['news_id'] ;
    $is_get_id = 0 ;
}
else
{
    $is_get_id = 1 ;
}




include "accesories/connection.php";

$sql_byline = "select newsid, byline from nas where local_published_date = '$selected_date' order by created_date desc;";
$run_sql_byline= mysqli_query($connection, $sql_byline);
$num_rows_byline = mysqli_num_rows($run_sql_byline);

if(isset($_GET['nod']))
{
    $nod  = $_GET['nod'] ;
    $nod = (int) $nod ;
}
else
{
    $nod  = 20 ;
}


$sql_remote_top = "SELECT nas.byline, nas.newsid , web.wp_post_id,web.wp_post_type
FROM nas
INNER JOIN web ON nas.newsid=web.newsid where web.wp_post_id order by web.pushed_date desc limit $nod ;";
$run_sql_remote_top= mysqli_query($connection, $sql_remote_top);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/remotecopycreator.css">
    <link rel="stylesheet" href="assets/progress/style.css">

    <title>NEPAL NEWS BANK DASHBOARD</title>

    <style>
    .disabled {
        cursor: no-drop;
    }

    @font-face {
        font-family: preeti;
        src: url(preeti.TTF);
    }

    .form-nepali {
        font-family: preeti;
        font-size: 19px
    }
    </style>

    <style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    </style>
</head>

<body>





    <?php
        include "accesories/navbar/nav.php";
    ?>
    <br>
    <div class="container">
        <div class="card shadow-lg  mb-5 bg-white rounded">
            <div class="card-header bg-info ">
                <h4 style="color:#fff" class="mb-0">Remote Copy Creator</h4>
            </div>
            <div class="card-header  ">
                <form class="form-inline">
                    <div class="form-group">
                        <p class="h4 text-info"><b>Step 1.</b> Select a news date</p>
                        <!-- select newsdate to display news byline in DB  -->
                    </div>
                    <div class="form-group mx-sm-3 ">
                        <label for="inputPassword2" class="sr-only">Date</label>
                        <input type="date" class="form-control form-control-sm" id="date" placeholder="Date"
                            value="<?php echo $selected_date ; ?>">
                    </div>

                </form>
            </div>

            <?php

                if($num_rows_byline != 0)
                    {
            ?>
            <div id="loader-icon" style="display:none;"><img src="LoaderIcon.gif" /></div>

            <div class="card-body">
                <div class="row">

                    <!-- ---------------------------------BYLINE COLUMN---------------------------------- -->
                    <div class="col-3">



                        <!--   id="remote_form"-->
                        <p class="h4 text-info"><b>Step 2.</b> Select a byline</p>
                        <div class="nav card list-group list-group-flush sidebar_byline flex-column nav-pills"
                            id="v-pills-tab" role="tablist" aria-orientation="vertical" style="flex-wrap: nowrap;">
                            <?php
                                                            $counter  = 1 ;
                                                            $symbolNumber  = 1 ;
                                                            if($num_rows_byline == 0)
                                                            {
                                                        ?>
                            <a class="active ml-3 mt-2" href="#">No news available</a>
                            <?php
                                                }
                                                while($row_byline = mysqli_fetch_assoc($run_sql_byline))
                                                {
                                                    $by_line  = $row_byline['byline'];
                                                    $news_id= $row_byline['newsid'];

                                                    if( $is_get_id == 0 )
                                                    {
                                                        if($news_id == $news_id_get)
                                                        {
                                                            $active_id = 'active';
                                                            $content_id = $news_id ;
                                                        }
                                                        else
                                                        {
                                                            $active_id = '';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        if($counter == 1)
                                                        {
                                                            $active_id = 'active';
                                                            $content_id = $news_id ;
                                                        }
                                                        else
                                                        {
                                                            $active_id = '';
                                                        }
                                                    }
                                                    $counter++ ;
                                        ?>





                            <a class="nav-link list-group-item p-2 <?php echo  $active_id ; ?>"
                                href="remotecopycreator.php?news_id=<?php echo $news_id ; ?>&date=<?php echo $selected_date ; ?>"><?php echo $symbolNumber;?>.
                                <span class="<?php echo $font_style ; ?>"><?php echo $by_line ; ?></span></a>


                            <?php  $symbolNumber++;
                                    }
                                ?>

                        </div>
                    </div>
                    <!-- ----------------------------------------------------------------------------- -->

                    <?php
                                            // echo $content_id ;
                                            if(isset($content_id))
                                            {

                                                    $sql_content = "select * from nas where newsid = '$content_id' ";
                                                    $run_sql_content= mysqli_query($connection, $sql_content);
                                                    $num_rows_content = mysqli_num_rows($run_sql_content);

                                                    if($num_rows_content == 1)
                                                    {

                                                        $row_content = mysqli_fetch_assoc($run_sql_content);

                                                        $date_local = $row_content['local_published_date'];
                                                        $byline_local = $row_content['byline'];
                                                        $newsid_local = $row_content['newsid'];
                                                        $category_list = $row_content['category_list'];



                                                        $regularfeed_full = $row_content['regular_feed'];
                                                        // $preview_full = $row_content['previewgif'];
                                                        $thumbnail_full = $row_content['thumbnail'];
                                                        $readyversion_full = $row_content['ready_version'];                            
                                                        $newsbody_full = $row_content['news_file'];
                                                        $additional_full = $row_content['additional_file'];


                                                        $audio_description_full = $row_content['audio_description'];
                                                        $audio_bites_description_full = $row_content['audio_bites_description'];
                                                        $audio_bites_full = $row_content['audio_bites'];



                                                        $regularfield = explode('/' ,$regularfeed_full );
                                                        $regularfield = end($regularfield) ; 

                                                        // $preview = explode('/' ,$preview_full );
                                                        // $preview = end($preview) ; 

                                                        $thumbnail = explode('/' ,$thumbnail_full );
                                                        $thumbnail = end($thumbnail) ; 

                                                        $readyversion = explode('/' ,$readyversion_full );
                                                        $readyversion = end($readyversion) ;

                                                        $newsbody = explode('/' ,$newsbody_full );
                                                        $newsbody = end($newsbody) ; 

                                                        $additional = explode('/' ,$additional_full );
                                                        $additional = end($additional) ; 





                                                        $photos = $row_content['photos'];
                                                        $photos_array = explode(',' , $photos);

                                                        $audio = $row_content['audio_complete_story'];

                                                        $roughcut = $row_content['rough_cut'];
                                                        $video_type = $row_content['video_type'];

                                                      
                                                      

                                                        
                                                    $sql_content_web = "select * from web where newsid = '$content_id' ";
                                                    $run_sql_content_web= mysqli_query($connection, $sql_content_web);
                                                    $num_rows_content_web = mysqli_num_rows($run_sql_content_web);

                                                    $row_content_web = mysqli_fetch_assoc($run_sql_content_web);
                                                    $regularfeed_full_web = $row_content_web['regular_feed'];
                                                    // $preview_full_web = $row_content_web['previewgif'];
                                                    $thumbnail_full_web = $row_content_web['thumbnail'];
                                                    $readyversion_full_web = $row_content_web['ready_version'];                            
                                                    $newsbody_full_web = $row_content_web['news_file'];
                                                    $audio_full_web = $row_content_web['audio_complete_story'];
                                                    $roughcut_full_web = $row_content_web['rough_cut'];

                                                        
                                                    $additional_full_web = $row_content_web['additional_file'];

                                                    $audio_bites_full_web = $row_content_web['audio_bites'];


                                                    $gallery_full_web = $row_content_web['photos'];
                                                    $gallery_full_web = explode("," , $gallery_full_web);

                                                    $pushed_by_web = $row_content_web['pushed_by'];

                                                    $wp_id = $row_content_web['wp_post_id'];

                                                    $vimeo_regularfield = $row_content_web['vimeo_regular_feed'];
                                                    $vimeo_readyversion = $row_content_web['vimeo_ready_version'];
                                                    $vimeo_ready_version = $row_content_web['vimeo_rough_cut'];





                                                        


                                ?>




                    <div class="col-9">



                        <form method="POST" action="accesories/remote_submit.php">



                            <?php

                                                        if(isset($_SESSION['notice_remote']) )
                                                {
                                                    if($_SESSION['notice_remote'] == 'Error')
                                                    {
                                                        $notice  = 'Error pushing news. Please try again';
                                                        $bg_color = 'red';
                                                        $color = '#000';
                                                        $color_down = '#000';
                                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'Success')
                                                    {
                                                        $notice  = 'Succesfully pushed the news to remote database !';
                                                        $bg_color = 'rgb(102, 255, 51,0.5)';
                                                        $color = '#009933';
                                                        $color_down = '#4BB543';                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'Error_push_post')
                                                    {
                                                        $notice  = 'Error Creating post. Please post again';
                                                        $bg_color = 'red';
                                                        $color = '#000';
                                                        $color_down = '#000';
                                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'Success_push_post')
                                                    {
                                                        $notice  = 'Succesfully Created Post !';
                                                        $bg_color = 'rgb(102, 255, 51,0.5)';
                                                        $color = '#009933';
                                                        $color_down = '#4BB543';                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'Error_remote_delete')
                                                    {
                                                        $notice  = 'Error deleting post. Please delete again';
                                                        $bg_color = 'red';
                                                        $color = '#000';
                                                        $color_down = '#000';
                                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'success_remote_delete')
                                                    {
                                                        $notice  = 'Succesfully Deleted Post !';
                                                        $bg_color = 'rgb(102, 255, 51,0.5)';
                                                        $color = '#009933';
                                                        $color_down = '#4BB543';                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'Error_remote_delete')
                                                    {
                                                        $notice  = 'Error deleting remote Files. Please delete again';
                                                        $bg_color = 'red';
                                                        $color = '#000';
                                                        $color_down = '#000';
                                                        

                                                    }

                                                    if($_SESSION['notice_remote'] == 'success_remotefile_delete')
                                                    {
                                                        $notice  = 'Succesfully Deleted Remote Files !';
                                                        $bg_color = 'rgb(102, 255, 51,0.5)';
                                                        $color = '#009933';
                                                        $color_down = '#4BB543';                                        

                                                    }

                                                    


                                            ?>

                            <div class="alert m-3 alert-success fade show" role="alert"
                                style="background-color: <?php echo $bg_color ; ?>; color:<?php echo $color ; ?>">
                                <strong style="color:<?php echo $color_down ; ?>">Notice : </strong>
                                <?php echo $notice; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <?php
                                                    unset($_SESSION['notice_remote']);    
                                                }
                                            ?>


                                <p class="h4 text-info"><b>Step 3.</b> Select following content
                                    <a href="news_edit.php?id=<?php echo $content_id ; ?>" ><button type="button"  class="btn btn-warning mr-5"> EDIT </button> </a>
                                </p>

                            <div class="list-group">
                                <?php
                                $handle_push_nofile = array();
                                $handle_push_nopush = array();
                                                    if($newsbody_full != NULL)
                                            {

                                            
                                                if(file_exists($newsbody_full))
                                                {
                                                    if($newsbody_full_web != NULL)
                                                    {
                                                        $input = 'disabled';
                                                        $value_input = '';
                                                        $message = '<span>'.$newsbody.'</span><span class="float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg></span>
                                                        ';
                                                        $ischecked = '';
                                                        $class_comp = '';
                                                        $sta_a = 1 ;
                                                       
                                                    }
                                                    else
                                                    {

                                                        $input = '';
                                                        $value_input = 'news_file';
                                                        $message = '<span>'.$newsbody.'</span><span class="float-right">
                                                        </span>
                                                        ';

                                                        $rem_to_push = 1 ;
                                                        $ischecked = 'checked';
                                                        $class_comp = 'compulsory';
                                                        array_push($handle_push_nopush , 0);
                                                        

                                                    }


                                                
                                                    
                                                }
                                                else
                                                {
                                                    $input = 'disabled';
                                                    $value_input = '';
                                                    $message = '<span>'.$newsbody.'</span><span class="float-right">
                                                    <span class="text-danger pr-2">News Body File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                    </svg></span>
                                                        ';
                                                        $ischecked = '';
                                                        $class_comp = 'compulsory miss';
                                                    
                                                    array_push($handle_push_nofile , 0);

                                                    
                                                }

                                ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                aria-label="Checkbox for following text input"
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>>
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>
                                </div>

                                <?php
                                                }
                                                else
                                                {
                                                    $sta_a = 1 ;
                                                }
                                    ?>

                                <?php

                                        if($regularfeed_full != NULL)
                                        {
                                            if(file_exists($regularfeed_full))
                                            {
                                                
                                               

                                                if($video_type == 'selfhost')
                                                {
                                                    if($regularfeed_full_web != NULL)
                                                    {
                                                        $input = 'disabled';
                                                        $value_input = '';
                                                        $message = '<span>'.$regularfield.'</span><span class="float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg></span>
                                                        ';
                                                        $ischecked = '';
                                                        $class_comp = '';
                                                        $sta_b = 1 ;

                                                    }
                                                    else
                                                    {
                                                        $input = '';
                                                        $value_input = 'regularFeed';
                                                        $message = '<span>'.$regularfield.'</span><span class="float-right">
                                                        </span>
                                                        ';
    
                                                        $rem_to_push = 1 ;
                                                        $ischecked = 'checked';
                                                        $class_comp = 'compulsory';
                                       
                                                        array_push($handle_push_nopush , 0);
                                                    }
                                                }

                                                if($video_type == 'vimeo')
                                                {
                                                    if($vimeo_regularfield != NULL)
                                                    {
                                                        $input = 'disabled';
                                                        $value_input = '';
                                                        $message = '<span>'.$regularfield.'</span><span class="float-right">Pushed to VIMEO
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg></span>
                                                        ';
                                                        $ischecked = '';
                                                        $class_comp = '';
                                                        $sta_b = 1 ;
                                              
                                                    } 
                                                    else
                                                    {
                                                        $input = '';
                                                        $value_input = 'regularFeed';
                                                        $message = '<span>'.$regularfield.'</span><span class="float-right">
                                                        </span>
                                                        ';
    
                                                        $rem_to_push = 1 ;
                                                        $ischecked = 'checked';
                                                        $class_comp = 'compulsory';
                                                        array_push($handle_push_nopush , 0);
                                                    }
                                                    
                                                }
                                              
                                              


                                            
                                                
                                            }
                                            else
                                            {
                                                $input = 'disabled';
                                                $value_input = '';
                                                $message = '<span>'.$regularfield.'</span><span class="float-right">
                                                <span class="text-danger pr-2">Regular Feed File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                </svg></span>
                                                    ';
                                                    $ischecked = '';
                                                    $class_comp = 'compulsory miss';

                                                    array_push($handle_push_nofile , 0);
                                                   
                                            }

                                    ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                aria-label="Checkbox for following text input"
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>>
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>
                                </div>

                                <?php
                                                }
                                                else
                                                {
                                                    $sta_b = 1 ;
                                                }
                                    ?>



                                <?php

                                        if($readyversion_full != NULL)
                                        {

                                            if(file_exists($readyversion_full))
                                            {

                                                if($video_type == 'selfhost')
                                                {

                                                    if($readyversion_full_web != NULL)
                                                    {
                                                        $input = 'disabled';
                                                        $value_input = '';

                                                        $message = '<span>'.$readyversion.'</span><span class="float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg></span>
                                                        ';
                                                        $ischecked = '';
                                                        $class_comp = '';
                                                        $sta_c = 1 ;
                                                    }
                                                    else
                                                    {
                                                        $input = '';
                                                        $value_input = 'ReadyVersion';
                                                        $message = '<span>'.$readyversion.'</span><span class="float-right">
                                                        </span>
                                                        ';
                                                        $rem_to_push = 1 ;
                                                        $ischecked = 'checked';
                                                        $class_comp = 'compulsory';
                                                        array_push($handle_push_nopush , 0);
                                                    }
                                                }

                                                if($video_type == 'vimeo')
                                                {
                                                    if($vimeo_readyversion != NULL)
                                                    {
                                                        $input = 'disabled';
                                                        $value_input = '';

                                                        $message = '<span>'.$readyversion.'</span><span class="float-right">Pushed to VIMEO
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg></span>
                                                        ';
                                                        $ischecked = '';
                                                        $class_comp = '';
                                                        $sta_c = 1 ;
                                                    }
                                                    else
                                                    {
                                                        $input = '';
                                                        $value_input = 'ReadyVersion';
                                                        $message = '<span>'.$readyversion.'</span><span class="float-right">
                                                        </span>
                                                        ';
                                                        $rem_to_push = 1 ;
                                                        $ischecked = 'checked';
                                                        $class_comp = 'compulsory';
                                                        array_push($handle_push_nopush , 0);
                                                    }

                                                }

                                                $readyversion_full_web = 1 ;
                                                
                                            }
                                            else
                                            {
                                                $regularfeed_full_exist = 0 ;
                                                $input = 'disabled';
                                                $value_input = '';
                                                $ischecked = '';
                                                $class_comp = 'compulsory miss';
                                                $message = '<span>'.$readyversion.'</span><span class="float-right">
                                                <span class="text-danger pr-2">Ready Vesion File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                </svg></span>
                                                    ';

                                                    array_push($handle_push_nofile , 0);
                                            }

                                        
                                ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                aria-label="Checkbox for following text input "
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>>
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>
                                </div>

                                <?php
                                            }
                                            else
                                            {
                                                $sta_c = 1 ;
                                            }                                    
                                    ?>


                                <?php
                                            
                                        if($thumbnail_full != NULL)
                                        {

                                        if(file_exists($thumbnail_full))
                                        {
                                            if($thumbnail_full_web != NULL)
                                            {
                                                $input = 'disabled';
                                                $value_input = '';
                                                $ischecked = '';
                                                $message = '<span>'.$thumbnail.'</span><span class="float-right">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg></span>
                                                    ';

                                                    $class_comp = '';
                                                    $sta_e = 1 ;
                                            }
                                            else
                                            {
                                                $input = '';
                                                $value_input = 'thumbnail';
                                                $ischecked = 'checked';
                                                $message = '<span>'.$thumbnail.'</span><span class="float-right">
                                                    </span>
                                                    ';
                                                    $rem_to_push = 1 ;
                                                    $class_comp = 'compulsory';

                                                    array_push($handle_push_nopush , 0);
                                            }
                                        
                                            
                                        }
                                        else
                                        {
                                        
                                            $input = 'disabled';
                                            $value_input = '';
                                            $ischecked = '';
                                            $message = '<span>'.$thumbnail.'</span><span class="float-right">
                                            <span class="text-danger pr-2">Thumbnail File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                            </svg></span>
                                                ';
                                                $class_comp = 'compulsory miss';

                                                array_push($handle_push_nofile , 0);
                                        }

                                    
                                ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                aria-label="Checkbox for following text input"
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>>
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>
                                </div>

                                <?php
                                            }
                                            else
                                            {
                                                $sta_e = 1 ;
                                            }
                                ?>



                                <?php 
                                            if($audio != NULL )
                                            {
                                                $audio_file_exist = $audio ;
                                            

                                                $audio_full = explode('/' ,$audio );
                                                $audio = end($audio_full) ;

                                                if(file_exists($audio_file_exist))
                                                {
                                                    if($audio_full_web != NULL)
                                                    {
                                                        $input = 'disabled';
                                                        $value_input = '';
                                                        $ischecked = '';
                                                        $message = '<span>'.$audio.'</span><span class="float-right">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                        </svg></span>
                                                        ';
                                                        $class_comp = '';
                                                        $sta_f = 1 ;
                                                    }
                                                    else
                                                    {
                                                    
                                                        $input = '';
                                                        $value_input = 'audio';
                                                        $ischecked = 'checked';
                                                        $message = '<span>'.$audio.'</span><span class="float-right">
                                                    </span>
                                                    ';
                                                    $rem_to_push = 1 ;
                                                    $class_comp = 'compulsory';
                                                    array_push($handle_push_nopush , 0);

                                                    }
                                                
                                                
                                                    
                                                }
                                                else
                                                {
                                                    
                                                    $input = 'disabled';
                                                    $value_input = '';
                                                    $ischecked = '';
                                                    $message = '<span>'.$audio.'</span><span class="float-right">
                                                    <span class="text-danger pr-2">AC Store File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                    </svg></span>
                                                        ';
                                                        $class_comp = 'compulsory miss';
                                                        array_push($handle_push_nofile , 0);
                                                }

                                        ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>
                                                aria-label="Checkbox for following text input"
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>">
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>
                                </div>

                                <?php
                                                    
                                                }
                                                else
                                                {
                                                    $sta_f = 1 ;
                                                }
                                    ?>



                                <?php 
                                                if($roughcut != NULL)
                                            {
                                                
                                                $rouhh_cut_file_exist = $roughcut ;
                                                $roughcut_full = explode('/' ,$roughcut );
                                                $roughcut = end($roughcut_full) ;

                                                
                                              

                                                if(file_exists($rouhh_cut_file_exist))
                                                {
                                                    

                                                    if($video_type == 'selfhost')
                                                    {
                                                        
                                                    
                                                        if($roughcut_full_web != NULL)
                                                        {
                                                            
                                                            $input = 'disabled';
                                                            $value_input = '';
                                                            $ischecked = '';
                                                            $message = '<span>'.$roughcut.'</span><span class="float-right">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                            </svg></span>
                                                            ';
                                                            $class_comp = '';
                                                            $sta_g = 1 ;

                                                           
                                                            
                                                        }
                                                        else
                                                        {
                                                            
                                                            $input = '';
                                                            $value_input = 'RoughCut';
                                                            $message = '<span>'.$roughcut.'</span><span class="float-right">
                                                            </span>
                                                            ';
                                                            $ischecked = 'checked';
                                                            $rem_to_push = 1 ;
                                                            $class_comp = 'compulsory';
                                                            array_push($handle_push_nopush , 0);
                                                        }
                                                    }

                                                    if($video_type == 'vimeo')
                                                    {
                                                        if($vimeo_ready_version != NULL)
                                                        {
                                                           

                                                            $input = 'disabled';
                                                            $value_input = '';
                                                            $ischecked = '';
                                                            $message = '<span>'.$roughcut.'</span><span class="float-right">Pushed to VIMEO
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                            </svg></span>
                                                            ';
                                                            $class_comp = '';
                                                            $sta_g = 1 ;

                                                            
                                                        }
                                                        else
                                                        {
                                                            $input = '';
                                                            $value_input = 'RoughCut';
                                                            $message = '<span>'.$roughcut.'</span><span class="float-right">
                                                            </span>
                                                            ';
                                                            $ischecked = 'checked';
                                                            $rem_to_push = 1 ;
                                                            $class_comp = 'compulsory';
                                                            array_push($handle_push_nopush , 0);
                                                        }
                                                    }
                                                
                                                    
                                                }
                                                else
                                                {
                                                    
                                                    $input = 'disabled';
                                                    $value_input = '';
                                                    $ischecked = '';
                                                    $message = '<span>'.$roughcut.'</span><span class="float-right">
                                                    <span class="text-danger pr-2">Rough Cut File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                    </svg></span>
                                                        ';
                                                        $class_comp = 'compulsory miss';

                                                        array_push($handle_push_nofile , 0);
                                                }
                                        ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>
                                                aria-label="Checkbox for following text input"
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>">
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>

                                </div>
                      
                                <?php
                                                    
                                                }
                                                else
                                                {
                                                    $sta_g = 1 ;
                                                }
                                ?>
                            <!------------------------- Additional File Remote ----------------------------------->

                            <?php 
                                                if($additional_full != NULL)
                                            {
                                                
                                                $additional_file_exist = $additional_full ;
                                                $additional_full = explode('/' ,$additional );
                                                $additional = end($additional_full) ;

                                                
                                              
                                                if(file_exists($additional_file_exist))
                                                {                                             

                                                   
                                                        
                                                    
                                                        if($additional_full_web != NULL)
                                                        {
                                                            
                                                            $input = 'disabled';
                                                            $value_input = '';
                                                            $ischecked = '';
                                                            $message = '<span>'.$additional.'</span><span class="float-right">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                            </svg></span>
                                                            ';
                                                            $class_comp = '';
                                                            $sta_z = 1 ;

                                                           
                                                            
                                                        }
                                                        else
                                                        {
                                                            
                                                            $input = '';
                                                            $value_input = 'additional_files';
                                                            $message = '<span>'.$additional.'</span><span class="float-right">
                                                            </span>
                                                            ';
                                                            $ischecked = 'checked';
                                                            $rem_to_push = 1 ;
                                                            $class_comp = 'compulsory';
                                                            array_push($handle_push_nopush , 0);
                                                        }
                                                  

                                                
                                                
                                                    
                                                }
                                                else
                                                {
                                                    
                                                    $input = 'disabled';
                                                    $value_input = '';
                                                    $ischecked = '';
                                                    $message = '<span>'.$additional.'</span><span class="float-right">
                                                    <span class="text-danger pr-2">Additional File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                    </svg></span>
                                                        ';
                                                        $class_comp = 'compulsory miss';
                                                        array_push($handle_push_nofile , 0);
                                                }
                                        ?>

                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="checkbox" name="file_name[]"
                                                value="<?php echo $value_input ; ?>" <?php echo $input ; ?>
                                                aria-label="Checkbox for following text input"
                                                <?php echo $ischecked ; ?>
                                                class="files big-checkbox <?php echo $class_comp ; ?>">
                                        </div>
                                    </div>
                                    <div class="form-control"><?php echo $message ; ?></div>

                                </div>
                      
                                <?php
                                                    
                                                }
                                                else
                                                {
                                                    $sta_z = 1 ;
                                                }
                                ?>


                            <!------------------------- Additional File Remote End ------------------------------->

                              <!------------------------- Audio Bites File Remote ----------------------------------->

                              <?php 
                              $is_audio_bites_pushed_array = array();
                                            if($audio_bites_full != NULL)
                                            {
                                                $audio_bites_full_exp = explode("," ,$audio_bites_full );
                                                $audio_bites_full_web_exp = explode("," ,$audio_bites_full_web );
                                                echo "<br>";
                                                
                                                foreach($audio_bites_full_exp as $ab)
                                                {
                                                
                                                    $ab_exist = $ab ;
                                                    $ab_full = explode('/' ,$ab );
                                                    $ab_msg = end($ab_full) ;

                                                
                                                   
                                                    if(file_exists($ab))
                                                    {
                                                        $rem_check = explode("/" , $ab);    
                                                        // print_r($rem_check)   ;
                                                        array_shift($rem_check);
                                                        // print_r($rem_check)   ;
                                                        $rem_check = implode("/" , $rem_check);
                                                        // echo "<br>-----------Check state: $rem_check------------<br>"  ;                   
   
                                                            if(in_array($rem_check , $audio_bites_full_web_exp ))
                                                            {
                                                                
                                                                $input = 'disabled';
                                                                $value_input = '';
                                                                $ischecked = '';
                                                                $message = '<span>'.$ab_msg.'</span><span class="float-right">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                                </svg></span>
                                                                ';
                                                                $class_comp = '';
                                                                array_push($is_audio_bites_pushed_array , 1);

                                                            
                                                                
                                                            }
                                                            else
                                                            {
                                                                
                                                                $input = '';
                                                                $value_input = $ab;
                                                                $message = '<span>'.$ab_msg.'</span><span class="float-right">
                                                                </span>
                                                                ';
                                                                $ischecked = 'checked';
                                                                $rem_to_push = 1 ;
                                                                $class_comp = 'compulsory';
                                                                array_push($is_audio_bites_pushed_array , 0);
                                                                array_push($handle_push_nopush , 0);
                                                            }
                                                    

                                                    
                                                    
                                                        
                                                    }

                                                    else
                                                    {
                                                        
                                                        $input = 'disabled';
                                                        $value_input = '';
                                                        $ischecked = '';
                                                        $message = '<span>'.$ab_msg.'</span><span class="float-right">
                                                        <span class="text-danger pr-2">Audio Bites File Doesnt Exist Locally</span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                                        </svg></span>
                                                            ';
                                                            $class_comp = 'compulsory miss';
                                                            array_push($is_audio_bites_pushed_array , 0);
                                                            array_push($handle_push_nofile , 0);
                                                    }
                                        ?>

                                                    <div class="input-group ">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <input type="checkbox" name="audio_bites[]"
                                                                    value="<?php echo $value_input ; ?>" <?php echo $input ; ?>
                                                                    aria-label="Checkbox for following text input"
                                                                    <?php echo $ischecked ; ?>
                                                                    class="files big-checkbox <?php echo $class_comp ; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-control"><?php echo $message ; ?></div>

                                                    </div>

                      
                                <?php
                                                }                                                    
                                            }
                                            else
                                            {
                                                array_push($is_audio_bites_pushed_array , 1);
                                            }
                                ?>


                            <!------------------------- Audio bites File Remote End ------------------------------->


                                <p class="h4 text-info mt-3 pl-0"><b>Step 4.</b> Select following images</p>
                                <ul class=" ">

                                    <!-- ---------IMAGE SECTION ------------ -->

                                    <?php
                                                $gal_counter = 1 ;
                                                $index = 0 ;
                                                $sta_array = array();


                                                foreach($photos_array as $ph_arr)
                                                {
                                                    if(empty($ph_arr)) break ;
                                                    
                                                   
                                                    if(file_exists($ph_arr))
                                                    {
                                                        $exp_pharr_arr = explode("/" , $ph_arr);
                                                        $exp_pharr = end($exp_pharr_arr);

                                                        array_shift($exp_pharr_arr);
                                                        $implde_remote_format = implode("/" , $exp_pharr_arr);
                                                        
                                                        // $web_gall = explode("/" , $gallery_full_web[$index]);                                                        
                                                        // $web_gall = end($web_gall);

                                                        // $index++ ; 

                                                   
                                                        // if($exp_pharr ==  $web_gall)

                                                        if(in_array($implde_remote_format , $gallery_full_web))
                                                        {
                                                            $input = 'disabled' ;
                                                            $selected = '';
                                                            array_push($sta_array , 1);
                                                            $class_gall = "";
                                                        }
                                                        else
                                                        {
                                                            $input = '' ;
                                                            $selected = 'checked';
                                                            $rem_to_push = 1 ;
                                                            array_push($sta_array , 0);
                                                            $class_gall = "compulsory";
                                                            array_push($handle_push_nopush , 0);

                                                        }
                                            ?>
                                    <li>
                                        <input type="checkbox" name="gall_img[]" value="<?php echo $ph_arr ; ?>"
                                            id="cb<?php echo $gal_counter ; ?>"
                                            class="files <?php echo $class_gall ; ?>" <?php echo $selected ; ?>
                                            <?php echo $input ; ?> />
                                        <label for="cb<?php echo $gal_counter ; ?>"><img
                                                src="<?php echo $ph_arr ; ?>" /></label>
                                    </li>
                                    <?php
                                                    }
                                                    else {
                                                        array_push($handle_push_nofile , 0);
                                                    }
                                                
                                                    $gal_counter++;
                                                }
                                            ?>


                                </ul>
                                <p class="h4 text-info "><b>Step 5.</b> Pushed By</p>
                                <select class="form-control form-control-sm" name="pushed_by">
                                    <option selected value="<?php echo $_SESSION['fuser'] ;?>">
                                        <?php echo $_SESSION['fuser'] ;?></option>
                                    <!-- <option <?php  //if($pushed_by_web == 'publisher 2') echo 'selected';   ?>
                                        value="publisher 2">Publisher 2</option> -->

                                </select>
                            </div>

                            <p class="h4 text-info mt-3 "><b>Step 6.</b> </p>

                            <input type="hidden" name="news_id" value="<?php echo $content_id ; ?>">

                            <?php
                                        

                                        if(in_array(0 , $sta_array))
                                        {
                                            $sta_h = 0 ;
                                        }
                                        else
                                        {
                                            $sta_h = 1 ;
                                        }

                                        if(in_array(0 , $is_audio_bites_pushed_array))
                                        {
                                            $sta_y = 0 ;
                                        }
                                        else
                                        {
                                            $sta_y = 1 ;
                                        }

                                        
                                        

                                        if(isset($sta_g))
                                        {
                                            $sta_g = $sta_g ;
                                        }
                                        else
                                        {
                                            $sta_g = 1 ;
                                        }

                                        if(isset($sta_f))
                                        {
                                            $sta_f = $sta_f ;
                                        }
                                        else
                                        {
                                            $sta_f = 1 ;
                                        }

                                        // echo "Hello: $sta_a  && $sta_b  && $sta_c  && $sta_e  && $sta_h && $sta_g && $sta_f && $sta_z && $sta_y";

                                        // if($sta_a  && $sta_b  && $sta_c  && $sta_e  && $sta_h && $sta_g && $sta_f && $sta_z && $sta_y)
                                        // {

                                        //     $dis_pushhh = 'disabled';
                                        //     $curs_style= 'not-allowed';

                                        //     if($wp_id == NULL)
                                        //     {
                                        //         $dis_del_remote_file = '';
                                        //         $curs_style_del_remote_files= '';
                                        //     }
                                        //     else
                                        //     {
                                        //         $dis_del_remote_file = 'disabled';
                                        //         $curs_style_del_remote_files= 'not-allowed;display:none';
                                        //     }

                                            

                                        // }
                                        // else
                                        // {
                                        //     $dis_pushhh = '';
                                        //     $curs_style= '';

                                        //     $dis_del_remote_file = 'disabled';
                                        //     $curs_style_del_remote_files= 'not-allowed;display:none';

                                        // }
                                       
                                        // print_r($handle_push_nofile );
                                        // print_r($handle_push_nopush );
                                        // echo empty($handle_push_nofile ) ."-------".empty($handle_push_nopush);
                                        if(empty($handle_push_nofile ) && empty($handle_push_nopush))
                                        {   
                                            
                                                $dis_pushhh = 'disabled';
                                                $curs_style= 'not-allowed';

                                                if($wp_id == NULL)
                                                {
                                                    $dis_del_remote_file = '';
                                                    $curs_style_del_remote_files= '';
                                                }
                                                else
                                                {
                                                    $dis_del_remote_file = 'disabled';
                                                    $curs_style_del_remote_files= 'not-allowed;display:none';
                                                }

                                                $allow_create_post = 1;
    

                                        }
                                        else
                                        {
                                            $dis_pushhh = '';
                                            $curs_style= '';

                                            $dis_del_remote_file = 'disabled';
                                            $curs_style_del_remote_files= 'not-allowed;display:none';

                                            $allow_create_post = 0;

                                        }

                                        

                                 
                                                    

                                        if($regularfeed_full == null &&  $thumbnail_full == null && $readyversion_full == null && 
                                        $newsbody_full == null && $audio == null && $roughcut == null && 
                                        $photos == null && $photos == ''   )
                                        {
                                            $dis_pushhh = '';
                                        }
                                        $status_dis_pushhh = $dis_pushhh ;

                                        

                                            // if($sta_g)
                                            // {
                                            //     $dis_pushhhh = 'disabled';
                                            //     $curs_stylee= 'not-allowed';

                                            // }

                                            // else
                                            // {
                                            //     $dis_pushhhh = '';
                                            //     $curs_stylee= '';

                                            // }

                                ?>

                            <button type="submit" class="btn btn-info sub_push"
                                style="cursor: <?php echo $curs_style; ?>" <?php echo $dis_pushhh ; ?>
                                <?php //echo $dis_pushhhh ; ?> value="submit" name="submit">
                                Push data to remote
                            </button>

                            <span id="error_push"></span>
                            <span id="error_push_miss"></span>

                            <!-- <button type="submit" class="btn btn-danger"
                                    style="cursor: <?php //echo $curs_style_del_remote_files; ;?>"
                                    <?php //echo $dis_del_remote_file ; ?> value="submit" name="del_remote_files">
                                    Delete Remote Data
                                </button> -->




                            <?php
                                            }
                                            }
                                    ?>






                        </form>



                        <p class="h4 text-info mt-3 "><b>Step 7.</b> </p>
                        <?php
                                        

                                            if($wp_id != null)
                                            {
                                                $dis_pushhh = 'disabled';
                                                $curs_style_create= 'not-allowed';
                                            }
                                            else
                                            {
                                                if( $allow_create_post)
                                                {
                                                    $dis_pushhh = '';
                                                }
                                                else
                                                {
                                                    $dis_pushhh = 'disabled';
                                                    $curs_style_create= 'not-allowed';
                                                }
                                            }
                                        ?>

                        <div style="display:flex">
                            <span class="mr-2">
                                <form method="POST" action="accesories/wp_post_create.php">
                                    <input type="hidden" name="news_id" value="<?php echo $content_id ; ?>">
                                    <button class="btn btn-info" style="cursor:<?php echo  $curs_style_create ; ?>; "
                                        <?php echo $dis_pushhh ; ?> type="submit" name="submit_push">Create Post
                                    </button>
                                </form>
                            </span>
                            <?php

                    if($wp_id != null )
                    {

                ?>
                            <!-- <span>
                                <form method="POST" action='accesories/wp_post_delete.php'>
                                    <input type="hidden" name="wp_id" value="<?php// echo $wp_id ; ?>">
                                    <input type="hidden" name="news_id" value="<?php// echo $news_id ; ?>">
                                    <input type="submit" value="Delete Web Post" class="btn btn-danger"
                                        name="del_remote">
                                </form>
                            </span> -->
                        </div>
                        <?php
                }
            ?>




                    </div>



                    <?php
// if($status_dis_pushhh != 'disabled')

if($num_rows_content_web > 0)
{
    $delete_allowed = 0 ;
}
else {
    $delete_allowed = 1 ;
}



// if(!in_array(0 , $handle_push_nopush) && $wp_id == null)
if($delete_allowed )
{ 
    
?>
                    <hr />
                    <form method="POST" action='accesories/local_post_delete.php'>
                        <input type="hidden" name="news_id" value="<?php echo $newsid_local ; ?>">
                        <input type="hidden" name="byline" value="<?php echo $byline_local ; ?>">
                        <input type="hidden" name="date" value="<?php echo $date_local ; ?>">
                        <input type="hidden" name="type" value="<?php echo $category_list ?>">

                        <input type="submit" value="Delete Collected News" class="btn btn-danger" name="del_nas">
                    </form>
                    <hr />
                    <?php

}
?>

                </div>
            </div>







            <div class="card-footer text-muted bg-light  ">


                <div class="text-right ">

                </div>

                <?php
                    if($rem_to_push )
                    {
                        $notice  = 'All Files are not pushed.';
                        $bg_color = 'red';
                                            $color = '#000';
                                            $color_down = '#000';
                    }
                    else
                    {
                        $notice  = 'All Files are pushed.';
                        $bg_color = 'rgb(102, 255, 51,0.5)';
                        $color = '#009933';
                        $color_down = '#4BB543';
                    }
                   
                     ?>

                <div class="alert m-3 alert-success fade show" role="alert"
                    style="background-color: <?php echo $bg_color ; ?>; color:<?php echo $color ; ?>">
                    <strong style="color:<?php echo $color_down ; ?>">Notice : </strong>
                    <?php echo $notice; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


            </div>









            <div class="form-group" id="process" style="display:none;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0"
                        aria-valuemax="100" style="">
                    </div>
                </div>
                <?php
                    }
                    else
                    {

                        $min_query = "SELECT local_published_date 
                        FROM nas 
                        WHERE local_published_date < '$selected_date'
                        ORDER BY local_published_date  desc
                        LIMIT 1 ;";
                        $run_min_query= mysqli_query($connection, $min_query);
                        $num_rows_min_query = mysqli_num_rows($run_min_query);
                        

                        if($num_rows_min_query > 0)
                        {
                            $row_min_query = mysqli_fetch_assoc($run_min_query);
                            $min_date = $row_min_query['local_published_date'];
                        }


                        $max_query = " SELECT local_published_date 
                        FROM nas 
                        WHERE local_published_date > '$selected_date'
                        ORDER BY local_published_date  
                        LIMIT 1 ; " ;

                        $run_max_query= mysqli_query($connection, $max_query);
                        $num_rows_max_query = mysqli_num_rows($run_max_query);


                        if($num_rows_max_query > 0)
                        {
                            $row_max_query = mysqli_fetch_assoc($run_max_query);
                            $max_date = $row_max_query['local_published_date'];
                        }
                        
                    
                ?> <div class="card-body">
                    <div class="card-body text-center " style="height:400px">
                        <h3 style="padding-top:100px">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 pb-2" width="45" height="45"
                                fill="currentColor" class="bi bi-calendar-x" viewBox="0 0 16 16">
                                <path
                                    d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                            </svg>
                            No news available on selected date. Please select date between :
                        </h3>
                        <?php
                    if(isset($min_date))
                    {
                ?>
                        <b>Earliest News Date:</b> <?php echo $min_date ; ?>
                        <?php
                    }
                    else
                    {                        
                ?>
                        <b>Earliest News Date:</b> Earliest news date not available.
                        <?php
                    }
                ?>
                        <br>
                        <?php
                    if(isset($max_date))
                    {
                ?>
                        <b>Latest News Date:</b> <?php echo $max_date ; ?>
                        <?php
                    }
                    else
                    {
                ?>
                        <b>Latest News Date:</b> Latest news date not available.
                        <?php
                    }
                ?>

                    </div>
                    <?php
                    }
                ?>

                </div>
            </div>



            <div class="card shadow-lg  mb-5 bg-white rounded">
                <div class="card-header bg-info ">
                    <div class="row">
                        <div class="col-lg-9">
                            <h4>Latest News List</h4>
                            <!--  (Datas : <?php // if($nod > 0) echo $nod ; ?> ) -->
                        </div>
                        <div class="col-lg-3 ">
                            <form method="GET" class="btn-group">
                                <input type="number" class="form-control " name="nod" id="colFormLabelSm"
                                    value="<?php if($nod > 0) echo $nod ; ?>" placeholder="Number of Data">
                                <button type="button btn-sm" class="btn btn-outline-light">Submit</button>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                        $counter_top = 1;
                        while($row_remote_top = mysqli_fetch_assoc($run_sql_remote_top))
                        {
                            //IS NOT NULL
                            $top_byline = $row_remote_top['byline'];
                            $top_newsid = $row_remote_top['newsid'];
                            $top_wp_post_id = $row_remote_top['wp_post_id'];
                            $top_wp_post_type = $row_remote_top['wp_post_type'];

                            if($top_wp_post_type == 'publish') $wp_post_type_status = 'checked';
                            else $wp_post_type_status = '';

                        
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center row">
                            <div class="col-lg-7">
                                <p class="m-0"><strong><?php echo $counter_top ; ?>.
                                    </strong><?php echo $top_byline ; ?>
                                </p>
                            </div>

                            <div class="col-lg-3">
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#exampleModal_top<?php echo $counter_top ; ?>">
                                    Delete post + remote data
                                </button>
                            </div>
                            <div class="col-lg-2">


                                <label class="switch">
                                    <input type="checkbox" class="post_type" value='<?php echo $top_wp_post_id; ?>'
                                        <?php echo $wp_post_type_status ; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </li>


                        <div class="modal fade" id="exampleModal_top<?php echo $counter_top ; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Delete Archive Video</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete Remote News "<strong>
                                            <?php echo $top_byline ; ?>" ?
                                        </strong>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <form action="accesories/remote_wp_delete.php" method="POST">

                                            <input type="hidden" class="btn btn-danger" name="wp_id"
                                                value="<?php echo $top_wp_post_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="news_id"
                                                value="<?php echo $top_newsid ; ?>">





                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php
                            





                            $counter_top++;
                        }
                    ?>
                    </ul>
                </div>
            </div>



        </div>
    </div>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script> -->


    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> -->


    <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
    <script src="assets/progress/jquerymin.js"></script>
    <script src="assets/progress/ajax.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous">
    </script>


    <script>
    $(document).on('click', '.files', function() {

        if ($(this).is(":checked") == false) {
            $(this).removeAttr('checked');
        } else {
            $(this).attr('checked', 'checked');
        }

    });

    $("#date").change(function() {
        var selc_date = $('#date').val();
        location.href = "remotecopycreator.php?date=" + selc_date;



    });


    $(document).on('click', '.post_type', function() {

        var id = $(this).val();
        var type = null;

        if ($(this).is(":checked") == false) {
            type = "draft";
        } else {
            $(this).attr('checked', 'checked');
            type = "publish";
        }



        $.ajax({
            url: "accesories/wp_post_type_update.php?wp_post_id=" + id + "&wp_post_status=" + type,
            method: "POST",
            data: {

            },
            dataType: "text",
            success: function(data) {



            }
        });

    });






    var checkBoxes = $('.compulsory'),
        submitButton = $('.sub_push');

    if ($('.miss').length > 0) {
        $("#error_push_miss").html("Error: Please copy the missing file and refresh the tool.").css("color", "red");
        submitButton.addClass('disabled');

    }







    checkBoxes.change(function() {
        submitButton.attr("disabled", checkBoxes.is(":not(:checked)"));
        if (checkBoxes.is(":not(:checked)")) {
            submitButton.addClass('disabled');
            $("#error_push").html("Error: Please select all files.<br>").css("color", "red");
        } else {
            submitButton.removeClass('disabled');
            $("#error_push").html("");
        }
    });
    </script>





</body>

</html>