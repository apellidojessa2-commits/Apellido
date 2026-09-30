<?php
session_start();

// --- DATABASE CONFIGURATION ---
$db_host = 'localhost';$db_user = 'root';
$db_pass = '';$db_name = 'user_management';

// Connect to MySQL server
$conn = new mysqli($db_host, $db_user,$db_pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Automatically create database and tasks table if missing
$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` ");
$conn->select_db($db_name);

$table_sql = "CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT 1,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_sql);

// --- HANDLE ACTIONS ---
$message = '';

// Add New Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title)) {
        $stmt =$conn->prepare("INSERT INTO tasks (title, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $title,$description);
        if ($stmt->execute()) {$message = "Task recorded in the shadow log. 🥀";
        } else {
            $message = "Error adding task: " . $conn->error;
        }
        $stmt->close();
    }
}

// Delete Task
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt =$conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();$stmt->close();
    header("Location: user.php");
    exit();
}

// Fetch All Tasks
$result =$conn->query("SELECT * FROM tasks ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - Withered Workspace</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
            color: #e4e4e7;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 850px;
            margin: 0 auto;
            background: #27272a;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45);
            border: 1px solid #3f3f46;
            padding: 30px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #3f3f46;
            padding-bottom: 18px;
            margin-bottom: 25px;
        }
        h1 {
            font-size: 22px;
            color: #f43f5e;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-links a {
            color: #fb7185;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.2s;
        }
        .nav-links a:hover {
            color: #fda4af;
            text-decoration: underline;
        }
        .nav-separator {
            color: #52525b;
            margin: 0 8px;
        }
        .card {
            background: #18181b;
            border: 1px solid #3f3f46;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 25px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        }
        h2 {
            font-size: 15px;
            color: #e4e4e7;
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-group {
            margin-bottom: 14px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #a1a1aa;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px 14px;
            font-family: inherit;
            font-size: 14px;
            background-color: #27272a;
            color: #f4f4f5;
            border: 1px solid #52525b;
            border-radius: 8px;
            outline: none;
            transition: all 0.2s;
        }
        input[type="text"]:focus, textarea:focus {
            border-color: #e11d48;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.2);
            background-color: #18181b;
        }
        textarea {
            height: 75px;
            resize: vertical;
        }
        button {
            background: linear-gradient(135deg, #be123c 0%, #881337 100%);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(136, 19, 55, 0.4);
            transition: all 0.2s ease;
        }
        button:hover {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            transform: translateY(-1px);
        }
        .message {
            background: #4c0519;
            color: #fecdd3;
            border: 1px solid #881337;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #18181b;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #3f3f46;
        }
        th, td {
            padding: 12px 16px;
            font-size: 14px;
            text-align: left;
        }
        th {
            background-color: #27272a;
            color: #a1a1aa;
            font-weight: 700;
            border-bottom: 1px solid #3f3f46;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        td {
            border-bottom: 1px solid #27272a;
            color: #e4e4e7;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .status-badge {
            display: inline-block;
            background: #3f3f46;
            color: #fda4af;
            border: 1px solid #71717a;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 12px;
            text-transform: uppercase;
        }
        .delete-btn {
            color: #f43f5e;
            background-color: #4c0519;
            padding: 5px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .delete-btn:hover {
            background-color: #881337;
            color: #ffe4e6;
        }
        .empty-state {
            text-align: center;
            color: #71717a;
            padding: 25px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>🥀 Welcome, user1234!</h1>
            <div class="nav-links">
                <a href="user.php">My Tasks</a>
                <span class="nav-separator">|</span>
                <a href="logout.php">Log Out</a>
            </div>
        </header>

        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>🖤 Add a New Task</h2>
            <form method="POST" action="user.php">
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" name="title" placeholder="Enter task title..." required>
                </div>
                
                <div class="form-group">
                    <label>Description (Optional)</label>
                    <textarea name="description" placeholder="Enter task details..."></textarea>
                </div>
                
                <button type="submit" name="add_task">Add Task</button>
            </form>
        </div>

        <h2>🥀 My Active Workspace</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 60%;">Task Details</th>
                    <th style="width: 20%; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result &&$result->num_rows > 0): ?>
                    <?php while ($row =$result->fetch_assoc()): ?>
                        <tr>
                            <td><span class="status-badge"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                <?php if (!empty($row['description'])): ?>
                                    <br><small style="color: #a1a1aa;"><?php echo htmlspecialchars($row['description']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="user.php?delete=<?php echo $row['id']; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty-state">No withered tasks in your workspace yet! 🥀</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>