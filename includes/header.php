<style>

    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    header {
        width: 100%;
        background-color: #343a40;
        padding: 15px 20px;
        color: white;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .navbar-brand {
        font-size: 1.8rem;
        font-weight: bold;
        color: #ffffff;
        text-decoration: none;
        text-transform: uppercase;
    }

    .navbar-brand:hover {
        color: #f8f9fa;
    }

    .navbar-nav {
        display: flex;
        gap: 20px;
        list-style: none;
    }

    .navbar-nav li {
        position: relative;
    }

    .navbar-nav a {
        text-decoration: none;
        color: white;
        padding: 10px 15px;
        font-size: 1rem;
        display: block;
        text-transform: capitalize;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .navbar-nav a:hover {
        background-color: #007bff;
    }

    .navbar-nav .dropdown-content {
        display: none;
        position: absolute;
        background-color: #343a40;
        min-width: 160px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 5px;
    }

    .navbar-nav .dropdown:hover .dropdown-content {
        display: block;
    }

    .navbar-nav .dropdown-item {
        padding: 10px 15px;
        text-decoration: none;
        color: white;
        display: block;
        transition: background-color 0.3s;
    }

    .navbar-nav .dropdown-item:hover {
        background-color: #007bff;
    }

    .navbar-toggle {
        display: none;
    }

    @media (max-width: 768px) {
        .navbar-nav {
            flex-direction: column;
            gap: 10px;
            display: none;
            width: 100%;
            text-align: center;
        }

        .navbar-nav.show {
            display: flex;
        }

        .navbar-toggle {
            display: block;
            background-color: #343a40;
            border: none;
            color: white;
            font-size: 1.5rem;
            padding: 10px;
            cursor: pointer;
        }
    }
</style>

<?php
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>
</head>

<body>
    <header>
        <div class="navbar">
            <!-- Brand Logo -->
            <a class="navbar-brand" href="../index.php">Online Bookstore</a>

            <!-- Navbar Toggle (for mobile) -->
            <button class="navbar-toggle" id="navbar-toggle">
                &#9776;
            </button>

            <!-- Navbar Links -->
            <ul class="navbar-nav" id="navbar-nav">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../pages/books.php">Browse Books</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="../admin/dashboard.php">Admin Dashboard</a></li>
                <?php endif; ?>

                <?php if (isLoggedIn()): ?>
                    <li class="dropdown">
                        <a href="#">Welcome, <?php echo $_SESSION['username']; ?></a>
                        <div class="dropdown-content">
                            <a href="../login/logout.php" class="dropdown-item">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="../login/login.php">Login</a></li>
                    <li><a href="../login/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <script>
        // Toggle navigation on mobile
        const toggleButton = document.getElementById('navbar-toggle');
        const navMenu = document.getElementById('navbar-nav');

        toggleButton.addEventListener('click', () => {
            navMenu.classList.toggle('show');
        });
    </script>
</body>

</html>