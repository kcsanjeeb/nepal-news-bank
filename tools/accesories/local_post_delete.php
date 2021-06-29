<?php


include "session_handler/session_service.php";
include "connection.php";
include "nas_function/functions.php";

include "../global/timezone.php";




// -------------- VARIABLE DECLARATION ----------------
// $interview_path = 'my_data/interview_data';
// $news_path = 'my_data/news_data';
// ----------------------------------------------------



// print_r($_POST);

$news_id = $_POST['news_id'] ;
$news_id = mysqli_real_escape_string($connection, $news_id);


$byline = $_POST['byline'] ;
$byline = mysqli_real_escape_string($connection, $byline);
$byline_dir = remove_special_chars($byline);

$date = $_POST['date'] ;
$date = mysqli_real_escape_string($connection, $date);

$type = $_POST['type'] ;
$type = mysqli_real_escape_string($connection, $type);

$dir_path = $_POST['dir_path'] ;
$dir_path = mysqli_real_escape_string($connection, $dir_path);

$query_del_web = "delete from web where newsid = '$news_id'";
$run_query = mysqli_query($connection , $query_del_web);


$query_del_nas = "delete from nas where newsid = '$news_id'";
$run_query = mysqli_query($connection , $query_del_nas);


$interview_path = $dir_path;
$news_path = $dir_path;




if($type == $_SESSION['interview_id'])
{
  $glob_path = $dir_path."/".$byline_dir;
}
else
{
  $glob_path = $dir_path."/".$date."/".$byline_dir;

}

$dir = $glob_path;
$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($it,
             RecursiveIteratorIterator::CHILD_FIRST);
foreach($files as $file) {
    if ($file->isDir()){
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
}
rmdir($dir);

// if($type == $_SESSION['interview_id'])
// {
//   // interview
//   $files = glob('../'.$interview_path.'/'.$byline_dir.'/*'); // get all file names
//   foreach($files as $file){ // iterate files

//     if(end(explode("/" , $file)) == 'bonus_media')
//     {
//       $files_bonus = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/bonus_media'); // get all file names
//       foreach($files_bonus as $fb){

//         if(is_file($fb)) {
//           unlink($fb); // delete file
//         }  

//       }
//       rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir.'/bonus_media');
      
//     }

//     if(end(explode("/" , $file)) == 'audio_bites')
//     {
//       $files_bonus = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/audio_bites'); // get all file names
//       foreach($files_bonus as $fb){

//         if(is_file($fb)) {
//           unlink($fb); // delete file
//         }  

//       }
//       rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir.'/audio_bites');
      
//     }

//     if(end(explode("/" , $file)) == 'gallery')
//     {
//       $files_bonus = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/gallery'); // get all file names
//       foreach($files_bonus as $fb){

//         if(is_file($fb)) {
//           unlink($fb); // delete file
//         }  

//       }
//       rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir.'/gallery');
      
//     }


//     if(is_file($file)) {
//       unlink($file); // delete file
//     }
//   }
//   rmdir('../'.$interview_path.'/'.$byline_dir);
// }
// else
// {
//   $files = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/*'); // get all file names
//   foreach($files as $file){ // iterate files

//     if(end(explode("/" , $file)) == 'bonus_media')
//     {
//       $files_bonus = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/bonus_media/*'); // get all file names
//       foreach($files_bonus as $fb){

//         if(is_file($fb)) {
//           unlink($fb); // delete file
//         }  

//       }
//       rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir.'/bonus_media');
//     }

    
//     if(end(explode("/" , $file)) == 'audio_bites')
//     {
//       $files_bonus = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/audio_bites'); // get all file names
//       foreach($files_bonus as $fb){

//         if(is_file($fb)) {
//           unlink($fb); // delete file
//         }  

//       }
//       rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir.'/audio_bites');
      
//     }

//     if(end(explode("/" , $file)) == 'gallery')
//     {
//       $files_bonus = glob('../'.$news_path.'/'.$date.'/'.$byline_dir.'/gallery'); // get all file names
//       foreach($files_bonus as $fb){

//         if(is_file($fb)) {
//           unlink($fb); // delete file
//         }  

//       }
//       rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir.'/gallery');
      
//     }
    
//     if(is_file($file)) {
//       unlink($file); // delete file
//     }
//   }
//   rmdir('../'.$news_path.'/'.$date.'/'.$byline_dir);
// }


// print_r($files);
if(isset($location))
{
    $location_redirect = $location;
}
else
{
    $location_redirect = '../remotecopycreator.php';
}





header("Location: ".$location_redirect);