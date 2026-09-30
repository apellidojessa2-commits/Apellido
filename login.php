<?php
session_start();
include 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim removes any accidental spaces before or after the username
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (empty($user) || empty($pass)) {
        $error = "Please fill in all fields.";
    } else {
        $sql = "SELECT * FROM users WHERE username = '$user'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if (password_verify($pass, $row['password'])) {
                // Update last login time
                $update_sql = "UPDATE users SET last_login = NOW() WHERE id = '{$row['id']}'";
                $conn->query($update_sql);

                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['id'] = $row['id'];

                if ($row['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: user.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            // Show the exact username being searched for debugging
            $error = "User not found: '" . htmlspecialchars($user) . "'";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Apellido</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fff0f5 0%, #f3e5f5 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-card { background: white; padding: 45px 40px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05); width: 380px; animation: floatUp 0.8s ease-out; }
        @keyframes floatUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        h2 { text-align: center; color: #e83e8c; font-size: 26px; margin-bottom: 30px; font-weight: 700; letter-spacing: 1px; }
        label { display: block; font-size: 14px; font-weight: 500; color: #555; margin-bottom: 8px; margin-top: 15px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 14px 18px; border: 1px solid #fce4ec; border-radius: 12px; background-color: #fff5f8; color: #333; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; transition: 0.3s; }
        input[type="text"]::placeholder, input[type="password"]::placeholder { color: #f8bbd0; }
        input[type="text"]:focus, input[type="password"]:focus { border-color: #f8bbd0; background-color: #ffffff; box-shadow: 0 0 10px rgba(248, 187, 208, 0.3); }
        input[type="submit"] { width: 100%; padding: 15px; background: linear-gradient(to right, #f8bbd0, #f48fb1); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 25px; transition: 0.3s; font-family: 'Poppins', sans-serif; letter-spacing: 1px; }
        input[type="submit"]:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(244, 143, 177, 0.4); }
        .error { color: #d32f2f; background: #ffebee; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; }
        .link { text-align: center; margin-top: 25px; font-size: 13px; color: #888; }
        .link a { color: #e83e8c; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .link a:hover { color: #f48fb1; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Welcome Back 🌸</h2>
        <?php if($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter your username" required autocomplete="off">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
            <input type="submit" value="Log In ✨">
        </form>
        <p class="link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>