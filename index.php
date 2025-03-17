<style>
    .container {
        margin: 0 auto;
        padding: 20px;
    }

    .hero-section {
        background-color: #f8f9fa;
        text-align: center;
    }

    .hero-title {
        font-size: 2.5em;
        margin-bottom: 10px;
    }

    .hero-subtitle {
        font-size: 1.2em;
        color: #6c757d;
    }

    .hero-divider {
        width: 50%;
        margin: 20px auto;
        border: 1px solid #dee2e6;
    }

    .hero-text {
        font-size: 1em;
        color: #343a40;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }

    .button:hover {
        background-color: #0056b3;
    }

    .button-large {
        font-size: 1.2em;
    }

    .button-small {
        font-size: 0.9em;
    }

    .main-content {
        background: #f7f7f7;
        margin: 20px;
        border-radius: 25px;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .content-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;
    }

    .content-column {
        flex: 1;
        min-width: 300px;
        max-width: 48%;
    }

    .section-title {
        font-size: 1.8em;
        margin-bottom: 20px;
    }

    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .book-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.3s ease-in-out;
    }

    .book-card:hover {
        transform: scale(1.05);
    }

    .book-card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .book-title {
        font-size: 1.2em;
        font-weight: bold;
    }

    .book-author {
        font-size: 1em;
        color: #6c757d;
    }

    .book-genre {
        font-size: 0.9em;
        color: #495057;
    }

    .book-price {
        font-size: 1.1em;
        color: #28a745;
        font-weight: bold;
    }

    .book-rating {
        font-size: 1em;
        color: #ffcc00;
        margin-bottom: 10px;
    }

    .icon-star {
        font-size: 1.2em;
        margin-right: 3px;
        font-style: normal;
    }

    .icon-star.filled::before {
        content: "\f005";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #ffcc00;
    }

    .icon-star.half::before {
        content: "\f123";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #ffcc00;
    }

    .icon-star.empty::before {
        content: "\f006";
        font-family: "Font Awesome 6 Free";
        font-weight: 400;
        color: #ddd;
    }

    .main-content {
        padding: 20px;
        max-width: 1200px;
        margin: auto;
        background-color: #f4f7f6;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
include 'includes/header.php';

$database = new Database();
$db = $database->connect();

//pulls newest book
$query = "SELECT * FROM books ORDER BY created_at DESC LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$newest_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

//ppulls highest rated book
$query = "SELECT b.*, AVG(r.rating) as avg_rating 
            FROM books b 
            LEFT JOIN reviews r ON b.book_id = r.book_id 
            GROUP BY b.book_id 
            ORDER BY avg_rating DESC 
            LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$popular_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Welcome to the Online Bookstore</h1>
        <p class="hero-subtitle">Browse our collection of books and share your reviews.</p>
        <hr class="hero-divider">
        <p class="hero-text">Find your next favorite book today!</p>
        <a class="button button-large" href="pages/books.php" role="button">Browse Books</a>
    </div>
</div>

<div class="main-content">
    <div class="content-row">
        <div class="content-column">
            <h2 class="section-title">Latest Books</h2>
            <div class="book-grid">
                <?php foreach ($newest_books as $book): ?>
                    <div class="book-card">
                        <div class="book-card-body">
                            <h5 class="book-title"><?php echo $book['title']; ?></h5>
                            <h6 class="book-author"><?php echo $book['author']; ?></h6>
                            <p class="book-genre">Genre: <?php echo $book['genre']; ?></p>
                            <p class="book-price"><strong>$<?php echo number_format($book['price'], 2); ?></strong></p>
                            <a href="pages/book_details.php?id=<?php echo $book['book_id']; ?>" class="button button-small">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="content-column">
            <h2 class="section-title">Popular Books</h2>
            <div class="book-grid">
                <?php foreach ($popular_books as $book): ?>
                    <div class="book-card">
                        <div class="book-card-body">
                            <h5 class="book-title"><?php echo $book['title']; ?></h5>
                            <h6 class="book-author"><?php echo $book['author']; ?></h6>
                            <p class="book-genre">Genre: <?php echo $book['genre']; ?></p>
                            <p class="book-rating">
                                <?php
                                $rating = round($book['avg_rating'], 1);
                                echo 'Rating: ';
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="icon-star filled"></i>';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<i class="icon-star half"></i>';
                                    } else {
                                        echo '<i class="icon-star empty"></i>';
                                    }
                                }
                                echo " (" . ($rating ?: 'No ratings') . ")";
                                ?>
                            </p>
                            <a href="pages/book_details.php?id=<?php echo $book['book_id']; ?>" class="button button-small">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>