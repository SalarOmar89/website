<?php
include "db.php";
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include "header.php";

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM admin WHERE id = $id");
$admin = mysqli_fetch_assoc($result);

$success = '';
$error = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        // Delete admin
        if ($id == $_SESSION['admin_id']) {
            $error = "❌ You cannot delete your own account.";
        } else {
            mysqli_query($conn, "DELETE FROM admin WHERE id = $id");
            $success = "✅ Admin deleted successfully.";
            header("Refresh: 2; url=manage_admins.php");
        }
    } else {
        // Update admin
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $newPassword = $_POST['password'];

        $update = "UPDATE admin SET username = '$username', email = '$email'";
        if (!empty($newPassword)) {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update .= ", password = '$passwordHash'";
        }
        $update .= " WHERE id = $id";

        if (mysqli_query($conn, $update)) {
            $success = "✅ Admin updated successfully.";
            // Refresh the admin data
            $result = mysqli_query($conn, "SELECT * FROM admin WHERE id = $id");
            $admin = mysqli_fetch_assoc($result);
        } else {
            $error = "❌ Error: " . mysqli_error($conn);
        }
    }
}
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
    
    .edit-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
    }
    
    .form-title {
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
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--border-radius);
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--secondary);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    .btn-danger {
        background: var(--danger);
        color: white;
    }
    
    .btn-danger:hover {
        background: #d1144a;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }
    
    .btn-group {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
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
    
    .card {
        background: var(--light);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
    }
    
    .card-title {
        margin-top: 0;
        color: var(--primary);
    }
    
    @media (max-width: 768px) {
        .edit-container {
            padding: 1rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
    }
</style>

<div class="edit-container">
    <h2 class="form-title">Edit Admin</h2>
    
    <?php if($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3 class="card-title">Admin Information</h3>
        <form method="post">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">New Password (leave blank to keep current)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Update Admin</button>
                
                <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                    <button type="submit" name="delete" class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete this admin?')">
                        Delete Admin
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>