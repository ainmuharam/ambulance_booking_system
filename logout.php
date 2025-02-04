<?php
session_start();

ini_set('session.gc_maxlifetime', 120);
session_set_cookie_params(120); // Set cookie lifetime to 2 minutes

// Clear the session
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 60*60,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
unset($_SESSION['login']);
session_destroy(); // destroy session
header("location:index.php"); 
exit(); // It's a good practice to call exit after header redirection
?>