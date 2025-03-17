<?php include '../includes/header.php'; ?>
<?php include 'controller.php'; ?>
<link rel="stylesheet" href="style.css">

<div class="container">
    <div class="main-content">
        <h2>Admin Dashboard</h2>

        <!-- ========================= -->
        <!-- Overview Statistics Cards -->
        <!-- ========================= -->
        <div class="row top-custom-margin-bottom-large">
            <!-- Total Books -->
            <div class="column-total-books">
                <div class="card card-total-books">
                    <div class="card-header">
                        <h5 class="card-title">Total Books</h5>
                    </div>
                    <div class="card-body">
                        <h1 class="display-number"><?php echo isset($data['book_count']) ? $data['book_count'] : 'Data not found'; ?></h1>
                    </div>
                </div>
            </div>
            <!-- Total Users -->
            <div class="column-total-users">
                <div class="card card-total-users">
                    <div class="card-header">
                        <h5 class="card-title">Total Users</h5>
                    </div>
                    <div class="card-body">
                        <h1 class="display-number"><?php echo isset($data['user_count']) ? $data['user_count'] : 'Data not found'; ?></h1>
                    </div>
                </div>
            </div>
            <!-- Total Reviews -->
            <div class="column-total-reviews">
                <div class="card card-total-reviews">
                    <div class="card-header">
                        <h5 class="card-title">Total Reviews</h5>
                    </div>
                    <div class="card-body">
                        <h1 class="display-number"><?php echo isset($data['review_count']) ? $data['review_count'] : 'Data not found'; ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row custom-margin-bottom-large">
            <div class="column-left">
                <!-- ========================= -->
                <!-- Most Popular Book Section -->
                <!-- ========================= -->
                <div class="card card-popular-book">
                    <div class="card-header">
                        <h5>Most Popular Book</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($data['most_popular']): ?>
                            <h5><?php echo htmlspecialchars($data['most_popular']['title']); ?></h5>
                            <p>Author: <?php echo htmlspecialchars($data['most_popular']['author']); ?></p>
                            <p>Average Rating: <?php echo number_format($data['most_popular']['avg_rating'], 1); ?></p>
                        <?php else: ?>
                            <p>No reviews yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ========================= -->
                <!-- Reports Section -->
                <!-- ========================= -->
                <div class="card card-reports">
                    <div class="card-header">
                        <h5 class="mb-0">Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="reports.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-book mr-2"></i> Inventory Report - Shows a listing of all books
                            </a>
                            <a href="reports.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-chart-line mr-2"></i> Popularity Report - Shows books and their average ratings
                            </a>
                            <a href="reports.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-users mr-2"></i> User Activity Report - Shows user engagement statistics
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="column-right">
                <!-- ========================= -->
                <!-- Latest Reviews Section -->
                <!-- ========================= -->
                <div class="card card-recent-reviews">
                    <div class="card-header">
                        <h5>Latest Reviews</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($data['latest_reviews'] as $review): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($review['username']); ?></strong> rated
                                    <em><?php echo htmlspecialchars($review['book_title']); ?></em>
                                    <span class="badge"><?php echo $review['rating']; ?> stars</span>
                                    <br><small><?php echo $review['created_at']; ?></small>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($data['latest_reviews'])): ?>
                                <p>No recent reviews.</p>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
