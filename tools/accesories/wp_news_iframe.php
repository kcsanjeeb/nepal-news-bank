<?php

include "session_handler/session_service.php";
include "connection.php";
include "environment/ftp.php";
include "environment/wp_api_env.php";
include "nas_function/functions.php";
include "../global/timezone.php";




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

    $myfile = fopen("../log/live_log.txt", "a") or die("Unable to open file!");  
    fwrite($myfile, "\n--------------- $title ------------------ \n");

    $text = "Title:  $title\n";
    fwrite($myfile, $text);
    $text = "Iframe:  ".$iframe."\n";
    fwrite($myfile, $text);
    $text = "Live Iframe Added Succesfully \n";
    fwrite($myfile, $text);   

    
    fwrite($myfile, "------------------------------------------------------- "); 
    fclose($myfile);



}

if($_POST['insert'] == 'delete')
{
    $id_del = mysqli_real_escape_string($connection,$_POST['id_del']);   
    
    $del_title = mysqli_real_escape_string($connection,$_POST['del_title']);  
    $del_iframe = mysqli_real_escape_string($connection,$_POST['del_iframe']);  
    
    $query = "delete from iframe  where id = '$id_del' ;"; 
    $run_query = mysqli_query($connection , $query); 


    $myfile = fopen("../log/live_log.txt", "a") or die("Unable to open file!");  
    fwrite($myfile, "\n--------------- Iframe: $del_title ------------------ \n");

    $text = "Title:  $del_title\n";
    fwrite($myfile, $text);
    $text = "Iframe:  ".$del_iframe."\n";
    fwrite($myfile, $text);
    $text = "Live Iframe Deleted Succesfully \n";
    fwrite($myfile, $text);   

    
    fwrite($myfile, "------------------------------------------------------- "); 
    fclose($myfile);

}





$sql_iframe = "select * from iframe ";
$run_sql_iframe= mysqli_query($connection, $sql_iframe);

$arrays_rows = array();

while($rows = mysqli_fetch_assoc($run_sql_iframe))
{
    $title =  $rows['iframe_title'] ;
    $iframe_iframe =  $rows['iframe_iframe'] ;
    $iframe_text =  $rows['iframe_text'] ;

    $iframe_iframe = str_replace("<","*1*",$iframe_iframe);
    $iframe_iframe = str_replace(">","*2*",$iframe_iframe);

    $new_plans['_rtbs_title'] = $title;
    $new_plans['_rtbs_content'] = $iframe_iframe.'<br><div id="copy-text-iframe">'.$iframe_text.'</div><a href="https://nepalnewsbank.com/live/" id="hookup-button" class="button-background button-background--primary button-background--small">Go to hookup page</a>' ;

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
$respCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


curl_close($ch);


echo "esponce code: ".$respCode ;


echo "<br>esponce code: ".$response ;

if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = $_SERVER['HTTP_REFERER'];
}


header("Location: ". $location_redirect);