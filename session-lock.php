<?php
date_default_timezone_set('Asia/Kolkata');
session_start();
if(!isset($_SESSION['PH_USER_ID']))
{
    header("Location: ../index.php");
}


if (isset($_SESSION['last_activity']) && !isset($_SESSION['session_expires'])) {
    // Get the current time
    $currentTime = time();

    $lastActivityTime = $_SESSION['last_activity'];

    $expirationTime = strtotime('today midnight');

    if ($currentTime > $expirationTime) {
        session_unset();
        session_destroy();

        // Set the session_expires flag to prevent repeated destruction
        $_SESSION['session_expires'] = true;

        //echo "expires";
        
        header("Location: ../index.php");
    }
}

// Update the last activity time in the session
$_SESSION['last_activity'] = time();

//echo date('H:i:s');
//echo $_SESSION['last_activity'];
?>