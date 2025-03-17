<style>
  /* Base styling */
  body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    color: #333;
  }

  .main-content {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  /* Breadcrumb navigation */
  .breadcrumb {
    background-color: #f0f0f0;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
  }

  .breadcrumb-item {
    display: inline;
    margin-right: 5px;
  }

  .breadcrumb-item a {
    color: #0066cc;
    text-decoration: none;
  }

  /* Book details section */
  .book-details-box {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
  }

  .book-title-author {
    flex: 1;
    min-width: 300px;
    margin-right: 20px;
  }

  .book-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
  }

  .book-author {
    font-size: 16px;
    color: #666;
  }

  .book-info {
    flex: 1;
    min-width: 150px;
    display: flex;
    gap: 20px;
    margin-right: 20px;
  }

  .book-info p {
    margin: 0;
  }

  .rating-summary {
    flex: 1;
    min-width: 150px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Star ratings */
  .star-rating i {
    color: #f5c518;
    margin-right: 2px;
  }

  /* Alerts */
  .alert {
    padding: 10px;
    border-radius: 5px;
    margin: 15px 0;
  }

  .alert-success {
    background-color: #d4edda;
    color: #155724;
  }

  .alert-danger {
    background-color: #f8d7da;
    color: #721c24;
  }

  .alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
  }

  /* Review form */
  .review-form-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    margin: 20px 0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .form-group {
    margin-bottom: 15px;
  }

  .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
  }

  .form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }

  .btn-primary {
    background-color: #0066cc;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .btn-primary:hover {
    background-color: #0052a3;
  }

  /* Review cards */
  .review-card {
    background-color: #fff;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    flex-wrap: wrap;
  }

  .review-author {
    font-size: 14px;
    color: #666;
  }

  .review-body p {
    margin-top: 10px;
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php
$page_title = "Book Details";
require_once '../includes/config.php';
require_once '../includes/db.php';
include '../includes/header.php';
// book id check
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: books.php");
  exit();
}

$book_id = intval($_GET['id']);

$database = new Database();
$db = $database->connect();

// book info grab
$query = "SELECT * FROM books WHERE book_id = :book_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':book_id', $book_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
  header("Location: books.php");
  exit();
}

$book = $stmt->fetch(PDO::FETCH_ASSOC);

// book review grab
$query = "SELECT r.*, u.username 
            FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.book_id = :book_id 
            ORDER BY r.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':book_id', $book_id);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

//rate avg
$avg_rating = 0;
$review_count = count($reviews);
if ($review_count > 0) {
  $total_rating = 0;
  foreach ($reviews as $review) {
    $total_rating += $review['rating'];
  }
  $avg_rating = round($total_rating / $review_count, 1);
}

//gotta check for double reviews or the page will explode
$user_has_reviewed = false;
$user_review = null;
if (isLoggedIn()) {
  $query = "SELECT * FROM reviews WHERE user_id = :user_id AND book_id = :book_id";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':user_id', $_SESSION['user_id']);
  $stmt->bindParam(':book_id', $book_id);
  $stmt->execute();

  if ($stmt->rowCount() > 0) {
    $user_has_reviewed = true;
    $user_review = $stmt->fetch(PDO::FETCH_ASSOC);
  }
}

//rvw procc
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn()) {
  $title = sanitize($_POST['title']);
  $description = sanitize($_POST['description']);
  $rating = intval($_POST['rating']);

  if (empty($title) || empty($description) || $rating < 1 || $rating > 5) {
    $error = "All fields are required and rating must be between 1 and 5";
  } else {
    if ($user_has_reviewed) {
      // update review
      $query = "UPDATE reviews SET title = :title, description = :description, rating = :rating WHERE review_id = :review_id";
      $stmt = $db->prepare($query);
      $stmt->bindParam(':title', $title);
      $stmt->bindParam(':description', $description);
      $stmt->bindParam(':rating', $rating);
      $stmt->bindParam(':review_id', $user_review['review_id']);

      if ($stmt->execute()) {
        $success = "Your review has been updated!";
        header("Location: book_details.php?id=$book_id&success=updated");
        exit();
      } else {
        $error = "Something went wrong. Please try again.";
      }
    } else {
      //prep new review
      $query = "INSERT INTO reviews (user_id, book_id, title, description, rating) VALUES (:user_id, :book_id, :title, :description, :rating)";
      $stmt = $db->prepare($query);
      $stmt->bindParam(':user_id', $_SESSION['user_id']);
      $stmt->bindParam(':book_id', $book_id);
      $stmt->bindParam(':title', $title);
      $stmt->bindParam(':description', $description);
      $stmt->bindParam(':rating', $rating);

      if ($stmt->execute()) {
        $success = "Your review has been submitted!";
        header("Location: book_details.php?id=$book_id&success=added");
        exit();
      } else {
        $error = "Something went wrong. Please try again.";
      }
    }
  }
}

