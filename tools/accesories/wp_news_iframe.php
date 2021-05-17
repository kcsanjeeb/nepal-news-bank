<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/wp_api_env.php";
include "nas_function/functions.php";
date_default_timezone_set("Asia/Kathmandu");


if($_POST['insert'] == 'insert')
{
    $title = mysqli_real_escape_string($connection,$_POST['title']);
    $iframe = mysqli_real_escape_string($connection,$_POST['iframe']);
    $iframe_text = mysqli_real_escape_string($connection,$_POST['iframe_text']);

    $iframe_text = str_replace("<","&lt;",$iframe_text);
    $iframe_text = str_replace(">","&gt;",$iframe_text);
    
    
    $query = "insert into iframe(
        iframe_title , iframe_iframe ,  iframe_text 
      
        ) 
        VALUES 
        ('$title', '$iframe' ,  '$iframe_text' )"; 

    $run_query = mysqli_query($connection , $query);



}

if($_POST['insert'] == 'delete')
{
    $id_del = mysqli_real_escape_string($connection,$_POST['id_del']);     
    
    $query = "delete from iframe  where id = '$id_del' ;"; 
    $run_query = mysqli_query($connection , $query); 

}

$sql_iframe = "select * from iframe ";
$run_sql_iframe= mysqli_query($connection, $sql_iframe);

$arrays_rows = array();

while($rows = mysqli_fetch_assoc($run_sql_iframe))
{
    $title =  $rows['iframe_title'] ;
    $iframe_iframe =  $rows['iframe_iframe'] ;
    $iframe_text =  $rows['iframe_text'] ;

    // $iframe_text = str_replace("<","&lt;",$iframe_iframe);
    // $iframe_text = str_replace(">","%gt;;",$iframe_iframe);

    $new_plans['_rtbs_title'] = $title;
    $new_plans['_rtbs_content'] = $iframe_iframe.'<br>'.$iframe_text ;

    array_push($arrays_rows , $new_plans);


}




$json_rows = json_encode($arrays_rows);

$post = [
    'rows_json' => $json_rows,
   
];

$ch = curl_init("$domain_url/custom_live_tabs_api.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$response = curl_exec($ch);

curl_close($ch);

// // print_r($post);
// echo $response ;

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = $_SERVER['HTTP_REFERER'];
}


header("Location: ". $location_redirect);