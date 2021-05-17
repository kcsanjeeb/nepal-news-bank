<?php

include "accesories/session_handler/session_views.php";
include "accesories/environment/wp_api_env.php";
include "accesories/connection.php";

include "global/timezone.php";

// include "accesories/session_handler/session_views.php";

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
foreach($result as $res)
{
    
    $series[$i]['id'] = $res['id'];
    $series[$i]['name'] = $res['title']['rendered'];
    $i++;


}



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


$sql_content = "select * from archive_video order by published_date desc ";
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
                        <h4>Archive Video RC Creator</h4>
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

                        <form method="POST" action="accesories/archive_submit.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class=" p-0 col-lg-12 h5 text-info">Step 1. Submit Date *
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
                                <label class=" p-0 col-lg-12 h5 text-info">Step 2. Enter Archive Title *
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
                            <!-- stock video  card -->
                            <!-- The tags for news. example: sports,football,messi,goal. Should be in CSV(comma separated format) -->
                            <!-- <div class="form-group mt-3">
                                <label class="col-lg-12 p-0  h5 text-info">Step 3. Select News category*</label>
                              
                            </div> -->

                            <!-- series-->
                            <div class="form-group mt-3">
                                <label class="col-lg-12 p-0  h5 text-info">Step 3. Select Series*
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

                                <select class="form-control" id="exampleFormControlSelect2" name="series" required>
                                    <!-- <option value="906">Series 1</option>
                                    <option value="906">Series 2</option>
                                    <option value="906">Series 3</option>
                                    <option value="906">Series 4</option>
                                    <option value="906">Series 5</option> -->

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
                                <label class="col-lg-12 p-0  h5 text-info">Step 4. Select News Tags*
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
                                <!-- <input type="text" class="form-control" placeholder="Enter news byline" name="newsTag" required> -->
                                <select multiple name="newsTag[]" id="tags">
                                    <!-- <option value="141">Business</option>
                                    <option value="142">Entertainment</option>
                                    <option value="134">Sports</option>
                                    <option value="135">International</option>
                                    <option value="136">Glamour</option> -->

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

                            <label class=" p-0 col-lg-12 h5 text-info pb-2">Step 5. Select Archive Video*</label>
                            <div class="row">

                                <div class="col-sm-4 ">
                                    <div class="card ">
                                        <img src="./assets/images/placeholder.jpg" class="card-img-top "
                                            id="videolongplaceholder" alt="...">
                                        <div id="videolongID"></div>
                                        <div class="card-body">
                                            <!-- <span class="">Step 5. Submit Video*</span> -->
                                            <div>
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

                                            <input type="file" id="videolong" name="video"
                                                onchange="return videolongValidation()" required>
                                            <small id="emailHelp" class="form-text text-muted">5min to 7min
                                                video</small>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Select one image for news thumbnail-->
                            <div class="form-group ">
                                <label class=" p-0 mt-2 col-lg-12 h5 text-info">Step 6. Select Thumbnail *
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
                                <label class=" p-0 h5 text-info">Step 7.
                                    <div>
                                        <input value="Post" id="submitbtn" type="submit" name="submit"
                                            class="btn btn-primary mt-2">
                                    </div>
                                    <div>
                                        <!-- <button id="loaderbtn" class="btn btn-warning mt-2" type="button"
                                                style="display: none;" disabled>
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                Posting Stock Footage ...
                                            </button> -->
                                    </div>


                        </form>


                    </div>
                </div>
            </div>


        </div>

        <div class="col-lg-12">
            <div class="card shadow-lg  mb-5 bg-white rounded">
                <div class="card-header bg-info ">
                    <h4>ARCHIVE VIDEO LIST</h4>
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

                                $video = $row_content['video'];
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
                                        Are you sure you want to delete Archive Video <strong> <?php echo $byline ; ?> ? </strong>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <form action="accesories/delete_archive_video.php" method="POST">
                                            <input type="hidden" class="btn btn-danger" name="archive_id" value="<?php echo $archive_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="wp_id" value="<?php echo $wp_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="wp_media_id" value="<?php echo $media_id ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="byline" value="<?php echo $byline ; ?>">
                                            
                                            <input type="hidden" class="btn btn-danger" name="video"
                                            value="<?php echo $video ; ?>">
                                            <input type="hidden" class="btn btn-danger" name="thumbnail"
                                            value="<?php echo $thumbnail ; ?>">

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
    </div>

    <script>
    function submitloader() {
        document.getElementById("submitbtn").style.display = "none";
        document.getElementById("loaderbtn").style.display = "block";

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