<?php


function remove_special_chars($byline)
{
    // \ 
    $special_chars = array("<", ">", ":" , "/",  "|", "?", "*" , '"' , ',') ;
    $clean_byline = str_replace($special_chars, "", $byline) ;
    return $clean_byline ;
}

function getId($table)
{
    include "connection.php";

    
    if($table == 'archive_video')
    {
        $sql_news_code = "SELECT FLOOR(RAND() * 99999) AS new_id
        FROM archive_video 
        WHERE 'archive_id' NOT IN (SELECT archive_id FROM archive_video)
        LIMIT 1;";
    }

    if($table == 'archive_photos')
    {
        $sql_news_code = "SELECT FLOOR(RAND() * 99999) AS new_id
        FROM archive_photos 
        WHERE 'archive_id' NOT IN (SELECT archive_id FROM archive_photos)
        LIMIT 1;";
    }

    if($table == 'nas')
    {
        $sql_news_code = "SELECT FLOOR(RAND() * 99999) AS new_id
        FROM nas 
        WHERE 'newsid' NOT IN (SELECT newsid FROM nas)
        LIMIT 1;";
    }

    if($table == 'archives')
    {
        $sql_news_code = "SELECT FLOOR(RAND() * 99999) AS new_id
        FROM archives 
        WHERE 'archive_id' NOT IN (SELECT archive_id FROM archives)
        LIMIT 1;";
    }
   


    $run_query = mysqli_query($connection,$sql_news_code);
    $row = mysqli_fetch_assoc($run_query);
    $new_seed = $row['new_id'];

    if(isset($new_seed))
    {
        $new_seed = $row['new_id'];
    }
    else
    {
        $new_seed = rand(11111,99999);
    }


    return $new_seed ;

}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

function ftp_remote($folder  , $sourceName )
{
    include "environment/ftp.php";

    /*

    1. Open
    2. Close
    3. Process
    */


        $ftp = ftp_connect("$ftp_url");
        ftp_login($ftp, "$ftp_username", "$ftp_password");
        ftp_pasv($ftp, true);



    $file_status = ftp_put($ftp, "/$folder", "$sourceName", FTP_BINARY);
    
    


    $ftp = ftp_connect("$ftp_url");
    ftp_login($ftp, "$ftp_username", "$ftp_password");
    ftp_pasv($ftp, true);
    $file_status = ftp_put($ftp, "/$folder", "$sourceName", FTP_BINARY); 
    ftp_close($ftp); 



    if(isset($file_status))
    {
        return 1 ;
    }
    else
    {
        return 0 ;
    }

    
}

function ftp_delete_rem($dir , $type)
{
    include "environment/ftp.php";
    $ftp = ftp_connect("$ftp_url");
    ftp_login($ftp, "$ftp_username", "$ftp_password");
    ftp_pasv($ftp, true);

    if($type == 'folder')
    {
        if(ftp_rmdir($ftp, $dir))
        {
           
            ftp_close($ftp);
            return 1;
        }
        else
        {
            
            ftp_close($ftp);
            return 0 ;
        }
    
       
    }
    else
    {
            if (ftp_delete($ftp, $dir))
            {
                
                 ftp_close($ftp);
                 return 1 ;
            }
            else
            {
               
                ftp_close($ftp);
                return 0;
            }
    }
   
    
    ftp_close($ftp);
   


    return 0 ;

}


function wp_token()
{
    include "environment/wp_api_env.php";
    $url = "$domain_url/wp-json/aam/v1/authenticate";

    $payloadName = array('username' => $wp_api_username, 'password' => $wp_api_password );
    $unpws = json_encode($payloadName);
    $curl = curl_init();
    curl_setopt_array($curl, array(                    
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $unpws ,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json",
            
        ),
    ));


    $response = curl_exec($curl);
    $response = json_decode($response);
    $response = json_decode(json_encode($response) , true);
    $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);                    
    curl_close($curl);

    $cookie_name = "wptoken";
    $cookie_value = $response['jwt']['token'];
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

    return $respCode ;
}

function ftp_login_nas($username, $password)
{
    include "environment/ftp.php";
    $ftp_server = "$ftp_url";

    $ftp_user = $username."@$ftp_un_suffix";
    $ftp_pass = $password;

    // set up a connection or die
    $conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 

    // try to login
    if (@ftp_login($conn_id, $ftp_user, $ftp_pass)) {
        // echo "Connected as $ftp_user@$ftp_server\n";
        $response = 1 ;
    } else {
        // echo "Couldn't connect as $ftp_user\n";
        $response = 0 ;
    }

    // close the connection
    ftp_close($conn_id);
    
    return $response ;
}
