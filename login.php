<?php
// Include database connection
require 'db_connect.php';

session_start();
$message = "";

// Handling login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $sql = "SELECT id, role, name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $role, $name, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Store session variables
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect users based on their role
            if ($role === "Admin") {
                header("Location: admin_dashboard.php");
            } elseif ($role === "Customer") {
                header("Location: customer_dashboard.php");
            } elseif ($role === "Rider") {
                header("Location: rider_dashboard.php");
            } else {
                header("Location: index.php"); // Fallback redirection
            }
            exit();
        } else {
            $message = "Invalid email or password!";
        }
    } else {
        $message = "No user found with this email!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <p style="color: red; font-weight: bold;"><?= $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
