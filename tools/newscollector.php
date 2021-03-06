<?php
error_reporting(0);
include "accesories/session_handler/session_views.php";
include "accesories/environment/wp_api_env.php";
include "global/timezone.php";

include "global/district_data.php";
include "global/cameraman_data.php";
include "global/createdby_data.php";
include "global/reporter_data.php";


$selected_date = date("Y-m-d");




 // fetch tags id


 $url = "$domain_url/wp-json/wp/v2/video_tag";
 $data = '';
 $curl = curl_init();
 curl_setopt_array($curl, array(                    
 CURLOPT_URL => $url,
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_TIMEOUT => 30,
 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 CURLOPT_CUSTOMREQUEST => 'GET',
 CURLOPT_POSTFIELDS => $data ,
     CURLOPT_HTTPHEADER => array(
         "cache-control: no-cache",
         "content-type: application/json",
         'Authorization: Bearer '.$token_bearer
     ),
 ));
 
 $response = curl_exec($curl);          
 $result = json_decode($response);
 $result = json_decode(json_encode($result) , true);     
 $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 $err = curl_error($curl);                    
 curl_close($curl);
 
 $i= 0 ;
 foreach($result as $res)
 {
     
     $tags[$i]['id'] = $res['id'];
     $tags[$i]['name'] = $res['name'];
     $i++;
 
 
 }


 

 $url = "$domain_url/wp-json/wp/v2/video_category/?per_page=99";
 $data = '';
 $curl = curl_init();
 curl_setopt_array($curl, array(                    
 CURLOPT_URL => $url,
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_TIMEOUT => 30,
 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 CURLOPT_CUSTOMREQUEST => 'GET',
 CURLOPT_POSTFIELDS => $data ,
     CURLOPT_HTTPHEADER => array(
         "cache-control: no-cache",
         "content-type: application/json",
         'Authorization: Bearer '.$token_bearer
     ),
 ));
 
 $response = curl_exec($curl);          
 $result = json_decode($response);
 $result = json_decode(json_encode($result) , true);     
 $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 $err = curl_error($curl);                    
 curl_close($curl);
 
 $i= 0 ;
 foreach($result as $res)
 {
     
     $category[$i]['id'] = $res['id'];
     $category[$i]['name'] = $res['name'];
     $i++;
 
 
 }
 




 $url = "$domain_url/wp-json/wp/v2/haru_series";
$data = '';
$curl = curl_init();
curl_setopt_array($curl, array(                    
CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'GET',
CURLOPT_POSTFIELDS => $data ,
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
        'Authorization: Bearer '.$token_bearer
    ),
));

$response = curl_exec($curl);          
$result = json_decode($response);
$result = json_decode(json_encode($result) , true);     
$respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);                    
curl_close($curl);

$i= 0 ;
foreach($result as $res)
{
    
    $series[$i]['id'] = $res['id'];
    $series[$i]['name'] = $res['title']['rendered'];
    $i++;


}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <title>News Collector</title>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous">
    </script>
    <!---- filter multi-select-- -->
    <link rel="stylesheet" href="assets/css/filter-multi-select.css" />
    <script src="assets/js/filter-multi-select-bundle.min.js"></script>
    <script src="assets/js/multi-select-tags.js"></script>
    <link href="http://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/css/nepali.datepicker.v3.7.min.css" rel="stylesheet" type="text/css"/>

</head>
<style>
nav {
    background-color: #1a051d !important
}

h4 {
    color: aliceblue;
    margin-bottom: 0;
    font-size: 1.2rem;
}

b {
    color: #000
}

.help_icon:hover {
    color: #1a051d;
    cursor: pointer
}

.imageSelected {
    height: 150px;
    width: 150px;
}

@font-face {
    font-family: preeti;
    src: url(preeti.TTF);
}

.form-nepali {
    font-family: preeti;
}

.preview-images-zone {
    width: 100%;
    border: 1px solid #ddd;
    min-height: 180px;
    /* display: flex; */
    padding: 5px 5px 0px 5px;
    position: relative;
    overflow: auto;
}

.preview-images-zone>.preview-image:first-child {
    height: 185px;
    width: 185px;
    position: relative;
    margin-right: 5px;
}

.preview-images-zone>.preview-image {
    height: 90px;
    width: 90px;
    position: relative;
    margin-right: 5px;
    float: left;
    margin-bottom: 5px;
}

