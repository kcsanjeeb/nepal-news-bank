<?php

include "accesories/session_handler/session_views.php";
include "accesories/connection.php";
include "accesories/environment/wp_api_env.php";
include "global/timezone.php";

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
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MTcyMDYwMzMsImlzcyI6Imh0dHBzOlwvXC9uZXBhbG5ld3NjbGllbnQuc2FuamVlYmtjLmNvbS5ucCIsImV4cCI6MTYyMjQ3NjQyMywianRpIjoiYzc1MWVkZTMtY2FlYi00ODJkLTljZjAtOTlkMjViZGJkMGYxIiwidXNlcklkIjoxLCJyZXZvY2FibGUiOnRydWUsInJlZnJlc2hhYmxlIjoidHJ1ZSJ9.DShsuK9x9qaOVRP7iCKgMacKKRKUvZ5ZSSFJFT_Kg98' 
    ),
));

$response = curl_exec($curl);          
$result = json_decode($response);
$result = json_decode(json_encode($result) , true);     
$respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);                    
curl_close($curl);

$i= 0 ;

$containing_video_array = array();
$not_containing_video_array = array();


foreach($result as $res)
{
    
    $series[$i]['id'] = $res['id'];
    $series[$i]['name'] = $res['title']['rendered'];

    if (strpos( strtolower($res['title']['rendered']), 'picture') !== false) {
        array_push($containing_video_array , $series[$i] );
    }
    else
    {
        array_push($not_containing_video_array , $series[$i] );
    }


    $i++;


}

$series = array_merge($containing_video_array,$not_containing_video_array);


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
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MTcyMDYwMzMsImlzcyI6Imh0dHBzOlwvXC9uZXBhbG5ld3NjbGllbnQuc2FuamVlYmtjLmNvbS5ucCIsImV4cCI6MTYyMjQ3NjQyMywianRpIjoiYzc1MWVkZTMtY2FlYi00ODJkLTljZjAtOTlkMjViZGJkMGYxIiwidXNlcklkIjoxLCJyZXZvY2FibGUiOnRydWUsInJlZnJlc2hhYmxlIjoidHJ1ZSJ9.DShsuK9x9qaOVRP7iCKgMacKKRKUvZ5ZSSFJFT_Kg98' 
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

