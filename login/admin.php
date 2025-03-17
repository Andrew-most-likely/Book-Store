<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f0f2f5;
    }

    .login-form {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .login-form h2 {
        text-align: center;
        color: #007bff;
        margin-bottom: 20px;
    }

    .login-form h4 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .login-form .form-group {
        margin-bottom: 20px;
    }

    .login-form .form-group label {
        font-size: 14px;
        color: #333;
        display: block;
        margin-bottom: 8px;
    }

    .login-form .form-group input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
    }

    .login-form .form-group input:focus {
        border-color: #007bff;
    }

    .login-form .btn-login {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }

    .login-form .btn-login:hover {
        background-color: #0056b3;
    }

    .login-form .error-message,
    .login-form .success-message {
        margin-bottom: 20px;
        padding: 10px;
        text-align: center;
        font-size: 14px;
        border-radius: 4px;
    }

    .login-form .error-message {
        background-color: #f8d7da;
        color: #721c24;
    }

    .login-form .success-message {
        background-color: #d4edda;
        color: #155724;
    }

    .login-form .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .login-form .back-link a {
        color: #007bff;
        text-decoration: none;
    }

    .login-form .back-link a:hover {
        text-decoration: underline;
    }
</style>

<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db.php';

$error = '';
$success = '';

// admin redirect
if (isLoggedIn() && isAdmin()) {
    header("Location: ../admin/dashboard.php");
    exit();
} elseif (isLoggedIn() && !isAdmin()) {
    // non admin redirect
    header("Location: ../index.php");
    exit();
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        // data conn
        $database = new Database();
        $db = $database->connect();

        // Prep q so you cant inject sql
        $query = "SELECT user_id, username, password, role FROM users WHERE username = :username AND role = 'admin'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // user check
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // dash redirect
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Admin user not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bookstore</title>

</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <h2>Online Bookstore</h2>
            <h4>Admin Login</h4>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="back-link">
                <p>Return to <a href="login.php">User Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>