.preview-images-zone>.preview-image>.image-zone {
    width: 100%;
    height: 100%;
}

.preview-images-zone>.preview-image>.image-zone>img {
    width: 100%;
    height: 100%;
}

.preview-images-zone>.preview-image>.tools-edit-image {
    position: absolute;
    z-index: 100;
    color: #fff;
    bottom: 0;
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
    display: none;
}

.preview-images-zone>.preview-image>.image-cancel {
    font-size: 18px;
    position: absolute;
    top: 0;
    right: 0;
    font-weight: bold;
    margin-right: 10px;
    cursor: pointer;
    display: none;
    z-index: 100;
}

.preview-image:hover>.image-zone {
    cursor: move;
    opacity: .5;
}

.preview-image:hover>.tools-edit-image,
.preview-image:hover>.image-cancel {
    display: block;
}

.ui-sortable-helper {
    width: 90px !important;
    height: 90px !important;
}

strong {
    color: #535a5c;
    font-weight: 700
}
/* 
.collector {
    filter: blur(2px);
} */

.loader{
    position: fixed;
  top: 50%;
  left: 50%;
  /* bring your own prefixes */
  transform: translate(-50%, -50%);
  z-index: 9;
}

</style>

<body>

    <?php
        include "accesories/navbar/nav.php";
    ?>
    <br>
    <div class="container">



        <div class="d-flex justify-content-center loader" id="preloader_boot" style="display:none !important;" >
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>



        <div class="collector">

            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-lg  mb-5 bg-white rounded">
                        <div class="card-header bg-info ">
                            <h4>News Collector</h4>
                        </div>
                        <div class="card-body">

                            <?php

                                if(isset($_SESSION['notice']) )
                                {
                                    if($_SESSION['notice'] == 'Error')
                                    {
                                        $notice  = 'Error while collecting news!';
                                        $bg_color = 'red';
                                        $color = '#000';
                                        $color_down = '#000';
                                        

                                    }

                                    if($_SESSION['notice'] == 'Success')
                                    {
                                        $notice  = 'News collection successfull.';
                                        $bg_color = 'rgb(102, 255, 51,0.5)';
                                        $color = '#009933';
                                        $color_down = '#4BB543';
                                        $notice2 = "Database logging successfull.";
                                        $sta = "succ";
                                    

                                    }


                                ?>

                            <div class="alert  alert-success fade show" role="alert"
                                style="background-color: <?php echo $bg_color ; ?>; color:<?php echo $color ; ?>">
                                <strong style="color:<?php echo $color_down ; ?>">Notice : </strong> <?php echo $notice; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php 
                                    if(isset($sta))
                                    {
                                ?>
                            <div class="alert  alert-success fade show" role="alert"
                                style="background-color: <?php echo $bg_color ; ?>; color:<?php echo $color ; ?>">
                                <strong style="color:<?php echo $color_down ; ?>">Notice : </strong> <?php echo $notice2; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php
                                    }
                            



                                        unset($_SESSION['notice']);
                                    
                                }

                            ?>


                            <form method="POST" enctype="multipart/form-data" id="collector_form" action="accesories/local_submit.php">
                                <!-- <form method="POST" enctype="multipart/form-data" action="accesories/test.php"> -->
                                <!-- The headline for news. -->

                                <STRONG>NEWS </strong>
                                <HR style="    border-top: 1px solid rgba(0,0,0)">

                                <div class="form-group">
                                    <label class="col-lg-12 p-0 h5 text-info">Step 1. Select News Date*
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>
                                    </label>
                                    <input class=" form-control col-lg-2" id="nepali-datepicker" type="text" name="newsdate"
                                         xxx>
                                    <!-- make date today's date -->


                                </div>
                                <div class="form-group">
                                    <label class=" p-0 col-lg-12 h5 text-info">Step 2. Enter News Byline*
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>

                                    </label>
                                    <div class="form-inline">
                                        <input type="text" class="form-control col-lg-12 pl-0"
                                            placeholder="Enter / Paste news byline" name="byLine" id="input_box" xxx
                                            onkeydown="limit(this);" onkeyup="limit(this);charcountupdate(this.value)">
                                        <!-- <input type="text" class="form-control col-lg-10" placeholder="Enter news byline" name="byLine" id="input_box" xxx  onkeydown="limit(this);" onkeyup="limit(this);charcountupdate(this.value)"> -->
                                        <!-- <div id="formByline" class="col-lg-10 pl-0">
                                            </div> -->
                                        <!-- <select class="custom-select my-1 col-lg-2" name='lang_selec'
                                                onchange="changeOrg()">
                                                <option value="nepali">Nepali Language</option>
                                                <option value="english">English Language</option>
                                            </select> -->
                                    </div>

                                    <small id="emailHelp" class="form-text text-muted">
                                        <span id=charcount></span>

                                    </small>



                                </div>






                                <!-- The body of news. Should extract text from file like txt and docs and pass to sql -->
                                <div class="form-group">
                                    <label class="col-lg-12 p-0 h5 text-info">Step 3. Select News Body File
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>
                                    </label>
                                    <input type="file" id="myfile" name="descFile" xxx>
                                    <small id="emailHelp" class="form-text text-muted">accepted formats : docx </small>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-12 p-0 h5 text-info">Step 4. Select Extra Files
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>
                                    </label>
                                    <div id="extra_selectors">
                                        <div>
                                            <input type="file" id="" class="extra_folder" name="extra_files[]" multiple>
                                        </div>
                                    </div>
                                    <textarea type="text" class="form-control col-lg-12 pl-0 mt-2" placeholder="Description"
                                        name="extra_files_description" id="input_box" rows="5"></textarea>


                                </div>
                                <!-- <button id="add_extra_folder" type="button">Add New</button> -->


                                <!-- <STRONG> SELECT VIDEOS </strong> -->
                                <label class="col-lg-12 p-0 h5 text-info">Step 5. Select Videos</label>
                                <HR style="    border-top: 1px solid rgba(0,0,0)">
                                <div class="row">
                                    <!--Select video and pass to sql-->






                                    <!-- video long card -->
                                    <div class="col-sm-4">
                                        <div class="card ">
                                            <img src="./assets/images/placeholder.jpg" class="card-img-top "
                                                id="regularfeedplaceholder" alt="...">
                                            <div id="regularfeedID"></div>
                                            <div class="card-body">
                                                <span><strong>5.1 Regular Feed</strong></span>
                                                <div class="float-right">
                                                    <svg data-toggle="popover" title="News Title"
                                                        data-content="Some content inside the popover"
                                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                        data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                        <path
                                                            d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                    </svg>
                                                </div>

                                                <input type="file" id="regularfeed" name="regularFeeddFile"
                                                    onchange="return regularFeedValidation()" xxx>
                                                <small id="emailHelp" class="form-text text-muted">5min to 7min
                                                    video</small>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- --------------- -->
                                    <!-- video lazy card -->
                                    <div class="col-sm-4">
                                        <div class="card ">
                                            <img src="./assets/images/placeholder.jpg" class="card-img-top "
                                                id="readyVersionplaceholder" alt="...">
                                            <div id="readyversionID"></div>
                                            <div class="card-body">
                                                <span><strong>5.2 Ready Version</strong></span>
                                                <div class="float-right">
                                                    <svg data-toggle="popover" title="News Title"
                                                        data-content="Some content inside the popover"
                                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                        data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                        <path
                                                            d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                    </svg>
                                                </div>
                                                <input type="file" id="readyversion" name="readyVersionFile"
                                                    onchange="return readyVersionValidation()">
                                                <small id="emailHelp" class="form-text text-muted">Video should be less than
                                                    3 minutes</small>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- --------------- -->
                                    <!-- video extra card -->
                                    <div class="col-sm-4">
                                        <div class="card ">
                                            <img src="./assets/images/placeholder.jpg" class="card-img-top "
                                                id="roughcutplaceholder" alt="...">
                                            <div id="roughcutID"></div>
                                            <div class="card-body">
                                                <span><strong>5.3 Rough Cut</strong></span>
                                                <div class="float-right">
                                                    <svg data-toggle="popover" title="News Title"
                                                        data-content="Some content inside the popover"
                                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                        data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                        <path
                                                            d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                    </svg>
                                                </div>
                                                <input type="file" id="roughcut" name="roughCutFile"
                                                    onchange="return roughcutValidation()">
                                                <small id="emailHelp" class="form-text text-muted">Video should be less than
                                                    3 minutes</small>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- --------------- -->

                                <!-- The tags for news. example: sports,football,messi,goal. Should be in CSV(comma separated format) -->
                                <div class="form-group mt-3">
                                    <label class="col-lg-12 p-0  h5 text-info">Step 6. Select News Category*</label>
                                    <!-- <input type="text" class="form-control" placeholder="Enter news byline" name="newsTag" xxx> -->
                                    <select multiple name="newsCategories[]" id="categories" required>



                                        <?php 
                                                if(isset($category))
                                                {
                                                    foreach($category as $category)
                                                    {
                                                    

                                                        if( strpos( strtolower($category['name']), "archive" ) !== false) {
                                                        continue ;
                                                        }
                                            ?>
                                        <option class="cat_opt" value="<?php echo $category['id'] ; ?>">
                                            <?php echo $category['name'] ; ?></option>
                                        <?php
                                                    }
                                                }
                                            ?>

                                    </select>
                                </div>

                                <!-- The tags for news. example: sports,football,messi,goal. Should be in CSV(comma separated format) -->
                                <div class="form-group">
                                    <label class="col-lg-12 p-0  h5 text-info">Step 7. Select News Tags</label>
                                    <!-- <input type="text" class="form-control" placeholder="Enter news byline" name="newsTag" xxx> -->
                                    <select multiple name="newsTag[]" id="tags">

                                        <?php 
                                                if(isset($tags))
                                                {
                                                    foreach($tags as $tag)
                                                    {
                                            ?>
                                        <option value="<?php echo $tag['id'] ; ?>"><?php echo $tag['name'] ; ?></option>
                                        <?php
                                                    }
                                                }
                                            ?>
                                        <!-- <option value="141">Business</option>
                                            <option value="142">Entertainment</option>
                                            <option value="134">Sports</option>
                                            <option value="135">International</option>
                                            <option value="136">Glamour</option>
                                        -->


                                    </select>
                                </div>

                                <HR style="    border-top: 1px solid rgba(0,0,0)">

                                <div class="form-group ">
                                    <label class="col-lg-12 p-0 h5 text-info">Step 8. Select Audio
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>

                                    </label><br>
                                    <div class="row">

                                        <div class="col-lg-6">
                                            <strong>8.1 Select Audio Complete Story</strong>
                                            <div class="mt-2">
                                                <input type="file" id="img" name="audio_complete_story">
                                            </div>
                                            <textarea class="form-control mt-2" id="exampleFormControlTextarea1"
                                                name="audio_desc" rows="5" placeholder="Description"></textarea>

                                        </div>
                                        <div class="col-lg-6">
                                            <strong>8.2 Select Audio Bites</strong>
                                            <div id="audio_bites_selector" class="mt-2">
                                                <input type="file" class="audio_bites" id="img" name="audio_bites[]">
                                            </div>
                                            <textarea class="form-control mt-2" id="exampleFormControlTextarea1"
                                                name="audio_bites_desc" rows="5" placeholder="Description"></textarea>
                                        </div>
                                    </div>

                                </div>



                                <!-- Select one audio file -->

                                <HR style="    border-top: 1px solid rgba(0,0,0)">

                                <STRONG class=" h5 text-info">Step 9. Select Images </strong>



                                <!-- Select one image for news thumbnail-->
                                <div class="form-group">
                                    <label class="col-lg-12 p-0"><strong>9.1 Video Thumbnail JPG / PNG</strong>
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>

                                    </label><br>
                                    <input type="file" id="thumbnailimg" onchange="return thumbnailValidation()"
                                        name="thumbImg" accept="image/*" xxx>
                                    <!-- Image preview -->
                                    <div id="thumbnailID"></div>
                                </div>





                                <div class="form-group">
                                    <label class="col-lg-12 p-0"><strong>9.2 Gallery Images JPG / PNG</strong>
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>

                                    </label><br>
                                    <textarea class="form-control mt-2" id="exampleFormControlTextarea1" name="gallery_desc"
                                        rows="5" placeholder="Description"></textarea>
                                    <input type="file" id="pro-image" name="galleryImage[]" accept="image/*" multiple>
                                    <!-- onclick="$('#pro-image').click()" -->
                                    <div class="preview-images-zone" style="display:none">
                                    </div>
                                </div>

                                <HR style="    border-top: 1px solid rgba(0,0,0)">

                                <div class="form-group">
                                    <label class="col-lg-12 p-0  h5 text-info">Step 10. Select Series</label>
                                    <svg data-toggle="popover" title="News Title"
                                        data-content="Some content inside the popover" xmlns="http://www.w3.org/2000/svg"
                                        width="20" height="20" fill="currentColor"
                                        class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                        data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                    </svg>

                                    </label>

                                    <select multiple name="series[]" id="series">

                                        <?php 
                                                if(isset($series))
                                                {
                                                    foreach($series as $series)
                                                    {
                                            ?>
                                        <option value="<?php echo $series['id'] ; ?>">
                                            <?php echo $series['name'] ; ?></option>
                                        <?php
                                                    }
                                                }
                                            ?>



                                    </select>
                                </div>



                                <!-- The body of news. Should extract text from file like txt and docs and pass to sql -->
                                <div class="form-group">
                                    <label class="col-lg-12 p-0 h5 text-info">Step 11. Select Bonus Media
                                        <svg data-toggle="popover" title="News Title"
                                            data-content="Some content inside the popover"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-info-circle float-right help_icon" data-toggle="tooltip"
                                            data-placement="left" title="Tooltip on left" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>
                                    </label>

                                    <div id="bonus_selectors">

                                        <div>
                                            <input type="file" class="bonus_media" name="bonus_media[]" multiple>
                                        </div>

                                    </div>

                                </div>
                                <!-- <button id="add_bonus_folder" type="button">Add Bonus </button> -->

                                <!-- <HR style="    border-top: 1px solid rgba(0,0,0)"> -->




                                <label class="col-lg-12 p-0  h5 text-info">Step 12. Select Others (optional)</label>


                                <div class="row">
                                    <div class="col-lg-6">


                                        <div class="form-group">
                                            <label class="col-lg-12 p-0"><strong>12.1. District*</strong>
                                                <svg data-toggle="popover" title="News Title"
                                                    data-content="Some content inside the popover"
                                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                    data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                    <path
                                                        d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                </svg>

                                            </label>
                                            <select class="form-control" id="exampleFormControlSelect2" name="district" xxx>
                                                <?php
                                                        foreach($district_global as $district)
                                                        {

                                                    ?>
                                                <option value="<?php echo $district ; ?>"><?php echo $district ; ?></option>

                                                <?php
                                                        }
                                                    ?>


                                            </select>
                                        </div>



                                        <div class="form-group">
                                            <label class="col-lg-12 p-0"><strong>12.2 Reporter*</strong>
                                                <svg data-toggle="popover" title="News Title"
                                                    data-content="Some content inside the popover"
                                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                    data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                    <path
                                                        d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                </svg>


                                            </label>
                                            <select class="form-control" id="exampleFormControlSelect2" name="reporter" xxx>
                                                <?php
                                                        foreach($reporter_global as $reporter)
                                                        {

                                                    ?>
                                                <option value="<?php echo $reporter ; ?>"><?php echo $reporter ; ?></option>

                                                <?php
                                                        }
                                                    ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-12 p-0"><strong>12.3 Camera Man*</strong>
                                                <svg data-toggle="popover" title="News Title"
                                                    data-content="Some content inside the popover"
                                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                    data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                    <path
                                                        d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                </svg>

                                            </label>
                                            <select class="form-control" id="exampleFormControlSelect2" name="camera_man"
                                                xxx>
                                                <?php
                                                        foreach($cameraman_global as $cman)
                                                        {

                                                    ?>
                                                <option value="<?php echo $cman ; ?>"><?php echo $cman ; ?></option>

                                                <?php
                                                        }
                                                    ?>
                                            </select>
                                        </div>




                                    </div>
                                    <div class="col-lg-6">


                                        <div class="form-group">
                                            <label class="col-lg-12 p-0"><strong>12.4 Created By*</strong>
                                                <svg data-toggle="popover" title="News Title"
                                                    data-content="Some content inside the popover"
                                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                    data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                    <path
                                                        d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                </svg>

                                            </label>
                                            <select class="form-control" id="exampleFormControlSelect2" name="uploaded_by"
                                                xxx>
                                                <?php
                                                        foreach($createdby_global as $cby)
                                                        {

                                                    ?>
                                                <option value="<?php echo $cby ; ?>"><?php echo $cby ; ?></option>

                                                <?php
                                                        }
                                                    ?>

                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-12 p-0"><strong>12.5 Video Type</strong>
                                                <svg data-toggle="popover" title="News Title"
                                                    data-content="Some content inside the popover"
                                                    xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    fill="currentColor" class="bi bi-info-circle float-right help_icon"
                                                    data-toggle="tooltip" data-placement="left" title="Tooltip on left"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                    <path
                                                        d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                                </svg>

                                            </label>
                                            <select class="form-control" id="exampleFormControlSelect2" name="video_type"
                                                xxx>
                                                <option value="selfhost">Selfhost</option>
                                                <option value="vimeo">Vimeo</option>

                                            </select>
                                        </div>




                                    </div>
                                </div>









                                <label class="col-lg-12 p-0 h5 text-info">Step 13.</label>

                                <button type="submit" class="btn btn-primary" id="submit_news" name="submit">Collect news</button>

                                <label class="col-lg-12 p-0 h5 text-info mt-2">Step 14.</label>
                                <span><button class="btn btn-danger disabled " style="cursor: no-drop;">Delete collected
                                        news</button></span>
                                <span>please <strong><a href="./remotecopycreator.php">go to Remote Copy
                                            Creator</a></strong> tool to delete</span>

                            </form>
                        </div>
                    </div>
                </div>

            </div>

        </div>


    </div>












        <!-- FORM DOM FOR LANGUAGE SELECTION IN NEWS TITLE--- -->

        <script>
        // $('#newsTag[]--1-chbx').hide();

        var s = document.getElementsByName('lang_selec')[0];

        function changeOrg() {
            // var value = s.options[s.selectedIndex].value;
            var value = '';
            // console.log(value);
            if (value == 'nepali') {
                document.getElementById('formByline').innerHTML =
                    `<input type="text" style="width:100%;" class="form-control form-nepali" placeholder=";dfrf/sf] lzif{s " name="byLine" id="input_box" xxx  onkeydown="limit(this);" onkeyup="limit(this);charcountupdate(this.value)">`
            }
            if (value == 'english') {
                document.getElementById('formByline').innerHTML =
                    `<input type="text" style="width:100%;" class="form-control" placeholder="Enter news byline english" name="byLine" id="input_box" xxx  onkeydown="limit(this);" onkeyup="limit(this);charcountupdate(this.value)">`
            }
            if (value == 'nepali_uni') {
                document.getElementById('formByline').innerHTML =
                    `<input type="text" style="width:100%;" class="form-control" placeholder="Enter news byline in nepali unicode" name="byLine" id="input_box" xxx  onkeydown="limit(this);" onkeyup="limit(this);charcountupdate(this.value)">`
            }
        }
        //on page load
        changeOrg();
        </script>

        <!-- ---NEWS BODY FILE VALIDATION -->


        <script>
        // -------------preview IMAGE VALIDATION------------------------ 
        function previewValidation() {
            var fileInput =
                document.getElementById('previewimg');

            var filePath = fileInput.value;
            console.log(filePath)
            // Allowing file type 
            var allowedExtensions =
                /(\.jpg|\.jpeg|\.png|\.gif|\.JPG)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Invalid Video preview file extension.');
                fileInput.value = '';
                document.getElementById(
                        'previewID').innerHTML =
                    '<img style="display:none" class="shadow" src="'
                '"/>';
                return false;
            } else {

                // Image preview 
                if (fileInput.files && fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(
                                'previewID').innerHTML =
                            '<img style="display:block;height:150px;width:auto;padding-top:15px;" class="shadow" src="' +
                            e.target.result +
                            '"/>';
                    };

                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
        }
        </script>


        <script>
        // -------------thumbnail IMAGE VALIDATION------------------------ 
        function thumbnailValidation() {
            var fileInput =
                document.getElementById('thumbnailimg');

            var filePath = fileInput.value;
            console.log(filePath)
            // Allowing file type 
            var allowedExtensions =
                /(\.jpg|\.jpeg|\.png|\.gif|\.JPG)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Invalid Video thumbnail file extension.');
                fileInput.value = '';
                document.getElementById(
                        'thumbnailID').innerHTML =
                    '<img style="display:none" class="shadow" src="'
                '"/>';
                return false;
            } else {

                // Image preview 
                if (fileInput.files && fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(
                                'thumbnailID').innerHTML =
                            '<img style="display:block;height:150px;width:auto;padding-top:15px;" class="shadow" src="' +
                            e.target.result +
                            '"/>';
                    };

                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
        }
        </script>
        <script>
        // -------------Video long VALIDATION------------------------ 
        function regularFeedValidation() {
            var fileInput =
                document.getElementById('regularfeed');

            var filePath = fileInput.value;
            console.log(filePath)
            // Allowing file type 
            var allowedExtensions =
                /(\.mp4|\.jpeg)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Invalid Video thumbnail file extension.');
                fileInput.value = '';
                document.getElementById(
                        'regularfeedID').innerHTML =
                    '<video width="320" height="240" controls style="display:none"><source src="" type="video/mp4"></video>';
                return false;
            } else {

                // video long preview 
                if (fileInput.files && fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(
                                'regularfeedID').innerHTML =
                            '<video width="100%" height="160px" controls style="display:block"><source src="' + e
                            .target.result + '" type="video/mp4"> </video> ';
                        document.getElementById('regularfeedplaceholder').style.display = "none"
                    };

                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
        }
        </script>

        <script>
        // -------------Video lazy VALIDATION------------------------ 
        function readyVersionValidation() {
            var fileInput =
                document.getElementById('readyversion');

            var filePath = fileInput.value;
            console.log(filePath)
            // Allowing file type 
            var allowedExtensions =
                /(\.mp4|\.jpeg)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Invalid Video lazy file extension.');
                fileInput.value = '';
                document.getElementById(
                        'readyversionID').innerHTML =
                    '<video width="320" height="240" controls style="display:none"><source src="" type="video/mp4"></video>';
                return false;
            } else {

                // video long preview 
                if (fileInput.files && fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(
                                'readyversionID').innerHTML =
                            '<video width="100%" height="160px" controls style="display:block"><source src="' + e
                            .target.result + '" type="video/mp4"> </video> ';
                        document.getElementById('readyVersionplaceholder').style.display = "none"
                    };

                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
        }
        </script>
        <script>
        // -------------Video extra VALIDATION------------------------ 
        function roughcutValidation() {
            var fileInput =
                document.getElementById('roughcut');

            var filePath = fileInput.value;
            console.log(filePath)
            // Allowing file type 
            var allowedExtensions =
                /(\.mp4|\.jpeg)$/i;

            if (!allowedExtensions.exec(filePath)) {
                alert('Invalid Video lazy file extension.');
                fileInput.value = '';
                document.getElementById(
                        'roughcutID').innerHTML =
                    '<video width="320" height="240" controls style="display:none"><source src="" type="video/mp4"></video>';

                return false;
            } else {

                // video extra preview 
                if (fileInput.files && fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(
                                'roughcutID').innerHTML =
                            '<video width="100%" height="160px" controls style="display:block"><source src="' + e
                            .target.result + '" type="video/mp4"> </video> ';
                        document.getElementById('roughcutplaceholder').style.display = "none"
                    };



                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
        }
        </script>
        <!-- -------------TOOLTIP ------------------------ -->
        <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
        });
        </script>
        <script>
        // Use the plugin once the DOM has been loaded.
        $(function() {
            // Apply the plugin 
            var tags = $('#tags').filterMultiSelect();

            $('#jsonbtn1').click((e) => {
                var b = true;
                var result = {
                    ...JSON.parse(tags.getSelectedOptionsAsJson(b)),

                }
                $('#jsonresult1').text(JSON.stringify(result, null, "  "));
            });
            $('#jsonbtn2').click((e) => {
                var b = false;
                var result = {
                    ...JSON.parse(tags.getSelectedOptionsAsJson(b)),

                }
                $('#jsonresult2').text(JSON.stringify(result, null, "  "));
            });
            $('#form').on('keypress keyup', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        });
        </script>


        <script>
        $(function() {
            // Apply the plugin 
            var series = $('#series').filterMultiSelect();

            $('#jsonbtn3').click((e) => {
                var b = true;
                var result = {
                    ...JSON.parse(series.getSelectedOptionsAsJson(b)),

                }
                $('#jsonresult3').text(JSON.stringify(result, null, "  "));
            });
            $('#jsonbtn3').click((e) => {
                var b = false;
                var result = {
                    ...JSON.parse(series.getSelectedOptionsAsJson(b)),

                }
                $('#jsonresult3').text(JSON.stringify(result, null, "  "));
            });
            $('#form').on('keypress keyup', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        });
        </script>

        <!-- count character in news title -->
        <script>
        function charcountupdate(str) {
            var lng = str.length;
            document.getElementById("charcount").innerHTML = lng + ' out of 300 characters';
            if (lng == 300) {
                console.log('exceedde')
                document.getElementById("charcount").style.color = "red"
            } else {
                document.getElementById("charcount").style.color = "#6c757d"
            }
        }
        </script>

        <!-- restrict number of words in news title -->
        <script>
        function limit(element) {
            var max_chars = 300;

            if (element.value.length > max_chars) {
                element.value = element.value.substr(0, max_chars);
            }
        }
        </script>

        <script>
        $(document).ready(function() {
            document.getElementById('pro-image').addEventListener('change', readImage, false);
            // $( ".preview-images-zone" ).sortable();
            $(document).on('click', '.image-cancel', function() {
                let no = $(this).data('no');
                $(".preview-image.preview-show-" + no).remove();
            });

            $("#pro-image").click(function() {
                // $(".preview-images-zone").empty();
            });

        });
        var num = 0;

        function readImage() {
            if (window.File && window.FileList && window.FileReader) {
                var files = event.target.files; //FileList object
                $(".preview-images-zone").css("display", "block");
                var output = $(".preview-images-zone");
                for (let i = 0; i < files.length; i++) {
                    var file = files[i];
                    if (!file.type.match('image')) continue;
                    var picReader = new FileReader();
                    picReader.addEventListener('load', function(event) {
                        var picFile = event.target;
                        var html = '<div class="preview-image  preview-show-' + num + '">' +
                            '<a  class="image-cancel" data-no="' + num + '">x</a>' +
                            '<div class="image-zone"><img id="pro-img-' + num + '" src="' + picFile.result +
                            '"></div>' +
                            '</div>';
                        output.append(html);
                        num = num + 1;
                    });
                    picReader.readAsDataURL(file);
                }
                // $("#pro-image").val('');
            } else {
                console.log('Browser not support');
            }
        }
        </script>
        <script>
        // Use the plugin once the DOM has been loaded.
        $(function() {
            // Apply the plugin 
            var categories = $('#categories').filterMultiSelect();

            $('#jsonbtn1').click((e) => {
                var c = true;
                var result = {
                    ...JSON.parse(categories.getSelectedOptionsAsJson(c)),

                }
                $('#jsonresult1').text(JSON.stringify(result, null, "  "));
            });
            $('#jsonbtn2').click((e) => {
                var b = false;
                var result = {
                    ...JSON.parse(categories.getSelectedOptionsAsJson(c)),

                }
                $('#jsonresult2').text(JSON.stringify(result, null, "  "));
            });
            $('#form').on('keypress keyup', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        });


        //----------------Bonus Folder Adder ---------------------------

        // $(document).on('click', '#add_bonus_folder', function() {
        $(document).on('change', '.bonus_media', function() {

            if ($(this)[0].files.length > 0) {


                var html = `
                <div>
                    <input type="file" class='bonus_media' name="bonus_media[]" multiple >
                </div>

            `;



                $("#bonus_selectors").append(html);

            }

        });


        //----------------Additional Folder Adder---------------------------

        // $(document).on('click', '#add_extra_folder', function() {

        $(document).on('change', '.extra_folder', function() {

            if ($(this)[0].files.length > 0) {
                var html = `
            <div>
                <input type="file" class="extra_folder" id="" name="extra_files[]" multiple >
            </div>

        `;

                $("#extra_selectors").append(html);
            }

        });


        //----------------Audio Bites Adder---------------------------

        // $(document).on('click', '#add_audio_bites', function() {

        $(document).on('change', '.audio_bites', function() {

            if ($(this)[0].files.length > 0) {
                var html = `
                <div> 
                    <input type="file" class="audio_bites" id="img" name="audio_bites[]">
                </div>

            `;

                $("#audio_bites_selector").append(html);
            }

        });

        
        $(document).on('click', '#submit_news', function() {

            $(".collector").css("filter", "blur(2px)");
            $("#preloader_boot").css("display", "");
            $('form#collector_form').submit();


        });



        </script>
<script src="http://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/js/nepali.datepicker.v3.7.min.js" type="text/javascript"></script>
<script type="text/javascript">
            window.onload = function() {
                var mainInput = document.getElementById("nepali-datepicker");
                mainInput.nepaliDatePicker();
            };
        </script>


</body>

</html>