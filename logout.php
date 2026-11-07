<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: start.html");
    exit();
?>
