
<?php

include "accesories/session_handler/session_views.php";
include "accesories/environment/wp_api_env.php";
  
date_default_timezone_set("Asia/Kathmandu");



$url = "$domain_url/custom_ticker_api.php?datas=fetch";
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

 if($result['status'] == 'enable')
 {
     $isChecked = 'checked';
 }
 else
 {
    $isChecked = '';
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
    <title>News Ticker</title>
</head>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 57px;
        height: 30px;
        border:2px solid rgb(0, 72, 100);
        border-radius: 34px;
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
        background-color: rgb(226, 226, 226);
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border: 1px solid rgb(0, 72, 100)
    }

    input:checked+.slider {
        background-color: #09ed46;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #09ed46;
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
<body>
<?php
        include "accesories/navbar/nav.php";
    ?>
    <br>
<div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-lg  mb-5 bg-white rounded">
                    <div class="card-header bg-info text-light ">
                        <div class="row">
                            <div class="col-lg-9">
                                <h4>NEWS TICKER</h4>
                            </div>
                            <div class="col-lg-3 text-right">
                                
                                    <span style="font-size: large;font-weight: 500;" class="pt-1">Showing  &nbsp;</span>
                                    <label class="switch">
                                        <input type="checkbox" class="post_type" <?php echo $isChecked  ; ?> >
                                        <span class="slider round"></span>
                                    </label>
                              
                            </div>
    
                        </div>
                    </div>

                    <form action = "accesories/wp_ticker_submit.php" method="POST"> 
                        <div class="card-body">
                            <div class="form-group">
                            <br>
                            <label class="col-lg-12 p-0 h5 text-info">Title *
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
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="title"><?php echo $result['title'] ;?></textarea>
                            
                                <br>
                                <label class="col-lg-12 p-0 h5 text-info">Ticker *
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
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="content"><?php echo $result['content'] ;?></textarea>
                                <br>


                                <button type="submit" class="btn btn-primary " name="submit">Post</button>
                            </div>
                        </div>
                    </form>
                </div>
              
            </div>
        </div>
        </div>  
        <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>

<script>
        
$(document).on('click', '.post_type', function() {

var id = $(this).val();
var type = null ;

if ($(this).is(":checked") == false) {
    type = "disable";
} else {
    $(this).attr('checked', 'checked');
    type = "enable";
}



$.ajax({
    url: "https://nepalnewsbank.com/custom_ticker_api.php?status="+type,
    method: "GET",
    data: {
        
    },
    dataType: "text",
    success: function(data) {



    }
});

});

</script>
    
</body>
</html>