<?php include '../includes/header.php'; ?>
<?php include 'controller.php'; ?>
<link rel="stylesheet" href="style.css">

<div class="main-content">
    <h2>Reports</h2>

    <!-- Navigation buttons for quick section access -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="btn-group" role="group">
                <a href="#inventory" class="btn btn-primary">Inventory Report</a>
                <a href="#popularity" class="btn btn-success">Popularity Report</a>
                <a href="#users" class="btn btn-info">User Activity Report</a>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- Inventory Report Section -->
    <!-- ========================= -->
    <div id="inventory" class="mb-5">
        <div class="card">
            <div class="card-header">
                <h5>Inventory Report - Listing of All Books</h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    // Fetch all books (excluding publication year)
                    $query = "SELECT title, author, genre, price FROM books ORDER BY title";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($books):
                ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Author</th>
                                    <th>Genre</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($books as $book): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                                        <td><?php echo htmlspecialchars($book['genre']); ?></td>
                                        <td><?php echo number_format($book['price'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No books found in the inventory.</p>
                    <?php endif; ?>

                <?php
                } catch (PDOException $e) {
                    echo 'Error: ' . $e->getMessage();
                }
                ?>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- Popularity Report Section -->
    <!-- ========================= -->
    <div id="popularity" class="mb-5">
        <div class="card">
            <div class="card-header">
                <h5>Popularity Report - Books and Average Ratings</h5>
            </div>
            <div class="card-body">
                <?php
                // Fetch books with their average ratings
                $query = "SELECT b.title, b.author, AVG(r.rating) as avg_rating
                          FROM books b
                          LEFT JOIN reviews r ON b.book_id = r.book_id
                          GROUP BY b.book_id
                          ORDER BY avg_rating DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($books):
                ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Average Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo number_format($book['avg_rating'], 1); ?> / 5</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No books found with reviews.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- User Activity Report -->
    <!-- ========================= -->
    <div id="users" class="mb-5">
        <div class="card">
            <div class="card-header">
                <h5>User Activity Report - User Engagement Statistics</h5>
            </div>
            <div class="card-body">
                <?php
                // Fetch users with their review count
                $query = "SELECT u.username, COUNT(r.review_id) as review_count
                          FROM users u
                          LEFT JOIN reviews r ON u.user_id = r.user_id
                          GROUP BY u.user_id
                          ORDER BY review_count DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($users):
                ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Number of Reviews</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo $user['review_count']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No users have posted reviews.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // jump too scroll button functionality
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
