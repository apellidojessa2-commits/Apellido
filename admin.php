<?php
session_start();

// --- DATABASE CONFIGURATION ---
$db_host = 'localhost';$db_user = 'root';
$db_pass = '';$db_name = 'user_management';

// Connect to MySQL server first
$conn = new mysqli($db_host, $db_user,$db_pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Automatically create database and table if missing
$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` ");
$conn->select_db($db_name);

$table_sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') DEFAULT 'User'
)";
$conn->query($table_sql);

// --- HANDLE ACTIONS ---
$message = '';

// Create New User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role =$_POST['role'];

    if (!empty($username) && !empty($_POST['password'])) {
        $stmt =$conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password,$role);
        if ($stmt->execute()) {$message = "User created successfully. 🌸";
        } else {
            $message = "Error creating user: " . $conn->error;
        }
        $stmt->close();
    }
}

// Delete User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt =$conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();$stmt->close();
    header("Location: admin.php");
    exit();
}

// Update Role
if (isset($_POST['update_role'])) {
    $id = intval($_POST['user_id']);
    $role =$_POST['role'];
    $stmt =$conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role,$id);
    $stmt->execute();$stmt->close();
    header("Location: admin.php");
    exit();
}

// Fetch All Users (Ordered descending by ID)
$result =$conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Floral Purple</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #4c1d95;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #581c87;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logout-btn {
            background-color: #9333ea;
            color: #fff;
            padding: 9px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.25);
            transition: all 0.2s ease;
        }
        .logout-btn:hover {
            background-color: #7e22ce;
            transform: translateY(-1px);
        }
        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0px 10px 30px rgba(126, 34, 206, 0.08);
            border: 1px solid #f3e8ff;
            margin-bottom: 25px;
        }
        .card h2 {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #6b21a8;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .alert {
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #d8b4fe;
        }
        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-size: 12px;
            font-weight: 700;
            color: #7e22ce;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input[type="text"], input[type="password"], select {
            padding: 10px 16px;
            border: 1.5px solid #e9d5ff;
            border-radius: 12px;
            outline: none;
            font-size: 14px;
            background-color: #faf5ff;
            color: #581c87;
            transition: all 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            border-color: #a855f7;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.15);
        }
        .submit-btn {
            background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);
            color: white;
            border: none;
            padding: 11px 22px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            align-self: flex-end;
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);
            transition: all 0.2s ease;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
            transform: translateY(-1px);
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 14px 16px;
            font-size: 14px;
        }
        th {
            border-bottom: 2px solid #f3e8ff;
            color: #7e22ce;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        td {
            border-bottom: 1px solid #f3e8ff;
            color: #581c87;
            font-weight: 500;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .role-select {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 8px;
        }
        .delete-btn {
            color: #dc2626;
            background-color: #fef2f2;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }
        .delete-btn:hover {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🌸 Admin Control Panel</h1>
        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>

    <?php if ($message): ?>
        <div class="alert"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>🌺 Create New User</h2>
        <form method="POST" action="admin.php" class="form-inline">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="User">User</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            
            <button type="submit" name="add_user" class="submit-btn">Add User</button>
        </form>
    </div>

    <div class="card">
        <h2>🪻 User Management</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row =$result->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $row['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <form method="POST" action="admin.php" style="margin:0;">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <select name="role" class="role-select" onchange="this.form.submit()">
                                    <option value="User" <?php if ($row['role'] === 'User') echo 'selected'; ?>>User</option>
                                    <option value="Admin" <?php if ($row['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="update_role" value="1">
                            </form>
                        </td>
                        <td>
                            <a href="admin.php?delete=<?php echo $row['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>