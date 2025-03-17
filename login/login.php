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

// usr redirect
if (isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        $database = new Database();
        $db = $database->connect();

        $query = "SELECT user_id, username, password, role FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] == 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bookstore</title>
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <h2>Online Bookstore</h2>
            <h4>User Login</h4>

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
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p>Are you an admin? <a href="admin.php">Admin Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>