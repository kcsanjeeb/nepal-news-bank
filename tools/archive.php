<?php
error_reporting(0);
include "accesories/session_handler/session_views.php";
include "accesories/environment/wp_api_env.php";
include "global/timezone.php";





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

    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script> -->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous">
    </script>
    <!---- filter multi-select-- -->
    <link rel="stylesheet" href="assets/css/filter-multi-select.css" />
    <script src="assets/js/filter-multi-select-bundle.min.js"></script>
    <script src="assets/js/multi-select-tags.js"></script>
</head>
<style>
.table-sortable tbody tr {
    cursor: move;
}
</style>

<body>

<?php
        include "accesories/navbar/nav.php";
    ?>
    <br>
 

    <form method="POST" action="accesories/archive_pics_vids.php" enctype="multipart/form-data">




        <div class="container">


            <!-- Title -->
            <div class="form-group mt-3">
                <label class="col-lg-12 p-0  h5 text-info">Step 1. News Title</label>

                <input type="text" class="form-control" name="title" placeholder="Title">
            </div>

            <!-- Category -->
            <div class="form-group mt-3">
                <label class="col-lg-12 p-0  h5 text-info">Step 2. Select News Category*</label>
                <!-- <input type="text" class="form-control" placeholder="Enter news byline" name="newsTag" xxx> -->
                <select multiple name="newsCategories[]" id="categories" required>



                    <?php 
                                        if(isset($category))
                                        {
                                            foreach($category as $category)
                                            {
                                             

                                                if( strpos( strtolower($category['name']), "archive" ) === false) {
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

            <!-- Tags -->
            <div class="form-group">
                <label class="col-lg-12 p-0  h5 text-info">Step 3. Select News Tags</label>
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

            <!-- Series -->
            <div class="form-group">
                <label class="col-lg-12 p-0  h5 text-info">Step 4. Series</label>
                <svg data-toggle="popover" title="News Title" data-content="Some content inside the popover"
                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-info-circle float-right help_icon" data-toggle="tooltip" data-placement="left"
                    title="Tooltip on left" viewBox="0 0 16 16">
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
                    <option value="<?php echo $series['id'] ; ?>"><?php echo $series['name'] ; ?></option>
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

            <!-- Select one image for news thumbnail-->
            <div class="form-group">
                <label class="col-lg-12 p-0  h5 text-info">Step 5. Thumbnail</label>
                <svg data-toggle="popover" title="News Title" data-content="Some content inside the popover"
                    xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-info-circle float-right help_icon" data-toggle="tooltip" data-placement="left"
                    title="Tooltip on left" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path
                        d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                </svg>

                </label><br>
                <input type="file" id="thumbnailimga"
                    onchange="return thumbnailValidation('thumbnailimga' , 'thumbnailIDa' )" name="thumbImg"
                    accept="image/*" xxx>
                <!-- Image preview -->
                <div id="thumbnailIDa"></div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-lg  mb-5 bg-white rounded">
                        <div class="card-header bg-info text-light ">
                            <div class="row">
                                <div class="col-lg-9">
                                    <h4>Archive Videos</h4>
                                </div>


                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <div class="row clearfix">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-hover table-sortable" id="tab_logic">
                                            <thead>
                                                <tr>
                                                    <th style="width:15%" class="text-center">
                                                        Video
                                                    </th>

                                                    <th style="width:70%" class="text-center">
                                                        Description
                                                    </th>

                                                    <th style="width:20%;" class="text-center">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="vid-rows">


                                                <tr id='addr0' data-id="0" class="hidden vid-row">
                                                    <td data-name="name">



                                                        <div class="card" style="width:100%">
                                                            <img src="./assets/images/placeholder.jpg"
                                                                class="card-img-top " id="videolongplaceholder"
                                                                alt="...">
                                                            <div id="videolongID"></div>
                                                            <div class="card-body">

                                                                <input type="file" id="videolong" name="video[]"
                                                                    onchange="return videolongValidation('videolong' , 'videolongID' , 'videolongplaceholder' )"
                                                                    required>


                                                            </div>
                                                        </div>


                                                    </td>

                                                    <td data-name="desc">
                                                        <textarea name="video[0][desc]" rows="10"
                                                            placeholder="Description" class="form-control"></textarea>
                                                    </td>

                                                    <td data-name="del">
                                                        <button name="del0"
                                                            class='btn btn-danger glyphicon glyphicon-remove row-remove vid-row-del'><span
                                                                aria-hidden="true">Delete</span></button>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a class="btn btn-primary float-right add-videos" data-id="1">Add Row</a>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>


        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-lg  mb-5 bg-white rounded">
                        <div class="card-header bg-info text-light ">
                            <div class="row">
                                <div class="col-lg-9">
                                    <h4>Archive Picture</h4>
                                </div>


                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <div class="row clearfix">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered table-hover table-sortable" id="tab_logic">
                                            <thead>
                                                <tr>
                                                    <th style="width:15%" class="text-center">
                                                        Image
                                                    </th>

                                                    <th style="width:65%" class="text-center">
                                                        Description
                                                    </th>

                                                    <th style="width:20%" class="text-center">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="pics-rows">


                                                <tr id='addr0' data-id="0" class="hidden pic-row">
                                                    <td data-name="name">


                                                        <div class="form-group">

                                                            <input type="file" id="thumbnailimg"
                                                                onchange="return thumbnailValidation('thumbnailimg' , 'thumbnailID')"
                                                                name="pic[]" accept="image/*" xxx>
                                                            <!-- Image preview -->
                                                            <div id="thumbnailID"></div>
                                                        </div>


                                                    </td>

                                                    <td data-name="desc">
                                                        <textarea name="pic[0][desc]" rows="10"
                                                            placeholder="Description" class="form-control"></textarea>
                                                    </td>

                                                    <td data-name="del">

                                                        <button name="del0"
                                                            class='btn btn-danger glyphicon glyphicon-remove row-remove pics-row-del'><span
                                                                aria-hidden="true">Delete</span></button>
                                                    </td>
                                                </tr>





                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <a class="btn btn-primary float-right add-pic" data-id="1"> Add Row</a>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary col-lg-6 float-right mb-5"
                            name="submit">Post</button>
                        <div class="col-lg-6">
                            Press on the post button to post archive.
                        </div>
                    </div>

                    <br>
                    <br>
                </div>
            </div>
        </div>



    </form>



    <script>
    // -------------thumbnail IMAGE VALIDATION------------------------ 
    function thumbnailValidation(thumbnailimg, thumbnailID) {
        var fileInput =
            document.getElementById(thumbnailimg);

        var filePath = fileInput.value;
        console.log(filePath)
        // Allowing file type 
        var allowedExtensions =
            /(\.jpg|\.jpeg|\.png|\.gif|\.JPG)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid Video thumbnail file extension.');
            fileInput.value = '';
            document.getElementById(
                    thumbnailID).innerHTML =
                '<img style="display:none" class="shadow" src="'
            '"/>';
            return false;
        } else {

            // Image preview 
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(
                            thumbnailID).innerHTML =
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
    function videolongValidation(videolong, videolongID, videolongplaceholder) {
        var fileInput =
            document.getElementById(videolong);

        var filePath = fileInput.value;
        console.log(filePath)
        // Allowing file type 
        var allowedExtensions =
            /(\.mp4|\.jpeg)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid Video thumbnail file extension.');
            fileInput.value = '';
            document.getElementById(
                    videolongID).innerHTML =
                '<video width="320" height="240" controls style="display:none"><source src="" type="video/mp4"></video>';
            return false;
        } else {

            // video long preview 
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(videolongID).innerHTML =
                        '<video width="100%" height="160px" controls style="display:block"><source src="' + e
                        .target.result + '" type="video/mp4"> </video> ';
                    document.getElementById(videolongplaceholder).style.display = "none"
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
    </script>



    <script>
    $(document).ready(function() {

        $(document).on("click", ".add-videos", function() {

            var counter = $(this).attr("data-id");


            $html = `
                                <tr id='addr` + counter + `' data-id="` + counter + `" class="hidden vid-row" >
                                                <td data-name="name">



                                                    <div class="card" style="width:100%">
                                                        <img src="./assets/images/placeholder.jpg" class="card-img-top "
                                                            id="videolongplaceholder` + counter + `" alt="...">
                                                        <div id="videolongID` + counter + `"></div>
                                                        <div class="card-body">

                                                            <input type="file" id="videolong` + counter +
                `" name="video[]"
                                                                onchange="return videolongValidation('videolong` +
                counter + `' , 'videolongID` + counter + `' , 'videolongplaceholder` + counter + `')" required>


                                                        </div>
                                                    </div>


                                                </td>

                                                <td data-name="desc">
                                                    <textarea name="video[` + counter + `][desc]" rows="10" placeholder="Description"
                                                        class="form-control"></textarea>
                                                </td>

                                                <td data-name="del">
                                                    <button name="del0"
                                                        class='btn btn-danger glyphicon glyphicon-remove row-remove vid-row-del'><span
                                                            aria-hidden="true">Delete</span></button>
                                                </td>
                                            </tr>
                            `;
            counter++;



            $(this).attr("data-id", counter);
            $("#vid-rows").append($html);




        });

        $(document).on("click", ".vid-row-del", function() {

            $(this).closest('.vid-row').remove();

        });




        $(document).on("click", ".add-pic", function() {

            var counter = $(this).attr("data-id");


            $html = `


            <tr id='addr` + counter + `' data-id="` + counter + `" class="hidden pic-row">
                                                <td data-name="name">


                                                    <div class="form-group">

                                                        <input type="file" id="thumbnailimg` + counter + `"
                                                            onchange="return thumbnailValidation('thumbnailimg` +
                counter + `' , 'thumbnailID` + counter + `' )" name="pic[]"
                                                            accept="image/*" xxx>
                                                        <!-- Image preview -->
                                                        <div id="thumbnailID` + counter + `"></div>
                                                    </div>


                                                </td>

                                                <td data-name="desc">
                                                    <textarea name="pic[` + counter + `][desc]" rows="10" placeholder="Description"
                                                        class="form-control"></textarea>
                                                </td>

                                                <td data-name="del">
                                                    <button name="del0"
                                                        class='btn btn-danger glyphicon glyphicon-remove row-remove pics-row-del'><span
                                                            aria-hidden="true">Delete</span></button>
                                                </td>
                                            </tr>
      `;
            counter++;



            $(this).attr("data-id", counter);
            $("#pics-rows").append($html);




        });

        $(document).on("click", ".pics-row-del", function() {

            $(this).closest('.pic-row').remove();

        });


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
    <script>
    // -------------thumbnail IMAGE VALIDATION------------------------ 
    // function thumbnailValidation() {
    //     var fileInput =
    //         document.getElementById('thumbnailimg');

    //     var filePath = fileInput.value;
    //     console.log(filePath)
    //     // Allowing file type 
    //     var allowedExtensions =
    //         /(\.jpg|\.jpeg|\.png|\.gif|\.JPG)$/i;

    //     if (!allowedExtensions.exec(filePath)) {
    //         alert('Invalid Video thumbnail file extension.');
    //         fileInput.value = '';
    //         document.getElementById(
    //                 'thumbnailID').innerHTML =
    //             '<img style="display:none" class="shadow" src="'
    //         '"/>';
    //         return false;
    //     } else {

    //         // Image preview 
    //         if (fileInput.files && fileInput.files[0]) {
    //             var reader = new FileReader();
    //             reader.onload = function(e) {
    //                 document.getElementById(
    //                         'thumbnailID').innerHTML =
    //                     '<img style="display:block;height:150px;width:auto;padding-top:15px;" class="shadow" src="' +
    //                     e.target.result +
    //                     '"/>';
    //             };

    //             reader.readAsDataURL(fileInput.files[0]);
    //         }
    //     }
    // }
    // </script>


</body>

</html>