$sql_content = "select * from archive_photos order by published_date desc ";
$run_sql_content= mysqli_query($connection, $sql_content);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <!---- filter multi-select-- -->
    <link rel="stylesheet" href="assets/css/filter-multi-select.css" />
    <script src="assets/js/filter-multi-select-bundle.min.js"></script>
    <script src="assets/js/multi-select-tags.js"></script>

    <style>
    h4 {
        color: aliceblue;
        margin-bottom: 0;
        font-size: 1.2rem;
    }

    b {
        color: #000
    }

    .help_icon:hover {
        color: #007bff;
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
    </style>
</head>
<style>
h4 {
    color: aliceblue;
    margin-bottom: 0;
    font-size: 1.2rem;
}
</style>

<body>
    <!-- Image and text -->



    <?php
        include "accesories/navbar/nav.php";
    ?>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-lg  mb-5 bg-white rounded">
                    <div class="card-header bg-info ">
                        <h4>Archive Picture Creator</h4>
                    </div>
                    <div class="card-body">
                        <?php


                    if(isset($_SESSION['notice']) )
                    {
                        if($_SESSION['notice'] == 'Error_check_file')
                        {
                            $notice  = 'Error publishing. Please try again';
                            $bg_color = 'red';
                            $color = '#000';
                            $color_down = '#000';
                            

                        }

                        if($_SESSION['notice'] == 'success')
                        {
                            $notice  = 'Succesfully published!';
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
                            unset($_SESSION['notice']);
                    }
                    ?>

                        <form method="POST" action="accesories/archive_picture_submit.php"
                            enctype="multipart/form-data">
                            <div class="form-group">
                                <label class=" p-0 col-lg-12 h5 text-info">Step 1. Select Date *
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
                                <input type="date" class="form-control col-lg-2" name="date" placeholder="Title">
                            </div>
                            <div class="form-group">
                                <label class=" p-0 col-lg-12 h5 text-info">Step 2. Enter Title *
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
                                <input class="form-control" name="title" placeholder="Title">

                            </div>

                            <div class="form-group">
                                <label class=" p-0 col-lg-12 h5 text-info">Step 3. Enter Description 
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
                                <textarea class="form-control" name="description" placeholder="Description"></textarea>
                            </div>


                            <div class="form-group mt-3">
                                <label class="col-lg-12 p-0  h5 text-info">Step 4. Select Series <svg
                                        data-toggle="popover" title="News Title"
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


                                <select multiple class="form-control" id="series" name="series[]" required>

                                    <?php 
                                        if(isset($series))
                                        {
                                            foreach($series as $series)
                                            {
                                    ?>
                                    <option value="<?php echo $series['id'] ; ?>"><?php echo $series['name'] ; ?>

                                    </option>
                                    <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </div>

                            <!-- The tags for news. example: sports,football,messi,goal. Should be in CSV(comma separated format) -->
                            <div class="form-group">
                                <label class="col-lg-12 p-0  h5 text-info">Step 5. Select News Tags <svg
                                        data-toggle="popover" title="News Title"
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
                                <!-- <input type="text" class="form-control" placeholder="Enter news byline" name="newsTag" required> -->
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



                                </select>
                            </div>



                            <div class="form-group ">
                                <label class=" p-0 mt-2 col-lg-12 h5 text-info">Step 6. Select Gallery Image *
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

                                </label> <br>
                                <input type="file" id="pro-image" name="galleryImage[]" accept="image/*" multiple>
                                <!-- onclick="$('#pro-image').click()" -->
                                <div class="preview-images-zone" style="display:none">
                                </div>
                            </div>


                            <!-- Select one image for news thumbnail-->
                            <div class="form-group ">
                                <label class=" p-0 mt-2 col-lg-12 h5 text-info">Step 7. Select Thumbnail 
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

                                </label> <br>
                                <input type="file" id="thumbnailimg" onchange="return thumbnailValidation()"
                                    name="thumb" accept="image/*" required>
                                <!-- Image preview -->
                                <div id="thumbnailID"></div>
                                <br>
                                <label class=" p-0 h5 text-info">Step 8.
                                    <div>
                                        <input value="Post" id="submitbtn" type="submit" name="submit"
                                            class="btn btn-primary mt-2">
                                    </div>
                                    <div>

                                    </div>


                        </form>


                    </div>
                </div>
            </div>



        </div>

        <div class="col-lg-12">
            <div class="card shadow-lg  mb-5 bg-white rounded">
                <div class="card-header bg-info ">
                    <h4>Archive Picture List</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">

                        <?php
                        $counter = 1;
                            while($row_content = mysqli_fetch_assoc($run_sql_content))
                            {
                                $byline = $row_content['title'];
                                $archive_id = $row_content['archive_id'];
                                $media_id = $row_content['wp_media_id'];
                                $wp_id = $row_content['wp_id'];

                                $gallery = $row_content['gallery'];
                                $thumbnail = $row_content['thumbnail'];

                                

                        ?>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <p class="m-0"><strong><?php echo $counter; ?>.</strong> <?php echo $byline; ?></p>


                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                data-target="#exampleModal<?php echo $counter ; ?>">
                                Delete
                            </button>
                        </li>




                        <div class="modal fade" id="exampleModal<?php echo $counter ; ?>" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Delete Archive Video</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete Archive Video <strong> <?php echo $byline ; ?> ?
                                        </strong>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <form action="accesories/delete_archive_photos.php" method="POST">
                                            <input type="hidden" class="btn btn-danger" name="archive_id"
                                                value="<?php echo $archive_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="wp_id"
                                                value="<?php echo $wp_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="wp_media_id"
                                                value="<?php echo $media_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="byline"
                                                value="<?php echo $byline ; ?>">

                                            <input type="hidden" class="btn btn-danger" name="thumbnail"
                                                value="<?php echo $thumbnail ; ?>">

                                            <input type="hidden" class="btn btn-danger" name="gallery"
                                                value="<?php echo $gallery ; ?>">





                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php
                        $counter++;
                        }
                    ?>


                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
    function submitloader() {
        document.getElementById("submitbtn").style.display = "none";
        document.getElementById("loaderbtn").style.display = "block";

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
    function videolongValidation() {
        var fileInput =
            document.getElementById('videolong');

        var filePath = fileInput.value;
        console.log(filePath)
        // Allowing file type 
        var allowedExtensions =
            /(\.mp4|\.jpeg)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid Video thumbnail file extension.');
            fileInput.value = '';
            document.getElementById(
                    'videolongID').innerHTML =
                '<video width="320" height="240" controls style="display:none"><source src="" type="video/mp4"></video>';
            return false;
        } else {

            // video long preview 
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(
                            'videolongID').innerHTML =
                        '<video width="100%" height="160px" controls style="display:block"><source src="' + e
                        .target.result + '" type="video/mp4"> </video> ';
                    document.getElementById('videolongplaceholder').style.display = "none"
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
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
    <script>
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
    </script>
</body>

</html>