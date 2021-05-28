

<?php

    if(basename($_SERVER['PHP_SELF'])=="newscollector.php") 
    { 
        $newscollector_page = "active";
    }
    else
    {
        $newscollector_page = "";
    }

    if(basename($_SERVER['PHP_SELF'])=="remotecopycreator.php") 
    { 
        $remotecopy_page = "active";
    }
    else
    {
        $remotecopy_page = "";
    }

    if(basename($_SERVER['PHP_SELF'])=="archivefootage.php") 
    { 
        $archivefootage_page = "active";
    }
    else
    {
        $archivefootage_page = "";
    }

    if(basename($_SERVER['PHP_SELF'])=="archivepicture.php") 
    { 
        $archivepicture_page = "active";
    }
    else
    {
        $archivepicture_page = "";
    }

    if(basename($_SERVER['PHP_SELF'])=="newsticker.php") 
    { 
        $newsticker_page = "active";
    }
    else
    {
        $newsticker_page = "";
    }

    if(basename($_SERVER['PHP_SELF'])=="livetext.php") 
    { 
        $livetext_page = "active";
    }
    else
    {
        $livetext_page = "";
    }

    if(basename($_SERVER['PHP_SELF'])=="archive.php") 
    { 
        $archive_page = "active";
    }
    else
    {
        $archive_page = "";
    }

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary  " style="background-color:#17a2b8 !important;  display: flex;
justify-content: space-around; color:#fff; font-size:25px; font-weight:600">
<!-- <a class="navbar-brand" style="text-align:center" href="#"> -->
    NEPAL NEWS BANK DASHBOARD
<!-- </a> -->
<br>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="background-color:#1a051d !important;">

<div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
        <li class="nav-item <?php echo $newscollector_page ; ?>">
            <a class="nav-link mr-3"
                href="newscollector.php">News-Collector
            </a>
        </li>
       
        <li class="nav-item <?php echo $remotecopy_page ; ?>">
            <a class="nav-link mr-3"
                href="remotecopycreator.php">Remote-Copy-Creator</a>
        </li>
     
        <li class="nav-item <?php echo $archivefootage_page ; ?>" >
            <a class="nav-link mr-3"
                href="archivefootage.php">Archive-Video-Creator</a>
        </li>
        <li class="nav-item <?php echo $archivepicture_page ; ?>">
            <a class="nav-link mr-3"
                href="archivepicture.php">Archive-Picture-Creator</a>
        </li>

        <li class="nav-item <?php echo $archive_page ; ?>">
            <a class="nav-link mr-3"
                href="archivepicture.php">Archives</a>
        </li>

        <li class="nav-item <?php echo $newsticker_page ; ?>">
            <a class="nav-link mr-3"
                href="newsticker.php">Ticker</a>
        </li>
        <li class="nav-item <?php echo $livetext_page ; ?>">
            <a class="nav-link mr-3"
                href="livetext.php">Live</a>
        </li>

    </ul>

</div>

<div class="form-inline">


    <div style="color:white">  
    User:   <?php echo $_SESSION['fuser']; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <form action="accesories/logout.php" method="POST">
        <button class="btn btn-outline-light my-2 my-sm-0" type="submit" name="logout">Logout</button>
    </form>


</div>
</nav>