//Handly little url ++
if (isset($_GET['success'])) {
  if ($_GET['success'] == 'added') {
    $success = "Your review has been submitted!";
  } elseif ($_GET['success'] == 'updated') {
    $success = "Your review has been updated!";
  }
}
?>



<div class="main-content">
  <div class="book-details-container">
    <div class="file-tree-breadcrumb">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <i class="fas fa-folder"></i> <a href="books.php">Books</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-file"></i> <?php echo $book['title']; ?>
          </li>
        </ol>
      </nav>
    </div>


    <div class="book-details-box">
      <div class="book-title"><?php echo $book['title']; ?></div>
      <div class="book-author">by <?php echo $book['author']; ?></div>
      <div class="book-info">
        <p><strong>Genre:</strong> <?php echo $book['genre']; ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
      </div>
      <div class="rating-summary">
        <h5><strong>Average Rating:</strong></h5>
        <div class="star-rating">

<!-- rounding algo for stars cause the font cant handle anything othr than 1 or .5 like deadass what -->
          <?php
          $avg_rating = round($avg_rating, 1);
          for ($i = 1; $i <= 5; $i++) {
            if ($i <= $avg_rating) {
              echo '<i class="fas fa-star"></i>';
            } elseif ($i - 0.5 <= $avg_rating) {
              echo '<i class="fas fa-star-half-alt"></i>';
            } else {
              echo '<i class="far fa-star"></i>';
            }
          }
          ?>
        </div>
        <span class="rating-value"><?php echo $avg_rating; ?> (<?php echo $review_count; ?> review<?php echo $review_count == 1 ? '' : 's'; ?>)</span>
      </div>
    </div>

    <hr>

    <!-- debug msg-->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Review Form -->
    <?php if (isLoggedIn()): ?>
      <div class="review-form-container">
        <h5 class="review-form-title"><?php echo $user_has_reviewed ? 'Update Your Review' : 'Write a Review'; ?></h5>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $book_id); ?>">
          <div class="form-group">
            <label for="title">Review Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo $user_has_reviewed ? $user_review['title'] : ''; ?>" required>
          </div>
          <div class="form-group">
            <label for="rating">Rating:</label>
            <select class="form-control" id="rating" name="rating" required>
              <option value="">Select a rating</option>
              <option value="5" <?php echo ($user_has_reviewed && $user_review['rating'] == 5) ? 'selected' : ''; ?>>5 - Excellent</option>
              <option value="4" <?php echo ($user_has_reviewed && $user_review['rating'] == 4) ? 'selected' : ''; ?>>4 - Very Good</option>
              <option value="3" <?php echo ($user_has_reviewed && $user_review['rating'] == 3) ? 'selected' : ''; ?>>3 - Good</option>
              <option value="2" <?php echo ($user_has_reviewed && $user_review['rating'] == 2) ? 'selected' : ''; ?>>2 - Fair</option>
              <option value="1" <?php echo ($user_has_reviewed && $user_review['rating'] == 1) ? 'selected' : ''; ?>>1 - Poor</option>
            </select>
          </div>
          <div class="form-group">
            <label for="description">Review:</label>
            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $user_has_reviewed ? $user_review['description'] : ''; ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary"><?php echo $user_has_reviewed ? 'Update Review' : 'Submit Review'; ?></button>
        </form>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Please <a href="../login/login.php">login</a> to write a review.</div>
    <?php endif; ?>

    <!-- Reviews-->
    <h3>Reviews</h3>

    <?php if ($review_count > 0): ?>
      <?php foreach ($reviews as $review): ?>
        <div class="review-card">
          <div class="review-header">
            <strong><?php echo $review['title']; ?></strong>
            <span class="review-rating">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if ($i <= $review['rating']): ?>
                  <i class="fas fa-star"></i>
                <?php else: ?>
                  <i class="far fa-star"></i>
                <?php endif; ?>
              <?php endfor; ?>
            </span>
            <div class="review-author"><?php echo $review['username']; ?> on <?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
          </div>
          <div class="review-body">
            <p><?php echo nl2br($review['description']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info">No reviews yet. Be the first to review this book!</div>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>