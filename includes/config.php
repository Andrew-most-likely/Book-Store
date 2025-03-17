<?php
// config.php - Include in every file
session_start();

// ========================
// User Authentication
// ========================

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: ../login/login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin()
{
    if (!isAdmin()) {
        header("Location: ../index.php");
        exit();
    }
}

// ========================
// Data Sanitization
// ========================

// Sanitize input data
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ========================
// Message Functions
// ========================

// Display success message
function showSuccess($message)
{
    return '<div class="alert alert-success">' . $message . '</div>';
}

// Display error message
function showError($message)
{
    return '<div class="alert alert-danger">' . $message . '</div>';
}
