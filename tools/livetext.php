<?php

include "accesories/session_handler/session_views.php";
include "accesories/environment/wp_api_env.php";
include "accesories/connection.php";
  
include "global/timezone.php";


 $url_table = "$domain_url/custom_live_table_api.php";
 $data_table = '';
 $curl_table = curl_init();
 curl_setopt_array($curl_table, array(                    
 CURLOPT_URL => $url_table,
 CURLOPT_RETURNTRANSFER => true,
 CURLOPT_TIMEOUT => 30,
 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 CURLOPT_CUSTOMREQUEST => 'GET',
 CURLOPT_POSTFIELDS => $data_table ,
     CURLOPT_HTTPHEADER => array(
         "cache-control: no-cache",
         "content-type: application/json",
         'Authorization: Bearer '.$token_bearer
     ),
 ));
 
 $response_table = curl_exec($curl_table);     
 $result_table = json_decode($response_table);
 $result_table = json_decode(json_encode($result_table) , true);     
 $respCode_table = curl_getinfo($curl_table, CURLINFO_HTTP_CODE);
 $err = curl_error($curl_table);                    
 curl_close($curl_table);

 $table_rows =  $result_table ;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <title>Live Text</title>
</head>
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 57px;
    height: 30px;
    border: 2px solid rgb(0, 72, 100);
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

#example table {
    counter-reset: rowNumber;
}

#example table tr>td:first-child {
    counter-increment: rowNumber;
}

#example table tr td:first-child::before {
    content: counter(rowNumber);
    min-width: 1em;
    margin-right: 0.5em;
}

iframe {
    width: 150px;
    height: 100px
}

