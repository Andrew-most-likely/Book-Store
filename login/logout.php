<?php
require_once '../includes/config.php';

// clear session
$_SESSION = array();

// KILL SESSION
session_destroy();

// login redirect
header("Location: ../login/login.php");
exit();
