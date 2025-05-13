<?php
// Start the session and handle the header redirection before any output.
session_start();
include 'db.php';

// Enable error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in, and redirect if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include the header file
include "header.php";

$success = '';
$error = '';

// Add new admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM admin WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "❌ Email already exists.";
    } else {
        // Insert new admin
        $insert = "INSERT INTO admin (username, email, password) VALUES ('$username', '$email', '$password')";
        if (mysqli_query($conn, $insert)) {
            $success = "✅ Admin added successfully.";
        } else {
            $error = "❌ Error: " . mysqli_error($conn);
        }
    }
}

// Delete admin
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevent deleting the currently logged-in admin
    if ($id == $_SESSION['admin_id']) {
        $error = "❌ You cannot delete your own account.";
    } else {
        // Delete admin from the database
        $delete_query = "DELETE FROM admin WHERE id = $id";
        if (mysqli_query($conn, $delete_query)) {
            $success = "✅ Admin deleted successfully.";
        //  header("Location: manage_admins.php");
            // header("Refresh: 2; url=manage_admins.php");
            // exit();
        } else {
            $error = "❌ Error: " . mysqli_error($conn);
        }
        
        } 
}

// Fetch all admins
$admins = mysqli_query($conn, "SELECT * FROM admin");
?>

<style>
    :root {
        --primary: #4361ee;
        --secondary: #3f37c9;
        --accent: #4895ef;
        --light: #f8f9fa;
        --dark: #212529;
        --success: #4cc9f0;
        --danger: #f72585;
        --border-radius: 8px;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    .admin-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
    }
    
    .section-title {
        color: var(--primary);
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        font-weight: 600;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: var(--dark);
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
    }
    
    .form-control:focus {
        border-color: var(--accent);
        outline: none;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
    }
    
    .btn {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--border-radius);
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }
    
    .btn:hover {
        background: var(--secondary);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    .btn-danger {
        background: var(--danger);
    }
    
    .btn-danger:hover {
        background: #d1144a;
    }
    
    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius);
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    
    .admin-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 2rem;
    }
    
    .admin-table th, 
    .admin-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .admin-table th {
        background: var(--light);
        font-weight: 600;
        color: var(--dark);
    }
    
    .admin-table tr:hover {
        background: #f5f5f5;
    }
    
    .action-link {color: var(--primary);
        text-decoration: none;
        margin-right: 0.5rem;
        font-weight: 500;
        transition: var(--transition);
    }
    
    .action-link:hover {
        text-decoration: underline;
        color: var(--secondary);
    }
    
    .action-link.danger {
        color: var(--danger);
    }
    
    .action-link.danger:hover {
        color: #d1144a;
    }
    
    .card {
        background: var(--light);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
    }
    
    .card-title {
        margin-top: 0;
        color: var(--primary);
        font-size: 1.4rem;
    }
    
    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
        }
        
        .admin-table {
            display: block;
            overflow-x: auto;
        }
    }
</style>

<div class="admin-container">
    <h2 class="section-title">Manage Admins</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3 class="card-title">Add New Admin</h3>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Add Admin</button>
        </form>
    </div>
    
    <h3 class="section-title">Admin List</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($admin = mysqli_fetch_assoc($admins)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($admin['id']); ?></td>
                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                    <td><?php echo htmlspecialchars($admin['email']); ?></td>
                    <td>
                        <a href="edit_admin.php?id=<?php echo $admin['id']; ?>" class="action-link">Edit</a>
                        <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                            <a href="manage_admins.php?delete=<?php echo $admin['id']; ?>" 
                               class="action-link danger" 
                               onclick="return confirm('Are you sure you want to delete this admin?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>