.small-title{
    font-size:15px;
    font-weight:600;
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
                                <h4>Live Pages</h4>
                            </div>
                            <div class="col-lg-3 text-right">



                            </div>

                        </div>
                    </div>


                    <div class="card-body">
                        <div class="form-group">

                            <form action="accesories/wp_news_iframe.php" method="POST">

                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="small-title">Live Title</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <input id="" type="text" name="title" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="small-title">Iframe</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <input id="" name="iframe" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="small-title">Description </label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <textarea id="" type="text" name="iframe_text"
                                                class="form-control" row="1"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="insert" value="insert">

                                <button type="submit" class="btn btn-primary mb-3"  name="submit">Post 
                                </button>
                                <br>
                            </form>

                            <div class="row">
                                <div class="col-lg-12">
                                    <table id="example" class="">
                                        <thead>
                                            <tr>
                                                <th class="text-center">SN</th>
                                                <th class="text-center">Live Title</th>
                                                <th class="text-center">Iframe</th>
                                                <th class="text-center">Description</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        
                                       

                                        <tbody>

                                            <?php
                                                $sql_iframe = "select * from iframe ";
                                                $run_sql_iframe= mysqli_query($connection, $sql_iframe);

                                                $counter_rownss = 1 ;
                                                while($rows = mysqli_fetch_assoc($run_sql_iframe))
                                                {
                                            ?>

                                            <tr>
                                                <td><?php echo $counter_rownss ; ?></td>
                                                <td><?php echo $rows['iframe_title'] ;?></td>
                                                <td><?php echo $rows['iframe_iframe'] ;?></td>
                                                <td><?php echo $rows['iframe_text'] ;?></td>

                                                <td>
                                                    <form action="accesories/wp_news_iframe.php" method="POST">
                                                        <input type="hidden" name="id_del"
                                                            value="<?php echo $rows['id'] ;?>">
                                                        <input type="hidden" name="del_title"
                                                            value="<?php echo $rows['iframe_title'] ;?>">
                                                        <input type="hidden" name="del_iframe"
                                                            value='<?php echo $rows['iframe_iframe'] ;?>'>
                                                        <input type="hidden" name="insert" value="delete">
                                                        <button type="submit"
                                                            class='btn btn-small btn-danger'>Delete</button>
                                                </td>
                                                </form>
                                            </tr>

                                            <?php
                                            $counter_rownss++;
                                                }
                                            ?>




                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="col-lg-12">
                <div class="card shadow-lg  mb-5 bg-white rounded">
                    <div class="card-header bg-info text-light ">
                        <div class="row">
                            <div class="col-lg-9">
                                <h4>Live Schedule</h4>
                            </div>



                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">



                            <div class="container">

                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="small-title">Event Name</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <input id="name" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="small-title">Location</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <input id="addr" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <label class="small-title">Date / Time</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="form-group">
                                            <input id="datetime" type="text" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2 col-lg-offset-2">
                                        <button id="addBtn" class="addBtn btn btn-primary ">
                                            Post
                                        </button>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table id="example" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class=" text-center">SN</th>
                                                    <th class="text-center">Event Name</th>
                                                    <th class=" text-center">Location</th>
                                                    <th class="text-center">Date / Time</th>
                                                    <th class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                            $counter = 1;
                                            unset($table_rows[0]); 
                                            unset($table_rows[1]); 

                                          
                                        
                                            foreach($table_rows as $tr)
                                            {
                                                $json = json_decode($tr['data']);
                                                $rows = json_decode(json_encode($json), true);
                                             
                                             
                                               
                                            
                                        ?>
                                                <tr>
                                                    <td><?php echo $counter ; ?></td>
                                                    <td><?php echo $rows['cells'][0]['data'] ; ?></td>
                                                    <td><?php echo $rows['cells'][1]['data'] ; ?></td>
                                                    <td><?php echo $rows['cells'][2]['data'] ; ?></td>
                                                    <td><button class='btn btn-small btn-danger delBtn'
                                                            data-id="<?php echo $tr['id']; ?> "
                                                            data-event="<?php echo $rows['cells'][0]['data'] ; ?>"
                                                            data-toggle="modal"
                                                            data-location="<?php echo $rows['cells'][1]['data'] ; ?>"
                                                            data-dt="<?php echo $rows['cells'][2]['data'] ; ?>"
                                                            data-target="#exampleModal<?php echo $counter ; ?>">Delete</button>
                                                    </td>
                                                    </td>
                                                </tr>



                                                <?php
                                        $counter++;
                                                
                                            }
                                        ?>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>


    </script>

    <script>
    $(document).ready(function() {
        var i = 1;

        $(".addBtn").click(function() {
            var event = $("#name").val();
            var location_row = $("#addr").val();
            var DatenTime = $("#datetime").val();
            // var markup = "<tr><td>" + i + "</td><td>" + name + "</td><td>" + addr + "</td><td>" +
            //     datetime +
            //     "</td><td><button class='btn btn-small btn-danger delBtn'>Delete</button></td></tr>";
            // $("table tbody").append(markup);
            i++;

            var table_id = 1;

            var data_row = `
            {"cells":
                [
                    {"data":
                        "` + event + `","hidden":false,"type":"text","meta":
                        ["wpdt-tc-000000","wpdt-bc-F9F9F9"]},
                        {"data":"` + location_row + `","hidden":false,"type":"text","meta":
                            ["wpdt-tc-000000","wpdt-bc-F9F9F9"]},
                            {"data":"` + DatenTime + `","hidden":false,"type":"text","meta":
                                ["wpdt-tc-000000","wpdt-bc-F9F9F9"]}]}
            `;


            $.ajax({

                url: "https://nepalnewsbank.com/custom_live_table_api.php",
                method: "POST",
                data: {
                    data_row: data_row,
                    table_id: table_id,

                },
                dataType: "text",
                success: function(data) {

                    $.ajax({

                        url: "accesories/wp_live_table_log.php",
                        method: "POST",
                        data: {
                            event: event,
                            location_row: location_row,
                            DatenTime: DatenTime,
                            action: "Added"

                        },
                        dataType: "text",
                        success: function(data) {



                            location.reload();


                        }
                    });





                }
            });


        });

        $(function() {
            $("table").on("click", ".delBtn", function() {

                var id = $(this).data('id');


                var event = $(this).data('event');
                var location_row = $(this).data('location');
                var DatenTime = $(this).data('dt');

                $.ajax({

                    url: "https://nepalnewsbank.com/custom_live_table_api.php?id=" + id,
                    method: "DELETE",
                    data: {


                    },
                    dataType: "text",
                    success: function(data) {
                        $.ajax({

                            url: "accesories/wp_live_table_log.php",
                            method: "POST",
                            data: {
                                event: event,
                                location_row: location_row,
                                DatenTime: DatenTime,
                                action: "Deleted"

                            },
                            dataType: "text",
                            success: function(data) {



                                location.reload();


                            }
                        });


                    }
                });

            });
        });

    });
    </script>

</body>

</html>