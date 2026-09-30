<?php
include 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user  = $_POST['username'] ?? '';
    $pass  = $_POST['password'] ?? '';
    $role  = $_POST['role'] ?? '';
    $fname = $_POST['first_name'] ?? '';
    $lname = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($user) || empty($pass) || empty($role) || empty($fname) || empty($lname) || empty($email)) {
        $message = "<div class='error'>Please fill in ALL fields.</div>";
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT); 

        $sql = "INSERT INTO users (username, password, role, first_name, last_name, email) 
                VALUES ('$user', '$hashed_pass', '$role', '$fname', '$lname', '$email')";

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='success'>Registration successful! <a href='login.php'>Login here</a></div>";
        } else {
            $message = "<div class='error'>Error: " . $conn->error . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Apellido</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #fff0f5 0%, #f3e5f5 100%); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; }
        .register-card { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05); width: 420px; animation: floatUp 0.8s ease-out; }
        @keyframes floatUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        h2 { text-align: center; color: #e83e8c; font-size: 26px; margin-bottom: 25px; font-weight: 700; letter-spacing: 1px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; margin-top: 12px; }
        input[type="text"], input[type="password"], input[type="email"], select { width: 100%; padding: 12px 16px; border: 1px solid #fce4ec; border-radius: 12px; background-color: #fff5f8; color: #333; font-size: 14px; font-family: 'Poppins', sans-serif; outline: none; transition: 0.3s; }
        input[type="text"]::placeholder, input[type="password"]::placeholder, input[type="email"]::placeholder { color: #f8bbd0; }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, select:focus { border-color: #f8bbd0; background-color: #ffffff; box-shadow: 0 0 10px rgba(248, 187, 208, 0.3); }
        select { appearance: none; cursor: pointer; }
        input[type="submit"] { width: 100%; padding: 15px; background: linear-gradient(to right, #f8bbd0, #f48fb1); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 25px; transition: 0.3s; font-family: 'Poppins', sans-serif; letter-spacing: 1px; }
        input[type="submit"]:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(244, 143, 177, 0.4); }
        .error { color: #d32f2f; background: #ffebee; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; }
        .success { color: #2e7d32; background: #e8f5e9; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; }
        .success a { color: #2e7d32; font-weight: 700; }
        .link { text-align: center; margin-top: 20px; font-size: 13px; color: #888; }
        .link a { color: #e83e8c; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .link a:hover { color: #f48fb1; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-card">
        <h2>Create Account 🌸</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" placeholder="Choose a username" required autocomplete="off">
            <label>Password</label>
            <input type="password" name="password" placeholder="Create a password" required>
            <label>Role</label>
            <select name="role" required>
                <option value="" disabled selected>Select your role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <label>First Name</label>
            <input type="text" name="first_name" placeholder="Enter your first name" required>
            <label>Last Name</label>
            <input type="text" name="last_name" placeholder="Enter your last name" required>
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="submit" value="Register ✨">
        </form>
        <p class="link">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>