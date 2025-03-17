<?php
// controller.php

$page_title = "Admin Dashboard";

require_once '../includes/config.php';
require_once '../includes/db.php';

// Ensure the user is an admin before proceeding
requireAdmin();

$database = new Database();
$db = $database->connect();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// =========================
// Get basic statistics
// =========================

// Get total number of books
$query = "SELECT COUNT(*) as book_count FROM books";
$stmt = $db->prepare($query);
$stmt->execute();
$book_count = $stmt->fetch(PDO::FETCH_ASSOC)['book_count'];

// Get total number of users (customers only)
$query = "SELECT COUNT(*) as user_count FROM users WHERE role = 'customer'";
$stmt = $db->prepare($query);
$stmt->execute();
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// Get total number of reviews
$query = "SELECT COUNT(*) as review_count FROM reviews";
$stmt = $db->prepare($query);
$stmt->execute();
$review_count = $stmt->fetch(PDO::FETCH_ASSOC)['review_count'];

// =========================
// Get additional insights
// =========================

// Find the highest-rated book
$query = "SELECT b.title, b.author, AVG(r.rating) as avg_rating 
          FROM books b 
          JOIN reviews r ON b.book_id = r.book_id 
          GROUP BY b.book_id 
          ORDER BY avg_rating DESC 
          LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$most_popular = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the most recent reviews
$query = "SELECT r.title, r.rating, r.created_at, u.username, b.title as book_title 
          FROM reviews r 
          JOIN users u ON r.user_id = u.user_id 
          JOIN books b ON r.book_id = b.book_id 
          ORDER BY r.created_at DESC 
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$latest_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================
// Prepare data for the view
// =========================
$data = [
    'book_count' => $book_count,
    'user_count' => $user_count,
    'review_count' => $review_count,
    'most_popular' => $most_popular,
    'latest_reviews' => $latest_reviews
];
