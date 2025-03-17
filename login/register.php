<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 0;
    }

    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f0f2f5;
    }

    .register-form {
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }

    .register-form h2 {
        text-align: center;
        color: #007bff;
        margin-bottom: 20px;
    }

    .register-form h4 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .register-form .form-group {
        margin-bottom: 20px;
    }

    .register-form .form-group label {
        font-size: 14px;
        color: #333;
        display: block;
        margin-bottom: 8px;
    }

    .register-form .form-group input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
    }

    .register-form .form-group input:focus {
        border-color: #007bff;
    }

    .register-form .btn-register {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
    }

    .register-form .btn-register:hover {
        background-color: #0056b3;
    }

    .register-form .error-message,
    .register-form .success-message {
        margin-bottom: 20px;
        padding: 10px;
        text-align: center;
        font-size: 14px;
        border-radius: 4px;
    }

    .register-form .error-message {
        background-color: #f8d7da;
        color: #721c24;
    }

    .register-form .success-message {
        background-color: #d4edda;
        color: #155724;
    }

    .register-form .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .register-form .back-link a {
        color: #007bff;
        text-decoration: none;
    }

    .register-form .back-link a:hover {
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

// reg procc
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // stack overflow is actually no help I hate this
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $database = new Database();
        $db = $database->connect();

        // usr exist check
        $query = "SELECT user_id FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Username already exists";
        } else {
            // email exist check
            $query = "SELECT user_id FROM users WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error = "Email already exists";
            } else {
                // PASS HASH :)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // prep new user input
                $query = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'customer')";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);

                if ($stmt->execute()) {
                    $success = "Registration successful. You can now login.";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bookstore</title>

</head>

<body>
    <div class="register-container">
        <div class="register-form">
            <h2>Online Bookstore</h2>
            <h4>User Registration</h4>

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
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-register">Register</button>
            </form>

            <div class="back-link">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>

</html>