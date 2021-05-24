<?php
include "session_handler/session_service.php";
include "../global/timezone.php";
$event = $_POST['event'];  
$location_row =$_POST['location_row']; 
$DatenTime =$_POST['DatenTime'];   
$action =$_POST['action']; 


$myfile = fopen("../log/live_log.txt", "a") or die("Unable to open file!");  
fwrite($myfile, "\n--------------- Table: $event  ------------------ \n");

$logged_date = date('Y-m-d H:i:s');
fwrite($myfile, "\n Logged Date: $logged_date \n");

$text = "Event:  $event\n";
fwrite($myfile, $text);
$text = "Location:  ".$location_row."\n";
fwrite($myfile, $text);
$text = "Date Time:  ".$DatenTime."\n";
fwrite($myfile, $text);
$text = "$event $action Succesfully From Live Table\n";
fwrite($myfile, $text);   


  
fwrite($myfile, "------------------------------------------------------- "); 
fclose($myfile);



?>