<style>
    /* General Layout */
    .main-content {
        padding: 20px;
        max-width: 1200px;
        margin: auto;
        background-color: #f4f7f6;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .section-title {
        font-size: 26px;
        font-weight: bold;
        margin-bottom: 30px;
        color: #333;
    }



    .search-form {
        display: flex;
        gap: 15px;
        align-items: center;
        justify-content: center;
    }

    .search-dropdown,
    .search-input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        width: 100%;
        max-width: 300px;
    }

    .search-input::placeholder {
        color: #bbb;
    }

    .button {
        background-color: #007bff;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-transform: uppercase;
        font-weight: 600;
    }

    .button:hover {
        background-color: #0056b3;
    }

    .button-secondary {
        background-color: #6c757d;
    }

    .button-secondary:hover {
        background-color: #5a6268;
    }

    .button-outline {
        border: 1px solid #007bff;
        color: rgb(255, 255, 255);
        text-decoration: none;
    }

    .button-outline:hover {
        background-color: #007bff;
        color: white;
    }

    /* Grid Layout for Books */
    .book-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }

    .book-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .book-card:hover {
        transform: translateY(-10px);
    }

    .book-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .book-author {
        font-size: 16px;
        color: #555;
        margin-bottom: 10px;
    }

    .book-genre {
        font-size: 14px;
        color: #777;
        margin-bottom: 10px;
    }

    .book-price {
        font-size: 16px;
        font-weight: bold;
        color: #28a745;
        margin-bottom: 10px;
    }

    .book-rating {
        font-size: 14px;
        margin-bottom: 10px;
        color: #f39c12;
    }

    .icon-star {
        font-size: 1.2em;
        margin-right: 3px;
        font-style: normal;
    }

    .icon-star.filled::before {
        content: "\f005";
        /* Solid star */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #f39c12;
    }

    .icon-star.half::before {
        content: "\f123";
        /* Half star */
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #f39c12;
    }

    .icon-star.empty::before {
        content: "\f006";
        /* Regular (empty) star */
        font-family: "Font Awesome 6 Free";
        font-weight: 400;
        color: #ddd;
    }

    .book-reviews {
        font-size: 14px;
        color: #555;
    }

    /* Alert Message */
    .alert-message {
        padding: 20px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 6px;
        margin-top: 30px;
        text-align: center;
    }

    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        .search-form {
            flex-direction: column;
            align-items: stretch;
        }

        .search-dropdown,
        .search-input,
        .button {
            width: 100%;
            max-width: 100%;
            margin-bottom: 15px;
        }

        .book-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }
</style>

<!-- For my lovly stars -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
$page_title = "Browse Books";
require_once '../includes/config.php';
require_once '../includes/db.php';
include '../includes/header.php';

$database = new Database();
$db = $database->connect();

//Search
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$search_field = isset($_GET['field']) ? sanitize($_GET['field']) : 'title';

//qry prep :P
$query = "SELECT b.*, AVG(r.rating) as avg_rating, COUNT(r.review_id) as review_count 
            FROM books b 
            LEFT JOIN reviews r ON b.book_id = r.book_id";

$params = array();

if (!empty($search)) {
    $query .= " WHERE b." . $search_field . " LIKE :search";
    $params[':search'] = "%" . $search . "%";
}

$query .= " GROUP BY b.book_id ORDER BY b.title ASC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <h2 class="section-title">Browse Books</h2>

    <div class="search-card">
        <div class="search-card-body">
            <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="search-form">
                <div class="search-group">
                    <select name="field" class="search-dropdown">
                        <option value="title" <?php echo $search_field == 'title' ? 'selected' : ''; ?>>Title</option>
                        <option value="author" <?php echo $search_field == 'author' ? 'selected' : ''; ?>>Author</option>
                        <option value="genre" <?php echo $search_field == 'genre' ? 'selected' : ''; ?>>Genre</option>
                    </select>
                </div>
                <div class="search-group">
                    <input type="text" class="search-input" name="search" placeholder="Search..." value="<?php echo $search; ?>">
                </div>
                <button type="submit" class="button">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="books.php" class="button button-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (count($books) > 0): ?>
        <div class="book-grid">
            <?php foreach ($books as $book): ?>
                <div class="book-card">

                    <div class="book-card-body">
                        <h5 class="book-title"><?php echo $book['title']; ?></h5>
                        <h6 class="book-author"><?php echo $book['author']; ?></h6>
                        <p class="book-genre">Genre: <?php echo $book['genre']; ?></p>
                        <p class="book-price"><strong>$<?php echo number_format($book['price'], 2); ?></strong></p>
                        <p class="book-rating">
                            <?php
                            $rating = round($book['avg_rating'], 1);
                            echo 'Rating: ';
                            if ($rating) {
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="icon-star filled"></i>';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<i class="icon-star half"></i>';
                                    } else {
                                        echo '<i class="icon-star empty"></i>';
                                    }
                                }
                                echo " (" . $rating . ")";
                            } else {
                                echo "No ratings yet";
                            }
                            ?>
                        </p>
                        <p class="book-reviews"><?php echo $book['review_count']; ?> review's</p>
                        <a href="book_details.php?id=<?php echo $book['book_id']; ?>" class="button button-outline">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert-message">No books found. Please try a different search.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>